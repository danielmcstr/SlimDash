<?php

namespace AppMain\Controller;

class BaseController extends \SlimDash\Core\SlimDashController
{
	public function fetchModules() 
	{
		// Base endpoint
        $base = 'https://brick-admin.firebaseio.com';

        // Auth token
        $token = $_COOKIE[env('AUTH_COOKIE', 'myfbtk')];

        // get list of modules
        $rsp = $this->execJsonRequest($base . '/modules.json', [], ["auth" => $token]); 
        return $rsp["body"];
	}

	public function fetchProjects() 
	{
		// Base endpoint
        $base = 'https://brick-admin.firebaseio.com';

        // Auth token
        $token = $_COOKIE[env('AUTH_COOKIE', 'myfbtk')];

        // get list of projects
        $rsp = $this->execJsonRequest($base . '/projects.json', [], ["auth" => $token]); 
        return $rsp["body"];
	}
}