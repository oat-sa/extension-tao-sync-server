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

namespace oat\taoSyncServer\export\dataProvider;

use core_kernel_classes_Resource;
use oat\generis\model\data\Ontology;
use oat\taoLti\models\classes\ConsumerService;
use oat\taoSync\model\dataProvider\AbstractDataProvider;
use oat\taoSyncServer\exception\DataProviderException;

class LtiConsumers extends AbstractDataProvider
{
    const TYPE = 'ltiConsumers';

    public function getType()
    {
        return self::TYPE;
    }

    /**
     * @param array $params
     * @return array
     * @throws DataProviderException
     */
    public function getData($params)
    {
        $data = $this->getClass()->searchInstances(
            [],
            ['recursive' => false, 'like' => false]
        );

        if ($this->getDataFormatter()) {
            $data = $this->getDataFormatter()->formatAll($data);
        }

        return $data;
    }

    /**
     * @return core_kernel_classes_Resource
     */
    private function getClass()
    {
        return $this->getServiceLocator()->get(Ontology::SERVICE_ID)->getClass(ConsumerService::CLASS_URI);
    }
}
