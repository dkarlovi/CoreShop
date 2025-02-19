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

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Bundle\FrontendBundle\Form\Type\SearchType;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends FrontendController
{
    public function widgetAction(Request $request): Response
    {
        $form = $this->createSearchForm();

        return $this->render($this->templateConfigurator->findTemplate('Search/_widget.html'), [
            'form' => $form->createView(),
        ]);
    }

    public function searchAction(Request $request): Response
    {
        $form = $this->createSearchForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $text = $formData['text'];
            $page = $request->get('page', 1);
            $itemsPerPage = 10;

            $query = [
                'name LIKE ?',
                'description LIKE ?',
                'shortDescription LIKE ?',
                'sku LIKE ?',
            ];
            $queryParams = [
                '%' . $text . '%',
                '%' . $text . '%',
                '%' . $text . '%',
                '%' . $text . '%',
                '%' . $this->container->get(StoreContextInterface::class)->getStore()->getId() . '%',
            ];

            $list = $this->get('coreshop.repository.product')->getList();
            $list->setCondition('active = 1 AND (' . implode(' OR ', $query) . ') AND stores LIKE ?', $queryParams);

            $paginator = $this->getPaginator()->paginate(
                $list,
                $page,
                $itemsPerPage
            );

            return $this->render($this->templateConfigurator->findTemplate('Search/search.html'), [
                'paginator' => $paginator,
                'searchText' => $text,
            ]);
        }

        return $this->redirectToRoute('coreshop_index');
    }

    protected function createSearchForm(): FormInterface
    {
        return $this->get('form.factory')->createNamed('coreshop', SearchType::class, null, [
            'action' => $this->generateUrl('coreshop_search'),
            'method' => 'GET',
        ]);
    }

    protected function getPaginator(): PaginatorInterface
    {
        return $this->get(PaginatorInterface::class);
    }
}
