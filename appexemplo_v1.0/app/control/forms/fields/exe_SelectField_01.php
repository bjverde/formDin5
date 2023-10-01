<?php

use Adianti\Registry\TSession;

class exe_SelectField_01 extends TPage
{
    protected $form; // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    // trait com onSave, onClear, onEdit...
    use Adianti\Base\AdiantiStandardFormTrait;
    // trait com onReload, onSearch, onDelete...
    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $frm = new TFormDin($this,'Exemplo do Campo Select Simples');

        $msg = '<b>O Select no FD5 mudou o comportamento padrão</b>.';
        $msg = $msg.'<br>No FD4 o padrão é o select sem autocomplete. Para usar esse padrão o atributo 17 desativa o autocomplete';
        $msg = $msg.'<br>No FD5 o padráo é o select COM autocomplete.';
        $frm->addHtmlField('fd5_html2',$msg, null, 'Dica:', null, 200);

        $frm->addGroupField('gp1', 'Selects Normais');
        $listFormas = array();
        $listFormas[1] ='Dinheiro';
        $listFormas[2] ='Cheque';
        $listFormas[3] ='Cartão';
        $listFormas[4] ='BitCoin';
        $listFormas[5] ='PayPal';
        $listFormas[6] ='Pag Seguro';
        $listFormas[7] ='Moderninha';
        $listFormas[8] ='Dolar';
        $listFormas[9] ='Euro';
        $listFormas[10]='Bolivares';
        $listFormas[11]='Vale transporte';
        $listFormas[12]='Galinha';
        $listFormas[13]='Bode';
        $listFormas[14]='Milho';
        $frm->addSelectField('forma_pagamento'  // 01: ID do campo
                           , 'Forma Pagamento:'
                           , TRUE         // 03: Obrigatorio
                           , $listFormas  // 04: array dos valores
                           , null         // 05: Default TRUE = cria nova linha , FALSE = fica depois do campo anterior
                           , null         // 06: Default FALSE = Label mesma linha, TRUE = Label acima
                           , null         // 07: Informe o ID do array ou array com a lista de ID's no formato "key=>id" para identificar a(s) opção(ões) selecionada(s)
                           , null         // 08: Default FALSE = SingleSelect, TRUE = MultiSelect
                           , null         // 09: Num itens que irão aparecer
                           , null         // 10: Largura em Pixels
                           , ' '          // 11: First Key in Display
                           , null         // 12: Frist Value in Display, use value NULL for required
                           , null         // 13: Nome coluna para preencher os valores
                           , null         // 14: Nome colune para exiver para usuario
                           , null         // 15:
                           , null         // 16:
                           , false        // 17: FORMDIN5: Define o se compote terá autocomplete
                           );
        
        $fg2 = $frm->addSelectField('forma_pagamento2'     // 01: ID do campo
                                   , 'Forma Pagamento (DEFAULT):'
                                   , TRUE                  // 03: Obrigatorio
                                   , $listFormas           // 04: array dos valores
                                   , true                  // 05: Default TRUE = cria nova linha , FALSE = fica depois do campo anterior
                                   , true                  // 06: Default FALSE = Label mesma linha, TRUE = Label acima
                                   , 2                     // 07: Informe o ID do array ou array com a lista de ID's no formato "key=>id" para identificar a(s) opção(ões) selecionada(s)
                                   , null  // 08: Default FALSE = SingleSelect, TRUE = MultiSelect
                                   , null  // 09: Num itens que irão aparecer
                                   , null  // 10: Largura em Pixels
                                   , null  // 11: First Key in Display
                                   , null  // 12: Frist VALUE in Display, use value NULL for required
                                   , 2     // 13: Nome coluna para preencher os valores
                                   , null  // 14: Nome colune para exiver para usuario
                                   , null  // 15
                                   , null  // 16
                                   , false // 17: FORMDIN5: Define o se compote terá autocomplete
                                   );
        $fg2->setToolTip('Campo com valor DEFAULT pré-selecionado');
        
        $fg3 = $frm->addSelectField('forma_pagamento3'     // 1: ID do campo
                                , 'Forma Pagamento (DEFAULT):'
                                , TRUE        // 03: Obrigatorio
                                , $listFormas // 04: array dos valores
                                , true        // 05: Default TRUE = cria nova linha , FALSE = fica depois do campo anterior
                                , true        // 06: Default FALSE = Label mesma linha, TRUE = Label acima
                                , null        // 07: Valor DEFAULT, informe o ID do array
                                , null        // 08: Default FALSE = SingleSelect, TRUE = MultiSelect
                                , null        // 09: Num itens que irão aparecer
                                , null        // 10: Largura em Pixels
                                , ' '         // 11: First Key in Display
                                , 4           // 12: Frist VALUE in Display, use value NULL for required
                                , null        // 13: Nome coluna para preencher os valores
                                , null        // 14: Nome colune para exiver para usuario
                                , null        // 15
                                , null        // 16
                                , false       // 17: FORMDIN5: Define o se compote terá autocomplete
                                );
        $fg3->setToolTip('Campo com valor DEFAULT pré-selecionado, ATRIBUTO 12');
        
        
        //$frm->addSelectField('estado', 'Estado (todos):', false, 'tb_uf', true, false, null, false, null, null, '-- selecione o Estado --', null, 'COD_UF', 'NOM_UF', null, 'cod_regiao')->addEvent('onChange', 'select_change(this)')->setToolTip('SELECT - esta campo select possui o código da região adicionado em sua tag option. Para adicionar dados extras a um campo select, basta definir as colunas no parâmetro $arrDataColumns.');
        $frm->closeGroup();
        
        $frm->addGroupField('gp5', 'Selects MultiSelect');
            $arrayBiomas = array();
            $arrayBiomas['AM']='Amazônia';
            $arrayBiomas['CE']='CERRADO';
            $arrayBiomas['CA']='Caatinga';
            $arrayBiomas['PA']='PANTANAL';
            $arrayBiomas['MA']='MATA ATLÂNTICA';
            $arrayBiomas['PM']='Pampa';
            $frm->addSelectField('seq_bioma'
                                , 'Bioma:'
                                , true
                                , $arrayBiomas  // 4: array dos valores
                                , null
                                , null
                                , null
                                , true);
            
            $frm->addSelectField('seq_bioma2'
                                , 'Bioma:'
                                , false
                                , $arrayBiomas // 4: array dos valores
                                , null
                                , null
                                , 3            // 7: Valor DEFAULT, informe o ID do array array(0=>3,1=>4)
                                , true);
        
            /*
            $frm->addSelectField('estadomultiselect'
                                , 'Estado (todos):'
                                , false
                                , 'tb_uf'      // 4: array dos valores. tb_uf vem do SqLite Configurdo na app de exemplo
                                , true
                                , false
                                , null
                                , true
                                , 5          // 9: Num itens que irão aparecer
                                , null       // 10: Largura em Pixels
                                , '-- selecione o Estado --'
                                , null
                                , 'COD_UF'
                                , 'NOM_UF'
                                , null
                                , 'cod_regiao');
            */
        $frm->closeGroup();


        $frm->addGroupField('fd5', 'FormDin 5');
        $msg = '<b>Não é um select que busca no banco</b>.'
              .'<br>O select abaixo com busca, coloca todas as opções no html da tela. Não faz a busca no banco.'
              .'<br><a href="index.php?class=exe_SelectFielddb">Exemplo com busca no banco</a>';
        $frm->addHtmlField('fd5_html1',$msg, null, 'Dica:', null, 200);
        

        $fg4 = $frm->addSelectField('forma_pagamento1_fd5'  // 1: ID do campo
                                    , 'Select com busca:'
                                    , false                  // 3: Obrigatorio
                                    , $listFormas           // 4: array dos valores
                                    , true                  // 5: Default TRUE = cria nova linha , FALSE = fica depois do campo anterior
                                    , false                  // 6: Default FALSE = Label mesma linha, TRUE = Label acima
                                    , null                  // 7: Valor DEFAULT, informe o ID do array
                                    , null
                                    , null                  //  9: Num itens que irão aparecer
                                    , null   // 10: Largura em Pixels
                                    , null  // 11 First Key in Display
                                    , null  // 12 Frist VALUE in Display, use value NULL for required
                                    , 2
                                    );


        $msg = '<b>Select com ICONE</b>.';
        $msg = $msg.'<br>Busque por nome de paises, EXEMPLO MAR';
        $msg = $msg.'<br>É possível incluir emojis basta copiar de colar do site <a href="https://emojipedia.org/pt">https://emojipedia.org/pt</a>';              
        $frm->addHtmlField('fd5_html2',$msg, null, 'Dica:', null, 200);        
        $listDdi = HtmlHelper::getListDdi();
        $fg5 = $frm->addSelectField('ddi_fd5'  // 1: ID do campo
                                    , 'Select com busca:'
                                    , false                  // 3: Obrigatorio
                                    , $listDdi           // 4: array dos valores
                                    , true                  // 5: Default TRUE = cria nova linha , FALSE = fica depois do campo anterior
                                    , false                  // 6: Default FALSE = Label mesma linha, TRUE = Label acima
                                    , null                  // 7: Valor DEFAULT, informe o ID do array
                                    , null
                                    , null                  //  9: Num itens que irão aparecer
                                    , null   // 10: Largura em Pixels
                                    , null  // 11 First Key in Display
                                    , null  // 12 Frist VALUE in Display, use value NULL for required
                                    , 2
                                    );

        // O Adianti permite a Internacionalização - A função _t('string') serve
        //para traduzir termos no sistema. Veja ApplicationTranslator escrevendo
        //primeiro em ingles e depois traduzindo
        $frm->setAction( _t('Save'), 'onSave', null, 'fa:save', 'green' );
        $frm->setActionLink( _t('Clear'), 'onClear', null, 'fa:eraser', 'red');

        $this->form = $frm->show();

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
    public function onClear()
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