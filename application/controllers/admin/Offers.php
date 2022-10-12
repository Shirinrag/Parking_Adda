<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Offers extends MY_Controller {

	public function __construct(){
		parent::__construct();
		auth_check(); // check login auth
		$this->rbac->check_module_access();
		$this->load->model('admin/offers_model', 'offers_model');
	}

	public function index(){

		$this->load->view('admin/includes/_header');
		$this->load->view('admin/offers/offers_list');
		$this->load->view('admin/includes/_footer');
	}
	
    public function datatable_json(){				   					   
		$records['data'] = $this->offers_model->get_all_offers();
		$data = array();
		$i=0;
		foreach ($records['data'] as $row) 
		{  
            $status = ($row['is_active'] == 1)? 'checked': '';
			if($row["offertype"] == 1){
				$offertype = '<span class="badge badge-primary">Per Hour</span>';
			}
			else{
				$offertype = '<span class="badge badge-warning">Percentage</span>';
			}

			$offerform = date("d-m-Y H:i a", strtotime($row['fromDate'].$row['fromtime']));
			$offerto = date("d-m-Y H:i a", strtotime($row['toDate'].$row['tottime']));

			$data[]= array(
				++$i,
				$row['offerText'],
				$row["offerDesc"],
				$offerform,
				$offerto,
				$offertype,
				'<input class="tgl_checkbox tgl-ios" 
				data-id="'.$row['id'].'" 
				id="cb_'.$row['id'].'"
				type="checkbox"  
				'.$status.'><label for="cb_'.$row['id'].'"></label>'
			);
		}
		$records['data']=$data;
		echo json_encode($records);						   
	}
	
	function change_status(){
	    
	$this->offers_model->change_status();
	
	}

}


?>