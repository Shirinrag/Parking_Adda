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
          <h3 class="card-title"><i class="fa fa-list"></i>&nbsp;Enforcers Complaints</h3>
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
        <?php echo form_open("/",'id="advance_search"') ?>
        <div class="row">
          <div class="col-md-4">
            <label>Enforcers Complaint Status</label><hr style="margin:5px 0px;" />
            <input checked="checked" onchange="complaints_filter()" name="complaint_type" value="0" type="radio"  />All&nbsp;&nbsp;&nbsp;
            <input onchange="complaints_filter()" name="complaint_type" value="1" type="radio"  /> Replace &nbsp;&nbsp;&nbsp;
            <input onchange="complaints_filter()" name="complaint_type" value="2" type="radio"  /> Refund &nbsp;&nbsp;&nbsp;
            <input onchange="complaints_filter()" name="complaint_type" value="3" type="radio"  /> Resolved
          </div>
       
        </div>
        <?php echo form_close(); ?>
      </div>
    </div> 
    
    
    <div class="card">
      <div class="card-body table-responsive">
        <table id="na_datatable" class="table table-bordered table-striped" width="200%">
          <thead>
            <tr>
              <th>#Id</th>
              <th>Booking Id</th>
              <th>Place</th>
              <th>Address</th>
              <th>Issues</th>
              <th>From Date</th>
              <th>To Date</th>
              <th>From Time</th>
              <th>To Time</th>
              <th>Slot Name</th>
              <th>Display ID</th>
              <th>Verifier</th>
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



<div class="modal fade" id="action_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <center><h5 class="modal-title" id="exampleModalLabel">Enforcer Actions</h5></center>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Booking Id</label>
            <input type="text" class="form-control" id="booking_id" readonly="" value="">
            <input type="hidden" class="form-control" id="unique_id"  value="">
          </div>

          <div class="form-group">
           <label for="recipient-name" class="col-form-label">Actions</label>
           <select name="status" id="issue_type" class="form-control" >
                    <option value="1">Replace</option>
                    <option value="2">Refund</option>
                    <option value="3">Resolved</option>
           </select>
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Enforcer Remark</label>
            <textarea class="form-control" id="remarks"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" onclick="submit_complaints()" class="btn btn-primary">Send</button>
      </div>
    </div>
  </div>
</div>


<!-- End -->


<!-- DataTables -->
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>

<script>
  //---------------------------------------------------
  var table = $('#na_datatable').DataTable( {
    "processing": true,
    "serverSide": false,
    "ajax": "<?=base_url('admin/complaint/datatable_json')?>",
    "order": [[0,'asc']],
    "columnDefs": [
    { "targets": 0, "name": "id", 'searchable':true, 'orderable':true},
    { "targets": 1, "name": "unique_booking_id", 'searchable':true, 'orderable':true},
    { "targets": 2, "name": "placename", 'searchable':true, 'orderable':true},
    { "targets": 3, "name": "place_address", 'searchable':true, 'orderable':true},
    { "targets": 4, "name": "complaint_text", 'searchable':false, 'orderable':false},
    { "targets": 5, "name": "booking_from_date", 'searchable':true, 'orderable':true},
    { "targets": 6, "name": "booking_to_date", 'searchable':true, 'orderable':true},
    { "targets": 7, "name": "from_time", 'searchable':true, 'orderable':true},
    { "targets": 8, "name": "to_time", 'searchable':true, 'orderable':true},
    { "targets": 9, "name": "slot_name", 'searchable':true, 'orderable':true},
    { "targets": 10, "name": "display_id", 'searchable':true, 'orderable':true},
    { "targets": 11, "name": "verifier_name", 'searchable':true, 'orderable':true},
    { "targets": 12, "name": "status", 'searchable':true, 'orderable':true},
    { "targets": 12, "name": "action", 'searchable':true, 'orderable':true}

     

   
    ]
  });
</script>


<script type="text/javascript">
 

  function actions(id)
  {
    var array = id.split(',');
   var parking_id =array[0];
   var unique_id =array[1];


  $('#booking_id').val(parking_id);
  $('#unique_id').val(unique_id);
  $("#action_popup").modal("toggle");
 }

 
 function submit_complaints()
 {
  var booking_id = $('#booking_id').val();
  var unique_id = $('#unique_id').val();
  var issue_type = $('#issue_type').val();
  var other_remarks = $('#other_remarks').val();
  var enforcers_remark = $('#remarks').val();
  $.post('<?=base_url("admin/complaint/complaints_update")?>',
    {
      '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
    
      issue_type : issue_type,
      enforcers_remark : enforcers_remark,
      unique_id : unique_id,
    },
    function(data){
      $.notify("Status Changed Successfully", "success");
      location.reload();
    });


 }
 
 
  function complaints_filter()
  {
    var _form = $("#advance_search").serialize();
    $.ajax({
      data: _form,
      type: 'post',
      url: '<?php echo base_url();?>admin/Complaint/search',
      async: true,
      success: function(output){
        table.ajax.reload( null, false );
      }
    });
  }
  
  
  
</script>


