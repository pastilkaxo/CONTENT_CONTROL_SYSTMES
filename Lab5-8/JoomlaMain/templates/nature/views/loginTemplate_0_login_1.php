<section class="u-align-center u-clearfix u-section-1" id="sec-0e13">
  <div class="u-clearfix u-sheet u-sheet-1">
    <div class="u-form u-login-control u-form-1">
      <form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="u-clearfix u-form-custom-backend u-form-spacing-10 u-form-vertical u-inner-form" source="custom" name="form" style="padding: 10px;">
        <div class="u-form-group u-form-name">
          <label for="username-a30d" class="u-label" name="">Username *</label>
          <input type="text" placeholder="Enter your Username" id="username-a30d" name="username" class="u-input u-input-rectangle u-none u-input-1" required="">
        </div>
        <div class="u-form-group u-form-password">
          <label for="password-a30d" class="u-label" name="">Password *</label>
          <input type="password" placeholder="Enter your Password" id="password-a30d" name="password" class="u-input u-input-rectangle u-none u-input-2" required="">
        </div>
        <div class="u-form-checkbox u-form-group">
          <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?><input type="checkbox" id="checkbox-a30d" name="remember" value="On" class="u-field-input"><?php endif; ?>
          <label for="checkbox-a30d" class="u-block-816b-32 u-field-label" style="" name="">Remember me</label>
        </div>
        <div class="u-align-left u-form-group u-form-submit">
          <a href="#" class="u-btn u-btn-submit u-button-style u-btn-1">Login</a>
          <input type="submit" value="submit" class="u-form-control-hidden">
        </div>
        <input type="hidden" value="" name="recaptchaResponse">
      <?php echo $hiddenFields; ?></form>
    </div>
    <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>" class="u-border-1 u-border-active-palette-2-base u-border-hover-palette-1-base u-btn u-button-style u-login-control u-login-forgot-password u-none u-text-palette-1-base u-btn-2">Forgot password?</a>
    
                            <?php $usersConfig = JComponentHelper::getParams('com_users'); ?>
                            <?php if ($usersConfig->get('allowUserRegistration')) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>" class="u-border-1 u-border-active-palette-2-base u-border-hover-palette-1-base u-btn u-button-style u-login-control u-login-create-account u-none u-text-palette-1-base u-btn-3">Don't have an account?</a>
                            <?php endif; ?>
                        
  </div>
</section>
