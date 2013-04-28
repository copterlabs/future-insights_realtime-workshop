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
                'photos' => $photos,
            )
        );
    }
}

// Get most recent IG photos
$token = isset($_GET['access_token']) ? $_GET['access_token'] : NULL;
$tag = isset($_GET['tag']) ? $_GET['tag'] : 'photo';
$ch = curl_init('https://api.instagram.com/v1/tags/' . $tag . '/media/recent?count=16&access_token=' . $token);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = json_decode(curl_exec($ch));
$photos = $response->data;

/*

https://api.instagram.com/v1/tags/nofilter/media/recent

curl -F 'client_id=6019cd09a31d488eab0a8e072f408415' \
     -F 'client_secret=d1fcd47c601e4d80912f5168fc8efaa7' \
     -F 'object=tag' \
     -F 'aspect=media' \
     -F 'object_id=pbr' \
     -F 'callback_url=http://demo.copterlabs.com/filive/workshop/' \
     https://api.instagram.com/v1/subscriptions/

curl -X DELETE 'https://api.instagram.com/v1/subscriptions?client_secret=466d4d104a4449d896c5cc977e086379&client_id=e9550fc2f7654d5ba248fc710462af7a&object=all'

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

        <div class="message hidden"></div>

        <ul id="photos">

        <?php foreach ($photos as $photo): ?>
            <li>
                <a href="<?=$photo->link?>">
                    <img src="<?=$photo->images->thumbnail->url?>"
                         alt="<?=(empty($photo->caption)) ? $photo->caption->text : NULL ?>"
                         data-id="<?=$photo->id?>" />
                    <strong>Photo by <?=$photo->user->username?>.</strong>
                </a>
            </li>
        <?php endforeach; ?>

        </ul><!--/#photos-->

    </article>

<?php else: ?>

    <header>
        <h1>Log in to start playing with realtime!</h1>
    </header>

    <article>
        <a href="https://api.instagram.com/oauth/authorize/?client_id=<?=$ig_client_id?>&amp;redirect_uri=http://demo.copterlabs.com/filive/workshop/login.php&amp;response_type=code"
           class="login button">Login</a>
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
        pusher   = new Pusher('867d60a8d5de3996dd25'),
        channel  = pusher.subscribe('photos'),
        photos  = $('photos');

    channel.bind('new-photo', function(data){

        newcount += data.newcount;

        console.log(newcount);

        // If the loading LI still exists, removes it
        // if (photo_count===1) {
        //     for (var i=0; i<photos.childNodes.length; i++) {
        //         if (photos.childNodes[i].className==="loading") {
        //             photos.removeChild(photos.childNodes[i]);
        //         }
        //     }
        // }

        // Creates a new LI with the photo
        //TODO: Add photo markup
        // var li = document.createElement("li");
        // li.appendChild(data.photo);
        // ul.appendChild(li);

        var 

    });

});

</script>

</body>

</html>

