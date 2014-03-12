<?php

namespace ChrisCollins\GeneralUtils\Test\Tree;

use ChrisCollins\GeneralUtils\Tree\TreeNodeObjectInterface;

/**
 * TreeObjectStub
 *
 * A stub class to assist with testing the TreeNode class.
 */
class TreeObjectStub implements TreeNodeObjectInterface
{
    /**
     * @var integer The ID of this object.
     */
    protected $id = null;

    /**
     * @var integer The ID of this object's parent.
     */
    protected $parentId = null;

    /**
     * Constructor.
     *
     * @param integer $id The ID.
     * @param integer $parentId The ID of the parent object.
     */
    public function __construct($id, $parentId)
    {
        $this->id = $id;
        $this->parentId = $parentId;
    }

    /**
     * {@inheritDoc}
     */
    public function isParentOf(TreeNodeObjectInterface $object)
    {
        return $object->getParentId() === $this->id;
    }

    /**
     * Accessor method.
     *
     * @return integer The value of the property.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Accessor method.
     *
     * @return integer The value of the property.
     */
    public function getParentId()
    {
        return $this->parentId;
    }
}
