<?php if( psp_can_edit_task( $post_id, $task_phase['index'], $task['ID'] ) ) { ?>

		<span class="psp-task-edit-links">

			<span class="psp-task-edit-links__actions">

				<a href="#edit-task-<?php echo $task['ID']; ?>" class="task-edit-link"><b class="fa fa-adjust"></b> <?php _e('update','psp_projects'); ?></a>

				<?php $task_atts = apply_filters( 'psp_task_attributes', array(
					'task'			=>	$task['ID'],
					'phase'			=>	$task_phase['index'],
                         'phase_id'          =>   $task_phase['ID'],
					'project'			=>	$post_id,
				), $post_id, $task_phase['index'], $task['ID'] ); ?>

				<a href="#" class="complete-task-link js-kb-complete-task-link"
					<?php foreach( $task_atts as $att => $val ): ?>
						data-<?php echo $att; ?>="<?php echo esc_attr( $val ); ?>"
					<?php endforeach; ?>
					>
					<b class="fa fa-check"></b>
					<?php esc_html_e( 'complete', 'psp_projects' ); ?>
				</a>

			</span>

			<span id="edit-task-<?php echo $task['ID']; ?>" class="js-kb-task-select task-select">

				<span class="psp-select-wrapper">
					<select id="edit-task-select-<?php echo $task_phase['index'] . '-' . $task['ID']; ?>" class="edit-task-select">
						<option value="<?php echo esc_attr( $task['status'] ); ?>"><?php echo $task['status']; ?>%</option>
						<?php $values = apply_filters( 'psp_task_values', array(
							'0'		=>	'0%',
							'5'		=>	'5%',
							'10'	=>	'10%',
							'15'	=>	'15%',
							'20'	=>	'20%',
							'25'	=>	'25%',
							'30'	=>	'30%',
							'35'	=>	'35%',
							'40'	=>	'40%',
							'45'	=>	'45%',
							'50'	=>	'50%',
							'55'	=>	'55%',
							'60'	=>	'60%',
							'65'	=>	'65%',
							'70'	=>	'70%',
							'75'	=>	'75%',
							'80'	=>	'80%',
							'85'	=>	'85%',
							'90'	=>	'90%',
							'95'	=>	'95%',
							'100'	=>	'100%'
						) );
						foreach( $values as $value => $label ): ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</span>

				<?php
				$data_atts = apply_filters( 'psp_single_task_data_atts', array(
                         'task'			=>	$task['ID'],
                         'phase'			=>	$task_phase['index'],
                         'phase_id'          =>   $task_phase['ID'],
                         'project'			=>	$post_id,
				)); ?>

				<input type="submit" name="save" value="save" class="task-save-button js-kb-task-save-button" <?php foreach( $data_atts as $att => $value ) echo 'data-' . $att . '="' . esc_attr($value) . '" '; ?>>

		   </span>

		   <?php do_action( 'psp_task_edit_links_end', $post_id, $task_phase['index'], $task['ID'] ); ?>

		</span>


	<?php } ?>
