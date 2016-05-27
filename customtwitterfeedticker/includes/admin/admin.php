<?php 


// create ctft plugin settings menu
add_action('admin_menu', 'ctft_custom_create_menu');

function ctft_custom_create_menu() {

    //create new top-level menu
    add_menu_page('CUSTOM Twitter Feed Settings', 'CUSTOM Twitter Feed Settings', 'administrator', __FILE__, 'ctft_custom_settings_page');

    //call register settings function
    add_action( 'admin_init', 'ctft_register_customsettings' );
}


function ctft_register_customsettings() {
    //register our settings
    register_setting( 'ctft-settings-group', '_ctft_twitter_api_key' );
    register_setting( 'ctft-settings-group', '_ctft_twitter_api_secret' );
    register_setting( 'ctft-settings-group', '_ctft_twitter_access_token' );
    register_setting( 'ctft-settings-group', '_ctft_twitter_access_token_secret' );
    register_setting( 'ctft-settings-group', '_ctft_twitter_screen_name' );
    register_setting( 'ctft-settings-group', '_ctft_twitter_caching_time' );
    register_setting( 'ctft-settings-group', '_ctft_twitter_number_of_tweets' );
    register_setting( 'ctft-settings-group', '_ctft_twitter_icon' );
    register_setting( 'ctft-settings-group', '_ctft_number_of_tweets_to_show_in_ticker' );
    register_setting( 'ctft-settings-group', '_ctft_twitter_row_height_in_ticker' );
}

function ctft_custom_settings_page() { ?>
<div class="wrap" style="margin-left:30px;">
<h2>Twitter Feed Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'ctft-settings-group' ); ?>
    <?php do_settings_sections( 'ctft-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Twitter API Key</th>
        <td><input type="text" name="_ctft_twitter_api_key" value="<?php echo esc_attr( get_option('_ctft_twitter_api_key') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Twitter Api Secret</th>
        <td><input type="text" name="_ctft_twitter_api_secret" value="<?php echo esc_attr( get_option('_ctft_twitter_api_secret') ); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Twitter Access Token</th>
        <td><input type="text" name="_ctft_twitter_access_token" value="<?php echo esc_attr( get_option('_ctft_twitter_access_token') ); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Twitter Access Secret</th>
        <td><input type="text" name="_ctft_twitter_access_token_secret" value="<?php echo esc_attr( get_option('_ctft_twitter_access_token_secret') ); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Twitter Screen Name</th>
        <td><input type="text" name="_ctft_twitter_screen_name" value="<?php echo esc_attr( get_option('_ctft_twitter_screen_name') ); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Caching Time (in hours)</th>
        <td>
            <select name='_ctft_twitter_caching_time'>
                <?php for($m=0.5; $m<=6;){
                    $value_sel = get_option('_ctft_twitter_caching_time');
                ?>
                <option value="<?php echo $m;?>" <?php if($value_sel==$m){ ?> selected=selected <?php } ?> ><?php echo $m;?></option>
                <?php $m+=0.5; 
            }?>
            </select>
			</td>
        </tr>
        <tr valign="top">
        <th scope="row">Number of tweets to fetch (maximum of 200 per distinct request)</th>
        <td><input type="text" name="_ctft_twitter_number_of_tweets" value="<?php echo esc_attr( get_option('_ctft_twitter_number_of_tweets') ); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Icon</th>
        <td>
            <?php 
                $twitter_icon2 = plugins_url( 'customtwitterfeedticker/assests/img/twitter-related-icon.jpg' );
                $twitter_icon1 = plugins_url( 'customtwitterfeedticker/assests/img/twitter-icon.jpg' ); 
                $user_icon = plugins_url( 'customtwitterfeedticker/assests/img/user.png'); 
            ?>
            <input type="radio" name="_ctft_twitter_icon" value="twitter_icon1" <?php if(get_option('_ctft_twitter_icon')=='' || get_option('_ctft_twitter_icon') =='twitter_icon1'){?> checked='checked' <?php } ?> /><img src='<?php echo $twitter_icon1; ?>' width='32' height='32'  />
            <input type="radio" name="_ctft_twitter_icon" value="twitter_icon2" <?php if(get_option('_ctft_twitter_icon')=='twitter_icon2'){?> checked='checked' <?php } ?> /><img src='<?php echo $twitter_icon2; ?>' width='32' height='32'  />
            <input type="radio" name="_ctft_twitter_icon" value="user_icon" <?php if(get_option('_ctft_twitter_icon')=='user_icon'){?> checked='checked' <?php } ?> /><img src='<?php echo $user_icon; ?>' width='32' height='32'  />
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">Number of tweets to show in ticker</th>
        <td><input type="text" name="_ctft_number_of_tweets_to_show_in_ticker" <?php if(get_option('_ctft_number_of_tweets_to_show_in_ticker') !=""){?> value="<?php echo esc_attr( get_option('_ctft_number_of_tweets_to_show_in_ticker') ); ?>" <?php } else{?> value="1" <?php }?> /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Height of tweet in ticker row</th>
        <td><input type="text" name="_ctft_twitter_row_height_in_ticker" <?php if(get_option('_ctft_twitter_row_height_in_ticker')!=""){?>value="<?php echo esc_attr( get_option('_ctft_twitter_row_height_in_ticker') ); ?>" <?php }else{?> value="200" <?php }?> /></td>
        </tr>

    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } 


add_filter( 'update_option__ctft_twitter_screen_name', 'ctft_delete_feeds_from_db', 10, 2 );

function ctft_delete_feeds_from_db( $old_value, $new_value )
{
   delete_transient( 'ctft_twitter_feed_results' );
}
?>