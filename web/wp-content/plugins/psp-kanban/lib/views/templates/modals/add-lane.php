<div class="psp-modal psp-add-lane psp-hide" id="psp-kb-add-lane">
     <a class="modal-close psp-modal-x" href="#"><i class="fa fa-close"></i></a>

     <div class="psp-modal-header">
          <div class="psp-h2"><?php esc_html_e( 'Add List', 'psp_projects' ); ?></div>
     </div>

     <form method="" action="post" class="psp-add-lane-form" data-post_id="<?php echo esc_attr($post_id); ?>">
          <div class="psp-form-fields">

               <?php
               $fields = apply_filters( 'psp_kb_upate_lane_fields', array(
                    'post_id' => array(
                         'type'    =>   'hidden',
                         'value'   =>   $post_id,
                         'name'    =>   'post_id'
                    ),
                    'label' => array(
                         'label'   =>   __( 'Name', 'psp_projects' ),
                         'type'    =>   'text',
                         'value'   =>   '',
                         'name'    =>   'label'
                    ),
                    'progress' => array(
                         'label'   =>   __( 'Update task progress to', 'psp_projects' ),
                         'name'    =>   'progress',
                         'type'    =>   'select',
                         'value'   =>   '',
                         'options' =>   psp_kb_get_progress_options(),
                    ),
                    'updatable' => array(
                         'label'   =>   __( 'Users can manually update task progress', 'psp_projects' ),
                         'name'    =>   'updatable',
                         'type'    =>   'select',
                         'value'   =>   '',
                         'options' =>   array(
                              'yes' => 'Yes',
                              'no'  => 'No'
                         )
                    )
               ) );

               foreach( $fields as $slug => $field ) {

                    call_user_func( 'psp_kb_field_' . $field['type'], $field );

               } ?>

          </div>
          <div class="psp-modal-actions">
               <input type="submit" class="pano-btn pano-btn-primary" value="<?php esc_attr_e( 'Add List', 'psp_projects' ); ?>">
          </div>
     </form>

</div>
