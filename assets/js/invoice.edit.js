/* globals ko, console, moment */
/* exported invoiceEdit*/
var invoiceEdit = function(units, taxes, items) {

    var base, key;

    /**
     * Avoid scope issues in callbacks and anonymous functions by referring to `this` as `base`
     * @type {Object}
     */
    base = this;

    // --------------------------------------------------------------------------

    /**
     * The unit types available
     * @type {observableArray}
     */

    base.units = ko.observableArray();
    for (key in units) {
        if (units.hasOwnProperty(key)) {
            base.units.push({
                'slug': key,
                'label': units[key]
            });
        }
    }

    // --------------------------------------------------------------------------

    /**
     * The taxes available
     * @type {observableArray}
     */
    base.taxes = ko.observableArray([{
        'id': '',
        'label': 'No Tax - 0%',
    }]);
    for (key in taxes) {
        if (taxes.hasOwnProperty(key)) {
            base.taxes.push({
                'id': taxes[key].id,
                'rate': taxes[key].rate,
                'label': taxes[key].label + ' - ' + taxes[key].rate + '%'
            });
        }
    }

    // --------------------------------------------------------------------------

    /**
     * The items attached to this invoice
     * @type {observableArray}
     */
    base.items = ko.observableArray();
    for (key in items) {
        if (items.hasOwnProperty(key)) {
            items[key].quantity  = ko.observable(items[key].quantity);
            items[key].unit_cost = ko.observable(items[key].unit_cost.localised);
            if (items[key].tax !== null) {
                items[key].tax_id = ko.observable(items[key].tax.id);
            } else {
                items[key].tax_id = ko.observable(null);
            }
            base.items.push(items[key]);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * The invoice's state
     * @type {Observable}
     */
    base.state = ko.observable('');

    // --------------------------------------------------------------------------

    /**
     * When the invoice is dated
     * @type {Observable}
     */
    base.dated = ko.observable('');

    // --------------------------------------------------------------------------

    /**
     * the invoices terms
     * @type {Observable}
     */
    base.terms = ko.observable('');

    // --------------------------------------------------------------------------

    base.addItem = function()
    {
        var item = {
            'id': null,
            'quantity': ko.observable(1),
            'unit': null,
            'label': '',
            'body': '',
            'unit_cost': ko.observable(null),
            'tax_id': ko.observable(null)
        };

        base.items.push(item);
    };

    // --------------------------------------------------------------------------

    base.removeItem = function()
    {
        base.items.remove(this);
    };

    // --------------------------------------------------------------------------

    base.moveUp = function()
    {
        var i = base.items.indexOf(this);
        if (i >= 1) {
            var array = base.items();
            base.items.splice(i-1, 2, array[i], array[i-1]);
        }
    };

    // --------------------------------------------------------------------------

    base.moveDown = function()
    {
        var i = base.items().indexOf(this);
        if (i < base.items().length - 1) {
            var rawNumbers = base.items();
            base.items.splice(i, 2, rawNumbers[i + 1], rawNumbers[i]);
        }
    };

    // --------------------------------------------------------------------------

    base.submitText = ko.computed(function() {

        if (base.state() === 'DRAFT') {

            return 'Save Draft';

        } else {

            var dated = moment(base.dated(), 'YYYY-MM-DD');

            if (dated.isAfter()) {

                return 'Schedule to Send';

            } else {

                return 'Send Now';
            }
        }
    });

    // --------------------------------------------------------------------------

    base.submitClass = ko.computed(function() {

        if (base.state() === 'DRAFT') {

            return 'btn btn-primary';

        } else {

            var dated = moment(base.dated(), 'YYYY-MM-DD');

            if (dated.isAfter()) {

                return 'btn btn-warning';

            } else {

                return 'btn btn-success';
            }
        }
    });

    // --------------------------------------------------------------------------

    base.stateChanged = function() {
        base.state($('#invoice-state').val());
    };

    // --------------------------------------------------------------------------

    base.dateChanged = function() {
        base.dated($('#invoice-dated').val());
    };

    // --------------------------------------------------------------------------

    base.termsChanged = function() {
        base.terms(parseInt($('#invoice-terms').val(), 10));
    };

    // --------------------------------------------------------------------------

    base.termsText = function() {

        var str;
        if (!base.terms()) {

            str = 'receipt';

        } else {

            var dueDate = moment(base.dated(), 'YYYY-MM-DD');

            if (dueDate.isValid()) {

                dueDate.add(base.terms(), 'd');
                str = dueDate.format('MMM Do, YYYY');

            } else {
                return '<b class="text-danger">Please select a valid date</b>';
            }
        }
        return 'Invoice will be due on <strong>' + str + '</strong>';
    };

    // --------------------------------------------------------------------------

    base.calculateSubTotal = ko.computed(function() {

        var total = 0;

        for (var i = base.items().length - 1; i >= 0; i--) {
            total += base.items()[i].quantity() * base.items()[i].unit_cost();
        }

        return total;
    });

    // --------------------------------------------------------------------------

    base.calculateTax = ko.computed(function() {

        var total = 0;

        for (var i = base.items().length - 1; i >= 0; i--) {
            if (base.items()[i].tax_id()) {
                for (var x = base.taxes().length - 1; x >= 0; x--) {
                    if (base.taxes()[x].id === base.items()[i].tax_id()) {
                        total += base.items()[i].quantity() * base.items()[i].unit_cost() * (base.taxes()[x].rate/100);
                    }
                }
            }
        }

        return total;
    });

    // --------------------------------------------------------------------------

    base.calculateGrandTotal = ko.computed(function() {

        var total = 0;
        total += base.calculateSubTotal();
        total += base.calculateTax();

        return total;
    });

    // --------------------------------------------------------------------------

    base.save = function() {

        if (base.state() === 'DRAFT') {

            return true;

        } else {

            var dated = moment(base.dated(), 'YYYY-MM-DD');

            if (dated.isAfter()) {

                return confirm('Save changes and Schedule the invoice to be sent on ' + dated.format('MMM Do, YYYY') + '?');

            } else {

                return confirm('Save changes and send invoice now?');
            }
        }
    };

    // --------------------------------------------------------------------------

    base.preview = function() {

        $('<div>').html('Preview functionality coming soon.').dialog({
            title: 'Work in Progress',
            resizable: false,
            draggable: false,
            modal: true,
            buttons:
            {
                OK: function() {
                    $(this).dialog('close');
                },
            }
        });
    };

    // --------------------------------------------------------------------------

    /**
     * Write a log to the console
     * @param  {String} message The message to log
     * @param  {Mixed}  payload Any additional data to display in the console
     * @return {Void}
     */
    base.log = function(message, payload)
    {
        if (typeof(console.log) === 'function') {

            if (payload !== undefined) {

                console.log('Invoice Edit:', message, payload);

            } else {

                console.log('Invoice Edit:', message);
            }
        }
    };

    // --------------------------------------------------------------------------

    /**
     * Write a warning to the console
     * @param  {String} message The message to warn
     * @param  {Mixed}  payload Any additional data to display in the console
     * @return {Void}
     */
    base.warn = function(message, payload)
    {
        if (typeof(console.warn) === 'function') {

            if (payload !== undefined) {

                console.warn('Invoire Edit:', message, payload);

            } else {

                console.warn('Invoire Edit:', message);
            }
        }
    };
};
