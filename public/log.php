<?php
	$content = date('m/d/Y h:i:s a', time());
	$fp = fopen("res.txt","wb");
	fwrite($fp,$content);
	fclose($fp);
?>