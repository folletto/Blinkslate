    <!-- \/ Default Post - List -->
    <div class="article">
      <div class="meta"><time class="time"><?php the_time("Y-m-d"); ?></time></div>
      <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
      <?php if ($post->post_excerpt) { ?>
      <p class="excerpt">
         <?php echo get_the_excerpt(); ?>
         <div class="post-call-to-action-excerpt"><a href="<?php the_permalink() ?>">&rarr; read all</a></div>
      </p>
      <?php } else { ?>
        <?php the_content(); ?>
        <div class="post-call-to-action-full"><a href="<?php the_permalink() ?>">&rarr; comment</a></div>
      <?php } ?>
      
      <!--
      <?php trackback_rdf(); ?>
      -->
    </div>
    <!-- /\ Default Post - List -->