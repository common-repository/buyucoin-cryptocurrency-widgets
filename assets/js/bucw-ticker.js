jQuery(document).ready(function($) {
    var sliders = [];
     get_data();
    setInterval(() => {
          $('.bucw-ticker-wrp').show();
          get_dataagain();
    }, 5 * 60 * 1000);
    function get_data() {
        var custom_arr1 = [];
        $('.bucw-container').each(function() {
            var raw_json = JSON.parse($(this).find('#ticker_settings').html());
            var ispeed = $('.bucw-container').attr("data-tickerspeed");
            var nonce = $('.bucw-container').attr("data-nonce");
            var formData = {
                'action': 'bucw_get_ticker_data',
                'data': raw_json,
                'nonce': nonce,
            };
            $.ajax({
                type: "POST",
                url: ajax_object.ajax_url,
                data: formData,
                success: function(data) { 
                    if (data == "") {

                        $('.preloader-placeholder').hide();
                        $('#bucw-ticker-widget-'+raw_json.post_id).hide();
                        $('.bucw_no_data').show();

                    }
                    else{
                    $('.preloader-placeholder').hide();
                    $('#bucw-ticker-widget-'+raw_json.post_id).find("ul").html(data);
                        $('.bucw-ticker-wrp').each(function (i, item) {
                            var slider;
                            slider = $('.bucw-ticker-wrp').bxSlider({
                                controls: false,
                                speed: ispeed * 4000,
                                ticker: true,
                                tickerHover: true,
                                slideWidth: 'auto',
                                responsive: true,
                                minSlides: 1,
                                maxSlides: 12,
                            });
                            
                        });
                    }
                },
                error: function(xhr, status) {
                    console.log("Sorry, there was a problem!");
                },
               
            });
        });
    }
    function get_dataagain() {
        $('.bucw-container').each(function() {
            var raw_json = JSON.parse($(this).find('#ticker_settings').html());
            var ispeed = $('.bucw-container').attr("data-tickerspeed");
            var nonce = $('.bucw-container').attr("data-nonce");
            var formData = {
               'action': 'bucw_get_ticker_data',
               'data': raw_json,
                'nonce': nonce,
           };           
           $.ajax({
               type: "POST",
               url: ajax_object.ajax_url,
               data: formData,
                success: function(data) { 
                   $('.preloader-placeholder').hide();
                   $(sliders).each(function() {
                    this.destroySlider();
                });
                   $('#bucw-ticker-widget-'+raw_json.post_id).find("ul").html(data);
               },
               error: function(xhr, status) {
                   console.log("Sorry, there was a problem!");
               },
               complete: function(xhr, status) {
                setTimeout(function() {
                   // $('.dlt-ticker').each(function(i, item) {
                       var slider;
                       $(sliders).each(function() {
                        this.reloadSlider();
                    });
                   
                }, 5000);
               }
           });
        });
    }
});