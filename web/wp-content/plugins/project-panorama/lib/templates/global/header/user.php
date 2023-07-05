<?php if( is_user_logged_in() ): ?>
	<aside class="psp-masthead-user">
		<?php
		$cuser = wp_get_current_user(); ?>
		<span class="psp-masthead-user__message">
			<a href="<?php echo wp_logout_url( get_post_type_archive_link('psp_projects') ); ?>" class="psp-masthead-user__logout"><?php echo esc_html_e( 'Log Out', 'psp_projects' ); ?></a> <?php esc_html_e('Hello','psp_projects'); ?> <?php esc_html_e($cuser->display_name); ?>!
		</span>
		<?php echo get_avatar($cuser->ID); ?>
	</aside>
	<?php
endif; ?>
