<?php
/**
 * Function that outputs the gallery code
 *
 * @return null
 *
 **/

add_shortcode( 'psp-project-gallery', 'psp_project_gallery_shortcode' );
function psp_project_gallery_shortcode() {

	$post_id 	= get_the_ID();
	$images		=	get_field( 'gallery', $post_id );
	$style		=	get_field( 'gallery_style', $post_id );

	ob_start();

	if( ( $style == 'grid' ) || ( empty( $style ) ) ) {

		psp_gallery_output_grid( $images );

	} elseif( $style == 'masonry' ) {

		psp_gallery_output_masonry( $images );

	} elseif( $style == 'slideshow' ) {

		psp_gallery_output_slideshow( $images );

	} elseif( $style == 'band' ) {

		psp_gallery_output_banded( $images );

	}

	return ob_get_clean();


}

add_shortcode( 'psp-gallery', 'psp_gallery_shortcode' );
function psp_gallery_shortcode( $atts ) {

	if( !isset( $atts[ 'id'] ) ) {
		$atts['id'] = get_the_ID();
	}

	$images		=	get_field( 'gallery', $atts[ 'id' ] );
	$style		=	get_field( 'gallery_style', $atts[ 'id' ] );

	wp_enqueue_script( 'jquery' );
	psp_gallery_assets( $style );

	ob_start(); ?>
		<div id="psp-project">
			<?php
			if( empty( $images ) ) {

				return;

			}

			if( ( $style == 'grid' ) || ( empty( $style ) ) ) {

				psp_gallery_output_grid( $images );

			} elseif( $style == 'masonry' ) {

				psp_gallery_output_masonry( $images );

			} elseif( $style == 'slideshow' ) {

				psp_gallery_output_slideshow( $images );

			} elseif( $style == 'band' ) {

				psp_gallery_output_banded( $images );

			} ?>
		</div>
	<?php

	return ob_get_clean();

}

function psp_gallery_include( $post_id = null ) {

	$post_id = ( $post_id === null ? get_the_ID() : $post_id );

	$images 		= get_field( 'gallery', $post_id );
	$location 	= psp_get_option( 'psp_gallery_location', 'overview' );
	$style		= get_field( 'gallery_style', $post_id );

	$count_class = 'image-count-' . ( isset($images) && !empty($images) ? count($images) : '0' );

	do_action( 'psp_before_gallery', get_the_ID(), $location, $style, $images );

	echo '<div id="psp-gallery-container" class="' . esc_attr($count_class) . '">';

		do_action( 'psp_before_gallery_images', get_the_ID(), $location, $style, $images );

		psp_gallery_style( $style, $post_id, $images );

		do_action( 'psp_after_gallery_images', $post_id, $images );

	echo '</div>';

	do_action( 'psp_after_gallery', $post_id, $images );

}

function psp_gallery_output_grid( $images, $post_id = null ) {

	$post_id = ( $post_id === null ? get_the_ID() : $post_id );

	psp_gallery_assets( 'grid' );

	if( $images && !empty($images) ):

	    $location 	= get_option( 'psp_gallery_location' ); ?>

		<div class="psp-gallery-section psp-gallery-located-<?php echo $location; ?> psp-gallery-grid <?php if(! get_field( 'gallery_description', $post_id ) ) { echo 'grid-align-center'; } ?>">

			<div class="psp-row cf group">

				<?php if( get_field( 'gallery_description', $post_id ) ): ?>

					<div class="psp-col-md-4">

						<div class="psp-overview-box cf">

	        				<?php if( get_field( 'gallery_title', $post_id ) ) { echo '<div class="psp-4">' . get_field( 'gallery_title', $post_id ) . '</div>'; } ?>

							<?php if( get_field( 'gallery_description', $post_id ) ) { ?>

								<div class="psp-gallery-description">

									<?php echo wpautop( get_field( 'gallery_description', $post_id ) ); ?>

								</div>

							<?php } ?>

						</div>

						</div>

						<div class="psp-col-md-8">

				<?php else: ?>

					<?php if( get_field( 'gallery_title', $post_id ) ) { echo '<h2 class="psp-text-center">' . get_field( 'gallery_title', $post_id ) . '</h2>'; } ?>

					<div class="psp-col-md-12">

				<?php endif; ?>

	        			<div class="psp-gallery-images cf">
							<div class="psp-gallery-items">
								<?php
								$i = 0;
								foreach( $images as $image ):

									$i++; ?>
									<div class="psp-gallery-item">
										<?php
										$caption = ( isset( $image['caption'] ) ? 'data-title="' . $image['caption'] . '"' : '' ); ?>
										<a href="<?php echo $image['url']; ?>" class="psp-gallery-item" data-lightbox="panorama" <?php echo $caption; ?>>
											<img src="<?php echo $image['sizes']['psp-gallery-thumbnail']; ?>" alt="<?php echo $image['alt']; ?>"/>
											<?php if( ( isset( $image['caption'] ) && ( !empty( $image['caption'] ) ) ) ) { ?>
												<span class="caption"><strong><?php echo $image['caption']; ?></strong></span>
											<?php } ?>
											<?php if( ( isset( $image['description'] ) && ( !empty( $image['description'] ) ) ) ) { ?>
												<span class="description"><?php echo wpautop( $image['description'] ); ?></span>
											<?php } ?>
										</a>
									</div>
								<?php endforeach; ?>
	            			</div>

	        			</div>

					</div>

		</div>

	<?php else: ?>
		<div class="psp-gallery-section psp-gallery-located-<?php echo $location; ?> psp-gallery-grid <?php if(! get_field( 'gallery_description', $post_id ) ) { echo 'grid-align-center'; } ?>"></div>
	<?php endif; ?>

</div>

<?php }

function psp_gallery_output_masonry( $images, $post_id = null ) {

	$post_id = ( $post_id === null ? get_the_ID() : $post_id );

	psp_gallery_assets( 'masonry' ); ?>

	<div class="psp-gallery-section psp-gallery-located-<?php echo get_option( 'psp_gallery_location' ); ?> psp-gallery-masonry">

        <?php if( get_field( 'gallery_title', $post_id ) ) { echo '<div class="psp-h2">' . get_field( 'gallery_title', $post_id ) . '</div>'; } ?>

		<?php if( get_field( 'gallery_description', $post_id ) ) { ?>

			<div class="psp-gallery-description">

				<?php echo wpautop( get_field( 'gallery_description', $post_id ) ); ?>

			</div>

		<?php } ?>

        <div class="psp-masonry psp-gallery-items">

            <?php $i = 0; foreach( $images as $image ): $i++; ?>

			<div class="psp-masonry-item psp-gallery-item">
				<?php
				$caption = ( isset( $image['caption'] ) ? 'data-title="' . $image['caption'] . '"' : '' ); ?>
				<a href="<?php echo $image[ 'url' ]; ?>" class="psp-gallery-item" <?php echo $caption; ?> data-lightbox="panorama">
					<img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>"/>
					<?php if( ( isset( $image['caption'] ) && ( !empty( $image['caption'] ) ) ) ) { ?>
						<span class="caption"><strong><?php echo $image['caption']; ?></strong></span>
					<?php } ?>
					<?php if( ( isset( $image['description'] ) && ( !empty( $image['description'] ) ) ) ) { ?>
						<span class="description"><?php echo wpautop( $image['description'] ); ?></span>
					<?php } ?>
				</a>
			</div>

			<?php endforeach; ?>

		</div>

	</div>

<?php }


function psp_gallery_output_slideshow( $images, $post_id = null ) {

	$post_id = ( $post_id === null ? get_the_ID() : $post_id );

	psp_gallery_assets( 'slideshow' );

	$location = get_option( 'psp_gallery_location' ); ?>

	<div class="psp-gallery-section psp-gallery-located-<?php echo get_option( 'psp_gallery_location' ); ?> psp-gallery-slideshow">

        <?php if( get_field( 'gallery_title', $post_id ) ) { echo '<div class="psp-h2">' . get_field( 'gallery_title', $post_id ) . '</div>'; } ?>

		<?php if( get_field( 'gallery_description', $post_id ) ) { ?>

			<div class="psp-gallery-description">

				<?php echo wpautop( get_field( 'gallery_description', $post_id ) ); ?>

			</div>

		<?php } ?>

		<div class="psp-slideshow flexslider">

			<ul class="slides">

        		<?php $i = 0; foreach( $images as $image ): $i++; ?>

					<li class="slide">
						<?php
						$caption = ( isset( $image['caption'] ) ? 'data-title="' . $image['caption'] . '"' : '' ); ?>
						<a href="<?php echo $image[ 'url' ]; ?>" class="psp-gallery-item" data-lightbox="panorama" <?php echo $caption; ?>>
							<img src="<?php echo $image[ 'sizes' ][ 'psp-gallery-slideshow' ]; ?>" alt="<?php echo $image['alt']; ?>" />
							<?php if( ( isset( $image['caption'] ) && ( !empty( $image['caption'] ) ) ) ) { ?>
								<span class="caption"><strong><?php echo $image['caption']; ?></strong></span>
							<?php } ?>
							<?php if( ( isset( $image['description'] ) && ( !empty( $image['description'] ) ) ) ) { ?>
								<span class="description"><?php echo wpautop( $image['description'] ); ?></span>
							<?php } ?>
						</a>
					</li>

				<?php endforeach; ?>

			</ul>

		</div>

	</div>

<?php }

function psp_gallery_output_banded( $images, $post_id = null ) {

	$post_id = ( $post_id === null ? get_the_ID() : $post_id );

	psp_gallery_assets( 'banded' );

	$location = get_option( 'psp_gallery_location' ); ?>

	<div class="psp-gallery-section psp-gallery-located-<?php echo get_option( 'psp_gallery_location' ); ?> psp-gallery-banded">

        <?php if( get_field( 'gallery_title', $post_id ) ) { echo '<div class="psp-h2">' . get_field( 'gallery_title', $post_id ) . '</div>'; } ?>

		<?php if( get_field( 'gallery_description', $post_id ) ) { ?>

			<div class="psp-gallery-description">

				<?php echo wpautop( get_field( 'gallery_description', $post_id ) ); ?>

			</div>

		<?php } ?>

		<div class="touchcarousel minimal-light">
			<div class="touchcarousel-container">

				<?php $i = 0; foreach( $images as $image ): $i++; ?>

					<div class="touchcarousel-item">
						<?php
						$caption = ( isset( $image['caption'] ) ? 'data-title="' . $image['caption'] . '"' : '' ); ?>
						<a href="<?php echo $image[ 'url' ]; ?>" class="psp-gallery-item" data-lightbox="panorama" <?php echo $caption; ?>>
							<img src="<?php echo $image[ 'sizes' ][ 'large' ]; ?>" alt="<?php echo $image['alt']; ?>" />
							<?php if( ( isset( $image['caption'] ) && ( !empty( $image['caption'] ) ) ) ) { ?>
								<span class="caption"><strong><?php echo $image['caption']; ?></strong></span>
							<?php } ?>
							<?php if( ( isset( $image['description'] ) && ( !empty( $image['description'] ) ) ) ) { ?>
								<span class="description"><?php echo wpautop( $image['description'] ); ?></span>
							<?php } ?>
						</a>
					</div>

				<?php endforeach; ?>

			</div>
		</div>

	</div>
	<?php

}

add_action( 'psp_footer', 'psp_gallery_modal_markup');
add_action( 'wp_footer', 'psp_gallery_modal_markup' );
function psp_gallery_modal_markup() {

	if( get_post_type() == 'psp_projects' ) { ?>

	<div id="psp-gallery-modal" class="psp-gallery-modal">

		<div class="psp-row">

			<?php
			$col_class = ( psp_get_option('psp_gallery_show_description') != 'no' ? 'psp-col-md-8' : 'psp-col-md-12' ); ?>

			<div class="psp-gallery-image <?php echo esc_attr( $col_class ); ?>">

			</div>

			<?php if( psp_get_option('psp_gallery_show_description') != 'no' ): ?>
				<div class="psp-gallery-modal-content psp-col-md-4">

					<div class="psp-gallery-desc">

					</div>

					<div class="psp-gallery-caption">

					</div>

				</div>
			<?php else: ?>
				<div class="psp-gallery-caption psp-col-md-12">

				</div>
			<?php endif; ?>

		</div>

	</div>

	<?php }

}

add_filter( 'template_include', 'psp_edit_gallery_template', 10000, 1 );
function psp_edit_gallery_template( $template ) {

	if( !current_user_can('edit_psp_projects') ) {
		return $template;
	}

	if( !is_singular('psp_projects') || !isset($_GET['psp-edit-gallery']) ) {
		return $template;
	}

	return PANO_GALLERY_PATH . '/templates/uploader.php';

}

// add_action( 'psp_after_gallery_images', 'psp_edit_gallery_elements', 10, 2 );
function psp_edit_gallery_elements( $post_id, $images ) {

	if( !current_user_can('edit_psp_projects') ) {
		return;
	}

	$label = ( isset($images) && !empty($images) ? __( 'Edit Gallery', 'psp-projects' ) : __( 'Add Gallery', 'psp-projects' ) ); ?>

	<div class="psp-gallery-front-end">
		<div class="psp-edit-gallery-button"><a href="#" class="js-edit-psp-gallery pano-btn pano-btn-primary"><?php echo esc_html($label); ?></a></div>
		<div class="psp-gallery-frontend-wrapper psp-hide">
			<div class="psp-gallery-loading">
				<img src="<?php echo esc_url( PANO_GALLERY_URL . '/assets/img/loading.gif' ); ?>" alt="Loading...">
			</div>
			<a href="#" class="js-edit-gallery-close" data-post_id="<?php echo esc_attr(get_the_ID()); ?>"><i class="fa fa-close"></i></a>
			<iframe id="psp-gallery-iframe" border="0" src="<?php echo esc_attr(get_the_permalink() . '?psp-edit-gallery=1&post_id=' . get_the_ID() ); ?>"></iframe>
		</div>
	</div>

	<?php

}

add_action( 'wp_ajax_psp_update_gallery_fe', 'psp_update_gallery_fe' );
function psp_update_gallery_fe() {

	if( !isset($_POST['post_id']) ) {
		return false;
	}

	$post_id = $_POST['post_id'];
	$style   = get_field( 'gallery_style', $post_id );

	ob_start();

	psp_gallery_style( $style, $post_id );

	wp_send_json_success( array( 'success' => true, 'markup' => ob_get_clean() ) );

}

function psp_gallery_style( $style = null, $post_id = null, $images = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );
	$style   = ( $style == null ? get_field( 'gallery_style', $post_id ) : $style );
	$images	 = ( $images == null ? get_field( 'gallery', $post_id ) : $images );

	$styles = apply_filters( 'psp_gallery_styles', array(
			'grid'		=> 'psp_gallery_output_masonry',
			'slideshow' => 'psp_gallery_output_slideshow',
			'masonry'	=> 'psp_gallery_output_masonry',
			'band'		=> 'psp_gallery_output_banded'
		) );

	foreach( $styles as $slug => $function ) {

		if( $slug != $style ) {
			continue;
		}

		call_user_func( $function, $images, $post_id );

	}

}
