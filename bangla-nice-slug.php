<?php

/*
  Plugin Name: Bangla Nice Slug
  Plugin URI:
  Description: Sanitize Bangla title for post slug.
  Author: জাহেদুল ইসলাম
  Author URI: http://www.banglatext.com/
  Version: 1.1
 */

#load_plugin_textdomain('bangla-nice-slug', false, dirname(plugin_basename(__FILE__)) . '/languages');

#register_activation_hook(__FILE__, 'bns_on_activation');

add_action('sanitize_title', 'bns_sanitize_title', 0);


/**
 * Sanitize title
 *
 * @param string $title
 * @return sanitized title
 */
function bns_sanitize_title($title)
{
	$bnphonetic = new bns_phonetic();
	$slug = $bnphonetic->phonetic($title);
	$slug = strtolower($slug);
	$slug = preg_replace('/\s+/', ' ', $slug);
	$slug = str_replace(' ', '-', $slug);
	$slug = preg_replace('/[^a-z0-9-_]/', '', $slug);
	$slug = preg_replace('/[-]+/', '-', $slug);
	$slug = trim($slug, '-');

	return $slug;
}
class bns_phonetic {
private $wordmap = array();
public function phonetic($text)
{
	#$text=html_entity_decode($text, null, "utf-8");
	$text = str_replace('।', '.', $text);
	$words=preg_split("/([\x{0980}-\x{09FF}]+)/u",$text,null,PREG_SPLIT_DELIM_CAPTURE);
	#print_r($words);
	$parts= count($words);
	for($i=0,$n=0; $i<$parts; $i=$i+2, $n++)
	{
	$text=$words[$i+1];
	if($this->bn_dic_translit($text)!=false){
		$output.=$words[$i] . $this->bn_dic_translit($text);
		continue;
	}
	$sub=substr($text, 0, 9);
	if($sub=="জ্ঞ")
		$text="gqY".substr($text, 9);
	elseif($sub=="ধ্ব")
		$text="dqhqw".substr($text, 9);
	$bnzukto=array("অ্যা","ংজ্ঞ","ক্ব","ক্ষ্ম","ক্ষ","ঙ্ক","ঙ্খ","ঙ্গ","ঙ্ঘ","চ্ছ্ব","জ্জ্ব","জ্ঞ","ঞ্চ","ঞ্ছ","ঞ্জ","ঞ্ঝ","ণ্ব","ত্ত্ব","ত্ব","ত্ম","থ্ব","দ্দ্ব","দ্ব","দ্ম","ধ্ব","ন্ত্ব","ন্দ্ব","ন্ব","ব্ব","ম্ব","র্ব","হ্ম","ত্‍","র্য","হ্ব","্য","্ব","ংগ","ংঘ","ড়","য়");
	$enzukto=array("a","nqg","kqk","kqk","kqk","Nqk","Nqkqh","Nqg","Nqg","cqhqCqh","jqj","gqg","nqcqh","nqCqh","nqj","nqjqh","nqn","tqt","tqt","tqt","tqt","dqd","dqd","dqd","dqhqd","nqt","nqd","nqn","bqb","mqb","rqb","mqm","tq","rqz","hqb","qy","qw","nqg","nqgqh","R","y");
	$bnchar=array("অ","আ","ই","ঈ","উ","ঊ","ঋ","এ","ঐ","ও","ঔ","ক","খ","গ","ঘ","ঙ","চ","ছ","জ","ঝ","ঞ","ট","ঠ","ড","ঢ","ণ","ত","থ","দ","ধ","ন","প","ফ","ব","ভ","ম","য","র","ল","শ","ষ","স","হ","ড়","ঢ়","য়","ৎ","ং","ঃ","ঁ","া","ি","ী","ু","ূ","ৃ","ে","ৈ","ো","ৌ","্‌","্","।","৳","০","১","২","৩","৪","৫","৬","৭","৮","৯");
	$enchar=array("o","qa","qi","qI","qu","qU","ri","qe","oi","qO","ou","k","kqh","g","gqh","Nqgq","c","cqh","j","jqh","Y","T","Tqh","D","Dqh","N","t","tqh","d","dqh","n","p","f","b","v","m","z","r","l","sqh","Sqh","s","h","R","Rqh","y","tq","nqgq","Hq","","a","i","I","u","U","qri","e","oi","O","ou","q","q",".","$","0","1","2","3","4","5","6","7","8","9");
//	$text=str_replace("Iy", "Iyo", $text);
	$text=str_replace($bnzukto, $enzukto, $text);
	$text=str_replace($bnchar, $enchar, $text);
	$text=preg_replace("/([bcdfghjklmnprstvwyz])q([aeiou])/i", "$1o$2", $text);
	$text=str_replace(array("Oya","Oye"), array("wa","we"), $text);
	$text=preg_replace("/([bcdfghjklmnprstvwyz])(?=[bcdfghjklmnprstvwyz])/i", "$1o", $text, -1, $count);
	#$text=preg_replace("/([bcdfghjklmnprstvwz])([bcdfghjklmnprstvwyz])/i", "$1o$2", $text);
	$text=preg_replace("/q([^haieouIUO](qh)?)$/", "q$1o", $text);
//	$text=str_replace(array("iqi"," "y", $text);
	$text=str_replace("q", null, $text);
	#echo $text[0]."\n";
	if($text[0]==$text[1])
		$text=substr($text, 1);
	$text=preg_replace("/^([^aeiou]h?)[bw]/i","$1",$text);
	$text=preg_replace("/(iy)$/i", "$1o", $text);
	$output.=$words[$i] . $text;
	}
	return $output;
}
/**
 *
 * @param type $message
 * @param type $errno
 */
function bn_dic_translit($word)
{
	
	if(isset($this->wordmap[$word])){
		return $this->wordmap[$word];
	}
	else {
		return false;
	}
}
function bns_phonetic(){
	$this->wordmap = unserialize(file_get_contents(__DIR__ .'/bnwordmap.dat'));
}
}
