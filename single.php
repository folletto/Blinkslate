<?php get_header(); ?>
	
  	<?php
  	if (have_posts()) {
  		while (have_posts()) : the_post();
			  @include wpp::get_category_matching_partial('posts', 'php');
  		endwhile; ?>
  		
  		<?php $related = wpp::related_posts(
  		  3,
  		  'article', // category filter
  		  '<h4><a href="%post_permalink%">%post_title%</a></h4>',
  		  null, // dateformat
  		  0, // post_id
  		  'nil'
  		); 
  		if ($related != 'nil') { ?>
  		<div id="related">
  		  <div class="tray">
  		    <span class="black">Related posts</span>
  		  </div>
    		<?php echo $related; ?>
  	  </div>
	    <?php } ?>
  	  
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