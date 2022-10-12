
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Update Legal Info</h3>
          </div>
          <!-- /.card-header -->
           <?php $this->load->view('admin/includes/_messages.php') ?>
          <?php echo form_open(base_url('admin/legal/updateData/'.$legalInfo['id']), 'class="form-horizontal"' )?> 


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
                  <textarea class="form-control" name="place_name" placeholder="Place Name"><?php  echo $legalInfo['placename'];?></textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Place Address</label>
                  <textarea class="form-control"  name="place_address" placeholder="Enter Place Address"><?php  echo $legalInfo['place_address'];?></textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Pin Code</label>
                 <input type="number"  class="form-control" name="pincode" placeholder="Enter Pincode" value="<?php echo $legalInfo['pincode'];?>">
                </div>
              </div>
              
            </div><br>

            <?php if(($legalInfo['phase_status'])!='1'){?>
            <div class="col-md-6">
              <div class="form-group">
                    <div class="col-md-12">
                      <input type="submit" name="forward" value="Forward" class="btn btn-success pull-right" >
                    </div>
                     <div class="col-md-12">
                      <input type="submit" name="submit" value="Update" class="btn btn-primary pull-right"  style="margin-right: 27px;">
                    </div>
               </div> &nbsp;&nbsp;&nbsp;
              </div>
            <?php } ?>
          </div>
             <?php echo form_close(); ?>
        </div>
      </div>
    </section>
  </div>

