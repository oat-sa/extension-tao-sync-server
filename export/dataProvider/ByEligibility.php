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

namespace oat\taoSyncServer\export\dataProvider;

use oat\taoSync\export\dataProvider\dataReader\AbstractDataReader;
use oat\taoSync\model\dataProvider\AbstractDataProvider;
use oat\taoSync\model\Exception\SyncDataProviderException;
use oat\taoSyncServer\export\dataProvider\dataReader\Eligibility;

class ByEligibility extends AbstractDataProvider
{
    const OPTION_READER = 'reader';

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return Eligibility::TYPE;
    }

    /**
     * @return string
     * @throws SyncDataProviderException
     */
    public function getType()
    {
        return $this->getDataReader()->getType();
    }

    /**
     * @param array $params
     * @return array
     * @throws SyncDataProviderException
     */
    public function getResources(array $params)
    {
        if (!array_key_exists(Eligibility::TYPE, $params)) {
            return [];
        }
        return $this->getDataReader()->getData($params[Eligibility::TYPE]);
    }

    /**
     * @return AbstractDataReader
     * @throws SyncDataProviderException
     */
    protected function getDataReader()
    {
        $reader = $this->getOption(self::OPTION_READER);

        if (!$reader instanceof AbstractDataReader) {
            throw new SyncDataProviderException('Invalid data reader for ' . __CLASS__);
        }

        return $this->propagate($reader);
    }
}
