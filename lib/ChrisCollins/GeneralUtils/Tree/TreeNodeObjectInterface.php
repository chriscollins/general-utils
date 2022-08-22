<?php

namespace ChrisCollins\GeneralUtils\Tree;

/**
 * TreeNodeObjectInterface
 *
 * An interface that objects that will be stored inside TreeNodes must implement.
 */
interface TreeNodeObjectInterface
{
    /**
     * Determine if this object is the immediate parent of another object.
     *
     * @param TreeNodeObjectInterface $object The potential child.
     *
     * @return bool True if this object is the parent of the given object.
     */
    public function isParentOf(TreeNodeObjectInterface $object): bool;
}
