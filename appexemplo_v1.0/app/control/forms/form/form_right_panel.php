<?php
class form_right_panel extends TPage
{
    protected $form; //Registration form Adianti
    protected $frm;  //Registration component FormDin 5
    protected $adianti_target_container;

    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
        $this->adianti_target_container = 'adianti_right_panel';

        $frm = new TFormDin($this,'Painel a direita generico');
        $frm->addHiddenField('id'); //POG para evitar problema de noticie
        $frm->addHtmlField('html1', '<b>Painel a direita generico.', null, 'Dica:', null, 200);

        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        //$frm->setActionLink( _t('Close'), 'onClose', null, 'fa:times', 'red');
        $frm->setActionHeaderLink( _t('Close'), 'onClose', null, 'fa:times', 'red');

        $this->form = $frm->show();
        
        // add the table inside the page
        parent::add($this->form);
        parent::add($this->html);
    }    

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }    
    /**
     * Clear filters
     */
    public function onClear()
    {
        $this->clearFilters();
        $this->onReload();
    } 
}