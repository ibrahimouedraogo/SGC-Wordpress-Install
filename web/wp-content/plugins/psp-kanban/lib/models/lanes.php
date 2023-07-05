<?php
function psp_kb_get_default_lanes() {

     return apply_filters( 'psp_kb_default_lanes', array(
          'new' => array(
               'slug'         =>   'new',
               'label'        =>   __( 'New To-dos', 'psp_projects' ),
               'progress'     =>   false,
               'default'      =>   true,
               'updatable'    =>   true,
               'required'     =>   true,
          ),
          'icebox' => array(
               'slug'         =>   'icebox',
               'label'        =>  __( 'Icebox', 'psp_projects' ),
               'progress'     =>   false,
               'updatable'    =>   true,
          ),
          'backlog' => array(
               'slug'         =>   'backlog',
               'label'        =>  __( 'Backlog', 'psp_projects' ),
               'progress'     =>   false,
               'updatable'    =>   true,
          ),
          'progress' => array(
               'slug'         =>   'progress',
               'label'        =>__( 'In Progress', 'psp_projects' ),
               'progress'     =>   50,
               'updatable'    =>   true,
          ),
          'review' => array(
               'slug'         => 'review',
               'label'        => __( 'Review/QA', 'psp_projects' ),
               'progress'     =>   75,
               'updatable'    =>   true,
          ),
          'completed' => array(
               'slug'         => 'completed',
               'label'        => __( 'Completed', 'psp_projects' ),
               'progress'     =>   100,
               'updatable'    =>   false,
               'required'     =>   true,
          )
     ) );

}

function psp_kb_init_project( $post_id = null ) {

     if( $post_id == null ) {
          $post_id = get_the_ID();
     }

     $lanes = get_post_meta( $post_id, '_psp_kanban_lanes', true );

     // They've already been set!
     if( $lanes ) {
          // return $lanes;
     }

     $lanes = array();
     $default_lanes = psp_kb_get_default_lanes();

     $tasks = psp_kb_get_all_tasks($post_id);

     foreach( $default_lanes as $slug => $settings ) {

          $lane = $settings;
          $lane['tasks'] = array();

          if( !empty($settings['default']) ) {

               $lane['default'] = true;

               $lane['tasks'] = $tasks['new'];

          }

          if( $slug == 'completed' ) {
               $lane['tasks'] = $tasks['completed'];
          }

          $lanes[ $slug ] = $lane;

     }

     delete_post_meta( $post_id, '_psp_kanban_lane' );
     update_post_meta( $post_id, '_psp_kanban_lanes', $lanes );

     // $lanes = get_post_meta( $post_id, '_psp_kanban_lane', true );

     return apply_filters( 'psp_kb_init_project_lanes', $lanes );

}

function psp_kb_field_text( $field ) { ?>

     <div class="psp-form-field">
          <div class="psp-frontend-field">
               <label for="<?php echo esc_attr($field['name']); ?>"><?php echo esc_html( $field['label'] ); ?></label>
               <input type="text" name="<?php echo esc_attr($field['name']); ?>" value="<?php echo ( !empty($field['value']) ? $field['value'] : '' ); ?>" id="<?php echo esc_attr($field['name']); ?>">
          </div>
     </div>

     <?php

}

function psp_kb_field_select( $field ) { ?>

     <div class="psp-form-field">
          <div class="psp-frontend-field">
               <label for="<?php echo esc_attr($field['name']); ?>"><?php echo esc_html( $field['label'] ); ?></label>
               <div class="psp-select-wrapper">
                    <select name="<?php echo esc_attr($field['name']); ?>" id="<?php echo esc_attr($field['name']); ?>">
                         <?php
                         foreach( $field['options'] as $value => $label ): ?>
                              <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                         <?php endforeach; ?>
                    </select>
               </div>
          </div>
     </div>

     <?php

}

function psp_kb_field_hidden( $field ) { ?>

     <div class="psp-form-field">
          <div class="psp-frontend-field">
               <label for="<?php echo esc_attr($field['name']); ?>"><?php echo esc_html( $field['label'] ); ?></label>
               <input type="hidden" name="<?php echo esc_attr($field['name']); ?>" value="<?php echo ( !empty($field['value']) ? $field['value'] : '' ); ?>" id="<?php echo esc_attr($field['label']); ?>">
          </div>
     </div>

     <?php

}
