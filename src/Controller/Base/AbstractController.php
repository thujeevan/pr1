<?php
namespace pr1\Controller\Base;

use Silex\Application;
use Silex\ControllerProviderInterface;

/**
 * This class contains base controller class
 * 
 * @author Thujeevan Thurairajah
 */
abstract class AbstractController implements ControllerProviderInterface {

    /**
     * Implementation of abstract connect method
     *
     * @param \Silex\Application $app
     * @return mixed controllers_factory
     */
    public abstract function connect(Application $app);
}
