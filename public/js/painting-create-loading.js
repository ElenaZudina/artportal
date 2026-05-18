(function () {
    'use strict';

    var form = document.getElementById('paintingSaveForm');
    var submitButton = document.getElementById('paintingSaveSubmit');
    var submitSpinner = document.getElementById('paintingSaveSpinner');
    var submitText = document.getElementById('paintingSaveSubmitText');

    if (!form || !submitButton) {
        return;
    }

    form.addEventListener('submit', function () {
        submitButton.disabled = true;

        if (submitSpinner) {
            submitSpinner.classList.remove('d-none');
        }

        if (submitText) {
            submitText.textContent = 'Processing image...';
        }
    });
})();
