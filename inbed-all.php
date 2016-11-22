<?php
/*
Plugin Name: Inbed All
Plugin URI: https://github.com/revolution-messaging
Description: Embed everything
Version: 1.1
Author: Walker Hamilton, recent update by Joe Pahl
Author URI: https://revolutionmessaging.com/
*/

class Inbed {

    protected $tag = null;

    protected $id = null;

    protected $url = null;

    private $youtube_regex = '/(youtu.be\/|v\/|u\/\w\/|embed\/|\?v=|\&v=)([a-zA-Z0-9\_\-]{11})/';

    private $vimeo_regex = '/vimeo.com\/([0-9]{1,})/';

    private $storify_regex = '/storify.com\/([\/\-\_a-zA-Z0-9]{1,})/';

    private $vine_regex = '/vine.co\/v\/([a-zA-Z0-9]{11})/';

		//private $src_content_regex = '/src=\'([^"]*)\'/i'; // nbc, msnbc, today  /src="(.*?)"/i

    private $flickr_url_regex = '/\/photos\/([a-zA-Z0-9\-\_\/]{7,})/';

    private $flickr_setid_regex = '/\/sets\/([0-9]{5,})/';

    private $twitter_content_regex = '/<blockquote[0-9a-zA-Z\-"\ \=]*>(.*)<\/blockquote>/';

    private $twitter_url_regex = '/href\=\"https\:\/\/twitter.com([\-\_\/0-9a-zA-Z]{5,})/';

    private $twitter_timeline_regex = '/twitter.com\/([a-zA-Z0-9\/]{3,})/';

    private $twitter_timeline_widget_regex = '/widgets\/([0-9]{3,})/';

    private $instagram_regex = '/instagram.com\/p\/([\-\_0-9a-zA-Z]{5,})/';

    private $ustream_regex = '/embed\/(schannel)?\/?([0-9]{1,})/';

    public function embed($atts, $content, $tag) {
        /* reset */
        $this->tag = null;
        $this->id = null;
        $this->url = null;
        $this->layout = null;
        /* end reset */
        if($atts)
            extract( $atts );

				$this->layout = $layout;

        if($content) {
            $this->setContent($content, $tag);
        }

        if(isset($tag)) {
            $this->tag = $tag;
        }

        if($this->tag=='kimbia' && isset($channel)) {
            $this->id = $channel;
        }

        if(isset($id) && (strpos($id, 'http')!==false || strlen($id)>20)) {
            $url = (string)$id;
            unset($id);
        }

        if(isset($formhash)) {
            $this->id = $formhash;
        } else if(isset($id)) {
            $this->id = $id;
        }

        if(isset($url)) {

            if($this->tag=='soundcloud' && !defined('SOUNDCLOUD_CLIENT_ID')) {
                return 'You must define soundcloud client id in your functions.php or wp-config.php file: <code>define(\'SOUNDCLOUD_CLIENT_ID\', \'put your soundcloud application client ID here\');</code>';
            }
            if($this->tag=='soundcloud' && !defined('SOUNDCLOUD_SECRET')) {
                return 'You must define soundcloud client secret in your functions.php or wp-config.php file: <code>define(\'SOUNDCLOUD_SECRET\', \'put your soundcloud application client secret here\');</code>';
            }
            if(isset($settings)){
                // Right now, just twitter timelines
                $this->setURL($url, array($settings));
            } else {
                $this->setURL($url);
            }
        }

        if(($tag == 'video' || $tag=='image') && (!isset($url) || empty($url))) {
            return 'You must provide a URL with the video tag: <code>[video url="https://www.youtube.com/watch?v=99CRJR6IL8c"]</code>';
        } else if($tag=='image' && (!isset($url) || empty($url))) {
            return 'You must provide a URL with the image tag: <code>[image url="http://25.media.tumblr.com/51a0d1b6d56036dbd7b3aa228594961a/tumblr_mw8h7nCbr01t1x93po1_500.jpg"]</code>';
        }

        if($this->id && $this->tag) {
            switch($this->tag) {
                case 'paypal':
                    if(!isset($button) && isset($value)) { $button = '<button name="submit" type="submit"><span>'.$value.'</span></button>'; } else { $button = '<input type="image" src="'.$button.'">'; }
                    return '<div class="form-container"><form class="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="'.$this->id.'">'.$button.'<img border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form></div>';
                    break;
                case 'ustream':
                    if(isset($width)){$width = ' width="'.$width.'"';} else {$width="";}
                    if(isset($height)){$height = ' height="'.$height.'"';} else {$height="";}
                    return '<div class="inbed inbed-video ustream"><iframe'.$width.$height.' src="//www.ustream.tv/embed'.$this->id.'?v=3&amp;wmode=direct" scrolling="no" frameborder="0" style="border: 0px none transparent;"></iframe></div>';
                    break;
                case 'kimbia':
                    $kimbia_vars = array();
                    if(isset($channel)){$kimbia_vars[] = 'channel='.$channel;}
                    if(isset($profile)){$kimbia_vars[] = 'messagingProfile='.$profile;}
                    if(!empty($kimbia_vars)) {
                        $kimbia_get = '?'.implode('&', $kimbia_vars);
                    } else {$kimbia_get = '';}
                    return '<div class="inbed inbed-form kimbia"><script src="https://widgets.kimbia.com/widgets/form.js'.$kimbia_get.'"></script></div>';
                    break;
                case 'flickr':
                    if(isset($width)){$width = ' width="'.$width.'"';} else {$width="";}
                    if(isset($height)){$height = ' height="'.$height.'"';} else {$height="";}
                    return '<div class="inbed inbed-image flickr"><object'.$width.$height.'><param name="flashvars" value="offsite=true&lang=en-us&page_show_url='.urlencode($this->url).'&set_id='.$this->id.'&jump_to="></param><param name="movie" value="http://www.flickr.com/apps/slideshow/show.swf?v=140556"></param><param name="allowFullScreen" value="true"></param><embed type="application/x-shockwave-flash" src="http://www.flickr.com/apps/slideshow/show.swf?v=140556" allowFullScreen="true" flashvars="offsite=true&lang=en-us&page_show_url='.urlencode($this->url).'&set_id='.$this->id.'&jump_to="'.$width.$height.'></embed></object>';
                    break;
                case 'twitter-timeline':
                    if(!isset($this->id)) {
                        return 'You need to provide the id or the settings url of the widget you\'ve configured: <code>[twitter-timeline url="https://twitter.com/TwitterMusic/timelines/393773266801659904" settings="https://twitter.com/settings/widgets/440598623684808704/edit"]</code>';
                    } else if(!isset($this->url)) {
                        return 'You need to provide the URL of the timeline you want to embed: <code>[twitter-timeline url="https://twitter.com/TwitterMusic/timelines/393773266801659904" settings="https://twitter.com/settings/widgets/440598623684808704/edit"]</code>';
                    } else {
                        if(!isset($title)) {
                            $title = 'Twitter Timeline';
                        }
                        return '<div class="inbed inbed-story twitter-timeline"><a class="twitter-timeline" data-dnt="true" href="'.$this->url.'" data-widget-id="'.$this->id.'">'.$title.'</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></div>';
                    }
                    break;
                case 'vimeo':
                    $get_arr = array();
                    $get_arr[] = (isset($badge)) ? 'badge=1' : 'badge=0';
                    if(isset($title))
                        $get_arr[] = 'title=1';
                    $get_arr[] = (isset($portrait)) ? 'portrait=1' : 'portrait=0';
                    if(isset($loop))
                        $get_arr[] = 'loop=1';
                    if(isset($player_id) && ctype_alnum($player_id))
                        $get_arr[] = 'player_id='.$player_id;
                    if(isset($autoplay))
                        $get_arr[] = 'autoplay=1';
                    $get_str = (count($get_arr)>0) ? '?'.implode('&amp;', $get_arr) : '';
                    return '<div class="inbed inbed-video vimeo"><iframe src="//player.vimeo.com/video/'.$this->id.$get_str.'" scrolling="no" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
                    break;
                case 'youtube':
                    $get_arr = array('modestbranding=1');
                    if(isset($autohide) && ($autohide==='0'||$autohide==='1'||$autohide==='2'||$autohide===0||$autohide===1||$autohide===2))
                        $get_arr[] = 'autohide='.$autohide;
                    if(isset($controls) && ($controls==='0'||$controls==='1'||$controls==='2'||$controls===0||$controls===1||$controls===2))
                        $get_arr[] = 'controls='.$controls;
                    if(isset($theme) && ($theme=='dark'||$theme=='light'))
                        $get_arr[] = 'theme='.$theme;
                    if(isset($vq)) {
                        switch($vq) {
                            case 'small':
                                $get_arr[] = 'VQ=small';
                                break;
                            case 'medium':
                                $get_arr[] = 'VQ=medium';
                                break;
                            case 'large':
                                $get_arr[] = 'VQ=large';
                                break;
                            case '720':
                            default:
                                $get_arr[] = 'VQ=HD720';
                        }
                    } else if(!isset($hq_off)) {
                        $get_arr[] = 'VQ=HD720';
                    }
                    if(isset($cc))
                        $get_arr[] = 'cc_load_policy=1';
                    if(isset($loop))
                        $get_arr[] = 'loop=1';
                    $get_arr[] = (isset($showinfo)) ? 'showinfo=1': 'showinfo=0';
                    if(isset($playsinline))
                        $get_arr[] = 'playsinline=1';
                    if(isset($autoplay))
                        $get_arr[] = 'autoplay=1';
                    if(!isset($rel))
                        $get_arr[] = 'rel=0';
                    $get_arr[] = (isset($annotations)) ? 'iv_load_policy=1' : 'iv_load_policy=3';
                    $get_str = (count($get_arr)>0) ? '?'.implode('&amp;', $get_arr) : '';
                    return '<div class="inbed inbed-video youtube"><iframe src="//www.youtube.com/embed/'.$this->id.$get_str.'" scrolling="no" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
                    break;
                case 'ustream':
                    return '<div class="inbed inbed-video ustream"><iframe src="//www.ustream.tv/embed/'.$this->id.'?v=3&amp;wmode=direct" scrolling="no" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
                    break;
                case 'instagram':
                    return '<div class="inbed inbed-image instagram"><iframe src="//instagram.com/p/'.$this->id.'/embed/" frameborder="0" scrolling="no" allowtransparency="true" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
                    break;
                case 'vine':
                    $vine_url = 'https://vine.co/v/'.$this->id.'/embed'.'/';
                    if(isset($method) && ($method=='postcard' || $method=='simple')) {
                        $vine_url .= $method;
                    } else {
                        $vine_url .= 'simple';
                    }
                    if(isset($audio) && $audio=='on') {
                        $vine_url .= '?audio=1';
                    }
                    return '<div class="inbed inbed-video vine"><iframe class="vine-embed" src="'.$vine_url.'" frameborder="0"></iframe><script async src="//platform.vine.co/static/scripts/embed.js" charset="utf-8" webkitallowfullscreen mozallowfullscreen allowfullscreen></script></div>';
                    break;
                case 'soundcloud':
                    $sc_url = 'https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'.$this->id.'&amp;';
                    if(isset($autoplay) && $autoplay=='on')
                        $sc_url .= 'auto_play=true';
                    else
                        $sc_url .= 'auto_play=false';
                    if(isset($related) && $related=='on')
                        $sc_url .= '&amp;hide_related=false';
                    else
                        $sc_url .= '&amp;hide_related=true';
                    if(isset($artwork) && $artwork=='on')
                        $sc_url .= '&amp;show_artwork=true';
                    else
                        $sc_url .= '&amp;show_artwork=false';
                    if(isset($color) && !empty($color))
                        $sc_url .= '&amp;color='.$color;
                    if(isset($comments) && $comments=='on')
                        $sc_url .= '&amp;show_comments=true';
                    else
                        $sc_url .= '&amp;show_comments=false';
                    if(isset($playcount) && $playcount=='on')
                        $sc_url .= '&amp;show_playcount=true';
                    else
                        $sc_url .= '&amp;show_playcount=false';
                    if(isset($liking) && $liking=='on')
                        $sc_url .= '&amp;liking=true';
                    else
                        $sc_url .= '&amp;liking=false';
                    if(isset($buying) && $buying=='on')
                        $sc_url .= '&amp;buying=true';
                    else
                        $sc_url .= '&amp;buying=false';
                    if(isset($download) && $download=='on')
                        $sc_url .= '&amp;download=true';
                    else
                        $sc_url .= '&amp;download=false';
                    return '<div class="inbed inbed-audio soundcloud"><iframe scrolling="no" frameborder="no" src="'.$sc_url.'"></iframe></div>';
                    break;
                case 'wufoo':
                    if(!isset($username))
                        return 'You need to provide your username for Wufoo.';
                    if(!isset($formhash) || !isset($id))
                        return 'You need to provide your formhash or form ID for Wufoo.';
                    if(isset($autoresize) && $autoresize=='off')
                        $autoresize = 'false';
                    else
                        $autoresize = 'true';
                    if(isset($header) && $header=='show')
                        $header = 'show';
                    else
                        $header = 'hide';
                    if(isset($ssl) && $ssl=='off')
                        $ssl = 'false';
                    else
                        $ssl = 'true';
                    if(isset($scrolling) && $scrolling=='on')
                        $scrolling = 'yes';
                    else
                        $scrolling = 'no';
                    if(isset($iframe)) {
                        return '<div class="inbed inbed-form wufoo"><iframe allowTransparency="true" frameborder="0" scrolling="'.$scrolling.'" src="https://'.$username.'.wufoo.com/embed/'.$this->id.'/"><a href="https://'.$username.'.wufoo.com/forms/'.$this->id.'/">Fill out my Wufoo form!</a></iframe></div>';
                    } else {
                        return '<div class="inbed inbed-form wufoo"><div id="wufoo-'.$this->id.'">
                        Fill out my <a href="https://'.$username.'.wufoo.com/forms/'.$this->id.'">online form</a>.
                        </div>
                        <script type="text/javascript">var '.$this->id.';(function(d, t) {
                        var s = d.createElement(t), options = {
                        \'userName\':\''.$username.'\',
                        \'formHash\':\''.$this->id.'\',
                        \'autoResize\':'.$autoresize.',
                        \'height\':\''.$height.'\',
                        \'async\':true,
                        \'host\':\'wufoo.com\',
                        \'header\':\''.$header.'\',
                        \'ssl\':'.$ssl.'};
                        s.src = (\'https:\' == d.location.protocol ? \'https://\' : \'http://\') + \'wufoo.com/scripts/embed/form.js\';
                        s.onload = s.onreadystatechange = function() {
                        var rs = this.readyState; if (rs) if (rs != \'complete\') if (rs != \'loaded\') return;
                        try { '.$this->id.' = new WufooForm();'.$this->id.'.initialize(options);'.$this->id.'.display(); } catch (e) {}};
                        var scr = d.getElementsByTagName(t)[0], par = scr.parentNode; par.insertBefore(s, scr);
                        })(document, \'script\');</script></div>';
                    }
                    break;
                default:
                    return 'Sorry, but we couldn\'t figure out how to embed this tag.';
            }
        } else if($this->url && $this->tag) {

            switch($this->tag) {
                case 'video':
                    break;
                case 'audio':
                    break;
                case 'storify':
                    return '<div class="inbed inbed-story storify"><div class="storify"><iframe src="'.$this->url.'/embed" frameborder="no" allowtransparency="true"></iframe><script src="'.$this->url.'.js"></script><noscript>[<a href="http:'.$this->url.'" target="_blank">View story on Storify</a>]</noscript></div></div>';
                    break;
                case 'msnbc':
                    return '<div class="inbed inbed-video msnbc"><iframe src="'.$this->url.'" scrolling="no" border="no"></iframe></div>';
										break;
                case 'nbcnews':
                    return '<div class="inbed inbed-video nbcnews"><iframe src="http://www.nbcnews.com/widget/video-embed/'.$this->url.'" frameborder="0" allowfullscreen></iframe></div>';
										break;
                case 'today':
                    return '<div class="inbed inbed-video today"><iframe src="'.$this->url.'" scrolling="no" border="no" webkitallowfullscreen mozallowfullscreen allowfullscreen frameborder="0" autoplay="0"></iframe></div>';
										break;

								case 'fbvideo':
                    return '<div class="inbed inbed-video fbvideo '. $this->layout .'"><iframe src="https://www.facebook.com/plugins/video.php?href=' . urlencode($this->url) . '" scrolling="no" frameborder="0" allowTransparency="true" allowFullScreen="true"></iframe></div>';
										break;

								case 'fbpost':
                    return '<div class="fbpost">' . $this->url . '</div>';
										break;

                case 'nbc':
                    return '<div class="inbed inbed-video nbc" itemprop="video" itemscope itemtype="http://schema.org/VideoObject"><iframe src="'.$this->url.'" frameBorder="0" seamless="seamless" allowFullScreen></iframe></div>';
                    break;
                case 'twitter':
                    if(isset($conversation) && $conversation=='on')
                        $conversation = '';
                    else
                        $conversation = ' data-conversation="none"';
/*
                    if(isset($cards) && $cards=='on')
                        $cards = '';
                    else
                        $cards = ' data-conversation="none"';
*/
                    return '<div class="inbed inbed-story twitter-conversation"><blockquote class="twitter-tweet" lang="en"'.$conversation.'>'.$this->content.'</blockquote><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script></div>';
                    break;
                case 'image':
                    break;
                case 'gist':
                    return '<div class="inbed inbed-code gist"><script src="'.$this->url.'.js"></script></div>';
                    break;
                default:
                    return 'Sorry, but we couldn\'t figure out how to embed this tag.';
            }
        } else {
            if($this->url)
                return 'Sorry, but we couldn\'t figure out how to embed that URL.';
            else if($this->id)
                return 'Sorry, but we couldn\'t figure out how to embed that ID.';
            else
                return 'Sorry, but we couldn\'t figure out how to embed anything using that shortcode.';
        }
    }

    private function setURL($url, $settings=array()) {
        if(strpos($url, 'youtube')!==false || strpos($url, 'youtu.be')!==false)
            $this->tag = 'youtube';
        else if(strpos($url, 'twitter.com')!==false && strpos($url, 'timelines')!==false)
            $this->tag = 'twitter-timeline';
        else if(strpos($url, 'today.com')!==false ) {
            $this->tag = 'today';
            $this->url = preg_replace('/\/video\//', '/offsite/', $url, 1);
        } else if(strpos($url, 'nbcnews.com')!==false) {
            $this->tag = 'nbcnews';
            $urlarray = explode('-', $url);
            $this->url = $urlarray[count($urlarray)-1];
        } else if(strpos($url, 'vimeo')!==false)
            $this->tag = 'vimeo';
        else if(strpos($url, 'storify.com')!==false)
            $this->tag = 'storify';
        else if(strpos($url, 'instagram.com')!==false)
            $this->tag = 'instagram';
        else if(strpos($url, 'soundcloud.com')!==false)
            $this->tag = 'soundcloud';
        else if(strpos($url, 'vine.co')!==false)
            $this->tag = 'vine';
        else if(strpos($url, 'twitter.com')!==false) {
        		$this->tag = 'twitter';
        		$this->url = $url;
        }
        switch($this->tag) {
            case 'soundcloud':
                require_once dirname(__FILE__).'/soundcloud/Services/Soundcloud.php';
                $client = new Services_Soundcloud(SOUNDCLOUD_CLIENT_ID, SOUNDCLOUD_SECRET, 'http://',$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
                $track = json_decode($client->get('resolve', array('url' => $url)));
                $this->id = $track->id;
                break;
            case 'flickr':
                $matches = array();
                preg_match($this->flickr_url_regex, $url, $matches);
                if(isset($matches[1]))
                    $this->url = '/photos/'.$matches[1];
                if(strpos($this->url, '/show')===false) { $this->url .= '/show'; }
                $matches = array();
                preg_match($this->flickr_setid_regex, $this->url, $matches);
                if(isset($matches[1]))
                    $this->id = $matches[1];
                break;
            case 'twitter-timeline':
                $matches = array();
                preg_match($this->twitter_timeline_regex, $url, $matches);
                if(isset($matches[1]))
                    $this->url = $matches[1];
                $matches = array();
                if(isset($settings[0])) {
                    preg_match($this->twitter_timeline_widget_regex, $settings[0], $matches);
                    if(isset($matches[1]))
                        $this->id = $matches[1];
                }
                break;
            case 'fbvideo':
								$url_parts = parse_url($url);
								$url = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
                $this->url = $url;
                break;
            case 'vimeo':
                $matches = array();
                preg_match($this->vimeo_regex, $url, $matches);
                if(isset($matches[1]))
                    $this->id = $matches[1];
                break;
            case 'ustream':
                $matches = array();
                preg_match($this->ustream_regex, $url, $matches);
                if(isset($matches[1]))
                        if($matches[1]!='')
                            $this->id = '/'.$matches[1].'/'.$matches[2];
                        else
                            $this->id = '/'.$matches[2];
                break;
            case 'storify':
                $matches = array();
                preg_match($this->storify_regex, $url, $matches);
                if(isset($matches[1]))
                    $this->url = '//storify.com/'.$matches[1];
                break;
            case 'vine':
                $matches = array();
                preg_match($this->vine_regex, $url, $matches);
                if(isset($matches[1]))
                    $this->id = $matches[1];
                break;
            case 'flickr-flash':

                break;
            case 'youtube':
                $matches = array();
                preg_match($this->youtube_regex, $url, $matches);
                if(isset($matches[2]))
                    $this->id = $matches[2];
                break;
            case 'instagram':
                $matches = array();
                preg_match($this->instagram_regex, $url, $matches);
                if(isset($matches[1]))
                    $this->id = $matches[1];
                break;
            case 'twitter':
            		$matches = array();
                preg_match($this->twitter_content_regex, $url, $matches);
                if(isset($matches[1]))
                    $this->url = $matches[1];
                break;
            case 'gist':
                $this->url = str_replace('.js', '', $url);
                break;
            case 'polldaddy':
                break;
        }
    }

    private function setContent($content, $tag) {
        $content = str_replace(array("\r\n", "\n", "\r"), '', $content);
        if(strpos($content, 'twitter.com')!==false) {
            $this->tag = $tag;
            $matches = array();
            preg_match($this->twitter_content_regex, $content, $matches);
            if(isset($matches[1])) {
                $this->content = $matches[1];
                $matches = array();
                preg_match($this->twitter_url_regex, $this->content, $matches);
                if(isset($matches[1])) {
                    $this->url = 'https://twitter.com'.$matches[1];
                }
            }
        }
        // MSNBC and NBC
        else if(strpos($content, 'theplatform.com')!==false) {
            $this->tag = $tag;
            $dom = new DOMDocument();
						$dom->loadHTML($content);
						$this->url = $dom->getElementsByTagName('iframe')->item(0)->getAttribute('src');
        }
        else if(strpos($content, 'facebook.com')!==false) {
            $this->tag = $tag;
            $this->url = $content;
        }
    }
}

$inbed = new Inbed();

function inbed($atts=null, $content, $tag) {
    global $inbed;
		$content = html_entity_decode($content);
    return $inbed->embed($atts, $content, $tag);
}

add_shortcode('inbed', 'inbed');
add_shortcode('paypal', 'inbed');
add_shortcode('kimbia', 'inbed');
add_shortcode('storify', 'inbed');
add_shortcode('flickr', 'inbed');
add_shortcode('today', 'inbed');
add_shortcode('msnbc', 'inbed');
add_shortcode('nbc', 'inbed');
add_shortcode('nbcnews', 'inbed');
add_shortcode('fbvideo', 'inbed');
add_shortcode('fbpost', 'inbed');
add_shortcode('ustream', 'inbed');
add_shortcode('image', 'inbed');
add_shortcode('video', 'inbed');
add_shortcode('vimeo', 'inbed');
add_shortcode('youtube', 'inbed');
add_shortcode('instagram', 'inbed');
add_shortcode('soundcloud', 'inbed');
add_shortcode('vine', 'inbed');
add_shortcode('wufoo', 'inbed');
add_shortcode('polldaddy', 'inbed');
add_shortcode('gist', 'inbed');
add_shortcode('tweet', 'inbed');
add_shortcode('twitter', 'inbed');
add_shortcode('twitter-timeline', 'inbed');

add_filter('no_texturize_shortcodes', 'inbed_all_no_texture', 1);

function inbed_all_no_texture($shortcodes) {
  $shortcodes[] = 'nbc';
  $shortcodes[] = 'msnbc';
  $shortcodes[] = 'fbpost';
  return $shortcodes;
}
