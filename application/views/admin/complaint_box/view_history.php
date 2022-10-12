<!DOCTYPE html>
<html>
   <head>
      <title></title>
      s
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
      <section class="content">
         <div class="card card-default color-palette-bo">
            <div class="card-header">
               <div class="d-inline-block">
                  <h5>Compaint History Of Booking Id-
                     <?php echo $booking_info[0]['unique_booking_id']; ?>
                  </h5>
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
                                    <button class="btn btn-link" data-toggle="collapse"
                                       data-target="#collapseOne" aria-expanded="true"
                                       aria-controls="collapseOne">
                                    User Informations
                                    </button>
                                 </h5>
                              </div>
                              <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                 data-parent="#accordion">
                                 <div class="student-profile py-4">
                                    <div class="container">
                                       <div class="row">
                                          <div class="col-lg-4">
                                             <div class="card shadow-sm">
                                                <div class="card-header bg-transparent text-center">
                                                   <img class="profile_img"
                                                      src="<?php echo $user_info['image']; ?>"
                                                      alt="profile">
                                                   <h3>
                                                      <?php echo $user_info['firstname'].' '.$user_info['lastname']; ?>
                                                   </h3>
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
                                                         <td width="15%">
                                                            <?php echo $user_info['email']; ?>
                                                         </td>
                                                      </tr>
                                                      <tr>
                                                         <th width="5%">Contact</th>
                                                         <?php


                                                            $mob = $this->session->userdata('call_start_url').$user_info['mobile_no'];
                                                            $hang_var= $this->session->userdata('call_hangup_url');
                                                            $mob1 = $hang_var;

                                                          
                                                            
                                                            ?>
                                                         <td width="15%">
                                                            <?php echo $user_info['mobile_no']; ?>
                                                            <a href="#"
                                                               onclick="makecall('<?php echo $mob; ?>')"
                                                               title="Start Call"><img
                                                               src="https://easycloud.net.in/Parking_Adda/uploads/icons/start.png"
                                                               style="height: 39px;margin-left:7%"></a>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                                            <a href="#"
                                                               onclick="endcall('<?php echo $mob1; ?>')"
                                                               title="End Call"><img
                                                               src="https://easycloud.net.in/Parking_Adda/uploads/icons/hangup.png"
                                                               style="height: 20px;margin-left:50%"></a>
                                                         </td>
                                                      </tr>
                                                      <tr>
                                                         <th width="5%">Address</th>
                                                         <td width="15%">
                                                            <?php echo $user_info['address']; ?>
                                                         </td>
                                                      </tr>
                                                      <tr>
                                                         <th width="5%">Registartion Date</th>
                                                         <td width="15%">
                                                            <?php echo date("d-m-Y H:i A", strtotime($user_info['created_at'])); ?>
                                                         </td>
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
                                    <button class="btn btn-link collapsed" data-toggle="collapse"
                                       data-target="#collapseTwo" aria-expanded="false"
                                       aria-controls="collapseTwo">
                                    Booking History
                                    </button>
                                 </h5>
                              </div>
                              <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                 data-parent="#accordion">
                                 <div class="card-body">
                                    <div class="col-lg-12">
                                       <div class="card shadow-sm">
                                          <div class="card-body pt-0">
                                             <table class="table table-bordered">
                                                <tr>
                                                   <th width="15%">Booking Id</th>
                                                   <td width="25%">
                                                      <?php echo $booking_info[0]['unique_booking_id']; ?>
                                                   </td>
                                                   <th width="15%">Slot Name</th>
                                                   <td width="25%">
                                                      <?php
                                                         if(!empty($booking_info[0]['replacement_data'])){
                                                         
                                                             foreach($booking_info[0]['replacement_data'] as $keys =>$data){
                                                         
                                                                 if($keys==0){
                                                         
                                                                     
                                                         
                                                                     echo "Current :&nbsp;&nbsp;"."<span class='badge badge-success'>".$data['slot_name']."</span>"."<br>";
                                                         
                                                                 
                                                         
                                                                 }else{
                                                         
                                                                      echo "Replaced : "."<span class='badge badge-danger'>"."".$data['slot_name']."</span>"."<br>";
                                                         
                                                                      
                                                         
                                                                 }
                                                         
                                                                
                                                         
                                                             }
                                                         
                                                         }else{
                                                         
                                                              echo " &nbsp;&nbsp;"."<span class='badge badge-success'>".$booking_info[0]['slot_name']."</span>"."<br>";
                                                         
                                                         }
                                                         
                                                         
                                                         
                                                         
                                                         
                                                         ?>
                                                   </td>
                                                   <th width="25%">Display Id </th>
                                                   <td width="25%">
                                                      <?php
                                                         if(!empty($booking_info[0]['replacement_data'])){
                                                         
                                                             foreach($booking_info[0]['replacement_data'] as $keys =>$data){
                                                         
                                                                 if($keys==0){
                                                         
                                                                     
                                                         
                                                                     echo "<span class='badge badge-success'>".$data['display_id']."</span>"."<br>";
                                                         
                                                                 
                                                         
                                                                 }else{
                                                         
                                                                      echo "<span class='badge badge-danger'>"."".$data['display_id']."</span>"."<br>";
                                                         
                                                                      
                                                         
                                                                 }
                                                         
                                                                
                                                         
                                                             }
                                                         
                                                         }else{
                                                         
                                                             echo " &nbsp;&nbsp;"."<span class='badge badge-success'>".$booking_info[0]['display_id']."</span>"."<br>";
                                                         
                                                         }
                                                         
                                                         
                                                         
                                                         
                                                         
                                                         ?>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th width="15%">CAR Details</th>
                                                   <td width="25%">
                                                      <?php echo $booking_info[0]['car_name']; ?>
                                                   </td>
                                                   <th width="15%">CAR Number</th>
                                                   <td width="25%">
                                                      <?php echo $booking_info[0]['car_number']; ?>
                                                   </td>
                                                   <th width="15%">Booking Type</th>
                                                   <td width="25%">
                                                      <?php if(($booking_info[0]['booking_type'])==0)echo "Daily";else echo "PASS"; ?>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th width="15%">Place Info</th>
                                                   <td width="25%">
                                                      <?php echo $booking_info[0]['place_address']; ?>
                                                   </td>
                                                   <th width="15%">Verifier Name</th>
                                                   <td width="35%">
                                                      <?php echo $booking_info[0]['verifier_name']."<br>"; ?>
                                                      <center><b>
                                                         <?php 
                                                            $var = "https://192.168.1.21/API/click2call.php?user=bds18&phone=";
                                                            
                                                            $mob = $var.$booking_info[0]['verifier_contact'];
                                                            
                                                            $hang_var= "https://192.168.1.21/agc/api.php?source=PARKINGADDA&user=bds18&pass=bds18&agent_user=bds18&function=external_hangup&value=1";
                                                            
                                                            $mob1 = $hang_var;
                                                            
                                                            
                                                            
                                                            ?>
                                                         <?php echo $booking_info[0]['verifier_contact']."<br>"; ?>
                                                         </b>
                                                      </center>
                                                      <a href="#"
                                                         onclick="makecall('<?php echo $mob; ?>')"
                                                         title="Start Call"><img
                                                         src="https://easycloud.net.in/Parking_Adda/uploads/icons/start.png"
                                                         style="height: 39px;margin-left:7%"></a>
                                                      &nbsp;&nbsp;&nbsp;&nbsp;
                                                      <a href="#"
                                                         onclick="endcall('<?php echo $mob1; ?>')"
                                                         title="End Call"><img
                                                         src="https://easycloud.net.in/Parking_Adda/uploads/icons/hangup.png"
                                                         style="height: 20px;margin-left:50%"></a>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th width="15%">Booking From </th>
                                                   <td width="25%">
                                                      <?php
                                                         $booking_from =  $booking_info[0]['booking_from_date'].$booking_info[0]['from_time'];
                                                         
                                                         $new_date = date("d-m-Y h:i A", strtotime($booking_from));
                                                         
                                                         echo $new_date; ?>
                                                   </td>
                                                   <th width="15%">Booking To </th>
                                                   <td width="25%">
                                                      <?php
                                                         $booking_to =  $booking_info[0]['booking_to_date'].$booking_info[0]['to_time'];
                                                         
                                                         $booking_to_date = date("d-m-Y h:i A", strtotime($booking_from));
                                                         
                                                         echo $booking_to_date; ?>
                                                   </td>
                                                   <th width="15%">Cost</th>
                                                   <td width="25%">
                                                      <?php echo $booking_info[0]['cost']; ?>
                                                   </td>
                                                </tr>
                                             </table>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="card">
                              <div class="card-header" id="headingThree">
                                 <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" data-toggle="collapse"
                                       data-target="#collapseThree" aria-expanded="false"
                                       aria-controls="collapseThree">
                                    Wallet Informations
                                    </button>
                                 </h5>
                              </div>
                              <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                 data-parent="#accordion">
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
                                                <td>
                                                   <?php echo $key+1;?>
                                                </td>
                                                <td>
                                                   <?php echo $wallet_data['transac_id'];?>
                                                </td>
                                                <td>
                                                   <?php echo $wallet_data['amount'];?>
                                                </td>
                                                <td>
                                                   <?php echo $status;?>
                                                </td>
                                                <td>
                                                   <?php echo $new_date;?>
                                                </td>
                                             </tr>
                                             <?php }
                                                ?>
                                          </tbody>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <?php echo form_open(base_url('admin/complaint/cc_actions/'.$booking_info[0]['booking_id']), 'class="form-horizontal"' )?>
                           <div class="row">
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <div class="col-md-12">
                                       <center>
                                          <div>
                                             <img src="<?php echo $booking_info[0]['issue_img']; ?>" style="height: 390px;width: 415px;border-radius:15px;" >
                                          </div>
                                       </center>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <label for="" class="col-md-12 control-label">Verifier Resolution
                                    Status</label>
                                    <div class="col-md-8">
                                       <?php
                                          if(($booking_info[0]['vf_status'])==1){
                                          
                                              $status="Replace";}else if($booking_info[0]['vf_status']==2){
                                          
                                                  $status = "Refund";}else if($booking_info[0]['vf_status']==0){ $status = "Pending"; }else{ $status = "Resolved";}
                                          
                                          ?>
                                       <input type="text" name="status_type" class="form-control" id=""
                                          placeholder="" value="<?php echo $status;?>" readonly>
                                       <input type="hidden" name="enf_status" class="form-control"
                                          id="enf_status" placeholder=""
                                          value="<?php echo $booking_info[0]['vf_status'];?>" readonly>
                                       <input type="hidden" name="user_id" class="form-control"
                                          id="user_id" placeholder=""
                                          value="<?php echo $user_info['user_id'];?>" readonly>
                                       <input type="hidden" name="booking_id" class="form-control"
                                          id="booking_id" placeholder=""
                                          value="<?php echo $booking_info[0]['booking_id'];?>" readonly>
                                    </div>
                                    <br>
                                    <label for="username" class="col-md-12 control-label">Issues Type</label>
                                    <div class="col-md-8">
                                       <textarea class="form-control" id="remarks"
                                          readonly><?php echo $booking_info[0]['complaint_text'] ?></textarea>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                       <div class="col-md-8">
                                          <?php
                                             if($booking_info[0]['status']=='1'){?>
                                          <label for="Despostions"
                                             class="col-md-12 control-label">Despostions</label>
                                          <div class="col-md-8">
                                             <input type="text" class="form-control" id="" placeholder=""
                                                value="<?php echo $booking_info[0]['descriptions'] ?>"
                                                readonly>
                                          </div>
                                          <?php }else{
                                             ?>
                                          <label for="Despostions" class="col-md-12 control-label">Select
                                          Despostions</label>
                                          <select name="despositions" class="form-control" required="">
                                             <option value="">Select Despostions</option>
                                             <?php foreach($despositions as $data): ?>
                                             <option value="<?= $data['id']; ?>">
                                                <?= $data['descriptions']; ?>
                                             </option>
                                             <?php endforeach; ?>
                                          </select>
                                          <?php }?>
                                       </div>
                                    </div>
                                    <label for="cc_remarks" class="col-md-6 control-label">Remark By
                                    Customer Care</label>
                                    <div class="col-md-8">
                                       <?php if($booking_info[0]['status']==1){ ?>
                                       <textarea class="form-control" id="cc_remarks" name="cc_remarks"
                                          readonly><?php echo $booking_info[0]['customercareRemark']; ?></textarea>
                                       <?php }else{ ?>
                                       <textarea class="form-control" id="cc_remarks"
                                          name="cc_remarks"></textarea>
                                       <?php }?>
                                    </div>
                                    <br>
                                    <?php if($booking_info[0]['status']==0 || $booking_info[0]['status']=='2'){ ?>
                                    <div class="form-group">
                                       <div class="col-md-6">
                                          <input type="submit" name="submit" value="Submit"
                                             class="btn btn-primary pull-right">
                                       </div>
                                    </div>
                                    <?php } ?>
                                 </div>
                              </div>
                              <?php  echo form_close(); ?>
                           </div>
                           </form>
                           <!-- /.box-body -->
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
   function makecall(ab) {
   
   
   
       var data = localStorage.getItem("callstarted");
   
       if (data != '' && data == '1') {
   
           $.notify("Please End The Previous Call First.", "error");
   
   
   
       } else {
   
           if(confirm("are you sure to start the call.")){
           $("#callstarted").show();
           localStorage.setItem("callstarted", "1");
           var ab;   
           $.post(ab,
   
               {
   
                   '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
   
   
   
               });
        }
   
       }

   
   
   
   
   
   }
   
   
   
   function endcall(ab) {
   
       if(confirm("are you sure to end the call.")){
         
       
   
       $("#callstarted").hide();
   
       localStorage.setItem("callstarted", "0");
   
       var ab;
   
       $.post(ab,
   
           {
   
               '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
   
   
   
           });
   }
   }
   
</script>