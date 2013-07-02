<?php
/**
* @version $Id: vcard.php 10381 2008-06-01 03:35:53Z pasamio $
* Modified PHP vCard class v2.0
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
class vCard {
	var $properties;
	var $filename;
	var $newLine	= "\n";

	function setPhoneNumber($number, $type='') {
	// type may be PREF | WORK | HOME | VOICE | FAX | MSG | CELL | PAGER | BBS | CAR | MODEM | ISDN | VIDEO or any senseful combination, e.g. "PREF;WORK;VOICE"
		$key = 'TEL';
		if ($type!='') {
			$key .= ';'. $type;
		}
		$key.= ';ENCODING=QUOTED-PRINTABLE';

		$this->properties[$key] = $this->quoted_printable_encode($number);
	}

	// UNTESTED !!!
	function setPhoto($type, $photo) { // $type = "GIF" | "JPEG"
		$this->properties["PHOTO;TYPE=$type;ENCODING=BASE64"] = base64_encode($photo);
	}

	function setFormattedName($name) {
		$this->properties['FN'] = $this->quoted_printable_encode($name);
	}

	function setName($family='', $first='', $additional='', $prefix='', $suffix='') {
		$this->properties['N'] = "$family;$first;$additional;$prefix;$suffix";
		$this->filename = "$first%20$family.vcf";
		if ($this->properties['FN']=='') {
			$this->setFormattedName(trim("$prefix $first $additional $family $suffix"));
		}
	}

	function setBirthday($date) { // $date format is YYYY-MM-DD
		$this->properties['BDAY'] = $date;
	}

	function setAddress($postoffice='', $extended='', $street='', $city='', $region='', $zip='', $country='', $type='HOME;POSTAL') {
	// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$key = 'ADR';
		if ($type!='') {
			$key.= ";$type";
		}

		$key.= ';ENCODING=QUOTED-PRINTABLE';
		$this->properties[$key] = $this->encode($name).';'.$this->encode($extended).';'.$this->encode($street).';'.$this->encode($city).';'.$this->encode($region).';'.$this->encode($zip).';'.$this->encode($country);

		if ($this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] == '') {
			//$this->setLabel($postoffice, $extended, $street, $city, $region, $zip, $country, $type);
		}
	}

	function setLabel($postoffice='', $extended='', $street='', $city='', $region='', $zip='', $country='', $type='HOME;POSTAL') {
		$label = '';
		if ($postoffice!='') {
			$label.= $postoffice;
			$label.= $this->newLine;
		}

		if ($extended!='') {
			$label.= $extended;
			$label.= $this->newLine;
		}

		if ($street!='') {
			$label.= $street;
			$label.= $this->newLine;
		}

		if ($zip!='') {
			$label.= $zip .' ';
		}

		if ($city!='') {
			$label.= $city;
			$label.= $this->newLine;
		}

		if ($region!='') {
			$label.= $region;
			$label.= $this->newLine;
		}

		if ($country!='') {
			$country.= $country;
			$label.= $this->newLine;
		}

		$this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] = $this->quoted_printable_encode($label);
	}

	function setEmail($address) {
		$this->properties['EMAIL;INTERNET'] = $address;
	}

	function setNote($note) {
		$this->properties['NOTE;ENCODING=QUOTED-PRINTABLE'] = $this->quoted_printable_encode($note);
	}

	function setURL($url, $type='') {
	// $type may be WORK | HOME
		$key = 'URL';
		if ($type!='') {
			$key.= ";$type";
		}

		$this->properties[$key] = $url;
	}

	function getVCard() {
		$text = 'BEGIN:VCARD';
		$text.= $this->newLine;
		$text.= 'VERSION:2.1';
		$text.= $this->newLine;

		foreach($this->properties as $key => $value) {
			$text.= "$key:$value\r\n";
		}

		$text.= 'REV:'. date('Y-m-d') .'T'. date('H:i:s') .'Z';
		$text.= $this->newLine;
		$text.= 'MAILER:PHP vCard class by Kai Blankenhorn';
		$text.= $this->newLine;
		$text.= 'END:VCARD';
		$text.= $this->newLine;

		return $text;
	}

	function getFileName() {
		return $this->filename;
	}
	
		
	function encode($string) {
		return $this->escape($this->quoted_printable_encode($string));
	}
	
	function escape($string) {
		return str_replace(';',"\;",$string);
	}
	
	// taken from PHP documentation comments
	function quoted_printable_encode($input, $line_max = 76) {
		$hex 		= array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		$lines 		= preg_split("/(?:\r\n|\r|\n)/", $input);
		$eol 		= $this->newLine;
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
				} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always $this->encode "\t", which is *not* required
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
?>