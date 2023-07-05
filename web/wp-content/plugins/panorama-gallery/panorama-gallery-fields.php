<?php

if(function_exists("register_field_group"))
{
register_field_group(array (
    'id' => 'acf_project-gallery',
    'title' => __('Project Gallery','psp-projects'),
    'fields' => array (
        array (
            'key' => 'field_5424a66e77cd4',
            'label' => __('Title','psp-projects'),
            'name' => 'gallery_title',
            'type' => 'text',
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'formatting' => 'html',
            'maxlength' => '',
        ),
        array (
            'key' => 'field_5424a67777cd5',
            'label' => __('Description','psp-projects'),
            'name' => 'gallery_description',
            'type' => 'wysiwyg',
            'default_value' => '',
            'toolbar' => 'full',
            'media_upload' => 'no',
        ),
        array (
            'key' 			=> 'field_5424a67777cda',
            'label' 		=> __('Style','psp-projects'),
            'name' 			=> 'gallery_style',
			'instructions'	=> __( 'What type of gallery would you like?', 'psp-projects' ),
            'type' 			=> 'select',
			'choices'		=>	array(
				'grid'		=>	__( 'Grid', 'psp-projects' ),
				'masonry'	=>	__( 'Masonry', 'psp-projects' ),
				'slideshow'	=>	__( 'Slideshow', 'psp-projects' ),
				'band'		=>	__( 'Banded', 'psp-projects' )
			),
            'default_value' => 'grid',
            'allow_null' 	=> 0,
            'multiple' 		=> 0,
        ),
        /*
		array (
            'key' => 'field_543d18b50d845',
            'label' => 'Location',
            'name' => 'gallery_location',
            'type' => 'select',
            'instructions' => __('Where do you want the gallery to show up?','psp-projects'),
            'choices' => array (
                'overview' => __('After Overview','psp-projects'),
                'progress' => __('After Progress','psp-projects'),
                'phases' => __('After Phases','psp-projects'),
            ),
            'default_value' => 'overview',
            'allow_null' => 0,
            'multiple' => 0,
        ),
		*/
        array (
            'key' => 'field_5424a358051b1',
            'label' => 'Photos',
            'name' => 'gallery',
            'type' => 'gallery',
            'instructions' => __('Upload photos related to this project','psp-projects'),
            'preview_size' => 'thumbnail',
            'library' => 'all',
        ),
    ),
    'location' => array (
        array (
            array (
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'psp_projects',
                'order_no' => 0,
                'group_no' => 0,
            ),
        ),
    ),
    'options' => array (
        'position' => 'normal',
        'layout' => 'default',
        'hide_on_screen' => array (
        ),
    ),
    'menu_order' => 0,
    ));
}

add_filter( 'psp_phase_fields', 'psp_gallery_add_upload_fields' );
function psp_gallery_add_upload_fields( $phases ) {

    $new_fields = array();

    foreach( $phases['fields'] as $sub_field ) {

        if( $sub_field['name'] == 'phases' ) {

            $new_phase_fields = array();

            foreach( $sub_field['sub_fields'] as $sub_phase_field ) {

                if( $sub_phase_field['name'] == 'description' ) {
                    $sub_phase_field['media_upload'] = 'yes';
                }

                $new_phase_fields[] = $sub_phase_field;

            }

            $sub_field['sub_fields'] = $new_phase_fields;

        }

        $new_fields[] = $sub_field;

    }

    $phases['fields'] = $new_fields;

    return $phases;

}
