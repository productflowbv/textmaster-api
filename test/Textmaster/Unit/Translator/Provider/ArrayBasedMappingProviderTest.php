<?php

/*
 * This file is part of the Textmaster Api v1 client package.
 *
 * (c) Christian Daguerre <christian@daguer.re>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Textmaster\Unit\Translator\Provider;

use PHPUnit\Framework\TestCase;
use Textmaster\Exception\MappingNotFoundException;
use Textmaster\Translator\Provider\ArrayBasedMappingProvider;
use Textmaster\Unit\Mock\MockTranslatable;

class ArrayBasedMappingProviderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetProperties()
    {
        $subjectMock = new MockTranslatable();
        $mappings = [
            'Textmaster\Unit\Mock\MockTranslatable' => ['name'],
        ];

        $provider = new ArrayBasedMappingProvider($mappings);
        $properties = $provider->getProperties($subjectMock);

        $this->assertSame(['name'], $properties);
    }

    /**
     * @test
     */
    public function shouldNotSetWrongActivity()
    {
        $this->expectException(MappingNotFoundException::class);
        $subjectMock = new MockTranslatable();
        $mappings = [
            'Textmaster\Unit\Mock\WrongClass' => ['name'],
        ];

        $provider = new ArrayBasedMappingProvider($mappings);
        $provider->getProperties($subjectMock);
    }
}
