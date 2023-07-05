<?php
$approved_class = ( empty($task_docs) ? 'psp-hide' : '' );
$empty_class    = ( empty($task_docs) ? '' : 'psp-hide' );

do_action( 'psp_before_task_docs_wrapper', $post_id, $task_id ); ?>

<div class="psp-task-documents">

    <?php do_action( 'psp_before_task_doc_list', $post_id, $task_id ); ?>

    <div class="psp-task-documents-wrapper">
        <?php do_action( 'psp_inside_task_doc_wrapper' ); ?>

            <div class="psp-task-docs-list psp-documents-row">
                <?php
                if( !empty( $task_docs ) ):
                    do_action( 'psp_start_of_doc_list', $post_id, $task_id );
					// Using same template as Phase Tasks
                    foreach( $task_docs as $doc ) include( psp_template_hierarchy( 'projects/phases/documents/single.php') );
                    do_action( 'psp_end_of_doc_list', $post_id, $task_id );
                endif; ?>
           </div>
        <?php if( empty($task_docs) ): ?>
            <div class="psp-notice task-docs-empty-message"><em><?php esc_html_e( 'No documents attached to this task.', 'psp_projects' ); ?></em></div>
        <?php endif;

        do_action( 'psp_inside_task_doc_wrapper_after', $post_id, $task_id ); ?>
    </div>

    <?php do_action( 'psp_after_task_doc_list', $post_id, $task_id ); ?>

</div>
