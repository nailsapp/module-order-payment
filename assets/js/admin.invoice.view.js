function InvoiceView() {
    var base = this;

    // --------------------------------------------------------------------------

    base.__construct = function() {
        base.initRefund();
    };

    // --------------------------------------------------------------------------

    base.initRefund = function() {
        $('.js-confirm-refund').on('click', function() {

            var href, max, max_formatted, return_to, msg;

            href = $(this).attr('href');
            max = $(this).data('max');
            max_formatted = $(this).data('max-formatted');
            return_to = $(this).data('return-to');

            msg = 'Please confirm refund amount and reason:';
            msg += '<br /><br /><input type="text" id="refund-amount" value="' + max / 100 + '" style="width: 100%">';
            msg += '<div id="refund-amount-error" class="text-danger hidden" style="margin-bottom: 1em;"></div>';
            msg += '<textarea id="refund-reason" placeholder="Reason for refund (optional)" style="width: 100%; margin: 0;"></textarea>';

            $('<div>').html(msg).dialog({
                'title': 'Are you sure?',
                'resizable': false,
                'draggable': false,
                'modal': true,
                'buttons': {
                    'OK': function() {

                        var amount = $('#refund-amount').val();
                        var reason = $('#refund-reason').val();

                        if (amount <= 0) {

                            $('#refund-amount-error')
                                .text('Refund amount must be greater than 0.')
                                .removeClass('hidden');

                        } else if (amount > max / 100) {

                            $('#refund-amount-error')
                                .text('Refund amount must be no greater than ' + max_formatted + '.')
                                .removeClass('hidden');

                        } else {

                            $('#refund-amount-error').addClass('hidden');

                            var form = $('<form>').attr({'method': 'POST', 'action': href});

                            form.append($('<input>').attr({'name': 'amount', 'value': amount}));
                            form.append($('<input>').attr({'name': 'reason', 'value': reason}));
                            form.append($('<input>').attr({'name': 'return_to', 'value': return_to}));

                            $('body').append(form);

                            form.submit();
                            $(this).dialog('close');
                        }
                    },
                    'Cancel': function() {
                        $(this).dialog('close');
                    }
                }
            });

            return false;
        });
    };

    // --------------------------------------------------------------------------

    return base.__construct();
};

var _INVOICE_VIEW = new InvoiceView();
