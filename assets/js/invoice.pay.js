var invoicePay = function() {
    var base = this;

    // --------------------------------------------------------------------------

    base.__construct = function() {

        $('.js-goto-section').on('click', function() {
            base.gotoSection($(this).data('section'));
        });

        $('.js-card-select input').on('click', function() {

            if ($(this).val() === 'NEW') {
                $('#js-add-card').removeClass('hidden');
            } else {
                $('#js-add-card').addClass('hidden');
            }
        }).trigger('click');

        $('#js-view-invoice').iFrameResize();

        $('#js-cc-num').payment('formatCardNumber');
        $('#js-cc-exp').payment('formatCardExpiry');
        $('#js-cc-cvc').payment('formatCardCVC');

        return base;
    };

    // --------------------------------------------------------------------------

    base.gotoSection = function(section) {

        $('.js-section .panel-collapse').addClass('collapse');
        $('#js-panel-' + section + ' .panel-collapse').removeClass('collapse')
    };

    // --------------------------------------------------------------------------

    return base.__construct();
}();