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

          <div class="col-12 col-sm-4 col-md-4" onclick="callme(1)" >

            <a href="#" style="color: black">

             <div class="info-box mb-3" id="bookingC">

              <span class="info-box-icon  elevation-1" style="color: #f9fffe">

                <img src="https://carnab.com/images/high-quality-cars.png">

              </span>

              <div class="info-box-content">

                <span class="info-box-text"><b>Booking Complaints</b></span>

                <span class="info-box-number"><?php echo $this->session->userdata('BookingComplaints'); ?></span>

              </div>

            </div>

          </a> 

          </div>



          <div class="col-12 col-sm-4 col-md-4" onclick="callme(2)" >

                <a href="#" style="color: black">

             <div class="info-box mb-3" id="otherC">

              <span class="info-box-icon  elevation-1" style="color: #f9fffe">

               <img src="https://cdn-icons-png.flaticon.com/512/4233/4233799.png">

              </span>

              <div class="info-box-content">

                <span class="info-box-text"><b>Other Complaints</b></span>

                <span class="info-box-number"><?php echo $this->session->userdata('OtherComplaints'); ?></span>

              </div>

            </div>

          </a> 

          </div>

          <!-- Slots Complaints -->

           <div class="col-12 col-sm-4 col-md-4" onclick="callme(3)" >

                <a href="#" style="color: black">

             <div class="info-box mb-3" id="SlotsC">

              <span class="info-box-icon  elevation-1" style="color: #f9fffe">

               <img src="https://pngimg.com/uploads/parking/parking_PNG81.png">

              </span>

              <div class="info-box-content">

                <span class="info-box-text"><b>Slots Complaints</b></span>

                <span class="info-box-number"><?php echo $this->session->userdata('SlotsComplaints'); ?></span>

              </div>

            </div>

          </a> 

          </div>





        </div>



      </div>

    </div>

    

     <center>

            <h5><b>Closed Complaints List</b></h5>

        </center>

    <div class="card" style="display:none" id="booking_complaints">

        <div class="card-body table-responsive" style="border:2px solid;border-radius:11px;">

        <table id="na_datatable" class="table table-bordered table-striped" width="105%">

          <thead>

             <tr style="background-color:burlywood">

              <th style="width:1%">#Id</th>

              <th style="width:1%">Booking Id</th>

              <th style="width:10%">Place Info</th>

              <th style="width:5%">Issues</th>

              <th style="width:5%">From Date</th>

              <th style="width:5%">To Date</th>          

              <th style="width:2%">Verifier</th>

              <th style="width:2%">Source</th>

              <th style="width:2%">Action</th>

            </tr>

          </thead>

        </table>

      </div>

    </div>

    

    

    

    

    

    

                                                            <!--Other Complaints-->

                                                            

    <div class="card" style="display:none" id="other_complaints">

      <div class="card-body table-responsive" style="border:2px solid;border-radius:11px;">

        <table id="othercomplaints" class="table table-bordered table-striped" width="105%">

          <thead>

               <tr style="background-color:burlywood">
                  <th>Id</th>

                  <th>Username</th>

                  <th>Issue Type</th>

                  <th>Complaint</th>

                  <th>Source</th>

                  <th>Issues Raised</th>

                  <th>Status</th>

                  <th>Action</th>

                </tr>

                </thead>

        </table>

      </div>

    </div>






              <!-- Slots Complaints -->



     <div class="card" style="display:none" id="slots_complaints">
      <div class="card-body table-responsive" style="border:2px solid;border-radius:11px;">
        <table id="slotscomplaints" class="table table-bordered table-striped" width="105%">
          <thead>
               <tr style="background-color:burlywood">
                  <th>Id</th>
                  <th>Place Info</th>
                  <th>Slot Info</th>
                  <th>Issue Raised On</th>
                  <th>Issue Fixed On</th>
                  <th>Source</th>
                  <th>Closed By</th>
                </tr>
                </thead>
        </table>
      </div>
    </div>
  </section>  
</div>

<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>

<script>

window.onload = function() {
  callme(1);
};







function callme(type){

  if(type==1){

    $("#booking_complaints").show();
    $("#other_complaints").hide();
    $("#slots_complaints").hide();
    $('#SlotsC').attr('style','background-color: #fff');
    $('#bookingC').attr('style','background-color: #71ff71');
    $('#otherC').attr('style','background-color: #fff');

    alert = function() {};





      

    var table = $('#na_datatable').DataTable( {
    "processing": true,
    "serverSide": false,
    "ajax": "<?=base_url('admin/complaint/cc_datatable_json')?>",
    "order": [[1,'desc']],
    "columnDefs": [
    { "targets": 0, "name": "id", 'searchable':true, 'orderable':true},
    { "targets": 1, "name": "unique_booking_id", 'searchable':true, 'orderable':true},
    { "targets": 2, "name": "placename", 'searchable':true, 'orderable':true},
    { "targets": 3, "name": "complaint_text", 'searchable':false, 'orderable':false},
    { "targets": 4, "name": "booking_from_date", 'searchable':true, 'orderable':true},
    { "targets": 5, "name": "booking_to_date", 'searchable':true, 'orderable':true},
    { "targets": 6, "name": "verifier_name", 'searchable':true, 'orderable':true},
    { "targets": 7, "name": "action", 'searchable':false, 'orderable':true},
    { "targets": 8, "name": "source", 'searchable':true, 'orderable':true},
    ]
  });

      

  }else if(type==2){

      $("#booking_complaints").hide();
      $("#other_complaints").show();
      $("#slots_complaints").hide();
      $('#bookingC').attr('style','background-color: #fff');
      $('#otherC').attr('style','background-color: #71ff71');
      $('#SlotsC').attr('style','background-color: #fff');

      // alert = function() {};
    var table = $('#othercomplaints').DataTable({
    "processing": true,
    "serverSide": false,
    "ajax": "<?=base_url('admin/complaint/getAllOtherComplaints')?>",
    "order": [[5,'asc']],
    "columnDefs": [
    { "targets": 0, "name": "id", 'searchable':true, 'orderable':true},
    { "targets": 1, "name": "username", 'searchable':true, 'orderable':true},
    { "targets": 2, "name": "complaint_topic", 'searchable':true, 'orderable':true},
    { "targets": 3, "name": "description", 'searchable':false, 'orderable':false},
    { "targets": 4, "name": "source_type", 'searchable':true, 'orderable':true},
    { "targets": 5, "name": "created_date", 'searchable':true, 'orderable':true},
    { "targets": 6, "name": "status", 'searchable':true, 'orderable':true},
    { "targets": 7, "name": "action", 'searchable':false, 'orderable':true},
    ]

  });
  }else{
      $("#booking_complaints").hide();
      $("#other_complaints").hide();
      $("#slots_complaints").show();
      $('#bookingC').attr('style','background-color: #fff');
      $('#otherC').attr('style','background-color: #fff');
      $('#SlotsC').attr('style','background-color: #71ff71');

    var table = $('#slotscomplaints').DataTable({
    "processing": true,
    "serverSide": false,
    "ajax": "<?=base_url('admin/complaint/getClosedSlotsComplaints')?>",
    "order": [[5,'asc']],
    "columnDefs": [
    { "targets": 0, "name": "id", 'searchable':true, 'orderable':true},
    { "targets": 1, "name": "username", 'searchable':true, 'orderable':true},
    { "targets": 2, "name": "complaint_topic", 'searchable':true, 'orderable':true},
    { "targets": 3, "name": "description", 'searchable':false, 'orderable':false},
    { "targets": 4, "name": "source_type", 'searchable':true, 'orderable':true},
    { "targets": 5, "name": "created_date", 'searchable':true, 'orderable':true},
    { "targets": 6, "name": "status", 'searchable':true, 'orderable':true},
    ]

  });
  }

}









</script>





<script type="text/javascript">

  

  function cc_actions(id)

  {

    var array = id.split(',');

   var parking_id =array[0];

   var unique_id =array[1];





  $('#booking_id').val(parking_id);

  $('#unique_id').val(unique_id);

  $("#action_popup").modal("toggle");

 }



 

 function submit_complaints()

 {

  var booking_id = $('#booking_id').val();

  var unique_id = $('#unique_id').val();

  var issue_type = $('#issue_type').val();

  var other_remarks = $('#other_remarks').val();

  var enforcers_remark = $('#remarks').val();

  $.post('<?=base_url("admin/complaint/complaints_update")?>',

    {

      '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',

    

      issue_type : issue_type,

      enforcers_remark : enforcers_remark,

      unique_id : unique_id,

    },

    function(data){

      $.notify("Status Changed Successfully", "success");

      location.reload();

    });





 }

 

  function complaints_filter()

  {

    var _form = $("#cc_advance_search").serialize();

    $.ajax({

      data: _form,

      type: 'post',

      url: '<?php echo base_url();?>admin/Complaint/cc_search',

      async: true,

      success: function(output){

        table.ajax.reload( null, false );

      }

    });

  }

</script>





