<?php
/*
 * ========================================================================
 * Copyright (c) 2011 Vladislav "FractalizeR" Rastrusny
 * Website: http://www.fractalizer.ru
 * Email: FractalizeR@yandex.ru
 * ------------------------------------------------------------------------
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================================
 */

namespace FractalizeR\Smarty;

use Silex\Application;
use FractalizeR\Smarty\ServiceProvider as SmartyServiceProvider;

use Symfony\Component\HttpFoundation\Request;

/**
 * SmartyExtension test cases
 *
 * Smarty 3.0 demo files should be corrected for tests to pass when PHPUnit is run in CLI mode:
 *
 * - delete line with {popup_init} from Smarty\demo\templates\header.tpl (should be done already from Smarty 3.0.8 onwards)
 *
 * @author Vladislav Rastrusny aka FractalizeR <FractalizeR@yandex.ru>
 */
class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string Path to smarty distribution
     */
    private $smartyPath;

    public function setUp()
    {
        $this->smartyPath = realpath(__DIR__ . '/../../../../vendor/Smarty/smarty');
        if (!isset($_SERVER['SERVER_NAME'])) {
            $_SERVER['SERVER_NAME'] = 'Value for Smarty demo';
        }

        if (!is_dir($this->smartyPath)) {
            $this->markTestSkipped('Smarty submodule was not installed.');
        }
    }

    public function testRegisterAndRender()
    {
        $app = new Application();
        $app['debug'] = true;

        $app->register(new SmartyServiceProvider(), array(
            'smarty.dir' => $this->smartyPath,
            'smarty.options' => array(
                'setTemplateDir' => $this->smartyPath . '/demo/templates',
                'setCompileDir' => $this->smartyPath . '/demo/templates_c',
                'setConfigDir' => $this->smartyPath . '/demo/configs',
                'setCacheDir' => $this->smartyPath . '/demo/cache',
            )
        ));

        $app->get('/hello', function() use ($app) {
            /** @var \Smarty $smarty  */
            $smarty = $app['smarty'];
            $smarty->debugging = true;
            $smarty->caching = true;
            $smarty->cache_lifetime = 120;

            $smarty->assign("Name", "Fred Irving Johnathan Bradley Peppergill", true);
            $smarty->assign("FirstName", array("John", "Mary", "James", "Henry"));
            $smarty->assign("LastName", array("Doe", "Smith", "Johnson", "Case"));
            $smarty->assign("Class", array(
                array("A", "B", "C", "D"),
                array("E", "F", "G", "H"),
                array("I", "J", "K", "L"),
                array("M", "N", "O", "P")
            ));

            $smarty->assign("contacts", array(
                array("phone" => "1", "fax" => "2", "cell" => "3"),
                array("phone" => "555-4444", "fax" => "555-3333", "cell" => "760-1234")
            ));

            $smarty->assign("option_values", array("NY", "NE", "KS", "IA", "OK", "TX"));
            $smarty->assign("option_output", array("New York", "Nebraska", "Kansas", "Iowa", "Oklahoma", "Texas"));
            $smarty->assign("option_selected", "NE");

            return $smarty->fetch('index.tpl');
        });

        $request = Request::create('/hello');

        $response = $app->handle($request);
        $htmlContent = $response->getContent();
        $this->assertGreaterThan(7000, strlen($htmlContent));
        $this->assertNotRegExp('/Whoops/', $htmlContent);
    }
}
