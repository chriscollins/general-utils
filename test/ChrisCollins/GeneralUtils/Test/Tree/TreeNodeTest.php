<?php

namespace ChrisCollins\GeneralUtils\Test\Tree;

use ChrisCollins\GeneralUtils\Test\AbstractTestCase;
use ChrisCollins\GeneralUtils\Tree\TreeNode;

/**
 * TreeNodeTest
 */
class TreeNodeTest extends AbstractTestCase
{
    /**
     * @var array An array of TreeNodeObjectInterface objects.
     */
    protected $treeObjects = array();

    /**
     * @var TreeNode A TreeNode.
     */
    protected $instance;

    /**
     * Set up.
     */
    public function setUp(): void
    {
        $this->treeObjects = $this->getTreeObjects();

        $this->instance = new TreeNode(new TreeObjectStub(1, 2));
    }

    public function testConstructorSetsObject(): void
    {
        $treeObject = $this->treeObjects[0];

        $node = new TreeNode($treeObject);

        $this->assertEquals($treeObject, $node->getObject());
    }

    /**
     * Data provider for field names and inputs.
     *
     * @return array An array of field names and values.
     */
    public static function getFieldNamesAndInputs()
    {
        $treeObject = new TreeObjectStub(1, 2);

        return array(
            array('object', $treeObject),
            array('parent', new TreeNode($treeObject)),
            array('children', array()),
        );
    }

    /**
     * @dataProvider getFieldNamesAndInputs
     */
    public function testGettersReturnValuesSetBySetters($fieldName, $input): void
    {
        $setter = 'set' . ucfirst($fieldName);
        $getter = 'get' . ucfirst($fieldName);

        // Assert that a fluent interface is used.
        $instance = $this->instance->$setter($input);
        $this->assertInstanceOf('ChrisCollins\GeneralUtils\Tree\TreeNode', $instance);

        $this->assertEquals($input, $instance->$getter());
    }

    public function testGetChildrenReturnsNodesAddedByAddChild(): void
    {
        $node = new TreeNode($this->treeObjects[0]);
        $childNode = new TreeNode($this->treeObjects[1]);

        $node->addChild($childNode);

        $children = $node->getChildren();
        $this->assertCount(1, $children);
        $this->assertEquals($childNode, $children[0]);
    }

    public function testIsRootNodeReturnsExpectedValue(): void
    {
        $node = new TreeNode($this->treeObjects[0]);
        $this->assertTrue($node->isRootNode());

        $parent = new TreeNode($this->treeObjects[1]);
        $this->assertTrue($node->isRootNode());

        $node->setParent($parent);
        $this->assertFalse($node->isRootNode());
        $this->assertTrue($parent->isRootNode());
    }

    public function testBuildTreeAndGetRootsReturnsExpectedTree(): void
    {
        $roots = TreeNode::buildTreeAndGetRoots($this->treeObjects);

        $this->assertCount(2, $roots);
        $this->assertInstanceOf('ChrisCollins\GeneralUtils\Tree\TreeNode', $roots[0]);
        $this->assertInstanceOf('ChrisCollins\GeneralUtils\Tree\TreeNode', $roots[1]);

        $treeRoot = null;
        $orphanedRoot = null;
        foreach ($roots as $root) {
            if ($root->getObject()->getId() === 1) {
                $treeRoot = $root;
            } elseif ($root->getObject()->getId() === 7) {
                $orphanedRoot = $root;
            } else {
                $this->fail('Unexpected root found.');
            }
        }

        $this->assertNull($treeRoot->getParent());
        $this->assertNull($orphanedRoot->getParent());

        $this->assertCount(0, $orphanedRoot->getChildren());

        $treeRootChildren = $treeRoot->getChildren();
        $this->assertCount(2, $treeRootChildren);

        $childWithTwoChildren = null;
        $childWithOneChild = null;
        foreach ($treeRootChildren as $child) {
            if ($child->getObject()->getId() === 2) {
                $childWithTwoChildren = $child;
            } elseif ($child->getObject()->getId() === 3) {
                $childWithOneChild = $child;
            } else {
                $this->fail('Unexpected child found.');
            }
        }

        $this->assertEquals($treeRoot, $childWithTwoChildren->getParent());
        $this->assertEquals($treeRoot, $childWithOneChild->getParent());

        $this->assertCount(2, $childWithTwoChildren->getChildren());
        $this->assertCount(1, $childWithOneChild->getChildren());

        foreach ($childWithTwoChildren->getChildren() as $leaf) {
            $this->assertEquals($childWithTwoChildren, $leaf->getParent());
            $this->assertEmpty($leaf->getChildren());
        }

        foreach ($childWithOneChild->getChildren() as $leaf) {
            $this->assertEquals($childWithOneChild, $leaf->getParent());
            $this->assertEmpty($leaf->getChildren());
        }
    }

    /**
     * Get some test objects that can be structured in a tree.
     *
     * @return array An array of objects implementing TreeNodeObjectInterface.
     */
    protected function getTreeObjects()
    {
        $root1 = new TreeObjectStub(1, null);
        $root2 = new TreeObjectStub(7, 44);

        $level1Object1 = new TreeObjectStub(2, 1);
        $level1Object2 = new TreeObjectStub(3, 1);

        $level2Object1 = new TreeObjectStub(4, 2);
        $level2Object2 = new TreeObjectStub(5, 2);
        $level2Object3 = new TreeObjectStub(6, 3);

        return array($root1, $level1Object1, $level1Object2, $level2Object1, $level2Object2, $level2Object3, $root2);
    }
}
