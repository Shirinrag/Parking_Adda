<?php



class Reports_model extends CI_Model
{



	public function getPlaces()
	{

		$this->db->select('cpp.*');

		$this->db->from('ci_parking_places cpp');

		$this->db->where('cpp.place_status', '1');

		$this->db->where('cpp.is_deleted', '0');

		$this->db->order_by('cpp.placename');

		$query = $this->db->get();

		$data = array();

		if ($query !== FALSE && $query->num_rows() > 0) {

			$data = $query->result_array();
		}

		return $data;
	}





	public function getUsers()
	{

		$sql1 = "SELECT id, CONCAT(firstname, ' ' , lastname) as fullname,mobile_no FROM `ci_users` WHERE is_active = '1'";

		$query = $this->db->query($sql1);

		return $data = $query->result_array();
	}

	public function getBookingInfo($data)
	{



		if ($data['place_id'] != 'all') {

			$place_ids = $data['place_id'];

			$place_id = "cb.place_id=$place_ids";
		} else {

			$place_id = "cb.place_id!=''";
		}

		if ($data['type'] != "all") {

			$types = $data['type'];

			$type = "cb.booking_status='$types'";
		} else {

			$type = "cb.booking_status!=''";
		}

		$from_date = $data['from_date'];

		$to_date = date('Y-m-d', strtotime($data['to_date']));

		$sql = "SELECT cu.id as userid,`cb`.`unique_booking_id`, `cu`.`firstname`, `cu`.`lastname`, `cpsi`.`display_id` as `slotinfo`, `cpp`.`placename`,

				     `cpp`.`place_address`, `cb`.`booking_from_date`, `cb`.`from_time`, `cb`.`booking_to_date`, `cb`.`to_time`, `cb`.`cost`,

				     `cb`.`originalCost`, `cb`.`booking_status`, `cb`.`created_date` FROM `ci_booking` `cb` 

				     LEFT JOIN `ci_users` `cu` ON `cb`.`user_id` = `cu`.`id` LEFT JOIN `ci_parking_places` `cpp` ON `cb`.`place_id` = `cpp`.`id` 

				     LEFT JOIN `ci_parking_slot_info` `cpsi` ON `cb`.`slot_id` = `cpsi`.`slot_no` 

				     WHERE `cb`.`booking_from_date` BETWEEN '$from_date' AND '$to_date' AND $type AND $place_id";

		$query = $this->db->query($sql);

		$data = array();

		if ($query !== FALSE && $query->num_rows() > 0) {

			$data = $query->result_array();
		}





		return $data;
	}



	public function getDownloadInfo($data)
	{

		$from_date = $data['from_date'];

		$to_date = date('Y-m-d', strtotime($data['to_date'] . ' +1 day'));

		$sql1 = "SELECT COUNT(id) as total_download FROM `ci_users` WHERE created_at BETWEEN '$from_date' AND '$to_date'";

		$query = $this->db->query($sql1);

		return $data = $query->result_array();
	}



	public function getTxnInfo($data)
	{

		$userid = $data['userid'];

		$from_date = $data['from_date'];
		
		$to_date =  date('Y-m-d', strtotime($data['to_date'] . ' +1 day'));




		if ($from_date != "" && $to_date != "") {

			$where = "AND cth.on_created BETWEEN '$from_date' AND  '$to_date'";
		}

		if ($userid != 'all') {

			$where = $where . "AND cu.id='$userid'";
		}

		$sql1 = "SELECT cu.id as userid,cu.firstname,cu.lastname,cu.email,cu.mobile_no,cth.order_id,cth.amount,cth.payment_id,cth.on_created FROM `ci_transaction_history` as cth 

					left join ci_users as cu on cth.user_id = cu.id

					WHERE cth.status = '1' $where"; 

		$query = $this->db->query($sql1);

		return $data = $query->result_array();
	}



	public function getSummaryData($data)
	{

		$from_date = $data['from_date'];

		$to_date = $data['to_date'];

		$sql1 = "SELECT COUNT(id) as ticket_sales,CAST(created_date as date) as created_date,SUM(cost) as cost,concat(booking_from_date,' ',from_time) as from_date,concat(booking_to_date,' ',to_time) as to_date FROM `ci_booking` WHERE (booking_status = 0 OR booking_status=1) AND CAST(created_date as date) BETWEEN '$from_date' AND '$to_date' GROUP BY CAST(created_date as date)";

		$query = $this->db->query($sql1);

		return $data = $query->result_array();
	}



	public function getUsersData($data)
	{

		$from_date = $data['from_date'];
		$to_date = $data['to_date'];
		$sql = "SELECT id,CONCAT(firstname,' ',lastname ) as fullname,email,device_type,mobile_no,created_at as registartion_date FROM `ci_users` as cu 
                        WHERE CAST(created_at as date) BETWEEN '$from_date' AND '$to_date' ORDER BY created_at";
		$query = $this->db->query($sql);
		$user_info = $query->result_array();

		if (!empty($user_info)) {
			foreach ($user_info as $key => $info) {
				$user_id = $info['id'];
				$sql1 = "SELECT * FROM `ci_car_details` WHERE user_id = $user_id LIMIT 1";
				$query1 = $this->db->query($sql1);
				$car_info = $query1->row_array();

				$final[] = $info;
				$final[$key]['reg'] = date("d-m-Y H:i A", strtotime($info['registartion_date']));

				if (!empty($car_info['car_number'])) {
					$final[$key]['car_number'] = $car_info['car_number'];
				} else {
					$final[$key]['car_number'] = "<center><i class='fa fa-times' aria-hidden='true' style='color:red'></i></center>";
				}
			}
		} else {
			$final = [];
		}
        
      

		return $final;
	}



	public function getBookingHrs($date)
	{

		$sql1 = "SELECT *  FROM `ci_booking` WHERE (booking_status = 0 OR booking_status=1) AND booking_from_date = '$date'";

		$query = $this->db->query($sql1);

		$data = $query->result_array();

		$hourss  = "0";

		$fdata = array();

		foreach ($data as $key => $values) {

			$date1 = strtotime($values['booking_from_date'] . " " . $values['from_time']);

			$date2 = strtotime($values['booking_to_date'] . " " . $values['to_time']);

			$diff = abs($date2 - $date1);

			$years = floor($diff / (365 * 60 * 60 * 24));

			$months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));

			$days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

			$hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));

			$minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);

			$seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));

			$hourss = $hourss + $hours;
		}

		return $hourss;
	}







	public function getDownloadsByDate($date)
	{

		$sql1 = "SELECT COUNT(id) as total_download FROM `ci_users` WHERE CAST(created_at as DATE) ='$date'";

		$query = $this->db->query($sql1);

		return $data = $query->result_array()[0]['total_download'];
	}



	public function getWalletHistory($date)
	{



		$sql = "SELECT COUNT(id) as total_wallet_recharge,CAST(onCreated as date) as txn_date,SUM(amount) as recharge_amount

				 FROM `ci_wallet_history` WHERE status = 1 AND CAST(onCreated as date) ='$date' AND payment_type = 1";

		$query = $this->db->query($sql);

		return $data = $query->result_array();
	}



	public function getData()
	{



		$sql =  "SELECT COUNT(id) as total_booking,user_id FROM `ci_booking` WHERE booking_status = 0 || booking_status=1 AND is_deleted = 0  GROUP by user_id";

		$query = $this->db->query($sql);

		$data = $query->result_array();



		foreach ($data as $key => $values) {

			if (($values['total_booking']) > 1) {

				$va[] =  $values['user_id'];
			}
		}

		$List = implode(', ', $va);



		$sql1 =  "SELECT COUNT(id) as total_booking,user_id FROM `ci_booking` WHERE booking_status = 0 || booking_status=1 AND is_deleted = 0  GROUP by user_id";

		$query1 = $this->db->query($sql);

		$data1 = $query->result_array();
	}

	public function getUsersWalletInfo($data){
	  
	  
	
	$from_date = $data['from_date'];
	$to_date =  date('Y-m-d', strtotime($data['to_date'] . ' +1 day'));
	$this->db->select("CONCAT(cu.firstname,' ',cu.lastname) as username,cwu.*");
	$this->db->from('ci_wallet_user cwu');
	$this->db->join('ci_users cu','cwu.user_id=cu.id','left');
	$where = "cwu.onCreated BETWEEN '$from_date' AND '$to_date' AND  cwu.is_deleted=0";
	$this->db->where($where);
	$query = $this->db->get();
    $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
		}
		
        return $data;
	}
	
	

	public function getWalletData($data){
	    
	$from_date = $data['from_date'];
	$to_date =  date('Y-m-d', strtotime($data['to_date'] . ' +1 day'));

	$this->db->select("CONCAT(cu.firstname,' ',cu.lastname) as username,cwh.*");
	$this->db->from('ci_wallet_history cwh');
	$this->db->join('ci_users cu','cwh.user_id=cu.id','left');
	$where = "cwh.onCreated BETWEEN '$from_date' AND '$to_date' AND  cu.is_active=1 AND cwh.payment_type=0";
	$this->db->where($where);
	$query = $this->db->get();
    $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
		}
        return $data;
	}


	public function getInternalUsersData($user_id){
	$this->db->from('ci_internal_users ciu');
	$where = "ciu.user_id=$user_id";
	$this->db->where($where);
	$query = $this->db->get();
    $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
		}
    return $data;
	}
}
