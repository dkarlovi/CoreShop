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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension\Document;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\Document\Editable;

/**
 * @psalm-suppress InvalidReturnType, InvalidReturnStatement
 */
class Select extends Editable
{
    /**
     * @var ResourceInterface|null
     */
    public $resource;

    public function __construct(protected string $repositoryName, protected string $nameProperty, protected string $type)
    {
    }

    public function getType()
    {
        return $this->type;
    }

    public function frontend()
    {
        return '';
    }

    public function getData()
    {
        return $this->resource;
    }

    /**
     * @return ResourceInterface|null
     */
    public function getResourceObject()
    {
        if ($this->resource) {
            $object = $this->getRepository()->find($this->resource);

            if ($object instanceof ResourceInterface) {
                return $object;
            }
        }

        return null;
    }

    public function isEmpty()
    {
        return !$this->getResourceObject() instanceof ResourceInterface;
    }

    public function getOptions()
    {
        $data = $this->getRepository()->findAll();
        $result = [];

        foreach ($data as $resource) {
            if (!$resource instanceof ResourceInterface) {
                throw new \InvalidArgumentException('Only ResourceInterface is allowed');
            }

            $result[] = [
                $resource->getId(),
                $this->getResourceName($resource),
            ];
        }

        $options = [];
        $options['store'] = $result;

        return $options;
    }

    public function setDataFromEditmode($data)
    {
        $this->resource = $data;

        return $this;
    }

    public function setDataFromResource($data)
    {
        $this->resource = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getForWebserviceExport($document = null, $params = [])
    {
        return [
            'id' => $this->resource->getId(),
        ];
    }

    /**
     * @return mixed
     */
    protected function getResourceName(ResourceInterface $resource)
    {
        $getter = 'get' . ucfirst($this->nameProperty);

        if (!method_exists($resource, $getter)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Property with Name %s does not exist in resource %s',
                    $this->nameProperty,
                    $resource::class
                )
            );
        }

        return $resource->$getter();
    }

    /**
     * @return RepositoryInterface
     */
    private function getRepository()
    {
        $repo = \Pimcore::getContainer()->get($this->repositoryName);

        if (!$repo instanceof RepositoryInterface) {
            throw new \InvalidArgumentException(sprintf('Repository with Identifier %s not found or not public', $this->repositoryName));
        }

        return $repo;
    }
}
