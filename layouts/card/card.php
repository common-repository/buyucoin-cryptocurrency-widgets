<?php
// bucw_currency_symbol($fiat_currency).bucw_format_number($coin_price);
$vol=($mb_format=="on")?bucw_mb_format($coin_price*$v24):bucw_format_number($coin_price*$v24);
$sign=($change_class=="up")?" +":"";
 $coin_html .= '<div class="bucw row1">
 <div class="bucw-coin-name">
     <span class="coins-icon">'.$coin_logo_html.'</span>
     <div class="bucw-coinname-wrapper">
         <div class="bucw-coinsname">'.esc_html($coin_name).'</div><div class="bucw-coins-changes"> <span>('.esc_html($market_name).') </span>
         <span class="bucw-coins-changes '.esc_attr($change_class).'">'.esc_html(bucw_currency_symbol($fiat_currency).bucw_format_number($c24)).'</span> 
        </div></div>
         
 </div>
 <div class="bucw-coin-price">
   <div class="price-wrapper">
       <div class="price"> '.esc_html($coin_price_html).'</div>
       <div class="bucw-coins-changes"><span class="coin-changes ' . esc_attr($change_class) . '">'. esc_html($percent_change_24h).'%
      </span>(24H)</div>
   </div>
</div>
</div>
<div class="bucw-row2">
<div class="coins-value">
<div class="cvlu">'.__("High 24H","bucw").'</div>
<span class="value-changes h24-high">' .esc_html(bucw_currency_symbol($fiat_currency).$h24) . '</span>
</div>
<div class="coins-value">
<div class="cvlu">'.__("Low 24H","bucw").'</div>
<span class="value-changes h24-low">'.esc_html(bucw_currency_symbol($fiat_currency).$l24 ). '</span>
</div>
<div class="coins-value">
<div class="cvlu">'.__("Volume 24H","bucw").'</div>
<span class="value-changes">'.esc_html(bucw_currency_symbol($fiat_currency).$vol).'</span>
</div>
</div>';