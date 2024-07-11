<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo app_name; ?> Login</title>
  <!-- Tell the browser to be responsive to screen width -->

  <link rel="shortcut icon" href="<?php echo favicon; ?>">

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>dist/css/AdminLTE.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>plugins/iCheck/square/blue.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page" id="loginPage">
<div class="login-box">
  <div class="login-logo">

    <a href="<?php echo base_url(); ?>"><img style="margin-top: 20px; height:80px" src="<?php echo logo; ?>"><br/><?php echo login_page_heading; ?> </a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">

  	<?php if( ! isset( $on_hold_message ) ) { 

  			if( isset( $login_error_mesg ) ){
  				$msg = '
						<p>
							Login Error #' . $this->authentication->login_errors_count . '/' . config_item('max_allowed_attempts') . ': Invalid Username/Email Address, or Password.
						</p>
						<p>
							Username, email address and password are all case sensitive.
						</p>
					';

				echo $this->ap->getAlert('danger', $msg, 'Login Error', 'fa fa-exclamation-triangle');
  			}
  			if( $this->input->get(AUTH_LOGOUT_PARAM) ){

  				echo $this->ap->getAlert('success', 'You have successfully logged out.', 'logged out', 'fa fa-sign-out');
  			}

  		?>

	  	<?php  ?>

	    <p class="login-box-msg">Sign in to start your session</p>

	    <?php echo form_open();  ?>
	      <div class="form-group has-feedback">
	        <input type="text" class="form-control" placeholder="Email or Username" name="login_string" id="login_string" value="">
	        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
	      </div>
	      <div class="form-group has-feedback">
	        <input type="password" class="form-control" placeholder="Password" name="login_pass" id="login_pass" autocomplete="off" readonly="readonly" onfocus="this.removeAttribute('readonly');" id="submit_button" value="">
	        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
	      </div>
	      <div class="row">
	      	<div class="col-xs-8"><p>&nbsp;</p></div>
	        <div class="col-xs-4">
	          <button type="submit" class="btn btn-primary btn-block btn-flat" id="submit_button">Sign In <i class="fa fa-sign-in"></i> </button>
	        </div>
	        <!-- /.col -->
	      </div>
	    </form>

	    <div style="font-size: 20px;">How to use: <a target="_blank" href="<?php echo base_url(); ?>How-to-use.pdf">User Guide
	    	</a>
	    	<br/>
	    	Username: SuperAdmin<br/>
	    	Password: Demo@123
	    </div>

	<?php }
	else
	{
		$msg ='
				<p>
					Excessive Login Attempts
				</p>
				<p>
					You have exceeded the maximum number of failed login<br />
					attempts that this website will allow.
				<p>
				<p>
					Your access to login and account recovery has been blocked for ' . ( (int) config_item('seconds_on_hold') / 60 ) . ' minutes.
				</p>
				<p>
					Please use the <a href="/examples/recover">Account Recovery</a> after ' . ( (int) config_item('seconds_on_hold') / 60 ) . ' minutes has passed,<br />
					or contact us if you require assistance gaining access to your account.
				</p>
			';

		echo $this->ap->getAlert('danger', $msg, 'Login blocked', 'fa fa-ban');

	}
	?>

    
    <!-- /.social-auth-links -->

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="<?php echo base_url('assets/theme/'); ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url('assets/theme/'); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?php echo base_url('assets/theme/'); ?>plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script>
</body>
</html>
