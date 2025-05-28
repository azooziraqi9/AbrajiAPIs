<?php

namespace Modules\Dashboard\Abstract;

use PhpParser\Builder\Interface_;

interface DashboardServiceInterface
{
    public function GetDashboard($authorization);

}
