<?php

declare(strict_types=1);

class PhakeTest_ReturnByReferenceMethodClass
{
    private $something = [];

    /**
     * Returns the something array by reference.
     *
     * @return array
     */
    public function &getSomething()
    {
        return $this->something;
    }
}
