      <!-- \/ Default Page - Full -->
      <article class="article">
        <h1><?php the_title(); ?></h1>
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
      </article>
      <!-- /\ Default Page - Full -->