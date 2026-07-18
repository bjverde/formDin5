<?php

class DashboardGeral extends TPage
{
    // PSR-12 / PER Coding Style)
    // 1. Constantes
    
    // 2. Variáveis Estáticas
    private static $formName = 'form_DashboardGeral';

    // 3. Variáveis Normais (de Instância)
    protected $form;
    private $formFields = [];
    private $controllerVendaInfo;

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param = null)
    {
        parent::__construct();

        if(!empty($param['target_container'])){
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("DASHBOARD GERAL");

        $vc_dt_include_inicial = new TDateTime('vc_dt_include_inicial');
        $vc_dt_include_inicial->setDatabaseMask('yyyy-mm-dd hh:ii');
        $vc_dt_include_inicial->setMask('dd/mm/yyyy hh:ii');
        $vc_dt_include_inicial->setSize('100%');

        $vc_dt_include_final = new TDateTime('vc_dt_include_final');
        $vc_dt_include_final->setDatabaseMask('yyyy-mm-dd hh:ii');
        $vc_dt_include_final->setMask('dd/mm/yyyy hh:ii');
        $vc_dt_include_final->setSize('100%');

        $this->form->addFields([Constantes::getObjTLabel("Unidade")],[$vc_system_unit_id]
                              ,[Constantes::getObjTLabel("Vendedor")],[$vc_vendedor_id]);

        $this->form->addFields([Constantes::getObjTLabel("Data venda de")],[$vc_dt_include_inicial]
                              ,[Constantes::getObjTLabel("Data venda até")],[$vc_dt_include_final]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_ongenerate = $this->form->addAction("Gerar", new TAction([$this, 'onGenerate']), 'fas:tachometer-alt #ffffff');
        $this->btn_ongenerate = $btn_ongenerate;
        $btn_ongenerate->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar", new TAction([$this, 'onClear']), 'fas:eraser #F44336');
        $this->btn_onclear = $btn_onclear;


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container'])){
            $container->add(TBreadCrumb::create(["DASHBOARD","DASHBOARD GERAL"]));
        }
        $container->add($this->form);

        parent::add($container);

    } // fim do construct


    public function onClear($param){
        TFormDin::clearListFilter($this, __CLASS__);
    }    

    /**
     * Load the datagrid with data
     */
    public function onGenerate($param = null)
    {
        try {
            $data = $this->form->getData();

            $data_inicial = $data->vc_dt_include_inicial;
            $data_final = $data->vc_dt_include_final;
            $vendedor_id = $data->vc_vendedor_id;
            $system_unit_id = $data->vc_system_unit_id;

            //parent::add(new TFormSeparator("Vagas", null, '18', '#eee'));
            //parent::add( $this->getDivInfoVagas() );
            parent::add(new TFormSeparator("Certificados", null, '18', '#eee'));
            parent::add( $this->getDivInfoCertificados($data_inicial, $data_final, $vendedor_id, $system_unit_id) );
            parent::add( $this->getDivInfoVendasMock() );
            parent::add( $this->getDivInfoStatusMock() );
            parent::add( $this->getDivCertificadosAvencer($vendedor_id, $system_unit_id) );
            parent::add( $this->getDivGrafPizza($data_inicial, $data_final, $vendedor_id, $system_unit_id) );
            parent::add( $this->getDivGrafVendaDia($data_inicial, $data_final, $vendedor_id, $system_unit_id) );
            //parent::add(new TFormSeparator("Passageiros", null, '18', '#eee'));
            //parent::add( $this->getDivInfoResumoPassageirosDia($data->dtInclusao) );
            //parent::add( $this->getDivTipoVeiculo($data->dtInclusao) );
            //parent::add( $this->getDivTipoCracha($data->dtInclusao) );


            // FIM OLD Dashboard --------------------------------------------------

            /*
            $saidaVeiculosController = new Saidas_veiculosController();
            $listVeiculos = $saidaVeiculosController->selectGroupByVeiculo($data);
            $resultQtdVeiculo = CountHelper::count($listVeiculos);
            $listMotoristas = $saidaVeiculosController->selectGroupByMotorista($data);
            $resultQtdMotorista = CountHelper::count($listMotoristas);            

            $infoBoxRegistro = $this->showInfoBox('Quantidade de saídas','car-side','orange',$qtdRegistro);
            $i1 = TElement::tag('div', $infoBoxRegistro);
            $i1->class = 'col-sm-4';

            $infoBoxVeiculos = $this->showInfoBox('Quantidade de carros','car','green',$resultQtdVeiculo);
            $i2 = TElement::tag('div', $infoBoxVeiculos);
            $i2->class = 'col-sm-4';

            $infoBoxMotoristas = $this->showInfoBox('Quantidade de motoristas','user',null,$resultQtdMotorista);
            $i3 = TElement::tag('div', $infoBoxMotoristas);
            $i3->class = 'col-sm-4';

            $div = new TElement('div');
            $div->class = "row";                                            
            $div->add( $i1 );
            $div->add( $i2 );
            $div->add( $i3 );
            $div->add( $this->showPieChartVeiculos($listVeiculos) );
            $div->add( $this->showPieChartMotoristas($listMotoristas) );
            $div->add( $this->showBarChartVeiculos($listVeiculos) );
            $div->add( $this->showBarChartMotoristas($listMotoristas) );
            parent::add($div);
            $this->showGridVeiculos($listVeiculos);
            $this->showGridMotoristas($listMotoristas);
            */


        }
        catch (Exception $e){
            // shows the exception error message
            new TMessage('error', $e->getMessage());
        }
    }


    public function onShow($param = null)
    {
        $this->onGenerate($param);
    }

    public function onReload($param = null)
    {
        $this->onGenerate($param);
    }


    public function getDivInfoCertificados($data_inicial, $data_final, $vendedor_id, $system_unit_id){
        $qtd_vendas_item = $this->controllerVendaInfo->getQtdVendasCertificadoItem(true,$data_inicial, $data_final, $vendedor_id, $system_unit_id);

        $entrada= TFormDinGraph::showInfoBox('Certificados vendidos','sign-in-alt','blue',$qtd_vendas_item);
        $saida  = TFormDinGraph::showInfoBox('XXXX','sign-out-alt','red',0);
        $saldo  = TFormDinGraph::showInfoBox('XXX','car fa-fw','black',0);

        $divInfoVagas = new TElement('div');
        $divInfoVagas->class = 'row';
        $divInfoVagas->setProperty('id','info_vagas');

        $i1 = TElement::tag('div', $entrada);
        $i1->class = 'col-sm-4';

        $i2 = TElement::tag('div', $saida);
        $i2->class = 'col-sm-4';

        $i3 = TElement::tag('div', $saldo);
        $i3->class = 'col-sm-4';

        $divInfoVagas->add( $i1 );
        $divInfoVagas->add( $i2 );
        $divInfoVagas->add( $i3 );

        return $divInfoVagas;
    }



    ///-----------------------------------------------
    /// Métricas
    ///-----------------------------------------------

    public function getGridTipoVeiculo($titulo,$listDados){
        $gridSocios = new BootstrapDatagridWrapper(new TDataGrid);
        $gridSocios->width = '100%';        

        $gridSocios->addColumn(new TDataGridColumn('DSTIPOACESSOVEICULO',"Tipo", 'right'));
        $gridSocios->addColumn(new TDataGridColumn('QTD', "Saída", 'right'));
        $gridSocios->createModel();
        $gridSocios->addItems($listDados);
        $panel = TPanelGroup::pack($titulo, $gridSocios);
        return $panel;
    }  

    public function getGridTipoCracha($titulo,$listDados){
        $gridSocios = new BootstrapDatagridWrapper(new TDataGrid);
        $gridSocios->width = '100%';
        
        $gridSocios->addColumn(new TDataGridColumn('DSTIPOSITUACAOCRACHA',"Cracha", 'right'));
        $gridSocios->addColumn(new TDataGridColumn('QTD', "Saída", 'right'));
        $gridSocios->createModel();
        $gridSocios->addItems($listDados);
        $panel = TPanelGroup::pack($titulo, $gridSocios);
        return $panel;
    }

    public function getDivTipoCracha($dtInclusao){
        $listDados = $this->controller->selectGroupBySituacaoCracha($dtInclusao);

        $titulo = Dash::LABEL_CRACHA;
        $grid = $this->getGridTipoCracha($titulo,$listDados);

        $dadosGraf = [ ['Tipo', 'Quantidade'] ];        
        foreach ($listDados as $obj) {
            $item = [$obj->DSTIPOSITUACAOCRACHA, (int)$obj->QTD];
            array_push($dadosGraf,$item);
        }
        $graf = TFormDinGraph::gerarPieChart($dadosGraf,'100%','400px',$titulo,'Quantidade','Status');

        $div = new TElement('div');
        $div->class = 'row';
        $div->setProperty('id','tipo_cracha');

        $i1 = TElement::tag('div', $graf);
        $i1->class = 'col-sm-6';

        $i2 = TElement::tag('div', $grid);
        $i2->class = 'col-sm-6';

        $div->add( $i1 );
        $div->add( $i2 );
        return $div;
    }

    public function getDivInfoStatusMock(){
        $qtd_vencidos          = TFormDinGraph::showInfoBox('Qtd Vencidos', 'times-circle', 'red', 5);
        $qtd_avencer           = TFormDinGraph::showInfoBox('Qtd a Vencer', 'exclamation-triangle', 'yellow', 15);
        $qtd_ativo             = TFormDinGraph::showInfoBox('Qtd Ativo', 'check-circle', 'green', 100);
        $certificados_vendidos = TFormDinGraph::showInfoBox('Certificados Vendidos', 'certificate', 'purple', 80);

        $div = new TElement('div');
        $div->class = 'row';
        $div->setProperty('id','info_status_mock');

        $i1 = TElement::tag('div', $qtd_vencidos);
        $i1->class = 'col-sm-3';

        $i2 = TElement::tag('div', $qtd_avencer);
        $i2->class = 'col-sm-3';

        $i3 = TElement::tag('div', $qtd_ativo);
        $i3->class = 'col-sm-3';

        $i4 = TElement::tag('div', $certificados_vendidos);
        $i4->class = 'col-sm-3';

        $div->add( $i1 );
        $div->add( $i2 );
        $div->add( $i3 );
        $div->add( $i4 );

        return $div;
    }

}//fim DashboardGeral