/* exported invoicePay */
var invoicePay = function() {
    var base = this;

    // --------------------------------------------------------------------------

    base.__construct = function() {

        //  Bind to driver selection
        $('.js-invoice-driver-select input').on('click', function() {

            //  Highlight selection
            $('.js-invoice-driver-select.active').removeClass('active');
            $(this).closest('.js-invoice-driver-select').addClass('active');

            //  Show payment fields
            $('.js-invoice-panel-payment-details').addClass('hidden');
            if ($(this).data('is-card')) {

                $('#js-invoice-panel-payment-details-card')
                    .removeClass('hidden');

            } else {

                $('.js-invoice-panel-payment-details[data-driver="' + $(this).data('driver') + '"]')
                    .removeClass('hidden');
            }

            //  Update button
            var btnString = $(this).data('is-redirect') ? 'Continue' : 'Pay Now';
            $('#js-invoice-pay-now')
                .removeClass('btn--warning btn--disabled')
                .text(btnString);

            //  Hide any errors
            $('#js-invoice-driver-select + .alert--danger').remove();
            $('.js-invoice-panel-payment-details input + .alert--danger').remove();

        });
        $('.js-invoice-driver-select input:checked').trigger('click');

        //  Card input formatting
        $('.js-invoice-cc-num').payment('formatCardNumber');
        $('.js-invoice-cc-exp').payment('formatCardExpiry');
        $('.js-invoice-cc-cvc').payment('formatCardCVC');

        //  CVC Card type formatting
        $('.js-invoice-cc-num').on('keyup', function() {

            var cardNum = $(this).val().trim();
            var cardType = $.payment.cardType(cardNum);
            var cardCvc = $('.js-invoice-cc-cvc');

            cardCvc.removeClass('amex other');

            if (cardNum.length > 0) {

                switch (cardType) {
                    case 'amex':
                        cardCvc.addClass('amex');
                        break;

                    default:
                        cardCvc.addClass('other');
                        break;
                }
            }
        });

        $('.js-invoice-cc-num').trigger('keyup');

        //  Validation
        $('#js-invoice-main-form').on('submit', function() {

            var isValid = true;

            //  Hide errors
            $('#js-invoice-driver-select + .alert--danger').remove();
            $('.js-invoice-panel-payment-details input').removeClass('has-error');

            //  Driver selected
            var selectedDriver = $('.js-invoice-driver-select input:checked');
            if (selectedDriver.length !== 0) {

                $('.js-invoice-panel-payment-details:not(.hidden) :input').each(function() {

                    var val = $(this).val().trim();

                    if ($(this).data('is-required') && val.length === 0) {

                        isValid = false;
                        $(this).addClass('has-error');
                    }

                    if ($(this).data('cc-num') && !$.payment.validateCardNumber(val)) {

                        isValid = false;
                        $(this).addClass('has-error');
                    }

                    if ($(this).data('cc-exp')) {
                        var expObj = $.payment.cardExpiryVal(val);
                        if (!$.payment.validateCardExpiry(expObj.month, expObj.year)) {

                            isValid = false;
                            $(this).addClass('has-error');
                        }
                    }

                    if ($(this).data('cc-cvc') && !$.payment.validateCardCVC(val)) {

                        isValid = false;
                        $(this).addClass('has-error');
                    }

                });

            } else {

                isValid = false;
                $('#js-invoice-driver-select').after('<p class="alert alert--danger">Please select an option.</p>');
            }

            if (!isValid) {
                $('#js-invoice').addClass('shake');
                setTimeout(function() {
                    $('#js-invoice').removeClass('shake');
                }, 500);
            } else {
                $('#js-invoice').addClass('masked');
            }

            return isValid;
        });

        return base;
    };
    // --------------------------------------------------------------------------

    return base.__construct();
}();
