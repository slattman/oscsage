<?php

define('mysql_host', 'xxx.xx.xx.xxx:xxxx');
define('mysql_user', 'sysadmin');
define('mysql_password', 'xxxx');
define('mysql_db', 'simply');

class Sage {

	private static $link;

	private static function construct() {
		self::connect();
	}

	private static function destruct($str = 0) {
		self::disconnect();
	}

	private static function connect() {
		self::$link = mysql_connect(mysql_host, mysql_user, mysql_password) or die("Unable to connect to sage.");
		mysql_select_db('simply', self::$link);
	}

	private static function query($query = false) {
		if ($query) {
			return mysql_query($query, self::$link);
		} else {
			return 0;
		}
	}

	private static function disconnect() {
		mysql_close(self::$link);
	}

	/*
	 * Static Creation Method
	*/
	public static function execute() {
		self::construct();
		return new sage();
	}

	/*
	 * Public methods();
	*/

	public function addCustomer($customers_id = false) {
		global $messageStack;
		if ($customers_id and is_object($messageStack)) {
			$c = mysql_fetch_object(tep_db_query("select c.*, a.* from customers c, address_book a where c.customers_id = $customers_id and c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id"));	
			if (is_object($c) and $c->customers_email_address) {
				$sc = mysql_fetch_object(self::query("select * from tcustomr where sEmail = '".$c->customers_email_address."' limit 1"));
				if (!is_object($sc)) {
					$r = mysql_fetch_assoc(self::query("SELECT max(lId) + 1 as cid FROM `tcustomr` WHERE 1")); extract($r);
					if ($cid > 1) {

						$lNextId = $cid + 1;
						$r = mysql_fetch_assoc(tep_db_query("select zone_name as province from zones where zone_id = $c->entry_zone_id")); extract($r);
						$r = mysql_fetch_assoc(tep_db_query("select countries_name as country from countries where countries_id = $c->entry_country_id")); extract($r);
						self::query("INSERT INTO `tcustomr` (`lId`, `sName`, `dtASDate`, `tmASTime`, `sASUserId`, `sASOrgId`, `sCntcName`, `sStreet1`, `sStreet2`, `sCity`, `sProvState`, `sCountry`, `sPostalZip`, `sPhone1`, `sPhone2`, `sFax`, `dCrLimit`, `dAmtYtd`, `dLastYrAmt`, `fDiscPay`, `nDiscDay`, `nNetDay`, `bStatement`, `bContOnChq`, `bEmailForm`, `bEmailCnfm`, `bUseSimply`, `bUseMyItem`, `dAmtYtdHm`, `dAmtLYHm`, `dCrLimitHm`, `bMemInToDo`, `bUsed`, `lCurrncyId`, `sEmail`, `sWebSite`, `bUseMailAd`, `bInactive`, `lTaxCode`, `bIntCust`, `lDfltDptId`, `lAcDefRev`, `lDpDefRev`, `lCompId`, `nLangPref`, `lPrcListId`, `lModVer`, `dtSince`, `dtLastSal`, `bSyncOL`, `lInvLocId`, `dStdDisc`, `lSalManID`, `bDirectPay`) VALUES ('".$cid."', '".$c->customers_firstname." ".$c->customers_lastname."', NOW(), '1899-12-30 11:06:52', 'Toni', 'winsim', '', '".$c->entry_street_address."', '', '".$c->entry_city."', '".$province."', '".$country."', '".$c->entry_postcode."', '".$c->entry_telephone."', '', '', -1, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, -1, 0, 1, 1, '".$c->customers_email_address."', '', 1, 0, 20, 0, 0, 0, 0, 1, 0, 1, 0, NOW(), NOW(), 0, 1, 0, 0, 0)");
						
						self::query("UPDATE `simply`.`tNxtPIds` SET `dtASDate`='".date("Y-m-d", time())." 00:00:00',`tmASTime`='1899-12-30 ".date("H:i:s")."',`sASUserId`='Toni',`sASOrgId`='winsim',`lNextId`=".$lNextId." WHERE `lId`=50");
						self::query("UPDATE `simply`.`tTBStat` SET `dtASDate`='".date("Y-m-d", time())." 00:00:00',`tmASTime`='1899-12-30 ".date("H:i:s")."',`sASUserId`='Toni',`sASOrgId`='winsim' WHERE `lId`=50");
						self::query("INSERT INTO `simply`.`tCusUDF` (`lCusId`,`dtASDate`,`tmASTime`,`sASUserId`,`sASOrgId`,`sUsrDfnd1`,`sUsrDfnd2`,`sUsrDfnd3`,`sUsrDfnd4`,`sUsrDfnd5`,`bPopInfo1`,`bPopInfo2`,`bPopInfo3`,`bPopInfo4`,`bPopInfo5`) VALUES (".$cid.",'".date("Y-m-d", time())." 00:00:00','1899-12-30 ".date("H:i:s")."','Toni','winsim','','','','','',0,0,0,0,0)");
						self::query("INSERT INTO `simply`.`tCusTxDt` (`lCustId`,`lTaxAuth`,`bExempt`,`sTaxId`,`dtASDate`,`tmASTime`,`sASUserId`,`sASOrgId`) VALUES (".$cid.",1,0,'','".date("Y-m-d", time())." 00:00:00','1899-12-30 ".date("H:i:s")."','Toni','winsim')");
						self::query("INSERT INTO `simply`.`tCusTxDt` (`lCustId`,`lTaxAuth`,`bExempt`,`sTaxId`,`dtASDate`,`tmASTime`,`sASUserId`,`sASOrgId`) VALUES (".$cid.",2,0,'','".date("Y-m-d", time())." 00:00:00','1899-12-30 ".date("H:i:s")."','Toni','winsim')");
						self::query("INSERT INTO `simply`.`tCusTxDt` (`lCustId`,`lTaxAuth`,`bExempt`,`sTaxId`,`dtASDate`,`tmASTime`,`sASUserId`,`sASOrgId`) VALUES (".$cid.",3,0,'','".date("Y-m-d", time())." 00:00:00','1899-12-30 ".date("H:i:s")."','Toni','winsim')");
						
						$r = mysql_fetch_assoc(self::query("SELECT ptPosX, ptPosY FROM tIcnNdxC WHERE lUserId=10 and lLineNum = (SELECT lNumLast FROM ticnhdrc WHERE lUserId = 10)")); extract($r);
						$r = mysql_fetch_assoc(self::query("SELECT lLineNum + 1, lLdgrRecId + 1 FROM tIcnNdxC  WHERE lUserId=10 AND lLineNum=(SELECT max(`lLineNum`) from ticnndxc where `lUserId`=10)")); extract($r);
						self::query("INSERT INTO `simply`.`tIcnNdxC` (`lUserId`,`lLineNum`,`lLdgrRecId`,`ptPosX`,`ptPosY`) VALUES (10,".$lLineNum.",".$lLdgrRecId.",".$ptPosX.",".$ptPosY.")");

						$ar = tep_db_query("select * from address_book where customers_id = $customers_id and address_book_id != $c->customers_default_address_id");
						while ($a = mysql_fetch_object($ar)) {
							$lId = 0;
							$province = '';
							$country = '';
							$r = mysql_fetch_assoc(tep_db_query("select zone_name as province from zones where zone_id = $a->entry_zone_id")); extract($r);
							$r = mysql_fetch_assoc(tep_db_query("select countries_name as country from countries where countries_id = $a->entry_country_id")); extract($r);
							$r = mysql_fetch_assoc(self::query("SELECT max(lId) + 1 as lId FROM `tCustShp` WHERE 1")); extract($r);
							self::query("INSERT INTO `simply`.`tCustShp` (`lCustId`,`lId`,`dtASDate`,`tmASTime`,`sASUserId`,`sASOrgId`,`sAddrName`,`sAddrNameF`,`sShipCntc`,`sShipStrt1`,`sShipStrt2`,`sShipCity`,`sShipPrvSt`,`sShipCnty`,`sShipPstZp`,`sShipPhn1`,`sShipPhn2`,`sShipFax`,`sShipEmail`,`bDefAddr`) VALUES (".$cid.",".$lId.",'".date("Y-m-d", time())." 00:00:00','1899-12-30 ".date("H:i:s")."','Toni','winsim','Ship-to Address','Adresse d\'expédition','".$a->entry_firstname." ".$a->entry_lastname."','".$a->entry_street_address."','','".$a->entry_city."','".$province."','".$country."','".$a->entry_postcode."','".$a->entry_telephone."','','".$a->entry_fax."','".$a->entry_email_address."',0)");
						}

						self::query("UPDATE `simply`.`tNxtPIds` SET `dtASDate`='".date("Y-m-d", time())." 00:00:00',`tmASTime`='1899-12-30 ".date("H:i:s")."',`sASUserId`='Toni',`sASOrgId`='winsim',`lNextId`=".$lId." WHERE `lId`=386");

				        	$messageStack->add_session('search', sprintf(NOTICE_CUSTOMER_SYNC, $customers_id), 'success');
					} else {
					        $messageStack->add_session('search', sprintf(NOTICE_CUSTOMER_SYNC_FAILED, $customers_id), 'warning');
					}
				} else {
				        $messageStack->add_session('search', sprintf(NOTICE_CUSTOMER_SYNC_FAILED, $customers_id), 'warning');
				}
			} else {
			        $messageStack->add_session('search', sprintf(NOTICE_CUSTOMER_SYNC_FAILED, $customers_id), 'warning');
			}
			self::destruct();
		}
	}

}
