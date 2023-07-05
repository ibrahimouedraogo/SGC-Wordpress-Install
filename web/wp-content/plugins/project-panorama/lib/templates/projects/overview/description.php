<?php  $post_id = ( isset($post_id) ? $post_id : get_the_ID() );  ?>
<div id="psp-description" class="psp-overview__description">
	<div class="psp-overview__summary">

		<div class="psp-h4 psp-box-title"><?php esc_html_e( 'Project Description', 'psp_projects' ); ?></div>

		<?php do_action( 'before_project_description', $post_id ); ?>

		<div class="psp-description-content">
			<?php echo do_shortcode( get_field( 'project_description', $post_id ) ); ?>
		</div>

		<?php do_action( 'after_project_description', $post_id ); ?>

	</div>
</div> <!--/#psp-description-->
