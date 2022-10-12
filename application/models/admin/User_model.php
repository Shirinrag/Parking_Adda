<?php

	class User_model extends CI_Model{



		public function add_user($data){

			$this->db->insert('ci_users', $data);

			return true;

		}



		//---------------------------------------------------

		// get all users for server-side datatable processing (ajax based)

		public function get_all_users()
	{

		$this->db->select('*');

		$this->db->where('is_admin', 0);

		$data =  $this->db->get('ci_users')->result_array();

		if (!empty($data)) {
			foreach ($data as $key => $value) {
				$userid = $value['id'];
				$this->db->select('car_number');
				$this->db->where('user_id', $userid);
				$this->db->where("is_deleted",0);
				

				$car_data =  $this->db->get('ci_car_details')->result_array();
				$car_number = "";
				if(count($car_data)>0){
					foreach($car_data as $keys => $values){
						$car_number =$values['car_number'].",".$car_number;
					}	
				}
				else{
					$car_number = '<center><i class="bi bi-x"></i></center>';
				}

				$new[] = $value;
				$new[$key]['car_number'] = $car_number;
				
			}

			return $new;
		}
	}





		//---------------------------------------------------

		// Get user detial by ID

		public function get_user_by_id($id){

			$query = $this->db->get_where('ci_users', array('id' => $id));

			return $result = $query->row_array();

		}



		//---------------------------------------------------

		// Edit user Record

		public function edit_user($data, $id){

			$this->db->where('id', $id);

			$this->db->update('ci_users', $data);

			return true;

		}



		//---------------------------------------------------

		// Change user status

		//-----------------------------------------------------

		function change_status()

		{		

			$this->db->set('is_active', $this->input->post('status'));

			$this->db->where('id', $this->input->post('id'));

			$this->db->update('ci_users');

		} 



	}



?>