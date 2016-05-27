<?php 
class CTFTTwitterHelper{

    public function isSecure() {
        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $isSecure = true;
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            $isSecure = true;
        }
        $REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
        return $REQUEST_PROTOCOL;
    }

    public function linkify_twitter_status($status_text)
    {
      // linkify URLs
      $status_text = preg_replace(
        '/(https?:\/\/\S+)/',
        '<a href="\1">\1</a>',
        $status_text
      );

      // linkify twitter users
      $status_text = preg_replace(
        '/(^|\s)@(\w+)/',
        '\1@<a href="http://twitter.com/\2">\2</a>',
        $status_text
      );

      // linkify tags
      $status_text = preg_replace(
        '/(^|\s)#(\w+)/',
        '\1#<a href="http://twitter.com/search?q=%23\2">\2</a>',
        $status_text
      );

      return $status_text;
    }

    public function twitter_time_format($date,$granularity=2) {
        //$date = strtotime($date);
        $difference = time() - $date;
        $periods = array('decade' => 315360000,
            'year' => 31536000,
            'month' => 2628000,
            'week' => 604800, 
            'day' => 86400,
            'hour' => 3600,
            'minute' => 60,
            'second' => 1);
        $retval ='';
        foreach ($periods as $key => $value) {
            if ($difference >= $value) {
                $time = floor($difference/$value);
                $difference %= $value;
                $retval .= ($retval ? ' ' : '').$time.' ';
                $retval .= (($time > 1) ? $key.'s' : $key);
                $granularity--;
            }
            if ($granularity == '0') { break; }
        }
        return ' about '.$retval.' ago';      
    }

    public function pr($arr){
        echo"<pre>";
        print_r($arr);
        die();
    }
}
?>