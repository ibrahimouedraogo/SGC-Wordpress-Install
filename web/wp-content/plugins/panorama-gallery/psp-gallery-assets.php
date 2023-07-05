<?php
/**
 * Add custom scripts to the back and front end
 *
 * @return null
 *
 **/

function psp_gallery_assets( $style = 'grid' ) {

    psp_register_style( 'psp-gallery', plugin_dir_url(__FILE__) . 'assets/css/psp-gallery.css', null, PANORAMA_GALLERY_VER );
    psp_register_style( 'psp-gallery-lightbox', PANO_GALLERY_URL . 'assets/css/lightbox.min.css', null, PANORAMA_GALLERY_VER );

    psp_register_script( 'psp-gallery-edit', PANO_GALLERY_URL . 'assets/js/psp-gallery-edit.js', null, PANORAMA_GALLERY_VER );
    psp_register_script( 'psp-gallery-lightbox', PANO_GALLERY_URL . 'assets/js/lightbox.min.js', null, PANORAMA_GALLERY_VER );

	if( $style == 'masonry' ) {
          psp_register_script( 'psp-gallery-masonry', plugin_dir_url( __FILE__ ) . 'assets/js/psp-gallery-masonry.js', null, PANORAMA_GALLERY_VER );
     }

	if( $style == 'slideshow' ) {

        psp_register_script( 'psp-gallery-slideshow', plugin_dir_url( __FILE__ ) . 'assets/js/psp-gallery-slideshow.js', null, PANORAMA_GALLERY_VER );
        psp_register_style( 'psp-gallery-slideshow', plugin_dir_url(__FILE__) . 'assets/css/psp-gallery-slideshow.css', null, PANORAMA_GALLERY_VER );

	}

	if( $style == 'banded' ) {

        psp_register_script( 'psp-gallery-banded', plugin_dir_url( __FILE__ ) . 'assets/js/psp-gallery-banded.js', null, PANORAMA_GALLERY_VER );
        psp_register_style( 'psp-gallery-banded', plugin_dir_url(__FILE__) . 'assets/css/psp-gallery-banded.css', null, PANORAMA_GALLERY_VER );

	}

}


add_action( 'admin_enqueue_scripts', 'psp_gallery_admin_assets' );
function psp_gallery_admin_assets() {

    wp_register_style('psp-gallery-admin',plugin_dir_url(__FILE__).'assets/css/psp-gallery-admin.css');
    wp_enqueue_style('psp-gallery-admin');

}
