<?php 

   $method = $this->router->fetch_method(); 

  if(empty($_GET)){

      $type = 1;

  }else{

      $type = $_GET['type'];

  }

?>

<section class="content">
     <div class="container-fluid">
     
        <div class="row">

          <div class="col-12 col-sm-6 col-md-6">
            <a href="<?php echo base_url()?>admin/Complaint/Pending_complaint?type=1" style="color: black">
             <div class="info-box mb-3" <?php if($method =='Pending_complaint' && ($type =='1')){ echo "style='background-color:#71ff71';"; } ?>>
              <span class="info-box-icon  elevation-1" style="color: #f9fffe">
                <img src="https://carnab.com/images/high-quality-cars.png">
              </span>
              <div class="info-box-content">
                <span class="info-box-text"><b>Booking Complaints</b></span>
                <span class="info-box-number">Pending : <?php echo ($counts['verifier_complaints']+ $counts['calls_complaints']);  ?></span>
              </div>
            </div>
          </a> 
          </div>




          <div class="col-12 col-sm-6 col-md-6"  >

            <a href="<?php echo base_url()?>admin/Complaint/Pending_complaint?type=2" style="color: black">

               <div class="info-box mb-3" <?php if($method =='Pending_complaint' && ($type =='2' || $type =='3')){ echo "style='background-color:#71ff71';"; } ?>>

              <span class="info-box-icon bg-info elevation-1">

                <img src="https://cdn-icons-png.flaticon.com/512/4233/4233799.png">

                </span>

              <div class="info-box-content">

                <span class="info-box-text"><b>Other Complaints</b></span>

                  <span class="info-box-number">Pending : <?php echo ($counts['user_app_complaints']+ $counts['other_complaints']);  ?></span>

              </div>

            </div>

          </a>

          </div>

        </div>



      </div>

      <?php

        if($method =='Pending_complaint' && (!empty($_GET))){ ?>

          <center>

            <h5><b>Pending Complaints List</b></h5>

        </center>

       <?php }

      

      ?>

      

      

      

    </section>