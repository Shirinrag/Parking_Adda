<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css"> 
<div class="content-wrapper">
  <section class="content">
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
        <table id="legal_datatable" class="table table-bordered table-striped" width="100%">
          <thead>
            <tr>
              <th>#Id</th>
              <th>Place Name</th>
              <th>Address</th>
              <th>Vendor</th>
              <th>Slots</th>
              <th>Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </section>  
</div>

<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>
<script src="<?= base_url() ?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>
<script>
  var table = $('#legal_datatable').DataTable( {
    "processing": true,
    "serverSide": false,
    "ajax": "<?=base_url('admin/operation/operations_datatable_json')?>",
    "order": [[0,'desc']],
    "columnDefs": [
    { "targets": 0, "name": "id", 'searchable':true, 'orderable':true},
    { "targets": 1, "name": "placename", 'searchable':true, 'orderable':true},
    { "targets": 2, "name": "place_address", 'searchable':true, 'orderable':true},
    { "targets": 3, "name": "vendor_name", 'searchable':true, 'orderable':true},
    { "targets": 4, "name": "no_of_slots", 'searchable':true, 'orderable':true},
    { "targets": 5, "name": "view", 'searchable':true, 'orderable':true}
    
    ]
  });
</script>
