<?php
//namespace app\lib\widget\FormDin5\dashboard;

use Adianti\Widget\Chart\TNumericIndicator;

class TFormDinNumericIndicator extends TNumericIndicator
{
    private $fontColor = '#000000';
    private $cardColor = '#ffffff';
    
    // Propriedades re-declaradas porque são private no TNumericIndicator
    protected $icon;
    protected $iconColor = '#ffffff'; // Cor padrão do ícone
    protected $value;
    protected $color;
    
    // Novos comportamentos encapsulados
    /**
     * Define a cor da fonte (texto) do indicador.
     * @param string $color Cor no formato hexadecimal, rgb, etc. Ex: '#000000'
     */
    public function setFontColor($color)
    {
        $this->fontColor = $color;
    }
    
    /**
     * Define a cor de fundo do cartão principal.
     * @param string $color Cor no formato hexadecimal, rgb, etc. Ex: '#ffffff'
     */
    public function setCardColor($color)
    {
        $this->cardColor = $color;
    }
    
    /**
     * Define a classe do ícone a ser exibido.
     * ATENÇÃO: É necessário informar o nome completo da classe da biblioteca de ícones.
     * Exemplo: 'fa fa-car' ou 'fas fa-car fa-fw' (e não apenas 'car').
     * @param string $icon Classes CSS do ícone
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * Define a cor do ícone.
     * @param string $color Cor no formato hexadecimal, rgb, etc. Ex: '#ff0000'
     */
    public function setIconColor($color)
    {
        $this->iconColor = $color;
    }

    /**
     * Define o valor numérico (ou texto) a ser exibido.
     * @param mixed $value Valor a ser exibido
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Define a cor de fundo primária do container do ícone.
     * @param string $color Cor de fundo (classe ou hexadecimal)
     */
    public function setColor($color)
    {
        $this->color = $color;
    }
    
    /**
     * Retorna a cor atual da fonte.
     * @return string
     */
    public function getFontColor()
    {
        return $this->fontColor;
    }
    
    /**
     * Retorna a cor atual do cartão.
     * @return string
     */
    public function getCardColor()
    {
        return $this->cardColor;
    }
    
    /**
     * Retorna as classes css do ícone.
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Retorna a cor configurada para o ícone.
     * @return string
     */
    public function getIconColor()
    {
        return $this->iconColor;
    }
    
    /**
     * Retorna o valor que está sendo exibido.
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Retorna a cor de fundo primária do container do ícone.
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Renderiza o componente carregando o template HTML do FormDin5 e 
     * processando as variáveis encapsuladas no componente.
     */
    public function show()
    {
        // Se usar TChartBase, você tem acesso à formatação de números nativa do Adianti
        $value = (float) $this->getValue();
        if (isset($this->numericMask)) {
            $value = number_format($value, $this->numericMask[0], $this->numericMask[1], $this->numericMask[2]);
        }
        
        if (!empty($this->numberPrefix)) {
            $value = $this->numberPrefix . ' ' . $value;
        }
        
        // Renderiza usando o SEU template v2
        $infoBox = new THtmlRenderer('app/lib/widget/FormDin5/resources/info-box-v2.html');
        $infoBox->enableSection('main', [
            'title'      => $this->getTitle(), //Herdado do TChartBase
            'icon'       => $this->getIcon(),
            'iconColor'  => $this->getIconColor(),
            'background' => $this->getColor(),
            'value'      => $value,
            'fontColor'  => $this->getFontColor(),
            'cardColor'  => $this->getCardColor()
        ]);
        
        parent::add($infoBox);
        //Chamando show do parente do parente
        \Adianti\Widget\Base\TElement::show();
    }
}