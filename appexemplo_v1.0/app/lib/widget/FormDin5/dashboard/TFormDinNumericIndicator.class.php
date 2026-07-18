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
    public function setFontColor($color)
    {
        $this->fontColor = $color;
    }
    
    public function setCardColor($color)
    {
        $this->cardColor = $color;
    }
    
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function setIconColor($color)
    {
        $this->iconColor = $color;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }
    
    public function getFontColor()
    {
        return $this->fontColor;
    }
    
    public function getCardColor()
    {
        return $this->cardColor;
    }
    
    public function getIcon()
    {
        return $this->icon;
    }

    public function getIconColor()
    {
        return $this->iconColor;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function getColor()
    {
        return $this->color;
    }

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