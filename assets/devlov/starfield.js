/* DevLov — "Modo creativo" starfield. Ported from the twinkling/
   mouse-proximity canvas starfield already used on devlov-app's dark
   landing page (F:\IA\devlov-app\src\components\StarField.tsx) — same
   visual language (white/violet/orange stars, subtle twinkle, glow near
   the cursor), reimplemented in plain JS/canvas since this codebase has
   no React, and scoped to a CONTAINER element instead of the whole
   viewport (devlov-app's version owns the entire page; here it has to
   coexist with real page chrome).

   Dark-mode only, and only on elements explicitly opted in via
   [data-dl-starfield] — deliberately NOT applied to data-dense screens
   (ticket lists, admin tables), matching the low-density-only decoration
   boundary already established for this app's Lottie animations and
   login ambient glow blobs. Respects prefers-reduced-motion. */
(function () {
    var TINT_RGB = { white: '255,255,255', accent: '255,157,77', violet: '152,132,245' };
    var PROXIMITY_RADIUS = 150;

    function prefersReducedMotion() {
        return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function isDark() {
        return document.documentElement.getAttribute('data-theme') === 'dark';
    }

    function makeStars(width, height) {
        var count = Math.min(160, Math.max(40, Math.floor((width * height) / 9000)));
        var stars = [];
        for (var i = 0; i < count; i++) {
            var roll = Math.random();
            var tint = roll < 0.14 ? 'accent' : roll < 0.3 ? 'violet' : 'white';
            stars.push({
                x: Math.random() * width,
                y: Math.random() * height,
                r: 1 + Math.random() * 1.8,
                baseAlpha: 0.5 + Math.random() * 0.4,
                phase: Math.random() * Math.PI * 2,
                speed: 0.3 + Math.random() * 0.6,
                tint: tint
            });
        }
        return stars;
    }

    function mount(container) {
        if (container.__dlStarfieldMounted) return;
        container.__dlStarfieldMounted = true;

        var canvas = document.createElement('canvas');
        canvas.className = 'dl-starfield-canvas';
        canvas.setAttribute('aria-hidden', 'true');
        container.insertBefore(canvas, container.firstChild);

        var ctx = canvas.getContext('2d');
        var stars = [], width = 0, height = 0, raf = 0, running = false;
        var dpr = Math.min(window.devicePixelRatio || 1, 2);
        var mouse = { x: -9999, y: -9999 };

        function resize() {
            width = container.clientWidth;
            height = container.clientHeight;
            canvas.width = width * dpr;
            canvas.height = height * dpr;
            canvas.style.width = width + 'px';
            canvas.style.height = height + 'px';
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            stars = makeStars(width, height);
        }

        function onMove(e) {
            var rect = container.getBoundingClientRect();
            mouse.x = e.clientX - rect.left;
            mouse.y = e.clientY - rect.top;
        }
        function onLeave() { mouse.x = -9999; mouse.y = -9999; }

        function frame(t) {
            ctx.clearRect(0, 0, width, height);
            for (var i = 0; i < stars.length; i++) {
                var s = stars[i];
                var twinkle = 0.55 + 0.45 * Math.sin(t * 0.001 * s.speed + s.phase);
                var alpha = s.baseAlpha * twinkle;
                var radius = s.r;
                var dx = s.x - mouse.x, dy = s.y - mouse.y;
                var dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < PROXIMITY_RADIUS) {
                    var closeness = 1 - dist / PROXIMITY_RADIUS;
                    alpha = Math.min(1, alpha + closeness * 0.5);
                    radius = s.r * (1 + closeness * 1.8);
                }
                ctx.beginPath();
                ctx.fillStyle = 'rgba(' + TINT_RGB[s.tint] + ',' + alpha + ')';
                ctx.arc(s.x, s.y, radius, 0, Math.PI * 2);
                ctx.fill();
            }
            raf = requestAnimationFrame(frame);
        }

        function start() {
            if (running || prefersReducedMotion()) return;
            running = true;
            resize();
            window.addEventListener('resize', resize);
            container.addEventListener('pointermove', onMove);
            container.addEventListener('pointerleave', onLeave);
            raf = requestAnimationFrame(frame);
        }
        function stop() {
            if (!running) return;
            running = false;
            cancelAnimationFrame(raf);
            window.removeEventListener('resize', resize);
            container.removeEventListener('pointermove', onMove);
            container.removeEventListener('pointerleave', onLeave);
            if (width && height) ctx.clearRect(0, 0, width, height);
        }

        function sync() {
            if (isDark()) { start(); } else { stop(); }
        }
        sync();

        new MutationObserver(sync).observe(document.documentElement, {
            attributes: true, attributeFilter: ['data-theme']
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var els = document.querySelectorAll('[data-dl-starfield]');
        for (var i = 0; i < els.length; i++) mount(els[i]);
    });
})();
