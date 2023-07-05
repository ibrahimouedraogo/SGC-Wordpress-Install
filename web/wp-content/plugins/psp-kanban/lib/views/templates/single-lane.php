<?php
$class = apply_filters( 'psp_lane_class', 'psp-lane ' . 'psp-lane-' . $lane['slug'] . ' psp-lane-tasks-' . ( !empty($lane['tasks']) ? count($lane['tasks']) : '0' ) . ( $lane['updatable'] ? '' : ' psp-lane-no-edit' ), $lane ); ?>

<?php do_action( 'psp_before_lane', $lane, $post_id ); ?>

<div class="<?php echo esc_attr($class); ?>" data-slug="<?php echo esc_attr($lane['slug']); ?>" data-progress="<?php echo esc_attr( ( $lane['progress'] ? $lane['progress'] : 'false' ) ); ?>" data-updatable="<?php echo esc_attr( ( $lane['updatable'] ? 'true' : 'false' ) ); ?>">

     <?php do_action( 'psp_before_lane_heading', $lane, $post_id ); ?>

     <div class="psp-lane__label">

          <?php do_action( 'psp_before_lane_title', $lane, $post_id ); ?>

          <div class="psp-h3">
               <span class="psp-lane-handle"><?php echo wp_kses_post($lane['label']); ?></span>
          </div>

          <div class="psp-lane__menu">
               <span class="psp-lane__menu-trigger">&#8230;</span>
               <div class="psp-lane__menu-items">
                    <?php
                    $items = apply_filters( 'psp_kb_lane_menu_items', array(
                         'edit'    =>   array(
                              'label'   =>   __( 'Edit', 'psp_projects' ),
                              'link'    =>   '#psp-edit-lane',
                              'condition' => true,
                              'class'   =>   'psp-modal-btn psp-js-edit-lane'
                         ),
                         'delete'  =>   array(
                              'label'   =>   __( 'Delete', 'psp_projects' ),
                              'link'    =>   '#',
                              'class'   =>   'psp-js-del-lane',
                              'condition' => true
                         )
                    ) );

                    if( isset($lane['required']) && $lane['required'] ) {
                         unset($items['delete']);
                    }

                    foreach( $items as $slug => $item ):

                         if( !$item['condition'] ) {
                              continue;
                         }

                         $class = 'psp-lane-menu--' . $slug . ' ' . ( !empty($item['class']) ? $item['class'] : '' ); ?>

                         <a href="<?php echo esc_url($item['link']); ?>" class="<?php echo esc_attr($class); ?>"><?php echo esc_html( $item['label'] ); ?></a>

                    <?php endforeach; ?>
               </div>
          </div>

          <?php do_action( 'psp_after_lane_title', $lane, $post_id ); ?>

     </div>

     <?php do_action( 'psp_after_lane_heading', $lane, $post_id ); ?>

     <div class="psp-lane__container" data-slug="<?php echo esc_attr($lane['slug']); ?>" data-progress="<?php echo esc_attr( ( $lane['progress'] ? $lane['progress'] : 'no' ) ); ?>" data-updatable="<?php echo esc_attr( ( $lane['updatable'] ? 'yes' : 'no' ) ); ?>">
          <?php
          do_action( 'psp_before_lane_tasks', $lane, $post_id );
          if( !empty($lane['tasks']) ):
               foreach( $lane['tasks'] as $task_id ):
                    include('task-single.php');
               endforeach;
          endif;
          do_action( 'psp_after_lane_tasks', $lane, $post_id ); ?>
     </div>

     <?php do_action( 'psp_after_lane_tasks_wrapper', $lane, $post_id ); ?>

</div>

<?php do_action( 'psp_after_lane', $lane, $post_id ); ?>
