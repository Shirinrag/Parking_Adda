<link rel="stylesheet" href="<?= base_url()?>assets/plugins/datatables/dataTables.bootstrap4.css">
<style>

h5.card-title {
    color: white;
    font-weight: 700;
    font-size: 16px;
}
p.card-text {
    color: white;
    font-weight: 400;
}
</style>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Wallet System</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Parking Management</a></li>
              <li class="breadcrumb-item active">Wallet System</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
          <div class="col-sm-3">
              <div class="card" style="background:#fcbf49;">
                <div class="card-body" style="height:100px;">
                  <h5 class="card-title">Today's Earning</h5>
                  <p class="card-text"><?php
                      $result=0;
                     foreach($todays_earning as $earning){
                         $result+= $earning['amount'];
                
                     };
                     print_r($result);
                  ?></p>
                  <!--<a href="#" class="btn btn-primary">120000</a>-->
                </div>
              </div>
              
          </div>
          
          <div class="col-sm-3">
              <div class="card" style="background:#71D178;">
                <div class="card-body" style="height:100px;">
                  <h5 class="card-title">Add Total User's to Wallet</h5>
                  <p class="card-text"><?php
                      
                     print_r(count($total_users));
                  ?></p>
                  <!--<a href="#" class="btn btn-primary">4500000</a>-->
                </div>
              </div>
          </div>
          
          <div class="col-sm-3">
              <div class="card" style="background:#A44CD3;">
                <div class="card-body" style="height:100px;">
                  <h5 class="card-title">Add Refundable Amount to User</h5>
                  <p class="card-text"><?php
                      $result=0;
                     foreach($refundable_amount as $amount){
                         $result+= $amount['amount'];
                
                     };
                     print_r($result);
                  ?></p>
                  <!--<a href="#" class="btn btn-primary">900</a>-->
                </div>
              </div>
          </div>
          
          <div class="col-sm-3">
              <div class="card" style="background:#00b4d8;">
                <div class="card-body" style="height:100px;">
                  <h5 class="card-title">Total Amount</h5>
                  <p class="card-text"><?php
                      $result=0;
                     foreach($total_users as $total_amount){
                         $result+= $total_amount['amount'];
                
                     };
                     print_r($result);
                  ?></p>
                  <!--<a href="#" class="btn btn-primary">14500</a>-->
                </div>
              </div>
          </div>
      </div>
      
      <div class="row">
          <div class="col-sm-12">
              <div class="card" style="">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                          <h6 class="card-title"><strong>Main Balance</strong></h6>
                          <p class="card-text" style="color:black;">120000</p>
                       </div>
                       
                       <div class="col-sm-6">
                           <div class="row">
                               <div class="col-sm-4">
                                 <p class="card-text" style="color:grey;"><strong>Valid Till</strong></p>
                                 <p class="card-text" style="color:black;">08/22</p>
                              </div>
                              <div class="col-sm-4">
                                 <p class="card-text" style="color:grey;"><strong>Card Holder</strong></p>
                                 <p class="card-text" style="color:black;">BDS Services PVT LTD</p>
                              </div>
                              <div class="col-sm-4">
                                 <p class="card-text" ></p><br>
                                 <p class="card-text" style="color:black;">**** **** **** 1234</p>
                              </div>
                           </div>
                       </div>
                    </div>
                    <br>
                    <div class="row">
                             <div class="col-sm-12">  
                                 <div class="progress">
                                      <div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                 </div>
                             </div>  
                    </div>
                </div>
              </div>
              
          </div>
      </div>
        
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Payment History</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
<table id="na_datatable" class="table table-bordered table-striped" width="100%">
          <thead>
            <tr>
              <th>#<?= trans('id') ?></th>
              <th>#<?= trans('onUpdate') ?></th>
              <th><?= trans('username') ?></th>
              <th><?= trans('amount') ?></th>
              <th><?= trans('status') ?></th>
              <th width="100" class="text-right"><?= trans('action') ?></th>
            </tr>
          </thead>
        </table>
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
    "ajax": "<?=base_url('admin/wallet/datatable_json')?>",
    "order": [[4,'desc']],
    "columnDefs": [
    { "targets": 0, "name": "id", 'searchable':true, 'orderable':true},
    { "targets": 1, "name": "onUpdate", 'searchable':true, 'orderable':true},
    { "targets": 2, "name": "username", 'searchable':true, 'orderable':true},
    { "targets": 3, "name": "amount", 'searchable':true, 'orderable':true},
    { "targets": 4, "name": "is_active", 'searchable':true, 'orderable':true},
    { "targets": 5, "name": "Action", 'searchable':false, 'orderable':false,'width':'100px'}
    ]
  });
</script>



<script>
  $(function () {
    $("#example1").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
  });
</script>
</body>
</html>
