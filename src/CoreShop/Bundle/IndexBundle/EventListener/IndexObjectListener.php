<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Bundle\IndexBundle\EventListener;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Service\IndexUpdaterServiceInterface;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\ElementEventInterface;

final class IndexObjectListener
{
    /**
     * @var IndexUpdaterServiceInterface
     */
    private $indexUpdaterService;

    /**
     * @param IndexUpdaterServiceInterface $indexUpdaterService
     */
    public function __construct(IndexUpdaterServiceInterface $indexUpdaterService)
    {
        $this->indexUpdaterService = $indexUpdaterService;
    }

    /**
     * @param ElementEventInterface $event
     */
    public function onPostUpdate(ElementEventInterface $event)
    {
        if ($event instanceof DataObjectEvent) {
            $object = $event->getObject();

            if (!$object instanceof IndexableInterface) {
                return;
            }

            $this->indexUpdaterService->updateIndices($object);
        }
    }

    /**
     * @param ElementEventInterface $event
     */
    public function onPostDelete(ElementEventInterface $event)
    {
        if ($event instanceof DataObjectEvent) {
            $object = $event->getObject();

            if (!$object instanceof IndexableInterface) {
                return;
            }

            $this->indexUpdaterService->removeIndices($object);
        }
    }
}
