<?php

// Error reporting is turned up to 11 for the purposes of this demo 
ini_set("display_errors",1); 
ERROR_REPORTING(E_ALL); 

// Exception handling 
set_exception_handler("exception_handler"); 
function exception_handler( $exception ) 
{ 
    echo $exception->getMessage(); 
} 

?>
<!DOCTYPE html><html lang = "en">
<head>
<meta http-equiv = "Content-Type" content = "text/html;charset=utf-8" />
<!-- Meta information -->
<title>Demo: Roll Your Own Templating System in PHP</title>
<meta name = "description" content = "A demo of the templating system by Jason Lengstorf" />

</head>
<body>
<?php

// Load the Template class 
require_once "system/class.template.inc.php"; 
      
// Create a new instance of the Template class 
$template = new Template; 
       
// Set the testing template file location 
//$template->template_file = "template-test.inc"; 
$template->template_file = "entry-list.inc"; 
//$template->entries[] = (object) array( 'test' => 'This was inserted using template tags!' );
$template->entries = load_envato_blog_posts('audiojungle');

//$extra = (object) array('header' => (object) array('header_stuff' => 'Some extra content'), 'footer' => (object) array('footerStuff' => 'More extra content.'));
           
// Output the template markup 
echo $template->generate_markup(); 

//echo '<pre>', print_r(load_envato_blog_posts(), TRUE), '</pre>';

/** Loads entries from the Envato API for a given stie */
?> </body></html>  <?php
function load_envato_blog_posts($site="themforest") {
    return array(
        0 => (object)[
            "title" => "Interview With \"The Man\": Jeffrey Way",
            "url" => "http://feedproxy.google.com/~r/themeforest/~3/5oZEgpMCn3Q/",
            "site" => "themeforest.net",
            "posted_at" => "2009-12-19"
        ],
        1 => (object)[
            "title" => "ThemeForest Week in Review",
            "url" => "http://feedproxy.google.com/~r/themeforest/~3/fAiw8Xw1Q8U/",
            "site" => "themeforest.net",
            "posted_at" => "2009-12-19"
        ]
    );        
 
/*// Set up the request for the Envato API
    $url = "http://marketplace.envato.com/api/edge/blog-posts:".$site.".json";
    
    // Initialize an empty array to store entries 
    $entries = array();

    // Load the data from the API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $ch_data = curl_exec($ch);
    curl_close($ch);
    
    // If entires were returned, load them into the array
    if (!empty($ch_data)) {
        // Convert the JSON into an array of entry objects
        $json_data = json_decode($ch_data, TRUE);
        
        foreach ($json_data["blog-posts"] as $entry) {
            $entries[] = (object) $entry;
        }

        return $entries;
    }
    else {
        die("Something went wrong with the API request!");
    }    
*/
}

?>
