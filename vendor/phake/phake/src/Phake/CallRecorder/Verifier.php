<?php

declare(strict_types=1);

namespace Phake\CallRecorder;

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
 * Can verify calls recorded into the given recorder.
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Verifier
{
    /**
     * @var Recorder
     */
    protected $recorder;

    /**
     * @var \Phake\IMock|class-string
     */
    protected $obj;

    /**
     * @param Recorder $recorder
     * @param \Phake\IMock|class-string $obj
     */
    public function __construct(Recorder $recorder, $obj)
    {
        $this->recorder = $recorder;
        $this->obj      = $obj;
    }

    /**
     * Returns whether or not a call has been made in the associated call recorder.
     *
     * @todo Maybe rename this to findMatchedCalls?
     *
     * @param CallExpectation $expectation
     *
     * @return VerifierResult
     */
    public function verifyCall(CallExpectation $expectation)
    {
        $matcher = new \Phake\Matchers\MethodMatcher($expectation->getMethod(), $expectation->getArgumentMatcher());
        $calls   = $this->recorder->getAllCalls();

        $matchedCalls     = [];
        $methodNonMatched = [];
        $obj_interactions = false;
        foreach ($calls as $call) {
            /* @var $call Call */
            if ($call->getObject() === $expectation->getObject()) {
                $obj_interactions = true;
                $args             = $call->getArguments();
                try {
                    $matcher->assertMatches($call->getMethod(), $args);
                    $callInfo = $this->recorder->getCallInfo($call);
                    assert($callInfo instanceof CallInfo);
                    $matchedCalls[] = $callInfo;
                    $this->recorder->markCallVerified($call);
                } catch (\Phake\Exception\MethodMatcherException $e) {
                    if ($call->getMethod() == $expectation->getMethod()) {
                        $message = $e->getMessageWithComparisonDiff();
                        if (strlen($message)) {
                            $message = "\n{$message}";
                        }
                        $methodNonMatched[] = $call->__toString() . $message;
                    }
                }
            }
        }

        $verifierModeResult = $expectation->getVerifierMode()->verify($matchedCalls);
        if (!$verifierModeResult->getVerified()) {
            $additions = '';
            if (!$obj_interactions) {
                $additions .= ' In fact, there are no interactions with this mock.';
            }

            if (count($methodNonMatched)) {
                $additions .= "\nOther Invocations:\n===\n  " . implode("\n===\n  ", str_replace("\n", "\n  ", $methodNonMatched)) . "\n===";
            }

            return new VerifierResult(
                false,
                [],
                $expectation->__toString() . ', ' . $verifierModeResult->getFailureDescription() . '.' . $additions
            );
        }


        return new VerifierResult(true, $matchedCalls);
    }

    /**
     * @return VerifierResult
     */
    public function verifyNoCalls()
    {
        $result = true;

        $reportedCalls = [];
        foreach ($this->recorder->getAllCalls() as $call) {
            $result          = false;
            $reportedCalls[] = $call->__toString();
        }

        if ($result) {
            return new VerifierResult(true, []);
        }
        $desc = 'Expected no interaction with mock' . "\n"
                . 'Invocations:' . "\n  ";
        return new VerifierResult(false, [], $desc . implode("\n  ", $reportedCalls));
    }

    /**
     * Ensures all calls for this verifier have actually been verified
     *
     * @return VerifierResult
     */
    public function verifyNoOtherCalls()
    {
        $result = true;

        $reportedCalls = [];
        foreach ($this->recorder->getUnverifiedCalls() as $call) {
            $result          = false;
            $reportedCalls[] = $call->__toString();
        }

        if ($result) {
            return new VerifierResult(true, []);
        }
        $desc = 'Expected no interaction with mock' . "\n"
                . 'Invocations:' . "\n  ";
        return new VerifierResult(false, [], $desc . implode("\n  ", $reportedCalls));
    }

    /**
     * @return \Phake\IMock|class-string
     */
    public function getObject()
    {
        return $this->obj;
    }
}
