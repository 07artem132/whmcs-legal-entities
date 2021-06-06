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
use WHMCS\Module\Addon\LegalEntities\Models\ActModel;
use WHMCS\Module\Addon\LegalEntities\Models\DocModel;
use WHMCS\Module\Addon\LegalEntities\Models\SharedDocModel;
use WHMCS\View\Menu\MenuFactory;
use WHMCS\Module\Addon\LegalEntities\Interfaces\PageInterface;

class AdminEditPage implements PageInterface
{
    protected $templateName = 'admin_add_doc.tpl';
    protected $vars = [];

    function __construct()
    {
        try {
            if (!array_key_exists('id', $_GET))
                redir(sprintf('module=%s&action=index', ModuleConfig::getModuleName()), 'addonmodules.php');
            if (!array_key_exists('type', $_GET)) {
                if (array_key_exists('edf', $_GET)) {
                    $model = DocModel::findOrFail($_GET['id']);
                    $model->send_edf = intval($_GET['edf']);
                    $model->saveOrFail();
                    LogController::addSuccess(__CLASS__, sprintf('adminid->%s edit doc->%s', $_SESSION['adminid'], $_GET['id']));
                    redir(sprintf('module=%s&action=index', ModuleConfig::getModuleName()), 'addonmodules.php');
                }
                if (array_key_exists('mail', $_GET)) {
                    $model = DocModel::findOrFail($_GET['id']);
                    $model->send_mail = intval($_GET['mail']);
                    $model->saveOrFail();
                    LogController::addSuccess(__CLASS__, sprintf('adminid->%s edit doc->%s', $_SESSION['adminid'], $_GET['id']));
                    redir(sprintf('module=%s&action=index', ModuleConfig::getModuleName()), 'addonmodules.php');
                }

                $form = $this->createForm();

                $form->loadForm($_GET['id']);
                if ($_SERVER['REQUEST_METHOD'] != 'GET') {
                    $oldFile = $form->getSetting('Добавление документа', 'file');
                    $form->saveForm($_POST, $_FILES);
                    $newFile = $form->getSetting('Добавление документа', 'file');

                    if (strcasecmp($oldFile, $newFile) !== 0) {
                        if (unlink($oldFile)) {
                            LogController::addSuccess(__CLASS__, sprintf('adminid->%s remove old file doc->%s', $_SESSION['adminid'], $_GET['id']));
                        } else {
                            LogController::addError(__CLASS__, sprintf('adminid->%s error old remove file doc->%s', $_SESSION['adminid'], $_GET['id']));
                        }
                    }
                    LogController::addSuccess(__CLASS__, sprintf('adminid->%s edit doc->%s', $_SESSION['adminid'], $_GET['id']));
                    redir(sprintf('module=%s&action=index', ModuleConfig::getModuleName()), 'addonmodules.php');
                }
                $this->vars['configField'] = $form->getSettingsAsArray();
            } elseif ($_GET['type'] == 'shared') {
                $form = $this->createFormShared();
                $form->loadForm($_GET['id']);
                $this->vars['configField'] = $form->getSettingsAsArray();
                if ($_SERVER['REQUEST_METHOD'] != 'GET') {
                    $oldFile = $form->getSetting('Добавление документа', 'file');
                    $form->saveForm($_POST, $_FILES);
                    $newFile = $form->getSetting('Добавление документа', 'file');

                    if (strcasecmp($oldFile, $newFile) !== 0) {
                        if (unlink($oldFile)) {
                            LogController::addSuccess(__CLASS__, sprintf('adminid->%s remove shared old file doc->%s', $_SESSION['adminid'], $_GET['id']));
                        } else {
                            LogController::addError(__CLASS__, sprintf('adminid->%s error shared old remove file doc->%s', $_SESSION['adminid'], $_GET['id']));
                        }
                    }
                    LogController::addSuccess(__CLASS__, sprintf('adminid->%s edit doc->%s', $_SESSION['adminid'], $_GET['id']));
                    redir(sprintf('module=%s&action=shared', ModuleConfig::getModuleName()), 'addonmodules.php');
                }
            } elseif ($_GET['type'] == 'act') {
                if (array_key_exists('edf', $_GET)) {
                    $model = ActModel::firstOrNew(['rel_id'=>$_GET['id']]);
                    $model->send_edf = intval($_GET['edf']);
                    $model->saveOrFail();
                    LogController::addSuccess(__CLASS__, sprintf('adminid->%s edit act doc->%s', $_SESSION['adminid'], $_GET['id']));
                    redir(sprintf('module=%s&action=acts', ModuleConfig::getModuleName()), 'addonmodules.php');
                }
                if (array_key_exists('mail', $_GET)) {
                    $model = ActModel::firstOrNew(['rel_id'=>$_GET['id']]);
                    $model->send_mail = intval($_GET['mail']);
                    $model->saveOrFail();
                    LogController::addSuccess(__CLASS__, sprintf('adminid->%s edit act doc->%s', $_SESSION['adminid'], $_GET['id']));
                    redir(sprintf('module=%s&action=acts', ModuleConfig::getModuleName()), 'addonmodules.php');
                }
            }
        } catch (\Throwable $e) {
            LogController::addError(__CLASS__, sprintf('adminid->%s', $_SESSION['adminid']), $e);
        }

    }

    function createFormShared()
    {
        $form = new FormHtmlHelper(new SharedDocModel(), false);
        return $form->addGroup((new FormGroupHtmlHelper('Добавление документа', 3))
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

    function createForm()
    {
        $form = new FormHtmlHelper(new DocModel(), false);
        return $form->addGroup((new FormGroupHtmlHelper('Добавление документа', 3))
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
                ->addSelectAllow('Акт', 'Акт сверки')
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

    /**
     * @return string
     */
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

    /**
     * @return MenuFactory|null
     */
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
            'Лог' => '',
        ];
    }
}