<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class magicmirror2 extends eqLogic {
    /*     * *************************Attributs****************************** */
	
    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {}
     */
	 
    public static function cron5() {
		log::add('magicmirror2','debug', 'cron enter');
		foreach (self::byType('magicmirror2') as $magicmirror2) {//parcours tous les équipements du plugin vdm
			log::add('magicmirror2','debug', 'refresh::'.($magicmirror2->getId()));
			if ($magicmirror2->getIsEnable() == 1) {//vérifie que l'équipement est actif
				$cmd = $magicmirror2->getCmd(null, 'mm_refresh');//retourne la commande "refresh si elle existe
				if (!is_object($cmd)) {//Si la commande n'existe pas
					continue; //continue la boucle
				}
				$cmd->execCmd(); // la commande existe on la lance
			}
		}
    }
    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {}
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {}
     */

    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {}

    public function postInsert() {}

    public function preSave() {}

    public function postSave() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		

    }

    public function preUpdate() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		if ($this->getConfiguration('magicmirror_ip') == '') {
			throw new Exception(__('L\'adresse IP ne peut pas être vide !', __FILE__));
		}
		if ($this->getConfiguration('cjmm_notification_timer') == '') {
			throw new Exception(__('Délai d\'affichage des notifications ne peut pas être vide !', __FILE__));
		}
		if ($this->getConfiguration('cjmm_apiChecked') == '' || $this->getConfiguration('cjmm_apiChecked') == '0') {
			throw new Exception(__('Vous n\'avez pas vérifié l\'api de votre MagicMirror² !', __FILE__));
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::configuration completed !');
    }

    public function postUpdate() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		
		
		// CMD Power Off
		$mm_poweroff = $this->getCmd(null, 'mm_poweroff');
		if (!is_object($mm_poweroff)) {
			$mm_poweroff = new magicmirror2Cmd();
			$mm_poweroff->setLogicalId('mm_poweroff');
			$mm_poweroff->setIsVisible(1);
			$mm_poweroff->setName(__('Eteindre', __FILE__));
			$mm_poweroff->setConfiguration('description',__('Eteindre l\'équipement.', __FILE__));
			$mm_poweroff->setType('action');
			$mm_poweroff->setSubType('other');
			$mm_poweroff->setEqLogic_id($this->getId());
			$mm_poweroff->setOrder(1);
			$mm_poweroff->save();
		}

		// CMD Remote.html
		$mm_remotePage = $this->getCmd(null, 'mm_remotePage');
		if (!is_object($mm_remotePage)) {
			$mm_remotePage = new magicmirror2Cmd();
			$mm_remotePage->setLogicalId('mm_remotePage');
			$mm_remotePage->setIsVisible(1);
			$mm_remotePage->setName(__('Remote.html', __FILE__));
			$mm_remotePage->setConfiguration('description',__('Accéder à la page remote.html.', __FILE__));
			$mm_remotePage->setType('info');
			$mm_remotePage->setSubType('string');
			$mm_remotePage->setEqLogic_id($this->getId());
			$mm_remotePage->setOrder(2);
			$mm_remotePage->save();
		}

		// CMD Restart
		$mm_restart = $this->getCmd(null, 'mm_restart');
		if (!is_object($mm_restart)) {
			$mm_restart = new magicmirror2Cmd();
			$mm_restart->setLogicalId('mm_restart');
			$mm_restart->setIsVisible(1);
			$mm_restart->setName(__('Redémarer', __FILE__));
			$mm_restart->setConfiguration('description',__('Redémarer l\'équipement.', __FILE__));
			$mm_restart->setType('action');
			$mm_restart->setSubType('other');
			$mm_restart->setEqLogic_id($this->getId());
			$mm_restart->setOrder(2);
			$mm_restart->save();
		}

		// CMD Reload
		$mm_reload = $this->getCmd(null, 'mm_reload');
		if (!is_object($mm_reload)) {
			$mm_reload = new magicmirror2Cmd();
			$mm_reload->setLogicalId('mm_reload');
			$mm_reload->setIsVisible(1);
			$mm_reload->setName(__('Recharger', __FILE__));
			$mm_reload->setConfiguration('description',__('Recharger l\'application MagicMirror².', __FILE__));
			$mm_reload->setType('action');
			$mm_reload->setSubType('other');
			$mm_reload->setEqLogic_id($this->getId());
			$mm_reload->setOrder(3);
			$mm_reload->save();
		}

		// CMD Monitor Off
		$mm_monitorOff = $this->getCmd(null, 'mm_monitorOff');
		if (!is_object($mm_monitorOff)) {
			$mm_monitorOff = new magicmirror2Cmd();
			$mm_monitorOff->setLogicalId('mm_monitorOff');
			$mm_monitorOff->setIsVisible(1);
			$mm_monitorOff->setName(__('AffichageOff', __FILE__));
			$mm_monitorOff->setConfiguration('description',__('HDMI Off.', __FILE__));
			$mm_monitorOff->setType('action');
			$mm_monitorOff->setSubType('other');
			$mm_monitorOff->setEqLogic_id($this->getId());
			$mm_monitorOff->setOrder(3);
			$mm_monitorOff->save();
		}
		
		// CMD Monitor On
		$mm_monitorOn = $this->getCmd(null, 'mm_monitorOn');
		if (!is_object($mm_monitorOn)) {
			$mm_monitorOn = new magicmirror2Cmd();
			$mm_monitorOn->setLogicalId('mm_monitorOn');
			$mm_monitorOn->setIsVisible(1);
			$mm_monitorOn->setName(__('AffichageOn', __FILE__));
			$mm_monitorOn->setConfiguration('description',__('HDMI On.', __FILE__));
			$mm_monitorOn->setType('action');
			$mm_monitorOn->setSubType('other');
			$mm_monitorOn->setEqLogic_id($this->getId());
			$mm_monitorOn->setOrder(3);
			$mm_monitorOn->save();
		}

		// CMD Set Brightness
		$mm_monitorBrightness = $this->getCmd(null, 'mm_monitorBrightness');
		if (!is_object($mm_monitorBrightness)) {
			$mm_monitorBrightness = new magicmirror2Cmd();
			$mm_monitorBrightness->setLogicalId('mm_monitorBrightness');
			$mm_monitorBrightness->setIsVisible(1);
			$mm_monitorBrightness->setName(__('Luminosité', __FILE__));
			$mm_monitorBrightness->setConfiguration('description',__('Luminosité de 1 à 100.', __FILE__));
			$mm_monitorBrightness->setType('action');
			$mm_monitorBrightness->setSubType('slider');
			$mm_monitorBrightness->setEqLogic_id($this->getId());
			$mm_monitorBrightness->setOrder(3);
			$mm_monitorBrightness->save();
		}
		
		// CMD Refresh (html)
		$mm_refreshHtml = $this->getCmd(null, 'mm_refreshHtml');
		if (!is_object($mm_refreshHtml)) {
			$mm_refreshHtml = new magicmirror2Cmd();
			$mm_refreshHtml->setLogicalId('mm_refreshHtml');
			$mm_refreshHtml->setIsVisible(1);
			$mm_refreshHtml->setName(__('Rafraichir HTML', __FILE__));
			$mm_refreshHtml->setConfiguration('description',__('Rafraichir l\'affichage. (recharge la page html)', __FILE__));
			$mm_refreshHtml->setType('action');
			$mm_refreshHtml->setSubType('other');
			$mm_refreshHtml->setEqLogic_id($this->getId());
			$mm_refreshHtml->setOrder(3);
			$mm_refreshHtml->save();
		}
		
		// CMD Refresh widget
		$refresh = $this->getCmd(null, 'mm_refresh');
		if (!is_object($refresh)) {
			$refresh = new magicmirror2Cmd();
			$refresh->setLogicalId('mm_refresh');
			$refresh->setIsVisible(1);
			$refresh->setName(__('Rafraîchir', __FILE__));
			$refresh->setConfiguration('description',__('Forcer le rafraichissement des statuts et du widget.', __FILE__));
			$refresh->setType('action');
			$refresh->setSubType('other');
			$refresh->setEqLogic_id($this->getId());
			$refresh->setOrder(4);
			$refresh->save();
		}
		
		// CMD Check MagicMirror Status
   		$mm_status = $this->getCmd(null, 'mm_status');
		if (!is_object($mm_status)) {
			$mm_status = new magicmirror2Cmd();
			$mm_status->setLogicalId('mm_status');
			$mm_status->setIsVisible(1);
			$mm_status->setName(__('Statut', __FILE__));
			$mm_status->setConfiguration('description',__('Disponibilité de l\'équipement. (vérification via une requete HTTP)', __FILE__));
			$mm_status->setType('info');
			$mm_status->setSubType('binary');
			$mm_status->setEqLogic_id($this->getId());
			$mm_status->setOrder(5);
			$mm_status->save();
		}

		// CMD Monitor Check Status
   		$mm_monitorstatus = $this->getCmd(null, 'mm_monitorstatus');
		if (!is_object($mm_monitorstatus)) {
			$mm_monitorstatus = new magicmirror2Cmd();
			$mm_monitorstatus->setLogicalId('mm_monitorstatus');
			$mm_monitorstatus->setIsVisible(1);
			$mm_monitorstatus->setName(__('Affichage', __FILE__));
			$mm_monitorstatus->setConfiguration('description',__('Statut de l\'affichage.', __FILE__));
			$mm_monitorstatus->setType('info');
			$mm_monitorstatus->setSubType('string');
			$mm_monitorstatus->setEqLogic_id($this->getId());
			$mm_monitorstatus->setOrder(6);
			$mm_monitorstatus->save();
		}

		// CMD Monitor Check Status
   		$mm_monitorgetbrightness = $this->getCmd(null, 'mm_monitorgetbrightness');
		if (!is_object($mm_monitorgetbrightness)) {
			$mm_monitorgetbrightness = new magicmirror2Cmd();
			$mm_monitorgetbrightness->setLogicalId('mm_monitorgetbrightness');
			$mm_monitorgetbrightness->setIsVisible(1);
			$mm_monitorgetbrightness->setName(__('Valeur Luminosité', __FILE__));
			$mm_monitorgetbrightness->setConfiguration('description',__('Valeur de la luminosité.', __FILE__));
			$mm_monitorgetbrightness->setType('info');
			$mm_monitorgetbrightness->setSubType('numeric');
			$mm_monitorgetbrightness->setEqLogic_id($this->getId());
			$mm_monitorgetbrightness->setOrder(6);
			$mm_monitorgetbrightness->save();
 		}
			
		// CMD Monitor Change Status
   		$mm_monitortoggle = $this->getCmd(null, 'mm_monitortoggle');
		if (!is_object($mm_monitortoggle)) {
			$mm_monitortoggle = new magicmirror2Cmd();
			$mm_monitortoggle->setLogicalId('mm_monitortoggle');
			$mm_monitortoggle->setIsVisible(1);
			$mm_monitortoggle->setName(__('Masquer l\'affichage', __FILE__));
			$mm_monitortoggle->setConfiguration('description',__('Activer ou Désactiver l\'affichage.', __FILE__));
			$mm_monitortoggle->setType('action');
			$mm_monitortoggle->setSubType('other');
			$mm_monitortoggle->setEqLogic_id($this->getId());
			$mm_monitortoggle->setOrder(7);
			$mm_monitortoggle->save();
		}
		
		// CMD Send Notification
   		$mm_sendnotification = $this->getCmd(null, 'mm_sendnotification');
		if (!is_object($mm_sendnotification)) {
			$mm_sendnotification = new magicmirror2Cmd();
			$mm_sendnotification->setLogicalId('mm_sendnotification');
			$mm_sendnotification->setIsVisible(1);
			$mm_sendnotification->setName(__('Notification', __FILE__));
			$mm_sendnotification->setConfiguration('description',__('Envoyer une notification (ALERT ou NOTIFICATION), voir configuration de l\'équipement.', __FILE__));
			$mm_sendnotification->setType('action');
			$mm_sendnotification->setSubType('message');
			$mm_sendnotification->setEqLogic_id($this->getId());
			$mm_sendnotification->setOrder(8);
			$mm_sendnotification->save();
		}

		// ***********************************
		// MagicMirror2 - Module Support

		// CMD Background Check Status
   		$mm_backgroundstatus = $this->getCmd(null, 'mm_backgroundstatus');
		if (!is_object($mm_backgroundstatus) && $this->getConfiguration('plugin-BackgroundSlideshow-enable')) {
			$mm_backgroundstatus = new magicmirror2Cmd();
			$mm_backgroundstatus->setLogicalId('mm_backgroundstatus');
			$mm_backgroundstatus->setIsVisible(1);
			$mm_backgroundstatus->setName(__('Fond d\'écran', __FILE__));
			$mm_backgroundstatus->setConfiguration('description',__('Statut du fond d\'écran', __FILE__));
			$mm_backgroundstatus->setType('info');
			$mm_backgroundstatus->setSubType('string');
			$mm_backgroundstatus->setEqLogic_id($this->getId());
			$mm_backgroundstatus->setOrder(9);
			$mm_backgroundstatus->save();
		}elseif(is_object($mm_backgroundstatus) && !$this->getConfiguration('plugin-BackgroundSlideshow-enable')){
			$mm_backgroundstatus->remove();
		}
		
		// CMD Background Change Visibility
   		$mm_backgroundtoggle = $this->getCmd(null, 'mm_backgroundtoggle');
		if (!is_object($mm_backgroundtoggle) && $this->getConfiguration('plugin-BackgroundSlideshow-enable')) {
			$mm_backgroundtoggle = new magicmirror2Cmd();
			$mm_backgroundtoggle->setLogicalId('mm_backgroundtoggle');
			$mm_backgroundtoggle->setIsVisible(1);
			$mm_backgroundtoggle->setName(__('Masquer fond d\'écran', __FILE__));
			$mm_backgroundtoggle->setConfiguration('description',__('Afficher ou Masquer le fond d\'écran', __FILE__));
			$mm_backgroundtoggle->setType('action');
			$mm_backgroundtoggle->setSubType('other');
			$mm_backgroundtoggle->setEqLogic_id($this->getId());
			$mm_backgroundtoggle->setOrder(10);
			$mm_backgroundtoggle->save();
		}elseif(is_object($mm_backgroundstatus) && !$this->getConfiguration('plugin-BackgroundSlideshow-enable')){
			$mm_backgroundtoggle->remove();
		}
	
		if($this->getIsEnable()){
			log::add('magicmirror2','debug',$tmpLogPrefix.'::refresh now !');
			$cmd = $this->getCmd(null, 'mm_refresh');
			if (is_object($cmd)) {
				$cmd->execCmd();
			}
		}else{
			log::add('magicmirror2','debug',$tmpLogPrefix.'::non activé !');
		}
    }

    public function preRemove() {}

    public function postRemove() {}
	
	public function getMM_Status() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		$changed = false;
		$tmpStatus = 0;
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		$headers = array('Content-type: application/json');
		$ch = curl_init ("http://".$myhost.":".$myport."/");
		curl_setopt ($ch, CURLOPT_POST, false); 
		curl_setopt ($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
			log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		$curl_info = curl_getinfo($ch);
		log::add('magicmirror2','debug',$tmpLogPrefix.'::http code->'.$curl_info['http_code']);
		curl_close($ch);
		if($curl_info['http_code'] == 200){
			$tmpStatus = 1;
		}else{
			$tmpStatus = 0;
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::tmpStatus='.$tmpStatus);
		$changed = $this->checkAndUpdateCmd('mm_status', $tmpStatus) || $changed;
		log::add('magicmirror2','debug',$tmpLogPrefix.'::changed? #'.$changed.'#');
		
		return $changed;
	}

	public function doMM_PowerOff() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		
		$changed = false;
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		$headers = array('Content-type: application/json');
		$ch = curl_init ("http://".$myhost.":".$myport."/remote?action=SHUTDOWN");
		curl_setopt ($ch, CURLOPT_POST, false); 
		curl_setopt ($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
			log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		curl_close($ch);
		$json = json_decode($response, true);
		$tmpMonitorStatus = 0;
		log::add('magicmirror2','debug',$tmpLogPrefix.'::json result->'.(print_r($response,true)));

		$this->refreshWidget();
	}

	public function getMM_MonitorStatus() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		
		// if status = 0, exit now and don't make curl query
		$temps = $this->getCmd(null,'mm_status');
		if(!$temps->execCmd()){
			log::add('magicmirror2','debug',$tmpLogPrefix.'::exit now!, status='.$temps->execCmd().', don\'t make curl request');
			return false;
		}
		
		$changed = false;
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		$headers = array('Content-type: application/json');
		$ch = curl_init ("http://".$myhost.":".$myport."/remote?action=MONITORSTATUS");
		curl_setopt ($ch, CURLOPT_POST, false); 
		curl_setopt ($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
			log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		curl_close($ch);
		$json = json_decode($response, true);
		$tmpMonitorStatus = 0;
		log::add('magicmirror2','debug',$tmpLogPrefix.'::json result->'.(print_r($response,true)));
		if($json['success']){
			switch($json["monitor"]){
				case "on":
					$tmpMonitorStatus = 1;
					break;
				case "off":
					$tmpMonitorStatus = 0;
					break;
			}
		}else{
			$tmpMonitorStatus = 2;
			log::add('magicmirror2','error',($this->getName()).'::Erreur lors de la récupération du statut de l\'affichage ! ');
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::tmpMonitorStatus='.$tmpMonitorStatus);
		$changed = $this->checkAndUpdateCmd('mm_monitorstatus', $tmpMonitorStatus) || $changed;
		log::add('magicmirror2','debug',$tmpLogPrefix.'::changed? #'.$changed.'#');
		return $changed;
	}
	
	public function getMM_Module_BackgroundStatus() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		
		// if status = 0, exit now and don't make curl query
		$temps = $this->getCmd(null,'mm_status');
		if(!$temps->execCmd()){
			log::add('magicmirror2','debug',$tmpLogPrefix.'::exit now!, status='.$temps->execCmd().', don\'t make curl request');
			$changed = $this->checkAndUpdateCmd('mm_backgroundstatus', 2);
			return $changed;
		}
		
		
		$changed = false;
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		$headers = array('Content-type: application/json');
		$ch = curl_init ("http://".$myhost.":".$myport."/remote?action=MODULE_DATA");
		curl_setopt ($ch, CURLOPT_POST, false);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 2);		
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
			log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		curl_close($ch);
		$json = json_decode($response, true);
		$tmpBackgroundStatus = 0;
		$moduleFound = false;
		log::add('magicmirror2','debug',$tmpLogPrefix.'JSON RESULT->'.(print_r($response,true)));
		foreach($json["moduleData"] as $module){
			log::add('magicmirror2','debug',$tmpLogPrefix.'::moduleData: '.(print_r($module["name"],true)));
			if($module["name"] == "MMM-BackgroundSlideshow"){
				$moduleFound = true;
				log::add('magicmirror2','debug',$tmpLogPrefix.'::MMM-BackgroundSlideshow FOUND ! ! !');
				if($module["hidden"] == true){
					$tmpBackgroundStatus = 0;
				}else{
					$tmpBackgroundStatus = 1;
					}
				log::add('magicmirror2','debug',$tmpLogPrefix.'::hidden ?:'.(print_r($module["hidden"],true))."-".$tmpBackgroundStatus);
			}
		}
		if(!$moduleFound) {
			$tmpBackgroundStatus = 2;
			log::add('magicmirror2','error', ($this->getName()).'::Erreur lors de la récupération des informations du background.');
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::tmpBackgroundStatus='.$tmpBackgroundStatus);
		$changed = $this->checkAndUpdateCmd('mm_backgroundstatus', $tmpBackgroundStatus);
		log::add('magicmirror2','debug',$tmpLogPrefix.'::changed? #'.$changed.'#');
		return $changed;
	}

	public function getMM_MonitorBrightness() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		
		$changed = false;
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		$headers = array('Content-type: application/json');
		$ch = curl_init ("http://".$myhost.":".$myport."/api/brightness");
		curl_setopt ($ch, CURLOPT_POST, false); 
		curl_setopt ($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
			log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		curl_close($ch);
		$json = json_decode($response, true);
		$tmpMonitorBrightness = 0;
		log::add('magicmirror2','debug',$tmpLogPrefix.'::json result->'.(print_r($response,true)));
		if($json['success']){
			$tmpMonitorBrightness = $json["result"];
		}else{
			log::add('magicmirror2','error',($this->getName()).'::Erreur lors de la récupération du statut de la luminosité ! ');
			return false;
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::tmpMonitorBrightness='.$tmpMonitorBrightness);
		$changed = $this->checkAndUpdateCmd('mm_monitorgetbrightness', $tmpMonitorBrightness) || $changed;
		//log::add('magicmirror2','debug',$tmpLogPrefix.'::changed? #'.$changed.'#');
		return true;
	}
	public function doMM_Restart() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		$changed = false;
		$tmpCmdResult = false;
		$headers = array('Content-type: application/json');
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		$ch = curl_init ("http://".$myhost.":".$myport."/remote?action=REBOOT");
		curl_setopt ($ch, CURLOPT_POST, false); 
		curl_setopt ($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
			log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		curl_close($ch);
		log::add('magicmirror2','debug',$tmpLogPrefix.'::curl result::'.(print_r($response,true)));
		$json = json_decode($response, true);
		if($json["success"] == true){
			switch($json["success"]){
				case false:
					$tmpCmdResult = 1;
					break;
				case true:
					$tmpCmdResult = 0;
					break;
			}
		}else{
				log::add('magicmirror2','error',($this->getName()).'::Erreur lors de l\'execution de la commande !');
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::cmd result->'.$tmpCmdResult);
		$changed = $this->checkAndUpdateCmd('mm_status', $tmpCmdResullt) || $changed;
		log::add('magicmirror2','debug',$tmpLogPrefix.'::changed? #'.$changed.'#');
		return $changed;
	}

	public function doMM_Reload() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		$changed = false;
		$tmpCmdResult = false;
		$headers = array('Content-type: application/json');
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		$ch = curl_init ("http://".$myhost.":".$myport."/remote?action=RESTART");
		curl_setopt ($ch, CURLOPT_POST, false);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
			log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		curl_close($ch);
		log::add('magicmirror2','debug',$tmpLogPrefix.'::curl result::'.(print_r($response,true)));
		$json = json_decode($response, true);
		if($json["success"] == true){
			switch($json["success"]){
				case false:
					$tmpCmdResult = 1;
					break;
				case true:
					$tmpCmdResult = 0;
					break;
			}
		}else{
				log::add('magicmirror2','error',($this->getName()).'::Erreur lors de l\'execution de la commande !');
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::tmpCmdResult->'.$tmpCmdResult);
		$changed = $this->checkAndUpdateCmd('mm_reload', $tmpCmdResult) || $changed;
		log::add('magicmirror2','debug',$tmpLogPrefix.'::changed? #'.$changed.'#');
		return $changed;
	}
	
	public function doMM_RefreshHtml() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		$changed = false;
		$tmpCmdResult = false;
		$headers = array('Content-type: application/json');
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		$ch = curl_init ("http://".$myhost.":".$myport."/remote?action=REFRESH");
		curl_setopt ($ch, CURLOPT_POST, false);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
			log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		curl_close($ch);
		log::add('magicmirror2','debug',$tmpLogPrefix.'::curl result::'.(print_r($response,true)));
		$json = json_decode($response, true);
		if($json["success"] == true){
			switch($json["success"]){
				case false:
					$tmpCmdResult = 1;
					break;
				case true:
					$tmpCmdResult = 0;
					break;
			}
		}else{
				log::add('magicmirror2','error',($this->getName()).'::Erreur lors de l\'execution de la commande !');
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::tmpCmdResult->'.$tmpCmdResult);
		return $changed;
	}

	public function doMM_MonitorOn() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		$changed = false;
		$tmpCmdResult = false;
		$headers = array('Content-type: application/json');
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		$ch = curl_init ("http://".$myhost.":".$myport."/api/monitor/on");
		curl_setopt ($ch, CURLOPT_POST, false);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
			log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		curl_close($ch);
		log::add('magicmirror2','debug',$tmpLogPrefix.'::curl result::'.(print_r($response,true)));
		$json = json_decode($response, true);
		if($json["success"] == true){
			switch($json["success"]){
				case false:
					$tmpCmdResult = 0;
					break;
				case true:
					$changed = 1;
					$tmpCmdResult = 1;
					break;
			}
		}else{
				log::add('magicmirror2','error',($this->getName()).'::Erreur lors de l\'execution de la commande !');
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::tmpCmdResult->'.$tmpCmdResult);
		return $changed;
	}
	
	public function doMM_MonitorOff() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		$changed = false;
		$tmpCmdResult = false;
		$headers = array('Content-type: application/json');
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		$ch = curl_init ("http://".$myhost.":".$myport."/api/monitor/off");
		curl_setopt ($ch, CURLOPT_POST, false);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
			log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		curl_close($ch);
		log::add('magicmirror2','debug',$tmpLogPrefix.'::curl result::'.(print_r($response,true)));
		$json = json_decode($response, true);
		if($json["success"] == true){
			switch($json["success"]){
				case false:
					$tmpCmdResult = 0;
					break;
				case true:
					$tmpCmdResult = 1;
					$changed = 1;
					break;
			}
		}else{
				log::add('magicmirror2','error',($this->getName()).'::Erreur lors de l\'execution de la commande !');
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::tmpCmdResult->'.$tmpCmdResult);
		return $changed;
	}

	public function doMM_MonitorSetBrightness($value) {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		//$value="45;
		$changed = false;
		$tmpCmdResult = false;
		$headers = array('Content-type: application/json');
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		//$value=10;
		$ch = curl_init ("http://".$myhost.":".$myport."/api/brightness/$value");
		curl_setopt ($ch, CURLOPT_POST, false);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
			log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		curl_close($ch);
		log::add('magicmirror2','debug',$tmpLogPrefix.'::curl result::'.(print_r($response,true)));
		$json = json_decode($response, true);
		if($json["success"] == true){
			switch($json["success"]){
				case false:
					$tmpCmdResult = 0;
					break;
				case true:
					$tmpCmdResult = 1;
					$changed = 1;
					break;
			}
		}else{
				log::add('magicmirror2','error',($this->getName()).'::Erreur lors de l\'execution de la commande !');
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::tmpCmdResult->'.$tmpCmdResult);
		$this->getMM_MonitorBrightness(); //force get value
		return $value;
	}
		
	public function doMM_MonitorToggle() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		$changed = false;
		$tmpCmdResult = false;
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		$headers = array('Content-type: application/json');
		$ch = curl_init ("http://".$myhost.":".$myport."/remote?action=MONITORTOGGLE");
		curl_setopt ($ch, CURLOPT_POST, false);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
			log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		curl_close($ch);
		log::add('magicmirror2','debug',$tmpLogPrefix.'::curl result::'.(print_r($response,true)));
		$json = json_decode($response, true);
		if($json["success"] == true){
			switch($json["monitor"]){
				case "on":
					$tmpCmdResult = 1;
					break;
				case "off":
					$tmpCmdResult = 0;
					break;
			}
		}else{
			$tmpCmdResult = 0;
			log::add('magicmirror2','error',($this->getName()).'::Erreur lors de l\'execution de la commande !');
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::tmpCmdResult=.'.$tmpCmdResult);
		$changed = $this->checkAndUpdateCmd('mm_monitorstatus', $tmpCmdResult) || $changed;
		log::add('magicmirror2','debug',$tmpLogPrefix.'::changed? #'.$changed.'#');
		return $changed;
	}

	public function doMM_Module_BackgroundToggle() {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		$changed = false;
		$tmpCmdResult = 0;
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		$headers = array('Content-type: application/json');
		$ch = curl_init ("http://".$myhost.":".$myport."/remote?action=TOGGLE&module=MMM-BackgroundSlideshow");
		curl_setopt ($ch, CURLOPT_POST, false);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
					log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		curl_close($ch);
		log::add('magicmirror2','debug',$tmpLogPrefix.'::curl result::'.(print_r($response,true)));
		$json = json_decode($response, true);
		switch($json["success"]){
			case true:
				$tmpCmdResult = 1;
				$changed = $this->getMM_Module_BackgroundStatus() || $changed;
				break;
			case false:
				$tmpCmdResult = 0;
				break;
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::tmpCmdResult='.$tmpCmdResult);
		log::add('magicmirror2','debug',$tmpLogPrefix.'::changed? #'.$changed.'#');
		return $changed;
	}

	// SEND NOTIFICATION (alert or notif)
	public function doMM_Notification($_options = array()) {
		$tmpLogPrefix = ($this->getName()).'::function::'.__FUNCTION__;
		log::add('magicmirror2','debug',$tmpLogPrefix);
		foreach($_options as $opt){
			log::add('magicmirror2','debug',$tmpLogPrefix.'::option::**'.$title.'**'.$message.'**'.$opt);
		}
		$changed = false;
		$tmpCmdResult = false;
		$myhost = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		
		$notifParams = '{"title":"'.$_options["title"].'","message":"'.$_options["message"].'","timer": '.($this->getConfiguration('cjmm_notification_timer')).', "type": "'.($this->getConfiguration('cjmm_notification_type')).'"}';
		$headers = array('Content-type: application/json');
		$ch = curl_init ("http://".$myhost.":".$myport."/api/module/alert/showalert");
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 2);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $notifParams);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if(curl_errno($ch)){
			//throw new Exception(curl_error($ch));
			log::add('magicmirror2','debug',$tmpLogPrefix.'::Host Unreachable, cURL ERROR('.curl_errno($ch).')');
		}
		curl_close($ch);
		log::add('magicmirror2','debug',$tmpLogPrefix.'::curl result->'.(print_r($response,true)));
		$json = json_decode($response, true);
		switch($json["success"]){
			case true:
				$tmpCmdResult = 1;
				break;
			case false:
				$tmpCmdResult = 0;
				log::add('magicmirror2','error',($this->getName()).'::Erreur lors de l\'execution de la commande !');
				break;
		}
		log::add('magicmirror2','debug',$tmpLogPrefix.'::tmpCmdResult='.$tmpCmdResult);
		$changed = $this->checkAndUpdateCmd('mm_monitorstatus', $tmpCmdResult) || $changed;
		log::add('magicmirror2','debug',$tmpLogPrefix.'::changed? #'.$changed.'#');
		return $changed;
	}
	
	// WIDGET RENDER
	public function toHtml($_version = 'dashboard')	{
		log::add('magicmirror2','debug', 'toHTML - Widget Rendering');
		$replace = $this->preToHtml($_version);
		if (!is_array($replace)) {
			return $replace;
		}

		$uid = $this->getId() . self::UIDDELIMITER . mt_rand() . self::UIDDELIMITER;
		$replace['#uid#'] = $uid;
		
		$replace['#magic_url#'] = $this->getConfiguration('magicmirror_ip');
		$myport = $this->getConfiguration('cjmm_customport');
		if($myport == ""){ $myport = "8080"; }
		$replace['#magic_customport#'] = $myport;


		$temps = $this->getCmd(null,'mm_remotePage');
		$replace ['#wdgtCmd_id_remotePage#'] = $temps->getId();
		$replace ['#wdgtCmd_isVisible_remotePage#'] = $temps->getIsVisible();
		$replace ['#wdgtCmd_name_remotePage#'] = __($temps->getName(), __FILE__);

		$temps = $this->getCmd(null,'mm_poweroff');
		$replace ['#wdgtCmd_id_poweroff#'] = $temps->getId();
		$replace ['#wdgtCmd_isVisible_poweroff#'] = $temps->getIsVisible();
		$replace ['#wdgtCmd_name_poweroff#'] = __($temps->getName(), __FILE__);
		log::add('magicmirror2','debug', __($temps->getName(), __FILE__));	
		
		$temps = $this->getCmd(null,'mm_status');
		$replace ['#wdgtCmd_id_status#'] = $temps->getId();
		$replace ['#wdgtCmd_value_status#'] = $temps->execCmd();
		$replace ['#wdgtCmd_isVisible_status#'] = $temps->getIsVisible();
		$replace ['#wdgtCmd_name_status#'] = __($temps->getName(), __FILE__);
		log::add('magicmirror2','debug', __($temps->getName(), __FILE__));
		
		$temps = $this->getCmd(null,'mm_monitorstatus');
		$replace ['#wdgtCmd_value_monitorstatus#'] = $temps->execCmd();
		$replace ['#wdgtCmd_isVisible_monitorstatus#'] = $temps->getIsVisible();
		$replace ['#wdgtCmd_name_monitorstatus#'] = __($temps->getName(), __FILE__);
		log::add('magicmirror2','debug', __($temps->getName(), __FILE__));

		$temps = $this->getCmd(null,'mm_monitortoggle');
		$replace ['#wdgtCmd_id_monitorstatus#'] = $temps->getId();
		$replace ['#wdgtCmd_isVisible_monitortoggle#'] = $temps->getIsVisible();

		if($this->getConfiguration('plugin-BackgroundSlideshow-enable')){
			$temps = $this->getCmd(null,'mm_backgroundstatus');
			$replace ['#wdgtCmd_value_backgroundstatus#'] = $temps->execCmd();
			$replace ['#wdgtCmd_isVisible_backgroundstatus#'] = $temps->getIsVisible();
			$replace ['#wdgtCmd_name_backgroundstatus#'] = __($temps->getName(), __FILE__);
			$temps = $this->getCmd(null,'mm_backgroundtoggle');
			$replace ['#wdgtCmd_id_backgroundstatus#'] = $temps->getId();
			$replace ['#wdgtCmd_isVisible_backgroundtoggle#'] = $temps->getIsVisible();
		}else{
			$replace ['#wdgtCmd_value_backgroundstatus#'] = 2;
			$replace ['#wdgtCmd_isVisible_backgroundstatus#'] = 0;
			$replace ['#wdgtCmd_id_backgroundstatus#'] = 0;
			$replace ['#wdgtCmd_isVisible_backgroundtoggle#'] = 0;
		}

		$temps = $this->getCmd(null,'mm_refresh');
		$replace ['#refresh_cmd_id#'] = $temps->getId();
		$replace ['#refresh_cmd_isVisible#'] = $temps->getIsVisible();
		$replace ['#refresh_cmd_name#'] = __($temps->getName(), __FILE__);
		
		$temps = $this->getCmd(null,'mm_restart');
		$replace ['#wdgtCmd_id_restart#'] = $temps->getId();
		$replace ['#wdgtCmd_isVisible_restart#'] = $temps->getIsVisible();
		$replace ['#wdgtCmd_name_restart#'] = __($temps->getName(), __FILE__);
		
		$temps = $this->getCmd(null,'mm_reload');
		$replace ['#wdgtCmd_id_reload#'] = $temps->getId();
		$replace ['#wdgtCmd_isVisible_reload#'] = $temps->getIsVisible();
		$replace ['#wdgtCmd_name_reload#'] = __($temps->getName(), __FILE__);
		
		$temps = $this->getCmd(null,'mm_refreshHtml');
		$replace ['#wdgtCmd_id_refreshHtml#'] = $temps->getId();
		$replace ['#wdgtCmd_isVisible_refreshHtml#'] = $temps->getIsVisible();
		$replace ['#wdgtCmd_name_refreshHtml#'] = __($temps->getName(), __FILE__);
		
		$temps = $this->getCmd(null,'mm_sendnotification');
		$replace ['#wdgtCmd_id_notification#'] = $temps->getId();
		$replace ['#wdgtCmd_isVisible_notification#'] = $temps->getIsVisible();
		$replace ['#wdgtCmd_name_notification#'] = __($temps->getName(), __FILE__);
		
		$temps = $this->getCmd(null,'mm_monitorBrightness');
		$replace ['#wgtCmd_id_brightness#'] = $temps->getId();
		$replace ['#wdgtCmd_isVisible_brightness#'] = $temps->getIsVisible();
		$replace ['#wdgtCmd_name_brightness#'] = __($temps->getName(), __FILE__);
		
		$temps = $this->getCmd(null,'mm_monitorGetBrightness');
		$replace ['#wdgtCmd_brightness_value#'] = $temps->execCmd();
		//$replace ['#wdgtCmd_isVisible_brightness#'] = $temps->getIsVisible();
		//$replace ['#wdgtCmd_name_brightness#'] = __($temps->getName(), __FILE__);
		
		//mm_monitorGetBrightness
		$html = template_replace($replace, getTemplate('core', $_version, 'magicmirror2','magicmirror2'));
		cache::set('MagicMirrorWidget' . $_version . $this->getId(), $html, 0);
		return $html;
	}

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class magicmirror2Cmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
		$eqLogic = $this->getEqLogic();
		if (!is_object($eqLogic) || $eqLogic->getIsEnable() != 1) {
			throw new Exception(__("Equipement désactivé impossible d'exécuter la commande : " . $this->getHumanName(), __FILE__));
		}
		log::add('magicmirror2','debug','execute: '.($this->getLogicalId()).'-'.(print_r($_options,true)));
		$changed = 0;
        switch ($this->getLogicalId()) {
			case "mm_poweroff":
				$changed = $eqLogic->doMM_PowerOff() || $changed;
				break;													  
			case "mm_status":
				break;
			case "mm_refresh":
				$eqLogic->getMM_MonitorBrightness();
				$changed = $eqLogic->getMM_Status() || $changed;
				if(is_object($eqLogic->getCmd(null, 'mm_monitorstatus')) && (($eqLogic->getCmd(null, 'mm_monitorstatus'))->getIsVisible())){
					$changed = $eqLogic->getMM_MonitorStatus() || $changed;
				}
				log::add('magicmirror2','debug','execute: '.($eqLogic->getConfiguration('plugin-BackgroundSlideshow-enable')));
				if(is_object($eqLogic->getCmd(null, 'mm_backgroundstatus')) && (($eqLogic->getCmd(null, 'mm_backgroundstatus'))->getIsVisible()) && $eqLogic->getConfiguration('plugin-BackgroundSlideshow-enable')){
					$changed = $eqLogic->getMM_Module_BackgroundStatus() || $changed;
					log::add('magicmirror2','debug','execute: active!');
				}
				break;
			case "mm_restart":
				$changed = $eqLogic->doMM_Restart() ||$changed;
				break;
			case "mm_reload":
				$changed = $eqLogic->doMM_Reload() ||$changed;
				break;
			case "mm_monitorOn":
				$changed = $eqLogic->doMM_MonitorOn() ||$changed;
				break;
			case "mm_monitorOff":
				$changed = $eqLogic->doMM_MonitorOff() ||$changed;
				break;
			case "mm_monitorBrightness":
				$value = $_options['slider'];
				log::add('magicmirror2','debug',$value);
				$changed = $eqLogic->doMM_MonitorSetBrightness($value) ||$changed;
				break;
			case "mm_monitorGetBrightness":
				$changed = $eqLogic->getMM_MonitorBrightness() ||$changed;
			case "mm_refreshHtml":
				$changed = $eqLogic->doMM_RefreshHtml() ||$changed;
				break;
			case "mm_monitorstatus":
				break;
			case "mm_backgroundstatus":
				break;
			case "mm_monitortoggle":
				$changed = $eqLogic->doMM_MonitorToggle() || $changed;
				break;
			case "mm_backgroundtoggle":
				$changed = $eqLogic->doMM_Module_BackgroundToggle() || $changed;
				break;
			case "mm_sendnotification":
				$changed = $eqLogic->doMM_Notification($_options) || $changed;
				break;
		}
		log::add('magicmirror2','debug','execute::changed?::'.$changed);
		if(true){
			$eqLogic->refreshWidget();
		}
    }

    /*     * **********************Getteur Setteur*************************** */
}


