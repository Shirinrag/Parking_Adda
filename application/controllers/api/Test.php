<?php 
class Test extends CI_Controller
{
    
    // Live apis 
        public function __construct()
         { 
        parent::__construct();
        
    }
    function _returnSingle($err) {
		foreach ($err as $key => $value) {
			return $err[$key];
		}
	}
/*  public function inserttransac_id1(){
        $walletdata = $this->db->Select('*')->from('ci_wallet_history')->get()->result_array();
        foreach($walletdata as $wallet){
             $transac_id=$this->create_transac_id();
             $this->db->where('id',$wallet['id'])->update('ci_wallet_history',array('transac_id'=>$transac_id));
        }
    }*/
     // function issuecreate_sensor()
     
     
     
     public function test(){
         echo "hello"; die;
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
        
                if(count($sensorcomp)>0)
                {
                    
                    $currentdate = date('Y-m-d');
                    $compdate =date("Y-m-d",strtotime($sensorcomp[0]['created_at']));
                    // $enddate_fulld=date("Y-m-d H:i:s", strtotime($getBooking[0]['booking_to_date'].' '.$getBooking[0]['reserve_to_time']));
                    if($currentdate>$compdate){
                        
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
/*	 function create_transac_id()
     {
        // $i=0;
        // $transcac_id = random_string('num', 18);
        $transcac_id = '';
        for($i = 0; $i < 16; $i++) { $transcac_id .= mt_rand(0, 9); }
        
        // print($this->generateCode(16));
        $getresult = $this->db->Select('*')->from('ci_wallet_history')->where('transac_id',$transcac_id)->get()->result();
        if(count($getresult)>0){
            
            $this->create_transac_id();
            // echo '11212122122';
            
        }
        else{
            echo $transcac_id;
            
        }
    }*/
    
     public function onAutoCompleted_bookingApi() // cronjob apis
    {
        
        $getBookingList = $this->db->select('*')->from('ci_booking')->where('booking_status','0')->where('is_deleted','0')->get()->result();
        foreach($getBookingList as $booking){
            if($booking->timezone=='Asia/Kolkata'){
                
                date_default_timezone_set('Asia/Kolkata');
                $currentDateTime = date('Y-m-d H:i:s');
                $toDateTime = date('Y-m-d H:i:s',strtotime($booking->booking_to_date.' '.$booking->reserve_to_time));
                print($toDateTime);
                print('\n');
                if($currentDateTime>=$toDateTime){
                    print($booking->id);
                    $this->db->where('id',$booking->id)->update('ci_booking',array('booking_status'=>'1'));
                    // if($booking-> replaced_booking_id!='0'){
                        $getBookingList = $this->db->select('*')->from('ci_booking')
                        ->where('unique_booking_id',$booking->unique_booking_id)->where('booking_status','4')
                        ->order_by('id asc')
                        ->where('is_deleted','0')->get()->result_array();
                        if(count($getBookingList)>0){
                            print($getBookingList[0]['id']);
                             $this->db->where('id',$getBookingList[0]['id'])->update('ci_booking',array('booking_status'=>'0'));
                        }
                    // }
                    print($booking->id);
                    print('\n');
                }
                
            }else{
                
                date_default_timezone_set('Europe/London');
                 $currentDateTime = date('Y-m-d H:i:s');
                $toDateTime = date('Y-m-d H:i:s',strtotime($booking->booking_to_date.' '.$booking->reserve_to_time));
                if($currentDateTime>=$toDateTime){
                    $this->db->where('id',$booking->id)->update('ci_booking',array('booking_status'=>'1'));
                    $getBookingList = $this->db->select('*')->from('ci_booking')
                        ->where('unique_booking_id',$booking->unique_booking_id)->where('booking_status','4')
                        ->order_by('id asc')
                        ->where('is_deleted','0')->get()->result_array();
                        if(count($getBookingList)>0){
                            print($getBookingList[0]['id']);
                             $this->db->where('id',$getBookingList[0]['id'])->update('ci_booking',array('booking_status'=>'0'));
                        }
                    print($booking->id);
                    print('\n');
                }
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
    
	public function sensorSlotstatusDetection(){
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
                                     
                                            // $this->notificationApiVerifier($getSlotDetails[0]['place_id'],$getSlotDetails[0]['slot_no'],'Sensor not working',$message,'0','10');
                                            $this->issuecreate_sensor($sensor['device_id'],
                                            0,
                                            $getSlotDetails[0]['place_id'],
                                            $getSlotDetails[0]['slot_no'],
                                            'Sensor not working',
                                            $message,
                                            '0',
                                            '10'
                                            );
                                          
                                      }else{
                                            $notifyLastCreated= $getNotify[0]->onCreated;
                                            $currentDatetime =  new DateTime(date("Y-m-d H:i:s"));
                                            $interval = $currentDatetime->diff(new DateTime($notifyLastCreated));
                                            echo  ' -----  '.$interval->i.' '.$interval->s.' -----  ';
                                            if($interval->i>0){
                                                // $message ='Some object is present over Sensor id: '.$sensor['device_id'].' of Slot :'.$getSlotData[0]['display_id'].' for placename :  '.$placename;
                                             // print($message);
                                             $this->issuecreate_sensor($sensor['device_id'],
                                                                         0,
                                                                         $getSlotDetails[0]['place_id'],
                                                                         $getSlotDetails[0]['slot_no'],
                                                                         'Sensor not working',
                                                                         $message,
                                                                         '0',
                                                                         '10');
                                               
                                            
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


            $listof_Slots = $this->db->select('slot_no,display_id')->from('ci_parking_slot_info')->order_by('slot_no','ASC')
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
                            ||$fromDate_u<=$fromDate_s&&$toDate_u>=$toDate_s||$fromDate_s<=$fromDate_u&&$toDate_s>=$toDate_u) {
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
                    "data"=>$slot_availabledata,'walletbalance'=>$walletbalance,'session'=>'1'));
                    
        // }else{
        //     $msg = array('status' => false, 'message' => 'Session expired',"data"=>[],'session'=>'0');
        //     echo json_encode($msg);
        // }
                    
                }
                else{$msg = array('status' => false, 'message' => strip_tags(validation_errors()),"data"=>[],'walletbalance'=>$walletbalance,'session'=>'1');
            echo json_encode($msg);}
                
                
            
    
}
    //$getSlotData[0]
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
                                                if($interval->i>3){
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
                // if($checkAuthoriz[0]->token==$token){
                //     return true;   
                // }else{
                // return false;    
                // }
                return true;
            }else{
              return false;
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
	
	public function tokenDecodeData($token) //token
	 {
        $jwt = new JWT();
        $jwtsecretkey = 'parkingAdda_user@2021'; //sceret key for token
        $data = $jwt->decode($token, $jwtsecretkey, true);
        return $data;
        
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
                ->post('lat'));

            $long1 = $this
                ->security
                ->xss_clean($this
                ->input
                ->post('long'));
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
        $updateData=array('version'=>'',
                            'build_no'=>'',
                            'app_url'=>'',
                            'update_type'=>'',     //0=flexible update,1=force update 
                            'whats_new'=>'',
                            'notice_count'=>'',
                            'mobiletype'=>'');
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
                        if($app_version<$app_version_db &&
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
            // $getofferDetails = $this->db->select('*')->from('ci_offers_master')
            // ->where('is_active','0')->where('is_deleted','0')->get()->result_array();
            /*$getofferDetails = $this->db->select('*')->from('ci_offers_master')->where('is_active','0')
            ->where('fromDate<=',date('Y-m-d'))->where('toDate>=',date('Y-m-d'))
            ->where('is_deleted','0')->order_by('priority asc')->get()->result_array();*/
            $getofferDetails=$this->newOfferuserValidation($user_id);
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
                    $message=$getofferDetails['offerData']['offerDesc'].' & Hurry and grab this offer as   '. $getofferDetails['message'] ;
                    $offerDetails =array('offerheader'=>$isOfferValid==true?$getofferDetails['offerData']['offerText']:'','offerdesc'=>$isOfferValid==true?$message:'');
                }
            $getAdvertisement = $this
                ->db
                ->select('id,ad_imageLink')
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
                        'appupdateDetails'=>$updateData
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
                        'appupdateDetails'=>$updateData
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
                    'appupdateDetails'=>$updateData
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
                'appupdateDetails'=>$updateData
            );
            echo json_encode($msg);
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
    
    public function get_bookings_placewise()
    {
        $this->form_validation->set_rules('place_id', 'Place Id', 'required');
        date_default_timezone_set('Asia/Kolkata');
        
        if ($this->form_validation->run()) 
        {
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
            
            
            if(!empty($timewise))
            {
                $getStatusVerifier = $this->db->select('id,subject')->from('master_verifier_issues')->where('type','1')->get()->result();
                $msg = array('status' => true, 'message' => "List of bookings assigned to verifier", 'bookings' => $timewise_new,'issuelist'=>
                count($getStatusVerifier)>0?$getStatusVerifier:
                    [],'desposition'=>$getdesposition);
                echo json_encode($msg);
            } 
            else 
            {
                $msg = array('status' => true, 
                'message' => "List of bookings assigned to verifier", 
                'bookings' => [],
                'issuelist'=>[],
                'desposition'=>$getdesposition);
                echo json_encode($msg);
            }
        }
        else{
             $mesg = array('status' => false,
                            'message' => strip_tags(validation_errors()),
                            'bookings' => [],
                            'issuelist'=>[],
                            'desposition'=>[]
                            );
                            echo json_encode($mesg);
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
    
   /*  public function get_bookings_placewise()
    {
        $this->form_validation->set_rules('place_id', 'Place Id', 'required');
        date_default_timezone_set('Asia/Kolkata');
        
        if ($this->form_validation->run()) {
            $place_id = $this->security->xss_clean($this->input->post('place_id'));
            
            $timewise = [];
            
            $this->db->select('BaseTbl.id as bookingId,,BaseTbl.book_ext,booking_from_date,booking_to_date,from_time,to_time,booking_type,BaseTbl.booking_status, car_det.car_number as carNo, BaseTbl.slot_id, BaseTbl.unique_booking_id, BaseTbl.replaced_booking_id, parking_slot_info.display_id, user.mobile_no as userNo');
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
                
                //$d,$place_id,$timewise,$n,$verify_status,$timewise_new
                $currendate_fulld=date("Y-m-d H:i:s");
                // $currendate_fulld=date("2022-03-09 12:49:00");
                // $currendate_d=date("2022-03-09");
                $currendate_d=date("Y-m-d");
                if($d['booking_type']=='0'){  // daily
                
                    $fromDate_s = date('Y-m-d H:i:s', strtotime($d['booking_from_date'] . ' ' . $d['from_time']));
                    $toDate_s =date('Y-m-d H:i:s', strtotime($d['booking_to_date'] . ' ' . $d['to_time']));
                    if($fromDate_s<=$currendate_fulld&&$toDate_s>=$currendate_fulld)
                    {
                        $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $data['isverifiedBooking']=false;
                        array_push($timewise_new,$data);
                    }else{
                        $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $data['isverifiedBooking']=true;
                        array_push($timewise_new,$data);
                    }
                    
                }
                else
                {     //passes
                // print('pass');
                    $fromDate_s = date('Y-m-d H:i:s', strtotime($currendate_d .' '. $d['from_time']));
                    $toDate_s =date('Y-m-d H:i:s', strtotime($currendate_d .' '. $d['to_time']));
                    if($fromDate_s<=$currendate_fulld&&$toDate_s>=$currendate_fulld){
                        $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $data['isverifiedBooking']=false;
                        array_push($timewise_new,$data);
                    }else{
                         $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $data['isverifiedBooking']=true;
                        array_push($timewise_new,$data);
                    }
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
    }*/
    
    public function get_verifier_bookings()
    {
        $this->form_validation->set_rules('verifier_id', 'Verifier Id', 'required');
        date_default_timezone_set('Asia/Kolkata');
        
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
            foreach($verifier as $v)
            {
                
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
                
                foreach($timewise as $v1){
                    //$v1,$data
                    $currendate_fulld=date("Y-m-d H:i:s");
                // $currendate_fulld=date("2022-03-09 12:49:00");
                // $currendate_d=date("2022-03-09");
                $currendate_d=date("Y-m-d");
                if($v1['booking_type']=='0')
                {  // daily
                
                    $fromDate_s = date('Y-m-d H:i:s', strtotime($v1['booking_from_date'] . ' ' . $v1['from_time']));
                    $toDate_s =date('Y-m-d H:i:s', strtotime($v1['booking_to_date'] . ' ' . $v1['to_time']));
                    if($fromDate_s<=$currendate_fulld&&$toDate_s>=$currendate_fulld)
                    {
                        // $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $v1['iscompletedTime']=false;
                        $value=  $this->get_verifier_bookings_logic($v1);  
                         array_push($data, $value);
                    }else{
                        // $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $v1['iscompletedTime']=true;
                        $value=  $this->get_verifier_bookings_logic($v1);  
                         array_push($data, $value);
                    }
                    
                }
                else
                {     //passes
                // print('pass');
                    $fromDate_s = date('Y-m-d H:i:s', strtotime($currendate_d .' '. $v1['from_time']));
                    $toDate_s =date('Y-m-d H:i:s', strtotime($currendate_d .' '. $v1['to_time']));
                    if($fromDate_s<=$currendate_fulld&&$toDate_s>=$currendate_fulld){
                        // $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                        $v1['iscompletedTime']=false;
                        // array_push($timewise_new,$data);
                         $value=  $this->get_verifier_bookings_logic($v1);  
                         array_push($data, $value); 
                    }else{
                        //  $data=$this->verifier_bookings_logic($d,$place_id,$timewise,$n,$verify_status,$timewise_new);
                         $v1['iscompletedTime']=true;
                         $value=  $this->get_verifier_bookings_logic($v1);  
                         array_push($data, $value); 
                    }
                }
                 
                }
                
            $i++;
                
            }
            $status_n = [];
            $timewise_new=[];
            $n = 0;
            foreach($data as $d){
                
                if(in_array($data[$n]['booking_id'] , $verify_status)){
                    $this->db->select('booking_id, verify_status');
                    $this->db->from('ci_booking_verify');
                    $this->db->where('booking_id', $data[$n]['booking_id']);
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
                    
                array_push($timewise_new,$d);
            $n++;}
            
            if(!empty($data)){
                $getStatusVerifier = $this->db->select('id,subject')->from('master_verifier_issues')->where('type','1')->get()->result();
                $msg = array(
                    'status' => true,
                    'message' => "List of bookings assigned to verifier",
                    'data' => $timewise_new,
                    'issuelist'=>count($getStatusVerifier)>0?$getStatusVerifier:[], 
                    'enforcerList'=> $enforcerList,
                    'desposition'=>$getdesposition
                    );
                echo json_encode($msg);
            } else {
                $msg = array(
                'status' => true,
                'message' => "List of bookings assigned to verifier",
                'data' => [],
                'issuelist'=>[],
                'enforcerList'=> [],
                'desposition'=>$getdesposition
                );
                echo json_encode($msg);
            }
        }
        else{
            
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

    public function exports_data()
    {
            // $data[] = array('x'=> '$x', 'y'=> '$y', 'z'=> '$z', 'a'=> '$a');
            //SELECT * FROM `mpc_sensor` WHERE device_id=1 AND created_date>='2022-04-08 18:00:00' ORDER by id DESC 
            /* 	id 	place_id	slot_id	device_id	status	lat	lon	sensor_time	battery_voltage	created_date	updated_date*/
            // $newdata=array('id', 	'place_id',	'slot_id',	'device_id',	'status',	'lat',	'lon',	'sensor_time',	'battery_voltage',	'created_date',	'updated_date');
            $data=$this->db->select('*')->from('mpc_sensor')->where('device_id','23')->where('created_date>=','2022-07-19 00:00:00')
            ->order_by('id DESC')->get()->result_array();
            // $totaldata=array_push($newdata,$data);
             header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"device_new9".".csv\"");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');

            foreach ($data as $data_array) {
                fputcsv($handle, $data_array);
            }
                fclose($handle);
            exit;
        }
        
    public function onAutoCompleted_bookingApiPass() // cronjob apis
    {
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
    
    public function unverifierBookings($booking)
    {
         $isbookingverfied=false;
        if($booking['booking_type']=='0') // daily booking
        {
           
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
        {
         $currentdate = date('Y-m-d');
            $fromdate=date('Y-m-d',strtotime($booking['booking_from_date']));
            $todate=date('Y-m-d',strtotime($booking['booking_to_date']));
            if($fromdate<=$currentdate&&$todate>=$currentdate)
            {
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
            }
        }
        
        return array('isbookingverfied'=>$isbookingverfied,'bookingData'=>$booking);
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
    
    public function booking_extention_Verifier()
    {

        // exit();
        $this
            ->form_validation
            ->set_rules('verifier_id', 'Verifier Id', 'required');
        $this
            ->form_validation
            ->set_rules('ext_hrs', 'Car Id', 'required');
        $this
            ->form_validation
            ->set_rules('bookingId', 'Car Id', 'required');
       
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
                        'paid_status'=>'1',
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
                        $this->notificationallApiBuilding($b,'Your Booking',$message,'3','1',true); //3= Your booking detail screen
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
                            $message = "$noOfdaysRemaining days is remaining for you.";
                            $returnData = array('isoffervalid'=>true,'message'=>$message,'offerData'=>$getOfferData[0]);
                            return $returnData;
                        }
                        
                    }
                    else{
                        $currentDatetime =  date("Y-m-d");
                        $fromdate=date('Y-m-d',$getOfferData[0]['fromDate']);
                        $todate=date('Y-m-d',$getOfferData[0]['toDate']);
                        if($fromdate<=$currentDatetime&&$todate>=$currentDatetime){
                             $message = $getOfferData[0]['offerDesc'];
                            $returnData = array('isoffervalid'=>true,'message'=>$message,'offerData'=>$getOfferData[0]);
                            return $returnData;
                        }
                    }
                        
                }
                    // else{
                    //     $returnData = array('isoffervalid'=>false,'message'=>"No offer available");
                    //     return $returnData;
                    // }
                }
                // else
                // {
                //   $returnData = array('isoffervalid'=>false,'message'=>"No offer available");
                //         return $returnData;
                // }
        }
        else
        {
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
                                // print('inside is_per_user');
                                $getBookingList = $this->db->select('*')->from('ci_booking')->where('book_ext','')
                                ->where('user_id',$userid)
                                ->where('is_deleted','0')
                                // ->where('','')
                                ->get()->result_array();
                                // print_r($getBookingList);
                                if(count($getBookingList)<5)
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
                                $message = $getOfferData[0]['offerDesc'];
                                $returnData = array('isoffervalid'=>true,'message'=>$message,'offerData'=>$getAllOfferList[0]);
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
    }
    
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
    
    public function offermaptouser()
    {
        $getuserList = $this->db->select('*')->from('ci_users')->where('is_active','1')->get()->result_array();
        foreach($getuserList as $user){
            $getOffer=$this->db->select('*')->from('ci_offers_master')->where('is_deleted','0')->where('is_active','0')->order_by('id asc')->get()->result_array();
            if(count($getOffer)>0)
            {
                $insertData = array('user_id'=>$user['id'],'offer_id'=>$getOffer[0]['id'],'created_at'=>date('Y-m-d'));
                // print_r($insertData);
                $this->db->insert('ci_offer_users',$insertData);
            }
        }
        
    }
    
    public function sendAllNotification()
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

	
}

?>