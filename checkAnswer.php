<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$poly1 = "((7*s+10))/(2*s^3+11*s^2+12*s+10)";
$poly2 = "((7*s+10))/(2*s^3+11*s^2+12*s+10)";
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
    
//$command = "echo 'is($poly1 = $poly2);' | maxima";
//$output = shell_exec($command);
//echo $output;

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
    
    
    return $latex;
}

$latex2 = "\begin{equation*} \dfrac{7s+10}{2s^3+11s^2+12s+10} \end{equation*}";

$maxima = latexToMaxima($latex2);


echo "<br>";
echo $maxima; 

echo "<br>";

require_once('config.php');

$sql = "SELECT * FROM test";
$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if any rows were returned
if (count($rows) > 0) {
    foreach ($rows as $row) {
      echo "RAW solution". $row['solution'] . "<br>";
      echo "Parsed solution".latexToMaxima($row['solution']) . "<br>";
      echo "Answer from student".$row['answer'] . "<br>";
      echo "Parsed answer".latexToMaxima($row['answer']) . "<br>";
    }
  } else {
    echo "No results found";
  }
?>

