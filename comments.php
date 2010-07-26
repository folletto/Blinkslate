<?php /* File Protection */ if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) die(''); ?>
<?php
/****************************************************************************************************
 * Post Protection: check if the post is password protected.
 */
if (!empty($post->post_password)) {
	if ($post->post_password != $_COOKIE['wp-postpass_' . COOKIEHASH]) {
		echo '<p class="nocomments">' . _e("This post is password protected. Enter the password to view comments.") . '<p>';
		return;
	}
}


/****************************************************************************************************
 * Comments list
 */
if ($comments): ?>
    <div id="comments">
      <!-- \/ Comments -->
	    <?php foreach ($comments as $comment) include get_template_directory() . "/comment.php"; ?>
	    <!-- /\ Comments -->
    </div>
<?php
endif;


/****************************************************************************************************
 * Comment form
 */
if ($post->comment_status == "open") : ?>
    <section id="comment-form">
      <!-- \/ Comment Form -->	    
	    <?php /********************************************************************** REG? */
		  if (get_option('comment_registration') && !$user_ID): ?>
	      <p class="notice">Sorry, but you must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">logged in</a> to add a comment.</p>
	    <?php /********************************************************************** FORM */
		  else : ?>
		  	<h2>Do you have something to say? Please add it here:</h2>
	      <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">
		      <fieldset>
			      <p>
				      <textarea name="comment" id="comment" cols="60" rows="16" tabindex="1"></textarea>
				    </p>
			      <?php if ($user_ID): ?>
			      <p class="signature">
			        <em>&mdash;</em> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>
			      </p>
		        <?php else: ?>
            <p>
              <label for="author">Name</label>
            	<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" tabindex="2" /> <?php if (!$req) echo "<em>(optional)</em>"; ?>
            </p>
            <p>
              <label for="email">E-Mail</label>
            	<input type="email" name="email" id="email" value="<?php echo $comment_author_email; ?>" tabindex="3" /> <?php if (!$req) echo "<em>(optional)</em>"; ?>
            </p>
            <p>
              <label for="url">Website</label>
            	<input type="url" name="url" id="url" value="<?php echo $comment_author_url; ?>" tabindex="4" /> <small>(optional)</small>
            </p>
		        <?php endif; ?>
		        <p>
				      <input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
				      <input type="submit" name="submit" value="Add Comment" class="button" tabindex="5" />
			      </p>
		      </fieldset>
		      <?php do_action('comment_form', $post->ID); ?>
	      </form>
	      <!--<p class="allowedtags">Some HTML allowed: <?php echo allowed_tags(); ?></p>-->
	    <?php endif; /**************************************************************** END */ ?>
	    <!-- /\ Comment Form -->
    </section>
<?php endif; ?>