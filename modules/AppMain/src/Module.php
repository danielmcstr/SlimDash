<?php
namespace AppMain;
use SlimDash\Core;

class Module extends \SlimDash\Core\SlimDashModule {
	/**
	 * {@inheritdoc}
	 */
	public function initDependencies(\SlimDash\Core\SlimDashApp $app) {
		$container = $app->getContainer();
		$settings = $container->get('settings');

		// view renderer
		$container['renderer'] = function ($c) {
			// we will add folders after instatiation so that we can assign IDs
			$settings = $c->get('settings')['renderer'];
			$folders = $settings['folders'];
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

			$view = new \Slim\Views\Twig($folders, [
				//'cache' => $c->get('settings')['renderer']['cache'],
			]);

			// Instantiate and add Slim specific extension
			$basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');

			// Twig extension
			$view->addExtension(new \Slim\Views\TwigExtension($c['router'], $basePath));
			$view->addExtension(new \Twig_Extension_Debug());

			// Add the TranslationExtension to the view
			$view->addExtension(new \Symfony\Bridge\Twig\Extension\TranslationExtension($translator));

			return $view;
		};

		// monolog
		$container['logger'] = function ($c) {
			$settings = $c->get('settings')['Applogger'];
			$logger = new \Monolog\Logger($settings['name']);
			$logger->pushProcessor(new \Monolog\Processor\UidProcessor());
			$logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
			return $logger;
		};

		$container['session'] = function ($c) {
			$settings = $c->get('settings')['session'];

			$session_factory = new \Aura\Session\SessionFactory;
			$session = $session_factory->newInstance($_COOKIE);

			return $session->getSegment($settings['namespace']);
		};
	}

	/**
	 * {@inheritdoc}
	 */
	public function initMiddlewares(\SlimDash\Core\SlimDashApp $app) {
	}

	/**
	 * {@inheritdoc}
	 */
	public function initRoutes(\SlimDash\Core\SlimDashApp $app) {
		$jwtAuth = new FirebaseAuth([
			"path" => "/",
			"passthrough" => [
				"/app-main/login",
				"/app-main/auth/firebase"],
			"cookie" => env('AUTH_COOKIE', 'myfbtk'),
			"attribute" => "jwt",
			"error" => function ($request, $response, $arguments) {
				$data["status"] = "error";
				$data["message"] = $arguments["message"];
				if ($data["message"] == 'Token not found') {
					return $response->withRedirect('/app-main/login', 403);
				}
				return $response
					->withHeader("Content-Type", "application/json")
					->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
			},
		]);

		$app->add($jwtAuth);
		// set default url right now
		$ctrl = \AppMain\Controller\HomeController::class;
		// var_dump($ctrl);
		$app->route(['get'], '/', $ctrl, 'Dashboard')->setName('home');
		$app->route(['get'], '/app-main/login', $ctrl, 'Login')->setName('login');
		$app->route(['get'], '/app-main/logout', $ctrl, 'Logout')->setName('logout');
		$app->route(['get'], '/app-main/auth/firebase/', $ctrl, 'AuthFirebase')->setName('auth-firebase');
		$app->route(['get'], '/app-main/dash', $ctrl, 'Dashboard')->setName('dash');
	}
}