<?php 
// echo "<pre>";
// print_r($info);
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
                  <h3 class="card-title"><i class="fa fa-list"></i>&nbsp; Blocked Slots List</h3>
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
                     <td style="width: 38%;"><?= $info['info']['placename'];?></td>
                     <td><b>Booking From</b></td>
                     <td><?= date("d-m-Y H:i A", strtotime($info['info']['booking_from']));?></td>                     
                  </tr>
                  <tr>
                     <th scope="">Place Address</th>
                     <td><?= $info['info']['place_address'];?></td>
                     <td><b>Booking To</b></td>
                     <td><?= date("d-m-Y H:i A", strtotime($info['info']['booking_to']));?></td>                     
                  </tr>
                  <tr>
                     <th scope="">Slot Name</th>
                     <td><?= $info['info']['slot_name'];?></td>
                     <th>Display Id</th>
                     <td class='blink' style="color: red;"><b><?= $info['info']['display_id'];?></b></td>
                  </tr>
                   <tr>
                     <td><b>Issue Raised</b></td>
                     <td><?= date("d-m-Y H:i A", strtotime($info['info']['issue_raised_on']));?></td>
                     <td><b>Complaint Source</b></td>
                     <td><?php
						if($info['info']['complaint_source']==0){
							echo "Verifier App"." <b>(".$info['info']['verifier_name'].")</b>";
						}else{
							echo "Call";
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

          			$var = "https://192.168.1.21/API/click2call.php?user=bds18&phone=";
                    $hang_variable= "https://192.168.1.21/agc/api.php?source=PARKINGADDA&user=bds18&pass=bds18&agent_user=bds18&function=external_hangup&value=1";
                    $v_mobile = $var.$value['contact'];
                    $v_mobile1 = $hang_variable;


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

          			$var = "https://192.168.1.21/API/click2call.php?user=bds18&phone=";
                    $hang_variable= "https://192.168.1.21/agc/api.php?source=PARKINGADDA&user=bds18&pass=bds18&agent_user=bds18&function=external_hangup&value=1";
                    $en_mobile = $var.$values['contact'];
                    $en_mobile1 = $hang_variable;
                


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

                   $var = "https://192.168.1.21/API/click2call.php?user=bds18&phone=";
                   $hang_variable= "https://192.168.1.21/agc/api.php?source=PARKINGADDA&user=bds18&pass=bds18&agent_user=bds18&function=external_hangup&value=1";
                    $mobile = $var.$engineers['contact'];
                    $mobile1 = $hang_variable;
                
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
       			<div class="col-md-6">
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

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="col-md-12 control-label">Customercare Remark</label>
                            <div class="col-md-8">
                              <textarea class="form-control" placeholder="Customercare Remark" name="cc_remark"></textarea>
                              <input type="hidden" name="fk_slot_id" value="<?= $info['info']['fk_slot_id'] ?>">
                              <input type="hidden" name="fk_complaint_id" value="<?= $info['info']['fk_complaint_id'] ?>">
                        	</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="submit" name="submit" value="Add Complaints" class="btn btn-warning pull-right" style="margin-right: 52%;">
                    </div>
                </div>
                <br><br>

         





    

    </div>

    

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
    confirm("are you sure to start the call.");
    $("#callstarted").show();
    localStorage.setItem("callstarted", "1");
    var ab;
    $.post(ab,

    {
      '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
    });

    }

    

    

    }
</script>