class Refund {
    constructor(adminController) {
        this.adminController = adminController;
        this.adminController.onRefreshUi(() => {
            this.init();
        });
    }

    init() {
        $('.js-invoice-refund:not(.js-invoice-refund--processed)')
            .addClass('js-invoice-refund--processed')
            .on('click', (e) => {

                e.preventDefault();
                e.stopPropagation();

                let $item = $(e.currentTarget);

                this.showModal(
                    $item.data('id'),
                    $item.data('max'),
                    $item.data('max-formatted')
                )
            });
    }

    showModal(paymentId, maxAmount, maxAmountFormatted) {

        let msg;

        msg = 'Please confirm refund amount and reason:';
        msg += `<br /><br /><input type="text" id="refund-amount" value="${maxAmount / 100}" style="width: 100%">`;
        msg += '<div id="refund-amount-error" class="text-danger hidden" style="margin-bottom: 1em;"></div>';
        msg += '<textarea id="refund-reason" placeholder="Reason for refund (optional)" style="width: 100%; margin: 0;"></textarea>';

        let $modal = $('<div>')
            .html(msg)
            .dialog({
                'title': 'Are you sure?',
                'resizable': false,
                'draggable': false,
                'modal': true,
                'buttons': {
                    'OK': () => {

                        let amount = $('#refund-amount').val();
                        let reason = $('#refund-reason').val();

                        if (amount <= 0) {
                            this.setErrorMessage('Refund amount must be greater than 0.');

                        } else if (amount > maxAmount / 100) {
                            this.setErrorMessage(`Refund amount must be no greater than ${maxAmountFormatted}.`);

                        } else {

                            this.clearErrorMessage();

                            let $form = this.buildForm(paymentId, amount, reason);

                            $('body').append($form);

                            $form.submit();
                            $modal.dialog('close');
                        }
                    },
                    'Cancel': () => {
                        $modal.dialog('close');
                    }
                }
            });
    }

    // --------------------------------------------------------------------------

    buildForm(paymentId, amount, reason) {
        return $('<form>')
            .attr({
                'method': 'POST',
                'action': `${window.SITE_URL}admin/invoice/payment/refund/${paymentId}`
            })
            .append($('<input>').attr({
                'name': 'amount',
                'value': amount
            }))
            .append($('<input>').attr({
                'name': 'reason',
                'value': reason
            }))
            .append($('<input>').attr({
                'name': 'return_to',
                'value': window.location.href
            }));
    }

    // --------------------------------------------------------------------------

    setErrorMessage(msg) {
        $('#refund-amount-error')
            .text(msg)
            .removeClass('hidden');

        return this;
    }

    // --------------------------------------------------------------------------

    clearErrorMessage() {
        $('#refund-amount-error')
            .addClass('hidden');
    }
}

export default Refund;
