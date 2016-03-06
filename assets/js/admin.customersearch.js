var _INVOICE_CUSTOMERSEARCH;
_INVOICE_CUSTOMERSEARCH = function()
{
    var base = this;

    // --------------------------------------------------------------------------

    base.__construct =function() {

        $('input.customer-search').select2({
            placeholder: "Search for a customer",
            minimumInputLength: 3,
            ajax: {
                url: window.SITE_URL + 'api/invoice/customer/search',
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
                                'text': data.data[key].label
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
                        url: window.SITE_URL + 'api/invoice/customer/id',
                        data: {
                            'id': id
                        },
                        dataType: 'json'
                    }).done(function(data) {
                        console.log('init', data);
                        var out = {
                            'id': data.data.id,
                            'text': data.data.label
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