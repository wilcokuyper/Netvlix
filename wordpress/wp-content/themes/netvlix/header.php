<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php wp_title('-', true, 'right'); ?></title>

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <nav class="navbar navbar-dark bg-dark">
    <div class="container">
      <a href="<?php echo home_url(); ?>" class="navbar-brand"><?php bloginfo('name') ?></a>
    </div>
  </nav>
