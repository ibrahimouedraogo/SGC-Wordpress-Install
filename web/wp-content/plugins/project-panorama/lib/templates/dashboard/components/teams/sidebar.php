<aside id="team-single-sidebar" class="psp-archive-aside">

    <aside class="thumbnail">

        <?php
        if( has_post_thumbnail() ):
            the_post_thumbnail( 'thumbnail' );
        else: ?>
            <img src="<?php echo PROJECT_PANARAMA_URI; ?>/dist/assets/images/default-team.png" alt="<?php the_title(); ?>">
        <?php endif; ?>

    </aside>

    <?php if( get_field( 'description' ) ): ?>

        <div class="psp-archive-widget psp-widget">

            <div class="psp-h4"><?php the_title(); ?></div>

            <?php the_field( 'description' ); ?>

        </div>

    <?php endif; ?>

    <?php $members = psp_get_team_members( $post->ID ); ?>

    <div class="psp-archive-widget psp-widget">

        <div class="psp-h4"><?php esc_html_e( 'Members', 'psp_projects' ); ?></div>

        <?php if( $members ): ?>
            <div class="psp-team-member-list">
                <?php
                foreach( $members as $user ):
                    if( !$user || empty($user) ) {
                        continue;
                    } ?>
                    <div class="psp-team-member-list__item">
                        <?php if( current_user_can('delete_others_psp_projects') ): ?>
                            <a href="<?php echo esc_url( psp_get_user_permalink($user['ID']) ); ?>">
                        <?php endif; ?>
                                <?php echo $user[ 'user_avatar' ]; ?>
                                <strong><?php echo psp_get_nice_username_by_id( $user[ 'ID' ] ); ?></strong>
                        <?php if( current_user_can('delete_others_psp_projects') ): ?>
                            </a>
                        <?php endif; ?>
                        <span class="psp-last-login"><?php echo psp_verbose_login( $user[ 'ID' ] ); ?></span>
                   </div>
                <?php
                endforeach; ?>
           </div>
        <?php else: ?>
            <div class="psp-p"><?php esc_html_e( 'No users assigned to this team.', 'psp_projects' ); ?></div>
        <?php endif; ?>

    </div>

</aside>
