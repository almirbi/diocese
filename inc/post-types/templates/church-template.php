<?php

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <?php

            do_action( 'diocese_church_header' );
    
            do_action( 'diocese_church_description' );

            do_action( 'diocese_church_details' );
            ?>

        </div><!-- #post-## -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action( 'storefront_sidebar' );
get_footer();
