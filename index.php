<?php

/**
 * index.php
 * The base page.
 */

$texts_file = file_exists('custom/texts.json') ? 'custom/texts.json' : 'default/texts.json';
$texts_json = trim(file_get_contents($texts_file));
$texts = json_decode($texts_json);
$css_file = file_exists('custom/style.css') ? 'custom/style.css' : 'default/style.css';
$template_file = file_exists('custom/template.php') ? 'custom/template.php' : 'default/template.php';

require $template_file;
