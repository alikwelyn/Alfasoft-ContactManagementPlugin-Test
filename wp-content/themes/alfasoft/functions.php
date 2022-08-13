<?php

function theme_support()
{
  add_theme_support('post-thumbnails');
}

add_action('after_setup_theme', 'theme_support');

function register_styles()
{
  $version = wp_get_theme()->get('Version');
  wp_enqueue_style('bootstrap-blog-style', get_template_directory_uri() . "/style.css", array('bootstrap-blog-bootstrap'), $version, 'all');
  wp_enqueue_style('bootstrap-blog-bootstrap', "https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css", array(), '4.4.1', 'all');
  wp_enqueue_style('bootstrap-blog-font-awesome', "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css", array(), '5.13.0', 'all');
}

add_action('wp_enqueue_scripts', 'register_styles');

function register_scripts()
{
  wp_enqueue_script('functions', get_template_directory_uri() . "/functions.js", array(), '1.0', true);
}

add_action('wp_enqueue_scripts', 'register_scripts');
