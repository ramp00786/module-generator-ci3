<?php $slug = base_url(uri_string()); $ex = explode('/', $slug);  $pagename = end($ex); $called_menu = $this->uri->segment(2); ?>
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="text-center image">
          <img src="<?php echo base_url().logo ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          &nbsp;
        </div>
      </div>
      <!-- search form -->
      
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>

        
        <li class="<?php if($pagename=='dashboard')echo 'active'; ?>"><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

        <?php if($this->auth_level == 9){ ?>
          <li class="<?php if($pagename=='Menu')echo 'active'; ?>"><a href="<?php echo base_url('Menu'); ?>"><i class="fa fa-bars"></i> <span>Menu</span></a></li>

          

          



          <!-- <li class="<?php if($pagename=='Modules')echo 'active'; ?>"><a href="<?php echo base_url('Modules'); ?>"><i class="fa fa-bars"></i> <span>Modules</span></a></li> -->

          <li class="<?php if($pagename=='Modulegenerator')echo 'active'; ?>"><a href="<?php echo base_url('Modulegenerator'); ?>"><i class="fa fa-bars"></i> <span>Module Generator</span></a></li>

          <li class="<?php if($pagename=='Users')echo 'active'; ?>"><a href="<?php echo base_url('Users'); ?>"><i class="fa fa-bars"></i> <span>Users</span></a></li>

          <li class="<?php if($pagename=='Userpermission')echo 'active'; ?>"><a href="<?php echo base_url('Userpermission'); ?>"><i class="fa fa-bars"></i> <span>User Permissions</span></a></li>


        <?php } ?>



        <?php  

          // --- User modules
          
          if($modulePermission !='No data' && $modulePermission !='Table not exists'){
            $module_ids = array();
            foreach($modulePermission as $permission_info){
              $module_ids[] = $permission_info['module_id'];
            }
            $module_ids_str = implode(',', $module_ids);
          }
          else{
            $module_ids_str = '0';
          }

      
          
          $menus = $this->tfn->getData('id, menu_name', 'menus', "status = 1 ");         
          if($menus !='No data' && $menus != 'Table not exists'){
            foreach ($menus as $m_row) {
              
              $menu_slugs = array();
              $sub = '';
              $menu_name = $this->ap->MenuIdToName($m_row['id']);
              $menu_name = str_replace(' ', '-', $menu_name);

              if($this->auth_level == 9){
                $modules_group = $this->tfn->getData('slug, module_name', 'modules', "status = 1 AND menu_id = '".$m_row['id']."' ");
              }
              else{
                $modules_group = $this->tfn->getData('slug, module_name', 'modules', "status = 1 AND menu_id = '".$m_row['id']."' AND id IN (".$module_ids_str.") ");
              }

              

              if($modules_group !='No data'){
                $sub .= '<ul class="treeview-menu">';
                foreach ($modules_group as $module_info) {
                  if($pagename == $module_info['slug']){
                    $page_cls = 'active';
                  }
                  else{
                    $page_cls = '';
                  }
                  $sub .= '<li class=" '.$page_cls.' ">';
                  $url = "'".base_url('Module/').$menu_name.'/'.$module_info['slug']."'";
                  $params = "'".$m_row['id']."', '".$module_info['slug']."', ".$url."";                                 
                  $sub .= '<a onclick="redirecToPage('.$params.')" href="javascript:void(0)"><i class="fa fa-bars"></i> <span>'.$module_info['slug'].'</span></a>';
                  $menu_slugs[] = $menu_name.'/'.$module_info['slug'];
                  $sub .= '</li>';

                  // Static Menu for PIs Portal
                  
                  if($menu_name == 'PIs-Portal'){

                    //---Active class
                    if($pagename == 'Documents'){
                      $page_cls_pis = 'active';
                    }
                    else{
                      $page_cls_pis = '';
                    }

                    $sub .= '<li class=" '.$page_cls_pis.' ">';
                    $sub .= '<a href="'.base_url('PIsPortal/Documents').'"><i class="fa fa-bars"></i> <span>Documents</span></a>';

                    $menu_slugs[] = "Documents/Documents";
                  }

                }
                $sub .= '</ul>';
              }
              // echo "<div style='background-color:white'>";
              // print_r($menu_slugs);
              // echo $called_menu.'/'.$pagename;
              // echo "</div>";

              if(in_array($called_menu.'/'.$pagename, $menu_slugs)){
                $cls_act = 'active';
              }
              else{
                $cls_act = '';
              }
              echo '<li class="treeview '.$cls_act.' ">';
              echo '<a href="#">
                      <i class="fa fa-plus"></i> <span>'.$m_row['menu_name'].'</span>
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>';
              echo $sub;
              echo '</li>';
            }
          }


          $modules_group = $this->tfn->getData('slug', 'modules', "status = 1 AND menu_id != 0 "); 

          $homePages = array('Activity');

          if(in_array($pagename, $homePages)){
            $homeCls = 'active';
          }else{
            $homeCls = '';
          }

         ?>

        
        <!-- <li class="treeview <?php echo $homeCls; ?>">
          <a href="#">
            <i class="fa fa-gears"></i> <span>Master</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php if($pagename=='Activity')echo 'active'; ?>"><a href="<?php echo base_url('Activity'); ?>"><i class="fa fa-bars"></i> <span>Activities</span></a></li>
          </ul>
        </li> -->



        


        <?php 

          if($this->auth_level == 9){
            $modules = $this->tfn->getData('slug,module_name', 'modules', "status = 1 AND menu_id = '0' ");
          }
          else{
            $modules = $this->tfn->getData('slug,module_name', 'modules', "status = 1 AND menu_id = '0' AND id IN (".$module_ids_str.") ");
          }

          if($modules !='No data' && $modules !='Table not exists'){
            foreach ($modules as $row) { $module_slug = $row['slug']; 
              $url = "'".base_url('Module/').'None/'.$module_slug."'";
              $params_2 = "'0', '".$module_slug."', ".$url."";
            ?>
              <li class="<?php if($pagename==$module_slug)echo 'active'; ?>"><a onclick="redirecToPage(<?php echo $params_2 ?>)" href="javascript:void(0)"><i class="fa fa-circle-o"></i> <span><?php echo $module_slug ?></span></a></li>
              
              <?php
            }
          }

        ?>

        

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <script type="text/javascript">
    var baseurl = '<?php echo base_url(); ?>';
  </script>