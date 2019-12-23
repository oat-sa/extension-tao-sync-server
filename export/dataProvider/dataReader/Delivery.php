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

use oat\taoSync\model\dataProvider\dataReader\AbstractDataReader;
use oat\taoSync\model\Exception\SyncDataProviderException;
use oat\taoTestCenter\model\EligibilityService;

class Delivery extends AbstractDataReader
{
    const TYPE = 'delivery';

    /**
     * @inheritDoc
     */
    public function getData(array $eligibilityData)
    {
        $deliveries = [];

        foreach ($eligibilityData as $eligibility) {
            if (
                !is_array($eligibility)
                || !array_key_exists(EligibilityService::PROPERTY_DELIVERY_URI, $eligibility)
            ) {
                throw new SyncDataProviderException('Invalid eligibility data for testTaker data provider');
            }
            if (!is_array($eligibility[EligibilityService::PROPERTY_DELIVERY_URI])) {
                $deliveries[$eligibility[EligibilityService::PROPERTY_DELIVERY_URI]]
                    = $this->getResource($eligibility[EligibilityService::PROPERTY_DELIVERY_URI]);
            } else {
                foreach ($eligibility[EligibilityService::PROPERTY_DELIVERY_URI] as $id) {
                    if (!array_key_exists($id, $deliveries)) {
                        $deliveries[$id] = $this->getResource($id);
                    }
                }
            }
        }
        return $deliveries;
    }
}
