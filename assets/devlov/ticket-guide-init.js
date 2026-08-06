/* DevLov — first-time-only 3-step guide strip on "Open a New Ticket"
   (include/client/open.inc.php). Gating strategy:
   - A tiny inline <script> right after the strip's markup (in
     open.inc.php itself, not here) removes it immediately if
     localStorage already has the "seen" flag — that runs synchronously
     as the page parses, before this file loads, so a returning user
     never sees a flash of the strip.
   - This file only runs for a strip that actually rendered (i.e. the
     inline script did NOT remove it) — meaning this is either the
     user's first visit, or localStorage was unavailable/blocked. Being
     visible at all counts as "shown", so we set the flag right away:
     the strip won't render again on the next page load either way.
   - The (x) dismiss button just lets the user close it early within
     this same page view, before that next-load gating kicks in. */
$(document).ready(function () {
    var guide = document.getElementById('dl-ticket-guide');
    if (!guide) return;

    var FLAG = 'dl_seen_ticket_guide';

    function markSeen() {
        try { localStorage.setItem(FLAG, '1'); } catch (e) {}
    }

    markSeen();

    $(guide).find('.dl-ticket-guide-dismiss').on('click', function () {
        markSeen();
        $(guide).addClass('dl-ticket-guide-hide');
        setTimeout(function () {
            if (guide.parentNode) guide.parentNode.removeChild(guide);
        }, 200);
    });
});
