<?php

return [
    'detector_class' => \Enflow\OutdatedBrowser\Detector\UserAgentDetector::class,
    'memory_class' => \Enflow\OutdatedBrowser\Memory\CookieMemory::class,
    'inspector_class' => \Enflow\OutdatedBrowser\Inspector\FirstUserRequest::class,

    'blocked_user_agent_regexes' => [
        '~MSIE|Internet Explorer~i',
        '~Trident/6.0(.*)?~i',
    ],
];
