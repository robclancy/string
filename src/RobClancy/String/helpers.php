<?php

if ( ! function_exists('str'))
{
	function str($string) { return new RobClancy\String\String($string); }
}