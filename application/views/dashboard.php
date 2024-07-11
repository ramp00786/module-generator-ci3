<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $title; ?></title>

  <link rel="shortcut icon" href="<?php echo favicon ?>">
  
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>dist/css/AdminLTE.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style type="text/css">
    #dashboard label{
      font-size: 18px;
      text-shadow: 5px 5px 8px #585353;
    }

    .info-box1 {
  display: block;
  min-height: 90px;
  background: #fff;
  width: 50%;
  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
  border-radius: 2px;
  margin-bottom: 15px;
 
 
}
  </style>

</head>
<body class="hold-transition <?php echo skin ?> sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

  <?php echo $header; ?>
  <!-- Left side column. contains the logo and sidebar -->
  <?php echo $left_menu; ?>

  <!-- =============================================== -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Dashboard</h1>
      <ol class="breadcrumb">
        <li class="active"><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>   
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="box box-primary border-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Summary</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                    title="Collapse">
              <i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body" id="dashboard">


            <?php // $deli_per = round(($total_delivered*100)/$total_sent, 2); ?>
            <?php // $read_per = round(($total_read*100)/$total_sent, 2); ?>
            <?php // $reply_per = round(($total_replied*100)/$total_sent, 2); ?>

            


            
            <div class="col-md-4 col-sm-6 col-xs-12">
              <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Users</span>
                  <span class="info-box-number"><?php echo $total_users ?></span>
                  <div class="progress">
                    <div class="progress-bar" style="width: 100%"></div>
                  </div>
                  <span class="progress-description">
                    Total users
                  </span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
              <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-bars"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Menus</span>
                  <span class="info-box-number"><?php echo $total_menus ?></span>
                  <div class="progress">
                    <div class="progress-bar" style="width: 100%"></div>
                  </div>
                  <span class="progress-description">
                    Total Menus
                  </span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div>

            <div class="col-md-4 col-sm-6 col-xs-12">
              <div class="info-box bg-blue">
                <span class="info-box-icon"><i class="fa fa-paste"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Modules</span>
                  <span class="info-box-number"><?php echo $total_modules ?></span>
                  <div class="progress">
                    <div class="progress-bar" style="width: 100%"></div>
                  </div>
                  <span class="progress-description">
                    Total modules
                  </span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div>
           

            
         



        </div>

       
        <!-- /.box-body -->
        <div class="box-footer text-center">
           <h3>Contribute to Development</h3>
           <img src="<?php echo base_url() ?>assets/img/qr.jpeg" alt="">
        </div>

        <!-- /.box-footer-->
      </div>
      <!-- /.box -->
      <div id="open_create_project_div" hidden>
            <div class="box box-primary border-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Create Project</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                    title="Collapse">
              <i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body" id="dashboard">


            <?php // $deli_per = round(($total_delivered*100)/$total_sent, 2); ?>
            <?php // $read_per = round(($total_read*100)/$total_sent, 2); ?>
            <?php // $reply_per = round(($total_replied*100)/$total_sent, 2); ?>
             <div class="col-md-2 col-sm-12 col-xs-12"></div>

            <div class="col-md-4 col-sm-12 col-xs-12">
              <a href="<?php echo base_url("Project-with-GOC") ?>"><div class="info-box1 bg-teal" >
                <br>
               
                <h3><center ><span style="vertical-align:middle;margin-top: 00px;">With GOC</span></center></h3>
              </div><!-- /.info-box -->
            </div>

            <div class="col-md-4 col-sm-12 col-xs-12">
              <div class="info-box1 bg-olive" >
                <br>
                <h3><center ><span style="vertical-align:middle;">Manual</span></center></h3>
              </div><!-- /.info-box -->
            </div>            

             <div class="col-md-2 col-sm-12 col-xs-12"></div>


            
         



        </div>

       
        <!-- /.box-body -->
        <div class="box-footer">
           
        </div>
        
        <!-- /.box-footer-->
      </div>
        </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php echo $footer; ?>

  
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="<?php echo base_url('assets/theme/'); ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url('assets/theme/'); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="<?php echo base_url('assets/theme/'); ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url('assets/theme/'); ?>bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('assets/theme/'); ?>dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url('assets/theme/'); ?>dist/js/demo.js"></script>
<script>
  $(document).ready(function () {
    $('.sidebar-menu').tree()
  });


  function open_create_project_div()
  {
    $("#open_create_project_div").show();
  }
</script>
</body>
</html>