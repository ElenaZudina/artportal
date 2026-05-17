(function () {
    'use strict';

    var forms = document.querySelectorAll('.js-purchase-request-form');

    forms.forEach(function (form) {
        form.addEventListener('submit', function () {
            var submitButton = form.querySelector('button[type="submit"]');

            if (!submitButton) {
                return;
            }

            submitButton.disabled = true;

            var spinner = submitButton.querySelector('.js-purchase-request-spinner');
            var text = submitButton.querySelector('.js-purchase-request-text');

            if (spinner) {
                spinner.classList.remove('d-none');
            }

            if (text) {
                text.textContent = 'Sending request...';
            }
        });
    });
})();
