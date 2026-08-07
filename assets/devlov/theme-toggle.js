/* DevLov — "Vista clásica" / "Modo creativo" theme toggle.
   The actual dark/light SWITCH (setting data-theme on <html> before first
   paint, from localStorage) happens in a tiny inline <script> placed as
   early as possible in each page's <head> — see header.inc.php in both
   include/client/ and include/staff/ — so there's no flash of the wrong
   theme while this file loads. This file only wires up the visible
   toggle button(s): flips the attribute + localStorage on click, and
   keeps the button's own icon/label in sync with the current theme. */
(function () {
    var KEY = 'dl_theme';

    function isDark() {
        return document.documentElement.getAttribute('data-theme') === 'dark';
    }

    function syncButton(btn) {
        var dark = isDark();
        var label = dark ? 'Vista clásica' : 'Modo creativo';
        btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
        btn.setAttribute('title', label);
        btn.setAttribute('aria-label', dark ? 'Cambiar a vista clásica' : 'Cambiar a modo creativo');
        var $text = $(btn).find('.dl-theme-toggle-label');
        if ($text.length) $text.text(label);
    }

    $(document).ready(function () {
        var $buttons = $('.dl-theme-toggle');
        if (!$buttons.length) return;

        $buttons.each(function () { syncButton(this); });

        $buttons.on('click', function () {
            var next = isDark() ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            try { localStorage.setItem(KEY, next); } catch (e) {}
            $buttons.each(function () { syncButton(this); });
        });
    });
})();
