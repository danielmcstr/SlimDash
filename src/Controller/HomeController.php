<?php
namespace AppMain\Controller;

class HomeController extends \SlimDash\Core\SlimDashController
{
    public function getLogin()
    {
        $this->render('@theme/auth/login');
    }
    public function getLogout()
    {
        setcookie(env('AUTH_COOKIE', 'myfbtk'), null, -1, '/');
        $this->render('@theme/auth/logout');
    }
    public function getDashboard()
    {
        $this->render('@theme/home');
    }
    public function getAuthFirebase()
    {
        $token = $this->queryParam('token');
        setcookie(env('AUTH_COOKIE', 'myfbtk'), $token, time() + 3600, '/');

        // redirect to dashboard
        return $this->response->withRedirect('/app-main/dash');
    }
}
