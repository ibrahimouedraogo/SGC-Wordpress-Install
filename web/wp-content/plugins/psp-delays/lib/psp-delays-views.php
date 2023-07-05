<?php

add_action( 'psp_before_quick_overview_title', 'psp_delays_frontend_info' );
function psp_delays_frontend_info() {

    $post_id = get_the_ID();

    $args = array(
        'post_type'         =>  'psp_delays',
        'posts_per_page'    =>  1,
        'meta_key'          =>  '_psp-delay-project',
        'meta_value'        =>  $post_id
    );

    $delays = new WP_Query( $args );

    $class = ( $delays->have_posts() ? '' : 'hide' ); ?>
        <div class="psp-delays-link-wrap">
            <a href="<?php the_permalink(); ?>?delays=true" class="psp-delays-link <?php echo esc_attr($class); ?>"><span class="delays-value" data-count="<?php echo esc_attr( $delays->found_posts ); ?>"><?php echo $delays->found_posts . '</span> ' . __( 'Delays', 'psp-delays' ); ?></a>
            <?php if( current_user_can( 'publish_psp_project_delays' ) ): ?>
                <a class="psp-delays-btn psp-modal-btn pano-btn" href="#psp-delays-modal"><?php esc_html_e( 'Add Delay', 'psp-delays' ); ?></a>
            <?php endif; ?>
       </div>
    <?php

    if( current_user_can( 'publish_psp_project_delays' ) ): ?>
        <div id="psp-delays-modal" class="psp-modal">
             <div class="psp-modal-content">

                  <div class="psp-modal-header">
                     <div class="psp-h2"><?php esc_html_e( 'Add Delay', 'psp-delays' ); ?></div>
                  </div>

                  <div class="psp-modal-form" id="psp-delays-modal-form">

                    <div class="success-message psp-hide psp-success">
                        <?php echo wpautop( __( 'Delay added to project.', 'psp-delays' ) ); ?>
                    </div>
                    <div class="error-message psp-hide psp-error">
                        <?php echo wpautop( __( 'Error adding delay to project. Please check internet connectivity and try again.', 'psp-delays' ) ); ?>
                    </div>

                    <input type="hidden" id="psp-project-id" value="<?php echo get_the_ID(); ?>">

                    <div class="psp-form-fields">
                        <?php
                        $meta_fields = apply_filters( 'psp_delays_fe_modal_fields', array(
                            array(
                                'id'        =>  'psp-date-occured',
                                'label'     =>  __( 'Date Occured', 'psp-delays' ),
                                'type'      =>  'text',
                                'classes'   =>  'psp-date psp-datepicker',
                                'required'  =>  'true'
                            ),
                            array(
                                'id'        =>  'psp-days-delayed',
                                'label'     =>  __( 'Days Delayed', 'psp-delays' ),
                                'type'      =>  'number',
                                'required'  =>  'true',
                            ),
                            array(
                                'id'        =>  'psp-delays-description',
                                'label'     =>  __( 'Description', 'psp-delays' ),
                                'type'      =>  'textarea',
                                'required'  =>  'true'
                            )
                        ) );

                        $standard_fields = apply_filters( 'psp_delays_fe_modal_standard_fields', array(
                            'text',
                            'date',
                            'email',
                            'password',
                            'number',
                            'hidden'
                        ) ); ?>

                        <?php foreach( $meta_fields as $field ): ?>
                            <div class="psp-form-field">

                                <label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>

                                <?php if( $field['type'] == 'textarea' ): ?>
                                    <textarea name="<?php echo esc_attr( $field['id'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" <?php if( isset( $field['classes'] ) ) { echo 'class="' . $field['classes'] . '"'; } ?> <?php if( isset( $field['required'] ) ) { echo ' required'; } ?>></textarea>
                                <?php endif; ?>

                                <?php if( in_array( $field['type'],  $standard_fields ) ): ?>
                                    <input type="<?php echo esc_attr( $field['type'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['id'] ); ?>" value="" <?php if( isset( $field['classes'] ) ) { echo 'class="' . $field['classes'] . '"'; } if( isset( $field['required'] ) ) { echo ' required'; } ?>>
                                <?php endif; ?>

                                <?php do_action( 'psp_delays_modal_meta_fields', $field, get_the_ID() ); ?>

                           </div>
                        <?php endforeach; ?>
                   </div>

                </div>

                <div class="psp-modal-actions">
                    <input type="submit" class="psp-delays-submit pano-btn pano-btn-primary" value="<?php echo esc_attr_e( 'Save', 'psp-delays' ); ?>"> <a href="#" class="modal-close modal_close hidemodal"><?php esc_html_e( 'Cancel', 'psp-delays' ); ?></a>
                </div>

           </div>
        </div>

    <?php
    endif;

}

add_action( 'psp_before_quick_overview', 'psp_delays_show_on_front_end' );
function psp_delays_show_on_front_end() {

    if( isset( $_GET[ 'delays'] ) ) {
        $post_id = get_the_ID();
        psp_the_delays_list( $post_id );
    }

}

function psp_the_delays_list( $post_id = NULL ) {

    $post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

    echo psp_get_delays_list( $post_id );

}

function psp_get_delays_list( $post_id = NULL ) {

    $post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

    $args = array(
        'post_type'         =>  'psp_delays',
        'posts_per_page'    =>  -1,
        'meta_key'          =>  '_psp-delay-project',
        'meta_value'        =>  $post_id
    );

    $delays = new WP_Query( $args );

    ob_start(); ?>

    <div class="psp-box">

        <div class="psp-h3"><?php esc_html_e( 'Project Delays', 'psp-delays' ); ?></div>

        <?php
        if( $delays->have_posts() ): ?>

            <table class="psp-delays-table">
                <thead>
                    <tr>
                        <th class="psp-delays-th-date"><?php esc_html_e( 'Date', 'psp-delays' ); ?></th>
                        <th class="psp-delays-th-days"><?php esc_html_e( 'Days Delayed', 'psp-delays' ); ?></th>
                        <th class="psp-delays-th-desc"><?php esc_html_e( 'Description', 'psp-delays' ); ?></th>
                        <th  class="psp-delays-th-actions"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while( $delays->have_posts() ): $delays->the_post();

                        global $post;

                        $data = array(
                            'postid'       =>  $post->ID,
                            'date'          =>  get_post_meta( $post->ID, '_psp-delay-date', true ),
                            'days'          =>  get_post_meta( $post->ID, '_psp-delay-days', true ),
                            'description'   =>  get_post_meta( $post->ID, '_psp-delay-description', true )
                        ); ?>

                        <tr <?php foreach( $data as $key => $val ) echo 'data-' . $key . '="' . esc_attr( $val ) . '"'; ?>>
                            <td class="psp-delay-date-td"><small><?php echo esc_html( date( get_option('date_format'), strtotime(get_post_meta( $post->ID, '_psp-delay-date', true )) ) ); ?></small></td>
                            <td class="psp-text-center psp-delay-days-td"><?php echo esc_html( get_post_meta( $post->ID, '_psp-delay-days', true ) ); ?></td>
                            <td class="psp-delay-descr-td"><?php echo esc_html( get_post_meta( $post->ID, '_psp-delay-description', true ) ); ?></td>
                            <td class="psp-delay-actions-td">
                                <?php if( current_user_can( 'edit_psp_project_delays' ) ): ?>
                                    <a href="#" class="psp-delay-edit"><i class="fa fa-pencil"></i> <?php esc_html_e( 'Edit', 'psp-delays' ); ?></a>
                                <?php endif; ?>
                                <?php if( current_user_can( 'delete_psp_project_delays' ) ): ?>
                                    <a href="#" class="psp-delay-delete" data-nonce="<?php echo esc_attr( wp_create_nonce( 'psp_delays_delete_' . $post->ID ) ); ?>"><i class="fa fa-trash"></i> <?php esc_html_e( 'Delete', 'psp-delays' ); ?></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; wp_reset_query(); ?>
                    <?php if( current_user_can( 'edit_psp_project_delays' ) ): ?>
                        <tr class="psp-hide psp-delay-clone-row" data-postid="null">
                            <td>
                                <label for="psp-delay-date"><?php esc_html_e( 'Delay Date', 'psp-delays' ); ?></label>
                                <input type="text" class="psp-delay-date" name="psp-delay-date" class="psp-datepicker" value="">
                            </td>
                            <td>
                                <label for="psp-delay-days"><?php esc_html_e( 'Days Delayed', 'psp-delays' ); ?></label>
                                <input type="number" name="psp-delay-days" class="psp-delay-days" value="">
                            </td>
                            <td>
                                <label for="psp-delay-description"><?php esc_html_e( 'Description', 'psp-delays' ); ?></label>
                                <textarea class="psp-delay-description" name="psp-delay-description"></textarea>
                            </td>
                            <td class="psp-delays-edit-actions-td"><a class="pano-btn pano-btn-primary psp-delay-update" href="#"><?php esc_html_e( 'Update', 'psp-delays' ); ?></a> <a href="#" class="psp-delay-cancel"><?php esc_html_e( 'Cancel', 'psp-delays' ); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

    <?php else: ?>

        <div class="psp-notice">
            <div class="psp-p"><?php esc_html_e( 'No delays recorded on this project.', 'psp-delays' ); ?></div>
        </div>

    <?php endif; ?>

    </div>

    <?php
    return ob_get_clean();

}
