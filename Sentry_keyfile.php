<?php
$defaultCookieTime = 1800;  // default length of time that cookies last, shown in Seconds (1800 = thirty minutes). May be changed at will. Don't use commas!
$extendedCookieTime = 2419200; // 4 weeks
$CookieTime = $defaultCookieTime;

$psistCk = false;
$psist = '0';

if(isset($_COOKIE['psist'])){
	$psist = $_COOKIE['psist'];
}
if($psist == '1'){
	$psistCk = true;
}

$PreviousURL = '';
$URLnoQueryStr = '';
$NtvQueryStr = '';
$base_domain = '';
// get the protocol
if($_SERVER['HTTPS'] == 'off' || $_SERVER['HTTPS'] == 0 || $_SERVER['HTTPS'] == 'false' || $_SERVER['HTTPS'] == '' || $_SERVER['HTTPS'] == Null){
	$proto = 'http://';
}  //  checking value of $_SERVER['HTTPS']
else{
	$proto = 'https://';
}  // else checking value of $_SERVER['HTTPS']
// preserve the current Querystring, if any
if(isset($_SERVER['QUERY_STRING'])){
	if($_SERVER['QUERY_STRING'] != ''){
		$querStr = '?' . $_SERVER['QUERY_STRING'];
		$NtvQueryStr = $_SERVER['QUERY_STRING'];
	}  // if($_SERVER['QUERY_STRING'] != '')
  	else{
	  	$querStr = '';
  	} // else if($_SERVER['QUERY_STRING'] != '')
}  // if(isset($_SERVER['QUERY_STRING']))
// construct values to pass in the URL
$URLnoQueryStr = $proto . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$PreviousURL = $proto . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . $querStr;
$base_domain = $_SERVER['HTTP_HOST'];
$ip = '';
$ip = $_SERVER['REMOTE_ADDR'];

if(!isset($MembersOnly) && isset($FormView)){  // checking $FormView ensures keyfile was included, not browsed directly
	// this is NOT a Protected Page Access
	if(isset($_GET['logOut'])){
		// member is logging out, delete Sentry cookies
		setcookie('Sentry_loginTkn','deleted',time()-60000,'/');
		setcookie('Sentry_member_ID','deleted',time()-60000,'/');
		setcookie('Sentry_memberAccessLvl','deleted',time()-60000,'/');
		setcookie('Sentry_firstName','deleted',time()-60000,'/');
		setcookie('Sentry_lastName','deleted',time()-60000,'/');
		setcookie('Sentry_frmIso','deleted',time()-60000,'/');
		setcookie('psist','deleted',time()-60000,'/');
		$logOutURL = $_GET['logOutURL'];
		header("Location: $logOutURL");
		exit;
	}  // if(isset($_GET['logOut']))
	
	$isReturn = false;
	$loggedIn = false;
	
	// determine if this browser is already still logged in
	$newLogin = '';
	if(isset($_GET['newLogin'])){
		$newLogin = $_GET['newLogin'];  // newLogin will only contain a value if this IS DEFINITELY a login return hit from loginAction.asp
	}
	if(isset($_COOKIE['Sentry_loginTkn']) && $newLogin == ''){
		// the browser is already still logged in 
		if(isset($_COOKIE['Sentry_frmIso'])){
			// there was an Isolator cookie created before when member logged in
			if($_COOKIE['Sentry_frmIso'] != ''){
				// Isolator cookie was not empty string
				$Sentry_frmIso = $_COOKIE['Sentry_frmIso'];
				if($Sentry_frmIso == $Frm){
					// the Isolator cookie matches the $Frm string in the login topcode
					$loggedIn = true;
				} // if($Sentry_frmIso == $Frm)
				else{  //  if($Sentry_frmIso == $Frm)
					// the Isolator cookie DOES NOT match the $Frm string in the login topcode
					$loggedIn = false;
				}  // else if($Sentry_frmIso == $Frm)
			}  // if($_COOKIE['Sentry_frmIso'] != '')
			else{ // if($_COOKIE['Sentry_frmIso'] != '')
				// Isolator cookie WAS empty string
				$loggedIn = false;
			}  // else if($_COOKIE['Sentry_frmIso'] != '')
		} // if(isset($_COOKIE['Sentry_frmIso']))
		else{  // if(isset($_COOKIE['Sentry_frmIso']))
			// there was NOT an Isolator cookie set
			$loggedIn = false;
		}  // else if(isset($_COOKIE['Sentry_frmIso']))
		
		
		if($loggedIn == true){
			// freshen the cookies' expiration dates on refresh
			// otherwise, they will expire at preset time
			$Sentry_loginTkn = $_COOKIE['Sentry_loginTkn'];
			$Sentry_member_ID = $_COOKIE['Sentry_member_ID'];
			$Sentry_memberAccessLvl = $_COOKIE['Sentry_memberAccessLvl'];
			$Sentry_firstName = $_COOKIE['Sentry_firstName'];
			$Sentry_lastName = $_COOKIE['Sentry_lastName'];
			$Sentry_frmIso = $_COOKIE['Sentry_frmIso'];
			
			$newLength = (time() + $CookieTime);
			if($psistCk != true){
				setcookie('Sentry_loginTkn', $Sentry_loginTkn, $newLength,'/');
				setcookie('Sentry_member_ID', $Sentry_member_ID, $newLength,'/');
				setcookie('Sentry_memberAccessLvl', $Sentry_memberAccessLvl, $newLength,'/');
				setcookie('Sentry_firstName', $Sentry_firstName, $newLength,'/');
				setcookie('Sentry_lastName', $Sentry_lastName, $newLength,'/');
				setcookie('Sentry_frmIso', $Sentry_frmIso, $newLength,'/');
			} // if ($psistCk != true)
			$disabledStr = ' disabled'; // prevent double logins
			$color = ' color="#CCCCCC"'; // gray the text
		}  // if($loggedIn == true)
		else{
			// $loggedIn did NOT equal true
			$disabledStr = '';
			$color = '';
		}
	}  //  if(isset($_COOKIE['Sentry_loginTkn']) && $newLogin == '')
	else{  // if(isset($_COOKIE['Sentry_loginTkn']) && $newLogin == '')
		// the browser is NOT already still logged in OR this is a newLogin=1 hit from loginAction.asp
		$loggedIn = false;
		$disabledStr = '';
		$color = '';
		// determine if this is a fresh browse or examinable return from Sentry action page
		if(isset($_SERVER['QUERY_STRING'])){
			if(isset($_GET['Sentry_sendEmTo'])){
				$isReturn = true;		
				$Sentry_sendEmTo = $_GET['Sentry_sendEmTo'];
			} // if(isset($_GET['Sentry_sendEmTo']))
			else{
				$isReturn = false;
			}  // else if(isset($_GET['Sentry_sendEmTo']))
		} // if(isset($_SERVER['QUERY_STRING']))
		// base actions on whether browse is fresh or returned
		if($isReturn == false){
			// this is NOT a returning hit, it is a fresh browse, do nothing
			
		}  // if($isReturn == false)
		else{
			// $isReturn equals True, this is a returning hit from loginAction.asp
			// determine if $_GET['Sentry_sendEmTo'] was alone (user dumped) or not (user admitted)
			if(isset($_GET['Sentry_loginTkn'])){
				if($_GET['Sentry_loginTkn'] != ''){
					// user admitted by Sentry
					// get values from Querystring
					
					// BEGIN psist
					$psist = '0';
					if(isset($_GET['psist'])){
						$psist = $_GET['psist'];
					}
					if($psist == '1'){
						$CookieTime = $extendedCookieTime; // set Global CookieTime
						$length = (time() + $CookieTime);
						setcookie('psist', '1', $length,'/');  // set a cookie for psist 
					} // if($psist == '1')
					else{
						$CookieTime = $defaultCookieTime;
						if(isset($_GET['psist'])){  // if psist cookie exists
							 setcookie('psist','',time()-60000,'/');  // clear cookie
						}
					}  // else if($psist == '1')
					// END psist
					
					$length = (time() + $CookieTime);
					$Sentry_loginTkn = $_GET['Sentry_loginTkn'];
					$Sentry_member_ID = $_GET['Sentry_member_ID'];
					$Sentry_memberAccessLvl = $_GET['Sentry_memberAccessLvl'];
					$Sentry_firstName = $_GET['Sentry_firstName'];
					$Sentry_lastName = $_GET['Sentry_lastName'];
					
					// v. 2.5.2
					// encrypt personally identifiable info (first name and last name)
					$Sentry_firstName = strrev($Sentry_firstName);
					$Sentry_firstName = base64_encode($Sentry_firstName);
					$Sentry_lastName = strrev($Sentry_lastName);
					$Sentry_lastName = base64_encode($Sentry_lastName);
					
// BEGIN Webmaster (User) may place their custom Database code below this line
	// (this section only executes after your Member has safely been admitted to your site).	

// END Webmaster (User) may place their custom Database code above this line
					
					// set Cookies
					if(isset($Frm)){
						if($Frm != ''){
							setcookie('Sentry_frmIso', $Frm, $length,'/');
						} // if($Frm != '')
						else{ // if($Frm != '')
							// Frm is NOT set to a value in the topcode. Frm must be set to use this version of the Keyfile
							echo 'Frm value not set to a value in topcode. Keyfile version may be newer than topcode. To fix, update all Sentry code to newest version.';
							exit;
						}  // if($Frm != '')
					}  // if(isset($Frm)) 
					else{ // if(isset($Frm))
						// Frm is NOT set in the topcode. Frm must be set to use this version of the Keyfile
						echo 'Frm value not provided in topcode. Keyfile version may be newer than topcode. To fix, update all Sentry code to newest version.';
						exit;
					} // if(isset($Frm))	
					setcookie('Sentry_loginTkn', $Sentry_loginTkn, $length,'/');
					setcookie('Sentry_member_ID', $Sentry_member_ID, $length,'/');
					setcookie('Sentry_memberAccessLvl', $Sentry_memberAccessLvl, $length,'/');
					setcookie('Sentry_firstName', $Sentry_firstName, $length,'/');
					setcookie('Sentry_lastName', $Sentry_lastName, $length,'/');
					// send the browser to final place
					header("Location: $Sentry_sendEmTo");
					exit;
				} // if($_GET['Sentry_loginTkn'] != '')
			} // if(isset($_GET['Sentry_loginTkn']))
			else{
				// $_GET['Sentry_sendEmTo'] was alone, send away
				header("Location: $Sentry_sendEmTo");
				exit;
			} // else if(isset($_GET['Sentry_loginTkn']))		
		}  // else if($isReturn == false)
	} // else if(isset($_COOKIE['Sentry_loginTkn']) && $newLogin == '')
}  // if(!isset($MembersOnly) && isset($FormView))
elseif(isset($MembersOnly) && !isset($FormView)){
	// this IS a Protected Page Access ($MembersOnly is set)
	if($MembersOnly == true){
		// page protection is toggled on, ignore otherwise
		if(isset($_COOKIE['Sentry_Verified'])){
			// this interior block now NO LONGER EXECUTES EVER
			/* if($_COOKIE['Sentry_Verified'] == $WebKey){ // would be set to their db webKey in the topcode
				// they are clear, delete Sentry_Verified cookie
				setcookie('Sentry_Verified','',time()-60000,'/');
				// refresh other cookies, and display page
				$newLength = (time() + $CookieTime);
				
				$Sentry_loginTkn = $_COOKIE['Sentry_loginTkn'];
				$Sentry_member_ID = $_COOKIE['Sentry_member_ID'];
				$Sentry_memberAccessLvl = $_COOKIE['Sentry_memberAccessLvl'];
				$Sentry_firstName = $_COOKIE['Sentry_firstName'];
				$Sentry_lastName = $_COOKIE['Sentry_lastName'];
				$Sentry_frmIso = $_COOKIE['Sentry_frmIso'];
				
				setcookie('Sentry_loginTkn', $Sentry_loginTkn, $newLength,'/');
				setcookie('Sentry_member_ID', $Sentry_member_ID, $newLength,'/');
				setcookie('Sentry_memberAccessLvl', $Sentry_memberAccessLvl, $newLength,'/');
				setcookie('Sentry_firstName', $Sentry_firstName, $newLength,'/');
				setcookie('Sentry_lastName', $Sentry_lastName, $newLength,'/');
				setcookie('Sentry_frmIso', $Sentry_frmIso, $newLength,'/');
			}
			else{
				// Sentry_Verified cookie was set, but did NOT match, delete and dump
				setcookie('Sentry_Verified','',time()-60000,'/');
				$URL = 'http://www.sentrylogin.com/sentry/Sentry_noAccess.asp?Site_ID='.$Sentry_ID.'&Reason=mustLogin&VerifCkNoMtch=1';
				header("Location: $URL");
				exit;
			} */
		} // if(isset($_COOKIE['Sentry_Verified']))
		else{
			// $_COOKIE['Sentry_Verified'] is NOT set
			if(isset($_GET['Sentry_loginTkn'])){ // if there is a returned tkn in qs
				// this is (apparently) a return hit from checkRegistry.asp
				if(isset($_COOKIE['Sentry_loginTkn'])){
					// there is a cookie set
					if($_COOKIE['Sentry_loginTkn'] == $_GET['Sentry_loginTkn']){
						// they match
						// refresh the cookies, and DISPLAY THE PAGE
						$newLength = (time() + $CookieTime);
						
						$Sentry_loginTkn = $_COOKIE['Sentry_loginTkn'];
						$Sentry_member_ID = $_COOKIE['Sentry_member_ID'];
						$Sentry_memberAccessLvl = $_COOKIE['Sentry_memberAccessLvl'];
						$Sentry_firstName = $_COOKIE['Sentry_firstName'];
						$Sentry_lastName = $_COOKIE['Sentry_lastName'];
						$Sentry_frmIso = $_COOKIE['Sentry_frmIso'];
						if($psistCk != true){
							setcookie('Sentry_loginTkn', $Sentry_loginTkn, $newLength,'/');
							setcookie('Sentry_member_ID', $Sentry_member_ID, $newLength,'/');
							setcookie('Sentry_memberAccessLvl', $Sentry_memberAccessLvl, $newLength,'/');
							setcookie('Sentry_firstName', $Sentry_firstName, $newLength,'/');
							setcookie('Sentry_lastName', $Sentry_lastName, $newLength,'/');
							setcookie('Sentry_frmIso', $Sentry_frmIso, $newLength,'/');
						} // if($psistCk != true)
					} // if($_COOKIE['Sentry_loginTkn'] == $_GET['Sentry_loginTkn'])
					else{
						// cookie and qs do NOT match
						$qs = $_GET['Sentry_loginTkn'];
						if($qs == 'justBanned'){
							// delete cookies
							setcookie('Sentry_loginTkn','',time()-60000,'/');
							setcookie('Sentry_member_ID','',time()-60000,'/');
							setcookie('Sentry_memberAccessLvl','',time()-60000,'/');
							setcookie('Sentry_firstName','',time()-60000,'/');
							setcookie('Sentry_lastName','',time()-60000,'/');
							setcookie('Sentry_frmIso','',time()-60000,'/');
							setcookie('psist','',time()-60000,'/');
							// and dump
							$URL = 'http://www.sentrylogin.com/sentry/Sentry_noAccess.asp?Site_ID='.$Sentry_ID.'&Reason=justBanned';
							header("Location: $URL");
							exit;
						} // if($qs == 'justBanned')
						else{
							// delete cookies
							setcookie('Sentry_loginTkn','',time()-60000,'/');
							setcookie('Sentry_member_ID','',time()-60000,'/');
							setcookie('Sentry_memberAccessLvl','',time()-60000,'/');
							setcookie('Sentry_firstName','',time()-60000,'/');
							setcookie('Sentry_lastName','',time()-60000,'/');
							setcookie('Sentry_frmIso','',time()-60000,'/');
							setcookie('psist','',time()-60000,'/');
							// and dump, different message
							$URL = 'http://www.sentrylogin.com/sentry/Sentry_noAccess.asp?Site_ID='.$Sentry_ID.'&Reason=mustLogin&CkQsNoMtch=1';
							header("Location: $URL");
							exit;
						} // else if($qs == 'justBanned')
					}  // else if($_COOKIE['Sentry_loginTkn'] == $_GET['Sentry_loginTkn'])
				} // if(isset($_COOKIE['Sentry_loginTkn']))
				else{  
					// there is NO cookie set, possible query spoof attempt, dump
					$URL = 'http://www.sentrylogin.com/sentry/Sentry_noAccess.asp?Site_ID='.$Sentry_ID.'&Reason=mustLogin&SpoofTknCk=1';
					header("Location: $URL");
					exit;
				}  // else if(isset($_COOKIE['Sentry_loginTkn']))
			} // if(isset($_GET['Sentry_loginTkn']))
			else{  // there is no returned Sentry_loginTkn in qs, Fresh Browse or Refresh
			
				// CHECK REGISTRY
				// retrieve cookies, if any
				if(isset($_COOKIE['Sentry_loginTkn'])){
					$Sentry_loginTkn = $_COOKIE['Sentry_loginTkn'];
				} // if(isset($_COOKIE['Sentry_loginTkn']))
				else{
					$URL = 'http://www.sentrylogin.com/sentry/Sentry_noAccess.asp?Site_ID='.$Sentry_ID.'&Reason=mustLogin&NoLgnTknCk=1';
					header("Location: $URL");
					exit;
				} // else if(isset($_COOKIE['Sentry_loginTkn']))
				if(isset($_COOKIE['Sentry_member_ID'])){
					$Sentry_member_ID = $_COOKIE['Sentry_member_ID'];
				} // if(isset($_COOKIE['Sentry_member_ID']))
				else{
					$URL = 'http://www.sentrylogin.com/sentry/Sentry_noAccess.asp?Site_ID='.$Sentry_ID.'&Reason=mustLogin&NoMbrID=1';
					header("Location: $URL");
					exit;
				} // else if(isset($_COOKIE['Sentry_member_ID']))		
				// construct $checkRegistryURL
				// was: $checkRegistryURL = 'http://www.sentrylogin.com/sentry/noSockets/checkRegistry.asp?Sentry_ID=' . $Sentry_ID;
				// new for Version 2.75:
				$checkRegistryURL = 'http://www.sentrylogin.com/sentry/noSockets/checkRegistry.asp?vers=2.75&Sentry_ID=' . $Sentry_ID;
				$checkRegistryURL = $checkRegistryURL . '&tkn=' . $Sentry_loginTkn;
				$checkRegistryURL = $checkRegistryURL . '&memb_ID=' . $Sentry_member_ID;
				$URLnoQueryStr = urlencode($URLnoQueryStr);
				$checkRegistryURL = $checkRegistryURL . '&URLnoQry=' . $URLnoQueryStr;
				$NtvQueryStr = urlencode($NtvQueryStr);
				$checkRegistryURL = $checkRegistryURL . '&NtvQry=' . $NtvQueryStr;
				if(isset($PpLID)){
					$checkRegistryURL = $checkRegistryURL . '&PpLID=' . $PpLID;  // optional
				}
				if(isset($Level)){
					$checkRegistryURL = $checkRegistryURL . '&LvL=' . $Level;  // optional
				}
				if(isset($SingleOut)){
					if($SingleOut != 0){
						$checkRegistryURL = $checkRegistryURL . '&SngOut=' . $SingleOut;  // optional
					}
				}
				header("Location: $checkRegistryURL");
				exit;
			}  // if(isset($_GET['Sentry_loginTkn']))
		}  // else if(isset($_COOKIE['Sentry_Verified']))
	}  //  if($MembersOnly == true)
	else{  // $MembersOnly turned OFF
	}
} // elseif(isset($MembersOnly) && !isset($FormView))
if(!isset($MembersOnly) && !isset($FormView)){
	// this is a direct browse to the keyfile
	?>
This is Sentry_keyfile.php Version 2.76 (BUILD D) <br>
<br>
	<a href="http://www.sentrylogin.com/">Sentry Login Password Protection, Membership Management System.  Sentrylogin.com</a><br>
	<br>
	<?php
	echo dirname(__FILE__);
	echo '<br>';
	echo 'The Server https value is: ' . $_SERVER['HTTPS'];
}
?>