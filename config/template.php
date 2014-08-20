<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
get_header();
?>
<h2>
szablon z mmodulu
</h2>
<div class="container-bg-wraper">
    <div class="container-bg">
	<div class="main">
	    <div class="row resources-content">
		<?php if(is_user_logged_in()){ ?>
		<div class="col-sm-12">
		    <?php
			while ( have_posts() ) : the_post();
			    get_template_part( 'content' );
			endwhile;
		    ?>
		</div>
		<?php } ?>
		<?php if(!is_user_logged_in()){ ?>
		<div class="box col-lg-3 col-md-4 col-sm-6">
		<?php } else{ ?>
		<div class="box col-lg-6 col-md-8 col-sm-6">
		    <?php }
		    if(function_exists('pwp_authenticate')){
			pwp_authenticate();
		    }
		    ?>
		</div>
		<?php if(!is_user_logged_in() ){ ?>
		<div class="box col-lg-3 col-md-4 col-sm-6">

		    <h2><?php _e('Register', 'pwp'); ?></h2>

		    <?php
			if (pll_current_language() == 'pl'){
		    form('rejestracja');
			}else{
			  form('registration');
			}
		    ?>

		</div>
		<?php } ?>
		</div>
	    </div>
	</div>
    </div>
<?php get_footer();
