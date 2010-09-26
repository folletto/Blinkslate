<?php get_header(); ?>
	
	  <a name="highlight">
  	<?php if (have_posts()) { ?>
	  
	  <div class="list">
      <!-- \/ Listing -->
  	  <?php while (have_posts()) : the_post();
			  @include wpp::get_category_matching_partial('posts', 'list.php');
  		endwhile; ?>
      <!-- /\ Listing -->
    </div>
    
    <div class="paginator">
			<span class="next"><?php previous_posts_link('<span>&larr;</span> Newer Articles') ?></span>
			<span class="prev"><?php next_posts_link('Older Articles <span>&rarr;</span>') ?></span>
		</div>
    
	  <?php } else { ?>

    <!-- \/ Notice -->
		<div class="notice">
			<h2>Nothing found.</h2>
			<p>
			  It seems that there isn't nothing here.<br />
				Could I suggest a <a href="<?php bloginfo('url'); ?>">visit</a> to the homepage?
			</p>
		</div>
  	<!-- /\ Notice -->

  	<?php } ?>
  	
<?php get_footer(); ?>