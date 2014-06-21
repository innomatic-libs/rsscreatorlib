<?php

	/* 
		RSS class
		Copyright (c) 2002, by Aral Balkan

		Usage:
			new RSS ( dataSource ) 

			dataSource: array of associated arrays (item)
			item: associated array:
				-> title : title of item (str, suggest. max 100 chars for RSS 0.91 compatiblity)
				-> link : url that item links to (str, suggest. max 500 chars for RSS 0.91 compatiblity)
				-> description : info about item (str, suggest. max 500 chars for RSS 0.91 compatiblity)

			Methods:

			addDataSource ( dataSource )		- add new dataSource to RSS instance

			addChannel ( channel )				- add Channel info 
				channel : associated array
					-> about: url where this channel will be located (url of RSS file)
					-> title: title of channel (str, suggest. max 40 chars for RSS 0.91 compatiblity)
					-> link: link to root of site / section that this RSS serves (str, suggest. max 500 chars for RSS 0.91 compatiblity)
					-> description: info about this channel (str, suggest. max 500 chars for RSS 0.91 compatiblity)
			
			addImage ( image )						- add Image info (optional)
				image : associated array
					-> title : title of image (str, suggest. max 100 chars for RSS 0.91 compatiblity)
					-> link : url where image should link to (str, suggest. max 500 chars for RSS 0.91 compatiblity)
					-> url	: url where image is located (str, suggest. max 500 chars for RSS 0.91 compatiblity)

			addItem	 ( item )							- adds a single item to the RSS, see item structure, above.

			get( void )									- returns the RSS document as a string

		Limitations:
			* Currently does not support the optional <textInput> core element.
			* Arguments are not validated
	*/

	class RSSCreator {
		function _construct() {
			$this->items = array();
		}

		/*
			Public methods
		*/
		function addDataSource( $dataSource ) {
			// note: no dataSource validation is done, assumed to be well-formed.
			for ($item = 0; $item  < count($dataSource); $item++) {
				array_push($this->items, $dataSource[$item]);
			}
		}

		function addChannel ( $channel ) {
			$this->channel = array (
				about => $channel['url'],
				title => $channel['title'],
				link =>  $channel['link'],
				description => $channel['description']
			);
		}

		function addImage ( $image ) {
			$this->image = array (
				title => $image[title],
				link => $image[link],
				url => $image[url]
			);
		}

		function addItem ( $item ) {
			array_push($this->items, $item);
		}

		// returns string with RSS 1.0 compliant document
		function get() {
			$doc = "";
			$doc .= $this->getHeader();
			$doc .= $this->getChannel();
			$doc .= $this->getImage();
			$doc .= $this->getItems();
			$doc .= "</rdf:RDF>";
			return $doc;
		}

		/*
			Private methods
		*/
		function getHeader() {
			$header = "<?xml version=\"1.0\"?>
<rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns=\"http://purl.org/rss/1.0/\">\n";
			return $header;
		}

		function getChannel() {
			// start channel info
			$channel = "<channel rdf:about=\"".$this->channel['about']."\">
<title>".$this->channel['title']."</title>
<link>".$this->channel['link']."</link>
<description>".$this->channel['description']."</description>";

			// add image rdf resource if it exists
			if (isset($this->image)) {
				$channel .= "<image rdf:resource=\"".$this->image['url']."\"/>";
			}

			// add item resource info if items exist
			if (count($this->items) > 0 ) {
				$channel .= " <items>\n<rdf:Seq>";

				for ($item = 0; $item < count($this->items); $item++) {
					$channel .= "<rdf:li resource=\"".$this->items[$item]['link']."\"/>";
				}

				$channel .= "</rdf:Seq>\n</items>\n";

			}

			// end channel info
			$channel .= "</channel>\n";

			return $channel;
		}

		function getImage() {
			if (isset($this->image)) {
				$image = "<image rdf:about=\"".$this->image['url']."\">
<title>".$this->image['title']."</title>
<link>".$this->image['link']."</link>
<url>".$this->image['url']."</url>
</image>";
				return $image;
			} else {
				return "";
			}
		}

		function getItems() {
			$items = "";
			for ($item = 0; $item < count($this->items); $item++) {
				$items .= "<item rdf:about=\"".$this->items[$item]['link']."\">
<title>".$this->items[$item]['title']."</title>
<link>".$this->items[$item]['link']."</link>
<description>".$this->items[$item]['description']."</description>
</item>";
			}
			return $items;
		}

	} // end class: RSS

?>
