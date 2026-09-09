<?php

class MagicPoint
{
    /**
     * @param list<int> $maxMagicPointIncrements
     */
    public function recover(int $currentMagicPoint, int $originalMagicPoint, array $maxMagicPointIncrements, int $recoveryAmount): int
    {
        $currentMaxMagicPoint = $originalMagicPoint;
        foreach ($maxMagicPointIncrements as $increment) {
            $currentMaxMagicPoint += $increment;
        }

        return min($currentMaxMagicPoint, $currentMagicPoint + $recoveryAmount);
    }
}