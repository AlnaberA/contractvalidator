<?php

require_once("mcl_Oci.php");
require_once("mcl_Ldap.php");
mcl_Ldap::session_start('Validations');

class auth
{
	const ADMIN_BASIC = 1;
	const ADMIN_SUMMARY = 2;
	const ADMIN_CHANGE = 4;

	static $user;

	public static function check($key = false)
	{
		if (empty(self::$user)) {		

			$user = false;
			if (mcl_Ldap::authorized()) {
				$user = mcl_Ldap::get_user();
			}
			
			if (!$user) {
				self::$user = array(
					'status' => mcl_Ldap::get_error()
				);
			} else {

				self::$user = array(
					'status' => 'authorized',
					'level' => 0,
					'usid' => strtolower($user['username']),
					'name' => $user['fname'],
					'details' => $user
				);
			}
			
			//We no longer need the session at this point, so release
			session_write_close();
		}
		
		if (isset($key) && !empty($key) && isset(self::$user[$key])) {
			return self::$user[$key];
		} else {
			return self::$user;
		}
	}
	
	public static function deny()
	{
		if (!empty(self::$user)) {
			self::$user['status'] = 'denied';
		}
	}
}

?>