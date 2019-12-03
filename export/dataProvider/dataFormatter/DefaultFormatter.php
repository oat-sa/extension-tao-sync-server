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
use oat\taoSync\export\dataProvider\dataFormatter\AbstractDataFormatter;

class DefaultFormatter extends AbstractDataFormatter
{
    const OPTION_EXCLUDED_FIELDS = 'excluded-fields';

    /**
     * @inheritDoc
     */
    public function format(core_kernel_classes_Resource $resource)
    {
        $properties = $this->filterProperties($resource->getRdfTriples()->toArray());
        $properties['id'] = $resource->getUri();
        return $properties;
    }

    /**
     * @param array $triples
     * @return array
     */
    private function filterProperties(array $triples)
    {
        $excludedProperties = $this->getExcludedProperties();
        $properties = [];

        foreach ($triples as $triple) {
            if (!in_array($triple->predicate, $excludedProperties)) {
                $properties[$triple->predicate] = $triple->object;
            }

        }
        return $properties;
    }

    /**
     * @return array
     */
    private function getExcludedProperties()
    {
        if (is_array($this->getOption(self::OPTION_EXCLUDED_FIELDS))) {
            return $this->getOption(self::OPTION_EXCLUDED_FIELDS);
        }
        return [];
    }
}
