<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function shuffle_assoc($list) { 
  if (!is_array($list)) return $list; 

  $keys = array_keys($list); 
  shuffle($keys); 
  $random = array(); 
  foreach ($keys as $key) 
	$random[$key] = $list[$key]; 

  return $random; 
}

/**
 * Modifies a string to remove all non ASCII characters and spaces.
 */
function slugify($text)
{
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
 
    // trim
    $text = trim($text, '-');
 
    // transliterate
    if (function_exists('iconv'))
    {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }
 
    // lowercase
    $text = strtolower($text);
 
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
 
    if (empty($text))
    {
        return 'n-a';
    }
 
    return $text;
}

function currency_symbol($currency){

  switch($currency){
    case "GBP":
      return '&pound;';

    case "USD":
      return '$';

    case "USD":
      return 'CA$';

    default:
      return '?';
  }
}

function view_currency($currency, $value){

  switch($currency){
    case "GBP":
      return '&pound;'.number_format($value);

    case "USD":
      return '$'.number_format($value);

    case "USD":
      return 'CA$'.number_format($value);

    default:
      return number_format($value).$currency;
  }
}
