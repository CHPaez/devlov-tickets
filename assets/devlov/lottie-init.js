/* DevLov — wires the self-hosted lottie-web player (assets/devlov/vendor/lottie-light.min.js)
   to the two spots that use it: the "Please Wait" loading indicator shown by
   osticket.js's shared form-submit handler, and the ticket-creation
   thank-you page's success checkmark. */
$(document).ready(function () {
    if (typeof lottie === 'undefined') return;

    var root = window.DL_ROOT_PATH || '/';

    // Loading spinner: loops while a form submission is in flight. Classic
    // full-page POST forms here (not fetch/XHR), so "in flight" starts at
    // submit and ends when the browser navigates to the response — there's
    // no separate JS "done" event to hook, the page (and this animation)
    // simply gets replaced.
    var loadingEl = document.getElementById('loading-lottie');
    if (loadingEl) {
        var spinner = lottie.loadAnimation({
            container: loadingEl,
            renderer: 'svg',
            loop: true,
            autoplay: false,
            path: root + 'assets/devlov/lottie/loading-spinner.json'
        });
        $(document).on('submit', 'form', function () {
            spinner.play();
        });
    }

    // Ticket-creation thank-you checkmark: plays once on load, no loop.
    var successEl = document.getElementById('thankyou-lottie');
    if (successEl) {
        lottie.loadAnimation({
            container: successEl,
            renderer: 'svg',
            loop: false,
            autoplay: true,
            path: root + 'assets/devlov/lottie/checkmark-success.json'
        });
    }
});
