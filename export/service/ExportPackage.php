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
 * Copyright (c) 2019  (original work) Open Assessment Technologies SA;
 *
 * @author Yuri Filippovich
 */

namespace oat\taoSyncServer\export\service;

use oat\oatbox\service\ConfigurableService;
use oat\taoSync\model\dataProvider\SyncDataProviderCollection;
use oat\taoSync\model\Exception\SyncBaseException;
use oat\taoSync\package\SyncPackageService;
use oat\taoSyncServer\export\dataProvider\dataReader\Delivery;
use oat\taoSyncServer\export\dataProvider\TestCenter;

class ExportPackage extends ConfigurableService
{
    const FILE_PREFIX = 'syncPackageServer';

    /**
     * @param string $orgId
     * @return string
     * @throws SyncBaseException
     */
    public function createPackage($orgId)
    {
        $data = $this->getDataProviderCollection()->getData([TestCenter::PARAM_ORG_ID => $orgId]);

        $fileName = self::FILE_PREFIX . '_' . time() . '.json';

        if ($deliveryUris = $this->getDeliveryUris($data)) {
            $this->getExportDeliveryAssemblyService()->createCompiledDeliveryPackage($deliveryUris, $orgId);
        }

        return $this->getPackageService()->createPackage($data, $fileName, $orgId);
    }

    /**
     * @param array $data
     * @return array
     */
    private function getDeliveryUris(array $data)
    {
        $deliveryUris = [];

        if (array_key_exists(Delivery::TYPE, $data)) {
            foreach ($data[Delivery::TYPE]['resources'] as $delivery) {
                $deliveryUris[] = $delivery['uri'];
            }
        }
        return $deliveryUris;
    }

    /**
     * @return SyncDataProviderCollection
     */
    private function getDataProviderCollection()
    {
        return $this->getServiceLocator()->get(SyncDataProviderCollection::SERVICE_ID);
    }

    /**
     * @return SyncPackageService
     */
    private function getPackageService()
    {
        return $this->getServiceLocator()->get(SyncPackageService::SERVICE_ID);
    }

    /**
     * @return ExportDeliveryAssembly
     */
    private function getExportDeliveryAssemblyService()
    {
        return $this->getServiceLocator()->get(ExportDeliveryAssembly::SERVICE_ID);
    }
}
