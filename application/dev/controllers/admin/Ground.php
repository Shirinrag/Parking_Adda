<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ground extends MY_Controller {

	
	public function __construct(){

		parent::__construct();
		auth_check(); // check login auth
		$this->load->model('admin/legal_model', 'legal_model');
		$this->load->model('admin/ground_model', 'ground_model');
		$this->load->model('admin/slot_model','slot_model');
		$this->load->model('admin/sensor_model','sensor_model');
		
		
	}

	public function parking_list(){

		$data['title'] = '';
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/ground/parking_places', $data);
		$this->load->view('admin/includes/_footer');
	}


    public function ground_datatable_json(){
	
		$records['data'] = $this->ground_model->getGroundData();
		$data = array();
		$i=0;

		
		foreach ($records['data'] as $row) 
		{  
		    	$stage_id = $row['stage_id'];
			if($stage_id=='1' && $stage_id!='')
			{
                $view = '<a title="Edit" class="view btn btn-sm btn-warning" href='.base_url("admin/ground/slotGenrate/".$row['id']).' ><i class="fa fa-edit"></i></a>';
			
			}else{
			     $view ='<a title="view" class="view btn btn-sm btn-primary" href='.base_url("admin/ground/slotGenrate/".$row['id']).' ><i class="fa fa-eye"></i></a>';
			}
			$place_details = "<b>Place Address :-</b>".$row['place_address']."<br>".
							 "<b>Country :-</b>".$row['country_name']."<br>".
							 "<b>State :-</b>".$row['state_name']."<br>".
							 "<b>City :-</b>".$row['city_name']."<br>".
							 "<b>Pincode :-</b>".$row['pincode']."<br>";

			$data[]= array(
				++$i,
				$row['placename'],
				$place_details,
				$row['vendor_name'],
				$row['no_of_slots'],
				$view,
				
			);
		}
		
		$records['data']=$data;
		echo json_encode($records);	

	}

	public function slotGenrate($id){
		$data['title'] = 'Ground Team';
        $data['legalInfo']=$this->legal_model->getDataById($id)[0];
        $data['slot_info']=count($this->ground_model->getSlotInfoById($id));
        $data['slot_details']=$this->ground_model->getSlotInfoById($id);
      

		$this->load->view('admin/includes/_header');
		$this->load->view('admin/ground/add_slot_info', $data);
		$this->load->view('admin/includes/_footer');

	}
	public function genrateSlots($id){	
	    
	   // echo "<pre>";
	   // print_r($_POST);
	   // die;

		$this->rbac->check_operation_access(); // check opration permission
		if($this->input->post('submit')){
	
			$this->form_validation->set_rules('slots_counts', 'Slots Count', 'trim|required');
			$this->form_validation->set_rules('latitude', 'Latitude', 'trim|required');
			$this->form_validation->set_rules('longitude', 'Longitude', 'trim|required');
			$prfix = $_POST['prefix'];
			if ($this->form_validation->run() == FALSE) {
				$data = array(
					'errors' => validation_errors()
				);
				$this->session->set_flashdata('errors', $data['errors']);
				redirect(base_url('admin/ground/slotGenrate/'.$id),'refresh');
			}
			else{

				$data = array(
					'no_of_slots' => $this->input->post('slots_counts'),
					'latitude' => $this->input->post('latitude'),
					'longitude' => $this->input->post('longitude'));

				$data = $this->security->xss_clean($data);
				$result = $this->ground_model->UpdateSlotsInfo($id,$data);	
				if($result){
					$slots_count = $this->input->post('slots_counts');

					for($i=0;$i<$slots_count;$i++){
					$info= $this->ground_model->getSlotInfo($prfix);
					if($info['total']==0){
						$variable = $prfix."-AA000";
					}else{
						 $variable= $this->ground_model->getLastSlotName()['slot_name']; 
					}	
						$slot_name = $prfix."-".$this->ground_model->uniqueSlotName($variable); 
						$display_id = "P-".($i+1);
						$data = array('place_id'=>$id,
									 'slot_name'=>$slot_name,
									 'display_id'=>$display_id,
									 'machine_id'=>0,
									 'latitude'=>$this->input->post('latitude'),
									 'longitude'=>$this->input->post('longitude'),
									 'onOff_apply_date'=>'',
									 'reserved_userId'=>'',
									 'reserved_booking_time'=>''
									);

						$addSlotInfo = $this->ground_model->addSlotInfo($data);
					}
					$this->session->set_flashdata('success', 'Scusscessfully Slots Genrated!');
					redirect(base_url('admin/ground/slotGenrate/'.$id),'refresh');
				}else{
					$this->session->set_flashdata('error', 'something Went Wrong.!');
					redirect(base_url('admin/ground/slotGenrate/'.$id),'refresh');
					
				}

			}
		}
	}

	public function machineInstallation($id){
	
		$this->rbac->check_operation_access(); // check opration permission
		if($this->input->post('submit')){
		    
		    

		$machine_ids = $_POST['machine_id'];
		$slot_names = $_POST['slot_name'];
		$submited_count = count($machine_ids);
		$unique_machine_ids = array_unique($machine_ids);
		$unique_count = count($unique_machine_ids);
        $error =0;
		if($submited_count==$unique_count){

			foreach ($machine_ids as $key => $value) {

				$device_id =  $value; 
				$pk_device_id = $this->ground_model->getUniqueSesnorId($device_id)['unique_sensor_id']; 
				if($pk_device_id==""){
				    $error =1; 
				}
			    if($error=='0'){
				$slot_name = $slot_names[$key]; 
				$result = $this->ground_model->AssignMachineId($id,$pk_device_id,$slot_name);
				if($result<0){
					$this->session->set_flashdata('error', 'Something went!');
					redirect(base_url('admin/ground/slotGenrate/'.$id),'refresh');
				}
			   }else{
			        $this->session->set_flashdata('error', 'Entered devices are not exist.');
					redirect(base_url('admin/ground/slotGenrate/'.$id),'refresh');
					die;
			   }
				
			}

			$process_data = array('place_id'=>$id,'stages_id'=>'2','status'=>'1');
			$results = $this->legal_model->UpdateStageInfo($id,$process_data);

			if($results){
			$this->session->set_flashdata('success', 'Devices Success Mapped.!');
			redirect(base_url('admin/ground/slotGenrate/'.$id),'refresh');
		}else{
			$this->session->set_flashdata('error', 'Something Went Wrong.!');
			redirect(base_url('admin/ground/slotGenrate/'.$id),'refresh');
		}



		
		}else{
			$this->session->set_flashdata('error', 'Machine Id Should be Unique..!');
			redirect(base_url('admin/ground/slotGenrate/'.$id),'refresh');
		}
	}
	}
	
	
	public function add_devices()
	{
	    $data['title'] = '';
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/ground/add_devices', $data);
		$this->load->view('admin/includes/_footer');
	}
	
	public function add_device_id(){
		
		$this->rbac->check_operation_access(); // check opration permission

		if($this->input->post('submit')){
			$this->form_validation->set_rules('device_id', 'Device Id', 'trim|required|is_unique[tbl_sensor_list.device_id]|numeric');
			

			if ($this->form_validation->run() == FALSE) {
				$data = array(
					'errors' => validation_errors()
				);
				$this->session->set_flashdata('errors', $data['errors']);
				redirect(base_url('admin/Ground/add_devices'),'refresh');
			}
			else{
				$data = array(
					'device_id' => $this->input->post('device_id')
					
				);
				$data = $this->security->xss_clean($data);
				$result = $this->slot_model->add_device_id($data);
				if($result > 0){

					

					$this->session->set_flashdata('success', 'Device has been added successfully!');
					redirect(base_url('admin/Ground/add_devices'));
				}
				else
				{
				    $this->session->set_flashdata('error', 'Something Went Wrong!');
					redirect(base_url('admin/Ground/add_devices'));
				}
			}
		}
		else{
			$this->load->view('admin/includes/_header');
			$this->load->view('admin/Ground/add_devices');
			$this->load->view('admin/includes/_footer');
		}
		
	}
	
	public function device_list(){
		$data['title'] = '';
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/ground/device_list', $data);
		$this->load->view('admin/includes/_footer');
	}
	
	public function datatable_json(){				   					   
		$records['data'] = $this->slot_model->getDeviceData();
		
// 		echo "<pre>";
// 		print_r($records['data']);
// 		die;
		$data = array();
		$i=0;
		foreach ($records['data']   as $row) 
		{  
		    $last_inserted = $this->sensor_model->getSensorLogsById($row['device_id'])['created_date']; 
		    $last_inserted = date('d F Y H:i',strtotime($last_inserted)); 
		    
		  
		    $original_date = $row['onCreated'];
            if($row['test_status'] == 1 && $row['slot_name']!=""){
                 $device_statuss = '<span class="badge badge-success">Live</span>';
            }else if(($row['test_status'] == 1 && $row['test_status'] == 0) || $row['slot_name']==""){
                
                 $device_status = ($row['test_status'] == 1)? 'checked': '';
                 $device_statuss = '<input class="tgl_checkbox tgl-ios" 
				data-id="'.$row['id'].'" 
				id="cb_'.$row['id'].'"
				type="checkbox"  
				'.$device_status.'><label for="cb_'.$row['id'].'"></label>';
                
                }else{
                 $device_status = ($row['test_status'] == 1)? 'checked': '';
                 $device_statuss = '<input class="tgl_checkbox tgl-ios" 
				data-id="'.$row['id'].'" 
				id="cb_'.$row['id'].'"
				type="checkbox"  
				'.$device_status.'><label for="cb_'.$row['id'].'"></label>';
            }
            
            
            if($row['test_status'] == 1 && $row['slot_name']!=""){
                $final_status = '<span class="badge badge-warning">In Use</span>';
            }else if($row['test_status'] == 1 && $row['slot_name']==""){
                $final_status = '<span class="badge badge-primary">Ready To Use</span>';
            }else{
                $final_status = '<span class="badge badge-danger">'.$last_inserted.'</span>';
            }
            
            
		    $timestamp = date("d-m-Y",  strtotime($original_date));
		    
			$data[]= array(
				++$i,
				$row['placename'],
				$row['slot_name'],
				$row['display_id'],
				$row['device_id'],
		    	$timestamp,
		    	$device_statuss,
				$final_status,
			
			);
		}
		$records['data']=$data;
		echo json_encode($records);						   
	}
	
	public function update_status(){
	    $this->slot_model->change_status();
	}

}