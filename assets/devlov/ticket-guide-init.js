/* DevLov — 3-step guide strip(s) on the client ticket forms
   (include/client/open.inc.php's "Contanos tu problema..." and
   include/client/accesslink.inc.php's "Ingresá tu correo..."). Always
   visible on every page load (no first-time-only gating — that was the
   original design, reversed after the user liked how it turned out and
   asked for it to be permanent). The (x) button is just a per-view
   convenience — closes it for the current page view only, no memory,
   reappears on the next load like any other page chrome. */
$(document).ready(function () {
    $('.dl-ticket-guide').each(function () {
        var $guide = $(this);
        $guide.find('.dl-ticket-guide-dismiss').on('click', function () {
            $guide.addClass('dl-ticket-guide-hide');
            setTimeout(function () {
                $guide.remove();
            }, 200);
        });
    });
});
