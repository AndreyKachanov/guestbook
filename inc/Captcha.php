<?php 
	class Captcha 
	{

	    public $keycaptcha;
	    public $image;

			//генерируем капчу
		public function __construct() 
		{
			$letters = 'ABCDEFGKIJKLMNOPQRSTUVWXYZabcdefgkijklmnopqrstuvwxyz0123456789';
			$caplen = 5;
			$width = 210; 
			$height = 39;
			$font = "fonts/arial.ttf";
			$fontsize = 18;

			header('Content-type: image/png');

			$im = imagecreatetruecolor($width, $height);
			$im = imageCreateFromJPEG("img/bg_capture.jpg");



			imagesavealpha($im, true);

			$bg = imagecolorallocatealpha($im, 0, 0, 0, 127);

			imagefill($im, 0, 0, $bg);
			 
				$captcha = '';

			for ($i = 0; $i < $caplen; $i++) {
			  $captcha .= $letters[ rand(0, strlen($letters)-1) ];
			  $x = ($width - 20) / $caplen * $i + 10;
			  $x = rand($x, $x + 4);
			  $y = $height - ( ($height - $fontsize) / 2 );
			  $curcolor = imagecolorallocate( $im, rand(0, 100), rand(0, 100), rand(0, 100) );
			  $angle = rand(-25, 25);
			  imagettftext($im, $fontsize, $angle, $x, $y, $curcolor, $font, $captcha[$i]);
			}

			$this->keycaptcha = $captcha;
						
			$this->image = imagepng($im);

			imagedestroy($im);			
		}


	    public function printCaptcha() 
	    {
	    	return $this->image;
	    }

	    public function getKeyCaptcha()
	    {
	        return $this->keycaptcha;
	    }    
}