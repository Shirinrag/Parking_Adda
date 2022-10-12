<?php 
    $method = $this->router->fetch_method(); 
  if(empty($_GET)){

      $type = 1;

  }else{

      $type = $_GET['type'];

  }

?>
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css"> 

<div class="content-wrapper">

  <section class="content">

    <!-- For Messages -->

    <?php $this->load->view('admin/includes/_messages.php') ?>
    <div class="card">
     <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-sm-6 col-md-6"  >
            <a href="#" style="color: black" >
             <div class="info-box mb-3" id="BlockedSlotsDiv" onclick="callme(1)">
              <span class="info-box-icon  elevation-1" style="color: #f9fffe">
                <img src="https://carnab.com/images/high-quality-cars.png">
              </span>
              <div class="info-box-content">
                <span class="info-box-text"><b>Blocked Slots</b></span>
                <span class="info-box-number"><?= count($BlockedSlots);?></span>
              </div>
            </div>
          </a> 
          </div>
          <div class="col-12 col-sm-6 col-md-6"  >
                <a href="#" style="color: black">
             <div class="info-box mb-3" id="PendingVerificationsDiv" onclick="callme(2)">
              <span class="info-box-icon  elevation-1" style="color: #f9fffe">
               <img src="https://cdn-icons-png.flaticon.com/512/4233/4233799.png">
              </span>
              <div class="info-box-content">
                <span class="info-box-text"><b>Pending Verifications</b></span>
                <span class="info-box-number"><?= count($PendingVerifications);?></span>
              </div>
            </div>
          </a> 
          </div>
        </div>
      </div>
    </div>
        
        <!-- Blocked Slots Data -->
    <div class="card" style="display: none" id="BlockedSlotsData">
      
        <div class="card-body table-responsive" style="border:2px solid;border-radius:11px;">
        <center>
            <h5><b>Blocked Slots</b></h5>
        </center>

        <table id="BlockedSlots" class="table table-bordered table-striped" width="105%">
          <thead>
               <tr style="background-color:burlywood">
                  <th style="width:1%">#Id</th>
                  <th style="width:10%">Place Info</th>
                  <th style="width:5%">Slot Id</th>
                  <th style="width:5%">Issue Raised</th>
                  <th style="width:5%">Source</th>          
                  <th style="width:2%">Affected Booking</th>
                  <th style="width:2%">Action</th>
                </tr>
                </thead>
            <tbody>
            <?php 
            foreach ($BlockedSlots as $keys =>$values){?>
            <tr>
              <td><?= $keys+1; ?></td>
              <td><?= $values['placename'];?></td>
              <td><a href="#" onclick="UnblockSlots('<?= $values['complaint_id'] ?>')"><?= $values['display_id'];?></a></td>
              <td><?= date("d-m-Y H:i A", strtotime($values['issue_raised_on']));?></td>
              <td><?=  $src = ($values['complaint_source']==0) ? 'Replacement' : 'Guide App' ; ;?></td>
              <td><a href="<?= base_url('admin/complaint/getAffectedBookings/'.$values['complaint_id'].'/'.$values['slot_id'])?>" title="View Affected Bookings"  ><b> <?= $values['affected_booking'];?></b></a></td>
              <td></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

                         <!--Pending Verififcations-->

    <div class="card" style="display:none" id="PendingVerifications">
      <div class="card-body table-responsive" style="border:2px solid;border-radius:11px;">
        <center>
            <h5><b>Pending Verifications</b></h5>
        </center>

        <table id="PendingVerificationsTbl" class="table table-bordered table-striped" width="105%">

            <thead>
              <tr style="background-color:burlywood">
              <th style="width:1%">#Id</th>
              <th style="width:10%">Place Info</th>
              <th style="width:5%">Slot Id</th>
              <th style="width:5%">Issue Raised</th>
              <th style="width:5%">Source</th>          
              <th style="width:2%">Affected Booking</th>
              <th style="width:2%">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            foreach ($PendingVerifications as $keys =>$value){?>
            <tr>
              <td><?= $keys+1; ?></td>
              <td><?= $value['placename'];?></td>
              <td><?= $value['display_id'];?></td>
              <td><?= date("d-m-Y H:i A", strtotime($value['issue_raised_on']));?></td>
              <td><?=  $src = ($value['complaint_source']==0) ? 'Replacement' : 'Guide App' ; ;?></td>
              <td><a href="<?= base_url('admin/complaint/getAffectedBookings/'.$value['complaint_id'].'/'.$value['slot_id'])?>" title="View Affected Bookings"  ><b> <?= $value['affected_booking'];?></b></a></td>
              <td><a href="<?= base_url('admin/complaint/VerifySlotsComplaint/'.$value['complaint_id'].'/'.$value['place_id'])?>" title="View History" class="view btn btn-sm btn-warning"   ><i class="fa fa-edit"></i></a>
              </td>
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
var table = $('#BlockedSlots').DataTable( {
    "processing": true,
    "serverSide": false,
    "ajax": "",
  });
var table = $('#PendingVerificationsTbl').DataTable( {
      "processing": true,
      "serverSide": false,
      "ajax": "",
      });

window.onload = function() {
  callme(1)
};



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
        location.reload();


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





