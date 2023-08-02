<?php

declare(strict_types=1);

namespace VsWest\Framework\Log;

use Hyperf\context\ApplicationContext;

class Log
{
    public static function get(string $name = 'app')
    {
        return ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get($name);
    }
}
