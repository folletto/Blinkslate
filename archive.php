<?php get_header(); ?>
  	
  	<a name="highlight"></a>
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
			<span class="prev"><?php next_posts_link('<span>&rarr;</span> Older Articles') ?></span>
		</div>
    
  	<?php } else { ?>
    
    <!-- \/ Notice -->
		<div class="notice">
			<h2>Nothing found.</h2>
			<p>
			  The term you were looking for isn't written anywhere here.<br />
				Could I suggest a <a href="<?php bloginfo('url'); ?>">visit</a> to the homepage?
			</p>
		</div>
  	<!-- /\ Notice -->
    
  	<?php } ?>
  	
  	<?php //get_sidebar(); ?>
<?php get_footer(); ?>