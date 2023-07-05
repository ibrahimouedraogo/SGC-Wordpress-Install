<?php do_action( 'psp_dashboard_before_my_projects' ); ?>

<div class="psp-archive-section">

	<?php do_action( 'psp_dashboard_my_projects_before_heading' ); ?>

	<div class="psp-table-header psp-multi-row">
		<div class="psp-table-header__heading">
			<div class="psp-h2"><?php esc_html_e( 'Projects', 'psp_projects' ); ?></div>
			<?php include( psp_template_hierarchy( 'global/search-form.php' ) ); ?>
		</div>
		<div class="psp-table-header__sub">
			<div class="psp-sub-nav">
				<?php
				do_action( 'psp_dashboard_my_projects_before_nav' );

				$statuses    = psp_get_project_post_statuses();
				$page_status = get_query_var('psp_status_page');
				if( !$page_status ) {
					$page_status = 'active';
				}

				foreach( $statuses as $status ): ?>
					<div class="psp-sub-nav-item">
						<?php
						$link = trailingslashit(get_post_type_archive_link('psp_projects'));
						$class = ( $page_status == $status['slug'] ? 'active' : '' );
						if( get_option('permalink_structure') ):
							 $link .= 'status/' . $status['slug'];
						else:
							 $link = add_query_arg( 'psp_status_page', $status['slug'], $link );
						endif; ?>
						<a href="<?php echo esc_url($link); ?>" class="<?php echo esc_attr($class); ?>"><?php echo esc_html( $status['label']); ?></a>
					</div>
				<?php endforeach; ?>

				<?php do_action( 'psp_dashboard_my_projects_after_nav' ); ?>

			</div>
		</div>
	</div>

	<?php do_action( 'psp_dashboardmy_projects_after_heading' ); ?>

	<div class="psp-archive-list-wrapper">
		<?php echo psp_archive_project_listing( $projects ); ?>
	</div>

</div>

<?php do_action( 'psp_dashboard_after_my_projects' ); ?>
