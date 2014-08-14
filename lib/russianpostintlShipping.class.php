<?php

/**
 * Плагин расчета доставки Почтой России.
 * 
 */
class russianpostintlShipping extends waShipping
{
    
    public function allowedCurrency()
    {
        return 'RUB';
    }
    
    public function allowedWeightUnit()
    {
        return 'kg';
    }
    
    public function allowedAddress()
    {
        return array(
            array(
            'country' => $this->country
            )
        );
    }
    
    protected function calculate()
    {
        $services = array();
        
        $weight = $this->getTotalWeight();
        
        if($weight + $this->parcelweight > 20) {
            return sprintf("Вес отправления (%0.2f) превышает максимально допустимый (%0.2f)", $weight+$this->parcelweight, 20);
        }
        
        if(($weight+$this->smallpacketweight < 2) && $this->smallpacket) {
            
            $idx=2000;
            foreach([0.1, 0.25, 0.5, 1, 2] as $weightlim) {
                if($weight+$this->smallpacketweight <= $weightlim) {
                    $idx = $weightlim*1000;
                    break;
                }
            }
            
            if((($this->smallpacket == 1) || ($this->smallpacket == 3)) && isset($this->smallpacketcost[$idx])) {
                $rate = $this->smallpacketcost['fixedfee']['gnd'] + $this->smallpacketcost[$idx]['gnd'] + $this->handlingfee;
                if($this->roundfee) {
                    $rate = ceil($rate);
                }
                $services['SMALL_PACKET_GROUND'] = array(
                    'id' => 'SMALL_PACKET_GROUND',
                    'name' => 'Мелкий пакет (наземная доставка)',
                    'comment' => 'Отправление весом до 2 кг., без объявленной стоимости, как подарок или образцы',
                    'currency' => $this->currency,
                    'est_delivery' => $delivery_date = waDateTime::format('humandate', strtotime("+{$this->grounddeliverydate['from']} days")).' — '.waDateTime::format('humandate', strtotime("+{$this->grounddeliverydate['to']} days")),
                    'rate' => $rate
                );
            }
            
            if((($this->smallpacket == 2) || ($this->smallpacket == 3)) && isset($this->smallpacketcost[$idx])) {
                $rate = $this->smallpacketcost['fixedfee']['avia'] + $this->smallpacketcost[$idx]['avia'] + $this->handlingfee;
                if($this->roundfee) {
                    $rate = ceil($rate);
                }
                $services['SMALL_PACKET_AVIA'] = array(
                    'id' => 'SMALL_PACKET_AVIA',
                    'name' => 'Мелкий пакет (авиа доставка)',
                    'comment' => 'Отправление весом до 2 кг., без объявленной стоимости, как подарок или образцы. На самолетике.',
                    'currency' => $this->currency,
                    'est_delivery' => $delivery_date = waDateTime::format('humandate', strtotime("+{$this->aviadeliverydate['from']} days")).' — '.waDateTime::format('humandate', strtotime("+{$this->aviadeliverydate['to']} days")),
                    'rate' => $rate
                );
            }
        }
        
        if(($weight+$this->parcelweight < 20) && $this->parcel) {
            
            if(($this->parcel == 1) || ($this->parcel == 3)) {
                
                $rate = $this->parcelcost['fixedfee']['gnd'] + ceil($weight+$this->parcelweight)*$this->parcelcost['perkgfee']['gnd'] + $this->getTotalPrice() * $this->parcelinsurancefee / 100 + $this->handlingfee;
                if($this->roundfee) {
                    $rate = ceil($rate);
                }
                $services['PARCEL_GROUND'] = array(
                    'id' => 'PARCEL_GROUND',
                    'name' => 'Посылка (наземный транспорт)',
                    'comment' => 'Ценная посылка с объявленной стоимостью.',
                    'currency' => $this->currency,
                    'est_delivery' => $delivery_date = waDateTime::format('humandate', strtotime("+{$this->grounddeliverydate['from']} days")).' — '.waDateTime::format('humandate', strtotime("+{$this->grounddeliverydate['to']} days")),
                    'rate' => $rate
                );
            }
            
            if(($this->parcel == 2) || ($this->parcel == 3)) {
                $rate = $this->parcelcost['fixedfee']['avia'] + ceil($weight+$this->parcelweight)*$this->parcelcost['perkgfee']['avia'] + $this->getTotalPrice() * $this->parcelinsurancefee /100 + $this->handlingfee;
                if($this->roundfee) {
                    $rate = ceil($rate);
                }
                $services['PARCEL_AVIA'] = array(
                    'id' => 'PARCEL_AVIA',
                    'name' => 'Посылка (авиа доставка)',
                    'comment' => 'Ценная посылка с объявленной стоимостью.',
                    'currency' => $this->currency,
                    'est_delivery' => $delivery_date = waDateTime::format('humandate', strtotime("+{$this->aviadeliverydate['from']} days")).' — '.waDateTime::format('humandate', strtotime("+{$this->aviadeliverydate['to']} days")),
                    'rate' => $rate
                );
            }
            
        }
        
        if(empty($services)) {
            return 'Этот вид доставки недоступен';
        }
        
        return $services;
    }
    
    public function saveSettings($settings = array())
    {
        $fields = ['handlingfee', 'smallpacketweight', 'parcelinsurancefee', 'parcelweight'];
        foreach ($fields as $field) {
            if (isset($settings[$field])) {
                $settings[$field] = str_replace(',', '.', $settings[$field]);
            }
        }
        
        if(isset($settings['smallpacketcost'])) {
            foreach($settings['smallpacketcost'] as $key => $value) {
                $settings['smallpacketcost'][$key]['gnd'] = str_replace(',', '.', $settings['smallpacketcost'][$key]['gnd']);
                $settings['smallpacketcost'][$key]['avia'] = str_replace(',', '.', $settings['smallpacketcost'][$key]['avia']);
            }
        }
        
        if(isset($settings['parcelcost'])) {
            foreach($settings['parcelcost'] as $key => $value) {
                $settings['parcelcost'][$key]['gnd'] = str_replace(',', '.', $settings['parcelcost'][$key]['gnd']);
                $settings['parcelcost'][$key]['avia'] = str_replace(',', '.', $settings['parcelcost'][$key]['avia']);
            }
        }
        
        return parent::saveSettings($settings);
    }
    
    /**
     * Возвращает информацию о статусе отправления (HTML).
     *
     * @see waShipping::tracking()
     * @example return _wp('Online shipment tracking: <a href="link">link</a>.');
     * @param string $tracking_id Необязательный идентификатор отправления, указанный пользователем
     * @return string
     */
    public function tracking($tracking_id = null)
    {
        return 'Отслеживание отправления: <a href="http://www.russianpost.ru/Tracking20/" target="_blank">http://www.russianpost.ru/Tracking20/</a>';
    }

    protected function initControls()
    {
        $this->registerControl('SmallPacketPrices')->registerControl('ParcelPrices');
        parent::initControls();
    }
    
    public static function settingSmallPacketPrices($name, $params=array())
    {
        foreach ($params as $field => $param) {
            if (strpos($field, 'wrapper')) {
                unset($params[$field]);
            }
        }
        $control = '<table class="zebra">';
        $control .= '<thead><tr class="gridsheader"><th colspan="2">Вес</th><th>Наземная доставка</th><th>Авиа доставка</th></tr></thead><tbody>';
        
        if (!isset($params['value']) || !is_array($params['value'])) {
            $params['value'] = array();
        }

        waHtmlControl::addNamespace($params, $name);
        
        $weight_params = array_merge($params, ['title_wrapper'=>FALSE]);
        waHtmlControl::addNamespace($weight_params, 'fixedfee');
        $gnd_control = waHtmlControl::getControl(waHtmlControl::INPUT, 'gnd', array_merge($weight_params, ['value'=>$params['value']['fixedfee']['gnd']]));
        $avia_control = waHtmlControl::getControl(waHtmlControl::INPUT, 'avia', array_merge($weight_params, ['value'=>$params['value']['fixedfee']['avia']]));
        $control.= "<tr><td>Фиксированная плата</td><td>&rarr;</td><td>$gnd_control</td><td>$avia_control</td></tr>";
        
        $weight_titles = ['до 0.1 кг.', '0.101 &ndash; 0.25 кг.', '0.251 &ndash; 0.5 кг.', '0.501 &ndash; 1 кг.', '1.001 &ndash; 2 кг.'];
        
        foreach([100, 250, 500, 1000, 2000] as $idx=>$weightlim) {
            $weight_params = array_merge($params, ['title_wrapper'=>FALSE]);
            
            waHtmlControl::addNamespace($weight_params, $weightlim);
            
            $gnd_control = waHtmlControl::getControl(waHtmlControl::INPUT, 'gnd', array_merge($weight_params, ['value'=>$params['value'][$weightlim]['gnd']]));
            $avia_control = waHtmlControl::getControl(waHtmlControl::INPUT, 'avia', array_merge($weight_params, ['value'=>$params['value'][$weightlim]['avia']]));
            
            $control.= "<tr><td>{$weight_titles[$idx]}</td><td>&rarr;</td><td>$gnd_control</td><td>$avia_control</td></tr>";
        }

        return $control . '</tbody></table>';
    }
    
    public static function settingParcelPrices($name, $params=array()) {
        foreach ($params as $field => $param) {
            if (strpos($field, 'wrapper')) {
                unset($params[$field]);
            }
        }
        $control = '<table class="zebra">';
        $control .= '<thead><tr class="gridsheader"><th>&nbsp;</th><th>Наземная доставка</th><th>Авиа доставка</th></tr></thead><tbody>';
        
        waHtmlControl::addNamespace($params, $name);
        
        $fixed_fee_params = array_merge($params, ['title_wrapper'=>FALSE, 'description_wrapper'=>FALSE]);
        $perkg_fee_params = array_merge($params, ['title_wrapper'=>FALSE, 'description_wrapper'=>FALSE]);
        
        waHtmlControl::addNamespace($fixed_fee_params, 'fixedfee');
        waHtmlControl::addNamespace($perkg_fee_params, 'perkgfee');
        
        $control .= '<tr><td>Сбор за посылку</td><td>' .
                waHtmlControl::getControl(waHtmlControl::INPUT, 'gnd', array_merge($fixed_fee_params, ['value'=>$params['value']['fixedfee']['gnd']])) .
                '</td><td>' .
                waHtmlControl::getControl(waHtmlControl::INPUT, 'avia', array_merge($fixed_fee_params, ['value'=>$params['value']['fixedfee']['avia']])) .
                '</td></tr>';
        
        $control .= '<tr><td>Стоимость за каждый полный и неполный килограмм</td><td>' .
                waHtmlControl::getControl(waHtmlControl::INPUT, 'gnd', array_merge($perkg_fee_params, ['value'=>$params['value']['perkgfee']['gnd']])) .
                '</td><td>' .
                waHtmlControl::getControl(waHtmlControl::INPUT, 'avia', array_merge($perkg_fee_params, ['value'=>$params['value']['perkgfee']['avia']])) .
                '</td></tr>';
        
        return $control . '</table>';
    }
}
