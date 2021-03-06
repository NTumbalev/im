<?php 
/**
 * the purpose I added this class is to make the file system much flexible 
 * for customization.
 * Actually,  this is a kind of interface and you should modify it to fit your system
 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
 * @link www.phpletter.com
 * @since 4/August/2007
 */
	class Auth
	{
		function __construct()
		{
		}
		
		function Auth()
		{
			$this->__construct();
		}
		/**
		 * check if the user has logged
		 *
		 * @return boolean
		 */
		function isLoggedIn()
		{
			return (!empty($_SESSION['_sf2_attributes'][CONFIG_SESSION_LOGGEDIN_VARNAME])?true:false);
		}
		/**
		 * validate the username & password
		 * @return boolean
		 *
		 */
		function login()
		{
			if($_POST['username'] === CONFIG_LOGIN_USERNAME && $_POST['password'] === CONFIG_LOGIN_PASSWORD)
			{
				$_SESSION[CONFIG_SESSION_LOGGEDIN_VARNAME] = true;
				return true;
			}else 
			{
				return false;
			}
		}
	}
?>