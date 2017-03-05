<?php
namespace AppMain\Controller;

class ProjectController extends BaseController
{
    public function getProject($code)
    {
        /*
        // Base endpoint
        $base = 'https://brick-admin.firebaseio.com';

        // Auth token
        $token = $_COOKIE[env('AUTH_COOKIE', 'myfbtk')];

        // get list of modules
        $rsp = $this->execJsonRequest($base . '/modules.json', [], ["auth" => $token]); 
        $projs = $rsp["body"];
        $filtered = [];

        // filter out modules user does not have permission to
        foreach ($projs as $project => $value) {
            if (isset($value["members"][$this->jwt->sub])){
                $filtered[$project] = $value;
            }
        } 
        
        $this->render('@theme/project.html', ["modules" => json_encode($filtered)]);
        */

        // get project, validate user has access to project
        $this->render('@theme/project.html', ["code" => $code]);
    }


    public function getProjectModule($code, $module)
    {
        $this->render("/view/module/$module/index.html", ["code" => $code]);
    }
}
