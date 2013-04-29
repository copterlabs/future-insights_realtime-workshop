Realtime Demo App for Future Insights Live 2013
===============================================

This is the demo application that will be built during [Jason Lengtorf][1]'s workshop at
[Future Insights Live][2]. It reads photos with the tag #selfie and displays them instantly 
using [Instagram's API][3] and [Pusher][4].


Demo
----

A live demo of this code is available at http://filive-rtws.aws.af.cm/


Walkthrough
-----------

To build this app, you'll need to run through the following steps:

1.  Set up a live dev environment ([AppFog][5] is a quick and free option if 
    you don't have access to your own server)
1.  Register a new app with Instagram
1.  Register a new app with Pusher
1.  Subscribe to a tag on Instagram


### Choose Your Tag Wisely

Originally, this demo used `#selfie`, but so many selfies are posted on 
Instagram that the API limit of 5000 requests per hour was maxed out within 
minutes. Additionally, looking at the `#selfie` feed made me feel like a creepy 
old man.


### Adding a Subscription

Add a subscription to the tag `#catstagram` by executing the following command 
in a terminal:

    curl -F 'client_id=YOUR_CLIENT_ID' \
         -F 'client_secret=YOUR_CLIENT_SECRET' \
         -F 'object=tag' \
         -F 'aspect=media' \
         -F 'object_id=catstagram' \
         -F 'callback_url=http://YOURREDIRECTURI.com/' \
         https://api.instagram.com/v1/subscriptions/


### Deleting a Subscription

Remove all subscriptions by executing the following command in a terminal:

    curl -X DELETE 'https://api.instagram.com/v1/subscriptions?client_secret=YOUR_CLIENT_SECRET&client_id=YOUR_CLIENT_ID&object=all'


Copyright and license
---------------------

Copyright (c) 2013 Jason Lengstorf

Licensed under the MIT License (the "License"); you may not use this work 
except in compliance with the License. You may obtain a copy of the License in 
the LICENSE file, or at:

http://opensource.org/licenses/mit-license.php

Unless required by applicable law or agreed to in writing, software 
distributed under the License is distributed on an "AS IS" BASIS, WITHOUT 
WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the 
License for the specific language governing permissions and limitations under 
the License.


[1]: http://github.com/jlengstorf
[2]: http://futureinsightslive.com/las-vegas-2013/
[3]: http://instagram.com/developer/
[4]: http://pusher.com/
[5]: https://www.appfog.com/

