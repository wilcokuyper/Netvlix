<div <?php post_class( 'card d-flex flex-column' ); ?>>

  <img class="card-img-top" src="<?php netvlix_poster_url( get_post_meta ( get_the_ID(), 'poster_image', true ) ); ?>">

  <div class="card-content">

    <h4 class="card-title"><?php the_title(); ?>

      <span class="float-right small badge badge-info">
        <?php echo get_post_meta( get_the_ID(), 'vote_average' )[0]; ?>
      </span>

    </h4>

    <h6 class="card-subtitle text-muted"><?php echo netvlix_format_genres( get_the_ID() ); ?></h6>

    <p class="card-text"><?php the_excerpt(); ?></p>

  </div>

  <div class="card-footer mt-auto">

    <?php if ( get_post_meta( get_the_ID(), 'release_date' ) ) : ?>
    <span class="card-subtitle text-muted"><?php
      $date = date_create( get_post_meta( get_the_ID(), 'release_date' )[0] );
      echo 'Release datum: ' . date_format( $date, 'd-m-Y' );
    ?></span>
    <?php endif; ?>

  </div>

</div>
