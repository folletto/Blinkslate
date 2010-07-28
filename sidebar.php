  <!-- \/ Sidebar -->
  <div id="sidebar">
    <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('global')): ?>
    <div class="quote">
      <?php bloginfo('description'); ?>
    </div>
    <ul>
      <?php wp_list_pages('title_li='); ?>
    </ul>
    <?php endif; ?>
  </div>
  <!-- /\ Sidebar -->