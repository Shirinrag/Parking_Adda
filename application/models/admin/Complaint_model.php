<?php

	class Complaint_model extends CI_Model{



	public function get_complaint_datas(){

	    

		 $user_type = $this->session->userdata('admin_role');

		 $id = $this->session->userdata('admin_id');

		 

		 

	    	$this->db->select('

			ca.firstname as verifier_name,ci_parking_places.placename,

			tbl_verifier_complaints.id as complaint_id,

			ci_parking_places.place_address,ci_parking_slot_info.slot_no,

			ci_parking_slot_info.slot_name,

			ci_parking_slot_info.display_id,ci_booking.unique_booking_id,

			ci_booking.booking_from_date,ci_booking.booking_from_date,

			ci_booking.booking_to_date,ci_booking.booking_to_date,

			ci_booking.from_time,ci_booking.to_time,

			tbl_verifier_complaints.complaint_text,

			tbl_verifier_complaints.status,tbl_verifier_complaints.actionPerformedByEnforcer as enf_status');

			

			

			

		 $this->db->from('tbl_verifier_complaints');

		 $this->db->join('ci_admin ca','tbl_verifier_complaints.verifier_id=ca.admin_id','left');

		 $this->db->join('ci_parking_places','tbl_verifier_complaints.place_id=ci_parking_places.id','left');

		 $this->db->join('ci_parking_slot_info','tbl_verifier_complaints.slot_id=ci_parking_slot_info.slot_no','left');

		 $this->db->join('ci_booking','tbl_verifier_complaints.booking_id=ci_booking.id','left');

		 $this->db->join('ci_admin','tbl_verifier_complaints.enforcer_id=ci_admin.admin_id','left');

		 $this->db->join('ci_users','ci_booking.user_id=ci_users.id','left');

		 $this->db->order_by('tbl_verifier_complaints.id','desc');

		 if($user_type=='Enforcer'){

		  $this->db->where('tbl_verifier_complaints.enforcer_id',$id);

		}

		 if($this->session->userdata('complaint_type')!='' && $this->session->userdata('complaint_type')!=0){

			$this->db->where('tbl_verifier_complaints.actionPerformedByEnforcer	',$this->session->userdata('complaint_type'));}

			$this->session->unset_userdata('complaint_type');



		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;



	}





	public function upateComplaints(){

	

	    $unique_id = $this->input->post('unique_id');

		$issue_type = $this->input->post('issue_type');

		$enforcers_remark = $this->input->post('enforcers_remark');



		$data = array('actionPerformedByEnforcer'=>$issue_type,

		'remark'=>$enforcers_remark);



		$this->db->where('id', $unique_id);

		$this->db->update('tbl_verifier_complaints', $data);

		return true;

	}





	



	public function get_cc_complaint_datas(){

		 $user_type = $this->session->userdata('admin_role');

		 $id = $this->session->userdata('admin_id');

		$this->db->select('

			ca.firstname as verifier_name,ci_parking_places.placename,tbl_verifier_complaints.complaint_source as source,

			tbl_verifier_complaints.id as complaint_id,ci_booking.user_id,ci_booking.id as booking_id,

			ci_parking_places.place_address,ci_parking_slot_info.slot_no,

			ci_parking_slot_info.slot_name,

			ci_parking_slot_info.display_id,ci_booking.unique_booking_id,

			ci_booking.booking_from_date,ci_booking.booking_from_date,

			ci_booking.booking_to_date,ci_booking.booking_to_date,

			ci_booking.from_time,ci_booking.to_time,

			tbl_verifier_complaints.complaint_text,

			tbl_verifier_complaints.status,tbl_verifier_complaints.actionPerformedByEnforcer as enf_status,

			tbl_verifier_complaints.actionPerformedByVerifier as vf_status,tbl_verifier_complaints.fk_despostion_id');

		 $this->db->from('tbl_verifier_complaints');

		 $this->db->join('ci_admin ca','tbl_verifier_complaints.verifier_id=ca.admin_id','left');

		 $this->db->join('ci_parking_places','tbl_verifier_complaints.place_id=ci_parking_places.id','left');

		 $this->db->join('ci_parking_slot_info','tbl_verifier_complaints.slot_id=ci_parking_slot_info.slot_no','left');

		 $this->db->join('ci_booking','tbl_verifier_complaints.booking_id=ci_booking.id','left');

	     $this->db->join('ci_admin','tbl_verifier_complaints.enforcer_id=ci_admin.admin_id','left');

		 $this->db->join('ci_users','ci_booking.user_id=ci_users.id','left');

		 $this->db->order_by('tbl_verifier_complaints.id','desc');

		 $this->db->where('tbl_verifier_complaints.status',1);

		

	

	

		if($this->session->userdata('cc_complaint_type')!='' && $this->session->userdata('cc_complaint_type')!='0'){

			$this->db->where('tbl_verifier_complaints.actionPerformedByEnforcer	',$this->session->userdata('cc_complaint_type'));}

			else{

			    $this->session->unset_userdata('cc_complaint_type');

			}

			$this->session->unset_userdata('cc_complaint_type');

			

			

		



		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;



	}



	public function getComplaintInfo($id)

	{



		$this->db->select('

			tbl_verifier.name as verifier_name,ci_parking_places.placename,

			tbl_verifier_complaints.id as complaint_id,

			ci_parking_places.place_address,ci_parking_slot_info.slot_no,

			ci_parking_slot_info.slot_name,

			ci_parking_slot_info.display_id,ci_booking.unique_booking_id,

			ci_booking.booking_from_date,ci_booking.booking_from_date,

			ci_booking.booking_to_date,ci_booking.booking_to_date,

			ci_booking.from_time,ci_booking.to_time,

			tbl_verifier_complaints.complaint_text,

			tbl_verifier_complaints.status,tbl_verifier_complaints.actionPerformedByEnforcer as enf_status');

		 $this->db->from('tbl_verifier_complaints');

		 $this->db->join('tbl_verifier','tbl_verifier_complaints.verifier_id=tbl_verifier.id','left');

		 $this->db->join('ci_parking_places','tbl_verifier_complaints.place_id=ci_parking_places.id','left');

		 $this->db->join('ci_parking_slot_info','tbl_verifier_complaints.slot_id=ci_parking_slot_info.slot_no','left');

		 $this->db->join('ci_booking','tbl_verifier_complaints.booking_id=ci_booking.id','left');

		 $this->db->join('ci_admin','tbl_verifier_complaints.enforcer_id=ci_admin.admin_id','left');

		 $this->db->join('ci_users','ci_booking.user_id=ci_users.id','left');

		 $this->db->order_by('tbl_verifier_complaints.id','desc');

		 $this->db->where('tbl_verifier_complaints.id',$id);



		



		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;



	}







   // For Getting the users related data 

	public function getUsersInfo($id) 

	{ 

		$this->db->from('ci_booking');

		$this->db->join('ci_users','ci_booking.user_id=ci_users.id','left');

		$this->db->where('ci_booking.id',$id);

		$query=$this->db->get();

		return $query->row_array();



	}



	

	function getReplacementsData($uniqueBookingId){

    

            $this->db->select('cb.id,cpsi.slot_name,cpsi.display_id');

          	$this->db->from('ci_booking cb');

	        $this->db->join('ci_parking_slot_info cpsi','cb.slot_id = cpsi.slot_no','left');

	        $this->db->where('cb.unique_booking_id',$uniqueBookingId);

	       $this->db->order_by('cb.id','desc');

	        $query = $this->db->get();

		    $data = array();

		    if($query !== FALSE && $query->num_rows() > 0){

   			    $data = $query->result_array();

			}

	    	return $data;

	    	

	}

	

	

	

	function getBookingInfoById($id){



		$this->db->select('tbl_verifier_complaints.complaint_source,cdes.descriptions,ca.firstname as verifier_name,ca.mobile_no as verifier_contact,ci_parking_places.placename,

			tbl_verifier_complaints.id as complaint_id,ci_booking.user_id,ci_booking.id as booking_id,

			ci_parking_places.place_address,ci_parking_slot_info.slot_no,

			ci_parking_slot_info.slot_name,

			ci_parking_slot_info.display_id,ci_booking.unique_booking_id,

			ci_booking.booking_from_date,ci_booking.booking_from_date,

			ci_booking.booking_to_date,ci_booking.booking_to_date,

			ci_booking.from_time,ci_booking.to_time,ci_booking.paid_status,ci_booking.created_date,ci_booking.booking_type,ci_booking.cost,

			ci_booking.reserve_from_time,ci_booking.reserve_to_time,tbl_verifier_complaints.complaint_id as uq_complaint,

			tbl_verifier_complaints.complaint_text,ci_car_details.car_name,ci_car_details.car_number,tbl_verifier_complaints.customercareRemark,

			CONCAT(ci_admin.firstname, '   . ' ,ci_admin.lastname) as enforcer_name,ci_admin.email as enforcer_email,ci_admin.mobile_no as mobile_no,

			tbl_verifier_complaints.status,tbl_verifier_complaints.actionPerformedByEnforcer as enf_status,tbl_verifier_complaints.actionPerformedByVerifier as vf_status,tbl_verifier_complaints.issue_img');

		 $this->db->from('tbl_verifier_complaints');

		 $this->db->join('ci_despositions cdes','tbl_verifier_complaints.fk_despostion_id=cdes.id','left');

		 $this->db->join('ci_admin ca','tbl_verifier_complaints.verifier_id=ca.admin_id','left');

		 $this->db->join('ci_parking_places','tbl_verifier_complaints.place_id=ci_parking_places.id','left');

		 $this->db->join('ci_parking_slot_info','tbl_verifier_complaints.slot_id=ci_parking_slot_info.slot_no','left');

		 $this->db->join('ci_booking','tbl_verifier_complaints.booking_id=ci_booking.id','left');

	     $this->db->join('ci_admin','tbl_verifier_complaints.enforcer_id=ci_admin.admin_id','left');

		 $this->db->join('ci_users','ci_booking.user_id=ci_users.id','left');

		 $this->db->join('ci_car_details','ci_booking.car_id=ci_car_details.id','left');

		 $this->db->order_by('tbl_verifier_complaints.id','desc');

		 $this->db->where('ci_booking.id',$id);



		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

	}

	

	

	



	

	

	public function cancelBooking($userid,$booking_id ){

	    

	    $booking= $this->db->select('*')->from('ci_booking')->where('id',$booking_id)->where('user_id',$userid)->get()->result();

	    

                if(count($booking)>0){

                

                  $cancledBooking=  $this->db->where('id',$booking_id)->update('ci_booking',array('booking_status'=>'2'));

                  if($cancledBooking){

                    $get_amt = $this->db->select('*')->from('ci_wallet_user')->where('user_id',$userid)->get()->result();

                    if(count($get_amt)>0){

                        $new_amt =$get_amt[0]->amount+$booking[0]->cost;

                        $this->db->where('id',$get_amt[0]->id)->update('ci_wallet_user',array('amount'=>(float)$new_amt));

                        $inserData1=array("wallet_id"=>$get_amt[0]->id,"user_id"=>$get_amt[0]->user_id,"amount"=>$booking[0]->cost,"status"=>'1',"payment_type"=>'3','booking_id'=>$booking_id);

                        // $insertPayment1 = $this->db->insert('ci_wallet_history',$inserData1);  old comented unique txn id for payment

                        $this->wallet_history_log($inserData1);

                        $updateComplaintStatus=  $this->db->where('booking_id',$booking_id)->update('tbl_verifier_complaints',array('status'=>'1'));

                        

                        

                        $getNotify = $this->db->select('*')->from('ci_notify_track')->where('booking_id',$booking_id)->where('user_id',$userid)

                            ->where('notify_type','5')

                            ->where('is_deleted','0')

                            ->get()->result();

                            $emoji ="\u{E007F}";

                    $message= 'Your booking has been cancelled '.$emoji.' & Amount '.$booking[0]->cost.' has been refunded to your wallet';

                    $this->notificationForWallet($userid,$booking_id,$booking[0]->place_id,$booking[0]->slot_id, 'Booking & Wallet', $message,'6','5'); //6= booking list screen,5= refunded

                    }

                      

                  }

                    $msg = array('message' => "Successfully cancelled Booking",'status'=>'1');

                    return $msg;

                

                }else {

                    $msg = array('message' => "You cannot cancle this Booking !!",'status'=>'0');

                    return $msg;

                

                }

	    

	}

	

	public function replaceBooking($bookingId)

	 {

	      


          $getbooking = $this->db->select('*')->from('ci_booking')->where('id',$bookingId)->get()->result();
		  if($getbooking[0]->booking_status ==1 || $getbooking[0]->booking_status==2 || $getbooking[0]->booking_status==4){

              

               

                return $data['msg']="false";

          }

          else{

              

         

           if(count($getbooking)>0){

               

                $slot_id =  $this->voice_slot_available($getbooking[0]->place_id,$getbooking[0]->booking_from_date,$getbooking[0]->booking_to_date,$getbooking[0]->from_time,$getbooking[0]->to_time);

                

                if($slot_id!=''){

                $datainsert=array(

                   // 'unique_booking_id'=>$bookingid1,	

                    'user_id'	=>$getbooking[0]->user_id,

                    'place_id'	=>$getbooking[0]->place_id,

                    'unique_booking_id'=>$getbooking[0]->unique_booking_id,

                    'slot_id'	=>$slot_id,

                    'booking_status'=>'0', 	

                    'replaced_booking_id'	=>$getbooking[0]->id,

                    'booking_from_date'    =>$getbooking[0]->booking_type==1? date('Y-m-d'):$getbooking[0]->booking_from_date,

                    'booking_to_date'    =>$getbooking[0]->booking_type==1?date('Y-m-d'):$getbooking[0]->booking_to_date,

                    

                    // 'booking_from_date'	=>$getbooking[0]->booking_from_date,

                    // 'booking_to_date'	=>$getbooking[0]->booking_to_date,

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

                    $data = $this->db->insert('ci_booking',$datainsert);

                    

                    $last_id = $this->db->insert_id();

                    if($data){

                       $this->db->where('id',$bookingId)->update('ci_booking',array('booking_status'=>'4'));



                    }

              

                    

                      $rep_bookingId='';

                        $par_data=$this->db->select('*')->from('ci_booking')->where('unique_booking_id',$getbooking[0]->unique_booking_id)->like('book_ext','REP')->order_by("id", "Desc")->get()->result();

                        if(count($par_data)>0){

                            

                            $expoit = explode("P",$par_data[0]->book_ext);

                            $count = $expoit[1]+1;

                            $rep_bookingId = 'REP'.$count;

                        }else{

                            $rep_bookingId = 'REP'.'1';

                        }

                        

	                $this->db->where('id',$last_id)->update('ci_booking',array('book_ext'=>$rep_bookingId));

                    $updateComplaintStatus=  $this->db->where('booking_id',$bookingId)->update('tbl_verifier_complaints',array('status'=>'1'));

                    $getwalletid = $this->db->select('*')->from('ci_wallet_user')->where('user_id',$getbooking[0]->user_id)->get()->result();

                    if(count($getwalletid)>0){

                        /*

                        	wallet_id	user_id	amount	status 	payment_type 	

                        	booking_id  */

                        	$walletDatainsert=array(

                        	    'wallet_id'=>$getwalletid[0]->id,	'user_id'=>$getbooking[0]->user_id,	'amount'=>0,	'status'=>'4','payment_type'=>'5',

                        	'booking_id'=>$last_id);

                        // $this->db->insert('ci_wallet_history',$walletDatainsert);	

                         $this->wallet_history_log($walletDatainsert);



                    }

                    

                    $message = 'Your Booking : '.$getbooking[0]->unique_booking_id.' is replaced.';

                    $getNotify = $this->db->select('*')->from('ci_notify_track')->where('booking_id',$getbooking[0]->id)->where('user_id',$getbooking[0]->user_id)

                    ->where('notify_type','8')

                    ->where('is_deleted','0')

                    ->get()->result();

                    // print_r($getNotify);

                    if(count($getNotify)<=0){

                        // print($message);

                    $this->notificationallApiBuilding($getbooking[0],'Your Booking',$message,'3','8'); //3= Your booking detail screen

                    // $message = 'A Slot ( ID : '.$getwalletid[0]->slot_id.') has been booked at '.$getplaceName[0]->placename.' From '.$getwalletid[0]->from_time.' to '.$getwalletid[0]->to_time ;

                    $message ='Booking '.$getbooking[0]->unique_booking_id.' is replaced.';

                    $this->notificationApiVerifier($getbooking[0],'Booking',$message,'0','0');

                    }else{

                        // print('Notification already went');

                    }

            // print_r($datainsert);    

           }else{

             return $data['msg']="false";  

           }

           }

          }

          

        }

	public function getDespositions(){

	    

		$this->db->from('ci_despositions');

		$this->db->where('status',1);

		$query=$this->db->get();

		return $query->result_array();

	}



	public function update_cc_remarks($data, $id){

        $data['resolvedDate'] = date('Y-m-d : h:m');
     	$this->db->where('booking_id', $id);
    	$this->db->update('tbl_verifier_complaints', $data);
		$this->AddSlotsComplaints($id);
        return true;

	}

	

	public function updateCancelRemark($data, $id){

	 

	    $data['resolvedDate'] = date('Y-m-d : h:m');

		$this->db->where('booking_id', $id);

		$this->db->update('tbl_verifier_complaints', $data);

		return true;

	}

	

	public function UpdateStatus($id){

	    

	    $data['resolvedDate'] = date('Y-m-d : h:m');

	     $data['status'] = 2;

		$this->db->where('id', $id);

		$this->db->update('tbl_verifier_complaints', $data);

		return true;

	    

	}

	

	

	public function updateComplaintStatus($datas, $id){

	 

		$data = array('booking_status'=>0);

		$this->db->where('id', $id);

		$result = $this->db->update('ci_booking', $data);

		if($result){

		    $datas['resolvedDate'] = date('Y-m-d : h:m:s');

		    $this->db->where('booking_id', $id);

		    $result = $this->db->update('tbl_verifier_complaints', $datas);

		}

		return true;

	}

	

	   // 	Replace Notification 

        

        

        public function notificationallApiBuilding($b, $title, $body,$screen,$notifyType) // this function is uses firebase api to send notification.

    {

        // $buildingId = 394;

        // $societyId = 14;

        

        $getUserTopic = $this->db->select('notifn_topic')->from('ci_users')->where('id',$b->user_id)->where('is_active','1')->get()->result();

        // $getbuildingName = $this->db->select('building_name')->from('tbl_society_setup')->where('building_id',$buildingid)->get()->result();

        // print_r($getUserTopic);

        if(count($getUserTopic)>0){

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        // $token='all';

        $token = $getUserTopic[0]->notifn_topic;

        // print(' mmmm ');print($token);

        // print(' mmmm ');

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

        if($result){

            $this->db->insert('ci_notify_track',array("notify_type"=>$notifyType,"booking_id"=>$b->id,"user_id"=>$b->user_id,"place_id"=>$b->place_id,"slot_id"=>$b->slot_id ));

        }

        curl_close($ch);





        // echo $result;

            

        }else{

            // echo 'no building found'.$buildingid;

        }

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

            // print('token is printed : '.$token);

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

    

    // Ended Replace Notification 

	

	 public function notificationForWallet($userId, $bookingId,$place_id,$slot_id,$title, $body,$screen,$notifyType) // this function is uses firebase api to send notification.

     {

            // $buildingId = 394;

            // $societyId = 14;

        

            $getUserTopic = $this->db->select('notifn_topic')->from('ci_users')->where('id',$userId)->where('is_active','1')->get()->result();

            // $getbuildingName = $this->db->select('building_name')->from('tbl_society_setup')->where('building_id',$buildingid)->get()->result();

            // print_r($getUserTopic);

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

    

    

            // echo $result;

                

            }

            else{

                // echo 'no building found'.$buildingid;

            }

    }

    

    

    public function voice_slot_available($place_id,$from_date,$to_date,$from_time,$to_time) //slot availibility // in progress mpc_sensor

    {

         date_default_timezone_set('Asia/Kolkata');

            $multiDate = false;

             $listof_Slots = $this->db->select('slot_no,display_id')->from('ci_parking_slot_info')

                ->where('place_id', $place_id)->where('status', '0')->where('onOff_applied','0')->where('isBlocked','1')->where('is_deleted', '0')

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



public function getBookingId(){



	     $this->db->select('cb.unique_booking_id, cb.id,cu.firstname,cu.lastname,cu.mobile_no');

		 $this->db->from('ci_booking cb');

		 $this->db->join('ci_users cu','cb.user_id=cu.id','left');

		 $this->db->group_by('cb.unique_booking_id');

		 $this->db->order_by('cb.id','desc');



		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;



}



public function getDataByMobileNumber()

{

		 $this->db->select('id,firstname,lastname,mobile_no');

		 $this->db->from('ci_users');

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

}



public function getCBookingInfoById($id)

{

	 	 $this->db->select('cb.id as booking_id,cb.user_id,cb.unique_booking_id,cb.booking_from_date,

	 	 					cb.booking_from_date,cb.booking_to_date,cb.booking_to_date,

							cb.from_time,cb.to_time,cb.paid_status,cb.created_date,cb.booking_type,cb.cost,

							ccd.car_name,ccd.car_number,

							cpsi.display_id,cpsi.slot_name,

							cpp.placename,cpp.place_address');

		 $this->db->from('ci_booking cb');

		 $this->db->join('ci_parking_places cpp','cb.place_id = cpp.id','left');

		 $this->db->join('ci_parking_slot_info cpsi','cb.slot_id = cpsi.slot_no','left');

		 $this->db->join('ci_car_details ccd','cb.car_id = ccd.id','left');

		 $this->db->where('cb.id',$id);

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

}



public function getMasterIssues(){

	$this->db->from('master_verifier_issues');

	 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

		

}



public function getCUsersInfo($id) { 

		$this->db->from('ci_users');

		$this->db->where('id',$id);

		$query=$this->db->get();

		return $query->row_array();

	}

	

public function getCarInfoByUserId($id){

		 $this->db->from('ci_car_details ccd');

		 $this->db->where('ccd.user_id',$id);

		 $this->db->where('ccd.is_deleted',0);

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

}



	public function getTxnInfoByUserId($id){



		 $this->db->select('cb.unique_booking_id,cwh.*');	

		 $this->db->from('ci_wallet_history cwh');

		 $this->db->join('ci_booking cb','cwh.booking_id=cb.id','left');

		 $this->db->where('cwh.user_id',$id);

		 $this->db->order_by('cb.id','desc');

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

}



                    // Added On Date 08-03-2022





    public function getVerifiersByPlaceId($id){

		 $this->db->select('cpp.placename,cpp.place_address, ca.admin_id as verifier_id, ca.firstname,ca.lastname,ca.mobile_no');	

		 $this->db->from('tbl_verifier_place tvp');

		 $this->db->join('ci_booking cb','tvp.place_id  = cb.place_id','left');

		 $this->db->join('ci_parking_places cpp','tvp.place_id  = cpp.id','left');

		 $this->db->join('ci_admin ca','tvp.verifier_id = ca.admin_id','left');

		 $this->db->where('cb.id',$id);
		 $this->db->where('tvp.duty_date',date('Y-m-d'));
		 $this->db->where('ca.is_active',1);

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

}





        public function getEnforcersByPlaceId($id){

            

        

		 $this->db->select('ca.firstname,ca.lastname,ca.mobile_no');

		 $this->db->from('ci_booking cb');

		 $this->db->join('tbl_enforcer_place  tep','cb.place_id = tep.place_id','left');

		 $this->db->join('ci_admin ca','tep.enforcer_id = ca.admin_id','left');

		 $this->db->where('cb.id',$id);

	   	 $querys = $this->db->get();

		 $datas = array();

		if($querys !== FALSE && $querys->num_rows() > 0){

   			 $datas = $querys->result_array();

			}

            return $datas;

			

    }

















    public function getComplaints(){

		 $this->db->from('ci_booking_complaint_master cbcm');

		 $this->db->where('cbcm.status','1');

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

}







    public function getComplaintsinfoById($id,$id1){

	 	 $this->db->from('tbl_verifier_complaints tvc');

		 $this->db->where('tvc.booking_id',$id);

		 $this->db->where('tvc.id',$id1);

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

}





public function getDataForComplaint($booking_id){

	     $this->db->select('cb.user_id,cb.place_id,cb.slot_id');	

		 $this->db->from('ci_booking cb');

		 $this->db->where('cb.id',$booking_id);

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

}



public function AddDirectComplaintData($data){

	$this->db->insert('tbl_verifier_complaints', $data);

	return $last_id = $this->db->insert_id();

		 

}



  // Added By Raj On 09-03-2022

  function wallet_history_log($message){

        $transac_id=$this->create_transac_id();

        $message['transac_id']=$transac_id;

        // echo "<pre>";

        // print_r($message['transac_id']);

        // die;

        return  $insertPayment1 = $this->db->insert('ci_wallet_history',$message);

    }

    

    function create_transac_id(){

      

        $transcac_id = '';

        for($i = 0; $i < 12; $i++) { $transcac_id .= mt_rand(0, 9); }

        $getresult = $this->db->Select('*')->from('ci_wallet_history')->where('transac_id',$transcac_id)->get()->result();

        if(count($getresult)>0){

            $this->create_transac_id();

        }

        else{

            return $transcac_id;

        }

    }

    

    public function UpdateDirectComplaints($data,$unique_booking_id){

		$this->db->where('id', $unique_booking_id);

		$this->db->update('tbl_verifier_complaints', $data);

		return true;



}



public function updateDComplaintStatus($booking_id,$id){

	 

		$data = array('booking_status'=>0);

		$this->db->where('id', $booking_id);

		$result = $this->db->update('ci_booking', $data);

		if($result){

		    $datas['resolvedDate'] = date('Y-m-d : h:m:s');

		    $this->db->where('id', $id);

		    $result = $this->db->update('tbl_verifier_complaints', $datas);

		}

		return true;

	}

	





public function getBookingComplaints($id){



		 $this->db->from('ci_booking_complaint_master as cbcm');

		 $this->db->where('cbcm.id',$id);

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;



}



        // Added New 10/03/2022

        

function getDBookingInfoById($id){



		$this->db->select('tbl_verifier_complaints.complaint_source,cdes.descriptions,ca.firstname as verifier_name,ci_parking_places.placename,

			tbl_verifier_complaints.id as complaint_id,ci_booking.user_id,ci_booking.id as booking_id,

			ci_parking_places.place_address,ci_parking_slot_info.slot_no,

			ci_parking_slot_info.slot_name,

			ci_parking_slot_info.display_id,ci_booking.unique_booking_id,

			ci_booking.booking_from_date,ci_booking.booking_from_date,

			ci_booking.booking_to_date,ci_booking.booking_to_date,

			ci_booking.from_time,ci_booking.to_time,ci_booking.paid_status,ci_booking.created_date,ci_booking.booking_type,ci_booking.cost,

			ci_booking.reserve_from_time,ci_booking.reserve_to_time,tbl_verifier_complaints.complaint_id as uq_complaint,

			tbl_verifier_complaints.complaint_text,ci_car_details.car_name,ci_car_details.car_number,tbl_verifier_complaints.customercareRemark,

			CONCAT(ci_admin.firstname, '   . ' ,ci_admin.lastname) as enforcer_name,ci_admin.email as enforcer_email,ci_admin.mobile_no as mobile_no,

			tbl_verifier_complaints.status,tbl_verifier_complaints.actionPerformedByEnforcer as enf_status,tbl_verifier_complaints.actionPerformedByVerifier as vf_status');

		 $this->db->from('tbl_verifier_complaints');

		 $this->db->join('ci_despositions cdes','tbl_verifier_complaints.fk_despostion_id=cdes.id','left');

		 $this->db->join('ci_admin ca','tbl_verifier_complaints.verifier_id=ca.admin_id','left');

		 $this->db->join('ci_parking_places','tbl_verifier_complaints.place_id=ci_parking_places.id','left');

		 $this->db->join('ci_parking_slot_info','tbl_verifier_complaints.slot_id=ci_parking_slot_info.slot_no','left');

		 $this->db->join('ci_booking','tbl_verifier_complaints.booking_id=ci_booking.id','left');

	     $this->db->join('ci_admin','tbl_verifier_complaints.enforcer_id=ci_admin.admin_id','left');

		 $this->db->join('ci_users','ci_booking.user_id=ci_users.id','left');

		 $this->db->join('ci_car_details','ci_booking.car_id=ci_car_details.id','left');

		 $this->db->order_by('tbl_verifier_complaints.id','desc');

		 $this->db->where('ci_booking.unique_booking_id',$id);



		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

	}

	

	public function getUqId($id){

	     $this->db->select('id');

	     $this->db->from('ci_booking');

		 $this->db->where('ci_booking.unique_booking_id',$id);

		 $this->db->order_by('ci_booking.id','desc');

		 $this->db->limit(1);

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;



	}

	

	

	public function getUniqueId($id){

	     $this->db->select('unique_booking_id');

	     $this->db->from('ci_booking');

		 $this->db->where('ci_booking.id',$id);

		 $this->db->order_by('ci_booking.id','desc');

		 $this->db->limit(1);

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;



	}

	

	

	

	

public function getOtherBookingInfoByUserId($userid){

		 $this->db->select('source_type,id,user_id,complaint_topic,description,status,created_date');

	     $this->db->from('tbl_complaint');

		 $this->db->where('tbl_complaint.user_id',$userid);

// 		 $this->db->where('tbl_complaint.status',0);

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

	}



public function getOtherComplaintsById($id,$complaint_id){

	   	 $this->db->from('tbl_complaint');

		 $this->db->where('tbl_complaint.user_id',$id);

		 $this->db->where('tbl_complaint.id',$complaint_id);

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

}



public function getOtherComplaintMaster(){



	 	 $this->db->from('ci_other_complaint_type_master');

		 $this->db->where('status',1);

		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

}







	// Started By Raj Namdev 	14-03-2022





 public function ResolveMobileComplaints($data){

     



    

	$this->db->insert('ci_other_complaints', $data);

	$last_id = $this->db->insert_id();

	$this->db->where('id', $data['fk_tbl_complaint_id']);

	if($data['fk_disposition_id']==2){

	  $this->db->update('tbl_complaint', array('status'=>1));

	}

	else

	{

	    $this->db->update('tbl_complaint', array('status'=>2));

	}

	



	return $last_id;

		 

}





public function getDirectOtherCompByUserId($id)

{

	$this->db->select('coc.fk_disposition_id,coc.source_type,coc.id,coc.user_id,coctm.descriptions as complaint_topic,coc.problem_description as description,coc.fk_disposition_id as status,coc.created_date');

	$this->db->from('ci_other_complaints coc');

	$this->db->join('ci_other_complaint_type_master coctm','coc.complaint_type_id=coctm.id','left');

	$this->db->where('coc.user_id',$id);

	$this->db->where('coc.source_type',0);



		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

}



public function getComplaintsData($complaint_id)

{

	$this->db->from('ci_other_complaints coc');

	$this->db->where('coc.fk_tbl_complaint_id',$complaint_id);



		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

}





public function getOtherComplaintsData($complaint_id)

{

	$this->db->from('ci_other_complaints coc');

	$this->db->where('coc.id',$complaint_id);



		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

}







function UpdateMobileComplaints($data, $uniqueComplaintid,$source){

	    

    

       

	    if($source==0){



	    	$this->db->where('id', $uniqueComplaintid);

			$this->db->update('ci_other_complaints', $data);



	    }else{



	    	$this->db->where('fk_tbl_complaint_id', $uniqueComplaintid);

			$update = $this->db->update('ci_other_complaints', $data);

			if($data['fk_disposition_id']==2){

			    if($update){

			    $this->db->where('id', $uniqueComplaintid);

			    $this->db->update('tbl_complaint', array('status'=>'1'));

			}

			}

			

			

	    }

		

		return true;

	}

	

	

function CheckDataExist($id){

    $this->db->from('ci_other_complaints coc');

	$this->db->where('coc.fk_tbl_complaint_id',$id);



		 $query = $this->db->get();

		 $data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

		return $data;

} 





function getPendingBookingComplaints($dataType)

{	

      

	    $this->db->select('cb.id,cb.unique_booking_id,CONCAT(cb.booking_from_date,"  ",cb.from_time) as booking_from,

	   	CONCAT(cb.booking_to_date,"  ",cb.to_time) as booking_to, cpp.placename,cpp.place_address,CONCAT(ca.firstname," ",ca.lastname) as verifier_name, tvc.status as complint_status, mvi.subject as issue_type');

		$this->db->from('tbl_verifier_complaints tvc');

		$this->db->join('ci_booking cb','tvc.booking_id = cb.id','left');

		$this->db->join('ci_parking_places cpp','tvc.place_id = cpp.id','left');

		$this->db->join('ci_admin ca','tvc.verifier_id = ca.admin_id','left');

		$this->db->join('master_verifier_issues mvi','tvc.complaint_id = mvi.id','left');

	    if($dataType ='pending'){

	      $where = "(tvc.status = '0' || tvc.status='2') AND tvc.complaint_source = '1'";

	    }else{

	         $where = "tvc.status = '1' AND tvc.complaint_source = '1'";

	    }

		

		$this->db->where($where);

		$this->db->order_by('tvc.id','desc');

		$query=$this->db->get();

		$data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

			

	   $datas = array();

	   foreach($data as $keys =>$values){

	       $datas[] = $values;

	       $datas[$keys]['source'] = "App";

	   }

	   

      return $datas;



		



}









// Started By Raj Namdev (16.03.2022)





function getComplaintsCounts()

{

		$this->db->select('count(id) as verifiers_complaints');

		$this->db->from('tbl_verifier_complaints');

		$where = "(status = '0' || status='2') AND complaint_source = '1'";

		$this->db->where($where);

		$query=$this->db->get();

		$verifiers_complaints = $query->row_array();



// =====================================================================================================

							// Calls Complaints 

		$this->db->select('count(id) as bookingcomplaintsBycalls');

		$this->db->from('tbl_verifier_complaints');

		$where1 = "(status = '0' || status='2') AND complaint_source = '2'";

		$this->db->where($where1);

		$query1=$this->db->get();

		$data1 = $query1->row_array();



		$this->db->select('count(id) as othercomplaintsBycalls');

		$this->db->from('ci_other_complaints');

		$where2 = "fk_disposition_id = '1' AND fk_tbl_complaint_id = '0' AND  source_type='0'";

		$this->db->where($where2);

		$query2=$this->db->get();

		$data2 = $query2->row_array();

		







		$PendingCallsComplaints = $data1['bookingcomplaintsBycalls'];



//==========================================================================================================

					// Other Complaints

		$this->db->select('COUNT(id) as other_complaints');

		$this->db->from('ci_other_complaints');

		$where3 = "fk_tbl_complaint_id = '0' AND fk_disposition_id='1'";

		$this->db->where($where3);

		$query3=$this->db->get();

		$data3 = $query3->row_array();



		$this->db->select('count(id) as ci_other_complaintss');

		$this->db->from('tbl_complaint');

		$where4 = "status='0'";

		$this->db->where($where4);

		$query4=$this->db->get();

		$data4 = $query4->row_array();

		$OthersComplaints = $data3['other_complaints']+$data4['ci_other_complaintss'];

//==========================================================================================================	

								// Other Complaints 

		$this->db->select('count(id) as app_complaints');

		$this->db->from('tbl_complaint');

		$where5 = "source_type= '1' and (status='0' || status='2')";

		$this->db->where($where5);

		$query5=$this->db->get();

		$data5 = $query5->row_array();



		$this->db->select('count(id) as app_complaints');

		$this->db->from('ci_other_complaints');

		$where6 = "fk_tbl_complaint_id!='0' AND fk_disposition_id='1'";

		$this->db->where($where6);

		$query6=$this->db->get();

		$data6 = $query6->row_array();



		$UsersAppComplaints = $data5['app_complaints'];

		$data['verifier_complaints'] = $verifiers_complaints['verifiers_complaints'];

		$data['calls_complaints'] = $PendingCallsComplaints;



			// $OthersComplaints



		$data['other_complaints'] = $data2['othercomplaintsBycalls'];   

		$data['user_app_complaints'] = $UsersAppComplaints;



		return $data;



}





public function PendingUsersComplaint($dataType){



        

		 $this->db->select('CONCAT(cu.firstname," ",cu.lastname) as username,tc.source_type,tc.id,tc.user_id,tc.complaint_topic,tc.description,tc.status,tc.created_date');

	     $this->db->from('tbl_complaint tc');

	     $this->db->join('ci_users cu','tc.user_id = cu.id','left');

	        

	     if($dataType=='pending'){

	      $where5 = "tc.source_type= '1' and (tc.status='0' OR tc.status='2')";

	     }else{

	      $where5 = "tc.source_type= '1' and (tc.status='1')";

	     }

		 $this->db->where($where5);

		 $this->db->order_by('tc.created_date','desc');

		 $query = $this->db->get();

		 $data1 = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data1 = $query->result_array();

			}

		$data['mobile_app'] = $data1;

		

		

// 		echo "<pre>";

// 		print_r($data1);

// 		die;





	$this->db->select('CONCAT(cu.firstname," ",cu.lastname) as username,coc.fk_disposition_id,coc.source_type,coc.id,coc.user_id,coctm.descriptions as complaint_topic,coc.problem_description as description,coc.fk_disposition_id as status,coc.created_date');

	$this->db->from('ci_other_complaints coc');

	$this->db->join('ci_other_complaint_type_master coctm','coc.complaint_type_id=coctm.id','left');

	$this->db->join('ci_users cu','coc.user_id=cu.id','left');



	$where6 = "coc.fk_tbl_complaint_id!='0' AND coc.fk_disposition_id='1'";

	$this->db->where($where6);





		 $query2 = $this->db->get();

		 $data2 = array();

		if($query2 !== FALSE && $query2->num_rows() > 0){

   			 $data2 = $query2->result_array();

			}

		 $data['direct'] = $data2;





		 return $data;

		

}





public function getBookingComplaintsCalls($dataType){





		$this->db->select('tvc.id,tvc.booking_id,cb.unique_booking_id,CONCAT(cb.booking_from_date,"  ",cb.from_time) as booking_from,

	   	CONCAT(cb.booking_to_date,"  ",cb.to_time) as booking_to, cpp.placename,cpp.place_address,CONCAT(ca.firstname," ",ca.lastname) as verifier_name, tvc.status as complint_status, mvi.subject as issue_type');

		$this->db->from('tbl_verifier_complaints tvc');

		$this->db->join('ci_booking cb','tvc.booking_id = cb.id','left');

		$this->db->join('ci_parking_places cpp','tvc.place_id = cpp.id','left');

		$this->db->join('ci_admin ca','tvc.verifier_id = ca.admin_id','left');

		$this->db->join('master_verifier_issues mvi','tvc.complaint_id = mvi.id','left');

		$where = "(tvc.status = '0' || tvc.status='2') AND tvc.complaint_source = '2'";

		

		if($dataType ='pending'){

	      $where = "(tvc.status = '0' || tvc.status='2') AND tvc.complaint_source = '2'";

	    }else{

	         $where = "tvc.status = '1' AND tvc.complaint_source = '2'";

	    }

		    

		    

		

		$this->db->where($where);

		$this->db->order_by('tvc.id','desc');

		$query=$this->db->get();

		$data = array();

		if($query !== FALSE && $query->num_rows() > 0){

   			 $data = $query->result_array();

			}

			

		$datas = array();

		foreach($data as $keys => $values){

		    $datas[] = $values;

		    $datas[$keys]['source'] = "Call";

		}

	

		return $datas;

    

	







}



function getOtherComplaintsCalls(){



	$this->db->select('CONCAT(cu.firstname," ",cu.lastname) as username,coc.fk_disposition_id,coc.source_type,coc.id,coc.user_id,coctm.descriptions as complaint_topic,coc.problem_description as description,coc.fk_disposition_id as status,coc.created_date');

	$this->db->from('ci_other_complaints coc');

	$this->db->join('ci_other_complaint_type_master coctm','coc.complaint_type_id=coctm.id','left');

	$this->db->join('ci_users cu','coc.user_id=cu.id','left');

	$where6 = "coc.fk_disposition_id='1' AND coc.fk_tbl_complaint_id = 0 AND coc.source_type='0'";

	$this->db->where($where6);





		 $query2 = $this->db->get();

		 $data2 = array();

		if($query2 !== FALSE && $query2->num_rows() > 0){

   			 $data2 = $query2->result_array();

			}

		

	

    	$datas = array();

		foreach($data2 as $keys =>$values){

		    

		    $datas[] = $values;

		    $datas[$keys]['created_date']= date('d-m-Y H:i A', strtotime($values['created_date']));

		    $datas[$keys]['actions']=""; 

		}

	

	   

		return $datas;





}



function AddSlotsComplaints($booking_id){

         $this->db->select('id as fk_complaint_id,place_id,slot_id,verifier_id,complaint_text,booking_id');
         $this->db->from('tbl_verifier_complaints');
		 $this->db->where('booking_id',$booking_id);
		 $query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
	    $slots_complaint = $data[0]; 
	    $AddSlotsComplaints = $this->db->insert('ci_slots_complaints',$slots_complaint);
	  
	    return true;

}



 public function getBlockedSlots(){



         $this->db->select('cs.name as country_name, cs.name as state,city.name as cityname,
         cpp.placename,cpp.place_address,cpsi.slot_name,cpsi.display_id,
         csc.complaint_text,csc.complaint_source,csc.issue_raised_on,csc.complaint_status,csc.slot_id');
         $this->db->from('ci_slots_complaints csc');
		 $this->db->join('ci_parking_slot_info cpsi','csc.slot_id = cpsi.slot_no','left');
		 $this->db->join('ci_parking_places cpp','cpsi.place_id = cpp.id','left');
		 $this->db->join('ci_countries cc','cpp.fk_country_id = cc.id','left');
		 $this->db->join('ci_states cs','cpp.fk_state_id = cs.id','left');
		 $this->db->join('ci_cities  city','cpp.city_id = city.id','left');
		 $where = "csc.complaint_status = 0 OR csc.complaint_status = 1";
         $this->db->where($where);
         $this->db->group_by('csc.slot_id');
         $this->db->order_by('csc.id','desc');
		 $query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		return $data;

 }



public function UpdateSlotsStatus(){

    $slot_id = $_POST['id'];

    $this->db->where('slot_id', $slot_id);

    $update_complaints = $this->db->update('ci_slots_complaints', array('complaint_status'=>'2'));

	if($update_complaints){

	    $this->db->where('slot_no', $slot_id);

	    $this->db->update('ci_parking_slot_info', array('isBlocked'=>'0'));

	    

	}

	return true;

}


 public function getSlotsInfoById($id){


         $this->db->select('cs.name as country_name, cs.name as state,city.name as cityname,
         cpp.placename,cpp.place_address,cpsi.slot_name,cpsi.display_id,
         csc.complaint_text,csc.complaint_source,csc.issue_raised_on,csc.complaint_status,csc.slot_id,csc.booking_id,csc.id as complaint_id,csc.place_id');
         $this->db->from('ci_slots_complaints csc');
		 $this->db->join('ci_parking_slot_info cpsi','csc.slot_id = cpsi.slot_no','left');
		 $this->db->join('ci_parking_places cpp','cpsi.place_id = cpp.id','left');
		 $this->db->join('ci_countries cc','cpp.fk_country_id = cc.id','left');
		 $this->db->join('ci_states cs','cpp.fk_state_id = cs.id','left');
		 $this->db->join('ci_cities  city','cpp.city_id = city.id','left');
		 if($id=='0' && $id!='P'){
		 	$where = "csc.is_verified='1' && (csc.complaint_status='0' || csc.complaint_status='1')";
		 }else if($id!='0' && $id=='P'){
			$where = "csc.is_verified='0' && (csc.complaint_status='0' || csc.complaint_status='1')";
		 }else{
		 	$where = "csc.is_verified='0' && (csc.complaint_status='0' || csc.complaint_status='1')  AND csc.booking_id=$id ";
		 }
		 $this->db->where($where);
         $this->db->group_by('csc.slot_id');
         $this->db->order_by('csc.id','desc');
		 $query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}

		$datas = array();
		foreach($data as $keys=> $values){

			$datas[] = $values;
			$count = $this->getAffectedBookings($values['complaint_id'],$values['slot_id']);
			$datas[$keys]['affected_booking'] = count($count);
		}
		return $datas;
 }


  public function GetDataForSlotsVerification($id){
         $this->db->select('cb.unique_booking_id,CONCAT(cb.booking_from_date," ", cb.from_time) as booking_from,
         					CONCAT(cb.booking_to_date," ", cb.to_time) as booking_to,cpp.placename,cpp.id as place_id,cpp.place_address,
         					country.name as country, cs.name as state, city.name as city,cpp.pincode,tv.name as vendore_name,
							tv.mobileno as vcontact,cpsi.slot_name,cpsi.display_id,tsl.device_id,csc.issue_raised_on,csc.complaint_source,csc.img_attachments,
							CONCAT(ca.firstname," ",ca.lastname) as verifier_name,csc.img_attachments,csc.slot_id as fk_slot_id,csc.id as fk_complaint_id');
         $this->db->from('ci_slots_complaints csc');
         $this->db->join('ci_admin ca','csc.verifier_id = ca.admin_id','left');
		 $this->db->join('ci_booking  cb','csc.booking_id = cb.id','left');
		 $this->db->join('ci_parking_places cpp','csc.place_id = cpp.id','left');
		 $this->db->join('ci_countries country','cpp.fk_country_id = country.id','left');
		 $this->db->join('ci_states cs','cpp.fk_state_id = cs.id','left');
		 $this->db->join('ci_cities city','cpp.city_id = city.id','left');
		 $this->db->join('tbl_vendor tv','cpp.vendor_id = tv.id','left');
		 $this->db->join('ci_parking_slot_info  cpsi','csc.slot_id = cpsi.slot_no','left');
		 $this->db->join('tbl_sensor_list tsl','cpsi.machine_id = tsl.id','left');
		 $where = "csc.id=$id";
         $this->db->where($where);
		 $query = $this->db->get();
		 $datas = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $datas= $query->result_array();
			}	
		$verifiers = $this->getPlaceVerifiers($datas[0]['place_id']);
		$enforcers = $this->getPlaceEnforcers($datas[0]['place_id']);
		$engineers = $this->getEngineers();
		$data['info'] = $datas[0];
		$data['verifiers'] = $verifiers;
		$data['enforcers'] = $enforcers;
		$data['engineers'] = $engineers;

		return $data;


 }


public function getPlaceVerifiers($id){

	     $this->db->select('ca.mobile_no as contact,CONCAT(ca.firstname," ",ca.lastname) as verifier_name');
         $this->db->from('tbl_verifier_place tvp');
		 $this->db->join('ci_admin ca','tvp.verifier_id = ca.admin_id','left');
         $this->db->where('tvp.place_id',$id);
         $this->db->where('ca.mobile_no!=','');
         $this->db->where('ca.admin_role_id','3');
         $this->db->group_by('1');
         $this->db->order_by('1');
		 $query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		return $data;
}


public function getPlaceEnforcers($id){

	     $this->db->select('ca.mobile_no as contact,CONCAT(ca.firstname," ",ca.lastname) as enforcer_name');
         $this->db->from('tbl_enforcer_place tvp');
		 $this->db->join('ci_admin ca','tvp.enforcer_id = ca.admin_id','left');
         $this->db->where('tvp.place_id',$id);
         $this->db->where('ca.mobile_no!=','');
         $this->db->group_by('1');
         $this->db->order_by('1');
		 $query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		return $data;
}

public function getEngineers(){


	     $this->db->select('ca.admin_id,ca.mobile_no as contact,CONCAT(ca.firstname," ",ca.lastname) as engineer_name');
         $this->db->from('ci_admin ca');
         $this->db->where('ca.admin_role_id','6');
         $this->db->where('ca.is_active','1');
         $this->db->order_by('1');
		 $query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		return $data;


}

 public function ForwardComplaints($data){


 	unset($data['submit']);
 	$data['cc_assigned_by'] = $this->session->userdata('admin_id');
	$fwdcomplaint = $this->db->insert('ci_engineers_complaints', $data);

	if($fwdcomplaint){
		$this->db->where('slot_no', $data['fk_slot_id']);
		$block = $this->db->update('ci_parking_slot_info', array('isBlocked'=>0));
		if($block){
			$this->db->where('id', $data['fk_complaint_id']);
		    $block = $this->db->update('ci_slots_complaints', array('engineer_id'=>$data['fk_eng_id'],'no_of_hrs'=>$data['estimated_hrs'],'complaint_status'=>1,'is_verified'=>1));
		}
	}
	return true;
}



  public function getAffectedBookings($complaint_id,$slot_id)
    {
	            $slotIssue_id = $complaint_id;
	            $slot_id = $slot_id;
	            $getSlotIssueData = $this->db->select('*')->from('ci_slots_complaints')->where('id',$slotIssue_id)->where('complaint_status!=','2')
	            ->get()->result_array();
	            if(count($getSlotIssueData)>0){
    	            $bookingList = $this->db->select('*')->from('ci_booking')->where('slot_id',$slot_id)
    	            ->where('booking_status','0')
    	            ->get()->result_array();
    	            $issue_raised_on =$getSlotIssueData[0]['issue_raised_on'];
    	           $noofHrs=$getSlotIssueData[0]['no_of_hrs'];
    	       
                             $expected_issueEnd =  date('Y-m-d H:i:s',strtotime($issue_raised_on . ' +'.$noofHrs.' hours'));
                             $from_date = date('Y-m-d',strtotime($issue_raised_on));
                             $from_time = date('H:i:s',strtotime($issue_raised_on));
                             $to_date = date('Y-m-d',strtotime($expected_issueEnd));
                             $to_time = date('H:i:s',strtotime($expected_issueEnd));
                             $listofAffectedBookings = $this->booking_detection($bookingList,$from_date,$to_date,$from_time,$to_time);
                             return $listofAffectedBookings;
	        
    }
}


    public function UnblockSlots($id){

         $this->db->from('ci_slots_complaints');
         $this->db->where('id', $id );
         $this->db->where('is_deleted','0');
		 $query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		$complaint_data = $data[0];
		if(!empty($complaint_data)){

			$update_data = array('complaint_status'=>'2','issue_resolved_on'=>date('Y-m-d : h:m'));
			$this->db->where('id', $id);
		    $update_compl = $this->db->update('ci_slots_complaints', $update_data);
		    if($update_compl){
		    	$this->db->where('slot_no', $complaint_data['slot_id']);
		    	$this->db->where('place_id', $complaint_data['place_id']);
		        $update_compl = $this->db->update('ci_parking_slot_info', array('isBlocked'=>'1'));
		    }

		}
		return $data;
    }

    public function getVerificationRequestById($place_id){

    	$verifiers = $this->getPlaceVerifiers($place_id);
		$enforcers = $this->getPlaceEnforcers($place_id);
		$engineers = $this->getEngineers();
		$data['verifiers'] = $verifiers;
		$data['enforcers'] = $enforcers;
		$data['engineers'] = $engineers;
		return $data;
    }

    public function getPendingSlotsInfo($id){

    	$this->db->select('cpp.placename,cpp.id as place_id,cpp.place_address,
         					country.name as country, cs.name as state, city.name as city,cpp.pincode,tv.name as vendore_name,
							tv.mobileno as vcontact,cpsi.slot_name,cpsi.display_id,tsl.device_id,csc.issue_raised_on,csc.complaint_source,csc.img_attachments,
							CONCAT(ca.firstname," ",ca.lastname) as verifier_name,csc.img_attachments,csc.slot_id as fk_slot_id,csc.id as fk_complaint_id,csc.img_attachments');
         $this->db->from('ci_slots_complaints csc');
         $this->db->join('ci_admin ca','csc.verifier_id = ca.admin_id','left');
		 $this->db->join('ci_parking_places cpp','csc.place_id = cpp.id','left');
		 $this->db->join('ci_countries country','cpp.fk_country_id = country.id','left');
		 $this->db->join('ci_states cs','cpp.fk_state_id = cs.id','left');
		 $this->db->join('ci_cities city','cpp.city_id = city.id','left');
		 $this->db->join('tbl_vendor tv','cpp.vendor_id = tv.id','left');
		 $this->db->join('ci_parking_slot_info  cpsi','csc.slot_id = cpsi.slot_no','left');
		 $this->db->join('tbl_sensor_list tsl','cpsi.machine_id = tsl.id','left');
		 $where = "csc.id=$id";
         $this->db->where($where);
		 $query = $this->db->get();
		 $datas = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $datas= $query->result_array();
			}	

		return $datas[0];

    }

    public function booking_detection($data,$from_date,$to_date,$from_time,$to_time) //slot availibility // in progress mpc_sensor
    {
         date_default_timezone_set('Asia/Kolkata');
            $multiDate = false;
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
                                            array_push($listof_bookedSlots,$v);
                                    }
                                }
                                else
                                { 
                                     $fromDate_s = date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v['reserve_from_time']));
                                     $toDate_s =date('Y-m-d H:i:s', strtotime($from_date . ' ' . $v['reserve_to_time']));
                                     if ($fromDate_u <= $fromDate_s && $toDate_u >= $fromDate_s || $fromDate_u <= $toDate_s && $toDate_u >= $toDate_s
                                     ||$fromDate_u<=$fromDate_s&&$toDate_u>=$toDate_s||$fromDate_s<=$fromDate_u&&$toDate_s>=$toDate_u) 
                                     {
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
                                        array_push($listof_bookedSlots,$v);
                                    }
                                }
                                
                            }
                        }
                    
                    }
                    return $listof_bookedSlots;
        
    }

    public function getClosedSlotsComplaints(){
    	$this->db->select('cpp.placename,cpp.id as place_id,cpp.place_address,
         					country.name as country, cs.name as state, city.name as city,cpp.pincode,tv.name as vendore_name,
							tv.mobileno as vcontact,cpsi.slot_name,cpsi.display_id,tsl.device_id,csc.issue_raised_on,csc.complaint_source,csc.img_attachments,
							CONCAT(ca.firstname," ",ca.lastname) as verifier_name,csc.img_attachments,csc.slot_id as fk_slot_id,csc.id as fk_complaint_id,csc.img_attachments,csc.issue_resolved_on');
         $this->db->from('ci_slots_complaints csc');
         $this->db->join('ci_admin ca','csc.verifier_id = ca.admin_id','left');
		 $this->db->join('ci_parking_places cpp','csc.place_id = cpp.id','left');
		 $this->db->join('ci_countries country','cpp.fk_country_id = country.id','left');
		 $this->db->join('ci_states cs','cpp.fk_state_id = cs.id','left');
		 $this->db->join('ci_cities city','cpp.city_id = city.id','left');
		 $this->db->join('tbl_vendor tv','cpp.vendor_id = tv.id','left');
		 $this->db->join('ci_parking_slot_info  cpsi','csc.slot_id = cpsi.slot_no','left');
		 $this->db->join('tbl_sensor_list tsl','cpsi.machine_id = tsl.id','left');
		 $where = "csc.complaint_status='2'";
         $this->db->where($where);
		 $query = $this->db->get();
		 $datas = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $datas= $query->result_array();
			}	

		return $datas;

    }



    


 


}



?>