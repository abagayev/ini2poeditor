<?php

/* 
 *  A script to import from INI file to poeditor.com
 *  Sections will be imported as tags, keys as terms and values as translations
 *  You will need php5 and curl to run this code
 * 
 *  This was developed by Anton Bagaiev in 2015 at http://dontgiveafish.com
 *  Send me a postcard if you want mailto:tony@dontgiveafish.com
 */

// config

$api_url = 'https://poeditor.com/api/';
$api_token = '';
$project_id = 0;

$languages = array(
    // language code => path to file
    // first language is language to get terms
    'uk' => 'uk_UA.ini',
    'ru' => 'ru_RU.ini',
);

// this is data teplates

$template_data = array(
    'api_token'=> $api_token,
    'id' => $project_id,
    'action' => '',
    'data' => array()
);

$template_term = array(
      'term' => '',
      'context' => '',
      'plural' => '',
      'comment' => '',
      'tags' => ''
);

$template_translation = array(
    'term' => array(
        'term' => '',
        'context' => ''
    ),
    'definition' => array(
        'forms' => array(),
        'fuzzy' => '0'
    )

);

// initialize curl

$curl = curl_init();
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_URL, $api_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);

// fill terms

$ini = parse_ini_file(array_values($languages)[0], true);
$terms = array();

foreach ($ini as $tag => $tag_frases) {
    foreach ($tag_frases as $frase_key => $frase_value) {
        $term = $template_term;
        $term['term'] = $frase_key;
        $term['tags'] = $tag;
        
        $terms[] = $term;
    }
}

$data = $template_data;
$data['action'] = 'add_terms';
$data['data'] = json_encode($terms);

curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
$result = curl_exec($curl);
echo "$result\n";

// fill languages

foreach ($languages as $language => $ini_filename) {

    $ini = parse_ini_file($ini_filename);

    $translations = array();

    foreach ($ini as $key => $value) {
        $translation = $template_translation;

        $translation['term']['term'] = $key;
        $translation['definition']['forms'][] = $value;

        $translations[] = $translation;

    }

    $data = $template_data;
    $data['action'] = 'update_language';
    $data['language'] = $language;
    $data['data'] = json_encode($translations);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($curl);
    echo "$result\n";

}
