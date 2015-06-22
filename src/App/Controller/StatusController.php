<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatusController implements ControllerProviderInterface
{
    /**
     * Note: please do not use such code for real applications :).
     *
     * @param Application $app
     *
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        /** @var $controllers */
        $controllers = $app['controllers_factory'];
        $controllers->get('/', function (Application $app, Request $request) {
            $output = '';
            $status = 200;

            try {
                /** @var Connection $database */
                $database = $app['db'];
                $database->executeQuery('CREATE TABLE IF NOT EXISTS hits (hitTime DATETIME NOT NULL DEFAULT NOW(), ip TEXT)');

                $database->insert('hits', array(
                    'ip' => $request->getClientIp(),
                ));

                $result = $database->fetchAssoc('SELECT COUNT(*) as number FROM hits');
                $output .= '<h1>'.$result['number'].' hits !</h1>';
                $output .= '<p>Current server: '.$_SERVER['SERVER_ADDR'].'</p>';

                $lastHits = $database->fetchAll('SELECT * FROM hits ORDER BY hits.hitTime DESC LIMIT 10');
                $output .= '<h2>Last 10 hits</h2>';
                $output .= '<table><thead><tr><th>Datetime</th><th>IP</th></tr></thead><tbody>';
                foreach ($lastHits as $hit) {
                    $output .= '<tr><td>'.$hit['hitTime'].'</td><td>'.$hit['ip'].'</td></tr>';
                }

                $output .= '</tbody></table>';
                $output .= '<h1>Server</h1>';
                $output .= '<pre>'.print_r($_SERVER, true).'</pre>';
            } catch (\Exception $e) {
                $output .= '<p>[EXCEPTION] '.$e->getMessage().'</p>';
                $output .= '<pre>'.$e->getTraceAsString().'</pre>';
                $output .= '<hr />';
                $output .= '<pre>'.print_r($app['db.options'], true).'</pre>';
                $output .= '<hr />';
                $output .= '<pre>'.print_r($_ENV, true).'</pre>';
                $output .= '<hr />';
                $output .= '<pre>'.print_r($_SERVER, true).'</pre>';

                $status = 500;
            }

            return new Response($output, $status);
        });

        return $controllers;
    }
}
