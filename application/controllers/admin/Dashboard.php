<?php defined('BASEPATH') OR exit('No direct script access allowed');



class Dashboard extends My_Controller {



	public function __construct(){

		parent::__construct();

		auth_check(); // check login auth

		$this->rbac->check_module_access();

		if($this->uri->segment(3) != '')
		$this->rbac->check_operation_access();

		$this->load->model('admin/dashboard_model', 'dashboard_model');
        $this->load->model('admin/Complaint_model', 'complaint_model');
        $this->load->model('admin/Booking_model', 'booking_model');

	}

	//--------------------------------------------------------------------------

	public function index(){

		$data['title'] = 'Dashboard';

		$this->load->view('admin/includes/_header', $data);

    	$this->load->view('admin/dashboard/general');

    	$this->load->view('admin/includes/_footer');

	}

	//--------------------------------------------------------------------------

	public function index_1(){

		$data['all_users'] = $this->dashboard_model->get_all_users();

		$data['active_users'] = $this->dashboard_model->get_active_users();

		$data['deactive_users'] = $this->dashboard_model->get_deactive_users();

		$data['title'] = 'Dashboard';

		$this->load->view('admin/includes/_header', $data);

    	$this->load->view('admin/dashboard/index', $data);

    	$this->load->view('admin/includes/_footer');

	}



	//--------------------------------------------------------------------------

     public function index_2(){

		$data['title'] = 'Dashboard';
		$datas = $this->dashboard_model->getPlaceData();
		$data['piechart'] = $this->dashboard_model->getPieChartData();
		$data['counts'] = $this->complaint_model->getComplaintsCounts();
		$data['PendingVerifications'] = $this->complaint_model->getSlotsInfoById('P');
		$data['followUpdata'] = count($this->booking_model->getFollowUpBookingData());
		$placename ="";
		$earnings = ""; 

		foreach ($datas as $key => $value) {
			 $placename .= "'".substr($value['placename'],0,10).".. B- ".$value['total_bookings']."',"; 
			 $earnings .= $value['total_earning'].","; 
		}
		$data['places']  = rtrim($placename, ", ");
		$data['earnings'] = rtrim($earnings, ", ");
		$this->load->view('admin/includes/_header');
    	$this->load->view('admin/dashboard/index2',$data);
    	$this->load->view('admin/includes/_footer');



	}
	//--------------------------------------------------------------------------

	public function index_3(){

		$data['title'] = 'Dashboard';

		$this->load->view('admin/includes/_header');

    	$this->load->view('admin/dashboard/index3');

    	$this->load->view('admin/includes/_footer');

	}
	
	public function getDashboardData(){
		$data['all_users'] = $this->dashboard_model->getActiveUsers();
		$data['booking_info'] = $this->dashboard_model->getBookingsData();
		$data['active_places'] = $this->dashboard_model->getActivePlaces();
		$data['users_type'] = $this->dashboard_model->getUsersType();
// 		$data['pie_data'] = $this->dashboard_model->getBookingsInfo();
		echo json_encode($data);
	}
	


}
?>	