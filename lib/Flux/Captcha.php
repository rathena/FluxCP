<?php
/**
 * Draws a CAPTCHA image for use in forms such as the registration form.
 */
class Flux_Captcha {
	/**
	 * GD image resource.
	 *
	 * @access protected
	 * @var resource
	 */
	protected $gd;
	
	/**
	 * CAPTCHA options.
	 *
	 * @access public
	 * @var array
	 */
	public $options;
	
	/**
	 * Security code.
	 *
	 * @access public
	 * @var code
	 */
	public $code;
	
	/**
	 * Create new CAPTCHA.
	 */
	public function __construct($options = array())
	{
		$this->options = array_merge(
			array(
				'chars'      => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWWXYZ0123456789',
				'length'     => 5,
				'background' => FLUX_DATA_DIR.'/captcha/background.png',
				'fontPath'   => realpath(FLUX_DATA_DIR.'/captcha/fonts'),
				'fontName'   => 'default.ttf',
				'fontSize'   => 28,
				'yPosition'  => 40,
				'useDistort' => true,
				'distortion' => 10
			),
			$options
		);
		
		// Let GD know where our fonts are.
		//putenv("GDFONTPATH={$this->options['fontPath']}"); // Possibly breaks on Windows?
		
		// Generate security code.
		$this->generateCode();
		
		// Generate CAPTCHA image.
		$this->generateImage();
	}
	
	/**
	 * Generate the security code to be used.
	 *
	 * @access protected
	 */
	protected function generateCode()
	{
		$code  = '';
		$chars = str_split($this->options['chars']);
		
		for ($i = 0; $i < $this->options['length']; ++$i) {
			$code .= $chars[array_rand($chars)];
		}
		
		$this->code = $code;
		return $code;
	}
	
	/**
	 * Generate the image.
	 *
	 * @access protected
	 */
	protected function generateImage()
	{
		$this->gd = imagecreatefrompng($this->options['background']);
		$yPos   = $this->options['yPosition'];
		$font   = "{$this->options['fontPath']}/{$this->options['fontName']}";
		$size   = $this->options['fontSize'];
		$shade1 = imagecolorallocate($this->gd, 240, 240, 240);
		$shade2 = imagecolorallocate($this->gd, 60, 60, 60);
		$shade3 = imagecolorallocate($this->gd, 0, 0, 0);
		
		if (function_exists('imagettftext')) {
			$distA = -$this->options['distortion'];
			$distB = +$this->options['distortion'];
			
			foreach (str_split($this->code, 1) as $i => $char) {
				imagettftext($this->gd, $size + 2, $this->options['useDistort'] ? rand($distA, $distB) : 0, ((28 * $i) + 10), $yPos, $shade3, $font, $char);
				imagettftext($this->gd, $size + 4, $this->options['useDistort'] ? rand($distA, $distB) : 0, ((28 * $i) + 10), $yPos, $shade2, $font, $char);
				imagettftext($this->gd, $size    , $this->options['useDistort'] ? rand($distA, $distB) : 0, ((28 * $i) + 10), $yPos, $shade1, $font, $char);
			}
		}
		else {
			$text  = "FreeType2 is needed\n";
			$text .= "for CAPTCHA support.\n";
			foreach (explode("\n", $text) as $i => $line) {
				imagestring($this->gd, 3, 5, (12 * ($i + 1)), $line, $shade1);
			}
		}
	}
	
	/**
	 * Display image.
	 *
	 * @access public
	 */
	public function display()
	{
		header('Content-Type: image/png');
		imagepng($this->gd);
		exit;
	}
	
	public function __destruct()
	{
		imagedestroy($this->gd);
	}
}
?>
