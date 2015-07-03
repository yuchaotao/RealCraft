<?php

class Download extends CI_CONTROLLER {

	function index(){
		$filename = "RealCraft.apk";
		header('Content-Type: application/force-download'); //指定下载文件类型
		header('Content-Disposition: attachment; filename="'.$filename.'"'); //指定下载文件的描述
		header('Content-Length:'.filesize($filename)); //指定下载文件的大小

		//将文件内容读取出来并直接输出，以便下载
		readfile($filename);
	}
}