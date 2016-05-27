jQuery(document).ready(function(){
    var nt_example1 = jQuery('#nt-example1').newsTicker({
        row_height: ticker_option_data._ctft_twitter_row_height_in_ticker,
        max_rows: ticker_option_data.number_of_tweets_to_show_in_tikcer,
        autostart:true,
        duration: 4000,
        prevButton: jQuery('#nt-example1-prev'),
        nextButton: jQuery('#nt-example1-next')
    });

})