<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css"> 

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <section class="content">
    <!-- For Messages -->
    <?php $this->load->view('admin/includes/_messages.php') ?>
    <div class="card">
      <div class="card-header">
        <div class="d-inline-block">
          <h3 class="card-title"><i class="fa fa-list"></i>&nbsp;Parking Places</h3>
        </div>
        
      </div>
    </div>


    <div class="card">
<div class="card-body table-responsive">
        <table id="parking_datatable" class="table table-bordered table-striped" width="100%">
          <thead>
            <tr>
              <th>#Id</th>
              <th>Place Info</th>
              <th>Country</th>
              <th>Vendor</th>
              <th>Booking Type</th>
              <th>Slots</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </section>  
</div>


<!-- Popup Forms  -->



<!-- End -->


<!-- DataTables -->
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>

<script src="<?= base_url() ?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>




<script>
  var table = $('#parking_datatable').DataTable( {
    "processing": true,
    "serverSide": false,
    "ajax": "<?=base_url('admin/parking/parking_datatable')?>",
    "order": [[0,'DESC']],
    "columnDefs": [
    { "targets": 0, "name": "id", 'searchable':true, 'orderable':true},
    { "targets": 1, "name": "placename", 'searchable':true, 'orderable':true},
    { "targets": 2, "name": "country_name", 'searchable':true, 'orderable':true},
    { "targets": 3, "name": "state_name", 'searchable':false, 'orderable':false},
    { "targets": 4, "name": "city_name", 'searchable':true, 'orderable':true},
    { "targets": 5, "name": "pincode", 'searchable':true, 'orderable':true},
    
    
    ]
  });
</script>


<script type="text/javascript">
  $("body").on("change",".tgl_checkbox",function(){
    console.log('checked');
    $.post('<?=base_url("admin/parking/update_status")?>',
    {
      '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
      id : $(this).data('id'),
      status : $(this).is(':checked') == true?1:0
    },
    function(data){
      $.notify("Status Changed Successfully", "success");
    });
  });
</script>



