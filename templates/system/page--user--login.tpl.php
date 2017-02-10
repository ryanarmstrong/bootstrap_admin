<?php
/**
 * @file
 * Bootstrap Admin template for the user login page.
 *
 * @ingroup themeable
 */

?>

<div class="main-container container">
  <div class="row">
    <div class="col-sm-6 col-md-4 col-md-offset-4" style="margin-top: 10%;">
      <div class="account-wall">
        <img class="profile-img" src="/profiles/tegrazone/themes/content_flow_admin/img/user-icon.png" />
        <div class="form-signin">
          <?php /* region--content.tpl.php */ ?>
          <?php print render($page['content']); ?>
        </div>
      </div>
    </div>
  </div>
</div>
