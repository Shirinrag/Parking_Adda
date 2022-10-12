
<?php 

// echo "<pre>";
// print_r($other_complaint);
// die;
?>

<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css"> 
<div class="content-wrapper">
  <section class="content">
    <?php $this->load->view('admin/includes/_messages.php') ?>
    
    <div class="card">
      <div class="card-header">
       <center><h4><b>Other Complaints From Call</b></h4></center>
       
      </div>
    </div>
    
    
    <div class="card">
      <div class="card-body table-responsive">
        <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Username</th>
                  <th>Issue Type</th>
                  <th>Complaint</th>
                  <th>Source</th>
                  <th>Issues Raised</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  <?php foreach ($OtherCompFromCalls as $key => $value) {

                    $source = ($value['source_type']==1) ? '<span class="badge badge-primary">User App</span>' : '<span class="badge badge-dark">Call</span>' ;
                    $status = ($value['fk_disposition_id']==1) ? '<span class="badge badge-danger">Pending</span>' : '<span class="badge badge-success">Resolved</span>';
                   ?>
                <tr>

                 <td><?php echo $value['username']; ?></td>
                 <td><?php echo $value['complaint_topic']; ?></td>
                 <td><?php echo $value['description']; ?></td>
                 <td><?php echo $source; ?></td>
                 <td><?php echo $value['created_date']; ?></td>
                 <td><?php echo $status; ?></td>
                  <td><a href="<?php echo base_url('admin/complaint/add_other_complaint/'.$value['id']."/".$value['user_id']."/".$value['source_type']) ?>" title="View History" class="view btn btn-sm btn-warning"   ><i class="fa fa-edit"></i></a></td>

                </tr>

                  
                <?php  } ?>
               
                </tbody>
                <tfoot>
                
                </tfoot>
              </table>
      </div>
    </div>
  </section>  
</div>





<!-- DataTables -->
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>

<script>
  $(function(){
    $("#example1").DataTable();
    
  });
  
  
  setTimeout(function(){
   window.location.reload(1);
}, 10000);

</script>




