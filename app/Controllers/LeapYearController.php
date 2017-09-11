<?php
namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\LeapYear;

class LeapYearController
{
    public function indexAction(Request $request, $year)
    {
        $leapyear = new LeapYear();
        if ($leapyear->isLeapYear($year)) {
            $response = new Response($year.'是闰年!'.'现在时间是:'.date('Y-m-d H:i:s', time()).rand());
        } else {
            $response = new Response($year.'不是闰年!'.'现在时间是:'.date('Y-m-d H:i:s', time()).rand());
        }

        return $response;
    }
}
