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
use oat\taoEncryption\Service\EncryptionSymmetricService;
use oat\taoEncryption\Service\KeyProvider\FileKeyProviderService;
use oat\taoEncryption\Service\KeyProvider\SimpleKeyProviderService;
use oat\taoEncryption\Service\LtiConsumer\EncryptedLtiConsumer;
use oat\taoSync\model\Exception\SyncDataProviderException;

class EncryptLtiConsumerFormatter extends RdfDataFormatter
{
    const SERVICE_ID = 'taoEncryption/encryptLtiConsumer';

    const OPTION_ENCRYPTION_SERVICE = 'encryptionService';
    const OPTION_ENCRYPTION_KEY_PROVIDER_SERVICE = 'keyProviderService';

    /** @var EncryptionSymmetricService */
    private $encryptionService;

    /**
     * @param core_kernel_classes_Resource $resource
     * @return array
     * @throws SyncDataProviderException
     */
    public function formatResource(core_kernel_classes_Resource $resource)
    {
        $properties = parent::formatResource($resource);

        if (!empty($properties[EncryptedLtiConsumer::PROPERTY_CUSTOMER_APP_KEY])) {
            $properties[EncryptedLtiConsumer::PROPERTY_ENCRYPTED_APPLICATION_KEY]
                = $this->encryptCustomerAppKey(current($properties[EncryptedLtiConsumer::PROPERTY_CUSTOMER_APP_KEY]));

            unset($properties[EncryptedLtiConsumer::PROPERTY_CUSTOMER_APP_KEY]);
        }
        ksort($properties);

        return $properties;
    }

    /**
     * @param string $customerAppKey
     * @return string
     * @throws SyncDataProviderException
     */
    protected function encryptCustomerAppKey($customerAppKey)
    {
        /** @var SimpleKeyProviderService $keyProvider */
        $keyProvider = $this->getServiceLocator()->get(SimpleKeyProviderService::SERVICE_ID);
        $keyProvider->setKey($customerAppKey);

        $this->getEncryptionService()->setKeyProvider($keyProvider);

        return base64_encode($this->getEncryptionService()->encrypt($this->getApplicationKey()));
    }

    /**
     * @return string
     * @throws SyncDataProviderException
     */
    protected function getApplicationKey()
    {
        if (!$this->hasOption(static::OPTION_ENCRYPTION_KEY_PROVIDER_SERVICE)) {
            throw new SyncDataProviderException(
                'Invalid EncryptLtiConsumerFormatter configuration: key provider missing'
            );
        }

        /** @var FileKeyProviderService $keyProvider */
        $keyProvider = $this->getServiceLocator()->get(
            $this->getOption(static::OPTION_ENCRYPTION_KEY_PROVIDER_SERVICE)
        );

        return $keyProvider->getKeyFromFileSystem();
    }

    /**
     * @return array|EncryptionSymmetricService|object
     * @throws SyncDataProviderException
     */
    protected function getEncryptionService()
    {
        if (is_null($this->encryptionService)) {
            $service = $this->getServiceLocator()->get(
                $this->getOption(static::OPTION_ENCRYPTION_SERVICE)
            );
            if (!$service instanceof EncryptionSymmetricService) {
                throw new SyncDataProviderException(
                    'Encryption Service must be instance of EncryptionSymmetricService'
                );
            }

            $this->encryptionService = $service;
        }
        return $this->encryptionService;
    }
}
