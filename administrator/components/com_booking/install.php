<?php

/**
 * @package ARTIO Booking
 * @copyright Copyright (C) ARTIO s.r.o. All rights reserved.
 * @author ARTIO s.r.o., http://www.artio.net
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class com_bookingInstallerScript {

    /**
     * Uninstall extensions. Included in component administrator folder.
     */
    public function uninstall() {
        JFactory::getLanguage()->load('com_booking', JPATH_ADMINISTRATOR);

        JLoader::import('helpers.installer', dirname(__FILE__));

        AInstaller::uninstall();
        AInstallerJoomFish::uninstall();

        $file = JPATH_ROOT . '/plugins/bookingpayment/paymentmethod.php';
        if (JFile::exists($file)) {
            JFile::delete($file);
        }
    }

    /**
     * Show welcome page. Included in unpacked package in temporary folder.
     */
    function postflight() {
        JFactory::getLanguage()->load('com_booking', JPATH_ADMINISTRATOR);

        JLoader::import('admin.defines', dirname(__FILE__));
        JLoader::import('admin.helpers.installer', dirname(__FILE__));

        AInstaller::install();
        AInstallerJoomFish::install();

        $folder = JPath::clean(JPATH_ROOT . '/plugins/bookingpayment');
        $file = JPath::clean(dirname(__FILE__) . '/admin/extensions/payment_methods/paymentmethod.php');
        $target = JPath::clean(JPATH_ROOT . '/plugins/bookingpayment/paymentmethod.php');
        if (JFolder::exists($folder) && JFile::exists($file)) {
            JFile::copy($file, $target);
        }

        $this->_db();
        $this->_cfg();
        $this->_acl();
        $this->_payments();

        echo '<div class="install" style="background-color: #FFF;padding: 20px;">';
        echo '<a href="index.php?option=com_booking" title="">';
        echo '<img src="' . JURI::root() . 'components/com_booking/assets/images/logo-150.png" alt="" style="float: left;padding-right: 20px;" />';
        echo '</a>';
        echo '<h2 style="color: #0B55C4;font-size: 30px;line-height: 30px;margin: 0;padding: 0 0 10px 170px;">';
        echo '<a href="index.php?option=com_booking" title="">ARTIO Booking</a>';
        echo '</h2>';
        echo '<p style="font-size: 14px;margin: 0;padding: 0 0 10px 170px;width: 50%;">' . JText::_('BOOKING_DESCRIPTION') . '</p>';
        echo '<p style="font-size: 14px;margin: 0;padding: 0 0 10px 170px;width: 50%;">' . str_replace('"_QQ_"', '"', JText::_('SAMPLE_DATA')) . '</p>';
        echo '</div>';
    }

    /**
     * Allow basic rule list for each user.
     */
    private function _acl() {
        $db = JFactory::getDbo();

        $rule = array();
        $rule['core.admin'] = 0;
        $rule['core.manage'] = 0;
        $rule['booking.reservation.create'] = 1;
        $rule['booking.reservations.manage'] = 0;
        $rule['booking.item.manage'] = 0;
        $rule['booking.view.customers'] = 0;
        $rule['booking.edit.customer'] = 1;
        $rule['booking.mailing.new.reservation'] = 0;
        $rule['booking.mailing.change.reservation'] = 0;
        $rule['booking.mailing.cancel.reservation'] = 0;

        // load existing rules
        $query = $db->getQuery(true);
        $query->select('rules')->from('#__assets')->where("name = 'com_booking'");
        $db->setQuery($query);
        $rules = $db->loadResult();

        $rules = json_decode($rules);

        // initialize with fresh installation
        if (!is_object($rules))
            $rules = new stdClass();

        foreach ($rule as $code => $value)
            if (!isset($rules->$code))
                $rules->$code = new stdClass();

        // load all usergroups
        $query = $db->getQuery(true);
        $query->select('id')->from('#__usergroups');
        $db->setQuery($query);
        $userGroups = $db->loadColumn();

        // allow rule list for each usergroup
        foreach ($userGroups as $userGroup)
            foreach ($rule as $code => $value)
                if ($value)
                    $rules->$code->$userGroup = $value;

        // save rules back
        $rules = json_encode($rules);
        $rules = $db->quote($rules);

        $query = $db->getQuery(true);
        $query->update('#__assets')->set("rules = $rules")->where("name = 'com_booking'");
        $db->setQuery($query);
        $db->query();

        // load existing configuration from database
        $query = $db->getQuery(true);
        $query->select('params')
                ->from('#__extensions')
                ->where('type = ' . $db->quote('component'))
                ->where('element = ' . $db->quote('com_booking'));

        $params = $db->setQuery($query)->loadResult();

        // set asset id of ACL
        $registry = new JRegistry($params);
        $registry->set('asset_id', 'com_booking');

        // save back to database
        $params = $registry->toString();

        $query = $db->getQuery(true);
        $query->update('#__extensions')
                ->set('params = ' . $db->quote($params, false))
                ->where('type = ' . $db->quote('component'))
                ->where('element = ' . $db->quote('com_booking'));

        $db->setQuery($query)->query();
    }

    /**
     * Allow all payment methods for each usergroup
     */
    public function _payments() {
        $db = JFactory::getDbo();

        // component as ACL parent
        $query = $db->getQuery(true);
        $query->select('id')->from('#__assets')->where('name = ' . $db->quote('com_booking'));
        $parent = $db->setQuery($query)->loadResult();

        // installed payment methods
        $query = $db->getQuery(true);
        $query->select('id')->from('#__booking_payment');
        $ids = $db->setQuery($query)->loadColumn();

        // system usergroups
        $query = $db->getQuery(true);
        $query->select('id')->from('#__usergroups');
        $groups = $db->setQuery($query)->loadColumn();

        // allow payment methods for each user group as default
        $rule = 'booking.payment.pay';
        $rules = new stdClass();
        $rules->$rule = new stdClass();
        foreach ($groups as $group)
            $rules->$rule->$group = 1;
        $rules = json_encode($rules);

        foreach ($ids as $id) {
            $asset = JTable::getInstance('Asset');
            /* @var $asset JTableAsset */

            // load existing ACL
            $query = $db->getQuery(true);
            $query->select('id')->from('#__assets')->where('name = ' . $db->quote('com_booking.payment.' . $id));
            $asset->id = $db->setQuery($query)->loadResult();

            $asset->level = 2;
            $asset->title = $asset->name = 'com_booking.payment.' . $id;
            $asset->parent_id = $parent;
            $asset->rules = $rules;

            $asset->store();

            // save ACL ID in payment method
            $query = $db->getQuery(true);
            $query->update('#__booking_payment')->set('asset_id = ' . $db->quote($asset->id))->where('id = ' . $db->quote($id));
            $db->setQuery($query)->query();
        }
    }

    /**
     * Setup default configuration with fresh installation.
     */
    private function _cfg() {
        JModelLegacy::addIncludePath(JPath::clean(dirname(__FILE__) . '/admin/models'));
        $model = JModelLegacy::getInstance('Config', 'BookingModel');
        /* @var $model BookingModelConfig */
        $model->setAdmins();
    }

    public function db() {
        com_bookingInstallerScript::_db(JPath::clean(dirname(__FILE__) . '/sql/install.mysql.utf8.sql'));
    }

    /**
     * Update existing database table list during reinstall.
     */
    private function _db($sql = null) {
        $db = JFactory::getDBO();
        $queries = JFile::read($sql ? $sql : JPath::clean(dirname(__FILE__) . '/admin/sql/install.mysql.utf8.sql')); // complete statement to install database
        $tables = $matches = $attributes = array();

        foreach ($db->splitSql($queries) as $query) // run all queries for non-existing tables
            if (JString::trim($query))
                $db->setQuery($query)->query();

        if (preg_match_all('/CREATE TABLE IF NOT EXISTS `([^`]*)` ([^;]*);/isU', $queries, $matches, PREG_SET_ORDER)) // search create table statement
            foreach ($matches as $match)
                if (preg_match_all('/(  `([^`]+)`[^,]+),/', $match[2], $attributes, PREG_SET_ORDER)) // search create table attribute statement
                    foreach ($attributes as $attribute)
                        $tables[$match[1]][$attribute[2]] = $attribute[1]; // fieldname => statement

                    foreach ($tables as $tablename => $attributes) { // update existing tables	
            $fields = $db->getTableColumns($tablename); // attribute list of existing table
            foreach ($attributes as $fieldname => $statement) { // process attribute list from new table statement
                if (!isset($fields[$fieldname])) // new attribute
                    $db->setQuery("ALTER TABLE $tablename ADD $statement")->query();
                else
                    unset($fields[$fieldname]); // attribute already exists
            }
            if (!empty($fields)) // attributes are not in the new table statement = drop them
                $db->setQuery("ALTER TABLE $tablename DROP " . implode(', DROP ', array_keys($fields)))->query();
        }
    }

}
