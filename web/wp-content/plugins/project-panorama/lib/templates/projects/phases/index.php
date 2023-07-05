<!-- Hidden admin URL so we can do Ajax -->
<?php
$post_id 	 	 = ( isset($post_id) ? $post_id : get_the_ID() );
$phases		 = get_field( 'phases', $post_id );
$phase_index 	 = 0;
$i		      = 0;
$c			 = 0;
$colors 	 	 = psp_get_phase_color();
$phase_summary  = psp_get_phase_summary( get_field('phases', $post_id ) );
$wrapper_class	 = ( $style == 'psp-shortcode' ? 'psp-shortcode-phases' : 'psp-no-shortcode-phases' ); ?>

<input id="psp-ajax-url" type="hidden" value="<?php echo admin_url(); ?>admin-ajax.php">

<?php if( $phases || current_user_can('edit_psp_projects') ): ?>

	<hgroup class="psp-section-heading psp-row">

		<?php do_action( 'psp_before_phases_title', $phases, $post_id ); ?>

		<div class="psp-h2 psp-section-title">

			<?php do_action( 'psp_before_phases_section_title' ); ?>

			<?php esc_html_e( 'Phases', 'psp_projects' ); ?>

			<?php do_action( 'psp_after_phases_section_title' ); ?>

		</div>

		<?php do_action( 'psp_after_phases_title', $phases, $post_id ); ?>

		<div class="psp-section-data">

			<?php do_action( 'psp_before_project_phase_section_data', $phases, $post_id ); ?>

			<?php if(comments_open( $post_id )): ?>
				<a href="#" class="psp-expand-comments" data-toggle="<?php esc_attr_e( 'Collapse Discussions', 'psp_projects' ); ?>"><i class="fa fa-plus"></i> <span><?php esc_html_e( 'Expand Discussions', 'psp_projects' ); ?></span></a> <span class="psp-pipe">|</span>
			<?php endif; ?>

			<span class="psp-phases-completed" data-value="<?php echo esc_attr($phase_summary['completed']); ?>"><?php echo esc_html($phase_summary['completed']); ?></span>  / <span class="psp-phases-total" data-value="<?php echo esc_attr($phase_summary['total']); ?>"><?php echo esc_html($phase_summary['total']) . ' ' . __( 'Completed', 'psp_projects' ); ?></span>

			<?php do_action( 'psp_after_project_phase_section_data', $phases, $post_id ); ?>

		</div>

		<?php do_action( 'psp_after_phases_data', $phases, $post_id ); ?>

	</hgroup>

<?php endif; ?>

<?php
do_action( 'psp_before_all_phases', $post_id, $phases ); ?>

<script>
	var chartOptions = {
		responsive: true,
		percentageInnerCutout : <?php echo esc_js( apply_filters( 'psp_graph_percent_inner_cutout', 92 ) ); ?>,
		maintainAspectRatio: true,
		// animation: false
	}
   var allCharts = [];
</script>

<div class="<?php echo esc_attr( apply_filters( 'psp_phase_wrapper_class', $wrapper_class . ' psp-phase-wrap psp-total-phases-' . psp_get_phase_count() ) ); ?>">

	<?php
	while ( have_rows( 'phases', $post_id ) ) : $phase = the_row();

		if( get_sub_field('private_phase') && !psp_can_edit_project() ) {
		    $phase_index++;
		    continue;
		}

		include( psp_template_hierarchy( 'projects/phases/single.php') );

	endwhile;

	do_action( 'psp_after_all_phases', $post_id, $phases );
	?>
</div>
