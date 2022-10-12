	<?php defined('BASEPATH') OR exit('No direct script access allowed');



class Complaint extends MY_Controller {



	

	public function __construct(){



		parent::__construct();

		$this->load->model('admin/Complaint_model', 'complaint_model');

		$this->load->model('admin/User_model', 'user_model');

		$this->load->model('admin/Wallet_model', 'wallet_model');

		$this->load->model('admin/Booking_model', 'booking_model');

		

		

		auth_check(); // check login auth

	}

	

// 	public function index(){

// 	    redirect(base_url('admin/complaint/Direct_complaint'),'refresh');

// 	}



	public function Enforcer_c(){



		$data['title'] = '';

		$this->load->view('admin/includes/_header');

		$this->load->view('admin/complaint_box/enforcer_complaint', $data);

		$this->load->view('admin/includes/_footer');

	}

	

	public function search()

	{

		$this->session->set_userdata('complaint_type',$this->input->post('complaint_type'));

	}

	

	

	public function datatable_json(){				   					   

		 

		$records['data'] = $this->complaint_model->get_complaint_datas();

		$data = array();

		$i=0;

		foreach ($records['data']   as $row) 

		{  

			if($row['enf_status']==1){

			$status = '<span class="badge badge-primary">Replace</span>';

			}else if($row['enf_status']==2){

			$status = '<span class="badge badge-warning">Refund</span>';

			}else if($row['enf_status']==0){

				$status = '<span class="badge badge-danger">Pending</span>';

			}

			else{

				$status = '<span class="badge badge-success">Resolved</span>';

			}

			

			

			if($row['enf_status']==0){

				$view = '<a title="View" class="view btn btn-sm btn-warning" 

				 onClick= actions("' .$row['unique_booking_id']. ',' .$row['complaint_id']. '");> <i class="fa fa-edit"></i></a>';

			}else{

				$view="";

			}

			

			$data[]= array(

				++$i,

				$row['unique_booking_id'],

				$row['placename'],

				$row['place_address'],

				$row['complaint_text'],

				date_time($row['booking_from_date']),

				date_time($row['booking_to_date']),

				$row['from_time'],

				$row['to_time'],

				$row['slot_name'],

				$row['display_id'],

				$row['verifier_name'],

				$status,

			    $view

			);

		}

		

		$records['data']=$data;

		echo json_encode($records);						   

	}

	

	

	

	

	public function Vendor_c(){



		$data['title'] = '';



		$this->load->view('admin/includes/_header');

		$this->load->view('admin/complaint_box/vendor_complaint', $data);

		$this->load->view('admin/includes/_footer');

	}

	public function Engineer_c(){



		$data['title'] = '';



		$this->load->view('admin/includes/_header');

		$this->load->view('admin/complaint_box/engineer_complaint', $data);

		$this->load->view('admin/includes/_footer');

	}

	public function Legal_c(){



		$data['title'] = '';



		$this->load->view('admin/includes/_header');

		$this->load->view('admin/complaint_box/legal_complaint', $data);

		$this->load->view('admin/includes/_footer');

	}

	public function Customer_care_c(){

	    $data['title'] = '';

	    $booking_complaints = count($this->complaint_model->get_cc_complaint_datas());
	    $slots_complaints = count($this->complaint_model->getClosedSlotsComplaints());

	    $this->session->set_userdata('BookingComplaints', $booking_complaints);
	    $this->session->set_userdata('SlotsComplaints', $slots_complaints);


		$this->load->view('admin/includes/_header');

		$this->load->view('admin/complaint_box/customer_care_complaint', $data);

		$this->load->view('admin/includes/_footer');

	}

	

	public function cc_search(){

	$this->session->set_userdata('cc_complaint_type',$this->input->post('cc_complaint_type')); 

	}

	

		public function cc_datatable_json(){				   					   

		$records['data'] = $this->complaint_model->get_cc_complaint_datas();

		$data = array();

		$i=0;

		foreach ($records['data'] as $row) 

		{  

			if($row['enf_status']==1){

			$status = '<span class="badge badge-primary">Replace</span>';

			}else if($row['enf_status']==2){

			$status = '<span class="badge badge-warning">Refund</span>';

			}else if($row['enf_status']==0){

				$status = '<span class="badge badge-danger">Pending</span>';

			}else{

				$status = '<span class="badge badge-success">Resolved</span>';}

			$source = ($row['source']==1) ? '<span class="badge badge-warning">App</span>' : '<span class="badge badge-primary">Call</span>' ;

				

			if(($row['status']==1)){

			$view = '<a title="View History" class="view btn btn-sm btn-success"  href='.base_url("admin/complaint/view_complaint/".$row['booking_id']).' ><i class="fa fa-eye"></i></a>';

			}else{

			$view = '<a title="View History" class="view btn btn-sm btn-danger"  href='.base_url("admin/complaint/view_complaint/".$row['booking_id']).' ><i class="fa fa-edit"></i></a>';

			}

			$place_name = "<b>"."Place Name : "."</b>".$row['placename']."<br>"."<b>"."Address : "."</b>".$row['place_address'];

			$date_stack1 = date("d-m-Y H:i a", strtotime($row['booking_from_date'].$row['from_time']));

			$date_stack2 = date("d-m-Y H:i a", strtotime($row['booking_to_date'].$row['to_time']));

				

			    $data[]= array(

				++$i,

				'<a title="View History" href='.base_url("admin/complaint/view_complaint/".$row['booking_id']).' >'.$row['unique_booking_id'].'</a>',

				$place_name,

				$row['complaint_text'],

				$date_stack1,

				$date_stack2,

				$row['verifier_name'],

				$source,

			    $view,

			);

		}

		$records['data']=$data;

		echo json_encode($records);						   

	}

	

	

	

	

   

	

	public function complaints_update(){

  		$this->complaint_model->upateComplaints(); // Update Complaints By Enforcer

	}



    public function view_complaint($id){

		$record['user_info'] = $this->complaint_model->getUsersInfo($id);
		$booking_info = $this->complaint_model->getBookingInfoById($id);
		foreach($booking_info as $Keys => $values){
		    if($values['descriptions']=='Replace'){
		        $uniqueBookinId = $values['unique_booking_id'];
		        $getReplacementsData = $this->complaint_model->getReplacementsData($uniqueBookinId);
		        $record['booking_info'][] = $values; 
		        $record['booking_info'][$Keys]['issue_img'] = $values['issue_img'];
		        $record['booking_info'][$Keys]['replacement_data'] = $getReplacementsData;
		    }
		    $record['booking_info'][] = $values; 
		}
		$record['wallet_info'] = $this->wallet_model->getWalletInfoById($id);
		$record['despositions'] = $this->complaint_model->getDespositions();
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/complaint_box/view_history', $record);
		$this->load->view('admin/includes/_footer');
	}

	

	

	public function cc_actions($id=""){

		$this->rbac->check_operation_access(); // check opration permissions
		if($this->input->post('submit')){
			$this->form_validation->set_rules('despositions', 'Despositions', 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				$data = array(
					'errors' => validation_errors()
				);
				$this->session->set_flashdata('errors', $data['errors']);
				redirect(base_url('admin/complaint/cc_actions/'.$id),'refresh');
			}
			else{
			         $user_id = $this->input->post('user_id');
				     $booking_id = $this->input->post('booking_id');

				$data = array(
					'fk_despostion_id' => $this->input->post('despositions'),
					'customercareRemark' => $this->input->post('cc_remarks'),
					'status' => 1

				);



				$data = $this->security->xss_clean($data);

				// 1:Replace 2:Refund 3:Query Fixed

				

				if($this->input->post('despositions')=='3'){

					// Update the status on booking table

					$complaint_result = $this->complaint_model->updateComplaintStatus($data,$id);

				}else if($this->input->post('despositions')=='2'){

				     $refund_result = $this->complaint_model->cancelBooking($user_id,$booking_id);
				     if($refund_result['status']==1){
				         $result = $this->complaint_model->updateCancelRemark($data, $id);
				     }else{
				          $this->session->set_flashdata('error', 'You cannot cancle this Booking');
				          redirect(base_url('admin/complaint/view_complaint/'.$id),'refresh');
				     }
				    }
				    else if($this->input->post('despositions')=='1'){	
				        $replace_result = $this->complaint_model->replaceBooking($booking_id);
				        if($replace_result=='false'){
				            $this->session->set_flashdata('error', 'Replacement Is Not Available.');
				            redirect(base_url('admin/complaint/view_complaint/'.$id),'refresh');
				        }else{				            
				            $result = $this->complaint_model->update_cc_remarks($data, $id); 
				            $this->session->set_flashdata('success', 'Successfully Replaced.');
				            redirect(base_url('admin/complaint/SlotsBlockingVerification/'.$id),'refresh');

				        }

				    }

					$this->session->set_flashdata('success', 'Status has been updated successfully!');

					redirect(base_url('admin/complaint/Customer_care_c'));

			}

		}

		

		else{

			

			$this->load->view('admin/includes/_header');

			$this->load->view('admin/admin/edit', $data);

			$this->load->view('admin/includes/_footer');

		}

		

	}





		public function Ground_team_c(){

		$data['title'] = '';

		$this->load->view('admin/includes/_header');

		$this->load->view('admin/complaint_box/ground_team_complaint', $data);

		$this->load->view('admin/includes/_footer');

	}

	

	public function Direct_complaint(){

		$data['booking_id'] = $this->complaint_model->getBookingId();
		$data['mob_data'] = $this->complaint_model->getDataByMobileNumber();
		$data['counts'] = $this->complaint_model->getComplaintsCounts();
		$this->load->view('admin/includes/_header');
		$this->load->view('admin/complaint_box/direct_complaint/addcomplaint',$data);

	}

	

	public function getAllOtherComplaints(){

            

		$other_complaints_data = $this->complaint_model->PendingUsersComplaint('closed');

	

		$app_datas = array();

		if(!empty($other_complaints_data['mobile_app'])){

		foreach ($other_complaints_data['mobile_app']  as $key => $value) {

		    

		    if($value['status']==0 || $value['status']==2){

		        $status = 1;

		    }else{

		         $status = 1;

		    }

		    $value['actions'] = $value['status'];

		    $value['fk_disposition_id'] = $status;

		    $value['created_date'] = date('d-m-Y H:i a', strtotime($value['created_date']));

			$app_datas[]  = $value;

		}

	}

		$other_complaint = $app_datas;

	    $OtherCompFromCalls = $this->complaint_model->getOtherComplaintsCalls();

		$otherComplaints = array_merge($other_complaint,$OtherCompFromCalls);

	    $data = array();

		$i=0;

		foreach($otherComplaints as $row){

		    

		     $source = ($row['source_type']==1) ? '<span class="badge badge-primary">User App</span>' : '<span class="badge badge-dark">Call</span>' ;

             $status = '<span class="badge badge-success">Resolved</span></span>';

             $view = '<a title="View History" class="view btn btn-sm btn-danger"  href='.base_url("admin/complaint/add_other_complaint/".$row['id']."/".$row['user_id']."/".$row['source_type']).' ><i class="fa fa-eye"></i></a>';



		      $data[]= array(

				++$i,

				$row['username'],

				$row['complaint_topic'],

				$row['description'],

    			$source,

    			$row['created_date'],

    			$status,

    			$view,

			);

		}

		

        $records['data']=$data;

        $this->session->set_userdata('OtherComplaints', count($data));

       	echo json_encode($records);	

    

    }

    

    

	public function Pending_complaint(){

	    

	    if(empty($_GET)){

	        $type = 0;

	    }else{

	      $type = $_GET['type'];

	    }

	    
		$data['counts'] = $this->complaint_model->getComplaintsCounts();
		$verifierComplaints = $this->complaint_model->getPendingBookingComplaints('pending');
		$bookingComplaints = $this->complaint_model->getBookingComplaintsCalls('pending');
		$other_complaints_data = $this->complaint_model->PendingUsersComplaint('pending');
		$app_datas = array();
		$unaddressed = array();
		if(!empty($other_complaints_data['mobile_app'])){
		foreach ($other_complaints_data['mobile_app']  as $key => $value) {
		    if($value['status']=='2' || $value['status']=='1'){
		    if($value['status']==0 || $value['status']==2){
		        $status = 1;
		    }
		    $value['actions'] = $value['status'];
		    $value['fk_disposition_id'] = $status;
		    $value['created_date'] = date('d-m-Y H:i a', strtotime($value['created_date']));
			$app_datas[]  = $value;
		}else{
		    $unaddressed[] = $value;
		}

	}

		}

		

	

		$other_complaint = $app_datas;

	    $OtherCompFromCalls = $this->complaint_model->getOtherComplaintsCalls();

		$data['othercomplaints'] = array_merge($other_complaint,$OtherCompFromCalls);

        $data['booking_complaints'] = array_merge($verifierComplaints,$bookingComplaints);

		$this->load->view('admin/includes/_header');

		if($type=='1' || $type=='0'){

		$this->load->view('admin/complaint_box/direct_complaint/PendingComplaints',$data);

		}else if($type=='3'){

		    $data['othercomplaints'] = $unaddressed;

		   $this->load->view('admin/complaint_box/direct_complaint/PendingOtherComplaints',$data); 

		}else{

		 $this->load->view('admin/complaint_box/direct_complaint/PendingOtherComplaints',$data);

		}

	}

	

        public function checkComplaintStatus(){

	    $id = $this->input->post('booking_id'); 

		$complaints_data = $this->complaint_model->getDBookingInfoById($id);

        $data = array();

		$sts = 0;

		foreach ($complaints_data as $key => $value) {

				if($value['complaint_source']==2){

					$complaint_txt = $this->complaint_model->getBookingComplaints($value['uq_complaint'])[0]['descriptions'];

					$value['complaint_text'] = $complaint_txt;

					$data[] = $value;

				}else{

				  $data[] = $value;

				}

				if(($value['status']==1) && ($value['descriptions'])){

					$sts = "1";

				}

				

		}

		$data['complaints'] = $data;

		$data['buttons'] = $sts;

		echo json_encode($data);

	}

	

	public function add_complaint($id=0,$id1=0){

		if(!empty($_POST)){

			

			 $id = $this->complaint_model->getUqId($_POST['booking_id'])[0]['id'];

			 $ids = $this->complaint_model->getUqId($_POST['booking_id'])[0]['id'];

		}else{

			$id;

			$ids = $this->complaint_model->getUniqueId($id)[0]['unique_booking_id'];

		}

		$record['ReplacementsData'] = $this->complaint_model->getReplacementsData($ids);

		$record['user_info'] = $this->complaint_model->getUsersInfo($id);

		$record['booking_info'] = $this->complaint_model->getCBookingInfoById($id)[0];

		$record['wallet_info'] = $this->wallet_model->getWalletInfoById($id);

		$record['despositions'] = $this->complaint_model->getDespositions();

		$record['verifier_remarks'] = $this->complaint_model->getMasterIssues();

		$record['despositions'] = $this->complaint_model->getDespositions();

		$record['verifier_list'] = $this->complaint_model->getVerifiersByPlaceId($id);

		$record['enforcers_list'] = $this->complaint_model->getEnforcersByPlaceId($id);

		$record['complaints_master'] = $this->complaint_model->getComplaints();

		$complaints = $this->complaint_model->getComplaintsinfoById($id,$id1);

		$record['complaints_info'] = (!empty($complaints)) ?  $complaints[0] : $complaints ;

		$record['counts'] = $this->complaint_model->getComplaintsCounts();

		$this->load->view('admin/includes/_header');

		$this->load->view('admin/complaint_box/direct_complaint/register_complaint', $record);

		$this->load->view('admin/includes/_footer');

	}

	

	public function add_other_complaint($complaint_id=0,$user_id=0,$type=0){

		



		$id = (empty($_POST['user_id'])) ? $user_id :$_POST['user_id']; 

		$record['user_info'] = $this->complaint_model->getCUsersInfo($id);

		$record['cars_info'] = $this->complaint_model->getCarInfoByUserId($id);

		$record['txn_info'] = $this->complaint_model->getTxnInfoByUserId($id);

		$record['complaint_master'] = $this->complaint_model->getOtherComplaintMaster();



		$record['other_complaints_info'] = array();

		if($type==1)

		{

		if($id!="" && $complaint_id!=""){

			$record['other_complaints_info'] = $this->complaint_model->getOtherComplaintsById($id,$complaint_id);

			if(!empty($record['other_complaints_info'])){

			    $record['other_complaints_info'] = $record['other_complaints_info'][0];

			}

			$record['comp_info'] =$this->complaint_model->getComplaintsData($complaint_id);

			if(!empty($record['comp_info'])){

				$record['comp_info'] = $record['comp_info'][0];

			}

			}



		}else

		{

			$record['comp_info'] = $this->complaint_model->getOtherComplaintsData($complaint_id);

			if(!empty($record['comp_info'])){

				$record['comp_info'] = $record['comp_info'][0];

			}

		}

		$record['counts'] = $this->complaint_model->getComplaintsCounts();

		$record['complaint_source'] = $type;	

		$record['unique_complaint_id'] = $complaint_id;	

		$this->load->view('admin/includes/_header');

		$this->load->view('admin/complaint_box/direct_complaint/othercomplaint',$record);

		$this->load->view('admin/includes/_footer');





	}

	

    public function registerBookingComp(){



		if($this->input->post('submit')){

			$booking_id = $_POST['booking_id'];

			$booking_data= $this->complaint_model->getDataForComplaint($booking_id)[0];



			$status = ($_POST['dispositions_id']==0) ? 2 : 1 ;

			$complaint_array = array('verifier_id'=>$_POST['verifier_id'],

									 'place_id'=>$booking_data['place_id'],

									 'complaint_source'=>'2',

									 'slot_id'=>$booking_data['slot_id'],

									 'booking_id'=>$_POST['booking_id'],

									 'complaint_id'=>$_POST['complaint_id'],

									 'enforcer_id'=>'0',

									 'status'=>$status ,

									 'fk_despostion_id'=>$_POST['verifier_action'],

									 'customercareRemark'=>$_POST['cc_remark']);

									 

		

			if(empty($_POST['unique_booking_id'])){

				$addComplaint = $this->complaint_model->AddDirectComplaintData($complaint_array); 

				$unique_booking_id = (!empty($_POST['unique_booking_id'])) ? $_POST['unique_booking_id'] : $addComplaint;

			

			}else

			{

			    

				 $unique_booking_id = $_POST['unique_booking_id'];

				 $addComplaint = $this->complaint_model->UpdateDirectComplaints($complaint_array,$unique_booking_id); 

			}	 

	

			if($addComplaint>0){

				$unique_booking_id = (!empty($_POST['unique_booking_id'])) ? $_POST['unique_booking_id'] : $addComplaint;

			    

				if($status==1 && $_POST['verifier_action']==1){

				    

					$replace_result = $this->complaint_model->replaceBooking($booking_id);

				        if($replace_result=='false'){

				            $updateStatus = $this->complaint_model->UpdateStatus($unique_booking_id);     

				            $this->session->set_flashdata('error', 'Replacement Is Not Available.');

				            redirect(base_url('admin/complaint/add_complaint/'.$booking_id."/".$unique_booking_id),'refresh');

				            

				        }else{

				           

				        	 $this->session->set_flashdata('success', 'Successfully Booking Replaced.'); 

				           	 redirect(base_url('admin/complaint/add_complaint/'.$booking_id."/".$unique_booking_id),'refresh');

				        }

				}

				//Refund

				else if($status==1 && $_POST['verifier_action']==2){ 



					$refund_result = $this->complaint_model->cancelBooking($booking_data['user_id'],$booking_id);

				     if($refund_result['status']==1){

				     	 $data=array();

				         $result = $this->complaint_model->updateCancelRemark($data,$booking_id);

				         $this->session->set_flashdata('success', "Successfully Booking Cancelled.");

					     redirect(base_url('admin/complaint/add_complaint/'.$booking_id."/".$unique_booking_id),'refresh');



				     }else{

				       $this->session->set_flashdata('error', 'You cannot cancle this Booking');

					   redirect(base_url('admin/complaint/add_complaint/'.$booking_id."/".$unique_booking_id),'refresh');

				     }





				} 

				else if($status==1 && $_POST['verifier_action']==3){

					$complaint_result = $this->complaint_model->updateDComplaintStatus($booking_id,$unique_booking_id);

					if($complaint_result>0){

					$this->session->set_flashdata('success', "Successfully Complaints Added");

				   	redirect(base_url('admin/complaint/add_complaint/'.$booking_id."/".$unique_booking_id),'refresh');

					}else{

					$this->session->set_flashdata('error', "Something Went Wrong");

				    redirect(base_url('admin/complaint/add_complaint/'.$booking_id."/".$unique_booking_id),'refresh');

					}

				}

				else{

				    $this->session->set_flashdata('success', "Successfully Complaints Updated");

			        redirect(base_url('admin/complaint/add_complaint/'.$booking_id."/".$unique_booking_id),'refresh');

				}

			

			}else{

				$this->session->set_flashdata('errors',"Something Went Wrong");

				redirect(base_url('admin/complaint/add_complaint/'.$booking_id."/".$unique_booking_id),'refresh');

			}

			

			





		}else{

			

			echo "wrong"; die;



		}

	}

	

	

public function checkOtherStatus()

	{

		$user_id = $_POST['user_id'];

		$other_complaints_data = $this->complaint_model->getOtherBookingInfoByUserId($user_id);

		$DirectCompData = $this->complaint_model->getDirectOtherCompByUserId($user_id);

   		$app_datas = array();

		if(!empty($other_complaints_data)){

		foreach ($other_complaints_data  as $key => $value) {

		    

		    $value['actions'] = $value['status'];

		    $value['fk_disposition_id'] = $value['status'];

		    $value['created_date'] = date('d-m-y H:i:s', strtotime($value['created_date']));

			$app_datas[]  = $value;

		}

	}

		

		$direct_datas = array();

		if(!empty($DirectCompData)){



			foreach ($DirectCompData  as $key => $values) {

			

			$values['actions']  = $values['fk_disposition_id'];

			$values['created_date'] = date('d-m-y H:i:s', strtotime($values['created_date']));

			

			

			$direct_datas[]  = $values;

		}



		}

		$data = array_merge($app_datas,$direct_datas);

		$data['other_complaint'] = $data;



		echo json_encode($data);

	}

	



public function checkComplaintsById(){



	$complaint_id = $_POST['complaint_id'];

	$record['booking_info'] = $this->complaint_model->getCBookingInfoById($complaint_id)[0];

	echo json_encode($record);



	





	

}

public function registerOtherComp(){



	if(!empty($_POST)){



        // echo "<pre>";

        // print_r($_POST);

        // die;

		// 0:Direct 1:mobile



		$source = $_POST['complaint_source'];  

		$otherComplaintId = (empty($_POST['complaint_id'])) ? 0 : $_POST['complaint_id'] ;

		$source_type = (empty($_POST['source_type'])) ? 0 : $_POST['source_type'] ;

		

		$data  = array('fk_tbl_complaint_id'=>$otherComplaintId,

						'user_id'=>$_POST['user_id'],

						'source_type'=>$source_type,

						'complaint_type_id'=>$_POST['other_complaint_type'],

						'fk_disposition_id'=>$_POST['dispostion_id'],

						'problem_description'=>$_POST['problem_description'],

						'cc_remark'=>$_POST['cc_remark']);



		if($_POST['unique_complaint_id']!=0 && $source==0){

		$UpdateOtherComplaints = $this->complaint_model->UpdateMobileComplaints($data,$_POST['unique_complaint_id'],$source); 

		}

		else if($_POST['unique_complaint_id']!=0 && $source==1){

            $check = $this->complaint_model->CheckDataExist($_POST['unique_complaint_id']); 

            

            if(count($check)==0){

                $UpdateOtherComplaints = $this->complaint_model->ResolveMobileComplaints($data); 

            }else{

               $UpdateOtherComplaints = $this->complaint_model->UpdateMobileComplaints($data,$_POST['unique_complaint_id'],$source); 



            }

		}else{

				$UpdateOtherComplaints = $this->complaint_model->ResolveMobileComplaints($data); 

		}





	if($UpdateOtherComplaints){



		$this->session->set_flashdata('success',"Successfully Complaints Added.");

		redirect(base_url('admin/complaint/Direct_complaint'),'refresh');

	}else{



		$this->session->set_flashdata('error',"Something Went Wrong");

		redirect(base_url('admin/complaint/Direct_complaint'),'refresh');

	}

	}

}





function PendingBookingComplaints(){



		$data['counts'] = $this->complaint_model->getComplaintsCounts();

		$data['pendingComplaints'] = $this->complaint_model->getPendingBookingComplaints();

		$this->load->view('admin/includes/_header');

		$this->load->view('admin/complaint_box/pending/PendingBookingComplaints',$data);

		$this->load->view('admin/includes/_footer');



}





function PendingUserAppComplaints(){





		$other_complaints_data = $this->complaint_model->PendingUsersComplaint();

		$app_datas = array();

		if(!empty($other_complaints_data['mobile_app'])){

		foreach ($other_complaints_data['mobile_app']  as $key => $value) {

		    

		    if($value['status']==0 || $value['status']==2){

		        $status = 1;

		    }

		    $value['actions'] = $value['status'];

		    $value['fk_disposition_id'] = $status;

		    $value['created_date'] = date('d-m-Y H:i a', strtotime($value['created_date']));

			$app_datas[]  = $value;

		}

	}

		$data['other_complaint'] = $app_datas;

		$data['OtherCompFromCalls'] = $this->complaint_model->getOtherComplaintsCalls();

		$data['counts'] = $this->complaint_model->getComplaintsCounts();

		$this->load->view('admin/includes/_header');

		$this->load->view('admin/complaint_box/pending/PendingUserAppComplaints',$data);

		$this->load->view('admin/includes/_footer');



}





function PendingCallsComplaints(){



		$data['counts'] = $this->complaint_model->getComplaintsCounts();

		$data['BookingFromCalls'] = $this->complaint_model->getBookingComplaintsCalls();

		$this->load->view('admin/includes/_header');

		$this->load->view('admin/complaint_box/pending/PendingCallsComplaints',$data);

		$this->load->view('admin/includes/_footer');



}



function PendingOtherCallsComplaints(){



		$data['counts'] = $this->complaint_model->getComplaintsCounts();

		$data['OtherCompFromCalls'] = $this->complaint_model->getOtherComplaintsCalls();

		$this->load->view('admin/includes/_header');

		$this->load->view('admin/complaint_box/pending/PendingOtherComplaints.php',$data);

		$this->load->view('admin/includes/_footer');



}



function slots_complaints(){

		$this->load->view('admin/includes/_header');
		$this->load->view('admin/complaint_box/slots_complaint/viewBlockedSlots.php');
		$this->load->view('admin/includes/_footer');
}

function SlotsBlockingVerification($id){

		$records['data'] = $this->complaint_model->getSlotsInfoById($id);	
	    $this->load->view('admin/includes/_header');
		$this->load->view('admin/complaint_box/slots_complaint/SlotsBlockVerification.php',$records);
		$this->load->view('admin/includes/_footer');

}



function getBlockedSlots(){

    

    $records['data'] = $this->complaint_model->getBlockedSlots();
    $data = array();
		$i=0;
		foreach ($records['data'] as $row) {  
		   $status = ($row['complaint_status'] == 0 || $row['complaint_status'] == 1)? '': 'checked';
		   $source = ($row['complaint_source']==0) ? '<span class="badge badge-primary">Replacement</span>' : '<span class="badge badge-primary">Verifier App</span>' ;
		   $place_info = "<b>"."Place : "."</b>".$row['placename']."<br>"."<b>"."Address : "."</b>".$row['place_address']."<br>".$row['country_name'].",".$row['state'].",".$row['cityname'];
		   $var = '<input class="tgl_checkbox tgl-ios" 
				data-id="'.$row['slot_id'].'" 
				id="cb_'.$row['slot_id'].'"
				type="checkbox"  
				'.$status.'><label for="cb_'.$row['slot_id'].'"></label>';

		     $data[]= array(
				++$i,
				$place_info,
				$row['display_id'],
				date("d-m-Y H:i a", strtotime($row['issue_raised_on'])),
				$source,
				$var,

			);

		}

	    

	    $records['data']=$data;

		echo json_encode($records);	

		

    

}



function updateSlotsStatus(){

    $this->complaint_model->UpdateSlotsStatus();

}

function SlotsComplaintsVerification($id)
{

		$data['info'] = $this->complaint_model->GetDataForSlotsVerification($id);
	    $this->load->view('admin/includes/_header');
		$this->load->view('admin/complaint_box/slots_complaint/verifications/getSlotInfo.php',$data);
		$this->load->view('admin/includes/_footer');

}

 function forward_complaints(){

 	if(isset($_POST['submit'])){
 		$data = $_POST;
 		$FwdComplaint = $this->complaint_model->ForwardComplaints($data);
 		if($FwdComplaint){
 			 $this->session->set_flashdata('success', 'Successfully Slot Blocked');
 			 redirect(base_url('admin/complaint/getAllBlockedSlots'),'refresh');
 		}else{
 			 $this->session->set_flashdata('error', 'Something Went Wrong.');
 			 redirect(base_url('admin/complaint/getAllBlockedSlots'),'refresh');
 		}
 	}

 }

 function getAllBlockedSlots(){

 		$records['BlockedSlots'] = $this->complaint_model->getSlotsInfoById('0');
 		$records['PendingVerifications'] = $this->complaint_model->getSlotsInfoById('P');
 	 	$this->load->view('admin/includes/_header');
		$this->load->view('admin/complaint_box/slots_complaint/BlockedSlots.php',$records);
		$this->load->view('admin/includes/_footer');

 
 }

 function UnblockSlots(){
 	if(!empty($_POST['complaint_id'])){
 		$complaint_id = $_POST['complaint_id'];
 		$this->complaint_model->UnblockSlots($complaint_id);

 	}
 }

 function VerifySlotsComplaint($complaint_id,$place_id){

 	$records['slot_info'] = $this->complaint_model->getPendingSlotsInfo($complaint_id); 
 	$records['info'] = $this->complaint_model->getVerificationRequestById($place_id);
 	$this->load->view('admin/includes/_header');
	$this->load->view('admin/complaint_box/slots_complaint/verifications/getPendingSlotVerification.php',$records);
	$this->load->view('admin/includes/_footer');


 }

 function getAffectedBookings($complaint_id,$slot_id){
 	$records['affectedBookings'] = $this->complaint_model->getAffectedBookings($complaint_id,$slot_id); 
 	$this->load->view('admin/includes/_header');
	$this->load->view('admin/complaint_box/slots_complaint/verifications/affectedBookings.php',$records);
	$this->load->view('admin/includes/_footer');
 }

 function getClosedSlotsComplaints(){


 	$getClosedSlotsComplaints = $this->complaint_model->getClosedSlotsComplaints(); 
 	$data = array();

 	// echo "<pre>";
 	// print_r($getClosedSlotsComplaints);
 	// die;

		$i=0;

		foreach ($getClosedSlotsComplaints as $row) 

		{  

			
			$source = ($row['complaint_source']==1) ? '<span class="badge badge-warning">Guide App</span>' : '<span class="badge badge-primary">Replacement</span>' ;
			
			$place_name = "<b>"."Place Name : "."</b>".$row['placename']."<br>"."<b>"."Address : "."</b>".$row['place_address'];
			
			$issue_raised_on = date("d-m-Y H:i a", strtotime($row['issue_raised_on']));
			$issue_resolved_on = date("d-m-Y H:i a", strtotime($row['issue_resolved_on']));


		
			    $data[]= array(
				++$i,
				$place_name,
				$row['display_id'],
				$issue_raised_on,
				$issue_resolved_on,
				$source,
				'',

			);

		}
		$records['data']=$data;
		echo json_encode($records);	

 }


}



?>

