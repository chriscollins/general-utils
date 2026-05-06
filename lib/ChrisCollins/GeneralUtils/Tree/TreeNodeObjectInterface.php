<?php

declare(strict_types=1);

namespace ChrisCollins\GeneralUtils\Tree;

/**
 * TreeNodeObjectInterface
 *
 * An interface that objects that will be stored inside TreeNodes must implement.
 */
interface TreeNodeObjectInterface
{
    public function isParentOf(TreeNodeObjectInterface $object): bool;
}
