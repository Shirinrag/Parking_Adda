<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Parking extends MY_Controller {

	

	public function __construct(){
		
		parent::__construct();
		auth_check(); // check login auth
		$this->rbac->check_module_access();

		$this->load->model('admin/parking_model', 'parking_model');
		$this->load->model('admin/location_model', 'location_model');
		$this->load->model('admin/vendors_model', 'vendors_model');
		$this->load->model('admin/legal_model', 'legal_model');
		$this->load->model('admin/ground_model', 'ground_model');
		
		
		
		
		$this->load->library('pagination'); // loaded codeigniter pagination liberary

		
	}
	
	public function parking_datatable(){
		$records['data'] = $this->parking_model->getAllParkingPlaceData();
		$data = array();
		$i=0;
		foreach ($records['data'] as $row) 
		{  
			$status = ($row['place_status'] == 1)? 'checked': '';
			$booking_type = ($row['status'] == 1)? 'Daily': 'Pass';
			$placeinfo = "<b>Place Name :</b>".$row['placename']."<br>"."<b>Address :</b>".$row['place_address'];
			$data[]= array(

				

				++$i,
				$placeinfo,
				$row['country_name'],
				$row['vendor_name'],
				$booking_type,
				$row['no_of_slots'],				
				'<input class="tgl_checkbox tgl-ios" 
				data-id="'.$row['id'].'" 
				id="cb_'.$row['id'].'"
				type="checkbox"  
				'.$status.'><label for="cb_'.$row['id'].'"></label>',
				'<a title="Edit" class="update btn btn-sm btn-warning" href="'.base_url('admin/parking/editPlaces/'.$row['id']).'"> <i class="fa fa-pencil-square-o"></i></a>
				<a title="Delete" class="delete btn btn-sm btn-danger" href='.base_url("admin/parking/delete_place/".$row['id']).' title="Delete" onclick="return confirm(\'Do you want to delete ?\')"> <i class="fa fa-trash-o"></i></a>'			
			);
		}
		$records['data']=$data;
		echo json_encode($records);	


	}
	public function update_status(){
	    
	$this->parking_model->change_status();
	
	}
	
	public function delete_place($id = 0)
	{
		$this->rbac->check_operation_access(); 
		$this->parking_model->delete_place($id);
		$this->session->set_flashdata('success', 'Place has been deleted successfully!');
		redirect(base_url('admin/parking/parkinglist'));
	}
	
    public function editPlaces($id){
		$data['title'] = 'Update Parking Places';
        $data['legalInfo']=$this->legal_model->getDataById($id)[0];
        $data['slot_info']=count($this->ground_model->getSlotInfoById($id));
        $data['slot_details']=$this->ground_model->getSlotInfoById($id);
        $data['hourly'] = $this->ground_model->getPricesInfo($id,'0');
		$data['daily'] = $this->ground_model->getPricesInfo($id,'1');
		$data['passess'] = $this->ground_model->getPricesInfo($id,'pass');
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/parking/edit', $data);
		$this->load->view('admin/includes/_footer');

	}
	
	
	
	




	public function parking_system(){
        
		$data['title'] = 'Parking Management System';
        $data['result_data']=$this->db->where('is_deleted', 0)->limit(10)->order_by('id','DESC')->get('ci_parking_places')->result();
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/parking/parking_list', $data);
		$this->load->view('admin/includes/_footer');
	}
	
	public function list(){

		$this->session->unset_userdata('user_search_type');
		$this->session->unset_userdata('user_search_from');
		$this->session->unset_userdata('user_search_to');

		$data['title'] = 'All Parking Management Data';

		$this->load->view('admin/includes/_header');
		$this->load->view('admin/parking/list', $data);
		$this->load->view('admin/includes/_footer');
	}
	
	public function create_parking_pdf(){

		$this->load->helper('pdf_helper'); // loaded pdf helper
		$data['all_parking'] = $this->parking_model->get_all_simple_users();

		$this->load->view('admin/parking/list_pdf', $data);
	}
		public function parkinglist(){	
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/parking/all_parking_list');
		$this->load->view('admin/includes/_footer');
	}
		public function addparking(){

		$data['title'] = 'Add Parking ';
        $data['country']=$this->location_model->getCountryList();
        $data['vendors']=$this->vendors_model->GetAllVendors();
        
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/parking/add_parking', $data);
		$this->load->view('admin/includes/_footer');
	}


	public function getStates(){
		$country_id = $_POST['country_id'];
		$states=$this->location_model->getStateListById($country_id);
		echo json_encode($states);
	}
	public function getCity(){
		 $state_id = $_POST['state_id'];
		$city=$this->location_model->getCityListById($state_id);
		echo json_encode($city);
	}

	public function addplaces(){
	    $this->rbac->check_operation_access(); // check opration permission
		if($this->input->post('submit')){
			$this->form_validation->set_rules('vendor_id', 'Vendor Name', 'trim|required');
			$this->form_validation->set_rules('country_id', 'State Name', 'trim|required');
			$this->form_validation->set_rules('state_id', 'State Name', 'trim|required');
			$this->form_validation->set_rules('city_id', 'City name', 'trim|required');
			$this->form_validation->set_rules('place_name', 'Place Name', 'trim|required');
			$this->form_validation->set_rules('place_address', 'Place Address', 'trim|required');
			$this->form_validation->set_rules('slots_counts', 'Slots Count', 'trim|min_length[1]|max_length[6]|required');
			$this->form_validation->set_rules('pincode', 'Pincode', 'trim|min_length[6]|max_length[6]|required');
			if ($this->form_validation->run() == FALSE) {
				$data = array(
					'errors' => validation_errors()
				);
				$this->session->set_flashdata('errors', $data['errors']);
				redirect(base_url('admin/parking/addparking'),'refresh');
			}
			else{
				$data = array(
					'vendor_id' => $this->input->post('vendor_id'),
					'placename' => $this->input->post('place_name'),
					'place_address' => $this->input->post('place_address'),
					'applied_slots' => $this->input->post('slots_counts'),
					'fk_country_id' => $this->input->post('country_id'),
					'fk_state_id' => $this->input->post('state_id'),
					'city_id' => $this->input->post('city_id'),
					'pincode' => $this->input->post('pincode'),
					
				);
				$data = $this->security->xss_clean($data);
				$result = $this->parking_model->addNewPlaces($data);
				$result=1;
				if($result){

					$this->session->set_flashdata('success', 'Place has been added successfully!');
					redirect(base_url('admin/parking/addparking'));
				}
			}
		}
		else{
			$this->load->view('admin/includes/_header');
			$this->load->view('admin/parking/addparking');
			$this->load->view('admin/includes/_footer');
		}

	}



public function legal_datatable_json(){
		$records['data'] = $this->legal_model->getLegalData();
		$data = array();
		$i=0;
		
		foreach ($records['data'] as $row) 
		{  
			$stage_id = $row['stage_id'];


			
			if($stage_id!='0' && $stage_id!='')
			{
			 $stage_name= $this->legal_model->getStageName($stage_id);

			
			 if($stage_id == 5){
				$view ='<span class="badge badge-success">'.$stage_name['stages'].'</span>';
			 }
			 else{
				

				$view ='<span class="badge badge-primary">'.$stage_name['stages'].'</span>';
			 }

		
			}else{
				$view = '<a title="Edit" class="view btn btn-sm btn-warning" href='.base_url("admin/legal/updateLeagalInfo/".$row['id']).' ><i class="fa fa-edit"></i></a>';
			}

			$place_details = "<b>Place Name:- </b>".$row["placename"]."<br>".
							 "<b>Place Address:- </b>".$row["place_address"]."<br>".
							 "<b>Country:- </b>".$row["country_name"]."<br>".
							 "<b>State:- </b>".$row["state_name"]."<br>".
							 "<b>City:- </b>".$row["city_name"]."<br>".
							 "<b>Pincode:- </b>".$row["pincode"]."<br>";
			$data[]= array(
				++$i,
				$place_details,
				$row['vendor_name'],
				$row['no_of_slots'],
				$view,
				
			);
		}
		
		$records['data']=$data;
		echo json_encode($records);	

	}



	



	public function legal_process(){

		$data['title'] = '';

		$this->load->view('admin/includes/_header');
		$this->load->view('admin/parking/legal_processing', $data);
		$this->load->view('admin/includes/_footer');
	}
	public function parking_area(){

		$data['title'] = '';

		$this->load->view('admin/includes/_header');
		$this->load->view('admin/parking/parking_area', $data);
		$this->load->view('admin/includes/_footer');
	}
	public function parking_slot(){

		$data['title'] = '';

		$this->load->view('admin/includes/_header');
		$this->load->view('admin/parking/parking_slot', $data);
		$this->load->view('admin/includes/_footer');
	}

public function parking_cost(){

		$data['title'] = '';

		$this->load->view('admin/includes/_header');
		$this->load->view('admin/parking/parking_cost', $data);
		$this->load->view('admin/includes/_footer');
	}


	public function edit($id){

		$data['title'] = '';

		$this->load->view('admin/includes/_header');
		$this->load->view('admin/parking/edit', $data);
		$this->load->view('admin/includes/_footer');


	}
	
 public function Replace_Device(){
	$avail = $this->parking_model->checkDeviceExist();
	if(count($avail)>0){

		$data = $this->parking_model->checkDevice();
		if(count($data)>0)
		{
			//Device is in use
			 $is_exist = "1"; 
		}else{
			//Device is in use
			 $is_exist = "2"; 
		}
	}else{
		//Device Not Exist
		$is_exist = "0"; 
	}
	

	
	$records['status'] = $is_exist;
	echo json_encode($records);	
	}
	
	
	 public function getPricesInfo($place_id,$type){

                $this->db->select('id,hrs,cost,pass,onCreated');
                $this->db->from('ci_price_slab');
                if($type=='pass'){
                $where = "place_id=$place_id AND (pass=2 OR pass=4) AND 'is_deleted'=0";    
                }else{
                    $where = "place_id=$place_id AND pass=$type AND 'is_deleted'=0";
                }
                $this->db->where($where);
                $query = $this->db->get();
                $data = array();
                if($query !== FALSE && $query->num_rows() > 0){
                 $data = $query->result_array();
                 }
                return $data;
    }
    
    
    public function getPriceInfo(){
		$priceData = $this->ground_model->getPricesInfoBySlabId($this->input->post());
		echo json_encode($priceData);	

	}
	
	public function UpdatePrices(){
		$place_id = $this->parking_model->UpdatePriceInfo($this->input->post());
		$this->session->set_flashdata('success', "successfully price details updated.");
		redirect(base_url('admin/parking/editPlaces/'.$place_id),'refresh');
	}
	
	public function deactivePrice($id,$place_id){
		$record = $this->parking_model->deactivePrice($id);
		if($record){
			$this->session->set_flashdata('success', "successfully price details deleted.");
			redirect(base_url('admin/parking/editPlaces/'.$place_id),'refresh');
		}else{
			$this->session->set_flashdata('error', "something went wrong.");
			redirect(base_url('admin/parking/editPlaces/'.$place_id),'refresh');
		}
		
	}
	
}

?>
