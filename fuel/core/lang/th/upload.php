<?php

return array(
	'error_'.\Upload::UPLOAD_ERR_OK						=> 'ไฟล์อัพโหลดสำเร็จ',
	'error_'.\Upload::UPLOAD_ERR_INI_SIZE				=> 'อัพโหลดไฟล์เกินกว่าคำสั่ง upload_max_filesize ใน php.ini',
	'error_'.\Upload::UPLOAD_ERR_FORM_SIZE				=> 'อัพโหลดไฟล์เกินกว่าคำสั่ง MAX_FILE_SIZE ที่ถูกระบุไว้ใน HTML',
	'error_'.\Upload::UPLOAD_ERR_PARTIAL				=> 'ไฟล์ที่อัพโหลดถูกอัพโหลดเพียงบางส่วนเท่านั้น',
	'error_'.\Upload::UPLOAD_ERR_NO_FILE				=> 'ไม่มีไฟล์ถูกอัพโหลด',
	'error_'.\Upload::UPLOAD_ERR_NO_TMP_DIR				=> 'โฟลเดอร์สำหรับอัพโหลดชั่วคราวหายไป',
	'error_'.\Upload::UPLOAD_ERR_CANT_WRITE				=> 'ล้มเหลวในการเขียนไฟล์ที่อัพโหลดลงดิสก์',
	'error_'.\Upload::UPLOAD_ERR_EXTENSION				=> 'อัพโหลดถูกบล็อคโดยส่วนขยายของ PHP',
	'error_'.\Upload::UPLOAD_ERR_MAX_SIZE				=> 'อัพโหลดไฟล์เกินขนาดสูงสุดที่กำหนดไว้',
	'error_'.\Upload::UPLOAD_ERR_EXT_BLACKLISTED		=> 'ไม่อนุญาตให้อัพโหลดไฟล์ที่มีนามสกุลนี้',
	'error_'.\Upload::UPLOAD_ERR_EXT_NOT_WHITELISTED	=> 'ไม่อนุญาตให้อัพโหลดไฟล์ที่มีนามสกุลนี้',
	'error_'.\Upload::UPLOAD_ERR_TYPE_BLACKLISTED		=> 'ไม่อนุญาตให้อัพโหลดไฟล์ประเภทนี้',
	'error_'.\Upload::UPLOAD_ERR_TYPE_NOT_WHITELISTED	=> 'ไม่อนุญาตให้อัพโหลดไฟล์ประเภทนี้',
	'error_'.\Upload::UPLOAD_ERR_MIME_BLACKLISTED		=> 'ไม่อนุญาตให้อัพโหลดไฟล์ที่มี mime type นี้',
	'error_'.\Upload::UPLOAD_ERR_MIME_NOT_WHITELISTED	=> 'ไม่อนุญาตให้อัพโหลดไฟล์ที่มี mime type นี้',
	'error_'.\Upload::UPLOAD_ERR_MAX_FILENAME_LENGTH	=> 'ชื่อไฟล์ที่อัปโหลดเกินความยาวสูงสุดที่กำหนดไว้',
	'error_'.\Upload::UPLOAD_ERR_MOVE_FAILED			=> 'ไม่สามารถย้ายไฟล์ที่อัพโหลดไปยังตำแหน่งที่กำหนดไว้ได้',
	'error_'.\Upload::UPLOAD_ERR_DUPLICATE_FILE 		=> 'ไฟล์ที่มีชื่อนี้มีอยู่แล้ว',
	'error_'.\Upload::UPLOAD_ERR_MKDIR_FAILED			=> 'ไม่สามารถสร้างไดเร็คทอรี่เป้าหมายได้',
);
