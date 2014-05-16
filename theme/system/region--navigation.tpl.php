<?php
/**
 * @file
 * region--navigation.tpl.php
 *
 * Default theme implementation to display the "navigation" region.
 *
 * Available variables:
 * - $content: The content for this region, typically blocks.
 * - $attributes: String of attributes that contain things like classes and ids.
 * - $content_attributes: The attributes used to wrap the content. If empty,
 *   the content will not be wrapped.
 * - $region: The name of the region variable as defined in the theme's .info
 *   file.
 * - $page: The page variables from bootstrap_process_page().
 *
 * Helper variables:
 * - $is_admin: Flags true when the current user is an administrator.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 *
 * @see bootstrap_preprocess_region().
 * @see bootstrap_process_page().
 *
 * @ingroup themeable
 */
?>

<header<?php print $attributes; ?>>
  <?php if ($content_attributes): ?><div class="container-fluid"><?php endif; ?>
  <div class="navbar-header">
    <a class="logo navbar-btn pull-left" href="<?php print $page['front_page']; ?>" title="<?php print t('Home'); ?>">
      <img src="<?php print $page['logo']; ?>" alt="<?php print t('Home'); ?>" />
    </a>
    <a class="name navbar-brand" href="<?php print $page['front_page']; ?>" title="<?php print t('Home'); ?>"><?php print $page['site_name']; ?></a>
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
  </div>
  <div class="navbar-collapse collapse">
    <nav role="navigation">
      <?php if ($page['primary_nav'] || $page['secondary_nav'] || $content): ?>
        <?php print render($page['primary_nav']); ?>
        <?php print render($page['secondary_nav']); ?>
        <?php print $content; ?>
      <?php endif; ?>
      <?php print render($variables['task_menu']); ?>
    </nav>
  </div>
  <?php if ($content_attributes): ?></div><?php endif; ?>
</header>
