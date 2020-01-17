<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoSyncServer\scripts\update;


use common_Exception;
use common_ext_ExtensionUpdater;
use oat\oatbox\event\EventManager;
use oat\taoDeliveryRdf\model\event\DeliveryRemovedEvent;
use oat\taoDeliveryRdf\model\event\DeliveryUpdatedEvent;
use oat\taoSyncServer\listener\DeliveryListener;

class Updater extends common_ext_ExtensionUpdater
{
    /**
     * @param $initialVersion
     * @return string|void
     * @throws common_Exception
     */
    public function update($initialVersion)
    {
        $this->skip('0.1.0', '0.2.0');

        if ($this->isVersion('0.2.0')) {
            $serviceManager = $this->getServiceManager();
            $serviceManager->register(DeliveryListener::SERVICE_ID, new DeliveryListener());

            $eventManager = $serviceManager->get(EventManager::SERVICE_ID);
            $eventManager->attach(DeliveryRemovedEvent::class, [DeliveryListener::SERVICE_ID, 'deleteDeliveryAssemblyFile']);
            $eventManager->attach(DeliveryUpdatedEvent::class, [DeliveryListener::SERVICE_ID, 'deleteDeliveryAssemblyFile']);
            $serviceManager->register(EventManager::SERVICE_ID, $eventManager);
            $this->setVersion('0.3.0');
        }

        $this->skip('0.3.0', '0.3.1');
    }
}
