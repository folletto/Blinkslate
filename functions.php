<?php
require "lib/wordpress.portal.php";
include "lib/editor-prolayout/editor-prolayout.php";

/******************************************************************************************
 * Theme Functions
 * by Davide 'Folletto' Casali <folletto AT gmail DOT com>
 * 
 * Last revision: 2009 12 30
 *
 */
class theme {
  static $id = 'blankslatetheme';
  
  function count_posts($category, $year = null, $month = null) {
    /****************************************************************************************************
     * Get the number of posts from a category in a specific month.
     * Hint: get_the_time("m") or get_the_time("Y")
     * 
     * @param     category slug
     * @param     month number (2 digits) or year number (4 digits), null for no limit (all times)
     * @return    int
     */
  	global $wpdb;
  	
  	// ****** Cache
  	$cachekey = $category . intval($year) . intval($month);
    $count = wp_cache_get($cachekey, self::$id);
  	if ($count !== false) return $count;
    
    // ****** Init
    $year = intval($year);
    $month = intval($month);
    $AND_year = '';
    $AND_month = '';
    if ($month > 0 && $month < 13) $AND_month = 'AND MONTH(post_date) = ' . $month;
    if (sizeof($year) == 4) $AND_year = 'AND YEAR(post_date) = ' . $year;
    
    // ****** Query
    $query = "SELECT COUNT(*) AS num_posts
      FROM {$wpdb->posts} AS p
      INNER JOIN {$wpdb->term_relationships} AS tr ON tr.object_id = p.ID
      INNER JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
      INNER JOIN {$wpdb->terms} AS tm ON tm.term_id = tt.term_id
      WHERE
        post_type = 'post'
        AND post_status = 'publish'
        AND tm.name = '%s'
        {$AND_year}
        {$AND_month}
      ";
    $count = $wpdb->get_var($wpdb->prepare($query, $category));
    
    // ****** Cache
  	wp_cache_set($cachekey, $count, self::$id);
    
    // ****** Filter
    return $count;
  }
}

?>