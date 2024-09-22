<?php

declare(strict_types=1);

namespace Phake\Matchers;

/*
 * Phake - Mocking Framework
 *
 * Copyright (c) 2010-2022, Mike Lively <m@digitalsandwich.com>
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

/**
 * An argument collection matcher that can match 0 to many parameters
 */
interface IChainableArgumentMatcher
{
    /**
     * Assert the matcher on a given list of argument values. Throws an exception if the matcher doesn't match
     *
     * @param array $arguments
     *
     * @throw Exception
     * @return void
     */
    public function doArgumentsMatch(array &$arguments);

    /**
     * returns the next matcher in the chain
     *
     * @return IChainableArgumentMatcher|null
     */
    public function getNextMatcher();

    /**
     * Sets the next matcher in the chain.
     *
     * @param IChainableArgumentMatcher $matcher
     * @throws \InvalidArgumentException when the given matcher cannot be chained to this matcher
     * @return void
     */
    public function setNextMatcher(IChainableArgumentMatcher $matcher);

    /**
     * Asserts whether or not this matcher can be chained to the the given matcher
     *
     * @param IChainableArgumentMatcher $matcher
     * @throws \InvalidArgumentException When this matcher cannot be chained to the previous matcher.
     * @return void
     */
    public function assertPreviousMatcher(IChainableArgumentMatcher $matcher);

    /**
     * Returns a human readable description of the argument matcher
     * @return string
     */
    public function __toString();
}
