<?php

declare(strict_types=1);

namespace Phake\Annotation;

/*
 * Phake - Mocking Framework
 *
 * Copyright (c) 2010-2022, Mike Lively <mike.lively@sellingsource.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *  *  Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *  *  Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *  *  Neither the name of Mike Lively nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Testing
 * @package    Phake
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2010 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.digitalsandwich.com/
 */

use PHPUnit\Framework\TestCase;

class NativeReaderTest extends TestCase
{
    private $reader;

    #[\Phake\Mock]
    private $mock;

    #[\Phake\Mock(self::class)]
    private $mockWithOrderedType;

    #[\Phake\Mock(class: self::class)]
    private $mockWithNamedType;

    #[\Phake\Mock(foo: self::class)]
    private $mockWithNoType;

    protected function setUp(): void
    {
        $this->reader = new NativeReader();
    }

    public function testGettingPropertiesWithMockAnnotations()
    {
        $properties = array_map(
            function($p) { return $p->getName(); },
            $this->reader->getPropertiesWithMockAnnotation(new \ReflectionClass($this))
        );

        $expectedProperties = [
            'mock',
            'mockWithOrderedType',
            'mockWithNamedType',
            'mockWithNoType',
        ];

        $this->assertSame($expectedProperties, $properties);
    }

    public static function getMockTypeDataProvider()
    {
        yield 'no type' => [ null, 'mock' ];
        yield 'mock ordered type' => [ self::class, 'mockWithOrderedType' ];
        yield 'mock named type' => [ self::class, 'mockWithNamedType' ];
        yield 'no type 2' => [ null , 'mockWithNoType' ];
    }

    /**
     * @dataProvider getMockTypeDataProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getMockTypeDataProvider')]
    public function testGettingMockType(?string $expectedType, string $propertyName)
    {
        $this->assertSame($expectedType, $this->reader->getMockType(new \ReflectionProperty($this, $propertyName)));
    }
}
