<?php $unique_id = esc_attr( uniqid( 'search-form-' ) ); ?>

<form role="search" method="get" class="search-form form-inline"
      action="<?php global $wp; echo esc_url( home_url( $wp->request ) ); ?>">
  <input type="search" id="<?php echo $unique_id; ?>"
        class="search-field form-control col-sm-12"
        placeholder="Zoek films &hellip;"
        value="<?php echo get_search_query(); ?>" name="s" />
</form>
