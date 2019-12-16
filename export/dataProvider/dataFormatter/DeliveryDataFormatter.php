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

namespace oat\taoSyncServer\export\dataProvider\dataFormatter;

use core_kernel_classes_Resource;
use oat\taoEncryption\Rdf\EncryptedDeliveryRdf;
use oat\taoEncryption\Service\KeyProvider\FileKeyProviderService;

class DeliveryDataFormatter extends RdfDataFormatter
{
    /**
     * @param core_kernel_classes_Resource $resource
     * @return array
     */
    public function formatResource(core_kernel_classes_Resource $resource)
    {
        $properties = parent::formatResource($resource);

        /** @var FileKeyProviderService $keyProvider */
        $keyProvider = $this->getServiceLocator()->get(FileKeyProviderService::SERVICE_ID);
        $properties[EncryptedDeliveryRdf::PROPERTY_APPLICATION_KEY] = $keyProvider->getKeyFromFileSystem();

        return $properties;
    }

}