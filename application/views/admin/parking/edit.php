 <style>
  .pm{
    padding-left: 20%;
    padding-right: 20%;
    font-size: 20px;
  }
</style>

  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

    <!-- Main content -->

    <section class="content">

      <div class="container-fluid">

        <!-- SELECT2 EXAMPLE -->

        <div class="card card-default">

          <div class="card-header">

            <center><h2  class="card-title">Update Parking Places</h2></center>

          </div>

        </div>

        <?php $this->load->view('admin/includes/_messages.php') ?>



   <div class="accordion" id="accordionExample">
    <div class="card">
    <div class="card-header" id="headingOne">
      <h2 class="mb-0">
        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          Place Managements
        </button>
      </h2>
    </div>

    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
      <div class="card-body">
          <?php echo form_open(base_url('admin/ground/genrateSlots/'.$legalInfo['id']), 'class="form-horizontal"' )?> 
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Vendor Name </label>
                    <input type="text" class="form-control" placeholder="Vendor Name" value="<?php echo $legalInfo['vendor_name']; ?>" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Country</label>
                 <input type="text" class="form-control" placeholder="Country" value="<?php echo $legalInfo['country_name']; ?>" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>State</label>
                   <input type="text" class="form-control" placeholder="State" value="<?php echo $legalInfo['state_name']; ?>" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>City</label>
                 <input type="text" class="form-control" placeholder="City" value="<?php echo $legalInfo['city_name']; ?>" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Place Name</label>
                  <textarea class="form-control" placeholder="Place Name" readonly=""><?php  echo $legalInfo['placename'];?></textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Place Address</label>
                  <textarea class="form-control"  placeholder="Enter Place Address"  readonly=""><?php  echo $legalInfo['place_address'];?></textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Pin Code</label>
                 <input type="number"  class="form-control"  placeholder="Enter Pincode"  readonly="" value="<?php echo $legalInfo['pincode'];?>">
                </div>
              </div>
            <input type="hidden"  class="form-control" name="prefix" value="<?php echo $legalInfo['prefix'];?>" required="">
              <div class="col-md-6">
                <div class="form-group">
                 <label>Number of Slots</label>
                 <?php 
                 if($slot_info!='0'){?>
                 <input type="number"  class="form-control" name="slots_counts" placeholder="Number of Slots"   value="<?php echo $legalInfo['no_of_slots'];?>" required="" readonly="">
               <?php }else{?>
                 <input type="number"  class="form-control" name="slots_counts" placeholder="Number of Slots"   value="" required="">
               <?php }?>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Latitude</label>
                  <?php if($slot_info!='0'){?>
                 <input type="text"  class="form-control" name="latitude" placeholder="Enter Latitude"  value="<?php echo $legalInfo['latitude'];?>" readonly="">  <?php }else{?>
                   <input type="text"  class="form-control" name="latitude" placeholder="Enter Latitude"  value="" required="">
                    <?php }?>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Longitude</label>
                   <?php if($slot_info!='0'){?>
                  <input type="text"  class="form-control" name="longitude" readonly
                  value="<?php echo $legalInfo['longitude'];?>" ><?php }else{?>
                  <input type="text"  class="form-control" name="longitude" placeholder="Enter Longitude"   value="" required="">
                      <?php }?>
                </div>
              </div>              
            </div><br>
               <?php if($slot_info=='0'){?>
            <div class="col-md-6">
              <div class="form-group">
                    <div class="col-md-12">
                      <input type="submit" name="submit" value="Genrate Slots" class="btn btn-warning pull-right" >
                    </div>
               </div> 
              </div>
            <?php }?>
          </div>
       <?php echo form_close(); ?>
      </div>
    </div>
  </div>



  <!-- Device Managements Update Devices  -->

<div class="modal fade" id="device_update" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"> 
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update Device Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="" class="col-form-label">Device Id</label>
            <input type="text" class="form-control" id="device_id" readonly="">
          </div>
           <div class="form-group">
            <label for="" class="col-form-label">Enter New Device Id</label>
            <input type="text" class="form-control" id="new_device_id" onkeyup="checkdevice()">
          </div>
            <span id="error"></span>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Reasons:</label>
            <textarea class="form-control" id="reasons" placeholder="Reasons For Replacement" required=""> </textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Update</button>
      </div>
    </div>
  </div>
</div>







                        <!-- Price Managements Modal -->

<div class="modal fade" id="price_update" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"> 
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update Price Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="" class="col-form-label">Hours</label>
            <input type="text" class="form-control" id="hours" readonly="">
          </div>
           <div class="form-group">
            <label for="" class="col-form-label">Cost</label>
            <input type="text" class="form-control" id="costs">
          </div>
            
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Update</button>
      </div>
    </div>
  </div>
</div>

                        <!-- Ended Pricing Managements Modal -->


  <?php 
  if($slot_info!='0'){?>
  <div class="card">
    <div class="card-header" id="headingTwo">
      <h2 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          Device Managment
          </button>
      </h2>
    </div>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
      <div class="card-body">
        <div class="card-body table-responsive">
       <?php echo form_open(base_url('admin/ground/machineInstallation/'.$legalInfo['id']), 'class="form-horizontal"' )?> 
         <table id="example2" class="table table-bordered table-hover">
                <thead style="background-color:burlywood">
                <tr>
                  <th>Id</th>
                  <th>Slot Name</th>
                  <th>Display Id</th>
                  <th>Installation Date</th>
                  <th>Machine Id</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  <?php 
                  foreach ($slot_details as $key => $value) {
                      $date = date("d-m-Y", strtotime($value['created_date']));
                    ?>
                <tr>
                  <td><?php echo $key+1;?></td>
                  <td><?php echo $value['slot_name'];?></td>
                  <input type="hidden"  class="form-control" name="slot_name[]" value="<?php echo $value['slot_name'];?>" required="" readonly>
                  <td><?php echo $value['display_id'];?></td>
                  <td><?php echo $date ;?></td>
                  <td>
                    <?php if($value['device_id']==''){?>
                    <input type="text"  class="form-control" name="machine_id[]" placeholder="Enter Machine Id"  required=""><?php }else{?>
                      <?php echo $value['device_id'];}?>
                  </td>
                  <td>
                    <!--<a title="Edit" class="update btn btn-sm btn-warning" onclick="callme(<?php echo $value['device_id'] ?>)"><i class="fa fa-pencil-square-o"></i></a>-->
                    <a href="<?= base_url("admin/parking/deactiveSlot/".$value['slot_no']."/".$legalInfo['id']); ?>" onclick="return confirm('are you sure to deactive this slot ?')" class="delete btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></a>
                  </td>
                </tr>
                  <?php }
                  ?>
                </tr>
                </tfoot>
              </table>
            <?php if(($slot_details[0]['machine_id']==0 ) AND $slot_details[0]['stages_id']!=3){?>
            <div class="col-md-6">
              <div class="form-group">
                    <div class="col-md-12">
                      <input type="submit" name="submit" value="submit" class="btn btn-warning pull-right" >
                    </div>
               </div> 
              </div> 
              <?php }?>
              <?php echo form_close(); ?>
      </div>
      </div>
    </div>
  </div>
  <?php }?>
<!-- End Device Managements -->






        <!-- Started Pricing Managemnets From Here -->


    


<div class="card">
    <div class="card-header" id="headingThree">
      <h2 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
          Pricing Managment
          </button>
      </h2>
    </div>
    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
      <div class="card-body">
        <div class="card-body table-responsive">

        <center>
        <div>
          <!--<button type="button" onclick="checkType('1')" class="btn btn-primary btn-rounded" style="margin-top: 2px;background-color: #2c2f56;">Extentions</button>&nbsp;&nbsp;&nbsp;-->
          <button type="button" onclick="checkType('2')" class="btn btn-primary btn-rounded" style="margin-top: 2px;background-color: #2c2f56;">Hourly</button>&nbsp;&nbsp;&nbsp;
          <button  type="button" onclick="checkType('3')" class="btn btn-primary btn-rounded" style="margin-top: 2px;background-color: #2c2f56;">Daily</button>&nbsp;&nbsp;&nbsp;
          <button type="button" onclick="checkType('4')" class="btn btn-primary btn-rounded" style="margin-top: 2px;background-color: #2c2f56;">Passes</button>&nbsp;&nbsp;&nbsp;
        </div>
        </center>
          <br>

        

           <!--<div id="extentionManagement" style="display: none;">-->
           <!--   <div class="container pm" >-->
           <!--     <center><h4><b>Extention % Managements</b></h4></center>-->
           <!--       <table id="example2" class="table table-bordered table-hover">-->
           <!--     <thead style="background-color:burlywood">-->
           <!--     <tr>-->
           <!--       <th>Extetntion %</th>-->
           <!--       <th>Created Date</th>-->
           <!--       <th>Action</th>-->
           <!--     </tr>-->
           <!--     </thead>-->
           <!--    <tbody>-->
                 
           <!--     <tr>-->
           <!--       <td>1</td>-->
           <!--       <td>2</td>-->
           <!--       <td>-->
           <!--         <a title="Edit" class="update btn btn-sm btn-warning" onclick="callme()"><i class="fa fa-pencil-square-o"></i></a>-->
           <!--         <a href="" onclick="return confirm('are you sure to deactive this slot ?')" class="delete btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></a>-->
           <!--       </td>-->
           <!--     </tr>-->
               
           <!--     </tr>-->
           <!--     </tfoot>-->
           <!--   </table>-->
           <!--   </div>-->
           <!--   </div>-->
           <!-- </div>-->


         <!--  Ended Extention Management Division -->


         <!-- Started Hourly Price Managements -->


          <div id="hourlyPriceManagement" style="display: none;">
            <div class="container pm" >
            <center><h4><b>Hourly Price Managements</b></h4></center>
              <table id="example2" class="table table-bordered table-hover">
                <thead style="background-color:burlywood">
                <tr>
                  <th>Hours</th>
                  <th>Cost</th>
                  <th>Created Date</th>
                  <th>Action</th>
                </tr>
                </thead>
               <tbody>
                  <?php 
                  foreach ($hourly as $key => $value) {
                      $date = date("d-m-Y h:i A" , strtotime($value['onCreated']));
                    ?>
                <tr>
                  <td><?php echo $value['hrs'];?></td>
                  <td><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $value['cost'];?></td>
                  <td><?php echo $date ;?></td>
                  <td>
                    <a title="Edit" class="update btn btn-sm btn-warning" onclick="priceModal(<?php echo $value['id'] ?>,<?php echo $legalInfo['id'];?>,'1')"><i class="fa fa-pencil-square-o"></i></a>
                    <a href="<?= base_url("admin/parking/deactivePrice/".$value['id']."/".$legalInfo['id']); ?>" onclick="return confirm('are you sure to deactive this slot ?')" class="delete btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></a>
                  </td>
                </tr>
                  <?php }
                  ?>
                </tr>
                </tfoot>
              </table>
                <?php
                if(empty($hourly)) { ?>
                  <center><h4>Data Not Available..</h4></center>
                <?php } ?>

              </div>
            </div>
            </div>





         <!-- Ended Hourly Price Managements -->
         <!-- Started Daily Price Slab  -->
         <div id="dailyPriceManagement" style="display: none;">
            <div class="container pm" >
              <center><h4><b>Daily Price Managements</b></h4></center>
              <table id="example2" class="table table-bordered table-hover">
                <thead style="background-color:burlywood">
                <tr>
                  <th>Hours</th>
                  <th>Cost</th>
                  <th>Created Date</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  <?php 
                  foreach ($daily as $key => $value) {
                      $date = date("d-m-Y h:i A" , strtotime($value['onCreated']));
                    ?>
                <tr>
                  <td><?php echo $value['hrs'];?></td>
                  <td><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $value['cost'];?></td>
                  <td><?php echo $date ;?></td>
                  <td>
                    <a title="Edit" class="update btn btn-sm btn-warning" onclick="priceModal(<?php echo $value['id'] ?>,<?php echo $legalInfo['id'];?>,'2')"><i class="fa fa-pencil-square-o"></i></a>
                    <a href="<?= base_url("admin/parking/deactivePrice/".$value['id']."/".$legalInfo['id']); ?>" onclick="return confirm('are you sure to deactive this slot ?')" class="delete btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></a>
                  </td>
                </tr>
                  <?php }
                  ?>
                </tr>
               
              </table>
              
              <?php
                if(empty($daily)) { ?>
                  <center><h4>Data Not Available..</h4></center>
                <?php } ?>
               
             
                
              </div>
            </div>
            </div>
          <!-- Ended Daily Price Slab -->
          <!-- Started Passes Price Managements -->

          <div id="PassessPriceManagement" style="display: none;">
            <div class="container pm" >
              <center><h4><b>Passes Price Managements</b></h4></center>
              <table id="example2" class="table table-bordered table-hover">
                <thead style="background-color:burlywood">
                <tr>
                  <th>Hours</th>
                  <th>Cost</th>
                  <th>Created Date</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  <?php 
                  foreach ($passess as $key => $value) {
                      $date = date("d-m-Y", strtotime($value['onCreated']));
                    ?>
                <tr>
                  <td><?php echo $value['hrs'];?></td>
                  <td><?php echo $value['cost'];?></td>
                  <td><?php echo $date ;?></td>
                  <td>
                    <a title="Edit" class="update btn btn-sm btn-warning" onclick="priceModal(<?php echo $value['id'] ?>,<?php echo $legalInfo['id'];?>,'3')"><i class="fa fa-pencil-square-o"></i></a>
                    <a href="<?= base_url("admin/parking/deactivePrice/".$value['id']); ?>" onclick="return confirm('are you sure to deactive this slot ?')" class="delete btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></a>
                  </td>
                </tr>
                  <?php }  ?>
                 
                </tr>
                </tfoot>
              </table>

               <?php
                if(empty($passess)) { ?>
                  <center><h4>Data Not Available..</h4></center>
                <?php } ?>



              </div>
            </div>
            </div>



          <!-- Ended PLasses Price Managements -->




      </div>
      </div>
    </div>
  </div>
          <!-- /.card-header -->
        </div>
      </div>
    </section>
  </div>
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>
<script src="<?= base_url() ?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>
<script>

  function callme(id){
  $('#device_id').val(id);
  $('#device_update').modal('show');
  }
  function checkdevice(){
    var new_device_id = $('#new_device_id').val();
    $.post('<?=base_url("admin/parking/Replace_Device")?>',
    {
     '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
      new_device_id : new_device_id
    },
    function(data){
       var arr = $.parseJSON(data);
       var device_status = arr.status;
       if(device_status=='0')
       {
        error.textContent = "Device Not Exist";
        error.style.color = "red";
       }else if(device_status=='1'){
         error.textContent = "Device Already Mapped."
         error.style.color = "red"
       }else{
         error.textContent = "Device Available"
         error.style.color = "green"
       }
    }
    );
  }



    function checkType(id){

      if(id==1){
        $("#extentionManagement").show();
        $("#dailyPriceManagement").hide();
        $("#hourlyPriceManagement").hide();
        $("#PassessPriceManagement").hide();
      }else if(id==2){
        $("#dailyPriceManagement").hide();
        $("#extentionManagement").hide();
        $("#hourlyPriceManagement").show();
        $("#PassessPriceManagement").hide();
      }else if(id==3){
        $("#extentionManagement").hide();
        $("#dailyPriceManagement").show();
        $("#hourlyPriceManagement").hide();
        $("#PassessPriceManagement").hide();
      }else{
        $("#extentionManagement").hide();
        $("#dailyPriceManagement").hide();
        $("#hourlyPriceManagement").hide();
        $("#PassessPriceManagement").show();
      }
     
    }

  function priceModal(id,place_id,type){
    $('#price_update').modal('show');
     $.post('<?=base_url("admin/parking/getPriceInfo")?>',
    {
     '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
      'place_id' : place_id,'slab_id':id,'type':type
    },
    function(data){
       var arr = $.parseJSON(data);
         $('#hours').val(arr.hrs);
         $('#costs').val(arr.cost);
         $('#unique_id').val(arr.id);
         
    }
    );
  }
  function UpdatePrice(){

    var hrs = $('#hours').val();
    var costs = $('#costs').val();
    var id = $('#unique_id').val();
    

    $.ajax({
            url:"<?=base_url("admin/parking/UpdatePrices")?>",
            data:{'hrs' : hrs,'costs':costs,'unique_id':id,'<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'},
            method:"POST",
            success: function(data, textStatus, xhr) {
              

              },
            complete: function(xhr, textStatus) {
            location.reload();
            }   
        });





  }




</script>