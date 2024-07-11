<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $title.$page_title; ?></title>

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

  <style type="text/css">
    fieldset {
      margin-top: 15px;
      border: 1px solid #b5b0b0;
    }
    legend {
      margin-left: 20px;
      width: auto;
      color: #FFF;
      background: #302f2f;
      font-size: 1.5em;
      padding-left: 15px;
      padding-right: 15px;

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
      <h1><?php echo $page_title; ?></h1>
      <ol class="breadcrumb">
        <li><a href="javascript:void(0);"><i class="fa fa-users"></i> <?php echo $page_title; ?></a></li> 
       
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
          <h3 class="box-title"><?php echo $page_title; ?></h3>

          <div class="box-tools pull-right">

            <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#add_model"><i class="fa fa-plus"></i> Add</button>
            
          </div>
        </div>
        <div class="box-body"> 

          <!-- Display status message -->
          <?php if(!empty($success_msg)){ ?>
          <div class="col-xs-12">
              <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $success_msg; ?>
                <br/>
                <button data-toggle="collapse" class="btn btn-xs btn-info" data-target="#more_res">Show Details</button>
                <div id="more_res" class="collapse">
                  <?php echo $more_result; ?>
                </div>
            </div>
          </div>
          <div class="col-xs-12">
            
          </div>
          <?php } if(!empty($error_msg)){ ?>
          <div class="col-xs-12">
              <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $error_msg; ?>
              </div>
          </div>
          <?php } ?>


          <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>S no.</th>
                  <th>Menu name</th>
                  <th>Module Name</th>                  
                  <th>Slug</th>                  
                  <th>Created Date</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  <?php $n=0; if($$table_name == 'No data' || $$table_name == 'Table not exists'){}else{
                    foreach ($$table_name as $row) { $n++;
                      $rnd=rand(1, 99999999);
                      $hash='sqxn___'.$table_name.'___'.$row['id'].'___'.$rnd.'___'.$controller.'___record Deleted!';
                      $enc_id=base64_encode($hash); 
                     ?>
                        <tr>
                          <td><?php echo $n; ?></td>
                          <td><?php echo $this->ap->MenuIdToName($row['menu_id']); ?></td>
                          <td><?php echo $row['module_name']; ?></td>
                          <td><?php echo $row['slug']; ?></td>
                          <td><?php echo date('d M Y', strtotime($row['date'])).' '.$row['time']; ?></td>

                          

                          <td nowrap="">
                            <button onclick="editRecord('<?php echo $enc_id; ?>', 'edit_model');" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i> Edit</button>
                            &nbsp;&nbsp;
                            |
                            &nbsp;&nbsp;
                            <button onclick="deleteRecordModule('<?php echo $enc_id; ?>');" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button> 

                           

                            


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



<!-- Add modal -->
<div class="modal fade" id="add_model" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Generate Module</h4>
      </div>
      
      <?php echo form_open($controller.'/add'); ?>
        <div id="add_model_body">

          <div class="modal-body">
            <div id="resp"></div>

            <div class="form-group">
              <label id="for_module_name">Select Menu <span class="text-red">*</span> </label>
              <select name="menu_name" id="menu_name" class="form-control menu_name">
                <option value="0">None</option>
                <?php
                  if($menus !='No data'){
                    foreach ($menus as $m_row) {
                      echo '<option value="'.$m_row['id'].'">'.$m_row['menu_name'].'</option>';
                    }
                  }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label id="for_module_name">Module Name <span class="text-red">*</span> </label>
              <input type="text" name="module_name" id="module_name" class="form-control module_name" placeholder="enter module name">
            </div>

            <div class="form-group">
              <label id="for_slug">Slug <span class="text-red">*</span> </label>
              <input type="text" name="slug" id="slug" class="form-control slug" placeholder="enter slug">
            </div>

            <input type="hidden" id="field_count" name="field_count" value="0">
            <div class="form-group" id="next_btn">
              <button type="button" class="btn btn-success pull-right save_and_next" onclick="saveAndNext()">Next</button>
              <div class="clearfix"></div>
            </div>       

            <div class="fields_data" id="fields_data"></div>
            
          </div>
          <div class="modal-footer" style="border-top-color: #337ab7; display: none" id="fotter_btns">
            <?php $this->ap->getButtons(); ?>
          </div>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.Add modal -->

<!-- Edit modal -->
<div class="modal fade" id="edit_model">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit</h4>
      </div>
      <div id="edit_model_body"></div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.edit modal -->

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
          <input type="hidden" id="data_type" name="data"> 
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
<!-- /.delete modal -->





<!-- /.modal -->




<!-- jQuery 3 -->
<script src="<?php echo base_url('assets/theme/'); ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI -->
<!-- <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script> -->
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

<script src="<?php echo base_url('assets/theme/'); ?>dist/js/module_generator_updated.js"></script>
<script>
  $(document).ready(function () {
    $('.sidebar-menu').tree()
  })
</script>
<script>
  $(function () {
    $('#example1').DataTable()
  })
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