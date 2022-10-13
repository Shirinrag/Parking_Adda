<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Duty extends MY_Controller {



	public function __construct(){
		parent::__construct();
		auth_check(); // check login auth
		$this->rbac->check_module_access();
		$this->load->model('admin/duty_model', 'duty_model');

	}

	public function duty_allocation(){

		$data['placename'] = $this->duty_model->getPlaces();
		$data['verifiers'] = $this->duty_model->getVerifiers();
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/duty/duty_allocate',$data);
		$this->load->view('admin/includes/_footer');
	}


	public function getData(){

		$data['placename'] = $this->duty_model->getPlaces();
		$data['verifiers'] = $this->duty_model->getVerifiers();
		echo json_encode(array('success'=>true,'message'=>$data));
	}

	public function VerifiersDutyAssign(){
			if(isset($_POST['submit'])){
				for($i=0;$i<count($_POST['placename']);$i++){
					$place_id = $_POST['placename'][$i];
					$verifier_id = $_POST['verifiers_ids'][$i];
					$duty_date = $_POST['duty_date'][$i];
					$this->duty_model->AllocateDuty($place_id,$verifier_id,$duty_date);

				}
				$this->session->set_flashdata('success', 'Successfully Duty Assigned.');
				redirect(base_url('admin/duty/dutylist'),'refresh');

			}
	}

	function deallocateVerifier($id=''){

		$this->rbac->check_operation_access(); // check opration permission
		$this->duty_model->deactiveDuty($id);
		$this->session->set_flashdata('success','Verifer Duty has been Deleted Successfully.');	
		redirect('admin/duty/dutylist');

	}





	public function dutylist(){

		$data['title'] = 'Verifiers Duty';
		$data['verifiers'] = $this->duty_model->getVerifiers();
		$data['placename'] = $this->duty_model->getPlaces();
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/duty/dutylist',$data);
		$this->load->view('admin/includes/_footer');
	}

	public function filterdata(){
		$this->session->set_userdata('verifier_id',$this->input->post('type'));
		$this->session->set_userdata('date',$this->input->post('date'));
		$this->session->set_userdata('place_id',$this->input->post('place_id'));

	}

	public function list_data(){
		$data['info'] = $this->duty_model->get_all();
		$this->load->view('admin/duty/list',$data);

	}

}