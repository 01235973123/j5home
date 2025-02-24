<?php

/**
 * @package 	mod_os_contentslider - OS ContentSlider Module
 * @version		1
 * @created		July 2013

 * @author		Dang Thuc Dam
 * @email		damdt@joomservices.com
 * @website		http://joomservices.com
 * @support		http://joomservices.com
 * @copyright	Copyright (C) 2023 Joomdonation. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Object\CMSObject;
use Gumlet\ImageResize;
jimport('joomla.filesystem.file');

if (!class_exists('OsImageHelper')) 
{
	class OsImageHelper extends CMSObject 
	{
		static function getImageCreateFunction($type) 
		{
			switch ($type) 
			{
				case 'jpeg':
				case 'jpg':
					$imageCreateFunc = 'imagecreatefromjpeg';
					break;

				case 'png':
					$imageCreateFunc = 'imagecreatefrompng';
					break;

				case 'bmp':
					$imageCreateFunc = 'imagecreatefrombmp';
					break;

				case 'gif':
					$imageCreateFunc = 'imagecreatefromgif';
					break;

				case 'vnd.wap.wbmp':
					$imageCreateFunc = 'imagecreatefromwbmp';
					break;

				case 'xbm':
					$imageCreateFunc = 'imagecreatefromxbm';
					break;

				default:
					$imageCreateFunc = 'imagecreatefromjpeg';
			}

			return $imageCreateFunc;
		}

		static function getImageSaveFunction($type) {
			switch ($type) {
				case 'jpeg':
					$imageSaveFunc = 'imagejpeg';
					break;

				case 'png':
					$imageSaveFunc = 'imagepng';
					break;

				case 'bmp':
					$imageSaveFunc = 'imagebmp';
					break;

				case 'gif':
					$imageSaveFunc = 'imagegif';
					break;

				case 'vnd.wap.wbmp':
					$imageSaveFunc = 'imagewbmp';
					break;

				case 'xbm':
					$imageSaveFunc = 'imagexbm';
					break;

				default:
					$imageSaveFunc = 'imagejpeg';
			}

			return $imageSaveFunc;
		}

		static function resize($imgSrc, $imgDest, $dWidth, $dHeight, $crop = true, $quality = 100) 
		{
			global $configClass;

			$quality	= (int) $configClass['images_quality'];
			$info		= getimagesize($imgSrc);
			
			$sWidth		= $info[0];
			$sHeight	= $info[1];

			if($sWidth > 0 && $sHeight > 0)
			{

				if ($sHeight / $sWidth > $dHeight / $dWidth) 
				{
					$width = $sWidth;
					$height = round(($dHeight * $sWidth) / $dWidth);
					$sx = 0;
					$sy = round(($sHeight - $height) / 3);
				}
				else 
				{
					$height = $sHeight;
					$width = round(($sHeight * $dWidth) / $dHeight);
					$sx = round(($sWidth - $width) / 2);
					$sy = 0;
				}

				if (!$crop) {
					$sx = 0;
					$sy = 0;
					$width = $sWidth;
					$height = $sHeight;
				}

			}

			else
			{
				$width  = $dWidth;
				$height = $dHeight;
				$sx = 0;
				$xy = 0;
			}

			//echo "$sx:$sy:$width:$height";die();
			$ext = str_replace('image/', '', $info['mime']);

			if($ext == "webp" || $ext == "jpeg")
			{
				static::resizeImageUseNewLib($imgSrc, $imgDest, $dWidth, $dHeight, $quality);
			}
			else
			{
			
				$imageCreateFunc = self::getImageCreateFunction($ext);
				$imageSaveFunc = self::getImageSaveFunction(File::getExt($imgDest));

				$sImage = $imageCreateFunc($imgSrc);
				$dImage = imagecreatetruecolor($dWidth, $dHeight);

				// Make transparent
				if ($ext == 'png') 
				{
					imagealphablending($dImage, false);
					imagesavealpha($dImage, true);
					$transparent = imagecolorallocatealpha($dImage, 255, 255, 255, 127);
					imagefilledrectangle($dImage, 0, 0, $dWidth, $dHeight, $transparent);
				}

				imagecopyresampled($dImage, $sImage, 0, 0, $sx, $sy, $dWidth, $dHeight, $width, $height);
				if ($ext == 'png') 
				{
					$imageSaveFunc($dImage, $imgDest, 9);
				}
				else if ($ext == 'gif') 
				{
					$imageSaveFunc($dImage, $imgDest);
				}
				else 
				{
					$imageSaveFunc($dImage, $imgDest, $quality);
				}
			}
		}

		public static function resizeImageUseNewLib($source, $destination, $width, $height, $quality)
		{
			global $configClass;

			/** @var \Composer\Autoload\ClassLoader $autoLoader */
			$autoLoader = include JPATH_LIBRARIES . '/vendor/autoload.php';
			$autoLoader->setPsr4('Gumlet\\', JPATH_ROOT . '/components/com_osproperty/helpers/imageresize/lib');

			$image = new ImageResize($source);

			// Set quality for the image resizing
			
			$image->quality_png = (int) $quality;
			$image->quality_jpg = (int) $quality;
			$image->quality_webp = (int) $quality;
			
			$image->crop($width, $height)
				->save($destination);
			
		}

		static function createImage($imgSrc, $imgDest, $width, $height, $crop = true, $quality = 100) 
		{
			if (File::exists($imgDest)) 
			{
				$info = getimagesize($imgDest, $imageinfo);
				// Image is created
				if (($info[0] == $width) && ($info[1] == $height)) {
					return;
				}
			}
			self::resize($imgSrc, $imgDest, $width, $height, $crop, $quality);
		}
	}
}
?>
