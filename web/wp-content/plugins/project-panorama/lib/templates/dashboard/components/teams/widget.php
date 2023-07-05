<?php
if( !isset($user_id) || !$user_id ) {
    $cuser = wp_get_current_user();
    $user_id = $cuser->ID;
}

$teams = psp_get_user_teams( $user_id );
if( !empty( $teams ) ): ?>

    <?php do_action( 'psp_before_teams_dashboard_widget' ); ?>

        <div class="psp-archive-widget psp-teams-archive-widget">

            <div class="psp-h4"><?php esc_html_e( 'Teams', 'psp_projects' ); ?></div>

            <div class="psp-team-list">
                <?php foreach( $teams as $team ):
                    $members = get_field( 'team_members', $team->ID ); ?>
                    <div class="psp-team-list__item">
                        <?php psp_team_user_icons( $team->ID ); ?>
                        <a href="<?php echo esc_url( get_the_permalink( $team->ID ) ); ?>">
                           <strong><?php echo esc_html( get_the_title( $team->ID ) ); ?></strong>
                           <span class="psp-tmi-count"><?php echo count( $members ) . ' ' . __( 'Members', 'psp_projects' ); ?></span>
                        </a>
                   </div>
                <?php endforeach; ?>
           </div>

        </div>

    <?php do_action( 'psp_after_teams_dashboard_widget' ); ?>

<?php endif; ?>
