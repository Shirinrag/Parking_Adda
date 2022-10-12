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

          <h3 class="card-title"><i class="fa fa-list"></i>&nbsp; <?= ('Slot list') ?></h3>

        </div>

        <div class="d-inline-block float-right">

          <?php if($this->rbac->check_operation_permission('add')): ?>

            <a href="<?= base_url('admin/users/add'); ?>" class="btn btn-success"><i class="fa fa-plus"></i> <?= trans('add_new_user') ?></a>

          <?php endif; ?>

        </div>

      </div>

    </div>

    <div class="card">

      <div class="card-body table-responsive">

        <table id="na_datatable" class="table table-bordered table-striped" width="100%">

          <thead>

            <tr>

              <th><?= trans('id') ?></th>

              <th style="width:30%">Place Info</th>

              <th>State</th>

              <th>City</th>

              <th style="width:20%">Slot Info</th>

              <th style="width:10%">Machine Id</th>

              <th width="100" class="text-right"><?= trans('action') ?></th>

              

            </tr>

          </thead>

        </table>

      </div>

    </div>

  </section>  

</div>





<!-- DataTables -->

<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>

<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>



<script>

  //---------------------------------------------------

  var table = $('#na_datatable').DataTable( {

    "processing": true,

    "serverSide": false,

    "ajax": "<?=base_url('admin/slots/datatable_json')?>",

    "order": [[0,'asc']],

    "columnDefs": [

    { "targets": 0, "name": "id", 'searchable':true, 'orderable':true},

    { "targets": 1, "name": "placename", 'searchable':true, 'orderable':true},

    { "targets": 2, "name": "state_name", 'searchable':true, 'orderable':true},

    { "targets": 3, "name": "city_name", 'searchable':false, 'orderable':false},

    { "targets": 4, "name": "slot_name", 'searchable':true, 'orderable':true},

    { "targets": 5, "name": "machine_id", 'searchable':true, 'orderable':true},

    { "targets": 6, "name": "Action", 'searchable':false, 'orderable':false,'width':'100px'}

    ]

  });

</script>





<script type="text/javascript">

  $("body").on("change",".tgl_checkbox",function(){

    console.log('checked');

    $.post('<?=base_url("admin/slots/change_status")?>',

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





