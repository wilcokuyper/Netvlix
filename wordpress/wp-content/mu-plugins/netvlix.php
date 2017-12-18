<?php
/*
Plugin Name: Film CPT
Description: Deze plugin registreert de 'film' post type
Version: 1.0
License: GPLv2
text-domain: netvlix
*/

// Zorg ervoor dat dit script niet direct wordt uitgevoerd.
defined( 'ABSPATH' ) or die( 'Do not run this script directly!' );

// Maak een Custom Post Type voor de films uit MovieDB
function netvlix_create_movie_post_type() {
$labels = array(
		'name'                  => _x( 'Films', 'Post Type General Name', 'netvlix' ),
		'singular_name'         => _x( 'Film', 'Post Type Singular Name', 'netvlix' ),
		'menu_name'             => __( 'Films', 'netvlix' ),
		'name_admin_bar'        => __( 'Film', 'netvlix' ),
		'archives'              => __( 'Film Archieven', 'netvlix' ),
		'attributes'            => __( 'Film Attributen', 'netvlix' ),
		'all_items'             => __( 'Alle films', 'netvlix' ),
		'add_new_item'          => __( 'Voeg nieuwe film toe', 'netvlix' ),
		'add_new'               => __( 'Voeg toe', 'netvlix' ),
		'new_item'              => __( 'Nieuwe film', 'netvlix' ),
		'edit_item'             => __( 'Bewerk film', 'netvlix' ),
		'update_item'           => __( 'Update film', 'netvlix' ),
		'view_item'             => __( 'Bekijk film', 'netvlix' ),
		'view_items'            => __( 'Bekijk films', 'netvlix' ),
		'search_items'          => __( 'Zoek films', 'netvlix' ),
		'not_found'             => __( 'Niet gevonden', 'netvlix' ),
		'not_found_in_trash'    => __( 'Niet gevonden in de prullenbak', 'netvlix' ),
		'featured_image'        => __( 'Film poster', 'netvlix' ),
		'set_featured_image'    => __( 'Kies film poster', 'netvlix' ),
		'remove_featured_image' => __( 'Verwijder film poster', 'netvlix' ),
		'use_featured_image'    => __( 'Gebruik als film poster', 'netvlix' ),
		'insert_into_item'      => __( 'Voeg toe in film', 'netvlix' ),
		'uploaded_to_this_item' => __( 'Geupload naar deze film', 'netvlix' ),
		'items_list'            => __( 'Film lijst', 'netvlix' ),
		'items_list_navigation' => __( 'Film lijst navigatie', 'netvlix' ),
		'filter_items_list'     => __( 'Filter film lijst', 'netvlix' ),
	);
	$args = array(
		'label'                 => __( 'Film', 'netvlix' ),
		'description'           => __( 'Film', 'netvlix' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'page-attributes', 'thumbnail', 'custom-fields' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_icon'							=> 'dashicons-video-alt',
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'netvlix_film', $args );

	$labels = array(
		'name'                      => _x( 'Genre', 'taxonomy general name', 'netvlix' ),
		'singular_name'             => _x( 'Genre', 'texonomy singular name', 'netvlix' ),
		'menu_name'                 => __( 'Genre', 'netvlix' ),
		'all_items'                 => __( 'Alle genres', 'netvlix' ),
		'new_item_name'             => __( 'Nieuw genre omschrijving', 'netvlix' ),
		'add_new_item'              => __( 'Voeg nieuw genre toe', 'netvlix' ),
		'edit_item'                 => __( 'Bewerk genre', 'netvlix' ),
		'update_item'               => __( 'Update genre', 'netvlix' ),
		'view_item'                 => __( 'Bekijk genre', 'netvlix' ),
		'separate_items_with_commas'=> __( 'Separate genres with commas', 'netvlix' ),
		'add_or_remove_items'       => __( 'Voeg toe of verwijder genres', 'netvlix' ),
		'choose_from_most_used'     => __( 'Kies uit de meest gebruikte', 'netvlix' ),
		'popular_items'             => __( 'Populaire genres', 'netvlix' ),
		'search_items'              => __( 'Zoek genres', 'netvlix' ),
		'not_found'                 => __( 'Niet gevonden', 'netvlix' ),
		'no_terms'                  => __( 'Geen genres', 'netvlix' ),
		'items_list'                => __( 'Genres lijst', 'netvlix' ),
		'items_list_navigation'			=> __( 'Genres lijst navigatie', 'netvlix' ),
	);
	$args = array(
		'labels'                    => $labels,
		'hierarchical'              => false,
		'public'                    => true,
		'rewrite'										=> array( 'slug' => 'genres' ),
		'show_ui'                   => true,
		'show_admin_column'         => true,
		'show_in_nav_menus'         => true,
		'show_tagcloud'             => true,
	);
	register_taxonomy( 'netvlix_genre', 'netvlix_film', $args );
}
add_action( 'init', 'netvlix_create_movie_post_type', 0 );

// Maak een settings pagina waar de MovideDB Apikey kan worden ingevoerd
function netvlix_api_settings_page() {
?>
	<div class="wrap">
		<h1>theMovieDB Instellingen</h1>
		<form method="post" action="options.php">
		<?php
			settings_fields( 'section' );
		  do_settings_sections( 'netvlix_moviedb_options' );
		  submit_button();
		 ?>
		 </form>
	</div>
<?php
}

// Input veld voor the MovieDB API key
function netvlix_display_moviedb_api_element() {
?>
	<input type="text" name="netvlix_moviedb_apikey" id="netvlix_moviedb_apikey" value="<?php echo get_option( 'netvlix_moviedb_apikey' ); ?>" />
<?php
}

// Registreer de settings voor de API key
function netvlix_display_settings_panel_fields() {
	add_settings_section( 'section', 'Alle instellingen', null, 'netvlix_moviedb_options' );
	add_settings_field( 'netvlix_moviedb_apikey', 'API Key', 'netvlix_display_moviedb_api_element', 'netvlix_moviedb_options', 'section' );
  register_setting( 'section', 'netvlix_moviedb_apikey' );
}
add_action( 'admin_init', 'netvlix_display_settings_panel_fields' );

// Voeg het menu item toe aan de admin pagina
function netvlix_add_api_settings_menu_item() {

	add_submenu_page( 'options-general.php',
                  'theMovieDatabase',
                  'the Movie Database',
                  'manage_options',
                  'the-movie-database',
                  'netvlix_api_settings_page'
               );
}
add_action( 'admin_menu', 'netvlix_add_api_settings_menu_item' );

// Laad the MovieDB configuratie
function netvlix_load_moviedb_config() {
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
add_action( 'init', 'netvlix_load_moviedb_config' );

// Controleer of een genre al bestaat
function netvlix_genre_exists( $genre ) {
	$existing_terms = get_terms( array('taxonomy' => 'netvlix_genre') );
	foreach ( $existing_terms as $term ) {
		if ( $term->name === $genre ) {
			return true;
		}
	}
	return false;
}

// Haal alle genres op uit the MovieDB
function netvlix_load_genres() {
	if ( '' != get_option( 'netvlix_moviedb_apikey' ) ) {
		$response = wp_remote_get( 'https://api.themoviedb.org/3/genre/movie/list?api_key=' . get_option( 'netvlix_moviedb_apikey' ) );

		if ( is_array( $response ) ) {
  		$body = $response['body'];
			$genres = json_decode( $body, true );

			if ( count( $genres['genres'] ) ) {

				foreach ( $genres['genres'] as $genre ) {

					if ( !netvlix_genre_exists( $genre['id'] ) ) {

						wp_insert_term(
							$genre['id'],
							'netvlix_genre',
							array(
								'description' => $genre['name'],
								'slug' => strtolower( $genre['name'] ),
							)
						);

					}
				}
				return true;
			}
		}
	}
	return false;
}

// Verwijder alle films in WordPress, wordt uitgevoerd als de cronjob loopt
function netvlix_delete_all_movies() {
	$movies = get_posts( array( 'post_type' => 'netvlix_film', 'numberposts' => -1 ) );
	if ( !$movies ) {
		foreach ( $movies as $movie ) {
			wp_delete_post( $movie->ID, true );
		}
	}
}

// Haal de 50 populairste films op uit the MovieDB
function netvlix_load_popular_movies() {
	netvlix_delete_all_movies();
	if ( netvlix_load_genres() ) {

		if ( get_option( 'netvlix_moviedb_apikey' ) != '' ) {
			$page = 1;

			while ( $page < 4 ) {
				$response = wp_remote_get( 'https://api.themoviedb.org/3/discover/movie?sort_by=popularity.desc&page=' . $page . '&api_key=' . get_option( 'netvlix_moviedb_apikey' ) );

				if ( is_array( $response ) ) {
		  		$body = $response['body'];
					$moviedb_data = json_decode( $body, true );

					if ( isset( $moviedb_data['results'] ) ) {

						if ( 3 === $page ) {
							$films = array_slice( $moviedb_data['results'], 0, 10 );
						} else {
							$films = $moviedb_data['results'];
						}

						foreach ( $films as $film ) {

							wp_insert_post( array(
								'post_type' 		=> 'netvlix_film',
								'post_title'		=> $film['title'],
								'post_content'	=> $film['overview'],
								'meta_input' 		=> array(
									'popularity' 		=> $film['popularity'],
									'poster_image' 	=> $film['poster_path'],
									'release_date' 	=> $film['release_date'],
									'vote_average' 	=> $film['vote_average'],
								),
								'tax_input' 		=> array(
									'netvlix_genre'	=> join( ',', $film['genre_ids'] ),
								),
								'post_status'		=> 'publish',
								)
							);

						}
					}
				}
				$page++;
			}
		}
	}
}
add_action( 'netvlix_load_movie_hook', 'netvlix_load_polular_movies' );

// Registreer cronjob voor het dagelijks laden van de populairste films
function netvlix_register_daily_load_popular_movies_event() {
	if ( !wp_next_scheduled( 'netvlix_load_movie_hook' ) ) {
		wp_schedule_event( time(), 'daily', 'netvlix_load_movie_hook' );
	}
}
add_action( 'init', 'netvlix_register_daily_load_popular_movies_event' );

// Als er nog geen films in WordPress zitten, haal ze dan de eerste keer op uit the MovieDB
function netvlix_initial_movie_load() {
	if ( 0 === count( get_posts( array( 'post_type' => 'netvlix_film ') ) ) )
		netvlix_load_popular_movies();
}
add_action( 'init', 'netvlix_initial_movie_load' );
