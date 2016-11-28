<?php 

/** A templating enagine*/
class Template {
    // Stores the location of the template file
    public $template_file;

    // Store the entries to be parsed in the template 
    public $entries = array();
    
    // Stores the contents of the template file
    private $_template;

    // Write a public method to output the result of the templating engine
    /* @param array $extra Extra data for the header/footer
     * */
    public function generate_markup($extra = array()) {
        $this->_load_template();
        return $this->_parse_template($extra);
    }
    
    // Loads a template file with which markup should be formatted
    private function _load_template(){
        // Load the template...
        // Check for a custom template
        $template_file = "assets/templates/".$this->template_file;
        if (file_exists($template_file) && is_readable($template_file)) {
            $path = $template_file;
        }
        else if (file_exists($default_file = "assets/templates/default.inc") && is_readable($default_file)) {
            // Look for a system template
            $path = $default_file;
        }
        else {
            // If the default template is missing, throw an error
            throw new Exception("No default template found");
        }

        // Load the contents of the file and return them
        $this->_template = file_get_contents($path);
    }   
    
    /** Separates the template into header, loop, and footer for parsing
     *  @param array $extra Addition content for the header/footer
     *  @return string The entry markup
     * */
    private function _parse_template($extra = NULL) {
        // Create an alias of the template file property to save space
        $template = $this->_template;

        // Remove any PHP-Style comments from the template
        $comment_pattern = array('#/\*.*?\*/#', '#(?<!:)//.*#');
        $template = preg_replace($comment_pattern, NULL, $template);

        // Extract the main entry loop from the file
        $pattern = '#.*{loop}(.*?){/loop}.*#is'; 
        $entry_template = preg_replace($pattern, "$1", $template);
       
        // Extract the header from the template if one exists
        $header = trim(preg_replace('/^(.*)?{loop.*$/is', "$1", $template)); 
        if( $header===$template ) { 
            $header = NULL; 
        }
       
        // Extract the footer from the template if one exists
        $footer = trim(preg_replace('#^.*?{/loop}(.*)$#is', "$1", $template)); 
        if( $footer===$template ) { 
            $footer = NULL; 
        }
        
        // Define a regex to match an template tag
        $tag_pattern = '/{(\w+)}/';

        // Curry the function that will replace the tags with entry data
        $callback = $this->_curry('Template::replace_tags', 2);
        // Process each entry and insert its values into the loop
        $markup = NULL;
        for ($i = 0, $c = count($this->entries); $i < $c; ++$i) {

            $markup .= preg_replace_callback($tag_pattern, $callback(serialize($this->entries[$i])), $entry_template);

        }

        // If extra data was passed to fill in the header/footer, parse it here
        if (is_object($extra)) {
            foreach ($extra as $key => $props) {
                $$key = preg_replace_callback($tag_pattern, $callback(serialize($extra->$key)), $$key);
            }
        }

        // Return the formatted entries with the header and footer

        return $header . $markup . $footer;
    }

    // Write a static method to replace the template tags with entry data
    public static function replace_tags($entry, $matches) {
        // Unserialize the object
        $entry = unserialize($entry);
        // Make sure the template tag has matching array element
        if (property_exists($entry, $matches[1])) {
            // Grab the value from the Entry object 
            return $entry->{$matches[1]};
        }
        else {
            // Otherwise, simple return the tag as is 
            return "{".$matches[1]."}";
        }
    }

    // Write a private curring function to facilitate tag replacement
    private function _curry($function, $num_args) {
        $code = "\$args = func_get_args(); if (count(\$args) >= $num_args) { return call_user_func_array('$function', \$args);} \$args = var_export(\$args,  true); return create_function('', '\$a = func_get_args(); \$z = ' . \$args . '; \$a = array_merge(\$z, \$a); return call_user_func_array(\'$function\', \$a);');";

        $ret = create_function('', $code);

        return $ret;
    }
}

?>
