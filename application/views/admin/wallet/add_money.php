<link rel="stylesheet" href="<?= base_url()?>assets/plugins/datatables/dataTables.bootstrap4.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<style>
    input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
}

input[type=number] {
  -moz-appearance: textfield;
}
</style>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add Money</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Parking Management</a></li>
              <li class="breadcrumb-item active">Add Money</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
          <div class="col-sm-12">
              <div class="card" style="">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                         
                          <div class="form-group">
                               <label for="exampleInputamout">Add Money To Wallet</label>
                               <input type="number" class="form-control" id="exampleInputamout" aria-describedby="emailHelp" placeholder="Enter amount">
                          </div>
                          <input type="checkbox" data-toggle="toggle" data-on="PAY NOW" data-off="SWIPE" data-onstyle="success" data-offstyle="primary">

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
              <h3 class="card-title">Transactions</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>Transaction Date</th>
                  <th>Transaction ID</th>
                  <th>Account Name</th>
                  <th>Amount</th>
                  <th>Payment Type</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                  <td>12-Feb-2022</td>
                  <td>Internet
                    Explorer 4.0
                  </td>
                  <td>1200</td>
                  <td> Mastercard</td>
                  <td><a href="#" class="btn btn-success">Sent</a></td>
                </tr>
              
                
                <tr>
                  <td>10-Feb-2022</td>
                  <td>All others</td>
                  <td>14000</td>
                  <td>Visa</td>
                  <td><a href="#" class="btn btn-danger" style="color:white;">Received</a></td>
                </tr>
                </tbody>
              
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
