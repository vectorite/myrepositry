<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * JomGenius classes
 * 
 * @package		JomGenius
 * @version		12
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2010-2011 Brandon IT Consulting. All rights reserved.
 */

/* this class handles creating/returning singletons of factories */

/* changelog:
 * v7 added blackberry detection
 * v8 VM: increased category level nesting limit to 8; added extra page types
 * v10 VM: fix detection of pagetype when directly on menu item with no "page" param
 * v11 VM: added virtuemart history queries
 * v12 Core: fixed user groups for J2.5+
 */

// to allow this class to be defined in more than one component
if (!defined('JOMGENIUS_CLASS_LOADED')) {

	define( 'JOMGENIUS_CLASS_LOADED', 1 );
	define( 'JOMGENIUS_VERSION', 13 );
	define( 'JOMGENIUS_FILE', __FILE__ );

	class JomGeniusFactory extends JObject {
		
		// holds all our singletons
		var $factories = array();
		
		function &getFactory( $name ) {

			// this has to return a new object, but cached for this module id
			if ( !array_key_exists( $name, $this->factories ) ) {
				
				
				$this->factories[ $name ] = $this->factoryForName( $name );
			}
			
			return $this->factories[ $name ];
		}
	
		/* searches for the appropriate class file and if found, loads it and returns a class of the appropriate name */
		function &factoryForName( $name ) {
			
			
			if (! preg_match("#[a-zA-Z-_0-9]+#", $name ) ) {
				$ret = null; // returning by reference - must return a variable.
				return $ret;
			}
			$file = dirname(__FILE__) . DS . 'jomgenius' . DS . $name . '.php';
			if ( !file_exists( $file ) ) {
				$ret = null;
				return $ret;
			}
			include_once $file;
			$classname = 'JomGeniusClass' . $name;
			$obj = new $classname;
			
			
			if ( @$obj->shouldInstantiate() ) {
				return $obj;
			}
			$ret = null;
			return $ret;
		}
	}

	/* this class is the superclass of all individual factories */
	class JomGeniusParent extends JObject {
	
		function shouldInstantiate() {
			return true;
		}
		function componentExists( $name ) {
			if ( $name == null ) {
				return false;
			}
			$admin_component = JPATH_SITE . DS . "administrator" . DS . "components" . DS . $name;
			$component = JPATH_SITE . DS . "components" . DS . $name;
			if ( file_exists( $admin_component ) or file_exists( $component ) ) {
				return true;
			}
			return false;
		}
	
		/**
		 * convert a string/numeric argument to an array. Commas indicate separate array elements
		 * strict: if true, will not be split on commas and will not be trimmed. Returned will be
		 *         just the one string as the single element of the array.
		 */
		function convertToArray( $arg, $strict = false ) {
			if ( is_array( $arg ) ) {
				return $arg;
			}
			if ( $strict ) {
				return array( $arg );
			}
			if ( is_int( $arg ) or is_float( $arg ) ) return array( $arg );
			if ( is_string( $arg ) ) {
				$ret = explode( ',', $arg );
				foreach ( $ret as $key=>$val ) {
					$ret[$key] = trim( $ret[$key] );
				}
				return $ret;
			}
			// if it's anything else
			return array( $arg );
		}
	
		/**
		 * Utility function for converting timestamps to "the number of seconds/minutes/days since ..."
		 * $datestring must be in GMT, and works best if it's a datestamp from the database,
		 * e.g. 2010-09-02 02:35:29
		 * return is always the integer value - i.e. no decimal points. This is to make it easier
		 * to check for a certain number of days 'since' something happened. Was it 1 day ago? 2 days ago? etc.
		 */
		function timeSince( $datestring, $unit = 'minutes' ) {
			$difference = time() - JomGeniusParent::UTC_strtotime( $datestring );
			switch ( $unit ) {
				case 'seconds': return (int)$difference;
				case 'minutes': return (int)($difference / 60);
				case 'hours': return (int)($difference / 3600);
				case 'days': return (int)($difference / 86400);
			}
		}
	
		function UTC_strtotime( $date ) {
			if ( function_exists( 'date_default_timezone_get' ) ) {
				$old_tz = date_default_timezone_get();
				date_default_timezone_set( 'UTC' );
				$timestamp = strtotime( $date );
				date_default_timezone_set( $old_tz );
			} else {
				// appending "UTC" to almost anything in a date string seems to do the trick.
				// we assume that people are NOT going to include the time zone in the string
				// that they provide.
				$timestamp = strtotime( $date . " UTC");
			}
			return $timestamp;
		}

		/**
		 * This function returns a parameters object for a given menu item. It is intended to be used
		 * when a particular parameter is encoded into the menu item and NOT in the URL. Therefore if
		 * you think that a page should be displaying a content category, for example, but the category
		 * id is not in the URL, then the menu item should be checked and the value extracted from
		 * the parameters.
		 * Note: you should check that the params object really does refer to the component indicated
		 * by the rest of the URL (e.g. option, view etc), otherwise a renegade Itemid in a URL could
		 * mess things up a lot.
		 */
		function &menuItemParams() {
			$Itemid = JRequest::getInt( 'Itemid' );
			$menu =& JSite::getMenu();
			$params =& $menu->getParams( $Itemid );
			return $params;
		}
		
		/**
		 * grab just one parameter value
		 */
		function menuItemParam( $paramName, $default = null ) {
			static $params = null;
			if ( $params == null ) {
				$params = JomGeniusParent::menuItemParams();
			}
			if ( $params == null ) return null;
			return $params->get( $paramName, $default );
		}
		
		// convenience function, since "info" wasn't really a great choice for the main getter function.
		function get( $param ) {
			return $this->info( $param );
		}
		
		
		/**
		 * Main public method for checking stuff. Probably does not need overriding at subclass level
		 *
		 * This function encapsulates info(), and allows people to check what's in those values.
		 *
		 * type: the type of information requested (e.g. section_id, category_id, article_fulltext)
		 * value: a string, comma separated string, or array of values to check.
		 * operator: =, !=, <, >, etc.
		 * strict: if false, then commas in the $value get divided into separate items, each checked individually.
		 *         if true, then the $value is always checked as a single string.
		 *
		 * If an array of values is given, then only one of them has to match to return "true". This is an "OR"
		 * style of comparison. There's no point in doing "AND" comparisons, as it's unlikely that a single actual_value
		 * will match more than one submitted value using the same operator.
		 */

		function check( $string, $op1 = null, $op2 = null, $op3 = null ) {

			// either op1 is case_sensitive and op2 = strict, OR op1 is an argument, op2 is case_sens and op3 = strict.
			// problem is when the argument is a boolean... then we can't tell how the sensitivity/strictness works.
			// We ALWAYS assume that op1 is an argument IF the regex of the main string does not come up with an argument.
			

			// examples
			// "any categories = any of adam,bob"			-- " Succeed when any one of the categories is exactly equal to adam OR bob"
			// "all categories contain all of adam,bob"     -- " Succeed only when every category contains the string adam and the string bob."
			// "no categories = any of adam,bob"            -- " check all categories. Fail if any equal adam OR bob"
			// "count categories = 1"						--   both operands are scalar. the "=" does exact match.
			// "any categories < 2"							-- " Succeed when at least 1 of the categories matches the right operand."
			
			$string = ltrim( $string ); // don't need to use JString for ltrim.
			$prefix = '';
			
			$prefixes_base	=	'(all(?: *of)|any(?: *of)?|none(?: *of)?|no|count|number +of)';
			$prefixes		= $prefixes_base . '?';
			$prefixes_with_space = '(?:' . $prefixes_base . ' +)?';
			
			$actual		=	'([a-zA-Z_0-9]+)';
			$negators	= 	'(?:(is +not|are +not|not|!) +)?';
			$operators	=	'(<|<=|>|>=|!=|=(?:=)?|equal(?:s?)|contain(?:s)?|greater(?: *than)?|' .
							'less(?: *than)?|start(?:(?:s?) *with)?|end(?:(?:s?) *with)?|regex)';
			$operand	=	'(.*)';
			
			$regex		= '#^' . $prefixes_with_space . $actual . ' +' . $negators . $operators . ' *' . $prefixes . ' *' . $operand . '$#';
			$m = preg_match( $regex, $string, $matches );

			// [1] is "all/any etc", [2] is type, [3] is ! or "not", [4] is the operator, [5] is all/any etc, [6] is the value (to check against)
 			if ( $m ) {
				$prefix1	= $matches[1];
				$type		= $matches[2];
				$negator	= $matches[3];
				$operator	= $matches[4];
				$prefix2	= $matches[5];
				$value		= $matches[6];
				if ( JString::strlen( $value ) == 0 ) {
					$value			= $op1;
					$case_sensitive = ( $op2 === null ? false : $op2 );
					$strict			= ( $op3 === null ? false : $op3 );
				} else {
					$case_sensitive = ( $op1 === null ? false : $op1 );
					$strict			= ( $op2 === null ? false : $op2 );
				}
				
				$prefix1 = strtolower( str_replace( ' ', '', $prefix1 ) );
				$prefix2 = strtolower( str_replace( ' ', '', $prefix2 ) );
				$negator = strtolower( str_replace( ' ', '', $negator ) );
				
				switch( $prefix1 ) {
					case 'allof': $prefix1 = 'all'; break;
					case 'anyof': $prefix1 = 'any'; break;
					case 'noneof':
					case 'no':
						$prefix1 = 'none'; break;
					case 'numberof': $prefix1 = 'count'; break;
				}
				switch( $prefix2 ) {
					case 'allof': $prefix2 = 'all'; break;
					case 'anyof': $prefix2 = 'any'; break;
					case 'noneof':
					case 'no':
						$prefix2 = 'none'; break;
					case 'numberof': $prefix2 = 'count'; break;
				}

				switch( $negator ) {
					case '!':
					case 'arenot':
					case 'isnot':
						$negator = 'not';
				}

				switch( $operator ) {
					case '!=':
						if ( $negator == 'not') $negator = ''; // double negation
						else $negator = 'not';
						// fall through
						
					case '==':
					case 'equal':
					case 'equals':
						$operator = '='; break;
					case 'contains':
					case 'contain':
						$operator = 'contains'; break;
					case 'greaterthan':
					case 'greater':
						$operator = '>'; break;
					case 'lessthan':
					case 'less':
						$operator = '<'; break;
					case 'startwith':
					case 'startswith':
					case 'starts':
					case 'start':
						$operator = 'startswith'; break;
					case 'endwith':
					case 'endswith':
					case 'ends':
					case 'end':
						$operator = 'endswith'; break;
					default:
				}

				$actual_value = $this->info( $type ); // usually string, can also be boolean or array.

				if ( $prefix1 == 'count' ) {
					if ( $actual_value === null ) $actual_value = 0;
					else if ( is_array( $actual_value ) ) $actual_value = count( $actual_value );
					// an empty string is a count of 1. Hope that always works.
					else if ( is_string( $actual_value ) or is_integer( $actual_value ) or is_bool( $actual_value ) ) $actual_value = 1;
					else $actual_value = 0;
					$prefix1 = '';
				}

				return $this->_check( $prefix1, $actual_value, ($negator == 'not'), $operator, $prefix2, $value, $case_sensitive, $strict );
				
			}
			return false; // ERROR!!!
		}
	
		function op( $v1, $operator, $v2 ) {
			switch ( $operator ) {
				case '=' : return $v1 == $v2;
				case '>' : return $v1 > $v2;
				case '>=': return $v1 >= $v2;
				case '<' : return $v1 < $v2;
				case '<=': return $v1 <= $v2;
				case 'contains': return (JString::strpos( (string)$v1, (string)$v2 ) !== false);
				case 'startswith': return ( JString::substr( (string)$v1, 0, JString::strlen( (string)$v2 ) ) === (string)$v2 );
				case 'endswith': return ( JString::substr( (string)$v1, 0 - JString::strlen( (string)$v2 ) ) === (string)$v2 );
				case 'regex':
					$ret = @preg_match( (string)$v2, (string)$v1 );
					return $ret == true;
			}
		}
		
		/**
		 * Does the grunt work of comparison operations. Needs to be fed data by child classes.
		 * actual_value: a string value, usually derived from some element of the page (e.g. id, name).
		 * values: a string or array to check the actual_value against.
		 * prefix(es): all/any/none/count
		 * negator: not
		 * operator: <, <=, =, >, >=, contains, startswith, endswith, regex
		 */
		function _check( $prefix1, $actual_value, $negator, $operator, $prefix2, $value, $case_sensitive = false, $strict = false ) {
			
			$values = $this->convertToArray( $value, $strict ); // convert to array
			$actual_values = $this->convertToArray( $actual_value, $strict ); // convert to array

			// set defaults
			if ( $prefix1 == '' ) $prefix1 = 'any';
			if ( $prefix2 == '' ) $prefix2 = 'any';
			if ( $prefix2 == 'count' ) {
				$values = array( count( $values ) ); // convert to a count of itself, then put that integer into an array
				$prefix2 = 'any';
			}
			
			$operator = str_replace( ' ', '', $operator );
			
			
			// individual values will be pre-trimmed unless "strict" was specified.
		
			// Here's the situation:
			// $actual_values contains the values we are looking for
			// $values contains an array of values we want to check against that actual value (with operator)

			// pre-process $actual_values and user-supplied $values according to
			// $strict and $case_sensitive.
		
			if ( $operator != 'regex' and ( !$strict or !$case_sensitive )) {
				foreach ( $values as $key=>$value ) {
					if ( ! $case_sensitive ) {
						$values[$key] = JString::strtolower( $value );
					}
					if ( ! $strict ) {
						$values[$key] = JString::trim( $values[$key] );
					}
				}

				foreach ( $actual_values as $key=>$value ) {
					if (! is_bool( $value ) ) {
						if ( ! $case_sensitive ) {
							$actual_values[$key] = JString::strtolower( $value );
						}
						if ( ! $strict ) {
							$actual_values[$key] = JString::trim( $actual_values[$key] );	
						}
					}
				}
			}
		
			switch( $operator ) {
				case '=':
				case '<':
				case '<=':
				case '>':
				case '>=':
				case 'contains':
				case 'startswith':
				case 'endswith':
				case 'regex':
				
					$p = $prefix1 . '+' . $prefix2;
					
					foreach ( $actual_values as $actual_value ) {
						$actual_is_bool = is_bool( $actual_value );
						foreach ( $values as $value ) {
							if ( $actual_is_bool ) {
								if ( $value == 'true' ) $value = true;
								else if ( $value == 'false' ) $value = false;
							}
							$res = $this->op( $actual_value, $operator, $value );
							
							if ( $negator ) $res = !$res;
							
							// outer loop is LHS, inner loop is RHS.
							// 1. all + all:  fail if we get a "false". We pass at end of outer loop.
							// 2. all + any:  we "break" if we get a "true", fail at end of inner. pass at end of outer.
							// 3. all + none: fail if we ever get a true. pass at end of outer
							// 4. any + all:  any time we get a "false" we "break". pass at end of inner, fail at end of outer.
							// 5. any + any:  we pass if we ever get a "true", and fail at end of outer.
							// 6. any + none: we "break" if we get a "true". pass at end of inner. Fail at end of outer
							// 7. none + any: we fail if we ever get a "true", and pass if we get to the end of the outer.
							// 8. none + all: we "break" if we get a "false". fail at end of inner. pass at end of outer.
							// 9. none + none: we "break" if we get a "true". fail at end of inner. pass at end of outer
							
							// 1:
							if ( $p == 'all+all' and $res == false ) return false;
							// 2:
							if ( $p == 'all+any' and $res == true ) continue 2;
							// 3:
							if ( $p == 'all+none' and $res == true ) return false;
							// 4:
							if ( $p == 'any+all' and $res == false ) continue 2;
							// 5:
							if ( $p == 'any+any' and $res == true ) return true;
							// 6:
							if ( $p == 'any+none' and $res == true ) continue 2;
							// 7:
							if ( $p == 'none+any' and $res == true ) return false;
							// 8:
							if ( $p == 'none+all' and $res == false ) continue 2;
							// 9:
							if ( $p == 'none+none' and $res == true ) continue 2;
						}
						
						// end of inner loop:
						// 2:
						if ( $p == 'all+any' ) return false;
						// 4:
						if ( $p == 'any+all' ) return true;
						// 6:
						if ( $p == 'any+none' ) return true;
						// 8:
						if ( $p == 'none+all' ) return false;
						// 9:
						if ( $p == 'none+none' ) return false;							
					}
					
					// end of outer loop:
					// 1:
					if ( $p == 'all+all' ) return true;
					// 2:
					if ( $p == 'all+any' ) return true;
					// 3:
					if ( $p == 'all+none' ) return true;
					// 4:
					if ( $p == 'any+all' ) return false;
					// 5:
					if ( $p == 'any+any' ) return false;
					// 6:
					if ( $p == 'any+none' ) return false;
					// 7:
					if ( $p == 'none+any' ) return true;
					// 8:
					if ( $p == 'none+all' ) return true;
					// 9:
					if ( $p == 'none+none' ) return true;
					
					// shouldn't get to here!		
			} // end of switch
//	echo 'parse error????';
		}// end of function
	
	} // end of class. Function(s) follow.


	/* convenience function to make the syntax as easy as possible for people to use.
	 * e.g. simply: $vm = JomGenius("virtuemart");
	 */

	function &JomGenius( $name ) {
		static $factory;
		if ( !isset( $factory ) ) $factory = new JomGeniusFactory;
		
		return $factory->getFactory( $name );
	}

} /* JOMGENIUS_CLASS_LOADED */
