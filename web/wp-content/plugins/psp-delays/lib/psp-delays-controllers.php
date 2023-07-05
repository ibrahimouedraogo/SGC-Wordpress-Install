<?php
add_filter( 'acf/load_value/key=field_527d5d57fb853', 'psp_adjust_project_end_date', 10, 3 );
function psp_adjust_project_end_date( $date, $post_id ) {

    if( ( is_admin() ) || ( defined('DOING_AJAX') && !DOING_AJAX ) || ( get_query_var('psp_manage_page') ) ) return $date;

    $args = array(
        'post_type'         =>  'psp_delays',
        'posts_per_page'    =>  -1,
        'meta_key'          =>  '_psp-delay-project',
        'meta_value'        =>  $post_id,
        'fields'            =>  'ID'
    );

    $delays = get_posts( $args );

    // No delays found, return!
    if( empty( $delays ) ) return $date;

    $days = 0;

    foreach( $delays as $delay ) {
        $days += intval( get_post_meta( $delay->ID, '_psp-delay-days', true ) );
    }

    return date( 'Ymd', strtotime( $date . ' +'. $days . ' weekday' ) );

}

add_action( 'wp_ajax_nopriv_psp_update_delay_fe', 'psp_update_delay_fe' );
add_action( 'wp_ajax_psp_update_delay_fe', 'psp_update_delay_fe' );
function psp_update_delay_fe() {

    if( !isset( $_POST[ 'delay_id' ] ) || !current_user_can( 'delete_psp_project_delays' ) )
        wp_send_json_error( array( 'success' => false ) );

    if( get_post_type( $_POST[ 'delay_id'] ) != 'psp_delays' )
        wp_send_json_error( array( 'success' => false ) );

    $metas = apply_filters( 'psp_update_delay_fe_meta', array(
        array(
            'key'   =>  'psp-delay-days',
            'val'   =>  $_POST[ 'days' ],
            'type'  =>  'int'
        ),
        array(
            'key'   =>  'psp-delay-date',
            'val'   =>  $_POST[ 'date' ],
            'type'  =>  'string',
        ),
        array(
            'key'   =>  'psp-delay-description',
            'val'   =>  $_POST[ 'description' ],
            'type'  =>  'string',
        )
    ) );

    foreach( $metas as $meta ) {

        $val = $meta['val'];

        if( $meta['type'] == 'string' ) $val = sanitize_text_field( $meta['val'] );

        if( $meta['type'] == 'int' ) $val = intval( $meta['val'] );

        update_post_meta( $_POST[ 'delay_id' ], '_' . $meta['key'], $val );

    }

    $end_date = psp_adjust_project_end_date_txt( get_field( 'end_date', $_POST['project_id'] ), $_POST['project_id'] );

    wp_send_json_success( $end_date );

    exit();

}

add_action( 'wp_ajax_nopriv_psp_delete_delay_fe', 'psp_delete_delay_fe' );
add_action( 'wp_ajax_psp_delete_delay_fe', 'psp_delete_delay_fe' );
function psp_delete_delay_fe() {

    // Somethings not right, bail!
    // if( !isset( $_POST[ 'delay_id' ] ) || !check_admin_referer( 'psp_delays_delete_' . $_POST[ 'project_id' ] ) || !current_user_can( 'delete_psp_project_delays' ) )
    if( !isset( $_POST[ 'delay_id' ] ) || !current_user_can( 'delete_psp_project_delays' ) )
        wp_send_json_error( array( 'success' => false ) );

    if( get_post_type( $_POST[ 'delay_id'] ) != 'psp_delays' )
        wp_send_json_error( array( 'success' => false ) );

    wp_delete_post( $_POST[ 'delay_id' ], true );

    $end_date = psp_adjust_project_end_date_txt( get_field( 'end_date', $_POST['project_id'] ), $_POST['project_id'] );

    wp_send_json_success( $end_date );

    exit();

}

add_action( 'wp_ajax_nopriv_psp_add_delay_fe', 'psp_add_delay_fe' );
add_action( 'wp_ajax_psp_add_delay_fe', 'psp_add_delay_fe' );
function psp_add_delay_fe() {

    if( !isset( $_POST[ 'project_id' ] ) ) wp_send_json_error( array( 'success' => false ) );

    $metas = apply_filters( 'psp_add_delay_fe_meta', array(
        array(
            'key'   =>  'psp-delay-project',
            'val'   =>  $_POST[ 'project_id' ],
            'type'  =>  'int',
        ),
        array(
            'key'   =>  'psp-delay-days',
            'val'   =>  $_POST[ 'days_delayed' ],
            'type'  =>  'int'
        ),
        array(
            'key'   =>  'psp-delay-date',
            'val'   =>  $_POST[ 'date_occured' ],
            'type'  =>  'string',
        ),
        array(
            'key'   =>  'psp-delay-description',
            'val'   =>  $_POST[ 'description' ],
            'type'  =>  'string',
        )
    ) );

    $args = array(
        'post_title'    =>  get_the_title( $_POST['project_id'] ) . ' - ' . __( 'delayed', 'psp-delays' ) . $_POST['days_delayed'] . __( ' days ', 'psp-delays' ) . __( ' on ', 'psp-delays' ) . ' ' . $_POST[ 'date_occured' ],
        'post_type'     =>  'psp_delays',
        'post_status'   =>  'publish'
    );

    $post_id = wp_insert_post( $args );

    foreach( $metas as $meta ) {

        $val = $meta['val'];

        if( $meta['type'] == 'string' ) $val = sanitize_text_field( $meta['val'] );

        if( $meta['type'] == 'int' ) $val = intval( $meta['val'] );

        update_post_meta( $post_id, '_' . $meta['key'], $val );

    }

    ob_start();

    $end_date = psp_adjust_project_end_date_txt( get_field( 'end_date', $_POST['project_id'] ), $_POST['project_id'] );
    $timing   = psp_calculate_timing( $_POST['project_id'] );

    wp_send_json_success( array( 'success' => true, 'markup' => $end_date, 'post_id' => $post_id, 'timing' => $timing['percentage_complete'] ) );

    exit();

}

function psp_adjust_project_end_date_txt( $date, $post_id ) {

    $args = array(
        'post_type'         =>  'psp_delays',
        'posts_per_page'    =>  -1,
        'meta_key'          =>  '_psp-delay-project',
        'meta_value'        =>  $post_id,
        'fields'            =>  'ID'
    );

    $delays = get_posts( $args );

    // No delays found, return!
    if( empty( $delays ) ) return $date;

    $days = 0;

    foreach( $delays as $delay ) {
        $days += intval( get_post_meta( $delay->ID, '_psp-delay-days', true ) );
    }

    $end_date = date( 'Ymd', strtotime( $date . ' +'. $days . ' weekday' ) );

    return date( get_option('date_format'), strtotime($end_date) );


}
