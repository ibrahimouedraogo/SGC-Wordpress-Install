<div class="phase-comments">

	<div class="psp-h4 psp-section-toggle">
		<a href="#" class="comments-list-toggle">
			<?php _e( 'Discussion', 'psp_projects' ); ?>
			<span><i class="fa fa-comment"></i> <?php echo esc_html($comment_count); ?></span>
		</a>
	</div>

	<div class="phase-comments-wrapper" data-phase-key="<?php echo $phase_comment_key; ?>">


		<?php if ( ! empty( $phase_comment_key ) ) : ?>

			<?php
			$phase_comments   = psp_get_phase_comments( $phase_comment_key, $post_id );
			$reverse_comments = psp_get_option( 'psp_comment_reverse_order', false );

			$args = array(
				'callback'		=> 'project_status_comment',
				'max_depth'		=> 2,
				'reverse_top_level' => $reverse_comments
			); ?>

			<div class="psp-commentlist">
				<?php wp_list_comments( $args, $phase_comments ); ?>
			</div>

			<?php psp_phase_comment_form( $phase_comment_key, array(), $post_id ); ?>

		<?php endif; ?>

	</div>

</div><!--/.phase-comments-->
