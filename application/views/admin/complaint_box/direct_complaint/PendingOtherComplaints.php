<?php 

   $method = $this->router->fetch_method(); 

  if(empty($_GET)){

      $type = 1;

  }else{

      $type = $_GET['type'];

  }

?>

<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css"> 

<div class="content-wrapper">

  <section class="content">

        <?php $this->load->view('admin/includes/complaints'); ?>

    <div class="card">

       <div class="card-body table-responsive" style="border:2px solid;border-radius:11px;">

           <center>

        <a href="<?php echo base_url()?>admin/Complaint/Pending_complaint?type=2" style="color: black"><button type="button" class="btn btn-light btn-rounded" <?php if($method =='Pending_complaint' && ($type =='2')){ echo "style='border: 1px solid black;border-radius:25px;background-color:darkorange'"; }else{ echo "style='border: 1px solid black;border-radius:25px;'"; } ?>>Registered</button></a>

        &nbsp;&nbsp;&nbsp;<a href="<?php echo base_url()?>admin/Complaint/Pending_complaint?type=3" style="color: black"><button type="button" class="btn btn-light btn-rounded" <?php if($method =='Pending_complaint' && ($type =='3')){ echo "style='border: 1px solid black;border-radius:25px;background-color:darkorange'"; }else{ echo "style='border: 1px solid black;border-radius:25px;'"; } ?> >Un-registered</button></a>

        </center>

        <table id="example1" class="table table-bordered table-striped">

                <thead>

                <tr style="background-color:burlywood;">

                  <th>Username</th>

                  <th>Issue Type</th>

                  <th>Complaint</th>

                  <th>Source</th>

                  <th>Issues Raised</th>

                  <th>Status</th>

                  <th>Action</th>

                </tr>

                <tbody>

                  <?php foreach ($othercomplaints as $key => $value) {

                    $source = ($value['source_type']==1) ? '<span class="badge badge-primary">User App</span>' : '<span class="badge badge-dark">Call</span>' ;

                    $actions = ($value['source_type']==1) ? '<span class="badge badge-primary">User App</span>' : '<span class="badge badge-dark">Call</span>' ;

                    if($actions == 0){

                       $action = '<span class="badge badge-danger">Pending</span>';

                    }else if($actions == '2'){

                        $action = '<span class="badge badge-warning">Process</span>';

                    }else{

                        $action = '<span class="badge badge-warning">Resolved</span>';

                    }

                   ?>

                <tr>

                 <td><?php echo $value['username']; ?></td>

                 <td><?php echo $value['complaint_topic']; ?></td>

                 <td><?php echo $value['description']; ?></td>

                 <td><?php echo $source; ?></td>

                 <td><?php echo $value['created_date']; ?></td>

                 <td><?php echo $action; ?></td>

                 <td>

                    <a href="<?php echo base_url('admin/complaint/add_other_complaint/'.$value['id']."/".$value['user_id']."/".$value['source_type']) ?>" title="View History" class="view btn btn-sm btn-warning"   ><i class="fa fa-edit"></i></a>

                 </td>



                </tr>

                <?php  } ?>

                </tbody>

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
</script>


