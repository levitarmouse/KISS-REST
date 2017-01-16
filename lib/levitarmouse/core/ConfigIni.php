<?php
/**
 * ConfigIni
 *
 * @category  CORE
 * @package   Core
 * @created   2014
 * @author    Gabriel Prieto <gab307@gmail.com>
 * @copyright 2012 Levitarmouse
 * @license
 * @link
 */

namespace levitarmouse\core;

/**
 * Description of ConfigIni
 *
 * @category  CORE
 * @package   Core
 * @created   2012
 * @author    Gabriel Prieto <gab307@gmail.com>
 * @copyright 2012 Levitarmouse
 * @license
 * @link
 */
class ConfigIni
{
    protected $iniFullFsLocation;
    protected $rawConfiguration;
    protected $configuration;
    protected $configFolder;

    /**
     * Construct
     *
     * @param type $iniFileName Config file name with ini extension
     */
    public function __construct($iniFilePath = '', $analize = false)
    {
        if (!$iniFilePath) {
            return $this;
        }

        $this->iniFullFsLocation = $iniFilePath;

        if ($this->_retrieveConfiguration()) {
            $this->_init($analize);
        }
    }

    /**
     * RetrieveConfiguration
     *
     * @return array
     */
    private function _retrieveConfiguration()
    {
        $result = @$this->rawConfiguration = parse_ini_file($this->iniFullFsLocation, true);

        if (!$result) {
            throw new \Exception(\levitarmouse\rest\Response::INVALID_CONFIGURATION);
        }
        return ($result);
    }

    /**
     * Init
     *
     * @return none
     */
    private function _init($analize = false)
    {
        foreach ($this->rawConfiguration as $section => $config) {
            
            if ($analize) {
                $oConfig = new SmartObject($config);
            } else {
                $oConfig = new Object();                
            }
            
            foreach ($config as $attrib => $value) {
                $oConfig->$attrib = $value;
            }
            $this->configuration[$section] = $oConfig;
        }
    }
    
//    public function scanForObjects($sectionName = '') {
//        
//        $aConfigurations = $this->configuration;
//        
//        if (!$aConfigurations) {
//            return false;
//        }
//        
//        $prospect = null;
//        foreach ($aConfigurations as $section => $param) {
//
//            if ($sectionName) {
//                if (strtoupper($sectionName) == strtoupper($section)) {
//                    $prospect = $this->get($section, true);                    
//                }
//            } else {
//                $prospect = $this->get($section, true);                
//            }
//        }
//        
//        $section = array();
//        if ($prospect) {
//            foreach ($prospect as $key => $velue) {
//                if (is_string($velue)) {
//                    $object = json_decode($prospect);
//                    if (is_object($object)) {
////                        $
//                    }
//                }
//            }
//        }
//    }

    /**
     * GetConstant
     *
     * @param type $constant Constant Name
     *
     * @return type
     */
    public function getConstant($constant = '')
    {
        $value = (defined($constant)) ? constant($constant) : null;
        return $value;
    }

    /**
     * Get
     *
     * @param type $configName ConfigName
     * @param type $asArray    AsArray
     *
     * @return type
     */
    public function get($configName = '', $asArray = false)
    {
        if ($configName) {
            self::_init();

            $aSteps = explode('.', $configName);

            $seccion = (isset($aSteps[0])) ? $aSteps[0] : null;
//            $param   = (isset($aSteps[1])) ? strtoupper($aSteps[1]) : null;
            $param   = (isset($aSteps[1])) ? $aSteps[1] : null;

            $aConfigurations = $this->configuration;
            if ($seccion
                && $param
                && isset($aConfigurations[$seccion])
            ) {
                $value = $aConfigurations[$seccion]->$param;
                return $value;
            }
            if ($seccion
                && !$param
                && isset( $aConfigurations[$seccion] )
            ) {
                $seccion = $aConfigurations[$seccion];

                if ($asArray) {
                    $aSeccion = $seccion->getAttribs();

                    return $aSeccion;
                }
                return $seccion;
            }
        }
        return null;
    }
}