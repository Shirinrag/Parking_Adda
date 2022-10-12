<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= isset($title)? $title.' - ' : 'Title -' ?> <?= $this->general_settings['application_name']; ?></title>
   <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url($this->general_settings['favicon']); ?>">
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url()?>assets/plugins/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url()?>assets/dist/css/adminlte.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?= base_url()?>assets/plugins/iCheck/flat/blue.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="<?= base_url()?>assets/plugins/morris/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="<?= base_url()?>assets/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="<?= base_url()?>assets/plugins/datepicker/datepicker3.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?= base_url()?>assets/plugins/daterangepicker/daterangepicker-bs3.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="<?= base_url()?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <!-- DropZone -->
  <link rel="stylesheet" href="<?= base_url()?>assets/plugins/dropzone/dropzone.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- jQuery -->
  <script src="<?= base_url()?>assets/plugins/jquery/jquery.min.js"></script>

</head>

<style>
      .blink {
        animation: blinker 0.6s linear infinite;
        color: #1c87c9;
        font-size: 30px;
        font-weight: bold;
        font-family: sans-serif;
      }
      @keyframes blinker {
        50% {
          opacity: 0;
        }
      }
      .blink-one {
        animation: blinker-one 1s linear infinite;
      }
      @keyframes blinker-one {
        0% {
          opacity: 0;
        }
      }
      .blink-two {
        animation: blinker-two 1.4s linear infinite;
      }
      @keyframes blinker-two {
        100% {
          opacity: 0;
        }
      }
    </style>
    

<body class="hold-transition sidebar-mini <?=  (isset($bg_cover)) ? 'bg-cover' : '' ?>">

<!-- Main Wrapper Start -->
<div class="wrapper">

  <!-- Navbar -->

  <?php if(!isset($navbar)): ?>

  <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?= base_url('admin') ?>" class="nav-link" style="border: 1px solid black;border-radius: 23px;"><b><?= trans('home') ?></b></a>
      </li>
     &nbsp;&nbsp;
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?= base_url('admin/auth/logout') ?>" class="nav-link" style="border: 1px solid black;border-radius: 23px;"><b><?= trans('logout') ?></b></a>
      </li>&nbsp;&nbsp;
       <li class="nav-item d-none d-sm-inline-block">
        <a onclick="history.back();" class="nav-link" style="border: 1px solid black;border-radius: 23px;"><b>Back</b></a>
      </li>
&nbsp;&nbsp;
      

   

      <div style="display:none" id="callstarted">
       <li class="nav-item d-none d-sm-inline-block" >
        <div class="blink blink-one" style="background-color:chartreuse;border:2px solid;border-radius: 37px;" >
        <a href="#" onclick="endcall()" class="nav-link"><img src="https://icon-library.com/images/headset-icon-transparent/headset-icon-transparent-26.jpg" style="height: 26px;" title="Call Started"></a>
        </div>
      </li>
      </div>
       


      
      
    </ul>

    <!-- SEARCH FORM -->
  
    <!-- Right navbar links -->
    
      <!--  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fa fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      -->
	  </li>
     <!-- <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i
            class="fa fa-th-large"></i></a>
      </li> -->
    </ul>
  </nav>

  <?php endif; ?>

  <!-- /.navbar -->


  <!-- Sideabr -->

  <?php if(!isset($sidebar)): ?>

  <?php $this->load->view('admin/includes/_sidebar'); ?>

  <?php endif; ?>

  <!-- / .Sideabr -->
  <script>
      var data = localStorage.getItem("callstarted");
      if(data==1)
      {
          $("#callstarted").show();
      }else{
           $("#callstarted").hide();
      }
     
     
      function endcall(){
          
        var ab = 'https://192.168.1.21/agc/api.php?source=PARKINGADDA&user=bds18&pass=bds18&agent_user=bds18&function=external_hangup&value=1';
        confirm("are you sure to end the call.")
        $("#callstarted").hide();
        localStorage.setItem("callstarted", "0");
        var ab;
         $.post(ab,
    {
      '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>',
      
    });
    }
    
    
  </script>
