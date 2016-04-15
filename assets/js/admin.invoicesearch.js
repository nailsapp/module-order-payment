var _INVOICE_INVOICESEARCH;
_INVOICE_INVOICESEARCH = function()
{
    var base = this;

    // --------------------------------------------------------------------------

    base.__construct =function() {

        $('input.invoice-search').select2({
            placeholder: "Search for an invoice",
            minimumInputLength: 3,
            ajax: {
                url: window.SITE_URL + 'api/invoice/invoice/search',
                dataType: 'json',
                quietMillis: 250,
                data: function (term) {
                    return {
                        keywords: term
                    };
                },
                results: function (data) {
                    var out = {
                        'results': []
                    };

                    for (var key in data.data) {
                        if (data.data.hasOwnProperty(key)) {
                            out.results.push({
                                'id': data.data[key].id,
                                'text': data.data[key].ref + ' - ' + data.data[key].state.label
                            });
                        }
                    }

                    return out;
                },
                cache: true
            },
            initSelection: function(element, callback) {

                var id = $(element).val();

                if (id !== '') {

                    $.ajax({
                        url: window.SITE_URL + 'api/invoice/invoice/id',
                        data: {
                            'id': id
                        },
                        dataType: 'json'
                    }).done(function(data) {

                        var out = {
                            'id': data.data.id,
                            'text': data.data.ref + ' - ' + data.data.state.label
                        };

                        callback(out);
                    });
                }
            }
        });
    };

    // --------------------------------------------------------------------------

    return base.__construct();
}();