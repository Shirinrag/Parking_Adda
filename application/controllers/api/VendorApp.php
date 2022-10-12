<?php
defined('BASEPATH') or exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET,POST, OPTIONS");

class VendorApp extends CI_Controller
{
    // Live apis 
     public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('upload');
        $this->load->helper('cias_helper');
    }
    
    
    
    public function _returnSingle($err) {
		foreach ($err as $key => $value) {
			return $err[$key];
		}
	}
	public function tokenVerify($token){
        $jwt = new JWT();
        $jwtsecretkey = 'mpc_vendor'; //sceret key for token
        $data = $jwt->decode($token, $jwtsecretkey, true);
        $checkAuthoriz = $this->db->select('*')->from('tbl_vendor')->where('id',$data->id)->where('is_deleted','0')->get()->result();
        // $checkAuthoriz = $this->db->select('*')->from('ci_admin')->where('admin_id',$data->id)->where('admin_role_id','5')->where('is_active','1')->get()->result();
        if(count($checkAuthoriz)>0){
         return true;   
        }else{
          return false;
        }
	}
    
    public function tokenDecodeData($token){
        $jwt = new JWT();
        $jwtsecretkey = 'mpc_vendor'; //sceret key for token
        $data = $jwt->decode($token, $jwtsecretkey, true);
        return $data;
        
	}
	
	public function register_vendor(){
	    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	        
	        $this->form_validation->set_rules('name','Name','required');
	       // $this->form_validation->set_rules('lastname','Name','required');
	        $this->form_validation->set_rules('contactno','Contact Number','required|max_length[12]|min_length[10]');
	        $this->form_validation->set_rules('emailid','EmailId','required');
	        
	        if($this->form_validation->run()==false)
	        {
	            $errorMessage = $this->form_validation->error_array();
	            $msg = array('status' => false, 'message' => $this->_returnSingle($errorMessage));
                echo json_encode($msg);
	        }
	        else
	        {
	            $firstname=$this->security->xss_clean($this->input->post('name'));
	           // $lastname=$this->security->xss_clean($this->input->post('lastname'));
	            $contactno=$this->security->xss_clean($this->input->post('contactno'));
	            $emailid=$this->security->xss_clean($this->input->post('emailid'));
	            $device_id = $this->security->xss_clean($this->input->post('device_id'));
	            $device_type = $this->security->xss_clean($this->input->post('device_type'));
	            $mac_address = $this->security->xss_clean($this->input->post('mac_address'));
	           
	            $datainsert = array(
	                                    'name'=>$firstname,
	                                   // 'lastname'=>$lastname,
	                                   // 'username'=>$firstname.$lastname,
	                                   // 'admin_role_id'=>'5',
                        	            'mobileno'=>$contactno,
                        	           // 'password'=>md5($password),
                        	            'password'=>'',
                        	            'emailid'=>$emailid,
                        	            'device_id' => $device_id,
                        	            'device_type' => $device_type,
                        	            'mac_address' =>$mac_address
                        	           // 'adharcard_no'=>$aadharcardno,
                        	           // 'address'=>$venderaddress
                        	            );
	            if($this->verifynumber_email_registor($contactno,$emailid))
	            {
	                $insert = $this->db->insert('tbl_vendor',$datainsert);
    	            if($insert){
    	                $insert_id = $this->db->insert_id();
    	                $data =$this->db->select('id,name,user_img,mobileno,emailid,role_id,created_date,updated_date')
    	                ->from('tbl_vendor')->where('id',$insert_id)
    	                ->where('is_deleted','0')->get()->result(); 
    	                $jwt = new JWT();
                        $jwtsecretkey = 'mpc_vendor'; //sceret key for token
                        $token = $jwt->encode($data[0], $jwtsecretkey, 'HS256');
                        
                         $notifn_topic = $data[0]->mobileno.'MPCVendor';
                         $name = $data[0]->name;
	                $image = $data[0]->user_img;
                         $this->db->where('id',$data[0]->id)->
                         update('tbl_vendor',array('device_id'=>$device_id,
                         'device_type'=>$device_type,
                         'notifn_topic'=>$notifn_topic,
                         'token'=>$token,
                         'mac_address'=>$mac_address));
                        
    	               // $msg = array('status' => true, 'message' => 'Token successfully generated','token'=>$token);
    	               
    	                $msg = array('status' => true, 'message' => 'Successfully registered','token'=>$token,'notifn_topic'=>$notifn_topic, 'name' => $name, 'image' =>$image);
                echo json_encode($msg);
    	            }else{
    	                $msg = array('status' => false, 'message' => 'Failed to register','token'=>'','notifn_topic'=>'', 'name' => '', 'image' => '');
                echo json_encode($msg);
    	            }
	            }else{
	                $msg = array('status' => false, 'message' => 'EmailId or Contact No. already registered.','token'=>'','notifn_topic'=>'', 'name' => '', 'image' => '');
                echo json_encode($msg);
	            }
	        }
        }
	}
	
	 public function countries(){
	           $countries = $this->db->select('*')
	            ->from('ci_countries')
	            ->where('status','1')
	            ->get()
	            ->result_Array();
	            if(count($countries)>0){
	    $msg = array('status' => true, 'message' => 'List of Countries','countries'=>$countries);
                    echo json_encode($msg);}else{
                        $msg = array('status' => false, 'message' => 'No data present.','countries'=>[]);
                    echo json_encode($msg);
                    }
    }
    
    public function states(){
        $countryId = $this->input->get('countryId', TRUE);
	           $states = $this->db->select('*')
	            ->from('ci_states')
	            ->where('country_id',$countryId)
	            ->where('status','1')
	            ->get()
	            ->result_Array();
	             if(count($states)>0){
	    $msg = array('status' => true, 'message' => 'List of States','states'=>$states);
                    echo json_encode($msg);
	                 
	             }else{
                        $msg = array('status' => false, 'message' => 'No data present.','states'=>[]);
                    echo json_encode($msg);
                    }
	    
    }
	
	public function cities(){
	           $cities = $this->db->select('*')
	            ->from('ci_cities')
	            ->where('status','1')
	            ->get()
	            ->result_Array();
	    
                       if(count($states)>0){
	   $msg = array('status' => true, 'message' => 'List of Cities','cities'=>$cities);
                    echo json_encode($msg);
	                 
	             }else{
                        $msg = array('status' => true, 'message' => 'No data present.','cities'=>[]);
                    echo json_encode($msg);
                    }
    }
    
    public function support_details(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->form_validation->set_rules('token','Token','required');

        if ($this->form_validation->run()) {
            $token =  $this->security->xss_clean($this->input->post('token'));
            
             $verifyToken = $this->tokenVerify($token);
             $customerCareData = [];
	            if($verifyToken==true)
	            {
	               $tokenData = $this->tokenDecodeData($token);
	                $this->db->select('admin_id,username,firstname,lastname,email,mobile_no');
                    $this->db->from('ci_admin');
                    $this->db->where('admin_role_id', '9');
                    $this->db->where('is_active', '1');
                    $customerCareData = $this->db->get()->result();
                    
                     $msg = array('status' => true, 'message' => 'Support Details', 'session'=>'1', 'customerCareData'=> $customerCareData, 'supportEmail' => 'support@parkingadda.com');// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
                    
	            }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0');// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
            }
            else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1');
                echo json_encode($msg);
            }
        }
    }
    
    public function verify_registered_vendor(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	        $this->form_validation->set_rules('phoneNumber','Phone Number','required');
	        
	        if($this->form_validation->run()==false){
	            $errorMsg = $this->form_validation->error_array();
	             $msg = array('status' => false, 'message' => $this->_returnSingle($errorMsg));
            echo json_encode($msg);
	        }else{
	            $phoneNumber=$this->security->xss_clean($this->input->post('phoneNumber'));
	            $queryNumber = $this->db->select('id,name,user_img,mobileno,notifn_topic,adharcard_no')->from('tbl_vendor')->where('is_deleted','0')->where('mobileno',$phoneNumber)->get()->result();
	            if(count($queryNumber)>0){
	                $notifn_topic = $queryNumber[0]->notifn_topic;
	                $name = $queryNumber[0]->name;
	                $image = $queryNumber[0]->user_img;
	                $aadharNo = $queryNumber[0]->adharcard_no;
	                $jwt = new JWT();
                    $jwtsecretkey = 'mpc_vendor'; //sceret key for token
                    $token = $jwt->encode($queryNumber[0], $jwtsecretkey, 'HS256');//notifn_topic
                    $this->db->where('id',$queryNumber[0]->id)->
                         update('tbl_vendor',array(
                         'token'=>$token));
	                $msg = array('status' => true, 'message' => 'You can go ahead', 'token' => $token, 'notfn_topic' => $notifn_topic, 'name' => $name, 'image' =>$image, 'aadharNo' => $aadharNo);
                    echo json_encode($msg);
	            }
	            else{
	                $msg = array('status' => false, 'message' => 'Register yourself', 'token' => '', 'notfn_topic' => '', 'name' => '', 'image' => '', 'aadharNo' => '');
                    echo json_encode($msg);
	            }
	        }
	    }
        
    }
    
     public function verifynumber_email_registor($contactno,$emailid)
	{
	     
	            if($this->db->select('*')->from('tbl_vendor')->where('is_deleted','0')->where('mobileno',$contactno)->get()->num_rows()>0){
	                return false;
	            }
	            else if($this->db->select('*')->from('tbl_vendor')->where('is_deleted','0')->where('emailid',$emailid)->get()->num_rows()>0){
	               return false;
	            }else{
	                return true;
	            }
	}
    
    public function booking_information() //old
    {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->form_validation->set_rules('token','Token','required');
        $this->form_validation->set_rules('placeId', 'Place Id', 'required');

        if ($this->form_validation->run()) {
            $token = $this->security->xss_clean($this->input->post('token'));
            $placeId = $this->security->xss_clean($this->input->post('placeId'));
             $verifyToken = $this->tokenVerify($token);
	            if($verifyToken==true)
	            {
	               $tokenData = $this->tokenDecodeData($token);
	               
	               $this->db->select('id,placename');
                    $this->db->from('ci_parking_places');
                    $this->db->where('vendor_id', $tokenData->id);
                    $this->db->where('is_deleted', '0');
                    $place_info = $this->db->get()->result_Array();
                    
                    $placeList = [];
                    $bookings = [];
                    
                    foreach($place_info as $p){
                        array_push($placeList,$p['placename']);
                    }
                    if($placeId=='0'){
                        $this->db->select('id,vendor_id,unique_booking_id,book_ext,place_id,slot_id,booking_status,replaced_booking_id,booking_from_date,booking_to_date,from_time,to_time,booking_type,cost');
                        $this->db->from('ci_booking');
                        $this->db->where('vendor_id', $tokenData->id);
                        $this->db->where('is_deleted', '0');
                        $this->db->order_by('id DESC');
                        $bookingsList = $this->db->get()->result();
                        foreach($bookingsList as $b){
                            $this->db->select('slot_name');
                        $this->db->from('ci_parking_slot_info');
                        $this->db->where('slot_no', $b->slot_id);
                        $this->db->where('is_deleted', '0');
                        $slotName = $this->db->get()->result();
                        $this->db->select('placename');
                    $this->db->from('ci_parking_places');
                    $this->db->where('id', $b->place_id);
                    $this->db->where('is_deleted', '0');
                    $placeName = $this->db->get()->result();
                            $data = array(
                                "placeId"=>$b->place_id,
                                "placeName" => $placeName[0]->placename,
                                "slotName" => $slotName[0]->slot_name,
                                "bookingId" => $b->unique_booking_id,
                                "bookingExt" => $b->book_ext,
                                "fromDate" => $b->booking_from_date,
                                "toDate" => $b->booking_to_date,
                                "fromTime" => $b->from_time,
                                "toTime" => $b->to_time,
                                "bookingType" => $b->booking_type,
                                "cost" => $b->cost
                            );
                            array_push($bookings,$data);
                        }
                    }
                    else{
                        $this->db->select('id,unique_booking_id,book_ext,place_id,slot_id,booking_status,replaced_booking_id,booking_from_date,booking_to_date,from_time,to_time,booking_type,cost');
                        $this->db->from('ci_booking');
                        $this->db->where('place_id', $placeId);
                        $this->db->where('is_deleted', '0');
                        $this->db->order_by('id DESC');
                        $bookingsList = $this->db->get()->result_array();
                        $this->db->select('placename');
                        $this->db->from('ci_parking_places');
                        $this->db->where('id', $placeId);
                        $this->db->where('is_deleted', '0');
                        $place = $this->db->get()->result();
                        foreach($bookingsList as $b){
                            $this->db->select('slot_name');
                        $this->db->from('ci_parking_slot_info');
                        $this->db->where('slot_no', $b['slot_id']);
                        $this->db->where('is_deleted', '0');
                        $slotName = $this->db->get()->result();
                            $data = array(
                                "placeId"=>$placeId,
                                "placeName" => $place[0]->placename,
                                "slotName" => $slotName[0]->slot_name,
                                "bookingId" => $b['unique_booking_id'],
                                "bookingExt" => $b['book_ext'],
                                "fromDate" => $b['booking_from_date'],
                                "toDate" => $b['booking_to_date'],
                                "fromTime" => $b['from_time'],
                                "toTime" => $b['to_time'],
                                "bookingType" => $b['booking_type'],
                                "cost" => $b['cost']
                            );
                            array_push($bookings,$data);
                        }
                    }
                    
                    $msg = array('status' => True, 'message' => 'List of booking details','session'=>'1', 'places' => $place_info, 'data' => $bookings);
                    echo json_encode($msg);
	            }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0', 'places' => [],'data'=>[]);// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
        
            }
            else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()), 'data' => []);
                echo json_encode($msg);
            }
        }
        
    }
    
    public function bookings_list()
    {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->form_validation->set_rules('token','Token','required');
        $this->form_validation->set_rules('place_id', 'Place Id');
        $this->form_validation->set_rules('from_date', 'From Date');
        $this->form_validation->set_rules('to_date', 'To Date');

        if ($this->form_validation->run()) {
            $token = $this->security->xss_clean($this->input->post('token'));
            $placeId = $this->security->xss_clean($this->input->post('place_id'));
            $fromDate = $this->security->xss_clean($this->input->post('from_date'));
            $toDate = $this->security->xss_clean($this->input->post('to_date'));
             $verifyToken = $this->tokenVerify($token);
             $places = [
                 array(
                     "id"=>'0',
                     "placename"=>'All',
                     "place_address"=>'',
                     "no_of_slots"=>'',
                     "place_status"=>'',
                     )
                 ];
             $upcomingBookings = [];
             $todaysBookings = [];
             $pastBookings = [];
             $placeName = 'All';
             if($fromDate==''){
                 $fromDate = Date('Y-m-d', strtotime('-8 days'));
             }
             if($toDate == ''){
                 $toDate = Date('Y-m-d', strtotime('-1 days'));
             }
	            if($verifyToken==true)
	            {
	                $tokenData = $this->tokenDecodeData($token);
	                $this->db->select('id,placename,place_address,no_of_slots,place_status');
                    $this->db->from('ci_parking_places');
                    $this->db->where('vendor_id', $tokenData->id);
                    $this->db->where('is_deleted', '0');
                    $placesData = $this->db->get()->result_Array();
                    
                    foreach($placesData as $p){
                        array_push($places,$p);
                    }
                    
                    if($placeId != '0'){
                        $this->db->select('placename');
                    $this->db->from('ci_parking_places');
                    $this->db->where('id', $placeId);
                    // $this->db->where('is_deleted', '0');
                    $result = $this->db->get()->result();
                    
                    // print_r($result);
                    // exit();
                    
                    $placeName = $result[0]->placename;
                    } else {
                        $placeName = 'All';
                    }
                    
                    $this->db->select('booking.*,place.placename,place.place_address,slot.slot_no,slot.slot_name,slot.display_id');
                    $this->db->from('ci_booking as booking');
                    $this->db->join('ci_parking_places as place', 'booking.place_id = place.id');
                    $this->db->join('ci_parking_slot_info as slot', 'booking.slot_id = slot.slot_no');
                    $this->db->where('booking.vendor_id', $tokenData->id);
                    // $this->db->where('booking.booking_status', '0');
                    $this->db->group_start()->where('booking.booking_status','0')->or_where('booking.booking_status','1')->group_end();
                    $this->db->where('booking.is_deleted', '0');
                    $this->db->order_by('booking.id desc');
                    $bookings = $this->db->get()->result();
                    foreach($bookings as $b){
                        $b->from_time=date('h:i a',strtotime($b->from_time));
                        $b->to_time=date('h:i a',strtotime($b->to_time));
                        if($b->booking_from_date == date('Y-m-d')){
                            if($placeId=='0'){
                            array_push($todaysBookings,$b);
                            }else{
                                if($b->place_id == $placeId){
                                    array_push($todaysBookings,$b);
                                }
                            }
                            // array_push($todaysBookings,$b);
                        } else if(date('Y-m-d',strtotime($b->booking_from_date)) >= $fromDate && date('Y-m-d',strtotime($b->booking_to_date)) <= $toDate){
                            if($placeId=='0'){
                            array_push($pastBookings,$b);
                            }else{
                                if($b->place_id == $placeId){
                                    array_push($pastBookings,$b);
                                }
                            }
                        } else if($b->booking_from_date >= date('Y-m-d')){
                            if($placeId=='0'){
                            array_push($upcomingBookings,$b);
                            }else{
                                if($b->place_id == $placeId){
                                    array_push($upcomingBookings,$b);
                                }
                            }
                            // array_push($upcomingBookings,$b);
                        }
                    }
                    $msg = array('status' => true, 'message' => 'Booking List','session'=>'1','placeName'=>$placeName,'fromDate'=>$fromDate, 'toDate'=>$toDate, 'places' => $places,'upcomingBookings'=>$upcomingBookings,'todaysBookings'=>$todaysBookings,'pastBokings'=>$pastBookings);// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0', 'placeName'=>'','fromDate'=>'', 'toDate'=>'', 'places' => $places,'data'=>[]);// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
            }
            else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()), 'data' => []);
                echo json_encode($msg);
            }
        }
        
    }
    
    public function placeSlotOnOff() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $this->form_validation->set_rules('token','Token','required');
        $this->form_validation->set_rules('id','Id','required');  // it can be place id or slot id
        $this->form_validation->set_rules('placetype','Place type','required'); // place type is 0 = Places or 1 = Slot
        $this->form_validation->set_rules('status','Status','required'); // 0=on 1=off
        $this->form_validation->set_rules('date','Date','required'); // place or slot off date

        if ($this->form_validation->run()) {
            $token =  $this->security->xss_clean($this->input->post('token'));
            $id =  $this->security->xss_clean($this->input->post('id'));
            $placetype=  $this->security->xss_clean($this->input->post('placetype'));
            $status =  $this->security->xss_clean($this->input->post('status'));
            $date =  $this->security->xss_clean($this->input->post('date'));
            
             $verifyToken = $this->tokenVerify($token);
	            if($verifyToken==true)
	            {
	               $tokenData = $this->tokenDecodeData($token);
	               
	                $getData =$this->db->select('*')->from($placetype=='0'?'ci_parking_places':'ci_parking_slot_info')
	                ->where($placetype=='0'?'id':'slot_no',$id)->get()->result();
	                
	                if(count($getData)>0){
	                    $vendorid_d = $getData[0]->vendor_id;
	                    if($vendorid_d==$tokenData->id){
	                        if($placetype=='0'){//places
	                        //status
	                        if($getData[0]->status=='0'){
	                            
	                            if($status=='0'){
	                               $update =$this->db->where('id',$id)->update('ci_parking_places',array('onOff_applied'=>$status=='0'?'1':'2',
        	                           'onOff_apply_date'=>$date));
        	                           if($update){
        	                               $getSlotList = $this->db->select('*')->from('ci_parking_slot_info')->where('place_id',$id)->get()->result();
        	                               foreach($getSlotList as $d){
        	                                   $update =$this->db->where('slot_no',$d->slot_no)->update('ci_parking_slot_info',array('onOff_applied'=>$status=='0'?'1':'2',
        	                           'onOff_apply_date'=>$date));
        	                               }
        	                               $msg = array('status' => true, 'message' => $status=='0'?'Successfully applied for  Activation':'Successfully applied for Inactivation','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                                         echo json_encode($msg);
        	                           }
        	                            else{
        	                               $msg = array('status' => false, 'message' => 'Request failed.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                                           echo json_encode($msg);
        	                           }
	                            }else{
	                            
	                            $getLastBookingData = $this->db->select('*')->from('ci_booking')
	                           ->where('place_id',$id)
	                            ->order_by('booking_to_date DESC')->get()->result();
	                            if(count($getLastBookingData)>0){
	                                if($getLastBookingData[0]->booking_to_date>=date('Y-m-d',strtotime($date))){
	                                    $msg = array('status' => false, 'message' => 'You cannot choose this date. As your last booking date is '.$getLastBookingData[0]->booking_to_date,'session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                                   echo json_encode($msg);
	                                }else{
	                                    $update =$this->db->where('id',$id)->update('ci_parking_places',array('onOff_applied'=>$status=='0'?'1':'2',
	                           'onOff_apply_date'=>$date));
	                           if($update){
	                               $getSlotList = $this->db->select('*')->from('ci_parking_slot_info')->where('place_id',$id)->get()->result();
	                               foreach($getSlotList as $d){
	                                   $update =$this->db->where('slot_no',$d->slot_no)->update('ci_parking_slot_info',array('onOff_applied'=>$status=='0'?'1':'2',
	                           'onOff_apply_date'=>$date));
	                               }
	                               $msg = array('status' => true, 'message' => $status=='0'?'Successfully applied for Activation':'Successfully applied for Inactivation','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                                 echo json_encode($msg);
	                           }
	                            else{
	                               $msg = array('status' => false, 'message' => 'Request failed.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                                   echo json_encode($msg);
	                           }
	                                }
	                            }
	                            else{
	                           $update =$this->db->where('id',$id)->update('ci_parking_places',array('onOff_applied'=>$status=='0'?'1':'2',
	                           'onOff_apply_date'=>$date));
	                           if($update){
	                               $getSlotList = $this->db->select('*')->from('ci_parking_slot_info')->where('place_id',$id)->get()->result();
	                               foreach($getSlotList as $d){
	                                   $update =$this->db->where('slot_no',$d->slot_no)->update('ci_parking_slot_info',array('onOff_applied'=>$status=='0'?'1':'2',
	                           'onOff_apply_date'=>$date));
	                               }
	                               $msg = array('status' => true, 'message' => $status=='0'?'Successfully applied for Activation':'Successfully applied for Inactivation','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                                 echo json_encode($msg);
	                           }
	                            else{
	                               $msg = array('status' => false, 'message' => 'Request failed.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                                   echo json_encode($msg);
	                           }
	                                
	                            }
	                                
	                            }
	                            
	                        }
	                           else{
	                               $msg = array('status' => false, 'message' => 'Not allowed for monthly Places.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                                   echo json_encode($msg);
	                           }
	                        }
	                        else{//slots
	                           // tbl_parking_slot_info
	                           //print($getData[0]->vendor_id);
	                           $checkFeasibility = $this->db->select('*')->from('ci_parking_slot_info')->where('vendor_id',$getData[0]->vendor_id)->where('id',$id)->get()->result();
	                           if(count($checkFeasibility)>0){
	                               //if($checkFeasibility[0]->status=='0'){
	                                   
	                                   if($status=='0'){
	                                        $update =$this->db->where('slot_no',$id)->update('ci_parking_slot_info',array('onOff_applied'=>$status=='0'?'1':'2',
	                           'onOff_apply_date'=>$date));
	                           
	                           if($update){
	                               $msg = array('status' => true, 'message' => $status=='0'?'Successfully applied for Activation':'Successfully applied for Inactivation','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                            echo json_encode($msg);
	                           }
	                           else{
	                               $msg = array('status' => false, 'message' => 'Request failed.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                            echo json_encode($msg);
	                           }
	                                   }
	                                   else{
	                                   
	                           $getLastBookingData = $this->db->select('*')->from('ci_booking')
	                            ->order_by('booking_to_date DESC')->where('slot_id',$id)->get()->result();
	                                 if(count($getLastBookingData)>0){
	                                if($getLastBookingData[0]->booking_to_date>=date('Y-m-d',strtotime($date))){
	                                    $msg = array('status' => false, 'message' => 'You cannot choose this date. As your last booking date is '.$getLastBookingData[0]->booking_to_date,'session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                                   echo json_encode($msg);
	                                }else{
	                                    $update =$this->db->where('slot_no',$id)->update('ci_parking_slot_info',array('onOff_applied'=>$status=='0'?'1':'2',
	                           'onOff_apply_date'=>$date));
	                           
	                           if($update){
	                               $msg = array('status' => true, 'message' => $status=='0'?'Successfully applied for Activation':'Successfully applied for Inactivation','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                            echo json_encode($msg);
	                           }
	                           else{
	                               $msg = array('status' => false, 'message' => 'Request failed.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                            echo json_encode($msg);
	                           }
	                           
	                                }
	                            }
	                                else{
	                           $update =$this->db->where('slot_no',$id)->update('ci_parking_slot_info',array('onOff_applied'=>$status=='0'?'1':'2',
	                           'onOff_apply_date'=>$date));
	                           
	                           if($update){
	                               $msg = array('status' => true, 'message' => $status=='0'?'Successfully applied for Activation':'Successfully applied for Inactivation','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                            echo json_encode($msg);
	                           }
	                           else{
	                               $msg = array('status' => false, 'message' => 'Request failed.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                            echo json_encode($msg);
	                           }
	                            }
	                                   }
	                               //}
	                           //else{
	                           //    $msg = array('status' => false, 'message' => 'Not allowed for monthly Places.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                            // echo json_encode($msg);
	                           //}
	                               
	                           }
	                           else{
	                               $msg = array('status' => false, 'message' => 'No such slot present.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                            echo json_encode($msg);
	                           }
	                           
	                        }
	                       // print('it is equal to vendor');
	                    }
	                    else
	                    {
	                       $msg = array('status' => false, 'message' => 'Not authorized to edit this place.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                            echo json_encode($msg);
	                    }
	                }
	                else
	                {
	                     $msg = array('status' => false, 'message' => $placetype=='0'?'No Place is present':'No Slot is present','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                         echo json_encode($msg);
	                }
	              
	               
                   
	            }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0');// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
        
            }
            else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()), 'data' => []);
                echo json_encode($msg);
            }
        }
    }
    
    public function user_profile(){
	    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	        $this->form_validation->set_rules('token','Token','required');
	       // $this->form_validation->set_rules('loginMethod','EmailId','required'); // loginMethod =1 email,2= phoneNo
	        if($this->form_validation->run()==false)
	        {
	             $errorMsg = $this->form_validation->error_array();
	             $msg = array('status' => false, 'message' => $this->_returnSingle($errorMsg));
                 echo json_encode($msg);
	        }
	        else
	        {
	            $token= $this->security->xss_clean($this->input->post('token'));
	            $verifyToken = $this->tokenVerify($token);
	            $userDetails1 =array("id"=> "",
                                "name"=> "",
                                "mobileno"=> "",
                                "emailid"=> "",
                                "adharcard_no"=> "",
                                "address"=> "",
                                "user_img"=> "",
                                "aadharcard_img"=>"");
	               $bankDetail1 =
                    	array(
                            "id"=>"",
                            "vendor_id"=> "",
                            "account_name"=> "",
                            "bank_name"=> "",
                            "account_number"=> "",
                            "ifsc_code"=> "",
                            "mobile_no"=> "",
                            "cancelled_cheque"=> "",
                            "verify_status" => ""
                        );
	            if($verifyToken==true)
	            {
	                /*id	name	mobileno	emailid	adharcard_no	address	 	user_img	aadharcard_img*/
	               $tokenData = $this->tokenDecodeData($token);
	                $userData = $this->db->select('id,name,mobileno,emailid,adharcard_no,address,user_img,aadharcard_img,is_deleted')->from('tbl_vendor')->where('id',$tokenData->id)->where('is_deleted','0')->get()->result();
	               $bankDetails = $this->db->select('id,vendor_id,account_name,bank_name,account_number,ifsc_code,mobile_no,cancelled_cheque,verify_status,is_deleted')->
	               from('tbl_vendor_bankdetails')->where('vendor_id',$tokenData->id)->where('is_deleted','0')->get()->result();
	               
	               $userDetails1 = count($userData)>0?$userData[0]:array("id"=> "",
                                "name"=> "",
                                "mobileno"=> "",
                                "emailid"=> "",
                                "adharcard_no"=> "",
                                "address"=> "",
                                "user_img"=> "",
                                "aadharcard_img"=>"");
	               $bankDetail1 =count($bankDetails)>0?$bankDetails[0]: 
                    	array(
                            "id"=>"",
                            "vendor_id"=> "",
                            "account_name"=> "",
                            "bank_name"=> "",
                            "account_number"=> "",
                            "ifsc_code"=> "",
                            "mobile_no"=> "",
                            "cancelled_cheque"=> "",
                            "verify_status" => ""
                        );
	                   $msg = array('status' => true, 'message' => 'User Details','session'=>'1','userdata'=>$userDetails1,
	                   'bankDetails'=>$bankDetail1); // session ( 1= session is maintained , 0 = make user logout)
                       echo json_encode($msg);
	               
	            }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0','userdata'=>$userDetails1,
	                   'bankDetails'=>$bankDetail1);// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
	        }
	        
	    }
	}
	
    public function profileImageChange(){
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->form_validation->set_rules('token','Token','required');
        $this->form_validation->set_rules('image_data', 'Image', 'required');

        if ($this->form_validation->run()) {
            $token =  $this->security->xss_clean($this->input->post('token'));
            $image_base64 =  $this->security->xss_clean($this->input->post('image_data'));
            // $token = $this->input->post('token');
             $verifyToken = $this->tokenVerify($token);
	            if($verifyToken==true)
	            {
                $tokenData = $this->tokenDecodeData($token);
                $image = base64_decode($image_base64);
                $path='';
                $imagename = md5(uniqid(rand(), true));
                $path = "./uploads/";
                $filename = $imagename. '.' . 'png';
                file_put_contents($path.$filename, $image);  
                 $data = array(
                   'user_img'=>base_url().'uploads/'.$filename
                   );
                   $update=$this->db->where('id',$tokenData->id)->update('tbl_vendor',$data);
                    if($update){
                        $msg = array('status' => true,
					'message' =>'Profile image successfully updated','session'=>'1' );
                    echo json_encode($msg);
                }
                else{
                    $msg = array('status' => false,
					'message' => 'Failed to update image','session'=>'1' );
                    echo json_encode($msg);
                   
                }
                
	            }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0');// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
        
            }
            else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1' );
                echo json_encode($msg);
            }
        }
    }
    
    public function aadharCardDetails(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->form_validation->set_rules('token','Token','required');
        $this->form_validation->set_rules('image_data', 'Image');
        $this->form_validation->set_rules('aadharCardNo', 'Aadharcard Number');
        $this->form_validation->set_rules('upload_type', 'Upload Type', 'required'); // 0 = Aadhar Number & Image, 1 = Aadhar Number, 2 = Image

        if ($this->form_validation->run()) {
            $token =  $this->security->xss_clean($this->input->post('token'));
            $image_base64 =  $this->security->xss_clean($this->input->post('image_data'));
            $aadharCardNo =  $this->security->xss_clean($this->input->post('aadharCardNo'));
            $upload_type =  $this->security->xss_clean($this->input->post('upload_type'));
            
             $verifyToken = $this->tokenVerify($token);
	            if($verifyToken==true)
	            {
	               $tokenData = $this->tokenDecodeData($token);
	               if($upload_type=='0'){
	                   $image = base64_decode($image_base64);
                    $path='';
                    $imagename = md5(uniqid(rand(), true));
                    $path = "./uploads/";
                    $filename = $imagename. '.' . 'png';
                    file_put_contents($path.$filename, $image);
                    $data = array('aadharcard_img'=>base_url().'uploads/'.$filename,'adharcard_no'=>$aadharCardNo);
	                   $verifyAadharNo = $this->db->select('*')->from('tbl_vendor')->where('is_deleted','0')->where('adharcard_no',$aadharCardNo)->get()->result();
	                   if(count($verifyAadharNo)>0){
	                       $msg = array('status' => false, 'message' => 'AadharCard number already registered.','session'=>'1');
                         echo json_encode($msg);
	                   } else {
	                       $insert = $this->db->where('id',$tokenData->id)->update('tbl_vendor',$data);
	                   if($insert){
	                     $msg = array('status' => true, 'message' => 'Successfully updated details.', 'session'=>'1');
                         echo json_encode($msg);
	                   }
                         else{
                             $msg = array('status' => false, 'message' => 'Failed to update details.','session'=>'1');
                         echo json_encode($msg);
                         }
	                   }
	               } else if($upload_type=='1'){
	                   $data = array('adharcard_no'=>$aadharCardNo);
	                   $verifyAadharNo = $this->db->select('*')->from('tbl_vendor')->where('is_deleted','0')->where('adharcard_no',$aadharCardNo)->get()->result();
	                   if(count($verifyAadharNo)>0){
	                       $msg = array('status' => false, 'message' => 'AadharCard number already registered.','session'=>'1');
                         echo json_encode($msg);
	                   } else {
	                       $insert = $this->db->where('id',$tokenData->id)->update('tbl_vendor',$data);
	                   if($insert){
	                     $msg = array('status' => true, 'message' => 'Successfully updated details.', 'session'=>'1');
                         echo json_encode($msg);
	                   }
                         else{
                             $msg = array('status' => false, 'message' => 'Failed to update details.','session'=>'1');
                         echo json_encode($msg);
                         }
	                   }
	               } else if($upload_type=='2'){
	                    $image = base64_decode($image_base64);
                    $path='';
                    $imagename = md5(uniqid(rand(), true));
                    $path = "./uploads/";
                    $filename = $imagename. '.' . 'png';
                    file_put_contents($path.$filename, $image);
                    $data = array('aadharcard_img'=>base_url().'uploads/'.$filename);
	                   $insert = $this->db->where('id',$tokenData->id)->update('tbl_vendor',$data);
	                   if($insert){
	                     $msg = array('status' => true, 'message' => 'Successfully updated details.', 'session'=>'1');
                         echo json_encode($msg);
	                   }
                         else{
                             $msg = array('status' => false, 'message' => 'Failed to update details.','session'=>'1');
                         echo json_encode($msg);
                         }
	               }
	            }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0');// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
            }
            else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1');
                echo json_encode($msg);
            }
        }
    }
    
    public function vendorNameUpdate(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->form_validation->set_rules('token','Token','required');
        $this->form_validation->set_rules('name', 'Name', 'required');

        if ($this->form_validation->run()) {
            $token =  $this->security->xss_clean($this->input->post('token'));
            $name =  $this->security->xss_clean($this->input->post('name'));
            
             $verifyToken = $this->tokenVerify($token);
	            if($verifyToken==true)
	            {
	               $tokenData = $this->tokenDecodeData($token);
	               $insert = $this->db->where('id',$tokenData->id)->update('tbl_vendor',array('name'=>$name));
	                   if($insert){
	                     $msg = array('status' => true, 'message' => 'Successfully updated details.', 'session'=>'1');
                         echo json_encode($msg);
	                   }
                         else {
                             $msg = array('status' => false, 'message' => 'Failed to update details.','session'=>'1');
                         echo json_encode($msg);
                         }
	               }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0');// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
        
            }
            else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1');
                echo json_encode($msg);
            }
        }
    }
    
    public function addBankDetails(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $this->form_validation->set_rules('token','Token','required');
        $this->form_validation->set_rules('account_name','Account holder name','required');
        $this->form_validation->set_rules('bank_name','Bank name','required');
        $this->form_validation->set_rules('account_number','Account number','required');
        $this->form_validation->set_rules('ifsc_code','IFSC code','required');
        $this->form_validation->set_rules('mobile_no','Mobile number','required');
        $this->form_validation->set_rules('cancelled_cheque_img','Mobile number','required');
        if ($this->form_validation->run()) {
            $token =  $this->security->xss_clean($this->input->post('token'));
            $account_name =  $this->security->xss_clean($this->input->post('account_name'));
            $bank_name =  $this->security->xss_clean($this->input->post('bank_name'));
            $account_number =  $this->security->xss_clean($this->input->post('account_number'));
            $ifsc_code =  $this->security->xss_clean($this->input->post('ifsc_code'));
            $mobile_no =  $this->security->xss_clean($this->input->post('mobile_no'));
            $cancelled_cheque_img =  $this->security->xss_clean($this->input->post('cancelled_cheque_img'));
            
            // $token = $this->input->post('token');
             $verifyToken = $this->tokenVerify($token);
	            if($verifyToken==true)
	            {
	                $image = base64_decode($cancelled_cheque_img);
                     $path='';
                     $imagename = md5(uniqid(rand(), true));
                     $path = "./uploads/";
                     $filename = $imagename. '.' . 'png';
                     file_put_contents($path.$filename, $image);  
                //     $data = $imageType=='0'? array(
                //   'user_img'=>base_url().'uploads/'.$filename
                //   ):array(
                //   'aadharcard_img'=>base_url().'uploads/'.$filename
                //   );
	               $tokenData = $this->tokenDecodeData($token);
                    $datainsert = array('vendor_id'=>$tokenData->id,'account_name'=>$account_name, 'bank_name' =>$bank_name,'account_number'=>$account_number, 'ifsc_code'=>$ifsc_code,
                    'mobile_no'=>$mobile_no,'cancelled_cheque'=>base_url().'uploads/'.$filename);
                 $verify = $this->db->select('*')->from('tbl_vendor_bankdetails')->where('vendor_id',$tokenData->id)->where('is_deleted','0')->get()->result();
                //  if(count($verify)<=0){
                if(count($verify)>0){
                    $data = array('is_deleted'=>'1');
                    $update=$this->db->where('vendor_id',$tokenData->id)->update('tbl_vendor_bankdetails',$data);
                }
                   $insert = $this->db->insert('tbl_vendor_bankdetails',$datainsert);
                   if($insert){
                       $msg = array('status' => true, 'message' => 'Bank details inserted successfully.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
                   }
                   else{
                       $msg = array('status' => false, 'message' => 'Failed to insert successfully.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
                   }
                //  }
                //  else{
                //       $msg = array('status' => false, 'message' => 'Data is already present','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                //     echo json_encode($msg);
                //  }
                   
	            }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0');// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
        
            }
            else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()), 'data' => []);
                echo json_encode($msg);
            }
        }
    }
    
    public function reportViewApi(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $this->form_validation->set_rules('token','Token','required');
        $this->form_validation->set_rules('fromDate','FromDate','required'); //Y eg.(2021)
        $this->form_validation->set_rules('toDate','ToDate','required'); //Y-m eg.(2021-08)
        if ($this->form_validation->run()) {
            $token =  $this->security->xss_clean($this->input->post('token'));
            $fromDate =  $this->security->xss_clean($this->input->post('fromDate'));
            $toDate =  $this->security->xss_clean($this->input->post('toDate'));
            $listmonth=[];
            // $token = $this->input->post('token');
             $verifyToken = $this->tokenVerify($token);
	            if($verifyToken==true)
	            {
	               // print_r($this->db->last_query());
	               // exit();
	               $tokenData = $this->tokenDecodeData($token);
	               $getplaces = $this->db->select('id,vendor_id,placename,place_address,no_of_slots')->from('ci_parking_places')->where('vendor_id',$tokenData->id)->where('place_status','1')->get()->result();
	               $totalEarning =0;
	               $totalNoBooking=0;
	               foreach($getplaces as $place){
	                   
	               $getBooking = $this->db->select('*')->from('ci_booking')->where('place_id',$place->id)
	               ->where('DATE(created_date) >=',DATE($fromDate))
	               ->where('DATE(created_date) <=',DATE($toDate))->where('is_deleted','0')->get()->result();
	                // print_r($this->db->last_query());
	               // exit();
	               $onePlaceEarning =0;
	               
	               //print('rk----');
	               //print_r($getBooking);
    	               foreach($getBooking as $booking){
    	                  $onePlaceEarning= $onePlaceEarning + $booking->cost;
    	               }
    	               $place->earning=$onePlaceEarning;
    	           //    print('rk----');
	               //print_r($place);
    	               $totalEarning= $totalEarning + $place->earning;
    	               $totalNoBooking = $totalNoBooking +count($getBooking);
	               }
	               $msg = array('status' => true, 'message' => 'Reportview.','session'=>'1','totalEarning'=>$totalEarning,'totalBooking'=>$totalNoBooking,'placesList'=>$getplaces);// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	             }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0');// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
        
            }
            else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1');
                echo json_encode($msg);
            }
        }
    }
    
    public function support_enforce_Insert(){
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->form_validation->set_rules('token','Token','required');
        $this->form_validation->set_rules('subject','Subject','required');
        $this->form_validation->set_rules('desc','Description','required');
        $this->form_validation->set_rules('type','Type','required');// 0=support,1=enforcement 
        /*	id 	vendor_id 	type 0=support,1=enforcement 	subject 	desc 	status 	is_deleted 	onCreated 	onUpdated */
        // $this->form_validation->set_rules('token', 'Token', 'required');

        if ($this->form_validation->run()) {
            $token =  $this->security->xss_clean($this->input->post('token'));
            $subject =  $this->security->xss_clean($this->input->post('subject'));
            $desc =  $this->security->xss_clean($this->input->post('desc'));
            $type =  $this->security->xss_clean($this->input->post('type'));
             $verifyToken = $this->tokenVerify($token);
	            if($verifyToken==true)
	            {
	               $tokenData = $this->tokenDecodeData($token);
	               $update = $this->db->insert('tbl_vendor_support',array('vendor_id'=>$tokenData->id,'type'=>$type,'subject'=>$subject,'desc'=>$desc));
	               if($update){
	                   $msg = array('status' => true, 'message' => 'Successfully registered the request.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	               }
	               else{
	                   $msg = array('status' => false, 'message' => 'Failed to register the request.','session'=>'1');// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	               }
                   
	            }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0');// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
        
            }
            else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()), 'data' => []);
                echo json_encode($msg);
            }
        }
    }
    
    public function vendor_places_status(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $this->form_validation->set_rules('token','Token','required');
        if ($this->form_validation->run()) {
            $token =  $this->security->xss_clean($this->input->post('token'));
             $verifyToken = $this->tokenVerify($token);
             $data = [];
	            if($verifyToken==true)
	            {
	               $tokenData = $this->tokenDecodeData($token);
                 $places = $this->db->select('id,vendor_id,placename,place_address,no_of_slots,status,place_status,is_deleted')->from('ci_parking_places')->where('vendor_id',$tokenData->id)->get()->result_array();
                 foreach($places as $v){
                     if($v['is_deleted'] == '0'){
                         array_push($data,$v);
                     }
                 }
                //  print_r($data);
                //  exit();
                 if(count($data)>0){
                       $msg = array('status' => true, 'message' => 'List of vendor places','session'=>'1','data'=>$data);// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
                   }
	            }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0', 'data' => []);// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
            }
            else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()), 'data' => []);
                echo json_encode($msg);
            }
        }
    }
    
    public function vendor_places(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $this->form_validation->set_rules('token','Token','required');
        if ($this->form_validation->run()) {
            $token =  $this->security->xss_clean($this->input->post('token'));
             $verifyToken = $this->tokenVerify($token);
             $data = [];
	            if($verifyToken==true)
	            {
	               $tokenData = $this->tokenDecodeData($token);
                 $places = $this->db->select('id,vendor_id,placename,place_address,no_of_slots,status,place_status,onOff_applied,onOff_apply_date,is_deleted')->from('ci_parking_places')->where('vendor_id',$tokenData->id)->get()->result_array();
                 foreach($places as $v){
                     if($v['is_deleted'] == '0'){
                         $place = array(
                                "placeId"=>$v['id'],
                                "placeName" => $v['placename'],
                                "placeAddress" => $v['place_address'],
                                "noOfSlots" => $v['no_of_slots'],
                                "status" => $v['status'],
                                "placeStatus" => $v['place_status']==0?false:true,
                                "onOffApplied" => $v['onOff_applied'],
                                "appliedDate" => $v['onOff_apply_date']==''?"":date_format(date_create($v['onOff_apply_date']),"d M Y")
                            );
                         array_push($data,$place);
                     }
                 }
                //  print_r($data);
                //  exit();
                 if(count($data)>0){
                       $msg = array('status' => true, 'message' => 'List of vendor places','session'=>'1','data'=>$data);// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
                   }
	            }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0', 'data' => []);// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
            }
            else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()), 'data' => []);
                echo json_encode($msg);
            }
        }
    }
    
    public function place_slots(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->form_validation->set_rules('placeId','Place Id','required');
        $this->form_validation->set_rules('token','Token','required');
        if ($this->form_validation->run()) {
            $placeId =  $this->security->xss_clean($this->input->post('placeId'));
            $token =  $this->security->xss_clean($this->input->post('token'));
             $verifyToken = $this->tokenVerify($token);
             $data = [];
	            if($verifyToken==true)
	            {
	             $tokenData = $this->tokenDecodeData($token);
	             $place = $this->db->select('id,vendor_id,placename,place_address,no_of_slots,status,place_status,onOff_applied,onOff_apply_date,is_deleted')->from('ci_parking_places')->where('id',$placeId)->where('is_deleted','0')->get()->result_array();
                 $placeData = array(
                                "placeId"=>$place[0]['id'],
                                "placeName" => $place[0]['placename'],
                                "placeAddress" => $place[0]['place_address'],
                                "noOfSlots" => $place[0]['no_of_slots'],
                                "status" => $place[0]['status'],
                                "placeStatus" => $place[0]['place_status']==0?false:true,
                                "onOffApplied" => $place[0]['onOff_applied'],
                                "appliedDate" => $place[0]['onOff_apply_date']==''?"":date_format(date_create($place[0]['onOff_apply_date']),"d M Y")
                            );
                 $slots = $this->db->select('slot_no,place_id,slot_no,slot_name,display_id,status,onOff_applied,onOff_apply_date,is_deleted')->from('ci_parking_slot_info')->where('place_id',$placeId)->where('is_deleted','0')->get()->result_array();
                 foreach($slots as $v){
                     $slotData = array(
                                "slotId"=>$v['slot_no'],
                                "placeId" => $v['place_id'],
                                "slotNumber" => $v['slot_no'],
                                "slotName" => $v['slot_name'],
                                "displayId" => $v['display_id'],
                                "slotStatus" => $v['status']==0?true:false,
                                "onOffApplied" => $v['onOff_applied'],
                                "appliedDate" => $v['onOff_apply_date']==''?"":date_format(date_create($v['onOff_apply_date']),"d M Y")
                            );
                         array_push($data,$slotData);
                 }
                //  print_r($data);
                //  exit();
                 if(count($data)>0){
                       $msg = array('status' => true, 'message' => 'List of Slots','session'=>'1','place'=>$placeData,'data'=>$data);// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
                   }
	            }
	            else{
	                $msg = array('status' => false, 'message' => 'Access desnied for this user.','session'=>'0','place'=>'', 'data' => []);// session ( 1= session is maintained , 0 = make user logout)
                    echo json_encode($msg);
	            }
            }
            else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()), 'data' => []);
                echo json_encode($msg);
            }
        }
    }
    
    public function dashboard_details(){
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             $this->form_validation->set_rules('token', 'Token', 'required');
             
             if ($this->form_validation->run()) {
                 $token =  $this->security->xss_clean($this->input->post('token'));
                 $vendor_id =  $this->security->xss_clean($this->tokenVerify($token));
                 $token_data = $this->tokenDecodeData($token);
                 
                 
                 if($vendor_id==true){
                 
                     $this->db->select('*');
                     $this->db->from('ci_parking_places');
                     $this->db->where('vendor_id', $token_data->id);
                     $this->db->where('place_status', "1");
                     $place_info = $this->db->get()->result_Array();
                     
                    //  print_r($place_info);
                    //  exit();
                     
                     date_default_timezone_set('Asia/Kolkata');
                     $current_time = date("H:i:s");
                     $current_date = date("Y-m-d");
                     
                     
                     if(!empty($place_info)){
                         
                         $place_status = [];
                         $slot_status = [];
                         $total_slot_count = [];
                         $place_data = [];
                         $bookings = [];
                         $today_bookings = [];
                         $monthly_earning = [];
                         $booked_slots = [];
                         $total_earning = [];
                        
                         
                         $i = 0;
                         foreach($place_info as $v){
                             
                            array_push($place_status, $place_info[$i]['place_status']);
                            
                            $this->db->select('count(slot_id) as booked_slots');
                            $this->db->from('ci_booking');
                            $this->db->where('place_id', $place_info[$i]["id"]);
                            $this->db->where('booking_status', '0' );
                            $this->db->where('is_deleted', '0' );
                            $booked_slots_info = $this->db->get()->result_Array();
                            array_push($booked_slots, $booked_slots_info[0]["booked_slots"]);
                            
                            $this->db->select_sum('cost');
                            $this->db->from('ci_booking');
                            $this->db->where('place_id', $place_info[$i]["id"]);
                            $this->db->where('MONTH(created_date)', date('m'));
                            $this->db->where('is_deleted', '0' );
                            $earningMonthly = $this->db->get()->result_Array();
                    
                            $this->db->select_sum('cost');
                            $this->db->from('ci_booking');
                            $this->db->where('place_id', $place_info[$i]["id"]);
                            $this->db->where('is_deleted', '0' );
                            $earningTotal = $this->db->get()->result_Array();
                            
                            if($earningTotal[0]['cost'] == null){
                                $earningTotal[0]['cost'] = "0";
                            }
                            if($earningMonthly[0]['cost'] == null){
                                $earningMonthly[0]['cost'] = "0";
                            }
                            
                            $data = array (
                                        "placeId" => $place_info[$i]['id'],
                                        "placeName" => $place_info[$i]['placename'],
                                        "placeAddress" => $place_info[$i]['place_address'],
                                        "noOfSlots" => $place_info[$i]['no_of_slots'],
                                        "cost" => $earningTotal[0]['cost'],
                                        "placeStatus" => $place_info[$i]['place_status']
                                    );
                        
                        $this->db->select('id,unique_booking_id,book_ext,place_id,slot_id,booking_status,replaced_booking_id,booking_from_date,booking_to_date,from_time,to_time,booking_type,cost');
                        $this->db->from('ci_booking');
                        $this->db->where('place_id', $v['id']);
                        $this->db->where('booking_status', '0');
                        $this->db->where('is_deleted', '0');
                        $this->db->order_by('booking_status ASC');
                        $bookingsList = $this->db->get()->result();
                        foreach($bookingsList as $b){
                            $this->db->select('slot_name');
                        $this->db->from('ci_parking_slot_info');
                        $this->db->where('slot_no', $b->slot_id);
                        $this->db->where('is_deleted', '0');
                        $slotName = $this->db->get()->result();
                        $formTime = date('h:i A', strtotime($b->from_time));
                        $toTime = date('h:i A', strtotime($b->to_time));
                            $booking_data = array(
                                "placeId"=>$b->place_id,
                                "placeName" => $v['placename'],
                                "slotName" => $slotName[0]->slot_name,
                                "bookingId" => $b->unique_booking_id,
                                "bookingExt" => $b->book_ext,
                                "fromDate" => $b->booking_from_date,
                                "toDate" => $b->booking_to_date,
                                "fromTime" => $formTime,
                                "toTime" => $toTime,
                                "bookingType" => $b->booking_type,
                                "cost" => $b->cost
                            );
                            array_push($bookings,$booking_data);
                        }
                        //  array_push($today_bookings, $bookings);
                         array_push($place_data, $data);
                         array_push($total_earning, $earningTotal[0]['cost']);
                         array_push($monthly_earning, $earningMonthly[0]['cost']);
                         
                         $i++; 
                             
                         }
                        
                        $this->db->select_sum('no_of_slots');
                        $this->db->from('ci_parking_places');
                        $slot_count = $this->db->get()->result_Array();
                         
                         
                         if (in_array("1", $place_status) ||empty($slot_info)) {
                             
                             
                             $total_no_of_slots = $slot_count[0]["no_of_slots"];
                             $total_places = count($place_info);
                             //$total_no_of_booked_slots = $booked_slots[0]["booked_slots"];
                             $no_of_slots = array_sum($slot_status);
                             $slots_booking = array_sum($booked_slots);
                             $total_earn = array_sum($total_earning);
                             $monthly_earn = array_sum($monthly_earning);
                             
                            //  print_r($total_earn);
                            //  exit();
                             
                             $msg = array('status' => True, 'message' => 'Dashboard screen details', 'session' => "1", 'flag' => "1", 'no_of_parking_places' => "$total_places", 'no_of_booked_slots' => "$slots_booking", 'total_earning' => "$total_earn", 'monthly_earning' => "$monthly_earn", 'today_bookings' =>$bookings , 'places' => $place_data);
                             echo json_encode($msg);
                         }
                         else {
                             $msg = array('status' => True, 'message' => 'Dashboard screen details', 'session' => "1", 'flag' => "0", 'no_of_parking_places' => "", 'no_of_booked_slots' => "", 'total_earning' => "", 'monthly_earning' => "", 'today_bookings' =>[], 'places' => $place_data);
                             echo json_encode($msg);
                         }
                     }
                     else {
                         
                         $this->db->select('id, placename, place_address, no_of_slots, place_status');
                         $this->db->from('ci_parking_places');
                         $this->db->where('vendor_id', $token_data->id);
                         $inactive_places = $this->db->get()->result_Array();
                         $i=0;
                         $p = [];
                         foreach($inactive_places as $v){
                            $dt = array(
                                    "placeId" => $inactive_places[$i]['id'],
                                    "placeName" => $inactive_places[$i]['placename'],
                                    "placeAddress" => $inactive_places[$i]['place_address'],
                                    "noOfSlots" => $inactive_places[$i]['no_of_slots'],
                                    "cost" => '0',//$inactive_places[$i]['price'],
                                    "placeStatus" => $inactive_places[$i]['place_status']
                                );
                                
                                array_push($p, $dt);
                         $i++;}
                         
                         $msg = array('status' => True, 'message' => 'Dashboard screen details', 'session' => "1", 'flag' => "0", 'no_of_parked_places' => "", 'no_of_booked_slots' => "", 'total_earning' => "", 'monthly_earning' => "", 'today_bookings' =>[]  ,'places' => $p);
                         echo json_encode($msg);
                     }
                 }
                 else{
                    $msg = array('status' => True, 'message' => 'Dashboard screen details', 'session' => "0", 'flag' => "0", 'no_of_parked_places' => "", 'no_of_booked_slots' => "", 'total_earning' => "", 'monthly_earning' => "", 'today_bookings' =>[],  'places' => []);
                    echo json_encode($msg);
                }
             }
         }
    }
    
    public function add_place(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->form_validation->set_rules('token','Token','required');
            $this->form_validation->set_rules('placeName','Place Name','required');
            $this->form_validation->set_rules('placeAddress','Place Address','required');
            $this->form_validation->set_rules('landmark','Landmark','required');
            $this->form_validation->set_rules('city','City','required');
            $this->form_validation->set_rules('pincode','Pincode','required');
            $this->form_validation->set_rules('state','State','required');
            $this->form_validation->set_rules('noOfSlots','No. of Slots','required');

            if ($this->form_validation->run()) {
                $token =  $this->security->xss_clean($this->input->post('token'));
                $placeName =  $this->security->xss_clean($this->input->post('placeName'));
                $placeAddress =  $this->security->xss_clean($this->input->post('placeAddress'));
                $landmark =  $this->security->xss_clean($this->input->post('landmark'));
                $city =  $this->security->xss_clean($this->input->post('city'));
                $pincode =  $this->security->xss_clean($this->input->post('pincode'));
                $state =  $this->security->xss_clean($this->input->post('state'));
                $noOfSlots =  $this->security->xss_clean($this->input->post('noOfSlots'));
                
                $verifyToken = $this->tokenVerify($token);
                
                    if($verifyToken==true){
                        $tokenData = $this->tokenDecodeData($token);
                        
                        $data = array(
                                    'vendor_id' => $tokenData->id,
                                    'placename' => $placeName,
                                    'place_address' => $placeAddress.",".$landmark.",".$city."-".$pincode.",".$state.".",
                                    'no_of_slots' => $noOfSlots
                                );
                        
                        $insert_id = $this->db->insert('ci_parking_places', $data);
                       
                        $data = array(
                                    'vendor_id' => $tokenData->id,
                                    'placename' => $placeName,
                                    'place_address' => $placeAddress.",".$landmark.",".$city."-".$pincode.",".$state.".",
                                    'applied_slots' => $noOfSlots,
                                    "pincode"=>$pincode
                                ); 
                        $insert_id = $this->db->insert('master_parking_places', $data);
                        
                        if($insert_id){
                            $msg = array('status' => true, 'message' => 'Place has been inserted successfully!!', 'session' => "1");
                            echo json_encode($msg);
                        }
                        else {
                            $msg = array('status' => false, 'message' => 'Place insertion failed!!', 'session' => "1");
                            echo json_encode($msg);
                        }
                    }
            }
            else {
                $msg = array('status' => false, 'message' => 'Place insertion failed!!', 'session' => "1");
                echo json_encode($msg);
            }
        }
    }
    
    public function aboutUs_list(){

            $check_token1 = $this->db->select('*')->from('tbl_about_us')->where('module', '2')->get()->result(); //check token
            $list1=[];
            if (count($check_token1) > 0) {

                foreach($check_token1 as $c){
                    $c->details= base64_decode($c->details);
                    array_push($list1,$c);
                }

                $msg = array('status' => true, 'message' => 'List of data.','data'=>$list1);
                echo json_encode($msg);
            } 
            else {
                $msg = array('status' => false, 'message' => 'No data found.','data'=>[]);
                echo json_encode($msg);
            }
       
    }
    
    public function managePlacesStatusAuto(){
         $getVendorList = $this->db->select('*')->from('tbl_vendor')->where('is_deleted','0')->get()->result();
	               
	       foreach($getVendorList as $vendor){
	           $getplaces = $this->db->select('id,vendor_id,placename,place_address,no_of_slots,onOff_applied,onOff_apply_date')->from('ci_parking_places')
	               ->where('vendor_id',$vendor->id)
	               ->where('status','0')
	               ->where('onOff_applied !=','0')->where('DATE(onOff_apply_date)',date("Y-m-d"))->where('is_deleted','0')
	               ->get()->result();
	               
    	               foreach($getplaces as $place){
    	                   if($place->onOff_applied=='1'){//applied for on
    	                       $update = $this->db->where('id',$place->id)->update('ci_parking_places',array('place_status'=>'1','onOff_applied'=>'0','onOff_apply_date'=>''));
    	                       if($update){
    	                           $slotlist = $this->db->select('*')->from('ci_parking_slot_info')->where('place_id',$place->id)->where('is_deleted','0')->get()->result();
    	                           foreach($slotlist as $slot){
    	                               $update1 = $this->db->where('slot_no',$slot->slot_no)->update('ci_parking_slot_info',array('status'=>'0','onOff_applied'=>'0','onOff_apply_date'=>''));
    	                           }
    	                       }
    	                   }else{//applied for off
    	                       $update = $this->db->where('id',$place->id)->update('ci_parking_places',array('place_status'=>'0','onOff_applied'=>'0','onOff_apply_date'=>''));
    	                       if($update){
    	                           $slotlist = $this->db->select('*')->from('ci_parking_slot_info')->where('place_id',$place->id)->where('is_deleted','0')->get()->result();
    	                           foreach($slotlist as $slot){
    	                               $update1 = $this->db->where('slot_no',$slot->slot_no)->update('ci_parking_slot_info',array('status'=>'1','onOff_applied'=>'0','onOff_apply_date'=>''));
    	                           }
    	                       }
    	                   }
    	               }
	               
	               
	               }
	}
	
	public function manageSlotStatusAuto(){
         $getVendorList = $this->db->select('*')->from('tbl_vendor')->where('is_deleted','0')->get()->result();
	               
	       foreach($getVendorList as $vendor){
	           $getplaces = $this->db->select('*')->from('ci_parking_places')//status
	               ->where('vendor_id',$vendor->id)->where('status','0')->where('is_deleted','0')->get()->result();
	               
    	               foreach($getplaces as $place){
    	                   
            	           $getSlots = $this->db->select('*')
            	           ->from('ci_parking_slot_info')
        	               ->where('place_id',$place->id)
        	               ->where('onOff_applied !=','0')->where('DATE(onOff_apply_date)',date("Y-m-d"))->where('is_deleted','0')
        	               ->get()->result();
        	               
        	               foreach($getSlots as $slot){
        	                   $update = $this->db->where('slot_no',$slot->slot_no)->update('ci_parking_slot_info',array('status'=>$slot->onOff_applied=='1'?'0':'1','onOff_applied'=>'0','onOff_apply_date'=>''));
        	               }
    	               }
	               
	               
	               }
	}
	
	
}
?>