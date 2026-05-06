<?php

declare(strict_types=1);

namespace ChrisCollins\GeneralUtils\Test\Tree;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Iterator;
use ChrisCollins\GeneralUtils\Test\AbstractTestCase;
use ChrisCollins\GeneralUtils\Tree\TreeNode;

/**
 * TreeNodeTest
 */
final class TreeNodeTest extends AbstractTestCase
{
    /**
     * @var array An array of TreeNodeObjectInterface objects.
     */
    protected $treeObjects = [];

    /**
     * @var TreeNode A TreeNode.
     */
    protected $instance;

    /**
     * Set up.
     */
    protected function setUp(): void
    {
        $this->treeObjects = $this->getTreeObjects();

        $this->instance = new TreeNode(new TreeObjectStub(1, 2));
    }

    #[Test]
    public function constructorSetsObject(): void
    {
        $treeObject = $this->treeObjects[0];

        $node = new TreeNode($treeObject);

        $this->assertEquals($treeObject, $node->getObject());
    }

    /**
     * Data provider for field names and inputs.
     *
     * @return Iterator<(int|string), mixed> An array of field names and values.
     */
    public static function getFieldNamesAndInputs(): Iterator
    {
        $treeObject = new TreeObjectStub(1, 2);
        yield ['object', $treeObject];
        yield ['parent', new TreeNode($treeObject)];
        yield ['children', []];
    }

    #[Test]
    #[DataProvider('getFieldNamesAndInputs')]
    public function gettersReturnValuesSetBySetters($fieldName, $input): void
    {
        $setter = 'set' . ucfirst((string) $fieldName);
        $getter = 'get' . ucfirst((string) $fieldName);

        // Assert that a fluent interface is used.
        $instance = $this->instance->$setter($input);
        $this->assertInstanceOf(TreeNode::class, $instance);

        $this->assertEquals($input, $instance->$getter());
    }

    #[Test]
    public function getChildrenReturnsNodesAddedByAddChild(): void
    {
        $node = new TreeNode($this->treeObjects[0]);
        $childNode = new TreeNode($this->treeObjects[1]);

        $node->addChild($childNode);

        $children = $node->getChildren();
        $this->assertCount(1, $children);
        $this->assertEquals($childNode, $children[0]);
    }

    #[Test]
    public function isRootNodeReturnsExpectedValue(): void
    {
        $node = new TreeNode($this->treeObjects[0]);
        $this->assertTrue($node->isRootNode());

        $parent = new TreeNode($this->treeObjects[1]);
        $this->assertTrue($node->isRootNode());

        $node->setParent($parent);
        $this->assertFalse($node->isRootNode());
        $this->assertTrue($parent->isRootNode());
    }

    #[Test]
    public function buildTreeAndGetRootsReturnsExpectedTree(): void
    {
        $roots = TreeNode::buildTreeAndGetRoots($this->treeObjects);

        $this->assertCount(2, $roots);
        $this->assertInstanceOf(TreeNode::class, $roots[0]);
        $this->assertInstanceOf(TreeNode::class, $roots[1]);

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

        return [$root1, $level1Object1, $level1Object2, $level2Object1, $level2Object2, $level2Object3, $root2];
    }
}
