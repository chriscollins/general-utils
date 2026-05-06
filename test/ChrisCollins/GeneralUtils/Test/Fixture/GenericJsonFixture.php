<?php

declare(strict_types=1);

namespace ChrisCollins\GeneralUtils\Test\Fixture;

/**
 * GenericJsonFixture
 *
 * Test fixture to provide example generic JSON for testing JSON encoding/decoding.
 */
class GenericJsonFixture extends AbstractJsonLoadingFixture
{
    /**
     * {@inheritdoc}
     */
    protected function getJsonDirectory()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'GenericJson';
    }
}
