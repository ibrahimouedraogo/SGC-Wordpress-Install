<?php $slug = psp_get_option( 'psp_slug', 'panorama' ); ?>

<form role="search" method="get" id="psp-searchform" action="<?php echo esc_url(psp_get_current_url()); ?>">

	<div id="psp-searchform-box">
		<input type="text" name="psp_s" id="psp_s" placeholder="<?php esc_attr_e( 'Enter keywords...', 'psp_projects' ); ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;"/>
	    <?php
	    $project_types = get_terms( 'psp_tax' );
	    if( !empty( $project_types ) ): ?>
			<div class="psp-select-wrapper">
		        <select name="type" id="psp-types-select" autocomplete="false">
		            <option value="all"><?php esc_html_e( 'All Types', 'psp_projects' ); ?></option>
		            <?php foreach( $project_types as $type ): ?>
		                <option value="<?php echo esc_attr( $type->slug ); ?>" <?php if( isset($_GET['type']) && $_GET['type'] == $type->slug ) echo 'selected'; ?>><?php echo esc_html( $type->name ); ?></option>
		            <?php endforeach; ?>
		        </select>
			</div>
	    <?php endif; ?>

		<div class="psp-select-wrapper">
	    	<select name="count">
				<?php
				$counts = apply_filters( 'psp_search_return_results', array( '10', '20', '50', '100' ) );
				foreach( $counts as $count ): ?>
					<option value="<?php echo esc_attr( $count ); ?>" <?php if( isset($_GET['count']) && $_GET['count'] == $count ) echo 'selected'; ?>><?php echo esc_html( $count ); ?></option>
				<?php endforeach; ?>
	    	</select>
		</div>

	    <?php if( get_query_var( 'psp_status_page' ) ): ?>
	        <input type="hidden" name="psp_status_page" value="<?php echo esc_attr( get_query_var( 'psp_status_page' ) ); ?>">
	    <?php endif; ?>

		<?php do_action( 'psp_search_form_after_inputs' ); ?>

		<input type="hidden" value="psp_projects" name="post_type">
		<?php /*
	    <input type="hidden" value="<?php echo esc_attr( $status ); ?>" name="status">
	    */ ?>
		<button type="submit" id="searchsubmit" class="psp-search-btn"><i class="fa fa-search"></i></button>

	</div> <!--/#psp-searchform-box-->

</form>
