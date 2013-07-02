<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * JomGenius classes
 * 
 * @package		JomGenius
 * @version		5
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2010-2011 Brandon IT Consulting. All rights reserved.
 */

class JomGeniusClassJomsocial extends JomGeniusParent {
	
	/* for VirtueMart, we allow it to be instantiated even if we are not currently on the VM component.
	 * This is because we might want to check the contents of the cart on any page.
	 */
	
	var $Itemid;
	var $view;
	var $option;
	var $task;
	var $userid;
	var $groupid;
	var $catid;
	var $app;
		
	function __construct() {
		$this->Itemid		= JRequest::getVar('Itemid');
		$this->view			= JRequest::getWord('view');
		$this->option		= JRequest::getVar('option');
		$this->task			= JString::strtolower(JRequest::getVar('task'));
		$this->userid		= JRequest::getInt('userid', 0);
		$this->groupid		= JRequest::getInt('groupid', 0);
		$this->catid		= JRequest::getVar('catid', 0);
		$this->app			= JRequest::getVar('app');
	}
	
	function shouldInstantiate() {
		return $this->componentExists( 'com_community' );
	}
	
	/**
	 * A generic function that knows how to get lots of different info about the current page or product.
	 */
	function info( $type ) {
		$type = strtolower( str_replace( array(' ','_'), '', $type ) );
		switch( $type ) {
				
			case 'pagetype':
				return $this->pageType();
			
			case 'ismypage': // any type of page associated with a user id - if the id is mine
				return $this->isMyPage();
			
			case 'isgroupowner':
				return $this->isGroupOwner();
			
			case 'isphotocreator':
			
			case 'isalbumcreator':
			
			case 'isvideocreator':
			
			case 'numberofwallposts':
			
			case 'wallpostauthors': // returns array of author ids who have written on your wall.
			
			case 'amiingroup': // boolean; am in in the group of the item being veiewed? relies on groupid being present.
			
			case 'groupid':
			case 'groupowner':
			case 'groupcategoryid':
			case 'groupcategoryname':
			case 'groupcategorydescription':
			case 'groupname':
			case 'groupdescription':
			case 'groupemail':
			case 'groupwebsite':
			case 'groupcreateddate':
			case 'groupavatar':
			case 'groupthumb':
			case 'groupdiscusscount':
			case 'groupwallcount':
			case 'groupmembercount':
			case 'groupmembers':
				return $this->groupInfo( $type );

			case 'bulletincreatedby':
			case 'bulletintitle':
			case 'bulletinmessage':
			case 'bulletindate':
				return $this->bulletinInfo();
			
			// these ones are valid on the "view discussion" page.
			case 'discussiontitle':
			case 'discussionmessage':
			case 'discussionlastreplied':
			case 'discussioncreator':
			case 'discussionnumreplies': // #__community_wall where type = 'discussions' and content_id = <the discussion id>
			case 'discussionreplyauthors':
				return $this->discussionInfo( $type );
			
			// These ones can be used on any page. Because activities list is often ajax, the displayed items may be manipulated without page refresh.
			case 'numtotalactivities':
			case 'numtotalvisibleactivities':
			case 'nummyactivities':
			case 'nummyvisibleactivities':
				return $this->activities();
			
			// these ones apply to the logged-in user
			case 'mystatus':
			case 'mypoints':
			case 'mystatusage': // days
			case 'mystatusdate': // full datetime
			case 'ihaveavatar': // is not components/com_community/assets/default.jpg
			case 'myviews':
			case 'myfriendcount':
			case 'myreceivesystememails':
			case 'myreceiveapplicationnotifications':
			case 'myreceivewallcommentnotifications':
			case 'myprivacyprofileview':
			case 'myprivacyfriendsview':
			case 'myprivacyphotoview':
			case 'myactivitylimit':
				return $this->userInfo( $type, 'my' );
			
			
			// these ones apply to the user being viewed
			case 'userstatus':
			case 'userpoints':
			case 'userstatusage': // days
			case 'userstatusdate': // full datetime
			case 'userhasavatar': // is not components/com_community/assets/default.jpg
			case 'userviews':
			case 'userfriendcount':
			case 'userreceivesystememails':
			case 'userreceiveapplicationnotifications':
			case 'userreceivewallcommentnotifications':
			case 'userprivacyprofileview':
			case 'userprivacyfriendsview':
			case 'userprivacyphotoview':
			case 'useractivitylimit':
				return $this->userInfo( $type, 'user' );
			
			/*
			notifyEmailSystem=1
			privacyProfileView=0
			privacyPhotoView=0
			privacyFriendsView=0
			privacyVideoView=1
			notifyEmailMessage=1
			notifyEmailApps=1
			notifyWallComment=0
			activityLimit=30
		    */
			
			default:
		}
		
		if ( substr( $type, 0, 7 ) == 'myfield' ) return $this->myField( $type, 'my' );
		if ( substr( $type, 0, 9 ) == 'userfield' ) return $this->myField( $type, 'user' );
	}
	
	function userInfo( $type, $scope = 'my' ) {
		if ( $type == 'ihaveavatar' ) $type = 'myhaveavatar';
		$userid = ($scope == 'my') ? $user->id : $this->userid;
		if ( $userid == 0 ) return null;
		
		$info = $this->_userInfo( $userid );
		if ( ! is_array( $info ) ) return null;
		
		$type =  substr( $type, ( $scope == 'my' ) ? 2 : 4 ); // knock off leading 'my' or 'user'
		
		switch ( $type ) {
			case 'status':
			case 'points':
			case 'friendcount':
				return @$info[ $type ];
				
			case 'statusage': // days
			case 'statusdate': // full datetime
				return @$info[ 'posted_on' ];
			
			case 'haveavatar': // is not components/com_community/assets/default.jpg
				return @$info[ 'avatar' ] != 'components/com_community/assets/default.jpg';

			case 'views':
				return @$info[ 'view' ];

			case 'receivesystememails':
				return @$info[ 'params' ]->get( 'notifyEmailSystem' );
				
			case 'receiveapplicationnotifications':
				return @$info[ 'params' ]->get( 'notifyEmailApps' );

			case 'receivewallcommentnotifications':
				return @$info[ 'params' ]->get( 'notifyEmailApps' );

			case 'privacyprofileview':
				$p = @$info[ 'params' ]->get( 'privacyProfileView' );
				if ( $p == '0' ) return 'public';
				if ( $p == '20' ) return 'site members';
				if ( $p == '30' ) return 'friends';
				return;
				
			case 'privacyfriendsview':
				$p = @$info[ 'params' ]->get( 'privacyFriendsView' );
				if ( $p == '0' ) return 'public';
				if ( $p == '20' ) return 'site members';
				if ( $p == '30' ) return 'friends';
				if ( $p == '40' ) return 'only me';
				return;

			case 'privacyphotoview':
				$p = @$info[ 'params' ]->get( 'privacyPhotoView' );
				if ( $p == '0' ) return 'public';
				if ( $p == '20' ) return 'site members';
				if ( $p == '30' ) return 'friends';
				if ( $p == '40' ) return 'only me';
				return;

			case 'activitylimit':
				return @$info[ 'params' ]->get( 'privacyPhotoView' );
			
		}
	}
	
	/**
	 * Provides basic info out of the users table and some associated others.
	 */
	function _userInfo( $userid ) {
		static $users = array();
		if ( !array_key_exists( $userid, $users ) ) {
			$db 	=& JFactory::getDBO();
			$userid = $db->quote( $db->getEscaped( $userid ) );
			$query = "select * from #__community_users where userid = $userid";
			$db->setQuery( $query );
			$users[ $userid ] = $db->loadAssoc();
			
			// now want to get all the fields as well...
			
			$query = 'select f.fieldcode, fv.value from #__community_fields_values fv, #__community_fields f where fv.user_id = $userid and fv.field_id = f.id';
			$db->setQuery( $query );
			$list = $db->loadAssocList();
			$fields = array();
			foreach ( $list as $key=>$value ) {
				$real = strtolower( substr( $value['fieldcode'], 6 ) );
				$fields[$real] = $value[ 'value' ];
			}
			$users[ $userid ][ 'fields' ] = $fields;

			// convert from list of strings, to proper params object. We can use $params->get('name') etc.
			$users[ $userid ][ 'params' ] = new JParameter( $users[ $userid ][ 'params' ] );
			
		}
		return @$users[ $userid ];
	}
	
	/**
	 * we can use myField() to get hold of individual values but to be frank, it's best to
	 * get all of them with _userInfo, instead.
	 */
	
	function myField( $type, $scope ) {
		if ( $scope == 'my' ) {
			$type = substr( $type, 7 );
			$id = $user->id;
		}
		if ( $scope == 'user' ) {
			$type = substr( $type, 9 );
			$id = $this->userid;
		}
		if ( $id == 0 ) return ''; // we don't know which user to refer to
		
		$db 	=& JFactory::getDBO();
		
		$type = strtoupper( 'FIELD_' . $type ); // to get entry in #__community_fields
		$type = $db->quote( $db->getEscaped( $type ) );
		
		$query = "select fv.value from #__community_fields_values fv, #__community_fields f where f.fieldcode = $type and fv.field_id = f.id";
		$db->setQuery( $query );
		return $db->loadResult();
	}
	
	/**
	 * returns true in the following situations, else returns false:
	 *
	 * - looking at your own profile page while logged in
	 * - viewing an image you have uploaded
	 * - editing your own profile, avatar, details, privacy or preferences XXX maybe not...
	 * - 
	 */
	function isMyPage() {
		
	}
	
	function pageType() {
		$user 	=& JFactory::getUser();
		
		// USER REGISTRATION ETC
		
		if ( $this->option == 'com_user' and $this->view == 'reset' ) return 'reset';
		if ( $this->option == 'com_user' and $this->view == 'remind' ) return 'remind';
		if ( $this->option == 'com_community' ) {
			if ( $this->view == 'register' and $this->task == 'registerprofile' ) return 'register.profile';
			if ( $this->view == 'register' and $this->task == 'registeravatar' ) return 'register.avatar';
			if ( $this->view == 'register' and $this->task == 'registersuccess' ) return 'register.success';
			if ( $this->view == 'register' and $this->task == 'activation' ) return 'register.activation';
			if ( $this->view == 'register' and $this->task == 'activationresend' ) return 'register.activation';
			if ( $this->view == 'register' ) return 'register.main';
		}
		
		if ( $this->option != 'com_community' ) return null;
		
		// frontpage and any others that are governed solely by $view
		if ( in_array( $this->view, 
			array(
			'frontpage'
			) ) ) {
				return $this->view;
		}
		
		// SEARCH
		
		if ( $this->view == 'search' ) {
			if ( $this->task == 'browse' and JRequest::getVar('Search') != null ) return 'search.results';
			if ( $this->task == 'browse' ) return 'search';
			if ( $this->task == 'advancesearch' and JRequest::getVar('operator') != null ) return 'search.results.advanced';
			if ( $this->task == 'advancesearch' ) return 'search.advanced';
			if ( $this->task == 'field' ) {
				if ( JRequest::getVar('FIELD_GENDER') != null ) return 'search.results.gender';
				if ( JRequest::getVar('FIELD_STATE') != null ) return 'search.results.state';
				if ( JRequest::getVar('FIELD_CITY') != null ) return 'search.results.city';
				if ( JRequest::getVar('FIELD_COUNTRY') != null ) return 'search.results.country';
				if ( JRequest::getVar('FIELD_WEBSITE') != null ) return 'search.results.website';
				if ( JRequest::getVar('FIELD_COLLEGE') != null ) return 'search.results.college';
				if ( JRequest::getVar('FIELD_GRADUATION') != null ) return 'search.results.graduation';
			}
			if ( $this->task == '' ) return 'search';
			return '';
		}
		
		// INBOX
		
		if ( $this->view == 'inbox' ) {
			if ( $this->task == '' ) return 'inbox';
			if ( $this->task == 'sent' ) return 'inbox.sent';// would be good to know how many we have sent overall or today
			if ( $this->task == 'write' ) return 'inbox.write';
			if ( $this->task == 'read' ) return 'inbox.read';
			return '';
		}
		
		// APPLICATIONS
		
		if ( $this->view == 'apps' ) {
			if ( $this->task == '' ) return 'apps.my';
			if ( $this->task == 'browse' ) return 'apps.browse';
			return '';
		}
		
		// GROUPS
		
		if ( $this->view == 'groups' ) {
			if ( $this->task == '' ) return 'groups.all';
			if ( $this->task == 'mygroups' ) return 'groups.my';// would be good to know how many we have sent overall or today
			if ( $this->task == 'search' and JRequest::getVar('search') !== null ) return 'groups.search.results';
			if ( $this->task == 'search' ) return 'groups.search';
			if ( $this->task == 'viewgroup' ) return 'group.view';
			$categoryid = JRequest::getVar( 'categoryid' );
			if ( $categoryid != '' ) return 'groups.category.view';
			if ( $this->task == 'viewmembers' ) return 'groups.members.view';
			if ( $this->task == 'viewbulletins' ) return 'groups.bulletins.view';
			if ( $this->task == 'viewbulletin' ) return 'groups.bulletin.view';
			if ( $this->task == 'addnews' ) return 'groups.bulletin.add';
			if ( $this->task == 'adddiscussion' ) return 'groups.discussion.add';
			if ( $this->task == 'viewdiscussion' ) return 'groups.discussion.view';
			if ( $this->task == 'viewdiscussions' ) return 'groups.discussions.view';
			if ( $this->task == 'create' ) return 'group.create';
			if ( $this->task == 'created' ) return 'group.created';

			return '';
		}
		
		
		// PHOTOS
		if ( $this->view == 'photos' ) {
			if ( $this->task == 'uploader' and $this->groupid ) return 'photos.uploader.group';
			if ( $this->task == 'album' and $this->groupid ) return 'photos.album.group';
			if ( $this->task == 'album' and !$this->groupid and $this->userid == $user->id ) return 'photos.album.my';
			if ( $this->task == 'album' and !$this->groupid and $this->userid != $user->id ) return 'photos.album.user';
			if ( $this->task == 'photo' and $this->groupid ) return 'photos.photo.group';
			if ( $this->task == 'myphotos' and $this->userid == $user->id ) return 'photos.my';
			if ( $this->task == 'myphotos' and $this->userid != $user->id ) return 'photos.user';
			if ( $this->task == '' ) return 'photos.all';
			if ( $this->task == 'newalbum' and $this->userid == $user->id ) return 'photos.album.my.new';
			// personal photos:
			if ( $this->task == 'uploader') return 'photos.uploader';
			if ( $this->task == 'photo' ) return 'photos.photo';
			if ( $this->task == 'editalbum' ) return 'photos.editalbum';			
		}
		
		// VIDEOS
		if ( $this->view == 'videos' ) {
			if ( $this->task == '' and $this->groupid and !$this->catid) return 'videos.view.group.all';
			if ( $this->task == '' and $this->groupid and $this->catid) return 'videos.view.group.category';
			if ( $this->task == '' and $this->catid ) return 'videos.view.category';
			if ( $this->task == '' ) return 'videos.view.all'; // no cat, no group
			if ( $this->task == 'video' and $this->groupid ) return 'video.group';
			if ( $this->task == 'myvideos' and $this->userid == $user->id ) return 'videos.view.my';
			if ( $this->task == 'search' and JRequest::getVar('search-text') !== null ) return 'videos.search';
			if ( $this->task == 'search' ) return 'videos.search';
		}
		
		
		// PROFILE
		
		if ( $this->view == 'profile' and $this->task == '' ) {
			if ( $this->userid == $user->id ) return 'profile.my';
			if ( $this->userid != $user->id ) return 'profile.user';
		}
		
		if ( $this->view == 'profile' and in_array( $this->task, 
			array(
				// in profile:
			'uploadavatar',
			'edit', // edit profile
			'editdetails', // in profile
			'privacy',
			'preferences',
			
			) ) ) {
				return 'profile.' . $this->task;
		}
		
		// FRIENDS
		
		if ( $this->view == 'friends' and $this->task == '' ) {
			$filter = JRequest::getVar('filter');
			if ( $filter == 'mutual' ) return 'friends.mutual';
			return 'friends';
			// ignore the following for now.
			if ( $this->userid > 0 and $user->id == $this->userid ) {
				return 'friends.my'; // FIXME - not sure what the difference is between the two
			}
			return 'friends.all';
		}
		if ( $this->view == 'friends' and in_array( $this->task, 
			array(
			'invite', // after an invitation is sent, JRequest::getVar('action') == 'invite' as well
			'sent', // would be nice to know how many pendings there are...
			'pending' // would be nice to know how many pendings there are...
			) ) ) {
				return 'friends.' . $this->task;
		}
		
		// WALL
		if ( $this->task == 'app' and $this->app == 'walls' ) {
			if ( $this->view == 'profile' ) return 'wall';
		}
		
		
		return '';
	}
	
}