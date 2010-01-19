<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2010 Allan Jacobsen (allan.j@cobsen.dk)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Plugin 'Frontend change password' for the 'fechangepassword' extension.
 *
 * @author	Allan Jacobsen <allan.j@cobsen.dk>
 */


require_once(PATH_tslib."class.tslib_pibase.php");

class tx_fechangepassword_pi1 extends tslib_pibase {
	var $prefixId = "tx_fechangepassword_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_fechangepassword_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "fechangepassword";	// The extension key.
	
	/*cab services ag - begin */
	var $tmpl = '';
	/*cab services ag - end */
	
	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();
		$minlen = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],'minlength');
		$content = '';
		if ($minlen < 2) $minlen = 4;

		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		
		/*cab services ag - begin */
		$this->tmpl = $this->cObj->fileResource($this->conf['templateFile']);
		/*cab services ag - end */
		
		debug($GLOBALS['TSFE']);
		if ($GLOBALS['TSFE']->loginUser)        {
			$newpw=$this->piVars['pw'];
			$newpw2=$this->piVars['pw2'];
			$content = '<div class="message">';
			
			//		debug($this->cObj->data['pi_flexform']);
			if (($newpw==$newpw2) && (strlen($newpw) >= $minlen)) {
				if ((ctype_alnum($this->piVars['pw'])) and (ctype_alnum($this->piVars['pw2']))) {
					$v = array(
						'password' => $newpw
					);
					$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid='.$GLOBALS['TYPO3_DB']->quoteStr($GLOBALS['TSFE']->fe_user->user['uid'],'fe_users'),$v);
					$content.=$this->pi_getLL('succes','',1);
				} else {
					$content.=$this->pi_getLL('pwillegal','',1);
				}
			} else {
				if ($newpw!=$newpw2) {
					$content.=$this->pi_getLL('pwnotequal','',1);
				} elseif((isset($newpw)) && (strlen($newpw) < $minlen)) {
					$content.=$this->pi_getLL('pwtooshort','',1);
				}
			}
			
			/*cab services ag - begin */
			$content.='</div>';
			
			$tmpl = $this->cObj->getSubpart($this->tmpl, '###FORM###');
			
			$marker = array();
			$marker['###ACTION###'] = $this->pi_getPageLink($GLOBALS["TSFE"]->id);
			$marker['###NAME_PASSWORD###'] = $this->prefixId.'[pw]';
			$marker['###LABEL_PASSWORD###'] = $this->pi_getLL('password1','',1);
			$marker['###VALUE_PASSWORD###'] = htmlspecialchars($this->piVars["pw"]);
			
			$marker['###NAME_PASSWORD2###'] = $this->prefixId.'[pw2]';
			$marker['###LABEL_PASSWORD2###'] = $this->pi_getLL('password2','',1);
			$marker['###VALUE_PASSWORD2###'] = htmlspecialchars($this->piVars["pw2"]);
			
			$marker['###NAME_SUBMIT###'] = $this->prefixId.'[submit_button]';
			$marker['###VALUE_SUBMIT###'] = htmlspecialchars($this->pi_getLL("submit_button_label"));
			
			$content .= $this->cObj->substituteMarkerArrayCached($tmpl, $marker);
			/*cab services ag - end */
		}
		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/fechangepassword/pi1/class.tx_fechangepassword_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/fechangepassword/pi1/class.tx_fechangepassword_pi1.php"]);
}

?>
