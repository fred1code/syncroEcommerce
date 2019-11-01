<?php
class SyncAdminController extends ModuleAdminController{
    public function init()
    {
        return parent::init();
    }

    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign(array());
        $this->setTemplate('configure.tpl');


    }

    public function setMedia()
    {
        return parent::setMedia();
        $this->addJquery();



    }
}