<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     sh404sef
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     3.6.4.1481
 * @date		2012-11-01
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>

<div class="sh404sef-popup" id="sh404sef-popup">

<!-- markup common to all config layouts -->

<?php include JPATH_ADMINISTRATOR . '/components/com_sh404sef/views/config/tmpl/common_header.php'; ?>

<!-- start general configuration markup -->

<div id="element-box">
  <div class="t">
    <div class="t">
      <div class="t"></div>
    </div>
  </div>
<div class="m">

<form action="index.php" method="post" name="adminForm" id="adminForm">

  <div id="editcell">

  <!-- start of configuration html -->
  
  <?php
  
    echo JHtml::_('tabs.start', 'sh404SEFConf');
    echo JHtml::_('tabs.panel', 'Joomla', 'content');
    
  ?>
      
<table class="adminlist">

  <!-- shumisha 2007-06-30 new params for regular content  -->
  <?php

  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_USE_ALIAS'),
  JText::_('COM_SH404SEF_TT_USE_ALIAS'),
  $this->lists['usealias'] );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_USE_CAT_ALIAS'),
  JText::_('COM_SH404SEF_TT_USE_CAT_ALIAS'),
  $this->lists['useCatAlias'] );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_USE_MENU_ALIAS'),
  JText::_('COM_SH404SEF_TT_USE_MENU_ALIAS'),
  $this->lists['useMenuAlias'] );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INCLUDE_CONTENT_CAT'),
  JText::_('COM_SH404SEF_TT_INCLUDE_CONTENT_CAT'),
  $this->lists['includeContentCat'] );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INCLUDE_CONTENT_CAT_CATEGORIES'),
  JText::_('COM_SH404SEF_TT_INCLUDE_CONTENT_CAT_CATEGORIES'),
  $this->lists['includeContentCatCategories'] );
  
  $x++;
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_CATEGORIES_SUFFIX'),
  JText::_('COM_SH404SEF_TT_CATEGORIES_SUFFIX'),
            'contentCategoriesSuffix',
  $this->sefConfig->contentCategoriesSuffix, 30, 30 );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_SLUG_FOR_UNCATEGORIZED_ITEMS'),
  JText::_('COM_SH404SEF_TT_SLUG_FOR_UNCATEGORIZED_ITEMS'),
  $this->lists['slugForUncategorizedContent'] );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_CONTENT_TABLE_NAME'),
  JText::_('COM_SH404SEF_TT_INSERT_CONTENT_TABLE_NAME'),
  $this->lists['shInsertContentTableName'] );
  
  $x++;
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_CONTENT_TABLE_NAME'),
  JText::_('COM_SH404SEF_TT_CONTENT_TABLE_NAME'),
            'shContentTableName',
  $this->sefConfig->shContentTableName, 30, 30 );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_CONTENT_BLOG_NAME'),
  JText::_('COM_SH404SEF_TT_INSERT_CONTENT_BLOG_NAME'),
  $this->lists['shInsertContentBlogName'] );
  
  $x++;
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_CONTENT_BLOG_NAME'),
  JText::_('COM_SH404SEF_TT_CONTENT_BLOG_NAME'),
            'shContentBlogName',
  $this->sefConfig->shContentBlogName, 30, 30 );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_CONTENT_MULTIPAGES_TITLE'),
  JText::_('COM_SH404SEF_TT_INSERT_CONTENT_MULTIPAGES_TITLE'),
  $this->lists['shMultipagesTitle'] );

  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_ARTICLE_ID_TITLE'),
  JText::_('COM_SH404SEF_TT_INSERT_ARTICLE_ID_TITLE'),
  $this->lists['ContentTitleInsertArticleId'] );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_NUMERICAL_ID_CAT_LIST'),
  JText::_('COM_SH404SEF_TT_INSERT_NUMERICAL_ID_CAT_LIST'),
  $this->lists['shInsertContentArticleIdCatList'] );

  ?>
</table>
<table class="adminlist">  
  <!-- shumisha 2007-04-01 new params for Numerical Id insert  -->
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_INSERT_NUMERICAL_ID_TITLE'); ?></th>
    </tr>
  </thead>
  <?php
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_NUMERICAL_ID_TITLE'),
  JText::_('COM_SH404SEF_TT_INSERT_NUMERICAL_ID'),
  $this->lists['shInsertNumericalId'] );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_NUMERICAL_ID_CAT_LIST'),
  JText::_('COM_SH404SEF_TT_INSERT_NUMERICAL_ID_CAT_LIST'),
  $this->lists['shInsertNumericalIdCatList'] ); 
  
  ?>
  <!-- shumisha 2007-04-01 end of new params for Numerical Id insert  -->  

  </table><?php
  // end of params for regular content  -->
  
    echo JHtml::_('tabs.panel', 'Contacts', 'contact');

  // params for Com_contact  -->
  ?><table class="adminlist"><?php
    
  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_USE_CAT_ALIAS'),
  JText::_('COM_SH404SEF_TT_USE_CAT_ALIAS'),
  $this->lists['useContactCatAlias'] );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INCLUDE_CONTENT_CAT'),
  JText::_('COM_SH404SEF_TT_INCLUDE_CONTENT_CAT'),
  $this->lists['includeContactCat'] );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INCLUDE_CONTENT_CAT_CATEGORIES'),
  JText::_('COM_SH404SEF_TT_INCLUDE_CONTENT_CAT_CATEGORIES'),
  $this->lists['includeContactCatCategories'] );
  
  $x++;
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_CATEGORIES_SUFFIX'),
  JText::_('COM_SH404SEF_TT_CATEGORIES_SUFFIX'),
            'contactCategoriesSuffix',
  $this->sefConfig->contactCategoriesSuffix, 30, 30 );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_SLUG_FOR_UNCATEGORIZED_ITEMS'),
  JText::_('COM_SH404SEF_TT_SLUG_FOR_UNCATEGORIZED_ITEMS'),
  $this->lists['slugForUncategorizedContact'] );
  

  ?></table><?php
  // end of params for Contact  -->
  
    echo JHtml::_('tabs.panel', 'Weblinks', 'weblinks');

  // params for Com_weblinks  -->
  ?><table class="adminlist"><?php
    
  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_USE_CAT_ALIAS'),
  JText::_('COM_SH404SEF_TT_USE_CAT_ALIAS'),
  $this->lists['useWeblinksCatAlias'] );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INCLUDE_CONTENT_CAT'),
  JText::_('COM_SH404SEF_TT_INCLUDE_CONTENT_CAT'),
  $this->lists['includeWeblinksCat'] );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INCLUDE_CONTENT_CAT_CATEGORIES'),
  JText::_('COM_SH404SEF_TT_INCLUDE_CONTENT_CAT_CATEGORIES'),
  $this->lists['includeWeblinksCatCategories'] );
  
  $x++;
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_CATEGORIES_SUFFIX'),
  JText::_('COM_SH404SEF_TT_CATEGORIES_SUFFIX'),
            'weblinksCategoriesSuffix',
  $this->sefConfig->weblinksCategoriesSuffix, 30, 30 );
  
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_SLUG_FOR_UNCATEGORIZED_ITEMS'),
  JText::_('COM_SH404SEF_TT_SLUG_FOR_UNCATEGORIZED_ITEMS'),
  $this->lists['slugForUncategorizedWeblinks'] );
  

  ?></table><?php
  // end of params for Contact  -->
  
    echo JHtml::_('tabs.panel', 'Virtuemart', 'virtuemart');
    
  // params for Virtuemart  -->
  ?><table class="adminlist"><?php

  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_VM_INSERT_SHOP_NAME'),
  JText::_('COM_SH404SEF_TT_VM_INSERT_SHOP_NAME') . JText::_('COM_SH404SEF_TT_NAME_BY_COMP') . JText::_('COM_SH404SEF_TT_VIRTUEMART_NOTE'),
  $this->lists['shVmInsertShopName'] );
  $x++;
  
  ?></table><?php
  // end of params for Virtuemart  -->
  
    echo JHtml::_('tabs.panel', 'Community Builder', 'cb');
    
  // params for Community Builder  -->
  ?><table class="adminlist"><?php

  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_CB_INSERT_NAME'),
  JText::_('COM_SH404SEF_TT_CB_INSERT_NAME') . JText::_('COM_SH404SEF_TT_NAME_BY_COMP'),
  $this->lists['shInsertCBName'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_CB_INSERT_USER_NAME'),
  JText::_('COM_SH404SEF_TT_CB_INSERT_USER_NAME'),
  $this->lists['shCBInsertUserName'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_CB_INSERT_USER_ID'),
  JText::_('COM_SH404SEF_TT_CB_INSERT_USER_ID'),
  $this->lists['shCBInsertUserId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_CB_USE_USER_PSEUDO'),
  JText::_('COM_SH404SEF_TT_CB_USE_USER_PSEUDO'),
  $this->lists['shCBUseUserPseudo'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_CB_SHORT_USER_URL'),
  JText::_('COM_SH404SEF_TT_CB_SHORT_USER_URL'),
  $this->lists['shCBShortUserURL'] );
  
  ?></table><?php
  // end of params for Community Builder  -->
  
    echo JHtml::_('tabs.panel', 'Jomsocial', 'jomsocial');
    
  // params for JomSocial -->
  ?><table class="adminlist"><?php
  
  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_NAME'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_NAME') . JText::_('COM_SH404SEF_TT_NAME_BY_COMP'),
  $this->lists['shJSInsertJSName'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_CB_SHORT_USER_URL'),
  JText::_('COM_SH404SEF_TT_CB_SHORT_USER_URL'),
  $this->lists['shJSShortURLToUserProfile'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_USER_NAME'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_USER_NAME'),
  $this->lists['shJSInsertUsername'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_USER_FULL_NAME'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_USER_FULL_NAME'),
  $this->lists['shJSInsertUserFullName'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_CB_INSERT_USER_ID'),
  JText::_('COM_SH404SEF_TT_CB_INSERT_USER_ID'),
  $this->lists['shJSInsertUserId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_GROUP_CATEGORY'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_GROUP_CATEGORY'),
  $this->lists['shJSInsertGroupCategory'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_GROUP_CATEGORY_ID'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_GROUP_CATEGORY_ID'),
  $this->lists['shJSInsertGroupCategoryId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_GROUP_ID'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_GROUP_ID'),
  $this->lists['shJSInsertGroupId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_GROUP_BULLETIN_ID'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_GROUP_BULLETIN_ID'),
  $this->lists['shJSInsertGroupBulletinId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_DISCUSSION_ID'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_DISCUSSION_ID'),
  $this->lists['shJSInsertDiscussionId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_MESSAGE_ID'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_MESSAGE_ID'),
  $this->lists['shJSInsertMessageId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_PHOTO_ALBUM'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_PHOTO_ALBUM'),
  $this->lists['shJSInsertPhotoAlbum'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_PHOTO_ALBUM_ID'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_PHOTO_ALBUM_ID'),
  $this->lists['shJSInsertPhotoAlbumId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_PHOTO_ID'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_PHOTO_ID'),
  $this->lists['shJSInsertPhotoId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_VIDEO_CAT'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_VIDEO_CAT'),
  $this->lists['shJSInsertVideoCat'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_VIDEO_CAT_ID'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_VIDEO_CAT_ID'),
  $this->lists['shJSInsertVideoCatId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_JS_INSERT_VIDEO_ID'),
  JText::_('COM_SH404SEF_TT_JS_INSERT_VIDEO_ID'),
  $this->lists['shJSInsertVideoId'] );
 
  ?></table><?php
  // end of params for Jomsocial  -->
  
    echo JHtml::_('tabs.panel', 'Kunena', 'kunena');
    
  // params for Kunena -->
  ?><table class="adminlist"><?php
  
  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_FB_INSERT_NAME'),
  JText::_('COM_SH404SEF_TT_FB_INSERT_NAME') . JText::_('COM_SH404SEF_TT_NAME_BY_COMP'),
  $this->lists['shInsertFireboardName'] );


  ?></table><?php
  // end of params for kunena  -->
  
    echo JHtml::_('tabs.panel', 'MyBlog', 'MyBlog');
    
  // params for MyBlog -->
  ?><table class="adminlist"><?php
  
  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_MYBLOG_INSERT_NAME'),
  JText::_('COM_SH404SEF_TT_MYBLOG_INSERT_NAME') . JText::_('COM_SH404SEF_TT_NAME_BY_COMP'),
  $this->lists['shInsertMyBlogName'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_MYBLOG_INSERT_POST_ID'),
  JText::_('COM_SH404SEF_TT_MYBLOG_INSERT_POST_ID'),
  $this->lists['shMyBlogInsertPostId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_MYBLOG_INSERT_TAG_ID'),
  JText::_('COM_SH404SEF_TT_MYBLOG_INSERT_TAG_ID'),
  $this->lists['shMyBlogInsertTagId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_MYBLOG_INSERT_BLOGGER_ID'),
  JText::_('COM_SH404SEF_TT_MYBLOG_INSERT_BLOGGER_ID'),
  $this->lists['shMyBlogInsertBloggerId'] );

  ?></table><?php
  // end of params for MyBlog  -->
  
    echo JHtml::_('tabs.panel', 'Mosets tree', 'Mtree');
    
  // params for Mosets Tree -->
  ?><table class="adminlist"><?php

  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_MTREE_INSERT_NAME'),
  JText::_('COM_SH404SEF_TT_MTREE_INSERT_NAME') . JText::_('COM_SH404SEF_TT_NAME_BY_COMP'),
  $this->lists['shInsertMTreeName'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_MTREE_INSERT_LISTING_ID'),
  JText::_('COM_SH404SEF_TT_MTREE_INSERT_LISTING_ID'),
  $this->lists['shMTreeInsertListingId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_MTREE_PREPEND_LISTING_ID'),
  JText::_('COM_SH404SEF_TT_MTREE_PREPEND_LISTING_ID'),
  $this->lists['shMTreePrependListingId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_MTREE_INSERT_LISTING_NAME'),
  JText::_('COM_SH404SEF_TT_MTREE_INSERT_LISTING_NAME'),
  $this->lists['shMTreeInsertListingName'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_DOCMAN_INSERT_CAT_ID'),
  JText::_('COM_SH404SEF_TT_DOCMAN_INSERT_CAT_ID'),
  $this->lists['shMTreeInsertCategoryId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_DOCMAN_INSERT_CATEGORIES'),
  JText::_('COM_SH404SEF_TT_DOCMAN_INSERT_CATEGORIES'),
  $this->lists['shMTreeInsertCategories'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_CB_INSERT_USER_ID'),
  JText::_('COM_SH404SEF_TT_CB_INSERT_USER_ID'),
  $this->lists['shMTreeInsertUserId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_CB_INSERT_USER_NAME'),
  JText::_('COM_SH404SEF_TT_CB_INSERT_USER_NAME'),
  $this->lists['shMTreeInsertUserName'] );

  ?></table><?php
  // end of params for Mosets Tree  -->
  
    /*
    
    echo $pane->startPanel( 'iJoomla Mag', 'ijoomlamag' );
    
  // params for iJoomla magazine -->
  ?><table class="adminlist"><?php

  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_ACTIVATE_IJOOMLA_MAG'),
  JText::_('COM_SH404SEF_TT_ACTIVATE_IJOOMLA_MAG'),
  $this->lists['shActivateIJoomlaMagInContent'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_IJOOMLA_MAG_NAME'),
  JText::_('COM_SH404SEF_TT_INSERT_IJOOMLA_MAG_NAME') . JText::_('COM_SH404SEF_TT_NAME_BY_COMP'),
  $this->lists['shInsertIJoomlaMagName'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_IJOOMLA_MAG_MAGAZINE_ID'),
  JText::_('COM_SH404SEF_TT_INSERT_IJOOMLA_MAG_MAGAZINE_ID'),
  $this->lists['shInsertIJoomlaMagMagazineId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_IJOOMLA_MAG_ISSUE_ID'),
  JText::_('COM_SH404SEF_TT_INSERT_IJOOMLA_MAG_ISSUE_ID'),
  $this->lists['shInsertIJoomlaMagIssueId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_IJOOMLA_MAG_ARTICLE_ID'),
  JText::_('COM_SH404SEF_TT_INSERT_IJOOMLA_MAG_ARTICLE_ID'),
  $this->lists['shInsertIJoomlaMagArticleId'] ); 
  
  ?></table><?php
  // end of params for iJoomla magazine  -->
  
    echo $pane->endPanel(); 
    echo $pane->startPanel( 'iJoomla News', 'ijoomlanewsp' );
    
  // params for iJoomla NewsPortal -->
  ?><table class="adminlist"><?php
  
  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_IJOOMLA_NEWSP_NAME'),
  JText::_('COM_SH404SEF_TT_INSERT_IJOOMLA_NEWSP_NAME') . JText::_('COM_SH404SEF_TT_NAME_BY_COMP'),
  $this->lists['shInsertNewsPName'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_IJOOMLA_NEWSP_CAT_ID'),
  JText::_('COM_SH404SEF_TT_INSERT_IJOOMLA_NEWSP_CAT_ID'),
  $this->lists['shNewsPInsertCatId'] );
  $x++;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_INSERT_IJOOMLA_NEWSP_SECTION_ID'),
  JText::_('COM_SH404SEF_TT_INSERT_IJOOMLA_NEWSP_SECTION_ID'),
  $this->lists['shNewsPInsertSecId'] );

  ?></table><?php
  // end of params for iJoomla NewsPortal  -->
  
    echo $pane->endPanel();
    
    */
    
    echo JHtml::_('tabs.end'); 
  ?>
  
  <!-- end of configuration html -->

    <input type="hidden" name="c" value="config" />
    <input type="hidden" name="view" value="config" />
    <input type="hidden" name="layout" value="ext" />
    <input type="hidden" name="format" value="raw" />
    <input type="hidden" name="option" value="com_sh404sef" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="shajax" value="1" />
    <input type="hidden" name="tmpl" value="component" />
    
    <?php echo JHTML::_( 'form.token' ); ?>
  </div>  
</form>

  <div class="clr"></div>
</div>
  <div class="b">
    <div class="b">
      <div class="b"></div>
    </div>
  </div>
</div>

</div>