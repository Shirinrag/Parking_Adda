  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Parking Management</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#"><?= trans('home') ?></a></li>
              <li class="breadcrumb-item active">Parking List</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      
        
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <div class="col-md-8">
           

            <!-- TABLE: LATEST ORDERS -->
            <div class="card">
              <div class="card-header border-transparent">
                <h3 class="card-title">All Parking Area</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-widget="remove">
                    <i class="fa fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table m-0">
                    <thead>
                    <tr>
                      <th>Area ID</th>
                      <th>Parking Name</th>
                      <th>Status</th>
                      <th>Address</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach($result_data as $v){ ?>
                    <tr>
                      <td><a href="#">PA-<?php echo $v->id; ?></a></td>
                      <td><?php echo $v->placename; ?></td>
                      <td>
                          <?php 
                          switch($v->place_status){
                              
                              case 1:
                                  echo '<span class="badge badge-success">Complated</span>';
                                  break;
                                   case 2:
                                  echo '<span class="badge badge-warning">Pending</span>';
                                  break;
                                   case 3:
                                  echo '<span class="badge badge-danger">Legal</span>';
                                  break;
                                  case 4:
                                  echo '<span class="badge badge-info">Processing</span>';
                                 default:
                                  echo "";
                          }
                          ?>
                          </td>
                      <td>
                     <?php echo $v->place_address; ?>
                      </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                  </table>
                </div>
                <!-- /.table-responsive -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer clearfix">
                <a href="<?php echo base_url(); ?>/admin/Parking/addparking" class="btn btn-sm btn-info float-left">Apply New Parking</a>
                <a href="<?php echo base_url(); ?>/admin/Parking/parkinglist" class="btn btn-sm btn-secondary float-right">View All </a>
              </div>
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-4">
        

            <div class="card">
              <div class="card-header">
                <h3 class="card-title"> Parking Rules</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body"> 
                <div class="row">
                  <div class="col-md-8">
                    <div class="chart-responsive">
                      <canvas id="pieChart1" height="150"></canvas>
                    </div>
                    <!-- ./chart-responsive -->
                  </div>
                  <!-- /.col -->
                  <div class="col-md-4">
                    <ul class="chart-legend clearfix">
                      <li><i class="fa fa-circle-o text-danger"></i> Apply Form</li>
                      <li><i class="fa fa-circle-o text-success"></i> Verify Details</li>
                      <li><i class="fa fa-circle-o text-warning"></i> Legal Process</li>
                      <li><i class="fa fa-circle-o text-info"></i> Active</li>
                      <li><i class="fa fa-circle-o text-primary"></i> Pending</li>
                      <li><i class="fa fa-circle-o text-secondary"></i> Completed</li>
                    </ul>
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer bg-white p-0">
                <ul class="nav nav-pills flex-column">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                     Total Parking  Count
                      <span class="float-right text-danger">
                       
                        5</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      Total Enforcer Tickets 
                      <span class="float-right text-success">
                        4
                      </span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      Total Verifier Tickets 
                      <span class="float-right text-warning">
                        0
                      </span>
                    </a>
                  </li>
                </ul>
              </div>
              <!-- /.footer -->
            </div>
            <!-- /.card -->

           
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


  <!-- PAGE PLUGINS -->
<!-- SparkLine -->
<script src="<?= base_url() ?>assets/plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jVectorMap -->
<script src="<?= base_url() ?>assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- SlimScroll 1.3.0 -->
<script src="<?= base_url() ?>assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- ChartJS 1.0.2 -->
<script src="<?= base_url() ?>assets/plugins/chartjs-old/Chart.min.js"></script>

<script>
setTimeout(function(){
   window.location.reload(1);
}, 10000);
</script>




<!-- PAGE SCRIPTS -->
<script src="<?= base_url() ?>assets/dist/js/pages/parking.js"></script>