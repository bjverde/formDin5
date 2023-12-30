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


        //-15.807197901482752
        $idField = 'cood';
        $boolRequired = false;
        //$fd5Lat = new TFormDinNumericField($idField.'_lat','Latitude',$boolRequired,21);
        //$adiantiObjLat = $fd5Lat->getAdiantiObj();
        $adiantiObjLat = new TEntry($idField.'_lat');
        $adiantiObjLat->id = $idField.'_lat';

        $adiantiObjLon = new TEntry($idField.'_lon');
        $adiantiObjLon->id = $idField.'_lon';

        $adiantiObjAlt = new TEntry($idField.'_alt');
        $adiantiObjAlt->id = $idField.'_alt';        

        $scriptJswebCam = new TElement('script');
        $scriptJswebCam->setProperty('src', 'app/lib/widget/FormDin5/javascript/FormDin5GeoLocation.js?appver='.FormDinHelper::version());

        $btnScreenshot = new TButton('btnScreenshot');
        $btnScreenshot->class = 'btn btn-primary btn-sm';
        $btnScreenshot->setLabel('Informar Geolocalização');
        $btnScreenshot->setImage('fas:map-marker');
        $btnScreenshot->addFunction("fd5GetLocation('".$idField."')");        

        $idDivWebCam = $idField.'_videodiv';
        $divWebCam = new TElement('div');
        $divWebCam->class = 'fd5DivVideo';
        $divWebCam->setProperty('id',$idDivWebCam);
        $divWebCam->add($btnScreenshot);
        $divWebCam->add($adiantiObjLat);
        $divWebCam->add($adiantiObjLon);
        $divWebCam->add($adiantiObjAlt);
        $divWebCam->add($scriptJswebCam);

        $row2 = $frm->addFields([new TLabel("Cod Regiao:", '#FF0000', '14px', null)],[$divWebCam]
                                      ,[],[]);
       

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
            $data = $this->form->getData();
            $this->form->setData($data);
            $this->form->validate();
            
    
            //Função do FormDin para Debug
            FormDinHelper::d($param,'$param');
            FormDinHelper::debug($data,'$data');
            FormDinHelper::debug($_REQUEST,'$_REQUEST');

            new TMessage('info', 'Tudo OK!');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}