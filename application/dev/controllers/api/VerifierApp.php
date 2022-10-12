<?php
defined('BASEPATH') or exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET,POST, OPTIONS");

class VerifierApp extends CI_Controller
{
    // Test apis 
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
	
	public function verifier_login()
    {
        $this->form_validation->set_rules('email_id', 'Email id', 'required|valid_email', array('is_unique' => 'The Verifier has been Already Register'));
        $this->form_validation->set_rules('password', 'Password', 'required');
        
        if ($this->form_validation->run()) {
            $email = $this->security->xss_clean($this->input->post('email_id'));
            $password = $this->security->xss_clean($this->input->post('password'));
            
            $this->db->select('admin_id, admin_role_id, email, password,notifn_topic');
            $this->db->from('ci_admin');
            $this->db->where('email', $email);
             $this->db->where('admin_role_id', '3');
            $this->db->where('is_active', 1);
            $query = $this->db->get();
            
            $verifier = $query->result_Array();
            // print($email);
            // print_r($verifier);
            
            if(!empty($verifier)){
                if(verifyHashedPassword($password, $verifier[0]['password'])){
                    $msg = array('status' => true, 'message' => "Login Successfull!", 'data' => $verifier);
                    echo json_encode($msg);
                } else {
                    $msg = array('status' => false, 'message' => "Login Failed!", 'data' => []);
                    echo json_encode($msg);
                }
            } else {
                $msg = array('status' => false, 'message' => "Failed!", 'data' => []);
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
    
    public function get_verifier_bookings()
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->form_validation->set_rules('verifier_id', 'Verifier Id', 'required');
        
        
        if ($this->form_validation->run()) 
        {
            $verifier_id = $this->security->xss_clean($this->input->post('verifier_id'));
            
            $this->db->select('BaseTbl.place_id');
            $this->db->from('tbl_verifier_place as BaseTbl');
            $this->db->where('BaseTbl.verifier_id', $verifier_id);
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
                if($d['booking_type']=='0'){  // daily
                
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
            
            
            if(!empty($timewise)){
                $getStatusVerifier = $this->db->select('id,subject')->from('master_verifier_issues')->where('type','1')->get()->result();
                $msg = array('status' => true, 'message' => "List of bookings assigned to verifier", 'bookings' => $timewise_new,'issuelist'=>
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
            if(count($allBookingList)>0){
            foreach($allBookingList as $booking)
            {
                $currentdate = date('Y-m-d');
                $currentdatetimestart = date('Y-m-d H:i:s',strtotime($currentdate.' '.'00:00:00'));
                $currentDateTime = date('Y-m-d H:i:s');
                $checkBookingVerificetion =
                $booking['booking_type']=='0'?
                //daily
                $this->db->select('*')->from('ci_booking_verify')
                ->where('booking_id',$booking['bookingId'])
                ->where('verify_status','1')
                ->get()->result_array()
                ://passes
                    $this->db->select('*')->from('ci_booking_verify')
                                                ->where('booking_id',$booking['bookingId'])->where('verify_status','1')
                                                ->group_start()->where('onCreated>=',$currentdatetimestart)
                                                ->or_where('onCreated<=',$currentDateTime)->group_end()
                                                ->get()->result_array();
                
                if(count($checkBookingVerificetion)>0)
                {
                    // $booking['isverify']=true;
                    array_push($verifiedList,$booking);
                }else{
                    // $booking['isverify']=false;
                    array_push($unverifiedList,$booking);
                }
                    

                
            }
            $mesg = array('status' => true,
                            'message' => 'List of bookings',
                            // 'bookings' => $allBookingList,
                            'verifiedBookings'=>$verifiedList,
                            'unverifiedBookings'=>$unverifiedList
                        
                            );
                            echo json_encode($mesg);
            }else{
                $mesg = array('status' => false,
                            'message' => 'No booking data found.',
                            // 'bookings' => $allBookingList,
                            'verifiedBookings'=>[],
                            'unverifiedBookings'=>[]
                        
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
	               $placeIdList = $this->db->select('place_id')->from('tbl_verifier_place')->where('verifier_id',$verifierId)
	               ->where('isDeleted',0)->get()->result();
	               $list=[];
	               
	               $now = date('Y-m-d');
	               $sensorIssueData=array('isObjectOverSensor'=>false,'noOfIssues'=>'0','isSlotListAvailable'=>false);
	               if(count($placeIdList)>0){
	                   foreach($placeIdList as $placeId){
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
	                   if(count($placeList)>0){
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
        	                       if($now>=$startdate&&$now<=$enddate){
        	                           // print_r($v);
        	                   //exit();
        	                       array_push($number,$v);
        	                       //print_r($placeList);
        	                       $placeList[0]->noOfBookings=count($number);
        	                       }else{
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
/*    public function placeList()   // dashboard api
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
	               $placeIdList = $this->db->select('place_id')->from('tbl_verifier_place')->where('verifier_id',$verifierId)
	               ->where('isDeleted',0)->get()->result();
	               $list=[];
	               
	               $now = date('Y-m-d');
	               $sensorIssueData=array('isObjectOverSensor'=>false,'noOfIssues'=>'0','isSlotListAvailable'=>false);
	               if(count($placeIdList)>0){
	                   foreach($placeIdList as $placeId){
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
	                   if(count($placeList)>0){
	                       $getEnforcerPlace = $this->db->select('*')
	                       ->from('tbl_enforcer_place')
	                       ->where('place_id',$placeId->place_id)
	                       ->get()
	                       ->result();
	                       if(count($getEnforcerPlace)>0)
	                       {
    	                       $getEnforceDetails= $this->db->select('*')
    	                       ->from('ci_support_master')
    	                       ->where('id','1')->order_by('id asc')
    	                       ->where('is_deleted','0')
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
	                           
	                       }
	                       else
	                       {
	                           $placeList[0]->enforcerNo ='';
	                       }
	                       
	                       if(count($noOfBookings)>0){
	                           foreach($noOfBookings as $v){
	                       $startdate = date('Y-m-d',strtotime($v->booking_from_date));
	                       $enddate = date('Y-m-d',strtotime($v->booking_to_date));
        	                       if($now>=$startdate&&$now<=$enddate){
        	                           // print_r($v);
        	                   //exit();
        	                       array_push($number,$v);
        	                       //print_r($placeList);
        	                       $placeList[0]->noOfBookings=count($number);
        	                       }else{
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
    }*/
    
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
                            $this->notificationallApiBuilding($booking[0], 'Booking Verified', $message, '3', '1',false);
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
    
    public function notificationallApiBuilding($b, $title, $body, $screen, $notifyType,$insertoDB) // this function is uses firebase api to send notification.   bool $insertoDB =true or false
    {
        // $buildingId = 394;
        // $societyId = 14;
        $getUserTopic = $this
            ->db
            ->select('notifn_topic')
            ->from('ci_users')
            ->where('id', $b['user_id'])
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

            $extraNotificationData = ['title' => $title, 'body' => $body, 'screen' => $screen, 'bookingid' => $b['id'], "click_action" => "FLUTTER_NOTIFICATION_CLICK"];

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
                        "booking_id" => $b['id'],
                        "user_id" => $b['user_id'],
                        "place_id" => $b['place_id'],
                        "slot_id" => $b['slot_id']
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
        $getVerifierList=$this->db->select('*')->from('tbl_verifier_place')->where('place_id',$placeid)->where('isDeleted','0')->get()->result();
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
	            $isCheckedOut = false;
	            $alreadyCheckedout = $this->db->select('*')->from('ci_booking_check')->order_by('id desc')
	                                    ->where('booking_id',$booking_id)->where('created_at',date('Y-m-d'))->get()->result_array();
	           if(count($alreadyCheckedout)>0){
	               if($alreadyCheckedout[0]['check_out']=='0000-00-00 00:00:00'){
	                   $insertcheckout= $this->db->where('booking_id ',$booking_id)
	            ->where('created_at ',date('Y-m-d'))
	            ->update('ci_booking_check',array('check_out'=>date("Y-m-d H:i:s"),
	                   'updated_at'=>date("Y-m-d H:i:s"),'check_type'=>'1'));
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
                    
        if ($this->form_validation->run()) 
        {
            $verifier_id = $this->security->xss_clean($this->input->post('verifier_id'));
            
            $checkVerifier= $this->db->select('*')->from('ci_admin')
            ->where('admin_id',$verifier_id)->where('admin_role_id','3')
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
                if(count($checkVerifierLogin)>0){
                    $isverfierLogin=true;
                    $verifierLoginData = array('login_time'=>(String)$checkVerifierLogin[0]['login_time'],
                                    'logout_time'=>(String)$checkVerifierLogin[0]['logout_time'],
                                    'status'=>(String)$checkVerifierLogin[0]['status'], //1=login,0=logout 
                                    'created_at'=>(String)$checkVerifierLogin[0]['created_at']);
                    // $verifierLoginData=$checkVerifierLogin[0];
                    // print_r($verifierLoginData);
                    // die;
                    $getverifierplaceList = $this->db->select('*')->from('tbl_verifier_place')
                    ->where('verifier_id',$verifier_id)->where('isDeleted','0')->get()->result_array();
                    // print_r($getverifierplaceList);
                    $allBookingList=[];
                    $followUpBookingList=[];
                    $unVerifiedBookingList=[];
                    $dashboarditemList=[array('id'=>1,'itemName'=>'followUpBooking','title'=>'FollowUp Bookings'),
                    array('id'=>2,'itemName'=>'unVerifiedBooking','title'=>'Unverified Bookings')];
                    
                    if(count($getverifierplaceList)>0){
                        foreach($getverifierplaceList as $verifierPlace)
                        {
                             $bookingList = $this->db->select('*')->from('ci_booking')->where('place_id',$verifierPlace['place_id'])->where('booking_status','0')->order_by('id desc')->get()->result_array();
                        
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
                        ->where('id',$getverifierplaceList[0]['place_id'])->where('place_status','1')
                        ->where('is_deleted','0')->get()->result_array();
                        if(count($placeDetails)>0){
                            /*
        	id  			placename	place_address					no_of_slots		
         	place_status 0: Inactive 1: active 	latitude	longitude*/
                            $placeDetails = array('id'=>(String)$placeDetails[0]['id'],
                            'placename'=>(String)$placeDetails[0]['placename'],
                            'placeaddress'=>(String)$placeDetails[0]['place_address'],
                            'noOfSlots'=>(String)$placeDetails[0]['no_of_slots'],
                            'isactive'=>$placeDetails[0]['place_status']==0?false:true,
                            'lat'=>(String)$placeDetails[0]['latitude'],
                            'long'=>(String)$placeDetails[0]['longitude'],
                            'isplaceconnected'=>true);
                            $mesg = array('status' => true,
                                    'dashboardItems'=>$newDashBoardList,
                                    'placeDetails'=>$placeDetails,
                                    'islogin'=>$isverfierLogin,
                                    'verifierLoginData'=>$verifierLoginData,
                                    'verifierDetails'=>$verifierData,
                                    'message' => 'Dashboard data');
                                    echo json_encode($mesg);
                        }else{
                            $mesg = array('status' => false,
                                    'dashboardItems'=>[],
                                    'placeDetails'=>$placeDetails,
                                    'islogin'=>$isverfierLogin,
                                    'verifierLoginData'=>$verifierLoginData,
                                    'verifierDetails'=>$verifierData,
                                    'message' => 'No such place present');
                                    echo json_encode($mesg);
                        }
                        
                        
                    }else{
                        $mesg = array('status' => false,
                                    'dashboardItems'=>[],
                                    'placeDetails'=>$placeDetails,
                                    'islogin'=>$isverfierLogin,
                                    'verifierLoginData'=>$verifierLoginData,
                                    'verifierDetails'=>$verifierData,
                                    'message' => 'Verifier not assigned to any place');
                                    echo json_encode($mesg);
                        
                    }
                }else{
                     $mesg = array('status' => false,
                                'dashboardItems'=>[],
                                'placeDetails'=>$placeDetails,
                                'islogin'=>$isverfierLogin,
                                'verifierLoginData'=>$verifierLoginData,
                                'verifierDetails'=>$verifierData,
                                'message' => 'No login');
                                echo json_encode($mesg);
                }
            }else{
                $mesg = array('status' => false,
                                'dashboardItems'=>[],
                                'placeDetails'=>$placeDetails,
                                'islogin'=>$isverfierLogin,
                                'verifierLoginData'=>$verifierLoginData,
                                'verifierDetails'=>$verifierData,
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
                if($mincalculate<=55){
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
    
    public function verifier_login_out()
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
                        $mesg = array('status'=>false,
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
                        $mesg = array('status'=>false,
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
            
            $getverifierplaces = $this->db->select('*')->from('tbl_verifier_place')->where('verifier_id',$verifier_id)->where('isDeleted','0')->get()->result_array();
            
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
         $timewise_new=[];
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
                    if($fromDate_s<=$currendate_fulld&&$toDate_s>=$currendate_fulld)
                    {
                        $data=$this->verifier_bookingDetails_logic($booking[0]);
                        $data['iscompletedTime']=false;
                        $data['checkData']=$checkData;
                       
                        array_push($timewise_new,$data);
                    }
                    else if($fromDate_s<$currendate_fulld&&$toDate_s<$currendate_fulld)
                    {
                         $data=$this->verifier_bookingDetails_logic($booking[0]);
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
                    if($fromDate_s<=$currendate_fulld&&$toDate_s>=$currendate_fulld)
                    {
                        $data=$this->verifier_bookingDetails_logic($booking[0]);
                        $data['iscompletedTime']=false;
                        $data['checkData']=$checkData;
                        //  $data['checkType']=$checkData['check_type'];
                        array_push($timewise_new,$data);
                    }
                    else if($fromDate_s<$currendate_fulld&&$toDate_s<$currendate_fulld)
                    {
                        $data=$this->verifier_bookingDetails_logic($booking[0]);
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
            }
            // print_r($timewise_new);
            if(count($timewise_new)>0){
             $msg = array('status' => true, 
                'message' => "Booking Details.", 
                'bookings' => $timewise_new);
                echo json_encode($msg);
            }else{
                $msg = array('status' => false, 
                'message' => "No Booking Details.", 
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
                
                $getEnforcerPlace = $this->db->select('*')->from('tbl_enforcer_place')->where('place_id',$d['place_id'])->get()->result();
                
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
                
                    // if(in_array($timewise[$n]['bookingId'] , $verify_status)){
                        $this->db->select('booking_id, verify_status');
                        $this->db->from('ci_booking_verify');
                        $this->db->where('booking_id', $d['bookingId']);
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
                        
                    // }
                    // else {
                    //         $d['verify_status'] = "2";
                    // }
                    return $d;
            
    }
 //Testrk   
}
?>