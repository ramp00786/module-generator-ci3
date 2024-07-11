<!-- jQuery 3 -->

<header class="main-header">
   <!-- Logo -->
   <a href="<?php echo base_url(); ?>" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b><?php echo app_name_short; ?></b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b><?php echo str_replace('|', '', app_name); ; ?></b></span>
   </a>
   <!-- Header Navbar: style can be found in header.less -->
   <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
      </a>
      <div class="navbar-custom-menu">
         <ul class="nav navbar-nav">
            <!-- Messages: style can be found in dropdown.less-->
            
            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown">
               <img src="<?php echo base_url().logo ?>" class="user-image" alt="User Image">
               <span class="hidden-xs">
               <?php 
               $userInfo = $this->tfn->getData('*', "users", "user_id = '".$this->auth_user_id."' ");
               echo $userInfo[0]['first_name'];
               //echo str_replace('|', '', app_name); 
               //echo $this->auth_user_id;  ?>                
               </span>
               </a>
               <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                     <img src="<?php echo base_url().logo ?>" class="img-circle" alt="User Image">
                     <p>
                        <?php echo $userInfo[0]['first_name'].' '.$userInfo[0]['last_name']; ?> ( <?php echo $this->auth_user_id; ?> )                  
                     </p>
                  </li>
                  <!-- Menu Body -->
                  <!-- Menu Footer-->
                  <li class="user-footer">
                     <div class="pull-left">
                        <a href="<?php echo base_url(); ?>changepassword" class="btn btn-default btn-flat">Change Password</a>
                     </div>
                     <div class="pull-right">
                        <a href="<?php echo base_url(); ?>login/logout" class="btn btn-default btn-flat">Sign out</a>
                     </div>
                  </li>
               </ul>
            </li>
            <!-- Control Sidebar Toggle Button -->
            <!-- <li>
               <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
            </li> -->
         </ul>
      </div>
   </nav>
</header>

<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';
    var ci_controller = '<?php if(isset($controller)) echo $controller; ?>';
</script>
