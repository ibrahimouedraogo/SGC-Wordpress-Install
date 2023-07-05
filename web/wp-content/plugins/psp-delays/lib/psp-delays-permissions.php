<?php
add_action( 'init', 'psp_delays_permissions' );
function psp_delays_permissions() {

    $roles = array(
        'administrator',
        'editor',
        'psp_project_owner',
        'psp_project_creator',
        'psp_project_manager'
    );

    $caps = array(
        'edit_psp_project_delays',
        'delete_psp_project_delays',
        'publish_psp_project_delays'
    );

    foreach( $roles as $role_slug ) {

        $role = get_role( $role_slug );

        if( $role )
            foreach( $caps as $cap ) $role->add_cap( $cap );

    }

}
