
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
          <div class="card-header">
            <center><h2  class="card-title">Operations Team</h2></center>
          </div>
        </div>
        <?php $this->load->view('admin/includes/_messages.php') ?>

   <div class="accordion" id="accordionExample">
  <div class="card">
    <div class="card-header" id="headingOne">
      <h2 class="mb-0">
        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
         Slots Details
        </button>
      </h2>
    </div>


    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
      <div class="card-body">
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
            </div>
          
          </div>
      </div>
    </div>
  </div>
  <?php 
  if($slot_info!='0'){?>
  <div class="card">
    <div class="card-header" id="headingTwo">
      <h2 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          Machine Details 
        </button>
      </h2>
    </div>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
      <div class="card-body">
        <div class="card-body table-responsive">
      
         <table id="example2" class="table table-bordered table-hover">
                <thead style="background-color:burlywood">
                <tr>
                  <th>Id</th>
                  <th>Slot Name</th>
                  <th>Display Id</th>
                  <th>Latitude</th>
                  <th>Longitude</th>
                  <th>Machine Id</th>
                </tr>
                </thead>
                <tbody>
               
                  <?php 
                  foreach ($slot_details as $key => $value) {
                    ?>
                <tr>
                  <td><?php echo $key+1;?></td>
                  <td><?php echo $value['slot_name'];?></td>
                  <td><?php echo $value['display_id'];?></td>
                  <td><?php echo $value['latitude'];?></td>
                  <td><?php echo $value['longitude'];?></td>
                  <td><?php echo $value['device_id'];?></td>
                </tr>
                  <?php }
                  ?>
                  
                </tr>
                </tfoot>
              </table>
      </div>
      </div>
    </div>
  </div>
  <?php }?>

  
  <div class="card">
    <div class="card-header" id="headingThree">
      <h2 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
          Pricing Management
        </button>
      </h2>
    </div>
    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
      <div class="card-body">
      <div class="card-body table-responsive">
       
      
<center>      



<!-- Extention Managements  -->
<?php

  if($legalInfo['ext_per']=='0' || $legalInfo['ext_per']==''){
 ?>
  <div class="form-check" class="col-md-4" >
  <?php echo form_open(base_url('admin/operation/addExtentions/'.$legalInfo['id']), 'class="form-horizontal"' )?>
  <input type="text"  class="form-control" name="extention_charges" placeholder="Extention %" required="" onkeypress="return digitKeyOnly(event)" style="width:30%"><br>

     <div class="col-md-4" >
          <input type="submit" name="submit" value="submit" class="btn btn-warning pull-right" style="margin-right: 44%;" >
    </div>


 <?php echo form_close(); ?>
</div>
<!-- Extention Managements  End-->
<?php }else{ ?>
<div class="form-check" >
  <input class="form-check-input" type="radio" name="hourly" id="hourly"  onclick="callme('1')"  >
  <label class="form-check-label" >
    <b>Hourly</b>
  </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <input class="form-check-input" type="radio" name="hourly" id="daily" onclick="callme('2')">
  <label class="form-check-label" >
   <b> Slab Price</b>
  </label>
  
  
  <hr>
          <div class="card-body" id="slab_wise" style="display:none">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                    <select name="slab_type" id="slab_type" class="form-control select2" onchange="SlabType()" style="margin-left:103%" >
                    <option value="">Select Slab Type</option>
                          <option value="Daily">Daily</option>
                          <option value="Pass">Pass</option>
                    </select>
                </div>
              </div>
             </div> 
           </div>

                 <!-- Daily Price Managements -->

            <div id="daily_price_slots" style="display:none">
               <center><h5><b>Daily Slab Prices</b></h5></center><hr>

            <?php echo form_open(base_url('admin/operation/addDailyPrices/'.$legalInfo['id']), 'class="form-horizontal"' )?> 

            <?php
              if(count($daily_price_info)==0){
             ?>
              <table  class="table table-bordered table-hover" >
                <thead style="background-color:burlywood">
                <tr>
                  <th>Hours</th>
                  <th>Daily</th>
                </tr>
                </thead>
              <tbody>
              <tr>
              <td>
                <b>1</b>
                <input type="hidden"  class="form-control" name="hours[]" placeholder="Daily" value="1" readonly=""></td><td><input type="text"  class="form-control" name="price[]" placeholder="Price" required="" ></td>  
              </tr>
              <tr>
              <td>
                <b>3</b>
                <input type="hidden"  class="form-control" name="hours[]" placeholder="Daily" value="3" readonly=""></td><td><input type="text"  class="form-control" name="price[]" placeholder="price" required="" ></td>  
              </tr>
              <tr>
              <td>
                <b>6</b>
                <input type="hidden"  class="form-control" name="hours[]" placeholder="Daily" value="6" readonly=""></td><td><input type="text"  class="form-control" name="price[]" placeholder="price" required=""></td>  
              </tr>
              <tr>
              <td>
                <b>9</b>
                <input type="hidden"  class="form-control" name="hours[]" placeholder="Daily" value="9" readonly=""></td><td><input type="text"  class="form-control" name="price[]" placeholder="price" required="" ></td>  
              </tr>
              <tr>
              <td>
                <b>12</b>
                <input type="hidden"  class="form-control" name="hours[]" placeholder="Daily" value="12" readonly=""></td><td><input type="text"  class="form-control" name="price[]" placeholder="price" required="" ></td>  
              </tr>
              <tr>
              <td>
                <b>24</b>
                <input type="hidden"  class="form-control" name="hours[]" placeholder="Daily" value="24" readonly=""></td><td><input type="text"  class="form-control" name="price[]" placeholder="price"  required=""></td>  
              </tr>
              </tbody>
              </table>
               <div class="form-group">
                    <div class="col-md-12" >
                      <input type="submit" name="submit" value="submit" class="btn btn-warning pull-right" style="margin-right: 44%;" >
                    </div>
               </div> 
               
            
           <?php }else{?>
              <table  class="table table-bordered table-hover" >
                <thead style="background-color:burlywood">
                <tr>
                  <th>Hours</th>
                  <th>Daily</th>
                </tr>
                </thead>
              <tbody>   
                <?php foreach ($daily_price_info as $key => $value) {?>
                  <tr>
                  <td><?php echo  $value['hrs'];?></td>
                  <td><?php echo  $value['cost'];?></td>
                   </tr>
                <?php } ?>
              </tbody>
              </table>
           <?php }?>
            </div>
            <?php echo form_close(); ?>

            <!-- Daily Price Managements Ended-->




            
                       <!--   Monthly Price Managements Started -->

             <div id="pass_slots" style="display:none">
              <center><h5><b>Monthly/Weekly Pass Prices</b></h5></center><hr>
               <table  class="table table-bordered table-hover" >
                <thead style="background-color:burlywood">
                <tr>
                  <th>Hours</th>
                  <th>Weekly</th>
                  <th>Monthly</th>
                </tr>
                </thead>

                <?php if (count($weekly_price_info)==0 && count($monthly_price_info)==0){?>
                <tbody>
                 <?php echo form_open(base_url('admin/operation/addPrices/'.$legalInfo['id']), 'class="form-horizontal"' )?> 
                <tr>
                  <td><b>1<b>
                  <input type="hidden"  class="form-control" name="hours[]" placeholder="Daily" value="1">
                  </td>
                  <td><input type="text"  class="form-control" name="weekly_price[]" placeholder="Weekly" onkeypress="return digitKeyOnly(event)" required=""></td></td>
                  <td><input type="text"  class="form-control" name="monthly_price[]" placeholder="Monthly" onkeypress="return digitKeyOnly(event)" required=""></td></td>       
                </tr>
                <tr>
                  <td><b>3<b>
                  <input type="hidden"  class="form-control" name="hours[]" placeholder="Daily" value="3" required=""></td>
                  <td><input type="text"  class="form-control" name="weekly_price[]" placeholder="Weekly" onkeypress="return digitKeyOnly(event)" required=""></td></td>
                  <td><input type="text"  class="form-control" name="monthly_price[]" placeholder="Monthly" onkeypress="return digitKeyOnly(event)" required=""></td></td>       
                </tr>
                <tr>
                 
                  <td><b>6<b>
                  <input type="hidden"  class="form-control" name="hours[]" placeholder="Daily" value="6" required=""></td>
                  <td><input type="text"  class="form-control" name="weekly_price[]" placeholder="Weekly" onkeypress="return digitKeyOnly(event)" required=""></td></td>
                  <td><input type="text"  class="form-control" name="monthly_price[]" placeholder="Monthly" onkeypress="return digitKeyOnly(event)" required=""></td></td>       
                </tr>
                <tr>
                  <td><b>9<b>
                  <input type="hidden"  class="form-control" name="hours[]" placeholder="Daily" value="9" required="">
                </td>
                  <td><input type="text"  class="form-control" name="weekly_price[]" placeholder="Weekly" onkeypress="return digitKeyOnly(event)" required=""></td></td>
                  <td><input type="text"  class="form-control" name="monthly_price[]" placeholder="Monthly" onkeypress="return digitKeyOnly(event)" required=""></td></td>       
                </tr>
                <tr>
               
                  <td><b>12<b>
                  <input type="hidden"  class="form-control" name="hours[]" placeholder="Daily" value="12" required="">
                  </td>
                  <td><input type="text"  class="form-control" name="weekly_price[]" placeholder="Weekly"onkeypress="return digitKeyOnly(event)"  required=""></td></td>
                  <td><input type="text"  class="form-control" name="monthly_price[]" placeholder="Monthly" onkeypress="return digitKeyOnly(event)"  required=""></td></td>       
                </tr>
                <tr>
                 
                  <td><b>24<b>
                  <input type="hidden"  class="form-control" name="hours[]" placeholder="Daily" value="24" required="">
                  </td>
                  <td><input type="text"  class="form-control" name="weekly_price[]" placeholder="Weekly" onkeypress="return digitKeyOnly(event)" required=""></td></td>
                  <td><input type="text"  class="form-control" name="monthly_price[]" placeholder="Monthly" onkeypress="return digitKeyOnly(event)" required=""></td></td>       
                </tr>
              </tbody>

            <?php }else{ ?>
                 <tr>
                  <td>1</td>
                  <td><?php echo $weekly_price_info[0]['cost']; ?></td>
                  <td><?php echo $monthly_price_info[0]['cost']; ?></td>
                </tr>
               
                <tr>
                  <td>3</td>
                  <td><?php echo $weekly_price_info[1]['cost']; ?></td>
                  <td><?php echo $monthly_price_info[1]['cost']; ?></td>
                </tr>
                <tr>
                  <td>6</td>
                  <td><?php echo $weekly_price_info[2]['cost']; ?></td>
                  <td><?php echo $monthly_price_info[2]['cost']; ?></td>
                </tr>
                <tr>
                  <td>9</td>
                  <td><?php echo $weekly_price_info[3]['cost']; ?></td>
                  <td><?php echo $monthly_price_info[3]['cost']; ?></td>
                </tr>
                <tr>
                  <td>12</td>
                  <td><?php echo $weekly_price_info[4]['cost']; ?></td>
                  <td><?php echo $monthly_price_info[4]['cost']; ?></td>
                </tr>
                <tr>
                  <td>24</td>
                  <td><?php echo $weekly_price_info[5]['cost']; ?></td>
                  <td><?php echo $monthly_price_info[5]['cost']; ?></td>
                </tr>
                <?php }?>
              </table>
              <br><br>
              <?php if(count($weekly_price_info)==0 && count($monthly_price_info)==0){?>
               <div class="form-group">
                    <div class="col-md-12" >
                      <input type="submit" name="submit" value="submit" class="btn btn-warning pull-right" style="margin-right: 44%;" >
                    </div>
               </div> 
             <?php }?>
               <?php echo form_close(); ?>
            </div>
            </div>
            </div>
              
       <?php }?>       
                      <!--   Monthly Price Managements Ended -->
              
  
                              <!-- Hourly Price Managements -->



<div class="modal fade" id="hourly_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!--Header-->
      <div class="modal-header">
        <center><h4 class="modal-title" id="myModalLabel">Hourly Price</h4></center>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <!--Body-->
      <div class="modal-body">
        <?php echo form_open(base_url('admin/operation/addHourlyPrices/'.$legalInfo['id']), 'class="form-horizontal"' )?>
            <?php
            if(count($hourly)==0){
            ?>
             <div class="col-md-6">
                <label style="display: inline-block;padding-right:23px;">Hours Price </label>
                <div class="form-group">
                 <input type="hidden"  class="form-control" name="pass_type" value="0">
                 <input type="hidden"  class="form-control" name="ext_per" value="<?php echo $legalInfo['ext_per'] ?>">
                 <input type="text"  class="form-control" name="per_hour_price" placeholder="Enter Per Hours Price"  value="" onkeypress="return digitKeyOnly(event)" required>
                </div>
             </div>
              <div class="modal-footer">
        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
        <input type="submit" name="submit" value="submit" class="btn btn-warning pull-right">
      </div>

           <?php }else{?>

             <table  class="table table-bordered table-hover" >
                <thead style="background-color:burlywood">
                <tr>
                  <th>Hours</th>
                  <th>Cost</th>
                  <th>Type</th>
                 
                </tr>
                </thead>

               
                <tbody>
                <tr>
                  <td><?php echo $hourly[0]['hrs'];  ?></td>
                  <td><?php echo $hourly[0]['cost']; ?></td>
                  <td>Per Hour</td>
                 
                </tr>
              </tbody>
            </table>
          <?php }?>

        
      </div>
      
      <?php echo form_close(); ?>
    </div>
  </div>
</div>



                          <!-- Hourly Price Managements End -->

</div></center>
      </div>
    </div>
  </div>
</div>

        </div>
      </div>
    </section>
  </div>
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>
<script src="<?= base_url() ?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>
<script>



function callme(type){
    if(type==1){
        $('#hourly_modal').modal('show');
        $("#slab_wise").hide();
    }else if(type==2)
    {
          $("#slab_wise").show();
    }

}

function SlabType()
{
    var type = $('#slab_type').val();
    
    if(type=='Pass'){
        $("#pass_slots").show();
        $("#daily_price_slots").hide();
    }else{
        $("#pass_slots").hide();
        $("#daily_price_slots").show();
        
    }
}
  function digitKeyOnly(e) {
  var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
  var value = Number(e.target.value + e.key) || 0;

  if ((keyCode >= 37 && keyCode <= 40) || (keyCode == 8 || keyCode == 9 || keyCode == 13) || (keyCode >= 48 && keyCode <= 57)) {
    return true;
  }
  return false;
}



</script>