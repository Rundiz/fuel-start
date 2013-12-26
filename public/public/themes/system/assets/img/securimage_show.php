<?php

/**
 * Project:     Securimage: A PHP class for creating and managing form CAPTCHA images<br />
 * File:        securimage_show.php<br />
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or any later version.<br /><br />
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.<br /><br />
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA<br /><br />
 *
 * Any modifications to the library should be indicated clearly in the source code
 * to inform users that the changes are not a part of the original software.<br /><br />
 *
 * If you found this script useful, please take a quick moment to rate it.<br />
 * http://www.hotscripts.com/rate/49400.html  Thanks.
 *
 * @link http://www.phpcaptcha.org Securimage PHP CAPTCHA
 * @link http://www.phpcaptcha.org/latest.zip Download Latest Version
 * @link http://www.phpcaptcha.org/Securimage_Docs/ Online Documentation
 * @copyright 2009 Drew Phillips
 * @author drew010 <drew@drew-phillips.com>
 * @version 2.0.1 BETA (December 6th, 2009)
 * @package Securimage
 *
 */

include dirname(dirname(dirname(dirname(dirname(dirname(__DIR__)))))) . DIRECTORY_SEPARATOR . 'fuel' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'securimage' . DIRECTORY_SEPARATOR . 'securimage.php';

$img = new securimage();

// Change some settings

//$img->ttf_file        = './Quiff.ttf';
//$img->captcha_type    = Securimage::SI_CAPTCHA_MATHEMATIC; // show a simple math problem instead of text
$img->case_sensitive  = true;                              // true to use case sensitve codes - not recommended
//$img->image_height = 90;
//$img->image_width = 275;
$img->perturbation = 0.8; // 1.0 = high distortion, higher numbers = more distortion
$img->image_bg_color = new Securimage_Color("#555555");
$img->text_color = new Securimage_Color("#222222");
$img->text_transparency_percentage = 30; // 100 = completely transparent
$img->num_lines = 9;
$img->line_color = new Securimage_Color("#333333");
//$img->image_type = SI_IMAGE_PNG;
//$img->signature_color = new Securimage_Color(rand(0, 64), rand(64, 128), rand(128, 255));
//$img->text_minimum_distance = 25;
$img->code_length = 5;
$img->charset = "ABCDEFGHJKLMNPQRSTUVWYZ23456789";
$img->noise_color = new Securimage_Color('#333333');
$img->noise_level = 10; // 0-10


$img->show(); // alternate use:  $img->show('/path/to/background_image.jpg');
