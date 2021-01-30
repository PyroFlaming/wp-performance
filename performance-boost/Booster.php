<?php

namespace PerformanceBoost;

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

        return $html;
    }

    private function boost_css_extractor($html){

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
                $mod_script = str_replace('script', 'template', $script[0]);
                $mod_script = str_replace('<template', '<template pf-boost-id="boost-script-' . $key . '" ', $mod_script);
                
                // getter src attr content.
                preg_match('/src=(\'|")(.*?)(\'|")/m', $mod_script , $srcAttr);
                $srcAttr = $srcAttr[0];
                if ($srcAttr) {
                    $mod_script = str_replace('src=','pf-boost-src=',$mod_script);
                } else if ($script[1]){
                    array_push($inline_scripts, array('pf-boost-id'=> 'boost-script-'. $key,'content'=>$script[1]));
                    $mod_script = str_replace($script[1], '', $mod_script);
                }

                $optimize_html = str_replace($script[0],$mod_script, $optimize_html);
                $optimize_html = str_replace('</head>','<script>var boostInlineScripts=' . json_encode($inline_scripts) . '</script></head>', $optimize_html);
            }

            $html = $optimize_html;
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $html;
    }
}
