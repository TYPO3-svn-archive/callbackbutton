<?php

/**
 * Description of createHTMLTags
 * HelperClass to create HTML-Tags with PHP 
 * in order to differ between the logical and markup
 * Part of Webapplications.
 * Useful for e.g. typo3Extensions
 * 
 * @author Tobias Hahn
 */
class createHTMLTags {
	//put your code here
	
	/*
	 * Creates div-Tag with some chosen content
	 * and an some parameter to style it bey using css 
	 * 
	 * @param $content -> content displayed in div
	 * @param $id -> id for identification (css) optional
	 * @param $class -> class parameter of tag (css)optional
	 */
	function getDiv($content = null, $id = null, $class = null){
		$retId = '';
		$retClass = '';
		
		if(isset($id)){
			$retId = 'id="' . $id . '" ';
		}
		
		if(isset($class)){
			$retClass = 'class="' . $class . '" ';
		}
		$divTag = '<div ' . $retId. $retClass . '>' . $content . '</div> ';
		
		return $divTag;
	}
		
	function getOptions($value, $caption){
		 
		return '<option value="' . $value . '>' . $caption . '</option>';
		
	}
	
	function getBreaks(){
		
		return '<br />';
	}
	
	function getImg($src, $width = null, $height = null, $id= null, $class = null){
		
		$ret = '<img src="' . $src . '" ';
		$ret .= 'width="' . $width . '" ';
		$ret .= 'height = "' . $height . '" ';
		$ret .=	' id="' . $id . '" class="' . $class . '"/>';
		
		return $ret;
	}
	
	function getHeader($size, $content = null, $id = null, $class = null){
		$idStr = 'id="' . $id . '" ';
		$classStr = 'class="' . $class . '" ';
		
		$header = '<h' . $size .' ' . $idStr . $classStr . '>';
		$header .= $content . '</h' . $size . '>' ;
		return $header;
		
	}
	
	function getSpan($content = null, $class = null ,$id = null, $onclick = null, $name = null){
		return '<span id="' . $id  . '" class = "' . $class . '" onclick = " ' . $onclick .
			' " name="'. $name . '">' . $content . '</span>';
	}
	
	function getHr(){
		return '<hr />';
	}
	
	function getBold($content){
		return '<b>' . $content . '</b>';
	}
	
	function getButton($value = null, $name = null, $onclick = null,  $class = null , $img=null){
		
		$ret = '<input type="button" onclick="' . $onclick  . '" name="'.$name.'" value="' . $value . '" class="' . $class . '"' ;
		if($img!==null){
		$ret.= '" >';
		$ret .= $img;
		$ret.="</button>";
		}else{
			$ret.= ' />';
		}
		return $ret;
	}
	
	function getLink($content, $href = null,  $id=null, $class = null, $onclick = null){
		$ret = '<a href = "' . $href . '" $id = "' . $id . '"';
		$ret .= 'class = "' . $class . '" onclick = "' . $onclick . '" >';
		$ret .= $content . '</a>';
		
		return $ret;   
	}
	
	function getInput($content = null, $value = null, $type , $class = null, $id = null, $name = null){
		$ret = "<input type='" . $type . "' value = '" . $value . "' class = '" . $class . "'";
		$ret.= "id= '" . $id . "' name = '" . $name . "' /> ". $content;
		return $ret;
		
	}
}

?>
