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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
    
    ajax::init();
	$tmpLogPrefix = 'ajax::action->'.init('action');
	
	if (init('action') == 'checkAPI') {
		$myhost = $_POST['ip_addr'];
		log::add('magicmirror2','debug',$tmpLogPrefix.'::host ip -> '.$myhost.'');
		$headers = array('Content-type: application/json');
		$ch = curl_init ("http://".$myhost.":8080/api/test");
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
		log::add('magicmirror2','debug',$tmpLogPrefix.'::curl result->'.(print_r($response,true)));
		$json = json_decode($response, true);
		switch($json["success"]){
			case true:
				$tmpCmdResult = 1;
				ajax::success($tmpCmdResult);
				break;
			case false:
				$tmpCmdResult = 0;
				ajax::error($tmpCmdResult);
				break;
		}
	}

    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}

