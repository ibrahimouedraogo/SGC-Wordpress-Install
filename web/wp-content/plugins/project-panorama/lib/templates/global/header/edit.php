<?php
if( is_single() && get_post_type() == 'psp_projects' && psp_can_edit_project() ):
    $link = apply_filters( 'psp_project_edit_post_link', psp_get_edit_post_link() ); ?>
    <aside class="psp-masthead-edit">
        <a href="<?php echo esc_url($link); ?>"><span class="fa fa-pencil"></span> <?php esc_html_e( 'Edit Project', 'psp_projects' ); ?></a>
    </aside>
<?php endif;
