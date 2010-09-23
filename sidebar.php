  <!-- \/ Sidebar -->
  <div id="sidebar">
    <div class="quote">
      <?php bloginfo('description'); ?>
    </div>
    <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('global')): ?>
    <ul>
      <?php wp_list_pages('title_li='); ?>
    </ul>
    <?php endif; ?>
  </div>
  <!-- /\ Sidebar -->