<?php 
// echo "<pre>";
// print_r($slot_info);
// die;
?>
<style>
      .blink {
        animation: blinker 0.6s linear infinite;
        color: #1c87c9;
        font-size: 30px;
        font-weight: bold;
        font-family: sans-serif;
      }
      @keyframes blinker {
        50% {
          opacity: 0;
        }
      }
      .blink-one {
        animation: blinker-one 1s linear infinite;
      }
      @keyframes blinker-one {
        0% {
          opacity: 0;
        }
      }
      .blink-two {
        animation: blinker-two 1.4s linear infinite;
      }
      @keyframes blinker-two {
        100% {
          opacity: 0;
        }
      }
    </style>    
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url()?>assets/plugins/datatables/dataTables.bootstrap4.css">
<div class="content-wrapper">
   <section class="content">
      <!-- For Messages -->
      <?php $this->load->view('admin/includes/_messages.php') ?>
      <div class="card">
         <div class="card-header">
            <div class="d-inline-block">
               <center>
                  <h3 class="card-title"><i class="fa fa-list"></i>&nbsp;Slots Complaints Verification </h3>
               </center>
            </div>
         </div>
      </div>
      <div class="card">

         <div class="card-body table-responsive">
            <table class="table table-bordered">
               <thead style="background-color: burlywood;">
                  <tr>
                    <th colspan="6"><center>Slots Informations</center></th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <th scope="row">Place Name </th>
                     <td style="width: 38%;"><?= $slot_info['placename'];?></td>
                     <th scope="">Slot Name</th>
                     <td><?= $slot_info['slot_name'];?></td>                   
                  </tr>
                  <tr>
                     <th scope="">Place Address</th>
                     <td><?= $slot_info['place_address'];?></td>
                     <th>Display Id</th>
                     <td class='blink' style="color: red;"><b><?= $slot_info['display_id'];?></b></td>                     
                  </tr>
                  
                   <tr>
                     <td><b>Issue Raised</b></td>
                     <td><?= date("d-m-Y H:i A", strtotime($slot_info['issue_raised_on']));?></td>
                     <td><b>Complaint Source</b></td>
                     <td><?php
						if($slot_info['complaint_source']==0){
							echo "Guide App (Replacement)"." <b>(".$slot_info['verifier_name'].")</b>";
						}else{
							echo "Guide App (Direct)"." <b>(".$slot_info['verifier_name'].")</b>";
						} ;?></td>
                  </tr>

               </tbody>
            </table>
         </div>


         <center><h4><b>Verifiers List</b></h4></center>
         <div class="card">
      <div class="card-body table-responsive" style="border-radius:17px;border: 1px solid black;">
        <table id="na_datatable" class="table table-bordered table-striped" width="100%" >
          <thead style="background-color: burlywood;">
            <tr>
              <th>#<?= trans('id') ?></th>
              <th>Verifier Name</th>
              <th>Contact</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          	<?php 
          		foreach ($info['verifiers'] as $key => $value) { 

          		
                    $v_mobile = $this->session->userdata('call_start_url').$value['contact'];
                    $hang_var= $this->session->userdata('call_hangup_url');
                    $v_mobile1 = $hang_var;




          			?>
          			<tr>
          				<td><?= $key+1;?></td>
          				<td><?= $value['verifier_name'];?></td>
          				<td><?= $value['contact']?></td>
          				<td><a href="#" onclick="makecall('<?php echo $v_mobile; ?>')" title="Start Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/start.png" style="height: 28px;margin-left:7%"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                 		<a href="#" onclick="endcall('<?php echo $v_mobile1; ?>')" title="End Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/hangup.png" style="height: 15px;"></a></td>


          			</tr>
          		<?php }

          	?>
          	<tr>
          		
          	</tr>
          </tbody>
        </table>
      </div>

      <br><br>



      <center><h4><b>Enforcer List</b></h4></center>
         <div class="card">
      <div class="card-body table-responsive" style="border-radius:17px;border: 1px solid black;">
        <table id="enforcers_data" class="table table-bordered table-striped" width="100%">
          <thead style="background-color: burlywood;">
            <tr>
              <th>#<?= trans('id') ?></th>
              <th>Enforcer Name</th>
              <th>Contact</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          	<?php 
          		foreach ($info['enforcers'] as $key => $values) {

                    $en_mobile = $this->session->userdata('call_start_url').$value['contact'];
                    $hang_variable= $this->session->userdata('call_hangup_url');
                    $en_mobile1 = $hang_var;
                
          		 ?>
          			<tr>
          				<td><?= $key+1;?></td>
          				<td><?= $values['enforcer_name'];?></td>
          				<td><?= $values['contact']?></td>
          				<td><a href="#" onclick="makecall('<?php echo $en_mobile; ?>')" title="Start Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/start.png" style="height: 28px;margin-left:7%"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                 		<a href="#" onclick="endcall('<?php echo $en_mobile1; ?>')" title="End Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/hangup.png" style="height: 15px;"></a></td>

          			</tr>
          		<?php }

          	?>
          	<tr>
          		
          	</tr>
          </tbody>
        </table>
      </div>
    </div>
    <br><br>

    <center><h4><b>Engineers List</b></h4></center>
         <div class="card">
      <div class="card-body table-responsive" style="border-radius:17px;border: 1px solid black;">
      	<table id="engineers_data" class="table table-bordered table-striped">
          <thead style="background-color: burlywood;">
            <tr>
              <th>#<?= trans('id') ?></th>
              <th>Enforcer Name</th>
              <th>Contact</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          	<?php 
          		foreach ($info['engineers'] as $key => $engineers) {


                    $mobile = $this->session->userdata('call_start_url').$value['contact'];
                    $hang_variable= $this->session->userdata('call_hangup_url');
                    $mobile1 = $hang_var;                
          		 ?>
          			<tr>
          				<td><?= $key+1;?></td>
          				<td><?= $engineers['engineer_name'];?></td>
          				<td><?= $engineers['contact']?></td>
          				<td><a href="#" onclick="makecall('<?php echo $mobile; ?>')" title="Start Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/start.png" style="height: 28px;margin-left:7%"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                 		<a href="#" onclick="endcall('<?php echo $mobile1; ?>')" title="End Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/hangup.png" style="height: 15px;"></a></td>

          			</tr>
          		<?php }

          	?>
          	<tr>
          		
          	</tr>
          </tbody>
        </table>
      </div>

      <br><br><br>
        
        
          <?php echo form_open(base_url('admin/complaint/forward_complaints'), 'class="form-horizontal"');  ?>
          
          <div class="row">
              <div class="col-md-6">
                  <div class="row">
       			    <div class="col-md-12">
                    <div class="form-group">
                        <label for="" class="col-md-12 control-label">Forward Complaint To</label>
                         <div class="col-md-8">
                             <select name="fk_eng_id" class="form-control select2" style="width: 90%"  required="">
                    				<option value="">Select Enginner</option>
                    				 <?php foreach($info['engineers'] as $data): ?>
                          					<option value="<?= $data['admin_id']; ?>"><?= $data['engineer_name'] ; ?></option>
                      				 <?php endforeach; ?>
                    		</select>
                        </div>
                    </div>
                </div>
       			</div>
       			
       			
       			<div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="col-md-12 control-label">Estimation To Fix Issue</label>
                                    <div class="col-md-8">
                                      <!--  <input type="datetime-local" name="estimated_hrs" class="form-control" id=""
                                          placeholder="" value="" required="" > -->
                                          <input type="number" name="estimated_hrs" class="form-control" id=""
                                          placeholder="Please Enter Hrs." value="" required="" >

                        </div>
                    </div>

                </div>
                    </div>
                </div>
                
                
                <div class="row">
                    <div class="col-md-12">
                    <div class="form-group">
                        <label for="" class="col-md-12 control-label">Customercare Remark</label>
                            <div class="col-md-8">
                              <textarea class="form-control" placeholder="Customercare Remark" name="cc_remark"></textarea>
                              <input type="hidden" name="fk_slot_id" value="<?= $slot_info['fk_slot_id'] ?>">
                              <input type="hidden" name="fk_complaint_id" value="<?= $slot_info['fk_complaint_id'] ?>">
                        	</div>
                    </div>
                </div>
                </div>
                
                
                
                <div class="row">
                    <div class="col-md-12">
                    <div class="form-group">
                        <input type="submit" name="submit" value="Add Complaints" class="btn btn-warning pull-right" style="margin-right: 52%;">
                    </div>
                </div>
                </div>
                  
              </div>
              <div class="col-md-6">
                  <div class="container">
                    <?php 
                    if(!empty($slot_info['img_attachments'])){ ?>
                      <img src="<?= $slot_info['img_attachments']; ?>" width="400" height="300" style="border: 2px solid black;border-radius: 20px;">

                   <?php }

                    ?>
                  </div>
              </div>
          </div>
          
          
       			
       			
       			
                

                
                
                <br><br>
    </div>


              <!-- <div class="col-md-6">
                    <div class="form-group">
                         <div class="col-md-8">
                           
                        </div>
                    </div>

                </div> -->


    

</form>



      </div>

   </section>
</div>
<script src="<?= base_url()?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>
<script src="<?= base_url()?>assets/plugins/select2/js/select2.full.min.js"></script>

<script>
  $(function () {
    $("#engineers_data").DataTable();
    $('#enforcers_data').DataTable();
  });

    $(function () {
    $('.select2').select2()
});

    function makecall(ab){
    var data = localStorage.getItem("callstarted");
    if(data!='' && data=='1'){
     $.notify("Please End The Previous Call First.", "error");
    }else{
    if(confirm("are you sure to start the call.")){
    $("#callstarted").show();
    localStorage.setItem("callstarted", "1");
    var ab;
    $.post(ab,

    {
      '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
    });
    }
}
    

    

    }
</script>