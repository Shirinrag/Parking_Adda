<?php
	class Ground_model extends CI_Model{

		public function UpdateSlotsInfo($id,$data){

		$this->db->where('id', $id);
		$this->db->update('ci_parking_places', $data);
		return true;

		}
		
		
		public function getGroundData(){
            
            // Stage Id 1 Is For Check the Place for Engineering Team 
            
		 $this->db->select('ms.stages as stage_name,ms.id as stage_id,tv.name as vendor_name,cc.name as country_name,cs.name as state_name,city.name as city_name,cpp.*');
		 $this->db->from('ci_parking_places cpp');
		 $this->db->join('tbl_vendor tv','cpp.vendor_id = tv.id','left');
		 $this->db->join('ci_countries cc','cpp.fk_country_id=cc.id','left');
		 $this->db->join('ci_states cs','cpp.fk_state_id=cs.id','left');
		 $this->db->join('ci_cities city','city on cpp.city_id = city.id','left');
		 $this->db->join('tbl_places_stages tps','tps on cpp.id = tps.place_id','left');
		 $this->db->join('master_stages ms','ms on tps.stages_id = ms.id','left');
		 $this->db->where('tps.stages_id >=', '1');
		 $this->db->order_by('cpp.id', 'desc');



		$query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		return $data;
		}
		
		


		// Function For Unique Slots Number Genrations 
	public function uniqueSlotName($variable){

         $var1 = explode('-',$variable);
        if($var1[1]=='ZZ999'){
            print("Sorry we cannot go beyond this ");
        }
        else if($var1[1][2]==9&&$var1[1][3]==9&&$var1[1][4]==9){
            
            if($var1[1][1]=='Z'){
                $var1[1][0]=chr(ord($var1[1][0])+1);
                $var1[1][1]='A';
                $var1[1][2]=0;
                $var1[1][3]=0;
                $var1[1][4]=1;
            }else{
                $var1[1][1]=chr(ord($var1[1][1])+1);
                $var1[1][2]=0;
                $var1[1][3]=0;
                $var1[1][4]=1;
            }
            
        }
        else if($var1[1][3]==9&&$var1[1][4]==9){
            $var1[1][2]=$var1[1][2]+1;
            $var1[1][3]=0;
            $var1[1][4]=0;
        }
         else{
            if($var1[1][4]==9){
            $var1[1][3]=$var1[1][3]+1;
            $var1[1][4]=0;    
            }else{
                $var1[1][3]=$var1[1][3];
            $var1[1][4]=$var1[1][4]+1;
            }
            
            
        }
        
        return $var1[1];
    }

    function getLastSlotName(){

    	$this->db->select('slot_name');		
    	$this->db->from('ci_parking_slot_info');
		$this->db->where('is_deleted',0);
		$this->db->order_by('slot_no', 'desc');
		$this->db->limit(1);  
		$query=$this->db->get();
		return $query->row_array();
    }

    function getSlotInfo($prfix){
    	$this->db->select('count(slot_no) as total');		
    	$this->db->from('ci_parking_slot_info');
    	$this->db->like('slot_name',$prfix);
		$query=$this->db->get();
		return $query->row_array();
    }


    function getSlotInfoById($id){
    	 $this->db->select('cpsi.*,tsl.device_id,cps.stages_id');
         $this->db->from('ci_parking_slot_info cpsi');
         $this->db->join('tbl_sensor_list tsl','cpsi.machine_id = tsl.id','left');
         $this->db->join('tbl_places_stages cps','cpsi.place_id = cps.place_id','left');
         	 
         $this->db->where('cpsi.place_id', $id);
         $this->db->order_by('slot_no', 'ASC');
        $query = $this->db->get();
         $data = array();
        if($query !== FALSE && $query->num_rows() > 0){
             $data = $query->result_array();
            }
        return $data;
    }

    function getUniqueSesnorId($device_id){
        $this->db->select('id as unique_sensor_id');       
        $this->db->from('tbl_sensor_list');
        $this->db->where('device_id',$device_id);
        $this->db->where('is_deleted',0);
        $query=$this->db->get();
        return $query->row_array();

    }


    function addSlotInfo($data){

    	$this->db->insert('ci_parking_slot_info',$data);
		return true;
    }

    public function AssignMachineId($id,$pk_device_id,$slot_name){
        $data = array('machine_id' =>$pk_device_id);
        $this->db->where('slot_name', $slot_name);
        $this->db->where('place_id', $id);
        $this->db->update('ci_parking_slot_info', $data);
        return true;

        }
        
   public function getPricesInfo($place_id,$type){

                $this->db->select('id,hrs,cost,pass,onCreated');
                $this->db->from('ci_price_slab');
                if($type=='pass'){
                $where = "place_id=$place_id AND (pass=2 OR pass=4) AND is_deleted=0";    
                }else{
                    $where = "place_id=$place_id AND pass = $type AND is_deleted = 0";
                }
                $this->db->order_by('hrs');
                $this->db->where($where);
                $query = $this->db->get();
                $data = array();
                if($query !== FALSE && $query->num_rows() > 0){
                 $data = $query->result_array();
                 }
                return $data;
        }
        
    public function getPricesInfoBySlabId($data){
             if($data['type']==1){
                    $type = 0;
                }else if($data['type']==2){
                    $type = 1;
                }else{
                    $type = 4;
                }
                 $place_id = $data['place_id']; 
                 $slab_id = $data['slab_id']; 
                $this->db->select('id,hrs,cost,pass,onCreated');
                $this->db->from('ci_price_slab');
                if($type=='4'){
                $where = "id = $slab_id AND place_id= $place_id AND (pass=2 OR pass=4) AND 'is_deleted'= 0";
                }else{
                    $where = "id = $slab_id AND place_id = $place_id  AND pass=$type AND 'is_deleted'=0 ";
                }
                $this->db->where($where);
                $query = $this->db->get();
                $data = array();
                if($query !== FALSE && $query->num_rows() > 0){
                 $data = $query->result_array();
                 }
                return $data[0];

        }

	}
