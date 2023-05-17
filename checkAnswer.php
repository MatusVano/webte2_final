<?php
    $poly1 = "3/4";
    $poly2 = "0.75";
    $maxima_executable = '/usr/bin/maxima'; // cesta k maxime

    $maxima_command = sprintf('(%s) - (%s);', $poly1, $poly2);

    $maxima_output = shell_exec($maxima_executable . ' -b -q -r "' . $maxima_command . '"');

    $matches = [];
    preg_match('/\(%o\d+\)\s*(-?\d+\.?\d*)/', $maxima_output, $matches);
    $outcome = isset($matches[1]) ? floatval($matches[1]) : null;

    echo $outcome;

    if ($outcome == "0") {
        echo "The expressions are equal";
    } else {
        echo "The expressions are not equal";
    }
    
    $command = "echo 'is($poly1 = $poly2);' | maxima";

    $output = shell_exec($command);

    echo $output;

    function latexToMaxima($latex) {
        // Replace LaTeX commands with Maxima equivalents
        $maxima = str_replace("\\frac{", "(", $latex);
        $maxima = str_replace("}{", ")/(", $maxima);
        $maxima = str_replace("}", ")", $maxima);
    
        return $maxima;
    }
    
    $latex = "y = \\frac{a}{b} + c";
    $maxima = latexToMaxima($latex);
    echo $maxima;  // Outputs: y = (a)/(b) + c

?>