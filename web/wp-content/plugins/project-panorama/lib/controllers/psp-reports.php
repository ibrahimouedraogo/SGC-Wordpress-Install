<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
     require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class PSP_Projects_List extends WP_List_table {

     public function prepare_items() {

          $columns = $this->get_columns();
          $hidden = $this->get_hidden_columns();
          $sortable = $this->get_sortable_columns();

          $data = $this->table_data();
          usort( $data, array( &$this, 'sort_data' ) );

          $perPage = 10;
          $currentPage = $this->get_pagenum();
          $totalItems = count($data);

          $this->set_pagination_args( array(
               'total_items' => $totalItems,
               'per_page'    => $perPage
          ) );

          $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

          $this->_column_headers = array($columns, $hidden, $sortable);
          $this->items = $data;

     }

     public function get_columns() {

          $columns = array(
               'project'           => __( 'Project', 'psp_projects' ),
               'progress'          => __( 'Progress', 'psp_projects' ),
               'status'            => __( 'Status', 'psp_projects' ),
               'schedule'          => __( 'Schedule', 'psp_projects' ),
               'tasks'             => __( 'Tasks', 'psp_projects' ),
               'milestones'        => __( 'Milestones', 'psp_projects' )
          );

          return $columns;
     }


     /**
     * Define which columns are hidden
     *
     * @return Array
     */
     public function get_hidden_columns() {
          return array();
     }

     /**
     * Define the sortable columns
     *
     * @return Array
     */
     public function get_sortable_columns() {
          return array(
               'project'      =>   array( 'project', false),
               'status'       =>   array( 'status', false ),
               'schedule'       =>   array( 'schedule', false ),
          );
     }

     /**
     * Get the table data
     *
     * @return Array
     */
     private function table_data() {

          $cuser    = wp_get_current_user();
          $projects = psp_get_all_my_project_ids();

          if( empty($projects) ) {
               return false;
          }

          $data = array();

          foreach( $projects as $project_id ) {

               $status        = psp_get_project_status( $project_id );
               $completion    = psp_compute_progress( $project_id );
               $time_elapsed 	= psp_calculate_timing( $project_id );
               $client        = get_field( 'client', $project_id );

               if( !$completion ) {
                    $completion = '0';
               }

               // Timing
               $timing = array(
                    'slug'    => ( $completion < $time_elapsed[ 'percentage_complete' ] ? 'behind' : 'on-schedule' ),
                    'label'   => ( $completion < $time_elapsed['percentage_complete'] ? __( 'Behind', 'psp_projects' ) : __( 'On Schedule', 'psp_projects' ) ),
               );

               // Progress Output
               $progress = '<p class="psp-progress"><span class="psp-' . $completion . '"><strong>' . $completion . '%</strong></span></p>';
               ob_start();
               psp_the_timing_bar( $project_id );
               $progress .= ob_get_clean();

               // Tasks

               // Milestones
               $milestones = array(
                    'total'   =>   0,
                    'complete'     =>   0,
                    'overdue' => 0
               );

               if( $completion == '100' ) {

                    $status = array(
                         'slug'    =>   'completed',
                         'label'   =>   __( 'Completed', 'psp_projects' )
                    );

                    $timing = array(
                         'slug'    =>   'completed',
                         'label'   =>   __( 'Completed', 'psp_projects' )
                    );

               }

               $data[] = array(
                    'project'      => '<a class="psp-report__title" href="' . get_the_permalink($project_id) . '">' . get_the_title( $project_id ) . '</a>' . ( $client ? ' <span class="psp-client">' . get_field( 'client', $project_id ) . '</span>' : '' ),
                    'progress'     => $progress,
                    'status'       => '<span class="psp-bug psp-status-' . $status['slug'] . '">' . $status['label'] . '</span>',
                    'schedule'     => '<span class="psp-bug psp-timing-' . $timing['slug'] . '">' . $timing['label'] . '</span>',
                    'tasks'        => psp_report_project_task_stats( $project_id ),
                    'milestones'   => psp_report_project_milestone_stats( $project_id ),
               );



          }

          return $data;

    }

    public function column_default( $item, $column_name ) {

        switch( $column_name ) {
            case 'id':
            case 'project':
            case 'progress':
            case 'status':
            case 'schedule':
            case 'tasks':
            case 'milestones':
                return $item[ $column_name ];

            default:
                return print_r( $item, true ) ;
        }

    }

    /**
    * Allows you to sort the data by the variables set in the $_GET
    *
    * @return Mixed
    */
   private function sort_data( $a, $b ) {
       // Set defaults
       $orderby = 'project';
       $order = 'asc';

       // If orderby is set, use this as the sort column
       if(!empty($_GET['orderby']))
       {
           $orderby = $_GET['orderby'];
       }

       // If order is set use this as the order
       if(!empty($_GET['order']))
       {
           $order = $_GET['order'];
       }


       $result = strcmp( $a[$orderby], $b[$orderby] );

       if($order === 'asc')
       {
           return $result;
       }

       return -$result;

   }

}

class PSP_User_List extends WP_List_table {

     public function prepare_items() {

          $columns = $this->get_columns();
          $hidden = $this->get_hidden_columns();
          $sortable = $this->get_sortable_columns();

          $data = $this->table_data();
          usort( $data, array( &$this, 'sort_data' ) );

          $perPage = 10;
          $currentPage = $this->get_pagenum();
          $totalItems = count($data);

          $this->set_pagination_args( array(
               'total_items' => $totalItems,
               'per_page'    => $perPage
          ) );

          $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

          $this->_column_headers = array($columns, $hidden, $sortable);
          $this->items = $data;

     }

     public function get_columns() {

          $columns = array(
               'user'           => __( 'User', 'psp_projects' ),
               'projects'       => __( 'Active Projects', 'psp_projects' ),
               'projects_behind' => __( 'Projects Behind', 'psp_projects' ),
               'open_tasks'       => __( 'Open Tasks', 'psp_projects' ),
               'overdue_tasks'        => __( 'Overdue Tasks', 'psp_projects' ),
          );

          return $columns;
     }


     /**
     * Define which columns are hidden
     *
     * @return Array
     */
     public function get_hidden_columns() {
          return array();
     }

     /**
     * Define the sortable columns
     *
     * @return Array
     */
     public function get_sortable_columns() {
          return array(
               'projects'          =>   array( 'projects', false ),
               'projects_behind'   =>   array( 'projects_behind', false ),
               'open_tasks'        =>   array( 'overdue', false ),
               'overdue_tasks'     =>   array( 'assigned', false ),
          );
     }

     /**
     * Get the table data
     *
     * @return Array
     */
     private function table_data() {

          $data  = array();
          $users = get_users( array(
               'fields' => array( 'ID', 'display_name' )
          ) );

          foreach( $users as $user ) {

               $user_id  = $user->ID;

               $args = array(
          		'post_type'		=> 'psp_projects',
          		'posts_per_page'	=> -1,
                    'tax_query'         => array(
                         array(
                              'taxonomy'	=>	'psp_status',
                              'field'		=>	'slug',
                              'terms'		=>	array( 'completed', 'hold', 'canceled' ),
                              'operator'	=>	'NOT IN'
                              )
                         ),
                    'meta_query' => array(
                         'relation'	=>	'OR',
          	       	array(
          				'key' 	=> 'allowed_users_%_user',
          				'value' => $user_id
          			),
          			array(
          				'key'	=>	'_psp_post_author',
          				'value'	=>	$user_id
          			)
                    )
               );

               $projects = new WP_Query($args);
               $behind_projects = 0;

               while( $projects->have_posts() ) { $projects->the_post();

                    $project_id = get_the_ID();

                    $completion    = psp_compute_progress( $project_id );
                    $time_elapsed 	= psp_calculate_timing( $project_id );

                    if( !$completion ) {
                         $completion = '0';
                    }

                    if( $completion < $time_elapsed['percentage_complete'] ) {
                         $behind_projects++;
                    }

               }

               if( get_option('permalink_structure') ) {
                    $userlink = trailingslashit( get_post_type_archive_link('psp_projects') ) . 'user/' . $user_id;
               } else {
                    $userlink = add_query_arg( array( 'user' => $user_id ), get_post_type_archive_link('psp_projects') );
               }

               $user_data = array(
                    'user'              => '<a href="' . $userlink . '">' . get_avatar( $user_id ) . ' <strong>' . ' ' . $user->display_name . '</strong></a>',
                    'projects'          => ( $projects->found_posts > 0 ? $projects->found_posts : '' ),
                    'projects_behind'   => ( $behind_projects > 0 ? $behind_projects : '' ),
                    'open_tasks'        => 0,
                    'overdue_tasks'     => '0'
               );

               $tasks = psp_get_user_task_stats($user_id);

               $user_data['open_tasks'] = ( $tasks['total'] - $tasks['completed'] > 0 ? $tasks['total'] - $tasks['completed'] : '' );
               $user_data['overdue_tasks'] = ( $tasks['overdue'] > 0 ? $tasks['overdue'] : '' );

               $data[] = $user_data;

          }

          return $data;

    }

    public function column_default( $item, $column_name ) {

        switch( $column_name ) {
            case 'user':
            case 'projects':
            case 'projects_behind':
            case 'overdue_tasks':
            case 'open_tasks':
                return $item[ $column_name ];

            default:
                return print_r( $item, true ) ;
        }

    }

    /**
    * Allows you to sort the data by the variables set in the $_GET
    *
    * @return Mixed
    */
   private function sort_data( $a, $b ) {
       // Set defaults
       $orderby = 'user';
       $order = 'asc';

       // If orderby is set, use this as the sort column
       if(!empty($_GET['orderby']))
       {
           $orderby = $_GET['orderby'];
       }

       // If order is set use this as the order
       if(!empty($_GET['order']))
       {
           $order = $_GET['order'];
       }


       $result = strcmp( $a[$orderby], $b[$orderby] );

       if($order === 'asc')
       {
           return $result;
       }

       return -$result;

   }

}

function psp_report_project_task_stats( $post_id = null ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     $tasks = psp_get_project_task_stats( $post_id );

     $stats = array(
          array(
               'label'   =>   __( 'Complete', 'psp_projects' ),
               'stat'    =>   $tasks['complete'] . '/' . $tasks['total'],
          ),
          array(
               'label'   =>   __( 'In Progress', 'psp_projects' ),
               'stat'    =>   $tasks['in-progress'],
          ),
          array(
               'label'   =>   __( 'Overdue', 'psp_projects' ),
               'stat'    =>   $tasks['late']
          )
     );

     ob_start(); ?>

     <div class="psp-report-stats">
          <?php
          foreach( $stats as $stat ): ?>
               <div class="psp-report-stat <?php echo esc_attr( sanitize_title( 'stat-' . $stat['label'] ) . ' count-' . sanitize_title($stat['stat']) ); ?>">
                    <div class="psp-report-stat__num">
                         <?php echo esc_html( $stat['stat'] ); ?>
                    </div>
                    <div class="psp-report-stat__label">
                         <?php echo esc_html( $stat['label'] ); ?>
                    </div>
               </div>
          <?php endforeach; ?>
     </div>

     <?php
     return ob_get_clean();

}

function psp_report_project_milestone_stats( $post_id = null ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     $milestones = psp_get_project_milestone_stats( $post_id );

     $stats = array(
          array(
               'label'   =>   __( 'Complete', 'psp_projects' ),
               'stat'    =>   $milestones['complete'] . '/' . $milestones['total'],
          ),
          array(
               'label'   =>   __( 'Overdue', 'psp_projects' ),
               'stat'    =>   $milestones['late']
          )
     );

     ob_start(); ?>

     <div class="psp-report-stats">
          <?php
          foreach( $stats as $stat ): ?>
               <div class="psp-report-stat <?php echo esc_attr( sanitize_title( 'stat-' . $stat['label'] ) . ' count-' . sanitize_title($stat['stat']) ); ?>">
                    <div class="psp-report-stat__num">
                         <?php echo esc_html( $stat['stat'] ); ?>
                    </div>
                    <div class="psp-report-stat__label">
                         <?php echo esc_html( $stat['label'] ); ?>
                    </div>
               </div>
          <?php endforeach; ?>
     </div>

     <?php
     return ob_get_clean();

}
