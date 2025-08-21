<?php

return [
    // خرائط مزودي الشحن إلى قالب رابط التتبع
    // استبدال {number} تلقائياً برقم التتبع
    'carriers' => [
        'ups'    => 'https://www.ups.com/track?tracknum={number}',
        'fedex'  => 'https://www.fedex.com/fedextrack/?tracknumbers={number}',
        'dhl'    => 'https://www.dhl.com/global-en/home/tracking/tracking-express.html?tracking-id={number}',
        'aramex' => 'https://www.aramex.com/track/results?ShipmentNumber={number}',
        'usps'   => 'https://tools.usps.com/go/TrackConfirmAction?tLabels={number}',
    ],

    // رابط احتياطي إذا لم يُعرف الـ carrier
    'fallback' => 'https://track.aftership.com/{number}',
];
