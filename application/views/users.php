<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $title; ?></title>

  <link rel="shortcut icon" href="<?php echo base_url().favicon ?>">

  
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/theme/'); ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
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
<body class="hold-transition skin-purple sidebar-mini">
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
      <h1>Users</h1>
      <ol class="breadcrumb">
        <li><a href="javascript:void(0);"><i class="fa fa-users"></i> Users</a></li> 
       
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
          <h3 class="box-title">Users</h3>

          <div class="box-tools pull-right">
            <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#add-user"><i class="fa fa-plus"></i> Add User</button>
            
          </div>
        </div>
        <div class="box-body">         
          <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>S no.</th>
                  <th>Full Name</th>
                  <th>Email</th>
                  <th>Contact No.</th>
                  <th>Address</th>
                  <th>API Key</th>
                  <th>Uploaded Date</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  <?php $n=0; if($$table_name == 'No data' || $$table_name == 'Table not exists'){}else{
                    foreach ($$table_name as $row) { $n++;
                      $rnd=rand(1, 99999999);
                      $hash='sqxn___'.$table_name.'___'.$row['user_id'].'___'.$rnd.'___'.$controller.'___record Deleted!';
                      $enc_id=base64_encode($hash); 
                     ?>
                        <tr>
                          <td><?php echo $n; ?></td>
                          <td><?php echo $row['first_name'].' '.$row['last_name']; ?></td>
                          <td><?php echo $row['email']; ?></td>
                          <td><?php echo $row['contact_number']; ?></td>
                          <td><?php echo $row['address']; ?></td>
                          <td id="td_<?php echo $row['user_id'] ?>">
                            <?php 
                                
                                if($row['api_key'] =='' || $row['api_key'] == null){
                                  echo '<button class="btn btn-primary btn-xs" onclick="generateKey('.$row['user_id'].')">Generate API Key</button>';
                                }
                                else{
                                  echo $row['api_key'];
                                }
                            ?>
                              
                          </td>

                          <td><?php echo date('d M Y h:i a', strtotime($row['modified_at'])); ?></td>
                          <td nowrap="">


                            <button onclick="editRecord('<?php echo $enc_id; ?>', 'edit_model');" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i> Edit</button>
                            &nbsp;&nbsp;
                            |
                            &nbsp;&nbsp;
                            <button onclick="deleteRecord('<?php echo $enc_id; ?>');" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button> 

                           

                            


                          </td>
                        </tr>
                  <?php }
                  } ?>
                </tbody>
                
              </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
          
        </div>
        <!-- /.box-footer-->
      </div>
      <!-- /.box -->

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


<!-- Add Image modal -->
<div class="modal fade" id="add-user" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add User</h4>
      </div>

      <?php// echo form_open($controller.'/add'); ?>
      <?php echo form_open($controller.'/add', array('enctype' => 'multipart/form-data')); ?>
        <div class="modal-body"> 

          <?php $this->ap->create_form_new($formFields); ?>

          <!-- <div class="form-group">
              <label for="first_name">First Name <span class="text-red">*</span></label> 
              <input type="text" placeholder="Please enter full name" name="first_name" class="form-control" data-validation="required" data-validation-error-msg="Please enter first name">
          </div>

          <div class="form-group">
              <label for="first_name">Last Name <span class="text-red">*</span></label> 
              <input type="text" placeholder="Please enter last name" name="first_name" class="form-control" data-validation="required" data-validation-error-msg="Please enter last name">
          </div>

          <div class="form-group">
              <label for="email">Email <span class="text-red">*</span></label> 
              <input type="email" placeholder="Please enter email" name="email" class="form-control" data-validation="email" data-validation-error-msg="Please enter email">
          </div>

          <div class="form-group">
              <label for="contact_no">Contact no. <span class="text-red">*</span></label> 
              <input type="text" placeholder="Please enter contact no." name="contact_number" class="form-control" data-validation="required" data-validation-error-msg="Please enter contact no.">
          </div>

          <div class="form-group">
              <label for="address">Address</label> 
              <textarea placeholder="Please enter tag line" name="address" class="form-control"></textarea>
          </div> 

          <div class="form-group">
              <label for="password">Password</label> 
              <input type="text" placeholder="Please enter tag line" name="password" class="form-control">
          </div>  -->
         

        </div>


        <div class="modal-footer" style="border-top-color: #337ab7;">
          <?php $this->ap->getButtons(); ?>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- Edit Image modal -->
<div class="modal fade" id="edit_model">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Tag line</h4>
      </div>
      <span id="edit_model_body"></span>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- Delete confirm modal -->
<div class="modal fade" id="delConfirm">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-red">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Warning!</h4>
      </div>
      <?php echo form_open('Deletedata'); ?>
        <div class="modal-body">  
          <input type="hidden" id="qStr" name="q"> 
          <h4>Are you sure you want to delete this record?</h4>                                 
        </div>
        <div class="modal-footer" style="border-top-color: #337ab7;">
          <button type="button" class="btn btn-success pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
          <button type="submit" class="btn btn-danger"><i class="fa fa-check"></i> Yes</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- /.modal -->


<!-- jQuery 3 -->
<script src="<?php echo base_url('assets/theme/'); ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url('assets/theme/'); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="<?php echo base_url('assets/theme/'); ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url('assets/theme/'); ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
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
  })
</script>
<script>
  $(function () {
    $('#example1').DataTable()
  })

  function generateKey(user_id){
    $.post('Users/api_key', {user_id: user_id}, function(result){
      $('#td_'+user_id).html(result);
    });
  }
</script>
<script src="<?php echo base_url(); ?>assets/theme/dist/js/jquery.form-validator.min.js"></script>
<script>
    $.validate({
      validateOnBlur : false, // disable validation when input looses focus
      errorMessagePosition : 'top', // Instead of 'inline' which is default
      scrollToTopOnError : false // Set this property to true on longer forms
    });
</script>
</body>
</html>
