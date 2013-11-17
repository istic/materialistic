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
      return '&pound;'.number_format($value, 2);

    case "USD":
      return '$'.number_format($value, 2);

    case "CAD":
      return 'CA$'.number_format($value, 2);

    default:
      return number_format($value, 2).$currency;
  }
}

function array_median($array) {
  // perhaps all non numeric values should filtered out of $array here?
  $iCount = count($array);
  if ($iCount == 0) {
    return 0;
  }
  // if we're down here it must mean $array
  // has at least 1 item in the array.
  $middle_index = floor($iCount / 2);
  sort($array, SORT_NUMERIC);
  $median = $array[$middle_index]; // assume an odd # of items
  // Handle the even case by averaging the middle 2 items
  if ($iCount % 2 == 0) {
    $median = ($median + $array[$middle_index - 1]) / 2;
  }
  return $median;
}

function array_average($array){
  $sum = count($array);
  if($sum){
    return array_sum($array)/$sum;
  } else {
    return 0;
  }
}
