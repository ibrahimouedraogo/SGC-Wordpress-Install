<?php
/**
 * Project overview index
 *
 * Part template for the project overview, includes all the other sub templates
 *
 * @post_type  psp_project
 * @hierarchy  single
 */

$post_id 	= ( isset( $post_id ) ? $post_id : get_the_ID() );
$priorities = psp_get_priorities_list();
$priority 	= ( get_field('_psp_priority') ? get_field('_psp_priority') : 'normal' );
$priority 	= $priorities[$priority];

/**
 * @end vars
 */ ?>

<?php if( get_field( 'client_project_logo' ) ): $project_logo = get_field( 'client_project_logo' );  ?>
	<div id="psp-project-header">
		<?php echo '<img src="' . $project_logo['url'] . '" alt="' . get_field( 'client' ) . '" class="psp-client-project-logo">'; ?>
	</div>
<?php endif; ?>

<div id="psp-essentials" class="<?php echo esc_attr($style); ?> cf">

	<hgroup class="psp-section-heading">

		<?php do_action( 'psp_before_project_title', $post_id ); ?>

		<div class="psp-h1 psp-section-title">
			<?php the_title(); ?>
			<?php if( get_field('client') ): ?>
				<div class="psp-section-title__meta">
					<span class="psp-client"><?php the_field('client'); ?></span>
				</div>
			<?php endif; ?>
		</div> <!--/.psp-section-title-->

		<?php do_action( 'psp_after_project_title', $post_id ); ?>

		<div class="psp-section-data">

			<?php
			// Hook to add content
			do_action( 'psp_before_project_title_section_data' ); ?>
			<?php if( is_user_logged_in() ): ?>
			<?php echo esc_html(psp_compute_progress( get_the_id() )); ?>% <?php esc_html_e( 'Complete', 'psp_projects' ); ?>
			<?php endif; ?>
			<?php if( current_user_can('see_priority_psp_projects') ): ?>
				<span class="psp-priority psp-priority-<?php echo esc_attr($priority['slug']); ?>" data-placement="left" data-toggle="psp-tooltip" title="<?php echo esc_attr($priority['label']) . ' ' . esc_html('Priority', 'psp_projects'); ?>" style="background-color: <?php echo $priority['color']; ?>"></span>
			<?php endif; ?>

			<?php
			// Hook to add content
			do_action( 'psp_after_project_title_section_data' ); ?>

		</div>

		<?php do_action( 'psp_after_project_data', $post_id ); ?>

	</hgroup>

	<?php do_action( 'psp_before_description', $post_id ); ?>
<?php if( is_user_logged_in() ): ?>
	<div id="psp-quick-overview" class="psp-box psp-quick-overview">

		<?php do_action( 'psp_before_quick_overview_title' ); ?>

		<div class="psp-h4 psp-sub-title"><?php _e('Project Progress','psp_projects'); ?></div>

		<?php do_action( 'psp_after_quick_overview_title' ); ?>

		<div class="psp-quick-overview__broad">
			<?php
			do_action( 'psp_before_short_progress', $post_id );
				include( psp_template_hierarchy( '/projects/overview/short-progress' ) );
			do_action( 'psp_between_short_progress_and_overview_timing', $post_id );
				include( psp_template_hierarchy( '/projects/overview/timing' ) );
			do_action( 'psp_after_overview_timing', $post_id ); ?>
		</div>
		<div class="psp-quick-overview__detailed">
			<?php
		 	include( psp_template_hierarchy( '/projects/overview/summary' ) );
			do_action( 'psp_after_project_summary', $post_id ); ?>
		</div>
	</div>
<?php endif; ?>
	<div id="psp-description-documents" class="psp-description-documents">
		<div class="psp-box psp-no-padding psp-flex">
			<?php include( psp_template_hierarchy( '/projects/overview/description.php' ) ); ?>
			<?php include( psp_template_hierarchy( '/projects/overview/documents/index' ) ); ?>
		</div> <!--/.psp-overview-box-->
		<?php do_action( 'psp_before_quick_overview', $post_id ); ?>
	</div>

	<?php do_action( 'psp_after_quick_overview', $post_id ); ?>

</div> <!--/#psp-essentials-->
