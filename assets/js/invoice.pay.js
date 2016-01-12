/* exported invoicePay */
var invoicePay = function() {
    var base = this;

    // --------------------------------------------------------------------------

    base.__construct = function() {

        //  Bind to section buttons
        $('.js-goto-section').on('click', function() {
            base.gotoSection($(this).data('section'));
        });

        //  Toggle add card fields
        $('.js-card-select input').on('click', function() {

            if ($(this).val() === 'NEW') {
                $('#js-add-card').removeClass('hidden');
            } else {
                $('#js-add-card').addClass('hidden');
            }

        });

        $('.js-card-select input:checked').trigger('click');

        //  Resize iframe
        $('#js-view-invoice').iFrameResize();

        //  Format fields
        $('#js-cc-num').payment('formatCardNumber');
        $('#js-cc-exp').payment('formatCardExpiry');
        $('#js-cc-cvc').payment('formatCardCVC');

        //  Validate form
        $('#js-main-form').on('submit', function() {
            return base.validate();
        });

        return base;
    };

    // --------------------------------------------------------------------------

    base.gotoSection = function(section) {

        $('.js-section .panel-collapse').addClass('collapse');
        $('#js-panel-' + section + ' .panel-collapse').removeClass('collapse');
    };

    // --------------------------------------------------------------------------

    base.validate = function() {

        var isValid = true;

        //  js-panel-payment-method
        if ($('#js-panel-payment-method').length) {
            //  A payment method has been chosen
            //  @todo
            console.log('Validate payment method');
        }

        //  js-panel-payment-details
        if ($('#js-panel-payment-details').length) {

            if ($('.js-card-select').length) {
                //  If there are saved cards then one must be chosen
                if (!$('input[name=cc_saved]:checked').length) {
                    isValid = false;
                }

                if (!isValid) {
                    $('#js-saved-cards').addClass('shake');
                    setTimeout(function() { $('#js-saved-cards').removeClass('shake'); }, 500);
                }
            }

            if (!$('.js-card-select').length || $('input[name=cc_saved]:checked').val() === 'NEW') {

                //  If there are no saved cards then the credit card form must be validated
                var ccName = $('#js-cc-name').val().trim();
                var ccNum  = $('#js-cc-num').val().trim();
                var ccExp  = $('#js-cc-exp').val().trim().split('/');
                var ccCvc  = $('#js-cc-cvc').val().trim();

                if (!$.payment.validateCardNumber(ccNum)) {
                    isValid = false;
                    $('#js-cc-num').closest('.form-group').addClass('has-error');
                } else {
                    $('#js-cc-num').closest('.form-group').removeClass('has-error');
                }

                if (!ccName.length) {
                    isValid = false;
                    $('#js-cc-name').closest('.form-group').addClass('has-error');
                } else {
                    $('#js-cc-name').closest('.form-group').removeClass('has-error');
                }

                if (!$.payment.validateCardExpiry(ccExp[0], ccExp[1])) {
                    isValid = false;
                    $('#js-cc-exp').closest('.form-group').addClass('has-error');
                } else {
                    $('#js-cc-exp').closest('.form-group').removeClass('has-error');
                }

                if (!$.payment.validateCardCVC(ccCvc)) {
                    isValid = false;
                    $('#js-cc-cvc').closest('.form-group').addClass('has-error');
                } else {
                    $('#js-cc-cvc').closest('.form-group').removeClass('has-error');
                }

                if (!isValid) {
                    $('#js-add-card').addClass('shake');
                    setTimeout(function() { $('#js-add-card').removeClass('shake'); }, 500);
                }
            }
        }

        return isValid;
    };

    // --------------------------------------------------------------------------

    return base.__construct();
}();