<div class="task-comments">

	<div class="task-comments-wrapper" data-task-key="<?php echo esc_attr($task_comment_key); ?>">

		<?php if ( ! empty( $task_comment_key ) ) : ?>

			<?php
			$task_comments = psp_get_task_comments( $task_comment_key, $post_id ); ?>

			<div class="psp-commentlist">
				<?php wp_list_comments( array( 'callback' => 'project_status_comment', 'max_depth'	=>	2, 'post_id' => $post_id ), $task_comments ); ?>
			</div>

			<?php psp_task_comment_form( $task_comment_key, array(), $post_id ); ?>

		<?php endif; ?>

	</div>

</div><!--/.task-comments-->
