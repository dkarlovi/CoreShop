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

namespace CoreShop\Component\Order\Repository;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

interface CartRepositoryInterface extends PimcoreRepositoryInterface
{
    /**
     * @return OrderInterface[]
     */
    public function findForCustomer(CustomerInterface $customer): array;

    /**
     * @param string            $name
     */
    public function findNamedForCustomer(CustomerInterface $customer, $name): ?OrderInterface;

    public function findLatestByStoreAndCustomer(StoreInterface $store, CustomerInterface $customer): ?OrderInterface;

    /**
     * @param int $id
     */
    public function findCartById($id): ?OrderInterface;

    /**
     * @param int  $days
     * @param bool $anonymous
     * @param bool $customer
     *
     * @return OrderInterface[]
     */
    public function findExpiredCarts($days, $anonymous, $customer): array;
}
