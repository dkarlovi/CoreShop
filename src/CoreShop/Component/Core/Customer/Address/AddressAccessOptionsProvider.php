<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Customer\Address;

use CoreShop\Component\Core\Customer\Allocator\CustomerAddressAllocatorInterface;
use CoreShop\Component\Customer\Model\CompanyInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;

final class AddressAccessOptionsProvider implements SelectOptionsProviderInterface
{
    /**
     * @param array $context
     * @param Data  $fieldDefinition
     */
    public function getOptions($context, $fieldDefinition): array
    {
        if (!isset($context['object'])) {
            return [];
        }

        $object = $context['object'];

        if (!$object instanceof CustomerInterface) {
            return [];
        }

        $types = [
            [
                'value' => CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_ONLY,
                'key' => 'coreshop.company.address_access.own_only',
            ],
        ];

        if (!$object->getCompany() instanceof CompanyInterface) {
            return $types;
        }

        $types[] = [
            'value' => CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_COMPANY_ONLY,
            'key' => 'coreshop.company.address_access.company_only',
        ];

        $types[] = [
            'value' => CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_AND_COMPANY,
            'key' => 'coreshop.company.address_access.own_and_company',
        ];

        return $types;
    }

    public function getDefaultValue($context, $fieldDefinition): string
    {
        return CustomerAddressAllocatorInterface::ADDRESS_ACCESS_TYPE_OWN_ONLY;
    }

    public function hasStaticOptions($context, $fieldDefinition): bool
    {
        return false;
    }
}
