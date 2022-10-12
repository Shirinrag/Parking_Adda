
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
          <div class="card-header">
            <center><h2  class="card-title">Ground & Enginners Team</h2></center>
          </div>
        </div>
        <?php $this->load->view('admin/includes/_messages.php') ?>

   <div class="accordion" id="accordionExample">
  <div class="card">
    <div class="card-header" id="headingOne">
      <h2 class="mb-0">
        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
         Slots Managament
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
  <?php 
  if($slot_info!='0'){?>
  <div class="card">
    <div class="card-header" id="headingTwo">
      <h2 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          Machine Installation 
        </button>
      </h2>
    </div>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
      <div class="card-body">
        <div class="card-body table-responsive">
       <?php echo form_open(base_url('admin/ground/machineInstallation/'.$legalInfo['id']), 'class="form-horizontal"' )?> 
         <table id="example2" class="table table-bordered table-hover">
                <thead>
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
                  <input type="hidden"  class="form-control" name="slot_name[]" value="<?php echo $value['slot_name'];?>" required="" readonly>
                  <td><?php echo $value['display_id'];?></td>
                  <td><?php echo $value['latitude'];?></td>
                  <td><?php echo $value['longitude'];?></td>
                  <td>
                    <?php if($value['device_id']==''){?>
                    <input type="text"  class="form-control" name="machine_id[]" placeholder="Enter Machine Id"  required=""><?php }else{?>
                      <?php echo $value['device_id'];}?>
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

  
</div>






          <!-- /.card-header -->
        
        
          
        </div>
      </div>
    </section>
  </div>
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>
<script src="<?= base_url() ?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>

