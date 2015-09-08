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

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['smarty'] = function () use ($app) {
            if (!class_exists('Smarty') and !isset ($app['smarty.dir'])) {
                throw new \RuntimeException("Smarty class is not loaded and 'smarty.dir' is not defined. Please provide this option to Application->register() call.");
            }

            if (!class_exists('Smarty') and isset ($app['smarty.dir'])) {
                require_once($app['smarty.dir'] . '/libs/Smarty.class.php');
            }

            $smarty = isset($app['smarty.instance']) ? $app['smarty.instance'] : new \Smarty();
            if (isset($app["smarty.options"])) {
                foreach ($app["smarty.options"] as $smartyOptionName => $smartyOptionValue) {
                    $smarty->$smartyOptionName($smartyOptionValue);
                }
            }

            $smarty->assign("app", $app);

            if (isset($app['smarty.configure'])) {
                $app['smarty.configure']($smarty);
            }

            return $smarty;
        };
    }
}
