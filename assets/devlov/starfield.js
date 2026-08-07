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
   login ambient glow blobs.

   Respects prefers-reduced-motion, but NOT by rendering nothing — a user
   reported the starfield invisible on every desktop browser but working
   fine on their phone; turned out to be Windows' system-level "Show
   animations" accessibility toggle, which makes every browser on that
   machine report prefers-reduced-motion:reduce identically (mobile OSes
   have their own separate, usually-off-by-default setting, hence the
   phone/desktop split). The original version treated that as "don't draw
   at all," which meant users who'd never consciously opted into hiding
   this decoration just never saw it. Now: a static single-frame render
   (stars placed once, no twinkle, no animation loop, no cursor-glow
   interaction) is drawn instead — motion is suppressed, the decoration
   isn't erased.

   Intensity: an optional [data-dl-starfield-intensity="low"] on the same
   container scales density/brightness down (used on login.php, where
   stars are layered UNDERNEATH the existing glow-blob decoration rather
   than competing with it). Default ("full", used on open.php/view.php)
   is deliberately brighter/denser than the first version shipped here —
   the initial pass read as barely-visible flecks rather than a
   recognizable starfield. */
(function () {
    var TINT_RGB = { white: '255,255,255', accent: '255,157,77', violet: '152,132,245' };
    var PROXIMITY_RADIUS = 160;
    var INTENSITY = {
        full: { density: 6000, minCount: 70, maxCount: 220, rMin: 1.4, rMax: 3.2, aMin: 0.65, aMax: 1.0, glow: 1 },
        low:  { density: 11000, minCount: 30, maxCount: 90, rMin: 1.1, rMax: 2.4, aMin: 0.35, aMax: 0.6, glow: 0.6 }
    };

    function prefersReducedMotion() {
        return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function isDark() {
        return document.documentElement.getAttribute('data-theme') === 'dark';
    }

    function makeStars(width, height, cfg) {
        var count = Math.min(cfg.maxCount, Math.max(cfg.minCount, Math.floor((width * height) / cfg.density)));
        var stars = [];
        for (var i = 0; i < count; i++) {
            var roll = Math.random();
            var tint = roll < 0.14 ? 'accent' : roll < 0.3 ? 'violet' : 'white';
            stars.push({
                x: Math.random() * width,
                y: Math.random() * height,
                r: cfg.rMin + Math.random() * (cfg.rMax - cfg.rMin),
                baseAlpha: cfg.aMin + Math.random() * (cfg.aMax - cfg.aMin),
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

        var intensityKey = container.getAttribute('data-dl-starfield-intensity');
        var cfg = INTENSITY[intensityKey] || INTENSITY.full;

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
            stars = makeStars(width, height, cfg);
            if (prefersReducedMotion()) drawStatic();
        }

        function drawStars(alphaFor, radiusFor) {
            ctx.clearRect(0, 0, width, height);
            for (var i = 0; i < stars.length; i++) {
                var s = stars[i];
                var alpha = alphaFor(s);
                var radius = radiusFor(s);
                var rgb = TINT_RGB[s.tint];
                ctx.shadowColor = 'rgba(' + rgb + ',' + Math.min(1, alpha + 0.15) + ')';
                ctx.shadowBlur = radius * 3 * cfg.glow;
                ctx.beginPath();
                ctx.fillStyle = 'rgba(' + rgb + ',' + alpha + ')';
                ctx.arc(s.x, s.y, radius, 0, Math.PI * 2);
                ctx.fill();
                ctx.shadowBlur = 0;
            }
        }

        function drawStatic() {
            drawStars(function (s) { return s.baseAlpha; }, function (s) { return s.r; });
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
                var rgb = TINT_RGB[s.tint];
                ctx.shadowColor = 'rgba(' + rgb + ',' + Math.min(1, alpha + 0.15) + ')';
                ctx.shadowBlur = radius * 3 * cfg.glow;
                ctx.beginPath();
                ctx.fillStyle = 'rgba(' + rgb + ',' + alpha + ')';
                ctx.arc(s.x, s.y, radius, 0, Math.PI * 2);
                ctx.fill();
                ctx.shadowBlur = 0;
            }
            raf = requestAnimationFrame(frame);
        }

        function start() {
            if (running) return;
            running = true;
            resize();
            window.addEventListener('resize', resize);
            if (!prefersReducedMotion()) {
                container.addEventListener('pointermove', onMove);
                container.addEventListener('pointerleave', onLeave);
                raf = requestAnimationFrame(frame);
            }
            // else: resize() already called drawStatic() — no rAF loop,
            // no pointer listeners, nothing moves.
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
            // Fully restart on a reduced-motion change (not just a theme
            // change) so switching the OS setting while the page is open
            // flips between animated and static without a refresh.
            if (running) stop();
            if (isDark()) start();
        }
        sync();

        new MutationObserver(sync).observe(document.documentElement, {
            attributes: true, attributeFilter: ['data-theme']
        });
        if (window.matchMedia) {
            var rmq = window.matchMedia('(prefers-reduced-motion: reduce)');
            if (rmq.addEventListener) rmq.addEventListener('change', sync);
            else if (rmq.addListener) rmq.addListener(sync); // older Safari
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var els = document.querySelectorAll('[data-dl-starfield]');
        for (var i = 0; i < els.length; i++) mount(els[i]);
    });
})();
