<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Slots extends MY_Controller {



	public function __construct(){



		parent::__construct();

		auth_check(); // check login auth

		$this->rbac->check_module_access();



		$this->load->model('admin/slot_model', 'slot_model');

	}

	public function index(){

		$this->load->view('admin/includes/_header');

		$this->load->view('admin/slot/slot_list');

		$this->load->view('admin/includes/_footer');

	}

    public function datatable_json(){				   					   

		$records['data'] = $this->slot_model->get_all_slots();
		$data = array();
		$i=0;
		foreach ($records['data']   as $row) 

		{  

			$place_info = "<b>Place Name : </b>".$row["placename"]."<br>"."<b>Place Address : </b>".$row["place_address"];
			$slot_info = "<b>Slot Name : </b>".$row["slot_name"]."<br>"."<b>Display Id : </b>".$row["display_id"];
			$status = ($row['status'] == 1)? 'checked': '';
			$data[]= array(

				++$i,
				$place_info,
				$row['state_name'],
				$row['city_name'],
				$slot_info,
				$row['machine_id'],
				'<input class="tgl_checkbox tgl-ios" 
				data-id="'.$row['slot_no'].'" 
				id="cb_'.$row['slot_no'].'"
				type="checkbox"  
				'.$status.'><label for="cb_'.$row['slot_no'].'"></label>'
			);

		}
		$records['data']=$data;
		echo json_encode($records);						   
	}

	function change_status(){
		echo "<pre>";
		print_r($_POST);
		die;
	}





	



	



	



	



}





?>