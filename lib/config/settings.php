<?php
return array(
    
    'country' => array(
        'value' => 'blr',
        'title' => 'Страна',
        'description' => 'Страна, в которую осуществляется доставка',
        'control_type' => waHtmlControl::SELECT . ' waShipping::settingCountrySelect'
    ),
    
    'handlingfee' => array(
        'value' => 150,
        'title' => 'Стоимость упаковки и отправки',
        'description' => 'Дополнительная плата за упаковку, отправку посылки',
        'control_type' => waHtmlControl::INPUT
    ),
    
    'roundfee' => array(
        'value' => 150,
        'title' => 'Округление стоимости',
        'description' => 'Округление стоимости до целых рублей',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
            0 => 'Нет',
            1 => 'Да',
        )
    ),
    
    'smallpacket' => array(
        'value' => 3,
        'title' => 'Мелкий пакет',
        'description' => 'Разрешить отправку в мелких пакетах. Максимум 2 кг, без оценки и страховки, как подарок или образцы.',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
            0 => 'Не отправлять',
            1 => 'Только наземный транспорт',
            2 => 'Только авиа',
            3 => 'Наземный и авиа'
        )
    ),
    
    'smallpacketcost' => array(
        'value' => array(
            'fixedfee' => ['gnd'=>50, 'avia'=>60],
            100 => ['gnd'=>11, 'avia'=>12],
            250 => ['gnd'=>21, 'avia'=>22],
            500 => ['gnd'=>31, 'avia'=>32],
            1000 => ['gnd'=>41, 'avia'=>42],
            2000 => ['gnd'=>51, 'avia'=>52]
        ),
        'title' => 'Цены на отправку мелкого пакета',
        'control_type' => 'SmallPacketPrices'
    ),
    
    'smallpacketweight' => array(
        'value' => 0.2,
        'title' => 'Вес упаковки мелкого пакета',
        'control_type' => waHtmlControl::INPUT
    ),
    
    'parcel' => array(
        'value' => 3,
        'title' => 'Посылка',
        'description' => 'Разрешить отправку ценных посылок. Максимум 20 кг., процент за страхование.',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
            0 => 'Не отправлять',
            1 => 'Только наземный транспорт',
            2 => 'Только авиа',
            3 => 'Наземный и авиа'
        )
    ),
    
    'parcelcost' => array(
        'value' => array(
            'fixedfee' => ['gnd' => 101, 'avia' => 102],
            'perkgfee' => ['gnd' => 103, 'avia' => 104]
        ),
        'title' => 'Цены на отправку посылки',
        'description' => 'Цена на посылку складывается из двух частей. Фиксированная плата плюс плата за каждый полный и неполный килограмм.',
        'control_type' => 'ParcelPrices'
    ),
    
    'parcelinsurancefee' => array(
        'value' => 4,
        'title' => 'Страховой сбор',
        'description' => 'Процент страхового сбора',
        'control_type' => waHtmlControl::INPUT
    ),
    
    'parcelweight' => array(
        'value' => 0.4,
        'title' => 'Вес упаковки посылки',
        'control_type' => waHtmlControl::INPUT
    ),
    
    'grounddeliverydate' => array(
        'value' => ['from' => 15, 'to' => 25],
        'title' => 'Срок наземной доставки',
        'description' => 'Примерное значение min и max дней',
        'control_type' =>  waHtmlControl::INTERVAL
    ), 
    
    'aviadeliverydate' => array(
        'value' => ['from' => 10, 'to' => 20],
        'title' => 'Срок авиа доставки',
        'description' => 'Примерное значение min и max дней',
        'control_type' =>  waHtmlControl::INTERVAL
    ), 
);
//EOF
