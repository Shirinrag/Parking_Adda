<?php 

// echo "<pre>";
// print_r($data);
// die;

?>
<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <section class="content">
    <!-- For Messages -->
    <?php $this->load->view('admin/includes/_messages.php') ?>
    <div class="card">
      <div class="card-header">
        <div class="d-inline-block">
          <h3 class="card-title"><i class="fa fa-list"></i>&nbsp; Blocked Slots List</h3>
        </div>
       
      </div>
    </div>
    <div class="card">
      <div class="card-body table-responsive">
        <table id="na_datatable" class="table table-bordered table-striped" width="100%">
          <thead style="background-color: burlywood;">
            <tr>
              <th>#<?= trans('id') ?></th>
              <th><center>Place Info</center></th>
              <th><center>Slot </center></th>
              <th><center>Issued Date</center></th>
              <th><?= trans('status') ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($data as $key => $value) { ?>
                <tr>
                  <td style="width: 1%"><?php echo $key+1;?></td>
                  <td style="width: 45%"><?php echo "<b>Placename :  </b>".$value['placename']."<br>";
                  echo "<b>Address : </b>".$value['place_address']."<br>";
                  echo "<b>City : </b>".$value['cityname']."<br>";
                  ?></td>
                  <td style="width: 1%"><?php echo $value['display_id'];?></td>
                  <td style="width: 25%"><?php echo  date("d-m-Y H:i A", strtotime($value['issue_raised_on']));?></td>
                  <td>
                    

                    <a href="<?= base_url('admin/complaint/SlotsComplaintsVerification/'.$value['complaint_id']) ?>" title="View History" class="view btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                  </td>
                </tr>
            <?php  }
            ?>
          </tbody>
          
        </table>
      </div>
    </div>
  </section>  
</div>


<!-- DataTables -->
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>


<script>
  //---------------------------------------------------
  var table = $('#na_datatable').DataTable( {
    
  });
</script>


<script type="text/javascript">
  $("body").on("change",".tgl_checkbox",function(){
    console.log('checked');
      alert('Are You Sure want to Update Slots Informations');
        $.post('<?=base_url("admin/complaint/updateSlotsStatus")?>',
    {
      '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
      id : $(this).data('id'),
      status : $(this).is(':checked') == true?1:0
    },
    function(data){
       $.notify("Slot Activated Successfully", "success");
         setTimeout(function () {
        location.reload(true);
      }, 5000);
    });
  });
</script>




