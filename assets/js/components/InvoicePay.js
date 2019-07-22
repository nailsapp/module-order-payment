class InvoicePay {
    constructor() {

        //  Bind to driver selection
        $('.js-invoice-driver-select input')
            .on('click', (e) => {

                let $el = $(e.currentTarget);

                //  Highlight selection
                $('.js-invoice-driver-select.active')
                    .removeClass('active');

                $el
                    .closest('.js-invoice-driver-select')
                    .addClass('active');

                //  Show payment fields
                $('.js-invoice-panel-payment-details')
                    .addClass('hidden');

                if ($el.data('is-card')) {
                    $('#js-invoice-panel-payment-details-card')
                        .removeClass('hidden');
                } else {
                    $('.js-invoice-panel-payment-details[data-driver="' + $el.data('driver') + '"]')
                        .removeClass('hidden');
                }

                //  Update button
                let btnString = $el.data('is-redirect') ? 'Continue' : 'Pay Now';

                $('#js-invoice-pay-now')
                    .removeClass('btn--warning btn--disabled')
                    .text(btnString);

            });

        $('.js-invoice-driver-select input:checked')
            .trigger('click');

        //  Card input formatting
        $('.js-invoice-cc-num').payment('formatCardNumber');
        $('.js-invoice-cc-exp').payment('formatCardExpiry');
        $('.js-invoice-cc-cvc').payment('formatCardCVC');

        //  CVC Card type formatting
        $('.js-invoice-cc-num')
            .on('keyup', (e) => {

                let $el = $(e.currentTarget);
                let cardNum = $.trim($el.val());
                let cardType = $.payment.cardType(cardNum);
                let cardCvc = $('.js-invoice-cc-cvc');

                cardCvc.removeClass('amex other');
                $el.removeClass('has-error');

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
            })
            .trigger('keyup');

        //  Validation
        $('#js-invoice-main-form')
            .on('submit', function() {

                let isValid = true;

                //  Hide errors
                $('#js-error')
                    .addClass('hidden');

                //  Driver selected
                let selectedDriver = $('.js-invoice-driver-select input:checked');
                if (selectedDriver.length !== 0) {

                    $('.js-invoice-panel-payment-details:not(.hidden) :input')
                        .each((index, element) => {

                            let $el = $(element);
                            let val = $.trim($el.val());

                            if ($el.data('is-required') && val.length === 0) {
                                isValid = false;
                                $el.closest('.form__group').addClass('has-error');
                            }

                            if ($el.data('cc-num') && !$.payment.validateCardNumber(val)) {
                                isValid = false;
                                $el.closest('.form__group').addClass('has-error');
                            }

                            if ($el.data('cc-exp')) {
                                let expObj = $.payment.cardExpiryVal(val);
                                if (!$.payment.validateCardExpiry(expObj.month, expObj.year)) {

                                    isValid = false;
                                    $el.closest('.form__group').addClass('has-error');
                                }
                            }

                            if ($el.data('cc-cvc') && !$.payment.validateCardCVC(val)) {
                                isValid = false;
                                $el.closest('.form__group').addClass('has-error');
                            }

                            if (!isValid) {
                                $('#js-error')
                                    .html('Please check all fields')
                                    .removeClass('hidden');
                            }
                        });

                } else {

                    isValid = false;
                    $('#js-error')
                        .html('Please select an option')
                        .removeClass('hidden');
                }

                if (!isValid) {
                    $('#js-invoice').addClass('shake');
                    setTimeout(() => {
                        $('#js-invoice').removeClass('shake');
                    }, 500);
                } else {
                    $('#js-invoice').addClass('masked');
                }

                return isValid;
            });
    }
}

export default InvoicePay;
