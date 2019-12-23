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
use oat\taoEncryption\Rdf\EncryptedUserRdf;
use oat\taoEncryption\Service\EncryptionSymmetricServiceHelper;
use Exception;

class EncryptedUserRdfFormatter extends RdfDataFormatter
{
    use EncryptionSymmetricServiceHelper;

    const OPTION_ENCRYPTION_SERVICE = 'symmetricEncryptionService';

    const OPTION_ENCRYPTED_PROPERTIES = 'encryptedProperties';

    const OPTION_ENCRYPTION_KEY_PROVIDER_SERVICE = 'keyProviderService';

    /**
     * @return array
     */
    protected function getEncryptedProperties()
    {
        return $this->getOption(self::OPTION_ENCRYPTED_PROPERTIES) ?? [];
    }

    /**
     * @param core_kernel_classes_Resource $resource
     * @return array
     */
    public function formatResource(core_kernel_classes_Resource $resource)
    {
        $properties = parent::formatResource($resource);

        return $this->encryptProperties($properties);
    }

    /**
     * @param array $properties
     * @return array
     */
    public function encryptProperties(array $properties)
    {
        if (!isset($properties[EncryptedUserRdf::PROPERTY_ENCRYPTION_KEY])){
            return $properties;
        }

        $propertiesToEncrypt = $this->getEncryptedProperties();
        $keyEncryption = $properties[EncryptedUserRdf::PROPERTY_ENCRYPTION_KEY];

       foreach ($propertiesToEncrypt as $key) {
           if (array_key_exists($key, $properties)) {
               $properties[$key] = $this->encryptProperty($properties[$key], $keyEncryption);
           }
       }
        return $properties;
    }

    /**
     * @param array|string $values
     * @param string $keyEncryption
     * @return array|string
     * @throws Exception
     */
    public function encryptProperty($values, $keyEncryption)
    {
        if (!is_array($values)) {
            return base64_encode($this->getEncryptionService($keyEncryption)->encrypt($values));
        }
        $encrypted = [];
        foreach ($values as $value) {
            $encrypted[] = base64_encode($this->getEncryptionService($keyEncryption)->encrypt($value));
        }
        return $encrypted;
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
