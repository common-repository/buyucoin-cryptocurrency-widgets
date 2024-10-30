<?php
   $coin_html .= '<div class="bucw-coin-container">';
                
   $coin_html .= '<div class="bucw-left">' .  $coin_logo_html . '</div>';
   $coin_html .= '<div class="bucw-right">';
   $coin_html .= '<div class="bucw-coin-name-data"><span class="bucw-coin-name">' . esc_html($coin_name ). '  </span><span class="bucw_quatcls">('.esc_html($market_name).')</span> ';
   
   $coin_html .= '</div><div class="bucw-coin-price-data"><span class="bucw-coin-price">' . esc_html($coin_price_html) . '</span>'; 
   // if ($display_changes) {
  $coin_html .= '<span class="bucw-coin-changes ' . esc_attr($change_class) . '">';
  $coin_html .= $change_sign . $percent_change_24h.'%';
  $coin_html .= '</span>';
// }
$coin_html .= '</div></div></div>';
// if($ticker_style=='style-1'){
   $coin_html .= '</li>';