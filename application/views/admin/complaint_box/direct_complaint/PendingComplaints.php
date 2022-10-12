<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css"> 

<div class="content-wrapper">

<?php 

  $method = $this->router->fetch_method();



?>



<section class="content">

        

    <?php $this->load->view('admin/includes/complaints'); ?>

          

      <div class="card">

      <div class="card-body table-responsive" style="border:2px solid;border-radius:11px;">

        <table id="example1" class="table table-bordered table-striped">

                <thead>

                <tr style="background-color:burlywood">
                  <th>Booking Id</th>
                  <th>Booking From</th>
                  <th>Booking To</th>
                  <th><center>Address</center></th>
                  <th>Verifier</th>
                  <th>Issue Type</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  <?php foreach ($booking_complaints as $key => $value) { ?>
                <tr>


                  <td><?php echo $value['unique_booking_id'] ?></td>
                  <td><?php echo  date("d-m-Y H:i a", strtotime($value['booking_from'])); ?></td>
                  <td><?php echo  date("d-m-Y H:i a", strtotime($value['booking_to'])); ?></td>
                  <td><?php echo "<b>"."Place : "."</b>".$value['placename']."<br>";
                       echo "<b>". "Address : "."</b>".$value['place_address']."<br>";
                   ?></td>
                  <td><?php echo $value['verifier_name'] ?></td>
                  <td><?php echo $value['issue_type'] ?></td>
                  <?php 
                    if($value['source']=='Call'){ ?>
                     <td><a href="<?php echo  base_url('admin/complaint/add_complaint/'.$value['booking_id']."/".$value['id']); ?>" title="View History" class="view btn btn-sm btn-warning"   ><i class="fa fa-edit"></i></a></td>
                    <?php } else{ ?>
                     <td><a href="<?php echo  base_url('admin/complaint/view_complaint/'.$value['id']) ?>" title="View History" class="view btn btn-sm btn-warning"   ><i class="fa fa-edit"></i></a>
                     </i></a>
                   </td>
                   <?php }
                  ?>
                </tr>

                <?php  } ?>
                </tbody>
              </table>
      </div>

    </div>

    </section>

    

    

</div>



<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>

<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>



<script>

  $(function(){
    $("#example1").DataTable();
  });

function viewproblem(){
  alert("hii")
}
</script>







