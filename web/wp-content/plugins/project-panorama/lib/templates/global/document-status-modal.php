<?php
$post_id = ( isset($post_id) ? $post_id : get_the_ID() ); ?>

<div class="document-update-dialog psp-hide psp-modal" id="psp-document-status-modal">
    <div class="psp-modal-content">
        <form method="post" action="<?php echo esc_url( get_permalink($post_id) ); ?>" class="document-update-form">

            <?php if( is_user_logged_in() && get_post_type() == 'psp_projects' ) {
                apply_filters( 'psp_document_update_form_fields', psp_the_document_form_fields( $post_id, get_current_user_id() ) );
            } ?>

            <div class="psp-document-form">

                <div class="psp-modal-header">
                      <div class="psp-h2"><?php esc_html_e( 'Update Status', 'psp_projects' ); ?></div>
                </div>

                <div class="psp-hide psp-message-form">
                    <div class="psp-p"><strong><?php esc_html_e( 'Document Status Updated', 'psp_projects' ); ?></strong></div>
                    <div class="psp-hide psp-confirm-note psp-p"><?php esc_html_e( 'Notifications have been sent.', 'psp_projects' ); ?></div>
                </div>

                <div class="psp-form-fields">
                     <div class="psp-form-field">
                         <label for="psp-doc-status-field"><?php esc_html_e('Status','psp_projects'); ?></label>
                         <div class="psp-select-wrapper">
                             <select name="doc-status" class="psp-doc-status-field">
                                 <?php
                                 $options = apply_filters( 'psp_document_options', array(
                                     '---'						=>	'---',
                                     'Approved'					=>	__( 'Approved', 'psp_projects' ),
                                     'In Review'					=>	__( 'In Review', 'psp_projects' ),
                                     'Revisions'					=>	__( 'Revisions', 'psp_projects' ),
                                     'Rejected'					=>	__( 'Rejected', 'psp_projects' )
                                 ) );

                                 foreach( $options as $value => $title ): ?>
                                     <option value="<?php esc_attr_e( $value ); ?>"><?php esc_html_e( $title ); ?></option>
                                 <?php endforeach; ?>
                             </select>
                         </div>
                   </div>

                     <?php if( psp_get_project_users() ): ?>

                         <div class="psp-form-field">
                              <label for="psp-doc-notify"><?php esc_html_e( 'Notify', 'psp_projects' ); ?></label>
                              <div class="psp-p all-line">
                                   <label for="psp-du-doc-all">
                                        <input type="checkbox" class="all-checkbox" name="psp-notify-all" id="psp-du-doc-all" value="all"> <?php esc_html_e( 'All Users', 'psp_projects' ); ?>
                                   </label>
                                   <label for="psp-du-doc-specific">
                                        <input type="checkbox" class="specific-checkbox" name="psp-notify-specific" value="specific" id="psp-du-doc-specific"> <?php esc_html_e( 'Specific Users', 'psp_projects' ); ?>
                                   </label>
                              </div>

                              <div class="psp-notify-list">
                                   <?php
                                   $users = psp_get_project_users();
                                   psp_comment_notify_fields( $users, 'global-' . $post_id ); ?>
                              </div>

                              <?php
                              /*
                              <div class="psp-notify-list">
                                  <?php
                                  $users      = psp_get_project_users();
                                  $included   = array();

                                  foreach( $users as $user ):

                                      if( in_array( $user, $included ) ) continue;

                                      $included[] = $user;
                                      $username   = psp_get_nice_username( $user ); ?>

                                      <div class="psp-notify-user">
                                          <label for="<?php esc_attr_e( 'psp-du-doc-' . $user['ID'] ); ?>">
                                              <input id="<?php esc_attr_e( 'psp-du-doc-' . $user['ID'] ); ?>" type="checkbox" name="psp-user[]" value="<?php esc_attr_e($user['ID']); ?>" class="psp-notify-user-box"><?php esc_html_e($username); ?>
                                          </label>
                                     </div>

                                  <?php endforeach; ?>
                             </div>
                             */ ?>
                        </div>

                         <div class="psp-form-field">
                              <label for="psp-doc-message"><?php esc_html_e( 'Message', 'psp_projects' ); ?></label>
                              <textarea id="psp-doc-message" name="psp-doc-message"></textarea>
                         </div>

                     <?php endif; ?>
                </div>
            </div> <!--/.psp-document-form-->

            <div class="psp-modal-actions">
                <input type="submit" name="update" value="<?php esc_attr_e( 'Update', 'psp_projects' ); ?>"> <a href="#" class="modal-close js-psp-doc-status-reset"><?php esc_html_e( 'Cancel', 'psp_projects' ); ?></a>
            </div> <!--/.pano-modal-actions-->

        </form>
    </div> <!--/.psp-modal-content-->
</div>
