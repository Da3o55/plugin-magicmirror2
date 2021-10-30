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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function magicmirror2_install() {
    
}

function magicmirror2_update() {
    foreach (eqLogic::byType('magicmirror2') as $eqLogic) {
		// CMD Monitor Check Status
	   	$mm_monitorgetbrightness = $eqLogic->getCmd(null, 'mm_monitorgetbrightness');
		if (!is_object($mm_monitorgetbrightness)) {
			$mm_monitorgetbrightness = new magicmirror2Cmd();
			$mm_monitorgetbrightness->setLogicalId('mm_monitorgetbrightness');
			$mm_monitorgetbrightness->setIsVisible(1);
			$mm_monitorgetbrightness->setName(__('Valeur Luminosité', __FILE__));
			$mm_monitorgetbrightness->setConfiguration('description',__('Valeur de la luminosité.', __FILE__));
			$mm_monitorgetbrightness->setType('info');
			$mm_monitorgetbrightness->setSubType('numeric');
			$mm_monitorgetbrightness->setEqLogic_id($eqLogic->getId());
			$mm_monitorgetbrightness->setOrder(6);
			$mm_monitorgetbrightness->save();
 		}
		// CMD Set Brightness
		$mm_monitorBrightness = $eqLogic->getCmd(null, 'mm_monitorBrightness');
		if (!is_object($mm_monitorBrightness)) {
			$mm_monitorBrightness = new magicmirror2Cmd();
			$mm_monitorBrightness->setLogicalId('mm_monitorBrightness');
			$mm_monitorBrightness->setIsVisible(1);
			$mm_monitorBrightness->setName(__('Luminosité', __FILE__));
			$mm_monitorBrightness->setConfiguration('description',__('Luminosité de 1 à 100.', __FILE__));
			$mm_monitorBrightness->setType('action');
			$mm_monitorBrightness->setSubType('slider');
			$mm_monitorBrightness->setEqLogic_id($eqLogic->getId());
			$mm_monitorBrightness->setOrder(3);
			$mm_monitorBrightness->save();
		}
	}
}


function magicmirror2_remove() {
    
}

?>
