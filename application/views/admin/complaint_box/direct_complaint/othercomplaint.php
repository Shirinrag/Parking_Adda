

<link rel="stylesheet" href="<?= base_url()?>assets/plugins/datatables/dataTables.bootstrap4.css">



<!DOCTYPE html>

<html>

<head>

  <title></title>

</head>

<style>

    .student-profile .card {

    border-radius: 10px;

}



.student-profile .card .card-header .profile_img {

    width: 150px;

    height: 150px;

    margin: 10px auto;

    border: 10px solid #ccc;

    border-radius: 50%;

}



.student-profile .card h3 {

    font-size: 20px;

    font-weight: 700;

}



.student-profile .card p {

    font-size: 16px;

    color: #000;

}



.student-profile .table th,

.student-profile .table td {

    font-size: 14px;

    padding: 5px 10px;

    color: #000;

}

</style>

<body>



  <div class="content-wrapper">

    <!-- Main content -->

      <?php $this->load->view('admin/includes/complaints'); ?>



    <section class="content">

      <div class="card card-default color-palette-bo">

        <div class="card-header">

          <div class="d-inline-block"><h5>Compaint History Of Booking Id-<?php echo $user_info['firstname']." ".$user_info['lastname']; ?> </h5>

          </div>

       

        </div>

        <?php $this->load->view('admin/includes/_messages.php') ?>

        

        <div class="card-body">

          <div class="row">

            <div class="col-md-12">

              <div class="box">

                <!-- form start -->

               <div id="accordion">

  <div class="card">

    <div class="card-header" id="headingOne">

      <h5 class="mb-0">

        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">

            User Informations

        </button>

      </h5>

    </div>

       

    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">

      <div class="student-profile py-4">

  <div class="container">

    <div class="row">

      <div class="col-lg-4">

        <div class="card shadow-sm">

          <div class="card-header bg-transparent text-center">

            <img class="profile_img" src="<?php echo $user_info['image']; ?>" alt="profile">

            <h3><?php echo $user_info['firstname'].' '.$user_info['lastname']; ?></h3>

          </div>

         

        </div>

      </div>

      <div class="col-lg-8">

        <div class="card shadow-sm">

          <div class="card-header bg-transparent border-0">

            <h3 class="mb-0"></i>Customer Details</h3>

          </div>

          <div class="card-body pt-0">

            <table class="table table-bordered">

              <tr>

                <th width="5%">Email</th>

                <td width="15%"><?php echo $user_info['email']; ?></td>

                

              </tr>

              <tr>

                <th width="5%">Contact</th>

                <?php


                    $mob = $this->session->userdata('call_start_url').$user_info['mobile_no'];
                    $hang_var= $this->session->userdata('call_hangup_url');
                    $mob1 = $hang_var;


                 

                ?>

                <td width="15%"><?php echo $user_info['mobile_no']; ?>

                 <a href="#" id="startcalling"  onclick="makecall('<?php echo $mob; ?>')" title="Start Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/start.png" style="height: 39px;margin-left:7%"></a>

                 &nbsp;&nbsp;&nbsp;&nbsp;

                 <a href="#" onclick="endcall('<?php echo $mob1; ?>')" title="End Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/hangup.png" style="height: 20px;margin-left:50%"></a>

                </td>

                

               

              </tr>

              <tr>

                <th width="5%">Address</th>

                <td width="15%"><?php echo $user_info['address']; ?></td>

               

              </tr>

             

              <tr>

                <th width="5%">Created Date</th>

                <td width="15%"><?php echo $user_info['created_at']; ?></td>

               

              </tr>

            </table>

          </div>

        </div>

        

       

      </div>

    </div>

  </div>

</div>

    </div>

  </div>

 

              <!-- Car Informations -->





  <div class="card">

    <div class="card-header" id="headingTwo">

      <h5 class="mb-0">

        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">

         Car Informations

        </button>

      </h5>

    </div>

    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">

      <div class="card-body">

       <div class="col-lg-12">

        <div class="card shadow-sm">

          

         <div class="card-body pt-0">

             <table id="carinfo" class="table table-bordered table-striped">

                <thead>

                <tr>

                  <th>Sr</th>

                  <th>Car Model</th>

                  <th>CAR Number</th>

                  <th>Added On</th>

                </tr>

                </thead>

                <tbody>

                 

                  <?php 

                  foreach ($cars_info as $key => $cars) { ?>

                    <tr>

                     <td><?php echo $key+1; ?></td>

                     <td><?php echo $cars['car_name'];?></td>

                     <td><?php echo $cars['car_number'];?></td>

                     <td><?php echo date_time($cars['created_date']);?></td>

                    </tr>

                  <?php } ?> 

                  

                </tbody>

                

              </table>

          </div>

        </div>

        

       

      </div>

      </div>

    </div>

  </div>









                                <!-- Wallet Informations  -->





  <div class="card">

    <div class="card-header" id="Walletinformations">

      <h5 class="mb-0">

        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#Wallets" aria-expanded="false" aria-controls="Wallet">

        Wallet Informations

        </button>

      </h5>

    </div>

    <div id="Wallets" class="collapse" aria-labelledby="Walletinformations" data-parent="#accordion">

      <div class="card-body">

       <div class="col-lg-12">

        <div class="card shadow-sm">

          

         <div class="card-body pt-0">

             <table id="walletinfo" class="table table-bordered table-striped">

                <thead>

                <tr>

                  <th>Sr</th>

                  <th>Amount</th>

                  <th>Status</th>

                  <th>Booking Id</th>

                  <th>Transaction Date</th>

                </tr>

                </thead>

                <tbody>

                 

                  <?php 

                  foreach ($txn_info as $key => $transactions) {

                    if($transactions['status']==1){

                       $status = "<span class='badge badge-success'>Money Added</span>";}

                      else if($transactions['status']==2){

                        $status =  "<span class='badge badge-danger'>Money Deducted</span>";}

                      else{

                         $status = "<span class='badge badge-primary'>Replaced</span>";

                      }

                      if($transactions['booking_id']==0){



                         $booking = "<span class='badge badge-primary'>Payment Gateway</span>";

                      }else{

                       

                        $booking = '<a title="View History" target="_blank" class=""  href='.base_url("admin/complaint/add_complaint/".$transactions['booking_id']).' >'.$transactions['unique_booking_id'].'</a>';

                      }

                   ?>

                    <tr>

                     <td><?php echo $key+1; ?></td>

                     <td><?php echo $transactions['amount'];?></td>

                     <td><?php echo $status;?></td>

                     <td><a href="#" onclick="openbookinginfo('<?php echo $transactions['booking_id']; ?>')"><?php echo $transactions['unique_booking_id'];?></a></td>

                     <td><?php echo date_time($transactions['onUpdated']);?></td>

                    </tr>

                  <?php } ?> 

                  

                </tbody>

                

              </table>

          </div>

        </div>

        

       

      </div>

      </div>

    </div>

  </div>





                                <!-- End Wallet Informations  -->

                  <!--  Started Popup form to get the booking informations -->







    <div class="modal fade" id="bookingInfoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

      <div class="modal-dialog" role="document">

        <div class="modal-content" style="width:150%">

      <div class="modal-header">

         <center><h5 id="booking_text"></h5></center>



        <!-- <center><b><h5 class="modal-title" id="exampleModalLabel">Booking Informations Of  :</h5></b></center> -->

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>



      <div id="complaints_data"></div>     





    

    

    </div>

  </div>

</div>





                  <!--  Ended Popup form to get the booking informations -->









        

    <?php echo form_open(base_url('admin/complaint/registerOtherComp'), 'class="form-horizontal"');  ?>     

      <?php 

      if(!empty($other_complaints_info)){ ?>

        <div> 

        <div class="form-group">



          <input type="hidden" name="complaint_id" value="<?php echo  $other_complaints_info['id'];?>">

          <input type="hidden" name="user_id" value="<?php echo  $other_complaints_info['user_id'];?>">

          <input type="hidden" name="source_type" value="<?php echo  $other_complaints_info['source_type'];?>">



          

          

           <label for="" class="col-md-6 control-label">Complaint Topic</label>

           <textarea class="form-control" name="cc_remark" readonly=""><?php echo $other_complaints_info['complaint_topic'];  ?></textarea>

        </div>

        <div class="form-group">

           <label for="" class="col-md-6 control-label">Problem</label>

            <textarea class="form-control" name="cc_remark" readonly=""><?php echo $other_complaints_info['description'];  ?></textarea>

        </div>

      </div>

      

      <?php }else{?>

        <input type="hidden" name="user_id" value="<?php echo $user_info['id'];?>">





     <?php } ?>                        

       

       <input type="hidden" name="complaint_source" value="<?php echo  $complaint_source;?>">

       <input type="hidden" name="unique_complaint_id" value="<?php echo  $unique_complaint_id;?>">





       







      <div>

         <div class="form-group">

           <label for="" class="col-md-6 control-label">Issues Type</label>

            <select  class="form-control" name="other_complaint_type" required="" 

            <?php 

            if(!empty($comp_info['fk_disposition_id'])){

            if($comp_info['fk_disposition_id']==2 )

              { echo "disabled"; }

          }

             ?>>





              <option value="">Select Complaints</option>

                        <?php foreach($complaint_master as $data): ?>



                        <?php if(!empty($comp_info['complaint_type_id'])){ ?>

                        <option 

                        <?php if($comp_info['complaint_type_id']==$data['id']){ echo "selected";} ?>

                         value="<?= $data['id']; ?>"><?= $data['descriptions'];?></option>



                       <?php } else { ?>

                         <option value="<?= $data['id']; ?>"><?= $data['descriptions'];?></option>

                        <?php }?>

                        <?php endforeach; ?>

            </select>

        </div>





        <div class="form-group">

           <label for="" class="col-md-6 control-label">Problem Descriptions</label>

          

           <?php if(!empty($comp_info['problem_description'])){?>

           <textarea class="form-control" name="problem_description" required="" 

            <?php   if($comp_info['fk_disposition_id']==2 )

              { echo "disabled"; } ?>

            ><?php echo $comp_info['problem_description'];  ?></textarea>

         <?php } else {?>

             <textarea class="form-control" name="problem_description" required=""></textarea>

         <?php }?>

        </div>





        <div class="form-group">

           <label for="" class="col-md-6 control-label">Ticket Status</label>

            <select  class="form-control" name="dispostion_id" required=""   <?php 

            if(!empty($comp_info['fk_disposition_id'])){

            if($comp_info['fk_disposition_id']==2 )

              { echo "disabled"; }

          }

             ?>>



              <?php if(!empty($comp_info['fk_disposition_id'])){ ?>

              <option <?php if($comp_info['fk_disposition_id']==1){ echo "selected";} ?> value="1">Pending</option>

              <option <?php if($comp_info['fk_disposition_id']==2){ echo "selected";} ?> value="2">Closed</option>



            <?php }else{?>

              <option value="1">Pending</option>

              <option value="2">Closed</option>

             <?php } ?>

             

            </select>

        </div>

         <div class="form-group">

           <label for="" class="col-md-6 control-label">Remark By Customer Care</label>

           <?php if(!empty($comp_info['cc_remark'])) { ?>

            <textarea class="form-control" name="cc_remark" <?php

               if($comp_info['fk_disposition_id']==2 )

              { echo "disabled"; }



             ?>  ><?php echo $comp_info['cc_remark']; ?></textarea>

          <?php }else { ?>

               <textarea class="form-control" name="cc_remark"></textarea>

          <?php } ?>

        </div>





        <?php 

          if(!empty($comp_info))

          {

            if($comp_info['fk_disposition_id']!=2){ ?>



          <div class="form-group">

            <div class="col-md-6">

              <input type="submit" name="submit" value="Add Complaint" class="btn btn-primary pull-right">

            </div>

          </div>



           <?php }

          } else{ ?>

             <div class="form-group">

            <div class="col-md-6">

              <input type="submit" name="submit" value="Add Complaint" class="btn btn-primary pull-right">

            </div>

         </div>



        <?php  }

        ?>

       

         <?php echo form_close(); ?>

      </div>

</div>



               </div>

            </div>

          </div>  

        </div>

      </div>

    </section> 

  </div>



</body>

</html>

<script src="sweetalert.min.js"></script>

<script src="<?= base_url()?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>

<script>

  $(function () {

    $("#carinfo").DataTable();

    $('#walletinfo').DataTable();

  });

</script>



<script>

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

    

    function endcall(ab){

        if(confirm("are you sure to end the call.")){
        $("#callstarted").hide();
        localStorage.setItem("callstarted", "0");
        var ab;
        $.post(ab,
    {
       '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
    });

    }
    }



    function verifier_status(){

      var id = $("#verifier_remark").val();

      if(id==3){

        $('#textual_remark').show();

        }else{

          

          $('#other_remark').val('');

          $('#textual_remark').hide();

        }

    }



    function openbookinginfo(id){

      var complaint_id = id;

      $.post('<?=base_url("admin/complaint/checkComplaintsById")?>',

      {

      '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',

      complaint_id : complaint_id,

      },

    function(data){

      var data = $.parseJSON(data);

      var booking_infos = data.booking_info;

      $("#bookingInfoModal").modal("toggle");





          var html = "";

          html +='<br>';

          html +='<div class="card-body pt-0">';

          html +='<table class="table table-bordered">';



          html +='<tr>';

          html +='<th width="15%">Booking Id</th>';

          html +='<td width="25%">'+booking_infos.unique_booking_id+'</td>';

          html +='<th width="15%">Place Info</th>';

          html +='<td width="25%">'+booking_infos.placename+'</td>';

          html +='<th width="15%">Display Id </th>';

          html +='<td width="25%">'+booking_infos.display_id+'</td>';

        

          html +='</tr>';

          html +='<tr>';

          html +='<th width="15%">Slot Name</th>';

          html +='<td width="25%">'+booking_infos.slot_name+'</td>';

          html +='<th width="15%">CAR Number</th>';

          html +='<td width="25%">'+booking_infos.car_number+'</td>';

          html +='<th width="15%">Booking Type</th>';

          html +='<td width="25%">Daily</td>'; 

          html +='</tr>';





          html +='<tr>';

          html +='<th width="15%">Booking From </th>';

          html +='<td width="25%">'+booking_infos.booking_from_date+'  '+booking_infos.from_time+'</td>';

          html +='<th width="15%">Booking To </th>';

          html +='<td width="25%">'+booking_infos.booking_to_date+'  '+booking_infos.to_time+'</td>';

          html +='<th width="15%">Cost</th>';

          html +='<td width="25%">'+booking_infos.cost+'</td>';

          html +='</tr>';



          html +='</table>';

          html +='<div class="modal-footer">';

          html +='<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';

          html +='</div>';

          html +='</div>';

          

             var booking_info = "Complaints History of Booking Id : " +booking_infos.unique_booking_id ;

             $('#booking_text').text(booking_info);

             $("#complaints_data").html(html);





   });





    }

</script>