<div class='text_align_center font_weight_bold'>
    <h4>Razão da Despesa - <?= $stTipoRelatorio ?></h4>
</div>

<?php
    foreach($arNomOrgaoUnidade as $inOrgao => $unidade){
        foreach($unidade as $inUnidade => $orgaoUnidade){
            echo "<h5>";
            echo "  Orgão : ".$inOrgao.' - '.$orgaoUnidade['nom_orgao']." <br />";
            echo "  Unidade : ".$inUnidade.' - '.$orgaoUnidade['nom_unidade']." <br />";
            echo "</h5> ";

            $arDotacao = $arOrgaoUnidade[$inOrgao][$inUnidade];

            foreach($arDotacao as $stDotacao => $registro){
                echo "<h5>                   ";
                echo "  Dotação : ".$stDotacao;
                echo "</h5>                  ";

                echo "<table class='border'> ";
                echo "  <thead>              ";
                echo "      <tr>             ";
                echo "          <th class='text_align_center border font_size_9' width=\"6%\" >DATA PGTO     </th>";
                echo "          <th class='text_align_center border font_size_9' width=\"7%\" >EMPENHO       </th>";
                echo "          <th class='text_align_center border font_size_9' width=\"6%\" >NOTA          </th>";
                echo "          <th class='text_align_center border font_size_9' width=\"6%\" >OP            </th>";
                echo "          <th class='text_align_center border font_size_9' width=\"17%\">CREDOR        </th>";
                echo "          <th class='text_align_center border font_size_9' width=\"7%\" >VALOR BRUTO   </th>";
                echo "          <th class='text_align_center border font_size_9' width=\"6%\" >RETENÇÃO      </th>";
                echo "          <th class='text_align_center border font_size_9' width=\"7%\" >VALOR LÍQUIDO </th>";
                echo "          <th class='text_align_center border font_size_9' width=\"8%\" >DOCUMENTO     </th>";
                echo "          <th class='text_align_center border font_size_9' width=\"21%\">BCO PGTO      </th>";
                echo "          <th class='text_align_center border font_size_9' width=\"4%\" >FONTE         </th>";
                echo "          <th class='text_align_center border font_size_9' width=\"5%\" >ANULADO       </th>";
                echo "      </tr>            ";
                echo "  </thead>             ";
                echo "  <tbody>              ";

                foreach($registro as $pagamento){
                    if($pagamento['bo_pagamento_estornado']=='t')
                        $boAnul = 'Sim';
                    else
                        $boAnul = 'Não';

                    echo "      <tr> ";
                    echo "          <td class='text_align_center border font_size_9 tr_nivel_1' >".$pagamento['dt_pagamento']."                                              </td>";
                    echo "          <td class='text_align_center border font_size_9 tr_nivel_1' >".$pagamento['cod_empenho']."/".$pagamento['exercicio_empenho']."           </td>";
                    echo "          <td class='text_align_center border font_size_9 tr_nivel_1' >".$pagamento['cod_nota']."/".$pagamento['exercicio_nota']."                 </td>";
                    echo "          <td class='text_align_center border font_size_9 tr_nivel_1' >".$pagamento['cod_ordem']."/".$pagamento['exercicio_ordem']."               </td>";
                    echo "          <td class='text_align_left border font_size_9 tr_nivel_1'   >".$pagamento['numcgm']." - ".$pagamento['credor']."                         </td>";
                    echo "          <td class='text_align_right border font_size_9 tr_nivel_1'  >".number_format($pagamento['vl_pago'], '2', ',', '.')."                     </td>";
                    echo "          <td class='text_align_right border font_size_9 tr_nivel_1'  >".number_format($pagamento['vl_retencao'], '2', ',', '.')."                 </td>";
                    echo "          <td class='text_align_right border font_size_9 tr_nivel_1'  >".number_format($pagamento['vl_liquido'], '2', ',', '.')."                  </td>";
                    echo "          <td class='text_align_center border font_size_9 tr_nivel_1' >".$pagamento['documento']." - ".$pagamento['tipo_documento']."              </td>";
                    echo "          <td class='text_align_left border font_size_9 tr_nivel_1'   >".$pagamento['conta_banco']." - ".$pagamento['nom_conta_plano_pagamento']." </td>";
                    echo "          <td class='text_align_center border font_size_9 tr_nivel_1' >".$pagamento['cod_recurso_pgto']."                                          </td>";
                    echo "          <td class='text_align_center border font_size_9 tr_nivel_1' >".$boAnul."                                                                 </td>";
                    echo "      </tr> ";
                }

                echo "  </tbody> ";
                echo "</table>   ";

                $totalDotacao = $arTotalDotacao[$inOrgao][$inUnidade][$stDotacao];
                echo "<table style=\"margin-top:5mm;\">                                      ";
                echo "      <tr>                                                             ";
                echo "          <td class='text_align_center' width=\"25%\" >           </td>";
                echo "          <td class='text_align_right border' width=\"17%\" >Sub-Total Dotação                                               </td>";
                echo "          <td class='text_align_right border' width=\"7%\"  >".number_format($totalDotacao['vl_pago'], '2', ',', '.')."      </td>";
                echo "          <td class='text_align_right border' width=\"6%\"  >".number_format($totalDotacao['vl_retencao'], '2', ',', '.')."  </td>";
                echo "          <td class='text_align_right border' width=\"7%\"  >".number_format($totalDotacao['vl_liquido'], '2', ',', '.')."   </td>";
                echo "          <td class='text_align_center' width=\"38%\" >           </td>";
                echo "      </tr>                                                            ";
                echo "      <tr>                                                             ";
                echo "          <td class='text_align_center' >                         </td>";
                echo "          <td class='text_align_right border' >(-) Anulações Pgto                                              </td>";
                echo "          <td class='text_align_right border' >".number_format($totalDotacao['vl_anulado_p'], '2', ',', '.')." </td>";
                echo "          <td class='text_align_right border' >".number_format($totalDotacao['vl_anulado_r'], '2', ',', '.')." </td>";
                echo "          <td class='text_align_right border' >".number_format($totalDotacao['vl_anulado_l'], '2', ',', '.')." </td>";
                echo "          <td class='text_align_center' >                         </td>";
                echo "      </tr>                                                            ";
                echo "      <tr>                                                             ";
                echo "          <td class='text_align_center' >                         </td>";
                echo "          <td class='text_align_right border' >Total Dotação                                                 </td>";
                echo "          <td class='text_align_right border' >".number_format($totalDotacao['vl_total_p'], '2', ',', '.')." </td>";
                echo "          <td class='text_align_right border' >".number_format($totalDotacao['vl_total_r'], '2', ',', '.')." </td>";
                echo "          <td class='text_align_right border' >".number_format($totalDotacao['vl_total_l'], '2', ',', '.')." </td>";
                echo "          <td class='text_align_center' >                         </td>";
                echo "      </tr>                                                            ";
                echo "</table>                                                               ";
            }

            $totalUnidade = $arTotalOrgaoUnidade[$inOrgao][$inUnidade];
            echo "<table style=\"margin-top:5mm;\">                                      ";
            echo "      <tr>                                                             ";
            echo "          <td class='text_align_center' width=\"25%\" >           </td>";
            echo "          <td class='text_align_right border' width=\"17%\" >Sub-Total Unidade                                               </td>";
            echo "          <td class='text_align_right border' width=\"7%\"  >".number_format($totalUnidade['vl_pago'], '2', ',', '.')."      </td>";
            echo "          <td class='text_align_right border' width=\"6%\"  >".number_format($totalUnidade['vl_retencao'], '2', ',', '.')."  </td>";
            echo "          <td class='text_align_right border' width=\"7%\"  >".number_format($totalUnidade['vl_liquido'], '2', ',', '.')."   </td>";
            echo "          <td class='text_align_center' width=\"38%\" >           </td>";
            echo "      </tr>                                                            ";
            echo "      <tr>                                                             ";
            echo "          <td class='text_align_center' >                         </td>";
            echo "          <td class='text_align_right border' >(-) Anulações Pgto                                              </td>";
            echo "          <td class='text_align_right border' >".number_format($totalUnidade['vl_anulado_p'], '2', ',', '.')." </td>";
            echo "          <td class='text_align_right border' >".number_format($totalUnidade['vl_anulado_r'], '2', ',', '.')." </td>";
            echo "          <td class='text_align_right border' >".number_format($totalUnidade['vl_anulado_l'], '2', ',', '.')." </td>";
            echo "          <td class='text_align_center' >                         </td>";
            echo "      </tr>                                                            ";
            echo "      <tr>                                                             ";
            echo "          <td class='text_align_center' >                         </td>";
            echo "          <td class='text_align_right border' >Total Unidade                                                 </td>";
            echo "          <td class='text_align_right border' >".number_format($totalUnidade['vl_total_p'], '2', ',', '.')." </td>";
            echo "          <td class='text_align_right border' >".number_format($totalUnidade['vl_total_r'], '2', ',', '.')." </td>";
            echo "          <td class='text_align_right border' >".number_format($totalUnidade['vl_total_l'], '2', ',', '.')." </td>";
            echo "          <td class='text_align_center' >                         </td>";
            echo "      </tr>                                                            ";
            echo "</table>                                                               ";
        }

        $totalOrgao = $arTotalOrgao[$inOrgao];
        echo "<table style=\"margin-top:5mm;\">                                      ";
        echo "      <tr>                                                             ";
        echo "          <td class='text_align_center' width=\"25%\" >           </td>";
        echo "          <td class='text_align_right border' width=\"17%\" >Sub-Total Orgão                                               </td>";
        echo "          <td class='text_align_right border' width=\"7%\"  >".number_format($totalOrgao['vl_pago'], '2', ',', '.')."      </td>";
        echo "          <td class='text_align_right border' width=\"6%\"  >".number_format($totalOrgao['vl_retencao'], '2', ',', '.')."  </td>";
        echo "          <td class='text_align_right border' width=\"7%\"  >".number_format($totalOrgao['vl_liquido'], '2', ',', '.')."   </td>";
        echo "          <td class='text_align_center' width=\"38%\" >           </td>";
        echo "      </tr>                                                            ";
        echo "      <tr>                                                             ";
        echo "          <td class='text_align_center' >                         </td>";
        echo "          <td class='text_align_right border' >(-) Anulações Pgto                                            </td>";
        echo "          <td class='text_align_right border' >".number_format($totalOrgao['vl_anulado_p'], '2', ',', '.')." </td>";
        echo "          <td class='text_align_right border' >".number_format($totalOrgao['vl_anulado_r'], '2', ',', '.')." </td>";
        echo "          <td class='text_align_right border' >".number_format($totalOrgao['vl_anulado_l'], '2', ',', '.')." </td>";
        echo "          <td class='text_align_center' >                         </td>";
        echo "      </tr>                                                            ";
        echo "      <tr>                                                             ";
        echo "          <td class='text_align_center' >                         </td>";
        echo "          <td class='text_align_right border' >Total Orgão                                                 </td>";
        echo "          <td class='text_align_right border' >".number_format($totalOrgao['vl_total_p'], '2', ',', '.')." </td>";
        echo "          <td class='text_align_right border' >".number_format($totalOrgao['vl_total_r'], '2', ',', '.')." </td>";
        echo "          <td class='text_align_right border' >".number_format($totalOrgao['vl_total_l'], '2', ',', '.')." </td>";
        echo "          <td class='text_align_center' >                         </td>";
        echo "      </tr>                                                            ";
        echo "</table>                                                               ";
    }
?>
