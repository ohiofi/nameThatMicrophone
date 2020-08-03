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






/*

----------------------------------------------HERE IS THE SURVEY STUFF----------------------------------------------

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
  $insertSQL = sprintf("INSERT INTO survey (entryNumber, userName, gameNumber, questionNumber, q1, q2, q3, q4, q5) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['entry'], "int"),
                       GetSQLValueString($_SESSION['MM_Username'], "text"),
                       GetSQLValueString($_POST['gamenum'], "int"),
                       GetSQLValueString($_POST['ques'], "int"),
                       GetSQLValueString($_POST['RadioGroup1'], "text"),
                       GetSQLValueString($_POST['RadioGroup2'], "text"),
                       GetSQLValueString($_POST['RadioGroup3'], "text"),
                       GetSQLValueString($_POST['RadioGroup4'], "text"),
                       GetSQLValueString($_POST['RadioGroup5'], "text"));

  mysql_select_db($database_ohiofi, $ohiofi);
  $Result1 = mysql_query($insertSQL, $ohiofi) or die(mysql_error());

  $insertGoTo = "mainmenu.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}*/


$editFormAction = $_SERVER['PHP_SELF'];

if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_rsGame3 = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsGame3 = $_SESSION['MM_Username'];
}
mysql_select_db($database_ohiofi, $ohiofi);
$query_rsGame3=sprintf("SELECT game3responses.entryNumber, game3responses.questionNumber FROM game3responses WHERE userName=%s AND game3responses.tally=1 ORDER BY game3responses.questionNumber DESC",
  GetSQLValueString($colname_rsGame3, "text")); 
$rsGame3 = mysql_query($query_rsGame3, $ohiofi) or die(mysql_error());
$row_rsGame3 = mysql_fetch_assoc($rsGame3);
$totalRows_rsGame3 = mysql_num_rows($rsGame3);




mysql_select_db($database_ohiofi, $ohiofi);
$query_rsScore3=sprintf("SELECT game3responses.entryNumber, game3responses.questionNumber FROM game3responses WHERE userName=%s",
  GetSQLValueString($colname_rsGame3, "text")); 
$rsScore3 = mysql_query($query_rsScore3, $ohiofi) or die(mysql_error());
$row_rsScore3 = mysql_fetch_assoc($rsScore3);
$totalRows_rsScore3 = mysql_num_rows($rsScore3);


/* ------------------------------------- rsGame3_check is the fail safe -----------------------------------------*/

mysql_select_db($database_ohiofi, $ohiofi);
$query_rsGame3_check=sprintf("SELECT game3responses.entryNumber, game3responses.questionNumber FROM game3responses WHERE userName=%s AND game3responses.questionNumber=%s",
  GetSQLValueString($colname_rsGame3, "text"), GetSQLValueString($_POST['questionNumber'], "int")); 
$rsGame3_check = mysql_query($query_rsGame3_check, $ohiofi) or die(mysql_error());
$row_rsGame3_check = mysql_fetch_assoc($rsGame3_check);
$totalRows_rsGame3_check = mysql_num_rows($rsGame3_check);


mysql_select_db($database_ohiofi, $ohiofi);
$query_rsUser=sprintf("SELECT users.userID, users.game3 FROM users WHERE userName=%s ",
  GetSQLValueString($colname_rsGame3, "text")); 
$rsUser = mysql_query($query_rsUser, $ohiofi) or die(mysql_error());
$row_rsUser = mysql_fetch_assoc($rsUser);
$totalRows_rsUser = mysql_num_rows($rsUser);




$maxRows_rsScoreboard = 10;
$pageNum_rsScoreboard = 0;
if (isset($_GET['pageNum_rsScoreboard'])) {
  $pageNum_rsScoreboard = $_GET['pageNum_rsScoreboard'];
}
$startRow_rsScoreboard = $pageNum_rsScoreboard * $maxRows_rsScoreboard;

mysql_select_db($database_ohiofi, $ohiofi);
$query_rsScoreboard = "SELECT userName, game3 FROM users ORDER BY game3 DESC";
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
$query_rsScoreboard = "SELECT userName, game3 FROM users WHERE game3 > 0 ORDER BY game3 DESC";
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





/*
mysql_select_db($database_ohiofi, $ohiofi);
$query_rsSurvey=sprintf("SELECT survey.questionNumber FROM survey WHERE survey.userName=%s AND survey.gameNumber=3 ORDER BY survey.questionNumber DESC",
  GetSQLValueString($colname_rsGame3, "text")); 
$rsSurvey = mysql_query($query_rsSurvey, $ohiofi) or die(mysql_error());
$row_rsSurvey = mysql_fetch_assoc($rsSurvey);
$totalRows_rsSurvey = mysql_num_rows($rsSurvey);


*/


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	if ($totalRows_rsGame3_check == 0) {
		  $updateSQL = sprintf("UPDATE users SET game3=%s, game3total=%s WHERE userID=%s",
							   GetSQLValueString($_POST['points'], "int"),
							   GetSQLValueString($_POST['game3total'], "int"),
							   GetSQLValueString($_POST['userNumber'], "int"));
		
		  mysql_select_db($database_ohiofi, $ohiofi);
		  $Result1 = mysql_query($updateSQL, $ohiofi) or die(mysql_error());
	}
}
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
	if ($totalRows_rsGame3_check == 0) {
		  $updateSQL = sprintf("UPDATE users SET game3total=%s WHERE userID=%s",
							   GetSQLValueString($_POST['game3total'], "int"),
							   GetSQLValueString($_POST['userNumber'], "int"));
		
		  mysql_select_db($database_ohiofi, $ohiofi);
		  $Result1 = mysql_query($updateSQL, $ohiofi) or die(mysql_error());
	}
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	if ($totalRows_rsGame3_check == 0) {
		///if ($row_rsScore3['questionNumber'] == $totalRows_rsScore3){
			
					   
	  		$insertSQL = sprintf("INSERT INTO game3responses (entryNumber, userName, questionNumber, question, answer, tally) VALUES (%s, %s, %s, %s, %s, %s)",
						   GetSQLValueString($_POST['entryNumber'], "int"),
						   GetSQLValueString($_SESSION['MM_Username'], "text"),
						   GetSQLValueString($_POST['questionNumber'], "int"),
						   GetSQLValueString($_POST['question1'], "text"),
						   GetSQLValueString($_POST['response1'], "text"),
						   GetSQLValueString($_POST['tally'], "int"));
					   
	  		mysql_select_db($database_ohiofi, $ohiofi);
	 		$Result1 = mysql_query($insertSQL, $ohiofi) or die(mysql_error());
		///}
	}
	header('Location: ' . $_SERVER['PHP_SELF']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
	if ($totalRows_rsGame3_check == 0) {
		///if ($row_rsScore3['questionNumber'] == $totalRows_rsScore3){
	  		$insertSQL = sprintf("INSERT INTO game3responses (entryNumber, userName, questionNumber, question, answer, tally) VALUES (%s, %s, %s, %s, %s, %s)",
						   GetSQLValueString($_POST['entryNumber'], "int"),
						   GetSQLValueString($_SESSION['MM_Username'], "text"),
						   GetSQLValueString($_POST['questionNumber'], "int"),
						   GetSQLValueString($_POST['question2'], "text"),
						   GetSQLValueString($_POST['response2'], "text"),
						   GetSQLValueString($_POST['tally'], "int"));
	
	  		mysql_select_db($database_ohiofi, $ohiofi);
	  		$Result1 = mysql_query($insertSQL, $ohiofi) or die(mysql_error());
		///}
	}
	header('Location: ' . $_SERVER['PHP_SELF']);
}



$points = $totalRows_rsGame3 + 1;
$currentQuestion = $totalRows_rsScore3 + 1;
//print_r($currentQuestion . "vs" . $rsSurvey['questionNumber'] );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Microphones web app</title>

<link rel="stylesheet" type="text/css" href="musictechwebapps.css" />
<style>
.slideshowTape{
		position:absolute;
		left:0px;
		top:0px;	
		width:600px;
		<?php $totalNumberOfSlides = 35 ?>
		height:<?php echo($totalNumberOfSlides*400) ?>px !important;/*must be the number of slides multiplied by the height*/
	}
</style>

</head>
<body onload="preloader('win1')" />
<?php include("_includes/header.php"); ?>
<div id="graphiceqmain">
<div class="banner">
<div class="buttonGroup">
		<a href="#" id="singleButton_1" class="slideTab hidden" onclick="showSingle();"><!--show just one--></a>
        <a href="#" id="showAllButton_1" class="slideTab" onclick="showAll();"><!--show all--></a>
        <a href="#" class="slideTab" onclick="next(1);"><!--next mic--></a>
        <a href="#" class="slideTab" onclick="ran();"><!--RANDOM mic--></a>
		<a href="#" class="slideTab" onclick="next(0);"><!--previous mic--></a>
    	<a href="#" id="slideTab1" class="slideTab hidden" onclick="next(1);"><!--1 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab2" class="slideTab hidden" onclick="next(1);"><!--2 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab3" class="slideTab hidden" onclick="next(1);"><!--3 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab4" class="slideTab hidden" onclick="next(1);"><!--4 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab5" class="slideTab hidden" onclick="next(1);"><!--5 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab6" class="slideTab hidden" onclick="next(1);"><!--6 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab7" class="slideTab hidden" onclick="next(1);"><!--7 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab8" class="slideTab hidden" onclick="next(1);"><!--8 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab9" class="slideTab hidden" onclick="next(1);"><!--9 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab10" class="slideTab hidden" onclick="next(1);"><!--10 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab11" class="slideTab hidden" onclick="next(1);"><!--11 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab12" class="slideTab hidden" onclick="next(1);"><!--12 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab13" class="slideTab hidden" onclick="next(1);"><!--13 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab14" class="slideTab hidden" onclick="next(1);"><!--14 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab15" class="slideTab hidden" onclick="next(1);"><!--15 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab16" class="slideTab hidden" onclick="next(1);"><!--16 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab17" class="slideTab hidden" onclick="next(1);"><!--17 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab18" class="slideTab hidden" onclick="next(1);"><!--18 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab19" class="slideTab hidden" onclick="next(1);"><!--19 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab20" class="slideTab hidden" onclick="next(1);"><!--20 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab21" class="slideTab hidden" onclick="next(1);"><!--21 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab22" class="slideTab hidden" onclick="next(1);"><!--22 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab23" class="slideTab hidden" onclick="next(1);"><!--23 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab24" class="slideTab hidden" onclick="next(1);"><!--23 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab25" class="slideTab hidden" onclick="next(1);"><!--23 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab26" class="slideTab hidden" onclick="next(1);"><!--23 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab27" class="slideTab hidden" onclick="next(1);"><!--23 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab28" class="slideTab hidden" onclick="next(1);"><!--23 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab29" class="slideTab hidden" onclick="next(1);"><!--23 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab30" class="slideTab hidden" onclick="next(1);"><!--23 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab31" class="slideTab hidden" onclick="next(1);"><!--23 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab32" class="slideTab hidden" onclick="next(1);"><!--23 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab33" class="slideTab hidden" onclick="next(1);"><!--23 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab34" class="slideTab hidden" onclick="next(1);"><!--23 / <?php echo $totalNumberOfSlides ?>--></a>
        <a href="#" id="slideTab35" class="slideTab hidden" onclick="next(1);"><!--23 / <?php echo $totalNumberOfSlides ?>--></a>
        
</div>
</div>
<div class="banner">
<div class="slideshowWindow" id="slideshowWindow_1">
	<div class="slideshowTape" id="slideshowTape_1">
		<!---
        slide width:600px
        slide height:400px
        number of slides:<?php echo $totalNumberOfSlides ?>
        --->
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-01.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="1" class="hideMe" name="formnumber1" method="post" action=""><p>Make / Model</p>
                  
                  <select name="Question01" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                   
                </form>
               
                <form id="2" class="hideMe" name="formnumber2" method="post" action=""> <p>Capsule Design</p>
                  
                  <select name="Question02" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="2">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                   
                </form>
                
                <form id="3" class="hideMe" name="formnumber3" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question03" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="3">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                   
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-12.jpeg"  height='200' /></h1>
            <div class="textbox">
            	
                <form id="4" class="hideMe" name="formnumber4" method="post" action=""><p>Make / Model</p>
                  
                  <select name="Question04" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="4">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                   
                </form>
                
                <form id="5" class="hideMe" name="formnumber5" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question05" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="5">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                   
                </form>
                
                <form id="6" class="hideMe" name="formnumber6" method="post" action=""><p>Polar Pattern(s)</p>
                 
                  <select name="Question06" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="6">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                   
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-03.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="7" class="hideMe" name="formnumber7" method="post" action=""><p>Make / Model</p>
                  
                  <select name="Question07" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="7">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                
                <form id="8" class="hideMe" name="formnumber8" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question08" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="8">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="9" class="hideMe" name="formnumber9" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question09" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="9">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-14.jpeg"  height='200' /></h1>
            <div class="textbox">
            	
            <form id="10"  class="hideMe" name="formnumber10" method="post" action="">
            <p>Make / Model</p>
                  
                  <select name="Question10" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="10">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="11"  class="hideMe" name="formnumber11" method="post" action="">
                <p>Capsule Design</p>
                  
                  <select name="Question11" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="11">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="12" class="hideMe" name="formnumber12" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question12" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="12">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-05.jpeg" height='200' /></h1>
            <div class="textbox">
            	
            <form id="13" class="hideMe" name="formnumber13" method="post" action="">
            <p>Make / Model</p>
                  
                  <select name="Question13" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="13">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="14" class="hideMe" name="formnumber14" method="post" action="">
                <p>Capsule Design</p>
                  
                  <select name="Question14" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="14">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="15" class="hideMe" name="formnumber15" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question15" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="15">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-16.jpeg"  height='200' /></h1>
            <div class="textbox">
            	
                <form id="16" class="hideMe" name="formnumber16" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question16" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="16">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="17" class="hideMe" name="formnumber17" method="post" action="">
                <p>Capsule Design</p>
                  
                  <select name="Question17" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="17">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="18"  class="hideMe" name="formnumber18" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question18" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="18">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-07.jpeg" width='200' /></h1>
            <div class="textbox">
            	
                <form id="19" class="hideMe" name="formnumber19" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question19" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="19">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="20" class="hideMe" name="formnumber20" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question20" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="20">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="21" class="hideMe" name="formnumber21" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question21" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="21">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-18.jpeg"  height='200' /></h1>
            <div class="textbox">
            	
                <form id="22"  class="hideMe" name="formnumber22" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question22" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="22">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="23"  class="hideMe" name="formnumber23" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question23" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="23">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="24"  class="hideMe" name="formnumber24" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question24" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="24">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-09.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="25" class="hideMe" name="formnumber25" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question25" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="25">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="26" class="hideMe" name="formnumber26" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question26" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="26">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="27" class="hideMe" name="formnumber27" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question27" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="27">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-20.jpeg"  width='200' /></h1>
            <div class="textbox">
            	
                <form id="28" class="hideMe" name="formnumber28" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question28" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="28">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="29" class="hideMe" name="formnumber29" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question29" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="29">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="30" class="hideMe" name="formnumber30" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question30" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="30">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-11.jpeg" width='200' /></h1>
            <div class="textbox">
            	
                <form id="31" class="hideMe" name="formnumber31" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question31" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="31">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="32" class="hideMe" name="formnumber32" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question32" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="32">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="33" class="hideMe" name="formnumber33" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question33" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="33">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-22.jpeg"  height='200' /></h1>
            <div class="textbox">
            	
                <form id="34" class="hideMe" name="formnumber34" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question34" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="34">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="35" class="hideMe" name="formnumber35" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question35" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="35">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="36" class="hideMe" name="formnumber36" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question36" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="36">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-13.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="37" class="hideMe" name="formnumber37" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question37" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="37">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="38" class="hideMe" name="formnumber38" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question38" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="38">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="39" class="hideMe" name="formnumber39" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question39" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="39">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-04.jpeg"  width='200' /></h1>
            <div class="textbox">
            	
                <form id="40" class="hideMe" name="formnumber40" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question40" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="40">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="41" class="hideMe" name="formnumber41" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question41" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="41">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="42" class="hideMe" name="formnumber42" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question42" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="42">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-15.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="43" class="hideMe" name="formnumber43" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question43" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="43">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="44" class="hideMe" name="formnumber44" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question44" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="44">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="45" class="hideMe" name="formnumber45" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question45" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="45">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-06.jpeg"  height='200' /></h1>
            <div class="textbox">
            	
                <form id="46" class="hideMe" name="formnumber46" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question46" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="46">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="47" class="hideMe" name="formnumber47" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question47" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="47">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="48" class="hideMe" name="formnumber48" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question48" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="48">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-17.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="49" class="hideMe" name="formnumber49" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question49" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="49">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="50" class="hideMe" name="formnumber50" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question50" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="50">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="51" class="hideMe" name="formnumber51" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question51" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Cardioid</option>
                    <option value="51">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-08.jpeg"  height='200' /></h1>
            <div class="textbox">
            	
                <form id="52" class="hideMe" name="formnumber52" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question52" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="52">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="53" class="hideMe" name="formnumber53" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question53" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="53">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="54" class="hideMe" name="formnumber54" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question54" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="54">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-19.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="55" class="hideMe" name="formnumber55" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question55" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="55">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="56" class="hideMe" name="formnumber56" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question56" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="56">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="57" class="hideMe" name="formnumber57" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question57" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="57">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-10.jpeg"  height='200' /></h1>
            <div class="textbox">
            	
                <form id="58" class="hideMe" name="formnumber58" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question58" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="58">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="59" class="hideMe" name="formnumber59" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question59" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="59">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="60" class="hideMe" name="formnumber60" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question60" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="60">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-21.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="61"  class="hideMe" name="formnumber61" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question61" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="61">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="62"  class="hideMe" name="formnumber62" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question62" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="62">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="63"  class="hideMe" name="formnumber63" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question63" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="63">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-02.jpeg"  height='200' /></h1>
            <div class="textbox">
            	
                <form id="64"  class="hideMe" name="formnumber64" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question64" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="64">Shure SM81</option>
                  </select>
                  
                </form>
                
                <form id="65" class="hideMe" name="formnumber65" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question65" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="65">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="66" class="hideMe" name="formnumber66" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question66" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="66">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide">
			<h1><img class="imgfloatright"  src="img/imgres-23.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="67" class="hideMe" name="formnumber67" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question67" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="67">Shure SM57</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="68" class="hideMe" name="formnumber68" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question68" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="68">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="69"  class="hideMe" name="formnumber69" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question69" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="69">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide"><!--- Sennheiser e602-11 -->
			<h1><img class="imgfloatright"  src="img/imgres-24.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="70" class="hideMe" name="formnumber70" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question70" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="70">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="71" class="hideMe" name="formnumber71" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question71" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="71">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="72"  class="hideMe" name="formnumber72" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question72" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="72">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide"><!--- Shure Beta 58 -->
			<h1><img class="imgfloatright"  src="img/imgres-25.jpeg" width='200' /></h1>
            <div class="textbox">
            	
                <form id="73" class="hideMe" name="formnumber73" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question73" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="73">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="74" class="hideMe" name="formnumber74" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question74" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="74">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="75"  class="hideMe" name="formnumber75" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question75" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="75">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide"><!--- TEAC ME-120 -->
			<h1><img class="imgfloatright"  src="img/imgres-26.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="76" class="hideMe" name="formnumber76" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question76" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="76">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="77" class="hideMe" name="formnumber77" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question77" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="77">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="78"  class="hideMe" name="formnumber78" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question78" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="78">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide"><!--- AEA R84 -->
			<h1><img class="imgfloatright"  src="img/imgres-27.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="79" class="hideMe" name="formnumber79" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question79" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="79">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="80" class="hideMe" name="formnumber80" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question80" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="80">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="81"  class="hideMe" name="formnumber81" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question81" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Cardioid</option>
                    <option value="81">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide"><!--- Sennheiser e906 -->
			<h1><img class="imgfloatright"  src="img/imgres-28.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="82" class="hideMe" name="formnumber82" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question82" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="82">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="83" class="hideMe" name="formnumber83" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question83" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="83">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="84"  class="hideMe" name="formnumber81" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question84" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="84">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide"><!--- Shure Beta 181 -->
			<h1><img class="imgfloatright"  src="img/imgres-29.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="85" class="hideMe" name="formnumber85" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question85" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="85">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="86" class="hideMe" name="formnumber86" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question86" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="86">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="87"  class="hideMe" name="formnumber87" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question87" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="87">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide"><!--- AT 4050ST -->
			<h1><img class="imgfloatright"  src="img/imgres-30.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="88" class="hideMe" name="formnumber88" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question88" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="88">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="89" class="hideMe" name="formnumber89" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question89" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="89">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="90"  class="hideMe" name="formnumber90" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question90" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="90">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide"><!--- Shure KSM 44A -->
			<h1><img class="imgfloatright"  src="img/imgres-31.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="91" class="hideMe" name="formnumber91" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question91" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="91">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="92" class="hideMe" name="formnumber92" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question92" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="92">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="93"  class="hideMe" name="formnumber93" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question93" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="93">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide"><!--- Shure SM7B -->
			<h1><img class="imgfloatright"  src="img/imgres-32.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="94" class="hideMe" name="formnumber94" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question94" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="94">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="95" class="hideMe" name="formnumber95" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question95" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="95">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="96"  class="hideMe" name="formnumber96" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question96" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="96">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide"><!--- Blue Kiwi -->
			<h1><img class="imgfloatright"  src="img/imgres-33.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="97" class="hideMe" name="formnumber97" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question97" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="97">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="98" class="hideMe" name="formnumber98" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question98" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="98">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="99"  class="hideMe" name="formnumber99" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question99" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="99">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide"><!--- Rode K2 -->
			<h1><img class="imgfloatright"  src="img/imgres-34.jpeg" height='200' /></h1>
            <div class="textbox">
            	
                <form id="100" class="hideMe" name="formnumber100" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question100" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="-1">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="100">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="101" class="hideMe" name="formnumber101" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question101" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="-1">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="101">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="102"  class="hideMe" name="formnumber102" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question102" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="-1">Multipattern</option><option value="102">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
        <div class="slide"><!--- AKG C 414 XLS II -->
			<h1><img class="imgfloatright"  src="img/imgres-35.jpeg" width='200' /></h1>
            <div class="textbox">
            	
                <form id="103" class="hideMe" name="formnumber103" method="post" action="">
                <p>Make / Model</p>
                  
                  <select name="Question103" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">AEA R84</option><option value="-1">AKG C 414 B-ULS</option><option value="103">AKG C 414 XLII</option>
                    <option value="-1">Audio-Technica (AT) 4031</option>
                    <option value="-1">Audio-Technica (AT) 4033</option>
                    <option value="-1">Audio-Technica (AT) 4050ST</option><option value="-1">Audix D2</option>
                    <option value="-1">Audix D4</option>
                    <option value="-1">Audix D6</option>
                    <option value="-1">Audix i5</option>
                    <option value="-1">Blue Baby Bottle</option><option value="-1">Blue Kiwi</option>
                    <option value="-1">CAD E100</option>
                    <option value="-1">Electro-Voice (EV) ND308</option>
                    <option value="-1">Electro-Voice (EV) RE10</option>
                    <option value="-1">Electro-Voice (EV) RE20</option>
                    <option value="-1">Neumann KM184</option>
                    <option value="-1">Neumann M147</option>
                    <option value="-1">Neumann U87</option><option value="-1">Rode K2</option>
                    <option value="-1">Rode NT2</option>
                    <option value="-1">Royer R-122</option>
                    <option value="-1">Sennheiser e602-II</option><option value="-1">Sennheiser e604</option>
                    <option value="-1">Sennheiser e906</option><option value="-1">Sennheiser MD421</option>
                    <option value="-1">Shure Beta 181</option><option value="-1">Shure Beta 52</option>
                    <option value="-1">Shure Beta 58</option><option value="-1">Shure KSM44A</option><option value="-1">Shure SM57</option>
                    <option value="-1">Shure SM58</option><option value="-1">Shure SM7B</option>
                    <option value="-1">Shure SM81</option>
                  <option value="-1">TEAC ME-120</option></select>
                  
                </form>
                
                <form id="104" class="hideMe" name="formnumber104" method="post" action=""><p>Capsule Design</p>
                  
                  <select name="Question104" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Dynamic</option>
                    <option value="104">Condenser</option>
                    <option value="-1">Ribbon</option>
                    <option value="-1">Electret-Condenser</option>
                    <option value="-1">Tube Condenser</option>
                  </select>
                  
                </form>
                
                <form id="105"  class="hideMe" name="formnumber105" method="post" action=""><p>Polar Pattern(s)</p>
                  
                  <select name="Question105" onChange="checkAnswer(this.value)">
                    <!-- Add items and values to the widget-->
                    <option>Please select an item</option>
                    <option value="-1">Cardioid</option>
                    <option value="-1">Bidirectional</option>
                    <option value="-1">Cardioid and Omni</option><option value="-1">Cardioid and Bi</option>
                    <option value="-1">Cardioid, Omni, and Bi</option>
                    <option value="-1">Cardioid, Hypercardioid, Omni, and Bi</option><option value="-1">Cardioid, Supercardioid, Omni, and Bi</option>
                    <option value="-1">Supercardioid</option><option value="105">Multipattern</option><option value="-1">Variable</option>
                  </select>
                  
                </form>
                
            </div>
        </div>
	</div>
</div>
</div>
</div>

<div id="footer">
        	<div id="greatJob" class="popup">
            	<h2>
                	<?php $my_array = array(0 => "Great Job!", 1 => "Nicely Done!", 2 => "That's Right!");
					shuffle($my_array);
					echo($my_array[0]);
					?>               
                </h2>
              <form id="form1" name="form1" action="<?php echo $editFormAction; ?>" method="POST"><input name="entryNumber" type="hidden" value="" /><input name="userName" type="hidden" value="" /><input type="hidden" name="question1" id="question1" value=""><input type="hidden" name="response1" id="response1" value=""><input name="tally" type="hidden" value="1" /><input name="questionNumber" type="hidden" value="<?php echo $currentQuestion; ?>" /><input name="points" type="hidden" value="<?=$points ?>" /><input name="userNumber" type="hidden" value="<?=$row_rsUser["userID"] ?>" /><input name="game3total" type="hidden" value="<?php echo $totalRows_rsScore3; ?>" /><input class="submit button popupbutton" type="submit" name="Submit" id="Submit" value="Continue">
                  <input type="hidden" name="MM_insert" value="form1" />
                  <input type="hidden" name="MM_update" value="form1" />
              </form>
            </div>
            <div id="tryAgain" class="popup">
            	<h2>Incorrect</h2>
       		  <form id="form2" name="form2" action="<?php echo $editFormAction; ?>" method="POST"><input name="entryNumber" type="hidden" value="" /><input name="userName" type="hidden" value="" /><input type="hidden" name="question2" id="question2" value=""><input type="hidden" name="response2" id="response2" value=""><input name="tally" type="hidden" value="0" /><input name="questionNumber" type="hidden" value="<?php echo $currentQuestion; ?>" /><input name="userNumber" type="hidden" value="<?=$row_rsUser["userID"] ?>" /><input name="game3total" type="hidden" value="<?php echo $totalRows_rsScore3; ?>" /><input class="submit button popupbutton" type="submit" name="Submit" id="Submit" value="Continue">
           		  <input type="hidden" name="MM_insert" value="form2" />
                  <input type="hidden" name="MM_update" value="form2" />
                </form>
                
                
                
                <? if ($currentQuestion % 1 == 0) { ?>
                
                	<h2>Your current score is<br /><? echo $row_rsUser["game3"] ?> <? if ($row_rsUser["game3"] != 1)
                                echo " pts";
                            else
                                echo " pt";
                            ?></h2>
                    
                      
                      <hr />
                      
                      <p>High Scores</p>
                      <p>
                      <table border="0" STYLE="margin:15px;">
                        <?php
                        do { ?>
                          <tr>
                            <td><?php echo $row_rsScoreboard['userName']," "; ?></td>
                            <td><?php echo " "; ?></td>
                            <td><?php echo " ",$row_rsScoreboard['game3'];
                            if ($row_rsScoreboard['game3'] != 1)
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
                       
                      </p>
                      
                      <? } ?>
                
                
                
            </div>
            
        </div>
        
        
    <span id="blank"></span>    
    </div>

<?php include("_includes/footer.php"); ?>
</body>
<script>


///function goto3() {
///	document.getElementById("slideshowTape_1").style.left = "-1200px";
///}

var total = <?php echo $totalNumberOfSlides ?>;
var currentSlide = 1;

function goto(slideID) {
	document.getElementById("slideshowTape_1").style.top = (slideID - 1) * -400 + "px";
	var count;
	for(count=1; count<=total; count++) {
		document.getElementById("slideTab"+count).className = "slideTab hidden";
	}
	document.getElementById("slideTab"+slideID).className = "slideTab";
	currentSlide = slideID
}

function next(amount) {
	if (currentSlide==0) {
		showSingle();
	}
	else {
		if (amount==1) {
			currentSlide++;
			if (currentSlide>total) {
				currentSlide = 1;
				goto(currentSlide);
			}
			else {
			goto(currentSlide)
			}
		}
		else {
			currentSlide--;
			if (currentSlide==0) {
				currentSlide = total;
				goto(currentSlide);
			}
			else {
			goto(currentSlide);
			}
		}
	}
}

function showAll() {
	for(count=1; count<=total; count++) {
		document.getElementById("slideTab"+count).className = "slideTab hidden";
	}
	document.getElementById("slideshowTape_1").style.top = "0px";
	document.getElementById("slideshowWindow_1").className = "slideshowWindow showThemAll";
	document.getElementById("slideshowTape_1").className = "slideshowTape showThemAll";
	document.getElementById("singleButton_1").className = "slideTab";
	document.getElementById("showAllButton_1").className = "slideTab hidden";
	currentSlide=0;
}

function showSingle() {
	document.getElementById("slideshowWindow_1").className = "slideshowWindow";
	document.getElementById("slideshowTape_1").className = "slideshowTape";
	document.getElementById("singleButton_1").className = "slideTab hidden";
	document.getElementById("showAllButton_1").className = "slideTab";
	goto(1);
}

function ran() {
	if (currentSlide==0) {
		showSingle();
	}
	else {
		randomNumber=Math.round(Math.random()*<?php echo $totalNumberOfSlides ?>);
		randomNumber++;
		if (randomNumber==currentSlide) {
			randomNumber++;
		}
		if (randomNumber><?php echo $totalNumberOfSlides ?>) {
			randomNumber=1
		}
		goto(randomNumber);
	}
}

///goto(1);

var question = 1;
var oneChance = 0;

function newQuestion() {
	var randomNumber=Math.floor(Math.random()*<?php echo ($totalNumberOfSlides*3) ?>+1);
	if (randomNumber><?php echo ($totalNumberOfSlides*3) ?>) {
		randomNumber=1;
	}
	if (randomNumber<1) {
		randomNumber=1;
	}
	var whichSlide=(Math.ceil(randomNumber/3));
	goto(whichSlide);
	question=randomNumber;
	document.form1.question1.value = randomNumber;
	document.form2.question2.value = randomNumber;
	displayQuestion(question);
}

function checkAnswer(yourAnswer) {
	if (oneChance == 0) {
		document.form1.response1.value = yourAnswer;
		document.form2.response2.value = yourAnswer;
		if (question == yourAnswer) {
			document.getElementById("blank").innerHTML="<embed src=\"<?php $my_array = array(0 => 'win1', 1 => 'win2', 2 => 'win3', 3 => 'win4', 4 => 'win5', 5 => 'win6', 6 => 'win7', 7 => 'win8');
					shuffle($my_array);
					echo($my_array[0]);
					?>.mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" />";
			setTimeout('document.getElementById("greatJob").className = "popup popupActive"',300);
		}
		else {
			document.getElementById("blank").innerHTML="<embed src=\"<?php $my_array = array(0 => 'fail1', 1 => 'fail2', 2 => 'fail3', 3 => 'fail4', 4 => 'fail5', 5 => 'fail6', 6 => 'fail7', 7 => 'fail8');
					shuffle($my_array);
					echo($my_array[0]);
					?>.mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" />";
			setTimeout('document.getElementById("tryAgain").className = "popup popupActive"',300);
		}
	}
	oneChance++;
};

function displayQuestion(question) {
	document.getElementById(question).className = "showMe";
};
function preloader(soundfile) {
 	document.getElementById("blank").innerHTML= "<embed src=\""+soundfile+".mp3\" hidden=\"true\" autostart=\"true\" loop=\"false\" volume=\"0\" />";
}

newQuestion();








</script>
</html>
<?php


mysql_free_result($rsGame3);

mysql_free_result($rsGame3_check);

mysql_free_result($rsScore3);

mysql_free_result($rsUser);

mysql_free_result($rsScoreboard);

?>