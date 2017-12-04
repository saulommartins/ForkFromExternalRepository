<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/**
    * HTML do relatório Restos a Pagar por Credor
    * Data de Criação: 10/02/2016

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    $Id: LHRPCredor.php 64417 2016-02-18 18:03:51Z michel $
*/
?>
<div id="restos_pagar">
    <?php
        $inCountRestos = count($restos_pagar);
        $inCountRestosLinha = 1;
        foreach ($restos_pagar as $stNomCredor => $credor) {
            echo "<table class='border'>";
                echo "<thead>";
                    echo "<tr>";
                        echo "<td class=\"border text_align_left\" width=\"7%\" > CREDOR           </td>";
                        echo "<td class=\"border text_align_left\" colspan=11   > ".$stNomCredor." </td>";
                    echo "</tr>";

                    echo "<tr>";
                        echo "<td class=\"border text_align_left\"              > CNPJ/CPF                       </td>";
                        echo "<td class=\"border text_align_left\" colspan=2    > ".$credor['cpf_cnpj']."        </td>";
                        echo "<td class=\"border text_align_center\" colspan=3  > SALDO ATÉ EXERCÍCIO ANTERIOR   </td>";
                        echo "<td class=\"border text_align_center\" colspan=3  > MOVIMENTAÇÕES EXERCÍCIO ATUAL  </td>";
                        echo "<td class=\"border text_align_center\" colspan=3  > SALDO ATUAL                    </td>";
                    echo "</tr>";

                    echo "<tr>";
                        echo "<td class=\"border text_align_center\"               > EMPENHO           </td>";
                        echo "<td class=\"border text_align_center\" width=\"7%\"  > DT.EMISSÃO        </td>";
                        echo "<td class=\"border text_align_center\" width=\"7%\"  > VENCIMENTO        </td>";
                        echo "<td class=\"border text_align_center\" width=\"10%\" > EMPENHADO         </td>";
                        echo "<td class=\"border text_align_center\" width=\"9%\"  > A LIQUIDAR        </td>";
                        echo "<td class=\"border text_align_center\" width=\"10%\" > A PAGAR LIQUIDADO </td>";
                        echo "<td class=\"border text_align_center\" width=\"8%\"  > ANULADO           </td>";
                        echo "<td class=\"border text_align_center\" width=\"8%\"  > LIQUIDADO         </td>";
                        echo "<td class=\"border text_align_center\" width=\"8%\"  > PAGO              </td>";
                        echo "<td class=\"border text_align_center\" width=\"8%\"  > EMPENHADO         </td>";
                        echo "<td class=\"border text_align_center\" width=\"8%\"  > A LIQUIDAR        </td>";
                        echo "<td class=\"border text_align_center\" width=\"10%\" > A PAGAR LIQUIDADO </td>";
                    echo "</tr>";
                echo "</thead>";

                echo "<tbody>";
                    foreach ($credor['restos'] as $chave => $value) {
                        $inCredor = $value['cgm_credor'];
                        echo "<tr>";
                            echo "<td class=\"border text_align_center\" > ".$value['cod_empenho']."/".$value['exercicio']."            </td>";
                            echo "<td class=\"border text_align_center\" > ".$value['emissao']."                                        </td>";
                            echo "<td class=\"border text_align_center\" > ".$value['vencimento']."                                     </td>";
                            echo "<td class=\"border text_align_right\"  > ".number_format($value['empenhado'],2,',','.')."             </td>";
                            echo "<td class=\"border text_align_right\"  > ".number_format($value['aliquidar'],2,',','.')."             </td>";
                            echo "<td class=\"border text_align_right\"  > ".number_format($value['liquidadoapagar'],2,',','.')."       </td>";
                            echo "<td class=\"border text_align_right\"  > ".number_format($value['anulado'],2,',','.')."               </td>";
                            echo "<td class=\"border text_align_right\"  > ".number_format($value['liquidado'],2,',','.')."             </td>";
                            echo "<td class=\"border text_align_right\"  > ".number_format($value['pagamento'],2,',','.')."             </td>";
                            echo "<td class=\"border text_align_right\"  > ".number_format($value['empenhado_saldo'],2,',','.')."       </td>";
                            echo "<td class=\"border text_align_right\"  > ".number_format($value['aliquidar_saldo'],2,',','.')."       </td>";
                            echo "<td class=\"border text_align_right\"  > ".number_format($value['liquidadoapagar_saldo'],2,',','.')." </td>";
                        echo "</tr>";
                    }
                    echo "<tr>";
                        echo "<td class=\"border text_align_center\" colspan=3 > SUB-TOTAL CREDOR                                                     </td>";
                        echo "<td class=\"border text_align_right\"  > ".number_format($total_credor[$inCredor]['empenhado'],2,',','.')."             </td>";
                        echo "<td class=\"border text_align_right\"  > ".number_format($total_credor[$inCredor]['aliquidar'],2,',','.')."             </td>";
                        echo "<td class=\"border text_align_right\"  > ".number_format($total_credor[$inCredor]['liquidadoapagar'],2,',','.')."       </td>";
                        echo "<td class=\"border text_align_right\"  > ".number_format($total_credor[$inCredor]['anulado'],2,',','.')."               </td>";
                        echo "<td class=\"border text_align_right\"  > ".number_format($total_credor[$inCredor]['liquidado'],2,',','.')."             </td>";
                        echo "<td class=\"border text_align_right\"  > ".number_format($total_credor[$inCredor]['pagamento'],2,',','.')."             </td>";
                        echo "<td class=\"border text_align_right\"  > ".number_format($total_credor[$inCredor]['empenhado_saldo'],2,',','.')."       </td>";
                        echo "<td class=\"border text_align_right\"  > ".number_format($total_credor[$inCredor]['aliquidar_saldo'],2,',','.')."       </td>";
                        echo "<td class=\"border text_align_right\"  > ".number_format($total_credor[$inCredor]['liquidadoapagar_saldo'],2,',','.')." </td>";
                    echo "</tr>";
                echo "</tbody>";
            echo "</table>";

            if($inCountRestosLinha<$inCountRestos)
                echo "<br/>";

            $inCountRestosLinha++;
        }

        if(count($restos_pagar)>0)
            echo "<pagebreak />";
    ?>
</div>

<div id="totais_exercicios">
    <table class='border'>
        <thead>
        <tr>
            <td class="border text_align_center" colspan=3  > &nbsp;                        </td>
            <td class="border text_align_center" colspan=3  > SALDO ATÉ EXERCÍCIO ANTERIOR  </td>
            <td class="border text_align_center" colspan=3  > MOVIMENTAÇÕES EXERCÍCIO ATUAL </td>
            <td class="border text_align_center" colspan=3  > SALDO ATUAL                   </td>
        </tr>

        <tr>
            <td class="border text_align_center" width="21%" colspan=3 > SUB-TOTAL EXERCÍCIO </td>
            <td class="border text_align_center" width="10%" > EMPENHADO                     </td>
            <td class="border text_align_center" width="9%"  > A LIQUIDAR                    </td>
            <td class="border text_align_center" width="10%" > A PAGAR LIQUIDADO             </td>
            <td class="border text_align_center" width="8%"  > ANULADO                       </td>
            <td class="border text_align_center" width="8%"  > LIQUIDADO                     </td>
            <td class="border text_align_center" width="8%"  > PAGO                          </td>
            <td class="border text_align_center" width="8%"  > EMPENHADO                     </td>
            <td class="border text_align_center" width="8%"  > A LIQUIDAR                    </td>
            <td class="border text_align_center" width="10%" > A PAGAR LIQUIDADO             </td>
        </tr>
    </thead>

    <?php
        foreach ($total_exercicio as $stExercicio => $subTotal) {
            echo "<tbody>";
                echo "<tr>";
                    echo "<td class=\"border text_align_center\" colspan=3 > ".$stExercicio."                                      </td>";
                    echo "<td class=\"border text_align_right\"  > ".number_format($subTotal['empenhado'],2,',','.')."             </td>";
                    echo "<td class=\"border text_align_right\"  > ".number_format($subTotal['aliquidar'],2,',','.')."             </td>";
                    echo "<td class=\"border text_align_right\"  > ".number_format($subTotal['liquidadoapagar'],2,',','.')."       </td>";
                    echo "<td class=\"border text_align_right\"  > ".number_format($subTotal['anulado'],2,',','.')."               </td>";
                    echo "<td class=\"border text_align_right\"  > ".number_format($subTotal['liquidado'],2,',','.')."             </td>";
                    echo "<td class=\"border text_align_right\"  > ".number_format($subTotal['pagamento'],2,',','.')."             </td>";
                    echo "<td class=\"border text_align_right\"  > ".number_format($subTotal['empenhado_saldo'],2,',','.')."       </td>";
                    echo "<td class=\"border text_align_right\"  > ".number_format($subTotal['aliquidar_saldo'],2,',','.')."       </td>";
                    echo "<td class=\"border text_align_right\"  > ".number_format($subTotal['liquidadoapagar_saldo'],2,',','.')." </td>";
                echo "</tr>";
            echo "</tbody>";
        }
    ?>
    </table>
</div>
<br/>
<div id="totaL">
    <table>
        <!-- TOTAL -->
        <tbody>
            <tr>
                <td class="border text_align_center" colspan=3  > &nbsp;                        </td>
                <td class="border text_align_center" colspan=3  > SALDO ATÉ EXERCÍCIO ANTERIOR  </td>
                <td class="border text_align_center" colspan=3  > MOVIMENTAÇÕES EXERCÍCIO ATUAL </td>
                <td class="border text_align_center" colspan=3  > SALDO ATUAL                   </td>
            </tr>

            <tr>
                <td class="border text_align_center" width="21%" colspan=3 > TOTAL GERAL         </td>
                <td class="border text_align_center" width="10%" > EMPENHADO                     </td>
                <td class="border text_align_center" width="9%"  > A LIQUIDAR                    </td>
                <td class="border text_align_center" width="10%" > A PAGAR LIQUIDADO             </td>
                <td class="border text_align_center" width="8%"  > ANULADO                       </td>
                <td class="border text_align_center" width="8%"  > LIQUIDADO                     </td>
                <td class="border text_align_center" width="8%"  > PAGO                          </td>
                <td class="border text_align_center" width="8%"  > EMPENHADO                     </td>
                <td class="border text_align_center" width="8%"  > A LIQUIDAR                    </td>
                <td class="border text_align_center" width="10%" > A PAGAR LIQUIDADO             </td>
            </tr>

            <tr>
                <td class="border text_align_right" colspan=3 > &nbsp;                                                            </td>
                <td class="border text_align_right"  > <?php echo number_format($total[0]['empenhado'],2,',','.');             ?> </td>
                <td class="border text_align_right"  > <?php echo number_format($total[0]['aliquidar'],2,',','.');             ?> </td>
                <td class="border text_align_right"  > <?php echo number_format($total[0]['liquidadoapagar'],2,',','.');       ?> </td>
                <td class="border text_align_right"  > <?php echo number_format($total[0]['anulado'],2,',','.');               ?> </td>
                <td class="border text_align_right"  > <?php echo number_format($total[0]['liquidado'],2,',','.');             ?> </td>
                <td class="border text_align_right"  > <?php echo number_format($total[0]['pagamento'],2,',','.');             ?> </td>
                <td class="border text_align_right"  > <?php echo number_format($total[0]['empenhado_saldo'],2,',','.');       ?> </td>
                <td class="border text_align_right"  > <?php echo number_format($total[0]['aliquidar_saldo'],2,',','.');       ?> </td>
                <td class="border text_align_right"  > <?php echo number_format($total[0]['liquidadoapagar_saldo'],2,',','.'); ?> </td>
            </tr>
        </tbody>
    </table>
</div>
<br/><br/>

<?php
    if(count($arAssinaturas) > 0){
        echo "<br/><br/><br/><br/>";
        echo "<div id=\"assinaturas\">";
            foreach ($arAssinaturas as $key => $assinatura) {
                echo "<table align='center' width=\"99%\" >";
                    echo "<tbody>";
                        foreach ($assinatura as $chave => $campo) {
                            if(count($arAssinaturas)>1)
                                $width = 33;
                            else{
                                if(count($campo) == 3)
                                    $width = 33;
                                else if(count($campo) == 2)
                                    $width = 50;
                                else
                                    $width = 100;
                            }

                            echo "<tr>";
                                foreach ($campo as $linha => $texto) {
                                    if($chave==0)
                                        $texto = str_pad($texto, 70, '_', STR_PAD_LEFT);

                                    echo "<td class=\"text_align_center\" width=\"".$width."%\" > ".$texto." </td>";

                                    if(count($campo) == 2 && $linha == 1 && $width == 33)
                                        echo "<td class=\"text_align_center\" width=\"".$width."%\" > &nbsp; </td>";
                                    else if(count($campo) == 1 && $width == 33){
                                        echo "<td class=\"text_align_center\" width=\"".$width."%\" > &nbsp; </td>";
                                        echo "<td class=\"text_align_center\" width=\"".$width."%\" > &nbsp; </td>";
                                    }
                                }
                            echo "</tr>";
                        }
                    echo "</tbody>";
                echo "</table>";
                echo "<br/><br/><br/><br/>";
            }
        echo "</div>";
        echo "<pagebreak />";
    }
?>

<div id="filtro">
    <table>
        <tbody>
            <tr>
                <td class="text_align_left" colspan=2 > Filtro Utilizado </td>
            </tr>
            <?php
                foreach ($filtro as $chave => $value) {
                    echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"12%\" > ".$value['titulo']." </td>";
                        echo "<td class=\"text_align_left\"               > ".$value['valor']."  </td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</div>