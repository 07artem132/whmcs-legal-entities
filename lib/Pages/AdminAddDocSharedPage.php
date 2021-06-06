<?php

namespace WHMCS\Module\Addon\LegalEntities\Pages;

use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Controllers\LogController;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\FormGroupHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\FormHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\ItemCheckboxHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\ItemFileHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\ItemSelectHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\ItemTextHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\Interfaces\PageInterface;
use WHMCS\Module\Addon\LegalEntities\Models\DocModel;
use WHMCS\Module\Addon\LegalEntities\Models\LogModel;
use WHMCS\Module\Addon\LegalEntities\Models\SharedDocModel;
use WHMCS\View\Menu\MenuFactory;

class AdminAddDocSharedPage implements PageInterface
{
    protected $templateName = 'admin_add_doc_shared.tpl';
    protected $vars = [];

    function __construct()
    {
        try {
            $form = $this->createForm();
            if ($_SERVER['REQUEST_METHOD'] != 'GET') {
                $form->saveForm($_POST, $_FILES);
                LogController::addSuccess(__CLASS__, sprintf('adminid->%s add doc', $_SESSION['adminid']));
                redir(sprintf('module=%s&action=shared', ModuleConfig::getModuleName()), 'addonmodules.php');
            }
            $this->vars['configField'] = $form->getSettingsAsArray();
        } catch (\Throwable $e) {
            LogController::addError(__CLASS__, sprintf('adminid->%s', $_SESSION['adminid']), $e);
        }
    }

    function createForm()
    {
        $form = new FormHtmlHelper(new SharedDocModel(), false);
        return $form->addGroup((new FormGroupHtmlHelper('Настраиваемые поля счета', 3))
            ->addItem((new ItemTextHtmlHelper())
                ->setLabel('Название')
                ->setName('name')
                ->setDescription('Введите имя документа')
                ->setClass('form-control')
                ->required()
            )
            ->addItem((new ItemFileHtmlHelper())
                ->setLabel('Файл')
                ->setName('file')
                ->setDescription('Выберите файл который необходимо загрузить')
            )
        );
    }

    function getTemplateName(): string
    {
        return $this->templateName;
    }

    /**
     * @return array
     */
    function getVars(): array
    {
        return $this->vars;
    }

    function getSubMenu(): ?MenuFactory
    {
        return null;
    }

    /**
     * @return array
     */
    public function getBreadcrumb(): array
    {
        return [
            'Главная' => ModuleConfig::getModuleLink(),
            'Добавление документа' => '',
        ];
    }
}