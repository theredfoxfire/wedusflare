<?php
//wedusflare framework

namespace Calendar\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Calendar\Model\LeapYear;

class LeapYearController{
	public function indexAction(Request $request, $year){
		$leapyear = new LeapYear();
		if ($leapyear->isLeapYear($year)){
			$response = new Response('Yep '.$year.', this is a leap year!'.rand());
		} else {
			$response = new Response('Nope '.$year.', this is not a leap year!');
		}
		
		$date = date_create_from_format('Y-m-d H:i:s', '2005-10-15 10:00:00');
		$response->setPublic();
		$response->setEtag('abcde');
		$response->setLastModified($date);
		$response->setMaxAge(10);
		$response->setSharedMaxAge(10);
		
		return $response;
		
	}
}
