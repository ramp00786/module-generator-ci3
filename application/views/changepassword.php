<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $title; ?></title>

  <?php echo $favicon_link; ?>

  
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>/dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
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
      <h1>Change password</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Change password</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      
      
      <!-- password box -->
      <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Change password</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-md-3"><p>&nbsp;</p></div>
                <div class="col-md-6">
                  <?php if($alertData = $this->session->flashdata('alert')){ 
                    echo $this->ap->getAlert($alertData['type'], $alertData['message'], $alertData['heading'], $alertData['icon']);
                   } ?>
                   
                  <?php echo form_open('Changepassword/updatepass'); ?>
                    <!-- text input -->
                    <div class="form-group">
                      <label>Old Password <span class="text-danger">*</span></label>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control" placeholder="Old password"  name="old_password" id="login_string" data-validation="required" data-validation-error-msg="Please Enter Old Password">
                      </div>
                    </div>

                    <div class="form-group">
                      <label>New Password <span class="text-danger">*</span><small>password contestants uppercase letter number and special characters</small></label>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control" placeholder="New password" name="new_pass" data-validation="required" data-validation-error-msg="Please Enter New Password" minlength="8">
                      </div>
                    </div>

                    <div class="form-group">
                      <label>Confirm Password <span class="text-danger">*</span></label>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control" placeholder="Confirm password" name="cnf_pass" data-validation="confirmation" data-validation-confirm="new_pass" data-validation-error-msg="Password not match" minlength="8">
                      </div>
                    </div>

                    <div class="form-group">
                      <button class="btn btn-warning pull-right"><i class="fa fa-save"></i> Update</button>
                      <div class="clearfix"></div>
                    </div>

                  <?php echo form_close(); ?>
                </div>
                <div class="col-md-3"><p>&nbsp;</p></div>
            </div>
            <!-- /.box-body -->
      </div>
      <!-- /.box -->
      

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php echo $footer; ?>

  <!-- Control Sidebar -->  
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="<?php echo base_url('assets/theme/'); ?>/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url('assets/theme/'); ?>/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="<?php echo base_url('assets/theme/'); ?>/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url('assets/theme/'); ?>/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('assets/theme/'); ?>/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url('assets/theme/'); ?>/dist/js/demo.js"></script>
<script>
  $(document).ready(function () {
    $('.sidebar-menu').tree()
  })
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
    <script>
      $.validate({
        modules : 'security'
      });
    </script>
</body>
</html>
