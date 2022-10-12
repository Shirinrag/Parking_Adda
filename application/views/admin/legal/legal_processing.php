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
          <h3 class="card-title"><i class="fa fa-list"></i>&nbsp;Legal Process</h3>
        </div>
        
      </div>
    </div>


    <div class="card">
<div class="card-body table-responsive">
        <table id="legal_datatable" class="table table-bordered table-striped" width="100%">
          <thead>
            <tr>
              <th>#Id</th>
              <th>Place Details</th>
              <th>Vendor</th>
              <th>Slots</th>
              <th>Forward</th>
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
  //---------------------------------------------------
  var table = $('#legal_datatable').DataTable( {
    "processing": true,
    "serverSide": false,
    "ajax": "<?=base_url('admin/parking/legal_datatable_json')?>",
    "order": [[0,'asc']],
    "columnDefs": [
    { "targets": 0, "name": "id", 'searchable':true, 'orderable':true},
    { "targets": 1, "name": "placename", 'searchable':true, 'orderable':true},
    { "targets": 2, "name": "vendor_name", 'searchable':true, 'orderable':true},
    { "targets": 3, "name": "no_of_slots", 'searchable':true, 'orderable':true},
    { "targets": 4, "name": "view", 'searchable':true, 'orderable':true}
    
    ]
  });
</script>

