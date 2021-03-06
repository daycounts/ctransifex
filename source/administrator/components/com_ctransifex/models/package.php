<?php
/**
 * @author Daniel Dimitrov - compojoom.com
 * @date: 21.09.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellegacy');

class ctransifexModelPackage extends JModelLegacy
{

    public function __construct(array $config = array())
    {
        if (isset($config['project'])) {
            $this->projectId = $config['project']->id;
            $this->project = $config['project'];
        }

        parent::__construct($config);
    }

    public function add($resources, $language)
    {

        // now add the resources
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $completed = 0;

        $allResources = $this->countResources();

        foreach($resources as $resource) {
            $completed += $resource->completed;
        }

        $values = $db->q($this->projectId) .
                ',' . $db->q($language) .
                ',' . $db->q((int)$completed/$allResources) .
                ',' . $db->q(JFactory::getDate()->toSql());

        $query->insert('#__ctransifex_zips')
            ->columns(
            array(
                $db->qn('project_id'),
                $db->qn('lang_name'),
                $db->qn('completed'),
                $db->qn('created')
            )
        )->values($values);

        $db->setQuery($query);
        $db->execute();
    }

    public function countResources() {
        $db = JFactory::getDbo();
        $query = $db->getQuery('true');

        $query->select('COUNT(id) as count')->from('#__ctransifex_resources')->where('project_id = ' .$db->q($this->projectId));

        $db->setQuery($query);

        return $db->loadObject()->count;
    }
}