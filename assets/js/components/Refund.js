class Refund {

    /**
     * Initialises the class
     * @param adminController
     */
    constructor(adminController) {
        this.adminController = adminController;
        this
            .adminController
            .log('Constructing')
            .onRefreshUi(() => {
                this.init();
            });
    }

    // --------------------------------------------------------------------------

    /**
     * Binds to new rfeund buttons
     * @returns {Refund}
     */
    init() {
        this.adminController.log('Looking for new items');
        let $items = $('.js-invoice-refund:not(.js-invoice-refund--processed)');
        this.adminController.log(`Found ${$items.length} new items`);

        $items
            .addClass('js-invoice-refund--processed')
            .on('click', (e) => {

                this.adminController.log('Opening modal');

                e.preventDefault();
                e.stopPropagation();

                let $item = $(e.currentTarget);

                this.showModal(
                    $item.data('id'),
                    $item.data('max'),
                    $item.data('max-formatted')
                )
            });

        return this;
    }

    // --------------------------------------------------------------------------

    /**
     * Builds and shws the modal
     * @param paymentId The payment ID to refund
     * @param maxAmount The maximum refund amount permitted
     * @param maxAmountFormatted The maximum refund amount permitted, as a string
     * @returns {Refund}
     */
    showModal(paymentId, maxAmount, maxAmountFormatted) {

        this.$text = $('<p>').text('Please confirm refund amount and reason:');

        this.$inputAmount = $('<input>')
            .val(maxAmount / 100)
            .css({
                'width': '100%',
                'margin-bottom': '1em'
            });

        this.$amountError = $('<p>')
            .addClass('text-danger hidden')
            .css({
                'margin-bottom': '1em'
            });

        this.$inputReason = $('<textarea>')
            .attr('placeholder', 'Reason for refund (optional)')
            .css({
                'width': '100%',
                'margin': 0
            });

        this.$modal = $('<div>')
            .append(this.$text)
            .append(this.$inputAmount)
            .append(this.$amountError)
            .append(this.$inputReason)
            .dialog({
                'title': 'Are you sure?',
                'resizable': false,
                'draggable': false,
                'closeable': false,
                'modal': true,
                'buttons': {
                    'OK': () => {

                        let amount = this.$inputAmount.val();
                        let reason = this.$inputReason.val();

                        this.adminController.log('Clicked OK', amount, reason);

                        if (amount <= 0) {
                            this.setErrorMessage('Refund amount must be greater than 0.');

                        } else if (amount > maxAmount / 100) {
                            this.setErrorMessage(`Refund amount must be no greater than ${maxAmountFormatted}.`);

                        } else {
                            this
                                .clearErrorMessage()
                                .buildForm(paymentId, amount, reason)
                                .submitForm();
                        }
                    },
                    'Cancel': () => {
                        this.closeModal();
                    }
                }
            });

        return this;
    }

    // --------------------------------------------------------------------------

    /**
     * Submits the form
     * @returns {Refund}
     */
    submitForm() {
        this.adminController.log('Submitting form');
        this.$modal.dialog('close');
        this.$modal = $('<div>')
            .html('<p>Submitting refund...</p>')
            .dialog({
                'title': 'Please wait',
                'resizable': false,
                'draggable': false,
                'modal': true,
                'dialogClass': 'no-close',
            });
        $('body').append(this.$form);
        this.$form.submit();
        return this;
    }

    // --------------------------------------------------------------------------

    /**
     * Closes the modal
     * @returns {Refund}
     */
    closeModal() {
        this.adminController.log('Closing modal');
        this.$modal.dialog('close');
        if (this.$form) {
            this.$form.remove();
            this.$form = null;
        }
        return this;
    }

    // --------------------------------------------------------------------------

    /**
     * Builds a form to submit
     * @param paymentId
     * @param amount
     * @param reason
     * @returns {Refund}
     */
    buildForm(paymentId, amount, reason) {
        this.$form = $('<form>')
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

        return this;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the error message
     * @param msg
     * @returns {Refund}
     */
    setErrorMessage(msg) {

        this.adminController.warn(msg);

        this.$amountError
            .text(msg)
            .removeClass('hidden');

        return this;
    }

    // --------------------------------------------------------------------------

    /**
     * Clears any error messages
     * @returns {Refund}
     */
    clearErrorMessage() {

        this.$amountError
            .addClass('hidden');

        return this;
    }
}

export default Refund;
