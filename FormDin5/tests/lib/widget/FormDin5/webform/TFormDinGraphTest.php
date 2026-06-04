<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * ----------------------------------------------------------------------------
 */

use PHPUnit\Framework\TestCase;

class TFormDinGraphTest extends TestCase
{
    public function test_gerarBarChart()
    {
        $data = [
            ['Mês', 'Vendas'],
            ['Janeiro', 100],
            ['Fevereiro', 150]
        ];

        $chart = TFormDinGraph::gerarBarChart($data, '100%', 300, 'Título Gráfico', 'Vendas (R$)', 'Meses');
        $this->assertInstanceOf(THtmlRenderer::class, $chart);
    }

    public function test_gerarPieChart()
    {
        $data = [
            ['Categoria', 'Valor'],
            ['A', 30],
            ['B', 70]
        ];

        $chart = TFormDinGraph::gerarPieChart($data, '500px', '400px', 'Pizza', 'Y', 'X');
        $this->assertInstanceOf(THtmlRenderer::class, $chart);
    }

    public function test_showInfoBox()
    {
        $info = TFormDinGraph::showInfoBox('Pedidos', 'fa-shopping-cart', 'bg-blue', '45');
        $this->assertInstanceOf(THtmlRenderer::class, $info);
    }
}
