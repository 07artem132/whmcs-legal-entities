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
use WHMCS\View\Menu\MenuFactory;

class AdminAddDocPage implements PageInterface
{
    protected $templateName = 'admin_add_doc.tpl';
    protected $vars = [];

    function __construct()
    {
        try {
            $form = $this->createForm();
            if ($_SERVER['REQUEST_METHOD'] != 'GET') {
                $form->saveForm($_POST, $_FILES);
                LogController::addSuccess(__CLASS__, sprintf('adminid->%s add doc', $_SESSION['adminid']));
                redir(sprintf('module=%s&action=index', ModuleConfig::getModuleName()), 'addonmodules.php');
            }
            $this->vars['configField'] = $form->getSettingsAsArray();
        } catch (\Throwable $e) {
            LogController::addError(__CLASS__, sprintf('adminid->%s', $_SESSION['adminid']), $e);
        }
    }

    function createForm()
    {
        $form = new FormHtmlHelper(new DocModel(), false);
        return $form->addGroup((new FormGroupHtmlHelper('Настраиваемые поля счета', 3))
            ->addItem((new ItemTextHtmlHelper())
                ->setLabel('Название')
                ->setName('name')
                ->setDescription('Введите имя документа')
                ->setClass('form-control')
                ->required()
            )
            ->addItem((new ItemSelectHtmlHelper())
                ->setLabel('Тип')
                ->setName('type')
                ->addSelectAllow('Договор', 'Договор')
                ->addSelectAllow('Акт', 'Акт')
                ->addSelectAllow('Прочее', 'Прочее')
                ->setDescription('Выберите тип документа')
                ->makeAChoice()
                ->setClass('form-control')
                ->required()
            )
            ->addItem((new ItemTextHtmlHelper())
                ->setLabel('Введите id Продукта/Дополнения')
                ->setName('rel_id')
                ->setDescription('Введите id с которым ассоциировать данный документ')
                ->setClass('form-control')
                ->required()
            )
            ->addItem((new ItemSelectHtmlHelper())
                ->setLabel('Тип связи')
                ->setName('rel_type')
                ->addSelectAllow(1, 'Продукт')
                ->addSelectAllow(2, 'Дополнение')
                ->addSelectAllow(3, 'Домен')
                ->addSelectAllow(4, 'Клиент')
                ->setDescription('Выберите с чем ассоциировать id в поле выше')
                ->setClass('form-control')
                ->required()
            )
            ->addItem((new ItemFileHtmlHelper())
                ->setLabel('Файл')
                ->setName('file')
                ->setDescription('Выберите файл который необходимо загрузить')
            )
            ->addItem((new ItemCheckboxHtmlHelper())
                ->setLabel('Отправлен по почте')
                ->setName('send_mail')
                ->setDescription('Отметьте здесь если документ был отправлен по почте')
                ->setClass('form-check-input')
            )->addItem((new ItemCheckboxHtmlHelper())
                ->setLabel('Отправлен через ЭДО')
                ->setName('send_edf')
                ->setDescription('Отметьте здесь если документ был отправлен через ЭДО')
                ->setClass('form-check-input')
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