<?php
function psp_delays_post_type() {

	$labels = array(
		'name'                  => _x( 'Delays', 'Post Type General Name', 'psp-delays' ),
		'singular_name'         => _x( 'Delay', 'Post Type Singular Name', 'psp-delays' ),
		'menu_name'             => __( 'Delays', 'psp-delays' ),
		'name_admin_bar'        => __( 'Delays', 'psp-delays' ),
		'archives'              => __( 'Delay Archives', 'psp-delays' ),
		'parent_item_colon'     => __( 'Delays Item:', 'psp-delays' ),
		'all_items'             => __( 'All Delays', 'psp-delays' ),
		'add_new_item'          => __( 'Add New Delay', 'psp-delays' ),
		'add_new'               => __( 'Add Delay', 'psp-delays' ),
		'new_item'              => __( 'New Delay', 'psp-delays' ),
		'edit_item'             => __( 'Edit Delay', 'psp-delays' ),
		'update_item'           => __( 'Update Delay', 'psp-delays' ),
		'view_item'             => __( 'View Delay', 'psp-delays' ),
		'search_items'          => __( 'Search Delays', 'psp-delays' ),
		'not_found'             => __( 'Not found', 'psp-delays' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'psp-delays' ),
		'featured_image'        => __( 'Featured Image', 'psp-delays' ),
		'set_featured_image'    => __( 'Set featured image', 'psp-delays' ),
		'remove_featured_image' => __( 'Remove featured image', 'psp-delays' ),
		'use_featured_image'    => __( 'Use as featured image', 'psp-delays' ),
		'insert_into_item'      => __( 'Insert into delay', 'psp-delays' ),
		'uploaded_to_this_item' => __( 'Uploaded to this delay', 'psp-delays' ),
		'items_list'            => __( 'Delay list', 'psp-delays' ),
		'items_list_navigation' => __( 'Delay list navigation', 'psp-delays' ),
		'filter_items_list'     => __( 'Filter delay list', 'psp-delays' ),
	);
	$args = array(
		'label'                 => __( 'Delay', 'psp-delays' ),
		'description'           => __( 'Post Type Description', 'psp-delays' ),
		'labels'                => $labels,
		'supports'              => array( 'title' ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-clock',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'psp_delays', $args );

}
add_action( 'init', 'psp_delays_post_type', 0 );

add_action( 'add_meta_boxes', 'psp_delays_add_metabox' );
function psp_delays_add_metabox() {
	add_meta_box( 'psp_delays', __( 'Project Delays', 'psp-delays' ), 'psp_delays_metaboxes', 'psp_delays', 'advanced', 'high' );
}

function psp_delays_metaboxes( $post ) {

	wp_nonce_field( basename( __FILE__ ), 'psp_nonce' );

	$psp_delays_stored_meta	= get_post_meta( $post->ID );

	$projects				= array();
	$description 			= ( isset ( $psp_delays_stored_meta[ '_psp-delay-description' ] ) ? $psp_delays_stored_meta[ '_psp-delay-description' ][ 0 ] : '' );
	$set_project			= ( isset( $psp_delays_stored_meta[ '_psp-delay-project' ] ) ? $psp_delays_stored_meta[ '_psp-delay-project' ][ 0 ] : NULL );
	$args					= array(
		'media_buttons'		=>		false,
		'textarea_name'		=>		'psp-delay-description',
		'teeny'				=>		true,
		'id'				=>		'psp-delay-description',
	); ?>

	<?php $projects = psp_get_all_my_project_ids(); ?>

	<script>
		jQuery( document ).ready(function() {

			jQuery( '#psp-delay-date' ).datepicker();

		});
	</script>

	<p>
		<label for="psp-delay-project" class="psp-row-title"><?php esc_html_e( 'Project', 'psp-delays' ); ?></label>
		<select name="psp-delay-project" id="psp-delay-project" autocomplete="off">
			<?php foreach( $projects as $project_id ): ?>
				<option value="<?php echo esc_attr( $project_id ); ?>" <?php if( $set_project == $project_id ) { echo esc_attr( 'selected' ); } ?> ><?php echo esc_html( get_the_title( $project_id ) ); ?> - <?php the_field( 'client', $project_id ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php if( $set_project ): ?>
			<a href="<?php echo esc_url( get_the_permalink( $set_project ) ); ?>" target="_new"><?php esc_html_e( 'View Project', 'psp_delays' ); ?></a>
		<?php endif; ?>
	</p>

	<?php // TODO: Date picker ?>

	<p>
		<label for="psp-delay-date" class="psp-row-title"><?php esc_html_e( 'Date Occured', 'psp-delays' ); ?></label>
		<input type="text" name="psp-delay-date" id="psp-delay-date" class="psp-datepicker" value="<?php if ( isset ( $psp_delays_stored_meta[ '_psp-delay-date' ] ) ) echo esc_attr( $psp_delays_stored_meta[ '_psp-delay-date' ][ 0 ] ); ?>">
	</p>

	<p>
		<label for="psp-delay-count" class="psp-row-title"><?php esc_html_e( 'Days Delayed', 'psp-delays' ); ?></label>
		<input type="number" name="psp-delay-days" id="psp-delay-days" value="<?php if ( isset ( $psp_delays_stored_meta[ '_psp-delay-days' ] ) ) echo esc_attr( $psp_delays_stored_meta[ '_psp-delay-days' ][ 0 ] ); ?>">
	</p>

	<p>
		<label for="psp-delay-description" class="psp-row-title"><?php esc_html_e( 'Description', 'psp-delays' ); ?></label>
		<?php wp_editor( $description, 'psp-delays-description', $args ); ?>
	</p>

	<?php
}

add_action( 'save_post', 'psp_delays_save_meta_data' );
function psp_delays_save_meta_data( $post_id ) {

	if( 'psp_delays' != get_post_type( $post_id ) ) { return; }

	$ints = array(
		'psp-delay-project',
		'psp-delay-days'
	);

	$strings = array(
		'psp-delay-date',
		'psp-delay-description',
	);

	foreach( $ints as $int ) {
		if( isset( $_POST[ $int ] ) && intval( $_POST[ $int ] ) ) update_post_meta( $post_id, '_' . $int, $_POST[ $int ] );
	}

	foreach( $strings as $string ) {
		if( isset( $_POST[ $string ] ) ) update_post_meta( $post_id, '_' . $string, sanitize_text_field( $_POST[ $string ] ) );
	}

	return;

}

add_action( 'add_meta_boxes', 'psp_delays_edit_post_metabox' );
function psp_delays_edit_post_metabox() {
	add_meta_box( 'psp_delays_meta', __( 'Project Delays', 'psp-delays' ), 'psp_delays_edit_post_metabox_callback', 'psp_projects', 'normal', 'default' );
}

function psp_delays_edit_post_metabox_callback() {

	global $post;

	$post_id = $post->ID;

	echo '<input type="hidden" name="psp_delays_post_meta_nonce" id="psp_delays_post_meta_nonce" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

	$args = array(
		'post_type'			=>	'psp_delays',
		'posts_per_page'	=>	-1,
		'meta_key'          =>  '_psp-delay-project',
		'meta_value'        =>  $post_id
	);

	$delays = new WP_Query( $args ); ?>

	<table class="widefat wp-list-table psp-delays-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Date', 'psp-delays' ); ?></th>
				<th><?php esc_html_e( 'Days Delayed', 'psp-delays' ); ?></th>
				<th><?php esc_html_e( 'Description', 'psp-delays' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$days = 0;
			if( $delays->have_posts() ): while( $delays->have_posts() ): $delays->the_post();

				global $post;

				$data = array(
					'postid'       =>  $post->ID,
					'date'          =>  get_post_meta( $post->ID, '_psp-delay-date', true ),
					'days'          =>  get_post_meta( $post->ID, '_psp-delay-days', true ),
					'description'   =>  get_post_meta( $post->ID, '_psp-delay-description', true )
				);

				$days += intval( $data[ 'days' ] );	?>

				<tr <?php foreach( $data as $key => $val ) echo 'data-' . $key . '="' . esc_attr( $val ) . '" '; ?>>
					<td class="psp-delay-date-td"><small><?php echo esc_html( get_post_meta( $post->ID, '_psp-delay-date', true ) ); ?></small></td>
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
			<?php
			endwhile; wp_reset_query(); wp_reset_postdata();
			/**
			 * If the user can edit delays have a hidden row for JS
			 */
			if( current_user_can( 'edit_psp_project_delays' ) ): ?>
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
					<td class="psp-delays-edit-actions-td"><a class="button button-primary psp-delay-update" href="#"><?php esc_html_e( 'Update', 'psp-delays' ); ?></a> <a href="#" class="psp-delay-cancel"><?php esc_html_e( 'Cancel', 'psp-delays' ); ?></td>
				</tr>
			<?php
			endif;

			else: ?>
			<tr class="no-delays-row">
				<td colspan="4"><?php esc_html_e( 'No delays recorded at this time.', 'psp-delays' ); ?></td>
			</tr>
			<?php endif; ?>
		</tbody>
		<?php if( $delays->have_posts() ): ?>
			<tfoot>
				<tr>
					<td colspan="3"><?php esc_html_e( 'Total Days Delayed', 'psp-delays' ); ?></td>
					<td class="psp-date-table-total-delayed"><?php echo esc_html( $days ); ?></td>
				</tr>
			</tfoot>
		<?php endif; ?>
	</table>

	<div class="psp-hide psp-add-delay-form">
		<h3><?php esc_html_e( 'Add Delay', 'psp-delays' ); ?></h3>

		<div class="psp-modal-form" id="psp-delays-modal-form">

			<div class="success-message psp-hide psp-success">
				<?php echo wpautop( __( 'Delay added to project.', 'psp-delays' ) ); ?>
			</div>
			<div class="error-message psp-hide psp-error">
				<?php echo wpautop( __( 'Error adding delay to project. Please check internet connectivity and try again.', 'psp-delays' ) ); ?>
			</div>

			<input type="hidden" id="psp-project-id" value="<?php echo esc_attr( $post_id ); ?>">

			<table class="form-table">
				<?php
				$meta_fields = apply_filters( 'psp_delays_fe_modal_fields', array(
					array(
						'id'        =>  'psp-date-occured',
						'label'     =>  __( 'Date Occured', 'psp-delays' ),
						'type'      =>  'date',
						'classes'   =>  'psp-date psp-datepicker required',
					),
					array(
						'id'        =>  'psp-days-delayed',
						'label'     =>  __( 'Days Delayed', 'psp-delays' ),
						'type'      =>  'number',
						'classes'	=>	'required'
					),
					array(
						'id'        =>  'psp-delays-description',
						'label'     =>  __( 'Description', 'psp-delays' ),
						'type'      =>  'textarea',
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
				<tbody>
					<?php foreach( $meta_fields as $field ): ?>
						<tr>
							<th><label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
							<td>

								<?php if( $field['type'] == 'textarea' ): ?>
									<textarea name="<?php echo esc_attr( $field['id'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" <?php if( isset( $field['classes'] ) ) { echo 'class="' . $field['classes'] . '"'; } ?> <?php if( isset( $field['required'] ) ) { echo ' required'; } ?>></textarea>
								<?php endif; ?>

								<?php if( in_array( $field['type'],  $standard_fields ) ): ?>
									<input type="<?php echo esc_attr( $field['type'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['id'] ); ?>" value="" <?php if( isset( $field['classes'] ) ) { echo 'class="' . $field['classes'] . '"'; } if( isset( $field['required'] ) ) { echo ' required'; } ?>>
								<?php endif; ?>

								<?php do_action( 'psp_delays_modal_meta_fields', $field, get_the_ID() ); ?>

							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<script>
				jQuery( document ).ready(function() {
					jQuery( '.psp-datepicker' ).datepicker({
						'dateFormat' : 'mm/dd/yy'
					});
				});
			</script>

		</div>

		<div class="psp-modal-actions">
			<p class="pano-modal-add-btn"><input type="submit" class="psp-delays-submit button button-primary" value="<?php echo esc_attr_e( 'Save', 'psp-delays' ); ?>"> <a href="#" class="modal-close modal_close hidemodal"><?php esc_html_e( 'Cancel', 'psp-delays' ); ?></a></p>
		</div>

	</div>

	<p><a class="button button-primary psp-add-delay" href="#"><?php esc_html_e( 'Add Delay', 'psp-delays' ); ?></a></p>

	<?php

}
