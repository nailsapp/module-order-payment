<?php

/**
 * This model handles interactions with the app's "nails_invoice_source" table.
 *
 * @package  Nails\Invoice\Model
 * @category model
 */

namespace Nails\Invoice\Model;

use Nails\Common\Model\Base;

class Source extends Base
{
    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = NAILS_DB_PREFIX . 'invoice_source';

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = 'Source';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = 'nails/module-invoice';
}
