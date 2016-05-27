<?php
/**
 * Plugin Name: Custom Twitter Feed Ticker
 * Plugin URI: 
 * Description: Plugin handles twitter feeds, including @username.
 * Version: 1.0.0
 * Author: Surbhit Dubey
 * Author URI: http://techfreq.com/
 * License: GPLv2 or later
 * Text Domain: Custom Twitter Feed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define constants
 **/
if ( ! defined( 'CTFT_PLUGIN_URL' ) ) {
	define( 'CTFT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
class CustomTwitterFeedTicker {
    
    private $url;
    private $oauth_access_token;
    private $oauth_access_token_secret;
    private $consumer_key;
    private $consumer_secret;
    private $tweet_count;
    private $screen_name;


    public function __construct()
    {
        add_shortcode('ctft_twitterfeed', array($this, 'twitterfeed'));
        $this->url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
        $this->oauth_access_token = get_option('_ctft_twitter_access_token');
        $this->oauth_access_token_secret = get_option('_ctft_twitter_access_token_secret');
        $this->consumer_key = get_option('_ctft_twitter_api_key');
        $this->consumer_secret = get_option('_ctft_twitter_api_secret');
        $this->tweet_count = get_option('_ctft_twitter_number_of_tweets');
        $this->screen_name = get_option('_ctft_twitter_screen_name');

    }
     


    public function twitterfeed($type) {
        extract(shortcode_atts(array(
            'type' => 'type'
        ), $type));
       
        $special_query_results = get_transient( 'ctft_twitter_feed_results' );
        if ( false === get_transient( 'ctft_twitter_feed_results' ) ) {
         
           
        
        

        $oauth = array( 
                        'screen_name' => $this->screen_name,
                        'count' => $this->tweet_count,
                        'oauth_consumer_key' => $this->consumer_key,
                        'oauth_nonce' => time(),
                        'oauth_signature_method' => 'HMAC-SHA1',
                        'oauth_token' => $this->oauth_access_token,
                        'oauth_timestamp' => time(),
                        'oauth_version' => '1.0');
 
    $base_info = $this->buildBaseString($this->url, 'GET', $oauth);
    $composite_key = rawurlencode($this->consumer_secret) . '&' . rawurlencode($this->oauth_access_token_secret);
    $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
    $oauth['oauth_signature'] = $oauth_signature;

    // Make requests
    $header = array($this->buildAuthorizationHeader($oauth), 'Expect:');
    $options = array( CURLOPT_HTTPHEADER => $header,
                      //CURLOPT_POSTFIELDS => $postfields,
                      CURLOPT_HEADER => false,
                      CURLOPT_URL => $this->url.'?screen_name='.$this->screen_name.'&count='.$this->tweet_count,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_SSL_VERIFYPEER => false);

    $feed = curl_init();
    curl_setopt_array($feed, $options);
    $json = curl_exec($feed);
    curl_close($feed);

    $twitter_data = json_decode($json);
 
        if (array_key_exists('errors', $twitter_data)) {
            $result_data = '';
        }else{
         $result_data = $twitter_data;
         set_transient( 'ctft_twitter_feed_results', $twitter_data, 60*60*(get_option('_ctft_twitter_caching_time')));
        }

    
    }else{
        $result_data = $special_query_results;
    } 

    if(!empty($result_data)){
		require_once dirname( __FILE__ ) ."/includes/helper/helper.php";

        $TwitterHelper = new CTFTTwitterHelper();
            $output ='<div class="row">
                                <div class="col-md-12 centered">
                                    <div id="nt-example1-container">
                                        <i class="fa fa-arrow-up" id="nt-example1-prev"></i> 
                                        <ul id="nt-example1">';
            

            foreach ($result_data as $key => $result) {

                $http_url = $TwitterHelper->isSecure();

                $user_detail = $result->user;
                $date = new \DateTime($result->created_at);
                if(get_option('_ctft_twitter_icon')=='user_icon'){
                $twitter_icon = str_replace("http", $http_url,$user_detail->profile_image_url);
                }else if(get_option('_ctft_twitter_icon')=='twitter_icon2') {
                    $twitter_icon = plugins_url( 'assests/img/twitter-related-icon.jpg', __FILE__ );  
                }else {
                    $twitter_icon= plugins_url( 'assests/img/twitter-icon.jpg', __FILE__ );
                }
                $output .='<li>
                            <div class="col-md-2 col-xs-4">
                            <figure>
                            <img alt="Twitter" src="'.$twitter_icon.'"></figure>
                            </div>
                            <div class="col-md-10 col-xs-8">
                            <p class="title"><span><a target="_blank" href="https://twitter.com/'.$user_detail->screen_name.'">'.$user_detail->name.'</a></span> @'.$user_detail->screen_name.' •  '.$TwitterHelper->twitter_time_format($date->getTimestamp()).'</p>
                                <p class="title">'.$TwitterHelper->linkify_twitter_status($result->text).'</p>
                            </div>    
                            </li>'; 

            }
                    $output .='</ul>
                                <i class="fa fa-arrow-down" id="nt-example1-next"></i>
                            </div>
                        </div>
                    </div>';
            // Localize the script with new data
                   
            wp_enqueue_script( 'ticker-js', plugins_url( 'assests/js/jquery.twitterTicker.js', __FILE__ ), array(), '1.0.0', true );
            wp_register_script('main-ticker-js', plugins_url( 'assests/js/twitter-ticker.js', __FILE__ ));
            $ticker_option = array(
                '_ctft_twitter_row_height_in_ticker' => get_option('_ctft_twitter_row_height_in_ticker'),
                'number_of_tweets_to_show_in_tikcer' => get_option('_ctft_number_of_tweets_to_show_in_ticker')
            );
            wp_localize_script( 'main-ticker-js', 'ticker_option_data', $ticker_option ); 
            wp_enqueue_script( 'main-ticker-js');
            /*wp_enqueue_script( 'main-ticker-js', plugins_url( 'assests/js/main-ticker.js', __FILE__ ), array(), '1.0.0', true );*/
      
        }else{
            $output ='';
        }   
        return $output;   
            
    }

    private function buildBaseString($baseURI, $method, $params) {
        $r = array();
        ksort($params);
        foreach($params as $key=>$value){
            $r[] = "$key=" . rawurlencode($value);
        }
        return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
    }

    private function buildAuthorizationHeader($oauth) {
        $r = 'Authorization: OAuth ';
        $values = array();
        foreach($oauth as $key=>$value)
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        $r .= implode(', ', $values);
        return $r;
    }
     
} 

$customTwitter = new CustomTwitterFeedTicker();


require_once dirname( __FILE__ ) ."/includes/admin/admin.php";
?>