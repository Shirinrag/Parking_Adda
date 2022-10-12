<?php 
  $method = $this->router->fetch_method();



?>
<section class="content">
      <div class="container-fluid">
      
        <div class="row">
          <div class="col-12 col-sm-6 col-md-6">
            <a href="" style="color: black">
            <div class="info-box mb-6" <?php if($method =='PendingBookingComplaints'){ echo "style='background-color:#71ff71';"; } ?>>
              <span class="info-box-icon  elevation-1" style="color: #f9fffe">
                <img src="https://carnab.com/images/high-quality-cars.png">
              </span>
              <div class="info-box-content">
                <span class="info-box-text"><b>Verifications</b></span>
                <span class="info-box-number">Pending : <?php echo "1"; ?></span>
              </div>
            </div>
          </a> 
          </div>

          <div class="col-12 col-sm-6 col-md-6"  >
            <a href="" style="color: black">
              <div class="info-box mb-6" <?php if($method =='PendingCallsComplaints'){ echo "style='background-color:#71ff71';"; } ?>>

              <span class="info-box-icon bg-info elevation-1">
                <img src="https://cdn-icons-png.flaticon.com/512/4233/4233799.png">
                </span>
              <div class="info-box-content">
                <span class="info-box-text"><b>Blocked Slots</b></span>
                  <span class="info-box-number">Pending : <?php echo "2"; ?></span>
              </div>
            </div>
          </a>
          </div>
       
        </div>

      </div>
    </section>
