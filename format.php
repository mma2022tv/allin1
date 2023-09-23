<?php 
session_start();
$_SESSION['testSessionVar'] = 'Working';
$theURL = 'http://www.sentrylogin.com/sentry/format_action.PHP?ses=' . $_SESSION['testSessionVar'];
header("Location: $theURL");
?>

<html>
<head>
<title>Sentry PHP Server Format Detection</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<p><font size="2" face="Arial, Helvetica, sans-serif">Sentry has examined your
Server's PHP capabilities:<br>
</font></p>
<p><font size="2" face="Arial, Helvetica, sans-serif">PHP is <strong>NOT</strong> operating
on your server.</font></p>
<p><font size="2" face="Arial, Helvetica, sans-serif"> <br>
  It may still
        be possible for you to ask your webhost operators to provision (turn
          on) PHP (or even ASP) on your server. Please ask them to do so before
          deciding
   upon a flavor to use, and if they're able to provision ASP, we recommend that
   you use the ASP Flavor. Ultimately,
	    you may need to switch to a webhost such as Lunarpages.com, Hostek.com,
   GoDaddy.com, or any of thousands of others, in order to have your site hosted
   by a server with updated script handling.<br>
  <br>
  Test again at Format.asp and Format.php on your server after your webhost
   operators have informed you that they've made provision changes. </font></p>
<p>&nbsp;</p>
</body>
</html>
