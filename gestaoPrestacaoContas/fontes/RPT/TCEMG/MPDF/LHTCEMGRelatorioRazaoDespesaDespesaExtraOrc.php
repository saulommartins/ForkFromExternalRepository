<div class='text_align_center font_weight_bold'>
    <h4>Razão da Despesa - <?= $stTipoRelatorio ?></h4>
</div>

<?php
    foreach($arDia as $stDia => $registro){
        echo "<table class='border'> ";
        echo "  <thead>              ";
        echo "      <tr>             ";
        echo "          <th class='text_align_center border font_size_9' width=\"6%\" >DATA PGTO     </th>";
        echo "          <th class='text_align_center border font_size_9' width=\"28%\">DESPESA       </th>";
        echo "          <th class='text_align_center border font_size_9' width=\"24%\">CREDOR        </th>";
        echo "          <th class='text_align_center border font_size_9' width=\"8%\" >VALOR PAGO    </th>";
        echo "          <th class='text_align_center border font_size_9' width=\"7%\" >DOCUMENTO     </th>";
        echo "          <th class='text_align_center border font_size_9' width=\"18%\">BCO PGTO      </th>";
        echo "          <th class='text_align_center border font_size_9' width=\"4%\" >FONTE         </th>";
        echo "          <th class='text_align_center border font_size_9' width=\"5%\" >ANULADO       </th>";
        echo "      </tr>            ";
        echo "  </thead>             ";
        echo "  <tbody>              ";

        foreach($registro as $transferencia){
            if($transferencia['bo_pagamento_estornado']=='t')
                $boAnul = 'Sim';
            else
                $boAnul = 'Não';

            $stCredor = "";
            if(!empty($transferencia['cgm_credor']))
                $stCredor = $transferencia['cgm_credor']." - ".$transferencia['credor'];

            echo "      <tr> ";
            echo "          <td class='text_align_center border font_size_9 tr_nivel_1' >".$transferencia['dt_pagamento']."                              </td>";
            echo "          <td class='text_align_left border font_size_9 tr_nivel_1'   >".$transferencia['nome_despesa']."                              </td>";
            echo "          <td class='text_align_left border font_size_9 tr_nivel_1'   >".$stCredor."                                                   </td>";
            echo "          <td class='text_align_right border font_size_9 tr_nivel_1'  >".number_format($transferencia['valor'], '2', ',', '.')."       </td>";
            echo "          <td class='text_align_center border font_size_9 tr_nivel_1' >".$transferencia['documento']."                                 </td>";
            echo "          <td class='text_align_left border font_size_9 tr_nivel_1'   >".$transferencia['banco']."                                     </td>";
            echo "          <td class='text_align_center border font_size_9 tr_nivel_1' >".$transferencia['cod_recurso_banco']."                         </td>";
            echo "          <td class='text_align_center border font_size_9 tr_nivel_1' >".$boAnul."                                                     </td>";
            echo "      </tr> ";
        }

        echo "  </tbody> ";
        echo "</table>   ";

        $totalDia = $arTotalDia[$stDia];
        echo "<table style=\"margin-top:5mm;margin-bottom:5mm;\">                    ";
        echo "      <tr>                                                             ";
        echo "          <td class='text_align_center' width=\"34%\" >           </td>";
        echo "          <td class='text_align_right border' width=\"24%\" >Sub-Total Dia                                        </td>";
        echo "          <td class='text_align_right border' width=\"8%\"  >".number_format($totalDia['valor'], '2', ',', '.')." </td>";
        echo "          <td class='text_align_center' width=\"34%\" >           </td>";
        echo "      </tr>                                                            ";
        echo "      <tr>                                                             ";
        echo "          <td class='text_align_center' >                         </td>";
        echo "          <td class='text_align_right border' >(-) Anulações Pgto                                             </td>";
        echo "          <td class='text_align_right border' >".number_format($totalDia['valor_estornado'], '2', ',', '.')." </td>";
        echo "          <td class='text_align_center' >                         </td>";
        echo "      </tr>                                                            ";
        echo "      <tr>                                                             ";
        echo "          <td class='text_align_center' >                         </td>";
        echo "          <td class='text_align_right border' >Total Dia                                                    </td>";
        echo "          <td class='text_align_right border' >".number_format($totalDia['valor_liquido'], '2', ',', '.')." </td>";
        echo "          <td class='text_align_center' >                         </td>";
        echo "      </tr>                                                            ";
        echo "</table>                                                               ";
    }

    echo "<table style=\"margin-top:5mm;\">                                      ";
    echo "      <tr>                                                             ";
    echo "          <td class='text_align_center' width=\"34%\" >           </td>";
    echo "          <td class='text_align_right border' width=\"24%\" >Sub-Total Período                                   </td>";
    echo "          <td class='text_align_right border' width=\"8%\"  >".number_format($arTotal['valor'], '2', ',', '.')." </td>";
    echo "          <td class='text_align_center' width=\"34%\" >           </td>";
    echo "      </tr>                                                            ";
    echo "      <tr>                                                             ";
    echo "          <td class='text_align_center' >                         </td>";
    echo "          <td class='text_align_right border' >(-) Anulações Pgto                                            </td>";
    echo "          <td class='text_align_right border' >".number_format($arTotal['valor_estornado'], '2', ',', '.')." </td>";
    echo "          <td class='text_align_center' >                         </td>";
    echo "      </tr>                                                            ";
    echo "      <tr>                                                             ";
    echo "          <td class='text_align_center' >                         </td>";
    echo "          <td class='text_align_right border' >Total Período                                               </td>";
    echo "          <td class='text_align_right border' >".number_format($arTotal['valor_liquido'], '2', ',', '.')." </td>";
    echo "          <td class='text_align_center' >                         </td>";
    echo "      </tr>                                                            ";
    echo "</table>                                                               ";
?>
