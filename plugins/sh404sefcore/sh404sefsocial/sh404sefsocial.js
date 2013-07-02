/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     sh404sef
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     3.6.4.1481
 * @date        2012-11-01
 */

// get Google Analytics queue
var _gaq = _gaq || [];

// our own tracker
var _sh404sefSocialTrack = _sh404sefSocialTrack || [];

_sh404sefSocialTrack.setup = function() {

  FB.init({
    appId : _sh404sefSocialTrack.options.FBAppId ? _sh404sefSocialTrack.options.FBAppId : "154426421321384",
    channelUrl : _sh404sefSocialTrack.options.FBChannelUrl ? _sh404sefSocialTrack.options.FBChannelUrl : "",
    status : true, // check login status
    cookie : true, // enable cookies to allow the server to access the session
    oauth : true, // enable OAuth 2.0
    xfbml : true
  // parse XFBML
  });

  if (_sh404sefSocialTrack.options.enableAnalytics) {
    // compute tracker name
    _sh404sefSocialTrack.trackerName = _sh404sefSocialTrack.options.trackerName ? _sh404sefSocialTrack.options.trackerName
        : "sh404SEF_social_tracker";

    // enable tracking, either sync. or async.
    _sh404sefSocialTrack.setupFBTracking();
    _sh404sefSocialTrack.setupTweeterTracking();
  }
};

/*
 * Facebook tracking : Call directly if SDK loaded synchroneously or assign to
 * window.fbAsyncInit if using asynchronous loading
 */

_sh404sefSocialTrack.setupFBTracking = function() {

  try {
    if (FB && FB.Event && FB.Event.subscribe) {
      FB.Event.subscribe('edge.create', function(targetUrl) {
        // alert( 'tracking a FB like');
        _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_facebook', 'like', targetUrl, 1, true ]);
        // Google tracking
        if (_sh404sefSocialTrack.options.enableGoogleTracking) {
          _gaq.push([ '_trackSocial', 'facebook', 'like', targetUrl ]);
        }
      });
      FB.Event.subscribe('edge.remove', function(targetUrl) {
        // alert( 'tracking a FB unlike');
        _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_facebook', 'unlike', targetUrl, 1, true ]);
        // Google tracking
        if (_sh404sefSocialTrack.options.enableGoogleTracking) {
          _gaq.push([ '_trackSocial', 'facebook', 'unlike', targetUrl ]);
        }
      });
      FB.Event.subscribe('message.send', function(targetUrl) {
        // alert( 'tracking a FB send');
        _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_facebook', 'send', targetUrl, 1, true ]);
        // Google tracking
        if (_sh404sefSocialTrack.options.enableGoogleTracking) {
          _gaq.push([ '_trackSocial', 'facebook', 'send', targetUrl ]);
        }
      });
    }
  } catch (e) {
  }
};

// Tweeter tracking
_sh404sefSocialTrack.setupTweeterTracking = function() {
  try {
    if (twttr && twttr.events && twttr.events.bind) {
      twttr.events.bind('tweet', function(event) {
        if (event) {
          var targetUrl; // Default value is undefined.
          if (event.target && event.target.nodeName == 'IFRAME') {
            targetUrl = _sh404sefSocialTrack.extractParamFromUri(event.target.src, 'url');
          }
          // alert( 'tracking a tweet');
          _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_tweeter', 'tweet', targetUrl, 1, true ]);
          // Google tracking
          if (_sh404sefSocialTrack.options.enableGoogleTracking) {
            _gaq.push([ '_trackSocial', 'twitter', 'tweet', targetUrl ]);
          }
        }
      });
    }
  } catch (e) {
  }
};

/*
 * Pinterest tracking, through a callback
 */
_sh404sefSocialTrackPinterestTracking = function(msg, url) {
  try {
    if (msg == "pinned") {
      // alert( 'Image pinned');
      var targetMedia = _sh404sefSocialTrack.extractParamFromUri(url, 'url') + ' ('
          + _sh404sefSocialTrack.extractParamFromUri(url, 'media') + ')';
      _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_pinterest', msg, targetMedia, 1, true ]);
    }
  } catch (e) {
  }
};

/*
 * G+ requires a callback function for each click
 */
_sh404sefSocialTrackGPlusTracking = function(data) {
  try {
    if (data.state == "on") {
      // alert( 'tracking a plus one');
      _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_gplus', data.state, data.href, 1, true ]);
    } else if (data.state == "off") {
      // alert('tracking an unplus one');
      _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_gplus', data.state, data.href, 1, true ]);
    }
  } catch (e) {
  }
};

// Google page click tracking
_sh404sefSocialTrack.GPageTracking = function(target, source) {
  try {
    // alert( 'tracking GPage, page='+target+' from '+source);
    _gaq.push([ '_trackEvent', _sh404sefSocialTrack.trackerName + '_gplus_page', target, source, 1, true ]);
  } catch (e) {
  }
};

/**
 * Extracts a query parameter value from a URI. (c) Google - 2011
 * 
 * @param {string}
 *          uri The URI from which to extract the parameter.
 * @param {string}
 *          paramName The name of the query paramater to extract.
 * @return {string} The un-encoded value of the query paramater. underfined if
 *         there is no URI parameter.
 * @private
 */
_sh404sefSocialTrack.extractParamFromUri = function(uri, paramName) {
  if (!uri || !uri.indexOf('#')) {
    return;
  }
  var uri = uri.indexOf('#') > 0 ? uri.split('#')[1] : uri; // Remove anchor.

  // Find url param.
  paramName += '=';
  uri = uri.split('?');
  uri = uri[1] ? uri[1] : uri[0];
  var params = uri.split('&');
  for ( var i = 0, param; param = params[i]; ++i) {
    if (param.indexOf(paramName) === 0) {
      return unescape(decodeURI(param.split('=')[1]));
    }
  }
  return;
};
