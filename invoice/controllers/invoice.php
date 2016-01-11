<?php

/**
 * View invoices
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    controller
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;
use Nails\Common\Exception\NailsException;
use Nails\Invoice\Exception\DriverException;

class Invoice extends NAILS_Controller
{
    /**
     * Download a single invoice
     * @param  object $oInvoice The invoice object
     * @return void
     */
    protected function download($oInvoice)
    {
        dump('Download Invoice');
        dump($oInvoice);
    }

    // --------------------------------------------------------------------------

    /**
     * View a single invoice
     * @param  object $oInvoice The invoice object
     * @return void
     */
    protected function view($oInvoice)
    {
        $this->data['oInvoice'] = $oInvoice;

        // --------------------------------------------------------------------------

        if ($this->input->get('autosize')) {

            $oAsset = Factory::service('Asset');
            $oAsset->load(
                'iframe-resizer/js/iframeResizer.contentWindow.min.js',
                array('nailsapp/module-invoice', 'BOWER')
            );
        }

        // --------------------------------------------------------------------------

        $this->data['headerOverride'] = 'structure/header/blank';
        $this->data['footerOverride'] = 'structure/footer/blank';

        // --------------------------------------------------------------------------

        $this->load->view('structure/header', $this->data);
        $this->load->view('invoice/view/index', $this->data);
        $this->load->view('structure/footer', $this->data);
    }

    // --------------------------------------------------------------------------

    /**
     * Pay a single invoice
     * @param  object $oInvoice The invoice object
     * @return void
     */
    protected function pay($oInvoice)
    {
        //  Only open invoice can be paid
        if ($oInvoice->state->id !== 'OPEN' && !$this->input->isScheduled) {
            show_404();
        }

        //  If a user ID is specified, then the user must be logged in as that user
        if (!empty($oInvoice->user->id) && $oInvoice->user->id != activeUser('id')) {
            unauthorised();
        }

        $this->data['oInvoice']   = $oInvoice;
        $this->data['sUrlCancel'] = $this->input->get('cancel') ?: site_url();

        // --------------------------------------------------------------------------

        //  Payment drivers
        $oPaymentDriverModel    = Factory::model('PaymentDriver', 'nailsapp/module-invoice');
        $this->data['aDrivers'] = $oPaymentDriverModel->getEnabled();

        if (empty($this->data['aDrivers'])) {
            throw new DriverException('No enabled payment drivers', 1);
        }

        // --------------------------------------------------------------------------

        //  Saved cards
        if (isLoggedIn()) {

            $oUserMeta = Factory::model('UserMeta', 'nailsapp/module-auth');
            $oNow      = Factory::factory('DateTime');

            $this->data['aCardsFlat'] = array();
            $this->data['aCards']     = $oUserMeta->getMany(
                NAILS_DB_PREFIX . 'user_meta_invoice_card',
                activeUser('id')
            );

            foreach ($this->data['aCards'] as $oCard) {

                $oCard->label_formatted = $oCard->label . ' (' . $oCard->last_four . ')';

                //  Expired?
                $oExpiry          = new \DateTime($oCard->expiry);
                $oCard->isExpired = $oNow > $oExpiry;
            }

        } else {

            $this->data['aCards'] = array();
        }

        // --------------------------------------------------------------------------

        if ($this->input->post()) {

            $oFormValidation = Factory::service('FormValidation');

            if ($this->input->post('cc_saved') === 'NEW') {

                $oFormValidation->set_rules('cc_saved', '', '');
                $oFormValidation->set_rules('cc_name', '', 'xss_clean|required');
                $oFormValidation->set_rules('cc_num', '', 'xss_clean|required');
                $oFormValidation->set_rules('cc_exp', '', 'xss_clean|required');
                $oFormValidation->set_rules('cc_cvc', '', 'xss_clean|required');
                $oFormValidation->set_rules('cc_save', '', '');

            } else {

                $oFormValidation->set_rules('cc_saved', '', 'xss_clean|is_natural_no_zero');
            }

            $oFormValidation->set_message('required', lang('fv_required'));
            $oFormValidation->set_message('is_natural_no_zero', lang('fv_required'));

            if ($oFormValidation->run()) {

                try {

                    //  Validate the driver
                    $oDriver = false;
                    foreach ($this->data['aDrivers'] as $oDriverConfig) {
                        if ($oDriverConfig->slug == $this->input->post('driver')) {
                            $oDriver = $oDriverConfig;
                            break;
                        }
                    }

                    if (empty($oDriver)) {
                        throw new NailsException('Invalid Payment Driver', 1);
                    }

                    //  Set up card object
                    $oCard = Factory::factory('Card', 'nailsapp/module-invoice');

                    if ($this->input->post('cc_saved')) {

                        //  Lookup card
                        $oSavedCard = null;
                        foreach ($this->data['aCards'] as $oSavedCardConfig) {
                            if ($oSavedCardConfig->id == $this->input->post('cc_saved')) {
                                $oSavedCard = $oSavedCardConfig;
                                break;
                            }
                        }

                        if (empty($oSavedCard)) {
                            throw new NailsException('Invalid Saved Card', 1);
                        }

                        $oCard->setToken($oSavedCard->token);

                    } else {

                        $oCard->setName($this->input->post('cc_name'));
                        $oCard->setNumber($this->input->post('cc_num'));
                        $oCard->setExpiry($this->input->post('cc_exp'));
                        $oCard->setCvc($this->input->post('cc_cvc'));
                    }

                    //  Attempt payment
                    $oResult = $oCard->charge(
                        $oInvoice->totals->base->grand,
                        $oInvoice->currency,
                        $oDriver->slug
                    );

                    dumpanddie($oResult);

                    //  Handle saving the card, if needed
                    //  @todo

                    //  Handle redirect
                    //  If a redirect is required then send the user on their way, if no redirect is required then
                    //  send the user to the payment processing page

                    if (!empty($oResult->isRedirect)) {

                    } else {


                    }

                } catch (\Exception $e) {

                    $this->data['error'] = $e->getMessage();
                }

            } else {

                $this->data['error'] = lang('fv_there_were_errors');
            }
        }

        // --------------------------------------------------------------------------

        $oAsset = Factory::service('Asset');
        $oAsset->load('iframe-resizer/js/iframeResizer.min.js', array('nailsapp/module-invoice', 'BOWER'));
        $oAsset->load('jquery.payment/lib/jquery.payment.js', array('nailsapp/module-invoice', 'BOWER'));
        $oAsset->load('invoice.pay.min.js', 'nailsapp/module-invoice');
        $oAsset->load('invoice.pay.css', 'nailsapp/module-invoice');

        // --------------------------------------------------------------------------

        $this->load->view('structure/header', $this->data);
        $this->load->view('invoice/pay/index', $this->data);
        $this->load->view('structure/footer', $this->data);
    }

    // --------------------------------------------------------------------------

    /**
     * Remaps all requests to the viewInvoice method unless the *real* method exists
     * @return void
     */
    public function _remap()
    {
        $sInvoiceRef   = $this->uri->rsegment(2);
        $sInvoiceToken = $this->uri->rsegment(3);
        $sMethod       = $this->uri->rsegment(4) ?: 'view';

        //  @todo verify invoice and token
        $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');
        $oInvoice      = $oInvoiceModel->getByRef($sInvoiceRef);

        if (empty($oInvoice) || $sInvoiceToken !== $oInvoice->token || !method_exists($this, $sMethod)) {
            show_404();
        }

        $this->{$sMethod}($oInvoice);
    }
}
