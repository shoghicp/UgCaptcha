<?php
/*
  _    _                                      _                   _                 
 | |  | |                                    | |                 | |                
 | |  | |   __ _    __ _   _ __ ___     ___  | |   __ _   _ __   | |   __ _   _   _ 
 | |  | |  / _` |  / _` | | '_ ` _ \   / _ \ | |  / _` | | '_ \  | |  / _` | | | | |
 | |__| | | (_| | | (_| | | | | | | | |  __/ | | | (_| | | |_) | | | | (_| | | |_| |
  \____/   \__, |  \__,_| |_| |_| |_|  \___| |_|  \__,_| | .__/  |_|  \__,_|  \__, |
            __/ |                                        | |                   __/ |
           |___/                                         |_|                  |___/ 


    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

 

 *
 * @author shoghicp@gmail.com
 *

*/

function CheckCaptcha($Captcha){
	$Captcha = strtolower($Captcha);
	if(isset($_SESSION['captcha']) and $_SESSION['captcha'] == $Captcha){
		unset($_SESSION['captcha']);
		return true;
	}else{
		unset($_SESSION['captcha']);
		return false;
	}
}
function CaptchaController(){
	if((!isset($_SESSION['captcha_time']) or $_SESSION['captcha_time'] <= (time() - 2000)) and !$_POST){
		NewCaptcha();
	}elseif($_POST and $_POST['captcha'] != '' and (!isset($_SESSION['captcha_time']) or $_SESSION['captcha_time'] <= (time() - 2000))){
		if(CheckCaptcha($_POST['captcha'])){
			$_SESSION['captcha_time'] = time();
		}else{
			NewCaptcha();
		}
	}elseif($_SESSION['captcha_time'] > (time() - 2000)){
		$_SESSION['captcha_time'] = time();
	}
}
function NewCaptcha(){
	if(!CheckCaptcha($_POST['captcha'])){
		$parse = array();
		$parse['inputs'] = '';
		foreach($_POST as $Value => $Val){
			if($Value != 'captcha'){
				$parse['inputs'] .= '<input type="hidden" name="'.$Value.'" value="'.$Val.'"/>';
			}
		}
		$TPL = '<script type="text/javascript">	function reloadCaptcha() { 	document.images.captcha.src = "captcha.php?ghost=" + new Date().getSeconds();}</script><form method="post">{inputs}<table width="300"><tr><td class="c" colspan="2">Proteccion Captcha</td></tr><tr><th colspan="2">Sistema anti-bots</th></tr><tr><th>Introduce los car&aacute;cteres negros (4)<center><div id="captcha" style="border: 1px dashed rgb(102, 102, 102);width:150px;cursor:pointer;" onclick="reloadCaptcha();"><img name="captcha" src="'.$root.'captcha.php?ghost='. time() .'"></div></center></th><th>- No existe el 0<br>- No es sensible a mayusculas<br/>- Haz click en la imagen para recargar<br/><br/><input name="captcha" size="20" maxlength="4" type="text" style="border:1px solid lime;"/></th></tr><tr><th colspan="2"><input type="submit" value="Comprobar"/></th></tr></table></form>';
		
		display(parsetemplate($TPL, $parse),'Captcha', false);
	}
}
if(!defined("INSIDE")){
	session_start();
	$captcha = '';

	// Set the content-type
	header('Content-type: image/png');
	header('Server: UGamelaPlay Captcha');	
	header('X-Powered-By: UGamelaPlay Captcha');
	header('X-/Powered-By: UGamelaPlay Captcha');
	header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	header('Pragma: no-cache');
	header('Served-By: UGamelaPlay');
	header('X-Worlddomination: yes');
	// Create the image
	$im = imagecreatetruecolor(150, 30);

	// Create some colors
	$white = imagecolorallocate($im, 100, 255, 255);
	$wh2 = imagecolorallocate($im, 255, 255, 255);
	$grey = imagecolorallocate($im, 128, 128, 128);
	$black = imagecolorallocate($im, 0, 0, 0);
	imagefilledrectangle($im, 0, 0, 149, 29, $white);

	// Replace path by your own font path
	$font = 'Captcha.ttf';
	$caracteres = "15678QRSTU9ABCDEFGHI234JKLMNOPVWXYZ";
	// Add some shadow to the text
	for($i = 1; $i < 12; ++$i){
		$char = $caracteres{rand(0,34)};
		imagettftext($im, 20, mt_rand(0, 360), mt_rand(0, 150), mt_rand(0, 30), $grey, $font, $char);	
		imageline($im, mt_rand(0, 150), mt_rand(0, 30), mt_rand(0, 150), mt_rand(0, 30), $wh2);
	}

	// Add the text
	$array = array(mt_rand(340, 355), mt_rand(0, 20));
    for($i = 2;$i<6;++$i) {
		$text = $caracteres{mt_rand(0,34)};
        $captcha .= $text;
		imagettftext($im, mt_rand(17, 19), $array[mt_rand(0, 1)], $i * $i * $i, 20, $black, $font, $text);
    }
	imageellipse($im, mt_rand(59, 61), mt_rand(14, 16), mt_rand(70, 111), mt_rand(17, 23), $black);
	$_SESSION['captcha'] = strtolower($captcha);
	//imageline($im, 0, 14, 150, 16, $wh2);
	//imageline($im, 0, mt_rand(11, 15), 150, mt_rand(15,19), $white);

	imagepng($im);
	imagedestroy($im);
}

?>
