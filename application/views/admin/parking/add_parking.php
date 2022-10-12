 <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/select2/css/select2.min.css">

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Add Parking Place</h3>

          
          </div>
          <!-- /.card-header -->
           <?php $this->load->view('admin/includes/_messages.php') ?>
           <?php echo form_open(base_url('admin/parking/addplaces'), 'class="form-horizontal"');  ?>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Vendors </label>
                    <select name="vendor_id" id="vendor_id" class="form-control select2" >
                    <option value="">Select Vendors</option>
                    <?php foreach($vendors as $data): ?>
                          <option value="<?= $data['id']; ?>"><?= $data['name']; ?></option>
                      <?php endforeach; ?>
                    </select>
                </div>
              
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Country</label>
                   <select name="country_id" class="form-control select2" id="country_id"   onchange="getStates(this);">
                    <option value="">Select Country</option>
                    <?php foreach($country as $data): ?>
                      
                           
                                <option value="<?= $data['id']; ?>" 
                                <?php
                                if($data['id']=='101'){
                                    echo "selected='true'";
                                }else
                                {
                                    echo ""; 
                                }
                                ?>><?= $data['name']; ?></option>
                            
                        
                          
                      <?php endforeach; ?>
                    </select>
                </div>
              
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>State</label>
                  <select class="form-control select2" id="state_id" name="state_id" style="width: 100%;" onchange="getCity(this);">
                   <option>Select State</option>
                  </select>
                </div>
              
              </div>


              <div class="col-md-6">
                <div class="form-group">
                  <label>City</label>
                  <select class="form-control select2" style="width: 100%;" id="city_id" name="city_id">
                       <option>Select City</option>
                   
                  </select>
                </div>
              
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label>Place Name</label>
                  <textarea class="form-control" name="place_name" placeholder="Place Name"></textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Place Address</label>
                  <textarea class="form-control"  name="place_address" placeholder="Enter Place Address"></textarea>
                 
                </div>
              </div>

            <div class="col-md-6">
                <div class="form-group">
                  <label>Pin Code</label>
                 <input type="number"  class="form-control" name="pincode" placeholder="Enter Pincode">
                </div>
              </div>



              <div class="col-md-6">
                <div class="form-group">
                  <label>Number of Slots</label>
                  <input type="number" class="form-control" name="slots_counts" id="" placeholder="Number Of Slots">
                </div>
              </div>
            



             
   
            </div><br>
            <div class="col-md-6">
              <div class="form-group">
                    <div class="col-md-12">
                      <input type="submit" name="submit" value="Add Place" class="btn btn-primary pull-right">
                    </div>
                  </div>
              </div>

          </div>

             <?php echo form_close(); ?>
      
        </div>
     
      </div>
      <!-- /.container-fluid -->
    </section>
  </div>
<script src="<?= base_url() ?>assets/plugins/select2/js/select2.full.min.js"></script>
  <script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()
});


 window.onload = function() {
  var country_id = $('#country_id').val();
  getStates(country_id);
};

 function getStates(id){
   var country_id = $('#country_id').val();
   var postData = {
            '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
            'country_id' : country_id}
            $.post('<?=base_url('admin/parking/getStates')?>',postData,function(data){
            var states = $.parseJSON(data);
            
           
            $('#state_id').html('');
            $('#select2-state_id-container').html('No Records');
            if(states.length>0){
                 $('#select2-state_id-container').html('Select State');
                 $.each(states,function(i,val){
                html += '<option value="'+val.id+'">'+val.name+'</option>';    
            })

            }else{
                var html = '<option value="">No Records</option>';
            }
            $('#state_id').html(html);
        })
     
}

 function getCity(id){
   var stateId = $('#state_id').val();
   var postData = {
            '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
            'state_id' : stateId}
            $.post('<?=base_url('admin/parking/getCity')?>',postData,function(data){
            var cities = $.parseJSON(data);
            
           
            $('#city_id').html('');
            $('#select2-city_id-container').html('No Records');
            if(cities.length>0){
                 $('#select2-city_id-container').html('Select City');
                 $.each(cities,function(i,val){
                html += '<option value="'+val.id+'">'+val.name+'</option>';    
            })

            }else{
                var html = '<option value="">No Records</option>';
            }
            $('#city_id').html(html);
        })
   
     
}










</script>