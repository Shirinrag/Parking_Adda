<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Wallet extends MY_Controller {

	
	public function __construct(){

		parent::__construct();
		auth_check(); // check login auth
		$this->load->model('admin/Wallet_model');  // Load wallet model

		
	}
    
    public function index(){
        redirect(base_url('admin/wallet/wallet'),'refresh');
    }
	public function wallet(){

		$data['title'] = '';
		$data['todays_earning'] = $this->Wallet_model->todays_earning();
		$data['total_users'] = $this->Wallet_model->total_users();
		$data['refundable_amount'] = $this->Wallet_model->total_refundable();
		$data['table_data'] = $this->Wallet_model->table_data();
		
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/wallet/wallet_system', $data);
		$this->load->view('admin/includes/_footer');
	}
	public function Add_money(){

		$data['title'] = '';

		$this->load->view('admin/includes/_header');
		$this->load->view('admin/wallet/add_money', $data);
		$this->load->view('admin/includes/_footer');
	}
	
	
	public function datatable_json(){				   					   
		$records['data'] = $this->Wallet_model->table_data();
		$data = array();
         
		$i=0;
		foreach ($records['data']   as $row) 
		{  
		    $modified_date = date('Y:m:d', strtotime($row['onUpdated']));
			$status = ($row['is_active'] == 1)? 'checked': '';
			
			$data[]= array(
				++$i,
				$modified_date,
				$row['username'],
				$row['amount'],
				'<input class="tgl_checkbox tgl-ios" 
				data-id="'.$row['id'].'" 
				id="cb_'.$row['id'].'"
				type="checkbox"  
				'.$status.'><label for="cb_'.$row['id'].'"></label>',		

				'<a title="View" class="view btn btn-sm btn-info" href="'.base_url('admin/wallet/edit/'.$row['id']).'"> <i class="fa fa-eye"></i></a>
				<a title="Edit" class="update btn btn-sm btn-warning" href="'.base_url('admin/wallet/edit/'.$row['id']).'"> <i class="fa fa-pencil-square-o"></i></a>
				<a title="Delete" class="delete btn btn-sm btn-danger" href='.base_url("admin/wallet/delete/".$row['id']).' title="Delete" onclick="return confirm(\'Do you want to delete ?\')"> <i class="fa fa-trash-o"></i></a>'
			);
		}
		$records['data']=$data;
		echo json_encode($records);						   
	}
	
}

?>
