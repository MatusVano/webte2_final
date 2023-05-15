<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function parseFile($file)
{
    $file = explode(":", $file);
    $file_id = $file[0];
    $file_path = $file[1];

    $file_contents = file_get_contents($file_path);

    $pattern = '/\\\\section\*\{(.*?)\}.*?\\\\begin\{task\}(.*?)\\\\end\{task\}.*?\\\\begin\{solution\}(.*?)\\\\end\{solution\}/s';

    preg_match_all($pattern, $file_contents, $matches, PREG_SET_ORDER);

    $tasks = array();
    foreach ($matches as $match) {
        $section = $match[1];
        $task_content = trim($match[2]);
        $solution = trim($match[3]);

        // Extract image filename, if present
        $image_pattern = '/\\\\includegraphics\{(.*?)\}/';
        preg_match($image_pattern, $task_content, $image_match);
        $image = $image_match[1] ?? null;

        // Remove image LaTeX command from task content
        if ($image !== null) {
            $task_content = trim(preg_replace($image_pattern, '', $task_content));
        }

        $tasks[] = array(
            'section' => $section,
            'task' => $task_content,
            'solution' => $solution,
            'image' => $image
        );
    }

    return $tasks;
}
