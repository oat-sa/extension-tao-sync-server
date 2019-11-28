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
 * @author Oleksandr Zagovorychev <zagovorichev@1pt.com>
 */

namespace oat\taoSyncServer\export\service;

use oat\oatbox\service\ConfigurableService;
use oat\taoSync\model\Exception\SyncBaseException;
use oat\taoSyncServer\export\dataProvider\TestCenter;
use oat\taoSync\package\PackageService;
use oat\taoSync\model\dataProvider\DataProviderCollection;

class Export extends ConfigurableService
{
    const SERVICE_ID = 'taoSyncServer/Export';

    const FILE_PREFIX = 'syncPackageServer';

    /**
     * @param int $syncId
     * @param string $orgId
     * @throws SyncBaseException
     */
    public function createPackage($syncId, $orgId)
    {
        $data = $this->getDataProviderCollection()->getData([TestCenter::PARAM_ORG_ID => $orgId]);

        $fileName = self::FILE_PREFIX .'_'. $syncId . '.json';

        $this->getPackageService()->createPackage($data, $fileName);
    }

    /**
     * @return DataProviderCollection
     */
    private function getDataProviderCollection()
    {
        return $this->getServiceLocator()->get(DataProviderCollection::SERVICE_ID);
    }

    /**
     * @return PackageService
     */
    private function getPackageService()
    {
        return $this->getServiceLocator()->get(PackageService::SERVICE_ID);
    }
}
