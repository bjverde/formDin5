<?php

use Adianti\Registry\TSession;

class exe_TFormDinCordLatLon extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Exemplo TFormDinCordLatLon');
        $frm->addHiddenField('id'); //POG para evitar problema de noticie

        $frm->addCordLatLon('cood','Coordenadas',false,false,false);
       

        $this->form = $frm->show();
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));

        // add form actions
        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:check-circle green');
        $this->form->addActionLink(_t('Clear'),  new TAction([$this, 'clear']), 'fa:eraser red');

        // creates the page structure using a table
        $formDinBreadCrumb = new TFormDinBreadCrumb(__CLASS__);
        $vbox = $formDinBreadCrumb->getAdiantiObj();
        $vbox->add($this->form);
        
        // add the table inside the page
        parent::add($vbox);
    }

    /**
     * Clear filters
     */
    public function clear()
    {
        $this->clearFilters();
        $this->onReload();
    }

    public function onSave($param)
    {
        try
        {
            //$data = $this->form->getData();
            //$this->form->setData($data);
            //$this->form->validate();
            //Função do FormDin para Debug
            FormDinHelper::d($param,'$param');
            $latitude  = $param['cood_lat'];
            $longitude = $param['cood_lon'];

            $text[] = 'Tudo OK!';
            $text[] = '<a href="https://www.google.com/maps?q='.$latitude.','.$longitude.'" target="_blank">Abrir no google Maps</a>';
            $text[] = '<a href="https://www.bing.com/maps?cp='.$latitude.'~'.$longitude.'" target="_blank">Bing Maps</a>';
            $text[] = '<a href="https://www.openstreetmap.org/?mlat='.$latitude.'&mlon='.$longitude.'" target="_blank">OpenStreetMap (OSM)</a>';
            $text[] = '<a href="https://www.here.com/directions/drive/mylocation='.$latitude.','.$longitude.'" target="_blank">HERE Maps</a>';
            $text = TFormDinMessage::messageTransform($text);
            new TMessage(TFormDinMessage::TYPE_INFO, $text);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}