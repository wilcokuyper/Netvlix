<?php

function netvlix_theme_setup() {

	// Laad the MovieDB configuratie
	if ( get_option( 'netvlix_moviedb_apikey' ) != '' ) {
		$response = wp_remote_get( 'https://api.themoviedb.org/3/configuration?api_key=' . get_option( 'netvlix_moviedb_apikey' ) );

		// Sla een tweetal parameters op voor het tonen van de film posters
		if ( is_array( $response ) ) {
			$body = $response['body'];
			$config = json_decode( $body, true );
			$GLOBALS['base_url'] = $config['images']['secure_base_url'];
			$GLOBALS['poster_sizes'] = $config['images']['poster_sizes'];
		}
	}
}
add_action( 'after_setup_theme', 'netvlix_theme_setup' );

// Setup scripts en stylesheets
function netvlix_theme_scripts() {
	wp_enqueue_script( 'jquery-js', 'https://code.jquery.com/jquery-3.2.1.slim.min.js' );
	wp_enqueue_script( 'popper-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js' );
	wp_enqueue_script( 'bootstrap-js', get_theme_file_uri( '/assets/js/bootstrap.min.js' ), [ 'jquery-js' , 'popper-js' ] );
	wp_enqueue_script( 'main-js', get_theme_file_uri( '/assets/js/main.js' ), [ 'jquery-js' ], null, true );

	wp_enqueue_style( 'bootstrap-style', get_theme_file_uri( '/assets/css/bootstrap.min.css' ) );
	wp_enqueue_style( 'netvlix-movies-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'netvlix_theme_scripts' );

// Pas de permalink stuctuur aan naar /category/postname zodat het werkt met de genre rewrite
function netvlix_set_permalink(){
	global $wp_rewrite;
  $wp_rewrite->set_permalink_structure('/%category%/%postname%/');
}
add_action('init', 'netvlix_set_permalink');

// Genere post url
function netvlix_poster_url( $poster_path ) {
	echo $GLOBALS['base_url'] . $GLOBALS['poster_sizes'][2] . $poster_path;
}

// Helper function voor genre filter
function netvlix_show_genre_filter_select() {
	  $genres = get_terms( array(
	    'taxonomy'		=> 'netvlix_genre',
	    'hide_empty'	=> false,
	    'order_by'		=> 'name',
	  ) );

	if (count( $genres )) {
		if ( isset( get_queried_object()->taxonomy ) && get_queried_object()->taxonomy === 'netvlix_genre' ) {
			$selectedOption = get_queried_object()->name;
		} else {
			$selectedOptions = 'Alle genres';
		}
		?>
	<select class="form-control" id="genre-select-list" name="genre">
		<option value="-1"<?php if ( 'Alle genres' === $selectedOptions ) echo ' selected'; ?>>Alle genres</option>
		<?php foreach($genres as $genre) : ?>
		<option value="<?php echo $genre->slug; ?>"<?php if ( $genre->name === $selectedOption ) echo ' selected'; ?>><?php echo $genre->description ?></option>
		<?php endforeach; ?>
	</select>
	<?php
	}
}

// Formatteer genre lijst als string
function netvlix_format_genres ($id) {
	$genres = get_the_terms( $id, 'netvlix_genre' );
	if ($genres != false) {
		echo "Genres: ";
		$genre_array = array();
		foreach($genres as $genre) {
			$genre_array[] = $genre->description;
		}
		return substr( trim( join( ', ', $genre_array ) ), 0, 20 ) . '...';
	}
}

// Pas de lengte van excerpt aan
function netvlix_custom_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'netvlix_custom_excerpt_length' );

// Pas de titel van een taxonomy page aan
function netvlix_alter_genre_title( $title, $sep ) {
	if ( is_feed() )
			return $title;

	$title = get_bloginfo( 'name' );

	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
			$title = "$title $sep $site_description";

	elseif ( is_tax( 'netvlix_genre' ) )
		$title = "$title $sep " . get_term( get_queried_object()->term_id )->description;

	return $title;
}
add_filter( 'wp_title', 'netvlix_alter_genre_title', 10, 2 );

// Bootstrap pagination template
function netvlix_pagination($pages = '', $range = 2) {
	$showitems = ($range * 2) + 1;
	global $paged;
	if(empty($paged)) $paged = 1;
	if($pages == '')
	{
		global $wp_query;
		$pages = $wp_query->max_num_pages;

		if(!$pages)
			$pages = 1;
	}

	if(1 != $pages)
	{
		echo '<nav role="navigation">';
    echo '<ul class="pagination justify-content-center">';

	 	if($paged > 2 && $paged > $range+1 && $showitems < $pages)
			echo '<li class="page-item"><a class="page-link" href="'.get_pagenum_link(1).'">&laquo;</a></li>';

	 	if($paged > 1 && $showitems < $pages)
			echo '<li class="page-item"><a class="page-link" href="'.get_pagenum_link($paged - 1).'">&lsaquo;</a></li>';

		for ($i=1; $i <= $pages; $i++)
		{
			if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
			echo ($paged == $i)? '<li class="page-item active"><span class="page-link">'.$i.'</span></li>' : '<li class="page-item"><a class="page-link" href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
		}

		if ($paged < $pages && $showitems < $pages)
			echo '<li class="page-item"><a class="page-link" href="'.get_pagenum_link($paged + 1).'">&rsaquo;</a></li>';

	 	if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages)
			echo '<li class="page-item"><a class="page-link" href="'.get_pagenum_link($pages).'">&raquo;</a></li>';

	 	echo '</ul>';
  	echo '</nav>';
	}
}

// Voeg netvlix_film post type toe aan de zoekresultaten
function netvlix_search_filter( $query ) {
  if ( !is_admin() && $query->is_main_query() ) {
    if ( $query->is_search ) {
      $query->set( 'post_type', array( 'post', 'page', 'netvlix_film' ) );
    }
  }
}
add_action( 'pre_get_posts', 'netvlix_search_filter' );


function netvlix_show_films_on_frontpage( $query ) {

  // FIlter alleen de main query en alleen als we niet op de admin pagina zijn
  if ( is_admin() || ! $query->is_main_query() ) {
  	return;
  }

  global $wp;
  $front = false;

	// Controleer of we op de homepage of op een genre pagina zijn
  if ( ( is_home() || is_tax( 'netvlix_genre' ) || is_front_page() && empty( $wp->query_string ) ) ) {
  	$front = true;
  }

	// Als we op de $front page zijn passen we de query aan om de films te laden
  if ( $front ) {

    $query->set( 'post_type', 'netvlix_film' );
		$query->set( 'posts_per_page', 4 );
		$query->set( 'meta_key', 'popularity' );
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'order', 'DESC' );
    $query->set( 'page_id', '' );

    // Set properties to match an archive
    $query->is_page = 0;
    $query->is_singular = 0;
    $query->is_post_type_archive = 1;
    $query->is_archive = 1;

  }
}
add_action( 'pre_get_posts', 'netvlix_show_films_on_frontpage' );
