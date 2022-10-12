
<?php 

// echo "<pre>";
// print_r($BookingFromCalls);
// die;
?>

<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css"> 
<div class="content-wrapper">
  <section class="content">
    <?php $this->load->view('admin/includes/_messages.php') ?>
    <?php $this->load->view('admin/includes/complaints'); ?>
    <div class="card">
      <div class="card-header">
         <center><h4><b>Booking Complaints From Calls</b></h4></center>

      </div>
    </div>
    
    
    <div class="card">
      <div class="card-body table-responsive">
        <table id="example1" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Booking Id</th>
                  <th>Booking From</th>
                  <th>Booking To</th>
                  <th>Address</th>
                  <th>Verifier</th>
                  <th>Issue Type</th>
                  <th>Action</th>
                </tr>
                </thead>
               <tbody>
                  <?php foreach ($BookingFromCalls as $key => $value) { ?>
                <tr>

                  <td><?php echo $value['unique_booking_id'] ?></td>
                  <td><?php echo  date("d-m-Y H:i a", strtotime($value['booking_from'])); ?></td>
                  <td><?php echo  date("d-m-Y H:i a", strtotime($value['booking_to'])); ?></td>
                  <td><?php echo "<b>"."Place : "."</b>".$value['placename']."<br>";
                            echo "<b>". "Address : "."</b>".$value['place_address']."<br>";
                   ?></td>
                  <td><?php echo $value['verifier_name'] ?></td>
                  <td><?php echo $value['issue_type'] ?></td>
                  <td><a href="<?php echo  base_url('admin/complaint/add_complaint/'.$value['booking_id']."/".$value['id']); ?>" title="View History" class="view btn btn-sm btn-warning"   ><i class="fa fa-edit"></i></a></td>

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


</script>



