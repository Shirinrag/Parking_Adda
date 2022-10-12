<?php 

  // echo "<pre>";
  // print_r($affectedBookings);
  // die;

 ?>


<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css"> 
<div class="content-wrapper">
  <section class="content">
    <?php $this->load->view('admin/includes/_messages.php') ?>
   
        <!-- Blocked Slots Data -->
    <div class="card">
      
        <div class="card-body table-responsive" style="border:2px solid;border-radius:11px;">
        <center>
            <h5><b>Affected Bookings</b></h5>
        </center>


      <table id="affected_bookings_data" class="table table-bordered table-striped" width="105%">

            <thead>
              <tr style="background-color:burlywood">
              <th style="width:1%">#Id</th>
              <th style="width:10%">Booking Id</th>
              <th style="width:5%">From Time</th>
              <th style="width:5%">To Time</th>
              <th style="width:2%">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($affectedBookings as $key =>$values){ ?>
            <tr>
            <td><?= $key+1; ?></td>
            <td><?= $values['unique_booking_id'] ?></td>
            <td><?= date("d-m-Y H:i A", strtotime($values['booking_from_date'].$values['from_time'])); ?></td>
            <td><?= date("d-m-Y H:i A", strtotime($values['booking_to_date'].$values['to_time'])); ?></td>
            <td></td>
          </tr>
          <?php } ?>

          </tbody>
        </table>
      </div>
    </div>


   
  </section>  
</div>
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>
<script type="text/javascript">


 $(function () {
    $("#affected_bookings_data").DataTable();
  });


function UnblockSlots(complaint_id){
    if(confirm('Are You Sure Want To Activate This Slots?')){
    var ab;
    $.post('<?=base_url("admin/complaint/UnblockSlots")?>',
    {
      '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
      complaint_id : complaint_id,
    },
    
);
        $.notify("Successfully Slots Unblocked.", "success");

  }else{

  }
}

function callme(type){  

  if(type==1){
      $("#BlockedSlotsData").show();
      $("#PendingVerifications").hide();
      $('#BlockedSlotsDiv').attr('style','background-color: #71ff71');
      $('#PendingVerificationsDiv').attr('style','background-color: #fff');
      // alert = function() {};
   
  }else{

      $("#BlockedSlotsData").hide();
      $("#PendingVerifications").show();
      $('#BlockedSlotsDiv').attr('style','background-color: #fff');
      $('#PendingVerificationsDiv').attr('style','background-color: #71ff71');
      // alert = function() {};     
  }

}


</script>





