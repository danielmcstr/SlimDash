<?php
namespace AppMain\Controller;

class HomeController extends \SlimDash\Core\SlimDashController
{
    public function getLogin()
    {
        $this->render('@theme/login.html');
    }

    public function getLogout()
    {
        setcookie(env('AUTH_COOKIE', 'myfbtk'), null, -1, '/');
        $this->render('@theme/logout.html');
    }

    public function getDashboard()
    {
        // Base endpoint
        $base = 'https://brick-admin.firebaseio.com';

        // Auth token
        $token = $_COOKIE[env('AUTH_COOKIE', 'myfbtk')];

        // get list of projects
        $rsp = $this->execJsonRequest($base . '/projects.json', [], ["auth" => $token]); 
        $projs = $rsp["body"];
        $filtered = [];


        // filter out projects user does not have permission to
        foreach ($projs as $project => $value) {
            if (isset($value["members"][$this->jwt->sub])){
                $filtered[$project] = $value;
            }
        } 
        
        $this->render('@theme/main.html', ["projects" => json_encode($filtered)]);
    }

    public function getAuthFirebase()
    {
        $token = $this->queryParam('token');
        setcookie(env('AUTH_COOKIE', 'myfbtk'), $token, time() + 3600, '/');

        // redirect to dashboard
        return $this->response->withRedirect('/main');
    }
}
