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
 * Copyright (c) 2020  (original work) Open Assessment Technologies SA;
 *
 * @author Yuri Filippovich
 */

namespace oat\taoSyncServer\scripts\install;

use oat\oatbox\event\EventManager;
use oat\oatbox\extension\InstallAction;
use oat\taoDeliveryRdf\model\event\DeliveryRemovedEvent;
use oat\taoDeliveryRdf\model\event\DeliveryUpdatedEvent;
use common_Exception;
use oat\taoSyncServer\listener\DeliveryListener;

/**
 * php index.php 'oat\taoSyncServer\scripts\install\RegisterDeliveryEvents'
 */
class RegisterDeliveryEvents extends InstallAction
{
    /**
     * @param $params
     * @throws common_Exception
     */
    public function __invoke($params)
    {
        $eventManager = $this->getServiceManager()->get(EventManager::SERVICE_ID);
        $eventManager->attach(DeliveryRemovedEvent::class, [DeliveryListener::SERVICE_ID, 'deleteDeliveryAssemblyFile']);
        $eventManager->attach(DeliveryUpdatedEvent::class, [DeliveryListener::SERVICE_ID, 'deleteDeliveryAssemblyFile']);
        $eventManager->registerService(DeliveryListener::SERVICE_ID, new DeliveryListener([]));
        $this->getServiceManager()->register(EventManager::SERVICE_ID, $eventManager);
    }
}
