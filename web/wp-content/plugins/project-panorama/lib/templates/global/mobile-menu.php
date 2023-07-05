<div class="psp-mobile-nav">

     <div class="psp-mobile-nav-wrap">

          <button class="psp-js-close-mmenu"><i class="fa fa-close"></i></button>

          <?php do_action( 'psp_before_mobile_nav' ); ?>

          <div class="psp-mobile-nav__items">
              <?php
              $nav_items = psp_get_section_nav_items();

              foreach( $nav_items as $link ):

                  $class = apply_filters( 'psp_section_nav_link_class', 'inactive', $link[ 'slug' ] ); ?>

                  <div class="psp-mobile-nav__item" id="psp-sub-nav-<?php echo esc_attr($link['slug']); ?>"><a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $link[ 'url' ] ); ?>"><span class="<?php echo esc_attr( $link[ 'icon' ] ); ?>"></span> <?php echo esc_html( $link[ 'name' ] ); ?></a></div>

              <?php endforeach; ?>

          </div>

          <?php do_action( 'psp_after_mobile_nav' ); ?>

     </div>

</div>
