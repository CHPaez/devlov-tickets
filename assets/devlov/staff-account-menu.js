/* DevLov — top-right account menu (avatar) dropdown, staff panel only.
   GitHub-style: click the avatar/name trigger to open a vertical menu
   with Admin/Agent Panel, Profile, Log Out. Closes on outside click or
   Escape. Plain jQuery (already loaded sitewide), no new dependency. */
$(document).ready(function () {
    var $menu = $('#account-menu');
    if (!$menu.length) return;

    var $trigger = $menu.find('.account-menu-trigger');

    function closeMenu() {
        $menu.removeClass('open');
        $trigger.attr('aria-expanded', 'false');
    }
    function openMenu() {
        $menu.addClass('open');
        $trigger.attr('aria-expanded', 'true');
    }

    $trigger.on('click', function (e) {
        e.stopPropagation();
        if ($menu.hasClass('open')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    $(document).on('click', function (e) {
        if ($menu.hasClass('open') && $menu.has(e.target).length === 0 && !$menu.is(e.target)) {
            closeMenu();
        }
    });

    $(document).on('keydown', function (e) {
        if ((e.key === 'Escape' || e.keyCode === 27) && $menu.hasClass('open')) {
            closeMenu();
        }
    });
});
