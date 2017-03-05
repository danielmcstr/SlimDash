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
        $this->render('@theme/main.html');
    }

    public function getAuthFirebase()
    {
        $token = $this->queryParam('token');
        setcookie(env('AUTH_COOKIE', 'myfbtk'), $token, time() + 3600, '/');

        // redirect to dashboard
        return $this->response->withRedirect('/main');
    }
}
