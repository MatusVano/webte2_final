<?php
function checkAns($poly1,$poly2){
  $poly1 = latexToMaxima($poly1);
  $poly2 = latexToMaxima($poly2);
  
  echo $poly1;
  echo $poly2;
  $maxima_executable = '/usr/bin/maxima'; // cesta k maxime

  $maxima_command = sprintf('(%s) - (%s);', $poly1, $poly2);
  
  $maxima_output = shell_exec($maxima_executable . ' -b -q -r "' . $maxima_command . '"');
  echo $maxima_output;
  $matches = [];
  preg_match('/\(%o\d+\)\s*(-?\d+\.?\d*)/', $maxima_output, $matches);
  $outcome = isset($matches[1]) ? floatval($matches[1]) : null;
  
  echo $outcome;
  
  if ($outcome == "0") {
      echo "The expressions are equal"; //zapise plny počer
  } else {
      echo "The expressions are not equal"; //zapise 0
  }
}

function latexToMaxima($latex) {
    
    $latex = str_replace('\begin{equation*}', '', $latex);
    $latex = str_replace('\end{equation*}', '', $latex); 
    $latex = str_replace('\left', '', $latex);
    $latex = str_replace('\right', '', $latex);   
    $latex = str_replace('\frac{', "(", $latex);
    $latex = str_replace("}{", ")/(", $latex);
    $latex = str_replace("}", ")", $latex);
    $latex = str_replace('\dfrac', '', $latex);
    $latex = str_replace('{', '(', $latex);
    $latex = str_replace('}', ')', $latex);
    $latex = str_replace('\\', '', $latex);
    $latex = str_replace('\cdot', '*', $latex);
    $latex = preg_replace('/([0-9]+)s/', '$1*s', $latex);
    $latex = preg_replace('/([0-9]+)t/', '$1*t', $latex);
    $latex = preg_replace('/([^a-zA-Z0-9_])e/', '$1*e', $latex);
    $latex = str_replace('(t-6)', '*(t-6)', $latex);
    $latex = str_replace('(t-4)', '*(t-4)', $latex);
    $latex = str_replace('(t-7)', '*(t-7)', $latex);
      
    //$latex = str_replace('\eta', '', $latex);
    
    return $latex;
}
?>

