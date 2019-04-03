<?php

namespace Common\Traits;

trait PercentTrait
{
    protected function percent(int $complete, int $remaining): array
    {
        $total = $complete + $remaining;
        if ($total > 0) {
            $percent = ($complete or $remaining) ? round($complete / $total * 100) : 0;
            return [
                'total' => $total,
                'complete' => $complete,
                'remaining' => $remaining,
                'percent' => $percent
            ];
        }
        return [];
    }
}
