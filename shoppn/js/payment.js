(function () {
    function showMessage(text, type) {
        const box = document.getElementById('payment-message');
        if (!box) { return; }
        box.textContent = text;
        box.className = 'alert ' + (type === 'info' ? 'alert-info' : 'alert-error');
        box.hidden = false;
        box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('pay-button');
        if (!button) { return; }

        const label = button.textContent;

        button.addEventListener('click', function () {
            if (typeof PaystackPop === 'undefined') {
                showMessage('Paystack could not be loaded. Check your internet connection and try again.', 'error');
                return;
            }

            const amount = parseInt(button.dataset.amount, 10);

            if (!button.dataset.key || !button.dataset.email || !amount || amount < 1) {
                showMessage('Payment details are incomplete. Please go back to your cart and try again.', 'error');
                return;
            }

            const handler = PaystackPop.setup({
                key: button.dataset.key,
                email: button.dataset.email,
                amount: amount,
                currency: 'GHS',
                ref: button.dataset.ref,
                callback: function (response) {
                    button.disabled = true;
                    button.textContent = 'Verifying payment...';
                    window.location.href = button.dataset.callback + '?ref=' + encodeURIComponent(response.reference);
                },
                onClose: function () {
                    button.disabled = false;
                    button.textContent = label;
                    showMessage('Payment window closed. You have not been charged and your cart is unchanged.', 'info');
                }
            });

            handler.openIframe();
        });
    });

    window.addEventListener('pageshow', function (event) {
        const button = document.getElementById('pay-button');
        if (event.persisted && button) { window.location.reload(); }
    });
})();
