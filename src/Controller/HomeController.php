<?php
namespace AppMain\Controller;

class HomeController extends BaseController
{
    public function getLogin()
    {
        $this->render('@theme/login.html');
    }

    public function getLogout()
    {
        setcookie(getenv('AUTH_COOKIE'), null, -1, '/');
        $this->render('@theme/logout.html');
    }

    public function getDashboard()
    {
        $projs = $this->fetchProjects();
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
        setcookie(getenv('AUTH_COOKIE'), $token, time() + 3600, '/');

        // redirect to dashboard
        return $this->response->withRedirect('/');
    }
}
