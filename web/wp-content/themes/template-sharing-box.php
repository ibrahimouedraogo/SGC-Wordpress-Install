<?php 
/* Social Share Buttons template for Wordpress
 * By Daan van den Bergh 
 */ 

$postUrl = 'http' . ( isset( $_SERVER['HTTPS'] ) ? 's' : '' ) . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"; ?>

<section class="sharing-box content-margin content-background clearfix">
    <h5 class="sharing-box-name">Don't be selfish. Share the knowledge!</h5>
    <div class="share-button-wrapper">
        <a target="_blank" class="share-button share-twitter" href="https://twitter.com/intent/tweet?url=<?php echo $postUrl; ?>&text=<?php echo the_title(); ?>&via=<?php the_author_meta( 'twitter' ); ?>" title="Tweet this">Tweet this</a>
        <a target="_blank" class="share-button share-facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $postUrl; ?>" title="Share on Facebook">Share on Facebook</a>
        <a target="_blank" class="share-button share-googleplus" href="https://plus.google.com/share?url=<?php echo $postUrl; ?>" title="Share on Google+">Share on Google+</a>
    </div>
</section>
