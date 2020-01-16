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
use Exception;
use oat\taoEncryption\Service\EncryptionSymmetricService;
use oat\taoEncryption\Service\EncryptionSymmetricServiceHelper;
use oat\taoEncryption\Service\KeyProvider\FileKeyProviderService;
use oat\taoEncryption\Service\LtiConsumer\EncryptedLtiConsumer;

class EncryptLtiConsumerFormatter extends RdfDataFormatter
{
    use EncryptionSymmetricServiceHelper;

    const SERVICE_ID = 'taoEncryption/encryptLtiConsumer';

    const OPTION_ENCRYPTION_SERVICE = 'encryptionService';
    const OPTION_ENCRYPTION_KEY_PROVIDER_SERVICE = 'keyProviderService';

    /** @var EncryptionSymmetricService */
    private $encryptionService;

    /**
     * @param core_kernel_classes_Resource $resource
     * @return array
     * @throws Exception
     */
    public function formatResource(core_kernel_classes_Resource $resource)
    {
        $properties = parent::formatResource($resource);

        if (!empty($properties[EncryptedLtiConsumer::PROPERTY_CUSTOMER_APP_KEY])) {
            $properties[EncryptedLtiConsumer::PROPERTY_ENCRYPTED_APPLICATION_KEY]
                = $this->encryptCustomerAppKey($properties[EncryptedLtiConsumer::PROPERTY_CUSTOMER_APP_KEY]);

            unset($properties[EncryptedLtiConsumer::PROPERTY_CUSTOMER_APP_KEY]);
        }
        ksort($properties);

        return $properties;
    }

    /**
     * @param string $customerAppKey
     * @return string
     * @throws Exception
     */
    protected function encryptCustomerAppKey($customerAppKey)
    {
        return base64_encode($this->getEncryptionService($customerAppKey)->encrypt($this->getApplicationKey()));
    }

    /**
     * @return string
     */
    protected function getApplicationKey()
    {
        return $this->getServiceLocator()->get(FileKeyProviderService::SERVICE_ID)->getKeyFromFileSystem();
    }

    /**
     * @inheritdoc
     */
    protected function getOptionEncryptionService()
    {
        return $this->getOption(static::OPTION_ENCRYPTION_SERVICE);
    }

    /**
     * @inheritdoc
     */
    protected function getOptionEncryptionKeyProvider()
    {
        return $this->getOption(static::OPTION_ENCRYPTION_KEY_PROVIDER_SERVICE);
    }
}
