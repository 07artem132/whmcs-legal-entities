<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 11.01.2020, 17:21
 *
 */

namespace WHMCS\Module\Addon\LegalEntities\Pages;

use WHMCS\Module\Addon\LegalEntities\Configs\ModuleConfig;
use WHMCS\Module\Addon\LegalEntities\Controllers\LogController;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\FormGroupHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\FormHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\ItemCheckboxHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\ItemFileHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\ItemSelectClientCustomFieldHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\ItemSelectClientGroupHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\ItemSelectHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\HtmlHelper\ItemTextHtmlHelper;
use WHMCS\Module\Addon\LegalEntities\Interfaces\PageInterface;
use WHMCS\Module\Addon\LegalEntities\Models\SettingModel;
use WHMCS\View\Menu\MenuFactory;

class AdminSettingsPage implements PageInterface
{
    private $templateName = 'admin_settings.tpl';
    private $vars = [];

    function __construct()
    {
        try {
            $form = $this->createForm();
            $form->loadForm(null);
            if ($_SERVER['REQUEST_METHOD'] != 'GET') {
                LogController::addSuccess(__CLASS__, sprintf('adminid->%s изменены настройки', $_SESSION['adminid'], $_GET['id']));
                $form->saveForm($_POST, $_FILES);
            }

            $this->vars['configField'] = $form->getSettingsAsArray();
        } catch (\Throwable $e) {
            LogController::addError(__CLASS__, sprintf('adminid->%s', $_SESSION['adminid']), $e);
        }

    }

    function getTemplateName(): string
    {
        return $this->templateName;
    }

    function createForm()
    {
        $form = new FormHtmlHelper(new SettingModel(), true);

        $form->addGroup((new FormGroupHtmlHelper('Платежная информация', 1))
            ->addItem((new ItemTextHtmlHelper())
                ->setLabel('Банк получателя')
                ->setName('payeesBank')
                ->setClass('form-control')
                ->required()
            )
            ->addItem((new ItemTextHtmlHelper())
                ->setLabel('БИК')
                ->setName('bik')
                ->setClass('form-control')
                ->required()
            )
            ->addItem((new ItemTextHtmlHelper())
                ->setLabel('№Р/С')
                ->setName('accountNumber1')
                ->setClass('form-control')
                ->required()
            )
            ->addItem((new ItemTextHtmlHelper())
                ->setLabel('№К/С')
                ->setName('accountNumber2')
                ->setClass('form-control')
                ->required()
            )
            ->addItem((new ItemTextHtmlHelper())
                ->setLabel('ИНН')
                ->setName('inn')
                ->setClass('form-control')
                ->required()
            )
            ->addItem((new ItemTextHtmlHelper())
                ->setLabel('КПП')
                ->setName('kpp')
                ->setClass('form-control')
                ->required()
            )
            ->addItem((new ItemTextHtmlHelper())
                ->setLabel('Получатель')
                ->setName('reciver')
                ->setClass('form-control')
                ->required()
            ))
            ->addGroup((new FormGroupHtmlHelper('Информация о юр лице', 2))
                ->addItem((new ItemSelectHtmlHelper())
                    ->setLabel('Ставка НДС')
                    ->setName('nds')
                    ->setClass('form-control')
                    ->required()
                    ->makeAChoice()
                    ->addSelectAllow('10', 'НДС 10%')
                    ->addSelectAllow('18', 'НДС 18%')
                    ->addSelectAllow('20', 'НДС 20%')
                    ->addSelectAllow('0', 'НДС не облагается')
                )
                ->addItem((new ItemTextHtmlHelper())
                    ->setLabel('индекс')
                    ->setName('index')
                    ->setClass('form-control')
                    ->required()
                )
                ->addItem((new ItemTextHtmlHelper())
                    ->setLabel('Адрес')
                    ->setName('adress')
                    ->setClass('form-control')
                    ->required()
                )
                ->addItem((new ItemTextHtmlHelper())
                    ->setLabel('Руководитель')
                    ->setName('leader')
                    ->setClass('form-control')
                    ->required()
                )
                ->addItem((new ItemFileHtmlHelper())
                    ->setLabel('Подпись')
                    ->setName('leader-sign')
                    ->required()
                )
                ->addItem((new ItemTextHtmlHelper())
                    ->setLabel('Бухгалтер')
                    ->setName('bookkeeper')
                    ->setClass('form-control')
                    ->required()
                )
                ->addItem((new ItemFileHtmlHelper())
                    ->setLabel('Подпись')
                    ->setName('bookkeeper-sign')
                    ->required()
                )
                ->addItem((new ItemFileHtmlHelper())
                    ->setLabel('Печать')
                    ->setName('printing-sign')
                    ->required()
                )
            )
            ->addGroup((new FormGroupHtmlHelper('Настраиваемые поля счета', 3))
                ->addItem((new ItemTextHtmlHelper())
                    ->setLabel('Зона коментария 1')
                    ->setName('comment1')
                    ->setDescription('макросы: %invoice.date%, %invoice.id%')
                    ->setClass('form-control')
                    ->required()
                )
                ->addItem((new ItemTextHtmlHelper())
                    ->setLabel('Зона коментария 2')
                    ->setName('comment2')
                    ->setDescription('макросы: %invoice.date%, %invoice.id%')
                    ->setClass('form-control')
                    ->required()
                )
                ->addItem((new ItemTextHtmlHelper())
                    ->setLabel('Зона коментария 3')
                    ->setName('comment3')
                    ->setDescription('макросы: %invoice.date%, %invoice.id%')
                    ->setClass('form-control')
                    ->required()
                )
            )
            ->addGroup((new FormGroupHtmlHelper('Дополнительные опции', 4))
                ->addItem((new ItemCheckboxHtmlHelper())
                    ->setLabel('Отправлять со счетом PDF')
                    ->setName('sendPdf')
                    ->setClass('form-check-input')
                )->addItem((new ItemTextHtmlHelper())
                    ->setLabel('Наименование услуги "пополнение баланса"')
                    ->setName('addFundsName')
                    ->setClass('form-control')
                ))
            ->addGroup((new FormGroupHtmlHelper('Настройка авто добавления клиента в группу', 5))
                ->addItem((new ItemSelectClientGroupHtmlHelper())
                    ->setLabel('Группа для юридических лиц')
                    ->setName('client_legal_entities_group_id')
                    ->makeAChoice()
                    ->setDefaultName('Группа для юридических лиц')
                    ->setClass('form-control')
                    ->required()
                ))
            ->addGroup((new FormGroupHtmlHelper('Настройка дополнительных полей клиента', 6))
                ->addItem((new ItemSelectClientCustomFieldHtmlHelper())
                    ->setLabel('Банк получателя')
                    ->setName('client_payeesBank_id')
                    ->setDefaultSortOrder(13)
                    ->setDefaultType('text')
                    ->setDefaultName('Банк получателя')
                    ->setDefaultShowOrder()
                    ->setClass('form-control')
                    ->textBox()
                    ->textArea()
                    ->makeAChoice()
                    ->required()
                )
                ->addItem((new ItemSelectClientCustomFieldHtmlHelper())
                    ->setLabel('БИК')
                    ->setName('client_bik_id')
                    ->setDefaultType('text')
                    ->setDefaultSortOrder(12)
                    ->setDefaultName('БИК')
                    ->setDefaultShowOrder()
                    ->setClass('form-control')
                    ->textBox()
                    ->textArea()
                    ->makeAChoice()
                    ->required()
                )
                ->addItem((new ItemSelectClientCustomFieldHtmlHelper())
                    ->setLabel('№Р/С')
                    ->setName('client_pc_id')
                    ->setDefaultType('text')
                    ->setDefaultSortOrder(11)
                    ->setDefaultName('№Р/С')
                    ->setDefaultShowOrder()
                    ->setClass('form-control')
                    ->textBox()
                    ->textArea()
                    ->makeAChoice()
                    ->required()
                )
                ->addItem((new ItemSelectClientCustomFieldHtmlHelper())
                    ->setLabel('№К/С')
                    ->setName('client_kc_id')
                    ->setDefaultType('text')
                    ->setDefaultSortOrder(10)
                    ->setDefaultName('№К/С')
                    ->setDefaultShowOrder()
                    ->setClass('form-control')
                    ->textBox()
                    ->textArea()
                    ->makeAChoice()
                    ->required()
                )
                ->addItem((new ItemSelectClientCustomFieldHtmlHelper())
                    ->setLabel('КПП')
                    ->setName('client_kpp_id')
                    ->setDefaultType('text')
                    ->setDefaultSortOrder(9)
                    ->setDefaultName('КПП')
                    ->setDefaultShowOrder()
                    ->setClass('form-control')
                    ->textBox()
                    ->textArea()
                    ->makeAChoice()
                    ->required()
                )
                ->addItem((new ItemSelectClientCustomFieldHtmlHelper())
                    ->setLabel('ИНН')
                    ->setName('client_inn_id')
                    ->setDefaultType('text')
                    ->setDefaultSortOrder(8)
                    ->setDefaultShowOrder()
                    ->setDefaultName('ИНН')
                    ->setClass('form-control')
                    ->textBox()
                    ->textArea()
                    ->makeAChoice()
                    ->required()
                )
                ->addItem((new ItemSelectClientCustomFieldHtmlHelper())
                    ->setLabel('ОГРН')
                    ->setName('client_ogrn_id')
                    ->setDefaultName('ОГРН')
                    ->setDefaultSortOrder(7)
                    ->setDefaultShowOrder()
                    ->setDefaultType('text')
                    ->setClass('form-control')
                    ->textBox()
                    ->textArea()
                    ->makeAChoice()
                    ->required()
                )
                ->addItem((new ItemSelectClientCustomFieldHtmlHelper())
                    ->setLabel('Должность руководителя')
                    ->setName('client_head_position_id')
                    ->setDefaultShowOrder()
                    ->setDefaultSortOrder(6)
                    ->setDefaultName('Должность руководителя')
                    ->setDefaultType('text')
                    ->setClass('form-control')
                    ->textBox()
                    ->textArea()
                    ->makeAChoice()
                    ->required()
                )
                ->addItem((new ItemSelectClientCustomFieldHtmlHelper())
                    ->setLabel('ФИО Руководителя')
                    ->setName('client_full_name_of_the_head_id')
                    ->setDefaultType('text')
                    ->setDefaultShowOrder()
                    ->setDefaultSortOrder(5)
                    ->setDefaultName('ФИО Руководителя')
                    ->setClass('form-control')
                    ->textBox()
                    ->textArea()
                    ->makeAChoice()
                    ->required()
                )
                ->addItem((new ItemSelectClientCustomFieldHtmlHelper())
                    ->setLabel('Действует на основании')
                    ->setName('client_acts_on_the_basis_id')
                    ->setDefaultShowOrder()
                    ->setDefaultName('Действует на основании')
                    ->setDefaultSortOrder(4)
                    ->setDefaultType('text')
                    ->setClass('form-control')
                    ->textBox()
                    ->textArea()
                    ->makeAChoice()
                    ->required()
                )
                ->addItem((new ItemSelectClientCustomFieldHtmlHelper())
                    ->setLabel('Юридический адрес')
                    ->setName('client_address_id')
                    ->setDefaultName('Юридический адрес')
                    ->setDefaultSortOrder(3)
                    ->setDefaultShowOrder()
                    ->setDefaultType('text')
                    ->setClass('form-control')
                    ->textBox()
                    ->textArea()
                    ->makeAChoice()
                    ->required()
                )
                ->addItem((new ItemSelectClientCustomFieldHtmlHelper())
                    ->setLabel('Наличие ЭДО')
                    ->setName('client_edf_exits_id')
                    ->setDefaultName('Есть ЭДО')
                    ->setDefaultSortOrder(2)
                    ->setDefaultType('tickbox')
                    ->setDefaultShowOrder()
                    ->setClass('form-control')
                    ->yesNo()
                    ->makeAChoice()
                    ->required()
                )
                ->addItem((new ItemSelectClientCustomFieldHtmlHelper())
                    ->setLabel('Тип аккаунта')
                    ->setName('client_account_type_id')
                    ->setDefaultType('tickbox')
                    ->setDefaultShowOrder()
                    ->setDefaultSortOrder(1)
                    ->setDefaultName('Юридическое лицо')
                    ->setClass('form-control')
                    ->yesNo()
                    ->makeAChoice()
                    ->required()
                ));

        return $form;
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
            'Главная' => '',
        ];
    }
}