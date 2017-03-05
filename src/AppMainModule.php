<?php
namespace AppMain;

use SlimDash\Core;

class AppMainModule extends \SlimDash\Core\SlimDashModule
{
    /**
     * {@inheritdoc}
     */
    public function initDependencies(\SlimDash\Core\SlimDashApp $app)
    {
        $container = $app->getContainer();
        $settings  = $container->get('settings');

        // view renderer
        $container['renderer'] = function ($c) {
            // we will add folders after instatiation so that we can assign IDs
            $settings = $c->get('settings')['renderer'];
            $folders  = $settings['folders'];
            unset($settings['folders']);

            // First param is the "default language" to use.
            $translator = new \Symfony\Component\Translation\Translator($c->get('settings')['language'], new \Symfony\Component\Translation\MessageSelector());

            // Set a fallback language incase you don't have a translation in the default language
            $translator->setFallbackLocales([$c->get('settings')['language']]);

            // Add a loader that will get the php files we are going to store our translations in
            $translator->addLoader('php', new \Symfony\Component\Translation\Loader\PhpFileLoader());
            // Add language files here
            $translator->addResource('php', APP_PATH . '/lang/en.php', 'en'); // English
            $translator->addResource('php', APP_PATH . '/lang/es_ES.php', 'es_ES'); // Spanish

            $twigExtra = [];
            if (isset($settings['cache'])) {
                $parms['cache'] = $settings['cache'];
            }

            $view = new \Slim\Views\Twig($folders, $twigExtra);

            // Instantiate and add Slim specific extension
            $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');

            // Twig extension
            $view->addExtension(new \Slim\Views\TwigExtension($c['router'], $basePath));
            $view->addExtension(new \Twig_Extension_Debug());

            // Add the TranslationExtension to the view
            $view->addExtension(new \Symfony\Bridge\Twig\Extension\TranslationExtension($translator));

            $twig = $view->getEnvironment();
            $lexer = new \Twig_Lexer($twig, array(
                'tag_comment'   => array('[#', '#]'),
                'tag_block'     => array('[%', '%]'),
                'tag_variable'  => array('[[', ']]'),
                'interpolation' => array('#[', ']'),
            ));
            $twig->setLexer($lexer);
            return $view;
        };

        // monolog
        $container['logger'] = function ($c) {
            $settings = $c->get('settings')['logger'];
            $logger   = new \Monolog\Logger($settings['name']);
            $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
            $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
            return $logger;
        };

        $container['session'] = function ($c) {
            $settings = $c->get('settings')['session'];

            $session_factory = new \Aura\Session\SessionFactory;
            $session         = $session_factory->newInstance($_COOKIE);

            return $session->getSegment($settings['namespace']);
        };

        $container["jwt"] = function ($container) {
            return new StdClass;
        };
    }

    /**
     * {@inheritdoc}
     */
    public function initMiddlewares(\SlimDash\Core\SlimDashApp $app)
    {
        // check for roles
    }

    /**
     * {@inheritdoc}
     */
    public function initRoutes(\SlimDash\Core\SlimDashApp $app)
    {
        $container = $app->getContainer();

        $jwtAuth = new \SlimDash\Core\FirebaseAuth([
            "path"        => "/",
            "passthrough" => [
                "/login",
                "/auth/firebase"
            ],
            "cookie"      => env('AUTH_COOKIE', 'myfbtk'),
            "attribute"   => "jwt",
            "error"       => function ($request, $response, $arguments) {
                $data["status"]  = "error";
                $data["message"] = $arguments["message"];
                if ($data["message"] == 'Token not found') {
                    return $response->withRedirect('/login', 403);
                }
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            },
            "callback"    => function ($request, $response, $arguments) use ($container) {
                $container["jwt"] = $arguments["decoded"];
            }
        ]);

        $app->add($jwtAuth);
        // set default url right now
        $ctrl = \AppMain\Controller\HomeController::class;

        // var_dump($ctrl);
        $app->route(['get'], '/', $ctrl, 'Dashboard')->setName('home');
        $app->route(['get'], '/login', $ctrl, 'Login')->setName('login');
        $app->route(['get'], '/logout', $ctrl, 'Logout')->setName('logout');
        $app->route(['get'], '/auth/firebase/', $ctrl, 'AuthFirebase')->setName('auth-firebase');
        $app->route(['get'], '/main', $ctrl, 'Dashboard')->setName('dash');

        $ctrl = \AppMain\Controller\ProjectController::class;
        // projects and modules
        $app->route(['get'], '/project/{code}', $ctrl, 'Project')->setName('manage-project');
        $app->route(['get'], '/project/{code}/{module}', $ctrl, 'ProjectModule')->setName('manage-project-module');
    }
}
