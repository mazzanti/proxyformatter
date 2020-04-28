<?php
/**
 * ProxyFormatter
 */

define("AUTHOR_NAME","giulio",true);

define("APP_NAME","ProxyFormatter",true);
define("APP_VERSION","2.7.8",true);


$date=date("Y-m-d H.i.s");
define("FILE_NAME","proxylist ".$date.".bat",true);

function autorized($pincode,$temp){
	if($pincode === $temp) { return true; }
	return false;
}

function decodeData($data){
	return html_entity_decode(stripslashes(trim($data)));
}

function encodeData($data){
	return addslashes(htmlentities(trim($data)));
}

function writedump($obj){
	if(is_string($str)){
		echo "<code>$obj</code>";
	}else{
		echo "<pre>";
		print_r($obj);
		echo "</pre>";
	}
}

function isIp($string){
	$string = explode('.',$string);
	if(sizeof($string)==4){
		return true;
	}
return false;
}

$pin = (isset($_REQUEST['pin']) && !empty($_REQUEST['pin'])) ? encodeData($_REQUEST['pin']) : 0;

if(!autorized(PIN_CODE,$pin)){//pin check
//	echo "no";
}else{//pin check
$proxy_input = (isset($_REQUEST['proxy_input']) && !empty($_REQUEST['proxy_input'])) ? ($_REQUEST['proxy_input']) : '';
$prefix = (isset($_REQUEST['prefix']) && !empty($_REQUEST['prefix'])) ? ($_REQUEST['prefix']) : '';

$doubles = (isset($_REQUEST['doubles'])) ? 1 : 0;
$doublesip = (isset($_REQUEST['doublesip'])) ? 1 : 0;
$return_file = (isset($_REQUEST['return_file'])) ? 1 : 0;

$t = $proxy_input;

$t = str_replace('
','	',$t);

$t = str_replace(':','	',$t);
$t = str_replace(';','	',$t);
$t = str_replace(' ','	',$t);
$t = explode('	',$t);

$n_in=0;
$n_out=0;

$ip = array();
foreach($t as $k => $v){
	if(isIp($v)){
		$n_in++;
		$tporta=$t[$k+1];
		if(!$tporta){ $tporta='80';}
		$tip=$v.":".$tporta;
		$tip=trim($tip);
		if($doubles){
			if(!in_array($tip,$ip)){ $ip[]=$tip;}
		}else{
			$ip[]=$tip;
		}
	}
}

//double ips
if(($doublesip) && (!$doubles)){

	$ipdoubles = array();
	foreach($ip as $k => $v){
		$onlyip = explode(":",$v); $onlyip = $onlyip[0];
		if(in_array($onlyip,$ipdoubles)){
			unset($ip[$k]);
		}else{
			$ipdoubles[]=$onlyip;
		}
	}

}
unset($ipdoubles);unset($onlyip);

$n_out=sizeof($ip);

$final='';
if($n_out){

//array to string
$t = implode('
',$ip);


//add the prefix to a line
if(!empty($prefix)){
	$t=str_replace("\n","\n".$prefix,$t);
	$t=$prefix.$t;
}


$final = $t;
}



if(($return_file) && ($n_out)){//check file download
	header("Content-Type: application/octet-stream");
	header('Content-Disposition: attachment; filename="'.FILE_NAME.'"');
	echo $final;
}else{
?>

<html>
<head>
<style>
body {
	font: 12px verdana,arial,tahoma;
	color: #FFFFFF;
	background: #000000;
}
input {
	border:1px solid #000000;
}

textarea {
	border:1px solid #000000;
}

th,td {
	vertical-align: top;
}
</style>

<title><?=APP_NAME?> v.<?=APP_VERSION?> by <?=AUTHOR_NAME?></title>
</head>

<body>
<h1><?=APP_NAME?> v.<?=APP_VERSION?></h1><h2> by <?=AUTHOR_NAME?> - Ferrara</h2>

<form name="form1" action="proxyformatter.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="">

<table>
<tr>
<th>ProxyInput<br /><br />
<input type="button" value="Clear" onclick="javascript:this.form.proxy_input.value='';this.form.proxy_input.focus();" style="width:100%" /><br />
<input type="button" value="Select all" onclick="javascript:this.form.proxy_input.select();" style="width:100%" />
</th><td> <textarea name="proxy_input" id="proxy_input" rows="20" cols="80" title=""><?=decodeData($proxy_input)?></textarea> <tr>

<th>Prefix</th><td> <input name="prefix" id="prefix" type="text" size="50" maxlength="254" value="<?=($prefix)?>" title="" />
<!-- <tr><th>No doubles</th><td> <input type="checkbox"<?=($doubles) ? ' checked="checked"': ''?> id="" name="doubles" />Check double IPs and Ports //-->
<tr><th>No double IPs</th><td> <input type="checkbox"<?=($doublesip) ? ' checked="checked"': ''?> id="" name="doublesip" />Check double IPs
<tr><th>Return file</th><td> <input type="checkbox"<?=($return_file) ? ' checked="checked"': ''?> id="" name="return_file" />Save proxy list into your computer
<tr><th></th><td><br /><input type="submit" name="send" value="SEND AND WORK iT!" style="width:100%;height:50px"/></td></tr>

 </table>
 <input type="hidden" name="pin" value="<?=$pin?>" />



</form>
<br />
<hr />

<?php
if(($final) && $n_out){
?>
<form name="out">
<table>
<tr><th>ProxyOutput<br /><br />
<input type="button" value="Clear" onclick="javascript:this.form.proxy_output.value='';" style="width:100%" /><br />
<input type="button" value="Select all" onclick="javascript:this.form.proxy_output.select();" style="width:100%" /></th>
<td style="text-align:right;">Parsed: <?=$n_in?> Ok: <?=$n_out?>
<br />
<textarea name="proxy_output" id="proxy_output" rows="20" cols="80" title=""><?=$final?></textarea></td>
</tr>
</table>
</form>
<?php
}else{
	if(isset($_REQUEST['send']))
		echo "<script type=\"text/javascript\">alert('No valid data was found');</script>";
}
?>
</body>
</html>
<?php
}//check file download

unset($b);unset($proxy_input);unset($prefix);
unset($final);unset($t);unset($ip);
}//pin check
?>
