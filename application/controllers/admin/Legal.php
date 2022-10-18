<?php defined('BASEPATH') OR exit('No direct script access allowed');

	

class Legal extends MY_Controller {



	public function __construct(){



		parent::__construct();

		auth_check(); // check login auth

		$this->rbac->check_module_access();

		$this->load->model('admin/legal_model', 'legal_model');
	}
	public function legal_process(){
		$data['title'] = '';
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/legal/legal_processing', $data);
		$this->load->view('admin/includes/_footer');
	}

	



	public function updateLeagalInfo($id){



		$data['title'] = 'Legal';

        $data['legalInfo']=$this->legal_model->getDataById($id)[0];

		$this->load->view('admin/includes/_header');

		$this->load->view('admin/legal/update_legal_info', $data);

		$this->load->view('admin/includes/_footer');

	}



	public function updateData($id){

        

        

		$this->rbac->check_operation_access(); // check opration permission

		if($this->input->post('submit')){

	

			$this->form_validation->set_rules('place_name', 'Place Name', 'trim|required');

			$this->form_validation->set_rules('place_address', 'Place Address', 'trim|required');

			$this->form_validation->set_rules('pincode', 'Pincode', 'trim|min_length[6]|max_length[6]|required');

			if ($this->form_validation->run() == FALSE) {

				$data = array(

					'errors' => validation_errors()

				);

				$this->session->set_flashdata('errors', $data['errors']);

				redirect(base_url('admin/legal/updateLeagalInfo/'.$id),'refresh');

			}

			else{



				$data = array(

					'placename' => $this->input->post('place_name'),

					'place_address' => $this->input->post('place_address'),

					'pincode' => $this->input->post('pincode'));



				$data = $this->security->xss_clean($data);

				$result = $this->legal_model->UpdatePlaces($id,$data);

				$process_data = array('place_id'=>$id,

				         'stages_id'=>'1',

				         'status'=>'2');

				if($result){

					// Set the Flags for place verification is on process

					$results = $this->legal_model->AddStageInfo($process_data);

					if($results){

					$this->session->set_flashdata('success', 'Place has been updated successfully!');

					redirect(base_url('admin/legal/updateLeagalInfo/'.$id),'refresh');

					}else{

						$this->session->set_flashdata('error', 'something Went Wrong.!');

						redirect(base_url('admin/legal/updateLeagalInfo/'.$id),'refresh');

					}

				}else{

					$this->session->set_flashdata('error', 'something Went Wrong.!');

					redirect(base_url('admin/legal/updateLeagalInfo/'.$id),'refresh');

				}
			}

		}else if($this->input->post('forward'))

		{

			$process_data = array('place_id'=>$id,'stages_id'=>'1','status'=>'1');

			$update_results = $this->legal_model->UpdateStageInfo($id,$process_data);

			if($update_results){



					$this->session->set_flashdata('success', 'Place has been Successfully Forwarded To Engineering Team.!');

					redirect(base_url('admin/parking/legal_process/'.$id),'refresh');

					}else{

						$this->session->set_flashdata('error', 'something Went Wrong.!');

						redirect(base_url('admin/legal/updateLeagalInfo/'.$id),'refresh');

					}



		}

		else{

			$this->load->view('admin/includes/_header');

			redirect(base_url('admin/legal/updateLeagalInfo/'.$id),'refresh');

			$this->load->view('admin/includes/_footer');

		}

	}



 }















