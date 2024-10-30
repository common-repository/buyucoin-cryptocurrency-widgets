jQuery(document).ready(function($) {
    get_data();
    setInterval(() => {
        get_dataagain();
    },5 * 60 * 1000);
    function get_data() {
        $('.bucw-main-wrapper').each(function() {
            var type =  $(this).data('type');            
            var nonce = $(this).attr("data-nonce");
            var cls = 'div';            
            if(type=='list'){
                var cls ='.bucw_table tbody';
            }
            var raw_json = JSON.parse($(this).find('#ticker_settings').html());
           
            var formData = {
                'action': 'bucw_get_ticker_data',
                'data': raw_json,
                'nonce': nonce,
            };
        
            $.ajax({
                type: "POST",
                dataType: "json",
                url: ajax_object.ajax_url,
                data: formData,
                dataType: "html",
                
                success: function(data) {                 
                    if(data==""){
                       
                        $('.preloader-placeholder').hide();
                        $('#bucw-table-widget-'+raw_json.post_id).hide();
                        $('table.bucw_table thead').hide();
                        $('table.bucw_table thead').hide();
                        $('.bucw_no_data').show();
                       
                    }
                    else{
                    $('.preloader-placeholder').hide();
                    $('.bucw-main-wrapper.coins-table').show();
                    $('table.bucw_table thead').show();
                    $('table.bucw_table thead').show();
                    
                    
                    if(type =='card'){
                     $('.bucw-card-wrp').show();
                    }
                    $('#bucw-table-widget-'+raw_json.post_id).find(cls).html(data);
                    }
                },
                error: function(xhr, status) {
                    console.log("Sorry, there was a problem!");
                },
                complete: function(xhr, status) {
                }
            });
        });
    }
    function get_dataagain() {
        $('.bucw-main-wrapper').each(function() {
            var type =  $(this).data('type');    
            var nonce = $(this).attr("data-nonce");
            var cls = 'div';            
            if(type=='list'){
                var cls ='.bucw_table tbody';
            }
            var raw_json = JSON.parse($(this).find('#ticker_settings').html());
            var ispeed = $('.bucw-main-wrapper').attr("data-tickerspeed");
            var formData = {
               'action': 'bucw_get_ticker_data',
               'data': raw_json,
                'nonce': nonce,
           };
           
           $.ajax({
               type: "POST",
               url: ajax_object.ajax_url,
               data: formData,
               // dataType: "html",
               // dataType: "json",
                success: function(data) { 
                    $('.preloader-placeholder').hide();
                    $('.bucw-main-wrapper').show();
                 
                    
                    $('table.bucw_table thead').show();
                    $('#bucw-table-widget-'+raw_json.post_id).find(cls).html(data);
                },
               error: function(xhr, status) {
                   console.log("Sorry, there was a problem!");
               },
               complete: function(xhr, status) {
                }
           });
        });
       
    }
});