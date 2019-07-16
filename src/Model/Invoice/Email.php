<?php

/**
 * Payment model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model\Invoice;

use Nails\Common\Model\Base;
use Nails\Factory;

class Email extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->table             = NAILS_DB_PREFIX . 'invoice_email';
        $this->defaultSortColumn = 'created';
    }

    // --------------------------------------------------------------------------

    /**
     * This method applies the conditionals which are common across the get_*()
     * methods and the count() method.
     *
     * @param array $aData Data passed from the calling method
     *
     * @return void
     **/
    protected function getCountCommon(array $aData = []): void
    {
        if (empty($aData['select'])) {
            $aData['select'] = [
                $this->getTableAlias() . '.*',
                'ea.ref email_ref',
            ];
        }

        //  Common joins
        $oDb = Factory::service('Database');
        $oDb->join(NAILS_DB_PREFIX . 'email_archive ea', $this->getTableAlias() . '.email_id = ea.id', 'LEFT');

        parent::getCountCommon($aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The getAll() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param  object $oObj      A reference to the object being formatted.
     * @param  array  $aData     The same data array which is passed to _getcount_common, for reference if needed
     * @param  array  $aIntegers Fields which should be cast as integers if numerical and not null
     * @param  array  $aBools    Fields which should be cast as booleans if not null
     * @param  array  $aFloats   Fields which should be cast as floats if not null
     *
     * @return void
     */
    protected function formatObject(
        &$oObj,
        array $aData = [],
        array $aIntegers = [],
        array $aBools = [],
        array $aFloats = []
    ) {
        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);

        $oEmailer = factory::service('Emailer', 'nails/module-email');
        $aTypes   = $oEmailer->getTypes();

        $oEmail = (object) [
            'id'          => (int) $oObj->email_id ?: null,
            'ref'         => $oObj->email_ref,
            'type'        => (object) [
                'slug'  => $oObj->email_type,
                'label' => '',
            ],
            'preview_url' => $oObj->email_id ? siteUrl('email/view/' . $oObj->email_ref) : null,
        ];

        if (!empty($aTypes[$oEmail->type->slug])) {
            $oEmail->type->label = $aTypes[$oEmail->type->slug]->name;
        } else {
            $oEmail->type->label = preg_replace('/[-_]/', ' ', $oEmail->type->slug);
        }

        $oObj->email = $oEmail;

        unset($oObj->email_id);
        unset($oObj->email_type);
    }
}
