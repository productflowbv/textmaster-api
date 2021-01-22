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
use Textmaster\Translator\Provider\ChainedMappingProvider;
use Textmaster\Unit\Mock\MockTranslatable;

class ChainedMappingProviderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetProperties()
    {
        $providerMock1 = $this->createMock('Textmaster\Translator\Provider\MappingProviderInterface');
        $providerMock2 = $this->createMock('Textmaster\Translator\Provider\MappingProviderInterface');
        $providers = [$providerMock1, $providerMock2];

        $subjectMock = new MockTranslatable();

        $providerMock1->expects($this->once())
            ->method('getProperties')
            ->will($this->throwException(new MappingNotFoundException('value')));

        $providerMock2->expects($this->once())
            ->method('getProperties')
            ->willReturn(['name']);

        $provider = new ChainedMappingProvider($providers);
        $properties = $provider->getProperties($subjectMock);

        $this->assertSame(['name'], $properties);
    }

    /**
     * @test
     */
    public function shouldNotGetProperties()
    {
        $this->expectException(MappingNotFoundException::class);
        $providerMock1 = $this->createMock('Textmaster\Translator\Provider\MappingProviderInterface');
        $providerMock2 = $this->createMock('Textmaster\Translator\Provider\MappingProviderInterface');
        $providers = [$providerMock1, $providerMock2];

        $subjectMock = new MockTranslatable();

        $providerMock1->expects($this->once())
            ->method('getProperties')
            ->will($this->throwException(new MappingNotFoundException('value')));

        $providerMock2->expects($this->once())
            ->method('getProperties')
            ->will($this->throwException(new MappingNotFoundException('value')));

        $provider = new ChainedMappingProvider($providers);
        $provider->getProperties($subjectMock);
    }
}
