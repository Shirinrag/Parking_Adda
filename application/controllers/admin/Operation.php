<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Operation extends MY_Controller {

	
	public function __construct(){

		parent::__construct();
		auth_check(); // check login auth
		$this->load->model('admin/legal_model', 'legal_model');
		$this->load->model('admin/ground_model', 'ground_model');
		$this->load->model('admin/operations_model', 'operations_model');
		
		
	}

	public function parking_places(){

		$data['title'] = '';
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/operations/parking_places', $data);
		$this->load->view('admin/includes/_footer');
	}

public function operations_datatable_json(){
	
		$records['data'] = $this->operations_model->getOperationsData();
		$data = array();
		$i=0;
		foreach ($records['data'] as $row) 
		{  
			$stage_id = $row['stage_id'];
			
			if($stage_id!='0' && $stage_id!='')
			{
			 $stage_name= $this->legal_model->getStageName($stage_id);
			 $view ='<a title="Edit" class="view btn btn-sm btn-warning" href='.base_url("admin/operation/view/".$row['id']).' ><i class="fa fa-edit"></i></a>';
			}else{
				$view = '<a title="Edit" class="view btn btn-sm btn-warning" href='.base_url("admin/ground/slotGenrates/".$row['id']).' ><i class="fa fa-edit"></i></a>';
			}

			$address = "<b>Address : </b>".$row['place_address']."<br>".
			"<b>City : </b>".$row['city_name'].","."&nbsp"."<b>Pincode :</b>".$row['pincode']."."."<br>".
			"<b>State : </b>".$row['state_name']."<br>".
			"<b>Country : </b>".$row['country_name']."<br>";
		   
			$data[]= array(
				++$i,
				$row['placename'],
				$address,
				$row['vendor_name'],
				$row['no_of_slots'],
				$view,
				
			);
		}
		
		$records['data']=$data;
		echo json_encode($records);	

	}

	public function view($id){
	    
	    
		$data['title'] = 'Operation Team';
        $data['legalInfo']=$this->legal_model->getDataById($id)[0];
        $data['slot_info']=count($this->ground_model->getSlotInfoById($id));
        $data['slot_details']=$this->ground_model->getSlotInfoById($id);
        $data['price_info']=$this->operations_model->getPriceInfoById($id);
        $data['daily_price_info']=$this->operations_model->getPriceInfoByIdType($id,1);
        $data['weekly_price_info']=$this->operations_model->getPriceInfoByIdType($id,2);
        $data['monthly_price_info']=$this->operations_model->getPriceInfoByIdType($id,3);
        $data['hourly']=$this->operations_model->getPriceInfoByIdType($id,0);
        // $data['verifier_info']=$this->operations_model->getVerifiers($id);
        
        // echo "<pre>";
        // print_r($data['verifier_info']);
        // die;
        
        
        $data['enforcer_info']=$this->operations_model->getEnforcers($id);
        
       
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/operations/view', $data);
		$this->load->view('admin/includes/_footer');

	}

	public function addPrices($id){

		$this->rbac->check_operation_access(); 
		if($this->input->post('submit')){
			$hours = $this->input->post('hours');
			$weekly_price = $this->input->post('weekly_price');
			$monthly_price = $this->input->post('monthly_price');


			foreach ($hours as $key => $value) {
					

					$hours = $value;
					$weekly = $weekly_price[$key];
					$monthly = $monthly_price[$key];

					$data = array('weekly'=>$weekly,'monthly'=>$monthly);
					$i = 2;
					foreach ($data as $key1 => $value1){
						 $place_id= $id;
						 $hrs = $hours;
						 $cost = $value1;
						 $pass_type = $i++;
						 $slabInfo = array('place_id'=>$place_id,'hrs'=>$hrs,'cost'=>$cost,'pass'=>$pass_type);
						 $addPrices = $this->operations_model->addSlabInfo($slabInfo);
					}
				}
				$this->session->set_flashdata('success', 'Successfully Prices Added.!');
				redirect(base_url('admin/operation/view/'.$id),'refresh');	
			}

		}

		public function addDailyPrices($id){
	
			$this->rbac->check_operation_access(); 
			if($this->input->post('submit')){
			$hours = $this->input->post('hours');
			$daily_price = $this->input->post('price');

			foreach ($hours as $key => $value) {
					$hours = $value;
					$cost = $daily_price[$key];
					$data = array('place_id'=>$id,'hrs'=>$hours,'cost'=>$cost,'pass'=>1);
					$addDailyPrices = $this->operations_model->addDailyPriceInfo($data);
				}
				$this->session->set_flashdata('success', 'Successfully Daily Prices Added.!');
				redirect(base_url('admin/operation/view/'.$id),'refresh');


		}

			
		}
		
	public function	addHourlyPrices($id)
	{
	

		$this->rbac->check_operation_access(); 
		if($this->input->post('submit')){
			$pass_type = $this->input->post('pass_type');
			$per_hour_price = $this->input->post('per_hour_price');
			$data = array('place_id'=>$id,'hrs'=>'1','cost'=>$per_hour_price,'pass'=>$pass_type);
			$addHourlyPrice = $this->operations_model->addHourlyPrice($data);
			if($addHourlyPrice>0){
				
				$this->session->set_flashdata('success', 'Successfully Hourly Rate Added.!');
				redirect(base_url('admin/operation/view/'.$id),'refresh');	
			}else{
				$this->session->set_flashdata('error', 'Something went wrong.!');
				redirect(base_url('admin/operation/view/'.$id),'refresh');	
			}


		}

	   
	}

	public function addExtentions($id)
	{
		// Add Extention Percantage
		$this->rbac->check_operation_access(); 
		if($this->input->post('submit')){
			$percantage = $this->input->post('extention_charges');
		}
		$data = array('ext_per'=>$percantage);
		$result = $this->operations_model->updateExtentionById($id,$data);
		if($result>0)
		{
			$this->session->set_flashdata('success', 'Successfully Extetntion Added.!');
			redirect(base_url('admin/operation/view/'.$id),'refresh');	
		}else{
			$this->session->set_flashdata('error', 'Something went wrong.!');
			redirect(base_url('admin/operation/view/'.$id),'refresh');
		}

	}
	
	public function updatePriceType($id){
	    
	    $this->rbac->check_operation_access(); 
		if($this->input->post('submit')){
			$type = $this->input->post('hourly');
		}
		$data = array('pricing_type'=>$type);
		$result = $this->operations_model->updatePricingTypeById($id,$data);
		if($result>0)
		{
			$this->session->set_flashdata('success', 'Successfully Extetntion Added.!');
			redirect(base_url('admin/operation/view/'.$id),'refresh');	
		}else{
			$this->session->set_flashdata('error', 'Something went wrong.!');
			redirect(base_url('admin/operation/view/'.$id),'refresh');
		}
		
		
	   
	}
	
	
	
                // Started From Raj Namdev     (12:08PM)
                
    public function addVerifiers($id){
        if(!(empty($_POST))){
            foreach($_POST['verifiers_id'] as $keys => $data){
               $AddVerifers = $this->operations_model->AddVerifiers($id,$data);
            }
            $this->session->set_flashdata('success', 'Successfuly Verifier Assigned.');
			redirect(base_url('admin/operation/view/'.$id),'refresh');
		
        }else{
            $this->session->set_flashdata('error', 'Something Went Wrong.');
			redirect(base_url('admin/operation/view/'.$id),'refresh');
        }
        
       
    }
    
    public function addEnforcers($id){
        
         if(!(empty($_POST))){
            foreach($_POST['enforcers_id'] as $keys => $data){
               $AddVerifers = $this->operations_model->AddEnforcers($id,$data);
            }
            $this->session->set_flashdata('success', 'Successfuly Enforcers Assigned.');
			redirect(base_url('admin/operation/view/'.$id),'refresh');
		
        }else{
            $this->session->set_flashdata('error', 'Something Went Wrong.');
			redirect(base_url('admin/operation/view/'.$id),'refresh');
        }
        
        
    }
                
     
}