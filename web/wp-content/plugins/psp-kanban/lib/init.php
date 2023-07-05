<?php
$deps = array(
    'controllers/init',
    'controllers/settings',
    'models/init',
    'views/views'
);
if( function_exists( 'psp_get_option' ) ) {
     foreach( $deps as $dep ) include_once( $dep . '.php' );
}
