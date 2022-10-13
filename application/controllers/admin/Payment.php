<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Payment extends MY_Controller {
    public function __construct() {
        parent::__construct();
        auth_check(); // check login auth
        
    }
    public function all_payment() {
        $data['title'] = '';
        $this->load->view('admin/includes/_header');
        $this->load->view('admin/payment/payment_list', $data);
        $this->load->view('admin/includes/_footer');
    }
    public function add_payment() {
        $data['title'] = '';
        $this->load->view('admin/includes/_header');
        $this->load->view('admin/payment/add_payment', $data);
        $this->load->view('admin/includes/_footer');
    }
    public function successfully() {
        $data['title'] = '';
        $this->load->view('admin/includes/_header');
        $this->load->view('admin/payment/success_payment', $data);
        $this->load->view('admin/includes/_footer');
    }
    public function failed() {
        $data['title'] = '';
        $this->load->view('admin/includes/_header');
        $this->load->view('admin/payment/failed_payment', $data);
        $this->load->view('admin/includes/_footer');
    }
}
?>

