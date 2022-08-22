<?php

namespace ChrisCollins\GeneralUtils\Test\Tree;

use ChrisCollins\GeneralUtils\Tree\TreeNodeObjectInterface;
use InvalidArgumentException;

/**
 * TreeObjectStub
 *
 * A stub class to assist with testing the TreeNode class.
 */
class TreeObjectStub implements TreeNodeObjectInterface
{
    /**
     * @var int|null The ID of this object.
     */
    private ?int $id;

    /**
     * @var int|null The ID of this object's parent.
     */
    private ?int $parentId;

    /**
     * Constructor.
     *
     * @param int|null $id The ID.
     * @param int|null $parentId The ID of the parent object.
     */
    public function __construct(?int $id, ?int $parentId)
    {
        $this->id = $id;
        $this->parentId = $parentId;
    }

    /**
     * {@inheritDoc}
     */
    public function isParentOf(TreeNodeObjectInterface $object): bool
    {
        if (!$object instanceof TreeObjectStub) {
            throw new InvalidArgumentException('Invalid object given.');
        }

        return $object->getParentId() === $this->id;
    }

    /**
     * Accessor method.
     *
     * @return int|null The value of the property.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Accessor method.
     *
     * @return int|null The value of the property.
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }
}
