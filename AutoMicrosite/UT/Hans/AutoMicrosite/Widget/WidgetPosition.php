<?php
namespace UT\Hans\AutoMicrosite\Widget;

/**
 * Description of WidgetPosition
 *
 * @author Hans
 */
class WidgetPosition {
    
    const LEFT = 'left';
    const RIGHT = 'right';
    const CENTER = 'center';
    const TOP = 'top';
    const BOTTOM = 'bottom';
    
    private $horizontal;
    
    private $vertical;
    
    public function __construct($horizontal, $vertical) {
        $this->horizontal = $horizontal;
        $this->vertical = $vertical;
    }
    
    public function __toString() {
        return $this->horizontal .'-'. $this->vertical;
    }

}

?>