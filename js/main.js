jQuery(function($){

    var newcount = 0,
        photos   = $('photos'),
        min_ID   = photos.data('next-min-ID'),
        load_photos = function() {

            // Hides the notification
            $("#count-bar").addClass("hidden");

            $.getJSON(
                'https://api.instagram.com/v1/tags/' + tag 
                    + '/media/recent?callback=?',
                {
                    'access_token': $.QueryString['access_token'],
                    'count': 16,
                    'min_id': min_ID
                }
            )
            .done(function(response){
                var new_photos = response.data,
                    pagination = response.pagination,
                    delay = 0,
                    anim_speed = 200;

                // Removes the loading LI if present
                $("#photos").find('.loading').hide(400).delay(400).remove();

                // Resets the new photo count
                newcount = 0;

                // Sets the new min ID for loading images
                min_ID = pagination.next_min_id;

                for (x in new_photos) {
                    var photoCont = $("#photos"),
                        photo = new_photos[x],
                        caption = null;

                    if (photo.caption!==null) {
                        caption = photo.caption.text;
                    }

                    $('<img />', {
                        src: photo.images.thumbnail.url,
                        alt: caption,
                        data: {
                            info: photo
                        }
                    })
                    .load(function(){
                        var photo = $(this).data('info'),
                            byline = $('<strong />', {
                                text: 'Photo by ' + photo.user.username
                            });
                        
                        $('<a />', {
                            href: photo.link,
                            html: this
                        })
                        .css({opacity: 0})
                        .delay(delay)
                        .prependTo($("#photos"))
                        .append(byline)
                        .wrap('<li />')
                        .animate({ opacity: 1 }, anim_speed);

                        delay += anim_speed
                    });
                }
            })
            .error(function(data){
                console.log(data);
            });
        };

    channel.bind('new-photo', function(data){

        newcount += data.newcount;

        var plural = (newcount===1) ? 'photo' : 'photos';
            phrase = newcount+' new '+plural+' uploaded.';

        $('#count-bar').removeClass('hidden').find('#count').text(phrase);

    });

    $("#image-loader").bind('click', function(event){
        event.preventDefault();

        load_photos();
    });

    load_photos();

});

/**
 * Retrieves query string values
 * 
 * Plugin by BrunoLM: http://stackoverflow.com/a/3855394/463471
 */
(function($) {
    $.QueryString = (function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p=a[i].split('=');
            if (p.length != 2) continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    })(window.location.search.substr(1).split('&'))
})(jQuery);
