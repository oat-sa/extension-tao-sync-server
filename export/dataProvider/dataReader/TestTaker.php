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

use oat\taoSync\export\dataProvider\dataReader\AbstractDataReader;
use oat\taoSyncServer\exception\DataProviderException;
use oat\taoTestCenter\model\EligibilityService;

class TestTaker extends AbstractDataReader
{
    const TYPE = 'testTaker';

    /**
     * @inheritDoc
     */
    public function getData(array $eligibilityData)
    {
        $testTakers = [];
        foreach ($eligibilityData as $eligibility) {

            if (!array_key_exists(EligibilityService::PROPERTY_TESTTAKER_URI, $eligibility)) {
                throw new DataProviderException('Invalid eligibility data for testTaker data provider');
            }
            $testTakers[] = $this->getResource($eligibility[EligibilityService::PROPERTY_TESTTAKER_URI]);
        }
        return $testTakers;
    }
}
