<?php

class Hapyfish2_Alchemy_Bll_Upload
{
	const MAX_SIZE = 512000;
	const ALLOW = 'jpg,png,gif';
	public static function check($photo)
	{
		if(!$photo['tmp_name']){
			return null;
		}
		if($photo['size'] > self::MAX_SIZE){
			$error = '请上传500k以内的图片';
		}
		list($width, $height, $type, $attr) = @getimagesize($photo['tmp_name']);
		$format = 'qita';
        switch ($type) {
            case IMAGETYPE_GIF:
                if (@imagecreatefromgif($photo['tmp_name'])) $format = 'gif';
                break;
            
            case IMAGETYPE_JPEG:
                if (@imagecreatefromjpeg($photo['tmp_name'])) $format = 'jpg';
                break;
                
            case IMAGETYPE_PNG:
                if (@imagecreatefrompng($photo['tmp_name'])) $format = 'png';
                break;
                
            default:
                break;
        }
        $allow = explode(',', self::ALLOW);
		if(!in_array($format, $allow)){
			$error = '请上传jpg,gif,png格式的图片';
		}
		if(isset($error)){
			return $error;
		}
		$data['type'] = $photo['type'];
		$data['image'] = fread(fopen($photo['tmp_name'],  "r"), $photo['size']);
		fclose($photo['tmp_name']);
		return $data;
	}	
}