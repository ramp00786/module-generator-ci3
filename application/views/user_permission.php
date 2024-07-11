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
      <h1>User Permission <small>Set module permission to user</small></h1> 
      <ol class="breadcrumb">
        <li ><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>   
        <li class="active">User Permission</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="row">
        <div class="col-md-12">
          <?php if($alert = $this->session->flashdata('alert')){
            echo $this->ap->getAlert($alert['type'], $alert['msg'], $alert['heading'], $alert['icon']);
          } 
          ?>
        </div>
      </div>

      <!-- Default box -->
      <div class="box box-primary border-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Set permission to module for user</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                    title="Collapse">
              <i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>

         <?php echo form_open('Userpermission/addorupdate'); ?>
         	<!-- box body -->
	        <div class="box-body" id="dashboard">
	           <div class="form-group">
	              <label>Select user <span class="text-red">*</span></label> 
	              <select onchange="getUsersPermission(this.value)" data-validation="required" data-validation-error-msg="Please select" data-validation-error-msg="Please select user" name="user_id" class="form-control">
	              	<option value="">-</option>
	              	<?php 
	              		if($users !='No data'){
	              			
	              			foreach ($users as $user) {
	              				echo "<option value='".$user['user_id']."'>".$user['email']."(".$user['first_name']." ".$user['last_name']." - ".$user['user_id']." ) </option>";
	              			}
	              		}
	              	?>
	              </select>
	          </div>


	          <span id="modue_fields"> Select Users to fetch modules data</span>


	          

	          

	          
	        </div>
         	<!-- box body -->

	       
	        <!-- box footer -->
	        
	        <div class="box-footer">
	        	<button class="btn btn-sm btn-primary pull-right">Submit</button>
	        </div>
	        <!-- box footer -->


    	</form>

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

<script src="<?php echo base_url(); ?>assets/theme/dist/js/jquery.form-validator.min.js"></script>
<script>
    $.validate({
      validateOnBlur : false, // disable validation when input looses focus
      errorMessagePosition : 'inline', // Instead of 'inline' which is default
      scrollToTopOnError : false // Set this property to true on longer forms
    });
</script>

<script>
  $(document).ready(function () {
    $('.sidebar-menu').tree()
  });


  function open_create_project_div()
  {
    $("#open_create_project_div").show();
  }

  function getUsersPermission(user_id){
    if(user_id !=''){
      $('#modue_fields').html('Please wait..');
      $.get("<?php echo base_url() ?>/Userpermission/users_module_data", {user_id:user_id}, function(data, status){
        $('#modue_fields').html(data);
      });
    }
    else{
      $('#modue_fields').html('Select Users to fetch modules data');
    }
  }
</script>
</body>
</html>