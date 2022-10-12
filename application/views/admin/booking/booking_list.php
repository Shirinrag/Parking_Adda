<link rel="stylesheet" href="<?= base_url()?>assets/plugins/datatables/dataTables.bootstrap4.css">











  <!-- Content Wrapper. Contains page content -->



  <div class="content-wrapper">



    <!-- Content Header (Page header) -->



      <div class="container-fluid">

         <div class="container-fluid">

        <div class="row">

           <div class="col-12 col-sm-6 col-md-6" onclick="callme(1)"  >

            <a href="#" style="color: black">

             <div class="info-box mb-6" id="AllBookings">

              <span class="info-box-icon  elevation-1" style="color: #f9fffe">

                <img src="https://i.pinimg.com/originals/84/0a/b8/840ab8583a4f89050c46f063cc205a57.png">

              </span>

              <div class="info-box-content">

                <span class="info-box-text"><b>All Booking</b></span>

                <span class="info-box-number"><?php echo $Allbookings; ?></span>

              </div>

            </div>

          </a> 

          </div>





           <div class="col-12 col-sm-6 col-md-6" onclick="callme(2)" >

            <a href="#" style="color: black">

             <div class="info-box mb-6" id="FolloupsBookings">

              <span class="info-box-icon  elevation-1" style="color: #f9fffe">

                <img src="https://cdn-icons-png.flaticon.com/512/4233/4233799.png">

              </span>

              <div class="info-box-content">

                <span class="info-box-text"><b>Pending Followup</b></span>

                <span class="info-box-number"><?php echo $Followups; ?></span>

              </div>

            </div>

          </a> 

          </div>















        </div>

      </div>

   





    </section>







    <!-- Main content -->



    <section class="content">



      <div class="row">



        <div class="col-12">



          <div class="card">



            <div class="card-header">



              <h3 class="card-title">All Data</h3>



            </div>



            <!-- /.card-header -->



           <div class="card">













      <div class="card-body table-responsive" id="all_bookings" style="display: none">

        <table id="na_datatable" class="table table-bordered table-striped" width="130%">

          <thead>

            <tr>

              <th style="width:1%" >#Id</th>
              <th style="width:3%">Booking Id</th>
              <th style="width:5%">Username</th>
              <th style="width:12%">Place Info</th>
              <th style="width:8%">From Date</th>
              <th style="width:8%">To Date</th>  
              <th style="width:4%">Checkout Status</th>
              <th style="width:2%">Booking Status</th>
              <th style="width:2%">Cost</th>


            </tr>

          </thead>

        </table>

      </div>







      <div class="card-body table-responsive" id="followups_table" style="display: none">

        <table id="fna_datatable" class="table table-bordered table-striped" width="130%">

          <thead>

            <tr>
              <th style="width:1%" >#Id</th>
              <th style="width:3%">Booking Id</th>
              <th style="width:2%">Username</th>
              <th style="width:10%">Place Info</th>
              <th style="width:8%">From Date</th>
              <th style="width:8%">To Date</th>  
              <th style="width:4%">Checkout Status</th>
              <th style="width:2%">Booking Status</th>
              <th style="width:2%">Cost</th>

            </tr>

          </thead>

        </table>

      </div>











    </div>





          </div>









         





        </div>





      </div>





    </section>





  </div>













<!-- DataTables -->



<script src="<?= base_url()?>assets/plugins/datatables/jquery.dataTables.js"></script>

<script src="<?= base_url()?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>







<script>

window.onload = function(){

    

    if(localStorage.getItem("type")!=''){

        callme(localStorage.getItem("type"));

    }else{

         callme(1);

    }

  

};





function callme1(ab){





   

   var callstarturl = "<?php echo $this->session->userdata('call_start_url'); ?>";

  var url = callstarturl + ab;

   



    var data = localStorage.getItem("callstarted");



    if(data!='' && data=='1'){



     $.notify("Please End The Previous Call First.", "error");



        



    }else{



    if(confirm("are you sure to start the call.")){



    $("#callstarted").show();



    localStorage.setItem("callstarted", "1");



    var url;



    $.post(url,



    {



      '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',



      



    });



    }



    }



    



    }





function callme(type){

 alert = function() {};

  if(type==1){

      $('#AllBookings').attr('style','background-color: #71ff71');

      $('#FolloupsBookings').attr('style','background-color: #fff');

      $("#all_bookings").show();

      $("#followups_table").hide();

      localStorage.setItem("type", "1");





     var table = $('#na_datatable').DataTable({

    "processing": true,

    "serverSide": false,

    "ajax": "<?=base_url('admin/Booking/datatable_json')?>",

    "order": [[1,'desc']],

    "columnDefs": [

    { "targets": 0, "name": "id", 'searchable':true, 'orderable':true},

    { "targets": 1, "name": "unique_booking_id", 'searchable':true, 'orderable':true},

    { "targets": 2, "name": "usernames", 'searchable':true, 'orderable':true},

    { "targets": 3, "name": "cost", 'searchable':true, 'orderable':true},

    { "targets": 4, "name": "firstname", 'searchable':true, 'orderable':true},

    { "targets": 5, "name": "placename", 'searchable':true, 'orderable':true},

    { "targets": 6, "name": "booking_from_date", 'searchable':true, 'orderable':true},

    { "targets": 7, "name": "booking_to_date", 'searchable':true, 'orderable':true},
    { "targets": 8, "name": "cost", 'searchable':true, 'orderable':true},




    ]

  });



    }else{

      $('#FolloupsBookings').attr('style','background-color: #71ff71');

      $('#AllBookings').attr('style','background-color: #fffs');

      $("#all_bookings").hide();

      $("#followups_table").show();

      localStorage.setItem("type", "2");





    var table = $('#fna_datatable').DataTable({

    "processing": true,

    "serverSide": false,

    "ajax": "<?=base_url('admin/Booking/followup_datatable_json')?>",

    "order": [[1,'desc']],

    "columnDefs": [

   
    { "targets": 0, "name": "id", 'searchable':true, 'orderable':true},

    { "targets": 1, "name": "unique_booking_id", 'searchable':true, 'orderable':true},

    { "targets": 2, "name": "usernames", 'searchable':true, 'orderable':true},

    { "targets": 3, "name": "cost", 'searchable':true, 'orderable':true},

    { "targets": 4, "name": "firstname", 'searchable':true, 'orderable':true},

    { "targets": 5, "name": "placename", 'searchable':true, 'orderable':true},

    { "targets": 6, "name": "booking_from_date", 'searchable':true, 'orderable':true},

    { "targets": 7, "name": "booking_to_date", 'searchable':true, 'orderable':true},
    { "targets": 8, "name": "cost", 'searchable':true, 'orderable':true},

    ]

  });







    }

}









</script>



</body>



</html>



