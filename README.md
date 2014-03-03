Inbed all
===================

A Wordpress plugin that allows you to embed stuff with shortcodes. The goal is to support EVERYTHING.


#### Vimeo

    [vimeo id="83621712"]

    [vimeo url="https://vimeo.com/83621712"]

#### Youtube

    [youtube id="AGrAe9jHhx4"]

    [youtube url="https://www.youtube.com/watch?v=AGrAe9jHhx4"]

    [youtube url="http://youtu.be/AGrAe9jHhx4"]

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

    [twitter conversation="off" cards="off"]<blockquote class="twitter-tweet" lang="en"><p>Today&#39;s WOD: 12 pages on historic preservation tax credits…. 3, 2, 1, GO!</p>&mdash; Kyle Juvers (@kjuvers) <a href="https://twitter.com/kjuvers/statuses/440175998332440576">March 2, 2014</a></blockquote><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>[/twitter]

###### Options

* cards (string) On or Off, defaults to "off"
* conversation (string) On or Off, defaults to "off"

#### Wufoo

Go to your Wufoo form manager (wufoo.com), then to Code -> Embed under the form you'd like to embed and grab the "Wordpress Shortcode" tag.

    [wufoo username="walker" formhash="z1uj6kvm1qkq3zz" autoresize="true" height="606" header="show" ssl="true"]

###### Options

* username (string) required
* formhash or id (string) required
* autoresize (string) on or off. defaults to off
* header (string) show or hide. defaults to hide.
* ssl (string)  on or off. defaults to on.
* iframe (string) on or off. defaults to off. javascript embed is used instead.
* scrolling (string) on or off. defaults to on. this is for the iframe embed only.