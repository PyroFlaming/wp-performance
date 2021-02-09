<?php

namespace PerformanceBoost;

use CSSFromHTMLExtractor\CssFromHTMLExtractor as ExtractorExtension;

class Booster
{
    public function __construct() {
        // add plugin page to turn on and off specific optimizations.

        // add listener for printing page. and apply turn on optimizations.
        // - check site with some type of cache plugins is all ok.
        add_filter('booster_filter_output_content', function($html) {
            if(!is_admin()){
                return self::boost_optimize_page($html);
            }else {
                return $html;
            }
        });
    }

    private function boost_page_set() {

    }

    private function boost_optimize_page($html){

        var_dump('PAGE OPTIMIZE');
        $html = self::boost_js_extractor($html);
        $html = self::boost_css_extractor($html);
        $html = self::append_plugin_script($html);
        return $html;
    }

    private function append_plugin_script($html) {
        $base_url = BASE_URL;
        return str_replace('</head>','<script src="'.$base_url.'assets/scripts-loader.js" deffer="true"></script></head>', $html);
    }

    private function boost_css_extractor($html){
        
        try {
            $optimize_html = $html;

            $critical_css_extractor = new ExtractorExtension();       
            // preg_match_all('\<link .+href="\..+css.+"\>', $optimize_html, $match);
            preg_match_all('<link.*(href=[\"|\']{1}(\S+\.css?\S+)[\"|\']{1}).*>', $optimize_html, $styles_links, PREG_SET_ORDER);
            
            foreach($styles_links as $index => $style) {
                // todo get critical css and append it to html
                $template_style = str_replace('link', 'template pf-boost-id="boost-style-'. $key .'" ', $style[0]);
                $template_style = str_replace('href=', 'pf-boost-href=', $template_style);
                
                $file_path = str_replace(home_url() .'/', get_home_path(), $style[2]);

                // check for query params in end ?ver=1.3.5.6... etc
                if(($query_params_index = strpos($file_path, '?')) !== false){
                    var_dump($query_params_index);
                    $file_path = substr($file_path, 0, $query_params_index);
                }

                // try to reade file directly from file system
                if (($content = @file_get_contents($file_path)) !== false) {
                    // change the link only if we get css with no problems.
                    $optimize_html = str_replace($style[0], $template_style . '</template>', $optimize_html);
                    $critical_css_extractor->addBaseRules($content);
                    continue;
                }

                // fail back for not found files.
                if (($content = @file_get_contents($style[2])) !== false) {
                    // change the link only if we get css with no problems.
                    $optimize_html = str_replace($style[0], $template_style . '</template>', $optimize_html);
                    $critical_css_extractor->addBaseRules($content);
                    continue;
                }
            }

            $critical_css_extractor->addHtmlToStore($optimize_html);

            $critical_css =  $critical_css_extractor->buildExtractedRuleSet();

            $optimize_html = str_replace('</head>', '<style data-boost-critical-css="true">' . $critical_css . '</style></head>', $optimize_html);

            $html = $optimize_html;
        } catch (\Throwable $th) {
            throw $th;
        }

        return $html;
    }

    private function boost_js_extractor($html){
        try {
            $optimize_html = $html;

            $jspatern = '/<script\b[^>]*>([\s\S]*?)<\/script>/m';
            // $jspatern = "/\<script(.*?)?\>(.|\\n)*?\<\/script\>/i";

            preg_match_all($jspatern, $optimize_html, $scripts_tags, PREG_SET_ORDER, 0);
            $inline_scripts = array();
            // var_dump($scripts_tags);

            foreach ($scripts_tags as $key => $script) {
                $mod_script = str_replace($script[1], '', $script[0]);
                $mod_script = str_replace('script', 'template', $mod_script);
                $mod_script = str_replace('<template', '<template pf-boost-id="boost-script-' . $key . '" ', $mod_script);
                
                // getter src attr content.
                preg_match('/src=(\'|")(.*?)(\'|")/m', $mod_script , $srcAttr);
                $srcAttr = $srcAttr[0];
                if ($srcAttr) {
                    $mod_script = str_replace('src=','pf-boost-src=',$mod_script);
                } else if ($script[1]){
                    array_push($inline_scripts, array('pf-boost-id'=> 'boost-script-'. $key,'content'=>$script[1]));
                }

                $optimize_html = str_replace($script[0],$mod_script, $optimize_html);
            }
            
            $optimize_html = str_replace('</head>','<script>var boostInlineScripts=' . json_encode($inline_scripts) . '</script></head>', $optimize_html);
            $html = $optimize_html;
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $html;
    }
}
