<link rel="stylesheet" href="<?= base_url()?>assets/plugins/datatables/dataTables.bootstrap4.css">


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Device List</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Device Management</a></li>
              <li class="breadcrumb-item active">Device List</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">All Data</h3>
            </div>
            <!-- /.card-header -->
           <div class="card">

      <div class="card-body table-responsive">
        <table id="na_datatable" class="table table-bordered table-striped" width="100%">
          <thead>
            <tr>
              <th>#Id</th>
              <th>Place Name</th>
              <th>Slot Name</th>
              <th>Display Id</th>
              <th>Device ID</th>
              <th>Created Date</th>
              <th>Device Status</th>
              <th></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


<!-- DataTables -->
<script src="<?= base_url()?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>

<script>
  //---------------------------------------------------
  var table = $('#na_datatable').DataTable( {
    "processing": true,
    "serverSide": false,
    "ajax": "<?=base_url('admin/Ground/datatable_json')?>",
    "order": [[0,'DESC']],
    "columnDefs": [
    { "targets": 0, "name": "id",'searchable':true, 'orderable':true},
    { "targets": 1, "name": "placename",'searchable':true, 'orderable':true},
    { "targets": 2, "name": "slot_name",'searchable':true, 'orderable':true},
    { "targets": 3, "name": "display_id", 'searchable':true, 'orderable':true},
    { "targets": 4, "name": "device_id", 'searchable':true, 'orderable':true},
    { "targets": 5, "name": "onCreated",  'searchable':true, 'orderable':true},
    { "targets": 6, "name": "device_id", 'searchable':true, 'orderable':true},
    { "targets": 7, "name": "device_id", 'searchable':true, 'orderable':true},


    ]
  });
  
  
  
  $("body").on("change",".tgl_checkbox",function(){
    console.log('checked');
    $.post('<?=base_url("admin/ground/update_status")?>',
    {
      '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
      id : $(this).data('id'),
      status : $(this).is(':checked') == true?1:0
    },
    function(data){
      $.notify("Status Changed Successfully", "success");
      location.reload();
    });
  });

</script>
</body>
</html>
