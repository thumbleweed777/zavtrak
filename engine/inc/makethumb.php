<?php
/*
=====================================================
 DataLife Engine Nulled by M.I.D-Team
-----------------------------------------------------
 http://www.mid-team.ws/
-----------------------------------------------------
 Copyright (c) 2004,2009 SoftNews Media Group
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: makethumb.php
-----------------------------------------------------
 Назначение: создание уменьшенных копий
=====================================================
*/
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

$gd_version = 2;

class thumbnail {
	var $img;
	var $watermark_image_light;
	var $watermark_image_dark;
	
	function thumbnail($imgfile) {
		//detect image format
		
#php 5.3
# 		$this->img['format'] = ereg_replace( ".*\.(.*)$", "\\1", $imgfile );


		$this->img['format'] = preg_replace( "/.*\.(.*)$/", "\\1", $imgfile );
		$this->img['format'] = strtoupper( $this->img['format'] );
		if( $this->img['format'] == "JPG" || $this->img['format'] == "JPEG" ) {
			$this->img['format'] = "JPEG";
			$this->img['src'] = @imagecreatefromjpeg( $imgfile );
		} elseif( $this->img['format'] == "PNG" ) {
			$this->img['format'] = "PNG";
			$this->img['src'] = @imagecreatefrompng( $imgfile );
		} elseif( $this->img['format'] == "GIF" ) {
			$this->img['format'] = "GIF";
			$this->img['src'] = @imagecreatefromgif( $imgfile );
		} else {
			echo "Not Supported File! Thumbnails can only be made from .jpg, gif and .png images!";
			@unlink( $imgfile );
			exit();
		}

		if( !$this->img['src'] ) {
			echo "Not Supported File! Thumbnails can only be made from .jpg, gif and .png images!";
			@unlink( $imgfile );
			exit();
		
		}

		$this->img['lebar'] = @imagesx( $this->img['src'] );
		$this->img['tinggi'] = @imagesy( $this->img['src'] );
		$this->img['lebar_thumb'] = $this->img['lebar'];
		$this->img['tinggi_thumb'] = $this->img['tinggi'];
		//default quality jpeg
		$this->img['quality'] = 90;
		
	}
	
	function size_auto($size = 100, $site = 0) {
		global $gd_version;
		
		$site = intval( $site );
		
		if( $this->img['lebar'] <= $size and $this->img['tinggi'] <= $size ) {
			$this->img['lebar_thumb'] = $this->img['lebar'];
			$this->img['tinggi_thumb'] = $this->img['tinggi'];
			return 0;
		}
		
		switch ($site) {
			
			case "1" :
				if( $this->img['lebar'] <= $size ) {
					$this->img['lebar_thumb'] = $this->img['lebar'];
					$this->img['tinggi_thumb'] = $this->img['tinggi'];
					return 0;
				} else {
					$this->img['lebar_thumb'] = $size;
					$this->img['tinggi_thumb'] = ($this->img['lebar_thumb'] / $this->img['lebar']) * $this->img['tinggi'];
				}
				
				break;
			
			case "2" :
				if( $this->img['tinggi'] <= $size ) {
					$this->img['lebar_thumb'] = $this->img['lebar'];
					$this->img['tinggi_thumb'] = $this->img['tinggi'];
					return 0;
				} else {
					$this->img['tinggi_thumb'] = $size;
					$this->img['lebar_thumb'] = ($this->img['tinggi_thumb'] / $this->img['tinggi']) * $this->img['lebar'];
				}
				
				break;
			
			default :
				
				if( $this->img['lebar'] >= $this->img['tinggi'] ) {
					$this->img['lebar_thumb'] = $size;
					$this->img['tinggi_thumb'] = ($this->img['lebar_thumb'] / $this->img['lebar']) * $this->img['tinggi'];
				
				} else {
					
					$this->img['tinggi_thumb'] = $size;
					$this->img['lebar_thumb'] = ($this->img['tinggi_thumb'] / $this->img['tinggi']) * $this->img['lebar'];
				
				}
				
				break;
		}
		
		if( $gd_version == 1 ) {
			$this->img['des'] = imagecreate( $this->img['lebar_thumb'], $this->img['tinggi_thumb'] );
			@imagecopyresized( $this->img['des'], $this->img['src'], 0, 0, 0, 0, $this->img['lebar_thumb'], $this->img['tinggi_thumb'], $this->img['lebar'], $this->img['tinggi'] );
		} elseif( $gd_version == 2 ) {
			$this->img['des'] = imagecreatetruecolor( $this->img['lebar_thumb'], $this->img['tinggi_thumb'] );
			@imagecopyresampled( $this->img['des'], $this->img['src'], 0, 0, 0, 0, $this->img['lebar_thumb'], $this->img['tinggi_thumb'], $this->img['lebar'], $this->img['tinggi'] );
		}
		
		$this->img['src'] = $this->img['des'];
		return 1;
	}
	
	function jpeg_quality($quality = 90) {
		//jpeg quality
		$this->img['quality'] = $quality;
	}
	
	function save($save = "") {
		
		if( $this->img['format'] == "JPG" || $this->img['format'] == "JPEG" ) {
			//JPEG
			imagejpeg( $this->img['src'], "$save", $this->img['quality'] );
		} elseif( $this->img['format'] == "PNG" ) {
			//PNG
			imagepng( $this->img['src'], "$save" );
		} elseif( $this->img['format'] == "GIF" ) {
			//GIF
			imagegif( $this->img['src'], "$save" );
		}
		
		imagedestroy( $this->img['src'] );
	}
	
	function show() {
		if( $this->img['format'] == "JPG" || $this->img['format'] == "JPEG" ) {
			//JPEG
			imageJPEG( $this->img['src'], "", $this->img['quality'] );
		} elseif( $this->img['format'] == "PNG" ) {
			//PNG
			imagePNG( $this->img['src'] );
		} elseif( $this->img['format'] == "GIF" ) {
			//GIF
			imageGIF( $this->img['src'] );
		}
		
		imagedestroy( $this->img['src'] );
	}
	
	// *************************************************************************
	function insert_watermark($min_image) {
		global $config;
		$margin = 7;
		
		$this->watermark_image_light = ROOT_DIR . '/templates/' . $config['skin'] . '/dleimages/watermark_light.png';
		$this->watermark_image_dark = ROOT_DIR . '/templates/' . $config['skin'] . '/dleimages/watermark_dark.png';
		
		$image_width = imagesx( $this->img['src'] );
		$image_height = imagesy( $this->img['src'] );
		
		list ( $watermark_width, $watermark_height ) = getimagesize( $this->watermark_image_light );
		
		$watermark_x = $image_width - $margin - $watermark_width;
		$watermark_y = $image_height - $margin - $watermark_height;
		
		$watermark_x2 = $watermark_x + $watermark_width;
		$watermark_y2 = $watermark_y + $watermark_height;
		
		if( $watermark_x < 0 or $watermark_y < 0 or $watermark_x2 > $image_width or $watermark_y2 > $image_height or $image_width < $min_image or $image_height < $min_image ) {
			return;
		}
		
		$test = imagecreatetruecolor( 1, 1 );
		imagecopyresampled( $test, $this->img['src'], 0, 0, $watermark_x, $watermark_y, 1, 1, $watermark_width, $watermark_height );
		$rgb = imagecolorat( $test, 0, 0 );
		
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		
		$max = min( $r, $g, $b );
		$min = max( $r, $g, $b );
		$lightness = ( double ) (($max + $min) / 510.0);
		imagedestroy( $test );
		
		$watermark_image = ($lightness < 0.5) ? $this->watermark_image_light : $this->watermark_image_dark;
		
		$watermark = imagecreatefrompng( $watermark_image );
		
		imagealphablending( $this->img['src'], TRUE );
		imagealphablending( $watermark, TRUE );
		
		imagecopy( $this->img['src'], $watermark, $watermark_x, $watermark_y, 0, 0, $watermark_width, $watermark_height );
		
		imagedestroy( $watermark );
	
	}

}
?>