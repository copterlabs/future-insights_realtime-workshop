<?php

require_once 'inc/config.inc.php';

// Catches the Instagram realtime Pubsubhubub challenge flow
if (isset($_GET['hub_challenge'])) {
    echo $_GET['hub_challenge'];
    exit;
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    // Instantiates Pusher PHP API
    require 'lib/Pusher.php';

    $update = file_get_contents('php://input');

    $photos = json_decode($update);
    if (is_array($photos)) {
        $length = count($photos);
    }

    if ($length>0) {
        $pusher = new Pusher($pusher_key, $pusher_secret, $pusher_app_id);
        $pusher->trigger(
            'photos', 
            'new-photo', 
            array(
                'newcount' => $length,
            )
        );
    }
}

// Get most recent IG photos
$token = isset($_GET['access_token']) ? $_GET['access_token'] : NULL;
$tag = isset($_GET['tag']) ? $_GET['tag'] : 'instacat';

$api_url = 'https://api.instagram.com/v1/tags/' 
         . $tag . '/media/recent?count=16&access_token=' . $token;

$page_url = 'http://' . $_SERVER['SERVER_NAME'] 
          . dirname($_SERVER['REQUEST_URI']) 
          . '/?access_token=' . $token
          . '&tag=' . $tag;

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = json_decode(curl_exec($ch));
$photos = property_exists($response, 'data') ? $response->data : array();
$next_min_id = property_exists($response, 'pagination') ? $response->pagination->next_min_id : NULL;
$next_max_id = property_exists($response, 'pagination') ? $response->pagination->next_max_id : NULL;

/*

https://api.instagram.com/v1/tags/nofilter/media/recent

curl -F 'client_id=6019cd09a31d488eab0a8e072f408415' \
     -F 'client_secret=d1fcd47c601e4d80912f5168fc8efaa7' \
     -F 'object=tag' \
     -F 'aspect=media' \
     -F 'object_id=catstagram' \
     -F 'callback_url=http://demo.copterlabs.com/filive/workshop/' \
     https://api.instagram.com/v1/subscriptions/

curl -X DELETE 'https://api.instagram.com/v1/subscriptions?client_secret=d1fcd47c601e4d80912f5168fc8efaa7&client_id=6019cd09a31d488eab0a8e072f408415&object=all'

*/

?>
<!doctype html>
<html lang="en">

<head>

<meta charset="utf-8" />

<link rel="stylesheet" href="css/master.css" />

<title>Realtime Workshop by Jason Lengstorf &mdash; Future Insights Live</title>

</head>

<body>

<?php if ($token!==NULL): ?>

    <header>
        <h1>Photos tagged with #<?=$tag?></h1>
    </header>

    <article>

        <div id="count-bar" class="message hidden">
            <p>
                <strong id="count">0 new photos posted.</strong>
                <a href="<?=$page_url?>"
                   class="button"
                   id="image-loader">&#8635; Load the new images </a>
            </p>
        </div>

        <ul id="photos" data-next-min-ID="<?=$next_max_id?>">

        <?php //foreach ($photos as $photo): ?>
            <li class="loading">Loading&hellip;</li>
            <!-- <li>
                <a href="<?=$photo->link?>">
                    <img src="<?=$photo->images->thumbnail->url?>"
                         alt="<?=(empty($photo->caption)) ? $photo->caption->text : NULL ?>"
                         data-id="<?=$photo->id?>" />
                    <strong>Photo by <?=$photo->user->username?></strong>
                </a>
            </li> -->
        <?php //endforeach; ?>

        </ul><!--/#photos-->

    </article>

<?php else: ?>

    <header>
        <h1>Log in to start playing with realtime!</h1>
    </header>

    <article>
        <a href="https://api.instagram.com/oauth/authorize/?client_id=<?=$ig_client_id?>&amp;redirect_uri=http://demo.copterlabs.com/filive/workshop/login.php&amp;response_type=code"
           class="login button">Login &rarr;</a>
    </article>

<?php endif; ?>

<footer>
    <p>
        This demo was created by 
        <a href="http://www.lengstorf.com/">Jason Lengstorf</a> for use at the 
        realtime workshop at Future Insights Live 2013. It is released under the 
        MIT License.
    </p>
</footer>

<script src="http://code.jquery.com/jquery-2.0.0.min.js"></script>
<script src="http://js.pusher.com/2.0/pusher.min.js"></script>
<script>

jQuery(function($){

    // Enable pusher logging - don't include this in production
    Pusher.log = function(message) {
        if (window.console && window.console.log) window.console.log(message);
    };

    // Flash fallback logging - don't include this in production
    WEB_SOCKET_DEBUG = true;

    var newcount = 0,
        pusher   = new Pusher('<?=$pusher_key?>'),
        channel  = pusher.subscribe('photos'),
        photos   = $('photos'),
        min_ID   = photos.data('next-min-ID'),
        load_photos = function() {
            // Hides the notification
            $("#count-bar").addClass("hidden");

            photos.find('.loading').hide(400).delay(400).remove();

            $.getJSON(
                'https://api.instagram.com/v1/tags/<?=$tag?>/media/recent?callback=?',
                {
                    'access_token': '<?=$token?>',
                    'count': 16,
                    'min_id': min_ID
                }
            )
            .done(function(response){
                var new_photos = response.data,
                    pagination = response.pagination,
                    delay = 0,
                    anim_speed = 200;                

                // Resets the new photo count
                newcount = 0;

                // Sets the new min ID for loading images
                min_ID = pagination.next_min_id;

                for (x in new_photos) {
                    var photoCont = $("#photos"),
                        photo = new_photos[x],
                        caption = (photo.caption!==null) ? photo.caption.text : '',
                        img = $('<img />', {
                            src: photo.images.thumbnail.url,
                            alt: caption
                        }),
                        link = $('<a />', {
                            href: photo.link,
                            html: img
                        })
                        .hide()
                        .delay(delay)
                        .prependTo($("#photos"))
                        .append($('<strong />'), {
                            text: 'Photo by ' + photo.user.username
                        })
                        .wrap('<li />')
                        .show(anim_speed);

                    delay += anim_speed;
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

});

</script>

</body>

</html>

