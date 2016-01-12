<?php

/**
 * View saved cards
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    controller
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;

class Card extends NAILS_Controller
{
    public function index()
    {
        $this->load->view('structure/header', $this->data);
        $this->load->view('invoice/card/index', $this->data);
        $this->load->view('structure/footer', $this->data);
    }

    // --------------------------------------------------------------------------

    public function add()
    {
        $this->session->set_flashdata('message', '@todo');
        $sReturn = $this->input->get('return') ?: 'invoice/card';
        redirect($sReturn);
    }

    // --------------------------------------------------------------------------

    public function delete()
    {
        $this->session->set_flashdata('message', '@todo');
        $sReturn = $this->input->get('return') ?: 'invoice/card';
        redirect($sReturn);
    }
}
