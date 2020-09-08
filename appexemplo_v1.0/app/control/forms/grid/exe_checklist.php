<?php
    /**
     * FormCheckListView
     *
     * @version    1.0
     * @package    samples
     * @subpackage tutor
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class exe_checklist extends TPage
    {
        private $form;
        
        public function __construct()
        {
            parent::__construct();
            
            $this->form = new BootstrapFormBuilder;
            $this->form->setFormTitle('Checklist');
            
            $orderlist = new TCheckList('order_list');
            $orderlist->addValidation('Order list', new TRequiredValidator); // cannot be less the 3 characters
            
            //$orderlist->addColumn('id',          'Id',          'center',  '10%');
            $orderlist->addColumn('description', 'Description', 'left',    '50%');
            $orderlist->addColumn('sale_price',  'Price',       'left',    '40%');
            $orderlist->setHeight(250);
            $orderlist->makeScrollable();
            
            
            $input_search = new TEntry('search');
            $input_search->placeholder = _t('Search');
            $input_search->setSize('100%');
            $orderlist->enableSearch($input_search, 'id, description');
            
            $hbox = new THBox;
            $hbox->style = 'border-bottom: 1px solid gray;padding-bottom:10px';
            $hbox->add( new TLabel('Order list') );
            $hbox->add( $input_search )->style = 'float:right;width:30%;';
            
            // load order items
            $orderlist->addItems( Product::allInTransaction('samples') );
            $this->form->addContent( [$hbox] );
            $this->form->addFields( [$orderlist] );
            
            $this->form->addAction( 'Save', new TAction([$this, 'onSave']), 'fa:save green');
            
            // wrap the page content using vertical box
            $vbox = new TVBox;
            $vbox->style = 'width: 100%';
            $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $vbox->add($this->form);
            parent::add($vbox);
        }
        
        /**
         * Simulates an save button
         * Show the form content
         */
        public function onSave($param)
        {
            try
            {
                $data = $this->form->getData(); // optional parameter: active record class
                
                $this->form->validate();
                echo '<pre>';
                echo '$this->form->getData()';
                var_dump($data);
                echo '</pre>';
                echo 'onSave($param)';
                echo '<pre>';
                var_dump($param);
                echo '</pre>';
                
                // put the data back to the form
                $this->form->setData($data);
            }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage());
            }
        }
    }

