<?php get_header(); ?>
	
  	<?php
  	if (have_posts()) {
  		while (have_posts()) : the_post();
			  @include wpp::get_category_matching_partial('posts', 'php');
  		endwhile; ?>
  		
  		<aside id="related">
  		  <div class="tray">
  		    <span class="black">Related <a href="<?php bloginfo('url'); ?>/category/type/article/">articles</a></span>
  		    <span class="categories">
  		      <a href="<?php bloginfo('url'); ?>/category/content/life/"<?php echo (in_category('life') ? ' class="lit"' : '') ?>>life</a> &middot;
  		      <a href="<?php bloginfo('url'); ?>/category/content/design/"<?php echo (in_category('design') ? ' class="lit"' : '') ?>>design</a> &middot;
  		      <a href="<?php bloginfo('url'); ?>/category/content/psy/"<?php echo (in_category('psy') ? ' class="lit"' : '') ?>>psychology</a> &middot;
  		      <a href="<?php bloginfo('url'); ?>/category/content/tech/"<?php echo (in_category('tech') ? ' class="lit"' : '') ?>>technology</a> &middot;
  		      <a href="<?php bloginfo('url'); ?>/category/content/simplicity/"<?php echo (in_category('simplicity') ? ' class="lit"' : '') ?>>simplicity</a> &middot;
  		      <a href="<?php bloginfo('url'); ?>/category/content/complexity/"<?php echo (in_category('complexity') ? ' class="lit"' : '') ?>>complexity</a>
  		    </span>
  		  </div>
    		<?php echo wpp::related_posts(
    		  3,
    		  'article', // category filter
    		  '<h3><a href="%post_permalink%" class="ico-article">%post_title%</a></h3>',
    		  null, // dateformat
    		  0, // post_id
    		  '<h3>Nothing yet.</h3>'
    		); ?>
  	  </aside>
  	  
  		<?php comments_template(); ?>
  		
  	<?php } else { ?>
    
    <!-- \/ Notice -->
		<div class="notice">
			<h2>Nothing.</h2>
			<p>
			  Sorry, but you are looking for something that isn't here.<br />
				Could I suggest a <a href="<?php bloginfo('url'); ?>">visit</a> to the homepage?
			</p>
		</div>
  	<!-- /\ Notice -->
  	
  	<?php } ?>
  	
  	<?php //get_sidebar(); ?>
<?php get_footer(); ?>