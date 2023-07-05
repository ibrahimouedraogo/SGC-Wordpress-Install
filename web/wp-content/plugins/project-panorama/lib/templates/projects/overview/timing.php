<div id="psp-time-overview">

     <?php do_action( 'psp_timing_wrapper_start' ); ?>

    <?php
    $post_id        = ( isset( $post_id ) ? $post_id : get_the_ID() );
    $start_date     = psp_get_the_start_date( NULL, $post_id );
    $end_date       = psp_get_the_end_date( NULL, $post_id ); ?>

    <div class="psp-overview__dates">
        <?php if( $start_date || $end_date ): ?>
            <?php if( $start_date ): ?>
                <div class="psp-archive-list-dates psp-the-start-date">
                    <?php do_action( 'psp_timing_before_start_date', $post_id, $start_date ); ?>
                    <div class="psp-h5"><?php esc_html_e( 'Start Date', 'psp_projects' ); ?></div>
                    <div class="psp-p"><?php echo esc_html($start_date); ?></div>
                    <?php do_action( 'psp_timing_after_start_date', $post_id, $start_date ); ?>
                </div>
            <?php endif;
            if( $end_date ): ?>
                <div class="psp-archive-list-dates psp-the-end-date">
                    <?php do_action( 'psp_timing_before_end_date', $post_id, $end_date ); ?>
                    <div class="psp-h5"><?php esc_html_e( 'End Date', 'psp_projects' ); ?></div>
                    <div class="psp-p"><?php echo esc_html($end_date); ?></div>
                    <?php do_action( 'psp_timing_after_end_date', $post_id, $end_date ); ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php do_action( 'psp_timing_no_dates', $post_id ); ?>
        <?php endif; ?>
    </div>

    <?php
    do_action( 'psp_timing_before_header', $post_id );

    do_action( 'psp_timing_after_header', $post_id );

    do_action( 'psp_timing_after_dates', $post_id, $start_date, $end_date ); ?>

</div>
