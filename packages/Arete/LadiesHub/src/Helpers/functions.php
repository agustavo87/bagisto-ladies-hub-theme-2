<?php

declare(strict_types = 1);

namespace Arete\LadiesHub\Helpers;


function difference () {
    static $start = null;
    if(!$start) $start = now();
    return $start->diff(now());
}
