<?php 

// echo "<pre>";
// print_r($this->session->userdata());
// die;

?>

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

    <?php $this->load->view('admin/includes/complaints'); ?>



    <section class="content">

      <div class="card card-default color-palette-bo">

        <div class="card-header">

          <div class="d-inline-block"><h5></b>Compaint History Of Booking Id-<b><?php echo $booking_info['unique_booking_id']; ?></b> </h5>

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

            <h3 class="mb-0"></i>General Information</h3>

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

                 <a href="#" onclick="makecall('<?php echo $mob; ?>')" title="Start Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/start.png" style="height: 39px;margin-left:7%"></a>

                 &nbsp;&nbsp;&nbsp;&nbsp;

                 <a href="#" onclick="endcall('<?php echo $mob1; ?>')" title="End Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/hangup.png" style="height: 20px;margin-left:50%"></a>

                </td>

              </tr>

              <tr>

                <th width="5%">Address</th>

                <td width="15%"><?php echo $user_info['address']; ?></td>

              </tr>

                

            <tr>

                <th width="5%">Registartion Date</th>

                <td width="15%"><?php echo date("d-m-Y H:i A", strtotime($user_info['created_at'])); ?></td>

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

  <div class="card">

    <div class="card-header" id="headingTwo">

      <h5 class="mb-0">

        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">

         Booking History

        </button>

      </h5>

    </div>

    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">

      <div class="card-body">

       <div class="col-lg-12">

        <div class="card shadow-sm">

       

          <div class="card-body pt-0">

            <table class="table table-bordered">

                

              <tr>

                <th width="15%">Booking Id</th>

                <td width="25%"><?php echo $booking_info['unique_booking_id']; ?></td>

                 <th width="15%">Slot Name</th>

                <td width="25%">

                <?php

                if(!empty($ReplacementsData)){

                    foreach($ReplacementsData as $keys =>$data){

                        if($keys==0){

                            

                            echo "Current :&nbsp;&nbsp;"."<span class='badge badge-success'>".$data['slot_name']."</span>"."<br>";

                        

                        }else{

                             echo "Replaced : "."<span class='badge badge-danger'>"."".$data['slot_name']."</span>"."<br>";

                             

                        }

                       

                    }

                }else{

                     echo " &nbsp;&nbsp;"."<span class='badge badge-success'>".$booking_info['slot_name']."</span>"."<br>";

                }

             

                

                ?>

                </td>

                <th width="25%">Display Id </th>

                <td width="25%"><?php

                if(!empty($ReplacementsData)){

                    foreach($ReplacementsData as $keys =>$data){

                        if($keys==0){

                            

                            echo "<span class='badge badge-success'>".$data['display_id']."</span>"."<br>";

                        

                        }else{

                             echo "<span class='badge badge-danger'>"."".$data['display_id']."</span>"."<br>";

                             

                        }

                       

                    }

                }else{

                    echo " &nbsp;&nbsp;"."<span class='badge badge-success'>".$booking_info['display_id']."</span>"."<br>";

                }

             

                

                ?></td>

                

                

                

              </tr>

              <tr>

                <th width="15%">CAR Details</th>

                <td width="25%"><?php echo $booking_info['car_name']; ?></td>

                <th width="15%">CAR Number</th>

                <td width="25%"><?php echo $booking_info['car_number']; ?></td>

                <th width="15%">Booking Type</th>

                <td width="25%"><?php if(($booking_info['booking_type'])==0)echo "Daily";else echo "PASS"; ?></td>

                

              </tr>

              

              <tr>

                 <th width="15%">Place Name</th>

                <td width="25%"><?php echo $booking_info['placename']; ?></td>

                <th width="15%">Place Address</th>

                <td width="25%"><?php echo $booking_info['place_address']; ?></td>

                

              </tr>      

              

              <tr>

                <th width="15%">Booking From </th>

                <td width="25%"><?php

                     $booking_from =  $booking_info['booking_from_date'].$booking_info['from_time'];

                     $new_date = date("d-m-Y h:i A", strtotime($booking_from));

                echo $new_date; ?></td>

                <th width="15%">Booking To </th>

                <td width="25%"><?php

                     $booking_to =  $booking_info['booking_to_date'].$booking_info['to_time'];

                     $booking_to_date = date("d-m-Y h:i A", strtotime($booking_to));

                echo $booking_to_date; ?></td>

                <th width="15%">Cost</th>

                <td width="25%"><?php echo $booking_info['cost']; ?></td>

               

              </tr>

            

              

             

            

            </table>

          </div>

        </div>

        

       

      </div>

      </div>

    </div>

  </div>



 <!--  Verifiers List -->



   <div class="card">

    <div class="card-header" id="verifiers_list">

      <h5 class="mb-0">

        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwos" aria-expanded="false" aria-controls="collapseTwos">

         Verifiers List

        </button>

      </h5>

    </div>

    <div id="collapseTwos" class="collapse" aria-labelledby="verifiers_list" data-parent="#accordion">

      <div class="card-body">

       <div class="col-lg-12">

        <div class="card shadow-sm">

          

         <div class="card-body pt-0">

              <table id="example2" class="table table-bordered table-hover">

                <thead>

                <tr>

                  <th>Sr</th>

                  <th>Verifier Name</th>

                  <th>Place</th>

                  <th>Address</th>

                  <th>Contact</th>

                  <th>Action</th>

                </tr>

                </thead>

                <tbody>

                  <?php
                   $var = $this->session->userdata('call_start_url');
                   $hang_variable = $this->session->userdata('call_hangup_url');
                  foreach ($verifier_list as $key => $verifier) {
                    $mobile = $var.$verifier['mobile_no'];
                    $mobile1 = $hang_variable;
                   ?>

                 <tr>

                  <td><?php echo $key+1; ?></td>

                  <td><?php echo $verifier['firstname']." ".$verifier['lastname']; ?></td>

                  <td><?php echo $verifier['placename'];?></td>

                  <td><?php echo $verifier['place_address']?></td>

                  <td><?php echo $verifier['mobile_no'];?></td>

                  <td>  <a href="#" onclick="makecall('<?php echo $mobile; ?>')" title="Start Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/start.png" style="height: 28px;margin-left:7%"></a>

                 &nbsp;&nbsp;&nbsp;&nbsp;

                 <a href="#" onclick="endcall('<?php echo $mobile1; ?>')" title="End Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/hangup.png" style="height: 15px;"></a></td>

                </tr>

                  <?php }

                   ?>

               

                </tbody>

              

              </table>

          </div>

        </div>

        

       

      </div>

      </div>

    </div>

  </div>



 <!-- Verifier List End -->

 

 

 

 

 

 <!--Enforcers Start-->

 



   <div class="card">

    <div class="card-header" id="enforcers_list">

      <h5 class="mb-0">

        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseEnfo" aria-expanded="true" aria-controls="collapseEnfo">

         Enforcers List

        </button>

      </h5>

    </div>

    <div id="collapseEnfo" class="collapse" aria-labelledby="enforcers_list" data-parent="#accordion">

      <div class="card-body">

       <div class="col-lg-12">

        <div class="card shadow-sm">

          

         <div class="card-body pt-0">

              <table id="example2" class="table table-bordered table-hover">

                <thead>

                <tr>

                  <th>Sr</th>

                  <th>Enforcers Name</th>

                  <th>Contact</th>

                  <th>Action</th>

                </tr>

                </thead>

                <tbody>

                  <?php

                   $var = $this->session->userdata('call_start_url');
                   $hang_variable = $this->session->userdata('call_hangup_url');
                  foreach ($enforcers_list as $key => $enforcers) {

                    $mobile = $var.$enforcers['mobile_no'];

                    $mobile1 = $hang_variable;



                   ?>

                 <tr>

                  <td><?php echo $key+1; ?></td>

                  <td><?php echo $enforcers['firstname']." ".$enforcers['lastname']; ?></td>

                  <td><?php echo $enforcers['mobile_no'];?></td>

                  <td>  <a href="#" onclick="makecall('<?php echo $mobile; ?>')" title="Start Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/start.png" style="height: 28px;margin-left:7%"></a>

                 &nbsp;&nbsp;&nbsp;&nbsp;

                 <a href="#" onclick="endcall('<?php echo $mobile1; ?>')" title="End Call"><img src="https://easycloud.net.in/Parking_Adda/uploads/icons/hangup.png" style="height: 15px;"></a></td>

                </tr>

                  <?php }

                   ?>

               

                </tbody>

              

              </table>

          </div>

        </div>

        

       

      </div>

      </div>

    </div>

  </div>



 <!-- Verifier List End -->

 

 

 <!--Enforcers End-->

  <div class="card">

    <div class="card-header" id="headingThree">

      <h5 class="mb-0">

        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">

         Wallet Informations

        </button>

      </h5>

    </div>

    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">

      <div class="card-body">

          <div class="card-body">

              <table id="example2" class="table table-bordered table-hover">

                <thead>

                <tr>

                  <th>Sr.</th>

                  <th>Txn. Id</th>

                  <th>Amount</th>

                  <th>Transaction Type</th>

                  <th>Date</th>

                </tr>

                </thead>

                <tbody>

                <tr>

                    

                  <?php

                    foreach ($wallet_info as $key => $wallet_data) {

                      if($wallet_data['status']==1){

                        $status =  "Added";}else if($wallet_data['status']==2){

                           $status = "Deducted";}else{

                              $status = "Replaced";

                            }

                      $new_date = date("d-m-Y h:i:s A", strtotime($wallet_data['onCreated']));

                    ?>

                      <tr>

                        <td><?php echo $key+1;?></td>

                        <td><?php echo $wallet_data['transac_id'];?></td>

                        <td><?php echo $wallet_data['amount'];?></td>

                        <td><?php echo $status;?></td>

                        <td><?php echo $new_date;?></td>



                      </tr>

                     

                    <?php }

                   ?>

                 

                </tbody>

              

              </table>

            </div>

    </div>

  </div>



</div>





                                    <!-- Start Form From Here -->

    

              <?php echo form_open(base_url('admin/complaint/registerBookingComp'), 'class="form-horizontal"');  ?>     

               <div class="form-group">



                    <?php 

                    if(!empty($complaints_info)){?>

                        <input type="hidden" name="unique_booking_id" value="<?php echo $complaints_info['id']; ?>">

                      <?php }

                    ?>    

                     <label for="" class="col-md-6 control-label">Select Verifier</label>

                    <div class="col-md-6">

                      <select name="verifier_id" id="verifier_id" class="form-control" required="" <?php if  

                      ($complaints_info['status']==1) echo "disabled='true'" ?>>

                         <option value="">Select Verifier</option>

                        <?php foreach($verifier_list as $data): ?>

                          <?php

                             if(!empty($complaints_info) && $complaints_info['verifier_id']!=''){?>

                             <option value="<?= $data['verifier_id']; ?>" 

                              <?php 

                              if($data['verifier_id']==$complaints_info['verifier_id']){

                                echo "selected";

                              }

                              ?>

                              ><?= $data['firstname']." ".$data['lastname']; ?></option>

                             <?php }else{

                           ?>

                          <option value="<?= $data['verifier_id']; ?>"><?= $data['firstname']." ".$data['lastname']; ?></option>

                        <?php }?>

                        <?php endforeach; ?>

                    </select>

                      <input type="hidden" name="booking_id" value="<?php echo $booking_info['booking_id'];?>">

                    </div>

                </div>

                   <div class="form-group">

                     <label for="" class="col-md-6 control-label">Complaints Type</label>

                    <div class="col-md-6">

                         <select name="complaint_id" id="complaint_id" class="form-control" required=""  <?php if  

                      ($complaints_info['status']==1) echo "disabled='true'" ?>>

                         <option value="">Select Complaints</option>

                        <?php foreach($complaints_master as $data): ?>

                          <?php

                          if(!empty($complaints_info) && $complaints_info['complaint_id']!=""){ ?>

                            <option value="<?= $data['id']; ?>" 

                              <?php 

                              if($data['id']==$complaints_info['complaint_id']){

                                echo "selected";

                              }

                              ?>



                              ><?= $data['descriptions'];?></option>

                          <?php }else{ ?>

                              <option value="<?= $data['id']; ?>"><?= $data['descriptions'];?></option>

                          <?php } 



                           ?>

                        <?php endforeach; ?>

                      </select>

                      <input type="hidden" name="booking_id" value="<?php echo $booking_info['booking_id'];?>">

                    </div>

                </div>

                 <div class="form-group">

                     <label for="" class="col-md-6 control-label">Action Type</label>

                    <div class="col-md-6">

                         <select name="verifier_action" id="verifier_action" class="form-control" required="" <?php if  

                      ($complaints_info['status']==1) echo "disabled='true'" ?> >

                         <option value="">Select Actions</option>

                        <?php foreach($despositions as $data): ?>

                          <?php 

                          if(!empty($complaints_info) && $complaints_info['fk_despostion_id']!=""){?>

                            <option value="<?= $data['id']; ?>" 

                              <?php 

                              if($complaints_info['fk_despostion_id']==$data['id']){

                                echo "selected";

                              }

                              ?>

                              ><?= $data['descriptions']; ?></option>

                         <?php }else{?>

                             <option value="<?= $data['id']; ?>"><?= $data['descriptions']; ?></option>

                         <?php }

                          ?>

                        <?php endforeach; ?>

                      </select>

                    </div>

                </div>

                <div class="form-group">

                    <label for="" class="col-md-6 control-label">Dispostions Type</label>

                    <div class="col-md-6">

                       <select name="dispositions_id"  id="dispositions_id" class="form-control" required="" <?php if  

                      ($complaints_info['status']==1) echo "disabled='true'" ?>>

                        <?php 





                        if((!empty($complaints_info)) && $complaints_info['status']!=""){

                          

                        ?>

                        <option value="">Select Dispostions</option>

                        <option value="0"  <?php if($complaints_info['status']==0 || $complaints_info['status']==2){echo "selected";}?>>Pending</option>

                        <option value="1" <?php if($complaints_info['status']==1){echo "selected";}?>>Closed</option>

                        <?php 

                        }

                        else {?>

                        <option value="">Select Dispostions</option>

                        <option value="0" >Pending</option>

                        <option value="1" >Closed</option>

                      <?php }?>

                      </select>

                    </div>

                </div>

                <div class="form-group">

                    <label for="" class="col-md-6 control-label">Remark By Customer Care</label>

                    <?php



                     if(!empty($complaints_info) && $complaints_info['customercareRemark']!=""){?>

                    <div class="col-md-6">

                      <textarea class="form-control" name="cc_remark" <?php if  

                      ($complaints_info['status']==1) echo "readonly" ?> ><?php echo $complaints_info['customercareRemark']; ?></textarea>

                    </div>

                  <?php }else{ ?>

                    <div class="col-md-6">

                      <textarea class="form-control" name="cc_remark">  </textarea>

                    </div>

                  <?php }?>

                </div>

                <div class="form-group" <?php if(!empty($complaints_info) && $complaints_info['status']==1) echo "style='display:none'" ?>>

                    <div class="col-md-6">

                      <input type="submit" name="submit" value="Submit" class="btn btn-primary pull-right">

                    </div>

                </div>

                <?php echo form_close(); ?>





                 <!-- End Form From Here -->

              </div>

            </div>

          </div>  

        </div>

      </div>

    </section> 

  </div>



</body>

</html>



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

</script>