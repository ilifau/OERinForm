<?php
// Copyright (c) 2018 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

/**
 * OERinForm plugin config class
 *
 * @author Fred Neumann <fred.neumann@ili.fau.de>
 *
 */
class ilOERinFormConfig
{
	/**
	 * @var ilOERinFormParam[]	$params		parameters: 	name => ilOERinFormParam
	 */
	protected $params = array();

	/**
	 * Constructor.
	 * @param ilPlugin|string $a_plugin_object
	 */
	public function __construct($a_plugin_object = "")
	{
		$this->plugin = $a_plugin_object;
		$this->plugin->includeClass('class.ilOERinFormParam.php');

		/** @var ilOERinFormParam[] $params */
		$params = array();

        $params[] = ilOERinFormParam::_create(
            'config_base',
            $this->plugin->txt('config_base'),
            $this->plugin->txt('config_base_info'),
            ilOERinFormParam::TYPE_HEAD
        );
        $params[] = ilOERinFormParam::_create(
            'wiki_ref_id',
            $this->plugin->txt('wiki_ref_id'),
            $this->plugin->txt('wiki_ref_id_info'),
            ilOERinFormParam::TYPE_REF_ID
        );
        $params[] = ilOERinFormParam::_create(
            'pub_ref_id',
            $this->plugin->txt('pub_ref_id'),
            $this->plugin->txt('pub_ref_id_info'),
            ilOERinFormParam::TYPE_REF_ID
        );

        foreach ($params as $param)
        {
            $this->params[$param->name] = $param;
        }
        $this->read();
	}

    /**
     * Get the array of all parameters
     * @return ilOERinFormParam[]
     */
	public function getParams()
    {
        return $this->params;
    }

    /**
     * Get the value of a named parameter
     * @param $name
     * @return  mixed
     */
	public function get($name)
    {
        if (!isset($this->params[$name]))
        {
            return null;
        }
        else
        {
            return $this->params[$name]->value;
        }
    }

    /**
     * Set the value of the named parameter
     * @param string $name
     * @param mixed $value
     *
     */
    public function set($name, $value = null)
    {
        $param = $this->params[$name];

        if (isset($param))
        {
            $param->setValue($value);
        }
    }


    /**
     * Read the configuration from the database
     */
	public function read()
    {
        global $DIC;
        $ilDB = $DIC->database();

        $query = "SELECT * FROM oerinf_config";
        $res = $ilDB->query($query);
        while($row = $ilDB->fetchAssoc($res))
        {
            $this->set($row['param_name'], $row['param_value']);
        }
    }

    /**
     * Write the configuration to the database
     */
    public function write()
    {
        global $DIC;
        $ilDB = $DIC->database();

        foreach ($this->params as $param)
        {
            $ilDB->replace('oerinf_config',
                array('param_name' => array('text', $param->name)),
                array('param_value' => array('text', (string) $param->value))
            );
        }
    }
}