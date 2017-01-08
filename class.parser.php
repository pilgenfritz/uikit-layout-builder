<?php

final class Parser
{
	private $doc;
	private $docRoot;
	private $cbFile;
	private $tag;
	private $args;
	
	// ----------------------------------------------------------
	// public constructor
	// ----------------------------------------------------------
	
	public function __construct($page = null)
	{
		// DOMDocument
		$this->doc = null;
		// documentElement
		$this->docRoot = null;
		// code-behind file
		$this->cbFile = null;
		// tag searched for, default value of 'var'
		$this->tag = 'var';
		// tag that keeps the string which was passed to the constructor
		$this->args = null;
		
		if($page !== null)
		{
			$this->LoadDocument($page);
		}
		else
		{
			trigger_error("Invalid XHTML Document", E_USER_ERROR);
		}
	}
	
	// ----------------------------------------------------------
	// destructor
	// ----------------------------------------------------------
	
	public function __destruct()
	{
		$this->Fetch();
	}
	
	// ----------------------------------------------------------
	// helper method LoadDocument
	// ----------------------------------------------------------
	
	private function LoadDocument($page = null, $version = '1.0', $encoding = 'utf-8')
	{
		// Create new DOMDocument Object
		$this->doc = new DOMDocument($version, $encoding);
		
		if(file_exists($page))
		{
			$this->doc->load($page);
			$this->docRoot = $this->doc->documentElement;
			$this->args = $page;
			$this->cbFile = $this->docRoot->getAttribute('code');
			$this->docRoot->removeAttribute('code');
		}
		else
		{
			header("Location: /404");
		}
	}
	
	// ----------------------------------------------------------
	// helper method Fetch
	// ----------------------------------------------------------
	
	private function Fetch()
	{
		// Includes code-behind file before printing
		if(!empty($this->cbFile))
		{
			if(strrpos($this->cbFile, ',') !== 0)
			{
				$tmp_files = explode(',', $this->cbFile);
				
				foreach($tmp_files as $file)
				{
					// fix file names
					$filename = dirname($this->args) . "/" . trim($file);
					
					// @warning: unsafe under linux
					// file_exists() not working properly on the server, returning false
					/*if( file_exists(dirname($this->args) . "/" . trim($file)) )
					{
						include($filename);
					}*/
					include($filename);
				}
			}
			else
			{
				include($this->cbFile);
			}
		}
		
		if($this->doc)
		{
			// decode html entities into html tags
			print htmlspecialchars_decode($this->doc->saveHTML());
		}
		else
		{
			trigger_error("DOMDocument is null.", E_USER_ERROR);
		}
	}
	
	// ----------------------------------------------------------
	// public method __alloc()
	// method to allocate the content to the tags
	// ----------------------------------------------------------
	
	public function __alloc($tagname, $replacement)
	{
		// selects all tags
		$tags = $this->docRoot->getElementsByTagName($this->tag);
		// creates a new text node
		$textNode = new DOMText($replacement);
		// replace tags with text nodes
		foreach ($tags as $tag)
		{
			if($tag->attributes->getNamedItem('name')->nodeValue == $tagname)
			{
				$replace = $tag;
				$tag->appendChild($textNode);
				$tag->parentNode->replaceChild($textNode, $replace);
			}
		}
	}
	
	// ----------------------------------------------------------
	// public method __tag()
	// method to assign a new tag name
	// ----------------------------------------------------------
	
	public function __tag($newtag)
	{
		if($newtag)
		{
			$this->tag = $newtag;
		}
	}
}
?>