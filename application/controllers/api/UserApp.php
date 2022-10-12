<?php
ini_set("memory_limit","-1");
defined('BASEPATH') or exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET,POST, OPTIONS");

class UserApp extends CI_Controller
{
    // Live apis
    public function __construct()
     { 
        parent::__construct();
        $this->load->database();
        $this->load->library('encryption');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('upload');
    }
    
    function _returnSingle($err) 
    {
		foreach ($err as $key => $value) {
			return $err[$key];
		}
	}
    // to maintain session 
    public function tokenVerify($token) //token
     {
            $jwt = new JWT();
            $jwtsecretkey = 'parkingAdda_user@2021'; //sceret key for token
            $data = $jwt->decode($token, $jwtsecretkey, true);
            /**/
            // print_r($data);
            $checkAuthoriz = $this->db->
            select('id,username,email,mobile_no,role,is_verify,token,device_id,device_type')
            ->from('ci_users')->where('id',$data->id)->where('is_active','1')->get()->result();
            if(count($checkAuthoriz)>0){
                if($checkAuthoriz[0]->token==$token){
                    return true;   
                }else{
                return false;    
                }
                return true;
            }else{
              return false;
            }
         
     }
	
	public function tokenDecodeData($token) //token
	 {
        $jwt = new JWT();
        $jwtsecretkey = 'parkingAdda_user@2021'; //sceret key for token
        $data = $jwt->decode($token, $jwtsecretkey, true);
        return $data;
        
	}
	
    public function login_user() //login
     {
       
        $this->form_validation->set_rules('phone_no', 'Phone Number', 'numeric|exact_length[10]|required');
 
        if ($this->form_validation->run()) 
        {

            // POST data
            $phone_no = $this->security->xss_clean($this->input->post('phone_no'));
            $device_id = $this->security->xss_clean($this->input->post('deviceId'));
            $device_type = $this->security->xss_clean($this->input->post('deviceType'));
            $app_version = $this->security->xss_clean($this->input->post('app_version'));
            $app_build_no = $this->security->xss_clean($this->input->post('app_build_no'));
            
            $this->db->where('mobile_no', $phone_no)->where('is_active','1');
            $check_phone = $this->db->get('ci_users');

            // check phone no in database.
            if ($check_phone->num_rows() > 0) {
                // exist

                $result = $this->db->select('id,firstname,lastname,email,mobile_no,image,role,created_at,updated_at')->from('ci_users')
                ->where('mobile_no', $phone_no)->where('is_active','1')->get()->result_array();

              

                $jwt = new JWT();
                $jwtsecretkey = 'parkingAdda_user@2021'; //sceret key for token
                $token = $jwt->encode($result['0'], $jwtsecretkey, 'HS256');
                // print($token);
                // exit();
             /*   $update_data = [
                    'is_verify' => '1',
                    'is_active'=>'1',
                    'token'=>$token,
                    'notifn_topic'=>$phone_no.'MPCUser',
                    'device_type'=>$device_type,
                    'device_id'=>$device_id,
                    'updated_at' => date("Y-m-d h:i:sa")
                ];*/
                
                 if($app_version!=''&&$app_build_no!=''){
                     $update_data = ['is_verify' => '1', 'is_active' => '1', 'token' => $token, 'notifn_topic' => $phone_no . 'MPCUser', 
                'device_type' => $device_type, 'device_id' => $device_id, 'updated_at' => date("Y-m-d h:i:sa"),
                'app_version'=>$app_version,'app_build_no'=>$app_build_no
                ];
                }else{
                $update_data = ['is_verify' => '1', 'is_active' => '1', 'token' => $token, 'notifn_topic' => $phone_no . 'MPCUser', 
                'device_type' => $device_type, 'device_id' => $device_id, 'updated_at' => date("Y-m-d h:i:sa") ];
                }
                $this->db->where('mobile_no', $phone_no)->where('is_active','1');
             
                $this->db->update('ci_users', $update_data);
                

                $msg = array('status' => true, 'message' => 'Login Successfully..!!','token'=>$token);
                echo json_encode($msg);
            } else {
                // not exist
                $msg = array('status' => false, 'message' => 'Kindly Register Yourself..','token'=>'');
                echo json_encode($msg);
            }
        } else {
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'token'=>'');
            echo json_encode($msg);
        }
            
        // }
    }
    
    public function user_details() //user details for session in app
     {
        $this->form_validation->set_rules('contact_no', 'User Mobile No.', 'required');  
        
        
        if ($this->form_validation->run()) {
            
            $contact_no = $this->security->xss_clean($this->input->post('contact_no'));
            
                        
            
            $data= array(
                        "id"=> "0",
                        "name"=> "",
                        "email"=> "",
                        "phone_no"=> "",
                        "image"=> "",
                        "role_id"=> "",
                        "verify"=> "",
                        "is_deleted"=> "",
                        "created_date"=> "",
                        "updated_date"=> "",
                        "device_id"=> "",
                        "device_type"=> "",
                        "mac_address"=>"",
                        "notifn_topic"=> "",
                        "token"=> ""
                        // "advertisement"=>[]
                        );
            
            $user = $this->db->select('*')->from('ci_users')->where('mobile_no',$contact_no)->where('is_active','1')->get()->result_Array();
            // check if data present of not
            if(count($user)>0){
                $msg = array('status' => true, 'data' => $user[0],'message' => 'User details.','session'=>'1');
                echo json_encode($msg);
            }
            else {
                $msg = array('status' => false , 'data' => $data,'session'=>'1','message' => 'User details.');
                 echo json_encode($msg);
            }
            
        
        }
        else {
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()), 'data' => [],'session'=>'1');
            echo json_encode($msg);
        }
            
    
        
    }
    
    public function add_user_info() //registration
     {
        $this->form_validation->set_rules('number', 'Contact no.', 'required');
        $this->form_validation->set_rules('firstname', 'Name', 'required');
        $this->form_validation->set_rules('lastname', 'Name', 'required');

        if ($this->form_validation->run()) {
                $number = $this->security->xss_clean($this->input->post('number'));
                $firstname = $this->security->xss_clean($this->input->post('firstname'));
                $lastname = $this->security->xss_clean($this->input->post('lastname'));
                
                $emailId = $this->security->xss_clean($this->input->post('emailId'));
                $device_id = $this->security->xss_clean($this->input->post('device_id'));
                $device_type = $this->security->xss_clean($this->input->post('device_type'));
                $referenced_by = $this->security->xss_clean($this->input->post('referenced_by'));
                $terms_cond = $this->security->xss_clean($this->input->post('terms_cond'));
                $app_version = $this->security->xss_clean($this->input->post('app_version'));
                $app_build_no = $this->security->xss_clean($this->input->post('app_build_no'));
                
                
                //check user is already registered or not
                $getuser = $this->db->select('*')->from('ci_users')->where('mobile_no', $number)->where('is_active','1')->get()->result(); 
                $getuseremail =$emailId==''? []:$this->db->select('*')->from('ci_users')->where('email', $emailId)->where('is_active','1')->get()->result();
                
                if($referenced_by!=''||$referenced_by!=null){
                    $userReference_by = $this->db->Select('*')->from('ci_users')->where('referal_code',$referenced_by)->get()->result();
                     if(count($userReference_by)<=0){
                        $msg = array('status' => false, 'message' => 'Invalid Reference Code', 'token' => '');
                        echo json_encode($msg); 
                        exit();
                    }
                    
                }
            
            //check user number is already registered or not
                if(count($getuser)<=0){
                    //check user email is already registered or not
                            if(count($getuseremail)<=0){
                                
                                $getTermsConditionId=$this->db->select('*')->from('tbl_about_us')->where('module','1')
                                ->where('type','1')
                                ->where('is_deleted','1')->order_by('id desc')
                                ->get()->result_array();
                                $termsCondtnId = (String)count($getTermsConditionId)>0?$getTermsConditionId[0]['id']:0;
                                
                                $this->db->insert('ci_users',array('mobile_no'=>$number,
                                        'email'=>$emailId,
                                        // 'role'=>'10',
                                        'is_verify'=>'1',
                                        'device_id'=>$device_id,
                                        'device_type'=>$device_type,
                                        'notifn_topic'=>$number.'MPCUser',
                                        'firstname'=>$firstname,
                                        'lastname'=>$lastname,
                                        'username'=>$firstname.$lastname,
                                        'terms_condition'=>$terms_cond!=''?$terms_cond:1,
                                        'terms_conditn_id'=>$terms_cond!=''?$termsCondtnId:0,
                                        'app_version'=>$app_version,
                                        'app_build_no'=>$app_build_no
                                ));
                                $user_id = $this->db->insert_id();
                                //register user in wallet table
                                $this->db->insert('ci_wallet_user',array('user_id'=>$user_id,'amount'=>'50'));
                                
                                $walletid = $this
                                            ->db
                                            ->insert_id();
                    
                            $inserData1 = array(
                                        "wallet_id" => $walletid,
                                        "user_id" => $user_id,
                                        "amount" => '50',
                                        "status" => '1',
                                        "payment_type" => '0',
                            'last_wallet_amount'=>'0'
                                    );
                                    
                                $this->wallet_history_log($inserData1);
                                
                                //jwt token for security
                                $result = $this->db->select('id,firstname,lastname,email,mobile_no,image,role,created_at,updated_at')->from('ci_users')
                                ->where('mobile_no', $number)->where('is_active','1')->get()->result_array();
                                // print_r($result);
                                // exit();
                                $jwt = new JWT();
                                $jwtsecretkey = 'parkingAdda_user@2021'; //sceret key for token
                                $token = $jwt->encode($result['0'], $jwtsecretkey, 'HS256');
                                
                                // insert referal code of user.
                                $referalCode = $this->create_referalCode();
                                $this->db->where('mobile_no', $number)->where('is_active','1');
                                $this->db->update('ci_users',array('token'=>$token,'referal_code'=>$referalCode,'referenced_by'=>$referenced_by));
                                
                                $this->insertReferalAmountToWallet($referalCode,$referenced_by);  //need to uncomment when table is created
                                
                                $msg = array('status' =>true, 'message' => 'Info successfully inserted.', 'token' => $token);
                                echo json_encode($msg);
                            }
                         else
                         {
                                $msg = array('status' => false, 'message' => 'Email Id alredy registered', 'token' => '');
                                echo json_encode($msg); 
                         }
                }
                else
                {
                    $msg = array('status' => false, 'message' => 'User alredy registered', 'token' => '');
                    echo json_encode($msg);    
                }
        }
         else {
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()));
            echo json_encode($msg);
        }
    }
    
    //profile module starts here
    
    public function user_profile() // For profile API  
     {
          
                    $this->form_validation->set_rules('token', 'Token', 'required');
                    $data = array(
                                    'id' => '',
                                    'first_name' => '',
                                    'last_name' =>  '',
                                    'email' => '',
                                    'phone_no' => '',
                                    'image' => '',
                                    'verify' => '',
                                    'role_id' => '',
                                    'notifn_topic'=>'',
                                    'Message' => 'List of Vehicle',
                                    'car_details'=>[]
                                );
                    if ($this->form_validation->run())
                        {
            
                            $token = $this->security->xss_clean($this->input->post('token'));
                            $verifyToken = $this->tokenVerify($token);
            
                            if($verifyToken==true)
                                 {
                                
                                $tokenData = $this->tokenDecodeData($token);
                                // print_r($tokenData);
                                $this->db->where('id', $tokenData->id);
                                $this->db->where('is_verify', '1');
                                $this->db->where('is_active', '1');
                                $check_token = $this->db->get('ci_users');
                                $user_data = $check_token->result_array();
                                
                                // check table user
                                if ($check_token->num_rows() > 0) 
                                {
                                    
                                    $user_id = $user_data['0']['id'];
                                        $data = array( 
                                                    'id' => $user_data['0']['id'],
                                                    'first_name' => $user_data['0']['firstname'],
                                                    'last_name' =>  $user_data['0']['lastname'],
                                                    'email' => $user_data['0']['email'],
                                                    'phone_no' => $user_data['0']['mobile_no'],
                                                    'image' => $user_data['0']['image'],
                                                    'verify' => $user_data['0']['is_verify'],
                                                    'role_id' => $user_data['0']['role'],
                                                    'notifn_topic'=>$user_data['0']['notifn_topic'],
                                                    'Message' => 'List of Vehicle'
                                                    // 'car_details'=>[]
                                                );
                                    $this->db->where('user_id', $user_id);
                                    $this->db->where('is_deleted', '0');
                                    
                                    $check_car_details = $this->db->get('ci_car_details');
                                    
                                        // check car details
                                        if ($check_car_details->num_rows() > 0) 
                                        {
                                                // exist
                                                $fetch_car = $check_car_details->result_array(); //get car details
                                                $data['status'] = True;
                                                
                                                // $data['car_details']=[];
                                                $data['car_details'] = array();
                                                if (!empty($fetch_car)) {
                                                    foreach ($fetch_car as $car) {
                                                        $data['car_details'][] = $car;
                                                    }
                                                }
                            
                            
                            
                                                echo json_encode(array("status"=>true,"message"=>"profile data","data"=>$data,'session'=>'1'));
                                        }
                                        else 
                                        {
                                                // not exist
                                                $user_profile['status'] = true;
                                                $user_profile['message'] = 'No vehicle Register';
                                                $user_profile['session'] = '1';
                                                $user_profile['data'] = $data;
                                                $user_profile['data']['car_details']=[];
                                                echo json_encode($user_profile);
                                        }
                                        
                                    
                                } 
                                else 
                                {
                                    $msg = array('status' => false, 'message' => 'Invalid Token or User Must Not be verify','session'=>'0','data'=>$data);
                                    echo json_encode($msg);
                                }
                            } 
                            else 
                                 {
                                $msg = array('status' => false, 'message' => 'Accessdenied to you.','session'=>'0','data'=>$data);
                                echo json_encode($msg);
                            }
                        
                    }
                    else
                        {
                        $msg = array('status' => false, 'message' => $this->_returnSingle($errorMsg),'session'=>'1','data'=>$data);
                        echo json_encode($msg);
                    }
                
    }
    
    public function update_profile() //update profile  API
     {
        $this->form_validation->set_rules('firstname', 'firstname', 'required');
        $this->form_validation->set_rules('lastname', 'lastname', 'required');
        $this->form_validation->set_rules('token', 'token', 'required');
       


        if ($this->form_validation->run()) {
            
                    $token = $this->security->xss_clean($this->input->post('token'));
                    $firstname = $this->security->xss_clean($this->input->post('firstname'));
                    $lastname = $this->security->xss_clean($this->input->post('lastname'));
                    $verifyToken = $this->tokenVerify($token);
                    
                    if($verifyToken==true){
                        
                        $tokenData = $this->tokenDecodeData($token);
                        $this->db->where('id', $tokenData->id);
                        $this->db->where('is_verify', '1');
                        $this->db->where('is_active', '1');
                        $check_token = $this->db->get('ci_users'); //check token


                        if ($check_token->num_rows() > 0) {
            
                            $user_data = $check_token->result_array();
            
                            $user_id = $user_data['0']['id'];
            
                            $image_base64 = $this->input->post('image');
                            $image_extension = $this->input->post('image_extension'); //i will receive in .jpg , .png , etc
                            
                          
                            $path='';
                            $userdata;
                            if($image_base64!=''){
                                $image = base64_decode($image_base64);
                                
                                $imagename = md5(uniqid(rand(), true));
                                $filename = $imagename .'.' .'png';
                                $path = base_url()."uploads/".$filename;
                                $pathtosave ="./uploads/".$filename;
                                file_put_contents($pathtosave, $image);
                                $userdata = array(
                                'username' => $firstname.$lastname,
                                'firstname' => $firstname,
                                'lastname' => $lastname,//firstname
                                'image' => $path
                            );
                            }else{
                                $userdata = array(
                                'username' => $firstname.$lastname,
                                'firstname' => $firstname,
                                'lastname' => $lastname,//firstname
                            );
                            }
                            
                            $this->db->where('id', $user_id)->where('is_active','1');
                            $this->db->update('ci_users', $userdata); //update table userdata
                           
                            $msg = array('status' => true, 'message' => 'Sucessflly Updated Profile','session'=>'1');
                            echo json_encode($msg);
                        } else {
                            $msg = array('status' => false, 'message' => 'User not found.','session'=>'0');
                            echo json_encode($msg);
                        }
                    } 
                    else {
            $msg = array('status' => false, 'message' => 'Session expired','session'=>'0');
            echo json_encode($msg);
        }
        } else {
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1');
            echo json_encode($msg);
        }
        
    }
    
    // wallet module api start here
  
  public function wallet_api() // This api give details of wallet amount
    
    {

        $this
            ->form_validation
            ->set_rules('token', 'Token', 'required');

        if ($this
            ->form_validation
            ->run())
        {

            $token = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('token'));

            $verifyToken = $this->tokenVerify($token);

            if ($verifyToken == true)
            {

                $tokenData = $this->tokenDecodeData($token);
                // print_r($tokenData);
                $amount = $this
                    ->db
                    ->select('*')
                    ->from('ci_wallet_user')
                    ->where('user_id', $tokenData->id)
                    ->get()
                    ->result();
                $walletHistory = $this
                    ->db
                    ->select('*')
                    ->from('ci_wallet_history')
                    ->where('user_id', $tokenData->id)
                    ->where('status!=', '4')
                    ->order_by('id DESC')
                    ->get()
                    ->result();
                $wallet_list = [];
                // $razorpay_live_key= $this->encryptData("rzp_live_go5tICS12in2BY");
                // $razorpay_test_key= $this->encryptData("rzp_test_25fQbysZaqmc6L");
                $razorpay_live_key= "rzp_live_go5tICS12in2BY";
                $razorpay_test_key= "rzp_test_25fQbysZaqmc6L";
                $paymentgateway_list = [];
                foreach ($walletHistory as $wallet)
                {
                    $wallet->placeName = '';
                    $wallet->paymentForBooking = false; // 1=User account to wallet
                    if ($wallet->booking_id != '0')
                    {
                        $wallet->paymentForBooking = true; // 1= wallet to booking
                        $placeDetails_id = $this
                            ->db
                            ->select('place_id,unique_booking_id')
                            ->from('ci_booking')
                            ->where('id', $wallet->booking_id)
                            ->get()
                            ->result();
                        if (count($placeDetails_id) > 0)
                        {
                            $placeDetails = $this
                                ->db
                                ->select('*')
                                ->from('ci_parking_places')
                                ->where('id', $placeDetails_id[0]->place_id)
                                ->get()
                                ->result();
                            if (count($placeDetails) > 0)
                            {
                                $wallet->placeName = $placeDetails[0]->placename;

                            }
                            $wallet->unique_booking_id = $placeDetails_id[0]->unique_booking_id;
                        }
                        array_push($wallet_list, $wallet);
                    }
                    else
                    {
                        array_push($paymentgateway_list, $wallet);
                    }

                }
                if (count($amount) > 0)
                {

                    $msg = array(
                        'status' => true,
                        'msg' => "wallet Amount!!",
                        'amount' => $amount[0]->amount,
                        'razor_key'=>array('live'=>$razorpay_live_key,'test'=>$razorpay_test_key),
                        'wallethistory' => $wallet_list,
                        'paymentGateway' => $paymentgateway_list,
                        
                        'session' => '1'
                    );
                    echo json_encode($msg);

                }
                else
                {
                    $msg = array(
                        'status' => true,
                        'msg' => "No data found. !!",
                        'amount' => '0',
                        'razor_key'=>array('live'=>$razorpay_live_key,'test'=>$razorpay_test_key),
                        'wallethistory' => $wallet_list,
                        'paymentGateway' => $paymentgateway_list,
                        
                        'session' => '1'
                    );
                    echo json_encode($msg);
                }

            }
            else
            {
                $msg = array(
                    'status' => false,
                    'message' => 'Session expired',
                    'wallethistory' => [],
                    'session' => '0',
                    'paymentGateway' => [],
                    'razor_key'=>array('live'=>'','test'=>''),
                    'amount' => '0'
                );
                echo json_encode($msg);
            }

        }
        else
        {
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors()) ,
                'wallethistory' => [],
                'paymentGateway' => [],
                'session' => '1',
                'razor_key'=>array('live'=>'','test'=>''),
                'amount' => '0'
            );
            echo json_encode($msg);
        }

    }
   /*  public function wallet_api() // This api give details of waalet amount  
     {
        
        
        $this->form_validation->set_rules('token', 'Token', 'required');  
        
        if ($this->form_validation->run()) 
        {
            
            $token = $this->security->xss_clean($this->input->post('token'));
            
            $verifyToken = $this->tokenVerify($token);
                    
            if($verifyToken==true){
                        
                $tokenData = $this->tokenDecodeData($token);
                // print_r($tokenData);
                $amount= $this->db->select('*')->from('ci_wallet_user')->where('user_id',$tokenData->id)->get()->result();
                $walletHistory = $this->db->select('*')->from('ci_wallet_history')->where('user_id',$tokenData->id)->where('status!=','4')
                ->order_by('id DESC')->get()->result();
                $wallet_list=[];
                $paymentgateway_list=[];
                foreach($walletHistory as $wallet){
                    $wallet->placeName ='';
                    $wallet->paymentForBooking =false; // 1=User account to wallet
                    if($wallet->booking_id!='0'){
                        $wallet->paymentForBooking =true; // 1= wallet to booking 
                    $placeDetails_id = $this->db->select('place_id,unique_booking_id')->from('ci_booking')->where('id',$wallet->booking_id)->get()->result();
                    if(count($placeDetails_id)>0){
                        $placeDetails = $this->db->select('*')->from('ci_parking_places')->where('id',$placeDetails_id[0]->place_id)->get()->result();
                        if(count($placeDetails)>0){
                            $wallet->placeName =$placeDetails[0]->placename;
                            
                        }
                         $wallet->unique_booking_id =$placeDetails_id[0]->unique_booking_id;
                    }
                    array_push($wallet_list,$wallet);
                }else{
                    array_push($paymentgateway_list,$wallet);
                }
                
                }
                if(count($amount)>0){
                
                    $msg = array('status' => true, 'msg' => "wallet Amount!!", 'amount' =>$amount[0]->amount ,'wallethistory'=>$wallet_list,'paymentGateway'=>$paymentgateway_list,'session'=>'1');
                    echo json_encode($msg);
                
                }else {
                    $msg = array('status' => true, 'msg' => "No data found. !!", 'amount' => '0','wallethistory'=>$wallet_list,'paymentGateway'=>$paymentgateway_list,'session'=>'1');
                    echo json_encode($msg);
                }
            
            }
            else
            {
                $msg = array('status' => false, 'message' => 'Session expired','wallethistory'=>[],'session'=>'0','paymentGateway'=>[], 'amount' => '0');
                echo json_encode($msg);
            }
            
        }
        else
        {
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'wallethistory'=>[],'paymentGateway'=>[],'session'=>'1', 'amount' => '0');
            echo json_encode($msg);
        }
        
    
     }*/
    
    public function wallet_history() // This api give wallet history of user 
     {
         $this->form_validation->set_rules('token', 'Token', 'required');  
        
        if ($this->form_validation->run()) {
            $token = $this->security->xss_clean($this->input->post('token'));
            
            $verifyToken = $this->tokenVerify($token);
                    
            if($verifyToken==true){
                        
                $tokenData = $this->tokenDecodeData($token);
                // print_r($tokenData);
                
                $walletHistory = $this->db->select('*')->from('ci_wallet_history')->where('user_id',$tokenData->id)
                ->order_by('id DESC')->get()->result();
                $wallet_list=[];
                foreach($walletHistory as $wallet){
                    $wallet->placeName ='';
                    $wallet->paymentForBooking =false; // 1=User account to wallet
                    if($wallet->booking_id!='0'){
                        $wallet->paymentForBooking =true; // 1= wallet to booking 
                    $placeDetails_id = $this->db->select('place_id,unique_booking_id')->from('ci_booking')->where('id',$wallet->booking_id)->get()->result();
                    if(count($placeDetails_id)>0){
                        $placeDetails = $this->db->select('*')->from('ci_parking_places')->where('id',$placeDetails_id[0]->place_id)->get()->result();
                        if(count($placeDetails)>0){
                            $wallet->placeName =$placeDetails[0]->placename;
                            
                        }
                        $wallet->unique_booking_id =$placeDetails_id[0]->unique_booking_id;
                    }
                }
                array_push($wallet_list,$wallet);
                }
                if(count($wallet_list)>0){
                
                    $msg = array('status' => true, 'msg' => "wallet Amount!!" ,'wallethistory'=>$wallet_list,'session'=>'1');
                    echo json_encode($msg);
                
                }else {
                    $msg = array('status' => false, 'msg' => "No data found. !!",'wallethistory'=>[],'session'=>'1');
                    echo json_encode($msg);
                }
            
            }else{
            $msg = array('status' => false, 'message' => 'Session expired','wallethistory'=>[],'session'=>'0');
            echo json_encode($msg);
        }
            
        }else{
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'wallethistory'=>[],'session'=>'1');
            echo json_encode($msg);
        }
        
    
    }
    
    // public function insert_transact_log() // this api insert transaction wallet history  in onsuccess or failed.
    //  {
    //         $this->form_validation->set_rules('token', 'Token', 'required');
    //         $this->form_validation->set_rules('order_id', 'order_id', 'required');
    //         $this->form_validation->set_rules('payment_id', 'payment_id', 'required');
    //         $this->form_validation->set_rules('amount', 'amount', 'required'); 
    //         $this->form_validation->set_rules('status', 'status', 'required');
    //         $this->form_validation->set_rules('contact_no', 'contact_no', 'required');
            
    //         if ($this->form_validation->run()) {
                
    //             $token = $this->security->xss_clean($this->input->post('token'));
    //             $order_id = $this->security->xss_clean($this->input->post('order_id'));
    //             $payment_id = $this->security->xss_clean($this->input->post('payment_id'));
    //             $amount = $this->security->xss_clean($this->input->post('amount'));
    //             $status = $this->security->xss_clean($this->input->post('status'));
    //             $email_id = $this->security->xss_clean($this->input->post('email_id'));
    //             $contact_no = $this->security->xss_clean($this->input->post('contact_no'));
                
    //             $verifyToken = $this->tokenVerify($token);
                        
    //             if($verifyToken==true){
                            
    //                 $tokenData = $this->tokenDecodeData($token);
    //                 if($status=='1')
    //                     {
    //                         $get_amt = $this->db->select('*')->from('ci_wallet_user')->where('user_id',$tokenData->id)->get()->result();
    //                         if(count($get_amt)>0){
    //                                 $inserData=array(
    //                                     'order_id'=>$order_id,
    //                                     'payment_id'=>$payment_id,
    //                                     'amount'=>$amount,
    //                                     'status'=>$status,
    //                                     'email_id'=>$email_id,
    //                                     'contact_no'=>$contact_no,
    //                                     'user_id'=>$tokenData->id,
    //                                     'wallet_id'=>$get_amt[0]->id
    //                                 );
    //                                 $insertPayment = $this->db->insert('ci_transaction_history',$inserData);
    //                                 $new_amt =$get_amt[0]->amount+$amount;
    //                                 $this->db->where('id',$get_amt[0]->id)->update('ci_wallet_user',array('amount'=>(float)$new_amt));
    //                                 $message= 'Amount '.$amount.' has been Added to your wallet';
    //                                 $this->notificationForWallet($tokenData->id,'0','0','0', 'Wallet', $message,'4','3'); //3= money added to wallet , 4= wallet screen
                                    
    //                                 if($status=='1'){
    //                                     /* */
    //                                     $inserData1=array("wallet_id"=>$get_amt[0]->id,"user_id"=>$get_amt[0]->user_id,"amount"=>$amount,"status"=>'1',"payment_type"=>'1');
    //                                     // $insertPayment1 = $this->db->insert('ci_wallet_history',$inserData1);
    //                                     // $this->wallet_history_log($inserData1);
    //                                       $this->wallet_history_log($inserData1);
    //                                 }
    //                         }
    //                     }
    //                     $msg = array('status' => true, 'message' => 'Payment recorded','session'=>'1');
    //                     echo json_encode($msg);
                
    //                 }
    //             else
    //                 {
    //                     $msg = array('status' => false, 'message' => 'Session expired','session'=>'0');
    //                     echo json_encode($msg);
    //                 }
                
    //         }else {
    //         $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1');
    //         echo json_encode($msg);
    //     }
    // }
    public function insert_transact_log() // this api insert transaction wallet history  in onsuccess or failed.
    {
        $this
            ->form_validation
            ->set_rules('token', 'Token', 'required');
        $this
            ->form_validation
            ->set_rules('order_id', 'order_id', 'required');
        $this
            ->form_validation
            ->set_rules('payment_id', 'payment_id', 'required');
        $this
            ->form_validation
            ->set_rules('amount', 'amount', 'required');
        $this
            ->form_validation
            ->set_rules('status', 'status', 'required');
        $this
            ->form_validation
            ->set_rules('contact_no', 'contact_no', 'required');

        if ($this
            ->form_validation
            ->run())
        {

            $token = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('token'));
            $order_id = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('order_id'));
            $payment_id = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('payment_id'));
            $amount = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('amount'));
            $status = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('status'));
            $email_id = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('email_id'));
            $contact_no = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('contact_no'));

            $verifyToken = $this->tokenVerify($token);

            if ($verifyToken == true)
            {

                $tokenData = $this->tokenDecodeData($token);
               
                    $get_amt = $this
                        ->db
                        ->select('*')
                        ->from('ci_wallet_user')
                        ->where('user_id', $tokenData->id)
                        ->get()
                        ->result();
                    if (count($get_amt) > 0)
                    {
                        $getPaymenProcessingData = $this->db->select('*')->from('ci_transaction_history')
                        ->where('order_id',$order_id)->where('amount',$amount)->where('user_id',$tokenData->id)
                        ->where('status','0')->order_by('id DESC')
                        ->get()->result_array();
                        if(count($getPaymenProcessingData)>0)
                        {
                            
                            $checkpaymentInRazorpay = $this->verify_payment($payment_id,$getPaymenProcessingData[0]['is_live_payment'],$order_id);
                            // print($checkpaymentInRazorpay.' rk');die;
                            if($checkpaymentInRazorpay==1){
                            $inserData = array(
                                'order_id' => $order_id,
                                'payment_id' => $payment_id,
                                'amount' => $amount,
                                'status' => $status,
                                'email_id' => $email_id,
                                'contact_no' => $contact_no,
                                'user_id' => $tokenData->id,
                                'wallet_id' => $get_amt[0]->id,
                                'on_updated'=>date('Y-m-d H:i:s')
                            );
                            $insertPayment = $this
                                ->db->where('id',$getPaymenProcessingData[0]['id'])
                                ->update('ci_transaction_history', $inserData);
                            $new_amt = $get_amt[0]->amount + $amount;
                            $this
                                ->db
                                ->where('id', $get_amt[0]->id)
                                ->update('ci_wallet_user', array(
                                'amount' => (float)$new_amt
                            ));
                            $message = '₹ ' . $amount . ' has been added to your wallet';
                            $this->notificationForWallet($tokenData->id, '0', '0', '0', 'Wallet', $message, '4', '3'); //3= money added to wallet , 4= wallet screen
                            if ($status == '1')
                            {
                                /* */
                                $inserData1 = array(
                                    "wallet_id" => $get_amt[0]->id,
                                    "user_id" => $get_amt[0]->user_id,
                                    "amount" => $amount,
                                    "status" => '1',
                                    "payment_type" => '1',
                            'last_wallet_amount'=>$get_amt[0]->amount
                                );
                                // $insertPayment1 = $this->db->insert('ci_wallet_history',$inserData1);
                                // $this->wallet_history_log($inserData1);
                                $this->wallet_history_log($inserData1);
                                $lastWallet_insert_id = $this->db->insert_id();
                                
                                $offerDetails = $this->bonusAddInWallet($get_amt[0]->user_id,$amount);
                                // print_r($offerDetails);
                                if($offerDetails['offerwalletAmount']>0)
                                {
                                    $offerAmount=$offerDetails['offerwalletAmount'];
                                    $get_amt = $this->db->select('*')->from('ci_wallet_user')->where('user_id', $tokenData->id)->get()->result();
                                    if(count($get_amt)>0){
                                        // $walletAmount =$get_amt[0]['amount']; 
                                        $new_amt = $get_amt[0]->amount + $offerAmount;
                                        $this
                                            ->db
                                            ->where('id', $get_amt[0]->id)
                                            ->update('ci_wallet_user', array(
                                            'amount' => (float)$new_amt
                                        ));
                                        $message = '₹ ' . $offerAmount . ' has been added to your wallet for you wallet on recharge of Rs.'.$amount;
                                        $this->notificationForWallet($tokenData->id, '0', '0', '0', 'Wallet', $message, '4', '3'); //3= money added to wallet , 4= wallet screen
                                        
                                        $inserData1 = array(
                                                "wallet_id" => $get_amt[0]->id,
                                                "user_id" => $get_amt[0]->user_id,
                                                "amount" => $offerAmount,
                                                "status" => '1',
                                                "payment_type" => '0',
                                                "offer_id"=>$offerDetails['offerData']['id'],
                                                'last_wallet_amount'=>$get_amt[0]->amount,
                                                'payment_for'=>$lastWallet_insert_id
                                            );
                                // $insertPayment1 = $this->db->insert('ci_wallet_history',$inserData1);
                                // $this->wallet_history_log($inserData1);
                                $this->wallet_history_log($inserData1);
                                    }
                                }
                            }
                            
                            $msg = array(
                                    'status' => true,
                                    'message' => 'Payment successfully recorded',
                                    'session' => '1'
                                );
                                echo json_encode($msg);
                        }else{
                            $msg = array(
                                    'status' => false,
                                    'message' => 'Payment failed. Sorry for yor inconvenience.',
                                    'session' => '1'
                                    );
                            echo json_encode($msg);
                        }
                        }
                        else{
                            $msg = array(
                                    'status' => false,
                                    'message' => 'No such order is in process.',
                                    'session' => '1'
                                    );
                            echo json_encode($msg);
                        }
                    }
                    else{
                        $msg = array(
                            'status' => false,
                            'message' => 'User wallet not present',
                            'session' => '1'
                        );
                        echo json_encode($msg);
                    }
                        
                    
                
                /*$msg = array(
                    'status' => true,
                    'message' => 'Payment recorded',
                    'session' => '1'
                );
                echo json_encode($msg);*/

            }
            else
            {
                $msg = array(
                    'status' => false,
                    'message' => 'Session expired',
                    'session' => '0'
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
    
    public function verify_payment($payment_id,$isLive,$order_id) //through razorpay   ($isLive : 0=yes,1=no)
    {
        // $payment_id='pay_J7OA545gDvqyoq';
        // $isLive='1';
        $returnValue=0;
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/payments/$payment_id");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        
        // curl_setopt($ch, CURLOPT_USERPWD, '[YOUR_KEY_ID]' . ':' . '[YOUR_KEY_SECRET]');
        $isLive == '0' ? curl_setopt($ch, CURLOPT_USERPWD, 'rzp_live_go5tICS12in2BY' . ':' . 'ivr9QZMoeDOj2WEfACWUHIts') 
        : curl_setopt($ch, CURLOPT_USERPWD, 'rzp_test_25fQbysZaqmc6L' . ':' . 'IZveLQPTPanBdJ5mx4XWMlzL');
        
        $result = curl_exec($ch);
        // print_r($result);
        if (curl_errno($ch)) {
            // echo 'Error:' . curl_error($ch);
             $returnValue= 0;
        }else{
            $resultData = json_decode($result);
           /* if($resultData->error!=null){
                $returnValue= 0;
            }*/
            try{
                if (array_key_exists("error",$resultData))
                  {
                  $returnValue= 0;
                  }
                else
                  {
                       if($resultData->status=='captured'||$resultData->status=='authorized'){
                            if($isLive=='0'){
                                if($order_id==$resultData->order_id)
                                {
                                $returnValue= 1;
                                }
                                else{
                                    $returnValue= 0;
                                }
                            }else{
                                $returnValue= 1;
                            }
                            }else{
                                $returnValue= 0;
                            }
                      }
           
        }catch(Exception $e) {
          echo 'Message: ' .$e->getMessage();
        }
            
        }
        curl_close($ch);
        // print($returnValue);
        
        return $returnValue;

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
    // function issuecreate_sensor($deviceid,$isSensorIssue)
    function issuecreate_sensor()
    {       //engineering complaints
    //id 	device_id 	issue_type 0= senser not working,1=other 	status
    // $this->db->insert('ci_eng_complaint',array(
    //     'device_id'=>209,
    //     'issue_type'=>0
    //     ));
    $deviceid=209;
    $isSensorIssue=0;
        $sensorcomp= $this->db->select('*')->from('ci_eng_complaint')
        ->where('device_id',$deviceid)
        ->where('status','0')
        ->where('issue_type',$isSensorIssue)
        ->order_by('id DESC')
        // ->like('created_at', array('dates' => date('Y-m-d')))
        ->get()->result_array();
        
        if(count($sensorcomp)>0){
            
            $currentdate = date('Y-m-d');
            $compdate =date("Y-m-d",strtotime($sensorcomp[0]['created_at']));
            // $enddate_fulld=date("Y-m-d H:i:s", strtotime($getBooking[0]['booking_to_date'].' '.$getBooking[0]['reserve_to_time']));
            if($currentdate>$compdate){
                $this->db->insert('ci_eng_complaint',array(
                    'device_id'=>$deviceid,
                    'issue_type'=>$isSensorIssue
                    ));
            }
        }
        
        print_r($sensorcomp);
    }
  
    function create_referalCode()
    {
        // $i=0;
        $referalCode = random_string('alnum', 16);
        $getresult = $this->db->Select('*')->from('ci_users')->where('referal_code',$referalCode)->get()->result();
        if(count($getresult)>0){
            
            $this->create_referalCode();
            // echo '11212122122';
            
        }
        else{return $referalCode;
            
        }
    }
    
    function insertReferalAmountToWallet($referal_code,$referended_by)
    {
        
             $getReferalAmount = $this->db->select('*')->from('tbl_commons')->where('subject','referal_amount')->where('is_deleted','0')->get()->result(); //need to uncomment when table is created.
             $amount = count($getReferalAmount)>0?$getReferalAmount[0]->value:'0';
            // $amount=50;
            $getRegisteredUserDetail= $this->db->Select('*')->from('ci_users')->where('referal_code',$referal_code)->get()->result();
            $getReferenced_by_UserDetail= $this->db->Select('*')->from('ci_users')->where('referal_code',$referended_by)->get()->result();
        
            $getUserId = count($getRegisteredUserDetail)>0?$getRegisteredUserDetail[0]->id:'';
            $getUserId_referebcedBy = count($getReferenced_by_UserDetail)>0?$getReferenced_by_UserDetail[0]->id:'';
            //tbl_wallet_user
            $userUpdate= $this->db->where('user_id',$getUserId)->update('ci_wallet_user',array('amount'=>$amount));
            $message= '₹ '.$amount.' has been added to your wallet';
            $this->notificationForWallet($getUserId,'0','0','0', 'Wallet', $message,'4','3'); //3= money added to wallet , 4= wallet screen
        
            $getreferencedBy_walletDetail= $this->db->select('*')->from('ci_wallet_user')->where('user_id',$getUserId_referebcedBy)->get()->result();
            $referer_amount = count($getreferencedBy_walletDetail)>0?$getreferencedBy_walletDetail[0]->amount:'50';
            $referencedby_Update= $this->db->where('user_id',$getUserId_referebcedBy)->update('ci_wallet_user',array('amount'=>($referer_amount+$amount)));
            $message= '₹ '.$amount.' has been added to your wallet';
            $this->notificationForWallet($getUserId_referebcedBy,'0','0','0', 'Wallet', $message,'4','3'); //3= money added to wallet , 4= wallet screen
        
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
    
    public function car_List() //List of users registered cars
    {
      
     $this->form_validation->set_rules('token', 'Token', 'required');  
        $carsList=[];
        if ($this->form_validation->run()) {
            $token = $this->security->xss_clean($this->input->post('token'));
            
            $verifyToken = $this->tokenVerify($token);
                    
            if($verifyToken==true){
                        
            $tokenData = $this->tokenDecodeData($token);
              $carsList = $this->db->select('*')->from('ci_car_details')->where('user_id',$tokenData->id)->where('is_deleted','0')->get()->result();
              $walletDetails = $this->db->select('*')->from('ci_wallet_user')->where('user_id',$tokenData->id)->where('is_deleted','0')->get()->result_array();
              $walletbalance = (String)(count($walletDetails)>0?$walletDetails[0]['amount']:'0');
              if(count($carsList)>0){
                  $msg = array('status' => true, 'message' => 'List of Cars','session'=>'1','carList'=>$carsList,'walletBalance'=>$walletbalance);
                                        echo json_encode($msg);
              }else{
                  $msg = array('status' => true, 'message' => 'No data present','session'=>'1','carList'=>$carsList,'walletBalance'=>$walletbalance);
                                        echo json_encode($msg);
              }
             }else{
            $msg = array('status' => false, 'message' => 'Session expired','session'=>'0','carList'=>$carsList,'walletBalance'=>'0');
            echo json_encode($msg);
        }
            
             } else {
                        $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1','carList'=>$carsList,'walletBalance'=>'0');
                        echo json_encode($msg);
                    }
    }
    
    public function add_vehicle() //add vechicle API
    {
      
            date_default_timezone_set('Asia/Kolkata');
            $this->form_validation->set_rules('token', 'Token', 'required');
            $this->form_validation->set_rules('car_number', 'Car Number', 'required');
            // $this->form_validation->set_rules('car_name', 'Car Name', 'required');
            if ($this->form_validation->run()) {
    
    
                $token = $this->security->xss_clean($this->input->post('token'));
                $car_number = $this->security->xss_clean($this->input->post('car_number'));
                $car_name = $this->security->xss_clean($this->input->post('car_name'));
    // $token = $this->input->post('token');
                $verifyToken = $this->tokenVerify($token);
                
                if($verifyToken==true){
                    
                    $tokenData = $this->tokenDecodeData($token);
                    
                    $this->db->where('id', $tokenData->id);
                    $this->db->where('is_verify', '1');
                    $this->db->where('is_active', '1');
                    $check_token = $this->db->get('ci_users');
    
                if ($check_token->num_rows() > 0) {
                    $user_data = $check_token->result_array();
    
                    $user_id = $user_data['0']['id'];
    
                    $data = array(
                        'car_number' => $car_number,
                        'car_name'=>$car_name,
                        'user_id' => $user_id,
                        'is_deleted' => '0',
                        'created_date' => date("Y-m-d h:i:sa"),
                        'updated_date' => date("Y-m-d h:i:sa"),
                    );
    
                    $this->db->insert('ci_car_details', $data); //car details add
    
                    $msg['status'] = True;
                    $msg['message'] = 'Successfully Added Vehicle';
                    $msg['session'] = '1';
                    echo json_encode($msg);
                } else {
                    $msg = array('status' => false, 'message' => 'No user found.','session'=>'0');
                    echo json_encode($msg);
                }
                    
                }
                else{
                $msg = array('status' => false, 'message' => 'Session expired','session'=>'0');
                echo json_encode($msg);    
                }
            } else {
                $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1');
                echo json_encode($msg);
            }
    
        
    }
    
    public function delete_vechicle()  //delete particular vehicle API
    {
                $this->form_validation->set_rules('token', 'Token', 'required');
                $this->form_validation->set_rules('car_id', 'Car Id', 'required');
        
                if ($this->form_validation->run()) {
        
                    $token = $this->security->xss_clean($this->input->post('token'));
                    $verifyToken = $this->tokenVerify($token);
                    
                    if($verifyToken==true){
                        
                        $tokenData = $this->tokenDecodeData($token);
                    $this->db->where('id', $tokenData->id);
                    $this->db->where('is_verify', '1');
                    $this->db->where('is_active', '1');
                    $check_token = $this->db->get('ci_users');
        
                    if ($check_token->num_rows() > 0) {
        
                        $user_data = $check_token->result_array();
        
                        $user_id = $user_data['0']['id'];
        
        
                        $car_id = $this->security->xss_clean($this->input->post('car_id'));
                        $this->db->where('user_id', $user_id);
                        $this->db->where('id', $car_id);
                        $check_token = $this->db->get('ci_car_details');
                        if ($check_token->num_rows() > 0) {
                            $data = [
                                'is_deleted' => '1',
                            ];
                            $this->db->where('user_id', $user_id);
                            $this->db->where('id', $car_id);
                            $this->db->update('ci_car_details', $data);
                            $msg = array('status' => true, 'message' => 'Successfully Deleted','session'=>'1');
                            echo json_encode($msg);
                        } else {
                            $msg = array('status' => false, 'message' => 'The Car Id Does Not Exist','session'=>'1');
                            echo json_encode($msg);
                        }
                    } else {
                        $msg = array('status' => false, 'message' => 'Invalid Token','session'=>'1');
                        echo json_encode($msg);
                    }
                        
                    } else{
                    $msg = array('status' => false, 'message' => 'Session expired','session'=>'0');
                    echo json_encode($msg);    
                    }
                } else {
                    $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1');
                    echo json_encode($msg);
                }
            
        
    }
    
    public function dashboard_booking() //This api contains places list with users current day booking
    {

        date_default_timezone_set('Asia/Kolkata');
        $this
            ->form_validation
            ->set_rules('lat', 'Latitude', 'required|numeric', array(
            'required' => 'Please Provide User latitiude'
        ));
        $this
            ->form_validation
            ->set_rules('long', 'Longitude ', 'required|numeric', array(
            'required' => 'Please Provide User Longitude'
        ));
        $this
            ->form_validation
            ->set_rules('status', 'Status ', 'required');
        $this
            ->form_validation
            ->set_rules('distance', 'Distance ', 'required|numeric', array(
            'required' => 'Please Provide Distance',
            'numeric' => 'Please Provide Distance in Meters'
        ));
        $bookings = [];
        $bookingList = [];
        $advertisementList = [];
        $termsAndConditn=array('id'=>'',
                            'module'=>'',
                            'title'=>'',
                            'details'=>'',    
                            'type'=>'',
                            'showUpdateDialog'=>false);
        $updateData=array('version'=>'',
                            'build_no'=>'',
                            'app_url'=>'',
                            'update_type'=>'',     //0=flexible update,1=force update 
                            'whats_new'=>'',
                            'notice_count'=>'',
                            'mobiletype'=>'');
         $offerDetails =array('offerheader'=>'','offerdesc'=>'');
          $isOffersOn =false;
        if ($this
            ->form_validation
            ->run())
        { // validations
            

            $lat1 = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('lat')); //user

            $long1 = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('long')); //user
            $token = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('token'));
                
            $app_version = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('app_version'));
                
            $app_build_no = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('app_build_no'));
                
            $device_type = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('device_type'));

            $unit = 'M'; //units K,M
            $current_date = date('Y-m-d');

            // print($current_time);
            $distance = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('distance')); //distance
            $type = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('status')); //0 =daily, passes , 1 = passes
            
        //   $one='1.02.55';
        //   $two='1.02.0';
        //   if($one>$two){
        //       echo 'yes';
        //       echo true;
        //   }else{
        //       echo 'no';
        //       echo false;
        //   }
        //   die;
        $isAppUpdate=false;
        $user_id='0';
        
        
            if (!empty($token))
            {
                $tokenData = $this->tokenDecodeData($token);
                $user_id=$tokenData->id;
                $bookings = $this
                    ->db
                    ->query("SELECT ci_booking.id,ci_booking.user_id,ci_booking.place_id,
                           ci_booking.slot_id,ci_booking.booking_status,booking_from_date,booking_to_date,from_time,
                           to_time,paid_status,booking_type,cost,places1.placename,places1.place_address,slot1.slot_no  FROM ci_booking
                        JOIN ci_parking_places as places1 ON  ci_booking.place_id = places1.id 
                        JOIN ci_parking_slot_info as slot1 ON  ci_booking.slot_id = slot1.slot_no
                            where user_id= '$tokenData->id' AND  DATE(booking_from_date) <= '$current_date' AND  DATE(booking_to_date) >= '$current_date'
                        AND ci_booking.booking_status='0'    AND  ci_booking.is_deleted='0' 
                          ")->result_array();

                foreach ($bookings as $b)
                {

                    if ($b['booking_from_date'] == $b['booking_to_date'])
                    {
                        $current_time = date('H:i:s');
                        $totime_d = date('H:i:s', strtotime($b['to_time']));
                        $fromtime_d = date('H:i:s', strtotime($b['from_time']));
                        if ($current_time <= $totime_d)
                        {
                            array_push($bookingList, $b);
                        }
                    }
                    else
                    {
                        $currendate_fulld = date("Y-m-d H:i:s");
                        // $currendate_fulld=date("2022-03-08 12:51:00");
                        $currentdate = date('Y-m-d');
                        // $currentdate=date('2022-03-08');
                        if ($b['booking_type'] == '0') //daily
                        
                        {
                            $startdate_fulld = date("Y-m-d H:i:s", strtotime($b['booking_from_date'] . ' ' . $b['from_time']));
                            $enddate_fulld = date("Y-m-d H:i:s", strtotime($b['booking_to_date'] . ' ' . $b['to_time']));
                            // if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld ){
                            if ($currendate_fulld <= $enddate_fulld)
                            {
                                array_push($bookingList, $b);
                            }
                        }
                        else
                        {
                            $startdate_fulld = date("Y-m-d H:i:s", strtotime($currentdate . ' ' . $b['from_time']));
                            $enddate_fulld = date("Y-m-d H:i:s", strtotime($currentdate . ' ' . $b['to_time']));
                            //  if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld ){
                            if ($currendate_fulld <= $enddate_fulld)
                            {
                                array_push($bookingList, $b);
                            }
                        }

                    }

                }
                
                if($app_version!=''&&$app_build_no!=''){
                    $insertdata=
                    $device_type!=''?
                    array('app_version'=>$app_version,'app_build_no'=>$app_build_no,'device_type'=>$device_type)
                    :array('app_version'=>$app_version,'app_build_no'=>$app_build_no);
                    $insertversionofuser = $this->db->where('id',$tokenData->id)->where('is_active','1')
                    ->update('ci_users',$insertdata);
                    
                    
                    
                }
                
                $CheckUserTermsCondition = $this->db->select('*')->from('ci_users')->where('id',$user_id)->where('is_active','1')->get()->result_array();
                // print($user_id);
                // print(' - ');
                // print_r($CheckUserTermsCondition);
                if(count($CheckUserTermsCondition)>0){
                    $getCurrentTermsAndCondtn =$this->db->select('*')->from('tbl_about_us')->where('module','1')->where('type','1')->where('is_deleted','1')->order_by('id desc')->get()->result_array();
                    // print_r($getCurrentTermsAndCondtn);
                    if(count($getCurrentTermsAndCondtn)>0)
                    {
                        $userTemsConthId=$CheckUserTermsCondition[0]['terms_conditn_id'];
                        $latestTermsCOnditnId = $getCurrentTermsAndCondtn[0]['id'];
                        // print($userTemsConthId.' -- '.$latestTermsCOnditnId);
                        // exit();
                        if($latestTermsCOnditnId>$userTemsConthId)
                        {
                           $termsAndConditn=array('id'=> $getCurrentTermsAndCondtn[0]['id'],
                            'module'=> $getCurrentTermsAndCondtn[0]['module'],
                            'title'=> $getCurrentTermsAndCondtn[0]['title'],
                            'details'=>  str_replace("\n", "",base64_decode($getCurrentTermsAndCondtn[0]['details'])), 
                            'type'=> $getCurrentTermsAndCondtn[0]['type'],
                            'showUpdateDialog'=>true);
                        }
                        // else{
                        //     print('No need to update terms and condition.');
                        // }
                    }
                }
                // $checkbooking = $this->db->select('*')->from('ci_booking')->where('user_id',$tokenData->id)->get()->result_array();
                // if(count($checkbooking)>0){
                //     $isFirstBooking=false;
                // }
                
                
                

            }
            $getappUpdateDetails = $this->db->select('*')->from('ci_app_update_details')->order_by('id desc')->where('status','0')->where('is_deleted','0')->get()->result_array();
                    // print_r($getappUpdateDetails);
                    if(count($getappUpdateDetails)>0){
                        $app_version_db=$device_type=='1'?$getappUpdateDetails[0]['android_version']:$getappUpdateDetails[0]['ios_version'];
                        $app_build_no_db=$device_type=='1'?$getappUpdateDetails[0]['android_build_no']:$getappUpdateDetails[0]['ios_build_no'];
                        // print($app_version);
                        // print('  --  ');
                        // print($app_build_no);
                        // print(' & ');
                        // print($app_version_db);
                        // print(' -- ');
                        // print($app_build_no_db);
                        // if($app_version<$app_version_db){
                        //     print(' Yes '.$app_version.' is less then'.$app_version_db.' ');
                        // }
                        if($getappUpdateDetails[0]['updatefor']==3||$getappUpdateDetails[0]['updatefor']==$device_type){
                        if($app_version<$app_version_db ||
                        $app_build_no<$app_build_no_db){
                            $isAppUpdate=true;
                            $updateData=array('version'=>$device_type=='1'?$getappUpdateDetails[0]['android_version']:$getappUpdateDetails[0]['ios_version'],
                            'build_no'=>$device_type=='1'?$getappUpdateDetails[0]['android_build_no']:$getappUpdateDetails[0]['ios_build_no'],
                            'app_url'=>$device_type=='1'?$getappUpdateDetails[0]['android_url']:$getappUpdateDetails[0]['ios_url'],
                            'update_type'=>$getappUpdateDetails[0]['update_type'],     //0=flexible update,1=force update 
                            'whats_new'=>$getappUpdateDetails[0]['whats_new'],
                            'notice_count'=>$getappUpdateDetails[0]['notice_count'],
                            'mobiletype'=>$device_type=='1'?'Android':'IOS'
                            );
                           
                        }
                    }
                    }
            // $getofferDetails = $this->db->select('*')->from('ci_offers_master')
            // ->where('is_active','0')->where('is_deleted','0')->get()->result_array();
            /*$getofferDetails = $this->db->select('*')->from('ci_offers_master')->where('is_active','0')
            ->where('fromDate<=',date('Y-m-d'))->where('toDate>=',date('Y-m-d'))
            ->where('is_deleted','0')->order_by('priority asc')->get()->result_array();*/
            $getofferDetails=$this->newOfferuserValidation($user_id);
            // print($user_id);
            // print_r($getofferDetails);
                if($getofferDetails['isoffervalid']==true){
                    //  $validationData =$this->offeruserValidation($user_id,$getofferDetails['offerData']);
                    //  $checkUserValidation =$getofferDetails['offerData']['is_per_user']=='1'? $validationData['isoffervalid']:true;
                    // $isFirstBooking=false;
                    // $userData = $this->db->select('*')->from('ci_users')->where('id',$user_id)
                    // ->where('is_active','1')->get()->result_array();
                    // $perUSerText='';
                    $isOfferValid = $getofferDetails['isoffervalid'];
                    $isOffersOn = $isOfferValid==true?true:false;
                    $message=
                    // $getofferDetails['offerData']['offerDesc'].' & Hurry and grab this offer as   '.
                    $getofferDetails['message'] ;
                    $offerDetails =array('offerheader'=>$isOfferValid==true?$getofferDetails['offerData']['offerText']:'','offerdesc'=>$isOfferValid==true?$message:'');
                }
            $getAdvertisement = $this
                ->db
                ->select('id,ad_imageLink,redirect')
                ->from('master_advertisement')
                ->where('is_deleted', '0')
                ->order_by('id asc')
                ->get()
                ->result();

            // if(count($user)>0){
            $advertisementList = count($getAdvertisement) > 0 ? $getAdvertisement : [];
            
            
            
            
            $tbl_parking_places = $type == '0' ? $this
                ->db
                ->query("SELECT * FROM ci_parking_places where place_status = '1' AND is_deleted='0' AND (status='0' OR status='1') ")
                ->result_array() : $this
                ->db
                ->get_where('ci_parking_places', array(
                'status' => '1',
                'place_status' => '1',
                'is_deleted' => '0'
            ))
                ->result_array();
            if (!empty($tbl_parking_places))
            {
                foreach ($tbl_parking_places as $data)
                {
                    // print('hello');
                    $lat2 = $data['latitude']; //db lat
                    $long2 = $data['longitude']; // db long
                    $distance_cover = $this->distance((float)$lat1, (float)$long1, (float)$lat2, (float)$long2, $unit);

                    // print_r($distance_cover);
                    // exit();
                    if ($distance_cover <= $distance)
                    {
                        $price_Slab = $this->priceslabData($data['id'], $data['ext_per'], $data['pricing_type']);
                        $data['priceSlab'] = $price_Slab;
                        $data['distance'] = $distance_cover;
                        $park_place_data[] = $data;
                        $i = True;
                    }
                    else
                    {
                        $i = False;
                    }
                }

                if (!empty($park_place_data))
                {
                    /* $place_data['status'] = true;
                                        $place_data['message'] = 'List of Parking Places';
                                        $place_data['data'] = $park_place_data;
                                        $place_data['session'] = '1';*/
                    $distance = array_column($park_place_data, 'distance');
                    $placesList=array_multisort($distance, SORT_ASC, $park_place_data);
                    $msg = array(
                        'status' => true,
                        'message' => 'List of Parking Places',
                        'session' => '1',
                        'data' => $park_place_data,
                        'bookings' => $bookingList,
                        'advertisement' => $advertisementList,
                        'isFirstBooking'=>$isOffersOn,
                        'offers'=>$offerDetails,
                        'isAppUpdate'=>$isAppUpdate,
                        'appupdateDetails'=>$updateData,
                        'termsAndConditn'=>$termsAndConditn
                    );
                    echo json_encode($msg);
                }
                else
                {
                    $msg = array(
                        'status' => false,
                        'message' => 'No Parking Places Found Near your  Distance..',
                        'session' => '1',
                        'data' => [],
                        'bookings' => $bookingList,
                        'advertisement' => $advertisementList,
                        'isFirstBooking'=>$isOffersOn,
                        'offers'=>$offerDetails,
                        'isAppUpdate'=>$isAppUpdate,
                        'appupdateDetails'=>$updateData,
                        'termsAndConditn'=>$termsAndConditn
                    );
                    echo json_encode($msg);
                }
            }
            else
            {
                $msg = array(
                    'status' => false,
                    'message' => 'No Parking Places Found Near your  Distance..',
                    'session' => '1',
                    'data' => [],
                    'bookings' => $bookingList,
                    'advertisement' => $advertisementList,
                    'isFirstBooking'=>$isOffersOn,
                    'offers'=>$offerDetails,
                    'isAppUpdate'=>$isAppUpdate,
                    'appupdateDetails'=>$updateData,
                    'termsAndConditn'=>$termsAndConditn
                );
                echo json_encode($msg);
            }

        }
        else
        {
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors()) ,
                'session' => '1',
                'data' => [],
                'bookings' => $bookingList,
                'advertisement' => [],
                'isFirstBooking'=> $isOffersOn,
                'offers'=>$offerDetails,
                'isAppUpdate'=>$isAppUpdate,
                'appupdateDetails'=>$updateData,
                    'termsAndConditn'=>$termsAndConditn
            );
            echo json_encode($msg);
        }

    }
    // public function dashboard_booking() //This api contains places list with users current day booking
    // {

    //     date_default_timezone_set('Asia/Kolkata');
    //     $this
    //         ->form_validation
    //         ->set_rules('lat', 'Latitude', 'required|numeric', array(
    //         'required' => 'Please Provide User latitiude'
    //     ));
    //     $this
    //         ->form_validation
    //         ->set_rules('long', 'Longitude ', 'required|numeric', array(
    //         'required' => 'Please Provide User Longitude'
    //     ));
    //     $this
    //         ->form_validation
    //         ->set_rules('status', 'Status ', 'required');
    //     $this
    //         ->form_validation
    //         ->set_rules('distance', 'Distance ', 'required|numeric', array(
    //         'required' => 'Please Provide Distance',
    //         'numeric' => 'Please Provide Distance in Meters'
    //     ));
    //     $bookings = [];
    //     $bookingList = [];
    //     $advertisementList = [];
    //      $offerDetails =array('offerheader'=>'','offerdesc'=>'');
    //       $isFirstBooking =true;
    //     if ($this
    //         ->form_validation
    //         ->run())
    //     { // validations
            

    //         $lat1 = $this
    //             ->security
    //             ->xss_clean($this
    //             ->input
    //             ->post('lat'));

    //         $long1 = $this
    //             ->security
    //             ->xss_clean($this
    //             ->input
    //             ->post('long'));
    //         $token = $this
    //             ->security
    //             ->xss_clean($this
    //             ->input
    //             ->post('token'));
            
    //         $app_version = $this
    //             ->security
    //             ->xss_clean($this
    //             ->input
    //             ->post('app_version'));
                
    //         $app_build_no = $this
    //             ->security
    //             ->xss_clean($this
    //             ->input
    //             ->post('app_build_no'));
                
    //         $device_type = $this
    //             ->security
    //             ->xss_clean($this
    //             ->input
    //             ->post('device_type'));

    //         $unit = 'M'; //units K,M
    //         $current_date = date('Y-m-d');

    //         // print($current_time);
    //         $distance = $this
    //             ->security
    //             ->xss_clean($this
    //             ->input
    //             ->post('distance')); //distance
    //         $type = $this
    //             ->security
    //             ->xss_clean($this
    //             ->input
    //             ->post('status')); //0 =daily, passes , 1 = passes
            
           
    //         $isAppUpdate=false;
        
    //     $updateData=array('version'=>'',
    //                         'build_no'=>'',
    //                         'app_url'=>'',
    //                         'update_type'=>'',     //0=flexible update,1=force update 
    //                         'whats_new'=>'',
    //                         'notice_count'=>'',
    //                         'mobiletype'=>'');

    //         if (!empty($token))
    //         {
    //             $tokenData = $this->tokenDecodeData($token);
    //             $bookings = $this
    //                 ->db
    //                 ->query("SELECT ci_booking.id,ci_booking.user_id,ci_booking.place_id,
    //                       ci_booking.slot_id,ci_booking.booking_status,booking_from_date,booking_to_date,from_time,
    //                       to_time,paid_status,booking_type,cost,places1.placename,places1.place_address,slot1.slot_no  FROM ci_booking
    //                     JOIN ci_parking_places as places1 ON  ci_booking.place_id = places1.id 
    //                     JOIN ci_parking_slot_info as slot1 ON  ci_booking.slot_id = slot1.slot_no
    //                         where user_id= '$tokenData->id' AND  DATE(booking_from_date) <= '$current_date' AND  DATE(booking_to_date) >= '$current_date'
    //                     AND ci_booking.booking_status='0'    AND  ci_booking.is_deleted='0' 
    //                       ")->result_array();

    //             foreach ($bookings as $b)
    //             {

    //                 if ($b['booking_from_date'] == $b['booking_to_date'])
    //                 {
    //                     $current_time = date('H:i:s');
    //                     $totime_d = date('H:i:s', strtotime($b['to_time']));
    //                     $fromtime_d = date('H:i:s', strtotime($b['from_time']));
    //                     if ($current_time <= $totime_d)
    //                     {
    //                         array_push($bookingList, $b);
    //                     }
    //                 }
    //                 else
    //                 {
    //                     $currendate_fulld = date("Y-m-d H:i:s");
    //                     // $currendate_fulld=date("2022-03-08 12:51:00");
    //                     $currentdate = date('Y-m-d');
    //                     // $currentdate=date('2022-03-08');
    //                     if ($b['booking_type'] == '0') //daily
                        
    //                     {
    //                         $startdate_fulld = date("Y-m-d H:i:s", strtotime($b['booking_from_date'] . ' ' . $b['from_time']));
    //                         $enddate_fulld = date("Y-m-d H:i:s", strtotime($b['booking_to_date'] . ' ' . $b['to_time']));
    //                         // if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld ){
    //                         if ($currendate_fulld <= $enddate_fulld)
    //                         {
    //                             array_push($bookingList, $b);
    //                         }
    //                     }
    //                     else
    //                     {
    //                         $startdate_fulld = date("Y-m-d H:i:s", strtotime($currentdate . ' ' . $b['from_time']));
    //                         $enddate_fulld = date("Y-m-d H:i:s", strtotime($currentdate . ' ' . $b['to_time']));
    //                         //  if($startdate_fulld<=$currendate_fulld && $enddate_fulld>=$currendate_fulld ){
    //                         if ($currendate_fulld <= $enddate_fulld)
    //                         {
    //                             array_push($bookingList, $b);
    //                         }
    //                     }

    //                 }

    //             }
                
    //              if($app_version!=''&&$app_build_no!=''){
    //                 $insertdata=
    //                 $device_type!=''?
    //                 array('app_version'=>$app_version,'app_build_no'=>$app_build_no,'device_type'=>$device_type)
    //                 :array('app_version'=>$app_version,'app_build_no'=>$app_build_no);
    //                 $insertversionofuser = $this->db->where('id',$tokenData->id)->where('is_active','1')
    //                 ->update('ci_users',$insertdata);
                    
                    
                    
    //             }
                
    //             $checkbooking = $this->db->select('*')->from('ci_booking')->where('user_id',$tokenData->id)->get()->result_array();
    //             if(count($checkbooking)>0){
    //                 $isFirstBooking=false;
    //             }
                

    //         }
    //          $getappUpdateDetails = $this->db->select('*')->from('ci_app_update_details')->order_by('id desc')->where('status','0')->where('is_deleted','0')->get()->result_array();
    //                 // print_r($getappUpdateDetails);
    //                 if(count($getappUpdateDetails)>0){
    //                     $app_version_db=$device_type=='1'?$getappUpdateDetails[0]['android_version']:$getappUpdateDetails[0]['ios_version'];
    //                     $app_build_no_db=$device_type=='1'?$getappUpdateDetails[0]['android_build_no']:$getappUpdateDetails[0]['ios_build_no'];
    //                     if($app_version<$app_version_db
    //                     // &&
    //                     // $app_build_no<$app_build_no_db
    //                     ){
    //                         $isAppUpdate=true;
    //                         $updateData=array('version'=>$device_type=='1'?$getappUpdateDetails[0]['android_version']:$getappUpdateDetails[0]['ios_version'],
    //                         'build_no'=>$device_type=='1'?$getappUpdateDetails[0]['android_build_no']:$getappUpdateDetails[0]['ios_build_no'],
    //                         'app_url'=>$device_type=='1'?$getappUpdateDetails[0]['android_url']:$getappUpdateDetails[0]['ios_url'],
    //                         'update_type'=>$getappUpdateDetails[0]['update_type'],     //0=flexible update,1=force update 
    //                         'whats_new'=>$getappUpdateDetails[0]['whats_new'],
    //                         'notice_count'=>$getappUpdateDetails[0]['notice_count'],
    //                         'mobiletype'=>$device_type=='1'?'Android':'IOS'
    //                         );
                           
    //                     }
    //                 }
    //         $getofferDetails = $this->db->select('*')->from('tbl_commons')->where('id',2)->get()->result_array();
    //             if(count($getofferDetails)>0){
    //                 // $isFirstBooking=false;
                    
    //                 $offerDetails =array('offerheader'=>$getofferDetails[0]['subject'],'offerdesc'=>$getofferDetails[0]['value']);
    //             }
    //         $getAdvertisement = $this
    //             ->db
    //             ->select('id,ad_imageLink')
    //             ->from('master_advertisement')
    //             ->where('is_deleted', '0')
    //             ->order_by('id asc')
    //             ->get()
    //             ->result();

    //         // if(count($user)>0){
    //         $advertisementList = count($getAdvertisement) > 0 ? $getAdvertisement : [];
            
            
            
            
    //         $tbl_parking_places = $type == '0' ? $this
    //             ->db
    //             ->query("SELECT * FROM ci_parking_places where place_status = '1' AND is_deleted='0' AND (status='0' OR status='1') ")
    //             ->result_array() : $this
    //             ->db
    //             ->get_where('ci_parking_places', array(
    //             'status' => '1',
    //             'place_status' => '1',
    //             'is_deleted' => '0'
    //         ))
    //             ->result_array();
    //         if (!empty($tbl_parking_places))
    //         {
    //             foreach ($tbl_parking_places as $data)
    //             {
    //                 // print('hello');
    //                 $lat2 = $data['latitude']; //db lat
    //                 $long2 = $data['longitude']; // db long
    //                 $distance_cover = $this->distance((float)$lat1, (float)$long1, (float)$lat2, (float)$long2, $unit);

    //                 // print_r($distance_cover);
    //                 // exit();
    //                 if ($distance_cover <= $distance)
    //                 {
    //                     $price_Slab = $this->priceslabData($data['id'], $data['ext_per'], $data['pricing_type']);
    //                     $data['priceSlab'] = $price_Slab;
    //                     $data['distance'] = $distance_cover;
    //                     $park_place_data[] = $data;
    //                     $i = True;
    //                 }
    //                 else
    //                 {
    //                     $i = False;
    //                 }
    //             }

    //             if (!empty($park_place_data))
    //             {
    //                 /* $place_data['status'] = true;
    //                                     $place_data['message'] = 'List of Parking Places';
    //                                     $place_data['data'] = $park_place_data;
    //                                     $place_data['session'] = '1';*/
    //                 $msg = array(
    //                     'status' => true,
    //                     'message' => 'List of Parking Places',
    //                     'session' => '1',
    //                     'data' => $park_place_data,
    //                     'bookings' => $bookingList,
    //                     'advertisement' => $advertisementList,
    //                     'isFirstBooking'=>$isFirstBooking,
    //                     'offers'=>$offerDetails,
    //                     'isAppUpdate'=>$isAppUpdate,
    //                     'appupdateDetails'=>$updateData
    //                 );
    //                 echo json_encode($msg);
    //             }
    //             else
    //             {
    //                 $msg = array(
    //                     'status' => false,
    //                     'message' => 'No Parking Places Found Near your  Distance..',
    //                     'session' => '1',
    //                     'data' => [],
    //                     'bookings' => $bookingList,
    //                     'advertisement' => $advertisementList,
    //                     'isFirstBooking'=>$isFirstBooking,
    //                     'offers'=>$offerDetails,
    //                     'isAppUpdate'=>$isAppUpdate,
    //                     'appupdateDetails'=>$updateData
    //                 );
    //                 echo json_encode($msg);
    //             }
    //         }
    //         else
    //         {
    //             $msg = array(
    //                 'status' => false,
    //                 'message' => 'No Parking Places Found Near your  Distance..',
    //                 'session' => '1',
    //                 'data' => [],
    //                 'bookings' => $bookingList,
    //                 'advertisement' => $advertisementList,
    //                 'isFirstBooking'=>$isFirstBooking,
    //                 'offers'=>$offerDetails,
    //                 'isAppUpdate'=>$isAppUpdate,
    //                 'appupdateDetails'=>$updateData
    //             );
    //             echo json_encode($msg);
    //         }

    //     }
    //     else
    //     {
    //         $msg = array(
    //             'status' => false,
    //             'message' => strip_tags(validation_errors()) ,
    //             'session' => '1',
    //             'data' => [],
    //             'bookings' => $bookingList,
    //             'advertisement' => [],
    //             'isFirstBooking'=> $isFirstBooking,
    //             'offers'=>$offerDetails,
    //             'isAppUpdate'=>$isAppUpdate,
    //             'appupdateDetails'=>$updateData
    //         );
    //         echo json_encode($msg);
    //     }

    // }
    
    
    

    public function slot_available_new1() //slot availibility , without verification of verifier   
    {
    //     $timeZone='Asia/Kolkata';
    //       $ip = $_SERVER["REMOTE_ADDR"];
    // $query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));
    // if($query && $query['status'] == 'success') {
    //   echo 'Hello visitor from '.$query['country'].', '.$query['city'].'!'.' '.$query['timezone'];
    //   $timeZone=$query['timezone'];
    // } else {
    //   echo 'Unable to get location';
    // }
    // date_default_timezone_set('Europe/London');
        $this->form_validation->set_rules('place_id', 'Place id', 'required');
        $this->form_validation->set_rules('from_date', 'From time', 'required');
        $this->form_validation->set_rules('to_date', 'To Date', 'required');
        $this->form_validation->set_rules('from_time', 'From time', 'required');
        $this->form_validation->set_rules('to_time', 'To time', 'required');
        //  $this->form_validation->set_rules('token', 'Token', 'required');
        $this->form_validation->set_rules('multiDate', 'is boolean', 'required');  // true = multidate , false = single date

/*reserve_from_time)); //21 4
                        $toDate_s = date('Y-m-d H:i:s', strtotime($v->booking_to_date . ' ' . $v->reserve_to_time*/
        // $this->form_validation->set_rules('token', 'Token', 'required'); 
        
        
        if ($this->form_validation->run()) {
            // $token = $this->input->post('token');
            
            // $verifyToken = $this->tokenVerify($token);
                    
            // if($verifyToken==true){
                        
            // $tokenData = $this->tokenDecodeData($token);
            // post data
            $place_id = $this->security->xss_clean($this->input->post('place_id'));
            $from_date = $this->security->xss_clean($this->input->post('from_date'));
            $to_date = $this->security->xss_clean($this->input->post('to_date'));
            $from_time = $this->security->xss_clean($this->input->post('from_time'));
            $to_time = $this->security->xss_clean($this->input->post('to_time'));
            $multiDate = $this->security->xss_clean($this->input->post('multiDate'));
            $token = $this->security->xss_clean($this->input->post('token'));

// print('eee');
            $listof_Slots = $this->db->select('slot_no,display_id,isBlocked')->from('ci_parking_slot_info')->order_by('slot_no','ASC')
                ->where('place_id', $place_id)->where('status', '0')->where('onOff_applied','0')->where('is_deleted', '0')
                ->get()->result_Array();
            $data = $this->db->select('*')->from('ci_booking')
                ->where('place_id', $place_id)->group_start()->where('booking_status','0')->or_where('booking_status','3')->group_end()->where('is_deleted',"0")
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
                        //   if(count($getVerifyStatus)>0)
                        //   {
                        //     $vrifyBooking='1';    
                        //   }
                        //   $sensorStatusNew = count($sensorStatus)>0?$sensorStatus[0]->status:'0';
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
                            ||$fromDate_u<=$fromDate_s&&$toDate_u>=$toDate_s||$fromDate_s<=$fromDate_u&&$toDate_s>=$toDate_u) 
                            {
                            // array_push($listof_bookedSlots,array('slotid'=>$v->slot_id,'bookingid'=>$v->id));
                            //  $sensorStatus = $this->db->select('status')->from('mpc_sensor')->where('slot_id',$v->slot_id)->get()->result();
                            $sensorStatus = $this->db->Select('status')->from('mpc_sensor')->where('slot_id',$v->slot_id)->order_by("id", "DESC")->get()->result();
                             $bookedStatus = ''; //1==red,2==yellow
                            //  $getVerifyStatus = $this->db->select('*')->from('tbl_booking_verify')->where('booking_id',$v->id)
                            //   ->where('booking_type','0')->where('verify_status','1')->get()->result();
                            $getVerifyStatus = $this->db->select('*')->from('ci_booking_verify')->where('booking_id',$v->id)
                           ->where('booking_type','1')->where('verify_status','1')->order_by('id DESC')->get()->result();
                               $vrifyBooking='1';
                            //   if(count($getVerifyStatus)>0){
                            //       if(date('Y-m-d',strtotime($getVerifyStatus[0]->onCreated))>=date('Y-m-d')){
                            //   $vrifyBooking='1';        
                            //       }else{
                            //           $vrifyBooking='0';
                            //       }
                                   
                            //   }
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
                           ->where('booking_type','1')->where('verify_status','1')->order_by('id DESC')->get()->result();
                               $vrifyBooking='1';
                            //   if(count($getVerifyStatus)>0){
                            //       if(date('Y-m-d',strtotime($getVerifyStatus[0]->onCreated))>=date('Y-m-d')){
                            //   $vrifyBooking='1';        
                            //       }else{
                            //           $vrifyBooking='0';
                            //       }
                                   
                            //   }
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
                                //  $slot1->isGrey=$slot1['isBlocked']=='0'?true:false;
                        }
                      
                    }
                  
                    array_push($slot_availabledata,$slot1);
                }
                $walletbalance=0;
                if($token!=''){
                    $tokenData = $this->tokenDecodeData($token);
                $walletDetails = $this->db->select('*')->from('ci_wallet_user')->where('user_id',$tokenData->id)->where('is_deleted','0')->get()->result_array();
              $walletbalance = (String)(count($walletDetails)>0?$walletDetails[0]['amount']:'0');
                }
                echo json_encode(
                    array("status"=>true,
                    "message"=>"list of slots",
                    "place_id"=>(String)$place_id,
                    "data"=>$slot_availabledata,'walletbalance'=>(String)$walletbalance,'session'=>'1'));
                    
        // }else{
        //     $msg = array('status' => false, 'message' => 'Session expired',"data"=>[],'session'=>'0');
        //     echo json_encode($msg);
        // }
                    
                }
                else{$msg = array('status' => false, 'message' => strip_tags(validation_errors()),"place_id"=>'',"data"=>[],'walletbalance'=>(String)$walletbalance,'session'=>'1');
            echo json_encode($msg);}
                
                
            
    
}

    public function booking_successfull() // This api is call when user books a slot
    {
            // $timeZone = 'Asia/Kolkata';//'Europe/London';//'Asia/Kolkata';
            // $ip = $_SERVER["REMOTE_ADDR"];
            // $query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));
            // if($query && $query['status'] == 'success') {
            // //   echo 'Hello visitor from '.$query['country'].', '.$query['city'].'!'.' '.$query['timezone'];
            //   $timeZone = $query['timezone'];
            // } else {
            // //   echo 'Unable to get location';
            // }
        
        date_default_timezone_set('Asia/Kolkata');
        $this->form_validation->set_rules('from_date', 'From time', 'required');
        $this->form_validation->set_rules('to_date', 'To Date', 'required');
        $this->form_validation->set_rules('from_time', 'From time', 'required');
        $this->form_validation->set_rules('to_time', 'To time', 'required');
        $this->form_validation->set_rules('place_id', 'Place id', 'required');
        $this->form_validation->set_rules('slot_id', 'Slot id', 'required');
        $this->form_validation->set_rules('booking_type', 'Booking type', 'required');
        $this->form_validation->set_rules('cost', 'Cost', 'required');     
        $this->form_validation->set_rules('car_id', 'Car', 'required');
        $this->form_validation->set_rules('token', 'Token', 'required');  
        
        if ($this->form_validation->run()) {
            $token = $this->security->xss_clean($this->input->post('token'));
            
            $verifyToken = $this->tokenVerify($token);
                    
            if($verifyToken==true){
                        
            $tokenData = $this->tokenDecodeData($token);
            // post data
            
            $car_id = $this->security->xss_clean($this->input->post('car_id'));
            
            $from_date = $this->security->xss_clean($this->input->post('from_date'));
            $to_date = $this->security->xss_clean($this->input->post('to_date'));
            $from_time = $this->security->xss_clean($this->input->post('from_time'));
            $to_time = $this->security->xss_clean($this->input->post('to_time'));
            $user_id = $tokenData->id;
            $place_id = $this->security->xss_clean($this->input->post('place_id'));
            $slot_id = $this->security->xss_clean($this->input->post('slot_id'));
            $booking_type = $this->security->xss_clean($this->input->post('booking_type'));
            $cost = $this->security->xss_clean($this->input->post('cost'));
            
             $longitude = $this->security->xss_clean($this->input->post('longitude'));
                $latitude = $this->security->xss_clean($this->input->post('latitude'));


            $offer_id = $this
                    ->security
                    ->xss_clean($this
                    ->input
                    ->post('offer_id'));
            $originalCost = $this
                    ->security
                    ->xss_clean($this
                    ->input
                    ->post('originalCost'));
            $booking_status = '0';
             
                    
             $delay_time1 =     rand(100000,800000);
             $delay_time =     rand(1000000,1500000)+$delay_time1;
            //  print('delay :- ');print($delay_time);
            $slotValidation = $this->db->select('*')->from('ci_parking_slot_info')->where('slot_no',$slot_id)
            ->where('status','0')->where('is_deleted','0')->where('isBlocked','1')->get()->result();
             if(count($slotValidation)>0){
                // usleep($delay_time);
                     $getSlotReservedId = $this->db->select('*')->from('ci_parking_slot_info')->where('slot_no',$slot_id)
                      ->where('status','0')->where('is_deleted','0')->get()->result(); 
                    if(count($getSlotReservedId)>0){
                        if($getSlotReservedId[0]->reserved_userId==0||$getSlotReservedId[0]->reserved_userId==$tokenData->id){
                        $reservedSlot = array("reserved_userId"=>$tokenData->id,
                                "reserved_booking_time"=>date('Y-m-d H:i:s'));
                        $inserreserved_userid = $this->db->where('slot_no',$slot_id)->update('ci_parking_slot_info',$reservedSlot);
                        $delay_time1 =     rand(100000,1500000);
                    usleep($delay_time1);
                     $getSlotReservedId = $this->db->select('*')->from('ci_parking_slot_info')->where('slot_no',$slot_id)->where('reserved_userId',$tokenData->id)->get()->result(); 
                        if(count($getSlotReservedId)>0){
                            // if($getSlotReservedId[0]['reserved_userId']==)
                        
                            $get_wallet_amt = $this->db->select('*')->from('ci_wallet_user')->where('user_id',$tokenData->id)->get()->result();
                            if(count($get_wallet_amt)>0){
                                $new_amt =$get_wallet_amt[0]->amount;
                                if($cost<=$new_amt){
                                    // $delay_time1 =     rand(100000,1500000);
                                    // usleep($delay_time1);
                                    $reserve_from_time= date('H:i:s',strtotime($from_time . ' -10 minutes'));
                            $reserve_to_time= date('H:i:s',strtotime($to_time . ' +0 minutes'));
                            $placeDetails = $this->db->select('*')->from('ci_parking_places')->where('id',$place_id)->where('is_deleted','0')->get()->result();
                            $vendor_id=count($placeDetails)>0?$placeDetails[0]->vendor_id:0;
                //   echo $date;
                
                            $data = array(
                                    "booking_from_date" => $from_date,
                                    "booking_to_date" => $to_date,
                                    "from_time" => $from_time,
                                    "to_time" => $to_time,
                                    "booking_status"=>$booking_status,
                                    "paid_status" => '0',
                                    "user_id" => $user_id,
                                    "place_id" => $place_id,
                                    "slot_id" => $slot_id,
                                    "vendor_id"=>$vendor_id,
                                    "booking_type" => $booking_type,
                                    "cost" => $cost,
                                    "reserve_from_time"=>$reserve_from_time,
                                    "reserve_to_time"=>$reserve_to_time,
                                    "car_id"=>$car_id,
                                     'offer_id'=>$offer_id,
                                     "originalCost"=>$originalCost,
                                     'longitude' => $longitude,
                                     'latitude' => $latitude,
                                    "timezone"=>'Asia/Kolkata' // for manoj sir id we have set Europe/london
                                );  
                                
                                $chekavailbity=$this->check_slot_available($place_id,$from_date,$to_date,$from_time,$to_time,$slot_id);
                                // print('slot available :- ');
                                // print($chekavailbity);exit();
                                if($chekavailbity=='true'){
                                $result = $this->db->insert('ci_booking', $data);   
                                $last_id = $this->db->insert_id();
                                if($result == true){
                                     $getRep_b_ID='';
                                    $getRep_boking=$this->db->select('*')->from('ci_booking')->like('unique_booking_id','PAB')->order_by("unique_booking_id", "Desc")->get()->result();
                                    if(count($getRep_boking)>0){
                                        $explode = explode("B",$getRep_boking[0]->unique_booking_id);
                                        $count = 8-strlen($explode[1]+1);
                                        $bookingId_rep =$explode[1]+1;
                                        //  print($count);print('\n');
                                        // print($explode[1]);
                                        
                                        for($i=0;$i<$count;$i++){
                                        $bookingId_rep='0'.$bookingId_rep;
                                        }
                                        $getRep_b_ID = 'PAB'.$bookingId_rep;
                                    }
                                    else{
                                    $getRep_b_ID = 'PAB'.'00000001';    
                                    }
                                    
                                    $this->db->where('id',$last_id)->update('ci_booking',array('unique_booking_id'=>$getRep_b_ID));
                                    
                              $reservedSlot = array("reserved_userId"=>0,
                                "reserved_booking_time"=>'');
                        $inserreserved_userid = $this->db->where('slot_no',$slot_id)->update('ci_parking_slot_info',$reservedSlot);
                                    $new_amt1 =$new_amt-$cost;
                                    $this->db->where('id',$get_wallet_amt[0]->id)->update('ci_wallet_user',array('amount'=>(float)$new_amt1));
                                    
                                    $inserData1=array("wallet_id"=>$get_wallet_amt[0]->id,"user_id"=>$get_wallet_amt[0]->user_id,"amount"=>$cost,"status"=>'2',
                                    "payment_type"=>'2','booking_id'=>$last_id,
                            'last_wallet_amount'=>$get_wallet_amt[0]->amount);
                                                            // $insertPayment1 = $this->db->insert('ci_wallet_history',$inserData1);
                                    $this->wallet_history_log($inserData1);
                                    // $getNotify = $this->db->select('*')->from('ci_notify_track')->where('booking_id',$last_id)->where('user_id',$user_id)
                                    //         ->where('notify_type','4')
                                    //         ->where('is_deleted','0')
                                    //         ->get()->result();
                                    $message= '₹ '.$cost.' has been deducted from your wallet';
                                    $this->notificationForWallet($user_id,$last_id,$place_id,$slot_id, 'Wallet', $message,'4','4'); //notificationForWallet($userId, $bookingId,$place_id,$slot_id,$title, $body,$screen,$notifyType)
                                    $msg = array('status' => TRUE, 'message' => 'Booking Successfull', 'id' => "$last_id",'session'=>'1','walletStatus'=>'0');
                                                echo json_encode($msg);
                                }
                                else {
                                    $msg = array('status' => false, 'message' => 'Booking Failed', 'id' => "",'session'=>'1','walletStatus'=>'0');
                                                echo json_encode($msg);
                                }
                                    
                                }
                                else{
                                    $msg = array('status' => false, 'message' => 'This slot is already book.', 'id' => "",'session'=>'1','walletStatus'=>'0');
                                                echo json_encode($msg);
                                }
                                }else{
                                    $msg = array('status' => false, 'message' => 'Kindly refill your wallet.', 'id' => "",'session'=>'1','walletStatus'=>'1'); // walletStatus 1  = redirect to wallet page for refill 
                                                echo json_encode($msg);
                                }
                            }
                        }else{
                             $msg = array('status' => false, 'message' => 'Already Booking in progress for this Slot by different user.','id' => "",'session'=>'1','walletStatus'=>'0');
                echo json_encode($msg);
                        } 
                    }else{
                        $msg = array('status' => false, 'message' => 'Already Booking in progress for this Slot by different user. ','id' => "",'session'=>'1','walletStatus'=>'0');
            echo json_encode($msg);
                    }
                    }
                    else{
                     $msg = array('status' => false, 'message' => 'No such slot available for booking.','id' => "",'session'=>'1','walletStatus'=>'0');
            echo json_encode($msg);
                    }
             }else{
                $msg = array('status' => false, 'message' => 'This slot is not accessable. Kindly select another slot.','id' => "",'session'=>'1','walletStatus'=>'0');
            echo json_encode($msg);
            }
                    
        }else{
            $msg = array('status' => false, 'message' => 'Session expired','id' => "",'session'=>'0','walletStatus'=>'0');
            echo json_encode($msg);
        }
        }
        else{
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'id' => "",'session'=>'1','walletStatus'=>'0');
            echo json_encode($msg);
        }
    
}

    public function distance($lat1,$lon1,$lat2,$lon2,$unit) //distance
    {
            
                    $theta = $lon1 - $lon2;
                    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                    $dist = acos($dist);
                    $dist = rad2deg($dist);
                    $miles = $dist * 60 * 1.1515;
                    $unit = strtoupper($unit);
            
                    if ($unit == "K") {
                         return  ($miles * 1.609344);//.' in Km';
                     }
                     else if ($unit == "N") {
                         return ($miles * 0.8684);
                     }
                     else if($unit =="M")
                     {
                         return  (($miles * 1.609344)*1000);//.'  in Meters';
                     }
                     else {
                         return  $miles;
                    
        }
        
    }

    public function priceslabData($placeId,$ext_charges,$pricetype)
    {
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
    
    
    $i=0;
    foreach($getDetailPerPlace as $price){
        if($price->place_id==7){
            $perhourCost=array('hrs'=>(int)$price->hrs,'cost'=>(int)$price->cost);
            if($pricetype=='1'){
                $extcost =(int)$price->cost+ ((int)$price->cost/100)*$ext_charges;
             array_push($getperExtended,array('hrs'=>(int)$price->hrs,'cost'=>(int)$extcost));
            }
        }else{
        if($price->pass==0){
            // $perhourCost=
            $perhourCost=array('hrs'=>(int)$price->hrs,'cost'=>(int)$price->cost);
            if($pricetype=='1'){
                $extcost =(int)$price->cost+ ((int)$price->cost/100)*$ext_charges;
             array_push($getperExtended,array('hrs'=>(int)$price->hrs,'cost'=>(int)$extcost));
            }
        }
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
    }
        // else if($price->pass==4){
        //     array_push($getperExtended,array('hrs'=>(int)$price->hrs,'cost'=>(int)$price->cost));
        // }
        $i++;
    }
                }
    
    return array('perHour'=>$perhourCost,'perDay'=>$getperday,'perWeek'=>$getperWeek,'perMonth'=>$getperMonth,'extendPrice'=>$getperExtended);
    // $data=array('status'=>true,'message'=>'List of price slab','pricesSlab'=>array('perDay'=>$getperday,'perWeek'=>$getperWeek,'perMonth'=>$getperMonth));
    // echo json_encode($data);
}

    public function place_list()
    {
        
        // $this->form_validation->set_rules('user_id', 'User id', 'required');
        $this->form_validation->set_rules('token', 'Token', 'required');
        $dataList=[];
        if ($this->form_validation->run()) {
            $token = $this->security->xss_clean($this->input->post('token'));
            
            $verifyToken = $this->tokenVerify($token);
                    
                    if($verifyToken==true){
                        
                    $tokenData = $this->tokenDecodeData($token);
            
            $place_list = $this->db->select('ci_booking.user_id, ci_booking.place_id as place_id,ci_parking_places.placename as place_name ')
            ->from('ci_booking')->join('ci_parking_places', 'ci_booking.place_id = ci_parking_places.id')->where('ci_booking.user_id',$tokenData->id)
            ->where('ci_booking.is_deleted','0')->get()->result();
            
            $dataList = [array("user_id"=> $tokenData->id,
            "place_id"=>"12",
            "place_name"=>"Test"),
            array("user_id"=> $tokenData->id,
            "place_id"=>"11",
            "place_name"=>"BDS Services Private Limited"),
            array("user_id"=> $tokenData->id,
            "place_id"=>"37",
            "place_name"=>"BDS Services UK"),];
            
            // print_r($place_list);
            // exit();
            // $datalist=$place_list;
            // $uniqueList=$this->getUniqueHotels($place_list);
            
            $msg = array('status' => TRUE, 'message' => 'List of places','session'=>'1', 'data' => $dataList);
            echo json_encode($msg);
        } else {
            $msg = array('status' => false, 'message' => 'Session expired','session'=>'0', 'data' => $dataList);
            echo json_encode($msg);
        }
        }else {
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1', 'data' => $dataList);
            echo json_encode($msg);
        }
        
    }
    
    public function insert_complaint() //complaints 
    {
        
        // $this->form_validation->set_rules('place_id', 'Place Id', 'required');
        $this->form_validation->set_rules('compalaint_topic', 'Complaint Topic', 'required');
        $this->form_validation->set_rules('description', 'Complaint Description', 'required'); 
        $this->form_validation->set_rules('token', 'Token', 'required'); 
        
        
        if ($this->form_validation->run()) {
            $token = $this->security->xss_clean($this->input->post('token'));
            
            $verifyToken = $this->tokenVerify($token);
                    
            if($verifyToken==true){
                        
            $tokenData = $this->tokenDecodeData($token);
            // $place_id = $this->security->xss_clean($this->input->post('place_id'));
            $compalaint_topic = $this->security->xss_clean($this->input->post('compalaint_topic'));
            $description = $this->security->xss_clean($this->input->post('description'));
            
            // $place_name = $this->db->select('placename')->from('tbl_parking_places')->where('id',$place_id)->where('is_deleted','0')->get()->result_Array();
            

            $data = array(
                    "user_id" => $tokenData->id,
                    "place_id" => '',
                    "complaint_topic" => $compalaint_topic,
                    "description" => $description
                );            
                
                $result = $this->db->insert('tbl_complaint', $data);            
                if($result == true){
                    $msg = array('status' => TRUE, 'message' => 'Complaint successfully registered !!','session'=>'1');
                                echo json_encode($msg);
                }
                else {
                    $msg = array('status' => false , 'message' => 'Complaint registration failed!!','session'=>'1');
                                echo json_encode($msg);
                }
                        
                    }
                    else {
            $msg = array('status' => false, 'message' => 'Session expired','session'=>'0');
            echo json_encode($msg);
        }
        }else {
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1');
            echo json_encode($msg);
        }
    
        
    }

    public function get_booking_list() //Booking list of user
    {
       
        $this->form_validation->set_rules('token', 'Token', 'required');  
        
        if ($this->form_validation->run()) {
            $token = $this->security->xss_clean($this->input->post('token'));
            
            $verifyToken = $this->tokenVerify($token);
                    
            if($verifyToken==true)
            {
                        
            $tokenData = $this->tokenDecodeData($token);
            
            // $user_id = $this->input->post('user_id');
            
            $booking_det = $this->db->select('ci_booking.id as booking_id, ci_booking.*, ci_parking_places.*,car.car_number,car.car_name')
            ->from('ci_booking')->join('ci_parking_places', 'ci_booking.place_id = ci_parking_places.id')
            ->join('ci_car_details as car', 'car.id = ci_booking.car_id')
            ->where('ci_booking.user_id',$tokenData->id)->where('ci_booking.is_deleted','0')
            ->order_by("ci_booking.created_date", "desc")->get()->result_Array();
            
            // print_r($booking_det);
            
            $bookingCompleted_cancle=[];
            $booking = [];
            $i = 0;
            foreach($booking_det as $v){
                
                $from_d = strtotime($booking_det[$i]['booking_from_date']);
                $to_d = strtotime($booking_det[$i]['booking_to_date']);
                $from_t = strtotime($booking_det[$i]['from_time']);
                $to_t = strtotime($booking_det[$i]['to_time']);
                $from_date = date("d M Y", $from_d);
                $to_date = date("d M Y", $to_d);
                $from_time = date("h:i a", $from_t);
                $to_time = date("h:i a", $to_t);
                $booking_detail[0]['slot_name']='';
                $booking_detail[0]['display_id']='';
                $replaceBookingId=$booking_det[$i]['replaced_booking_id'];
                $replacedforBookingId='';
                $getraplacedData = $this->db->select('unique_booking_id')->from('ci_booking')->where('id',$replaceBookingId)->get()->result();
                if(count($getraplacedData)>0){
                    $replacedforBookingId=$getraplacedData[0]->unique_booking_id;
                }
                
                $datetime = "$from_date $from_time  -  $to_date $to_time";
                $booking_slotDetails=$this->db->select('*')->from('ci_parking_slot_info')->where('slot_no',$booking_det[$i]['slot_id'])->get()->result();
            if(count($booking_slotDetails)>0){
                $booking_detail[0]['slot_name']=$booking_slotDetails[0]->slot_name;
                $booking_detail[0]['display_id']=$booking_slotDetails[0]->display_id;
            }
                
                $data = array(
                        "user_id" => $tokenData->id,
                        "book_ext" => $booking_det[$i]['book_ext'],
                        "booking_id" => $booking_det[$i]['booking_id'],
                        "place_id" => $booking_det[$i]['place_id'],
                        "unique_booking_id" => $booking_det[$i]['unique_booking_id'],
                        "slot_no" => $booking_det[$i]['slot_id'],
                        "place_name" => $booking_det[$i]['placename'],
                        "place_address" => $booking_det[$i]['place_address'],
                        "car_number" => $booking_det[$i]['car_number'],
                        "car_name" => $booking_det[$i]['car_name'],
                        "booking_type" => $booking_det[$i]['booking_type'],
                        "datetime" => $datetime,//
                        'from_date'=>$from_date,
                        'to_date'=>$to_date,
                        'from_time'=>$from_time,
                        'to_time'=>$to_time,
                        'replaced_booking_id'=>$booking_det[$i]['replaced_booking_id'],
                        'replaced_for_booking_id'=>$replacedforBookingId,
                        "booking_status"=>$booking_det[$i]['booking_status'],
                        // "user_rating" => $booking_det[$i]['user_rating'],
                        "price" => $booking_det[$i]['cost'],
                        "latitude" => $booking_det[$i]['latitude'],
                        "longitude" => $booking_det[$i]['longitude'],
                        "display_id" => $booking_detail[0]['display_id'],
                        "slot_name" => $booking_detail[0]['slot_name'] 
                    );
                    if($booking_det[$i]['booking_status']=='0'){
                array_push($booking, $data);}
                else{
                    array_push($bookingCompleted_cancle, $data);
                }
            $i++;
            }
            $booking_list=[];
            $booking_list = array_merge($booking,$bookingCompleted_cancle);
            if(count($booking_list) >0){
                $msg = array('status' => TRUE, 'msg' => "Bookings list", 'data' => $booking_list,'session'=>'1');
                echo json_encode($msg);
            }
            else {
                $msg = array('status' => false, 'msg' => "No Booking found !!", 'data' => $booking_list,'session'=>'1');
                echo json_encode($msg);
            }
            }else{
                $msg = array('status' => false, 'message' => 'Session expired','session'=>'0','data'=>[]);
            echo json_encode($msg);
            }
        }else{
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1','data'=>[]);
            echo json_encode($msg);
        }
        
        
    }
    
    public function get_booking_detail() //Single Booking details screen in app
    {

        date_default_timezone_set('Asia/Kolkata');
        $this
            ->form_validation
            ->set_rules('booking_id', 'Booking Id', 'required');
        $this
            ->form_validation
            ->set_rules('token', 'Token', 'required');

        if ($this
            ->form_validation
            ->run())
        {
            $token = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('token'));
                
            

            $verifyToken = $this->tokenVerify($token);
            $verfierscontactList=[];
            if ($verifyToken == true)
            {

                $tokenData = $this->tokenDecodeData($token);

                $booking_id = $this
                    ->security
                    ->xss_clean($this
                    ->input
                    ->post('booking_id'));

                // $booking_detail = $this->db->select('tbl_booking.id as booking_id, tbl_booking.*, tbl_parking_places.placename,tbl_parking_places.place_address')->from('tbl_booking')->join('tbl_parking_places', 'tbl_booking.place_id = tbl_parking_places.id')->where('tbl_booking.id', $booking_id)->where('tbl_booking.is_deleted','0')->get()->result_Array();
                $booking_detail = $this
                    ->db
                    ->select('ci_booking.id as booking_id, ci_booking.unique_booking_id,
            ci_booking.book_ext,ci_booking.user_id,ci_booking.place_id,ci_booking.booking_status,ci_booking.slot_id,
            ci_booking.replaced_booking_id,ci_booking.booking_from_date,ci_booking.booking_to_date,ci_booking.from_time,ci_booking.to_time,ci_booking.booking_type,ci_booking.cost,
            ci_booking.car_id
            , ci_parking_places.placename,ci_parking_places.place_address,ci_parking_places.latitude,ci_parking_places.pricing_type,ci_parking_places.ext_per,
            ci_parking_places.longitude')
                    ->from('ci_booking')
                    ->join('ci_parking_places', 'ci_booking.place_id = ci_parking_places.id')
                    ->where('ci_booking.id', $booking_id)->where('ci_booking.is_deleted', '0')
                    ->get()
                    ->result_Array();

                if (count($booking_detail) > 0)
                {
                    
                    $from_d = strtotime($booking_detail[0]['booking_from_date']);
                    $to_d = strtotime($booking_detail[0]['booking_to_date']);
                    $from_t = strtotime($booking_detail[0]['from_time']);
                    $to_t = strtotime($booking_detail[0]['to_time']);

                    $from_date = date("d M Y", $from_d);
                    $to_date = date("d M Y", $to_d);
                    $fromTimeForCancel = date("H:i:s", $from_t);
                    $from_time = date("h:i a", $from_t);
                    $to_time = date("h:i a", $to_t);
                    $to_time_24hrs = date("H:i:s", $to_t);

                    $datetime = "$from_date - $to_date, $from_time - $to_time";

                    $booking_detail[0]['datetime'] = $datetime;
                    $booking_detail[0]['fromTimeForCancel'] = $fromTimeForCancel;
                    $booking_detail[0]['car_name'] = '';
                    $booking_detail[0]['car_number'] = '';
                    $booking_detail[0]['slot_name'] = '';
                    $booking_detail[0]['display_id'] = '';
                    $booking_detail[0]['from_time'] = $from_time;
                    $booking_detail[0]['to_time'] = $to_time; //unique_booking_id
                    $booking_detail[0]['to_time_24hrs'] = $to_time_24hrs;

                    $booking_slotDetails = $this
                        ->db
                        ->select('*')
                        ->from('ci_parking_slot_info')
                        ->where('slot_no', $booking_detail[0]['slot_id'])->get()
                        ->result();
                    if (count($booking_slotDetails) > 0)
                    {
                        $booking_detail[0]['slot_name'] = $booking_slotDetails[0]->slot_name;
                        $booking_detail[0]['display_id'] = $booking_slotDetails[0]->display_id;
                    }

                    $carDetails = $this
                        ->db
                        ->select('*')
                        ->from('ci_car_details')
                        ->where('id', $booking_detail[0]['car_id'])->get()
                        ->result();

                    $replaceBookingId = $booking_detail[0]['replaced_booking_id'];
                    // $replacedforBookingId='';
                    $booking_detail[0]['replaced_for_booking_id'] = '';
                    $getraplacedData = $this
                        ->db
                        ->select('unique_booking_id')
                        ->from('ci_booking')
                        ->where('id', $replaceBookingId)->get()
                        ->result();
                    if (count($getraplacedData) > 0)
                    {
                        $booking_detail[0]['replaced_for_booking_id'] = $getraplacedData[0]->unique_booking_id;
                    }
                    

                    $getVerifierPlace = $this
                        ->db
                        ->select('*')
                        ->from('tbl_verifier_place')
                        ->where('place_id', $booking_detail[0]['place_id'])->where('duty_date',date('Y-m-d'))->order_by("id", "Desc")->where('isDeleted', '0')
                        ->get()
                        ->result();
                    //print_r($getVerifierPlace);
                    if (count($getVerifierPlace) > 0)
                    {
                        foreach($getVerifierPlace as $verfier)
                        {
                            // $verfierscontactList=;
                             $getVerifierDetails = $this
                            ->db
                            ->select('*')
                            ->from('ci_admin')
                            ->where('admin_id', $verfier->verifier_id)
                            ->where('admin_role_id', '3')
                            ->where('is_active', '1')
                            ->get()
                            ->result();
                            
                            if (count($getVerifierDetails) > 0)
                            {
                                $checkLogin = $this->db->select('*')->from('tbl_verifier_login')->where('verifier_id',$verfier->verifier_id)->where('status','1')->where('created_at',date('Y-m-d'))->get()->result_array();
                                // print_r($checkLogin);
                                if(count($checkLogin)>0){
                                    $booking_detail[0]['mobile_no'] = $getVerifierDetails[0]->mobile_no;
                                    $verifierData= array('username'=>$getVerifierDetails[0]->username,'mobileNo'=>$getVerifierDetails[0]->mobile_no);
                                    array_push($verfierscontactList,$verifierData);
                                }else{
                                      $booking_detail[0]['mobile_no'] = '';
                                }
                                
                            }
                            else
                            {
                                $booking_detail[0]['mobile_no'] = '';
                            }
                    }
                       if(count($verfierscontactList)<=0)
                        {
                            $getCutomercareNumber =$this->db->select('*')->from('ci_support_master')->where('id',1)->where('is_deleted','0')->get()->result_array();
                            
                            $verifierData= array('username'=>'CustomerCare','mobileNo'=>$getCutomercareNumber[0]['contact']);
                            array_push($verfierscontactList,$verifierData);
                        }
                    }
                    else
                    {
                        $booking_detail[0]['mobile_no'] = '';
                    }

                    // print_r($carDetails);
                    if (count($carDetails) > 0)
                    {
                        foreach ($carDetails as $c)
                        {
                            $booking_detail[0]['car_name'] = $c->car_name;
                            $booking_detail[0]['car_number'] = $c->car_number;
                        }

                    }
                    $extendBookingPrice = $this
                        ->db
                        ->select('hrs,cost')
                        ->from('ci_price_slab')
                        ->where('place_id', $booking_detail[0]['place_id'])->where('pass', $booking_detail[0]['pricing_type'] == '0' ? 1 : '0')->where('is_deleted', '0')
                        ->get()
                        ->result();

                    $ext_price = [];
                    // if($booking_detail[0]['pricing_type']=='0'){
                    foreach ($extendBookingPrice as $ext)
                    {
                        $extcost = (int)$ext->cost + ((int)$ext->cost / 100) * $booking_detail[0]['ext_per'];
                        array_push($ext_price, array(
                            'hrs' => (String)$ext->hrs,
                            'cost' => (String)$extcost
                        ));
                    }

                    // }
                    $booking_detail[0]['extenPriceSlab'] = $extendBookingPrice;
                    $extendedStatus = false;
                    $isShowExtndBtn=false;
                    
                    $currentDatetime =  new DateTime(date("Y-m-d H:i:s"));
                    $verifyStatus=false;
                    if($booking_detail[0]['booking_type']==0) // daily
                    {
                        $checkBookingVerify = $this
                        ->db
                        ->select('*')
                        ->from('ci_booking_verify')
                        ->where('booking_id', $booking_id)->where('isDeleted', '0')
                        ->get()
                        ->result();
                        // print_r($checkBookingVerify);
                    // $verifyStatus = count($checkBookingVerify) > 0 ? true : false;
                    // $isShowExtndBtn=$verifyStatus==true?true:false;
                    if(count($checkBookingVerify)>0){
                        $verifyStatus=true;
                    }else{
                        $verifyStatus=false;
                        // $isShowExtndBtn=false;
                    }
                         $checkExtention = $this
                        ->db
                        ->select('*')
                        ->from('ci_booking')
                        ->where('replaced_booking_id', $booking_detail[0]['booking_id'])->where('is_deleted', '0')
                        ->get()
                        ->result();
                        // print_r($checkExtention);
                        if(count($checkExtention)<=0){
                                $todatetime=date('Y-m-d H:i:s',strtotime($booking_detail[0]['booking_to_date'].' '.$booking_detail[0]['to_time']));
                                $interval = $currentDatetime->diff(new DateTime($todatetime));
                                // print_r($interval);
                                // if($interval->i>=-5){
                                //     $isShowExtndBtn=true;
                                // } 
                                if($interval->invert==1){
                                    if($interval->days==0&&$interval->h==0&&$interval->i<=5){
                                        $isShowExtndBtn=true;
                                    }else{
                                        $isShowExtndBtn=false;
                                    }
                                }else{
                                    $isShowExtndBtn=true;
                                }
                        }else{
                            $extendedStatus =  true ; 
                            $isShowExtndBtn= false ;
                        }
                        // $extendedStatus = count($checkExtention) > 0 ? true : false; //need to remove after 1.1.0 app upload
                        // $isShowExtndBtn=count($checkExtention) > 0 ? false : $isShowExtndBtn;
                    
                       
                       
                        
                        
                        
                    }else{ //passes
                    $currendate_d=date('Y-m-d');
                    $currentDatetime = date('Y-m-d H:i:s',strtotime($currendate_d.' '.'00:00:00'));
                        $CurrentToDateTime =date('Y-m-d H:i:s');
                        
                        $checkBookingVerify =  $this->db->select('*')->from('ci_booking_verify')
                                                ->where('booking_id',$booking_id)->where('verify_status','1')
                                                ->where('onCreated>=',$currentDatetime)
                                                ->where('onCreated<=',$CurrentToDateTime)
                                                ->get()->result_array();
                                                // print_r($checkBookingVerify);
                    if(count($checkBookingVerify) > 0)
                    {
                        $verifyStatus=true;
                    }else{
                        $verifyStatus=false;
                        // $isShowExtndBtn=false;
                    } 
                        // $isShowExtndBtn=$verifyStatus==true?true:false;
                         $checkExtention = $this
                        ->db
                        ->select('*')
                        ->from('ci_booking')
                        ->where('replaced_booking_id', $booking_detail[0]['booking_id'])->where('booking_from_date',date('Y-m-d'))
                        ->where('booking_to_date',date('Y-m-d'))->where('is_deleted', '0')
                        ->get()
                        ->result();
                        if(count($checkExtention)<=0)
                        {
                            $currendate_d=date("Y-m-d");
                            $currentDatetime=new DateTime($CurrentToDateTime);
                            $toDate_s =new DateTime(date('Y-m-d H:i:s', strtotime($currendate_d .' '. $booking_detail[0]['to_time'])));
                            $interval =$currentDatetime->diff($toDate_s);
                            // print_r($interval);
                            if($interval->invert==1)
                            {
                                if($interval->days==0&&$interval->h==0&&$interval->i<=5){
                                    // print('in');
                                    $isShowExtndBtn=true;
                                }else{
                                    // print('out');
                                    $isShowExtndBtn=false;
                                }
                            }
                            else
                            {
                                // print('outer');
                                $isShowExtndBtn=true;
                            }
                        }
                        else{
                             $extendedStatus =  true ; 
                            $isShowExtndBtn= false ;
                        }
                        
                    
                    
                                                
                        
                        // print($isShowExtndBtn);
                       
                        
                        
                        
                        
                    }
                    
                    
                    
                    $bookingDetail = array(
                        "booking_id" => $booking_detail[0]['booking_id'],
                        "unique_booking_id" => $booking_detail[0]['unique_booking_id'],
                        "book_ext" => $booking_detail[0]['book_ext'],
                        "booking_status" => $booking_detail[0]['booking_status'],
                        "replaced_booking_id" => $booking_detail[0]['replaced_booking_id'],
                        "booking_from_date" => $booking_detail[0]['booking_from_date'],
                        "booking_to_date" => $booking_detail[0]['booking_to_date'],
                        "from_time" => $booking_detail[0]['from_time'],
                        "to_time" => $booking_detail[0]['to_time'],
                        "booking_type" => $booking_detail[0]['booking_type'],
                        "placename" => $booking_detail[0]['placename'],
                        "place_address" => $booking_detail[0]['place_address'],
                        "latitude" => $booking_detail[0]['latitude'],
                        "longitude" => $booking_detail[0]['longitude'],
                        "fromTimeForCancel" => $booking_detail[0]['fromTimeForCancel'],
                        "car_number" => $booking_detail[0]['car_number'],
                        "cost" => $booking_detail[0]['cost'],
                        "pricetype" => $booking_detail[0]['pricing_type'],
                        "display_id" => $booking_detail[0]['display_id'],
                        "to_time_24hrs" => $booking_detail[0]['to_time_24hrs'],
                        "replaced_for_booking_id" => $booking_detail[0]['replaced_for_booking_id'],
                        "mobile_no" => $booking_detail[0]['mobile_no'],
                        "extenPriceSlab" => $ext_price,
                        "extendStatus" => $extendedStatus,
                        'isShowExtndBtn'=>$isShowExtndBtn,
                        "place_id"=>$booking_detail[0]['place_id'],
                        "verifyStatus" => $verifyStatus,
                        "verfierscontactList"=>$verfierscontactList
                    );

                    $msg = array(
                        'status' => true,
                        'msg' => "Bookings details.",
                        'data' => $bookingDetail,
                        'session' => '1'
                    );
                    echo json_encode($msg);

                }
                else
                {
                    $msg = array(
                        'status' => false,
                        'msg' => "No data found. !!",
                        'data' => $booking_detail,
                        'session' => '1'
                    );
                    echo json_encode($msg);
                }

            }
            else
            {
                $msg = array(
                    'status' => false,
                    'message' => 'Session expired',
                    'session' => '0'
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
    /*public function get_booking_detail() //Single Booking details screen in app 
    {
        
            date_default_timezone_set('Asia/Kolkata');
        $this->form_validation->set_rules('booking_id', 'Booking Id', 'required');
        $this->form_validation->set_rules('token', 'Token', 'required');  
        
        if ($this->form_validation->run()) {
            $token = $this->security->xss_clean($this->input->post('token'));
            
            $verifyToken = $this->tokenVerify($token);
                    
            if($verifyToken==true){
                        
            $tokenData = $this->tokenDecodeData($token);
            
            $booking_id = $this->security->xss_clean($this->input->post('booking_id'));
            
            // $booking_detail = $this->db->select('tbl_booking.id as booking_id, tbl_booking.*, tbl_parking_places.placename,tbl_parking_places.place_address')->from('tbl_booking')->join('tbl_parking_places', 'tbl_booking.place_id = tbl_parking_places.id')->where('tbl_booking.id', $booking_id)->where('tbl_booking.is_deleted','0')->get()->result_Array();
            $booking_detail = $this->db->select('ci_booking.id as booking_id, ci_booking.unique_booking_id,
            ci_booking.book_ext,ci_booking.user_id,ci_booking.place_id,ci_booking.booking_status,ci_booking.slot_id,
            ci_booking.replaced_booking_id,ci_booking.booking_from_date,ci_booking.booking_to_date,ci_booking.from_time,ci_booking.to_time,ci_booking.booking_type,ci_booking.cost,
            ci_booking.car_id
            , ci_parking_places.placename,ci_parking_places.place_address,ci_parking_places.latitude,ci_parking_places.pricing_type,ci_parking_places.ext_per,
            ci_parking_places.longitude')->from('ci_booking')->join('ci_parking_places', 'ci_booking.place_id = ci_parking_places.id')->where('ci_booking.id', $booking_id)->where('ci_booking.is_deleted','0')->get()->result_Array();
            
            if(count($booking_detail)>0){
                $from_d = strtotime($booking_detail[0]['booking_from_date']);
            $to_d = strtotime($booking_detail[0]['booking_to_date']);
            $from_t = strtotime($booking_detail[0]['from_time']);
            $to_t = strtotime($booking_detail[0]['to_time']);
            
            $from_date = date("d M Y", $from_d);
            $to_date = date("d M Y", $to_d);
            $fromTimeForCancel = date("H:i:s", $from_t);
            $from_time = date("h:i a", $from_t);
            $to_time = date("h:i a", $to_t);
            $to_time_24hrs = date("H:i:s",$to_t);
            
            
            
            $datetime = "$from_date - $to_date, $from_time - $to_time";
            
            $booking_detail[0]['datetime'] = $datetime;
            $booking_detail[0]['fromTimeForCancel'] = $fromTimeForCancel;
            $booking_detail[0]['car_name']='';
            $booking_detail[0]['car_number']='';
            $booking_detail[0]['slot_name']='';
            $booking_detail[0]['display_id']='';
            $booking_detail[0]['from_time']=$from_time;
            $booking_detail[0]['to_time']=$to_time;//unique_booking_id
             $booking_detail[0]['to_time_24hrs']=$to_time_24hrs;
            
            $booking_slotDetails=$this->db->select('*')->from('ci_parking_slot_info')->where('slot_no',$booking_detail[0]['slot_id'])->get()->result();
            if(count($booking_slotDetails)>0){
                $booking_detail[0]['slot_name']=$booking_slotDetails[0]->slot_name;
                $booking_detail[0]['display_id']=$booking_slotDetails[0]->display_id;
            }
            
            $carDetails = $this->db->select('*')->from('ci_car_details')->where('id',$booking_detail[0]['car_id'])->get()->result();
            
            $replaceBookingId=$booking_detail[0]['replaced_booking_id'];
                // $replacedforBookingId='';
                $booking_detail[0]['replaced_for_booking_id']='';
                $getraplacedData = $this->db->select('unique_booking_id')->from('ci_booking')->where('id',$replaceBookingId)->get()->result();
                if(count($getraplacedData)>0){
                    $booking_detail[0]['replaced_for_booking_id']=$getraplacedData[0]->unique_booking_id;
                }
            
            $getVerifierPlace = $this->db->select('*')->from('tbl_verifier_place')->where('place_id',$booking_detail[0]['place_id'])->where('isDeleted','0')->get()->result();
	                       //print_r($getVerifierPlace);
	                       if(count($getVerifierPlace)>0){
	                       $getVerifierDetails= $this->db->select('*')->from('ci_admin')->where('admin_id',$getVerifierPlace[0]->verifier_id)->where('admin_role_id','3')->where('is_active','1')->get()->result();
	                       if(count($getVerifierDetails)>0){$booking_detail[0]['mobile_no'] = $getVerifierDetails[0]->mobile_no;}
	                       else{
	                           $booking_detail[0]['mobile_no'] ='';}
	                           
	                       }else{
	                           $booking_detail[0]['mobile_no'] ='';
	                       }
	                       
            // print_r($carDetails);
            if(count($carDetails)>0){
                foreach($carDetails as $c){
                    $booking_detail[0]['car_name']=$c->car_name;
                $booking_detail[0]['car_number']=$c->car_number;
                }
                
            }
            $extendBookingPrice= $this->db->select('hrs,cost')->from('ci_price_slab') 
            ->where('place_id',$booking_detail[0]['place_id'])
            ->where('pass',$booking_detail[0]['pricing_type']=='0'?1:'0')->where('is_deleted','0')->get()->result();
            
            $ext_price=[];
            // if($booking_detail[0]['pricing_type']=='0'){
                foreach($extendBookingPrice as $ext){
                                    $extcost =(int)$ext->cost+ ((int)$ext->cost/100)*$booking_detail[0]['ext_per'];
                                    array_push($ext_price,array('hrs'=>(String)$ext->hrs,'cost'=>(String)$extcost));
                }
                
            // }
            
            $booking_detail[0]['extenPriceSlab']=$extendBookingPrice;
            $extendedStatus = false;
            $checkExtention = $this->db->select('*')->from('ci_booking')->where('replaced_booking_id',$booking_id)->where('is_deleted','0')->get()->result();
            $extendedStatus = count($checkExtention)>0?true:false;
            $checkBookingVerify = $this->db->select('verify_status')->from('ci_booking_verify')->where('booking_id',$booking_id)->where('isDeleted','0')->get()->result();
            // print_r($checkBookingVerify);
            // exit();
            $verifyStatus = count($checkBookingVerify)>0?true:false;
            $bookingDetail =array(
            "booking_id"=> $booking_detail[0]['booking_id'],
            "unique_booking_id"=> $booking_detail[0]['unique_booking_id'],
            "book_ext"=> $booking_detail[0]['book_ext'],
            "booking_status"=> $booking_detail[0]['booking_status'],
            "replaced_booking_id"=> $booking_detail[0]['replaced_booking_id'],
            "booking_from_date"=> $booking_detail[0]['booking_from_date'],
            "booking_to_date"=> $booking_detail[0]['booking_to_date'],
            "from_time"=> $booking_detail[0]['from_time'],
            "to_time"=> $booking_detail[0]['to_time'],
            "booking_type"=> $booking_detail[0]['booking_type'],
            "placename"=> $booking_detail[0]['placename'],
            "place_address"=> $booking_detail[0]['place_address'],
            "latitude"=> $booking_detail[0]['latitude'],
            "longitude"=> $booking_detail[0]['longitude'],
            "fromTimeForCancel"=> $booking_detail[0]['fromTimeForCancel'],
            "car_number"=> $booking_detail[0]['car_number'],
            "cost"=> $booking_detail[0]['cost'],
            "pricetype"=> $booking_detail[0]['pricing_type'],
            "display_id"=>$booking_detail[0]['display_id'],
            "to_time_24hrs"=> $booking_detail[0]['to_time_24hrs'],
            "replaced_for_booking_id"=>$booking_detail[0]['replaced_for_booking_id'],
            "mobile_no"=> $booking_detail[0]['mobile_no'],
            "extenPriceSlab"=>$ext_price,
            "extendStatus"=>$extendedStatus,
            "verifyStatus"=>$verifyStatus,
            );
           
            $msg = array('status' => TRUE, 'msg' => "Bookings details.", 'data' => $bookingDetail,'session'=>'1');
            echo json_encode($msg);
                
                
            }else {
                    $msg = array('status' => false, 'msg' => "No data found. !!", 'data' => $booking_detail,'session'=>'1');
                    echo json_encode($msg);
                }
            
        }else{
            $msg = array('status' => false, 'message' => 'Session expired','session'=>'0');
            echo json_encode($msg);
        }
        }
        else{
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1');
            echo json_encode($msg);
        }
            
        
    }*/
    
    public function booking_cancel()
    {
        
        
        $this->form_validation->set_rules('token', 'Token', 'required');  
        $this->form_validation->set_rules('booking_id', 'Booking Id', 'required');  
        
        if ($this->form_validation->run()) {
            $token = $this->security->xss_clean($this->input->post('token'));
            $booking_id = $this->security->xss_clean($this->input->post('booking_id'));
            
            $verifyToken = $this->tokenVerify($token);
                    
            if($verifyToken==true){
                        
                $tokenData = $this->tokenDecodeData($token);
                
                $booking= $this->db->select('*')->from('ci_booking')->where('id',$booking_id)->where('user_id',$tokenData->id)->where('booking_status','0')->get()->result();
                if(count($booking)>0){
                
                  $cancledBooking=  $this->db->where('id',$booking_id)->update('ci_booking',array('booking_status'=>'2'));
                  if($cancledBooking){
                    $get_amt = $this->db->select('*')->from('ci_wallet_user')->where('user_id',$tokenData->id)->get()->result();
                    if(count($get_amt)>0){
                        $new_amt =$get_amt[0]->amount+$booking[0]->cost;
                        $this->db->where('id',$get_amt[0]->id)->update('ci_wallet_user',array('amount'=>(float)$new_amt));
                        $inserData1=array("wallet_id"=>$get_amt[0]->id,"user_id"=>$get_amt[0]->user_id,"amount"=>$booking[0]->cost,
                        "status"=>'1',"payment_type"=>'3','booking_id'=>$booking_id,
                            'last_wallet_amount'=>$get_amt[0]->amount);
                        // $insertPayment1 = $this->db->insert('ci_wallet_history',$inserData1);
                        $this->wallet_history_log($inserData1);
                        
                        $getNotify = $this->db->select('*')->from('ci_notify_track')->where('booking_id',$booking_id)->where('user_id',$tokenData->id)
                            ->where('notify_type','5')
                            ->where('is_deleted','0')
                            ->get()->result();
                            $emoji ="\u{E007F}";
                    $message= 'Your booking has been cancelled '.$emoji.' & ₹ '.$booking[0]->cost.' has been refunded to your wallet';
                    $this->notificationForWallet($tokenData->id,$booking_id,$booking[0]->place_id,$booking[0]->slot_id, 'Booking & Wallet', $message,'6','5'); //6= booking list screen,5= refunded
                    }
                      
                  }
                    $msg = array('status' => true, 'message' => "Successfully cancelled Booking",'session'=>'1');
                    echo json_encode($msg);
                
                }else {
                    $msg = array('status' => false, 'message' => "You cannot cancle this Booking !!",'session'=>'1');
                    echo json_encode($msg);
                }
            
            }else{
            $msg = array('status' => false, 'message' => 'Session expired','session'=>'0');
            echo json_encode($msg);
        }
            
        }else{
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1');
            echo json_encode($msg);
        }
        
    
    }
    
    public function booking_extention()
    {

        // exit();
        $this
            ->form_validation
            ->set_rules('token', 'Token', 'required');
        $this
            ->form_validation
            ->set_rules('ext_hrs', 'Car Id', 'required');
        $this
            ->form_validation
            ->set_rules('bookingId', 'Car Id', 'required');
        $this
            ->form_validation
            ->set_rules('cost', 'Cost', 'required');
        $this
            ->form_validation
            ->set_rules('uniqueBookingId', 'UniqueBookingId', 'required');
        if ($this
            ->form_validation
            ->run())
        {

            $token = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('token'));
            $verifyToken = $this->tokenVerify($token);
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
            $appcost = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('cost'));
            $uniqueBookingId = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('uniqueBookingId'));
                $cost='0';
            if ($verifyToken == true)
            {

                $tokenData = $this->tokenDecodeData($token);
                
                

                $this
                    ->db
                    ->where('id', $tokenData->id);
                $this
                    ->db
                    ->where('is_verify', '1')->where('is_active','1');
                // $this->db->where('role', '10');
                $check_token = $this
                    ->db
                    ->get('ci_users');

                if ($check_token->num_rows() > 0)
                {
                    $getBookingDetails=$this->db->select('*')->from('ci_booking')->where('id',$bookingId)
                ->where('booking_status','0')->where('user_id',$tokenData->id)->get()->result_array();
                if(count($getBookingDetails)>0){
                   $cost= $this->bookingExtent_priceCal($getBookingDetails[0]['place_id'],$ext_hrs);
                   $cost=$cost>$appcost?$cost:$appcost; // this is due to app round off
                    // print('cost :');
                    // print($cost);
                    
                    // die;
                    $user_data = $check_token->result_array();

                    $user_id = $user_data['0']['id'];
                    $get_wallet_amt = $this
                        ->db
                        ->select('*')
                        ->from('ci_wallet_user')
                        ->where('user_id', $tokenData->id)
                        ->get()
                        ->result();
                    if (count($get_wallet_amt) > 0)
                    {
                        $new_amt = $get_wallet_amt[0]->amount;
                        if ($cost < $new_amt)
                        {
                            $extention = $this->book_ext_Booking($bookingId, $ext_hrs, $cost, $uniqueBookingId);
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
                        }
                        else
                        {

                            $msg = array(
                                'status' => false,
                                'message' => 'Kindly refill your wallet.',
                                'id' => "",
                                'session' => '1',
                                'walletStatus' => '1'
                            ); // walletStatus 1  = redirect to wallet page for refill
                            echo json_encode($msg);

                        }
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
                        'message' => 'Invalid Token',
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
                    'message' => 'Session expired',
                    'session' => '0',
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
    
    public function book_ext_Booking($booking_id,$extHrs,$cost,$unique_booking_id)
    {
        
            date_default_timezone_set('Asia/Kolkata');
              
	   
	           $bookingId = $booking_id;
	           $getbooking=[];
	           $getBookingDetails=$this->db->select('*')->from('ci_booking')
	           //->where('id',$bookingId)
	           ->where('unique_booking_id',$unique_booking_id)->where('book_ext','')
	           ->order_by("id", "Desc")->get()->result();
	           if(count($getBookingDetails)>0){
	               array_push($getbooking,$getBookingDetails[0]);
	           }
	           //$this->db->where('id',$bookingId)->update('tbl_booking',array('booking_status'=>'4'));
	           $getbookingExt = $this->db->select('*')->from('ci_booking')
	           //->where('id',$bookingId)
	           ->where('unique_booking_id',$unique_booking_id)
	           ->like('book_ext','EXT') 
	           ->order_by("id", "asc")->get()->result();
	           //print_r($getbooking);
	           //exit();
	           foreach($getbookingExt as $b)
	           {
	               array_push($getbooking,$b);
	           }
	           $getbooking=array_reverse($getbooking);
	           //print_r($getbooking);
	           //exit();
	           if(count($getbooking)>0){
	               
	               /*unique_booking_id	user_id	place_id	slot_id	booking_status 	
replaced_booking_id	booking_from_date	booking_to_date	from_time	to_time	paid_status  
booking_type daily cost	reserve_from_time reserve time is 30 < 	reserve_to_time reserve time is 30 > 	vendor_id	car_id*/
                    $fromDate_d;$fromTime_d;$to_Date_d;$to_Time_d;$replaced_booking_id;
                    // print_r($getbooking[sizeof($getbooking) - 1]);
                    // exit();
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
                        $replaced_booking_id=$getbooking[0]->id;
                        // print($replaced_booking_id.' hi');
                    // exit();
                    }
                    else
                    {
                        $checksameDayExt = $this->db->select('*')->from('ci_booking')->where('unique_booking_id',$unique_booking_id)
                        ->where('booking_from_date<=',date('Y-m-d'))
                        ->where('booking_to_date>=',date('Y-m-d'))
                        ->where('booking_type','0')
                        ->order_by("id", "Desc")
                        ->get()->result_array();
                        // print_r($checksameDayExt);
                        // exit();
                        if(count($checksameDayExt)>0)
                        {
                            $fromDate_d=date('Y-m-d');
                            $fromTime_d=(String)$checksameDayExt[0]['to_time'];
                            $fromDateTime = date('Y-m-d H:i:s',strtotime($fromDate_d.' '.$fromTime_d));
                            $addhrs = ' + '.$extHrs.' hours';
                            $newtimestamp = strtotime( $fromDateTime.$addhrs);
                            $newTodatetime = date('Y-m-d H:i:s', $newtimestamp);
                            
                            $to_Date_d=date('Y-m-d', strtotime($newTodatetime));
                            $to_Time_d=date('H:i:s', strtotime($newTodatetime));
                            
                            $replaced_booking_id=$checksameDayExt[0]['id'];
                        //      print_r($fromDate_d);
                        // print(' - ');
                        // print_r($to_Date_d);
                            if($fromDate_d!=$to_Date_d){
                                return false;
                            }
                        }
                        else
                        {
                            $fromDate_d=date('Y-m-d');
                            $fromTime_d=(String)$getbooking[sizeof($getbooking)-1]->to_time;
                            $fromDateTime = date('Y-m-d H:i:s',strtotime($fromDate_d.' '.$fromTime_d));
                            $addhrs = ' + '.$extHrs.' hours';
                            $newtimestamp = strtotime( $fromDateTime.$addhrs);
                            $newTodatetime = date('Y-m-d H:i:s', $newtimestamp);
                            
                            $to_Date_d=date('Y-m-d', strtotime($newTodatetime));
                            $to_Time_d=date('H:i:s', strtotime($newTodatetime));
                            
                            $replaced_booking_id=$bookingId;
                            //  print_r($fromDate_d);
                            // print(' - ');
                            // print_r($to_Date_d);
                             if($fromDate_d!=$to_Date_d){
                                return false;
                            }
                        }
                       
                        // exit();
                    
                    }
                   
                    $bookinglist_check =  $this->bookinglist_check($getbooking[0]->place_id,$fromDate_d,$to_Date_d,$fromTime_d,$to_Time_d,$getbooking[0]->slot_id,$getbooking[0]->unique_booking_id);
	              
	                $datainsert=array(
	                   // 'unique_booking_id'=>$bookingid1,	
	                    'user_id'	=>$getbooking[0]->user_id,
	                    'place_id'	=>$getbooking[0]->place_id,
	                    'slot_id'	=>$getbooking[0]->slot_id,
	                    'unique_booking_id'=>$getbooking[0]->unique_booking_id,
	                    'booking_status'=>'0',
	                    'replaced_booking_id'=>$replaced_booking_id,
                        // 'replaced_booking_id'	=>$getbooking[sizeof($getbooking) - 1]->booking_type==0?$getbooking[0]->id:$getbooking[sizeof($getbooking) - 1]->id,
                        'booking_from_date'	=>$fromDate_d,
                        'booking_to_date'	=>$to_Date_d,
                        'from_time'=>$fromTime_d,
                        'to_time'	=>$to_Time_d,
                        'paid_status'=>$getbooking[0]->paid_status,
                        'booking_type'=>'0',
                        'cost'	=>$cost,
                        'reserve_from_time' =>$fromTime_d,	
                        'reserve_to_time' =>$to_Time_d,
                        'vendor_id'=>$getbooking[0]->vendor_id,
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
	                    $this->replace_bookings($bookinglist_check);
	                    
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

    public function replace_bookings($bookingList)
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
                        $this->notificationallApiBuilding($b,'Your Booking',$message,'3','1',true); //3= Your booking detail screen
                    // $message = 'A Slot ( ID : '.$b->slot_id.') has been booked at '.$getplaceName[0]->placename.' From '.$b->from_time.' to '.$b->to_time ;
                    // $this->notificationApiVerifier($b,'Booking',$message,'0','0');
                    }
                    else{
                        print('Notification already went');
                    }
                }
                }else{
                    $this->booking_cancel_ext($b->id);
                }
                
            
        }
    }
    
    public function booking_cancel_ext($bookingId)
    {
            
            $booking_id = $bookingId;
            
            
                
                $booking= $this->db->select('*')->from('ci_booking')->where('id',$booking_id)->where('booking_status','0')->get()->result();
                
                if(count($booking)>0){
                $user_id =$booking[0]->user_id ;
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
    
    public function check_slot_available($place_id,$from_date,$to_date,$from_time,$to_time,$slotid) //slot availibility
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
                $slotId_availability='false';
                foreach($slot_availabledata as $s){
                    // print(' - '.$s['slot_no'].' - ');
                    if($s['slot_no']==$slotid){
                        // print(' << '.$slotid.' >> ');
                        // print($s['bookedStatus']);
                        if(trim($s['bookedStatus']," ")=='0'){
                        $slotId_availability='true';
                        // print($slotid);
                    //   $slotId=$slotId==''?$s['slot_no']:'';
                       break;
                    //   print(':'.$slotId.' : ');
                    }
                    }
                    
                }
                // print($slotId);
                // print_r($slotId_availability);
                // exit();
                return $slotId_availability;
                // echo json_encode(
                //     array("status"=>true,
                //     "message"=>"list of slots",
                //     "data"=>$slot_availabledata,'session'=>'1'));
        
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

    public function notificationallApiBuilding($b, $title, $body, $screen, $notifyType,$insertoDB) // this function is uses firebase api to send notification.   bool $insertoDB =true or false
    {
        // $buildingId = 394;
        // $societyId = 14;
        $getUserTopic = $this
            ->db
            ->select('notifn_topic')
            ->from('ci_users')
            ->where('id', $b->user_id)
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
            print (' mmmm ');
            print ($token);
            print (' mmmm ');
            // print($token);
            $notification = ['title' => $title, 'body' => $body, 'icon' => 'myIcon', 'sound' => 'default_sound'];

            $extraNotificationData = ['title' => $title, 'body' => $body, 'screen' => $screen, 'bookingid' => $b->id, "click_action" => "FLUTTER_NOTIFICATION_CLICK"];

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
                        "booking_id" => $b->id,
                        "user_id" => $b->user_id,
                        "place_id" => $b->place_id,
                        "slot_id" => $b->slot_id
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
    
    public function aboutUs_list()
    {
       
            $check_token1 = $this->db->select('*')->from('tbl_about_us')->where('module', '1')->get()->result(); //check token
            $list1=[];
            if (count($check_token1) > 0) {

                foreach($check_token1 as $c){
                    $c->details= str_replace("\n", "",base64_decode($c->details));;
                    array_push($list1,$c);
                }
                // print_r($list1);
                $msg = array('status' => true, 'message' => 'List of data.','data'=>$list1);
                echo json_encode($msg);
            }
            else {
                $msg = array('status' => false, 'message' => 'No data found.','data'=>[]);
                echo json_encode($msg);
            }
       
    }
    
    // cronjob apis 
    public function notiftn_before_halfHour() //This api send notification to users before 30 min of bookig fromtime  // cronjob apis
    {
        date_default_timezone_set('Asia/Kolkata');
        $current_date = date('Y-m-d');
        // print($current_date);
        $getbookings = $this->db->select('*')->from('ci_booking')
        ->where('booking_status','0')
        ->where('DATE(booking_from_date) <=', $current_date)
        ->where( 'DATE(booking_to_date) >=', $current_date)
        ->where('is_deleted','0')->get()->result();
        print_r($getbookings);
            foreach($getbookings as $b){
                // $message = 'Your booking '.$b->unique_booking_id;
                // print($message);
                 $end= strtotime($b->from_time);
                $start = strtotime(date('H:i:s'));
                $mins = ($end -$start ) / 60;
                print($mins.' '.$b->id);
                print('<br>');
                // print($end);
                // print('<br>');
                
                $end1= strtotime($b->to_time);
                $start1 = strtotime(date('H:i:s'));
                $mins1 = ($end1 -$start1 ) / 60;
                print($mins1);
                print('\n\n');
                
                if($mins<=30&&$mins>=-5){
                    // print($b->id);
                    // print('<br>');
                    
                    $getplaceName = $this->db->select('placename')->from('ci_parking_places')->where('id',$b->place_id)->where('place_status','1')->where('is_deleted','0')->get()->result();
                    $getSlotName = $this->db->select('display_id,slot_name')->from('ci_parking_slot_info')->where('slot_no',$b->slot_id)->where('status','0')->where('is_deleted','0')->get()->result();
                    $slotname =count($getSlotName)>0?$getSlotName[0]->display_id.' ( '.$getSlotName[0]->slot_name.' ) ':$b->slot_id;
                    if(count($getplaceName)>0){
                    $message = 'You have booked '.' Slot No. : '.$slotname.' at '.$getplaceName[0]->placename.' From '.$b->from_time.' to '.$b->to_time.' Booking ID : '.$b->unique_booking_id ;
                    
                    $getNotify = $this->db->select('*')->from('ci_notify_track')->where('booking_id',$b->id)->where('user_id',$b->user_id)
                    ->where('notify_type','1')
                    ->where('is_deleted','0')
                    ->get()->result();
                    print_r($getNotify);
                    if(count($getNotify)<=0){
                        print($message);
                    $this->notificationallApiBuilding($b,'Your Booking',$message,'3','1',true); //3= Your booking detail screen
                    $message = 'A Slot ( ID : '.$b->slot_id.') has been booked at '.$getplaceName[0]->placename.' From '.$b->from_time.' to '.$b->to_time ;
                    $this->notificationApiVerifier($b,'Booking',$message,'0','0');
                    }else{
                        print('Notification already went');
                    }
                        
                    }}   
                    if($mins1<=15&&$mins1>=1){
                        $getplaceName = $this->db->select('placename')->from('ci_parking_places')->where('id',$b->place_id)->where('place_status','1')->where('is_deleted','0')->get()->result();
                    $getSlotName = $this->db->select('display_id,slot_name')->from('ci_parking_slot_info')->where('slot_no',$b->slot_id)->where('status','0')->where('is_deleted','0')->get()->result();
                    $slotname =count($getSlotName)>0?$getSlotName[0]->display_id.' ( '.$getSlotName[0]->slot_name.' ) ':$b->slot_id;
                    if(count($getplaceName)>0){
                    // $message = 'Your booking ('.$b->unique_booking_id.') of slot ID : '.$slotname.' at '.$getplaceName[0]->placename.' will end under '.round($mins1,0).' min.' ;
                    $message = 'Your Booking ID :'.$b->unique_booking_id.' at '.$getplaceName[0]->placename.' on '.$slotname.' will end under '.round($mins1,0).' min.'.
                    'Please checkout in time to avoid inconvenience or Extend your booking if you wish to park longer 🚗.';
                    
                    $getNotify = $this->db->select('*')->from('ci_notify_track')->where('booking_id',$b->id)->where('user_id',$b->user_id)
                    ->where('notify_type','7')
                    ->where('is_deleted','0')
                    ->get()->result();
                    print_r($getNotify);
                    if(count($getNotify)<=0){
                        print($message);
                    $this->notificationallApiBuilding($b,'Booking End',$message,'3','7',true); //3= Your booking detail screen
                    // $message = 'A Slot ( ID : '.$b->slot_id.') has been booked at '.$getplaceName[0]->placename.' From '.$b->from_time.' to '.$b->to_time ;
                    // $this->notificationApiVerifier($b,'Booking',$message,'0','0');
                    }else{
                        print('Notification already went');
                    }
                        
                    }
                    }
            }
        // print_r($getbookings);
    }
    
    public function notificationApiVerifier($b, $title, $body,$screen,$notifyType) // this function is uses firebase api to send notification.
    {
        // $buildingId = 394;
        // $societyId = 14;
        $getVerifierList=$this->db->select('*')->from('tbl_verifier_place')->where('place_id',$b->place_id)->where('isDeleted','0')->get()->result();
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
                'sound' => 'default_sound'
            ];
            
            $extraNotificationData = ['title' =>$title,
                'body' => $body,'screen'=>$screen,'bookingid'=>$b->id,
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
            // if($result){
            //     $this->db->insert('tbl_notify_track',array("notify_type"=>$notifyType,"booking_id"=>$b->id,"user_id"=>$b->user_id,"place_id"=>$b->place_id,"slot_id"=>$b->slot_id ));
            // }
            curl_close($ch);
    
    
            // echo $result;
                
            }else{
                // echo 'no building found'.$buildingid;
            }
            
        }
    }
    
    public function onAutoCompleted_bookingApi() // cronjob apis
    {

        $getBookingList = $this
            ->db
            ->select('*')
            ->from('ci_booking')
            ->where('booking_status', '0')
            // ->group_start()->where('booking_status','0')->or_where('booking_status','1')->group_end()
            ->where('is_deleted', '0')
            ->get()
            ->result();
            // print_r($getBookingList);
        foreach ($getBookingList as $booking)
        {
          
                date_default_timezone_set('Asia/Kolkata');
                $currentDateTime = date('Y-m-d');
                $toDateTime = date('Y-m-d', strtotime($booking->booking_to_date . '+1 day'));
                print_r($currentDateTime);
                print('  -  ');
                print_r($toDateTime);
                print (' hii \n');
                // exit();
                if ($currentDateTime >= $toDateTime)
                {
                    // if($booking->id==451){
                    print ($booking->id);
                    // $this
                    //     ->db
                    //     ->where('id', $booking->id)
                    //     ->update('ci_booking', array(
                    //     'booking_status' => '1'
                    // ));
                $insertcheckout= $this->db->where('booking_id ',$booking->id)
    	           // ->where('created_at',date('Y-m-d',strtotime(date('Y-m-d') . '-1 day')))
    	           ->where('check_type',0)
    	           ->where('check_out',null)
    	            ->update('ci_booking_check',array('check_out'=>date("Y-m-d H:i:s"),
    	                   'updated_at'=>date("Y-m-d H:i:s"),'check_type'=>'2'));
    	                   if($insertcheckout){
                	           $checkout=  $this->db->where('id',$booking->id)->update('ci_booking',array(
                	                   'booking_status'=>'1'));
    	                   }
    	                       
    	                   //}
	           
                 //newcomment   //  $message = 'Your Booking ' . $booking->unique_booking_id . ' is completed. Visit again 😃 ';
                 //   //  print ($message);
                 //   //  $this->notificationallApiBuilding($booking, 'Booking Completed', $message, '3', '1',false); //3= Your booking detail screen // false = dont insert to DB
               
                    // if($booking-> replaced_booking_id!='0'){
                    /*$getBookingList = $this
                        ->db
                        ->select('*')
                        ->from('ci_booking')
                        ->where('unique_booking_id', $booking->unique_booking_id)
                        ->where('booking_status', '4')
                        ->order_by('id asc')
                        ->where('is_deleted', '0')
                        ->get()
                        ->result_array();
                    if (count($getBookingList) > 0)
                    {
                        print ($getBookingList[0]['id']);
                        $this
                            ->db
                            ->where('id', $getBookingList[0]['id'])->update('ci_booking', array(
                            'booking_status' => '0'
                        ));
                    }*/
                    // }
                    print ($booking->id);
                    print ('\n');
                }

            
        }
        // exit();
         date_default_timezone_set('Asia/Kolkata');
        $getBookingListPass = $this
            ->db
            ->select('*')
            ->from('ci_booking')
            ->where('booking_status', '1')->where('booking_from_date<=',date('Y-m-d'))->where('booking_to_date>=',date('Y-m-d'))
            ->where('booking_type','1')
            ->where('is_deleted', '0')
            ->get()
            ->result();
            print_r($getBookingListPass);
            foreach($getBookingListPass as $booking){
                
                $currentDateTime = date('Y-m-d H:i:s');
                $toDateTime = date('Y-m-d H:i:s',strtotime($booking->booking_to_date.' '.$booking->reserve_to_time));
                print($toDateTime);
                print('\n');
                if($currentDateTime<=$toDateTime){
                    print(' --  '.$booking->id.' updated');
                    $this->db->where('id',$booking->id)->update('ci_booking',array('booking_status'=>'0'));
                    
                }
            }
        
    }
    
    public function slotReservedRemoving() // cronjob apis
    {
        date_default_timezone_set('Asia/Kolkata');
        $getSlotList = $this->db->select('*')->from('ci_parking_slot_info')->where('reserved_userId!=','0')->where('is_deleted','0')->get()->result();
         foreach($getSlotList as $slot){
             $currentDatetime =  new DateTime(date("Y-m-d H:i:s"));
             $interval = $currentDatetime->diff(new DateTime($slot->reserved_booking_time));
            // $elapsed = $interval->format('%y years %m months %a days %h hours %i minutes %s seconds');
            // echo $elapsed;
            // echo ' <<>> ';
          $time_ = $interval->i.' '.$interval->s;
          $min=$interval->i;
          $sec = $interval->s;
          $totalsec=($interval->i*60)+$sec;
            // echo $time_;
            // echo ' tt ';
            // echo $totalsec;
            if($totalsec>180){// 180 seconds 
                $reservedSlot = array("reserved_userId"=>0,
                                "reserved_booking_time"=>'');
                        $inserreserved_userid = $this->db->where('slot_no',$slot->slot_no)->update('ci_parking_slot_info',$reservedSlot);
            }
         }
    }
    
    public function cs_replaceBooking()
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->form_validation->set_rules('bookingId','Booking Type','required');
        if($this->form_validation->run()==false)
        {
             $errorMsg = $this->form_validation->error_array();
             $msg = array('status' => false, 'message' => $this->_returnSingle($errorMsg));
             echo json_encode($msg);
        }
        else
        {
           $bookingId = $this->input->post('bookingId');
           
           $getbooking = $this->db->select('*')->from('ci_booking')->where('id',$bookingId)->get()->result();
           if(count($getbooking)>0){
               
               /*unique_booking_id	user_id	place_id	slot_id	booking_status 	
                replaced_booking_id	booking_from_date	booking_to_date	from_time	to_time	paid_status  
                booking_type daily cost	reserve_from_time reserve time is 30 < 	reserve_to_time reserve time is 30 > 	vendor_id	car_id*/
                $slot_id =  $this->voice_slot_available($getbooking[0]->place_id,$getbooking[0]->booking_from_date,$getbooking[0]->booking_to_date,$getbooking[0]->from_time,$getbooking[0]->to_time);
                if($slot_id!='')
                {
                    $datainsert=array(
                   // 'unique_booking_id'=>$bookingid1,	
                    'user_id'	=>$getbooking[0]->user_id,
                    'place_id'	=>$getbooking[0]->place_id,
                    'unique_booking_id'=>$getbooking[0]->unique_booking_id,
                    'slot_id'	=>$slot_id,
                    'booking_status'=>'0', 	
                    'replaced_booking_id'	=>$getbooking[0]->id,
                    'booking_from_date'	=>$getbooking[0]->booking_type==1? date('Y-m-d'):$getbooking[0]->booking_from_date,
                    'booking_to_date'	=>$getbooking[0]->booking_type==1?date('Y-m-d'):$getbooking[0]->booking_to_date,
                    'from_time'=>$getbooking[0]->from_time,
                    'to_time'	=>$getbooking[0]->to_time,
                    'paid_status'=>$getbooking[0]->paid_status,
                    'booking_type'=>$getbooking[0]->booking_type,
                    'cost'	=>$getbooking[0]->cost,
                    'reserve_from_time' =>$getbooking[0]->reserve_from_time,	
                    'reserve_to_time' =>$getbooking[0]->reserve_to_time,
                    'vendor_id'=>$getbooking[0]->vendor_id,
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
                  /*  $rep_bookingId='';
                    $par_data=$this->db->select('*')->from('ci_booking')->like('unique_booking_id','PAR')->order_by("unique_booking_id", "Desc")->get()->result();
                    if(count($par_data)>0){
                        
                    $expoit = explode("R",$par_data[0]->unique_booking_id);
                    
                    $count = 8-strlen($expoit[1]+1);
                    $rep_bookingId =$expoit[1]+1;
                        for($i=0;$i<$count;$i++){
                        $rep_bookingId='0'.$rep_bookingId;
                        }
                        $rep_bookingId = 'PAR'.$rep_bookingId;
                    }else{
                        $rep_bookingId = 'PAR'.'00000001';
                    }
                    
                    $this->db->where('id',$last_id)->update('ci_booking',array('unique_booking_id'=>$rep_bookingId));*/
                    
                     $rep_bookingId='';
                        $par_data=$this->db->select('*')->from('ci_booking')->where('unique_booking_id',$getbooking[0]->unique_booking_id)->like('book_ext','REP')->order_by("id", "Desc")->get()->result();
                        if(count($par_data)>0)
                        {
                            
                            $expoit = explode("P",$par_data[0]->book_ext);
                            $count = $expoit[1]+1;
                            $rep_bookingId = 'REP'.$count;
                        }
                        else
                        {
                            $rep_bookingId = 'REP'.'1';
                        }
                        
	                    $this->db->where('id',$last_id)->update('ci_booking',array('book_ext'=>$rep_bookingId));
	                    $this->db->where('id',$bookingId)->update('ci_booking',array('booking_status'=>'4'));
                    
                        $getwalletid = $this->db->select('*')->from('ci_wallet_user')->where('user_id',$getbooking[0]->user_id)->get()->result();
                        if(count($getwalletid)>0)
                        {
                            
                            /*
                            	wallet_id	user_id	amount	status 	payment_type 	
                            	booking_id  */
                            	$walletDatainsert=array(
                            	    'wallet_id'=>$getwalletid[0]->id,	
                            	    'user_id'=>$getbooking[0]->user_id,	
                            	    'amount'=>0,	
                            	    'status'=>'4',
                            	    'payment_type'=>'5',
                            	    'booking_id'=>$last_id,
                            'last_wallet_amount'=>$getwalletid[0]->amount
                            	    );
                            // $this->db->insert('ci_wallet_history',$walletDatainsert);	
                            $this->wallet_history_log($walletDatainsert);
                        }
                        
                        $message = 'Your Booking : '.$getbooking[0]->unique_booking_id.' is replaced.';
                    
                    $getNotify = $this->db->select('*')->from('ci_notify_track')->where('booking_id',$getbooking[0]->id)->where('user_id',$getbooking[0]->user_id)
                    ->where('notify_type','8')
                    ->where('is_deleted','0')
                    ->get()->result();
                    print_r($getNotify);
                    if(count($getNotify)<=0)
                    {
                        print($message);
                        $this->notificationallApiBuilding($getbooking[0],'Your Booking',$message,'3','8',true); //3= Your booking detail screen
                        // $message = 'A Slot ( ID : '.$getwalletid[0]->slot_id.') has been booked at '.$getplaceName[0]->placename.' From '.$getwalletid[0]->from_time.' to '.$getwalletid[0]->to_time ;
                        $message ='Booking '.$getbooking[0]->unique_booking_id.' is replaced.';
                        $this->notificationApiVerifier($getbooking[0],'Booking',$message,'0','0');
                    }
                    else{
                        print('Notification already went');
                    }
                        
                    
                        print_r($datainsert);    
           }
           else
           {
             return "No slot available";  
           }
           }
        }
            
        
    }
    
    public function booking_cancel_web()
    {
        
        
        $this->form_validation->set_rules('userid', 'UserID', 'required');  
        $this->form_validation->set_rules('booking_id', 'Booking Id', 'required');  
        
        if ($this->form_validation->run()) {
            $userid = $this->security->xss_clean($this->input->post('userid'));
            $booking_id = $this->security->xss_clean($this->input->post('booking_id'));
            
           
                        
                // $tokenData = $this->tokenDecodeData($token);
                
                $booking= $this->db->select('*')->from('ci_booking')->where('id',$booking_id)->where('user_id',$userid)->get()->result();
                if(count($booking)>0){
                
                  $cancledBooking=  $this->db->where('id',$booking_id)->update('ci_booking',array('booking_status'=>'2'));
                  if($cancledBooking){
                    $get_amt = $this->db->select('*')->from('ci_wallet_user')->where('user_id',$userid)->get()->result();
                    if(count($get_amt)>0){
                        $new_amt =$get_amt[0]->amount+$booking[0]->cost;
                        $this->db->where('id',$get_amt[0]->id)->update('ci_wallet_user',array('amount'=>(float)$new_amt));
                        $inserData1=array("wallet_id"=>$get_amt[0]->id,"user_id"=>$get_amt[0]->user_id,"amount"=>$booking[0]->cost,
                        "status"=>'1',"payment_type"=>'3','booking_id'=>$booking_id,
                            'last_wallet_amount'=>$get_amt[0]->amount);
                        // $insertPayment1 = $this->db->insert('ci_wallet_history',$inserData1);
                         $this->wallet_history_log($inserData1);
                        
                        $getNotify = $this->db->select('*')->from('ci_notify_track')->where('booking_id',$booking_id)->where('user_id',$userid)
                            ->where('notify_type','5')
                            ->where('is_deleted','0')
                            ->get()->result();
                            $emoji ="\u{E007F}";
                    $message= 'Your booking has been cancelled '.$emoji.' & ₹ '.$booking[0]->cost.' has been refunded to your wallet';
                    $this->notificationForWallet($userid,$booking_id,$booking[0]->place_id,$booking[0]->slot_id, 'Booking & Wallet', $message,'6','5'); //6= booking list screen,5= refunded
                    }
                      
                  }
                    $msg = array('status' => true, 'message' => "Successfully cancelled Booking",'session'=>'1');
                    echo json_encode($msg);
                
                }else {
                    $msg = array('status' => false, 'message' => "You cannot cancle this Booking !!",'session'=>'1');
                    echo json_encode($msg);
                }
            
         
            
        }else{
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'session'=>'1');
            echo json_encode($msg);
        }
        
    
    }
    
    public function parkingplaceslistquery()
    {
        
        /*    $place_list = $this->db->select('ci_booking.user_id, ci_booking.place_id as place_id,ci_parking_places.placename as place_name ')
            ->from('ci_booking')->join('ci_parking_places', 'ci_booking.place_id = ci_parking_places.id')->where('ci_booking.user_id',$tokenData->id)
            ->where('ci_booking.is_deleted','0')->get()->result();*/
         $data['info']=$this->db->Select('ci_parking_places.*,ci_price_slab.hrs,ci_price_slab.cost')->from('ci_parking_places')
         ->join('ci_price_slab', 'ci_parking_places.id = ci_price_slab.place_id')
         ->where('ci_parking_places.place_status','1')->where('ci_parking_places.is_deleted', 0)->where('ci_price_slab.pass','0')->get()->result_array();
         print_r($data['info']);
    }
    
//     public function order_razorpay(){
// 	    // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
// 	    /*$data =array("amount"=>50000,
// 	    "currency"=>"INR",
// 	    "receipt"=>"rcptid_11");*/
// 	     $this->form_validation->set_rules('amount', 'Amount', 'required');  
// 	     $this->form_validation->set_rules('token', 'Token', 'required');  
// 	     $this->form_validation->set_rules('isLive', 'Check Live', 'required'); 
//         // $this->form_validation->set_rules('currency', 'Booking Id', 'required');  
//           $dataonEmpty= array(
//                         "id"=> "",
//                         "entity"=> "",
//                         "amount"=> 0,
//                         "amount_paid"=> 0,
//                         "amount_due"=> 0,
//                         "currency"=> "",
//                         "receipt"=> "",
//                         "offer_id"=> "",
//                         "status"=>"",
//                         "attempts"=> 0,
//                         "notes"=> [],
//                         "created_at"=> 0
//                     );
//         if ($this->form_validation->run()) {
//             $amount = $this->security->xss_clean($this->input->post('amount'));
//              $token = $this->security->xss_clean($this->input->post('token'));
//              $isLive = $this->security->xss_clean($this->input->post('isLive'));
//              $verifyToken = $this->tokenVerify($token);
            
//                             if($verifyToken==true)
//                                  {
//              $receipt = $this->create_referalCode(9);
//             // $booking_id = $this->security->xss_clean($this->input->post('booking_id'));
// 	    $data ='{"amount": '.$amount.',
//                     "currency": "INR",
//                     "receipt":'.'rcptid_11'.'
//                 }';
                
//             $ch = curl_init();
            
//             curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
//             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//             curl_setopt($ch, CURLOPT_POST, 1);
//             curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n    \"amount\": $amount,\n    \"currency\": \"INR\",\n    \"receipt\": \"$receipt\"\n}");
//             // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

//             $isLive=='true'?
//             curl_setopt($ch, CURLOPT_USERPWD, 'rzp_live_go5tICS12in2BY' . ':' . 'ivr9QZMoeDOj2WEfACWUHIts')
//             :curl_setopt($ch, CURLOPT_USERPWD, 'rzp_test_25fQbysZaqmc6L' . ':' . 'IZveLQPTPanBdJ5mx4XWMlzL');
         
//             $headers = array();
//             $headers[] = 'Content-Type: application/json';
//             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
//             $result = curl_exec($ch);
//             // print_r($result);
//             if (curl_errno($ch)) {
//                 // echo 'Error:' . curl_error($ch);
               
                
//                  echo json_encode(array('status'=>false,'message'=>'Order details fail to generate.','data'=>$dataonEmpty,'session'=>'1'));
//             }
//             else{
//                 $result= json_decode($result);
//                  echo json_encode(array('status'=>true,'message'=>'Order details successfully to generated.','data'=>$result,'session'=>'1'));
//              }
//              curl_close($ch);
//                                  }else{
//                                      $msg = array('status' => false, 'message' => 'Session expired','data'=>$dataonEmpty,'session'=>'0');
//             echo json_encode($msg);
//                                  }
//         }else{
//             $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'data'=>$dataonEmpty,'session'=>'1');
//             echo json_encode($msg);
//         }

// 	}
	public function order_razorpay()
    {
        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        /*$data =array("amount"=>50000,
        "currency"=>"INR",
        "receipt"=>"rcptid_11");*/
        $this
            ->form_validation
            ->set_rules('amount', 'Amount', 'required');
        $this
            ->form_validation
            ->set_rules('token', 'Token', 'required');
        $this
            ->form_validation
            ->set_rules('isLive', 'Check Live', 'required');
        // $this->form_validation->set_rules('currency', 'Booking Id', 'required');
        $dataonEmpty = array(
            "id" => "",
            "entity" => "",
            "amount" => 0,
            "amount_paid" => 0,
            "amount_due" => 0,
            "currency" => "",
            "receipt" => "",
            "offer_id" => "",
            "status" => "",
            "attempts" => 0,
            "notes" => [],
            "created_at" => 0
        );
        if ($this
            ->form_validation
            ->run())
        {
            $amount = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('amount'));
            $token = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('token'));
            $isLive = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('isLive'));
            $verifyToken = $this->tokenVerify($token);

            if ($verifyToken == true)
            {
                $receipt = $this->create_referalCode(9);
                $tokenData = $this->tokenDecodeData($token);
                // $booking_id = $this->security->xss_clean($this->input->post('booking_id'));
                $data = '{"amount": ' . $amount . ',
                    "currency": "INR",
                    "receipt":' . 'rcptid_11' . '
                }';

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n    \"amount\": $amount,\n    \"currency\": \"INR\",\n    \"receipt\": \"$receipt\"\n}");
                // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                $isLive == 'true' ? curl_setopt($ch, CURLOPT_USERPWD, 'rzp_live_go5tICS12in2BY' . ':' . 'ivr9QZMoeDOj2WEfACWUHIts') : curl_setopt($ch, CURLOPT_USERPWD, 'rzp_test_25fQbysZaqmc6L' . ':' . 'IZveLQPTPanBdJ5mx4XWMlzL');

                $headers = array();
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                // print_r($result);
                // print_r($result);
                if (curl_errno($ch))
                {
                    // echo 'Error:' . curl_error($ch);
                    

                    echo json_encode(array(
                        'status' => false,
                        'message' => 'Order details fail to generate.',
                        'data' => $dataonEmpty,
                        'session' => '1'
                    ));
                }
                else
                {
                    $result = json_decode($result);
                    $walletidData=$this->db->select('*')->from('ci_wallet_user')->where('user_id',$tokenData->id)->where('is_deleted','0')->get()->result_array();
                    $walletid=count($walletidData)>0?$walletidData[0]['id']:'';
                    /*	order_id	payment_id	wallet_id	amount	status  	email_id	contact_no	user_id	on_updated	on_created	is_deleted*/
                    // $jasonData=json_decode();
                    $insertTransactinProcess = array('order_id'=>$result->id,
                    'payment_id'=>'',
                    'wallet_id'=>$walletid,
                    'amount'=>($result->amount/100),
                    'status'=>'0', 
                    'is_live_payment'=>$isLive=='true'?'0':'1',
                    'user_id'=>$tokenData->id);//ci_transaction_history
                    
                    $this->db->insert('ci_transaction_history',$insertTransactinProcess);
                    // $result = json_decode($result);
                    echo json_encode(array(
                        'status' => true,
                        'message' => 'Order details successfully to generated.',
                        'data' => $result,
                        'session' => '1'
                    ));
                }
                curl_close($ch);
            }
            else
            {
                $msg = array(
                    'status' => false,
                    'message' => 'Session expired',
                    'data' => $dataonEmpty,
                    'session' => '0'
                );
                echo json_encode($msg);
            }
        }
        else
        {
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors()) ,
                'data' => $dataonEmpty,
                'session' => '1'
            );
            echo json_encode($msg);
        }

    }
    
	public function support_list() // This api give wallet history of user 
    {
         $this->form_validation->set_rules('token', 'Token', 'required');  
        
        if ($this->form_validation->run()) {
            $token = $this->security->xss_clean($this->input->post('token'));
            
            $verifyToken = $this->tokenVerify($token);
                    
            if($verifyToken==true){
                        
                $tokenData = $this->tokenDecodeData($token);
                // print_r($tokenData);
                /*
Full texts	id	user_id Ascending 1	place_id	complaint_topic	description	status 0: pending, 1: completed, 2:Process 	source_type*/
// $complaint_List=[];
                // $complaint_List_main
                $complaint_List= $this->db->select('id,user_id,place_id,complaint_topic,description,status,source_type,created_date')->from('tbl_complaint')
                ->where('user_id',$tokenData->id)
                ->order_by('id DESC')->get()->result();
                $supportDetails = $this->db->select('*')->from('ci_support_master')
                ->where('id','1')->get()->result();
                $contactno='';
                $email='';
                if(count($supportDetails)>0){
                   $contactno=$supportDetails[0]->contact;
                   $email=$supportDetails[0]->emailId;
                }
                foreach($complaint_List as $comp){
                    $date = date('d-M-Y h:i a',strtotime((String)$comp->created_date));
                    
                    $comp->created_date=$date;
                }
                if(count($complaint_List)>0){
                
                    $msg = array('status' => true, 'msg' => "Support details" ,'contactno'=>$contactno,'email'=>$email,'complaint_List'=>$complaint_List,'session'=>'1');
                    echo json_encode($msg);
                
                }else {
                    $msg = array('status' => false, 'msg' => "No data found. !!",'contactno'=>$contactno,'email'=>$email,'complaint_List'=>[],'session'=>'1');
                    echo json_encode($msg);
                }
            
            }
            else
            {
            $msg = array('status' => false, 'message' => 'Session expired','contactno'=>'','email'=>'','complaint_List'=>[],'session'=>'0');
            echo json_encode($msg);
        }
            
        }else{
            $msg = array('status' => false, 'message' => strip_tags(validation_errors()),'contactno'=>'','email'=>'','complaint_List'=>[],'session'=>'1');
            echo json_encode($msg);
        }
        
    
    }
    
    public function appUpdateDetails()
    {
        $this
            ->form_validation
            ->set_rules('version', 'Version', 'required');
        $this
            ->form_validation
            ->set_rules('buildno', 'Build no', 'required');
        $this
            ->form_validation
            ->set_rules('device_type', 'Build no', 'required');
            $isAppUpdate=false;
        
            $updateData=array('version'=>'',
                                'build_no'=>'',
                                'app_url'=>'',
                                'update_type'=>'',     //0=flexible update,1=force update 
                                'whats_new'=>'',
                                'mobiletype'=>'');

        if ($this
            ->form_validation
            ->run())
        {
            $app_version = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('version'));
            $app_build_no = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('buildno'));
            $device_type=$this
                ->security
                ->xss_clean($this
                ->input
                ->post('device_type'));
                
            
                
                 $getappUpdateDetails = $this->db->select('*')->from('ci_app_update_details')->order_by('id desc')->where('status','0')->where('is_deleted','0')->get()->result_array();
                    // print_r($getappUpdateDetails);
                    if(count($getappUpdateDetails)>0){
                        $app_version_db=$device_type=='1'?$getappUpdateDetails[0]['android_version']:$getappUpdateDetails[0]['ios_version'];
                        $app_build_no_db=$device_type=='1'?$getappUpdateDetails[0]['android_build_no']:$getappUpdateDetails[0]['ios_build_no'];
                        if($app_version<$app_version_db
                        // &&
                        // $app_build_no<$app_build_no_db
                        ){
                            $isAppUpdate=true;
                            $updateData=array('version'=>$device_type=='1'?$getappUpdateDetails[0]['android_version']:$getappUpdateDetails[0]['ios_version'],
                            'build_no'=>$device_type=='1'?$getappUpdateDetails[0]['android_build_no']:$getappUpdateDetails[0]['ios_build_no'],
                            'app_url'=>$device_type=='1'?$getappUpdateDetails[0]['android_url']:$getappUpdateDetails[0]['ios_url'],
                            'update_type'=>$getappUpdateDetails[0]['update_type'],     //0=flexible update,1=force update 
                            'whats_new'=>$getappUpdateDetails[0]['whats_new'],
                            'mobiletype'=>$device_type=='1'?'Android':'IOS'
                            );
                            $msg = array(
                            'status' => true,
                            'message' => 'New update available',
                            'isAppUpdate'=>$isAppUpdate,
                            'data' => $updateData
                        );
                          echo json_encode($msg);
                           
                        }else{
                            $msg = array(
                            'status' => false,
                            'message' => 'Your app is up to date.',
                            'isAppUpdate'=>$isAppUpdate,
                            'data' => $updateData
                        );
                          echo json_encode($msg);
                        }
                    }else{
                        $msg = array(
                            'status' => false,
                            'message' => 'No data found.',
                            'isAppUpdate'=>$isAppUpdate,
                            'data' => $updateData
                        );
             echo json_encode($msg);
                    }
            
        }
        else{
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors()),
                'isAppUpdate'=>$isAppUpdate,
                'data' => $updateData
            );
            echo json_encode($msg);
        }
    }
     
    public function paymentAutoUpdate()
    {
        date_default_timezone_set('Asia/Kolkata');
        $getPaymentRecords=$this->db->select('*')->from('ci_transaction_history')
        // ->where('user_id','1')
        ->where('status','0')->where('is_deleted','0')->get()->result_array();
        // print_r($getPaymentRecords);
        // die;
        foreach($getPaymentRecords as $payment){
            //order_id,is_live_payment
           $paymentData= $this->verify_orders(
               $payment['is_live_payment'],
               $payment['order_id'],
               $payment['amount']); // this will return on success =1 and failed =0;
            //   print_r($paymentData);
            //   die;
           if($paymentData['status']==1)
           {
            //   print($payment['order_id'].' sucess ');
            //   die;
               /*	id	order_id	payment_id		amount 	
status  	email_id	contact_no	user_id	is_live_payment  on_updated		is_deleted*/


//start
              $paymentNewData=array(
                //   'order_id'=>$paymentData->order_id,
              'payment_id'=>$paymentData['payment_id'],
            //   'amount'=>($paymentData->amount/100), 	
                'status'=>'1',  	
                'email_id'=>$paymentData['email'],
                'contact_no'=>$paymentData['contact'],
                // 'user_id'=>$payment->user_id,
                'on_updated'=>date('Y-m-d H:i:s')
                  );
              $this->db->where('id',$payment['id'])->where('user_id',$payment['user_id'])->where('is_deleted','0')->update('ci_transaction_history',$paymentNewData);
               
              $userwalletDetails = $this->db->select('*')->from('ci_wallet_user')
              ->where('id',$payment['wallet_id'])->where('is_deleted','0')->get()->result_array();
              if(count($userwalletDetails)>0)
              {
                $new_amt = $userwalletDetails[0]['amount'] + $payment['amount'];
                            $this
                                ->db
                                ->where('id', $payment['wallet_id'])
                                ->update('ci_wallet_user', array(
                                'amount' => (float)$new_amt
                            ));
                $message = '₹ ' . $payment['amount'] . ' has been Added to your wallet';
                $this->notificationForWallet($payment['user_id'], '0', '0', '0', 'Wallet', $message, '4', '3'); //3= money added to wallet , 4= wallet screen
                $inserData1 = array(
                                "wallet_id" => $payment['wallet_id'],
                                "user_id" => $payment['user_id'],
                                "amount" => $payment['amount'],
                                "status" => '1',
                                "payment_type" => '1',
                                'booking_id' => '0',
                            'last_wallet_amount'=>$userwalletDetails[0]['amount']
                            );
                                                // $insertPayment1 = $this->db->insert('ci_wallet_history',$inserData1);
                $this->wallet_history_log($inserData1);
                 $lastWallet_insert_id = $this->db->insert_id();
                                
                                $offerDetails = $this->bonusAddInWallet($payment['user_id'],$payment['amount']);
                                // print_r($offerDetails);
                                if($offerDetails['offerwalletAmount']>0)
                                {
                                    $offerAmount=$offerDetails['offerwalletAmount'];
                                    $get_amt = $this->db->select('*')->from('ci_wallet_user')->where('user_id', $payment['user_id'])->get()->result();
                                    if(count($get_amt)>0){
                                        // $walletAmount =$get_amt[0]['amount']; 
                                        $new_amt = $get_amt[0]->amount + $offerAmount;
                                        $this
                                            ->db
                                            ->where('id', $get_amt[0]->id)
                                            ->update('ci_wallet_user', array(
                                            'amount' => (float)$new_amt
                                        ));
                                        $message = '₹ ' . $offerAmount . ' has been added to your wallet for you wallet on recharge of Rs.'.$payment['amount'];
                                        $this->notificationForWallet($payment['user_id'], '0', '0', '0', 'Wallet', $message, '4', '3'); //3= money added to wallet , 4= wallet screen
                                        
                                        $inserData1 = array(
                                                "wallet_id" => $get_amt[0]->id,
                                                "user_id" => $get_amt[0]->user_id,
                                                "amount" => $offerAmount,
                                                "status" => '1',
                                                "payment_type" => '0',
                                                "offer_id"=>$offerDetails['offerData']['id'],
                                                'last_wallet_amount'=>$get_amt[0]->amount,
                                                'payment_for'=>$lastWallet_insert_id
                                            );
                                // $insertPayment1 = $this->db->insert('ci_wallet_history',$inserData1);
                                // $this->wallet_history_log($inserData1);
                                $this->wallet_history_log($inserData1);
                                    }
                                }
              }
               
               //end
               
               
               
               
           
               print($payment['order_id'].' paid success ');
           }
           else
           {
               
               $createdDate =new DateTime($payment['on_created']);
            //   $currentDate = date('Y-m-d H:i:s');
               
               $currentDatetime = new DateTime(date("Y-m-d H:i:s"));
            $interval = $currentDatetime->diff($createdDate);
            // $elapsed = $interval->format('%y years %m months %a days %h hours %i minutes %s seconds');
            // echo $elapsed;
            // echo ' <<>> ';
            $time_ =$interval->h.' '. $interval->i . ' ' . $interval->s;
            $min = $interval->i;
            $sec = $interval->s;
            $totalTime = $interval->days>0?(($interval->days*24)+$interval->h):$interval->h;
            // print_r($interval);
            // print($totalTime);
            $totalmin = $totalTime>0?(($totalTime*60)+$interval->i):$interval->i;
            // print(' - ');
            print($totalmin);
            // die;
            // print(' - ');
            if($totalTime>=12){
            //      $paymentNewData=array(
            //     //   'order_id'=>$paymentData->order_id,
            // //   'payment_id'=>$paymentData['payment_id'],
            // //   'amount'=>($paymentData->amount/100), 	
            //     'status'=>'2',  	
            //     // 'email_id'=>$paymentData['email'],
            //     // 'contact_no'=>$paymentData['contact'],
            //     // 'user_id'=>$payment->user_id,
            //     'on_updated'=>date('Y-m-d H:i:s')
            //       );
            print('inside minutes');
             $paymentNewData=array(
                'status'=>'2',  	
                'on_updated'=>date('Y-m-d H:i:s')
                  );
              $this->db->where('id',$payment['id'])->where('user_id',$payment['user_id'])->where('is_deleted','0')->update('ci_transaction_history',$paymentNewData);
            }
                //  print($interval->h);
            print($payment['order_id'].' paid failed ');
                
            }
          
               
           }
        }
    
    public function verify_orders($isLive,$order_id,$amount) //through razorpay   ($isLive : 0=yes,1=no)
    //  public function verify_orders()
    {
        // $order_id='order_JE0JmZJTqh4mnf';
        // $isLive='1';
        // $amount='111';
        
        $returnValue=0;
        $paymentData = array('payment_id'=>'',
                            'amount'=>'',
                            'email'=>'',
                            'contact'=>'',
                            'order_id'=>'',
                            'status'=>'0');
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/orders/$order_id/payments");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        
        // curl_setopt($ch, CURLOPT_USERPWD, '[YOUR_KEY_ID]' . ':' . '[YOUR_KEY_SECRET]');
        $isLive == '0' ? curl_setopt($ch, CURLOPT_USERPWD, 'rzp_live_go5tICS12in2BY' . ':' . 'ivr9QZMoeDOj2WEfACWUHIts') 
        : curl_setopt($ch, CURLOPT_USERPWD, 'rzp_test_25fQbysZaqmc6L' . ':' . 'IZveLQPTPanBdJ5mx4XWMlzL');
        
        $result = curl_exec($ch);
        // print_r($result);
        // die;
        if (curl_errno($ch)) {
            // echo 'Error:' . curl_error($ch);
            
        }else{
            $resultData = json_decode($result);
           /* if($resultData->error!=null){
                $returnValue= 0;
            }*/
            try{
                if (array_key_exists("error",$resultData))
                  {
                //   $returnValue= 0;
                  }
                else
                  {
                      $paymentList =$resultData->items;
                      if($paymentList>0){
                       foreach($paymentList as $payment)
                       {
                           if($payment->status=='captured'||$payment->status=='authorized'){
                            //   print('1122C&A');
                               if($payment->amount==($amount*100)){
                                // $returnValue =1;  
                                $paymentData = array('payment_id'=>$payment->id,
                            'amount'=>$payment->amount,
                            'email'=>$payment->email,
                            'contact'=>$payment->contact,
                            'order_id'=>$payment->order_id,
                            'status'=>'1');
                                // print('payment success');
                                break;
                               }
                               
                           }
                       }
                      }
                 }
           
        }catch(Exception $e) {
          echo 'Message: ' .$e->getMessage();
        }
            
        }
        curl_close($ch);
        // print_r($paymentData);
        
        return $paymentData;

    }
    
     public function priceCalculateOffer()
    {
         date_default_timezone_set('Asia/Kolkata');
         $this
            ->form_validation
            ->set_rules('userid', 'userid', 'required');
        $this
            ->form_validation
            ->set_rules('placeid', 'placeid', 'required');
        $this
            ->form_validation
            ->set_rules('slot_id', 'slot_id', 'required');
         $this
            ->form_validation
            ->set_rules('cost', 'cost', 'required');
        $this
            ->form_validation
            ->set_rules('passtype', 'passtype', 'required');
        $this
            ->form_validation
            ->set_rules('pricetype', 'pricetype', 'required');
        $this
            ->form_validation
            ->set_rules('fromdatetime', 'fromdatetime', 'required');
        $this
            ->form_validation
            ->set_rules('todatetime', 'todatetime', 'required');
        $this
            ->form_validation
            ->set_rules('no_hours', 'no_hours', 'required');
            
        if ($this
            ->form_validation
            ->run())
        {
            $userid = $this->security->xss_clean($this->input->post('userid'));
            $placeid = $this->security->xss_clean($this->input->post('placeid'));
            $slot_id = $this->security->xss_clean($this->input->post('slot_id'));
            $appcost = $this->security->xss_clean($this->input->post('cost')); // this cost comes from app
            $passtype = $this->security->xss_clean($this->input->post('passtype'));  //1=regular,2=weekly,3=monthly
            $pricetype = $this->security->xss_clean($this->input->post('pricetype')); //1=perhor ,0= priceslab
            $fromdatetime = $this->security->xss_clean($this->input->post('fromdatetime'));
            $todatetime = $this->security->xss_clean($this->input->post('todatetime'));
            $no_hours = $this->security->xss_clean($this->input->post('no_hours'));
            // $app_version = $this->security->xss_clean($this->input->post('version'));
            // print($pricetype.' $pricetype');
            
            $fromdatetime_d = new DateTime($fromdatetime);
            $todatetime_d = new DateTime($todatetime);
            $interval = $fromdatetime_d->diff($todatetime_d);
            // echo "difference " . $interval->y . " years, " . $interval->m." months, ".$interval->d." days "; 
            $noOfDays=$interval->days;


            $cost = $this->bookingCostcal($placeid,$passtype,$pricetype,$no_hours,$noOfDays);
            // print($costfromDB);die;
            if($passtype=='1'){
            /*$getOffers = $this->db->select('*')->from('ci_offers_master')
            // ->where('is_active','0')
            ->where('id','3')
            ->where('fromDate<=',date('Y-m-d'))->where('toDate>=',date('Y-m-d'))
            ->where('is_deleted','0')->order_by('priority asc')->get()->result_array();*/
           $validationData=$this->newOfferuserValidation($userid);
        //   print_r($validationData);
        // print_r($validationData);
        //   die;
            // print_r($getOffers);
            if($validationData['isoffervalid']==true)
            {
                $getOffers=$validationData['offerData'];
                if($getOffers['is_per_user']!=4){
                if($getOffers['id']==1){
                    $fromdate = date('Y-m-d').' 00:00:00';
                        $fromdate_str = strtotime($fromdate);
                        $fromdate_d =date('Y-m-d H:i:s', $fromdate_str);
                        
                         $check_booking=$this->db->select('*')->from('ci_booking')
                        ->where('user_id',$userid)
                        ->where('created_date>=',$fromdate_d)
                        ->where('created_date<=',date('Y-m-d H:i:s'))
                        ->get()->result_array();
                        // print_r($check_booking);
                        if(count($check_booking)>0){
                            $msg = array(
                            'status' => true,
                            'message' => 'Offer Already Availed.',
                            'newdata'=>array(
                                'offersApplied'=>false,
                                'offerId'=>'0',
                                'oldCost'=>(String)$cost,
                                'newCost'=>(String)$cost)
                        );
                        echo json_encode($msg);
                        exit();
                        }
                }
                    
                // print_r($check_booking);
            //   if(count($check_booking)<=0)
            //   {
                //   $validationData =$this->offeruserValidation($userid,$getOffers[0]);
                //   $validationData =$this->newOfferuserValidation($userid,$getOffers);//newOfferuserValidation
                //   $checkUserValidation =$getOffers['is_per_user']=='1'? $validationData['isoffervalid']:true;
                //   if($checkUserValidation==true){
                       
                    if($getOffers['offertype']=='1')
                    {   // perhour/ priceslab
                    // print($no_hours.'  -- ');
                    // print($getOffers[0]['max_hrs']);
                         if($no_hours>$getOffers['max_hrs'])
                         {
                            if($pricetype==1){ //per hour
                                $remainingHrs =$no_hours-$getOffers['max_hrs'];
                                $offerCalCost = $getOffers['max_hrs']*$getOffers['cost'];
                                
                                $remainingHrsCost =$remainingHrs*($cost/$no_hours);
                                $newCost=$offerCalCost+$remainingHrsCost;
                        
                        // $this->session->set_userdata(array('newcost'=>$newCost,'cost'=>$cost));
                        // $this->getVerifyCost();
                                $msg = array(
                                    'status' => true,
                                    'message' => 'Offer Applied',
                                    'newdata'=>array(
                                    'offersApplied'=>true,
                                    'offerId'=>$getOffers['id'],
                                    'oldCost'=>(String)$cost,
                                    'newCost'=>(String)round($newCost))
                                );
                                echo json_encode($msg);
                            }
                            else
                            { //priceslab
                            
                               $getPriceDetails = $this->db->select('*')->from('ci_price_slab')
                                ->where('place_id',$placeid)//pass
                                ->where('hrs<=',$getOffers['max_hrs'])
                                ->where('pass','1')
                                ->where('is_deleted','0')
                                ->order_by('hrs asc')
                                ->get()->result_array();
                                if(count($getPriceDetails)>0)
                                {
                                    $costOf2hrs ='0';
                                    $numberOfhrs='1';
                                    foreach($getPriceDetails as $priceslabData)
                                    {
                                        if($priceslabData['hrs']<=$getOffers['max_hrs'])
                                        {
                                            $costOf2hrs=$priceslabData['cost'];
                                            $numberOfhrs=$priceslabData['hrs'];
                                        }
                                    }
                                    /* $remainingHrs =$no_hours-2;
                                    $offerCalCost = 2*$getOffers[0]['cost'];
                                    
                                    $remainingHrsCost =$remainingHrs*($cost/$no_hours);
                                    $newCost=$offerCalCost+$remainingHrsCost;*/
                                    $newCost =($cost- $costOf2hrs)+($numberOfhrs*$getOffers['cost']);
                                //  $this->session->set_userdata(array('newcost'=>$newCost,'cost'=>$cost));
                                //  $this->getVerifyCost();
                                    $msg = array(
                                        'status' => true,
                                        'message' => 'Offer Applied',
                                        'newdata'=>array(
                                        'offersApplied'=>true,
                                        'offerId'=>$getOffers['id'],
                                        'oldCost'=>(String)$cost,
                                        'newCost'=>(String)round($newCost))
                                    );
                                    echo json_encode($msg);
                                }
                                else
                                {
                                    //  $this->session->set_userdata(array('newcost'=>$cost,'cost'=>$cost));
                                 $msg = 
                                 array(
                                    'status' => true,
                                    'message' => 'No offer present for current place.',
                                    'newdata'=>array(
                                    'offersApplied'=>false,
                                    'offerId'=>'0',
                                    'oldCost'=>(String)$cost,
                                    'newCost'=>(String)$cost)
                                    );
                                    echo json_encode($msg);   
                                }
                            }
                        }else{
                            
                            
                            $newCost = $no_hours*$getOffers['cost'];
                            // $this->session->set_userdata(array('newcost'=>$newCost,'cost'=>$cost));
                            // $this->getVerifyCost();
                            $msg = array(
                                'status' => true,
                                'message' => 'Offer Applied',
                                'newdata'=>array(
                                'offersApplied'=>true,
                                'offerId'=>$getOffers['id'],
                                'oldCost'=>(String)$cost,
                                'newCost'=>(String)round($newCost))
                            );
                            echo json_encode($msg);
                                
                            
                            
                        }
                    }
                    else if($getOffers['offertype']=='2'){  // percentage
                        // $cost_off = $no_hours/$getOffers[0]['percentage'];
                        $cost_off = ($cost*$getOffers['percentage'])/100;
                        $newCost=$cost-$cost_off;
                            // $this->session->set_userdata(array('newcost'=>$newCost,'cost'=>$cost));
                            // $this->getVerifyCost();
                            $msg = array(
                                'status' => true,
                                'message' => 'Offer Applied',
                                'newdata'=>array(
                                    'offersApplied'=>true,
                                    'offerId'=>$getOffers['id'],
                                    'oldCost'=>(String)$cost,
                                    'newCost'=>(String)round($newCost))
                            );
                            echo json_encode($msg);
                    }
                    
                // }else{
                //      $msg = array(
                //             'status' => true,
                //             'message' => 'No offer present.',
                //             'newdata'=>array(
                //                 'offersApplied'=>false,
                //                 'offerId'=>'0',
                //                 'oldCost'=>(String)$cost,
                //                 'newCost'=>(String)$cost)
                //         );
                //         echo json_encode($msg);
                // }
                   
           /*    }
                 else{
                    //  $this->session->set_userdata(array('newcost'=>$cost,'cost'=>$cost));
                            $msg = array(
                            'status' => true,
                            'message' => 'Offer Already Availed.',
                            'newdata'=>array(
                                'offersApplied'=>false,
                                'offerId'=>'0',
                                'oldCost'=>(String)$cost,
                                'newCost'=>(String)$cost)
                        );
                        echo json_encode($msg);
                    }*/
            }
            else{
                    $msg = array(
                                'status' => true,
                                'message' => 'No offer present for booking.',
                                'newdata'=>array(
                                    'offersApplied'=>true,
                                    'offerId'=>$getOffers['id'],
                                    'oldCost'=>(String)$cost,
                                    'newCost'=>(String)$cost)
                            );
                            echo json_encode($msg);
                }
                
            }
            else{
            // print_r($getOffers);
            // $this->session->set_userdata(array('newcost'=>$cost,'cost'=>$cost));
                    $msg = array(
                    'status' => true,
                    'message' => 'No offers present',
                    'newdata'=>array(
                        'offersApplied'=>false,
                        'offerId'=>'0',
                        'oldCost'=>(String)$cost,
                        'newCost'=>(String)$cost)
                );
                echo json_encode($msg);
            }
            }else{
                // $this->session->set_userdata(array('newcost'=>$cost,'cost'=>$cost));
                $msg = array(
                    'status' => true,
                    'message' => 'No offers present for Weekly/Monthly Booking',
                    'newdata'=>array(
                        'offersApplied'=>false,
                        'offerId'=>'0',
                        'oldCost'=>(String)$cost,
                        'newCost'=>(String)$cost)
                );
                echo json_encode($msg);
            }
        }else{
            /* $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors())
            );*/
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors()),
                'newdata'=>array(
                    'offersApplied'=>false,
                    'offerId'=>'0',
                    'oldCost'=>'0',
                    'newCost'=>'0')
                );
            echo json_encode($msg);
        }
    }
    
    public function offeruserValidation($userid,$offerDdata)
    {
        // $userid=1;
        $returnData = array('isoffervalid'=>false,'message'=>'No offer available');
        $getuserData = $this->db->select('*')->from('ci_users')->where('id',$userid)->where('is_active','1')->get()->result_array();
        if(count($getuserData)>0)
        {
            $getUser_RegisDate = $getuserData[0]['created_at'];
            $getUser_RegisDate_d =date("Y-m-d",strtotime($getUser_RegisDate));
            // $currendate = date('Y-m-d');
            // $noofUserDate = $getUser_RegisDate_d->diff($currendate);
            $currentDatetime =  new DateTime(date("Y-m-d"));
            $noofUserDate = $currentDatetime->diff(new DateTime($getUser_RegisDate_d));
            //  print($getUser_RegisDate_d);
            //  print(' -- ');
            //  print_r($currentDatetime);
            //  print(' -- ');
            // print_r($noofUserDate->days);
            if($noofUserDate->days<$offerDdata['no_of_count']){
                $noOfdaysRemaining =$offerDdata['no_of_count']- $noofUserDate->days;
                $message = "$noOfdaysRemaining days is remaining for you.";
                $returnData = array('isoffervalid'=>true,'message'=>$message);
                return $returnData;
            }else{
                $returnData = array('isoffervalid'=>false,'message'=>"No offer available");
                return $returnData;
            }
        }
        else
        {
           $returnData = array('isoffervalid'=>false,'message'=>"No offer available");
                return $returnData;
        }
    }
    public function newOfferuserValidation($userid)
    {
        // $userid=1;
        $returnData = array('isoffervalid'=>false,'message'=>'No offer available');
        $isUserRegisteredOffer= $this->db->select('*')->from('ci_offer_users')->where('user_id',$userid)->where('is_deleted','1')->get()->result_array();
        // print_r($isUserRegisteredOffer);
        if(count($isUserRegisteredOffer)>0)
        {
            $getOfferData=$this->db->select('*')->from('ci_offers_master')->where('id',$isUserRegisteredOffer[0]['offer_id'])->where('is_deleted','0')->get()->result_array();
            if(count($getOfferData)>0)
            {
                $getuserData = $this->db->select('*')->from('ci_users')->where('id',$userid)->where('is_active','1')->get()->result_array();
                if(count($getuserData)>0)
                {
                    if($getOfferData[0]['is_per_user']==1)
                    {
                        $getUser_RegisDate = $getuserData[0]['created_at'];
                        $getUser_RegisDate_d =date("Y-m-d",strtotime($getUser_RegisDate));
                        // $currendate = date('Y-m-d');
                        // $noofUserDate = $getUser_RegisDate_d->diff($currendate);
                        $currentDatetime =  new DateTime(date("Y-m-d"));
                         $noofUserDate = $currentDatetime->diff(new DateTime($getUser_RegisDate_d));
                        //  print($getUser_RegisDate_d);
                        //  print(' -- ');
                        //  print_r($currentDatetime);
                        //  print(' -- ');
                        // print_r($noofUserDate->days);
                        if($noofUserDate->days<$getOfferData[0]['no_of_count'])
                        {
                            $noOfdaysRemaining =$getOfferData[0]['no_of_count']- $noofUserDate->days;
                             $message_noofdays = "$noOfdaysRemaining days is remaining for you.";
                            $message=$getOfferData[0]['offerDesc'].' & Hurry and grab this offer as   '. $message_noofdays;
                            $returnData = array('isoffervalid'=>true,'message'=>$message,'offerData'=>$getOfferData[0]);
                            return $returnData;
                        }
                        
                    }
                    else
                    {
                        $currentDatetime =  date("Y-m-d");
                        $fromdate=date('Y-m-d',$getOfferData[0]['fromDate']);
                        $todate=date('Y-m-d',$getOfferData[0]['toDate']);
                        if($fromdate<=$currentDatetime&&$todate>=$currentDatetime)
                        {
                             $message = $getOfferData[0]['offerDesc'];
                            $returnData = array('isoffervalid'=>true,'message'=>$message,'offerData'=>$getOfferData[0]);
                            return $returnData;
                        }
                    }
                        
                }
                   
                }
            else{
                // print('no peruser offer');
              return  $this->checkOfferWithoutPerUser($userid);
            }
                
        }
        else
        {
         return   $this->checkOfferWithoutPerUser($userid);
        }
    }
    public function checkOfferWithoutPerUser($userid){
        $getAllOfferList= $this->db->select('*')->from('ci_offers_master')
            ->where('is_active','0')
            ->where('is_per_user!=','1')
            // ->where('id','3')
            ->where('fromDate<=',date('Y-m-d'))->where('toDate>=',date('Y-m-d'))
            ->where('is_deleted','0')->order_by('priority asc')->get()->result_array();
            // print_r($getAllOfferList);
            if(count($getAllOfferList)>0)
            {
                $currentDatetime =  date("Y-m-d");
                        $fromdate=date('Y-m-d',strtotime($getAllOfferList[0]['fromDate']));
                        $todate=date('Y-m-d',strtotime($getAllOfferList[0]['toDate']));
                        if($fromdate<=$currentDatetime&&$todate>=$currentDatetime)
                        {
                            // print('inside date');
                            if($getAllOfferList[0]['is_per_user']==3)
                            {
                                // print_r('under id 3');
                                // print('inside is_per_user');
                                $getBookingList = $this->db->select('*')->from('ci_booking')->where('book_ext','')
                                ->where('user_id',$userid)
                                ->where('is_deleted','0')
                                // ->where('','')
                                ->get()->result_array();
                                // print_r($getBookingList);
                                if(count($getBookingList)<$getAllOfferList[0]['is_per_user'])
                                {
                                     $returnData = array('isoffervalid'=>true,'message'=>$getAllOfferList[0]['offerDesc'],'offerData'=>$getAllOfferList[0]);
                                     return $returnData;
                                }
                                else
                                {
                                    $returnData = array('isoffervalid'=>false,'message'=>"No offer available");
                                    return $returnData;
                                }
                            }
                            else
                            {
                                // print_r('under id 4');
                                $message = $getAllOfferList[0]['offerDesc'];
                                $returnData = array('isoffervalid'=>true,'message'=>$message,'offerData'=>$getAllOfferList[0]);
                                // print_r($returnData);
                                return $returnData;
                            }
                            
                        }
                        else
                        {
                            $returnData = array('isoffervalid'=>false,'message'=>"No offer available");
                            return $returnData;
                        }
            }
            else{
                 $returnData = array('isoffervalid'=>false,'message'=>"No offer available");
                return $returnData;
            }
    }
/*    public function getVerifyCost(){
        echo "<pre>";
        $sessionData=$this->session->userdata();
        print_r($sessionData);
        print(' -- ');
        print($sessionData['newcost']);
        exit();
        // $this->session->unset_userdata(array('newcost','cost'));
    }*/
    
    public function bookingCostcal($place_id,$passType,$priceType,$noofhrs,$noOfDays)
    {
        $cost='0';
        // $place_id='12';
        // $passType='3';
        // $priceType='1';
        // $noofhrs='1';
        // $noOfDays='30';
        
            //         $passtype = $this->security->xss_clean($this->input->post('passtype'));  //1=regular,2=weekly,3=monthly
            // $pricetype = $this->security->xss_clean($this->input->post('pricetype')); //1=perhor ,0= priceslab
        
        $getPlaceDetails = $this->db->select('*')->from('ci_parking_places')
        ->where('id',$place_id)->where('place_status','1')->get()->result_array();
        if(count($getPlaceDetails)>0){
            $price_Slab = $this->priceslabData($getPlaceDetails[0]['id'], $getPlaceDetails[0]['ext_per'], $getPlaceDetails[0]['pricing_type']);
            //perHour,perDay,perWeek,perMonth,extendPrice
            if($priceType==1){
              $cost= $passType=='1'?$noofhrs* $price_Slab['perHour']['cost']:($noofhrs*$noOfDays)* $price_Slab['perHour']['cost'];
            }else{
                if($passType=='1'){
                    foreach($price_Slab['perDay'] as $priceDetails){
                        if($priceDetails['minhrs']<=$noofhrs&&$priceDetails['hrs']>=$noofhrs){
                           $cost=$priceDetails['cost'];
                        }
                    }
                }
                else if($passType=='2'){
                    foreach($price_Slab['perWeek'] as $priceDetails){
                        if($priceDetails['minhrs']<=$noofhrs&&$priceDetails['hrs']>=$noofhrs){
                           $cost=$priceDetails['cost'];
                        }
                    }
                }
                else if($passType=='3'){
                    foreach($price_Slab['perMonth'] as $priceDetails){
                       if($priceDetails['minhrs']<=$noofhrs&&$priceDetails['hrs']>=$noofhrs){
                           $cost=$priceDetails['cost'];
                        }
                    }
                }
                else if($passType=='4'){
                    foreach($price_Slab['extendPrice'] as $priceDetails){
                       if($priceDetails['minhrs']<=$noofhrs&&$priceDetails['hrs']>=$noofhrs){
                           $cost=$priceDetails['cost'];
                        }
                    }
                }
                
            }
        }
        // print_r($cost);
        return $cost;
        
        
    }
    
    public function encryptData($data) //aes
    {
        
        //  $data = $this->input->post('data');
         $ciphertext = $this->encryption->encrypt($data);
         return $ciphertext;
        //  $msg = array('status' => TRUE, 'message' => 'Successfuly data encrypted.','data'=>$ciphertext);
        //     echo json_encode($msg);
        //     $ciphertext1 = $this->encryption->decrypt($ciphertext);
        //  $msg = array('status' => TRUE, 'message' => 'Successfuly data Decrypted.','data'=>$ciphertext1);
        //     echo json_encode($msg);
             
     }
    
    public function encryption() //aes
    {
         $this->form_validation->set_rules('data', 'data', 'required');

        if ($this->form_validation->run()) {
         $data = $this->input->post('data');
         $ciphertext = $this->encryption->encrypt($data);
         $msg = array('status' => TRUE, 'message' => 'Successfuly data encrypted.','data'=>$ciphertext);
            echo json_encode($msg);
        //     $ciphertext1 = $this->encryption->decrypt($ciphertext);
        //  $msg = array('status' => TRUE, 'message' => 'Successfuly data Decrypted.','data'=>$ciphertext1);
        //     echo json_encode($msg);
             }
     }
     
     public function decryption()//aes
     {
         $this->form_validation->set_rules('data', 'data', 'required');

        if ($this->form_validation->run()) {
         $data = $this->input->post('data');
         $ciphertext = $this->encryption->decrypt($data);
         $msg = array('status' => TRUE, 'message' => 'Successfuly data Decrypted.','data'=>$ciphertext);
            echo json_encode($msg);
            
        }
     }
     
     public function getIpAdressLatlong()
     {
           $location = file_get_contents('http://ip-api.com/json/'.$_SERVER['REMOTE_ADDR']);
 print_r($location);
     }
     public function applyForVendor()
    {
         $this
            ->form_validation
            ->set_rules('name', 'Name', 'required');
        $this
            ->form_validation
            ->set_rules('landMark', 'LandMark', 'required');
        $this
            ->form_validation
            ->set_rules('address', 'Address', 'required');
        $this
            ->form_validation
            ->set_rules('contact', 'Contact', 'required');
        $this
            ->form_validation
            ->set_rules('token', 'Token', 'required');
        $this
            ->form_validation
            ->set_rules('apllyType', 'Apply Type', 'required'); //0=myself and 1= Others
            
        if ($this
            ->form_validation
            ->run())
        {

                  
            $name = $this->security->xss_clean($this->input->post('name'));
            $landMark = $this->security->xss_clean($this->input->post('landMark'));
            $address = $this->security->xss_clean($this->input->post('address'));
            $contact = $this->security->xss_clean($this->input->post('contact'));
            $token = $this->security->xss_clean($this->input->post('token'));
            $apllyType = $this->security->xss_clean($this->input->post('apllyType'));
            $verifyToken = $this->tokenVerify($token);
                    
            if($verifyToken==true){
                    //id 	name 	landmark 	address 	status 0=pending,1=processing,2=success 	created_at 	updated_at 	is_deleted
                    $tokenData = $this->tokenDecodeData($token);
                        // $this->db->where('id', $tokenData->id);
                    $data=array('name'=>$name,
                    'landmark'=>$landMark,
                    'address'=>$address,
                    'status'=>0,
                    'contact'=>$contact,
                    'user_id'=>$tokenData->id,
                    'apllyType'=>$apllyType
                    );
                    $applyVendor= $this->db->insert('ci_apply_for_vendor',$data);
                    if($applyVendor)
                    {
                        $msg = array(
                        'status' => true,
                        'message' => 'Successfully accepted your application'
                        ,'session'=>'1');
                    echo json_encode($msg);
                    }
                    else
                    {
                        $msg = array(
                        'status' => false,
                        'message' => 'Not able to receive your application'
                        ,'session'=>'1'
                        
                        );
                    echo json_encode($msg);
                    }
            }else{
                $msg = array('status' => false, 'message' => 'Session expired','session'=>'0');
            echo json_encode($msg);
            }
        }else{
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors())
                ,'session'=>'1'
                );
            echo json_encode($msg);
        }
     }
    
    public function checkVendorApplyStatus(){
         $this
            ->form_validation
            ->set_rules('token', 'Token', 'required');
            
        $mySelfApplyData=
                array(
                    'id'=>'',
                    'user_id'=>'',
                    'name'=>'',
                    'landmark'=>'',
                    'address'=>'',
                    'contact'=>'',
                    'apllyType'=>'',// 0=myself,1=others 
                    'status'=>'', //0=pending,1=processing,2=success 
                    'statusMsg'=>'',
                    'created_at'=>'',
                    'updated_at'=>'',
                    'is_deleted'=>'' 
                    );
                    
        if ($this
            ->form_validation
            ->run())
        {
            $token = $this->security->xss_clean($this->input->post('token'));
            $verifyToken = $this->tokenVerify($token);
                    $isAppliedForSelf = false;
               
                $appliedDataList=[];
            if($verifyToken==true){
            //id	user_id	name	landmark	address	contact	apllyType 0=myself,1=others 	status 0=pending,1=processing,2=success 	id	created_at	updated_at	is_deleted 
                $tokenData = $this->tokenDecodeData($token);
                $getApplyList = $this->db->select('*')->from('ci_apply_for_vendor')
                ->where('user_id',$tokenData->id)->where('is_deleted','1')->get()->result_array();
                
                // if(count($getApplyList)>0)
                // {
                $appliedData=[];
                    foreach($getApplyList as $apllyData)
                    {
                        if($apllyData['apllyType']=='0'){
                            $isAppliedForSelf = true;
                            $statusMsg=$apllyData['status']=='0'?'Pending':$apllyData['status']==1?'Processing':'Successfull';
                            $apllyData['statusMsg']=$statusMsg;
                            $mySelfApplyData=$apllyData;
                            
                        }else{
                            $statusMsg=$apllyData['status']=='0'?'Pending':$apllyData['status']==1?'Processing':'Successfull';
                            $apllyData['statusMsg']=$statusMsg;
                        array_push($appliedData,$apllyData);
                        }
                    }
                    if($isAppliedForSelf==true){
                        array_push($appliedDataList,$mySelfApplyData);
                        foreach($appliedData as $d){
                            array_push($appliedDataList,$d);
                        }
                    }else{
                        $appliedDataList=$appliedData;
                    }
                    $msg = array('status' => true, 'message' => 'Applied Data.',
                    'data'=>array(
                        'isAppliedForSelf'=>$isAppliedForSelf,
                        
                        'appliedDataList'=>$appliedDataList
                        ),
                        'session'=>'1');
                    echo json_encode($msg); 
                // }
            //     else {
            //         $msg = array('status' => false, 'message' => 'Not Applied till yet.',
            //         'data'=>array(
            //             'isAppliedForSelf'=>$isAppliedForSelf,
            //             'mySelfApplyData'=>$mySelfApplyData,
            //             'othersApplyData'=>$othersApplyData
            //             ),
            //             'session'=>'1');
            // echo json_encode($msg);  
            //     }
                
                
            }
            else
            {
              $msg = array('status' => false, 'message' => 'Session expired','data'=>array(
                        'isAppliedForSelf'=>$isAppliedForSelf,
                        'appliedDataList'=>$appliedDataList
                        ),'session'=>'0');
            echo json_encode($msg);  
            }
        }
        else{
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors()),
                'data'=>array(
                        'isAppliedForSelf'=>false,
                        'appliedDataList'=>[]
                        ),
                'session'=>'1'
                );
            echo json_encode($msg);
        }
    }
    
    public function userAcceptTermsCondtn(){
         $this
            ->form_validation
            ->set_rules('token', 'Token', 'required');
        $this
            ->form_validation
            ->set_rules('termCondtn_id', 'Token', 'required');
            
        
                    
        if ($this
            ->form_validation
            ->run())
        {
            $token = $this->security->xss_clean($this->input->post('token'));//termCondtn_id
            $termCondtn_id = $this->security->xss_clean($this->input->post('termCondtn_id'));
            $verifyToken = $this->tokenVerify($token);
                    
               
            if($verifyToken==true){
            //id	user_id	name	landmark	address	contact	apllyType 0=myself,1=others 	status 0=pending,1=processing,2=success 	id	created_at	updated_at	is_deleted 
                $tokenData = $this->tokenDecodeData($token); //$tokenData->id
                $query = $this->db->where('id',$tokenData->id)->update('ci_users',array('terms_conditn_id'=>$termCondtn_id,'updated_at'=>date('Y-m-d H:i:s')));
                if($query){
                     $msg = array('status' => true, 'message' => 'Thank you for Accepting T&C','session'=>'1');
            echo json_encode($msg); 
                }
                else{
                    $msg = array('status' => false, 'message' => 'Something went wrong.Kindly try after sometime.','session'=>'1');
            echo json_encode($msg);
                }
                
            }
            else
            {
              $msg = array('status' => false, 'message' => 'Session expired','session'=>'0');
            echo json_encode($msg);  
            }
        }
        else{
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors()),
                'session'=>'1'
                );
            echo json_encode($msg);
        }
    }
    
   public function bonusAddInWallet($userid,$amount)
    // public function bonusAddInWallet()
    {
        // $this
        //     ->form_validation
        //     ->set_rules('userid', 'userid', 'required');
        //     $this
        //     ->form_validation
        //     ->set_rules('amount', 'amount', 'required');
                    
        // if ($this
        //     ->form_validation
        //     ->run())
        // {
        //     // $userid,$amount
        //     $userid = $this->security->xss_clean($this->input->post('userid'));
        //     $amount = $this->security->xss_clean($this->input->post('amount'));
            $newAmount=0;
            $getofferDetails=$this->newOfferuserValidation($userid);
                // print($user_id);
            // print('sdfsd');
            // print_r($getofferDetails);
            if($getofferDetails['isoffervalid']==true)
            {
                if($getofferDetails['offerData']['is_per_user']==4)
                {
                    if($amount>=$getofferDetails['offerData']['no_of_count']){
                        if($getofferDetails['offerData']['offertype']==1)// price prehour
                        {
                            
                            $newAmount =$getofferDetails['offerData']['cost']; 
                        }
                        else  // percentage
                        {
                            $newAmount =round(($amount*$getofferDetails['offerData']['percentage'])/100);
                        }
                    }
                }
            }
            $getofferDetails['offerwalletAmount']=$newAmount;
            return $getofferDetails;
            // echo json_encode(array('offerDetails'=>$getofferDetails,'offerAmount'=>$newAmount));
            // return $newAmount;
        // }
    }
    public function placesListActiveUpcoming()
    {
        $this
            ->form_validation
            ->set_rules('token', 'Token', 'required');



        if ($this->form_validation->run()) {
            $token = $this->security->xss_clean($this->input->post('token'));
            $verifyToken = $this->tokenVerify($token);

            $activePlaceList = [];
            $upcomingPlaceList = [];
            if ($verifyToken == true) {

                $placeList = $this->db->select('*')->from('ci_parking_places')
                    ->where('place_status!=', '0')
                    ->where('is_deleted', '0')->get()
                    ->result_array();
                if (count($placeList) > 0) {
                    foreach ($placeList as $place) {
                        // $place['place_tag'] = $place['place_status'] == '1' ? 'Active' : $place['place_status'] == '2' ? 'UpComing' : '';
                        if ($place['place_status'] == '1') {
                            $place['place_tag'] = 'Active';
                            array_push($activePlaceList, $place);
                        } else if ($place['place_status'] == '2') {
                            $place['place_tag'] = 'UpComing';
                            array_push($upcomingPlaceList, $place);
                        }
                    }
                    $msg = array(
                        'status' => true,
                        'message' => 'List of places',
                        'activePlaceList' => $activePlaceList,
                        'upcomingPlaceList' => $upcomingPlaceList,
                        'session' => '1'
                    );
                    echo json_encode($msg);
                } else {
                    $msg = array(
                        'status' => false, 'message' => 'No places found.',
                        'activePlaceList' => $activePlaceList,
                        'upcomingPlaceList' => $upcomingPlaceList, 'session' => '1'
                    );
                    echo json_encode($msg);
                }
            } else {
                $msg = array(
                    'status' => false,
                    'message' => 'Session expired',
                    'activePlaceList' => $activePlaceList,
                    'upcomingPlaceList' => $upcomingPlaceList,
                    'session' => '0'
                );
                echo json_encode($msg);
            }
        } else {
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors()),

                'activePlaceList' => [],
                'upcomingPlaceList' => [],
                'session' => '1', 'placeList' => []
            );
            echo json_encode($msg);
        }
    }
    public function uniqueSensorNumber()
    {
        $this
            ->form_validation
            ->set_rules('token', 'Token', 'variable');



        if ($this->form_validation->run()) {
            $variable = $this->security->xss_clean($this->input->post('variable'));
            // $json = file_get_contents('php://input');
            // $data = json_decode($json);
            // print_r($data);
            // $variable = $data->last_Sensor_no; //'MH-AZ999';
            $var1 = explode('-', $variable);
            if ($var1[1] == 'ZZ999') {
                print("Sorry we cannot go beyond this ");
            } else if ($var1[1][2] == 9 && $var1[1][3] == 9 && $var1[1][4] == 9) {
                // echo 'hii';
                if ($var1[1][1] == 'Z') {
                    $var1[1][0] = chr(ord($var1[1][0]) + 1);
                    $var1[1][1] = 'A';
                    $var1[1][2] = 0;
                    $var1[1][3] = 0;
                    $var1[1][4] = 1;
                } else {
                    $var1[1][1] = chr(ord($var1[1][1]) + 1);
                    $var1[1][2] = 0;
                    $var1[1][3] = 0;
                    $var1[1][4] = 1;
                }
            } else if ($var1[1][3] == 9 && $var1[1][4] == 9) {
                $var1[1][2] = $var1[1][2] + 1;
                $var1[1][3] = 0;
                $var1[1][4] = 0;
            } else {
                // echo'hioo';
                if ($var1[1][4] == 9) {
                    $var1[1][3] = $var1[1][3] + 1;
                    $var1[1][4] = 0;
                } else {
                    // echo 'll';
                    $var1[1][3] = $var1[1][3];
                    $var1[1][4] = $var1[1][4] + 1;
                }
            }
            echo $var1[1];
        } else {
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors()),

                'activePlaceList' => [],
                'upcomingPlaceList' => [],
                'session' => '1', 'placeList' => []
            );
            echo json_encode($msg);
        }
        // print($var1[1]);
        // print('</br>');

    }

    public function userPlaceSuggestion()
    {
        $this
            ->form_validation
            ->set_rules('token', 'Token', 'required');
        $this
            ->form_validation
            ->set_rules('placeAddress', 'Token', 'required');



        if ($this
            ->form_validation
            ->run()
        ) {
            $token = $this->security->xss_clean($this->input->post('token')); //termCondtn_id
            $placeAddress = $this->security->xss_clean($this->input->post('placeAddress'));
            $contactNo = $this->security->xss_clean($this->input->post('contactNo'));
            $verifyToken = $this->tokenVerify($token);


            if ($verifyToken == true) {
                $tokenData = $this->tokenDecodeData($token); //$tokenData->id

               $data= array('user_id'=>$tokenData->id,
               	'suggestion'=>$placeAddress,
                	'contact_no'=>$contactNo);
                    $iunsert=$this->db->insert('tbl_user_suggestion',$data);
                    $msg = array(
                        'status' => true,
                        'message' => 'Thankyou for your suggestion. Have a nice day.',
                        'session' => '1'
                    );
                    echo json_encode($msg);
            } else {
                $msg = array('status' => false, 'message' => 'Session expired', 'session' => '0');
                echo json_encode($msg);
            }
        } else {
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors()),
                'session' => '1'
            );
            echo json_encode($msg);
        }
    }
    
    public function userDeactivation()
    {
        $this
            ->form_validation
            ->set_rules('token', 'Token', 'required');
        $this
            ->form_validation
            ->set_rules('reason', 'reason', 'required');



        if ($this
            ->form_validation
            ->run()
        ) {
            $token = $this->security->xss_clean($this->input->post('token')); //termCondtn_id
            $reason = $this->security->xss_clean($this->input->post('reason'));
            $verifyToken = $this->tokenVerify($token);


            if ($verifyToken == true) {
                $tokenData = $this->tokenDecodeData($token); //$tokenData->id
                
                
                $deactiVateUSer = $this->db->where('id',$tokenData->id)->where('is_active','1')->update('ci_users',array('is_active'=>'0'));
                
               $data= array(
                   'user_id'=>$tokenData->id,
               	    'reason'=>$reason);
                    $iunsert=$this->db->insert('tbl_user_deactivation',$data);
                    $msg = array(
                        'status' => true,
                        'message' => 'Successfully deactivated.',
                        'session' => '1'
                    );
                    echo json_encode($msg);
            } else {
                $msg = array('status' => false, 'message' => 'Session expired', 'session' => '0');
                echo json_encode($msg);
            }
        } else {
            $msg = array(
                'status' => false,
                'message' => strip_tags(validation_errors()),
                'session' => '1'
            );
            echo json_encode($msg);
        }
    }
    
}

?>