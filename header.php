<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <title><?php wp_title('&bull;', true, 'right'); bloginfo('name');?></title>
  <meta name="description" content="<?php bloginfo('description'); ?>" />
  <meta name="theme author" content="Davide Casali - intenseminimalism.com" />
  
  <link rel="alternate" type="application/rss+xml" title="Content feed" href="<?php bloginfo('rss2_url'); ?>" />
  
  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
  
  <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/style.css" media="screen, projection" />
  <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/style.print.css" type="text/css" media="print" />
  <link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/gfx/favicon.ico" type="image/vnd.microsoft.icon" />
  
  <?php wp_head(); ?>
  
<?php @include get_template_directory()."/cfg/meta-header.php"; ?>
</head>
<body class="blinkslate<?php if (is_single()) echo ' is-single'; ?><?php if (is_page() && !is_page("archives")) echo ' is-page'; ?>">
  <!-- Accessibility > --><div id="skip"><a href="#highlight">Skip to content</a><a href="#nav" accesskey="n">Skip to navigation <small>(accesskey n)</small></a></div>
  
  <!-- \/ Header -->
  <div id="header">
    <div class="section">
      <h1><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
      <!--<h2><?php bloginfo('description'); ?></h2>-->
    </div>
  </div>
  <!-- /\ Header -->
  
  <div id="all">
  <!-- \/ Content -->
  <div id="content">