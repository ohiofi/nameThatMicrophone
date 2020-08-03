<?php require_once('Connections/ohiofi.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "0,1,2";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsScoreboard = 10;
$pageNum_rsScoreboard = 0;
if (isset($_GET['pageNum_rsScoreboard'])) {
  $pageNum_rsScoreboard = $_GET['pageNum_rsScoreboard'];
}
$startRow_rsScoreboard = $pageNum_rsScoreboard * $maxRows_rsScoreboard;

mysql_select_db($database_ohiofi, $ohiofi);
$query_rsScoreboard = "SELECT userName, game5 FROM users ORDER BY game5 DESC";
$query_limit_rsScoreboard = sprintf("%s LIMIT %d, %d", $query_rsScoreboard, $startRow_rsScoreboard, $maxRows_rsScoreboard);
$rsScoreboard = mysql_query($query_limit_rsScoreboard, $ohiofi) or die(mysql_error());
$row_rsScoreboard = mysql_fetch_assoc($rsScoreboard);

if (isset($_GET['totalRows_rsScoreboard'])) {
  $totalRows_rsScoreboard = $_GET['totalRows_rsScoreboard'];
} else {
  $all_rsScoreboard = mysql_query($query_rsScoreboard);
  $totalRows_rsScoreboard = mysql_num_rows($all_rsScoreboard);
}
$totalPages_rsScoreboard = ceil($totalRows_rsScoreboard/$maxRows_rsScoreboard)-1;$maxRows_rsScoreboard = 10;
$pageNum_rsScoreboard = 0;
if (isset($_GET['pageNum_rsScoreboard'])) {
  $pageNum_rsScoreboard = $_GET['pageNum_rsScoreboard'];
}
$startRow_rsScoreboard = $pageNum_rsScoreboard * $maxRows_rsScoreboard;

mysql_select_db($database_ohiofi, $ohiofi);
$query_rsScoreboard = "SELECT userName, game5 FROM users WHERE game5 > 0 ORDER BY game5 DESC";
$query_limit_rsScoreboard = sprintf("%s LIMIT %d, %d", $query_rsScoreboard, $startRow_rsScoreboard, $maxRows_rsScoreboard);
$rsScoreboard = mysql_query($query_limit_rsScoreboard, $ohiofi) or die(mysql_error());
$row_rsScoreboard = mysql_fetch_assoc($rsScoreboard);

if (isset($_GET['totalRows_rsScoreboard'])) {
  $totalRows_rsScoreboard = $_GET['totalRows_rsScoreboard'];
} else {
  $all_rsScoreboard = mysql_query($query_rsScoreboard);
  $totalRows_rsScoreboard = mysql_num_rows($all_rsScoreboard);
}
$totalPages_rsScoreboard = ceil($totalRows_rsScoreboard/$maxRows_rsScoreboard)-1;

$queryString_rsScoreboard = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsScoreboard") == false && 
        stristr($param, "totalRows_rsScoreboard") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsScoreboard = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsScoreboard = sprintf("&totalRows_rsScoreboard=%d%s", $totalRows_rsScoreboard, $queryString_rsScoreboard);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Microphones web app</title>

<link rel="stylesheet" type="text/css" href="musictechwebapps.css" />

</head>

<body />



<?php include("_includes/header.php"); ?>
<div id="main">
  <div id="header">
    <h1>Microphones, Level 1</h1>
  </div>
  <div class="tutorsplitter">
  <div id="video">
  		<img src="img/microphonestutor.png" width="450px"/>
  	</div>
  <p>How to play: Select the Make/Model, Capsule Design, or Polar Pattern(s) from the list that corresponds with the pictured microphone.</p>
  </div>
  <div class="tutorsplitter">
  <table border="0" STYLE="margin:0px auto 0px auto;">
  <tr>
  <td style="text-align:right;"><b><i>High Scores</i></b></td>
  </tr>
    <?php
	do { ?>
      <tr>
        <td><?php echo $row_rsScoreboard['userName']," "; ?></td>
        <td><?php echo " "; ?></td>
        <td><?php echo " ",$row_rsScoreboard['game5'];
		if ($row_rsScoreboard['game5'] != 1)
			echo " pts";
		else
			echo " pt";
		?>
        
        
        </td>
      </tr>
      <?php 
	  } while ($row_rsScoreboard = mysql_fetch_assoc($rsScoreboard)); ?>
      <tr>
      	<td><?php if ($pageNum_rsScoreboard > 0) { // Show if not first page ?><a href="<?php printf("%s?pageNum_rsScoreboard=%d%s", $currentPage, max(0, $pageNum_rsScoreboard - 1), $queryString_rsScoreboard); ?>"><font size="1">Previous</font></a><?php } // Show if not first page ?></td>
        <td></td>
        <td><?php if ($pageNum_rsScoreboard < $totalPages_rsScoreboard) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_rsScoreboard=%d%s", $currentPage, min($totalPages_rsScoreboard, $pageNum_rsScoreboard + 1), $queryString_rsScoreboard); ?>"><font size="1">Next</font></a>
  <?php } // Show if not last page ?></td>
      </tr>
  </table>
  </div>
  <div id="gobackorcontinue" class="centered">
    <a href="mainmenu.php"><div class="button gobackorcontinue">
        <h2><img src="img/icon-undo.png" class="buttonicon"><br />Main Menu</h2>
    </div></a>
    <a href="microphones1.php"><div class="button gobackorcontinue">
        <h2><img src="img/icon-play.png" class="buttonicon"><br />Continue</h2>
    </div></a>
  </div><br /><p> &nbsp; <br /> &nbsp; <br />&nbsp;</p>
  <p>&nbsp;<br />&nbsp;<br />&nbsp;</p>
<p>&nbsp;<br />&nbsp;<br />&nbsp;</p>
<p>&nbsp;<br />&nbsp;<br />&nbsp;</p>
 &nbsp; <br />
  
  
  <span id="blank"></span> <br />
</div>
<?php include("_includes/footer.php"); ?>
</body>

</html>
<?php
mysql_free_result($rsScoreboard);
?>
