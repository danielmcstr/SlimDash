<?php
namespace AppMain\Controller;

class ProjectController extends BaseController
{
    public function fetchMyModules($project) 
    {
        $modules = $this->getModules();
        $filtered = []

        foreach ($modules as $module => $value) 
        {
            if (isset($project['modules'][$value]))
            {
                // if module has been excluded, skip module
                $mod = $project['modules'][$value];
                if (isset($mod["excluded"])) {
                    continue;
                }
            }

            $filtered[$module] = $value;
        }

        return $filtered;
    }

    public function getProject($code)
    {
        // get all project, validate user has access to project
        $projs = $this->fetchProjects();

        if (isset($projs[$code]) && isset($projs[$code]['members'][$this->jwt->sub])) 
        {
            $project = $projs[$code];
            $filtered = $this->filterMyModules($project)

            $this->render('@theme/project.html', [
                "code" => $code, 
                "project" => json_encode($project),
                "modules" => json_encode($filtered),
            ]);
        }

        // redirect dashboard if user does not have access to project
        return $this->response->withRedirect("/");
    }


    public function getProjectModule($code, $module)
    {
        // get all project, validate user has access to project
        $projs = $this->fetchProjects();

        if (isset($projs[$code]) && isset($projs[$code]['members'][$this->jwt->sub])) 
        {
            $project = $projs[$code];
            $filtered = $this->filterMyModules($project)

            $this->render("/view/$module/index.html", [
                "code" => $code, 
                "project" => json_encode($project),
                "modules" => json_encode($filtered),
                "module" => json_encode($filtered[$module]),
            ]);
        }

        // redirect to project home if user does not have access to module
        return $this->response->withRedirect("/project/$code");
    }
}
