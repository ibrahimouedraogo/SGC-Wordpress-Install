<div id="psp-comments">

	<?php if ( post_password_required() ) : ?>
			<div class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'psp_projects' ); ?></div>

		</div><!-- #comments -->

		<?php
		/* Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */
		return;
	endif; ?>

	<div class="psp-comments__body">
		<?php
		if ( have_comments() ) :

			global $post;
			$comment_count = psp_get_general_comment_count( $post->ID ); ?>

			<div class="psp-h2">
				<?php esc_html_e( 'Project Discussion', 'psp_projects' ); ?>
				<span>
					<?php printf( _n( 'One Response to %2$s', '<b class="psp-comments__count">%1$s</b> Responses to %2$s', $comment_count, 'psp_projects' ),
			number_format_i18n( $comment_count ), get_the_title() ); ?>
				</span>
			</div>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
				<div class="navigation">
					<div class="nav-previous">
						<?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'psp_projects' ) ); ?>
					</div>

					<div class="nav-next">
						<?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'psp_projects' ) ); ?>
					</div>
				</div> <!-- .navigation -->
			<?php endif; // check for comment navigation ?>

			<div class="psp-commentlist">
				<?php
					/* Loop through and list the comments.
					 */
					$psp_general_comments	= psp_get_general_comments( $post->ID );
					$reverse_comments 		= psp_get_option( 'psp_comment_reverse_order', false );

					$args = array(
						'callback'			=>	'project_status_comment',
						'avatar_size'			=>	'64',
						'reverse_top_level'		=>	( $reverse_comments ? false : true ),
						'max_depth'			=>	3
					);

					wp_list_comments( $args, $psp_general_comments );
				?>
			</div>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
				<div class="navigation">
					<div class="nav-previous">
						<?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'psp_projects' ) ); ?>
					</div>
					<div class="nav-next">
						<?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'psp_projects' ) ); ?>
					</div>
				</div><!-- .navigation -->
			<?php endif; // check for comment navigation ?>

		<?php else : // or, if we don't have comments: ?>

			<div class="psp-h2"><?php esc_html_e( 'Project Discussion', 'psp_projects' ); ?> <span><?php esc_html_e( 'No discussions posted at this time', 'psp_projects' ); ?></span></div>

			<?php
			/* If there are no comments and comments are closed,
			* let's leave a little note, shall we?
			*/
			if ( ! comments_open() ) : ?>
				<div class="psp-p psp-notice nocomments"><?php _e( 'Discussions are closed.', 'psp_projects' ); ?></div>
			<?php endif; // end ! comments_open() ?>

		<?php endif; // end have_comments() ?>

	</div>

	<?php psp_project_comment_form(); ?>

</div><!-- #comments -->
