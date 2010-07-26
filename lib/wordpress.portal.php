<?php 
/*
Plugin Name: WordPress Portal
Plugin URI: http://digitalhymn.com/argilla/wpp
Description: This is a widget and library to ease themes development. It could be included in the theme or added as plugin. You can add an updated plugin to fix existing themes.
Author: Davide 'Folletto' Casali
Version: 10.1.10
Author URI: http://intenseminimalism.com/
 ******************************************************************************************
 * WordPress Portal
 * WP Theming Functions Library
 * 
 * Last revision: 2010 01 10
 *
 * by Davide 'Folletto' Casali <folletto AT gmail DOT com>
 * intenseminimalism.com
 * Copyright (C) 2006/2010 - GNU General Public License (GPL) 2.0
 * 
 * Based upon a library developed for key-one.it (Kallideas / Key-One)
 * Thanks to Roberto Ostinelli and Alessandro Morandi.
 *
 */

/*
 * SUMMARY:
 *  wpp::foreach_post($filter, $limit): creates a custom TheLoop, with a filter match
 *  wpp::get_posts($filter, $limit): gets all the posts matching a filter
 *  wpp::foreach_attachment(): creates a custom TheLoop for the attachments, can be used inside TheLoop
 *  wpp::get_attachments($filter, $limit): gets all the posts matching a filter
 *  wpp::get_post_custom($custom, $before, $after, $optid): [TheLoop] gets the specified custom
 *  wpp::uri_category($field, $default): gets the category of the loaded page
 *  wpp::in_category($nicename): [TheLoop] checks if the posts belongs to that category
 *  wpp::is_term_child_of($child, $parent): checks if the category is child of another (nicename)
 *  wpp::related_posts(); gets the related posts using tag matching count (has format options)
 *  wpp::get_page_content($nicename, $on_empty): gets the specified page content
 *  wpp::get_zone(): gets an array containing ['type' => '...', 'id' => 'n', 'terms' => array(...)]
 *  wpp::is_admin($userid): check if the current logged user is an "administrator"
 *  wpp::get_last_comments($size): gets all the last comments
 *  wpp::get_last_comments_grouped($size): gets the last comments, one comment per post
 *  wpp::get_pages_root(): gets the root page of the current page subtree
 *  wpp::list_pages_of_section(): like wp_list_pages() but getting only the pages of the section
 *  wpp::add_virtual_page($url, $handlersarray): add a custom virtual page to be called when $url is called
 *  wpp::get_category_matching_partial($folder): gets the path of a file matching the category slug from a folder
 * 
 * DETAILS:
 * The most interesting function is the wpp_foreach_post() that in fact creates a custom
 * "The Loop", (like WP_Query, but simpler) using the syntax:
 *          while($post = wpp::foreach_post(array(...), 10)) { ... }
 * 
 * The function wpp::get_zone() retrieves the correct term from the page currently loaded.
 * For every loaded page it tries to match a tag (from the 'category' taxonomy).
 * It is like an evolved $wp_query->get_queried_object_id().
 * This is *really* useful to create complex sites, using the page hierarchy as structure,
 * matching the page slug with a category slug, using the page content as the section body.
 * 
 */

if (!isset($WPP_VERSION) && !class_exists("wpp")) {
  $WPP_VERSION = 'WordPressPortal/10.1.10';
  
  class wpp {
    
    static $id = "wordpressportal";
    static $loops = array();
    static $loops_backups = array();
    static $virtual_page = array();
    static $local_url = null; // rewrites the current filesystem path to a web URL
    static $purl = null; // contains the unmatched parts array() after self::add_virtual_page() match
    
    function foreach_anything($loopname, $filter = array(), $limit = null) {
      /****************************************************************************************************
       * Internal function.
       * Please use the specific functions: foreach_post, foreach_attachment.
       * Creates a custom The Loop to access anything: posts, pages, revisions, attachments.
       * Syntax:
       *   while(self::foreach_anything('posts', array(...), 10) { ... }
       * 
       * @param     loop name
       * @param      query parameteres array
       * @return    item or false
       */
      global $wp_query;
      // TheLoop variables
      global $post;
      global $previousday;
      
      $out = false;
      
      if (self::is_foreach_init_season($loopname)) {
        // *** Backup
        self::$loops_backups[$loopname] = array('post' => $post, 'previousday' => $previousday);
        
        // *** Filter
        if ($limit != null) $filter['posts_per_page'] = intval($limit);
        
        // *** Make sure minimum defaults are used
        $defaults = array(
          'post_type' => 'any', // any | attachment | post | page
          'post_status' => 'publish', // any | published | draft
          'post_parent' => 0,
        );
        
        // *** Query
        $args = wp_parse_args($filter, $defaults);
        self::$loops[$loopname] = new WP_Query($args);
      }
      
      // ****** Elaborate the custom The WPP Loop
      if (self::$loops[$loopname]->have_posts()) {
        // *** Next
        self::$loops[$loopname]->the_post();
        $out = $post;
      } else {
        // *** Reset
        unset(self::$loops[$loopname]); // kill custom loop
        $out = $post = null;
    
        // *** Restore backup
        $post = self::$loops_backups[$loopname]['post'];
        setup_postdata($post); // WP hook
        $previousday = self::$loops_backups[$loopname]['previousday'];
      }

      return $out;
    }
    function is_foreach_init_season($loopname) {
      /****************************************************************************************************
       * Internal function.
       * Checks if the named loop is already present (and so, able to loop).
       * 
       * @param     internal loop name
       * @return    boolean
       */
      return (!isset(self::$loops[$loopname]) || self::$loops[$loopname] === null);
    }
    
    function foreach_post($filter = array(), $limit = null) {
      /****************************************************************************************************
       * Creates a custom The Loop (i.e. like: while (have_posts()) : the_post(); [...] endwhile;).
       * Syntax:
       *   while(self::foreach_post(array(...), 10)) { ... }
       * 
       * @param     query parameteres array
       * @param      limit number of items
       * @return    item or false
       */
      $loopname = 'posts';
      if (self::is_foreach_init_season($loopname)) {
        if (!is_array($filter)) $filter = array();
        
        if (isset($filter['category'])) {
          $filter['category_name'] = $filter['category'];
          unset($filter['category']); // kill shortcut
        }
        if (isset($filter['page'])) {
          $filter['post_name'] = $filter['page'];
          unset($filter['page']); // kill shortcut
        }
        
        if (!isset($filter['post_type'])) {
          $filter['post_type'] = 'post';
        }
      }
      
      return self::foreach_anything($loopname, $filter, $limit);
    }
    function get_posts($filter, $limit = null) {
      /****************************************************************************************************
       * Gets all the posts into an array. Wraps wpp_foreach_post().
       *
       * @param      filter string (SQL WHERE) or array (converted to SQL WHERE, AND of equals (==))
       * @param      limit string (i.e. 1 or 1,10)
       * @return    posts array
       */
      $posts = array();

      while ($post = self::foreach_post($filter, $limit)) {
        $posts[] = $post;
      }

      return $posts;
    }
    
    function foreach_attachment($filter = array(), $limit = -1) {
      /****************************************************************************************************
       * Creates a custom The Loop to list attachments of the current post (or the passed one)
       * Syntax:
       *   while(self::foreach_attachment(array(...), 10)) { ... }
       * 
       * @param     query parameteres array
       * @param      limit number of items
       * @return    item or false
       */
      global $post;
      
      $loopname = 'attachments';
      if (self::is_foreach_init_season($loopname)) {
        if (!is_array($filter)) $filter = array();
        
        if (!isset($filter['post_parent'])) $filter['post_parent'] = $post->ID;
        
        if (isset($filter['name'])) {
          $filter['s'] = $filter['name'];
          unset($filter['name']); // kill shortcut
        }
        if (isset($filter['s'])) $filter['exact'] = true;
        
        $filter['post_type'] = 'attachment';
        $filter['post_status'] = 'any';
        $filter['orderby'] = "menu_order ASC, title DESC";
      }
      
      return self::foreach_anything($loopname, $filter, $limit);
    }
    function get_attachments($filter = array(), $limit = -1) {
      /****************************************************************************************************
       * Gets all the posts into an array. Wraps wpp_foreach_post().
       *
       * @param      filter string (SQL WHERE) or array (converted to SQL WHERE, AND of equals (==))
       * @param      limit string (i.e. 1 or 1,10)
       * @return    posts array
       */
      $posts = array();

      while ($post = self::foreach_attachment($filter, $limit)) {
        $posts[] = $post;
      }

      return $posts;
    }
    
    function get_post_custom($custom, $before = '', $after = '', $optid = 0) {
      /****************************************************************************************************
       * Get a specific custom item, optionally wrapped between two text strings.
       * Works inside The Loop only. To be used used outside specify the optional id parameter.
       *
       * @param      custom field
       * @param      before html
       * @param      after html
       * @param      optional id (to fetch the custom of a different post)
       * @return    html output
       */
      global $id;

      $out = '';
      if ($id && !$optid) $optid = $id;

      $custom_fields = get_post_custom($optid);

      if (isset($custom_fields[$custom])) {
        $out = $before . $custom_fields[$custom][0] . $after;
      }

      return $out;
    }
    
    function get_terms_recursive($ref, $levels = -1, $taxonomy = 'category') {
      /****************************************************************************************************
       * Get the terms matching the nicename (slug) and all its CHILDREN in a flat array.
       *
       * @param      term nicename (slug)
       * @param      (optional) depth of recursion (defult: -1, ALL)
       * @param      (optional) taxonomy, defaults to 'category'
       * @return    array of raw term rows
       */
      global $wpdb;
    
      $out = array();
    
      if ($ref !== '') {
        // ****** Where
        if (strval($ref) === strval(intval($ref))) {
          $where = "AND tt.parent = '" . $ref . "'"; // *** INT, use id for PARENT
        } else {
          $where = "AND t.slug = '" . $ref . "'"; // *** STRING, use slug for TERM
        }
      
        // ****** Query
        $query = "
          SELECT *
          FROM " . $wpdb->term_taxonomy . " As tt
          INNER JOIN " . $wpdb->terms . " As t ON t.term_id = tt.term_id
          WHERE
            tt.taxonomy = '" . $taxonomy . "'
            " . $where . "
        ";
      
        // ****** Data
        if ($terms = $wpdb->get_results($query)) {
          foreach ($terms as $term) {
            $out[] = $term; // Push
          
            if ($levels != 0) {
              $levels--;
              $out = array_merge($out, self::get_terms_recursive($term->term_id, $levels, $taxonomy));
            }
          }
        }
      }
    
      return $out;
    }
    function in_category($nicename) {
      /****************************************************************************************************
       * Checks if the post in the_loop belongs to the specified category nicename.
       * Different from in_category(), that checks for the id, not for the nicename.
       *
       * @param    container category nicename (slug)
       * @param    (optional) optional parent nicename
       * @return  boolean
       */
      return self::is_term_child_of($nicename, get_the_category());
    }
    function is_term_child_of($child_term, $parent_term) {
      /****************************************************************************************************
       * Checks if a category is child of another. Counts also self as true.
       *
       * @param    child category
       * @param    parent category (or array)
       * @return  boolean true
       */
      if (is_array($parent_term)) {
        $terms = $parent_term;
      } else {
        $terms = self::get_terms_recursive(strval($parent_term));
      }

      foreach ($terms as $term) {
        if ($child_term == $term->slug) {
          return true;
        }
      }
  
      return false;
    }
    function related_posts($number = null, $category = null, $format = null, $dateformat = null, $post_id = 0, $noposts = null) {
      /****************************************************************************************************
       * Get the post related to the current-looping post's matching tags.
       * Simplified from Simple Tags plugin. A very good one.
       * 
       * @param     numer of related posts to extract (max)
       * @param     output format (has a default)
       * @param     date format (defaults to WP default)
       * @param     post_id (optional post id, by default it uses the current The Loop post)
       * @param     string displayed when no posts are retrieved
       * @return    html string
       */
      global $wpdb;
      if (!$number) $number = 3;
      if (!$format) $format = '<a href="%post_permalink%" title="%post_title%">%post_title%</a> <span class="date">%post_date%</span> <span class="comments">%post_comment% comments</span>';
      if (!$dateformat) $dateformat = get_option('date_format');
      if (!$noposts) $noposts = '<div class="related-posts">No related posts.</div>';

  		// ****** Get current post data
  		$post_id = intval($post_id);
  		if ($post_id == 0) {
  			global $post;
  			$post_id = (int)$post->ID;
  		}

  		// ****** Cache
  		$cachekey = 'relatedposts-' . $post_id . '-' . md5($number . $format . $dateformat . $noposts);
  		$results = wp_cache_get($cachekey, self::$id . "-relatedposts");

  		// ****** No cache, query
  		if ($results === false) {
  			// ****** Set variables
  			$tags = get_the_tags($post_id);
  			if ($tags == false || $post_id == 0) return $noposts; // --> EXIT

  			if ($category) $category = "AND tm.name = '{$category}'";

  			// Limit days - 86400 seconds = 1 day
  			//$limitdays_sql = 'AND posts.post_date > "' . date('Y-m-d H:i:s', time() - $limit_days * 86400). '"';

  			// ****** SQL Tags list
  			$taglist = array();
  			foreach ($tags as $tag) { $taglist[] = '"' . $tag->term_id . '"'; };
  			$taglist = join($taglist, ", ");

  			$results = $wpdb->get_results(
  			  "SELECT DISTINCT p.ID, p.post_title, p.post_excerpt, p.comment_count, p.post_date, COUNT(tr.object_id) AS rank
  				FROM {$wpdb->posts} AS p
  				INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id
  				INNER JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
  				INNER JOIN {$wpdb->terms} AS tm ON tm.term_id = tt.term_id
  				WHERE
  				  tt.taxonomy = 'post_tag'
  				  AND (tt.term_id IN ({$taglist}))
  				  AND p.post_status = 'publish'
  				  AND p.post_type = 'post'
  				  AND p.ID != {$post_id}
  				  AND p.post_date < '" . current_time('mysql') . "'
  				GROUP BY tr.object_id
  				ORDER BY rank DESC
  				LIMIT 0, {$number}
  			");

  			// ****** Cache
  			wp_cache_set($cachekey, $results, self::$id . "-relatedposts");
  		}
  		if (!$results) return $noposts; // --> EXIT

  		// ****** Format items
  		foreach ($results as $result) {
  		  $output[] = strtr($format, array(
          '%post_id%' => $result->ID,
          '%post_title%' => $result->post_title,
          '%post_date%' => mysql2date($dateformat, $result->post_date),
          '%post_permalink%' => get_permalink($result->ID),
          '%post_excerpt%' => $result->post_excerpt,
          '%post_comment%' => $result->comment_count
        ));
      }

      // ****** Output
      $out = '<ul class="related-posts">' . "\r\n";
      $out .= '<li>' . join("</li>\r\n\t\t<li>", $output) . '</li>' . "\r\n";
      $out .= '</ul>' . "\r\n";

      return $out;
    }
    
    function get_page_content($nicename, $on_empty = "The page '%s' is empty.") {
      /****************************************************************************************************
       * Returns the specified page, given a nicename.
       *
       * @param      page nicename
       * @param      (optional) message on non-existing page
       * @return    page content string
       */
      $out = '';
    
      $posts = self::get_posts(array('page' => $nicename));
      if ($posts[0]->post_content)
        $out = $posts[0]->post_content;
      else
        $out = sprintf($on_empty, $nicename);
    
      return $out;
    }

    function get_zone($key = null, $taxonomy = 'category') {
      /****************************************************************************************************
       * Return the type of the 'zone' where we are, the matching id reference and the associated terms
       * It's like an improved $wp_query->get_queried_object_id().
       *
       * - returned zones: page, post, author, search, category, date, tag, home
       * (matching is_page, is_single, is_author, is_search, is_category, is_date, is_tag, is_home)
       *
       * @param      (optional) array shortcut (i.e. wpp_get_zone('id'))
       * @return    array ['type' => '...', 'id' => 'n', 'terms' => array(...)]
       */
      global $wp_query;
      
      $out = array(
        'type' => 'none',
        'id' => 0,
        'terms' => array(),
        'taxonomy' => $taxonomy
        );
      
      global $__cache_wpp_get_zone; // Cache
      if (!is_array($__cache_wpp_get_zone) || $__cache_wpp_get_zone['taxonomy'] != $taxonomy) {
        if (is_page()) {
          // *** We're in a PAGE
          global $post;
          $out['type'] = 'page';
          $out['id'] = $post->ID;
          $out['terms'] = array(get_term_by('slug', $post->post_name, $taxonomy));
        } else if (is_single()) {
          // *** We're in a POST
          global $post;
          $out['type'] = 'page';
          $out['id'] = $post->ID;
          $out['terms'] = wp_get_object_terms($post->ID, $taxonomy);
        } else if (is_author()) {
          // *** We're in AUTHOR
          global $author;
          $out['type'] = 'author';
          $out['id'] = $author;
        } else if (is_search()) {
          // *** We're in a SEARCH
          global $s;
          $out['type'] = 'search';
          $out['id'] = $s;
          $out['terms'] = array(get_term_by('slug', $s, $taxonomy));
        } else if (is_category()) {
          // *** We're in a CATEGORY
          global $cat;
          $out['type'] = 'cat';
          $out['id'] = $cat;
          $out['terms'] = array(get_term($cat, $taxonomy));
        } else if (is_date()) {
          // *** We're in a DATE
          global $m, $year, $monthnum;
          $out['type'] = 'date';
          $out['id'] = ($m ? $m : $year . str_pad($monthnum, 2, '0', STR_PAD_LEFT));
        } else if (is_tag()) {
          // *** We're in a TAG
          global $tag, $tag_id;
          $out['type'] = 'tag';
          $out['id'] = $tag;
          $out['terms'] = array(get_term($tag_id, $taxonomy));
        } else if (is_home()) {
          // *** We're in HOME
          global $paged;
          $out['type'] = 'home';
          $out['id'] = (intval($paged) ? intval($paged) : 1);
        } else if (is_404()) {
          // *** We're in 404
          global $paged;
          $out['type'] = '404';
          $out['id'] = '';
        }
        
        // Cleanup
        if (isset($out['terms'][0]) && $out['terms'][0] == false) $out['terms'] = array();
        
        $__cache_wpp_get_zone = $out; // <-- Cache
      } else {
        $out = $__cache_wpp_get_zone; // --> Cache
      }
    
      // ****** Return
      if ($key === null) return $out;
      return $out[$key];
    }
    
    function is_admin($uid = 0) {
      /****************************************************************************************************
       * Checks if the specified user ID is an admin user
       *
       * @param    user id (0 for current logged user)
       * @return  boolean
       */
      global $wpdb, $current_user;
  
      $out = false;
  
      // ****** Get current logged user
      if ($uid == 0 || strtolower($uid) == "me") {
        if (isset($current_user) && isset($current_user->id) && $current_user->id > 0) {
          $uid = $current_user->id;
        }
      }
  
      // ****** Query check Admin
      $query = "
        SELECT count(*) As isAdmin
        FROM " . $wpdb->usermeta . " As um
        WHERE
          um.user_id = '" . $uid . "' AND
          um.meta_key = 'wp_capabilities' AND
          um.meta_value LIKE '%" . "\"administrator\"" . "%'
        LIMIT 1
        ";
  
      // ****** Retrieving capabilities count
      if ($users = $wpdb->get_results($query)) {
        // *** Exists
        if ($users[0]->isAdmin > 0) {
          $out = true;
        }
      }
  
      return $out;
    }

    function get_last_comments($size = 10, $id = 0) {
      /****************************************************************************************************
       * Get comments list array.
       *
       * @param    number of comments to retrieve
       * @param    optional post ID to relate comments
       * @return  array
       */
      global $wpdb;
      $out = array();
  
      $sqlPost = "";
      if ($id > 0) $sqlPost = "AND p.ID = '" . $id . "'";
  
      $comments = $wpdb->get_results("
        SELECT
          c.comment_ID, c.comment_author, c.comment_author_email,
          c.comment_date, c.comment_content, c.comment_post_ID,
          p.post_title, p.comment_count
        FROM " . $wpdb->comments . " as c
        INNER JOIN " . $wpdb->posts . " as p ON c.comment_post_ID = p.ID
        WHERE
          comment_approved = '1'
          " . $sqlPost . "
        ORDER BY comment_date_gmt DESC
        LIMIT 0," . $size);
  
      foreach ($comments as $comment) {
        $out[] = array(
          'id' => $comment->comment_ID,
          'author' => $comment->comment_author,
          'email' => $comment->comment_author_email,
          'md5' => md5($comment->comment_author_email),
          'date' => $comment->comment_date,
          'content' => $comment->comment_content,
          'post' => array(
            'id' => $comment->comment_post_ID,
            'title' => $comment->post_title,
            'comments' => $comment->comment_count
          )
        );
      }
  
      return $out;
    }
    function get_last_comments_grouped($size = 10) {
      /****************************************************************************************************
       * Get comments list array.
       * Requires MySQL 4.1+ (nested queries, but just two calls).
       *
       * @param    number of comments to retrieve
       * @return  array
       */
      global $wpdb;
      $out = array();
  
      $sqlPost = "";
      if ($id > 0) $sqlPost = "AND p.ID = '" . $id . "'";
  
      // ****** Get the ID of the Last Comment for Each Post (sorted by Comment Date DESC)
      $last = $wpdb->get_results("
        SELECT
          c.comment_ID, c.comment_post_ID
        FROM " . $wpdb->comments . " as c
        INNER JOIN
          (SELECT MAX(comment_ID) AS comment_ID FROM " . $wpdb->comments . " GROUP BY comment_post_ID) cg
          ON cg.comment_ID = c.comment_ID
        WHERE
          comment_approved = '1'
        ORDER BY comment_date_gmt DESC
        LIMIT 0," . $size);
  
      $where = '';
      foreach ($last as $comment) {
        if ($where) $where .= ' OR ';
        $where .= "comment_ID = '" . $comment->comment_ID . "'";
      }
      $where = '(' . $where . ')';
  
      // ****** Get the Last Comments details
      $comments = $wpdb->get_results("
        SELECT
          c.comment_ID, c.comment_author, c.comment_author_email,
          c.comment_date, c.comment_content, c.comment_post_ID,
          p.post_title, p.comment_count
        FROM " . $wpdb->comments . " as c
        INNER JOIN " . $wpdb->posts . " as p ON c.comment_post_ID = p.ID
        WHERE
          comment_approved = '1' AND
          " . $where . "
        ORDER BY comment_date_gmt DESC
        LIMIT 0," . $size);
  
      foreach ($comments as $comment) {
        $out[] = array(
          'id' => $comment->comment_ID,
          'author' => $comment->comment_author,
          'email' => $comment->comment_author_email,
          'md5' => md5($comment->comment_author_email),
          'date' => $comment->comment_date,
          'post' => array(
            'id' => $comment->comment_post_ID,
            'title' => $comment->post_title,
            'comments' => $comment->comment_count
          )
        );    
      }
  
      return $out;
    }
    
    function get_pages_root() {
      /****************************************************************************************************
       * Returns the page at the top of the current pages subtree.
       * Copyright (C) 2007 + GNU/GPL2 by Roberto Ostinelli [http://www.ostinelli.net]
       * Modified by Davide 'Folletto' Casali.
       * 
       * @return  returns array('root', 'levels'), where root is a partial page object.
       */
      global $wp_query, $wpdb, $post;
      
      $out = array(
        'page' => null,
        'levels' => 0
      );
      
      global $__cache_wpp_list_pages_of_section; // Cache
      if (!is_array($__cache_wpp_list_pages_of_section)) {
        // *** Get all the pages
        $query = "
          SELECT ID, post_parent, post_title, post_name, post_type
          FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish'
        ";
        if ($post->post_type == 'page' && $results = $wpdb->get_results($query)) {
          // *** Generate (key, value) pairs
          $pages = array();
          foreach ($results as $result) {
            $pages[$result->ID] = $result;
          }
          // *** Walk the "tree" up to root
          $root = $post;
          while($root->post_parent) {
            $root = $pages[$root->post_parent];
            $out['levels']++;
          }
      
          $out['page'] = $root;
        }
        
        $__cache_wpp_list_pages_of_section = $out; // <-- Cache
      } else {
        $out = $__cache_wpp_list_pages_of_section; // --> Cache
      }
      
      // ****** Closing
      return $out;
    }
    function list_pages_of_section($arguments = '&title_li=') {
      /****************************************************************************************************
       * Echoes (HTML) the pages under the same parent page.
       * Copyright (C) 2007 + GNU/GPL2 by Roberto Ostinelli [http://www.ostinelli.net]
       * Modified by Davide 'Folletto' Casali.
       * 
       * @param    (optional) formatting arguments for wp_list_pages()
       * @param    (optional) boolean false to disable echo and trigger return data behaviour
       */
      $root = self::get_pages_root();
      return wp_list_pages($arguments . "&child_of=" . $root['page']->ID);
    }
    
    function add_virtual_page($url, $handlers = array()) {
      /****************************************************************************************************
       * Dynamically inject URL handlers inside WP structure.
       * 
       * @param    virtual URL to be handled (i.e. 'path/to/handle')
       * @param    php pages to be called (i.e. array(get_template_directory() . "/virtual.php", dirname(__FILE__) . "/virtual.php"));
       */
      if (is_array($handlers) && sizeof($handlers)) {
        // ****** Prepare data
        $url = rtrim($url, "/");
        self::$virtual_page[$url] = array(
          'handlers' => $handlers,
        );
        
  			// ****** SmartAss Rewrite
  			// NOTE: This function will trigger ONLY if it matches.
  			$url_wanted = rtrim(dirname($_SERVER['PHP_SELF']), "/") . "/" . $url;
  			$url_requested = substr(trim($_SERVER['REQUEST_URI']), 0, strlen($url_wanted));
  			if ($url_wanted == $url_requested) {
  				// ****** Add Filter
  				$pages = "";
  				$fx = create_function('$handler', '
  				  global $wp_query;
  				  $out = $handler;
  				  
        		foreach (array("' . join($handlers, '", "') . '") as $custom_template) {
        			if (file_exists($custom_template)) {
        				$out = $custom_template; // load our custom template
        				$wp_query->is_404 = false; // not a 404 anymore
        				break;
        			}
        		}
            
        		return $out;
  				');
  				remove_filter('template_redirect', 'redirect_canonical'); // we matched, avoid redirect! (risky?)
  				add_filter('404_template', $fx, 11); // hack 404, priority def. 10, 11 to be the last executing
  				
          
  				// Partial URL
          // Contains the remainder of the URL, removing the real part and also the add_virtual_page part:
          // it's just the unmatched string.
          // i.e. "http://yoursite.example.org/virtual/path/purl/zone" with add_virtual_page("virtual/path", ...)
          //      self::purl() => array('purl', 'zone');
  				self::$purl = explode("/", substr(trim($_SERVER['REQUEST_URI']), strlen($url_wanted) + 1));
  			}
      }
    }
    function get_local_url() {
      /****************************************************************************************************
       * Get the URI path to the folder containing WPP.
       * WPP filesystem location: "/users/–you/htdocs/wp-content/plugins/PluginName/lib/wordpress.portal.php"
       * get_local_url() result: "http://yoursite.example.org/wp-content/plugins/PluginName/lib/"
       * 
       * @return  full URI folder string where WPP resides
       */
      if (self::$local_url == null) self::$local_url = get_bloginfo('url') . '/' . preg_replace('/.*(wp-content\/.*)/i', '\\1', dirname(__FILE__)) . '/';
      return self::$local_url;
    }
    
    function get_category_matching_partial($folder, $ext = "php", $parent = null) {
      /****************************************************************************************************
       * Check the folder passed as argument for files matching the category slug name and extension.
       * Optionally you can restrict the search for the first level of children inside a specific parent,
       * identified by its slug or numeric term_id.
       * 
       * @return  include-able path string
       */
      $out = "";
      $categories = get_the_category();
      if ($parent && !is_int($parent)) $parent = get_category_by_slug($parent)->term_id;
      
      foreach ($categories as $category) {
        $out = get_template_directory() . "/" . trim($folder, '/') . "/" . $category->slug  . "." . $ext;
        if (is_file($out)) break;
        $out = null;
      }
      if (!$out) $out = get_template_directory() . "/" . trim($folder, '/') . "/default." . $ext;
			
			return $out;
    }
  }
  
  
  
  // \/ WIDGET ZONE
  function wppwidget($args, $key) {
    /****************************************************************************************************
     * Display WPP foreach widget.
     */
    // ****** Unwrap parameters
  	extract($args);
  	$options = get_option('wppwidget');
  	if ($key > -1) $options = $options[$key['number']];
  	//echo str_replace("\n", "<br/>", str_replace(" ", "&nbsp;&nbsp;", print_r($options[$key['number']], true)));

    // ****** Prepare data
  	$title = empty($options['title']) ? __('News') : apply_filters('widget_title', $options['title']);
  	$category = empty($options['category']) ? '' : $options['category'];
  	$count = empty($options['count']) ? '3' : $options['count'];
  	$more = $options['more'] > 0 ? true : false;

    // ****** Write output
  	while (self::foreach_post(array('cat' => $category), $more)) {
  	  $out .= '<li>
  	  <a href="' . get_permalink() . '">' . get_the_title() . '</a>
  	  <div class="">' . get_the_excerpt() . '</div>
  	  </li>';
  	}
  	if ($more) $out .= '<small class="wpwidget-more"><a href="' . '?cat=' . $category . '">' . __("Read more...") . '</a></small>';

    // \/ WIDGET CODE --------------------------------------------------\/
  	if (!empty($out)) {
  ?>
  	<?php echo $before_widget; ?>
  		<?php echo $before_title . $title . $after_title; ?>
  		<ul>
  			<?php echo $out; ?>
  		</ul>
  	<?php echo $after_widget; ?>
  <?php
  	}
  	// /\ WIDGET CODE --------------------------------------------------/\
  }
  function wppwidget_control($args) {
    /****************************************************************************************************
     * Configure WPP foreach widget.
     */
    $class = 'wppwidget';
    $key = ($args['number'] == -1) ? '%i%' : $args['number']; // -1 = no widget activated, %i% wildcard
    $formclasskey = $class . '[' . $key . ']';
  	$options = $newoptions = get_option($class);

  	// ****** Handle submit
  	if (isset($_POST[$class])) {
  	  foreach ($_POST[$class] as $key => $formoptions) {
    		$newoptions[$key]['title'] = strip_tags(stripslashes($_POST[$class][$key]['title']));
    		$newoptions[$key]['category'] = stripslashes($_POST[$class][$key]['category']);
    		$newoptions[$key]['count'] = intval(stripslashes($_POST[$class][$key]['count']));
    		$newoptions[$key]['more'] = isset($_POST[$class][$key]['more']);
  	  }
  	}
  	if ($options != $newoptions) {
  	  // TODO: MUST add cleanup code for removed widgets (hopefully in future added by WP team)
  		$options = $newoptions;
  		update_option($class, $options);
  	}

  	// ****** Prepare current content for form output
  	$title = @attribute_escape($options[$key]['title']);
  	$category = @attribute_escape($options[$key]['category']);
  	$count = @intval(attribute_escape($options[$key]['count']));
  	$more = @(bool)intval($options[$key]['more']);
  ?>
  		<p>
  		  <label for="<? echo $formclasskey; ?>[title]"><?php _e('Title:'); ?>
  		    <input class="widefat" id="<? echo $formclasskey; ?>[title]" name="<? echo $formclasskey; ?>[title]" type="text" value="<?php echo $title; ?>" />
  		  </label>
  		</p>
  		<p>
  			<label for="<? echo $formclasskey; ?>[category]"><?php _e( 'Category:' ); ?><br/>
  			  <?php
  			  wp_dropdown_categories(array(
  			    'name' => $formclasskey . '[category]',
  			    'hierarchical' => true,
  			    'hide_empty' => false,
  			    'selected' => $category,

  			    /*'show_option_all' => '', 'show_option_none' => '', 'orderby' => 'ID', 
            'order' => 'ASC', 'show_last_update' => 0, 'show_count' => 0, 'hide_empty' => 1, 
            'child_of' => 0, 'exclude' => '', 'echo' => 1, 'selected' => 0, 'hierarchical' => 0, 
            'name' => 'cat', 'class' => 'postform', 'depth' => 0*/
  			  ));
  			  ?>
  			</label>
  		</p>
  		<p>
  			<label for="<? echo $formclasskey; ?>[count]">
  		    <input style="width: 25px; text-align: center;" id="<? echo $formclasskey; ?>[count]" name="<? echo $formclasskey; ?>[count]" type="text" value="<?php echo $count; ?>" />
  		    <?php _e('post(s) displayed'); ?>
  		  </label>
  		</p>
  		<p>
  		  <label for="<? echo $formclasskey; ?>[more]">
  				<input type="checkbox" class="checkbox" id="<? echo $formclasskey; ?>[more]" name="<? echo $formclasskey; ?>[more]"<?php checked($more, true); ?> />
  				<?php _e('Show "more" button'); ?>
  			</label>
  		</p>
  		<input type="hidden" id="<? echo $formclasskey; ?>[submit]" name="<? echo $formclasskey; ?>[submit]" value="1" />
  <?php
  }

  add_action('init', 'wordpressportal_multiregister');
  function wordpressportal_multiregister() {
    /****************************************************************************************************
     * Register multiple widgets.
     *
     * You only need to configure the few parameters below.
     */
    $name = 'WordPress Portal';
    $description = 'Show any content from the specified category.';
    $class = 'wppwidget'; // class id, widget fx and with "_control" appended as widget Admin fx.

    // ****** Init
  	$prefix = sanitize_title($name); // $id prefix
  	$widget_ops = array(
  	  'classname' => $class,
  	  'description' => __($description),
  	);
  	$control_ops = array(
  	  //'width' => 200,
  	  //'height' => 200,
  	  'id_base' => $prefix,
  	);

  	// ****** Get Options
  	$options = get_option($class);
  	//if (isset($options[0])) unset($options[0]); // uh? why?
  	if (empty($options)) $options = array(array()); // Not initialized yet, create empty default.

  	// ****** Initialize widgets
  	foreach($options as $key => $content) {
  		wp_register_sidebar_widget($prefix . '-' . $key, __($name), $class, $widget_ops, array('number' => $key));
  		wp_register_widget_control($prefix . '-' . $key, __($name), $class . '_control', $control_ops, array('number' => $key));
  	}
  }
}


?>