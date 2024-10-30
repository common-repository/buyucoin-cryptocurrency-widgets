<?php
/*
  		List widget HTML 
    */
    $vol=($mb_format=="on")?bucw_mb_format($coin_price*$v24):bucw_format_number($coin_price*$v24);
    $coin_html .= '<tr id="' . esc_attr($coin_id) . '">';
    $coin_html .= '<td>'; 

    $coin_html .= '<div class="bucw_coin_info">
                <span class="bucw_coin_logo">'.$coin_logo_html.'<span class="bucw-mkt-name">('.esc_html($market_name).')</span></span>
  				<span class="name">'.esc_html($coin_name).' </span>';  				
  				$coin_html .= '</div></td><td class="price"><div class="price-value">' . esc_html($coin_price_html) . '</div>
               
  				';
    
    $coin_html .='</td>';
    $coin_html .= '<td><span class="changes ' . $change_class . '">';
    $coin_html .= $change_sign . $percent_change_24h.'%';
    $coin_html .= '</span></td>';
    $coin_html .='<td class="high"><div class="high">'  .esc_html(bucw_currency_symbol($fiat_currency).$h24 ). '</div>
  				';
    
    $coin_html .='</td>';
    $coin_html .='<td class="low"><div class="high">'  .esc_html(bucw_currency_symbol($fiat_currency).$l24) . '</div>
    ';

$coin_html .='</td>';
$coin_html .='<td class="volume"><div class="high">'  .esc_html(bucw_currency_symbol($fiat_currency).$vol). '</div>
';

$coin_html .='</td>';    
    $coin_html .= '</tr>';