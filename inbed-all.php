<?php
/*
Plugin Name: Inbed All
Plugin URI: http://github.com/walker/inbed-all
Description: Embed everything
Version: 1.0.0
Author: Walker Hamilton
Author URI: http://walkerhamilton.com/
*/

class Inbed {
	
	protected $tag = null;
	
	protected $id = null;
	
	protected $url = null;
	
	private $youtube_regex = '/(youtu.be\/|v\/|u\/\w\/|embed\/|\?v=|\&v=)([a-zA-Z0-9_-]{11})/';
	
	private $vimeo_regex = '/vimeo.com\/([0-9]{1,})/';
	
	private $vine_regex = '/vine.co\/v\/([a-zA-Z0-9]{11})/';
	
	private $msnbc_content_regex = '/http:\/\/player.theplatform.com\/([a-zA-Z0-9=\/\?\_\-]{1,})\'/';
	
	private $twitter_content_regex = '/<blockquote[0-9a-zA-Z\-"\ \=]*>(.*)<\/blockquote>/';
	
	private $twitter_url_regex = '/href\=\"https\:\/\/twitter.com([\-\_\/0-9a-zA-Z]{5,})/';
	
	private $instagram_regex = '/instagram.com\/p\/([\-\_0-9a-zA-Z]{5,})/';
	
	public function embed($atts, $content, $tag) {
		/* reset */
		$this->tag = null;
		$this->id = null;
		$this->url = null;
		/* end reset */
		
		if($atts)
			extract( $atts );
		
		if($content) {
			$this->setContent($content);
		}
		
		if(isset($tag)) {
			$this->tag = $tag;
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
			$this->setURL($url);
		}
		
		if(($tag == 'video' || $tag=='image') && (!isset($url) || empty($url))) {
			return 'You must provide a URL with the video tag: <code>[video url="https://www.youtube.com/watch?v=99CRJR6IL8c"]</code>';
		} else if($tag=='image' && (!isset($url) || empty($url))) {
			return 'You must provide a URL with the image tag: <code>[image url="http://25.media.tumblr.com/51a0d1b6d56036dbd7b3aa228594961a/tumblr_mw8h7nCbr01t1x93po1_500.jpg"]</code>';
		}
		
		if($this->id && $this->tag) {
			switch($this->tag) {
				case 'vimeo':
					return '<div class="video-container"><iframe width="100%" height="100%" src="//player.vimeo.com/video/'.$this->id.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
					break;
				case 'youtube':
					return '<div class="video-container"><iframe width="100%" height="100%" src="//www.youtube.com/embed/'.$this->id.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
					break;
				case 'ustream':
					return '<div class="video-container"><iframe width="100%" height="100%" src="http://www.ustream.tv/embed/'.$this->id.'?v=3&amp;wmode=direct" scrolling="no" frameborder="0" style="border: 0px none transparent;"></iframe></div>';
					break;
				case 'instagram':
					return '<iframe src="//instagram.com/p/'.$this->id.'/embed/" width="100%" height="100%" frameborder="0" scrolling="no" allowtransparency="true"></iframe>';
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
					return '<div class="video-container"><iframe width="100%" height="100%" class="vine-embed" src="'.$vine_url.'" frameborder="0"></iframe><script async src="//platform.vine.co/static/scripts/embed.js" charset="utf-8"></script></div>';
					break;
				case 'instagram':
					return '<div class="image-container"><iframe width="100%" height="100%" src="//instagram.com/p/'.$matches[2].'/embed/" frameborder="0" scrolling="no" allowtransparency="true"></iframe></div>';
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
					return '<div class="audio-container"><iframe width="100%" height="450" scrolling="no" frameborder="no" src="'.$sc_url.'"></iframe></div>';
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
						return '<div class="wufoo-container"><iframe height="100%" allowTransparency="true" frameborder="0" scrolling="'.$scrolling.'" style="width:100%;border:none"  src="https://'.$username.'.wufoo.com/embed/'.$this->id.'/"><a href="https://'.$username.'.wufoo.com/forms/'.$this->id.'/">Fill out my Wufoo form!</a></iframe></div>';
					} else {
						return '<div class="wufoo-container"><div id="wufoo-'.$this->id.'">
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
				case 'msnbc':
					return '<iframe src="'.$this->url.'" height="100%" width="100%" scrolling="no" border="no" ></iframe>';
				case 'twitter':
					if(isset($conversation) && $conversation=='on')
						$conversation = '';
					else
						$conversation = ' data-conversation="none"';
					if(isset($cards) && $cards=='on')
						$cards = '';
					else
						$cards = ' data-conversation="none"';
					return '<div class="twitter-container"><blockquote class="twitter-tweet" lang="en"'.$cards.$conversation.'>'.$this->content.'</blockquote><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script></div>';
					break;
				case 'image':
					break;
				case 'gist':
					return '<div class="gist-container"><script src="'.$this->url.'.js"></script></div>';
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
	
	private function setURL($url) {
		if(strpos($url, 'youtube')!==false || strpos($url, 'youtu.be')!==false)
			$this->tag = 'youtube';
		if(strpos($url, 'vimeo')!==false)
			$this->tag = 'vimeo';
		if(strpos($url, 'instagram.com')!==false)
			$this->tag = 'instagram';
		if(strpos($url, 'soundcloud.com')!==false)
			$this->tag = 'soundcloud';
		if(strpos($url, 'vine.co')!==false)
			$this->tag = 'vine';
		switch($this->tag) {
			case 'soundcloud':
				require_once dirname(__FILE__).'/soundcloud/Services/Soundcloud.php';
				$client = new Services_Soundcloud(SOUNDCLOUD_CLIENT_ID, SOUNDCLOUD_SECRET, 'http://',$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				$track = json_decode($client->get('resolve', array('url' => $url)));
				$this->id = $track->id;
				break;
			case 'vimeo':
				$matches = array();
				preg_match($this->vimeo_regex, $url, $matches);
				if(isset($matches[1]))
					$this->id = $matches[1];
				break;
			case 'iframe':
				$this->url = $url;
				break;
			case 'vine':
				$matches = array();
				preg_match($this->vine_regex, $url, $matches);
				if(isset($matches[1]))
					$this->id = $matches[1];
				break;
			case 'youtube':
				$matches = array();
				preg_match($this->vine_regex, $url, $matches);
				if(isset($matches[1]))
					$this->id = $matches[1];
				break;
			case 'instagram':
				$matches = array();
				preg_match($this->instagram_regex, $url, $matches);
				if(isset($matches[1]))
					$this->id = $matches[1];
				break;
			case 'twitter':
				break;
			case 'gist':
				$this->url = str_replace('.js', '', $url);
				break;
			case 'polldaddy':
				break;
		}
	}
	
	private function setContent($content) {
		$content = str_replace(array("\r\n", "\n", "\r"), '', $content);
		if(strpos($content, 'twitter.com')!==false) {
			$this->tag = 'twitter';
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
		} else if(strpos($content, 'theplatform.com')!==false) {
			$this->tag = 'msnbc';
			$matches = array();
			preg_match($this->msnbc_content_regex, $content, $matches);
			if(isset($matches[1])) {
				$this->url = 'http://player.theplatform.com/'.$matches[1];
			}
		}
	}
}

$inbed = new Inbed();

function inbed($atts=null, $content, $tag) {
	global $inbed;
	return $inbed->embed($atts, $content, $tag);
}

add_shortcode('inbed', 'inbed');
add_shortcode('msnbc', 'inbed');
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
