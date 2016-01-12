<?php

/**
 * View saved addresses
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    controller
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;

class Address extends NAILS_Controller
{
    public function index()
    {
        $this->load->view('structure/header', $this->data);
        $this->load->view('invoice/address/index', $this->data);
        $this->load->view('structure/footer', $this->data);
    }

    // --------------------------------------------------------------------------

    public function add()
    {
        $this->session->set_flashdata('message', '@todo');
        $sReturn = $this->input->get('return') ?: 'invoice/address';
        redirect($sReturn);
    }

    // --------------------------------------------------------------------------

    public function delete()
    {
        $this->session->set_flashdata('message', '@todo');
        $sReturn = $this->input->get('return') ?: 'invoice/address';
        redirect($sReturn);
    }
}
