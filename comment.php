<?php /* File Protection */ if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) die(''); ?>

<?php
/****************************************************************************************************
 * Prepare
 */
$author = get_comment_author_link();
$gravatar = '';
$type = get_comment_type('comment', 'trackback', 'pingback'); /* one of those three */
$commentalt = ($commentalt == " alt" ? "" : " alt"); /* is alternate? */
$moreclasses = ' ' . $type . $commentalt;
$moreclasses .= ($comment->comment_author_email == get_the_author_email() ? ' author' : ''); /* is author? */
//$commentcount = (isset($commentcount) ? $commentcount + 1 : 1);

// *** Gravatar
if (!function_exists("__custom_gravatar")) {
  function __custom_gravatar($email, $size = false, $default = false) {
  	$out = "http://www.gravatar.com/avatar.php?gravatar_id=" . md5($email);
  	if ($size) $out .= "&amp;size=" . $size;
  	if ($default) $out .= "&amp;default=" . urlencode($default);
  	return $out;
  }
  $gravatar = '<a href="http://www.gravatar.com" title="Add or change your avatar"><img src="' . __custom_gravatar($comment->comment_author_email, 50, get_bloginfo('template_url') . '/gfx/avatar-default.gif') . '" width="50" height="50" alt="" class="avatar" /></a>';
}

/****************************************************************************************************
 * Comment
 */
if ($comment->comment_type != 'pingback') { // Standard human comment
?>
    <div class="comment<?php echo $moreclasses; ?>" id="comment-<?php comment_ID(); ?>">
      <div class="meta"><time class="time"><a href="#comment-<?php comment_ID(); ?>"><?php comment_date("Y-m-d"); ?> <?php comment_time(); ?></a></time></div>
      <h2><?php comment_author_link(); ?>:</h2>
      <?php if ($comment->comment_approved == '0'): ?>
      <p class="notice">Your comment is awaiting moderation.</p>
      <?php endif; ?>
      <p class="content">
        <?php echo nl2br(get_comment_text()); ?>
      </p>
      <?php edit_comment_link('&epsilon;','<span class="editlink">','</span>'); ?>
    </div>
<?php } else { // Others: this will have in $moreclasses 'pingback' or else ?>
    <div class="comment pingback<?php echo $moreclasses; ?>" id="comment-<?php comment_ID(); ?>">
      <span class="pushout">&larr;</span>
      <div class="meta"><time class="time"><a href="#comment-<?php comment_ID(); ?>"><?php comment_date("Y-m-d"); ?> <?php comment_time(); ?></a></time></div>
      <h2><?php comment_author_link(); ?></h2>
      <p class="content">
        <?php echo nl2br(get_comment_text()); ?>
      </p>
      <?php edit_comment_link('&epsilon;','<span class="editlink">','</span>'); ?>
    </div>
<?php } ?>