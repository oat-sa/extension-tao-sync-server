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

namespace oat\taoSyncServer\export\dataProvider\dataReader;

use oat\generis\model\GenerisRdf;
use oat\tao\model\TaoOntology;
use oat\taoProctoring\model\ProctorService;
use oat\taoSync\export\dataProvider\dataReader\AbstractDataReader;
use oat\taoSyncServer\exception\DataProviderException;
use oat\taoTestCenter\model\ProctorManagementService;

class Proctor extends AbstractDataReader
{
    const TYPE = 'Proctor';

    /**
     * @inheritDoc
     */
    public function getData(array $params)
    {
        if (!array_key_exists('id', $params)) {
            throw new DataProviderException('Invalid  data for Proctor data provider');
        }

        $eligibility = $this->getClass(TaoOntology::CLASS_URI_TAO_USER)->searchInstances(
            [
                ProctorManagementService::PROPERTY_ASSIGNED_PROCTOR_URI => $params['id'],
                GenerisRdf::PROPERTY_USER_ROLES => ProctorService::ROLE_PROCTOR,
            ],
            ['recursive' => false, 'like' => false]
        );

        return $eligibility;
    }
}
