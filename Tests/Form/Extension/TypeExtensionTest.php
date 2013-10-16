<?php
namespace Thrace\MediaBundle\Tests\Form\Extension;

use Symfony\Component\Form\Extension\Core\CoreExtension;

class TypeExtensionTest extends CoreExtension
{

    protected $types;

    /**
     * Construct
     *
     * @param array $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * {@inheritDoc}
     */
    protected function loadTypes()
    {
        return array_merge(parent::loadTypes(), $this->types);
    }
}