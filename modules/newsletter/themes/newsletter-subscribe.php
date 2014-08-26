<?php

?>
<form method="post" action="" id="newsletter" >
    <fieldset>
    <p><?php _e( 'To receive information about events at the club Blue Note, please enter your e-mail address:', 'pwp'); ?></p>
        <div class="alert" style="display:none;"></div>
    <div class="form-main">
        <div class="input-group">
        <input name="email" type="text" class="form-control" placeholder="<?php _e('e-mail address', 'pwp'); ?>" />
        <?php if ( function_exists( 'pll_current_language' ) ){ ?>
        <input name="lang" type="hidden" value="<?php echo pll_current_language(); ?>"/>
            <input name="pll_load_front" type="hidden" value="true"/>
            <?php } ?>
        
        <span class="input-group-btn">
            <button id="submit" class="btn btn-primary" type="submit"><?php _e('Subscribe' ,'pwp'); ?></button>
        </span>
        </div>
            <?php if($newsletter_rules_link){ ?>
            <label class="checkbox"><?php ?>
        <input type="checkbox" value="1" name="rules"/><?php _e('I accept', 'pwp'); ?> <a id="<?php echo $newsletter_rules_id ?>" href="<?php echo $newsletter_rules_link; ?>" class="ax" ><?php _e('the rules of the newsletter', 'pwp'); ?></a>
            </label>
            <?php } ?>
    </div>
    </fieldset>
</form>    