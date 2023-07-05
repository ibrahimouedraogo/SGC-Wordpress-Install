<?php
$post_id 	= ( isset($post_id) ? $post_id : get_the_ID() );
$documents 	= get_field( 'documents', $post_id ); ?>
<div id="psp-documents-list">
	<?php
	if( !empty($documents) && count($documents) >= 6 ): ?>
		<div id="psp-document-nav">
			<input id="psp-documents-live-search" type="text" placeholder="Search...">
		</div>
	<?php
	endif; ?>
	<div class="psp-documents-row psp-ul">
        <?php
		$i = 0; // document_index
		$g = 0; // global document count
		if( $documents ): foreach( $documents as $doc ):
			$document_phase = $doc['document_phase'];
			$document_task = $doc['document_task'];
			if ( ( empty($document_phase) || $document_phase == 'unassigned' ) &&
				( empty($document_task) || $document_task == 'unassigned' ) ):
				$g++;
				include( psp_template_hierarchy( 'projects/overview/documents/single/document.php' ) );
			endif;
		$i++; endforeach; endif; ?>
	</div>
</div>
<?php
if( $g === 0 ) echo '<p class="phase-docs-empty-message">' . __( "No documents at this time." , "psp_projects" ) . '</p>';
