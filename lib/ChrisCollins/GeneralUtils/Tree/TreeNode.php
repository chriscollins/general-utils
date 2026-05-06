<?php

declare(strict_types=1);

namespace ChrisCollins\GeneralUtils\Tree;

/**
 * TreeNode
 *
 * A class to represent a node in a tree structure.
 */
class TreeNode
{
    private ?TreeNode $parent = null;

    /**
     * @var TreeNode[] Array of tree node children.
     */
    private array $children = [];

    public function __construct(private TreeNodeObjectInterface $object)
    {
    }

    public function getObject(): TreeNodeObjectInterface
    {
        return $this->object;
    }

    public function setObject($object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getParent(): ?TreeNode
    {
        return $this->parent;
    }

    public function setParent(?TreeNode $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return TreeNode[] The value of the property.
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param TreeNode[] $children The new value of the property.
     */
    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }

    public function addChild(TreeNode $child): self
    {
        $this->children[] = $child;

        return $this;
    }

    public function isRootNode(): bool
    {
        return !$this->parent instanceof TreeNode;
    }

    /**
     * Build a tree from an array of TreeNodeObjectInterface objects.
     *
     * @param TreeNodeObjectInterface[] $objects An array of TreeNodeObjectInterface objects to make into a tree.
     *
     * @return TreeNode[] An array of TreeNodes, each representing a root (i.e. a node for which no parent was found).
     */
    public static function buildTreeAndGetRoots(array $objects): array
    {
        $nodeClass = static::class; // Use late static binding so new nodes are created as the calling subclass.

        // Build a TreeNode for each object.
        $nodes = [];
        foreach ($objects as $object) {
            $nodes[] = new $nodeClass($object);
        }

        // Loop through each node and update set its parent and children.
        foreach ($nodes as $node) {
            $object = $node->getObject();

            foreach ($nodes as $potentialParentNode) {
                if ($potentialParentNode->getObject()->isParentOf($object)) {
                    $node->setParent($potentialParentNode);
                    $potentialParentNode->addChild($node);

                    break;
                }
            }
        }

        // Now find the root nodes and return them.
        $roots = [];
        foreach ($nodes as $potentialRoot) {
            if ($potentialRoot->isRootNode()) {
                $roots[] = $potentialRoot;
            }
        }

        return $roots;
    }
}
