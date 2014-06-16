Inbed all
===================

A Wordpress plugin that allows you to embed stuff with shortcodes. The goal is to support EVERYTHING.

"Embed" is too generic, so I used "inbed", as in: ["Our greatest glory is not in never falling but in rising every time we fall [in bed]"](http://en.wikipedia.org/wiki/Fortune_cookie#In_popular_culture)

#### Vimeo

    [vimeo id="83621712"]

    [vimeo url="https://vimeo.com/83621712"]

#### Youtube

    [youtube id="AGrAe9jHhx4"]

    [youtube url="https://www.youtube.com/watch?v=AGrAe9jHhx4"]

    [youtube url="http://youtu.be/AGrAe9jHhx4"]

To turn on autoplay, add in the autoplay setting to the tag:

    [youtube id="AGrAe9jHhx4" autoplay="true"]

To turn off automatically embedding at highquality, add in hq_off=true

    [youtube id="AGrAe9jHhx4" hq_off=true]

To set embedding quality specifically, add in vq variable:

    [youtube id="AGrAe9jHhx4" vq=large]

Potential values are:

    small, medium, large, 720


#### Ustream

    [ustream url="http://www.ustream.tv/embed/12703622"]
    
    [ustream url="https://www.ustream.tv/embed/schannel/960"]

#### Vine

    [vine id="hPXTA6l9AqQ"]

    [vine url="https://vine.co/v/hPXTA6l9AqQ"]

#### Instagram

    [instagram id="i9pZitqsAa"]

    [instagram url="http://instagram.com/p/i9pZitqsAa/"]

#### Gists

    [gist url="https://gist.github.com/walker/8733217"]

#### Soundcloud

Right now, I think this one relies on knowing the Soundcloud ID, but I'm working on getting track ID resolution via URLs working.

    [soundcloud id="135692753"]

All options on:

    [soundcloud id="135692753" artwork="on" color="990000" related="on" autoplay="on" download="on" buying="on" liking="on" playcount="on" comments="on"]

###### Options

* id (string)
* url (string)
* color (string) Hex color string
* artwork (string) On or Off, defaults to "off"
* related (string) On or Off, defaults to "off"
* autoplay (string) On or Off, defaults to "off"
* download (string) On or Off, defaults to "off"
* buying (string) On or Off, defaults to "off"
* liking (string) On or Off, defaults to "off"
* playcount (string) On or Off, defaults to "off"
* comments (string) On or Off, defaults to "off"

#### Twitter

The best way I could figure here was to allow you to slap the embed code between an open/close shortcode tag and then I grab what's necessary.

    [twitter conversation="off" cards="off"]<blockquote class="twitter-tweet" lang="en"><p>Looks like <a href="https://twitter.com/theroyale">@theroyale</a> has <a href="https://twitter.com/PerennialBeer">@perennialbeer</a> <a href="https://twitter.com/sumpcoffee">@sumpcoffee</a> Stout on tap. <a href="https://twitter.com/search?q=%23STL&amp;src=hash">#STL</a> <a href="https://twitter.com/search?q=%23Craftbeer&amp;src=hash">#Craftbeer</a></p>&mdash; STL Hops (@stlhops) <a href="https://twitter.com/stlhops/statuses/437704557938761728">February 23, 2014</a></blockquote><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>[/twitter]

###### Options

* cards (string) On or Off, defaults to "off"
* conversation (string) On or Off, defaults to "off"

#### Twitter Timeline

Embed your saved twitter widget and a timeline.

    [twitter-timeline url="https://twitter.com/TwitterMusic/timelines/393773266801659904" settings="https://twitter.com/settings/widgets/440598623684808704/edit"]

#### Storify

    [storify url="http://storify.com/TheClimateDesk/obama-s-climate-plan"]

#### Flickr

##### Set

    [flickr url="http://www.flickr.com/photos/walker_hamilton/sets/72157635983149564/"]

##### Options

* width (int)
* height (int)


#### Kimbia

    [kimbia channel="2014glasaintlouis/advembed" profile="trailnet"]

#### Wufoo

Go to your Wufoo form manager (wufoo.com), then to Code -> Embed under the form you'd like to embed and grab the "Wordpress Shortcode" tag.

With the least set of options:

    [wufoo username="walker" formhash="z1uj6kvm1qkq3zz"]

Or with more:

    [wufoo username="walker" formhash="z1uj6kvm1qkq3zz" autoresize="true" height="606" header="show" ssl="true"]

###### Options

* username (string) required
* formhash or id (string) required
* autoresize (string) on or off. defaults to off
* header (string) show or hide. defaults to hide.
* ssl (string)  on or off. defaults to on.
* iframe (string) on or off. defaults to off. javascript embed is used instead.
* scrolling (string) on or off. defaults to on. this is for the iframe embed only.

#### MSNBC

Just paste the MSNBC embed tag between the "msnbc" shortcodes.

    [msnbc]<iframe src='http://player.theplatform.com/p/2E2eJC/EmbeddedOffSite?guid=n_maddow_bblock_140228' height='500' width='635' scrolling='no' border='no' ></iframe>[/msnbc]
