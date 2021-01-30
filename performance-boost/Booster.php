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

        return $html;
    }

    private function boost_css_extractor($html){

        return $html;
    }

    private function boost_js_extractor($html){

        try {
            //code...
            $jspatern = '/<script\b[^>]*>([\s\S]*?)<\/script>/m';

            preg_match_all($jspatern, $html, $matches_js, PREG_SET_ORDER, 0);
            $scripts = array();

            foreach ($matched_js as $key => $script) {
                $mod_script = str_replace('script', 'template', $script[0]);
                $mod_script = str_replace('<template', '<template data-boost-id="boost-script-' . $key . '" ', $mod_script);
                
                // getter src attr content.
                preg_match('/src="(.*?)"/', $mod_script , $srcAttr);

                if ($srcAttr) {

                } else if ($script){

                }

            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $html;
    }
}
