  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#"><?= trans('home') ?></a></li>
              <li class="breadcrumb-item active"><?= trans('dashboard') ?> v2</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
      <div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fa fa-users"></i></span>
              <a href="<?php echo base_url()?>admin/users/"> 
              <div class="info-box-content">
                <span class="info-box-text" style="color: black;"><b>Users</b></span>
                <span class="info-box-number">
                  <p id="userscount"></p>
                   </a>
                </span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3" onclick="gotoTarget('bookingspage')">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-taxi"></i></span>
               <a href="<?php echo base_url()?>admin/Booking/All_booking"> 
              <div class="info-box-content">
                <span class="info-box-text" style="color: black;"><b>Completed Bookings</b></span>
                <span class="info-box-number"><p id="bookingscount"></p></span>
              </div>
            </a>
            </div>
          </div>
           <div class="js" id="midpageloader" style="display: none;"><div id="preloader"></div></div>
          <div class="clearfix hidden-md-up"></div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fa fa-map-marker"></i></span>
              <a href=""> 
              <div class="info-box-content">
                <span class="info-box-text" style="color: black;"><b>Places</b></span>
                <span class="info-box-number"><p id="activeplaces"></p></span>
              </div>
            </a>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-cloud-download"></i></span>
              <div class="info-box-content">
                <span class="info-box-text"><b>Downloads</b></span>
                    <p id="downloads_counts"></p>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <center><h5 class="card-title"><b>Bookings & Earnings</b> </h5></center>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <p class="text-center">
                    </p>

                    <div class="chart">
                      <div id="barchart_material" style="height: 300px; margin-top: 12%;"></div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <p class="text-center">
                      <strong>Bookings Informations</strong>
                    </p>
                      <div id="piechart_3d" style="width: 130%;height: 100%"  ></div>
                  </div>
                </div>
              </div>
              <center><h5>Pending Complaints </h5></center>
              <div class="card-footer">
                <div class="row">
                  <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                      <div class="info-box mb-3 bg-warning">
                      <span class="info-box-icon"><i class="fa fa-users"></i></span>
                      <a href="<?php echo base_url()?>admin/Complaint/Pending_complaint?type=2"> 
                      <div class="info-box-content">
                        <span class="info-box-text" style=""><b>Users Complaints</b></span>
                        <span class="info-box-number"><?php echo ($counts['user_app_complaints']+ $counts['other_complaints']);  ?></span>
                      </div>
                    </a>
                    </div>
                   </div>
                </div>
                  <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                     <div class="info-box mb-3 bg-success">
                      <span class="info-box-icon"><i class="fa fa-car"></i></span>
                      <a href="<?php echo base_url()?>admin/Complaint/Pending_complaint?type=1"> 
                      <div class="info-box-content">
                        <span class="info-box-text" style=""><b>Booking Complaints</b></span>
                        <span class="info-box-number"><?php echo ($counts['verifier_complaints']+ $counts['calls_complaints']);  ?></span>
                      </div>
                    </a>
                    </div>
                   </div>
                </div>
                 <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                     <div class="info-box mb-3 bg-danger">
                      <span class="info-box-icon"><i class="fa fa-ticket"></i></span>
                      <a href="<?php echo base_url()?>admin/Complaint/getAllBlockedSlots"> 
                      <div class="info-box-content">
                        <span class="info-box-text"><b>Slots Complaints</b></span>
                        <span class="info-box-number"><?= count($PendingVerifications);?></span>
                      </div>
                    </a>
                    </div>
                   </div>
                </div>
                <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                     <div class="info-box mb-3 bg-primary">
                      <span class="info-box-icon"><i class="fa fa-commenting"></i></span>
                      <a href="<?php echo base_url()?>admin/Booking/All_booking"> 
                      <div class="info-box-content">
                        <span class="info-box-text"><b>Pending Followup</b></span>
                        <span class="info-box-number"><?= $followUpdata; ?></span>
                      </div>
                    </a>
                    </div>
                   </div>
                </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Visitors Report</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-widget="remove">
                    <i class="fa fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body p-0">
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Browser Usage</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-8">
                    <div class="chart-responsive">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
s</script>
<script src="<?= base_url() ?>assets/plugins/sparkline/jquery.sparkline.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="<?= base_url() ?>assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


<script>

  window.onload = function() {
    onloads();

  } 

  function onloads(){
    $('#midpageloader').show();  
    jQuery.ajax({
      dataType: 'json',
      type: "POST",
      url: "<?=base_url("admin/dashboard/getDashboardData")?>",
      data : {'unique_id':'1','<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'},
      cache: false,
      beforeSend: function(){
        $('#midpageloader').show();
      },
      success: function(res){
          $('#userscount').text(res.all_users.total_users);
          $('#bookingscount').text(res.booking_info.total_completed_booking);
          $('#activeplaces').text(res.active_places.total_active_places);
          $('#downloads_counts').text(res.users_type.users_type)
        }

      });
      $('#midpageloader').hide();
  }

</script>

<script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Month', <?php echo $places; ?>],
          ['<?php echo date('d-m-Y h:s A'); ?>', <?php echo $earnings; ?>],
          ]);

        var options = {
          chart: {
          },
          bars: 'vertical' // Required for Material Bar Charts.
        };
        var chart = new google.charts.Bar(document.getElementById('barchart_material'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
</script>


  <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['Total Bookings', <?php echo $piechart['0']['total'];?>],
          ['Refunds/Cancelled',      <?php echo $piechart['1']['total'];?>],
          ['Replacements',  <?php echo $piechart['2']['total'];?>],
        ]);

        var options = {
          
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
      }
  </script>
<script>
  
  function gotoTarget(pagetype){



  }
</script>



    
