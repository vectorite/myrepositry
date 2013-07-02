<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * JomGenius classes
 * 
 * @package		JomGenius
 * @version		6
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2010-2011 Brandon IT Consulting. All rights reserved.
 * @changelog	v6 added articlenew pagetype
 */

class JomGeniusClassContent extends JomGeniusParent {
	
	var $Itemid;
	var $view;
	var $option;
	var $category_id;
	var $section_id;
	var $layout;
	var $task;
	var $id;
	
	function __construct() {
		$this->Itemid		= JRequest::getVar('Itemid');
		$this->view			= JRequest::getWord('view');
		$this->option		= JRequest::getVar('option');
		$this->category_id	= JRequest::getVar('category_id');
		$this->section_id	= JRequest::getVar('section_id');
		$this->layout		= JRequest::getVar('layout');
		$this->task			= JRequest::getVar('task');
		$this->id			= JRequest::getVar('id');
	}
	
	function shouldInstantiate() {
		return true;
		// we allow instantiation even if we are not viewing a com_content page,
		// as there are some things we can query without being on that page.
		//( $this->option == 'com_content' );
	}

	
	/* particular methods for this component */
	
	/**
	 * A generic function that knows how to get lots of different info about the current article, category or section.
	 */
	function info( $type ) {
		
		// some special handling, that need not hit the database
		switch( $type ) {
			case 'categoryid':
			case 'category_id': return $this->categoryId();
			case 'pagetype':
			case 'page_type'  : return $this->pageType();
			case 'page_number':
			case 'pagenumber' : return $this->pageNumber(); // pagination control, 0 is 1st page
			default:
		}
		
		if ( $this->option != 'com_content' ) return null;
		// from here on, only serves info relating to com_content pages that we are on now.

		// everything else requires the database
		if ( $this->view == 'article' ) {
			$row = $this->_infoForArticleId( $this->id );
		} else if ( $this->view == 'category' or $this->view == 'categories') {
			$row = $this->_infoForCategoryId( $this->id );
		}
		
		switch( $type ) {
			case 'article_id':
			case 'article_title':
			case 'article_alias':
			case 'article_hits':
			case 'article_version':
			case 'article_created_by':
			case 'article_modified_by':
			case 'article_introtext':
			case 'article_fulltext':

			case 'article_created':
			case 'article_modified':
			case 'article_publishup':
			case 'article_publishdown':
			case 'article_metakeywords':
			case 'article_metadescription':
			case 'article_created_age':
			case 'article_modified_age':
			case 'article_featured':
			case 'article_language':

			case 'category_id':
			case 'category_title':
			case 'category_alias':
			case 'category_description':
			case 'category_language':
			case 'category_note':
			case 'ancestor_category_titles':
			case 'ancestor_category_ids':
			
				return @$row->$type;
			default:
		}
		// are there some more things that we might need to calculate?
		return null;
	}
	
	/**
	 * pageNumber() gives pagination information. 0 = 1st page of pagination; index increases from there.
	 */
	function pageNumber() {
		if ( $this->option != 'com_content' ) return null;
		if ( $this->view == 'article' ) {
			return JRequest::getInt( 'limitstart', 0 );
		}
		$limitstart = JRequest::getInt( 'limitstart', 0 );
		$limit = JRequest::getInt( 'limit', 0 ); // provided by Joomla, not in the URL itself. # items per page.
		if ( $limit == 0 ) return 0; // we're not on a paginated page?
		return (int)( $limit / $limitstart );
	}
	
	
	function pageType() {
		if ( $this->option != 'com_content' ) return null;
		
		switch ( $this->view ) {
			case 'form':
				if ($this->layout == 'edit' and JRequest::getVar('a_id') != null ) return 'articleedit';
				return 'articlenew';
			case 'featured':
				return 'featured';
			case 'article':
				if ($this->task == 'article.save') return 'articlesave';
				return 'article';
			case 'category':
				if ($this->layout == 'blog' ) return 'categoryblog';
				return 'categorylist';
			case 'categories':
				return 'categories';
			case 'archive': // ??? untested
				return 'archive';
		}
		return '';
	}
		
	/* returns the category id of the list, or the item being displayed.
	 * If the list: this is taken from the URL
	 * If the item: if a category id was in the URL then this is used. Otherwise,
	 *  the category of the item is used.
	 */
	function categoryId() {
		if ( $this->option != 'com_content' ) return null;
		
		$category_id = null;
		
		if ( $this->view == "category" or $this->view == "categories" ) {
			/* category list pages (blog or list style) */
			$category_id = (int)$this->id;
		} else if (array_key_exists("catid",$_REQUEST)) {
			/* if the category id is in the URL */
			$category_id = (int)JRequest::getInt("catid",0);
		}
		if ( $category_id === null && $this->view == "article" ) {
			/* if it's an article page without the catid mentioned in the url */
			$row = $this->_infoForArticleId( $this->id );
			$category_id = (int)@$row->category_id;
		}
		return $category_id;
	}
	
	
	function _infoForArticleId( $id ) {
		static $rows = array();
		
		if ( !array_key_exists( $id, $rows ) ) {

			$db			= JFactory::getDbo();
			$query		= $db->getQuery(true); 

			$nullDate	= $db->Quote( $db->getNullDate() );
			$my_id		= $db->Quote( $db->getEscaped( (int)$id ) );
			$jnow		=& JFactory::getDate();
			$now		= $db->Quote( $db->getEscaped( $jnow->toMySQL() ) );			
			
			$query->select("DISTINCT

				a.id as article_id,
				a.title as article_title,
				a.alias as article_alias,
				a.language as article_language,
				a.featured as article_featured,
				a.hits as article_hits,
				a.version as article_version,
				a.created_by as article_created_by,
				a.modified_by as article_modified_by,
				a.introtext as article_introtext,
				a.fulltext as article_fulltext,
				a.created as article_created,
				a.modified as article_modified,
				a.publish_up as article_publishup,
				a.publish_down as article_publishdown,
				a.metakey as article_metakeywords,
				a.metadesc as article_metadescription,
				floor(time_to_sec(timediff(now(),a.created))/60) as article_created_age,
				floor(time_to_sec(timediff(now(),a.modified))/60) as article_modified_age,
			
				a.catid as category_id,
				cat1.title as category_title,
				cat1.alias as category_alias,
				cat1.language as category_language,
				cat1.description as category_description,
				cat1.note as category_note,
			
				cat1.title as ct1,
				cat1.id as ci1,
				cat2.title as ct2,
				cat2.id as ci2,
				cat3.title as ct3,
				cat3.id as ci3,
				cat4.title as ct4,
				cat4.id as ci4,
				cat5.title as ct5,
				cat5.id as ci5,
				cat6.title as ct6,
				cat6.id as ci6,
				cat7.title as ct7,
				cat7.id as ci7,
				cat8.title as ct8,
				cat8.id as ci8
				");
			$query->from("`#__content` a");
			$query->leftJoin( "`#__categories` cat1 ON a.catid = cat1.id ")
				->leftJoin( "`#__categories` cat2 ON cat1.parent_id = cat2.id ")
				->leftJoin( "`#__categories` cat3 ON cat2.parent_id = cat3.id ")
				->leftJoin( "`#__categories` cat4 ON cat3.parent_id = cat4.id ")
				->leftJoin( "`#__categories` cat5 ON cat4.parent_id = cat5.id ")
				->leftJoin( "`#__categories` cat6 ON cat5.parent_id = cat6.id ")
				->leftJoin( "`#__categories` cat7 ON cat6.parent_id = cat7.id ")
				->leftJoin( "`#__categories` cat8 ON cat7.parent_id = cat8.id ")
				;
				
			$query->where( "a.id = $my_id" )
				->where( "a.state = 1" )
			    ->where( "( a.publish_up = $nullDate OR a.publish_up <= $now )" )
		    	->where( "( a.publish_down = $nullDate OR a.publish_down >= $now )" );
			
			$db->setQuery( $query, 0, 1 );
			$row		= $db->loadObject();
			$cat_titles = array();
			$cat_ids	= array();
			for($i = 1; $i <= 8; $i++ ) {
				$ct = "ct$i";
				$ci = "ci$i";
				if ( $row->$ct != '' and $row->$ci != 1) $cat_titles[] = $row->$ct;
				if ( $row->$ci != '' and $row->$ci != 1) $cat_ids[] = $row->$ci;
			}
			$row->ancestor_category_titles = $cat_titles;
			$row->ancestor_category_ids = $cat_ids;
			$rows[$id]	= $row;
		}
		return @$rows[$id];
	}

	function _infoForCategoryId( $id ) {
		static $rows = array();
		
		if ( !array_key_exists( $id, $rows ) ) {
			$db			= JFactory::getDbo();
			$query		= $db->getQuery(true); 

			$nullDate	= $db->Quote( $db->getNullDate() );
			$my_id		= $db->Quote( $db->getEscaped( (int)$id ) );			
			
			$query->select("DISTINCT
				cat1.id as category_id,
				cat1.title as category_title,
				cat1.alias as category_alias,
				cat1.language as category_language,
				cat1.description as category_description,
				cat1.note as category_note,
			
				cat1.title as ct1,
				cat1.id as ci1,
				cat2.title as ct2,
				cat2.id as ci2,
				cat3.title as ct3,
				cat3.id as ci3,
				cat4.title as ct4,
				cat4.id as ci4,
				cat5.title as ct5,
				cat5.id as ci5,
				cat6.title as ct6,
				cat6.id as ci6,
				cat7.title as ct7,
				cat7.id as ci7,
				cat8.title as ct8,
				cat8.id as ci8
				");

			$query->from("`#__categories` cat1");
			$query->leftJoin( "`#__categories` cat2 ON cat1.parent_id = cat2.id" )
				->leftJoin( "`#__categories` cat3 ON cat2.parent_id = cat3.id" )
				->leftJoin( "`#__categories` cat4 ON cat3.parent_id = cat4.id" )
				->leftJoin( "`#__categories` cat5 ON cat4.parent_id = cat5.id" )
				->leftJoin( "`#__categories` cat6 ON cat5.parent_id = cat6.id" )
				->leftJoin( "`#__categories` cat7 ON cat6.parent_id = cat7.id" )
				->leftJoin( "`#__categories` cat8 ON cat7.parent_id = cat8.id" )
				;
				
			$query->where( "cat1.id = $my_id" )
			 	->where( "cat1.published = 1");

			$db->setQuery( $query, 0, 1 );
			$row		= $db->loadObject();

			$cat_titles = array();
			$cat_ids	= array();
			for($i = 1; $i <= 8; $i++ ) {
				$ct = "ct$i";
				$ci = "ci$i";
				if ( $row->$ct != '' and $row->$ci != 1) $cat_titles[] = $row->$ct;
				if ( $row->$ci != '' and $row->$ci != 1) $cat_ids[] = $row->$ci;
			}
			$row->ancestor_category_titles = $cat_titles;
			$row->ancestor_category_ids = $cat_ids;

			$rows[$id]	= $row;
		}
		return @$rows[$id];
	}
	

}