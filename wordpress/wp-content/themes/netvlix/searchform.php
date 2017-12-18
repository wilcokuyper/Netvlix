<?php
$unique_id = esc_attr( uniqid( 'search-form-' ) );

if ( !is_tax( 'netvlix_genre' ) ) {
  // redirect naar de home page (zoeken naar alle films)
  $form_callback = home_url( '/' );
} else {
  // redirect naar de taxonomy page (zoeken per genre)
  $form_callback = get_term_link( get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
}
?>

<form role="search" method="get" class="search-form form-inline"
      action="<?php echo esc_url( $form_callback ); ?>">
  <input type="search" id="<?php echo $unique_id; ?>"
        class="search-field form-control col-sm-12"
        placeholder="Zoek films &hellip;"
        value="<?php echo get_search_query(); ?>" name="s" />
</form>
