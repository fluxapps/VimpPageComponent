<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Class ilVimpPageComponentPlugin
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ilVimpPageComponentPlugin extends ilPageComponentPlugin {

    const PLUGIN_NAME = 'VimpPageComponent';

    /**
     * Get plugin name
     *
     * @return string
     */
    function getPluginName()
    {
        return "VimpPageComponent";
    }


    /**
     * Get plugin name
     *
     * @return string
     */
    function isValidParentType($a_parent_type)
    {
        return true;
    }

    /**
     * Get Javascript files
     */
    function getJavascriptFiles($a_mode)
    {
	    return array('./Customizing/global/plugins/Services/Repository/RepositoryObject/ViMP/vendor/video-js-6.4.0/video.min.js');
    }

    /**
     * Get css files
     */
    function getCssFiles($a_mode)
    {
        return array('./Customizing/global/plugins/Services/Repository/RepositoryObject/ViMP/vendor/video-js-6.4.0/video-js.min.css');
    }

} 

