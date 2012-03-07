<?php

/**
 * @file
 * Template file to show search result
 */
  print $opensearchserver_data['form'];
?>

<div id="results">
<table width="100%" border="0">
<tr>
<?php
  if ($opensearchserver_data['q']) {
    $oss_result = get_search_result($opensearchserver_data['result']);
    $oss_result_facet = get_search_result($opensearchserver_data['result_facet']);
    $search_query = $opensearchserver_data['q'];
    if ($oss_result->getResultFound() > 0 && $search_query != $opensearchserver_data['block_text']) {
      $result_time = get_result_time($opensearchserver_data['result']);
?>

<div align="left">
  <?php print check_plain(t($oss_result->getResultFound())); ?> documents found (<?php print check_plain(t($result_time));?> seconds)
</div>
  <?php  $max = get_max($oss_result);
}
?>

<?php
$check_filter_enabled = $opensearchserver_data['filter_enabled'] == 1 && $search_query != $opensearchserver_data['block_text'];
$check_filter_at_zero_result = $oss_result->getResultFound() <= 0 && $opensearchserver_data['no_filter'] == 1;
if ($check_filter_enabled && $check_filter_at_zero_result) {
  $filter_result = TRUE;
  }
elseif ($check_filter_enabled && $oss_result->getResultFound() > 0) {
  $filter_result = TRUE;
}
else {
  $filter_result = FALSE;
}
if ($filter_result) {
?>
<td width="25%">
  <div class="oss_facet">
  <div class="oss_facet_type"><?php print check_plain(t('Type'));?>
  <ul>
<?php
$check_facet_available = $opensearchserver_data['fq'] == NULL && $opensearchserver_data['tq'] == NULL && $opensearchserver_data['ts'] == NULL &&  $oss_result_facet >= 0;
$check_facet_available_atzero_result = $oss_result->getResultFound() <= 0 && $oss_result_facet <= 0;
if ($check_facet_available || $check_facet_available_atzero_result) {
  $facet_everything = TRUE;
}
else {
  $facet_everything = FALSE;
}
print generate_facet_everything($facet_everything, $search_query);
foreach ($oss_result_facet->getFacet('type') as $values) {
  $value = $values['name'];
  if ($value == $opensearchserver_data['fq']) { ?>
    <li><b><a href="/?q=opensearchserver/search/<?php print drupal_urlencode($search_query);?>/&fq=<?php print drupal_urlencode($value);?>"> <?php print drupal_ucfirst(check_plain($value)) . '(' .  $values . ')';?> </a></b></li>
<?php
}
else {
?>
<li><a href="/?q=opensearchserver/search/<?php print drupal_urlencode($search_query);?>/&fq=<?php print drupal_urlencode($value);?>"> <?php print drupal_ucfirst(check_plain($value)) . '(' .  $values . ')';?> </a></li>
<?php
  }
  }
?>
</ul>
</div>
<div class="oss_facet_categories"><br/>
  <?php print check_plain(t('Categories'));?>
<ul>
<li>
<?php
print generate_facet_everything($facet_everything, $search_query);
  if ($oss_result_facet->getFacet('taxonomy')) {
  foreach ($oss_result_facet->getFacet('taxonomy') as $taxonomys) {
  $taxonomy_name = $taxonomys['name'];
  if ($taxonomy_name == $opensearchserver_data['tq']) { ?>
    <li><b> <a href="/?q=opensearchserver/search/<?php print urlencode($search_query);?>/&tq=<?php print drupal_urlencode($taxonomy_name);?>"> <?php print drupal_ucfirst(check_plain($taxonomy_name)) . '(' .  $taxonomys . ')';?> </a></b></li>
<?php
  }
else {
?>
  <li><a href="/?q=opensearchserver/search/<?php print urlencode($search_query);?>/&tq=<?php print drupal_urlencode($taxonomy_name);?>"> <?php print drupal_ucfirst(check_plain($taxonomy_name)) . '(' .  $taxonomys . ')';?> </a></li>
<?php
  }
  }
}
?>
</ul>
</div>
<?php
  if ($opensearchserver_data['date_filter']) {
?>
<div class="oss_facet_categories"><br/>
<?php print check_plain(t('Date'));?>
<ul>
<?php
print generate_facet_everything($facet_everything, $search_query);
if ($oss_result_facet->getResultFound() > 0) {
  foreach ($opensearchserver_data['time_stamp'] as $timestamp) {
    if ($timestamp == $opensearchserver_data['ts']) { ?>
     <li><b><a href="/?q=opensearchserver/search/<?php print drupal_urlencode($search_query);?>/&ts=<?php print drupal_urlencode($timestamp);?>"> <?php print drupal_ucfirst(check_plain($timestamp));?> </a></b></li>
<?php
}
else {
?>
  <li><a href="/?q=opensearchserver/search/<?php print drupal_urlencode($search_query);?>/&ts=<?php print drupal_urlencode($timestamp);?>"> <?php print drupal_ucfirst(check_plain($timestamp));?> </a></li>
<?php
  }
  }
}
}
?>
</ul>
</div>
</div>
</td>
<?php
}
?>
<td width="80%">
  <?php if ($oss_result->getResultFound() <= 0 ||  $search_query == $opensearchserver_data['block_text']) {?>
  <div align="left" class="oss_error">
  <?php if ($search_query == $opensearchserver_data['block_text']) { ?>
  <p>To be processed a query can't be empty and should contains valid words.</p>
  <?php }?>
  <p> No documents containing all your search terms were found.</p>
  <p>Your Search Keyword <b><?php print check_plain(t($search_query)); ?></b> did not match any document</p><br/><p>Suggestions:</p>
  <p>- Make sure all words are spelled correctly.</p>
  <p>- Try different keywords.</p>
  <p>- Try more general keywords.</p>
  </div>
<?php
}
else {
for ($i = $oss_result->getResultStart(); $i < $max; $i++) {
  $category = stripslashes($oss_result->getField($i, 'type', TRUE));
  if ($category=="comments") {
  $subject = stripslashes($oss_result->getField($i, 'comments_subject', TRUE));
  $comment = stripslashes($oss_result->getField($i, 'comments_comment', TRUE));
  $user = stripslashes($oss_result->getField($i, 'user_name', TRUE));
  $user_url = stripslashes($oss_result->getField($i, 'user_url', TRUE));
  $type = stripslashes($oss_result->getField($i, 'type', TRUE));
  $url = stripslashes($oss_result->getField($i, 'url', TRUE));
?>
<div class="oss_result">
<div class="oss_result_field1"><a href="<?php print check_url($url);?>"><?php print $subject;?></a> </div>
<div class="oss_result_field2"><?php print $comment;?></div>
<?php
if ($opensearchserver_data['time_stamp'] == 1) {?>
  <div class="oss_result_field3"><a href="<?php print check_url($url);?>"><?php print $comment;?></a>&nbsp;&nbsp;
  <?php print $type;?> By <a href="<?php print $user_url;?>"><?php print $user;?></a>
  </div>
  <?php
}
else { ?>
  <div class="oss_result_field3"><a href="<?php print check_url($url)?>"><?php print check_url($url)?> </a></div><br/>
<?php
}
?>
</div>
<?php
}
else {
  $title = stripslashes($oss_result->getField($i, 'title', TRUE));
  $content = stripslashes($oss_result->getField($i, 'content', TRUE));
  $user = stripslashes($oss_result->getField($i, 'user_name', TRUE));
  $user_url = stripslashes($oss_result->getField($i, 'user_url', TRUE));
  $type = stripslashes($oss_result->getField($i, 'type', TRUE));
  $url = stripslashes($oss_result->getField($i, 'url', TRUE));
?>
<div class="oss_result">
<div class="oss_result_field1"><a href="<?php print check_url($url);?>"><?php print $title;?></a> </div>
<div class="oss_result_field2"><?php print $content;?></div>
<?php
  if ($opensearchserver_data['signature'] == 1) {
?>
<div class="oss_result_field3"><a href="<?php print check_url($url)?>"><?php print opensearchserver_create_url_snippet($url, $opensearchserver_data['url_snippet']);?> </a>&nbsp;&nbsp;
<?php
if ($user && $type) {
  print check_plain($type);?> By <a href="<?php print check_url($user_url);?>"><?php check_plain(print $user);?></a>
  <?php
}
?>
</div>
<?php
}
else {
?>
  <div class="oss_result_field3"><a href="<?php print check_url($url)?>"><?php print opensearchserver_create_url_snippet($url, $opensearchserver_data['url_snippet']);?> </a></div>
  <?php
}
}
?>
</div>
<?php
}
?>

<?php
foreach ($opensearchserver_data['paging'] as $page) {?>
  <span class="<?php print $page['style']; ?>"> <a href="<?php print $page['url'];?>"><?php print $page['label']; ?></a></span>&nbsp;&nbsp;
<?php
}
?>
</div>
<div align="right" class="oss_logo">
<img src="http://www.open-search-server.com/images/oss_logo_62x60.png" /><br/>
<a href="http://www.open-search-server.com/">Enterprise Search Made Yours</a>
</div>
<?php
}
?>
</td>
<?php
}
?>
</tr>
</table>
</div>