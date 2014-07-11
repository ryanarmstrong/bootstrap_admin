<?php

/**
 * @file
 * template.php
 */

/**
 * Implements hook_theme().
 *
 * Register theme hook implementations.
 *
 * The implementations declared by this hook have two purposes: either they
 * specify how a particular render array is to be rendered as HTML (this is
 * usually the case if the theme function is assigned to the render array's
 * #theme property), or they return the HTML that should be returned by an
 * invocation of theme().
 *
 * @see _bootstrap_theme()
 */
function bootstrap_admin_theme(&$existing, $type, $theme, $path) {
  return array(
    'bootstrap_btn_dropdown' => array(
      'variables' => array(
        'links' => array(),
        'attributes' => array(),
        'type' => NULL,
        'size' => NULL,
        'split' => FALSE,
      ),
    ),
    'user_login' => array(
      'render element' => 'form',
      'path' => drupal_get_path('theme', 'bootstrap_admin') . '/theme/user',
      'template' => 'user-login',
      'preprocess functions' => array(
         'bootstrap_admin_preprocess_user_login'
      ),
    ),
    'user_pass' => array(
      'render element' => 'form',
      'path' => drupal_get_path('theme', 'bootstrap_admin') . '/theme/user',
      'template' => 'user-pass',
      'preprocess functions' => array(
        'bootstrap_admin_preprocess_user_pass'
      ),
    ),
  );
}

/**
 * Implements hook_preprocess_region().
 */
function bootstrap_admin_preprocess_region(&$variables) {
  $region = $variables['region'];
  // Use the user content region template.
  if (($_GET['q'] == 'user/login' || $_GET['q'] == 'user' || $_GET['q'] == 'user/password') && $region == 'content') {
    $variables['theme_hook_suggestions'][] = 'region__content__user';
  }
  if ($_GET['q'] == 'user' && !user_is_logged_in()) {
    drupal_goto('user/login');
  }
}

/**
 * Override the theme_links() function
 */
function bootstrap_admin_links($variables) {
  $links = $variables['links'];
  $attributes = $variables['attributes'];
  $heading = $variables['heading'];

  global $language_url;
  $output = '';

  // Whitelist of pages that should use a split button group instead of the standard links theme
  $whitelist = array(
    'admin/structure/context',
    'admin/structure/flags',
    'admin/structure/menu/manage/translation',
  );

  // Intercept certain 'links' themes and convert them to other Bootstrap components
  // Convert Context inline links to a split button dropdown
  if (in_array($_GET['q'], $whitelist) && $variables['context'] != 'from_theme') {
    if (count($variables['links']) > 2) {
      $output = theme('bootstrap_btn_dropdown', array(
        'links' => $variables['links'],
        'split' => TRUE,
        'size' => 'sm',
        'attributes' => array('class' => array('links'))
      ));
    } else {
      foreach ($variables['links'] as $key => $link) {
        $output .= l($link['title'], $link['href'], array('attributes' => array('type' => 'button', 'class' => 'btn btn-default btn-sm')));
      }
    }

    return $output;
  }

  if (count($links) > 0) {
    $output = '';
    $output .= '<ul' . drupal_attributes($attributes) . '>';

    // Treat the heading first if it is present to prepend it to the
    // list of links.
    if (!empty($heading)) {
      if (is_string($heading)) {
        // Prepare the array that will be used when the passed heading
        // is a string.
        $heading = array(
          'text' => $heading,
          // Set the default level of the heading.
          'level' => 'li',
        );
      }
      $output .= '<' . $heading['level'];
      if (!empty($heading['class'])) {
        $output .= drupal_attributes(array('class' => $heading['class']));
      }
      $output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
    }

    $num_links = count($links);
    $i = 1;

    foreach ($links as $key => $link) {
      $children = array();
      if (isset($link['below'])) {
        $children = $link['below'];
      }
      $attributes = array('class' => array($key));
      // Add first, last and active classes to the list of links.
      if ($i == 1) {
        $attributes['class'][] = 'first';
      }
      if ($i == $num_links) {
        $attributes['class'][] = 'last';
      }
      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
        && (empty($link['language']) || $link['language']->language == $language_url->language)) {
        $attributes['class'][] = 'active';
      }

      if (count($children) > 0) {
        $attributes['class'][] = 'dropdown';
        $link['attributes']['data-toggle'] = 'dropdown';
        $link['attributes']['class'][] = 'dropdown-toggle';
      }

      if (!isset($link['attributes'])) {
        $link['attributes'] = array();
      }

      $link['attributes'] = array_merge($link['attributes'], $attributes);

      if (count($children) > 0) {
        $link['attributes']['class'][] = 'dropdown';
      }

      $output .= '<li' . drupal_attributes($attributes) . '>';

      if (isset($link['href'])) {
        if (count($children) > 0) {
          $link['html'] = TRUE;
          $link['title'] .= ' <span class="caret"></span>';
          $output .= '<a' . drupal_attributes($link['attributes']) . ' href="#">' . $link['title'] . '</a>';
        }
        else {
          // Pass in $link as $options, they share the same keys.
          $output .= l($link['title'], $link['href'], $link);
        }
      }
      elseif (!empty($link['title'])) {
        // Some links are actually not links, but wrap these with <span> so
        // title and class attributes can be added.
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }
        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
      }

      $i++;
      if (count($children) > 0) {
        $attributes = array();
        $attributes['class'] = array('dropdown-menu');
        $output .= theme('bootstrap_links', array('links' => $children, 'attributes' => $attributes));
      }
      $output .= "</li>\n";
    }
    $output .= '</ul>';
  }

  return $output;
}

/**
 * Overrides theme_bootstrap_btn_dropdown().
 */
function bootstrap_admin_bootstrap_btn_dropdown($variables) {
  // Add button classes.
  $variables['attributes']['class'][] = 'btn-group';
  $type_class = isset($variables['type']) ? ' btn-' . $variables['type'] : ' btn-default';
  $size_class = isset($variables['size']) ? ' btn-' . $variables['size'] : '';

  // Start markup.
  $output = '<div' . drupal_attributes($variables['attributes']) . '>';

  // Add as string if its not a link.
  if (isset($variables['label']) && is_array($variables['label'])) {
    $output .= l($variables['label']['title'], $variables['label']['href'], $variables['label']);
  }
  // If Button Split, add that.
  if ($variables['split'] === TRUE) {
    $first_link = array_shift($variables['links']);
    $output .= l($first_link['title'], $first_link['href'], array('attributes' => array('type' => 'button', 'class' => 'btn' . $type_class . $size_class)));
  }
  $output .= '<a class="btn' . $type_class . $size_class . ' dropdown-toggle" data-toggle="dropdown" href="#">';

  // It is a link, create one.
  if (isset($variables['label']) && is_string($variables['label'])) {
    $output .= check_plain($variables['label']);
  }
  $sub_links = is_array($variables['links']) ?
    theme('links', array(
      'links' => $variables['links'],
      'context' => 'from_theme',
      'attributes' => array(
        'class' => array('dropdown-menu'),
      ),
    )) : '';

  // Finish markup.
  $output .= '<span class="caret"></span></a>' . $sub_links . '</div>';
  return $output;
}

function bootstrap_admin_preprocess_user_login(&$vars) {
  $vars['form']['name']['#attributes']['placeholder'] = t('Username');
  unset($vars['form']['name']['#title']);
  $vars['form']['pass']['#attributes']['placeholder'] = t('Password');
  unset($vars['form']['pass']['#title']);
  $vars['form']['actions']['submit']['#attributes']['class'] = array('btn-primary', 'btn-block', 'btn-lg');
}
function bootstrap_admin_preprocess_user_pass(&$vars) {
  $vars['form']['name']['#attributes']['placeholder'] = t('Username or e-mail address');
  unset($vars['form']['name']['#title']);
  $vars['form']['actions']['submit']['#attributes']['class'] = array('btn-primary', 'btn-block', 'btn-lg');
}
