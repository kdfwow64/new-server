<?php

$res = new mysqli("localhost", "guilford_laravel", "I7qLzi@gQW$&", "guilford_laravel");

$result = mysqli_query($res, "SELECT value FROM permissions WHERE id = '2'");
$myrow = mysqli_fetch_array($result);
$on = $myrow[value];

if ($on == '1'){

$result1 = mysqli_query($res, "SELECT * FROM infos ORDER BY id LIMIT 1");
$myrow1 = mysqli_fetch_array($result1);
$business_name = $myrow1['business_name'];
$domain_name = $myrow1['domain_name'];
$rank = $myrow1['rank'];
$fname = $myrow1['admins_name'];
$email = $myrow1['email'];
$warning_total = $myrow1['warning_total'];
$error_total = $myrow1['error_total'];
$phone = $myrow1['phone'];
$address = $myrow1['mailing_address'];
$flag = $myrow1['flag'];
$black = $myrow1['black'];
$created_at = $myrow1['created_at'];
$updated_at = $myrow1['updated_at'];
$id = $myrow1['id'];



$total_error = $warning_total + $error_total;
if ($total_error <= '5'){$total_error = '8';}
if ($rank <= '10'){
$rank = $rank.'. The fact that you are on the first page is very good. But you will want to keep it there. There are many small tweaks we can show you to help keep you on the first page and even rank better.';
}
else {
$rank = $rank.'. The fact that you are not on the first page is bad. You are losing business by not being found first. There are many small tweaks we can show you that could help you rank better.';
}

// check to see if email is on suppression list if so do not send
$result2 = mysqli_query($res, "SELECT id FROM blacklists WHERE domain='$email' || domain='$domain_name' || domain='$fname' LIMIT 1");
if (!$result2) {
die('Send Email For Guilford Marketing failed to execute for some reason. Line 38 Error id');
}
$on_sup = mysqli_num_rows($result2);
if($on_sup == '0'){

$result3 = mysqli_query($res, "SELECT * FROM mails WHERE id='1'");
$myrow3 = mysqli_fetch_array($result3);
$subject = $myrow3['template_name'];
$html = $myrow3['template_text'];
$text = $myrow3['template_text2'];

$subject2 = str_replace("#domain#", $domain_name, $subject);
$html2 = str_replace("#fname#", $fname, $html);
$html3 = str_replace("#rank#", $rank, $html2);
$html4 = str_replace("#email#", $email, $html3);
$html5 = str_replace("#errors#", $total_error, $html4);
$htmlbod = str_replace("#domain#", $domain_name, $html5);

$text2 = str_replace("#fname#", $fname, $text);
$text3 = str_replace("#rank#", $rank, $text2);
$text4 = str_replace("#email#", $email, $text3);
$text5 = str_replace("#errors#", $total_error, $text4);
$textbod = str_replace("#domain#", $domain_name, $text5);



$htmlbod1 = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<title></title>
</head>
<body bgcolor=\"#ffffff\">
<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"700\" bgcolor=\"#ffffff\">
<tbody><tr><td align=\"left\">
<div style=\"background-color:#088ada; color:#ffffff; font-size: 18px;\"><center>
<b>The Website Analysis Report You Requested.<br />For Your Website: <span style=\"color:#ffffff;\">$domain_name</span></b>
</center></div><div style=\"margin:10px;\">
$htmlbod
<p></p>
</div>
<br /><br />
<center><span style=\"color:#088ada; font-size: 12px;\">
Sent By Guilford Marketing - 1501 Bowmore Pl, Mc Leansville, NC 27301<br />
If you no longer wish to receive these emails <a href=\"http://gmoptions.info/optout.php?id=$id&email=$email\">Click Here</a></span></center>
</td></tr></tbody></table>
<p></p>
</body>
</html>";

$textbod1 = "The Website Analysis Report You Requested.
For Website: $domain_name

$textbod


Sent By Guilford Marketing - 1501 Bowmore Pl, Mc Leansville, NC 27301
If you no longer wish to receive these emails, goto the following link: http://gmoptions.info/optout.php?id=$id&email=$email";


$from = "support@gmoptions.info";
$fromname = "Guilford Marketing";
$cemail = "support@gmoptions.info";

############ USE THIS FOR SEND GRID #########################
$url = 'https://api.sendgrid.com/';
$user = 'ucm';
$pass = 'Righttime2192@';
$json_string = array('to' => array($email));
$params = array(
    'api_user'  => $user,
    'api_key'   => $pass,
    'x-smtpapi' => json_encode($json_string),
    'to'        => $email,
    'subject'   => $subject2,
    'html'      => $htmlbod1,
    'text'      => $textbod1,
	'fromname'  => $fromname,
    'from'      => $from,
	'replyto'   => $cemail,
  );
$request =  $url.'api/mail.send.json';
$session = curl_init($request);
curl_setopt ($session, CURLOPT_POST, true);
curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
curl_setopt($session, CURLOPT_HEADER, false);
curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv3);
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($session);
curl_close($session);
if ($response == '{"message":"success"}'){

$result4 = mysqli_query($res, "INSERT INTO infos_used SELECT * FROM infos WHERE id='$id'");

//$result4 = mysqli_query($res, "INSERT INTO infos_used (`business_name`,`domain_name`,`rank`,`admins_name`,`email`,`phone`,`mailing_address`,`flag`,`black`,`warning_total`,`error_total`,`created_at`,`updated_at`) VALUES ('".$business_name."','".$domain_name."','".$rank."','".$fname."','".$email."','".$phone."','".$address."','".$flag."','".$black."','".$warning_total."','".$error_total."','".$created_at."','".$updated_at."')");

$result5 = mysqli_query($res, "INSERT INTO blacklists (domain,domainORemail) VALUES ('".$domain_name."','1')");
$result6 = mysqli_query($res, "INSERT INTO blacklists (domain,domainORemail) VALUES ('".$email."','2')");

$result7 = mysqli_query($res, "DELETE FROM infos WHERE id=".$id." AND email='".$email."'");

}
###############################################################
else {
echo $response;
}
} // end if on suppression list
else {
$result4 = mysqli_query($res, "INSERT INTO infos_used SELECT * FROM infos WHERE id='$id'");
$result5 = mysqli_query($res, "UPDATE infos_used SET black = '1' WHERE id='$id'");
$result7 = mysqli_query($res, "DELETE FROM infos WHERE id=".$id." AND email='".$email."'");
}

$res->close();

} // end if mailer is on

?>