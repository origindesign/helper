<?php

/**
 * @file
 * Contains \Drupal\helper\Twig\ImageHelper.
 */

namespace Drupal\helper\Twig;


/**
 * Provides the ImageHelper function within Twig templates.
 */
class ImageHelper extends \Twig_Extension {

    
    
    /**
     * {@inheritdoc}
     */
    public function getName() {      
        return 'image_helper';
    }

    
    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction(
                    'displayBackgroundImg', 
                    array($this, 'displayBackgroundImg'), 
                    array('is_safe' => array('html') ) 
                )
        );
    }


    /**
     * @param string $srcset
     *  String containing all rulesets for responsive image
     * @return string
     *  return an html string containing the styles tag on top and the background wrapper
     */
    public function displayBackgroundImg ($srcset ){

        $arrSrcset = explode(", ", $srcset);
        $style = "<style>";
        krsort($arrSrcset);

        foreach ($arrSrcset as $src){
            $arrSrc = explode(" ", $src);
            if (!isset($firstImg)) {
                $style .= '.bgImg { display: block; background-image:url('.$arrSrc[0].'); }';
                $firstImg = true;
            }
            $width = str_replace("w", "", $arrSrc[1]);
            $style .= '@media (max-width: '.$width.'px) { .bgImg { display: block; background-image:url('.$arrSrc[0].'); } }';
        }

        $style .= "</style>";
        $html = '<div class="bgImg"></div>';

        return $style.$html;
       
    }
    
    

}
