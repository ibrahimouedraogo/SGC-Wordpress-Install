<?php
add_action( 'admin_menu', 'register_psp_user_reports' );
function register_psp_user_reports() {
    add_submenu_page( 'edit.php?post_type=psp_projects', __( 'Reports', 'psp_projects' ), __( 'Reports', 'psp_projects' ), 'edit_others_psp_projects', 'psp_reports', 'psp_admin_display_reports' );
}

function psp_admin_display_reports() {

     psp_admin_assets( false );

     $tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'projects' ); ?>

     <div class="wrap">

         <h2><?php esc_html_e( 'Reports', 'psp_projects' ); ?></h2>

         <nav class="nav-tab-wrapper">
              <a href="edit.php?post_type=psp_projects&page=psp_reports&tab=projects" class="nav-tab <?php if( $tab == 'projects' ): ?>nav-tab-active<?php endif; ?>"><?php esc_html_e( 'Projects', 'psp_projects' ); ?></a>
              <a href="edit.php?post_type=psp_projects&page=psp_reports&tab=users" class="nav-tab <?php if( $tab == 'users' ): ?>nav-tab-active<?php endif; ?>"><?php esc_html_e( 'Users', 'psp_projects' ); ?></a>
         </nav>

         <?php
         if( $tab == 'projects' ):

              $projectList = new PSP_Projects_List();
              $projectList->prepare_items(); ?>

              <div class="psp-report__projects psp-report">
                   <?php $projectList->display(); ?>
              </div>

         <?php
         endif;

         if( $tab == 'users' ):

            $userList = new PSP_User_List();
            $userList->prepare_items(); ?>

            <div class="psp-report__users psp-report">
                 <?php $userList->display(); ?>
            </div>

         <?php
         endif; ?>

    </div>

<?php
}
