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

namespace oat\taoSyncServer\scripts\install;

use oat\oatbox\extension\InstallAction;
use common_Exception;
use oat\taoDeliveryRdf\model\assembly\CompiledTestConverterFactory;
use oat\taoSyncServer\export\service\ExportDeliveryAssembly;

/**
 * php index.php 'oat\taoSyncServer\scripts\install\RegisterExportDeliveryAssembly'
 *
 * Class RegisterSyncQueueRds
 * @package oat\taoSyncClient\scripts\install
 */
class RegisterExportDeliveryAssembly extends InstallAction
{
    /**
     * @param $params
     * @throws common_Exception
     */
    public function __invoke($params)
    {
        $service = new ExportDeliveryAssembly([
            ExportDeliveryAssembly::OPTION_ENCRYPTION_ALGORITHM => 'AES',
            ExportDeliveryAssembly::OPTION_OUTPUT_TEST_FORMAT => CompiledTestConverterFactory::COMPILED_TEST_FORMAT_XML
        ]);

        $this->getServiceManager()->register(ExportDeliveryAssembly::SERVICE_ID, $service);
    }
}
