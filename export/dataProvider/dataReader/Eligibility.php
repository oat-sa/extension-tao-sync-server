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

namespace oat\taoSyncServer\export\dataProvider\dataReader;

use oat\taoSync\export\dataProvider\dataReader\AbstractDataReader;
use oat\taoSyncServer\exception\DataProviderException;
use oat\taoTestCenter\model\EligibilityService;

class Eligibility extends AbstractDataReader
{
    const TYPE = 'Eligibility';

    /**
     * @inheritDoc
     */
    public function getData(array $testCenter)
    {
        if (!array_key_exists('id', $testCenter)) {
            throw new DataProviderException('Invalid data for Eligibility data provider');
        }

        $eligibility = $this->getClass(EligibilityService::CLASS_URI)->searchInstances(
            [EligibilityService::PROPERTY_TESTCENTER_URI => $testCenter['id']],
            ['recursive' => false, 'like' => false]
        );

        return $eligibility;
    }
}
