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

namespace oat\taoSyncServer\export\service;

use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\oatbox\service\ConfigurableService;
use oat\taoSync\package\SyncPackageService;
use tao_helpers_Uri;

class DeliveryAssemblyStorage extends ConfigurableService
{
    const ASSEMBLY_STORAGE_NAME = 'assembly';

    /**
     * @param string $deliveryUri
     * @return File
     */
    public function getDeliveryAssemblyFile($deliveryUri)
    {
        return $this->getAssemblyDirectory()->getFile($this->getAssemblerFileName($deliveryUri));
    }

    /**
     * @param string $deliveryUri
     * @return bool
     */
    public function deleteDeliveryAssemblyFile($deliveryUri)
    {
        return $this->getDeliveryAssemblyFile($deliveryUri)->delete();
    }

    /**
     * @param string $deliveryUri
     * @return string
     */
    private function getAssemblerFileName($deliveryUri)
    {
        return tao_helpers_Uri::encode($deliveryUri) . '.zip';
    }

    /**
     * @return Directory
     */
    private function getAssemblyDirectory()
    {
        return $this->getServiceLocator()->get(SyncPackageService::SERVICE_ID)
            ->getSyncDirectory()
            ->getDirectory(self::ASSEMBLY_STORAGE_NAME);
    }
}
