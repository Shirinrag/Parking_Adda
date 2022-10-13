<?php defined('BASEPATH') or exit('No direct script access allowed');
class Reports extends MY_Controller {
    public function __construct() {
        parent::__construct();
        auth_check(); // check login auth
        $this->rbac->check_module_access();
        $this->load->model('admin/reports_model', 'reports_model');
    }
    public function booking_reports() {
        $data['placename'] = $this->reports_model->getPlaces();
        $this->load->view('admin/includes/_header');
        $this->load->view('admin/reports/booking_reports', $data);
        $this->load->view('admin/includes/_footer');
    }
    public function transactions_reports() {
        $data['users'] = $this->reports_model->getUsers();
        $this->load->view('admin/includes/_header');
        $this->load->view('admin/reports/txn_reports', $data);
        $this->load->view('admin/includes/_footer');
    }
    public function summary_report() {
        $this->load->view('admin/includes/_header');
        $this->load->view('admin/reports/summary_reports');
        $this->load->view('admin/includes/_footer');
    }
    public function user_reports() {
        $this->load->view('admin/includes/_header');
        $this->load->view('admin/reports/users');
        $this->load->view('admin/includes/_footer');
    }
    public function wallet_reports() {
        $this->load->view('admin/includes/_header');
        $this->load->view('admin/reports/wallet_reports');
        $this->load->view('admin/includes/_footer');
    }
    public function bonus_reports() {
        $this->load->view('admin/includes/_header');
        $this->load->view('admin/reports/bonus_reports');
        $this->load->view('admin/includes/_footer');
    }
    public function getBookingData() {
        $bookingInfos = $this->reports_model->getBookingInfo($this->input->post());
        $DownloadInfo = $this->reports_model->getDownloadInfo($this->input->post());
        $ticketSales = "0";
        $earnings = "0";
        $total_hrs = "0";
        if (!empty($bookingInfos)) {
            foreach ($bookingInfos as $key => $value) {
                //$status = $this->checkUserisInternal($value['userid']);
                // if($status=='false'){
                $bookinfInfo[$key]['id'] = $key + 1;
                $bookinfInfo[$key]['userid'] = $value['userid'];
                $bookinfInfo[$key]['bookingId'] = $value['unique_booking_id'];
                $bookinfInfo[$key]['username'] = $value['firstname'] . " " . $value['lastname'];
                $bookinfInfo[$key]['slotinfo'] = $value['slotinfo'];
                $bookinfInfo[$key]['place_address'] = $value['placename'];
                $bookinfInfo[$key]['from_date'] = date("d-m-Y H:i A", strtotime($value['booking_from_date'] . " " . $value['from_time']));
                $bookinfInfo[$key]['to_date'] = date("d-m-Y H:i A", strtotime($value['booking_to_date'] . " " . $value['to_time']));
                $bookinfInfo[$key]['cost'] = $value['cost'];
                $bookinfInfo[$key]['created_date'] = date("d-m-Y H:i A", strtotime($value['created_date']));
                if ($value['booking_status'] == 0) {
                    $bookinfInfo[$key]['status'] = "Onprocess";
                } else if ($value['booking_status'] == 1) {
                    $bookinfInfo[$key]['status'] = "Completed";
                } else if ($value['booking_status'] == 2) {
                    $bookinfInfo[$key]['status'] = "Cancelled";
                } else {
                    $bookinfInfo[$key]['status'] = "Replaced";
                }
                $date1 = strtotime($value['booking_from_date'] . " " . $value['from_time']);
                $date2 = strtotime($value['booking_to_date'] . " " . $value['to_time']);
                $diff = abs($date2 - $date1);
                $years = floor($diff / (365 * 60 * 60 * 24));
                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
                $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
                $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));
                $hours = ($days * 24) + $hours;
                $bookinfInfo[$key]['booking_hrs'] = $hours . ":" . $minutes . ":" . $seconds;
                $data['bookingInfo'] = $bookinfInfo;
                if ($value['booking_status'] == '0' || $value['booking_status'] == 1) {
                    $ticketSales = ++$ticketSales;
                    $earnings+= $value['cost'];
                    $total_hrs+= $hours;
                }
                //	}
                
            }
        } else {
            $data['bookingInfo'] = array();
        }
        $data['ticketSales'] = $ticketSales;
        $data['earnings'] = $earnings;
        $data['total_hrs'] = $total_hrs;
        $data['total_downloads'] = $DownloadInfo[0]['total_download'];
        foreach ($data['bookingInfo'] as $keys => $val) {
            $user_id = $val['userid'];
            $status = $this->checkUserisInternal($user_id);
            if ($status == 'false' && $val['place_address'] != 'BDS Services Private Limited') {
                $datas[] = $val;
            }
        }
        unset($data['bookingInfo']);
        $data['bookingInfo'] = $datas;
        echo json_encode($data);
    }
    public function getTransactionsData() {
        if (!empty($this->input->post())) {
            $bookingInfos = $this->reports_model->getTxnInfo($this->input->post());
            $earnings = "0";
            $TxnInfo = array();
            foreach ($bookingInfos as $key => $value) {
                $status = $this->checkUserisInternal($value['userid']);
                if ($status == 'false') {
                    $TxnInfo[$key]['id'] = $key + 1;
                    $TxnInfo[$key]['userid'] = $value['userid'];
                    $TxnInfo[$key]['username'] = $value['firstname'] . " " . $value['lastname'];
                    $TxnInfo[$key]['email'] = $value['email'];
                    $TxnInfo[$key]['contact'] = $value['mobile_no'];
                    $TxnInfo[$key]['amount'] = $value['amount'];
                    $TxnInfo[$key]['order_id'] = $value['order_id'];
                    $TxnInfo[$key]['payment_id'] = $value['payment_id'];
                    $TxnInfo[$key]['on_created'] = date("d-m-Y H:i A", strtotime($value['on_created']));
                    $earnings+= ($value['amount']);
                }
            }
            $data['TxnInfo'] = array_values($TxnInfo);
            $data['total_txn'] = count($bookingInfos);
            $data['txn_amount'] = round($earnings);
            echo json_encode($data);
        }
    }
    public function getSummaryData() {
        if (!empty($this->input->post())) {
            $summaryData = $this->reports_model->getSummaryData($this->input->post());
            $ticket_sales = "0";
            foreach ($summaryData as $key => $value) {
                $sdata[$key]['id'] = $key + 1;
                $sdata[$key]['booking_count'] = $value['ticket_sales'];
                $sdata[$key]['date'] = date("d-m-Y ", strtotime($value['created_date']));
                $sdata[$key]['downloads'] = $this->reports_model->getDownloadsByDate($value['created_date']);
                $sdata[$key]['amount'] = $value['cost'];
                $sdata[$key]['total_booking_hrs'] = $this->reports_model->getBookingHrs($value['created_date']);
                $sdata[$key]['wallet_recharge_count'] = $this->reports_model->getWalletHistory($value['created_date']) [0]['total_wallet_recharge'];
                if ($this->reports_model->getWalletHistory($value['created_date']) [0]['recharge_amount'] != "") {
                    $sdata[$key]['wallet_recharge_amount'] = $this->reports_model->getWalletHistory($value['created_date']) [0]['recharge_amount'];
                } else {
                    $sdata[$key]['wallet_recharge_amount'] = 0;
                }
            }
            $data['summary'] = $sdata;
            echo json_encode($data);
        }
    }
    function getUsersDatas() {
        if (!empty($this->input->post())) {
            $UsersData = $this->reports_model->getUsersData($this->input->post());
            foreach ($UsersData as $k => $val) {
                $status = $this->checkUserisInternal($val['id']);
                if ($status == "false") {
                    $UsersDatas[] = $val;
                }
            }
            $data['summary'] = $UsersDatas;
            echo json_encode($data);
        }
    }
    function getBookingHours($date1, $date2) {
        $date1 = strtotime($date1);
        $date2 = strtotime($date2);
        $diff = abs($date2 - $date1);
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
        $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));
        return $hours;
    }
    function getMultiAreasUsers() {
        $Data = $this->reports_model->getData();
    }
    function getWalletInfo() {
        $walletData = $this->reports_model->getWalletData($this->input->post());
        $earnings = 0;
        $wdata = [];
        foreach ($walletData as $key => $value) {
            $status = $this->checkUserisInternal($value['user_id']);
            if ($status == "false") {
                $wdata[$key]['id'] = $key + 1;
                $wdata[$key]['user_id'] = $value['user_id'];
                $wdata[$key]['username'] = $value['username'];
                $wdata[$key]['amount'] = $value['amount'];
                $wdata[$key]['onCreated'] = date("d-m-Y H:i A", strtotime($value['onCreated']));
                $earnings+= ($value['amount']);
            }
        }
        echo json_encode(array_values($wdata));
    }
    function getUserWalletData() {
        if (!empty($this->input->post())) {
            $userWalletData = $this->reports_model->getUsersWalletInfo($this->input->post());
            $uwData = [];
            foreach ($userWalletData as $key => $value) {
                $status = $this->checkUserisInternal($value['user_id']);
                if ($status == "false") {
                    $uwData[$key]['id'] = $key + 1;
                    $uwData[$key]['user_id'] = $value['user_id'];
                    $uwData[$key]['username'] = $value['username'];
                    $uwData[$key]['amount'] = $value['amount'];
                    $uwData[$key]['onCreated'] = date("d-m-Y H:i A", strtotime($value['onCreated']));
                }
            }
            echo json_encode(array_values($uwData));
        }
    }
    function checkUserisInternal($user_id) {
        $internal_users = $this->reports_model->getInternalUsersData($user_id);
        if (count($internal_users) > 0) {
            $status = "true";
        } else {
            $status = "false";
        }
        return $status;
    }
    function getWalletData() {
        if (!empty($this->input->post())) {
            $walletData = $this->reports_model->getWalletData($this->input->post());
            $data['walletdatas'] = $wdata;
            $data['counts'] = count($wdata);
            echo json_encode($data);
        }
    }
}
