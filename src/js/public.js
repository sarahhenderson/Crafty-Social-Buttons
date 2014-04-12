jQuery(document).ready(function($) {

    var instances = [];

    for(var key in window) {
        if (key.indexOf('crafty_social_buttons_data_') === 0) {
            instances.push(window[key]);
        }
    }

    var makeLocalAjaxCallbackForShareCount = function(service, shareUrl, callbackUrl, key) {

        var url = callbackUrl + '&service=' + service + '&url=' + shareUrl;

        var serviceSlug = service.toLowerCase();
        $.ajax(url, {
            cache: false,
            type: 'get',
            dataType: 'json',
            contentType: 'application/json',
            success: function (data) {
                if (data && data.count) {
                    $("#crafty-social-share-count-" + serviceSlug + '-' + key).html(data.count);
                }
            },
            error: function(jqXHR, status, error) {
                //console.log(error);
            }
        });
    }

    for(var i = 0, l = instances.length; i < l; i++) {
        var settings = instances[i];

        var url = settings.url;
        var callbackUrl = settings.callbackUrl;
        var key = settings.key;

        for (var s = 0, sl = settings.services.length; s < sl; s++) {
            var service = settings.services[s];
            makeLocalAjaxCallbackForShareCount(service, url, callbackUrl, key);
        }
    }

});