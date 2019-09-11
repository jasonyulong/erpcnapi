<?php
	include "include/config.php";
	
	$type = $_REQUEST['action'];
	if($type =="Logout"){
	    session_destroy();
//		$_SESSION['user'] = "";
//		$_SESSION['goodsNewDevelopRequest'] = "";
//		$_SESSION['goodsDevelopRequest'] = "";
		header("location: login.php");
	}
	
	/* ���ⶩ�� */
	







?>
<script language="javascript">


location.href = 'login.php';

</script>
