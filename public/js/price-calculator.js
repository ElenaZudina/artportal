/**
 * Price Calculator
 * Initializes price calculator with AJAX backend
 * Works in both dashboard and painting form views
 */
(function () {
    'use strict';

    function initPriceCalculator() {
        // Get DOM elements
        var modeEl = document.getElementById('calc_mode');
        var valueHintEl = document.getElementById('calc_value_hint');
        var valueEl = document.getElementById('calc_value');
        var commissionEl = document.getElementById('calc_commission');
        var taxEl = document.getElementById('calc_tax');
        var expensesEl = document.getElementById('calc_expenses');
        var residentEl = document.getElementById('calc_is_tax_resident');
        var runBtn = document.getElementById('calc_run_btn');
        var alertEl = document.getElementById('price-calc-alert');
        var resultWrapEl = document.getElementById('price-calc-result');
        var mainLabelEl = document.getElementById('result_main_label');
        var mainOutputEl = document.getElementById('calc_result_main');
        var commissionOutEl = document.getElementById('calc_result_commission');
        var taxOutEl = document.getElementById('calc_result_tax');
        var expensesOutEl = document.getElementById('calc_result_expenses');

        // Optional elements for painting form
        var useBtn = document.getElementById('calc_use_price_btn');
        var priceInputEl = document.getElementById('painting_price');
        var latestPrice = null;

        // Bail out if required elements don't exist
        if (!modeEl || !runBtn) {
            return;
        }

        function setModeHint() {
            valueHintEl.textContent = modeEl.value === 'income'
                ? 'Desired income amount'
                : 'Current price amount';
        }

        function showError(message) {
            alertEl.textContent = message || 'Calculation error';
            alertEl.classList.remove('d-none');
        }

        function hideError() {
            alertEl.textContent = '';
            alertEl.classList.add('d-none');
        }

        modeEl.addEventListener('change', setModeHint);
        setModeHint();

        runBtn.addEventListener('click', function () {
            hideError();
            resultWrapEl.classList.add('d-none');

            // Disable use button if it exists
            if (useBtn) {
                useBtn.disabled = true;
            }

            var payload = new URLSearchParams();
            payload.append('mode', modeEl.value);
            payload.append('value', valueEl.value);
            payload.append('commission', commissionEl.value);
            payload.append('tax', taxEl.value);
            payload.append('expenses', expensesEl.value);
            payload.append('isTaxResident', residentEl.checked ? '1' : '0');

            fetch('/artportal/dashboard/price-calculate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: payload.toString()
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (json) {
                if (!json || !json.success || !json.data) {
                    showError(json && json.message ? json.message : 'Calculation failed');
                    return;
                }

                latestPrice = Number(json.data.price || 0);
                var isIncomeMode = modeEl.value === 'income';

                if (isIncomeMode) {
                    mainLabelEl.textContent = 'Your price:';
                    mainOutputEl.textContent = Number(json.data.price || 0).toFixed(2);
                } else {
                    mainLabelEl.textContent = 'Net profit:';
                    mainOutputEl.textContent = Number(json.data.netIncome || 0).toFixed(2);
                }

                taxOutEl.textContent = Number(json.data.taxAmount || 0).toFixed(2);
                commissionOutEl.textContent = Number(json.data.commissionAmount || 0).toFixed(2);
                expensesOutEl.textContent = Number(json.data.expenses || 0).toFixed(2);

                resultWrapEl.classList.remove('d-none');

                // Enable use button if it exists
                if (useBtn) {
                    useBtn.disabled = false;
                }
            })
            .catch(function () {
                showError('Network error');
            });
        });

        // Handle "Use price" button (only in painting form)
        if (useBtn && priceInputEl) {
            useBtn.addEventListener('click', function () {
                if (latestPrice === null) {
                    return;
                }

                priceInputEl.value = Number(latestPrice).toFixed(2);

                var modalEl = document.getElementById('priceCalculatorModal');
                if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                    var modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.hide();
                }
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPriceCalculator);
    } else {
        initPriceCalculator();
    }
})();
