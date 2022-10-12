<?php
defined('BASEPATH') or exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET,POST, OPTIONS");

class VerifierApp extends CI_Controller
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
    
    public function _returnSingle($err) 
    {
		foreach ($err as $key => $value) {
			return $err[$key];
		}
	}
	
	public function tokenVerify($token)
	{
        $jwt = new JWT();
        $jwtsecretkey = 'mpc_vendor'; //sceret key for token
        $data = $jwt->decode($token, $jwtsecretkey, true);
        $checkAuthoriz = $this->db->select('*')->from('ci_admin')->where('admin_id',$data->id)->where('admin_role_id','3')->where('is_active','1')->get()->result();
        if(count($checkAuthoriz)>0){
         return true;   
        }else{
          return false;
        }
	}
    
    public function tokenDecodeData($token)
    {
        $jwt = new JWT();
        $jwtsecretkey = 'mpc_vendor'; //sceret key for token
        $data = $jwt->decode($token, $jwtsecretkey, true);
        return $data;
        
	}
	
	public function verifier_info()
    {
        // $this->form_validation->set_rules('verifier_id', 'Verifer Id', 'required');
        $this->form_validation->set_rules('email_id', 'Email id', 'required|valid_email', array('is_unique' => 'The Verifier has been Already Register'));
        $this->form_validation->set_rules('password', 'Password', 'required');
        
        
        if ($this->form_validation->run()) {
            $email = $this->security->xss_clean($this->input->post('email_id'));
            $password = $this->security->xss_clean($this->input->post('password'));
            
            $this->db->select('admin_id, admin_role_id, email, password,notifn_topic');
            $this->db->from('ci_admin');
            $this->db->where('email', $email);
            //  $this->db->where('admin_role_id', '3');
             $this->db->group_start()->where('admin_role_id','3')->or_where('admin_role_id','11')->group_end();
            $this->db->where('is_active', 1);
            $query = $this->db->get();
            
            $verifier = $query->result_Array();
            // print($email);
            // print_r($verifier);
            
            if(!empty($verifier)){
                if(verifyHashedPassword($password, $verifier[0]['password'])){
                    
                    $verifier[0]['isadmin']=
                    $verifier[0]['admin_role_id']=='11'?true:false;
                    
                    $msg = array('status' => true, 'message' => "Login Successfull!", 'data' => $verifier);
                    echo json_encode($msg);
                } else {
                    $msg = array('status' => false, 'message' => "Incorrect EmailId or Password.", 'data' => []);
                    echo json_encode($msg);
                }
            } else {
                $msg = array('status' => false, 'message' => "No verifier found!", 'data' => []);
                echo json_encode($msg);
            }
        } else {
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'token'=>'');
            echo json_encode($msg);
        }
    }
    
    public function gethashpass($password)
    {
    
       echo password_hash($password, PASSWORD_BCRYPT);
    
    }
    
    function array_flatten($array)
    { 
      if (!is_array($array)) { 
        return FALSE; 
      } 
      $result = array(); 
      foreach ($array as $key => $value) { 
        if (is_array($value)) { 
          $result = array_merge($result, array_flatten($value)); 
        } 
        else { 
          $result[$key] = $value; 
        } 
      } 
      return $result; 
    } 
    
    public function getRefundedBookings()
    {
        $uniqueId = [];
            $this->db->select('booking.unique_booking_id');
            $this->db->from('ci_booking as booking');
            $this->db->join('ci_wallet_history as wallet_history', 'wallet_history.booking_id = booking.id');
            $this->db->where('wallet_history.payment_type', '3' );
            $this->db->where('booking.place_id !=', '1' );
            $this->db->where('booking.booking_status', '2' );
            $this->db->where('booking.is_deleted', 0);
            $query = $this->db->get()->result_Array();
            
            foreach($query as $q){
            //      print($q['unique_booking_id']);
            // exit();
                array_push($uniqueId,$q['unique_booking_id']);
            }
            
            // print_r($uniqueId);
            // exit();
            
        $mesg = array('status' => true,'query'=>$uniqueId, 'message' => 'Refunded bookings');
            echo json_encode($mesg);
    }
    
    public function get_verifier_bookings()
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->form_validation->set_rules('verifier_id', 'Verifier Id', 'required');
        
        
        if ($this->form_validation->run()) 
        {
            $verifier_id = $this->security->xss_clean($this->input->post('verifier_id'));
            
            $this->db->select('BaseTbl.place_id');
            $this->db->from('tbl_verifier_place as BaseTbl');
            $this->db->where('BaseTbl.verifier_id', $verifier_id)->where('BaseTbl.duty_date',date('Y-m-d'));
            $this->db->where('BaseTbl.isDeleted', 0);
            $query = $this->db->get();
            
            
            $verifier = $query->result_Array();
            // print_r($verifier);
            // exit();
            
            $enforcerList = [];
            
            $this->db->select('parking_place.placename as placeName,BaseTbl.mobile_no as enforcerNumber');
            $this->db->from('ci_admin as BaseTbl');
            $this->db->join('tbl_enforcer_place as enforcer_place', 'BaseTbl.admin_id = enforcer_place.enforcer_id');
            $this->db->join('ci_parking_places as parking_place', 'enforcer_place.place_id = parking_place.id');
            $this->db->where('BaseTbl.is_active', 1);
            $enforcerList = $this->db->get()->result_Array();
            
            
            $verify_status = [];
            
            $this->db->select('booking_id');
            $this->db->from('ci_booking_verify');
            $this->db->like('onCreated', date('Y-m-d'));
            $this->db->where('isDeleted', 0);
            $this->db->group_by('booking_id');
            $query1 = $this->db->get();
            $vstatus = $query1->result_Array();
            
            // print_r($vstatus);
            // exit();
            
            $getdesposition = $this->db->select('id,descriptions')->from('ci_despositions')->where('status','1')->get()->result();
            
            $i=0;
            foreach($vstatus as $v){
                array_push($verify_status,$vstatus[$i]['booking_id']);
            $i++;}
            
            $data = [];
            $i =0;
            foreach($verifier as $v){
                
                $this->db->select('BaseTbl.place_id, BaseTbl.id as booking_id, BaseTbl.booking_from_date, BaseTbl.booking_to_date, BaseTbl.from_time,BaseTbl.book_ext, BaseTbl.to_time,BaseTbl.booking_status, parking_places.placename,  BaseTbl.booking_type, car_det.car_number as carNo, BaseTbl.slot_id, parking_slot_info.display_id, BaseTbl.unique_booking_id, BaseTbl.replaced_booking_id, user.mobile_no as userNo');
                $this->db->from('ci_booking as BaseTbl');
                $this->db->join('ci_parking_places as parking_places', 'BaseTbl.place_id = parking_places.id');
                $this->db->join('ci_parking_slot_info as parking_slot_info', 'BaseTbl.slot_id = parking_slot_info.slot_no');
                $this->db->join('ci_users as user', 'BaseTbl.user_id = user.id');
                $this->db->join('ci_car_details as car_det', 'BaseTbl.car_id = car_det.id');
                $this->db->where('BaseTbl.place_id', $verifier[$i]['place_id']);
                $this->db->where('BaseTbl.booking_from_date <=', date('Y-m-d'));
                $this->db->where('BaseTbl.booking_to_date >=', date('Y-m-d'));
                $this->db->where('BaseTbl.from_time <=', date('H:i:s'));
                $this->db->order_by('BaseTbl.from_time desc');
                // $this->db->where("(BaseTbl.booking_status='0' OR BaseTbl.booking_status='3')", NULL, FALSE);
                $this->db->where('BaseTbl.is_deleted', 0);
                $query2 = $this->db->get();
                $place2 = $query2->result_Array();
                
                $timewise = $place2;
                // print_r($timewise);
                foreach($timewise as $v1){
                    //$v1,$data
                    $currendate_fulld=date("Y-m-d H:i:s");
                // $currendate_fulld=date("2022-03-09 12:49:00");
                // $currendate_d=date("2022-03-09");
                $currendate_d=date("Y-m-d");
                $checkData = array('id'=>'' ,
                                'booking_id'=>'' ,
                                'check_in'=>'' ,
                                'check_out'=>'' ,
                                'verifier_id'=>'' ,
                                'check_type'=>'0',
                                'created_at'=>'');
                // if($d['booking_type']=='0'){  // daily
                
                
                if($v1['booking_type']=='0'){  // daily
                $getCheckDetails = $this->db->select('*')->from('ci_booking_check')->where('booking_id',$v1['booking_id'])
                    ->where('is_deleted','0')->get()->result_array();
                    
                    if(count($getCheckDetails)>0){
                        $checkData = array('id'=>$getCheckDetails[0]['id'],
                                'booking_id'=>$getCheckDetails[0]['booking_id'] ,
                                'check_in'=>$getCheckDetails[0]['check_in'] ,
                                'check_out'=>$getCheckDetails[0]['check_out'] ,
                                'verifier_id'=>$getCheckDetails[0]['verifier_id'] ,
                                'check_type'=>$getCheckDetails[0]['check_type'],
                                'created_at'=>$getCheckDetails[0]['created_at']);
                    }
                
                    $fromDate_s = date('Y-m-d H:i:s', strtotime($v1['booking_from_date'] . ' ' . $v1['from_time']));
                    $toDate_s =date('Y-m-d H:i:s', strtotime($v1['booking_to_date'] . ' ' . $v1['to_time']));
                    if($fromDate_s<=$currendate_fulld&&$toDate_s>=$currendate_fulld)
                    {
                        // $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $v1['iscompletedTime']=false;
                        $value=  $this->get_verifier_bookings_logic($v1);
                        $value['checkData']=$checkData;
                         array_push($data, $value);
                    }
                    else if($fromDate_s<$currendate_fulld&&$toDate_s<$currendate_fulld){
                         $v1['iscompletedTime']=true;
                        $value=  $this->get_verifier_bookings_logic($v1); 
                        $value['checkData']=$checkData;
                         array_push($data, $value);
                    }
                   /* else{
                        // $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $v1['iscompletedTime']=true;
                        $value=  $this->get_verifier_bookings_logic($v1); 
                        $value['checkData']=$checkData;
                         array_push($data, $value);
                    }*/
                    
                }
                else
                {     //passes
                // print('pass');
                $getCheckDetails = $this->db->select('*')->from('ci_booking_check')->where('booking_id',$v1['booking_id'])
                ->where('created_at',date('Y-m-d'))
                    ->where('is_deleted','0')->get()->result_array();
                    
                    if(count($getCheckDetails)>0){
                        $checkData = array('id'=>$getCheckDetails[0]['id'],
                                'booking_id'=>$getCheckDetails[0]['booking_id'] ,
                                'check_in'=>$getCheckDetails[0]['check_in'] ,
                                'check_out'=>$getCheckDetails[0]['check_out'] ,
                                'verifier_id'=>$getCheckDetails[0]['verifier_id'] ,
                                'check_type'=>$getCheckDetails[0]['check_type'],
                                'created_at'=>$getCheckDetails[0]['created_at']);
                    }
                    $fromDate_s = date('Y-m-d H:i:s', strtotime($currendate_d .' '. $v1['from_time']));
                    $toDate_s =date('Y-m-d H:i:s', strtotime($currendate_d .' '. $v1['to_time']));
                    if($fromDate_s<=$currendate_fulld&&$toDate_s>=$currendate_fulld){
                        // $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $v1['iscompletedTime']=false;
                        // array_push($timewise_new,$data);
                         $value=  $this->get_verifier_bookings_logic($v1);  
                         $value['checkData']=$checkData;
                         array_push($data, $value); 
                    }
                    else if($fromDate_s<$currendate_fulld&&$toDate_s<$currendate_fulld){
                          $v1['iscompletedTime']=true;
                         $value=  $this->get_verifier_bookings_logic($v1);  
                         $value['checkData']=$checkData;
                         array_push($data, $value); 
                    }
                    /*else{
                        //  $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                         $v1['iscompletedTime']=true;
                         $value=  $this->get_verifier_bookings_logic($v1);  
                         $value['checkData']=$checkData;
                         array_push($data, $value); 
                    }*/
                }
                 
                }
                
            $i++;
                
            }
            $status_n = [];
            $timewise_new=[];
            $n = 0;
            // print_r(
            //     // count(
            //         $data
            //         // )
            //         );
            foreach($data as $d){
                // print($n);
                // print(' -- ');
                if(in_array($data[$n]['booking_id'] , $verify_status))
                {
                    $this->db->select('booking_id, verify_status');
                    $this->db->from('ci_booking_verify');
                    $this->db->where('booking_id', $data[$n]['booking_id']);
                    $this->db->like('onCreated', date('Y-m-d'));
                    $this->db->where('isDeleted', 0);
                    $query2 = $this->db->get();
                    $verify = $query2->result_Array();
                    
                    if(empty($verify))
                    {
                        $d['verify_status'] = "2";
                    }
                    else 
                    {
                        $d['verify_status'] = $verify[0]['verify_status'];
                    }
                }
                else 
                {
                        $d['verify_status'] = "2";
                }
                
                $isExtended= false;
                $getExtBookingList=$this->db->select('*')->from('ci_booking')->where('replaced_booking_id',$d['booking_id'])->get()->result_array();
                if(count($getExtBookingList)>0){
                    $d['isExtended']=true;
                }else{
                    $d['isExtended']=false;
                }
                    
                array_push($timewise_new,$d);
            $n++;
                
            }
            
            if(!empty($data)){
                $getStatusVerifier = $this->db->select('id,subject')->from('master_verifier_issues')->where('type','1')->get()->result();
                $msg = array('status' => true, 'message' => "List of bookings assigned to verifier", 'data' => $timewise_new,'issuelist'=>
                count($getStatusVerifier)>0?$getStatusVerifier:
                    [], 'enforcerList'=> $enforcerList,'desposition'=>$getdesposition);
                echo json_encode($msg);
            } else {
                $msg = array('status' => true, 'message' => "List of bookings assigned to verifier", 'data' => [],'issuelist'=>[], 'enforcerList'=> [],'desposition'=>$getdesposition);
                echo json_encode($msg);
            }
        }
    }
    
    
    public function get_verifier_bookings_logic($v1)
    {
            //$v1,$data
                    $issue = $this->db->select('*')
                ->from('tbl_verifier_complaints')
                ->where('booking_id', $v1['booking_id'])
                ->get()
                ->result_Array();
                $v1['booking_from_date'] = date('d-m-Y', strtotime($v1['booking_from_date']));
                $v1['booking_to_date'] =date('d-m-Y', strtotime($v1['booking_to_date']));
                if(count($issue)>0)
                {
                    $v1['complaint_text'] = $issue[0]['complaint_text'];
                    $v1['status'] = $issue[0]['status'];
                    $v1['actionPerformedByEnforcer'] = $issue[0]['actionPerformedByEnforcer'];
                    $v1['resolvedDate'] = $issue[0]['resolvedDate'];
                    $v1['remark'] = $issue[0]['remark'];
                    $v1['customercareRemark'] = $issue[0]['customercareRemark'];
                }
                else
                {
                    $v1['complaint_text'] = '';
                    $v1['status'] = '';
                    $v1['actionPerformedByEnforcer'] = '';
                    $v1['resolvedDate'] = '';
                    $v1['remark'] = '';
                    $v1['customercareRemark'] = '';
                }
                    
                    $getEnforcerPlace = $this->db->select('*')->from('tbl_enforcer_place')->where('place_id',$v1['place_id'])->get()->result();
                    
	                       if(count($getEnforcerPlace)>0){
	                           foreach($getEnforcerPlace as $e){
	                               
	                       $getEnforceDetails= $this->db->select('*')
	                       ->from('ci_admin')
	                       ->where('admin_id',$e->enforcer_id)
	                       ->where('admin_role_id','4')
	                       ->get()
	                       ->result();
	                           }
	                       if(count($getEnforceDetails)>0){
	                           $v1['enforcerNo'] = $getEnforceDetails[0]->mobile_no;
	                       }
	                       else{
	                           $v1['enforcerNo'] ='';
	                           }
	                       }else{
	                           $v1['enforcerNo'] ='';
	                       }
	                    return $v1;
    }
    
    public function get_bookings_placewise()
    {
        $this->form_validation->set_rules('place_id', 'Place Id', 'required');
        date_default_timezone_set('Asia/Kolkata');
        
        if ($this->form_validation->run()) {
            $place_id = $this->security->xss_clean($this->input->post('place_id'));
            
            $timewise = [];
            
            $this->db->select('BaseTbl.id as bookingId,BaseTbl.book_ext,booking_from_date,booking_to_date,from_time,to_time,booking_type,BaseTbl.booking_status, car_det.car_number as carNo, BaseTbl.slot_id, BaseTbl.unique_booking_id, BaseTbl.replaced_booking_id, parking_slot_info.display_id, user.mobile_no as userNo');
            $this->db->from('ci_booking as BaseTbl');
            $this->db->join('ci_parking_places as parking_places', 'BaseTbl.place_id = parking_places.id');
            $this->db->join('ci_parking_slot_info as parking_slot_info', 'BaseTbl.slot_id = parking_slot_info.slot_no');
            $this->db->join('ci_users as user', 'BaseTbl.user_id = user.id');
            $this->db->join('ci_car_details as car_det', 'BaseTbl.car_id = car_det.id');
            $this->db->where('BaseTbl.place_id', $place_id);
            $this->db->where('BaseTbl.booking_from_date <=', date('Y-m-d'));
            $this->db->where('BaseTbl.booking_to_date >=', date('Y-m-d'));
            // $this->db->where('BaseTbl.from_time <=', date('H:i:s'));
            // $this->db->where("(BaseTbl.booking_status='0' OR BaseTbl.booking_status='3')", NULL, FALSE);
            $this->db->order_by('BaseTbl.from_time desc');
            $this->db->where('BaseTbl.is_deleted', 0);
            $query2 = $this->db->get();
            
            $data2 = $query2->result_Array();
            
            $timewise = $data2;
            // print_r($timewise);
            // die;
            
            // $timewise = array_merge($data,$data2);
            
            $getdesposition = $this->db->select('id,descriptions')->from('ci_despositions')->where('status','1')->get()->result();
            $verify_status = [];
            
                $this->db->select('booking_id');
                $this->db->from('ci_booking_verify');
                $this->db->like('onCreated', date('Y-m-d'));
                $this->db->where('isDeleted', 0);
                $this->db->group_by('booking_id');
                $query1 = $this->db->get();
                $vstatus = $query1->result_Array();
                $i=0;
                foreach($vstatus as $v){
                    array_push($verify_status,$vstatus[$i]['booking_id']);
                $i++;}
        
            
            $status_n = [];
            
            $n = 0;
            $timewise_new=[];
            foreach($timewise as $d){
                // print_r($d);
                //$d,$place_id,$timewise,$n,$verify_status,$timewise_new
                $currendate_fulld=date("Y-m-d H:i:s");
                // $currendate_fulld=date("2022-03-09 12:49:00");
                // $currendate_d=date("2022-03-09");
                $currendate_d=date("Y-m-d");
                $checkData = array('id'=>'' ,
                                'booking_id'=>'' ,
                                'check_in'=>'' ,
                                'check_out'=>'' ,
                                'verifier_id'=>'' ,
                                'check_type'=>'0',
                                'created_at'=>'');
                if($d['booking_type']=='0')
                {  // daily
                
                $getCheckDetails = $this->db->select('*')->from('ci_booking_check')->where('booking_id',$d['bookingId'])
                    ->where('is_deleted','0')->get()->result_array();
                    
                    if(count($getCheckDetails)>0){
                        $checkData = array('id'=>$getCheckDetails[0]['id'],
                                'booking_id'=>$getCheckDetails[0]['booking_id'] ,
                                'check_in'=>$getCheckDetails[0]['check_in'] ,
                                'check_out'=>$getCheckDetails[0]['check_out'] ,
                                'verifier_id'=>$getCheckDetails[0]['verifier_id'] ,
                                'check_type'=>$getCheckDetails[0]['check_type'],
                                'created_at'=>$getCheckDetails[0]['created_at']);
                    }
                    
                    $fromDate_s = date('Y-m-d H:i:s', strtotime($d['booking_from_date'] . ' ' . $d['from_time']));
                    $toDate_s =date('Y-m-d H:i:s', strtotime($d['booking_to_date'] . ' ' . $d['to_time']));
                    if($fromDate_s<=$currendate_fulld&&$toDate_s>=$currendate_fulld)
                    {
                        $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $data['iscompletedTime']=false;
                        $data['checkData']=$checkData;
                       
                        array_push($timewise_new,$data);
                    }
                    else if($fromDate_s<$currendate_fulld&&$toDate_s<$currendate_fulld)
                    {
                         $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $data['iscompletedTime']=true;
                        $data['checkData']=$checkData;
                        //  $data['checkType']=$checkData['check_type'];
                        array_push($timewise_new,$data);
                    }
                    /*else{
                        $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $data['iscompletedTime']=true;
                        $data['checkData']=$checkData;
                        //  $data['checkType']=$checkData['check_type'];
                        array_push($timewise_new,$data);
                        
                    }*/
                    
                    
                    
                }
                else
                {     //passes
                // print('pass');
                $getCheckDetails = $this->db->select('*')->from('ci_booking_check')->where('booking_id',$d['bookingId'])
                ->where('created_at',date('Y-m-d'))
                    ->where('is_deleted','0')->get()->result_array();
                    
                    if(count($getCheckDetails)>0){
                        $checkData = array('id'=>$getCheckDetails[0]['id'],
                                'booking_id'=>$getCheckDetails[0]['booking_id'] ,
                                'check_in'=>$getCheckDetails[0]['check_in'] ,
                                'check_out'=>$getCheckDetails[0]['check_out'] ,
                                'verifier_id'=>$getCheckDetails[0]['verifier_id'] ,
                                'check_type'=>$getCheckDetails[0]['check_type'],
                                'created_at'=>$getCheckDetails[0]['created_at']);
                    }
                    $fromDate_s = date('Y-m-d H:i:s', strtotime($currendate_d .' '. $d['from_time']));
                    $toDate_s =date('Y-m-d H:i:s', strtotime($currendate_d .' '. $d['to_time']));
                    if($fromDate_s<=$currendate_fulld&&$toDate_s>=$currendate_fulld){
                        $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $data['iscompletedTime']=false;
                        $data['checkData']=$checkData;
                        //  $data['checkType']=$checkData['check_type'];
                        array_push($timewise_new,$data);
                    }
                     else if($fromDate_s<$currendate_fulld&&$toDate_s<$currendate_fulld)
                    {
                        $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $data['iscompletedTime']=true;
                        $data['checkData']=$checkData;
                        //  $data['checkType']=$checkData['check_type'];
                        array_push($timewise_new,$data);
                    }
                  /*  else{
                         $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $data['iscompletedTime']=true;
                        $data['checkData']=$checkData;
                        //  $data['checkType']=$checkData['check_type'];
                        array_push($timewise_new,$data);
                    }*/
                }
                $n++;
                // }
                
                 
                
            }
            
            $bookingListExt=[];
            foreach($timewise_new as $data){
                $isExtended= false;
                $getExtBookingList=$this->db->select('*')->from('ci_booking')->where('replaced_booking_id',$data['bookingId'])->get()->result_array();
                if(count($getExtBookingList)>0){
                    $data['isExtended']=true;
                }else{
                    $data['isExtended']=false;
                }
                array_push($bookingListExt,$data);
            }
            
            if(!empty($timewise)){
                $getStatusVerifier = $this->db->select('id,subject')->from('master_verifier_issues')->where('type','1')->get()->result();
                $msg = array('status' => true, 'message' => "List of bookings assigned to verifier", 'bookings' => $bookingListExt,'issuelist'=>
                count($getStatusVerifier)>0?$getStatusVerifier:
                    [],'desposition'=>$getdesposition);
                echo json_encode($msg);
            } else {
                $msg = array('status' => true, 'message' => "List of bookings assigned to verifier", 'bookings' => [],'issuelist'=>[],'desposition'=>$getdesposition);
                echo json_encode($msg);
            }
        }
    }
    
    public function verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new)
    {
        $issue = $this->db->select('*')
                ->from('tbl_verifier_complaints')
                ->where('booking_id', $d['bookingId'])
                ->get()
                ->result_Array();
                 $d['booking_from_date'] = date('d-m-Y', strtotime($d['booking_from_date']));
                $d['booking_to_date'] =date('d-m-Y', strtotime($d['booking_to_date']));
                if(count($issue)>0){
                    $d['complaint_text'] = $issue[0]['complaint_text'];
                    $d['status'] = $issue[0]['status'];
                    $d['actionPerformedByEnforcer'] = $issue[0]['actionPerformedByEnforcer'];
                    $d['resolvedDate'] = $issue[0]['resolvedDate'];
                    $d['remark'] = $issue[0]['remark'];
                    $d['customercareRemark'] = $issue[0]['customercareRemark'];
                }else{
                    $d['complaint_text'] = '';
                    $d['status'] = '';
                    $d['actionPerformedByEnforcer'] = '';
                    $d['resolvedDate'] = '';
                    $d['remark'] = '';
                    $d['customercareRemark'] = '';
                }
                
                $getEnforcerPlace = $this->db->select('*')->from('tbl_enforcer_place')->where('place_id',$place_id)->get()->result();
                
	                       if(count($getEnforcerPlace)>0){
	                       $getEnforceDetails= $this->db->select('*')
	                       ->from('ci_admin')
	                       ->where('admin_id',$getEnforcerPlace[0]->enforcer_id)
	                       ->where('admin_role_id','4')
	                       ->get()
	                       ->result();
	                       
	                       if(count($getEnforceDetails)>0){
	                           $d['enforcerNo'] = $getEnforceDetails[0]->mobile_no;
	                       }
	                       else{$d['enforcerNo'] ='';
	                       }
	                           
	                       }else{
	                           $d['enforcerNo'] ='';
	                       }
                
                    if(in_array($timewise[$n]['bookingId'] , $verify_status)){
                        $this->db->select('booking_id, verify_status');
                        $this->db->from('ci_booking_verify');
                        $this->db->where('booking_id', $timewise[$n]['bookingId']);
                        $this->db->like('onCreated', date('Y-m-d'));
                        $this->db->where('isDeleted', 0);
                        $query2 = $this->db->get();
                        $verify = $query2->result_Array();
                        
                        
                        
                        
                        if(empty($verify)){
                            $d['verify_status'] = "2";
                        }
                        else {
                            $d['verify_status'] = $verify[0]['verify_status'];
                        }
                        
                    }
                    else {
                            $d['verify_status'] = "2";
                    }
                    return $d;
            
    }
    
    public function getComplaints()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            date_default_timezone_set('Asia/Kolkata');
	        $this->form_validation->set_rules('verifierId','Verifier Id','required');
	        if($this->form_validation->run()==false)
	        {
	            $errorMsg = $this->form_validation->error_array();
	             $msg = array('status' => false, 'message' => $this->_returnSingle($errorMsg));
            echo json_encode($msg);
	        }
	        else
	        {
	            $verifierId = $this->security->xss_clean($this->input->post('verifierId'));
	            $raisedComplaints = [];
	               $result = $this->db->select('*')
	               ->from('tbl_verifier_complaints')
	               ->where('verifier_id',$verifierId)
	               ->like('createdDate', date('Y-m-d'))
	               ->where('is_deleted',0)
	               ->get()
	               ->result();
	               
	               if(count($result)>0){
	                   foreach($result as $r){
	                       $data = $this->db->select('*')
	                       ->from('ci_booking')
	                       ->where('id',$r->booking_id)
	                       ->get()
	                       ->result_array();
	                       if(count($data)>0){
	                           if($data[0]['booking_status'] == '3'){
	                               $r->booking=$data[0];
	                           array_push($raisedComplaints,$r);
	                       }
	                       }
	                   }
	                   $msg = array('status' => true, 'message' => 'List of Places...','raisedComplaints'=>$raisedComplaints);
                      echo json_encode($msg);
	               }else{
	                   $msg = array('status' => true, 'message' => 'List of Places...','data'=>[]);
                       echo json_encode($msg);
	               }
	        }
        }
    }
    
    public function get_all_bookingsNew()
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->form_validation->set_rules('place_id', 'Place Id', 'required');
        // $this->form_validation->set_rules('filter_type', 'Filter Type', 'required');//1=all,2=ongoing (unverifier),3=ongoing(Verified),4=Completed,5=upcoming,6=Others
        
        if ($this->form_validation->run()) 
        {
            $place_id = $this->security->xss_clean($this->input->post('place_id'));
            // $filter_type = $this->security->xss_clean($this->input->post('filter_type'));
            
            $timewise = [];
            
            $this->db->select('BaseTbl.id as bookingId,BaseTbl.book_ext,booking_from_date,booking_to_date,from_time,to_time,booking_type,BaseTbl.booking_status, car_det.car_number as carNo, BaseTbl.slot_id, BaseTbl.unique_booking_id, BaseTbl.replaced_booking_id, parking_slot_info.display_id, user.mobile_no as userNo');
            $this->db->from('ci_booking as BaseTbl');
            $this->db->join('ci_parking_places as parking_places', 'BaseTbl.place_id = parking_places.id');
            $this->db->join('ci_parking_slot_info as parking_slot_info', 'BaseTbl.slot_id = parking_slot_info.slot_no');
            $this->db->join('ci_users as user', 'BaseTbl.user_id = user.id');
            $this->db->join('ci_car_details as car_det', 'BaseTbl.car_id = car_det.id');
            $this->db->where('BaseTbl.place_id', $place_id);
            // $this->db->group_start()->where('BaseTbl.booking_status','0')->or_where('BaseTbl.booking_status','1')->group_end();
            $this->db->where('BaseTbl.booking_from_date <=', date('Y-m-d'));
            $this->db->where('BaseTbl.booking_to_date >=', date('Y-m-d'));
            // $this->db->where('BaseTbl.from_time <=', date('H:i:s'));
            // $this->db->where("(BaseTbl.booking_status='0' OR BaseTbl.booking_status='3')", NULL, FALSE);
            $this->db->order_by('BaseTbl.from_time desc');
            $this->db->where('BaseTbl.is_deleted', 0);
            $query2 = $this->db->get();
            
            $data2 = $query2->result_Array();
            
            $allBookingList = $data2;
            $verifiedList=[];
            $unverifiedList=[];
            $completedList = [];
            if(count($allBookingList)>0){
            foreach($allBookingList as $booking)
            {
                $currentdate = date('Y-m-d');
                $currentdatetimestart = date('Y-m-d H:i:s',strtotime($currentdate.' '.'00:00:00'));
                $currentDateTime = date('Y-m-d H:i:s');
                // print_r($booking);
                if($booking['booking_type']=='0'){
                //daily
                
                        $fromdatetime=date('Y-m-d h:i:s a',strtotime($booking['booking_from_date'].' '.$booking['from_time']));
                        $toDatetime =date('Y-m-d h:i:s a',strtotime($booking['booking_to_date'].' '.$booking['to_time']));
                        
                        $fromdatetime_d=new DateTime($fromdatetime);
                        $toDatetime_d=new DateTime($toDatetime);
                        
                        
                        $interval = $fromdatetime_d->diff($toDatetime_d);
                        $min = $interval->i>0?($interval->i.' min'):'';
                    $booking['no_of_hrs']=$interval->h.' hr '.$min;
                        // $booking['no_of_hrs']=$interval->h.' hr '.$interval->i.' min';
                        $booking['from_time']=date('h:i a',strtotime($fromdatetime));
                        $booking['to_time']=date('h:i a',strtotime($toDatetime));
                       
                        
                $checkBookingVerificetion = $this->db->select('*')->from('ci_booking_verify')
                ->where('booking_id',$booking['bookingId'])
                ->where('verify_status','1')
                ->get()->result_array();
                // print_r($booking['booking_status']);
                // exit();
                    if(count($checkBookingVerificetion)>0)
                    {
                       if($booking['booking_status']=='1' || $booking['booking_status']=='2'|| $booking['booking_status']=='4'){
                           array_push($completedList,$booking);
                       }else{
                           array_push($verifiedList,$booking);
                       }
                        // $booking['isverify']=true;
                        // array_push($verifiedList,$booking);
                    }else{
                        if($booking['booking_status']=='2'|| $booking['booking_status']=='4'){
                           array_push($completedList,$booking);
                       }else{
                           array_push($unverifiedList,$booking);
                       }
                        // $booking['isverify']=false;
                        // array_push($unverifiedList,$booking);
                    }
                }
                else{//passes completed bookings remaining
                        
                        $fromdatetime=date('Y-m-d h:i:s a',strtotime($currentdate.' '.$booking['from_time']));
                        $toDatetime =date('Y-m-d h:i:s a',strtotime($currentdate.' '.$booking['to_time']));
                        
                        $fromdatetime_d=new DateTime($fromdatetime);
                        $toDatetime_d=new DateTime($toDatetime);
                        
                        
                        $interval = $fromdatetime_d->diff($toDatetime_d);
                        $min = $interval->i>0?($interval->i.' min'):'';
                        $booking['no_of_hrs']=$interval->h.' hr '.$min;
                        // $booking['no_of_hrs']=$interval->h.' hr '.$interval->i.' min';
                        $booking['from_time']=date('h:i a',strtotime($fromdatetime));
                        $booking['to_time']=date('h:i a',strtotime($toDatetime));
                        
                   $checkBookingVerificetion =  $this->db->select('*')->from('ci_booking_verify')
                                                ->where('booking_id',$booking['bookingId'])->where('verify_status','1')
                                                ->where('onCreated>=',$currentdatetimestart)
                                                ->where('onCreated<=',$currentDateTime)
                                                ->get()->result_array();
                    if(count($checkBookingVerificetion)>0)
                    {
                    //     if($booking['booking_status']=='1'){
                    //       array_push($completedList,$booking);
                    //   }else{
                    //       array_push($verifiedList,$booking);
                    //   }
                        // $booking['isverify']=true;
                        array_push($verifiedList,$booking);
                    }else{
                        // $booking['isverify']=false;
                        array_push($unverifiedList,$booking);
                    }
                }
                
                    

                
            }
            $mesg = array('status' => true,
                            'message' => 'List of bookings',
                            // 'bookings' => $allBookingList,
                            'verifiedBookings'=>$verifiedList,
                            'unverifiedBookings'=>$unverifiedList,
                            'completedBookings'=>$completedList
                            );
                            echo json_encode($mesg);
            }else{
                $mesg = array('status' => false,
                            'message' => 'No booking data found.',
                            // 'bookings' => $allBookingList,
                            'verifiedBookings'=>[],
                            'unverifiedBookings'=>[],
                            'completedBookings'=>[]
                            );
                            echo json_encode($mesg);
            }
            
        }
         else{
             $mesg = array('status' => false,
                            'message' => strip_tags(validation_errors()),
                            'verifiedBookings'=>[],
                            'unverifiedBookings'=>[]
                            );
                            echo json_encode($mesg);
        }
    }
    
    public function placeList()   // dashboard api
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            date_default_timezone_set('Asia/Kolkata');
	        $this->form_validation->set_rules('verifierId','Verifier Id','required');
	        if($this->form_validation->run()==false)
	        {
	            $errorMsg = $this->form_validation->error_array();
	             $msg = array('status' => false, 'message' => $this->_returnSingle($errorMsg));
            echo json_encode($msg);
	        }
	        else
	        {
	            $verifierId = $this->security->xss_clean($this->input->post('verifierId'));
	               $placeIdList = $this->db->select('place_id')->from('tbl_verifier_place')->where('verifier_id',$verifierId)->where('duty_date',date('Y-m-d'))
	               ->where('isDeleted',0)->get()->result();
	               $list=[];
	               
	               $now = date('Y-m-d');
	               $sensorIssueData=array('isObjectOverSensor'=>false,'noOfIssues'=>'0','isSlotListAvailable'=>false);
	               if(count($placeIdList)>0){
	                   foreach($placeIdList as $placeId)
	                   {
	                   $number = [];
	                   $placeList = $this->db->select("id,vendor_id,placename,place_address,no_of_slots,place_status")
	                   ->from('ci_parking_places')
	                   ->where('id',$placeId->place_id)
	                   ->get()
	                   ->result();
	                   $noOfBookings = $this->db->select("*")
	                   ->from('ci_booking')
	                   ->where('place_id',$placeId->place_id)
	                   ->where("(booking_status='0' OR booking_status='3')", NULL, FALSE)
	                   ->where('is_deleted',0)
	                   ->get()
	                   ->result();
	                   if(count($placeList)>0)
	                   {
	                       //$getEnforcerPlace = $this->db->select('*')
	                       //->from('tbl_enforcer_place')
	                       //->where('place_id',$placeId->place_id)
	                       //->get()
	                       //->result();
	                       //if(count($getEnforcerPlace)>0){
    	                        $getEnforceDetails= $this->db->select('*')
    	                       ->from('ci_support_master')
    	                       ->where('id','1')
    	                       ->where('is_deleted','0')
    	                       ->order_by('id asc')
    	                       ->get()
    	                       ->result();
        	                       if(count($getEnforceDetails)>0)
        	                       {
        	                           $placeList[0]->enforcerNo = $getEnforceDetails[0]->contact;
        	                       }
        	                       else
        	                       {
        	                           $placeList[0]->enforcerNo ='';
        	                           
        	                       }
	                           
	                       //}else{
	                       //    $placeList[0]->enforcerNo ='';
	                       //}
	                       
	                       if(count($noOfBookings)>0){
	                           foreach($noOfBookings as $v){
	                       $startdate = date('Y-m-d',strtotime($v->booking_from_date));
	                       $enddate = date('Y-m-d',strtotime($v->booking_to_date));
        	                       if($now>=$startdate&&$now<=$enddate)
        	                       {
        	                           // print_r($v);
        	                   //exit();
        	                       array_push($number,$v);
        	                       //print_r($placeList);
        	                       $placeList[0]->noOfBookings=count($number);
        	                       }
        	                       else
        	                       {
        	                           $placeList[0]->noOfBookings=count($number);
        	                       }
	                           }
	                       }
	                       else{
	                           $placeList[0]->noOfBookings=0;
	                       }
	                       $sensorIssueData=    $this->placeList_issueDetect($placeId->place_id);
	                       $placeList[0]->sensorIssueData=$sensorIssueData;
	                       if($placeList[0]->place_status=='1'){
	                           array_push($list,$placeList[0]);
	                       }
	                       //print_r($list);
	                       //exit();
	                       
	                   
	                   }
	                   }
	                   $msg = array('status' => true, 'message' => 'List of Places...','data'=>$list
	                   //,'sensorIssueData'=>$sensorIssueData
	                   );
                      echo json_encode($msg);
	               }else{
	                   $msg = array('status' => false, 'message' => 'List of Places...','data'=>[]
	                   //,'sensorIssueData'=>$sensorIssueData
	                   );
                       echo json_encode($msg);
	               }
	        }
        }
    }     

    
    public function placeList_issueDetect($place_id)
    {
         date_default_timezone_set('Asia/Kolkata');
         
	           // $verifierId = $this->security->xss_clean($this->input->post('verifierId'));
	           // $place_id = $this->security->xss_clean($this->input->post('place_id'));
	            $slotList = $this->db->select('*')->from('ci_parking_slot_info')
	            ->where('ci_parking_slot_info.place_id',$place_id)
	            ->where('ci_parking_slot_info.status','0')
	            ->where('ci_parking_slot_info.is_deleted','0')
	           // ->where('tbl_sensor_list.is_deleted','0')
	           // ->where('tbl_sensor_list.test_status','1')
	            ->get()->result_array();
	            $current_time=date("H:i:s");
	            $current_date=date("Y-m-d");
                //   print($current_time);
                $currentdatetime=strtotime(date("Y-m-d H:i:s"));
                $enddate = strtotime("-3 min", $currentdatetime);
                $currentdatetime_d =date("Y-m-d H:i:s", $currentdatetime);
                $enddate_d=date("Y-m-d H:i:s", $enddate);
                $listSensor=[];
                // print_r($slotList);
	            if(count($slotList)>0){
    	            foreach($slotList as $slot){
    	                if($slot['isBlocked']=='1'){
    	                 $sensorregisteredList = $this->db->select('*')->from('tbl_sensor_list')->where('id',$slot['machine_id'])->where('is_deleted','0')->where('test_status','1')->get()->result_array();
    	                if(count($sensorregisteredList)>0){
    	                 $sensorDataList = $this->db->select('*')->from('mpc_sensor')->where('device_id',$sensorregisteredList[0]['device_id'])
                        ->where('sensor_time<=',$currentdatetime_d)
                        ->where('sensor_time>=',$enddate_d)
                        ->order_by('id' , 'desc')->get()->result_array();
                        
                        // print_r($sensorDataList);
                            if(count($sensorDataList)>0)
                            {
                                $redStatusCount =0;
                                foreach($sensorDataList as $details)
                                {
                                    if($details['status']=='1'){
                                        $redStatusCount=$redStatusCount+1;
                                    }
                                }
                                
                               
                                    $getSlotData = 
                                        $this->db->select('slot_no,display_id,place_id')->from('ci_parking_slot_info')
                                        ->where('machine_id', $sensorregisteredList[0]['id'])->where('status', '0')->where('onOff_applied','0')->where('is_deleted', '0')
                                        ->get()->result_Array();
                                            if(count($getSlotData)>0)
                                            {
                                                if($redStatusCount>=(count($sensorDataList)/2))
                                                {
                                                $getBooking = $this->db->select('*')->from('ci_booking')->where('slot_id',$getSlotData[0]['slot_no'])
                                                ->group_start()->where('booking_status','0')->or_where('booking_status','3')->group_end()
                                                ->where('booking_from_date<=',$current_date)
                                                ->where('booking_to_date>=',$current_date)
                                                // ->where('reserve_from_time<=',$current_time)
                                                // ->where('reserve_to_time>=',$current_time)
                                                ->where('is_deleted',"0")
                                                ->get()->result_array();
                                                
                                                if(count($getBooking)>0)
                                                {
                                                    // $currentdatetime_d =date("Y-m-d H:i:s", $getBooking[0]);
                                                    if($getBooking[0]['booking_from_date']== $getBooking[0]['booking_to_date']){
                                                        if($getBooking[0]['reserve_from_time']<=$current_time && $getBooking[0]['reserve_to_time']>=$current_time ){
                                                        
                                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                            'slot_no'=>$getSlotData[0]['slot_no'],
                                                            'display_id'=>$getSlotData[0]['display_id'],
                                                            // ,
                                                            'status'=>0,
                                                            'color'=>'Yellow',
                                                            'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' is booked'
                                                            ));
                                                            
                                                        }
                                                        else{
                                                           array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                              'color'=>'Red',
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                        }
                                                    
                                                }else{
                                                    $currendate_fulld=date("Y-m-d H:i:s");
                                                    $currentdate=date('Y-m-d');
                                                        
                                                         if($getBooking[0]['booking_type']=='0') //daily
                                                    {
                                                        $startdate_fulld =date("Y-m-d H:i:s",strtotime($getBooking[0]['booking_from_date'].' '.$getBooking[0]['reserve_from_time']));
                                                    $enddate_fulld=date("Y-m-d H:i:s", strtotime($getBooking[0]['booking_to_date'].' '.$getBooking[0]['reserve_to_time']));
                                                     if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld )
                                                     {
                                                        
                                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                            'slot_no'=>$getSlotData[0]['slot_no'],
                                                            'display_id'=>$getSlotData[0]['display_id'],
                                                            // ,
                                                            'status'=>0,
                                                            'color'=>'Yellow',
                                                            'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' is booked'
                                                            ));
                                                            }
                                                    else{
                                                           array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                              'color'=>'Red',
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                        }
                                                        
                                                    }else{
                                                       $startdate_fulld =date("Y-m-d H:i:s",strtotime($currentdate.' '.$getBooking[0]['reserve_from_time']));
                                                    $enddate_fulld=date("Y-m-d H:i:s", strtotime($currentdate.' '.$getBooking[0]['reserve_to_time']));
                                                     if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld )
                                                     {
                                                        
                                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                            'slot_no'=>$getSlotData[0]['slot_no'],
                                                            'display_id'=>$getSlotData[0]['display_id'],
                                                            // ,
                                                            'status'=>0,
                                                            'color'=>'Yellow',
                                                            'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' is booked'
                                                            ));
                                                            }
                                                    else{
                                                           array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                              'color'=>'Red',
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                        }
                                                    }
                                                }
                                                    
                                                }
                                                else
                                                {
                                                    
                                                       
                                                             array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                    'color'=>'Red',
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                            
                                                         
                                                
                                                }
                                                
                                                }
                                            else{
                                                array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                 'slot_no'=>$slot['slot_no'],
                                                    'display_id'=>$slot['display_id'],
                                                    'status'=>2,
                                                    'color'=>'Green',
                                                'msg'=>'Nothing is present over sensor. '.$sensorregisteredList[0]['device_id']));
                                            }
                                        }
                                        else{
                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                             'slot_no'=>$slot['slot_no'],
                                            'display_id'=>$slot['display_id'],
                                            'status'=>3,
                                            'color'=>'Grey',
                                            'msg'=>'Sensor id is '.$sensorregisteredList[0]['device_id'].' not connected with any slot.'));
                                        }
                               
                            
                                     
                                            
                                    
                                }
                            else
                            {
                               array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                'slot_no'=>$slot['slot_no'],
                                'display_id'=>$slot['display_id'],
                                'status'=>4,
                                'color'=>'Orange',
                                'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' not responding.'));
                            }
    	                }
    	                else{
    	                    array_push($listSensor,array('deviceid'=>'0',
                                'slot_no'=>$slot['slot_no'],
                                'display_id'=>$slot['display_id'],
                                'status'=>3,
                                'color'=>'Grey',
                                'msg'=>'slot no. '.$slot['slot_no'].' not connected to anyone sensor.'));
    	                }
    	                }
    	                else{
    	                    
        	                    array_push($listSensor,array('deviceid'=>'0',
                                    'slot_no'=>$slot['slot_no'],
                                    'display_id'=>$slot['display_id'],
                                    'status'=>5,
                                    'color'=>'black',
                                    'msg'=>'slot no. '.$slot['slot_no'].' is inaccessible.'));
        	                }
    	                
    	                
    	            }
    	            $isObjectOverSensor = false;
    	            $noOfIssues=0;
    	            foreach($listSensor as $sensorData){
    	                if($sensorData['status']=='1'){
    	                    $noOfIssues=$noOfIssues+1;
    	                }
    	            }
    	            
    	            if($noOfIssues>0){
    	                $isObjectOverSensor=true;
    	            }
    	            $issueData = array('isObjectOverSensor'=>$isObjectOverSensor,'noOfIssues'=>$noOfIssues,'isSlotListAvailable'=>true);
    	           // echo json_encode(array('status'=>true,'message'=>'list of data','slotdetails'=>$isObjectOverSensor,'noOfIssues'=>$noOfIssues));
    	           // echo json_encode(array('status'=>true,'message'=>'list of data','slotdetails'=>$listSensor));
    	           return $issueData;
	            }else{
	                $issueData = array('isObjectOverSensor'=>false,'noOfIssues'=>'0','isSlotListAvailable'=>false);
	                return $issueData;
	               // echo json_encode(array('status'=>false,'message'=>'No data available','slotdetails'=>$listSensor));
	            }
	            
	        
    }
    
    public function verifyParking()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            date_default_timezone_set('Asia/Kolkata');
	        $this->form_validation->set_rules('bookingId','Booking Id','required');
	        $this->form_validation->set_rules('verifierId','Verifier Id','required');
	        $this->form_validation->set_rules('bookingType','Booking Type','required');
	        $this->form_validation->set_rules('verifyStatus','Verify Status','required'); //1=verified 0=not verified
	        if($this->form_validation->run()==false)
	        {
	            $errorMsg = $this->form_validation->error_array();
	             $msg = array('status' => false, 'message' => $this->_returnSingle($errorMsg));
            echo json_encode($msg);
	        }
	        else
	        {
	            $bookingId = $this->security->xss_clean($this->input->post('bookingId'));
	            $verifierId = $this->security->xss_clean($this->input->post('verifierId'));
	            $bookingType = $this->security->xss_clean($this->input->post('bookingType'));
	            $verifyStatus = $this->security->xss_clean($this->input->post('verifyStatus'));
	            $update = $this->db->insert('ci_booking_verify',array('verifier_id'=>$verifierId,'booking_id'=>$bookingId,'booking_type'=>$bookingType,'verify_status'=>$verifyStatus==1?'1':'0'));
	               if($update)
	               {
	                   $updateBookingCheckin = $this->db->insert('ci_booking_check',array('check_in'=>date("Y-m-d H:i:s"),
	                   'booking_id'=>$bookingId,
	                   'verifier_id'=>$verifierId,
	                   'created_at '=>date("Y-m-d"),
	                   //'updated_date'=>date("Y-m-d H:i:s"),
	                   ));
	                   /*
	id 	booking_id 	check_in 	check_out 	verifier_id 	check_type 0=no action,1=force,2=auto 	is_deleted 0 = active , 1= deleted */
	                   $booking = $this->db->from('ci_booking')->where('id',$bookingId)->get()->result_array();
	                   if(count($booking)>0)
	                   {
                            $message = 'Your booking  ' . $booking[0]['unique_booking_id'] . ' is successfully verified by our Guid. '.'🚗😃';
                            $isUseArrow=false;
                            $this->notificationallApiBuilding_Verifier($booking[0], 'Booking Verified', $message, '3', '1',false,$isUseArrow);
	                   }
	                   $msg = array('status' => true, 'message' => 'Successfully Verified');
                      echo json_encode($msg);
	               }
	               else{
	                   $msg = array('status' => false, 'message' => 'Verification Failed');
                      echo json_encode($msg);
	               }
	        }
        }
    }
    
    public function notificationallApiBuilding_Verifier($b, $title, $body, $screen, $notifyType,$insertoDB,$isUseArrow) // this function is uses firebase api to send notification.   bool $insertoDB =true or false
    {
        // $buildingId = 394;
        // $societyId = 14;
        $getUserTopic = $this->db->select('notifn_topic')->from('ci_users')
            ->where('id', $isUseArrow==true?$b->user_id:$b['user_id'])
            ->where('is_active', '1')
            ->get()
            ->result();
        // $getbuildingName = $this->db->select('building_name')->from('tbl_society_setup')->where('building_id',$buildingid)->get()->result();
        // print_r($getUserTopic);
        if (count($getUserTopic) > 0)
        {
            $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
            // $token='all';
            $token = $getUserTopic[0]->notifn_topic;
            // print (' mmmm ');
            // print ($token);
            // print (' mmmm ');
            // print($token);
            $notification = ['title' => $title, 'body' => $body, 'icon' => 'myIcon', 'sound' => 'default_sound'];

            $extraNotificationData = ['title' => $title, 'body' => $body, 'screen' => $screen, 'bookingid' =>  $isUseArrow==true?$b->id:$b['id'], "click_action" => "FLUTTER_NOTIFICATION_CLICK"];

            $fcmNotification = ['to' => '/topics/' . $token, //single token
            'notification' => $notification, 'data' => $extraNotificationData];

            $headers = ['Authorization: key=' . 'AAAASeBlySQ:APA91bG5g4s-FAsFw9zfKEJ638XWzhpSGbeUa4jallP5rh0wG6dozGFrihHYj4bneh3qoGrFS74QO7Ra5l_kuTXpnH40KptG6wZvoZcGJGLBdjwMRLL8F6Ajfv9CWSRNqemDaVlvgHDB', 'Content-Type: application/json'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fcmUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
            $result = curl_exec($ch);
            // print($result);
            if ($result)
            {
                if($insertoDB==true){
                    $this
                        ->db
                        ->insert('ci_notify_track', array(
                        "notify_type" => $notifyType,
                        "booking_id" => $isUseArrow==true?$b->id:$b['id'],
                        "user_id" => $isUseArrow==true?$b->user_id:$b['user_id'],
                        "place_id" => $isUseArrow==true?$b->place_id:$b['place_id'],
                        "slot_id" => $isUseArrow==true?$b->slot_id:$b['slot_id']
                    ));
                }
            }
            curl_close($ch);

            // echo $result;
            
        }
        else
        {
            // echo 'no building found'.$buildingid;
            
        }
    }

    public function raise_issue()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            date_default_timezone_set('Asia/Kolkata');
	        $this->form_validation->set_rules('verifierId','Verifier Id','required');
	        $this->form_validation->set_rules('place_id','Place Id','required');
	        $this->form_validation->set_rules('slot_id','Slot Id','required');
	        $this->form_validation->set_rules('booking_id','Booking Id','required');
	        $this->form_validation->set_rules('complaint_id','Complaint Id','required');
	       // $this->form_validation->set_rules('complaint_text','Complaint Text','required');
	        $this->form_validation->set_rules('enforcer_id','Enforcer Id','required');
	        $this->form_validation->set_rules('issueimg','Image','required');
	       // $this->form_validation->set_rules('enforcer_id','Enforcer Id','required');
	        
	        if($this->form_validation->run()==false)
	        {
	            $errorMsg = $this->form_validation->error_array();
	             $msg = array('status' => false, 'message' => $this->_returnSingle($errorMsg));
            echo json_encode($msg);
	        }
	        else
	        {
	            $verifierId = $this->security->xss_clean($this->input->post('verifierId'));
	            $place_id = $this->security->xss_clean($this->input->post('place_id'));
	            $slot_id = $this->security->xss_clean($this->input->post('slot_id'));
	            $booking_id = $this->security->xss_clean($this->input->post('booking_id'));
	            $complaint_id = $this->security->xss_clean($this->input->post('complaint_id'));
	            $complaint_text = $this->security->xss_clean($this->input->post('complaint_text')==''?'':$this->input->post('complaint_text'));
	            $enforcer_id = $this->security->xss_clean($this->input->post('enforcer_id'));
	            $actionPerformedByVerifier = $this->security->xss_clean($this->input->post('desposition_id'));
	            //issueimg
	            $issueimg = $this->security->xss_clean($this->input->post('issueimg'));
	            
	            //actionPerformedByVerifier
	            /*verifier_id 	place_id 	slot_id 	booking_id 	complaint_id 
	            complaint_text 	enforcer_id 	createdDate 	status 0 = unsolved , 1 = solved 	resolvedDate 	remark 	is_deleted  */
	            
	              $image = base64_decode($issueimg);
                                
                                $imagename = md5(uniqid(rand(), true));
                                $filename = $imagename .'.' .'png';
                                $path = base_url()."uploads/slot_complaints/".$filename;
                                $pathtosave ="./uploads/slot_complaints/".$filename;
                                file_put_contents($pathtosave, $image);
                              
	            $data = array('verifier_id'=>$verifierId,'place_id'=>$place_id,'slot_id'=>$slot_id,'booking_id'=>$booking_id,
	            'complaint_id'=>$complaint_id,'complaint_text'=>$complaint_text,'enforcer_id'=>$enforcer_id,'status'=>'2',
	            'actionPerformedByVerifier'=>$actionPerformedByVerifier,'issue_img'=>$path);
	            
	            $inserdata= $this->db->insert('tbl_verifier_complaints',$data);
	            if($inserdata){
	                $this->db->where('id',$booking_id)->update('ci_booking',array('booking_status'=>'3'));
	                $msg = array('status' => true, 'message' => 'Successfully registered issue.');
                      echo json_encode($msg);
	            }else{
	               $msg = array('status' => false, 'message' => 'Failed to register issue.');
                      echo json_encode($msg); 
	            }
	            
	        }
        }
    }
    
    public function countryprefixadd()
    {
        
                $indianStates = ['AR' => 'Arunachal Pradesh',
'AR' => 'Arunachal Pradesh',
'AS' => 'Assam',
'BR' => 'Bihar',
'CT' => 'Chhattisgarh',
'GA' => 'Goa',
'GJ' => 'Gujarat',
'HR' => 'Haryana',
'HP' => 'Himachal Pradesh',
'JK' => 'Jammu and Kashmir',
'JH' => 'Jharkhand',
'KA' => 'Karnataka',
'KL' => 'Kerala',
'MP' => 'Madhya Pradesh',
'MH' => 'Maharashtra',
'MN' => 'Manipur',
'ML' => 'Meghalaya',
'MZ' => 'Mizoram',
'NL' => 'Nagaland',
'OR' => 'Odisha',
'PB' => 'Punjab',
'RJ' => 'Rajasthan',
'SK' => 'Sikkim',
'TN' => 'Tamil Nadu',
'TG' => 'Telangana',
'TR' => 'Tripura',
'UP' => 'Uttar Pradesh',
'UT' => 'Uttarakhand',
'WB' => 'West Bengal',
'AN' => 'Andaman and Nicobar Islands',
'CH' => 'Chandigarh',
'DN' => 'Dadra and Nagar Haveli',
'DD' => 'Daman and Diu',
'LD' => 'Lakshadweep',
'DL' => 'National Capital Territory of Delhi',
'PY' => 'Puducherry'];
                // $getStateList = $this->db->select('*')->from('ci_states')->where('country_id','101')->get()->result();
                // foreach($getStateList as $state){
                //     if('')
                    
                // }
                foreach($indianStates  as $key=>$value){
                    // print_r($key.' '.$value);
                    $this->db->where('name',$value)->update('ci_states',array('prefix'=>$key));
                }
    }
    
    public function sensorSlotstatusDetection()
    {
         date_default_timezone_set('Asia/Kolkata');
         $sensorList = $this->db->select('*')->from('tbl_sensor_list')->where('is_deleted','0')->where('test_status','1')->get()->result_array();
       
        $current_time=date("H:i:s");
        //   print($current_time);
        $current_date=date("Y-m-d");
        $currentdatetime=strtotime(date("Y-m-d H:i:s"));
        $enddate = strtotime("-3 min", $currentdatetime);
        $currentdatetime_d =date("Y-m-d H:i:s", $currentdatetime);
        $enddate_d=date("Y-m-d H:i:s", $enddate);
        
       $listSensor=[];
       
        foreach($sensorList as $sensor){
            $sensorDataList = $this->db->select('*')->from('mpc_sensor')->where('device_id',$sensor['device_id'])
            ->where('sensor_time<=',$currentdatetime_d)
            ->where('sensor_time>=',$enddate_d)
            ->order_by('id' , 'desc')->get()->result_array();
                if(count($sensorDataList)>0)
                {
                    $redStatusCount =0;
                    foreach($sensorDataList as $details)
                    {
                        if($details['status']=='1'){
                            $redStatusCount=$redStatusCount+1;
                        }
                    }
                    
                   
                        $getSlotData = $this->db->select('slot_no,display_id,place_id')->from('ci_parking_slot_info')
                                            ->where('machine_id', $sensor['id'])->where('status', '0')->where('onOff_applied','0')->where('is_deleted', '0')
                                            ->get()->result_Array();
                                if(count($getSlotData)>0)
                                {
                                    if($redStatusCount>=(count($sensorDataList)/2))
                                    {
                                    $getBooking = $this->db->select('*')->from('ci_booking')->where('slot_id',$getSlotData[0]['slot_no'])
                                    ->group_start()->where('booking_status','0')->or_where('booking_status','3')->group_end()
                                    ->where('booking_from_date<=',$current_date)
                                    ->where('booking_to_date>=',$current_date)
                                    // ->where('reserve_from_time<=',$current_time)
                                    // ->where('reserve_to_time>=',$current_time)
                                    ->where('is_deleted',"0")
                                    ->get()->result_array();
                                    if(count($getBooking)>0){
                                       
                                                    if($getBooking[0]['booking_from_date']== $getBooking[0]['booking_to_date']){
                                                        print('same dates');
                                                        if($getBooking[0]['reserve_from_time']<=$current_time && $getBooking[0]['reserve_to_time']>=$current_time ){
                                                        
                                                            array_push($listSensor,array('deviceid'=>$sensor['device_id'],
                                                            'slot_no'=>$getSlotData[0]['slot_no'],
                                                            'display_id'=>$getSlotData[0]['display_id'],
                                                            // ,
                                                            'status'=>0,
                                                            'color'=>'Yellow',
                                                            'msg'=>'Sensor id '.$sensor['device_id'].' is booked'
                                                            ));
                                                            
                                                        }
                                                        else{
                                                            $this->obj_over_sensorlogic($getSlotData[0],$listSensor,$sensor,'9'); // ,9=object over sensor
                                                            }
                                                        }else{
                                                            print('diff dates');
                                                    $currendate_fulld=date("Y-m-d H:i:s");
                                                    $currentdate=date('Y-m-d');
                                                    
                                                     if($getBooking[0]['booking_type']=='0') //daily
                                                    {
                                                        $startdate_fulld =date("Y-m-d H:i:s",strtotime($getBooking[0]['booking_from_date'].' '.$getBooking[0]['reserve_from_time']));
                                                        $enddate_fulld=date("Y-m-d H:i:s", strtotime($getBooking[0]['booking_to_date'].' '.$getBooking[0]['reserve_to_time']));
                                                        if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld ){
                                                                        
                                                             array_push($listSensor,array('deviceid'=>$sensor['device_id'],
                                                             'slot_no'=>$getSlotData[0]['slot_no'],
                                                             'display_id'=>$getSlotData[0]['display_id'],
                                                             // ,
                                                             'status'=>0,
                                                             'color'=>'Yellow',
                                                             'msg'=>'Sensor id '.$sensor['device_id'].' is booked'
                                                             ));
                                                                            
                                                            }
                                                            else
                                                            {
                                                              $this->obj_over_sensorlogic($getSlotData[0],$listSensor,$sensor,'9'); // ,9=object over sensor
                                                            }
                                                        
                                                    }else{
                                                        $startdate_fulld =date("Y-m-d H:i:s",strtotime($currentdate.' '.$getBooking[0]['reserve_from_time']));
                                                        $enddate_fulld=date("Y-m-d H:i:s", strtotime($currentdate.' '.$getBooking[0]['reserve_to_time']));
                                                        if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld ){
                                                                        
                                                             array_push($listSensor,array('deviceid'=>$sensor['device_id'],
                                                             'slot_no'=>$getSlotData[0]['slot_no'],
                                                             'display_id'=>$getSlotData[0]['display_id'],
                                                             // ,
                                                             'status'=>0,
                                                             'color'=>'Yellow',
                                                             'msg'=>'Sensor id '.$sensor['device_id'].' is booked'
                                                             ));
                                                                            
                                                            }
                                                            else
                                                            {
                                                              $this->obj_over_sensorlogic($getSlotData[0],$listSensor,$sensor,'9'); // ,9=object over sensor
                                                            }
                                                    }
                                                }
                                        
                                        }else{
                                             $this->obj_over_sensorlogic($getSlotData[0],$listSensor,$sensor,'9'); // ,9=object over sensor
                                      
                                    
                                    }
                                    }
                                else{
                                    array_push($listSensor,array('deviceid'=>$sensor['device_id'],
                                    'slot_no'=>$getSlotData[0]['slot_no'],
                                    'display_id'=>$getSlotData[0]['display_id'],
                                    'msg'=>'Nothing is present over sensor. '.$sensor['device_id']));
                                }
                            }
                            else{
                                array_push($listSensor,array('deviceid'=>$sensor['device_id'],
                                'slot_no'=>'0',
                                'display_id'=>'0',
                                'msg'=>'Sensor id is '.$sensor['device_id'].' not connected with any slot.'));
                            }
                   
                
                         
                                
                        
                    }
                else{
                    //  $message ='Sensor id: '.$sensor['device_id'].' of Slot :'.$getSlotData[0]['display_id'].' for placename :  '.$placename.' not working currently.';
                    
                    
                     $message ='Sensor id: '.$sensor['device_id'].' not working currently.';
                     $getsensorData = $this->db->select('*')->from('tbl_sensor_list')
                      ->where('device_id',$sensor['device_id'])
                     ->where('test_status','1')
                     ->where('is_deleted','0')->get()->result_array();
                     if(count($getsensorData)>0){
                     // print($message);
                     $getSlotDetails = $this->db->select('*')->from('ci_parking_slot_info')->where('machine_id',$getsensorData[0]['id'])->get()->result_array();
                     if(count($getSlotDetails)>0){
                         
                         $getNotify = $this->db->select('*')->from('ci_notify_track')
                                     ->where('place_id',$getSlotDetails[0]['place_id'])
                                     ->where('slot_id',$getSlotDetails[0]['slot_no'])
                                     ->where('notify_type','10')
                                     ->where('is_deleted','0')->order_by("id", "DESC")
                                     ->get()->result();
                                      if(count($getNotify)<=0){
                                     
                                     $this->issuecreate_sensor($sensor['device_id'],
                                            0,
                                            $getSlotDetails[0]['place_id'],
                                            $getSlotDetails[0]['slot_no'],
                                            'Sensor not working',
                                            $message,
                                            '0',
                                            '10'
                                            );
                                            // $this->notificationApiVerifier($getSlotDetails[0]['place_id'],$getSlotDetails[0]['slot_no'],'Sensor not working',$message,'0','10');
                                      }else{
                                          $notifyLastCreated= $getNotify[0]->onCreated;
                                            $currentDatetime =  new DateTime(date("Y-m-d H:i:s"));
                                            $interval = $currentDatetime->diff(new DateTime($notifyLastCreated));
                                            echo  ' -----  '.$interval->i.' '.$interval->s.' -----  ';
                                            if($interval->i>(30))
                                            { // half day gap for notification
                                                $this->issuecreate_sensor($sensor['device_id'],
                                                                            0,
                                                                            $getSlotDetails[0]['place_id'],
                                                                            $getSlotDetails[0]['slot_no'],
                                                                            'Sensor not working',
                                                                            $message,
                                                                            '0',
                                                                            '10'
                                                                            );
                                             // $message ='Some object is present over Sensor id: '.$sensor['device_id'].' of Slot :'.$getSlotData[0]['display_id'].' for placename :  '.$placename;
                                             // print($message);
                                              
                                             //   $this->notificationApiVerifier($getSlotDetails[0]['place_id'],$getSlotDetails[0]['slot_no'],'Sensor not working',$message,'0', '10');
                                            
                                             //  print(' hii in object ');
                                            }
                                            array_push($listSensor,array('deviceid'=>$sensor['device_id'],
                                                                            'slot_no'=>$getSlotDetails[0]['slot_no'],
                                                                            'display_id'=>$getSlotDetails[0]['display_id'],
                                                                            'msg'=>$message));
                                      }
                    
                    // echo 'Sensor id '.$sensor['device_id'].' not responding.';
                    // print('</br>');
                    }
                     else{
                         array_push($listSensor,array('deviceid'=>$sensor['device_id'],
                            'slot_no'=>'0',
                            'display_id'=>'0',
                            'msg'=>'Sensor id '.$sensor['device_id'].' not connected with any slots.'));
                     }
                         
                     }else{
                            array_push($listSensor,array('deviceid'=>$sensor['device_id'],
                            'slot_no'=>'0',
                            'display_id'=>'0',
                            'msg'=>'Sensor id '.$sensor['device_id'].' not registered or its in testing mode.'));
            }
            }
            
            
        }
        echo json_encode($listSensor);
    }
    
    public function issuecreate_sensor($deviceid,$isSensorIssue,$place_id,$slot_no,$title,$message,$screen,$notifyType)
    {       //engineering complaints
    
                $sensorcomp= $this->db->select('*')->from('ci_eng_complaint')
                ->where('device_id',$deviceid)
                ->where('status','0')
                ->where('issue_type',$isSensorIssue)
                ->order_by('id DESC')
                // ->like('created_at', array('dates' => date('Y-m-d')))
                ->get()->result_array();
        // print_r($sensorcomp);die;
                if(count($sensorcomp)>0)
                {
               
                  $currentdatetime=strtotime(date("Y-m-d H:i:s"));
                  $enddate = strtotime("-3 min", $currentdatetime);
                  $currentdatetime_d =date("Y-m-d H:i:s", $currentdatetime);
                  $enddate_d=date("Y-m-d H:i:s", $enddate);
        
    
                  $sensorDataList = $this->db->select('*')->from('mpc_sensor')->where('device_id',$deviceid)
                                    ->where('sensor_time<=',$currentdatetime_d)
                                    ->where('sensor_time>=',$enddate_d)
                                    ->order_by('id' , 'desc')->get()->result_array(); 
                                    // print_r($sensorDataList);
                  
                  if(count($sensorDataList)>0){
                      $this->db->where('id',$sensorcomp[0]['id'])->update('ci_eng_complaint',array(
                            'status'=>1
                            ));
                            $message ='Sensor id: '.$deviceid.' started working currently.';
                      $this->notificationApiVerifier($place_id,
                                                           $slot_no,
                                                           'Sensor working',
                                                           $message,
                                                           $screen,
                                                           $notifyType);
                  }else{
                                    
                    $currentdate = date('Y-m-d');
                    $compdate =date("Y-m-d",strtotime($sensorcomp[0]['created_at']));
                    // $enddate_fulld=date("Y-m-d H:i:s", strtotime($getBooking[0]['booking_to_date'].' '.$getBooking[0]['reserve_to_time']));
                    if($currentdate>$compdate){
                        
                        // $this->db->insert('ci_eng_complaint',array(
                        //     'device_id'=>$deviceid,
                        //     'issue_type'=>$isSensorIssue
                        //     ));
                            $this->notificationApiVerifier($place_id,
                                                           $slot_no,
                                                           $title,
                                                           $message,
                                                           $screen,
                                                           $notifyType);
                    }
                      
                  }
                }else{
                    $this->db->insert('ci_eng_complaint',array(
                            'device_id'=>$deviceid,
                            'issue_type'=>$isSensorIssue
                            ));
                            $this->notificationApiVerifier($place_id,
                                                           $slot_no,
                                                           $title,
                                                           $message,
                                                           $screen,
                                                           $notifyType);
                }
        
        // print_r($sensorcomp);
    }
    
    public function obj_over_sensorlogic($getSlotData,$listSensor,$sensor,$msgtype)  //$msgtype : 10=senser not working,9=object detected without booking
    { 
       
         $getplacedetails = $this->db->select('placename')->from('ci_parking_places')->where('id',$getSlotData['place_id'])->get()->result_array();
         $placename = count($getplacedetails)>0?$getplacedetails[0]['placename']:'';
                                        
                                        
                                         $getNotify = $this->db->select('*')->from('ci_notify_track')
                                         ->where('place_id',$getSlotData['place_id'])
                                         ->where('slot_id',$getSlotData['slot_no'])
                                         ->where('notify_type',$msgtype)
                                         ->where('is_deleted','0')->order_by("id", "DESC")
                                         ->get()->result();
                                        // print_r($getNotify);
                                         $message=''; 
                                                 $message_obj ='Some object is present over Sensor id: '.$sensor['device_id'].' of Slot :'.$getSlotData['display_id'].' for placename :  '.$placename;
                                                 $message_notwrk ='Sensor id: '.$sensor['device_id'].' not working currently.';
                                              $message=$msgtype=='9'?$message_obj:$message_notwrk;   
                                        if(count($getNotify)<=0)
                                        {
                                                
                                              
                                                 // print($message);
                                                 $this->notificationApiVerifier($getSlotData['place_id'],$getSlotData['slot_no'],'Sensor object present',$message,'0','9');
                                                
                                                  array_push($listSensor,array('deviceid'=>$sensor['device_id'],
                                                  'slot_no'=>$getSlotData['slot_no'],
                                                  'display_id'=>$getSlotData['display_id']
                                                  ,'msg'=>$message
                                                //   'Some object present over Sensor id is'.$sensor['device_id']
                                                 ));
                                                 
                                                 
                                                 
                                             }
                                        else
                                        {
                                                $notifyLastCreated= $getNotify[0]->onCreated;
                                                // // $datetime1 = new DateTime();
                                                // // $datetime2 = new DateTime('2011-01-03 17:13:00');
                                                // $interval = strtotime($currentdatetime)->diff(strtotime($notifyLastCreated));
                                                $currentDatetime =  new DateTime(date("Y-m-d H:i:s"));
                                                $interval = $currentDatetime->diff(new DateTime($notifyLastCreated));
                                                echo  ' -----  '.$interval->i.' '.$interval->s.' -----  ';
                                                if($interval->i>30){
                                                    // $message ='Some object is present over Sensor id: '.$sensor['device_id'].' of Slot :'.$getSlotData[0]['display_id'].' for placename :  '.$placename.' ðŸ¤”';
                                                 // print($message);
                                                 $this->notificationApiVerifier($getSlotData['place_id'],$getSlotData['slot_no'],'Sensor object present',$message,'0','9');
                                                
                                                //  print(' hii in object ');
                                                }
                                                 array_push($listSensor,array('deviceid'=>$sensor['device_id'],
                                                  'slot_no'=>$getSlotData['slot_no'],
                                                  'display_id'=>$getSlotData['display_id']
                                                  ,'msg'=>$message
                                                //   'Some object present over Sensor id is'.$sensor['device_id']
                                                 ));
                                                
                                             }
    }
    
    public function notificationApiVerifier($placeid,$slot_id,$title, $body,$screen,$notifyType) // this function is uses firebase api to send notification.
    {
        // $buildingId = 394;
        // $societyId = 14;
        $getVerifierList=$this->db->select('*')->from('tbl_verifier_place')->where('place_id',$placeid)->where('duty_date',date('Y-m-d'))->where('isDeleted','0')->get()->result();
        foreach($getVerifierList as $verifier){//verifier_id
        // $getUserTopic = $this->db->select('notify_topic')->from('tbl_verifier')->where('id',$verifier->verifier_id)->where('isDeleted','0')->get()->result();
        $getUserTopic = $this->db->select('notifn_topic')->from('ci_admin')
        ->where('admin_id',$verifier->verifier_id)
        ->where('admin_role_id','3')->where('is_active','1')->get()->result();
        // $getbuildingName = $this->db->select('building_name')->from('tbl_society_setup')->where('building_id',$buildingid)->get()->result();
        // print_r($getUserTopic);
            if(count($getUserTopic)>0){
            $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
            // $token='all';
            $token = $getUserTopic[0]->notifn_topic;
            print('token is printed : '.$token);
            // print($token);
         $notification = [
                'title' =>$title,
                'body' => $body,
                'icon' =>'myIcon', 
                // 'color'=> "#3352ff",
                'sound' => 'default_sound'
            ];
            
            $extraNotificationData = ['title' =>$title,
                'body' => $body,'screen'=>$screen,
                // 'bookingid'=>$b->id,
                
                "click_action"=> "FLUTTER_NOTIFICATION_CLICK"];
    
            $fcmNotification = [
                'to'=> '/topics/'.$token, //single token
                'notification' => $notification,
                
                'data' => $extraNotificationData
            ];
    
            $headers = [
                'Authorization: key=' . 'AAAASeBlySQ:APA91bG5g4s-FAsFw9zfKEJ638XWzhpSGbeUa4jallP5rh0wG6dozGFrihHYj4bneh3qoGrFS74QO7Ra5l_kuTXpnH40KptG6wZvoZcGJGLBdjwMRLL8F6Ajfv9CWSRNqemDaVlvgHDB',
                'Content-Type: application/json'
            ];
    
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$fcmUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
            $result = curl_exec($ch);
            // print($result);
            if($result){
                $this->db->insert('ci_notify_track',array("notify_type"=>$notifyType,"booking_id"=>'0',"user_id"=>'0',"place_id"=>$placeid,"slot_id"=>$slot_id ));
            }
            curl_close($ch);
    
    
            // echo $result;
                
            }else{
                // echo 'no building found'.$buildingid;
            }
            
        }
    }
    
    public function checknullValidation($stringData)
    {
        return $stringData!=null?$stringData:'';
    }
    
    
    public function getAllSlotInfo(){
       
       	$this->db->select('id as place_id');
		$this->db->from('ci_parking_places');
		$where = "place_status = 1 AND id!='1'";
		$this->db->where($where);
		$query=$this->db->get();
		$data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		$final = [];
		foreach($data as $key => $values){
		    
		    
		    $place_id = $values['place_id'];
		    $placename = $this->db->select('placename')->from('ci_parking_places')
	            ->where('id',$place_id)->get()->result_array()[0]['placename'];
		    $place_status = $this->getDeviceStatus($place_id);
		    $place_status['placename'] = $placename;
		    array_push($final,$place_status);
		}
	
        echo json_encode($final);
		
        
		
	            
       
    }
    
    
    public function getAllSlotInfo1(){
       
       	$this->db->select('id as place_id');
		$this->db->from('ci_parking_places');
		$where = "place_status = 1 AND id!='1'";
		$this->db->where($where);
		$query=$this->db->get();
		$data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		foreach($data as $key => $values){
    	    $place_id = $values['place_id'];
		    $placename = $this->db->select('placename')->from('ci_parking_places')
	            ->where('id',$place_id)->get()->result_array()[0]['placename'];
		    $place_status[] = ($this->getAllSensorsData($place_id));
		    $place_status[$key]['placename'] = $placename;
		}
		
	
		$final=[];
		foreach($place_status as $key => $val){
		    foreach($val['slotdetails'] as $k =>$val1){
		        if($val1['color'] == 'Orange'){
		           $f['deviceid'] = $val1['deviceid'];
		           $f['slot_no']  = $val1['slot_no'];
		           $f['placename'] = $this->getPlaceName($val1['slot_no'])[0]['placename'];
		           $f['display_id']  = $val1['display_id'];
		           $f['color']  = $val1['color'];
		           $f['msg']  = $val1['msg'];
		           array_push($final,$f);
		        }
		        
		    }
		}
		
		echo json_encode($final);
	
       	
    }
    
    
    public function getPlaceName($slot_no){
        
         
		     $this->db->select('cpp.placename');
             $this->db->from('ci_parking_slot_info cpsi');
             $this->db->join('ci_parking_places cpp','cpsi.place_id = cpp.id','left');
             $where = "cpsi.slot_no=$slot_no AND cpsi.is_deleted=0";
             $this->db->where($where);
		     $data = array();
		     $query = $this->db->get();
		     if($query !== FALSE && $query->num_rows() > 0){
   		     	 $data = $query->result_array();
		     }
		    return $data;
    }
    
    
    
    
    
    
    
 public function getDeviceStatus($id)
    {
         
	            $place_id = $id; 
	            $slotList = $this->db->select('*')->from('ci_parking_slot_info')
	            ->where('ci_parking_slot_info.place_id',$place_id)
	            ->where('ci_parking_slot_info.status','0')
	            ->where('ci_parking_slot_info.is_deleted','0')
	            ->order_by('slot_no asc')
	           // ->where('tbl_sensor_list.is_deleted','0')
	           // ->where('tbl_sensor_list.test_status','1')
	            ->get()->result_array();
	            $current_time=date("H:i:s");
	            $current_date=date("Y-m-d");
                //   print($current_time);
                $currentdatetime=strtotime(date("Y-m-d H:i:s"));
                $enddate = strtotime("-30 min", $currentdatetime);
                $currentdatetime_d =date("Y-m-d H:i:s", $currentdatetime);
                $enddate_d=date("Y-m-d H:i:s", $enddate);
                $listSensor=[];
                // print_r($slotList);
	            if(count($slotList)>0){
    	            foreach($slotList as $slot){
    	             
                        $isIssueOn = false;
                        
    	                $getSlotRaisedIssueData = $this->db->select('*')->from('ci_slots_complaints')
    	               ->where('slot_id',$slot['slot_no'])->where('complaint_status!=','2')->get()->result_array();
    	               if(count($getSlotRaisedIssueData)>0){
    	                   $isIssueOn=true;
    	                   $issueData =array('id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['id']),
    	                'place_id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['place_id']),
    	                'slot_id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['slot_id']),
    	                'is_verified'=>$this->checknullValidation($getSlotRaisedIssueData[0]['is_verified']),//0:Not Verifierd 1: Veriferd 	
                        'verifier_id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['verifier_id']),
                        'engineer_id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['engineer_id']),
                        'complaint_status'=>$this->checknullValidation($getSlotRaisedIssueData[0]['complaint_status']),//0:Pending 1:Processing 2:Solved 	
                        'complaint_text'=>$this->checknullValidation($getSlotRaisedIssueData[0]['complaint_text']),	
                        'verifier_remark'=>$this->checknullValidation($getSlotRaisedIssueData[0]['verifier_remark']),
                        'engineer_remark'=>$this->checknullValidation($getSlotRaisedIssueData[0]['engineer_remark']),
                        'img_attachments'=>$this->checknullValidation($getSlotRaisedIssueData[0]['img_attachments']),
                        'complaint_source'=>$this->checknullValidation($getSlotRaisedIssueData[0]['complaint_source']), //0:Replacement 1: Verifier App 	
                        'issue_raised_on'=>$this->checknullValidation($getSlotRaisedIssueData[0]['issue_raised_on']),
                        'issue_resolved_on'=>$getSlotRaisedIssueData[0]['complaint_status']==2?$this->checknullValidation($getSlotRaisedIssueData[0]['issue_resolved_on']):'');
    	               }
    	                if($slot['isBlocked']=='1'){
    	                 $sensorregisteredList = $this->db->select('*')->from('tbl_sensor_list')->where('id',$slot['machine_id'])->where('is_deleted','0')->where('test_status','1')->get()->result_array();
    	                if(count($sensorregisteredList)>0){
    	                 $sensorDataList = $this->db->select('*')->from('mpc_sensor')->where('device_id',$sensorregisteredList[0]['device_id'])
                        ->where('sensor_time<=',$currentdatetime_d)
                        ->where('sensor_time>=',$enddate_d)
                        ->order_by('id' , 'desc')->get()->result_array();
                        
                        // print_r($sensorDataList);
                            if(count($sensorDataList)>0)
                            {
                                $redStatusCount =0;
                                foreach($sensorDataList as $details)
                                {
                                    if($details['status']=='1'){
                                        $redStatusCount=$redStatusCount+1;
                                    }
                                }
                                
                               
                    
                                        $bookingList =  $this->db->select('cpsi.slot_no,cpsi.display_id,cpsi.place_id,cpp.placename');
                                        $this->db->from('ci_parking_slot_info as cpsi');
                                        $this->db->join('ci_parking_places as cpp', 'cpsi.place_id = cpp.id');
                                        $this->db->where('cpsi.status', 0);
                                        $this->db->where('cpsi.onOff_applied', 0);
                                        $this->db->where('cpsi.is_deleted', 0);
                                        $getSlotData = $this->db->get()->result_array();
            
            
                                        
                                       
                                        
                                        
                                            if(count($getSlotData)>0)
                                            {
                                                if($redStatusCount>=(count($sensorDataList)/2))
                                                {
                                                $getBooking = $this->db->select('*')->from('ci_booking')->where('slot_id',$getSlotData[0]['slot_no'])
                                                ->group_start()->where('booking_status','0')->or_where('booking_status','3')->group_end()
                                                ->where('booking_from_date<=',$current_date)
                                                ->where('booking_to_date>=',$current_date)
                                                // ->where('reserve_from_time<=',$current_time)
                                                // ->where('reserve_to_time>=',$current_time)
                                                ->where('is_deleted',"0")
                                                ->get()->result_array();
                                                
                                                if(count($getBooking)>0)
                                                {
                                                    // $currentdatetime_d =date("Y-m-d H:i:s", $getBooking[0]);
                                                    if($getBooking[0]['booking_from_date']== $getBooking[0]['booking_to_date']){
                                                        if($getBooking[0]['reserve_from_time']<=$current_time && $getBooking[0]['reserve_to_time']>=$current_time ){
                                                        
                                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                            'slot_no'=>$getSlotData[0]['slot_no'],
                                                            'display_id'=>$getSlotData[0]['display_id'],
                                                            // ,
                                                            'status'=>0,
                                                            'color'=>'Yellow',
                                                            'slotIssueData'=>$issueData,
                                                            'isIssueOn'=>$isIssueOn,
                                                            'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' is booked'
                                                            ));
                                                            
                                                        }
                                                        else{
                                                           array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                              'color'=>'Red',
                                                              'slotIssueData'=>$issueData,
                                                              'isIssueOn'=>$isIssueOn,
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                        }
                                                    
                                                }else{
                                                    $currendate_fulld=date("Y-m-d H:i:s");
                                                    $currentdate=date('Y-m-d');
                                                        
                                                         if($getBooking[0]['booking_type']=='0') //daily
                                                    {
                                                        $startdate_fulld =date("Y-m-d H:i:s",strtotime($getBooking[0]['booking_from_date'].' '.$getBooking[0]['reserve_from_time']));
                                                    $enddate_fulld=date("Y-m-d H:i:s", strtotime($getBooking[0]['booking_to_date'].' '.$getBooking[0]['reserve_to_time']));
                                                     if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld )
                                                     {
                                                        
                                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                            'slot_no'=>$getSlotData[0]['slot_no'],
                                                            'display_id'=>$getSlotData[0]['display_id'],
                                                            'placename' => $getSlotData[0]['placename'],
                                                            // ,
                                                            'status'=>0,
                                                            'color'=>'Yellow',
                                                            'slotIssueData'=>$issueData,
                                                            'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' is booked'
                                                            ));
                                                            }
                                                    else{
                                                           array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'placename' => $getSlotData[0]['placename'],
                                                              'status'=>1,
                                                              'color'=>'Red',
                                                              'isIssueOn'=>$isIssueOn,
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                        }
                                                        
                                                    }else{
                                                       $startdate_fulld =date("Y-m-d H:i:s",strtotime($currentdate.' '.$getBooking[0]['reserve_from_time']));
                                                    $enddate_fulld=date("Y-m-d H:i:s", strtotime($currentdate.' '.$getBooking[0]['reserve_to_time']));
                                                     if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld )
                                                     {
                                                        
                                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                            'slot_no'=>$getSlotData[0]['slot_no'],
                                                            'display_id'=>$getSlotData[0]['display_id'],
                                                            'placename' => $getSlotData[0]['placename'],
                                                            'status'=>0,
                                                            'color'=>'Yellow',
                                                            'isIssueOn'=>$isIssueOn,
                                                            'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' is booked'
                                                            ));
                                                            }
                                                    else{
                                                           array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'placename' => $getSlotData[0]['placename'],
                                                              'status'=>1,
                                                              'color'=>'Red',
                                                              'isIssueOn'=>$isIssueOn,
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                        }
                                                    }
                                                }
                                                    
                                                }
                                                else
                                                {
                                                    
                                                       
                                                             array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                                'color'=>'Red',
                                                                'isIssueOn'=>$isIssueOn,
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                            
                                                         
                                                
                                                }
                                                
                                                }
                                            else{
                                                array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                 'slot_no'=>$slot['slot_no'],
                                                    'display_id'=>$slot['display_id'],
                                                    'status'=>2,
                                                    'color'=>'Green',
                                                    'isIssueOn'=>$isIssueOn,
                                                'msg'=>'Nothing is present over sensor. '.$sensorregisteredList[0]['device_id']));
                                            }
                                        }
                                        else{
                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                             'slot_no'=>$slot['slot_no'],
                                            'display_id'=>$slot['display_id'],
                                            'status'=>3,
                                            'color'=>'Grey',
                                            'isIssueOn'=>$isIssueOn,
                                            'msg'=>'Sensor id is '.$sensorregisteredList[0]['device_id'].' not connected with any slot.'));
                                        }
                               
                            
                                     
                                            
                                    
                                }
                            else
                            {
                               array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                'slot_no'=>$slot['slot_no'],
                                'display_id'=>$slot['display_id'],
                                'status'=>4,
                                'color'=>'Orange',
                                'isIssueOn'=>$isIssueOn,
                                'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' not responding.'));
                            }
    	                }
    	                else{
    	                    array_push($listSensor,array('deviceid'=>'0',
                                'slot_no'=>$slot['slot_no'],
                                'display_id'=>$slot['display_id'],
                                'status'=>3,
                                'color'=>'Grey',
                                'isIssueOn'=>$isIssueOn,
                                'msg'=>'slot no. '.$slot['slot_no'].' not connected to anyone sensor.'));
    	                }
    	                }
    	                else{
    	                    
        	                    array_push($listSensor,array('deviceid'=>'0',
                                    'slot_no'=>$slot['slot_no'],
                                    'display_id'=>$slot['display_id'],
                                    'status'=>5,
                                    'color'=>'black',
                                    'isIssueOn'=>$isIssueOn,
                                    'msg'=>'slot no. '.$slot['slot_no'].' is inaccessible.'));
        	                }
    	                
    	                
    	            }
    	            
    	           return $listSensor;
	            }else{
	                return array('status'=>false,'message'=>'No data available','slotdetails'=>$listSensor);
	            }
	       
    }
    
    
    
    
    
    public function sensorSlotstatus_placewise()
    {
         date_default_timezone_set('Asia/Kolkata');
         
         $this->form_validation->set_rules('place_id','Place Id','required');
         
              if($this->form_validation->run()==false)
	        {
	            $errorMsg = $this->form_validation->error_array();
	             $msg = array('status' => false, 'message' => $this->_returnSingle($errorMsg));
            echo json_encode($msg);
	        }
	        else
	        {
	           // $verifierId = $this->security->xss_clean($this->input->post('verifierId'));
	            $place_id = $this->security->xss_clean($this->input->post('place_id'));
	            $slotList = $this->db->select('*')->from('ci_parking_slot_info')
	            ->where('ci_parking_slot_info.place_id',$place_id)
	            ->where('ci_parking_slot_info.status','0')
	            ->where('ci_parking_slot_info.is_deleted','0')
	            ->order_by('slot_no asc')
	           // ->where('tbl_sensor_list.is_deleted','0')
	           // ->where('tbl_sensor_list.test_status','1')
	            ->get()->result_array();
	            $current_time=date("H:i:s");
	            $current_date=date("Y-m-d");
                //   print($current_time);
                $currentdatetime=strtotime(date("Y-m-d H:i:s"));
                $enddate = strtotime("-3 min", $currentdatetime);
                $currentdatetime_d =date("Y-m-d H:i:s", $currentdatetime);
                $enddate_d=date("Y-m-d H:i:s", $enddate);
                $listSensor=[];
                // print_r($slotList);
	            if(count($slotList)>0){
    	            foreach($slotList as $slot){
    	                /*id 	
    	                place_id	
    	                slot_id	
    	                is_verified 0:Not Verifierd 1: Veriferd 	
                        verifier_id	
                        engineer_id	
                        complaint_status  0:Pending 1:Processing 2:Solved 	
                        complaint_text	
                        verifier_remark	
                        engineer_remark	
                        img_attachments	
                        complaint_source 0:Replacement 1: Verifier App 	
                        issue_raised_on	
                        issue_resolved_on */
                        $isIssueOn = false;
                        $issueData =array('id'=>'',
    	                'place_id'=>'',
    	                'slot_id'=>'',
    	                'is_verified'=>'',//0:Not Verifierd 1: Veriferd 	
                        'verifier_id'=>'',
                        'engineer_id'=>'',
                        'complaint_status'=>'',//0:Pending 1:Processing 2:Solved 	
                        'complaint_text'=>'',	
                        'verifier_remark'=>'',
                        'engineer_remark'=>'',
                        'img_attachments'=>'',
                        'complaint_source'=>'', //0:Replacement 1: Verifier App 	
                        'issue_raised_on'=>'',
                        'issue_resolved_on'=>'');
    	                $getSlotRaisedIssueData = $this->db->select('*')->from('ci_slots_complaints')
    	               ->where('slot_id',$slot['slot_no'])->where('complaint_status!=','2')->get()->result_array();
    	               if(count($getSlotRaisedIssueData)>0){
    	                   $isIssueOn=true;
    	                   $issueData =array('id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['id']),
    	                'place_id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['place_id']),
    	                'slot_id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['slot_id']),
    	                'is_verified'=>$this->checknullValidation($getSlotRaisedIssueData[0]['is_verified']),//0:Not Verifierd 1: Veriferd 	
                        'verifier_id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['verifier_id']),
                        'engineer_id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['engineer_id']),
                        'complaint_status'=>$this->checknullValidation($getSlotRaisedIssueData[0]['complaint_status']),//0:Pending 1:Processing 2:Solved 	
                        'complaint_text'=>$this->checknullValidation($getSlotRaisedIssueData[0]['complaint_text']),	
                        'verifier_remark'=>$this->checknullValidation($getSlotRaisedIssueData[0]['verifier_remark']),
                        'engineer_remark'=>$this->checknullValidation($getSlotRaisedIssueData[0]['engineer_remark']),
                        'img_attachments'=>$this->checknullValidation($getSlotRaisedIssueData[0]['img_attachments']),
                        'complaint_source'=>$this->checknullValidation($getSlotRaisedIssueData[0]['complaint_source']), //0:Replacement 1: Verifier App 	
                        'issue_raised_on'=>$this->checknullValidation($getSlotRaisedIssueData[0]['issue_raised_on']),
                        'issue_resolved_on'=>$getSlotRaisedIssueData[0]['complaint_status']==2?$this->checknullValidation($getSlotRaisedIssueData[0]['issue_resolved_on']):'');
    	               }
    	                if($slot['isBlocked']=='1'){
    	                 $sensorregisteredList = $this->db->select('*')->from('tbl_sensor_list')->where('id',$slot['machine_id'])->where('is_deleted','0')->where('test_status','1')->get()->result_array();
    	                if(count($sensorregisteredList)>0){
    	                 $sensorDataList = $this->db->select('*')->from('mpc_sensor')->where('device_id',$sensorregisteredList[0]['device_id'])
                        ->where('sensor_time<=',$currentdatetime_d)
                        ->where('sensor_time>=',$enddate_d)
                        ->order_by('id' , 'desc')->get()->result_array();
                        
                        // print_r($sensorDataList);
                            if(count($sensorDataList)>0)
                            {
                                $redStatusCount =0;
                                foreach($sensorDataList as $details)
                                {
                                    if($details['status']=='1'){
                                        $redStatusCount=$redStatusCount+1;
                                    }
                                }
                                
                               
                                    $getSlotData = 
                                        $this->db->select('slot_no,display_id,place_id')->from('ci_parking_slot_info')
                                        ->where('machine_id', $sensorregisteredList[0]['id'])->where('status', '0')->where('onOff_applied','0')->where('is_deleted', '0')
                                        ->get()->result_Array();
                                            if(count($getSlotData)>0)
                                            {
                                                if($redStatusCount>=(count($sensorDataList)/2))
                                                {
                                                $getBooking = $this->db->select('*')->from('ci_booking')->where('slot_id',$getSlotData[0]['slot_no'])
                                                ->group_start()->where('booking_status','0')->or_where('booking_status','3')->group_end()
                                                ->where('booking_from_date<=',$current_date)
                                                ->where('booking_to_date>=',$current_date)
                                                // ->where('reserve_from_time<=',$current_time)
                                                // ->where('reserve_to_time>=',$current_time)
                                                ->where('is_deleted',"0")
                                                ->get()->result_array();
                                                
                                                if(count($getBooking)>0)
                                                {
                                                    // $currentdatetime_d =date("Y-m-d H:i:s", $getBooking[0]);
                                                    if($getBooking[0]['booking_from_date']== $getBooking[0]['booking_to_date']){
                                                        if($getBooking[0]['reserve_from_time']<=$current_time && $getBooking[0]['reserve_to_time']>=$current_time ){
                                                        
                                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                            'slot_no'=>$getSlotData[0]['slot_no'],
                                                            'display_id'=>$getSlotData[0]['display_id'],
                                                            // ,
                                                            'status'=>0,
                                                            'color'=>'Yellow',
                                                            'slotIssueData'=>$issueData,
                                                            'isIssueOn'=>$isIssueOn,
                                                            'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' is booked'
                                                            ));
                                                            
                                                        }
                                                        else{
                                                           array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                              'color'=>'Red',
                                                              'slotIssueData'=>$issueData,
                                                              'isIssueOn'=>$isIssueOn,
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                        }
                                                    
                                                }else{
                                                    $currendate_fulld=date("Y-m-d H:i:s");
                                                    $currentdate=date('Y-m-d');
                                                        
                                                         if($getBooking[0]['booking_type']=='0') //daily
                                                    {
                                                        $startdate_fulld =date("Y-m-d H:i:s",strtotime($getBooking[0]['booking_from_date'].' '.$getBooking[0]['reserve_from_time']));
                                                    $enddate_fulld=date("Y-m-d H:i:s", strtotime($getBooking[0]['booking_to_date'].' '.$getBooking[0]['reserve_to_time']));
                                                     if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld )
                                                     {
                                                        
                                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                            'slot_no'=>$getSlotData[0]['slot_no'],
                                                            'display_id'=>$getSlotData[0]['display_id'],
                                                            // ,
                                                            'status'=>0,
                                                            'color'=>'Yellow',
                                                            'slotIssueData'=>$issueData,
                                                            'isIssueOn'=>$isIssueOn,
                                                            'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' is booked'
                                                            ));
                                                            }
                                                    else{
                                                           array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                              'color'=>'Red',
                                                              'slotIssueData'=>$issueData,
                                                              'isIssueOn'=>$isIssueOn,
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                        }
                                                        
                                                    }else{
                                                       $startdate_fulld =date("Y-m-d H:i:s",strtotime($currentdate.' '.$getBooking[0]['reserve_from_time']));
                                                    $enddate_fulld=date("Y-m-d H:i:s", strtotime($currentdate.' '.$getBooking[0]['reserve_to_time']));
                                                     if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld )
                                                     {
                                                        
                                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                            'slot_no'=>$getSlotData[0]['slot_no'],
                                                            'display_id'=>$getSlotData[0]['display_id'],
                                                            // ,
                                                            'status'=>0,
                                                            'color'=>'Yellow',
                                                            'slotIssueData'=>$issueData,
                                                            'isIssueOn'=>$isIssueOn,
                                                            'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' is booked'
                                                            ));
                                                            }
                                                    else{
                                                           array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                              'color'=>'Red',
                                                              'slotIssueData'=>$issueData,
                                                              'isIssueOn'=>$isIssueOn,
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                        }
                                                    }
                                                }
                                                    
                                                }
                                                else
                                                {
                                                    
                                                       
                                                             array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                                'color'=>'Red',
                                                                'slotIssueData'=>$issueData,
                                                                'isIssueOn'=>$isIssueOn,
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                            
                                                         
                                                
                                                }
                                                
                                                }
                                            else{
                                                array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                 'slot_no'=>$slot['slot_no'],
                                                    'display_id'=>$slot['display_id'],
                                                    'status'=>2,
                                                    'color'=>'Green',
                                                    'slotIssueData'=>$issueData,
                                                    'isIssueOn'=>$isIssueOn,
                                                'msg'=>'Nothing is present over sensor. '.$sensorregisteredList[0]['device_id']));
                                            }
                                        }
                                        else{
                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                             'slot_no'=>$slot['slot_no'],
                                            'display_id'=>$slot['display_id'],
                                            'status'=>3,
                                            'color'=>'Grey',
                                            'slotIssueData'=>$issueData,
                                            'isIssueOn'=>$isIssueOn,
                                            'msg'=>'Sensor id is '.$sensorregisteredList[0]['device_id'].' not connected with any slot.'));
                                        }
                               
                            
                                     
                                            
                                    
                                }
                            else
                            {
                               array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                'slot_no'=>$slot['slot_no'],
                                'display_id'=>$slot['display_id'],
                                'status'=>4,
                                'color'=>'Orange',
                                'slotIssueData'=>$issueData,
                                'isIssueOn'=>$isIssueOn,
                                'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' not responding.'));
                            }
    	                }
    	                else{
    	                    array_push($listSensor,array('deviceid'=>'0',
                                'slot_no'=>$slot['slot_no'],
                                'display_id'=>$slot['display_id'],
                                'status'=>3,
                                'color'=>'Grey',
                                'slotIssueData'=>$issueData,
                                'isIssueOn'=>$isIssueOn,
                                'msg'=>'slot no. '.$slot['slot_no'].' not connected to anyone sensor.'));
    	                }
    	                }
    	                else{
    	                    
        	                    array_push($listSensor,array('deviceid'=>'0',
                                    'slot_no'=>$slot['slot_no'],
                                    'display_id'=>$slot['display_id'],
                                    'status'=>5,
                                    'color'=>'black',
                                    'slotIssueData'=>$issueData,
                                    'isIssueOn'=>$isIssueOn,
                                    'msg'=>'slot no. '.$slot['slot_no'].' is inaccessible.'));
        	                }
    	                
    	                
    	            }
    	            
    	            echo json_encode(array('status'=>true,'message'=>'list of data','slotdetails'=>$listSensor));
	            }else{
	                echo json_encode(array('status'=>false,'message'=>'No data available','slotdetails'=>$listSensor));
	            }
	            
	        }
    }
    
    public function slot_raise_issue()
    { 
        date_default_timezone_set('Asia/Kolkata');
         $this->form_validation->set_rules('place_id','Place Id','required');
         $this->form_validation->set_rules('slot_id','Place Id','required');
         $this->form_validation->set_rules('verifier_id','Place Id','required');
         $this->form_validation->set_rules('complaint_text','Place Id','required');
         $this->form_validation->set_rules('remark','Place Id','required');
         $this->form_validation->set_rules('issueimg','Place Id','required');
         
              if($this->form_validation->run()==false)
	        {
	            $errorMsg = $this->form_validation->error_array();
	             $msg = array('status' => false, 'message' => $this->_returnSingle($errorMsg));
            echo json_encode($msg);
	        }
	        else
	        {
	            $place_id = $this->security->xss_clean($this->input->post('place_id'));
	            $slot_id = $this->security->xss_clean($this->input->post('slot_id'));
	            $verifier_id = $this->security->xss_clean($this->input->post('verifier_id'));
	            $complaint_text = $this->security->xss_clean($this->input->post('complaint_text'));
	            $remark = $this->security->xss_clean($this->input->post('remark'));
	            $issueimg = $this->security->xss_clean($this->input->post('issueimg'));
	            //
	            $getSlotComplaint = $this->db->select('*')->from('ci_slots_complaints')->where('place_id','$place_id')->where('complaint_status!=',2)->get()->result_array();
	            if(count($getSlotComplaint)<=0){
	                 
                                $image = base64_decode($issueimg);
                                
                                $imagename = md5(uniqid(rand(), true));
                                $filename = $imagename .'.' .'png';
                                $path = base_url()."uploads/slot_complaints/".$filename;
                                $pathtosave ="./uploads/slot_complaints/".$filename;
                                file_put_contents($pathtosave, $image);
                                $slotdata = array(
                                'place_id' => $place_id,
                                'slot_id' => $slot_id,
                                'verifier_id' => $verifier_id,//firstname
                                'complaint_text' => $complaint_text,
                                'verifier_remark' => $remark,
                                'img_attachments' => $path,
                                'complaint_source' => '1',
                                'issue_raised_on'=>date('Y-m-d H:i:s')
                            );
                            
                            $insert = $this->db->insert('ci_slots_complaints',$slotdata);
                            if($insert){
                             $msg=array('status'=>true,
	                'message'=>'Successfully registered complaint.');
	                echo json_encode($msg);
                            }else{
                                $msg=array('status'=>false,
	                'message'=>'Failed registered complaint.');
	                echo json_encode($msg);
                            }
                            /*	 	place_id	slot_id		
verifier_id		  	complaint_text	verifier_remark	engineer_remark	img_attachments	
complaint_source 0:Replacement 1: Verifier App 	issue_raised_on	issue_resolved_on	is_deleted 0=active,1= delete */
                            
	                
	            }else{
	                $msg=array('status'=>false,
	                'message'=>'Already issue in process');
	                echo json_encode($msg);
	            }
	            
	        }
    }
    
     public function checkout_booking()
    {
        date_default_timezone_set('Asia/Kolkata');
         
         $this->form_validation->set_rules('booking_id','Booking Id','required');
         $this->form_validation->set_rules('verifier_id','Verifier Id','required');
         $this->form_validation->set_rules('checkout_Stat','Checkout Stat','required');
         $this->form_validation->set_rules('checkout_time','Checkout Time','required');
         
            if($this->form_validation->run()==false)
	        {
	            $errorMsg = $this->form_validation->error_array();
	             $msg = array('status' => false, 'message' => $this->_returnSingle($errorMsg));
                echo json_encode($msg);
	        }
	        else
	        {
	            $verifierId = $this->security->xss_clean($this->input->post('verifier_id'));
	            $booking_id = $this->security->xss_clean($this->input->post('booking_id'));
	            $checkout_Stat = $this->security->xss_clean($this->input->post('checkout_Stat'));
	            $checkout_time = $this->security->xss_clean($this->input->post('checkout_time'));
	           // print_r($checkout_time);
	           // print(' -- ');
	            
	            $checkout_time_d =date('Y-m-d H:i:s',strtotime($checkout_time));
	           // print_r($checkout_time_d);
	           // exit();
	            $isCheckedOut = false;
	            $alreadyCheckedout = $this->db->select('*')->from('ci_booking_check')->order_by('id desc')
	                                    ->where('booking_id',$booking_id)->where('created_at',date('Y-m-d'))->get()->result_array();
	           if(count($alreadyCheckedout)>0){
	               if($alreadyCheckedout[0]['check_out']=='0000-00-00 00:00:00'||$alreadyCheckedout[0]['check_out']==null){
	                   $insertcheckout= $this->db->where('booking_id ',$booking_id)
	            ->where('created_at ',date('Y-m-d'))
	            ->update('ci_booking_check',array('check_out'=>$checkout_Stat=='2'||$checkout_Stat=='0'?date('Y-m-d H:i:s'):$checkout_time_d,
	                   'updated_at'=>date("Y-m-d H:i:s"),'check_type'=>'1','checkout_stat'=>$checkout_Stat));
	           $checkout=  $this->db->where('id',$booking_id)->where('booking_status','0')->update('ci_booking',array(
	                   'booking_status'=>'1'));
	                   if($checkout){
	                       $msg = array('status' => true, 'message' => 'Successfully checkout');
                            echo json_encode($msg);
	                   }
	                   else{
	                      $msg = array('status' => false, 'message' =>'Failed to checkout');
                            echo json_encode($msg); 
	                   }
	               }else{
	                   $msg = array('status' => false, 'message' =>'Already checked out');
                            echo json_encode($msg); 
	               }
	           }else{
	                $msg = array('status' => false, 'message' =>'Booking is not verified.');
                            echo json_encode($msg); 
	           }
	                              
	            
	            
	        }
    }
    
    public function slot_affected_bookings()
    {
        $this->form_validation->set_rules('slotIssue_id','slotIssue_id','required');
        $this->form_validation->set_rules('slot_id','slot_id','required');
        //  $this->form_validation->set_rules('verifier_id','Place Id','required');
         
              if($this->form_validation->run()==false)
	        {
	            $errorMsg = $this->form_validation->error_array();
	             $msg = array('status' => false, 'message' => $this->_returnSingle($errorMsg));
                echo json_encode($msg);
	        }
	        else
	        {
	            $slotIssue_id = $this->security->xss_clean($this->input->post('slotIssue_id'));
	            $slot_id = $this->security->xss_clean($this->input->post('slot_id'));
	             
	            $getSlotIssueData = $this->db->select('*')->from('ci_slots_complaints')->where('id',$slotIssue_id)->where('complaint_status!=','2')
	            ->get()->result_array();
	           // print($slotIssue_id);
	           // print_r($getSlotIssueData);
	            if(count($getSlotIssueData)>0){
    	            $bookingList = $this->db->select('*')->from('ci_booking')->where('slot_id',$slot_id)
    	            ->where('booking_status','0')
    	            ->get()->result_array();
    	           // ->group_start()->where('booking_status','0')->or_where('booking_status','3')->group_end()
    	           // print_r($bookingList);
    	            $issue_raised_on =$getSlotIssueData[0]['issue_raised_on'];
    	           $noofHrs=$getSlotIssueData[0]['no_of_hrs'];
    	           // $reserve_from_time= date('H:i:s',strtotime($from_time . ' -10 minutes'));
                //             $reserve_to_time= date('H:i:s',strtotime($to_time . ' +0 minutes'));
                             $expected_issueEnd =  date('Y-m-d H:i:s',strtotime($issue_raised_on . ' +'.$noofHrs.' hours'));
                            //  print($issue_raised_on);
                            //  print('  --  ');
                            //  print($expected_issueEnd);
                             $from_date = date('Y-m-d',strtotime($issue_raised_on));
                             $from_time = date('H:i:s',strtotime($issue_raised_on));
                             $to_date = date('Y-m-d',strtotime($expected_issueEnd));
                             $to_time = date('H:i:s',strtotime($expected_issueEnd));
                          /*   print(' -- ');
                             print($from_date);
                             print(' -- ');
                             print($from_time);
                             print(' -- ');
                             print($to_date);
                             print(' -- ');
                             print($to_time);*/
                             
                             $listofAffectedBookings = $this->booking_detection($bookingList,$from_date,$to_date,$from_time,$to_time);
                             if(count($listofAffectedBookings)>0){
                                 $msg = array('status'=>true,'bookinglist'=>$listofAffectedBookings,'msg'=>'List of affected bookings.');
                                 echo json_encode($msg);
                             }else{
                                 $msg = array('status'=>false,'bookinglist'=>[],'msg'=>'No affected bookings.');
                                 echo json_encode($msg);
                             }
                            //  print_r($listofAffectedBookings);
    	          /*  foreach($bookingList as $v){
    	                if($booking->booking_type==0){//daily
    	                   // if(){}
    	                }
    	                else{
    	                    
    	                }
    	                //issue_raised_on
    	               // if(){}
    	            }*/
    	           // print('list of bookings');
	            }
	            else{
	               $msg = array('status'=>false,'bookinglist'=>[],'msg'=>'No such slot issue present.');
                    echo json_encode($msg);
	            }
	           // print_r($bookingList);
	           // $booking_id = $this->security->xss_clean($this->input->post('booking_id'));
	        }
    }
    
    public function booking_detection($data,$from_date,$to_date,$from_time,$to_time) //slot availibility // in progress mpc_sensor
    {
         date_default_timezone_set('Asia/Kolkata');
            $multiDate = false;
            //  $listof_Slots = $this->db->select('slot_no,display_id')->from('ci_parking_slot_info')
            //     ->where('place_id', $place_id)->where('status', '0')->where('onOff_applied','0')->where('is_deleted', '0')
            //     ->get()->result_Array();
            // $data = $this->db->select('*')->from('ci_booking')
            //     ->where('place_id', $place_id)->where('booking_status','0')->where('is_deleted',"0")
            //     ->get()->result();
            
            $listof_bookedSlots=[];
                foreach ($data  as $v) {
                    // print_r($v);
                    
                    $fromDate_u = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $from_time)); //7-1 2
                    $toDate_u = date('Y-m-d H:i:s', strtotime($to_date . ' ' . $to_time));
                    
                        if($v['booking_type'] =='0')
                        { //daily
                            $fromDate_s = date('Y-m-d H:i:s', strtotime($v['booking_from_date'] . ' ' . $v['reserve_from_time'])); //21 4
                            $toDate_s = date('Y-m-d H:i:s', strtotime($v['booking_to_date'] . ' ' . $v['reserve_to_time']));  //25 5
                           
                            if ($fromDate_u <= $fromDate_s && $toDate_u >= $fromDate_s || $fromDate_u <= $toDate_s && $toDate_u >= $toDate_s
                            ||$fromDate_u<=$fromDate_s&&$toDate_u>=$toDate_s||$fromDate_s<=$fromDate_u&&$toDate_s>=$toDate_u)
                            {
                                // print_r($v);
                                // $sensorStatus = $this->db->Select('status')->from('mpc_sensor')->where('slot_id',$v->slot_id)->get()->result();
                                $sensorStatus = $this->db->Select('status')->from('mpc_sensor')->where('slot_id',$v['slot_id'])->order_by("id", "DESC")->get()->result();
                               $bookedStatus = ''; //1==red,2==yellow
                               
                               $getVerifyStatus = $this->db->select('*')->from('ci_booking_verify')->where('booking_id',$v['id'])
                               ->where('booking_type','0')->where('verify_status','1')->get()->result();
                               $vrifyBooking='1';
                               if(count($getVerifyStatus)>0)
                               {
                                $vrifyBooking='0';    
                               }
                               $sensorStatusNew = count($sensorStatus)>0?$sensorStatus[0]->status:'0';
                                 if($vrifyBooking=='1'&&$sensorStatusNew=='1')
                                 {
                                     $bookedStatus = '1';
                                 }
                                 else
                                 {
                                     $bookedStatus = '2';
                                 }
                                array_push($listof_bookedSlots,$v);
                            }
                            
                        }
                        else
                        {
                            
                            // print(' monthly '.$v->slot_id);
                         $fromDate_u_d =date('Y-m-d', strtotime($from_date));//7-2
                            $toDate_u_d = date('Y-m-d', strtotime($to_date));//7-28
                            $fromDate_s_d=date('Y-m-d', strtotime($v['booking_from_date']));//7-1
                            $toDate_s_d=date('Y-m-d', strtotime($v['booking_to_date']));//8-1
                            
                            
                            if($fromDate_s_d <= $fromDate_u_d && $toDate_s_d>=$fromDate_u_d || $fromDate_s_d<=$toDate_u_d&&$toDate_s_d>=$toDate_u_d )
                            {
                                // print_r($v->slot_id);
                                if($multiDate=='true')
                                { // for multiple date
                                     $fromDate_u = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $from_time)); //7-1 2
                                     $toDate_u = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $to_time));
                                     $fromDate_s = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v['reserve_from_time']));
                                     $toDate_s = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v['reserve_to_time']));
                                 
                                    if ($fromDate_u <= $fromDate_s && $toDate_u >= $fromDate_s || $fromDate_u <= $toDate_s && $toDate_u >= $toDate_s
                                    ||$fromDate_u<=$fromDate_s&&$toDate_u>=$toDate_s||$fromDate_s<=$fromDate_u&&$toDate_s>=$toDate_u) 
                                        {
                                            // array_push($listof_bookedSlots,array('slotid'=>$v->slot_id,'bookingid'=>$v->id));
                                            //  $sensorStatus = $this->db->select('status')->from('mpc_sensor')->where('slot_id',$v->slot_id)->get()->result();
                                            $sensorStatus = $this->db->Select('status')->from('mpc_sensor')->where('slot_id',$v['slot_id'])->order_by("id", "DESC")->get()->result();
                                             $bookedStatus = ''; //1==red,2==yellow
                                            //  $getVerifyStatus = $this->db->select('*')->from('tbl_booking_verify')->where('booking_id',$v->id)
                                            //   ->where('booking_type','0')->where('verify_status','1')->get()->result();
                                            $getVerifyStatus = $this->db->select('*')->from('ci_booking_verify')->where('booking_id',$v['id'])
                                           ->where('booking_type','1')->where('verify_status','0')->order_by('id DESC')->get()->result();
                                               $vrifyBooking='1';
                                               if(count($getVerifyStatus)>0){
                                                   if(date('Y-m-d',strtotime($getVerifyStatus[0]->onCreated))>=date('Y-m-d')){
                                               $vrifyBooking='0';        
                                                   }else{
                                                       $vrifyBooking='1';
                                                   }
                                                   
                                               }
                                               $sensorStatusNew = count($sensorStatus)>0?$sensorStatus[0]->status:'0';
                                             if($vrifyBooking=='1'&&$sensorStatusNew=='1'){
                                                 $bookedStatus = '1';
                                             }else{
                                                 $bookedStatus = '2';
                                             }
                                            // array_push($listof_bookedSlots,array('slotid'=>$v['slot_id'],'bookingid'=>$v['id'],"bookedStatus"=>$bookedStatus));
                                            array_push($listof_bookedSlots,$v);
                                        //   print_r($v);
                                    }
                                //   print('multidate');  
                                }
                                else
                                { // for single date
                                
                                     
                                     $fromDate_s = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v['reserve_from_time']));
                                     $toDate_s =date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v['reserve_to_time']));
                                     if ($fromDate_u <= $fromDate_s && $toDate_u >= $fromDate_s || $fromDate_u <= $toDate_s && $toDate_u >= $toDate_s
                                     ||$fromDate_u<=$fromDate_s&&$toDate_u>=$toDate_s||$fromDate_s<=$fromDate_u&&$toDate_s>=$toDate_u) 
                                     {
                                            //  $sensorStatus = $this->db->select('status')->from('mpc_sensor')->where('slot_id',$v->slot_id)->get()->result();
                                       $sensorStatus = $this->db->Select('status')->from('mpc_sensor')->where('slot_id',$v['slot_id'])->order_by("id", "DESC")->get()->result();
                                       $bookedStatus = ''; //1==red,2==yellow
                                       $getVerifyStatus = $this->db->select('*')->from('ci_booking_verify')->where('booking_id',$v['id'])
                                       ->where('booking_type','1')->where('verify_status','0')->order_by('id DESC')->get()->result();
                                           $vrifyBooking='1';
                                           if(count($getVerifyStatus)>0){
                                               if(date('Y-m-d',strtotime($getVerifyStatus[0]->onCreated))>=date('Y-m-d')){
                                           $vrifyBooking='0';        
                                               }else{
                                                   $vrifyBooking='1';
                                               }
                                               
                                           }
                                           $sensorStatusNew = count($sensorStatus)>0?$sensorStatus[0]->status:'0';
                                         if($vrifyBooking=='1'&&$sensorStatusNew=='1'){
                                             $bookedStatus = '1';
                                         }else{
                                             $bookedStatus = '2';
                                         }
                                        // array_push($listof_bookedSlots,array('slotid'=>$v['slot_id'],'bookingid'=>$v['id'],"bookedStatus"=>$bookedStatus));
                                        array_push($listof_bookedSlots,$v);
                                    }
                                }
                                
                            }
                        }
                    
                    }
                    // print_r($listof_bookedSlots);
                    return $listof_bookedSlots;
                   
               
        
    }
    
     public function verifier_dashboard()
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->form_validation->set_rules('verifier_id', 'Verifier Id', 'required');
        
        $placeDetails = array('id'=>'',
                    'placename'=>'',
                    'placeaddress'=>'',
                    'noOfSlots'=>'',
                    'isactive'=>false,
                    'lat'=>'',
                    'long'=>'',
                    'isplaceconnected'=>false);
        $isverfierLogin=false;
        $verifierLoginData = array('login_time'=>'',
                                    'logout_time'=>'',
                                    'status'=>'',
                                    'created_at'=>'');
        $verifierData = array('admin_id'=>'',
                                    'admin_role_id'=>'',
                                    'username'=>'',
                                    'firstname'=>'',
                                    'lastname'=>'',
                                    'email'=>'',
                                    'mobile_no'=>'',
                                    'is_verify'=>'',
                                    'is_active'=>'');
        $placeDetailsData = array('id'=>'',
                                    'placename'=>'',
                                    'placeaddress'=>'',
                                    'noOfSlots'=>'',
                                    'isactive'=>false,
                                    'lat'=>'0.0',
                                    'long'=>'0.0',
                                    'isplaceconnected'=>false);
                    
        if ($this->form_validation->run()) 
        {
            $verifier_id = $this->security->xss_clean($this->input->post('verifier_id'));
            $place_id = $this->security->xss_clean($this->input->post('place_id'));
            $role_id = $this->security->xss_clean($this->input->post('role_id'));
            
            $adminPlacesList = $role_id=='11'?$this->db->select('id,placename')->from('ci_parking_places')->where('place_status','1')->get()->result_array():[];
            $isAdmin=$role_id=='11'?true:false;
            $cutomerCareData=$this->db->select('*')->from('ci_admin')->where('admin_role_id','9')->where('is_active','1')->where('is_verify','1')->get()->result_array();
            // print_r($cutomerCareData);
            $checkVerifier= $this->db->select('*')->from('ci_admin')
            ->where('admin_id',$verifier_id)
            // ->where('admin_role_id',$role_id)
            ->where('is_verify','1')->where('is_active','1')
            ->order_by('admin_id desc')->get()->result_array();
            if(count($checkVerifier)>0){
                 $verifierData = array('admin_id'=>(String)$checkVerifier[0]['admin_id'],
                                    'admin_role_id'=>(String)$checkVerifier[0]['admin_role_id'],
                                    'username'=>(String)$checkVerifier[0]['username'],
                                    'firstname'=>(String)$checkVerifier[0]['firstname'],
                                    'lastname'=>(String)$checkVerifier[0]['lastname'],
                                    'email'=>(String)$checkVerifier[0]['email'],
                                    'mobile_no'=>(String)$checkVerifier[0]['mobile_no'],
                                    'is_verify'=>(String)$checkVerifier[0]['is_verify'],
                                    'is_active'=>(String)$checkVerifier[0]['is_active']);
                                    
                $checkVerifierLogin = $this->db->select('*')->from('tbl_verifier_login')
                ->where('verifier_id',$verifier_id)->where('status','1')
                ->where('created_at',date('Y-m-d'))->where('is_deleted','1')->order_by('id desc')->get()->result_array();
                
                    // $verifierLoginData=$checkVerifierLogin[0];
                    // print_r($verifierLoginData);
                    // die;
                     $this->db->select('*')->from('tbl_verifier_place');
                    $this->db->where('verifier_id',$verifier_id)->where('duty_date',date('Y-m-d'));
                    // $role_id=='11'?$this->db->where('place_id',$place_id):'';
                    $getverifierplaceList =$this->db->where('isDeleted','0')->get()->result_array();
                    // print_r($getverifierplaceList);
                    $allBookingList=[];
                    $followUpBookingList=[];
                    $unVerifiedBookingList=[];
                    $dashboarditemList=[array('id'=>1,'itemName'=>'followUpBooking','title'=>'FollowUp Bookings'),
                    array('id'=>2,'itemName'=>'unVerifiedBooking','title'=>'Unverified Bookings')];
                    
                    if(count($getverifierplaceList)>0){
                        foreach($getverifierplaceList as $verifierPlace)
                        {
                             $bookingList = $this->db->select('*')->from('ci_booking')->where('place_id',$role_id=='3'?$verifierPlace['place_id']:$place_id)
                             ->where('booking_from_date <=', date('Y-m-d'))
                             ->where('booking_to_date >=', date('Y-m-d'))
                             ->where('booking_status','0')->order_by('id desc')->get()->result_array();
                        
                            foreach($bookingList as $b)
                            {
                                    // $displayid='';
                                    $b['display_id']='';
                                    $getSlotDeTails = $this->db->select('*')->from('ci_parking_slot_info')->where('slot_no',$b['slot_id'])
                                    ->where('is_deleted','0')->order_by('slot_no desc')
                                    ->get()->result_array();
                                    if(count($getSlotDeTails)>0)
                                    {
                                        $b['display_id']=$getSlotDeTails[0]['display_id'];
                                    }
                                    $followup_unverifierData =$this->followupBookings_unverifierBookings($b);
                                    if($followup_unverifierData['isBookingEnding'])
                                    {
                                        array_push($followUpBookingList,$followup_unverifierData['bookingData']);
                                    }
                                    
                                    // $unverifiedbookingdata = $this->unverifierBookings($b);
                                    if($followup_unverifierData['isbookingverfied']==false)
                                    {
                                        array_push($unVerifiedBookingList,$followup_unverifierData['bookingData']);
                                    }
                                
                                
                            }
                            
                        }
                        $newDashBoardList=[];
                        foreach($dashboarditemList as $dashItem)
                        {
                            if($dashItem['id']==1)
                            {
                                $dashItem['bookingsList']=$followUpBookingList;
                                array_push($newDashBoardList,$dashItem);
                            }
                            else if($dashItem['id']==2)
                            {
                                $dashItem['bookingsList']=$unVerifiedBookingList;
                                array_push($newDashBoardList,$dashItem);
                            }
                        }
                        
                        $placeDetails = $this->db->select('*')->from('ci_parking_places')
                        ->where('id',$role_id=='3'?$verifierPlace['place_id']:$place_id)->where('place_status','1')
                        ->where('is_deleted','0')->get()->result_array();
                        if(count($placeDetails)>0){
                            /*
                	id  			placename	place_address					no_of_slots		
                 	place_status 0: Inactive 1: active 	latitude	longitude*/
                                    $placeDetailsData = array('id'=>(String)$placeDetails[0]['id'],
                                    'placename'=>(String)$placeDetails[0]['placename'],
                                    'placeaddress'=>(String)$placeDetails[0]['place_address'],
                                    'noOfSlots'=>(String)$placeDetails[0]['no_of_slots'],
                                    'isactive'=>$placeDetails[0]['place_status']==0?false:true,
                                    'lat'=>(String)$placeDetails[0]['latitude'],
                                    'long'=>(String)$placeDetails[0]['longitude'],
                                    'isplaceconnected'=>true);
                            if(count($checkVerifierLogin)>0){
                                    $isverfierLogin=true;
                                    $verifierLoginData = array('login_time'=>(String)$checkVerifierLogin[0]['login_time'],
                                                    'logout_time'=>(String)$checkVerifierLogin[0]['logout_time'],
                                                    'status'=>(String)$checkVerifierLogin[0]['status'], //1=login,0=logout 
                                                    'created_at'=>(String)$checkVerifierLogin[0]['created_at']);
                                    
                                    $mesg = array('status' => true,
                                            'dashboardItems'=>$newDashBoardList,
                                            'placeDetails'=>$placeDetailsData,
                                            'islogin'=>$isverfierLogin,
                                            'verifierLoginData'=>$verifierLoginData,
                                            'verifierDetails'=>$verifierData,
                                            'customerCareList'=>$cutomerCareData,
                                            'adminPlaceList'=>$adminPlacesList,
                                            'isAdmin'=>$isAdmin,
                                            'message' => 'Dashboard data');
                                            echo json_encode($mesg);
                            }else{
                                     $mesg = array('status' => false,
                                                'dashboardItems'=>[],
                                                'placeDetails'=>$placeDetailsData,
                                                'islogin'=>$isverfierLogin,
                                                'verifierLoginData'=>$verifierLoginData,
                                                'verifierDetails'=>$verifierData,
                                                'customerCareList'=>$cutomerCareData,
                                                'adminPlaceList'=>$adminPlacesList,
                                                'isAdmin'=>$isAdmin,
                                                'message' => 'No login');
                                                echo json_encode($mesg);
                }
                        }else{
                            $mesg = array('status' => false,
                                    'dashboardItems'=>[],
                                    'placeDetails'=>$placeDetailsData,
                                    'islogin'=>$isverfierLogin,
                                    'verifierLoginData'=>$verifierLoginData,
                                    'verifierDetails'=>$verifierData,
                                    'customerCareList'=>$cutomerCareData,
                                    'adminPlaceList'=>$adminPlacesList,
                                    'isAdmin'=>$isAdmin,
                                    'message' => 'No such place present');
                                    echo json_encode($mesg);
                        }
                        
                        
                    }else{
                        $mesg = array('status' => false,
                                    'dashboardItems'=>[],
                                    'placeDetails'=>$placeDetailsData,
                                    'islogin'=>$isverfierLogin,
                                    'verifierLoginData'=>$verifierLoginData,
                                    'verifierDetails'=>$verifierData,
                                    'customerCareList'=>$cutomerCareData,
                                    'isAdmin'=>$isAdmin,
                                    'adminPlaceList'=>$adminPlacesList,
                                    'message' => 'Verifier not assigned to any place');
                                    echo json_encode($mesg);
                        
                    }
               
            }else{
                $mesg = array('status' => false,
                                'dashboardItems'=>[],
                                'placeDetails'=>$placeDetailsData,
                                'islogin'=>$isverfierLogin,
                                'verifierLoginData'=>$verifierLoginData,
                                'verifierDetails'=>$verifierData,
                                'customerCareList'=>$cutomerCareData,
                                'isAdmin'=>$isAdmin,
                                'adminPlaceList'=>$adminPlacesList,
                                'message' => 'Verifier not registered');
                                echo json_encode($mesg);
            }
                
            }
            
           
            
            // print_r($allBookingList);
            
            
        
        else
        {
            // $mesg = array('status' => false, 'message' => strip_tags(validation_errors()));
            // echo json_encode($mesg);
            $mesg = array('status' => false,
                            'dashboardItems'=>[],
                            'placeDetails'=>$placeDetails,
                            'islogin'=>true,
                            'verifierLoginData'=>$verifierLoginData,
                            'verifierDetails'=>$verifierData,
                            'customerCareList'=>[],
                            'isAdmin'=>false,
                            'adminPlaceList'=>[],
                            'message' => strip_tags(validation_errors()));
                            echo json_encode($mesg);
        }
    }
    
    public function followupBookings_unverifierBookings($booking)
    {
        $currentTime = new DateTime(date('Y-m-d H:i:s'));
        $isBookingEnding = false;
         $isbookingverfied=false;
        if($booking['booking_type']=='0')
        {//daily
        // print('r-  ');
            $bookingEndTime = new DateTime(date('Y-m-d H:i:s',strtotime($booking['booking_to_date'].' '.$booking['to_time'])));
            // $currentDatetime =  new DateTime(date("Y-m-d H:i:s"));
             $interval = $currentTime->diff($bookingEndTime);
            //  print_r($interval);
             $mincalculate =$interval->h>0?(($interval->h*60)+$interval->i):$interval->i; 
             if($mincalculate<=50){
                 $isBookingEnding=true;
                 
                 
             }
             $booking['remainingtime'] =$mincalculate;
             
             //unverified booking check code 
              $checkisBookingVerified = $this->db->select('*')->from('ci_booking_verify')
            ->where('booking_id',$booking['id'])->
            where('verify_status','1')
            ->get()->result_array();
            if(count($checkisBookingVerified)>0)
            {
                $isbookingverfied=true; 
            }else{
                // print('wyuerwuey');
                $isbookingverfied=false; 
            }
        }
        else
        {   //pass
            $currentdate = date('Y-m-d');
            $fromdate=date('Y-m-d',strtotime($booking['booking_from_date']));
            $todate=date('Y-m-d',strtotime($booking['booking_to_date']));
            if($fromdate<=$currentdate&&$todate>=$currentdate)
            {
                //followupbooking code start here
                $currentDatetime = new DateTime(date('Y-m-d H:i:s'));
                $todate_pass = new DateTime(date('Y-m-d H:i:s',strtotime($currentdate.' '.$booking['to_time'])));
                $interval = $currentDatetime->diff($todate_pass);
                $mincalculate =$interval->h>0?(($interval->h*60)+$interval->i):$interval->i; 
                if($mincalculate<=15){
                   $isBookingEnding=true; 
                   
                }
                $booking['remainingtime'] =$mincalculate;
                //followupbooking code end here
                
                //unverified booking start here 
                $currentdatetimestart = date('Y-m-d H:i:s',strtotime($currentdate.' '.'00:00:00'));
                $currentDateTime = date('Y-m-d H:i:s');
                $checkisBookingVerified = $this->db->select('*')->from('ci_booking_verify')
                                                ->where('booking_id',$booking['id'])->where('verify_status','1')
                                                ->group_start()->where('onCreated>=',$currentdatetimestart)
                                                ->or_where('onCreated<=',$currentDateTime)->group_end()
                                                ->get()->result_array();
               
                if(count($checkisBookingVerified)>0)
                {
                    $isbookingverfied=true; 
                }else
                {
                    $isbookingverfied=false; 
                }
                //unverified booking end here
            }
            
            
        }
        return  array('isBookingEnding'=>$isBookingEnding,'isbookingverfied'=>$isbookingverfied,'bookingData'=>$booking);
        
    }
    
    public function verifier_login_out() // date 11-05-2022
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->form_validation->set_rules('verifier_id', 'Verifier Id', 'required');
         $this->form_validation->set_rules('logintype', 'Type Id', 'required'); //1= Login and 0= logout
        
        if ($this->form_validation->run()) 
        {
            $verifier_id = $this->security->xss_clean($this->input->post('verifier_id'));
            $logintype = $this->security->xss_clean($this->input->post('logintype'));
            $currentdate = date('Y-m-d');
            // $currentdate = date('Y-m-d',strtotime('2022-04-21'));
            
            if($logintype==1)
            {  //login 
                $checklogin = $this->db->select('*')->from('tbl_verifier_login')->
                where('verifier_id',$verifier_id)->where('created_at',$currentdate)->where('is_deleted','1')->order_by('id desc')->get()->result_array();
                if(count($checklogin)>0){
                    if($checklogin[0]['status']=='1'){
                        $mesg = array('status'=>true,
                        'message'=>'Already loged In for the day.');
                         echo json_encode($mesg);
                    }else{
                        $mesg = array('status'=>false,
                        'message'=>'Already loged Out for the day.');
                         echo json_encode($mesg);
                    }
                }else{
                    
                    $insertData= array(
                        'verifier_id'=>$verifier_id,
                        'login_time'=>date('Y-m-d H:i:s'),
                        'status' =>'1',//1=login,0=logout 
                        'created_at'=>$currentdate);
                        
                    $insertinDB = $this->db->insert('tbl_verifier_login',$insertData);
                    if($insertinDB){
                         $mesg = array('status'=>true,
                        'message'=>'LogedIn for the day.');
                         echo json_encode($mesg);
                    }else{
                         $mesg = array('status'=>false,
                        'message'=>'Failed to LogedIn for the day.');
                         echo json_encode($mesg);
                    }
                    
                }
            }
            else if($logintype==0)
            { //logout
                $checklogin = $this->db->select('*')->from('tbl_verifier_login')->
                where('verifier_id',$verifier_id)->where('created_at',$currentdate)->order_by('id desc')->get()->result_array();
                if(count($checklogin)>0){
                    if($checklogin[0]['status']=='1'){
                        $insertData=array(
                                    'logout_time'=>date('Y-m-d H:i:s'),
                                    'status' =>'0',//1=login,0=logout 
                                    );
                        
                        $insertinDB = $this->db->where('verifier_id',$verifier_id)->where('created_at',$currentdate)->update('tbl_verifier_login',$insertData);
                        if($insertinDB){
                             $mesg = array('status'=>true,
                            'message'=>'LogedOut for the day.');
                             echo json_encode($mesg);
                        }else{
                             $mesg = array('status'=>false,
                            'message'=>'Failed to LogedOut for the day.');
                             echo json_encode($mesg);
                        }
                    }else{
                        $mesg = array('status'=>true,
                        'message'=>'Already loged Out for the day.');
                         echo json_encode($mesg);
                    }
                }else{
                     $mesg = array('status'=>false,
                        'message'=>'No LogIn find for today.');
                         echo json_encode($mesg);
                }
            }
            else 
            {
                $mesg = array('status'=>false,
                        'message'=>'Login type not found.');
                         echo json_encode($mesg);
            }
            
        }
        else
        {
            $mesg = array('status' => false, 'message' => strip_tags(validation_errors()));
                        echo json_encode($mesg);
        }
    }
    
    public function slotissuelist()
    {
         date_default_timezone_set('Asia/Kolkata');
        $this->form_validation->set_rules('verifier_id', 'Verifier Id', 'required');
        
        if ($this->form_validation->run()) 
        {
            $verifier_id = $this->security->xss_clean($this->input->post('verifier_id'));
            
            $getverifierplaces = $this->db->select('*')->from('tbl_verifier_place')->where('verifier_id',$verifier_id)->where('duty_date',date('Y-m-d'))->where('isDeleted','0')->get()->result_array();
            
            $slotIssueList = [];
            foreach($getverifierplaces as $places){
                $getissuelist= $this->db->select('*')->from('ci_slots_complaints')->where('place_id',$places['place_id'])->order_by('id desc')
                ->where('is_deleted','0')->get()->result_array();
                foreach($getissuelist as $issue){
                    array_push($slotIssueList,$issue);
                }
            }
            
            $mesg = array('status' => true,'slotIssuelist'=>$slotIssueList, 'message' => 'Slot complaints list.');
            echo json_encode($mesg);
            
        }
        else{
            $mesg = array('status' => false,'slotIssuelist'=>[],  'message' => strip_tags(validation_errors()));
            echo json_encode($mesg);
        }
    }
    
    public function booking_Details()
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->form_validation->set_rules('booking_id', 'Booking Id', 'required');
        
        
        if ($this->form_validation->run()) 
        {
            $booking_id = $this->security->xss_clean($this->input->post('booking_id'));
            
            $bookingList =  $this->db->select('BaseTbl.id as bookingId,BaseTbl.book_ext,booking_from_date,booking_to_date,from_time,to_time,booking_type,BaseTbl.booking_status,BaseTbl.place_id, car_det.car_number as carNo, BaseTbl.slot_id, BaseTbl.unique_booking_id, BaseTbl.replaced_booking_id, parking_slot_info.display_id, user.mobile_no as userNo');
            $this->db->from('ci_booking as BaseTbl');
            $this->db->join('ci_parking_places as parking_places', 'BaseTbl.place_id = parking_places.id');
            $this->db->join('ci_parking_slot_info as parking_slot_info', 'BaseTbl.slot_id = parking_slot_info.slot_no');
            $this->db->join('ci_users as user', 'BaseTbl.user_id = user.id');
            $this->db->join('ci_car_details as car_det', 'BaseTbl.car_id = car_det.id');
            $this->db->where('BaseTbl.id', $booking_id);
            // $this->db->where('BaseTbl.booking_from_date <=', date('Y-m-d'));
            // $this->db->where('BaseTbl.booking_to_date >=', date('Y-m-d'));
            // $this->db->where('BaseTbl.from_time <=', date('H:i:s'));
            // $this->db->where("(BaseTbl.booking_status='0' OR BaseTbl.booking_status='3')", NULL, FALSE);
            $this->db->order_by('BaseTbl.from_time desc');
            $this->db->where('BaseTbl.is_deleted', 0);
            $booking = $this->db->get()->result_array();
            // print_r($booking);
         $timewise_new=[];
         $getdesposition = $this->db->select('id,descriptions')->from('ci_despositions')->where('status','1')->get()->result();
          $getStatusVerifier = $this->db->select('id,subject')->from('master_verifier_issues')->where('type','1')->get()->result();
            if(count($booking)>0){
                
                  $currendate_fulld=date("Y-m-d H:i:s");
                // $currendate_fulld=date("2022-03-09 12:49:00");
                // $currendate_d=date("2022-03-09");
                $currendate_d=date("Y-m-d");
                $checkData = array('id'=>'' ,
                                'booking_id'=>'' ,
                                'check_in'=>'' ,
                                'check_out'=>'' ,
                                'verifier_id'=>'' ,
                                'check_type'=>'0',
                                'created_at'=>'');
                if($booking[0]['booking_type']=='0'){  // daily
                
                $getCheckDetails = $this->db->select('*')->from('ci_booking_check')->where('booking_id',$booking[0]['bookingId'])
                    ->where('is_deleted','0')->get()->result_array();
                    
                    if(count($getCheckDetails)>0){
                        $checkData = array('id'=>$getCheckDetails[0]['id'],
                                'booking_id'=>$getCheckDetails[0]['booking_id'] ,
                                'check_in'=>$getCheckDetails[0]['check_in'] ,
                                'check_out'=>$getCheckDetails[0]['check_out'] ,
                                'verifier_id'=>$getCheckDetails[0]['verifier_id'] ,
                                'check_type'=>$getCheckDetails[0]['check_type'],
                                'created_at'=>$getCheckDetails[0]['created_at']);
                    }
                    
                    $fromDate_s = date('Y-m-d H:i:s', strtotime($booking[0]['booking_from_date'] . ' ' . $booking[0]['from_time']));
                    $toDate_s =date('Y-m-d H:i:s', strtotime($booking[0]['booking_to_date'] . ' ' . $booking[0]['to_time']));
                    
                    $fromdatetime_d=new DateTime($fromDate_s);
                    $toDatetime_d=new DateTime($toDate_s);
                    $interval = $fromdatetime_d->diff($toDatetime_d);
                    $min = $interval->i>0?($interval->i.' min'):'';
                    $booking[0]['no_of_hrs']=$interval->h.' hr '.$min;
                    
                     $checkExtention = $this
                        ->db
                        ->select('*')
                        ->from('ci_booking')
                        ->where('replaced_booking_id', $booking[0]['bookingId'])->where('is_deleted', '0')
                        ->get()
                        ->result();
                    $extendedStatus = count($checkExtention) > 0 ? true : false; 
                    $booking[0]['extendedStatus']=$extendedStatus;
                    $booking[0]['from_time']=date('h:i a',strtotime($fromDate_s));
                    $booking[0]['to_time']=date('h:i a',strtotime($toDate_s));
                    // if($fromDate_s<=$currendate_fulld&&$toDate_s>=$currendate_fulld)
                    // {
                    //     $data=$this->verifier_bookingDetails_logic($booking[0]);
                    //     $data['iscompletedTime']=false;
                    //     $data['checkData']=$checkData;
                       
                    //     array_push($timewise_new,$data);
                    // }
                    // else
                    if($fromDate_s<$currendate_fulld&&$toDate_s<$currendate_fulld)
                    {
                         $data=$this->verifier_bookingDetails_logic($booking[0]);
                        $data['iscompletedTime']=true;
                        $data['checkData']=$checkData;
                        //  $data['checkType']=$checkData['check_type'];
                        array_push($timewise_new,$data);
                    }else{
                        $data=$this->verifier_bookingDetails_logic($booking[0]);
                        $data['iscompletedTime']=false;
                        $data['checkData']=$checkData;
                       
                        array_push($timewise_new,$data);
                    }
                    /*else{
                        $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $data['iscompletedTime']=true;
                        $data['checkData']=$checkData;
                        //  $data['checkType']=$checkData['check_type'];
                        array_push($timewise_new,$data);
                        
                    }*/
                    
                    
                    
                }
                else
                {     //passes
                // print('pass');
                $getCheckDetails = $this->db->select('*')->from('ci_booking_check')->where('booking_id',$booking[0]['bookingId'])
                ->where('created_at',date('Y-m-d'))
                    ->where('is_deleted','0')->get()->result_array();
                    
                    if(count($getCheckDetails)>0){
                        $checkData = array('id'=>$getCheckDetails[0]['id'],
                                'booking_id'=>$getCheckDetails[0]['booking_id'] ,
                                'check_in'=>$getCheckDetails[0]['check_in'] ,
                                'check_out'=>$getCheckDetails[0]['check_out'] ,
                                'verifier_id'=>$getCheckDetails[0]['verifier_id'] ,
                                'check_type'=>$getCheckDetails[0]['check_type'],
                                'created_at'=>$getCheckDetails[0]['created_at']);
                    }
                    $fromDate_s = date('Y-m-d H:i:s', strtotime($currendate_d .' '. $booking[0]['from_time']));
                    $toDate_s =date('Y-m-d H:i:s', strtotime($currendate_d .' '. $booking[0]['to_time']));
                    $fromdatetime_d=new DateTime($fromDate_s);
                    $toDatetime_d=new DateTime($toDate_s);
                    $interval = $fromdatetime_d->diff($toDatetime_d);
                    $min = $interval->i>0?($interval->i.' min'):'';
                    $booking[0]['no_of_hrs']=$interval->h.' hr '.$min;
                        
                    $checkExtention = $this
                        ->db
                        ->select('*')
                        ->from('ci_booking')
                        ->where('replaced_booking_id', $booking[0]['bookingId'])->where('booking_from_date',date('Y-m-d'))->where('booking_to_date',date('Y-m-d'))->where('is_deleted', '0')
                        ->get()
                        ->result();
                    $extendedStatus = count($checkExtention) > 0 ? true : false; 
                    $booking[0]['extendedStatus']=$extendedStatus;
                    $booking[0]['from_time']=date('h:i a',strtotime($fromDate_s));
                    $booking[0]['to_time']=date('h:i a',strtotime($toDate_s));
                    // if($fromDate_s<=$currendate_fulld&&$toDate_s>=$currendate_fulld)
                    // {
                    //     $data=$this->verifier_bookingDetails_logic($booking[0]);
                    //     $data['iscompletedTime']=false;
                    //     $data['checkData']=$checkData;
                    //     //  $data['checkType']=$checkData['check_type'];
                    //     array_push($timewise_new,$data);
                    // }
                    // else
                    if($fromDate_s<$currendate_fulld&&$toDate_s<$currendate_fulld)
                    {
                        $data=$this->verifier_bookingDetails_logic($booking[0]);
                        $data['iscompletedTime']=true;
                        $data['checkData']=$checkData;
                        //  $data['checkType']=$checkData['check_type'];
                        array_push($timewise_new,$data);
                    }else{
                        $data=$this->verifier_bookingDetails_logic($booking[0]);
                        $data['iscompletedTime']=false;
                        $data['checkData']=$checkData;
                        //  $data['checkType']=$checkData['check_type'];
                        array_push($timewise_new,$data);
                    }
                  /*  else{
                         $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $data['iscompletedTime']=true;
                        $data['checkData']=$checkData;
                        //  $data['checkType']=$checkData['check_type'];
                        array_push($timewise_new,$data);
                    }*/
                }
            }
            // print_r($timewise_new);
            if(count($timewise_new)>0){
             $msg = array('status' => true, 
                'message' => "Booking Details.", 
                'desposition'=>$getdesposition,
                'issueList'=>$getStatusVerifier,
                'bookings' => $timewise_new);
                echo json_encode($msg);
            }else{
                $msg = array('status' => false, 
                'message' => "No Booking Details.", 
                'desposition'=>[],
                'issueList'=>[],
                'bookings' => $timewise_new);
                echo json_encode($msg);
            }
            
        }
        else
        {
              $msg = array('status' => false, 
                'message' => "No Booking Details.", 
                'bookings' => []);
                echo json_encode($msg);
            
        }
    }
    
    public function verifier_bookingDetails_logic($d)
    {
        // print_r($d);
        $issue = $this->db->select('*')
                ->from('tbl_verifier_complaints')
                ->where('booking_id', $d['bookingId'])
                ->get()
                ->result_Array();
                 $d['booking_from_date'] = date('d-m-Y', strtotime($d['booking_from_date']));
                $d['booking_to_date'] =date('d-m-Y', strtotime($d['booking_to_date']));
                if(count($issue)>0)
                {
                    $d['complaint_text'] = $issue[0]['complaint_text'];
                    $d['status'] = $issue[0]['status'];
                    $d['actionPerformedByEnforcer'] = $issue[0]['actionPerformedByEnforcer'];
                    $d['resolvedDate'] = $issue[0]['resolvedDate'];
                    $d['remark'] = $issue[0]['remark'];
                    $d['customercareRemark'] = $issue[0]['customercareRemark'];
                }
                else
                {
                    $d['complaint_text'] = '';
                    $d['status'] = '';
                    $d['actionPerformedByEnforcer'] = '';
                    $d['resolvedDate'] = '';
                    $d['remark'] = '';
                    $d['customercareRemark'] = '';
                }
                
                $getEnforcerPlace = $this->db->select('*')->from('tbl_enforcer_place')->where('place_id',$d['place_id'])->get()->result();
                
	                       if(count($getEnforcerPlace)>0)
	                       {
    	                       $getEnforceDetails= $this->db->select('*')
    	                       ->from('ci_admin')
    	                       ->where('admin_id',$getEnforcerPlace[0]->enforcer_id)
    	                       ->where('admin_role_id','4')
    	                       ->get()
    	                       ->result();
    	                       
    	                       if(count($getEnforceDetails)>0)
    	                       {
    	                           $d['enforcerNo'] = $getEnforceDetails[0]->mobile_no;
    	                       }
    	                       else{
    	                           $d['enforcerNo'] ='';
    	                       }
    	                           
	                       }else{
	                           $d['enforcerNo'] ='';
	                       }
                
                    // if(in_array($timewise[$n]['bookingId'] , $verify_status)){
                        $currendate_d=date('Y-m-d');
                        $currentDatetime = date('Y-m-d H:i:s',strtotime($currendate_d.' '.'00:00:00'));
                        $CurrentToDateTime =date('Y-m-d H:i:s');
                        
                        $verify = $d['booking_type']==0?$this->db->select('booking_id, verify_status')
                        ->from('ci_booking_verify')
                        ->where('booking_id', $d['bookingId'])
                        ->like('onCreated', date('Y-m-d'))
                        ->where('isDeleted', 0)
                        ->get()->result_array():
                          $this->db->select('booking_id, verify_status')->from('ci_booking_verify')
                                                ->where('booking_id',$d['bookingId'])->where('verify_status','1')
                                                ->where('onCreated>=',$currentDatetime)
                                                ->where('onCreated<=',$CurrentToDateTime)
                                                ->get()->result_array();
                    
                        
                        
                        
                        
                        if(empty($verify)){
                            $d['verify_status'] = "2";
                        }
                        else {
                            $d['verify_status'] = $verify[0]['verify_status'];
                        }
                        
                    // }
                    // else {
                    //         $d['verify_status'] = "2";
                    // }
                    return $d;
            
    }
   
    
    public function booking_extention_Verifier()
    {

        // exit();
        $this
            ->form_validation
            ->set_rules('verifier_id', 'Verifier Id', 'required');
        $this
            ->form_validation
            ->set_rules('ext_hrs', 'Extention hrs', 'required');
        $this
            ->form_validation
            ->set_rules('bookingId', 'Booking Id', 'required');
       
        $this
            ->form_validation
            ->set_rules('uniqueBookingId', 'UniqueBookingId', 'required');
        if ($this
            ->form_validation
            ->run())
        {

            $verifier_id = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('verifier_id'));
            // $verifyToken = $this->tokenVerify($token);
            $ext_hrs = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('ext_hrs'));
            $bookingId = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('bookingId'));
            /*$appcost = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('cost'));*/
            $uniqueBookingId = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('uniqueBookingId'));
                // $cost='0';
            
              
                // exit();
               
                $getBookingDetails=$this->db->select('*')->from('ci_booking')->where('id',$bookingId)
                // ->where('booking_status','0')
                ->group_start()->where('booking_status','0')->or_where('booking_status','1')->group_end()
                ->get()->result_array();
                if(count($getBookingDetails)>0){
                //   $getBookingDetails[0]->verfier_id=$verifier_id;
                   $cost= $this->bookingExtent_priceCal($getBookingDetails[0]['place_id'],$ext_hrs);
                   $cost=$cost; // this is due to app round off
                   
                   $extention = $this->book_ext_Booking($bookingId, $ext_hrs, $cost, $uniqueBookingId,$verifier_id);
                    if ($extention == true)
                    {
                        $msg = array(
                            'status' => true,
                            'message' => 'Booking successfully extended',
                            'session' => '1',
                            'walletStatus' => '0'
                        );
                        echo json_encode($msg);
    
                    }
                    else
                    {
                        $msg = array(
                            'status' => false,
                            'message' => 'Some thing went wrong. kindly try after sometime.',
                            'session' => '1',
                            'walletStatus' => '0'
                        );
                        echo json_encode($msg);
                    }
                        
                       
                    
                }else{
                    $msg = array(
                        'status' => false,
                        'message' => 'Booking is not in process.',
                        'session' => '1',
                        'walletStatus' => '0'
                    );
                    echo json_encode($msg);
                }

              
        }
        else
        {
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors()) ,
                'session' => '1'
            );
            echo json_encode($msg);
        }

    }
    
    public function bookingExtent_priceCal($place_id,$noofhrs)
    {
      $cost='0';
    //   $noofhrs=10;
      $newCost=0;
    //   $place_id=11;
       $getPlaceDetails = $this->db->select('*')->from('ci_parking_places')
        ->where('id',$place_id)->where('place_status','1')->get()->result_array();
        if(count($getPlaceDetails)>0){
            $price_Slab = $this->priceslabData($getPlaceDetails[0]['id'], $getPlaceDetails[0]['ext_per'], $getPlaceDetails[0]['pricing_type']);
          
              if($getPlaceDetails[0]['pricing_type']=='1')// per hour
              {
                   $cost= $noofhrs* $price_Slab['perHour']['cost'];
                }
                else if($getPlaceDetails[0]['pricing_type']=='0')// price slab
                { 
                    // print_r($price_Slab['perDay']);
                    foreach($price_Slab['perDay'] as $priceDetails){
                        if($priceDetails['minhrs']<=$noofhrs&&$priceDetails['hrs']>=$noofhrs){
                           $cost=$priceDetails['cost'];
                        }
                    }
                }  
            $newCost = $cost+(($cost*$getPlaceDetails[0]['ext_per'])/100);
        }
        // print($cost.' -- ');
        // print($newCost);
        return round($newCost);
    }
    
    public function bookingExtent_priceCal_api()
    {
       $this
            ->form_validation
            ->set_rules('place_id', 'place_id', 'required');
        $this
            ->form_validation
            ->set_rules('noofhrs', 'noofhrs', 'required');
        if ($this
            ->form_validation
            ->run())
        {

            $place_id = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('place_id'));
            $noofhrs = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('noofhrs'));
    //   $place_id,$noofhrs
      $cost='0';
    //   $noofhrs=10;
      $newCost=0;
    //   $place_id=11;
       $getPlaceDetails = $this->db->select('*')->from('ci_parking_places')
        ->where('id',$place_id)->where('place_status','1')->get()->result_array();
        if(count($getPlaceDetails)>0){
            $price_Slab = $this->priceslabData($getPlaceDetails[0]['id'], $getPlaceDetails[0]['ext_per'], $getPlaceDetails[0]['pricing_type']);
          
              if($getPlaceDetails[0]['pricing_type']=='1')// per hour
              {
                   $cost= $noofhrs* $price_Slab['perHour']['cost'];
                }
                else if($getPlaceDetails[0]['pricing_type']=='0')// price slab
                { 
                    // print_r($price_Slab['perDay']);
                    foreach($price_Slab['perDay'] as $priceDetails){
                        if($priceDetails['minhrs']<=$noofhrs&&$priceDetails['hrs']>=$noofhrs){
                           $cost=$priceDetails['cost'];
                        }
                    }
                }  
            $newCost = $cost+(($cost*$getPlaceDetails[0]['ext_per'])/100);
        }
        // print($cost.' -- ');
        // print($newCost);
        $msg = array(
                'status' => true,
                'message' => 'Total coast for Extended Booking' ,
                'cost' => round($newCost)
            );
            echo json_encode($msg);
        // return round($newCost);
        }else{
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors()) ,
                'cost' => '0'
            );
            echo json_encode($msg);
        }
    }
    
    public function priceslabData($placeId,$ext_charges,$pricetype)
    {
        // print($placeId.' '.$ext_charges.' '.$pricetype.' ---   ');
                /* 0=perhour,1= perday,2=week,3=month,4=extend */
                $priceslab_categoryList=['0','1','2','3','4' ];
                $getperday=[];
                $getperWeek=[];
                $getperMonth=[];
                $getperExtended=[];
                $perhourCost=array('hrs'=>1,'cost'=>0);
                foreach($priceslab_categoryList as $categ){
                $getDetailPerPlace = $this->db->select('place_id,hrs,cost,pass')->from('ci_price_slab')
                                    ->where('place_id',$placeId)->where('pass',$categ)
                                    ->where('is_deleted','0')->order_by('hrs ASC')
                                    ->get()->result();
   
                $perhr_list=[];
                
                if(count($getDetailPerPlace)>0)
                {
                    if($getDetailPerPlace[0]->pass==0){
                            // $perhourCost=
                            $perhourCost=array('hrs'=>(int)$getDetailPerPlace[0]->hrs,'cost'=>(int)$getDetailPerPlace[0]->cost);
                           array_push($perhr_list,array('placeid'=>$placeId,'perhors'=>$perhourCost)); 
                        //   $perhourCost['hrs']=(int)$getDetailPerPlace[0]->hrs;
                        // //   print((int)$getDetailPerPlace[0]->cost.' rk- ');
                        //   $perhourCost['cost']=(int)$getDetailPerPlace[0]->cost;
                            if($pricetype=='1'){
                                $extcost =(int)$getDetailPerPlace[0]->cost+ ((int)$getDetailPerPlace[0]->cost/100)*$ext_charges;
                             array_push($getperExtended,array('hrs'=>(int)$getDetailPerPlace[0]->hrs,'cost'=>(int)$extcost));
                            }
                        }
                    $i=0;
                    foreach($getDetailPerPlace as $price){
                       
                        
                        if($price->pass==1){
                            $minhrs=0;
                            if($i<=0){
                                $minhrs=0;
                            }
                            else if($i>0){
                                if($price->hrs==1){
                                    $minhrs=0;
                                }
                                else{
                                $minhrs=(int)($getDetailPerPlace[$i-1]->hrs+1);}
                            }
                            array_push($getperday,array('minhrs'=>$minhrs,'hrs'=>(int)$price->hrs,'cost'=>(int)$price->cost));
                          if($pricetype=='0'){
                                $extcost =(int)$price->cost+ ((int)$price->cost/100)*$ext_charges;
                             array_push($getperExtended,array('minhrs'=>$minhrs,'hrs'=>(int)$price->hrs,'cost'=>(int)$extcost));
                            }
                        }
                        else if($price->pass==2){
                             $minhrs=0;
                            if($i<=0){
                                $minhrs=0;
                            }
                            else if($i>0){
                                 if($price->hrs==1){
                                    $minhrs=0;
                                }
                                else{
                                $minhrs=(int)($getDetailPerPlace[$i-1]->hrs+1);}
                            }
                            array_push($getperWeek,array('minhrs'=>$minhrs,'hrs'=>(int)$price->hrs,'cost'=>(int)$price->cost));
                        }
                        else if($price->pass==3){
                             $minhrs=0;
                            if($i<=0){
                                $minhrs=0;
                            }
                            else if($i>0){
                                 if($price->hrs==1)
                                 {
                                    $minhrs=0;
                                 }
                                 else
                                 {
                                    $minhrs=(int)($getDetailPerPlace[$i-1]->hrs+1);
                                 }
                            }
                            array_push($getperMonth,array('minhrs'=>$minhrs,'hrs'=>(int)$price->hrs,'cost'=>(int)$price->cost));
                        }
                    
                        // else if($price->pass==4){
                        //     array_push($getperExtended,array('hrs'=>(int)$price->hrs,'cost'=>(int)$price->cost));
                        // }
                        $i++;
                    }
                 }
                //  if($placeId==7){
                //     //  print_r($getDetailPerPlace);
                //      print_r($perhourCost);
                // //  print_r(array('perHour'=>$perhourCost,'perDay'=>$getperday,'perWeek'=>$getperWeek,'perMonth'=>$getperMonth,'extendPrice'=>$getperExtended));
                //  die;}
                            }
                           
                           /* if($perhourCost==''){
                                $perhourCost=array('hrs'=>1,'cost'=>0);
                            }*/
                            // unsleep()
                          
                return array('perHour'=>$perhourCost,'perDay'=>$getperday,'perWeek'=>$getperWeek,'perMonth'=>$getperMonth,'extendPrice'=>$getperExtended);
                // $data=array('status'=>true,'message'=>'List of price slab','pricesSlab'=>array('perDay'=>$getperday,'perWeek'=>$getperWeek,'perMonth'=>$getperMonth));
                // echo json_encode($data);
        }
        
    public function book_ext_Booking($booking_id,$extHrs,$cost,$unique_booking_id,$verifier_id)
    {
        
            date_default_timezone_set('Asia/Kolkata');
            
	   
	           $bookingId = $booking_id;
	           //$this->db->where('id',$bookingId)->update('ci_booking',array('verfier_id'=>$verifier_id));
	           $getbooking = $this->db->select('*')->from('ci_booking')
	           //->where('id',$bookingId)
	           ->where('unique_booking_id',$unique_booking_id)->order_by("id", "Desc")->get()->result();
	           if(count($getbooking)>0){
	               
	               /*unique_booking_id	user_id	place_id	slot_id	booking_status 	
replaced_booking_id	booking_from_date	booking_to_date	from_time	to_time	paid_status  
booking_type daily cost	reserve_from_time reserve time is 30 < 	reserve_to_time reserve time is 30 > 	vendor_id	car_id*/
                    $fromDate_d;$fromTime_d;$to_Date_d;$to_Time_d;
                    if($getbooking[sizeof($getbooking) - 1]->booking_type==0)
                    {
                    $fromDate_d=(String)$getbooking[0]->booking_to_date;
                    $fromTime_d=(String)$getbooking[0]->to_time;
                    $fromDateTime = date('Y-m-d H:i:s',strtotime($fromDate_d.' '.$fromTime_d));
                    // $addhrs = ' +1 '.' minutes';
                    // $newtimestamp = strtotime( $fromDateTime.$addhrs);
                    // $fromTime_d_new = date('Y-m-d H:i:s', $newtimestamp);
                    // $hstr='23:00:00';
                    // $fromDateTime = date('Y-m-d H:i:s',strtotime($fromDate_d.' '.$fromTime_d));
                    // print($fromDateTime);
                    $addhrs = ' + '.$extHrs.' hours';
                    $newtimestamp = strtotime( $fromDateTime.$addhrs);
                    $newTodatetime = date('Y-m-d H:i:s', $newtimestamp);
                    
                    $to_Date_d=date('Y-m-d', strtotime($newTodatetime));
                    $to_Time_d=date('H:i:s', strtotime($newTodatetime));
                        
                    }
                    else{
                        $fromDate_d=date('Y-m-d');
                    $fromTime_d=(String)$getbooking[0]->to_time;
                    $fromDateTime = date('Y-m-d H:i:s',strtotime($fromDate_d.' '.$fromTime_d));
                    $addhrs = ' + '.$extHrs.' hours';
                    $newtimestamp = strtotime( $fromDateTime.$addhrs);
                    $newTodatetime = date('Y-m-d H:i:s', $newtimestamp);
                    
                    $to_Date_d=date('Y-m-d', strtotime($newTodatetime));
                    $to_Time_d=date('H:i:s', strtotime($newTodatetime));
                    }
                    // print('  ///  '.$to_Date_d.' '.$to_Time_d);
                    // exit();
                    $bookinglist_check =  $this->bookinglist_check($getbooking[0]->place_id,$fromDate_d,$to_Date_d,$fromTime_d,$to_Time_d,$getbooking[0]->slot_id,$getbooking[0]->unique_booking_id);
	               // print_r($bookinglist_check);
	               // exit();
	                $datainsert=array(
	                   // 'unique_booking_id'=>$bookingid1,	
	                    'user_id'	=>$getbooking[0]->user_id,
	                    'place_id'	=>$getbooking[0]->place_id,
	                    'slot_id'	=>$getbooking[0]->slot_id,
	                    'unique_booking_id'=>$getbooking[0]->unique_booking_id,
	                    'booking_status'=>'0', 	
                        'replaced_booking_id'	=>$getbooking[0]->id,
                        'booking_from_date'	=>$fromDate_d,
                        'booking_to_date'	=>$to_Date_d,
                        'from_time'=>$fromTime_d,
                        'to_time'	=>$to_Time_d,
                        'paid_status'=>'0',
                        'booking_type'=>'0',
                        'cost'	=>$cost,
                        'reserve_from_time' =>$fromTime_d,	
                        'reserve_to_time' =>$to_Time_d,
                        'vendor_id'=>$getbooking[0]->vendor_id,
                        'verfier_id'=>$verifier_id,
                        'car_id'=>$getbooking[0]->car_id
	                    );
	                    $this->db->insert('ci_booking',$datainsert);
	                    
	                    $last_id = $this->db->insert_id();
	                   // $count = 8-strlen($last_id);
                    //     $bookingId =$last_id;
                    //     for($i=0;$i<$count;$i++){
                    //     $bookingId='0'.$bookingId;
                    //     }
                        // $bookingid1 = 'PAR'.$bookingId;
                        $rep_bookingId='';
                        $par_data=$this->db->select('*')->from('ci_booking')->where('unique_booking_id',$getbooking[0]->unique_booking_id)->like('book_ext','EXT')->order_by("id", "Desc")->get()->result();
                        if(count($par_data)>0){
                            
                            $expoit = explode("T",$par_data[0]->book_ext);
                            $count = $expoit[1]+1;
                            $rep_bookingId = 'EXT'.$count;
                        }else{
                            $rep_bookingId = 'EXT'.'1';
                        }
                        
	                    $this->db->where('id',$last_id)->update('ci_booking',array('book_ext'=>$rep_bookingId));
	                    $this->replace_bookings($bookinglist_check,$verifier_id);
	                    
	                    $get_wallet_amt = $this->db->select('*')->from('ci_wallet_user')->where('user_id',$getbooking[0]->user_id)->get()->result();
	                    if(count($get_wallet_amt)>0){
	                        /*
	                        	wallet_id	user_id	amount	status 	payment_type 	
	                        	booking_id  */
	                        	$new_amt =$get_wallet_amt[0]->amount;
	                        	$new_amt1 =$new_amt-$cost;
                    $this->db->where('id',$get_wallet_amt[0]->id)->update('ci_wallet_user',array('amount'=>(float)$new_amt1));
                    
                    $inserData1=array("wallet_id"=>$get_wallet_amt[0]->id,"user_id"=>$get_wallet_amt[0]->user_id,"amount"=>$cost,
                    "status"=>'2',"payment_type"=>'2','booking_id'=>$last_id,
                            'last_wallet_amount'=>$get_wallet_amt[0]->amount);
                                            // $insertPayment1 = $this->db->insert('ci_wallet_history',$inserData1);
                                            $this->wallet_history_log($inserData1);
                    
                    $getNotify = $this->db->select('*')->from('ci_notify_track')->where('booking_id',$last_id)->where('user_id',$getbooking[0]->user_id)
                            ->where('notify_type','4')
                            ->where('is_deleted','0')
                            ->get()->result();
                    $message= '₹ '.$cost.' has been deducted from your wallet';
                    $this->notificationForWallet($getbooking[0]->user_id,$last_id,$getbooking[0]->place_id,$getbooking[0]->slot_id, 'Wallet', $message,'3','4'); //notificationForWallet($userId, $bookingId,$place_id,$slot_id,$title, $body,$screen,$notifyType)
                    /*$msg = array('status' => TRUE, 'message' => 'Booking Successfull', 'id' => "$last_id",'session'=>'1','walletStatus'=>'0');
                                echo json_encode($msg);
	                        	$walletDatainsert=array(
	                        	    'wallet_id'=>$getwalletid[0]->id,	'user_id'=>$getbooking[0]->user_id,	'amount'=>0,	'status'=>'4','payment_type'=>'5',
	                        	'booking_id'=>$last_id);
	                        $this->db->insert('tbl_wallet_history',$walletDatainsert);	*/
	                    }
	           // print_r($datainsert);
	           return true;
	           }else{
	               return false;
	           }
	        
        
    }
    
    function wallet_history_log($message)
    {
        $transac_id=$this->create_transac_id();
        $message['transac_id']=$transac_id;
        return  $insertPayment1 = $this->db->insert('ci_wallet_history',$message);
    }
    
    function create_transac_id()
    {
        // $i=0;
        // $transcac_id = random_string('num', 18);
        $transcac_id = '';
        for($i = 0; $i < 12; $i++) { $transcac_id .= mt_rand(0, 9); }
        
        // print($this->generateCode(16));
        $getresult = $this->db->Select('*')->from('ci_wallet_history')->where('transac_id',$transcac_id)->get()->result();
        if(count($getresult)>0){
            
            $this->create_transac_id();
            // echo '11212122122';
            
        }
        else{
            return $transcac_id;
            
        }
    }
    
    public function bookinglist_check($place_id,$from_date,$to_date,$from_time,$to_time,$slot_id,$unique_bookId) //slot availibility   // this is for extention
    {
         date_default_timezone_set('Asia/Kolkata');
            $multiDate = false;
             $listof_Slots = $this->db->select('slot_no,display_id')->from('ci_parking_slot_info')
                ->where('place_id', $place_id)->where('status', '0')->where('onOff_applied','0')->where('is_deleted', '0')
                ->get()->result_Array();
            $data = $this->db->select('*')->from('ci_booking')
                ->where('place_id', $place_id)->where('slot_id',$slot_id)
                ->where('unique_booking_id !=',$unique_bookId)->where('booking_status','0')->where('is_deleted',"0")
                ->get()->result();
               
            // print_r($data);
            // exit();
            $listof_bookedSlots=[];
            $listof_bookings=[];
                foreach ($data  as $v) {
                    // print_r($v);
                    $fromDate_u = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $from_time)); //7-1 2
            $toDate_u = date('Y-m-d H:i:s', strtotime($to_date . ' ' . $to_time));
                    if($v->booking_type =='0'){ //daily
                        $fromDate_s = date('Y-m-d H:i:s', strtotime($v->booking_from_date . ' ' . $v->reserve_from_time)); //21 4
                        $toDate_s = date('Y-m-d H:i:s', strtotime($v->booking_to_date . ' ' . $v->reserve_to_time));  //25 5
                       
                        if ($fromDate_u <= $fromDate_s && $toDate_u >= $fromDate_s || $fromDate_u <= $toDate_s && $toDate_u >= $toDate_s
                        ||$fromDate_u<=$fromDate_s&&$toDate_u>=$toDate_s||$fromDate_s<=$fromDate_u&&$toDate_s>=$toDate_u)
                        {
                            
                            array_push($listof_bookings,$v);
                              }
                        
                    }
                    else{
                        
                        // print(' monthly '.$v->slot_id);
                     $fromDate_u_d =date('Y-m-d', strtotime($from_date));//7-2
                        $toDate_u_d = date('Y-m-d', strtotime($to_date));//7-28
                        $fromDate_s_d=date('Y-m-d', strtotime($v->booking_from_date));//7-1
                        $toDate_s_d=date('Y-m-d', strtotime($v->booking_to_date));//8-1
                        
                        
                        if($fromDate_s_d <= $fromDate_u_d && $toDate_s_d>=$fromDate_u_d || $fromDate_s_d<=$toDate_u_d&&$toDate_s_d>=$toDate_u_d ){
                            // print_r($v->slot_id);
                        if($multiDate=='true')
                        { // for multiple date
                             $fromDate_u = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $from_time)); //7-1 2
                             $toDate_u = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $to_time));
                             $fromDate_s = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v->reserve_from_time));
                             $toDate_s = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v->reserve_to_time));
                         
                            if ($fromDate_u <= $fromDate_s && $toDate_u >= $fromDate_s || $fromDate_u <= $toDate_s && $toDate_u >= $toDate_s
                            ||$fromDate_u<=$fromDate_s&&$toDate_u>=$toDate_s||$fromDate_s<=$fromDate_u&&$toDate_s>=$toDate_u) {
                           
                            // array_push($listof_bookings,array('slotid'=>$v->slot_id,'bookingid'=>$v->id));
                            array_push($listof_bookings,$v);
                        //   print_r($v);
                        }
                        //   print('multidate');  
                        }
                        else{ // for single date
                        
                             
                             $fromDate_s = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v->reserve_from_time));
                             $toDate_s =date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v->reserve_to_time));
                             if ($fromDate_u <= $fromDate_s && $toDate_u >= $fromDate_s || $fromDate_u <= $toDate_s && $toDate_u >= $toDate_s
                             ||$fromDate_u<=$fromDate_s&&$toDate_u>=$toDate_s||$fromDate_s<=$fromDate_u&&$toDate_s>=$toDate_u) 
                             {
                                    //  $sensorStatus = $this->db->select('status')->from('mpc_sensor')->where('slot_id',$v->slot_id)->get()->result();
                           
                            // array_push($listof_bookings,array('slotid'=>$v->slot_id,'bookingid'=>$v->id));
                            array_push($listof_bookings,$v);
                            }
                        }
                            
                        }
                    }
                    
                    }
                   
                
                return $listof_bookings;
                
    }
    
    public function voice_slot_available($place_id,$from_date,$to_date,$from_time,$to_time) //slot availibility // in progress mpc_sensor
    {
         date_default_timezone_set('Asia/Kolkata');
            $multiDate = false;
             $listof_Slots = $this->db->select('slot_no,display_id')->from('ci_parking_slot_info')
                ->where('place_id', $place_id)->where('status', '0')->where('onOff_applied','0')->where('is_deleted', '0')
                ->get()->result_Array();
            $data = $this->db->select('*')->from('ci_booking')
                ->where('place_id', $place_id)->where('booking_status','0')->where('is_deleted',"0")
                ->get()->result();
            
            $listof_bookedSlots=[];
                foreach ($data  as $v) {
                    // print_r($v);
                    
                    $fromDate_u = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $from_time)); //7-1 2
                    $toDate_u = date('Y-m-d H:i:s', strtotime($to_date . ' ' . $to_time));
                    
                    if($v->booking_type =='0'){ //daily
                        $fromDate_s = date('Y-m-d H:i:s', strtotime($v->booking_from_date . ' ' . $v->reserve_from_time)); //21 4
                        $toDate_s = date('Y-m-d H:i:s', strtotime($v->booking_to_date . ' ' . $v->reserve_to_time));  //25 5
                       
                        if ($fromDate_u <= $fromDate_s && $toDate_u >= $fromDate_s || $fromDate_u <= $toDate_s && $toDate_u >= $toDate_s
                        ||$fromDate_u<=$fromDate_s&&$toDate_u>=$toDate_s||$fromDate_s<=$fromDate_u&&$toDate_s>=$toDate_u)
                        {
                            // print_r($v);
                            // $sensorStatus = $this->db->Select('status')->from('mpc_sensor')->where('slot_id',$v->slot_id)->get()->result();
                            $sensorStatus = $this->db->Select('status')->from('mpc_sensor')->where('slot_id',$v->slot_id)->order_by("id", "DESC")->get()->result();
                           $bookedStatus = ''; //1==red,2==yellow
                           
                           $getVerifyStatus = $this->db->select('*')->from('ci_booking_verify')->where('booking_id',$v->id)
                           ->where('booking_type','0')->where('verify_status','1')->get()->result();
                           $vrifyBooking='1';
                           if(count($getVerifyStatus)>0)
                           {
                            $vrifyBooking='0';    
                           }
                           $sensorStatusNew = count($sensorStatus)>0?$sensorStatus[0]->status:'0';
                             if($vrifyBooking=='1'&&$sensorStatusNew=='1'){
                                 $bookedStatus = '1';
                             }else{
                                 $bookedStatus = '2';
                             }
                            array_push($listof_bookedSlots,array('slotid'=>$v->slot_id,'bookingid'=>$v->id,"bookedStatus"=>$bookedStatus));
                              }
                        
                    }
                    else{
                        
                        // print(' monthly '.$v->slot_id);
                     $fromDate_u_d =date('Y-m-d', strtotime($from_date));//7-2
                        $toDate_u_d = date('Y-m-d', strtotime($to_date));//7-28
                        $fromDate_s_d=date('Y-m-d', strtotime($v->booking_from_date));//7-1
                        $toDate_s_d=date('Y-m-d', strtotime($v->booking_to_date));//8-1
                        
                        
                        if($fromDate_s_d <= $fromDate_u_d && $toDate_s_d>=$fromDate_u_d || $fromDate_s_d<=$toDate_u_d&&$toDate_s_d>=$toDate_u_d ){
                            // print_r($v->slot_id);
                        if($multiDate=='true')
                        { // for multiple date
                             $fromDate_u = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $from_time)); //7-1 2
                             $toDate_u = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $to_time));
                             $fromDate_s = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v->reserve_from_time));
                             $toDate_s = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v->reserve_to_time));
                         
                            if ($fromDate_u <= $fromDate_s && $toDate_u >= $fromDate_s || $fromDate_u <= $toDate_s && $toDate_u >= $toDate_s
                            ||$fromDate_u<=$fromDate_s&&$toDate_u>=$toDate_s||$fromDate_s<=$fromDate_u&&$toDate_s>=$toDate_u) {
                            // array_push($listof_bookedSlots,array('slotid'=>$v->slot_id,'bookingid'=>$v->id));
                            //  $sensorStatus = $this->db->select('status')->from('mpc_sensor')->where('slot_id',$v->slot_id)->get()->result();
                            $sensorStatus = $this->db->Select('status')->from('mpc_sensor')->where('slot_id',$v->slot_id)->order_by("id", "DESC")->get()->result();
                             $bookedStatus = ''; //1==red,2==yellow
                            //  $getVerifyStatus = $this->db->select('*')->from('tbl_booking_verify')->where('booking_id',$v->id)
                            //   ->where('booking_type','0')->where('verify_status','1')->get()->result();
                            $getVerifyStatus = $this->db->select('*')->from('ci_booking_verify')->where('booking_id',$v->id)
                           ->where('booking_type','1')->where('verify_status','0')->order_by('id DESC')->get()->result();
                               $vrifyBooking='1';
                               if(count($getVerifyStatus)>0){
                                   if(date('Y-m-d',strtotime($getVerifyStatus[0]->onCreated))>=date('Y-m-d')){
                               $vrifyBooking='0';        
                                   }else{
                                       $vrifyBooking='1';
                                   }
                                   
                               }
                               $sensorStatusNew = count($sensorStatus)>0?$sensorStatus[0]->status:'0';
                             if($vrifyBooking=='1'&&$sensorStatusNew=='1'){
                                 $bookedStatus = '1';
                             }else{
                                 $bookedStatus = '2';
                             }
                            array_push($listof_bookedSlots,array('slotid'=>$v->slot_id,'bookingid'=>$v->id,"bookedStatus"=>$bookedStatus));
                        //   print_r($v);
                        }
                        //   print('multidate');  
                        }
                        else{ // for single date
                        
                             
                             $fromDate_s = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v->reserve_from_time));
                             $toDate_s =date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v->reserve_to_time));
                             if ($fromDate_u <= $fromDate_s && $toDate_u >= $fromDate_s || $fromDate_u <= $toDate_s && $toDate_u >= $toDate_s
                             ||$fromDate_u<=$fromDate_s&&$toDate_u>=$toDate_s||$fromDate_s<=$fromDate_u&&$toDate_s>=$toDate_u) 
                             {
                                    //  $sensorStatus = $this->db->select('status')->from('mpc_sensor')->where('slot_id',$v->slot_id)->get()->result();
                           $sensorStatus = $this->db->Select('status')->from('mpc_sensor')->where('slot_id',$v->slot_id)->order_by("id", "DESC")->get()->result();
                           $bookedStatus = ''; //1==red,2==yellow
                           $getVerifyStatus = $this->db->select('*')->from('ci_booking_verify')->where('booking_id',$v->id)
                           ->where('booking_type','1')->where('verify_status','0')->order_by('id DESC')->get()->result();
                               $vrifyBooking='1';
                               if(count($getVerifyStatus)>0){
                                   if(date('Y-m-d',strtotime($getVerifyStatus[0]->onCreated))>=date('Y-m-d')){
                               $vrifyBooking='0';        
                                   }else{
                                       $vrifyBooking='1';
                                   }
                                   
                               }
                               $sensorStatusNew = count($sensorStatus)>0?$sensorStatus[0]->status:'0';
                             if($vrifyBooking=='1'&&$sensorStatusNew=='1'){
                                 $bookedStatus = '1';
                             }else{
                                 $bookedStatus = '2';
                             }
                            array_push($listof_bookedSlots,array('slotid'=>$v->slot_id,'bookingid'=>$v->id,"bookedStatus"=>$bookedStatus));
                            }
                        }
                            
                        }
                    }
                    
                    }
                   
                $slot_availabledata =[];
                foreach($listof_Slots as $slot1){
                    // print('fdhfdgdf');
                    $slot1['slotAvailable']=(string)0;
                    $slot1['bookedStatus']=(string)0;//0== green,,1 ==red,2 == yellow
                    $slot1['color']='Green';
                    foreach($listof_bookedSlots as $booked_s){
                        // print(' ** '.$slot1['slot_no'].'<br />'.$booked_s['slotid'].'** ');
                        
                        if($slot1['slot_no']==$booked_s['slotid']){
                             $slot1['slotAvailable']=(string)1;
                                $slot1['bookedStatus']=$booked_s['bookedStatus'];  //0== green,,1 ==red,2 == yellow
                                $slot1['color']=$booked_s['bookedStatus']=='1'?'Red':'Yellow';
                                // $slot1['sensorStatus']=$booked_s['sensor'];
                        }
                      
                    }
                  
                    array_push($slot_availabledata,$slot1);
                }
                $slotId='';
                foreach($slot_availabledata as $s){
                    // print(' - '.$s['slot_no'].' - ');
                    if(trim($s['bookedStatus']," ")=='0'){
                        
                       $slotId=$slotId==''?$s['slot_no']:'';
                       break;
                    //   print(':'.$slotId.' : ');
                    }
                }
                // print($slotId);
                // print_r($slot_availabledata);
                return $slotId;
                // echo json_encode(
                //     array("status"=>true,
                //     "message"=>"list of slots",
                //     "data"=>$slot_availabledata,'session'=>'1'));
        
}

    public function replace_bookings($bookingList,$verifier_id)
    {
        foreach($bookingList as $b){
            $slot_id =  $this->voice_slot_available($b->place_id,$b->booking_from_date,$b->booking_to_date,$b->from_time,$b->to_time);
                    //   exit();
           
            // print_r($get_wallet_amt);
                // print('jii');
                if($slot_id!=''){
            $reserve_from_time= date('H:i:s',strtotime($b->from_time . ' -15 minutes'));
            $reserve_to_time= date('H:i:s',strtotime($b->to_time . ' +15 minutes'));
            $placeDetails = $this->db->select('*')->from('ci_parking_places')->where('id',$b->place_id)->where('is_deleted','0')->get()->result();
            $vendor_id=count($placeDetails)>0?$placeDetails[0]->vendor_id:0;
            //   echo $date;
            $this->db->where('id',$b->id)->update('ci_booking',array('booking_status'=>'4'));
            $data = array(
	                   // 'unique_booking_id'=>$bookingid1,	
	                    'user_id'	=>$b->user_id,
	                    'place_id'	=>$b->place_id,
	                    'slot_id'	=>$slot_id,
	                    'unique_booking_id'=>$b->unique_booking_id,
	                    'booking_status'=>'0', 	
                        'replaced_booking_id'	=>$b->id,
                        'booking_from_date'	=>$b->booking_from_date,
                        'booking_to_date'	=>$b->booking_to_date,
                        'from_time'=>$b->from_time,
                        'to_time'	=>$b->to_time,
                        'paid_status'=>$b->paid_status,
                        'booking_type'=>'0',
                        'cost'	=>$b->cost,
                        'reserve_from_time' =>$b->from_time,	
                        'reserve_to_time' =>$b->to_time,
                        'vendor_id'=>$b->vendor_id,
                        'verfier_id'=>$verifier_id,
                        'car_id'=>$b->car_id
	                    );            
                // print_r($data);
                // exit();
                $result = $this->db->insert('ci_booking', $data);   
                $last_id = $this->db->insert_id();
                if($result == true)
                {
                   $rep_bookingId='';
                        $par_data=$this->db->select('*')->from('ci_booking')->where('unique_booking_id',$b->unique_booking_id)->like('book_ext','REP')->order_by("id", "Desc")->get()->result();
                        if(count($par_data)>0){
                            
                            $expoit = explode("P",$par_data[0]->book_ext);
                            $count = $expoit[1]+1;
                            $rep_bookingId = 'REP'.$count;
                        }else{
                            $rep_bookingId = 'REP'.'1';
                        }
                        
	                    $this->db->where('id',$last_id)->update('ci_booking',array('book_ext'=>$rep_bookingId));
                    // $new_amt1 =$new_amt-$cost;
                    // $this->db->where('id',$get_wallet_amt[0]->id)->update('tbl_wallet_user',array('amount'=>(float)$new_amt1));
                    
                    // $inserData1=array("wallet_id"=>$get_wallet_amt[0]->id,"user_id"=>$get_wallet_amt[0]->user_id,"amount"=>$cost,"status"=>'2',"payment_type"=>'2','booking_id'=>$last_id);
                    //                         $insertPayment1 = $this->db->insert('tbl_wallet_history',$inserData1);
                    
                    // $getNotify = $this->db->select('*')->from('tbl_notify_track')->where('booking_id',$last_id)->where('user_id',$user_id)
                    //         ->where('notify_type','4')
                    //         ->where('is_deleted','0')
                    //         ->get()->result();
                    // $message= 'Amount '.$cost.' has been deducted from your wallet';
                    // $this->notificationForWallet($user_id,$last_id,$place_id,$slot_id, 'Wallet', $message,'3','4'); //notificationForWallet($userId, $bookingId,$place_id,$slot_id,$title, $body,$screen,$notifyType)
                    // $msg = array('status' => TRUE, 'message' => 'Booking Successfull', 'id' => "$last_id",'session'=>'1','walletStatus'=>'0');
                    //             echo json_encode($msg);
                    
	                      
                    $message = 'Your booking is replaced by another slot'.' From '.$b->from_time.' to '.$b->to_time.' kindly check.' ;
                    //$message ='Your booking';
                    
                    $getNotify = $this->db->select('*')->from('ci_notify_track')->where('booking_id',$last_id)->where('user_id',$b->user_id)
                    ->where('notify_type','8')
                    ->where('is_deleted','0')
                    ->get()->result();
                    // print_r($getNotify);
                    if(count($getNotify)<=0){
                        // print($message);
                        $b->id = $last_id;
                        $bookingl=[];
                        array_push($bookingl,$b);
                        $isUseArrow=true;
                        $this->notificationallApiBuilding_Verifier($b,'Your Booking',$message,'3','1',true,$isUseArrow); //3= Your booking detail screen
                    // $message = 'A Slot ( ID : '.$b->slot_id.') has been booked at '.$getplaceName[0]->placename.' From '.$b->from_time.' to '.$b->to_time ;
                    // $this->notificationApiVerifier($b,'Booking',$message,'0','0');
                    }
                    else{
                        print('Notification already went');
                    }
                }
                }else{
                    $this->booking_cancel_ext($b->id,$verifier_id);
                }
                
            
        }
    }
    
    public function booking_cancel_ext($bookingId,$verifier_id)
    {
            
            $booking_id = $bookingId;
            
            
                
                $booking= $this->db->select('*')->from('ci_booking')->where('id',$booking_id)->where('booking_status','0')->get()->result();
                
                if(count($booking)>0){
                $user_id =$booking[0]->user_id ;
                $this->db->where('id',$booking_id)->update('ci_booking',array('verfier_id'=>$verifier_id));
                  $cancledBooking=  $this->db->where('id',$booking_id)->update('ci_booking',array('booking_status'=>'2'));
                  if($cancledBooking){
                    $get_amt = $this->db->select('*')->from('ci_wallet_user')->where('user_id',$user_id)->get()->result();
                    if(count($get_amt)>0){
                        $new_amt =$get_amt[0]->amount+$booking[0]->cost;
                        $this->db->where('id',$get_amt[0]->id)->update('ci_wallet_user',array('amount'=>(float)$new_amt));
                        $inserData1=array("wallet_id"=>$get_amt[0]->id,"user_id"=>$get_amt[0]->user_id,
                        "amount"=>$booking[0]->cost,"status"=>'1',"payment_type"=>'3','booking_id'=>$booking_id,
                            'last_wallet_amount'=>$get_amt[0]->amount);
                        // $insertPayment1 = $this->db->insert('ci_wallet_history',$inserData1);
                        $this->wallet_history_log($inserData1);
                        
                        $getNotify = $this->db->select('*')->from('ci_notify_track')->where('booking_id',$booking_id)->where('user_id',$user_id)
                            ->where('notify_type','5')
                            ->where('is_deleted','0')
                            ->get()->result();
                            $emoji ="\u{E007F}";
                    $message= 'Your booking has been cancelled '.$emoji.' & ₹ '.$booking[0]->cost.' has been refunded to your wallet';
                    $this->notificationForWallet($user_id,$booking_id,$booking[0]->place_id,$booking[0]->slot_id, 'Booking & Wallet', $message,'6','5'); //6= booking list screen,5= refunded
                    }
                      
                  }
                    // $msg = array('status' => true, 'message' => "Successfully cancelled Booking",'session'=>'1');
                    // echo json_encode($msg);
                
                }
    
    }
    
    public function notificationForWallet($userId, $bookingId,$place_id,$slot_id,$title, $body,$screen,$notifyType) // this function is uses firebase api to send notification.
    {
         
            $getUserTopic = $this->db->select('notifn_topic')->from('ci_users')->where('id',$userId)->where('is_active','1')->get()->result();
     
            if(count($getUserTopic)>0){
                $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
                // $token='all';
                $token = $getUserTopic[0]->notifn_topic;
                // print($token);
                $notification = [
                                    'title' =>$title,
                                    'body' => $body,
                                    'icon' =>'myIcon', 
                                    'sound' => 'default_sound'
                                ];
            
                $extraNotificationData = [
                                            'title' =>$title,
                                            'body' => $body,
                                            'screen'=>$screen,
                                            'bookingid'=>$bookingId,
                                            "click_action"=> "FLUTTER_NOTIFICATION_CLICK"
                                        ];
    
                $fcmNotification = [
                                        'to'=> '/topics/'.$token, //single token
                                        'notification' => $notification,
                                        'data' => $extraNotificationData
                                    ];
    
                $headers = [
                            'Authorization: key=' . 'AAAASeBlySQ:APA91bG5g4s-FAsFw9zfKEJ638XWzhpSGbeUa4jallP5rh0wG6dozGFrihHYj4bneh3qoGrFS74QO7Ra5l_kuTXpnH40KptG6wZvoZcGJGLBdjwMRLL8F6Ajfv9CWSRNqemDaVlvgHDB',
                            'Content-Type: application/json'
                        ];
    
    
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$fcmUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
                $result = curl_exec($ch);
                // print($result);
                if($result){
                    $this->db->insert('ci_notify_track',array("notify_type"=>$notifyType,"booking_id"=>$bookingId,"user_id"=>$userId,"place_id"=>$place_id,"slot_id"=>$slot_id ));
                }
                curl_close($ch);
    
                
            }
            else{
                // echo 'no building found'.$buildingid;
            }
    }
    
   
    
        public function get_all_issues()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            date_default_timezone_set('Asia/Kolkata');
	        $this->form_validation->set_rules('place_id','Place Id','required');
	        if($this->form_validation->run()==false)
	        {
	            $errorMsg = $this->form_validation->error_array();
	             $msg = array('status' => false, 'message' => $this->_returnSingle($errorMsg));
            echo json_encode($msg);
	        }
	        else
	        {
	            $place_id = $this->security->xss_clean($this->input->post('place_id'));
	            $bookingIssues = [];
	            $slotIssues = [];
	               $bookingIssues = $this->db->select('complaint.*,desposition.descriptions,slot.display_id,booking.unique_booking_id')
	               ->from('tbl_verifier_complaints as complaint')
	               ->join('ci_despositions as desposition', 'complaint.fk_despostion_id = desposition.id')
	               ->join('ci_parking_slot_info as slot', 'complaint.slot_id = slot.slot_no')
	               ->join('ci_booking as booking', 'complaint.booking_id = booking.id')
	               ->where('complaint.place_id',$place_id)
	               ->where('complaint.createdDate >=', date("Y-m-d H:i:s", strtotime("-10 days")))
	               ->where('complaint.createdDate <=', date("Y-m-d H:i:s"))
	               ->where('complaint.is_deleted',0)
	               ->get()
	               ->result_Array();
	               //print_r($bookingIssues);
	               
	               $slotIssues = $this->db->select('complaint.*,slot.display_id')
	               ->from('ci_slots_complaints as complaint')
	               ->join('ci_parking_slot_info as slot', 'complaint.slot_id = slot.slot_no')
	               ->where('complaint.place_id',$place_id)
	               ->where('complaint.issue_raised_on >=', date("Y-m-d H:i:s", strtotime("-10 days")))
	               ->where('complaint.issue_raised_on <=', date("Y-m-d H:i:s"))
	               ->where('complaint.is_deleted',0)
	               ->get()
	               ->result_Array();
	               //print_r($bookingIssues);
	               //if($bookingIssues==[]&&$slotIssues==[]){
	               //    $msg = array('status' => true, 'message' => 'No details found.','bookingIssues'=>$bookingIssues,'slotIssues'=>$slotIssues);
                //       echo json_encode($msg);
	               //}else{
	                   $msg = array('status' => true, 'message' => 'List of Places...','bookingIssues'=>$bookingIssues,'slotIssues'=>$slotIssues);
                      echo json_encode($msg);
	       // }
	            
	        }
        }
    }
    
     public function priceSlabDataForPlaces()
    {
        date_default_timezone_set('Asia/Kolkata');
	        $this->form_validation->set_rules('place_id','Place Id','required');
	        
	        
	        if($this->form_validation->run()==false)
	        {
	            $errorMsg = $this->form_validation->error_array();
	            $msg = array('status' => false, 'mesg' => $this->_returnSingle($errorMsg));
                echo json_encode($msg);
	        }
	        else
	        {
	           // $placeId = $this->security->xss_clean($this->input->post('verifierId'));
	            $place_id = $this->security->xss_clean($this->input->post('place_id'));
	            $getPlaceDetails = $this->db->select('*')->from('ci_parking_places')
                ->where('id',$place_id)->where('place_status','1')->get()->result_array();
                $price_Slab;
                $pricetype;
                $placetype;
                if(count($getPlaceDetails)>0)
                {
                    if(count($getPlaceDetails)>0)
                    {
                        $price_Slab = $this->priceslabData($getPlaceDetails[0]['id'], $getPlaceDetails[0]['ext_per'], $getPlaceDetails[0]['pricing_type']);
                        $pricetype=$getPlaceDetails[0]['pricing_type'];
                    $placetype=$getPlaceDetails[0]['status'];
    	            }
	           // print_r($price_Slab);
    	            echo json_encode(array('status'=>true,
    	            'mesg'=>'priceslab data',
    	            'pricetype'=>$pricetype,
    	            'placetype'=>$placetype,
    	            'priceSlabData'=>$price_Slab));
                }else{
                   echo json_encode(array('status'=>false,
    	            'mesg'=>'No data found.',
    	            'pricetype'=>$pricetype,
    	            'placetype'=>$placetype,
    	            'priceSlabData'=>$price_Slab)); 
                }
	        }
    }
    
    
    public function getAllSensorsData($place_ids)
    {
                date_default_timezone_set('Asia/Kolkata');
	            $place_id = $place_ids;
	            $slotList = $this->db->select('*')->from('ci_parking_slot_info')
	            ->where('ci_parking_slot_info.place_id',$place_id)
	            ->where('ci_parking_slot_info.status','0')
	            ->where('ci_parking_slot_info.is_deleted','0')
	            ->order_by('slot_no asc')
	           // ->where('tbl_sensor_list.is_deleted','0')
	           // ->where('tbl_sensor_list.test_status','1')
	            ->get()->result_array();
	            $current_time=date("H:i:s");
	            $current_date=date("Y-m-d");
                //   print($current_time);
                $currentdatetime=strtotime(date("Y-m-d H:i:s"));
                $enddate = strtotime("-3 min", $currentdatetime);
                $currentdatetime_d =date("Y-m-d H:i:s", $currentdatetime);
                $enddate_d=date("Y-m-d H:i:s", $enddate);
                $listSensor=[];
                // print_r($slotList);
	            if(count($slotList)>0){
    	            foreach($slotList as $slot){
    	                /*id 	
    	                place_id	
    	                slot_id	
    	                is_verified 0:Not Verifierd 1: Veriferd 	
                        verifier_id	
                        engineer_id	
                        complaint_status  0:Pending 1:Processing 2:Solved 	
                        complaint_text	
                        verifier_remark	
                        engineer_remark	
                        img_attachments	
                        complaint_source 0:Replacement 1: Verifier App 	
                        issue_raised_on	
                        issue_resolved_on */
                        $isIssueOn = false;
                        $issueData =array('id'=>'',
    	                'place_id'=>'',
    	                'slot_id'=>'',
    	                'is_verified'=>'',//0:Not Verifierd 1: Veriferd 	
                        'verifier_id'=>'',
                        'engineer_id'=>'',
                        'complaint_status'=>'',//0:Pending 1:Processing 2:Solved 	
                        'complaint_text'=>'',	
                        'verifier_remark'=>'',
                        'engineer_remark'=>'',
                        'img_attachments'=>'',
                        'complaint_source'=>'', //0:Replacement 1: Verifier App 	
                        'issue_raised_on'=>'',
                        'issue_resolved_on'=>'');
    	                $getSlotRaisedIssueData = $this->db->select('*')->from('ci_slots_complaints')
    	               ->where('slot_id',$slot['slot_no'])->where('complaint_status!=','2')->get()->result_array();
    	               if(count($getSlotRaisedIssueData)>0){
    	                   $isIssueOn=true;
    	                   $issueData =array('id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['id']),
    	                'place_id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['place_id']),
    	                'slot_id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['slot_id']),
    	                'is_verified'=>$this->checknullValidation($getSlotRaisedIssueData[0]['is_verified']),//0:Not Verifierd 1: Veriferd 	
                        'verifier_id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['verifier_id']),
                        'engineer_id'=>$this->checknullValidation($getSlotRaisedIssueData[0]['engineer_id']),
                        'complaint_status'=>$this->checknullValidation($getSlotRaisedIssueData[0]['complaint_status']),//0:Pending 1:Processing 2:Solved 	
                        'complaint_text'=>$this->checknullValidation($getSlotRaisedIssueData[0]['complaint_text']),	
                        'verifier_remark'=>$this->checknullValidation($getSlotRaisedIssueData[0]['verifier_remark']),
                        'engineer_remark'=>$this->checknullValidation($getSlotRaisedIssueData[0]['engineer_remark']),
                        'img_attachments'=>$this->checknullValidation($getSlotRaisedIssueData[0]['img_attachments']),
                        'complaint_source'=>$this->checknullValidation($getSlotRaisedIssueData[0]['complaint_source']), //0:Replacement 1: Verifier App 	
                        'issue_raised_on'=>$this->checknullValidation($getSlotRaisedIssueData[0]['issue_raised_on']),
                        'issue_resolved_on'=>$getSlotRaisedIssueData[0]['complaint_status']==2?$this->checknullValidation($getSlotRaisedIssueData[0]['issue_resolved_on']):'');
    	               }
    	                if($slot['isBlocked']=='1'){
    	                 $sensorregisteredList = $this->db->select('*')->from('tbl_sensor_list')->where('id',$slot['machine_id'])->where('is_deleted','0')->where('test_status','1')->get()->result_array();
    	                if(count($sensorregisteredList)>0){
    	                 $sensorDataList = $this->db->select('*')->from('mpc_sensor')->where('device_id',$sensorregisteredList[0]['device_id'])
                        ->where('sensor_time<=',$currentdatetime_d)
                        ->where('sensor_time>=',$enddate_d)
                        ->order_by('id' , 'desc')->get()->result_array();
                        
                        // print_r($sensorDataList);
                            if(count($sensorDataList)>0)
                            {
                                $redStatusCount =0;
                                foreach($sensorDataList as $details)
                                {
                                    if($details['status']=='1'){
                                        $redStatusCount=$redStatusCount+1;
                                    }
                                }
                                
                               
                                    $getSlotData = 
                                        $this->db->select('slot_no,display_id,place_id')->from('ci_parking_slot_info')
                                        ->where('machine_id', $sensorregisteredList[0]['id'])->where('status', '0')->where('onOff_applied','0')->where('is_deleted', '0')
                                        ->get()->result_Array();
                                            if(count($getSlotData)>0)
                                            {
                                                if($redStatusCount>=(count($sensorDataList)/2))
                                                {
                                                $getBooking = $this->db->select('*')->from('ci_booking')->where('slot_id',$getSlotData[0]['slot_no'])
                                                ->group_start()->where('booking_status','0')->or_where('booking_status','3')->group_end()
                                                ->where('booking_from_date<=',$current_date)
                                                ->where('booking_to_date>=',$current_date)
                                                // ->where('reserve_from_time<=',$current_time)
                                                // ->where('reserve_to_time>=',$current_time)
                                                ->where('is_deleted',"0")
                                                ->get()->result_array();
                                                
                                                if(count($getBooking)>0)
                                                {
                                                    // $currentdatetime_d =date("Y-m-d H:i:s", $getBooking[0]);
                                                    if($getBooking[0]['booking_from_date']== $getBooking[0]['booking_to_date']){
                                                        if($getBooking[0]['reserve_from_time']<=$current_time && $getBooking[0]['reserve_to_time']>=$current_time ){
                                                        
                                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                            'slot_no'=>$getSlotData[0]['slot_no'],
                                                            'display_id'=>$getSlotData[0]['display_id'],
                                                            // ,
                                                            'status'=>0,
                                                            'color'=>'Yellow',
                                                            'slotIssueData'=>$issueData,
                                                            'isIssueOn'=>$isIssueOn,
                                                            'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' is booked'
                                                            ));
                                                            
                                                        }
                                                        else{
                                                           array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                              'color'=>'Red',
                                                              'slotIssueData'=>$issueData,
                                                              'isIssueOn'=>$isIssueOn,
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                        }
                                                    
                                                }else{
                                                    $currendate_fulld=date("Y-m-d H:i:s");
                                                    $currentdate=date('Y-m-d');
                                                        
                                                         if($getBooking[0]['booking_type']=='0') //daily
                                                    {
                                                        $startdate_fulld =date("Y-m-d H:i:s",strtotime($getBooking[0]['booking_from_date'].' '.$getBooking[0]['reserve_from_time']));
                                                    $enddate_fulld=date("Y-m-d H:i:s", strtotime($getBooking[0]['booking_to_date'].' '.$getBooking[0]['reserve_to_time']));
                                                     if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld )
                                                     {
                                                        
                                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                            'slot_no'=>$getSlotData[0]['slot_no'],
                                                            'display_id'=>$getSlotData[0]['display_id'],
                                                            // ,
                                                            'status'=>0,
                                                            'color'=>'Yellow',
                                                            'slotIssueData'=>$issueData,
                                                            'isIssueOn'=>$isIssueOn,
                                                            'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' is booked'
                                                            ));
                                                            }
                                                    else{
                                                           array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                              'color'=>'Red',
                                                              'slotIssueData'=>$issueData,
                                                              'isIssueOn'=>$isIssueOn,
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                        }
                                                        
                                                    }else{
                                                       $startdate_fulld =date("Y-m-d H:i:s",strtotime($currentdate.' '.$getBooking[0]['reserve_from_time']));
                                                    $enddate_fulld=date("Y-m-d H:i:s", strtotime($currentdate.' '.$getBooking[0]['reserve_to_time']));
                                                     if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld )
                                                     {
                                                        
                                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                            'slot_no'=>$getSlotData[0]['slot_no'],
                                                            'display_id'=>$getSlotData[0]['display_id'],
                                                            // ,
                                                            'status'=>0,
                                                            'color'=>'Yellow',
                                                            'slotIssueData'=>$issueData,
                                                            'isIssueOn'=>$isIssueOn,
                                                            'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' is booked'
                                                            ));
                                                            }
                                                    else{
                                                           array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                              'color'=>'Red',
                                                              'slotIssueData'=>$issueData,
                                                              'isIssueOn'=>$isIssueOn,
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                        }
                                                    }
                                                }
                                                    
                                                }
                                                else
                                                {
                                                    
                                                       
                                                             array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                              'slot_no'=>$slot['slot_no'],
                                                              'display_id'=>$slot['display_id'],
                                                              'status'=>1,
                                                                'color'=>'Red',
                                                                'slotIssueData'=>$issueData,
                                                                'isIssueOn'=>$isIssueOn,
                                                              'msg'=>'Some object present over Sensor id is '.$sensorregisteredList[0]['device_id']
                                                             ));
                                                            
                                                         
                                                
                                                }
                                                
                                                }
                                            else{
                                                array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                                 'slot_no'=>$slot['slot_no'],
                                                    'display_id'=>$slot['display_id'],
                                                    'status'=>2,
                                                    'color'=>'Green',
                                                    'slotIssueData'=>$issueData,
                                                    'isIssueOn'=>$isIssueOn,
                                                'msg'=>'Nothing is present over sensor. '.$sensorregisteredList[0]['device_id']));
                                            }
                                        }
                                        else{
                                            array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                             'slot_no'=>$slot['slot_no'],
                                            'display_id'=>$slot['display_id'],
                                            'status'=>3,
                                            'color'=>'Grey',
                                            'slotIssueData'=>$issueData,
                                            'isIssueOn'=>$isIssueOn,
                                            'msg'=>'Sensor id is '.$sensorregisteredList[0]['device_id'].' not connected with any slot.'));
                                        }
                               
                            
                                     
                                            
                                    
                                }
                            else
                            {
                               array_push($listSensor,array('deviceid'=>$sensorregisteredList[0]['device_id'],
                                'slot_no'=>$slot['slot_no'],
                                'display_id'=>$slot['display_id'],
                                'status'=>4,
                                'color'=>'Orange',
                                'slotIssueData'=>$issueData,
                                'isIssueOn'=>$isIssueOn,
                                'msg'=>'Sensor id '.$sensorregisteredList[0]['device_id'].' not responding.'));
                            }
    	                }
    	                else{
    	                    array_push($listSensor,array('deviceid'=>'0',
                                'slot_no'=>$slot['slot_no'],
                                'display_id'=>$slot['display_id'],
                                'status'=>3,
                                'color'=>'Grey',
                                'slotIssueData'=>$issueData,
                                'isIssueOn'=>$isIssueOn,
                                'msg'=>'slot no. '.$slot['slot_no'].' not connected to anyone sensor.'));
    	                }
    	                }
    	                else{
    	                    
        	                    array_push($listSensor,array('deviceid'=>'0',
                                    'slot_no'=>$slot['slot_no'],
                                    'display_id'=>$slot['display_id'],
                                    'status'=>5,
                                    'color'=>'black',
                                    'slotIssueData'=>$issueData,
                                    'isIssueOn'=>$isIssueOn,
                                    'msg'=>'slot no. '.$slot['slot_no'].' is inaccessible.'));
        	                }
    	                
    	                
    	            }
    	            
    	            return (array('status'=>true,'message'=>'list of data','slotdetails'=>$listSensor));
	            }else{
	                return (array('status'=>false,'message'=>'No data available','slotdetails'=>$listSensor));
	            }
	            
	        
    }
    
    
    // Started By Raj Namdev 25-08-2022
    // Edited By Sushant 05-09-2022
    
    public function verifier_login()
    {
        // $this->form_validation->set_rules('verifier_id', 'Verifier Id', 'required');
        $this->form_validation->set_rules('email_id', 'Email id', 'required|valid_email', array('is_unique' => 'The Verifier has been Already Register'));
        $this->form_validation->set_rules('password', 'Password', 'required');
        
        if ($this->form_validation->run()) {
            $email = $this->security->xss_clean($this->input->post('email_id'));
            $password = $this->security->xss_clean($this->input->post('password'));
            
            $this->db->select('admin_id, admin_role_id, email, password,notifn_topic');
            $this->db->from('ci_admin');
            $this->db->where('email', $email);
            //  $this->db->where('admin_role_id', '3');
             $this->db->group_start()->where('admin_role_id','3')->or_where('admin_role_id','11')->group_end();
            $this->db->where('is_active', 1);
            $query = $this->db->get();
            
            $verifier = $query->result_Array();
            // print($email);
            // print_r($verifier);
            
            if(!empty($verifier)){
                if(verifyHashedPassword($password, $verifier[0]['password'])){
                    
                    $verifier[0]['isadmin']=
                    $verifier[0]['admin_role_id']=='11'?true:false;
                    
                    $msg = array('status' => true, 'message' => "Login Successfull!", 'data' => $verifier);
                    echo json_encode($msg);
                } else {
                    $msg = array('status' => false, 'message' => "Incorrect EmailId or Password.", 'data' => []);
                    echo json_encode($msg);
                }
            } else {
                $msg = array('status' => false, 'message' => "No verifier found!", 'data' => []);
                echo json_encode($msg);
            }
        } else {
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'token'=>'');
            echo json_encode($msg);
        }
    }
  
}
?>