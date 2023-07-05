<?php
// Load this here to pass into future functions and minimize DB queries
$lane_phases = apply_filters( 'psp_kb_lane_phases', get_field( 'phases', $post_id ) ); ?>

<?php do_action( 'psp_before_project_lanes', $lanes ); ?>
<div class="psp-kanban psp-section" data-post_id="<?php echo esc_attr( $post_id ); ?>" id="<?php echo esc_attr('psp-kanban-' . $post_id ); ?>">
     <div class="psp-wrapper">
          <div class="psp-section-heading psp-row">
               <div class="psp-h2 psp-section-title">
                    <a href="<?php echo esc_url( add_query_arg( 'psp_view', 'phases', get_the_permalink() ) ); ?>#psp-phases" class="psp-kb-switch" view="phases">
                         <?php
                         echo file_get_contents( PSP_KB_PATH . 'assets/img/phases.svg' ); ?>
                         <?php esc_html_e( 'Phases', 'psp_projects' ); ?>
                    </a>
                    <span class="is-active">
                         <?php
                         echo file_get_contents( PSP_KB_PATH . 'assets/img/board.svg' ); ?>
                         <?php esc_html_e( 'Board', 'psp_projects' ); ?>
                    </span>
               </div>

               <a href="#psp-kb-add-lane" class="psp-modal-btn js-psp-kb-add-lane pano-btn"><?php esc_html_e( 'Add List', 'psp_projects' ); ?></a>
          </div>
     </div>
     <div class="psp-lanes">
          <div class="psp-lanes-wrap">
               <?php /*
               <div class="psp-lanes__headings">
                    <?php
                    foreach( $lanes as $slug => $lane ):
                         include('single-lane-heading.php');
                    endforeach; ?>
               </div> */ ?>

               <div class="psp-lanes__cards">
                    <?php
                    do_action( 'psp_project_lanes_start', $lanes );

                    if( $lanes ):
                         foreach( $lanes as $slug => $lane ):
                              include('single-lane.php');
                         endforeach;
                    endif;

                    do_action( 'psp_project_lanes_end', $lanes );?>

               </div>
          </div>
     </div>
</div>
<?php do_action( 'psp_after_project_lanes', $lanes ); ?>

<div class="psp-kanban-modals">
     <?php
     $modals = apply_filters( 'psp_kanban_modal_templates', array(
          'add-lane',
          'edit-lane',
     ) );

     foreach( $modals as $modal ) {
          include_once( 'modals/' . $modal . '.php' );
     } ?>
</div>

<div class="psp-hide">

</div>
