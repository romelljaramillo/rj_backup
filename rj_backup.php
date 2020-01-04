<?php
/*
* 2019 Roanja
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to info@roanja.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade Roanja to newer
* versions in the future. If you wish to customize Roanja for your
* needs please refer to http://www.roanja.com for more information.
*
*  @author Roanja <info@roanja.com>
*  @copyright  2019 Roanja
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Roanja
*/
/**
 * @since   1.0.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopBackup;

class Rj_Backup extends Module
{
    protected $_html = '';
    private $database_host = '';
    private $database_port = '';
    private $database_name = '';
    private $database_user = '';
    private $database_password = '';
    private $database_backup_name = 'db_backup.sql';
    private $config;
    public $rjBackupAll = true;
    
    public function __construct()
    {
        $this->name = 'rj_backup';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Roanja';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->trans('Roanja Backup', array(), 'Modules.Rjbackup.Admin');
		$this->description = $this->trans('Backup you prestashop.', array(), 'Modules.Rjbackup.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $legacyBackup = new PrestaShopBackup();
        $this->rjBackupAll = $legacyBackup->psBackupAll;
        
        // $this->templateFile = 'module:ps_imageslider/views/templates/hook/slider.tpl';
    }
    
    /**
     * @see Module::install()
     */
    public function install()
    {
        if (parent::install()) {
            // return (bool)$res;
            return true;
        }
        return false;
    }
    
    /**
     * @see Module::uninstall()
     */
    public function uninstall()
    {
        /* Deletes Module */
        if (parent::uninstall()) {
            return true;
        }
        return false;
    }
    
    public function getContent()
    {
        
        // $this->_html .= $this->displayError('mensaje de displayError');
        // $this->_html .= $this->displayWarning('mensaje de displayWarning');
        // $this->_html .= $this->displayConfirmation('mensaje de displayConfirmation');
        // $this->_html .= $this->displayInformation('mensaje de displayInformation');
        if (Tools::isSubmit('submitConfiBackup') ||
            Tools::isSubmit('submitConfigFtp') ||
            Tools::isSubmit('submitBackupAll') || 
            Tools::isSubmit('create_Backup')){
            if ($this->_postValidation()) {
                $this->_postProcess();
                $this->_html .= $this->renderFormHost();
                $this->_html .= $this->renderFormFtp();
                $this->_html .= $this->renderFormBackupAll();
                $this->_html .= $this->renderFormCreateBackup();
                $this->_html .= $this->renderListBackup();
            }
        } else {
            $this->_html .= $this->renderFormHost();
            $this->_html .= $this->renderFormFtp();
            $this->_html .= $this->renderFormBackupAll();
            $this->_html .= $this->renderFormCreateBackup();
            $this->_html .= $this->renderListBackup();
        }
        
        return $this->_html;
    }
    
    protected function _postValidation() 
    {
        
        return true;
    }
    
    protected function _postProcess() 
    {

        if (Tools::isSubmit('submitConfiBackup')){
            $shop_groups_list = array();
            $shops = Shop::getContextListShopID();
            foreach ($shops as $shop_id) {
                $shop_group_id = (int)Shop::getGroupFromShop($shop_id, true);
                if (!in_array($shop_group_id, $shop_groups_list)) {
                    $shop_groups_list[] = $shop_group_id;
                }
                $res = Configuration::updateValue('database_host', Tools::getValue('database_host'), false, $shop_group_id, $shop_id);
                $res &= Configuration::updateValue('database_port', Tools::getValue('database_port'), false, $shop_group_id, $shop_id);
                $res &= Configuration::updateValue('database_name', Tools::getValue('database_name'), false, $shop_group_id, $shop_id);
                $res &= Configuration::updateValue('database_user', Tools::getValue('database_user'), false, $shop_group_id, $shop_id);
                $res &= Configuration::updateValue('database_password', Tools::getValue('database_password'), false, $shop_group_id, $shop_id);
                $res &= Configuration::updateValue('database_backup_name', Tools::getValue('database_backup_name'), false, $shop_group_id, $shop_id);
            }
            if (!$res) {
                $errors[] = $this->displayError($this->trans('The configuration could not be updated.', array(), 'Modules.Rjbackup.Admin'));
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=6&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
            }


        }
        if (Tools::isSubmit('submitConfigFtp')){
            $shop_groups_list = array();
            $shops = Shop::getContextListShopID();
            foreach ($shops as $shop_id) {
                $shop_group_id = (int)Shop::getGroupFromShop($shop_id, true);
                if (!in_array($shop_group_id, $shop_groups_list)) {
                    $shop_groups_list[] = $shop_group_id;
                }
                $res = Configuration::updateValue('protocolo_ftp', Tools::getValue('protocolo_ftp'), false, $shop_group_id, $shop_id);
                $res &= Configuration::updateValue('ftp_host', Tools::getValue('ftp_host'), false, $shop_group_id, $shop_id);
                $res &= Configuration::updateValue('ftp_port', Tools::getValue('ftp_port'), false, $shop_group_id, $shop_id);
                $res &= Configuration::updateValue('ftp_user', Tools::getValue('ftp_user'), false, $shop_group_id, $shop_id);
                $res &= Configuration::updateValue('ftp_password', Tools::getValue('ftp_password'), false, $shop_group_id, $shop_id);
            }
            if (!$res) {
                $errors[] = $this->displayError($this->trans('The configuration FTP.', array(), 'Modules.Rjbackup.Admin'));
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=6&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
            }
        }
        if (Tools::isSubmit('submitBackupAll')){
            $shop_groups_list = array();
            $shops = Shop::getContextListShopID();
            foreach ($shops as $shop_id) {
                $shop_group_id = (int)Shop::getGroupFromShop($shop_id, true);
                if (!in_array($shop_group_id, $shop_groups_list)) {
                    $shop_groups_list[] = $shop_group_id;
                }
                $res = Configuration::updateValue('PS_BACKUP_ALL', Tools::getValue('PS_BACKUP_ALL'), false, $shop_group_id, $shop_id);
                $res = Configuration::updateValue('PS_BACKUP_DROP_TABLE', Tools::getValue('PS_BACKUP_DROP_TABLE'), false, $shop_group_id, $shop_id);
            }
            if (!$res) {
                $errors[] = $this->displayError($this->trans('The configuration Ignore statistics tables.', array(), 'Modules.Rjbackup.Admin'));
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=6&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
            }
        }
        if (Tools::isSubmit('create_Backup')){
            $legacyBackup = new PrestaShopBackup();
            $res = $legacyBackup->add();
            if (!$res) {
                $errors[] = $this->displayError($this->trans('The configuration Ignore statistics tables.', array(), 'Modules.Rjbackup.Admin'));
            } else {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=6&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name);
            }
        }
        if (count($errors)) {
            $this->_html .= $this->displayError(implode('<br />', $errors));
        } 
    }
    
    public function renderFormHost()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('Settings host', array(), 'Modules.Rjbackup.Admin'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->trans('database host', array(), 'Modules.Rjbackup.Admin'),
                        'name' => 'database_host',
                        'class' => 'fixed-width-lg',
                        'desc' => $this->trans('The duration of the transition between two slides.', array(), 'Modules.Rjbackup.Admin')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('database port', array(), 'Modules.Rjbackup.Admin'),
                        'name' => 'database_port',
                        'class' => 'fixed-width-lg'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('database name', array(), 'Modules.Rjbackup.Admin'),
                        'name' => 'database_name',
                        'class' => 'fixed-width-lg'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('database user', array(), 'Modules.Rjbackup.Admin'),
                        'name' => 'database_user',
                        'class' => 'fixed-width-lg'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('database password', array(), 'Modules.Rjbackup.Admin'),
                        'name' => 'database_password',
                        'class' => 'fixed-width-lg'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Name backup', array(), 'Modules.Rjbackup.Admin'),
                        'name' => 'database_backup_name',
                        'class' => 'fixed-width-lg'
                    ),
                ),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Actions'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitConfiBackup';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        // if($this->getConfigFieldsValues()){
        //     var_dump($this->getConfigFieldsValues());
        // } else {
        //     var_dump('no hay contenido');
        // }
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }
    
	public function getConfigFieldsValues()
    {
        $id_shop_group = Shop::getContextShopGroupID();
        $id_shop = Shop::getContextShopID();
        if(include(_PS_ROOT_DIR_ . '/app/config/parameters.php')){
            $this->config = include(_PS_ROOT_DIR_ . '/app/config/parameters.php');
            
            if(!Tools::getValue('database_host', Configuration::get('database_host', null, $id_shop_group, $id_shop))){
                $this->database_host = $this->config['parameters']['database_host'];
            } else {
                $this->database_host = Tools::getValue('database_host', Configuration::get('database_host', null, $id_shop_group, $id_shop));
            }
            if(!Tools::getValue('database_port', Configuration::get('database_port', null, $id_shop_group, $id_shop))){
                $this->database_port = $this->config['parameters']['database_port'];
            } else {
                $this->database_port = Tools::getValue('database_port', Configuration::get('database_port', null, $id_shop_group, $id_shop));
            }
            if(!Tools::getValue('database_name', Configuration::get('database_name', null, $id_shop_group, $id_shop))){
                $this->database_name = $this->config['parameters']['database_name'];
            } else {
                $this->database_name = Tools::getValue('database_name', Configuration::get('database_name', null, $id_shop_group, $id_shop));
            }
            if(!Tools::getValue('database_user', Configuration::get('database_user', null, $id_shop_group, $id_shop))){
                $this->database_user = $this->config['parameters']['database_user'];
            } else {
                $this->database_user = Tools::getValue('database_user', Configuration::get('database_user', null, $id_shop_group, $id_shop));
            }
            if(!Tools::getValue('database_password', Configuration::get('database_password', null, $id_shop_group, $id_shop))){
                $this->database_password = $this->config['parameters']['database_password'];
            } else {
                $this->database_password = Tools::getValue('database_password', Configuration::get('database_password', null, $id_shop_group, $id_shop));
            }
            if(Tools::getValue('database_backup_name', Configuration::get('database_backup_name', null, $id_shop_group, $id_shop))){
                $this->database_backup_name = Tools::getValue('database_backup_name', Configuration::get('database_backup_name', null, $id_shop_group, $id_shop));
            }     
        }

        return array(
            'database_host' => $this->database_host,
    		'database_port' => $this->database_port,
    		'database_name' => $this->database_name,
    		'database_user' => $this->database_user,
    		'database_password' => $this->database_password,
    		'database_backup_name' => $this->database_backup_name
        );
        
    }
    
    public function renderFormFtp()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('Settings ftp destination', array(), 'Modules.Rjbackup.Admin'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    // Select
                    array(
                        'type' => 'select',
                        'label' => $this->trans('Protocolo', array(), 'Modules.Rjbackup.Admin'),
                        'name' => 'protocolo_ftp',
                        'required' => true,
                        'options' => array(
                            'query' => $tipe_ftp = array(
                                array(
                                    'tipe_ftp' => 'ftp',
                                    'name' => 'FTP'
                                ),
                                array(
                                    'tipe_ftp' => 'sftp',
                                    'name' => 'SFTP'
                                ),                                  
                            ),
                        'id' => 'tipe_ftp',
                        'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('FTP host', array(), 'Modules.Rjbackup.Admin'),
                        'name' => 'ftp_host',
                        'class' => 'fixed-width-lg',
                        'desc' => $this->trans('The duration of the transition between two slides.', array(), 'Modules.Rjbackup.Admin')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('FTP port', array(), 'Modules.Rjbackup.Admin'),
                        'name' => 'ftp_port',
                        'class' => 'fixed-width-lg'
                    ),                    
                    array(
                        'type' => 'text',
                        'label' => $this->trans('FTP user', array(), 'Modules.Rjbackup.Admin'),
                        'name' => 'ftp_user',
                        'class' => 'fixed-width-lg'
                    ),
                    array(
                        'type' => 'password',
                        'label' => $this->trans('FTP password', array(), 'Modules.Rjbackup.Admin'),
                        'name' => 'ftp_password',
                        'class' => 'fixed-width-lg'
                    ),
                ),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Actions'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitConfigFtp';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getFtpConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper->generateForm(array($fields_form));
    }

    public function renderFormBackupAll()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->getTranslator()->trans('Settings', array(), 'Admin.Global'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(                   
                    array(
                        'type' => 'switch',
                        'label' => $this->getTranslator()->trans('Ignore statistics tables', array(), 'Modules.Rjbackup.Admin'),
                        'name' => 'PS_BACKUP_ALL',
                        'desc' => $this->getTranslator()->trans('connections, connections_page, connections_source, guest, statssearch.', array(), 'Modules.Rjbackup.Admin'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->getTranslator()->trans('Enabled', array(), 'Admin.Global')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->getTranslator()->trans('Disabled', array(), 'Admin.Global')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->getTranslator()->trans('Drop existing tables during import', array(), 'Modules.Rjbackup.Admin'),
                        'name' => 'PS_BACKUP_DROP_TABLE',
                        'desc' => $this->getTranslator()->trans('If enabled, the backup script will drop your tables prior to restoring data.', array(), 'Modules.Rjbackup.Admin'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->getTranslator()->trans('Enabled', array(), 'Admin.Global')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->getTranslator()->trans('Disabled', array(), 'Admin.Global')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->getTranslator()->trans('Save', array(), 'Admin.Actions'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitBackupAll';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsBackupAll(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }
   
    public function getFtpConfigFieldsValues()
    {
        $id_shop_group = Shop::getContextShopGroupID();
        $id_shop = Shop::getContextShopID();

        return array(
            'protocolo_ftp' => Tools::getValue('protocolo_ftp', Configuration::get('protocolo_ftp', null, $id_shop_group, $id_shop)),
            'ftp_host' => Tools::getValue('ftp_host', Configuration::get('ftp_host', null, $id_shop_group, $id_shop)),
            'ftp_port' => Tools::getValue('ftp_port', Configuration::get('ftp_port', null, $id_shop_group, $id_shop)),
            'ftp_user' => Tools::getValue('ftp_user', Configuration::get('ftp_user', null, $id_shop_group, $id_shop)),
            'ftp_password' => Tools::getValue('ftp_password', Configuration::get('ftp_password', null, $id_shop_group, $id_shop)),
        );
        
    }

    public function getConfigFieldsBackupAll()
    {
        $id_shop_group = Shop::getContextShopGroupID();
        $id_shop = Shop::getContextShopID();

        return array(
            'PS_BACKUP_ALL' => Tools::getValue('PS_BACKUP_ALL', Configuration::get('PS_BACKUP_ALL', null, $id_shop_group, $id_shop)),
            'PS_BACKUP_DROP_TABLE' => Tools::getValue('PS_BACKUP_DROP_TABLE', Configuration::get('PS_BACKUP_DROP_TABLE', null, $id_shop_group, $id_shop)),
        );
        
    }

    public function renderFormCreateBackup()
    {
        $this->context->smarty->assign(
            array(
                'link' => $this->context->link
            )
        );
        return $this->display(__FILE__, 'rj_backup_form.tpl');
    }

    public function renderListBackup()
    {
        return $this->display(__FILE__, 'list.tpl');
    }

    protected function updateUrl($link)
    {
        if (substr($link, 0, 7) !== "http://" && substr($link, 0, 8) !== "https://") {
            $link = "http://" . $link;
        }

        return $link;
    }

}