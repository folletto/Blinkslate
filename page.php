<?php get_header(); ?>
	  
	  <a name="highlight"></a>
  	<?php
  	if (have_posts()) {
  		while (have_posts()) : the_post();
			  @include get_template_directory() . '/posts/page.php';
  		endwhile; ?>
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
    
    <?php //comments_template(); ?>
  	
  	<?php //get_sidebar(); ?>
<?php get_footer(); ?>