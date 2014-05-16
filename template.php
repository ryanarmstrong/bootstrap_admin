<?php

/**
 * @file
 * template.php
 */

function bootstrap_admin_preprocess_region(&$variables) {
  $region = $variables['region'];
  // Handle regions.
  switch ($region) {
    case 'navigation':
      // Load the Management and User menus to merge together.
      $management_menu = menu_tree('management');
      $user_menu = menu_tree('user-menu');

      // Merge the two menus into the Task Menu.
      $variables['task_menu'] = array_merge($management_menu, $user_menu);

      // Set the menu to use bootstrap_admin_menu_tree__navright().
      $variables['task_menu']['#theme_wrappers'] = array('menu_tree__navright');

      // Alter the titles to use Glyphicons instead of text.
      $variables['task_menu']['0']['#title'] = '<span class="glyphicon glyphicon-cog"></span>';
      $variables['task_menu']['0']['#localized_options']['html'] = TRUE;
      $variables['task_menu']['1']['#title'] = '<span class="glyphicon glyphicon-user"></span>';
      $variables['task_menu']['1']['#localized_options']['html'] = TRUE;
      $variables['task_menu']['2']['#title'] = '<span class="glyphicon glyphicon-log-out"></span>';
      $variables['task_menu']['2']['#localized_options']['html'] = TRUE;

      break;
  }
}

/**
 * Bootstrap theme wrapper function for the primary menu links.
 */
function bootstrap_admin_menu_tree__navright(&$variables) {
  return '<ul class="nav navbar-nav navbar-right">' . $variables['tree'] . '</ul>';
}

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
  );
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
  $type_class = '';
  $sub_links = '';

  $variables['attributes']['class'][] = 'btn-group';
  // Type class.
  if (isset($variables['type'])) {
    $type_class .= ' btn-' . $variables['type'];
  }
  else {
    $type_class .= ' btn-default';
  }

  // Size class.
  if (isset($variables['size'])) {
    $size_class .= ' btn-' . $variables['size'];
  }
  else {
    $size_class .= '';
  }

  // Start markup.
  $output = '<div' . drupal_attributes($variables['attributes']) . '>';

  // Add as string if its not a link.
  if (is_array($variables['label'])) {
    $output .= l($variables['label']['title'], $variables['label']['href'], $variables['label']);
  }
  // If Button Split, add that
  if ($variables['split'] === TRUE) {
    $first_link = array_shift($variables['links']);
    $output .= l($first_link['title'], $first_link['href'], array('attributes' => array('type' => 'button', 'class' => 'btn' . $type_class . $size_class)));
  }
  $output .= '<a class="btn' . $type_class . $size_class . ' dropdown-toggle" data-toggle="dropdown" href="#">';

  // It is a link, create one.
  if (is_string($variables['label'])) {
    $output .= check_plain($variables['label']);
  }
  if (is_array($variables['links'])) {
    $sub_links = theme('links', array(
      'links' => $variables['links'],
      'context' => 'from_theme',
      'attributes' => array(
        'class' => array('dropdown-menu'),
      ),
    ));
  }
  // Finish markup.
  $output .= '<span class="caret"></span></a>' . $sub_links . '</div>';
  return $output;
}

