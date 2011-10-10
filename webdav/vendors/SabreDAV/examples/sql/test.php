<?php

$r = '/^([^_-]+)((-[^_]+)?)_([^#]+)#calendar-proxy-(read|write)$/';

preg_match($r, 'aaa-bbb_ccc#calendar-proxy-read', $matches);
print_r($matches);

preg_match($r, 'aaa_ccc#calendar-proxy-read', $matches);
print_r($matches);
