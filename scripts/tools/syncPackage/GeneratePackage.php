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

namespace oat\taoSyncServer\scripts\tools\syncPackage;

use common_exception_Error;
use common_report_Report;
use oat\oatbox\extension\script\ScriptAction;
use oat\taoSync\model\Exception\SyncBaseException;
use oat\taoSyncServer\export\service\ExportPackage;

/**
 * php index.php 'oat\taoSyncServer\scripts\tools\syncPackage\GeneratePackage'
 */
class GeneratePackage extends ScriptAction
{
    const OPTION_ORGANISATION_ID = 'organisation_id';

    /**
     * @return string
     */
    protected function provideDescription()
    {
        return 'Creating new file with prepared data which have to be sent to the client.';
    }

    /**
     * @return array
     */
    protected function provideOptions()
    {
        return [
            self::OPTION_ORGANISATION_ID => [
                'prefix'       => 'o',
                'flag'         => false,
                'cast'         => 'integer',
                'longPrefix'   => self::OPTION_ORGANISATION_ID,
                'description'  => 'Organisation id',
                'required' => true
            ]
        ];
    }

    /**
     * @return array
     */
    protected function provideUsage()
    {
        return [
            'prefix' => 'h',
            'longPrefix' => 'help',
            'description' => 'Prints a help statement'
        ];
    }

    /**
     * @return common_report_Report
     * @throws common_exception_Error
     */
    protected function run()
    {
        $report = common_report_Report::createInfo('Script execution started');

        try {
            $this->getExportService()->createPackage($this->getOption(self::OPTION_ORGANISATION_ID));
            $report->add(common_report_Report::createSuccess('Done'));
        } catch (SyncBaseException $e) {
            $report->add(common_report_Report::createFailure($e->getMessage()));
        }

        return $report;
    }

    /**
     * @return ExportPackage
     */
    protected function getExportService()
    {
        return $this->getServiceLocator()->get(ExportPackage::class);
    }
}
