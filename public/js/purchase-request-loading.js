(function () {
    'use strict';

    var forms = document.querySelectorAll('.js-purchase-request-form');

    function setLoading(form, isLoading) {
        var submitButton = form.querySelector('button[type="submit"]');

        if (!submitButton) {
            return;
        }

        submitButton.disabled = isLoading;

        var spinner = submitButton.querySelector('.js-purchase-request-spinner');
        var text = submitButton.querySelector('.js-purchase-request-text');

        if (spinner) {
            spinner.classList.toggle('d-none', !isLoading);
        }

        if (text) {
            if (!text.dataset.defaultText) {
                text.dataset.defaultText = text.textContent;
            }

            text.textContent = isLoading ? 'Sending request...' : text.dataset.defaultText;
        }
    }

    forms.forEach(function (form) {
        form.addEventListener('submit', function () {
            setLoading(form, true);
        });
    });

    window.addEventListener('pageshow', function () {
        forms.forEach(function (form) {
            setLoading(form, false);
        });
    });
})();
