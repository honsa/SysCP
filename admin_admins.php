<?php
/**
 * filename: $Source$
 * begin: Wednesday, Aug 11, 2004
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version. This program is distributed in the
 * hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * @author Florian Lippert <flo@redenswert.de>
 * @copyright (C) 2003-2004 Florian Lippert
 * @package Panel
 * @version $Id$
 */

	define('AREA', 'admin');

	/**
	 * Include our init.php, which manages Sessions, Language etc.
	 */
	require("./lib/init.php");
	
	if(isset($_POST['id']))
	{
		$id=intval($_POST['id']);
	}
	elseif(isset($_GET['id']))
	{
		$id=intval($_GET['id']);
	}

	if($page=='admins' && $userinfo['change_serversettings'] == '1' )
	{
		if($action=='')
		{
			if(isset($_GET['sortby']))
			{
				$sortby=addslashes($_GET['sortby']);
			}
			else
			{
				$sortby='loginname';
			}
			if(isset($_GET['sortorder']) && strtolower($_GET['sortorder'])=='desc')
			{
				$sortorder='DESC';
			}
			else
			{
				$sortorder='ASC';
			}

			$admins='';
			$result=$db->query("SELECT * FROM `".TABLE_PANEL_ADMINS."` ORDER BY `$sortby` $sortorder");
			while($row=$db->fetch_array($result))
			{
				$row['traffic_used']=round($row['traffic_used']/(1024*1024),4);
				$row['traffic']=round($row['traffic']/(1024*1024),4);
				$row['diskspace_used']=round($row['diskspace_used']/1024,2);
				$row['diskspace']=round($row['diskspace']/1024,2);
				$row['deactivated'] = str_replace('0', $lng['panel']['yes'], $row['deactivated']);
				$row['deactivated'] = str_replace('1', $lng['panel']['no'], $row['deactivated']);

				$row = str_replace_array('-1', 'UL', $row, 'customers domains diskspace traffic mysqls emails email_forwarders ftps subdomains');

				eval("\$admins.=\"".getTemplate("admins/admins_admin")."\";");
			}
			eval("echo \"".getTemplate("admins/admins")."\";");
		}

		elseif($action=='delete' && $id!=0)
		{
			if($id == '1')
			{
				standard_error('youcantdeletechangemainadmin');
				exit;
			}
			$result=$db->query_first("SELECT * FROM `".TABLE_PANEL_ADMINS."` WHERE `adminid`='$id'");
			if($result['loginname']!='')
			{
				if(isset($_POST['send']) && $_POST['send']=='send')
				{
					$db->query("DELETE FROM `".TABLE_PANEL_ADMINS."` WHERE `adminid`='$id'");
					$db->query("DELETE FROM `".TABLE_PANEL_TRAFFIC_ADMINS."` WHERE `adminid`='$id'");
					$db->query("UPDATE `".TABLE_PANEL_CUSTOMERS."` SET `adminid` = '1' WHERE `adminid` = '$id'");
					$db->query("UPDATE `".TABLE_PANEL_DOMAINS."` SET `adminid` = '1' WHERE `adminid` = '$id'");
					updateCounters () ;

					header("Location: $filename?page=$page&s=$s");
				}
				else {
					ask_yesno('admin_admin_reallydelete', $filename, "id=$id;page=$page;action=$action", $result['loginname']);
				}
			}
		}

		elseif($action=='add')
		{
			if(isset($_POST['send']) && $_POST['send']=='send')
			{
				$name = addslashes ( $_POST['name'] ) ;
				$loginname = addslashes ( $_POST['loginname'] ) ;
				$loginname_check = $db->query_first("SELECT `loginname` FROM `".TABLE_PANEL_ADMINS."` WHERE `loginname`='".$loginname."'");
				$password = addslashes ( $_POST['password'] ) ;
				$email = $idna_convert->encode ( addslashes ( $_POST['email'] ) ) ;
				$customers = intval_ressource ( $_POST['customers'] ) ;
				$domains = intval_ressource ( $_POST['domains'] ) ;
				$subdomains = intval_ressource ( $_POST['subdomains'] ) ;
				$emails = intval_ressource ( $_POST['emails'] ) ;
				$email_forwarders = intval_ressource ( $_POST['email_forwarders'] ) ;
				$ftps = intval_ressource ( $_POST['ftps'] ) ;
				$mysqls = intval_ressource ( $_POST['mysqls'] ) ;
				$customers_see_all = intval ( $_POST['customers_see_all'] ) ;
				$domains_see_all = intval ( $_POST['domains_see_all'] ) ;
				$change_serversettings = intval ( $_POST['change_serversettings'] ) ;

				$diskspace = intval_ressource ( $_POST['diskspace'] ) ;
				$traffic = doubleval_ressource ( $_POST['traffic'] ) ;
				$diskspace = $diskspace * 1024 ;
				$traffic = $traffic * 1024 * 1024 ;

				if($name == '' || $loginname == '' || $loginname_check['loginname'] == $loginname || $password == '' || $email == '' || !verify_email($email) || !check_username($loginname))
				{
					standard_error('notallreqfieldsorerrors');
					exit;
				}
				else
				{
					if($customers_see_all != '1')
					{
						$customers_see_all = '0';
					}
					if($domains_see_all != '1')
					{
						$domains_see_all = '0';
					}
					if($change_serversettings != '1')
					{
						$change_serversettings = '0';
					}

					$result=$db->query("INSERT INTO `".TABLE_PANEL_ADMINS."` (`loginname`, `password`, `name`, `email`, `change_serversettings`, `customers`, `customers_see_all`, `domains`, `domains_see_all`, `diskspace`, `traffic`, `subdomains`, `emails`, `email_forwarders`, `ftps`, `mysqls`)
					                   VALUES ('$loginname', '".md5($password)."', '$name', '$email', '$change_serversettings', '$customers', '$customers_see_all', '$domains', '$domains_see_all', '$diskspace', '$traffic', '$subdomains', '$emails', '$email_forwarders', '$ftps', '$mysqls')");
					$adminid=$db->insert_id();
					header("Location: $filename?page=$page&s=$s");
				}
			}
			else
			{
				$change_serversettings=makeyesno('change_serversettings', '1', '0', '0');
				$customers_see_all=makeyesno('customers_see_all', '1', '0', '0');
				$domains_see_all=makeyesno('domains_see_all', '1', '0', '0');
				eval("echo \"".getTemplate("admins/admins_add")."\";");
			}
		}

		elseif($action=='edit' && $id!=0)
		{
			if($id == '1')
			{
				standard_error('youcantdeletechangemainadmin');
				exit;
			}
			$result=$db->query_first("SELECT * FROM `".TABLE_PANEL_ADMINS."` WHERE `adminid`='$id'");
			if($result['loginname']!='')
			{
				if(isset($_POST['send']) && $_POST['send']=='send')
				{
					$name = addslashes ( $_POST['name'] ) ;
					$newpassword = addslashes ( $_POST['newpassword'] ) ;
					$email = $idna_convert->encode ( addslashes ( $_POST['email'] ) ) ;
					$deactivated = intval ( $_POST['deactivated'] ) ;
					$customers = intval_ressource ( $_POST['customers'] ) ;
					$domains = intval_ressource ( $_POST['domains'] ) ;
					$subdomains = intval_ressource ( $_POST['subdomains'] ) ;
					$emails = intval_ressource ( $_POST['emails'] ) ;
					$email_forwarders = intval_ressource ( $_POST['email_forwarders'] ) ;
					$ftps = intval_ressource ( $_POST['ftps'] ) ;
					$mysqls = intval_ressource ( $_POST['mysqls'] ) ;
					$customers_see_all = intval ( $_POST['customers_see_all'] ) ;
					$domains_see_all = intval ( $_POST['domains_see_all'] ) ;
					$change_serversettings = intval ( $_POST['change_serversettings'] ) ;

					$diskspace = intval ( $_POST['diskspace'] ) ;
					$traffic = doubleval_ressource ( $_POST['traffic'] ) ;
					$diskspace = $diskspace * 1024 ;
					$traffic = $traffic * 1024 * 1024 ;

					if($name=='' || $email=='' || !verify_email($email) )
					{
						standard_error('notallreqfieldsorerrors');
						exit;
					}
					else
					{
						$updatepassword='';
						if($newpassword!='')
						{
							$updatepassword="`password`='".md5($newpassword)."', ";
						}

						if($deactivated != '1')
						{
							$deactivated = '0';
						}
						
						if($customers_see_all != '1')
						{
							$customers_see_all = '0';
						}
						if($domains_see_all != '1')
						{
							$domains_see_all = '0';
						}
						if($change_serversettings != '1')
						{
							$change_serversettings = '0';
						}

						$db->query("UPDATE `".TABLE_PANEL_ADMINS."` SET `name`='$name', `email`='$email', `change_serversettings` = '$change_serversettings', `customers` = '$customers', `customers_see_all` = '$customers_see_all', `domains` = '$domains', `domains_see_all` = '$domains_see_all', $updatepassword `diskspace`='$diskspace', `traffic`='$traffic', `subdomains`='$subdomains', `emails`='$emails', `email_forwarders`='$email_forwarders', `ftps`='$ftps', `mysqls`='$mysqls', `deactivated`='$deactivated' WHERE `adminid`='$id'");

						header("Location: $filename?page=$page&s=$s");
					}
				}
				else
				{
					$result['traffic']=$result['traffic']/(1024*1024);
					$result['diskspace']=$result['diskspace']/1024;
					$result['email'] = $idna_convert->decode($result['email']);
					$change_serversettings=makeyesno('change_serversettings', '1', '0', $result['change_serversettings']);
					$customers_see_all=makeyesno('customers_see_all', '1', '0', $result['customers_see_all']);
					$domains_see_all=makeyesno('domains_see_all', '1', '0', $result['domains_see_all']);
					$deactivated=makeyesno('deactivated', '1', '0', $result['deactivated']);
					eval("echo \"".getTemplate("admins/admins_edit")."\";");
				}
			}
		}
	}

?>