<?php

declare(strict_types=1);

namespace Test;

use Doctrine\Common\Collections\ArrayCollection;

final class AppCode
{
    public function execute() : void
    {
        $sliced = $this->createArrayCollection()
            ->slice(0, 1);

        $sliced = (new MyCollection())
            ->slice(0, 2);

        $sliced = (new MyArrayCollection())
            ->slice(0, 3);
    }

    private function createArrayCollection() : ArrayCollection
    {
        return new ArrayCollection([0, 1, 2, 3]);
    }
}
