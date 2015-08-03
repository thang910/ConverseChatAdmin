<?php

class Atmail_ConverseChatAdmin_Plugin extends Atmail_Controller_Plugin
{

    protected $_pluginFullName = 'SecureChat Administrator Settings';
    protected $_pluginAuthor = 'Nguyen Manh Thang<thang@linex.vn>';
    protected $_pluginDescription = 'DashBoard controller SecureChat';
    protected $_pluginCopyright = 'Copyright Prmail';
    protected $_pluginUrl = '';
    protected $_pluginNotes = '';
    protected $_pluginVersion = '1.1.0';
    protected $_pluginCompat = '7.0.0';
    protected $_pluginModule = 'admin';


    // class constructor
    public function __construct()
    {
        $this->_pluginDescription = "Admin Management for Converse Chat";
        $this->log = Zend_Registry::get('log');
        parent::__construct();
    }


    /*
    * creates the Cryptophoto tables upon installation
    */
    public function setup()
    {
        if(!$this->dbValid('#__converseChat')) {
              $db = Zend_Registry::get("dbAdapter");
              $db->query("CREATE TABLE `#__converseChat` (`rowid` int(11) unsigned NOT NULL auto_increment, `prebindURL` varchar(35) NOT NULL DEFAULT '', PRIMARY KEY  (`rowid`))");
              $db->query("INSERT INTO `#__converseChat` (`prebindURL`) VALUES('')");
            }
    }

    public function renderSettingsTabNav()
    {
        $this->log->debug("**\n Admin: renderMailSettingsNav \n**\n");

        $params = array(
            "title" => "ConverseChatAdmin Settings",
            "text"  => "ConverseChat",
            "icon"  => "images/cryptophoto.png"
        );

        echo $this->_createMailSettingsNavSection($params);
    }


    /*
    * The Cryptophoto settings section of the admin account
    */
    public function settings()
    {
        $this->_initView();
//        $this->log->debug("**\n Admin:ConverseChatAdmin settings \n**\n");
        $db = Zend_Registry::get("dbAdapter");
//
        if($this->dbValid('#__converseChat')) {
            $db_values = $db->fetchAll("SELECT * FROM `#__converseChat`");
//            SELECT LAST(CustomerName) AS LastCustomer FROM Customers;
            $settings = current($db_values);

          //check for existing converseChat
            if($settings['prebindURL'] != null) {
                $this->view->cp_prebind  = $settings['prebindURL'] ;
            }else{
                $this->view->cp_prebind = '';
            }
        }
      echo $this->view->render("settings.phtml");
    }


    /*
    * saves the site's Cryptophoto configuration when changing the keys and salt
    */
    public function saveSettings()
    {
        $this->_initView();
        require_once("library/jQuery/jQuery.php");
        $url = $this->getRequest()->getParam('cp_prebind');
        if($this->dbValid('#__converseChat')) {
              // fetch the current converse settings
              $db = Zend_Registry::get("dbAdapter");
              $db_values = $db->fetchAll("SELECT * FROM `#__converseChat`;");
              $settings = current($db_values);
              $prebind  = $settings['prebindURL'] ? $settings['prebindURL'] : "";
              $rowid      = $settings['rowid'];

            // non-empty prebindURL
              if (!empty($url)) {
                $prebind = $url;
               }

                $db->query("UPDATE `#__converseChat` SET `prebindURL`=? WHERE `rowid`=?", array(trim($prebind),$rowid));

             jQuery::addMessage("The settings have been updated");
        }

        else {
              jQuery::addMessage("Could not detect a valid ConverseChat installation");
              return;
            }

        echo $this->view->render("jsonresponse.phtml");

    }

//    function is_domain($domain) /* adde dby vicky*/
//    {
//        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain) //valid chars check
//                 && preg_match("/^.{1,253}$/", $domain) //overall length check
//                    && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain)); //length of each label
//
//    }

    public function dbValid($tableName) {

        $db = Zend_Registry::get("dbAdapter");
        $db_values = $db->fetchAll("SHOW TABLES LIKE '" . $tableName . "'");

        if(count($db_values)) {
          return true;
        }
        else {
          return false;
        }
      }
}
