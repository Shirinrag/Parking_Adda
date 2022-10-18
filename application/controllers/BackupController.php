<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set("memory_limit", "-1");
class BackupController extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }
    function database_backup() {
        $this->load->dbutil();
        $prefs = array(
	        'tables' => array('mpc_sensor'), // Array of tables to backup.
	        'ignore' => array(), // List of tables to omit from the backup
	        'format' => 'zip', // gzip, zip, txt
	        'filename' => 'mpc_sensor_' . date('Y-m-d_H-i'), // File name - NEEDED ONLY WITH ZIP FILES
	        'add_drop' => TRUE, // Whether to add DROP TABLE statements to backup file
	        'add_insert' => TRUE, // Whether to add INSERT data to backup file
	        'newline' => "\n"
	        // Newline character used in backup file
        );
        $backup = $this->dbutil->backup($prefs);
        if (!write_file('./uploads/backup/mpc_sensor_' . date('Y-m-d_H-i') . '.zip', $backup)) {
        //     echo "Error while creating auto database backup!";
        } else {
            echo "Database backup has been successfully Created";
            $today_date = date('Y-m-d');
            $this->load->model('model');
            $from_date = $today_date ." 00:00:00";
            $to_date = $today_date ." 23:59:00";
           $data = $this->model->selectWhereData('mpc_sensor', array('created_date'< $from_date), array('*'), false);
            // echo '<pre>'; print_r($data); exit;
            foreach ($data as $data_key => $data_row) {
            		$this->db->where('created_date <',$from_date);
              		$this->db->delete('mpc_sensor');
            }
        }
    }
}
