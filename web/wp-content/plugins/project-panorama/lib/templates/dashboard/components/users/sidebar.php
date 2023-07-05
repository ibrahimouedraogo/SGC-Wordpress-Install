<div class="psp-archive-aside">

    <div class="psp-user-thumbnail">
        <?php echo get_avatar( $user_id ); ?>
    </div>

    <div class="psp-user-meta">
        <div class="psp-user__name">
            <?php echo esc_html( psp_get_nice_username_by_id($user_id) ); ?>
        </div>
        <span class="psp-last-login"><?php echo psp_verbose_login($user_id); ?></span>
    </div>

    <div class="psp-archive-widget psp-user-project-stats">

        <div class="psp-h4"><?php esc_html_e( 'Projects', 'psp_projects' ); ?></div>

        <?php
        $project_stats = psp_get_users_project_breakdown($user_id); ?>

        <div class="psp-user-stats">
            <div class="psp-stat">
                <div class="psp-stat-stat">
                    <?php echo $project_stats['active']; ?>
                </div>
                <div class="psp-stat-label">
                    <?php esc_html_e( 'Active', 'psp_projects' ); ?>
                </div>
            </div>
            <div class="psp-stat">
                <div class="psp-stat-stat">
                    <?php echo $project_stats['completed']; ?>
                </div>
                <div class="psp-stat-label">
                    <?php esc_html_e( 'Complete', 'psp_projects' ); ?>
                </div>
            </div>
            <div class="psp-stat">
                <div class="psp-stat-stat">
                    <?php echo $project_stats['not_started']; ?>
                </div>
                <div class="psp-stat-label">
                    <?php esc_html_e( 'Not Started', 'psp_projects' ); ?>
                </div>
            </div>
        </div>

    </div>

    <div class="psp-archive-widget psp-user-task-stats">

        <div class="psp-h4"><?php esc_html_e( 'Tasks', 'psp_projects' ); ?></div>

        <?php
        $task_stats = psp_get_user_task_stats( $user_id ); ?>

        <div class="psp-user-stats cols-4">
            <div class="psp-stat">
                <div class="psp-stat-stat">
                    <?php echo $task_stats['total']; ?>
                </div>
                <div class="psp-stat-label">
                    <?php esc_html_e( 'Assigned', 'psp_projects' ); ?>
                </div>
            </div>
            <div class="psp-stat">
                <div class="psp-stat-stat">
                    <?php echo $task_stats['in_progress']; ?>
                </div>
                <div class="psp-stat-label">
                    <?php esc_html_e( 'In Progress', 'psp_projects' ); ?>
                </div>
            </div>
            <div class="psp-stat">
                <div class="psp-stat-stat">
                    <?php echo $task_stats['completed']; ?>
                </div>
                <div class="psp-stat-label">
                    <?php esc_html_e( 'Complete', 'psp_projects' ); ?>
                </div>
            </div>
            <div class="psp-stat">
                <div class="psp-stat-stat">
                    <?php echo $task_stats['overdue']; ?>
                </div>
                <div class="psp-stat-label">
                    <?php esc_html_e( 'Overdue', 'psp_projects' ); ?>
                </div>
            </div>
        </div>

    </div>


    <?php include( psp_template_hierarchy( 'dashboard/components/teams/widget.php' ) ); ?>

</div>
