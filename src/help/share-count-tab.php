<p><strong>Share Count Options</strong></p>

<dl>
<dt>Show share counts</dt>
<dd>This finds out from the sharing service how many times your post has been shared, and displays the number in a little bubble next to the icon.
   Not all services have this ability, so the count will only appear if the service supports it. Calling out to the services to obtain the counts
   is done after your page is loaded.  This means there might be a short delay before the count appears, but means the counts won't slow
   down the loading of the page.</dd>

<dt>Cache share counts</dt>
<dd>Enables the share counts to be saved for a short time.  By default (if you have share counts displayed) calls are made to get the latest share count
   every time a post is displayed.  While the size of the request and response are very small, this does result in a lot of calls out to Facebook, Google, Twitter etc.
   Caching allows the values to be remembered for a short time.  During the caching expiry period, no calls are made to get the latest value, and the share
   count will not appear to update.</dd>

<dt>Cache expiry</dt>
<dd>This is the number of minutes that share count values will be cached.  You can set it to anything between 1 minute and 180 minutes (3 hours).  Once share counts
   are cached, the values will not be refreshed until this number of minutes passes.  During this time the share counts will display the old values.</dd>

</dl>