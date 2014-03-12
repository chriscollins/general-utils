<?php

namespace ChrisCollins\GeneralUtils\Tree;

/**
 * TreeNode
 *
 * A class to represent a node in a tree structure.
 */
class TreeNode
{
    /**
     * @var TreeNodeObjectInterface The object contained by the tree node.
     */
    protected $object = null;

    /**
     * @var TreeNode The parent of this node.
     */
    protected $parent = null;

    /**
     * @var array Array of tree node children.
     */
    protected $children = array();

    /**
     * Constructor.
     *
     * @param TreeNodeObjectInterface $object The object contained by the tree node.
     */
    public function __construct(TreeNodeObjectInterface $object)
    {
        $this->object = $object;
    }

    /**
     * Accessor method.
     *
     * @return mixed The value of the property.
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Mutator method.
     *
     * @param mixed The new value of the property.
     *
     * @return self This object.
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Accessor method.
     *
     * @return TreeNode The value of the property.
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Mutator method.
     *
     * @param TreeNode The new value of the property.
     *
     * @return TreeNode This object.
     */
    public function setParent(TreeNode $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Accessor method.
     *
     * @return array The value of the property.
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Mutator method.
     *
     * @param array The new value of the property.
     *
     * @return TreeNode This object.
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Add a child.
     *
     * @param TreeNode $child A child tree node.
     */
    public function addChild(TreeNode $child)
    {
        $this->children[] = $child;
    }

    /**
     * Determine if this node is a root node, i.e. it has no parent.
     *
     * @return boolean True if this is a root node, i.e. it has no parent.
     */
    public function isRootNode()
    {
        return $this->parent === null;
    }

    /**
     * Build a tree from an array of TreeNodeObjectInterface objects.
     *
     * @param array $objects An array of TreeNodeObjectInterface objects to make into a tree.
     *
     * @return array An array of TreeNodes, each representing a root (i.e. a node for which no parent was found).
     */
    public static function buildTreeAndGetRoots(array $objects)
    {
        $nodeClass = get_called_class(); // Use late static binding so new nodes are created as the calling subclass.

        // Build a TreeNode for each object.
        $nodes = array();
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
        $roots = array();
        foreach ($nodes as $potentialRoot) {
            if ($potentialRoot->isRootNode()) {
                $roots[] = $potentialRoot;
            }
        }

        return $roots;
    }
}
