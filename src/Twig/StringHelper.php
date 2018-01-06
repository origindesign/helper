<?php

/**
 * @file
 * Contains \Drupal\helper\Twig\BackgroundHelper.
 */

namespace Drupal\helper\Twig;


use Drupal\Core\Datetime\DrupalDateTime;



/**
 * Provides the BackgroundHelper function within Twig templates.
 */
class StringHelper extends \Twig_Extension {


    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'string_helper';
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction(
                'uniqueString',
                array($this, 'uniqueString'),
                array('is_safe' => array('html') )
            ),
            new \Twig_SimpleFunction(
                'getVideoEmbed',
                array($this, 'getVideoEmbed'),
                array('is_safe' => array('html') )
            ),
            new \Twig_SimpleFunction(
                'getTermIdFromURL',
                array($this, 'getTermIdFromURL'),
                array('is_safe' => array('html') )
            ),
            new \Twig_SimpleFunction(
                'termsToClasses',
                array($this, 'termsToClasses'),
                array('is_safe' => array('html') )
            ),
            new \Twig_SimpleFunction(
                'displayFromToDates',
                array($this, 'displayFromToDates'),
                array('is_safe' => array('html') )
            ),
            new \Twig_SimpleFunction(
                'displayFromToTime',
                array($this, 'displayFromToTime'),
                array('is_safe' => array('html') )
            ),
            new \Twig_SimpleFunction(
                'fieldToTime',
                array($this, 'fieldToTime'),
                array('is_safe' => array('html') )
            ),
            new \Twig_SimpleFunction(
                'isTitleLong',
                array($this, 'isTitleLong'),
                array('is_safe' => array('html') )
            )


        );
    }


    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('machine', array($this, 'machineFilter')),
            new \Twig_SimpleFilter('obfuscate', array($this, 'obfuscateEmail')),
        );
    }



    public function machineFilter($string)
    {
        $value = str_replace('&amp; ','',$string);
        $value = str_replace(' ','-', strtolower($value));
        $value = preg_replace('@[^a-z0-9-]+@','', $value);
        return $value;
    }


    function obfuscateEmail($string)
    {
        // Casting $string to a string allows passing of objects implementing the __toString() magic method.
        $string = (string) $string;

        // Safeguard string.
        $safeguard = '$%$!!$%$';

        // Safeguard several stuff before parsing.
        $prevent = array(
            '|<input [^>]*@[^>]*>|is', // <input>
            '|(<textarea(?:[^>]*)>)(.*?)(</textarea>)|is', // <textarea>
            '|(<head(?:[^>]*)>)(.*?)(</head>)|is', // <head>
            '|(<script(?:[^>]*)>)(.*?)(</script>)|is', // <script>
        );
        foreach ($prevent as $pattern) {
            $string = preg_replace_callback($pattern, function ($matches) use ($safeguard) {
                return str_replace('@', $safeguard, $matches[0]);
            }, $string);
        }

        // Define patterns for extracting emails.
        $patterns = array(
            '|\<a[^>]+href\=\"mailto\:([^">?]+)(\?[^?">]+)?\"[^>]*\>(.*?)\<\/a\>|ism', // mailto anchors
            '|[_a-z0-9-]+(?:\.[_a-z0-9-]+)*@[a-z0-9-]+(?:\.[a-z0-9-]+)*(?:\.[a-z]{2,3})|i', // plain emails
        );

        foreach ($patterns as $pattern) {
            $string = preg_replace_callback($pattern, function ($parts) use ($safeguard) {
                // Clean up element parts.
                $parts = array_map('trim', $parts);

                // ROT13 implementation for JS-enabled browsers
                $js = '<script type="text/javascript">
Rot13={map:null,convert:function(e){Rot13.init();var t="";for(i=0;i<e.length;i++){var n=e.charAt(i);t+=n>="A"&&n<="Z"||n>="a"&&n<="z"?Rot13.map[n]:n}return t},init:function(){if(Rot13.map!=null)return;var e=new Array;var t="abcdefghijklmnopqrstuvwxyz";for(i=0;i<t.length;i++)e[t.charAt(i)]=t.charAt((i+13)%26);for(i=0;i<t.length;i++)e[t.charAt(i).toUpperCase()]=t.charAt((i+13)%26).toUpperCase();Rot13.map=e},write:function(e){document.write(Rot13.convert(e))}};Rot13.write(' . "'" . str_rot13($parts[0]) . "'" . ');</script>';

                // Reversed direction implementation for non-JS browsers
                if (stripos($parts[0], '<a') === 0) {
                    // Mailto tag; if link content equals the email, just display the email, otherwise display a formatted string.
                    $nojs = ($parts[1] == $parts[3]) ? $parts[1] : (' > ' . $parts[1] . ' < ' . $parts[3]);
                } else {
                    // Plain email; display the plain email.
                    $nojs = $parts[0];
                }
                $nojs = '<noscript><span style="unicode-bidi:bidi-override;direction:rtl;">' . strrev($nojs) . '</span></noscript>';

                // Safeguard the obfuscation so it won't get picked up by the next iteration.
                return str_replace('@', $safeguard, $js); // . $nojs
            }, $string);
        }

        // Revert all safeguards.
        return str_replace($safeguard, '@', $string);
    }


    /**
     * @param $from
     * @param $to
     * @param string $d
     * @param string $m
     * @param string $order
     * @return string
     */
    public function displayFromToDates ($from, $to, $d = 'j', $m = 'M', $order = 'm-d' ){

        $fromDate = new DrupalDateTime( $from );

        // If there is a to date
        if ($to){

            $toDate = new DrupalDateTime( $to );

            // Return from date only if days are the same
            if ( $fromDate->format("Y-m-d") == $toDate->format("Y-m-d") ){
                if($order == 'm-d'){
                    return '<span class="date">
                                <span class="month">'.$fromDate->format($m).'</span>
                                <span class="day">'.$fromDate->format($d).'</span>
                            </span>';
                }else{
                    return '<span class="date">
                                <span class="day">'.$fromDate->format($d).'</span>
                                <span class="month">'.$fromDate->format($m).'</span>                           
                            </span>';
                }
            }else{

                // If month are the same
                if ( $fromDate->format("Y-m") == $toDate->format("Y-m") ){
                    if($order == 'm-d'){
                        return '<span class="date">
                                    <span class="month">'.$fromDate->format($m).'</span>
                                    <span class="day">'.$fromDate->format($d).'</span>
                                    -
                                    <span class="day">'.$toDate->format($d).'</span>
                                </span>';
                    }else{
                        return '<span class="date">
                                    <span class="day">'.$fromDate->format($d).'</span>
                                    -
                                    <span class="day">'.$toDate->format($d).'</span>
                                    <span class="month">'.$fromDate->format($m).'</span>
                                </span>';
                    }



                }else{

                    // If months are different
                    if($order == 'm-d'){
                        return '<span class="date">
                                    <span>
                                        <span class="month">'.$fromDate->format($m).'</span>
                                        <span class="day">'.$fromDate->format($d).'</span>
                                    </span>                               
                                    <span class="to">-</span>
                                    <span>                                
                                        <span class="month">'.$toDate->format($m).'</span>
                                        <span class="day">'.$toDate->format($d).'</span>
                                    </span>
                                </span>';
                    }else{
                        return '<span class="date"> 
                                    <span>
                                        <span class="day">'.$fromDate->format($d).'</span>
                                        <span class="month">'.$fromDate->format($m).'</span>
                                    </span>                                
                                    <span class="to">-</span>
                                    <span>                                  
                                        <span class="day">'.$toDate->format($d).'</span>
                                        <span class="month">'.$toDate->format($m).'</span>
                                    </span>
                                </span>';
                    }
                }
            }

        }
        if($order == 'm-d'){
            return '<span class="date">
                        <span class="month">'.$fromDate->format($m).'</span>
                        <span class="day">'.$fromDate->format($d).'</span>
                    </span>';
        }else{
            return '<span class="date">                        
                        <span class="day">'.$fromDate->format($d).'</span>
                        <span class="month">'.$fromDate->format($m).'</span>
                    </span>';
        }

    }


    /**
     * @param string $string
     * @return string return a unique ID based on string
     */
    public function uniqueString ($string ){

        $cleanStr = $this->machineFilter($string);
        $cleanStr = substr($cleanStr, 0,25);
        //$arrStr = str_split($cleanStr, );

        return $cleanStr;

    }


    /**
     * @param $url
     * @return string embed code
     */
    public function getVideoEmbed ( $url ){

        if(strpos($url, 'you') !== false){
            $embed = '<iframe width="854" height="480" src="'.$url.'?autoplay=1&rel=0&modestbranding=1&autohide=1&showinfo=0" frameborder="0" allowfullscreen></iframe>';
        }

        if(strpos($url, 'vimeo') !== false){
            $json = file_get_contents("https://vimeo.com/api/oembed.json?url=".$url);
            $json = json_decode($json,true);
            $embed = '<iframe src="https://player.vimeo.com/video/'.$json['video_id'].'?autoplay=1" width="640" height="320" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        }

        if(isset($embed) && $embed != ''){
            return $embed;
        }else{
            return false;
        }

    }



    /**
     * @param $termUrl
     * @return string
     */
    public function getTermIdFromURL ( $termUrl ){
        $termUrlArr = explode('/',$termUrl);
        return end($termUrlArr);
    }




    /**
     * Transform a Term Reference field into a string of classes
     * @param $field_terms
     * @return array
     */
    public function termsToClasses ( $field_terms ){

        $classes = array();

        foreach ( $field_terms as $term){
            $classes[] = $this->machineFilter('term-'.$term["#plain_text"]);
        }

        return $classes;

    }




    public function fieldToTime ( $field_date ){

        $formatDate = '';

        //return $field_date.0[start_date)


        if ( isset($field_date['start_date']) ){
            $startTime = new DrupalDateTime($field_date['start_date']['#attributes']['datetime']);
            $startTime->setTimezone(new \DateTimeZone('America/New_York'));
            $formatDate = $startTime->format("g:ia");
        }

        if ( isset($field_date['end_date']) ){
            $endTime = new DrupalDateTime($field_date['end_date']['#attributes']['datetime']);
            $endTime->setTimezone(new \DateTimeZone('America/New_York'));
            if ( $endTime->format("g:ia") !== $startTime->format("g:ia")){
                $formatDate .= " to ". $endTime->format("g:ia");
            }
        }

        return $formatDate;



    }



    /**
     * @param $from int datetime
     * @param $to int datetime
     * @return string return time in a from to format
     */
    public function displayFromToTime ($from, $to ){

        $fromDate = new DrupalDateTime( $from );
        //$fromDate = DrupalDateTime::createFromTimestamp( (int)$from );

        // If there is a to date
        if ($to){

            $toDate = new DrupalDateTime( $to );
            //$toDate = DrupalDateTime::createFromTimestamp( (int)$to );

            if($fromDate != $toDate){
                return $fromDate->format("g:ia")." to ".$toDate->format("g:ia");
            }

        }

        return $fromDate->format("g:ia");

    }



    /**
     * @param $string
     * @return bool
     */
    public function isTitleLong($string ){

        $maxChar = 8;
        $isTooLong = false;
        $titleArr = explode(" ", $string);
        foreach ( $titleArr as $part ){
            $lengthPart = strlen($part);
            if($lengthPart > $maxChar){
                $isTooLong = true;
            }
        }

        return $isTooLong;

    }




}
