<?php
namespace app\lib\widget\FormDin5\dash;

use Adianti\Widget\Chart\TNumericIndicator;

class TFormDinNumericIndicator extends TNumericIndicator
{
    private $fontColor = '#000000';
    private $cardColor = '#ffffff';
    
    // Novos comportamentos encapsulados
    public function setFontColor($color)
    {
        $this->fontColor = $color;
    }
    
    public function setCardColor($color)
    {
        $this->cardColor = $color;
    }
    
    public function show()
    {
        // Se usar TChartBase, você tem acesso à formatação de números nativa do Adianti
        $value = (float) $this->value;
        if (isset($this->numericMask)) {
            $value = number_format($value, $this->numericMask[0], $this->numericMask[1], $this->numericMask[2]);
        }
        
        if (!empty($this->numberPrefix)) {
            $value = $this->numberPrefix . ' ' . $value;
        }
        
        // Renderiza usando o SEU template v2
        $infoBox = new THtmlRenderer('app/lib/widget/FormDin5/resources/info-box-v2.html');
        $infoBox->enableSection('main', [
            'title'      => $this->title, //Herdado do TNumericIndicator
            'icon'       => $this->icon,  //Herdado do TNumericIndicator
            'background' => $this->color, //Herdado do TNumericIndicator
            'value'      => $value,       //Herdado do TNumericIndicator
            'fontColor'  => $this->fontColor,
            'cardColor'  => $this->cardColor
        ]);
        
        parent::add($infoBox);
        parent::show();
    }
}