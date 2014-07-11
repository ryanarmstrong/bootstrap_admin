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
        <img class="profile-img" src="https://lh5.googleusercontent.com/-b0-k99FZlyE/AAAAAAAAAAI/AAAAAAAAAAA/eu7opA4byxI/photo.jpg?sz=120"
            alt="">
        <div class="form-signin">
          <?php /* region--content.tpl.php */ ?>
          <?php print render($page['content']); ?>
        </div>
      </div>
    </div>
  </div>
</div>
