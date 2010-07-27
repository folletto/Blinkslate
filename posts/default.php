      <!-- \/ Default Post - Full -->
      <div class="article">
        <div class="meta"><time class="time"><?php the_time("Y-m-d"); ?></time> <a href="<?php the_permalink() ?>" title="Permalink">&infin;</a></div>
        <h2><?php the_title(); ?></h2>
        <?php if ($post->post_excerpt) { ?>
        <p class="excerpt">
           <?php echo get_the_excerpt(); ?>
        </p>
        <?php } ?>
        
        <?php the_content(); ?>
        
        <div class="fold">
      	  <?php edit_post_link('&epsilon;','<span class="editlink">','</span>'); ?>
        </div>
  
        <!--
        <?php trackback_rdf(); ?>
        -->
      </div>
      <!-- /\ Default Post - Full -->