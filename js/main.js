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

                // Loops through the loaded photos
                for (x in new_photos) {
                    var photoCont = $("#photos"),
                        photo = new_photos[x],
                        caption = null;

                    // If a caption exists, sets it
                    if (photo.caption!==null) {
                        caption = photo.caption.text;
                    }

                    // Creates a new image element
                    $('<img />', {
                        src: photo.images.thumbnail.url,
                        alt: caption,
                        data: {
                            info: photo // Passes photo info to the callback
                        }
                    })
                    .load(function(){
                        
                        // Sets up shortcut vars and byline markup
                        var cont   = $("#photos"),
                            photo  = $(this).data('info'), // Reads photo data
                            byline = $('<strong />', {
                                text: 'Photo by ' + photo.user.username
                            });
                        
                        // Creates a new link around the image
                        $('<a />', {
                            href: photo.link,
                            html: this
                        })
                        .css({opacity: 0})  // Starts the effect
                        .delay(delay)       // Adds a delay
                        .prependTo(cont)    // Adds the new element to the DOM
                        .append(byline)     // Inserts the attribution
                        .wrap('<li />')     // Wraps the whole thing in a LI
                        .animate({
                            opacity: 1
                        }, anim_speed);     // Finishes the effect

                        delay += anim_speed // Simulates sequential loading
                    });
                }
            })
            .error(function(data){
                console.log(data);
            });
        };

    // Adds a realtime listener
    channel.bind('new-photo', function(data){

        // Keeps a running tally of new photos not yet loaded
        newcount += data.newcount;

        // Grammar stuffs
        var plural = (newcount===1) ? 'photo' : 'photos';
            phrase = newcount+' new '+plural+' uploaded.';

        // Updates the count bar with the new information
        $('#count-bar').removeClass('hidden').find('#count').text(phrase);

    });

    // Click handler for the "Load the New Images" button
    $("#image-loader").bind('click', function(event){
        event.preventDefault();

        load_photos();
    });

    // For initialization purposes, loads the photos once the DOM is ready
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
