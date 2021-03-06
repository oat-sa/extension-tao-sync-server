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

use oat\taoSync\model\dataProvider\AbstractDataProvider;
use oat\taoSync\model\Exception\SyncDataProviderException;
use oat\taoSync\model\synchronizer\custom\byOrganisationId\OrganisationIdTrait;

class TestCenter extends AbstractDataProvider
{
    use OrganisationIdTrait;

    const PARAM_ORG_ID = 'orgID';
    const TYPE = 'testCenter';

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * @param array $params
     * @return array
     * @throws SyncDataProviderException
     */
    public function getResources(array $params)
    {
        if (!isset($params[self::PARAM_ORG_ID])) {
            throw new SyncDataProviderException('Organisation id required');
        }

        return $this->getTestCentersByOrganisationId($params[self::PARAM_ORG_ID]);
    }
}
