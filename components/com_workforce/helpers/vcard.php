<?php
/**
* Modified by the Thinkery to avoid bugs in Joomla core Vcard class
*/

/***************************************************************************
PHP vCard class v2.0
(cKai Blankenhorn
www.bitfolge.de/en
kaib@bitfolge.de

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
***************************************************************************/

function encode($string) 
{
	return escape(quoted_printable_encode($string));
}

function escape($string) 
{
	return str_replace(';',"\;",$string);
}

// check if function exists to avoid core vcard bug
if(!function_exists('quoted_printable_encode'))
{
    function quoted_printable_encode($input, $line_max = 76) {
        $hex 		= array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
        $lines 		= preg_split("/(?:\r\n|\r|\n)/", $input);
        $eol 		= "\r\n";
        $linebreak 	= '=0D=0A';
        $escape 	= '=';
        $output 	= '';

        for ($j=0;$j<count($lines);$j++) {
            $line 		= $lines[$j];
            $linlen 	= strlen($line);
            $newline 	= '';

            for($i = 0; $i < $linlen; $i++) {
                $c 		= substr($line, $i, 1);
                $dec 	= ord($c);

                if ( ($dec == 32) && ($i == ($linlen - 1)) ) { // convert space at eol only
                    $c = '=20';
                } elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always encode "\t", which is *not* required
                    $h2 = floor($dec/16);
                    $h1 = floor($dec%16);
                    $c 	= $escape.$hex["$h2"] . $hex["$h1"];
                }
                if ( (strlen($newline) + strlen($c)) >= $line_max ) { // CRLF is not counted
                    $output .= $newline.$escape.$eol; // soft line break; " =\r\n" is okay
                    $newline = "    ";
                }
                $newline .= $c;
            } // end of for
            $output .= $newline;
            if ($j<count($lines)-1) {
                $output .= $linebreak;
            }
        }

        return trim($output);
    }
}

class vCard 
{
	var $properties;
	var $filename;

	function setPhoneNumber($number, $type='') 
    {
        // type may be PREF | WORK | HOME | VOICE | FAX | MSG | CELL | PAGER | BBS | CAR | MODEM | ISDN | VIDEO or any senseful combination, e.g. "PREF;WORK;VOICE"
		$key = 'TEL';
		if ($type!='') {
			$key .= ';'. $type;
		}
		$key.= ';ENCODING=QUOTED-PRINTABLE';

		$this->properties[$key] = quoted_printable_encode($number);
	}

	// UNTESTED !!!
	function setPhoto($type, $photo) 
    { 
        //// $type = "GIF" | "JPEG"
		$this->properties["PHOTO;TYPE=$type;ENCODING=BASE64"] = base64_encode($photo);
	}

	function setFormattedName($name) 
    {
		$this->properties['FN'] = quoted_printable_encode($name);
	}

	// needed to fix bug in vcard class
	function setName( $family = '', $first = '', $additional = '', $prefix = '', $suffix = '' ) 
    {
		$this->properties["N"] 	= "$family;$first;$additional;$prefix;$suffix";
		$this->setFormattedName( trim( "$prefix $first $additional $family $suffix" ) );
	}

	// needed to fix bug in vcard class
	function setAddress( $street = '', $city = '', $state = '', $zip = '', $type = 'HOME;POSTAL' ) 
    {
		// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$separator = ';';

		$key 		= 'ADR';
		if ( $type != '' ) {
			$key	.= $separator . $type;
		}
		$key.= ';ENCODING=QUOTED-PRINTABLE';

		$return = '';
        $return .= $separator;
		$return .= $separator . encode( $street );
		$return .= $separator . encode( $city );
		$return .= $separator . encode( $state);
		$return .= $separator . encode( $zip );

		$this->properties[$key] = $return;
	}

	function setLabel($postoffice = '', $extended = '', $street = '', $city = '', $region = '', $zip = '', $country = '', $type = 'HOME;POSTAL') 
    {
		$label = '';
		if ($postoffice!='') {
			$label.= $postoffice;
			$label.= "\r\n";
		}

		if ($extended!='') {
			$label.= $extended;
			$label.= "\r\n";
		}

		if ($street!='') {
			$label.= $street;
			$label.= "\r\n";
		}

		if ($zip!='') {
			$label.= $zip .' ';
		}

		if ($city!='') {
			$label.= $city;
			$label.= "\r\n";
		}

		if ($region!='') {
			$label.= $region;
			$label.= "\r\n";
		}

		if ($country!='') {
			$country.= $country;
			$label.= "\r\n";
		}

		$this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] = quoted_printable_encode($label);
	}

	function setEmail($address) 
    {
		$this->properties['EMAIL;INTERNET'] = $address;
	}

	function setNote($note) 
    {
		$this->properties['NOTE;ENCODING=QUOTED-PRINTABLE'] = quoted_printable_encode($note);
	}

	function setURL($url, $type = '') 
    {
        // $type may be WORK | HOME
		$key = 'URL';
		if ($type!='') {
			$key.= ";$type";
		}

		$this->properties[$key] = $url;
	}

	function getVCard( $sitename ) 
    {
		$text 	= 'BEGIN:VCARD';
		$text	.= "\r\n";
		$text 	.= 'VERSION:2.1';
		$text	.= "\r\n";

		foreach( $this->properties as $key => $value ) {
			$text	.= "$key:$value";
			$text	.= "\r\n";
		}
		$text	.= 'REV:'. date( 'Y-m-d' ) .'T'. date( 'H:i:s' ). 'Z';
		$text	.= "\r\n";
		$text	.= 'MAILER: Workforce vCard for '. $sitename;
		$text	.= "\r\n";
		$text	.= 'END:VCARD';
		$text	.= "\r\n";

		return $text;
	}

    // added ability to set filename
	function setFilename( $filename ) 
    {
		 $this->filename = $filename .'.vcf'; 
	}

	// added ability to set position/title
	function setTitle( $title ) 
    {
		$title 	= trim( $title );
		$this->properties['TITLE'] 	= $title;
	}

	// added ability to set organisation/company
	function setOrg( $org ) 
    {
		$org 	= trim( $org );
		$this->properties['ORG'] = $org;
	}

	function getFileName() 
    {
		$this->filename;
		return $this->filename;
	}
}
?>