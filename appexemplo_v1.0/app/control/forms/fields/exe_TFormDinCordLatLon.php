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

        // load the styles
        TPage::include_css('app/resources/css_form02.css');             

        $frm = new TFormDin($this,'Exemplo TFormDinCordLatLon & TFormDinMapCord');
        $frm->addHiddenField('id'); //POG para evitar problema de noticie

        // 1. Componente Tradicional de Geolocalização (Navegador)
        $frm->addGroupField('gp01','1. Geolocalização via Navegador (Tradicional)');
        $frm->addCordLatLon('cood','Coordenadas GPS',true,true,false);
        $frm->closeGroup();

        // 2. Novo Componente: Mapa Interativo com Inputs Visíveis (Leaflet)
        $frm->addGroupField('gp02','2. Mapa Interativo (Leaflet) - Inputs Visíveis e Arrastável');
        $msgMapVisible = 'Arraste o marcador ou clique no mapa para definir as coordenadas. Os campos de input abaixo atualizarão automaticamente. Você também pode digitar nos inputs para reposicionar o marcador no mapa.';
        $frm->addHtmlField('hint_visible', $msgMapVisible, null, 'Como usar:', null, null, true)->setClass('notice');
        // addMapCord params: idField, label, required, newLine, labelAbove, showFields, readOnly, defaultLat, defaultLon, zoom, height
        $frm->addMapCord('map_visible', 'Coordenadas Mapa', true, true, false, true, false, -15.793889, -47.882778, 12, 350);
        $frm->closeGroup();

        // 3. Novo Componente: Mapa Interativo com Inputs Escondidos (Leaflet)
        $frm->addGroupField('gp03','3. Mapa Interativo (Leaflet) - Inputs Ocultos');
        $msgMapHidden = 'O mapa abaixo interage com campos de input invisíveis (hidden). Excelente para interfaces mais limpas.';
        $frm->addHtmlField('hint_hidden', $msgMapHidden, null, 'Dica:', null, null, true)->setClass('notice');
        $frm->addMapCord('map_hidden', 'Localização Oculta', false, true, false, false, false, -23.550520, -46.633308, 13, 300);
        $frm->closeGroup();

        // 4. Novo Componente: Mapa Interativo em Modo Somente Leitura (Read-Only)
        $frm->addGroupField('gp04','4. Mapa Interativo (Leaflet) - Modo Somente Leitura');
        $msgMapReadOnly = 'O mapa abaixo está em modo somente leitura (ReadOnly). O marcador não pode ser arrastado e cliques no mapa estão desabilitados. Útil para exibição estática de pontos geográficos cadastrados.';
        $frm->addHtmlField('hint_readonly', $msgMapReadOnly, null, 'Nota:', null, null, true)->setClass('notice');
        $frm->addMapCord('map_readonly', 'Ponto Histórico (Rio de Janeiro)', false, true, false, true, true, -22.906847, -43.172896, 14, 250);
        $frm->closeGroup();

        $this->form = $frm->show();
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));

        // add form actions
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
            // Função do FormDin para Debug
            FormDinHelper::d($param,'$param');

            $text[] = '<h3>Dados Recebidos com Sucesso!</h3>';
            
            // 1. Coordenadas tradicionais
            if (!empty($param['cood_lat'])) {
                $text[] = '<b>1. Geolocalização (Tradicional):</b>';
                $text[] = 'Lat: ' . $param['cood_lat'] . ' | Lon: ' . $param['cood_lon'];
                $text[] = '<a href="https://www.openstreetmap.org/?mlat='.$param['cood_lat'].'&mlon='.$param['cood_lon'].'" target="_blank">Ver no OpenStreetMap</a><br>';
            }

            // 2. Mapa Visível
            if (!empty($param['map_visible_lat'])) {
                $text[] = '<b>2. Mapa com Inputs Visíveis (Leaflet):</b>';
                $text[] = 'Lat: ' . $param['map_visible_lat'] . ' | Lon: ' . $param['map_visible_lon'];
                $text[] = '<a href="https://www.openstreetmap.org/?mlat='.$param['map_visible_lat'].'&mlon='.$param['map_visible_lon'].'" target="_blank">Ver no OpenStreetMap</a><br>';
            }

            // 3. Mapa Oculto
            if (!empty($param['map_hidden_lat'])) {
                $text[] = '<b>3. Mapa com Inputs Ocultos (Leaflet):</b>';
                $text[] = 'Lat: ' . $param['map_hidden_lat'] . ' | Lon: ' . $param['map_hidden_lon'];
                $text[] = '<a href="https://www.openstreetmap.org/?mlat='.$param['map_hidden_lat'].'&mlon='.$param['map_hidden_lon'].'" target="_blank">Ver no OpenStreetMap</a><br>';
            }

            $text = TFormDinMessage::messageTransform($text);
            new TMessage(TFormDinMessage::TYPE_INFO, $text);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}