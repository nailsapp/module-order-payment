'use strict';

import '../sass/admin.scss';

import Refund from './components/Refund.js';

(function() {
    window.NAILS.ADMIN.registerPlugin(
        'nails/module-invoice',
        'Refund',
        function(controller) {
            return new Refund(controller);
        }
    );
})();
