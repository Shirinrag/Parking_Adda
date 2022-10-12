<?php defined('BASEPATH') OR exit('No direct script access allowed');



class Booking extends MY_Controller {



	

	public function __construct(){

	    

		parent::__construct();

		$this->load->model('admin/Booking_model', 'booking_model');

        $this->load->model('admin/Complaint_model', 'complaint_model');

		auth_check(); // check login auth

	}



	public function All_booking(){



		$data['title'] = '';

		$data['Allbookings'] = count($this->booking_model->getAllBookingData());

		$data['Followups'] = count($this->booking_model->getFollowUpBookingData());

		



		$this->load->view('admin/includes/_header');

		$this->load->view('admin/booking/booking_list', $data);

		$this->load->view('admin/includes/_footer');

	}

	

        public function datatable_json(){				   					   

		$records['data'] = $this->booking_model->getAllBookingData();

		$data = array();

		$i=0;

		foreach ($records['data']  as $row) 

		{  

			if($row['paid_status']==0){

			$paid_status = '<span class="badge badge-warning">Yes</span>';
			}
			else{
				$paid_status = '<span class="badge badge-danger">No</span>';
			}



		

			

			if($row['booking_status']==0){

			$booking_status = '<span class="badge badge-primary">On Process</span>';

			}else if($row['booking_status']==1){

			$booking_status = '<span class="badge badge-warning">Completed</span>';

			}else if($row['booking_status']==2){

				$booking_status = '<span class="badge badge-danger">Canceled</span>';

			}

			else if($row['booking_status']==3){

				$booking_status = '<span class="badge badge-success">Review</span>';

			}else{

			    $booking_status = '<span class="badge badge-success">Replaced</span>';

			}

			

			

			if($row['booking_type']==0){

			$booking_type = '<span class="badge badge-primary">Daily</span>';

			}

			else{

				$booking_type = '<span class="badge badge-danger">Pass</span>';

			}

		
			if($row['check_type']==0){

			    $checkout = '<span class="badge badge-danger">No</span>';

			}else if($row['check_type']==1){

			    $checkout = 'G-'.date("d-m-Y h:i A", strtotime($row['check_out']));

			}else{

			    $checkout = 'A-'.date("d-m-Y h:i A", strtotime($row['check_out']));

			}

            $mobileno = $row['mobile_no'];

			$place_address = "<b>"."Place Name : "."</b>".$row['placename']."<b>|</b>"."<br>"."<b>"."Address : "."</b>".$row['place_address']."<br><b>"."Display Id : "."".$row['display_id']."</b>";	

			$booking_stack1 = date("d-m-Y h:i A", strtotime($row['booking_from_date'].$row['from_time']));

			$booking_stack2 = date("d-m-Y h:i A", strtotime($row['booking_to_date'].$row['to_time']));

			$slotname_and_id = "<b>"."Place Name : "."</b>".$row['slot_name']."<br>"."<br>"."Display Id : "."</b>".$row['display_id'];

			$username = '<a href="#" onclick= callme1("' .$row['mobile_no']. '");>';
			$b = $row['firstname']."</a>";
			$usernames = $username.$b;

				 

    

    

        $data[]= array(

				++$i,
            	$row['unique_booking_id'],
				$usernames,
    			$place_address,
				$booking_stack1,
				$booking_stack2,
				$checkout,
				$booking_status,
				$row['cost'],


			);

        

		}

		

		$records['data']=$data;

		echo json_encode($records);						   

	}

	

	

	

	

	public function status_pending(){



		$data['title'] = '';



		$this->load->view('admin/includes/_header');

		$this->load->view('admin/booking/pending_bookings', $data);

		$this->load->view('admin/includes/_footer');

	}

	public function status_completed(){



		$data['title'] = '';



		$this->load->view('admin/includes/_header');

		$this->load->view('admin/booking/completed_bookings', $data);

		$this->load->view('admin/includes/_footer');

	}



public function direct_booking(){



		$data['title'] = '';



		$this->load->view('admin/includes/_header');

		$this->load->view('admin/booking/direct_booking', $data);

		$this->load->view('admin/includes/_footer');

	}

	

	

	    public function followup_datatable_json(){				   					   

		$records['data'] = $this->booking_model->getFollowUpBookingData();

		$data = array();

		$i=0;

		foreach ($records['data']   as $row) 

		{  

			if($row['paid_status']==0){

			$paid_status = '<span class="badge badge-warning">Yes</span>';

			}

			else{

				$paid_status = '<span class="badge badge-danger">No</span>';

			}



		

			

			if($row['booking_status']==0){

			$booking_status = '<span class="badge badge-primary">On Process</span>';

			}else if($row['booking_status']==1){

			$booking_status = '<span class="badge badge-warning">Completed</span>';

			}else if($row['booking_status']==2){

				$booking_status = '<span class="badge badge-danger">Canceled</span>';

			}

			else if($row['booking_status']==3){

				$booking_status = '<span class="badge badge-success">Review</span>';

			}else{

			    $booking_status = '<span class="badge badge-success">Replaced</span>';

			}

			

			

			if($row['booking_type']==0){

			$booking_type = '<span class="badge badge-primary">Daily</span>';

			}

			else{

				$booking_type = '<span class="badge badge-danger">Pass</span>';

			}

			

			

			if($row['check_type']==0){

			    $checkout = '<span class="badge badge-danger">No</span>';

			}else if($row['check_type']==1){

			    $checkout = 'G-'.date("d-m-Y h:i A", strtotime($row['check_out']));

			}else{

			    $checkout = 'A-'.date("d-m-Y h:i A", strtotime($row['check_out']));

			}
			$place_address = "<b>"."Place Name : "."</b>".$row['placename']."<b>|</b>"."<br>"."<b>"."Address : "."</b>".$row['place_address']."<br><b>"."Display Id : "."".$row['display_id']."</b>";	
			$booking_stack1 = date("d-m-Y h:i A", strtotime($row['booking_from_date'].$row['from_time']));
			$booking_stack2 = date("d-m-Y h:i A", strtotime($row['booking_to_date'].$row['to_time']));
			$username = '<a href="#" onclick= callme1("' .$row['mobile_no']. '");>';
			$b = $row['firstname']."</a>";
			$usernames = $username.$b;


        $data[]= array(

				++$i,
            	$row['unique_booking_id'],
				$usernames,
    			$place_address,
				$booking_stack1,
				$booking_stack2,
				$checkout,
				$booking_status,
				$row['cost'],
				

			);

        

		}

		

		$records['data']=$data;

		echo json_encode($records);						   

	}

	

	

	

	

	



	

}







?>

