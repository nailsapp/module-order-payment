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

namespace Nails\Invoice\Model;

use Nails\Factory;
use Nails\Common\Model\Base;

class InvoiceEmail extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->table             = NAILS_DB_PREFIX . 'invoice_email';
        $this->tablePrefix       = 'ie';
        $this->defaultSortColumn = 'created';
    }

    // --------------------------------------------------------------------------

    /**
     * This method applies the conditionals which are common across the get_*()
     * methods and the count() method.
     * @param array  $aData   Data passed from the calling method
     * @return void
     **/
    protected function getCountCommon($aData = array())
    {
        if (empty($aData['select'])) {
            $aData['select'] = array(
                $this->tablePrefix . '.*',
                'ea.ref email_ref'
            );
        }

        //  Common joins
        $oDb = Factory::service('Database');
        $oDb->join(NAILS_DB_PREFIX . 'email_archive ea', $this->tablePrefix . '.email_id = ea.id', 'LEFT');

        parent::getCountCommon($aData);
    }

    // --------------------------------------------------------------------------

    protected function formatObject($oObj)
    {
        parent::formatObject($oObj);

        $oEmailer = factory::service('Emailer', 'nailsapp/module-email');
        $aTypes   = $oEmailer->getTypes();

        $oEmail              = new \stdClass();
        $oEmail->id          = (int) $oObj->email_id ?: null;
        $oEmail->ref         = $oObj->email_ref;
        $oEmail->type        = new \stdClass();
        $oEmail->type->slug  = $oObj->email_type;
        $oEmail->type->label = '';
        $oEmail->preview_url = $oEmail->id ? site_url('email/view_online/' . $oEmail->ref) : null;

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
