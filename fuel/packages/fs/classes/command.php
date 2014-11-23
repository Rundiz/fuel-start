<?php

namespace Fs;

/**
 * FuelStart CLI command
 * 
 * @author Vee W.
 * 
 * The FuelStart Fs cli was copied from FuelPhp Oil.
 */
class Command
{


    protected static function _clear_args($actions = array())
    {
        foreach ($actions as $key => $action)
        {
            if (substr($action, 0, 1) === '-')
            {
                unset($actions[$key]);
            }

            // get rid of any junk added by Powershell on Windows...
            isset($actions[$key]) and $actions[$key] = trim($actions[$key]);
        }

        return $actions;
    }// _clear_args


    public static function help()
    {
        echo <<<HELP
Usage:
php fs [g|generate] [module_name]

[generate] is generete the module.

[module_name] is the module name. It should be StudlyCaps as PSR-1 specified.

Description:
The 'fs' is FuelStart command, It will be use for generate module that is ready to code.

HELP;
    }// help


    public static function init($args)
    {
        // Remove flag options from the main argument list
        $args = self::_clear_args($args);
        
        try {
            if (!isset($args[1])) {
                static::help();
                return false;
            }
            
            switch ($args[1]) {
                case 'g':
                case 'generate':
                    if (!isset($args[2])) {
                        \Cli::error('Please enter module name.');
                        \Cli::beep();
                        return false;
                    }
                    
                    call_user_func('Fs\Generate::module', array_slice($args, 2));
                    break;
                default:
                    static::help();
            }
        } catch (\Exception $e) {
            static::print_exception($e);
            exit(1);
        }// end try catch
    }// init


    protected static function print_exception(\Exception $ex)
    {
        \Cli::error('Uncaught exception '.get_class($ex).': '.$ex->getMessage());
        if (\Fuel::$env != \Fuel::PRODUCTION)
        {
            \Cli::error('Callstack: ');
            \Cli::error($ex->getTraceAsString());
        }
        \Cli::beep();
        \Cli::option('speak') and `say --voice="Trinoids" "{$ex->getMessage()}"`;

        if (($previous = $ex->getPrevious()) != null)
        {
            \Cli::error('');
            \Cli::error('Previous exception: ');
            static::print_exception($previous);
        }
    }


}