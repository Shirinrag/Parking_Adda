<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css"> 

<div class="content-wrapper">

<?php $this->load->view('admin/includes/complaints'); ?>

  <section class="content">

    <?php $this->load->view('admin/includes/_messages.php') ?>

    <div class="card" >

      <div class="card-header" style="margin-left:38%">

            <input  onclick="DivType(0)" name="cc_complaint_type" value="0" type="radio" checked=""  />&nbsp;&nbsp;Booking Related&nbsp;&nbsp;&nbsp;

            <input onclick="DivType()" name="cc_complaint_type" value="1" type="radio"  /> &nbsp;&nbsp;Other &nbsp;&nbsp;&nbsp;



      </div>

    </div>

  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/select2/css/select2.min.css">

  <div id="">

    <section class="content">

      <div class="container-fluid">

        <div class="card card-default">

          <div class="card-body">

            <div class="row" style="display: none" id="booking_div">

              <div class="col-md-6">

                 <?php echo form_open(base_url('admin/complaint/add_complaint'), 'class="form-horizontal"');  ?>

                <div class="form-group" >

                  <label>Search Booking</label><br>

                    <select name="booking_id" onchange="getval(this);" class="form-control select2" style="width: 90%"  required="">

                    <option value="">Search Booking</option>

                    <?php foreach($booking_id as $data): ?>

                          <option value="<?= $data['unique_booking_id']; ?>"><?= $data['unique_booking_id']."  |  ".$data['mobile_no']

                         ." | ".$data['firstname']." ".$data['lastname'] ; ?></option>

                      <?php endforeach; ?>

                    </select>

                    <input type="hidden" name="type" value="0">

                </div>

              </div>

                  <div class="form-group" id="submit_button" style="display: none">

                    <div class="col-md-12">

                      <input type="submit" name="submit" value="Add Complaints" class="btn btn-warning pull-right" style="margin-top:21%">

                    </div>

                  </div>

                   <?php echo form_close(); ?>

          </div>

           <div class="row" style="display: none" id="other_div">

          <div class="col-md-6">

                 <?php echo form_open(base_url('admin/complaint/add_other_complaint'), 'class="form-horizontal"');  ?>

              <div class="form-group" >

                  <label>Search Users</label><br>

                    <select name="user_id" onchange="getOther(this);" id="contact" class="form-control select2" style="width: 90%" >

                    <option value="">Search User</option>

                    <?php foreach($mob_data as $data): ?>

                          <option value="<?= $data['id']; ?>"><?= $data['mobile_no']." | ". $data['firstname']." ".$data['lastname']; ?></option>

                      <?php endforeach; ?>

                    </select>

                </div> 

              </div>

              <div class="form-group">

                    <div class="col-md-12">



                      <input type="submit" name="submit" value="Add Complaints" class="btn btn-warning pull-right" style="margin-top:21%">

                    </div>

                  </div>

                   <?php echo form_close(); ?>

          </div>

        </div>





<div id="all_complaints" style="display: none">

    <section class="content">

      <div class="row">

        <div class="col-12">

          <div class="card">

            <div class="card-header"> 

              <center><h5 id="booking_text"></h5></center>

            </div>

            <!-- /.card-header -->

            <div class="card-body">

              <table id="example2" class="table table-bordered table-hover">

                <thead>

                <tr>

                  <th>Car No</th>

                  <th>Place</th>

                  <th>Address</th>

                  <th>Issues</th>

                  <th>B.From</th>

                  <th>B.To</th>

                  <th>Resolutions</th>

                  <th>Source</th>

                  <th>Action</th>

                  <th>Status</th>

                </tr>

                </thead>



                <tbody id="complaints_data">     

                </tbody>

              </table>

            </div>

          </div>

        </div>

      </div>

    </section>







        </div>

        

        

        <!--Start Other Complaint Tables-->

        

    <div id="other_complaints_div" style="display: none">

     <section class="content">

      <div class="row">

        <div class="col-12">

          <div class="card">

            <div class="card-header"> 

              <center><h5 id="booking_text">Complaint Details</h5></center>

            </div>

            <!-- /.card-header -->

            <div class="card-body">

              <table id="example2" class="table table-bordered table-hover">

                <thead>

                <tr>

                  <th>Sr</th>

                  <th>Issues Type</th>

                  <th>Complaint</th>

                  <th>Source</th>

                  <th>Issue Raised</th>

                  <th>Status</th>

                  <th>Action</th>

                </tr>

                </thead>

                <tbody id="other_complaints_data">     

                </tbody>

              </table>

            </div>

          </div>

        </div>

      </div>

    </section>

    </div>

        



        

        <!--End Other Complaints Table-->

      </div>

      <!-- /.container-fluid -->





      



    </section>



  </div>





 





<script src="<?= base_url() ?>assets/plugins/select2/js/select2.full.min.js"></script>



  <script>

  $(function () {
    $('.select2').select2()
});



window.onload = function() {

  DivType(0);

};





function getOther(sel){

  var user_id = sel.value;

  $.post('<?=base_url("admin/complaint/checkOtherStatus")?>',

    {

      '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',

      user_id : user_id,

    },

    function(data){

      var data = $.parseJSON(data);

      var other_complaints = data.other_complaint;

      var count = other_complaints.length;

      if(count>0){

        var dhtml = '';var onum =1;



        $('#other_complaints_div').show();

         $.each(other_complaints, function(key, othercomplaintdata) {

             

        if(othercomplaintdata.source_type=='1'){

            var src = "1";

          }else{

            var src = "0";

          }

          

        if(othercomplaintdata.actions==0 && othercomplaintdata.source_type==1){

            var status_type = '<span class="badge badge-danger">Pending</span>';

            var action = '<a style="backgroud-color:b0dd84e8;" onclick="openotherhistory('+othercomplaintdata.id+','+othercomplaintdata.user_id+','+src+')"  title="View History"  class="view btn btn-sm btn-primary" ><i class="fa fa-edit"></i></a>';



        }else if(othercomplaintdata.actions==1 && othercomplaintdata.source_type==1){

             var action = '<a style="backgroud-color:b0dd84e8;" onclick="openotherhistory('+othercomplaintdata.id+','+othercomplaintdata.user_id+','+src+')" title="View History"  class="view btn btn-sm btn-warning" ><i class="fa fa-eye"></i></a>';

             var status_type = '<span class="badge badge-danger">Closed</span>';

        }

        else if(othercomplaintdata.actions==2 && othercomplaintdata.source_type==1){

             var status_type = '<span class="badge badge-danger">Processing</span>';

             var action = '<a style="backgroud-color:b0dd84e8;" onclick="openotherhistory('+othercomplaintdata.id+','+othercomplaintdata.user_id+','+src+')"  title="View History"  class="view btn btn-sm btn-primary" ><i class="fa fa-edit"></i></a>';



        }else if(othercomplaintdata.actions==1 && othercomplaintdata.source_type==0){

              var status_type = '<span class="badge badge-danger">Processing</span>';

              var action = '<a style="backgroud-color:b0dd84e8;" onclick="openotherhistory('+othercomplaintdata.id+','+othercomplaintdata.user_id+','+src+')"  title="View History"  class="view btn btn-sm btn-primary" ><i class="fa fa-edit"></i></a>';



              

        }else{

            var status_type = '<span class="badge badge-danger">Closed</span>';

            var action = '<a style="backgroud-color:b0dd84e8;" onclick="openotherhistory('+othercomplaintdata.id+','+othercomplaintdata.user_id+','+src+')" title="View History"  class="view btn btn-sm btn-warning" ><i class="fa fa-eye"></i></a>';



        }

            

          

          

          

          

        //   if(othercomplaintdata.fk_disposition_id==2){

        //           var action = '<a style="backgroud-color:b0dd84e8;" onclick="openotherhistory('+othercomplaintdata.id+','+othercomplaintdata.user_id+','+src+')" title="View History"  class="view btn btn-sm btn-warning" ><i class="fa fa-eye"></i></a>';

        //         }else{

        //           var action = '<a style="backgroud-color:b0dd84e8;" onclick="openotherhistory('+othercomplaintdata.id+','+othercomplaintdata.user_id+','+src+')"  title="View History"  class="view btn btn-sm btn-primary" ><i class="fa fa-edit"></i></a>';

        //         }



            if(othercomplaintdata.source_type == "1")

            {

              var sources = '<span class="badge badge-secondary" style="background-color:black;">App</span>';

            }else {

              var sources = '<span class="badge badge-warning">Call</span>';

            }



                dhtml +='<tr>';                

                dhtml +='<td>'+onum+'</td>';

                dhtml +='<td>'+othercomplaintdata.complaint_topic+'</td>';

                dhtml +='<td>'+othercomplaintdata.description+'</td>';

                dhtml +='<td>'+sources+'</td>';

                dhtml +='<td>'+othercomplaintdata.created_date+'</td>';

                dhtml +='<td>'+status_type+'</td>';

                dhtml +='<td>'+action+'</td>';

                dhtml +='</tr>';

                onum++;

         });

      $("#other_complaints_data").html(dhtml);

      }else{

        $('#other_complaints_div').hide();

      }





    });

}



function openotherhistory(complaint_id,user_id,src){

  var baseUrl="<?php echo base_url('admin/complaint/add_other_complaint/')?>";

  window.open(baseUrl+complaint_id+"/"+user_id+"/"+src, "_blank");



  

 

}









function DivType(id)

{

  if(id==0){

  $('#booking_div').show();

  $('#other_div').hide();

  $('#buttons').show();

  

  



  



 }else{

  $('#booking_div').hide();

  $('#other_div').show();

  $('#buttons').show(); 

  $("#all_complaints").hide();

  

  

 }

}





function getval(sel)

{

  var booking_id = sel.value;

  $.post('<?=base_url("admin/complaint/checkComplaintStatus")?>',

    {

      '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',

      booking_id : booking_id,

    },

    function(data){

      var data = $.parseJSON(data);

      var complaints = data.complaints;

      var count = complaints.length;

      if(data.buttons==1){

         $("#submit_button").hide();   

      }else{

        $("#submit_button").show();   

      }

      

      if(count>0){

        $("#all_complaints").show();

            var html = '';var num =1; var num1=0;



            $.each(complaints, function(key, complaintdata) {

              if(complaintdata.complaint_source==1){

                var source = '<span class="badge badge-primary">App</span>';

                if(complaintdata.status==1){

                var action = '<a style="backgroud-color:b0dd84e8;" onclick="openhistory('+complaintdata.booking_id+')" title="View History"  class="view btn btn-sm btn-warning" ><i class="fa fa-eye"></i></a>';

              }else{

                var action = '<a style="backgroud-color:b0dd84e8;" onclick="openhistory('+complaintdata.booking_id+')" title="View History"  class="view btn btn-sm btn-primary" ><i class="fa fa-edit"></i></a>';

              }

              }else{

                var source = '<span class="badge badge-secondary" style="background-color:black;">Call</span>';

                if(complaintdata.status==1){

                  var action = '<a style="backgroud-color:b0dd84e8;" onclick="opencallhistory('+complaintdata.booking_id+','+complaintdata.complaint_id+')" title="View History"  class="view btn btn-sm btn-warning" ><i class="fa fa-eye"></i></a>';

                }else{

                  var action = '<a style="backgroud-color:b0dd84e8;" onclick="opencallhistory('+complaintdata.booking_id+','+complaintdata.complaint_id+')" title="View History"  class="view btn btn-sm btn-primary" ><i class="fa fa-edit"></i></a>';

                }

                

              }

              if(complaintdata.status==1){

                 var c_status = '<i class="fa fa-check" aria-hidden="true" style="color:green;"></i>';

              }else if(complaintdata.status==2 || complaintdata.status==0){

                var c_status =  '<i class="fa fa-times" aria-hidden="true" style="color:red;"></i>';

              }



                html +='<tr>';

                

                html +='<td>'+complaintdata.car_number+'</td>';

                html +='<td>'+complaintdata.placename+'</td>';

                html +='<td>'+complaintdata.place_address+'</td>';

                html +='<td>'+complaintdata.complaint_text+'</td>';

                html +='<td>'+complaintdata.booking_from_date+'</td>';

                html +='<td>'+complaintdata.booking_to_date+'</td>';

                html +='<td>'+complaintdata.descriptions+'</td>';

                html +='<td>'+source+'</td>';

                html +='<td>'+action+'</td>';

                html +='<td>'+c_status+'</td>';

                html +='</tr>';



                num++; 

            });

            var booking_info = "Complaints History of Booking Id : " +complaints[0].unique_booking_id ;

            $('#booking_text').text(booking_info);

            $("#complaints_data").html(html);

          }else{

            $("#all_complaints").hide();

          }

    });



}



function openhistory(id){

  var baseUrl="<?php echo base_url('admin/complaint/view_complaint/')?>";

  window.open(baseUrl+id, "_blank");



}

function opencallhistory(id1,id2){

  

  var baseUrl="<?php echo base_url('admin/complaint/add_complaint/')?>";

  window.open(baseUrl+id1+"/"+id2, "_blank");

}











</script>

    

