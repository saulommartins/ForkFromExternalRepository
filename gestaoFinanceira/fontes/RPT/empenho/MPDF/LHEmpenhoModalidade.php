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
    * HTML do relatório Empenho por Modalidade
    * Data de Criação: 22/03/2016

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    $Id: LHEmpenhoModalidade.php 64778 2016-03-31 13:51:44Z michel $
*/
?>
<div id="empenho_modalidade">
    <?php
            echo "<table>";
                echo "<thead>";
                    echo "<tr>";
                        echo "<td class=\"font_size_12 text_align_center tr_nivel_1\" colspan=8 > Modalidade - ".$stModalidade." </td>";
                    echo "</tr>";

                    echo "<tr>";
                        echo "<td class=\"border_bottom text_align_center tr_nivel_1 font_size_10\" width=\"10%\" > DATA EMPENHO    </td>";
                        echo "<td class=\"border_bottom text_align_center tr_nivel_1 font_size_10\" width=\"8%\"  > EMPENHO         </td>";
                        echo "<td class=\"border_bottom text_align_left tr_nivel_1 font_size_10\"   width=\"31%\" > ENTIDADE        </td>";
                        echo "<td class=\"border_bottom text_align_right tr_nivel_1 font_size_10\"  width=\"11%\" > VALOR EMPENHADO </td>";
                        echo "<td class=\"border_bottom text_align_center tr_nivel_1 font_size_10\" width=\"8%\"  > LIQUIDAÇÃO      </td>";
                        echo "<td class=\"border_bottom text_align_center tr_nivel_1 font_size_10\" width=\"10%\" > DATA PAGAMENTO  </td>";
                        echo "<td class=\"border_bottom text_align_right tr_nivel_1 font_size_10\"  width=\"11%\" > VALOR LIQUIDADO </td>";
                        echo "<td class=\"border_bottom text_align_right tr_nivel_1 font_size_10\"  width=\"11%\" > VALOR PAGO      </td>";
                    echo "</tr>";
                echo "</thead>";

                echo "<tbody>";
                    foreach ($arEmpenho as $stCodEmpenho => $empenho) {
                        echo "<tr>";
                            echo "<td class=\"text_align_center tr_nivel_2 font_size_10\" > ".$arInfoEmpenho[$stCodEmpenho]['dt_empenho']."                          </td>";
                            echo "<td class=\"text_align_center tr_nivel_2 font_size_10\" > ".$arInfoEmpenho[$stCodEmpenho]['empenho']."                             </td>";
                            echo "<td class=\"text_align_left tr_nivel_2 font_size_10\"   > ".$arInfoEmpenho[$stCodEmpenho]['entidade']."                            </td>";
                            echo "<td class=\"text_align_right tr_nivel_2 font_size_10\"  > ".number_format($arInfoEmpenho[$stCodEmpenho]['vl_empenho'],2,',','.')." </td>";
                            echo "<td class=\"text_align_right tr_nivel_2 font_size_10\"  > </td>";
                            echo "<td class=\"text_align_right tr_nivel_2 font_size_10\"  > </td>";
                            echo "<td class=\"text_align_right tr_nivel_2 font_size_10\"  > </td>";
                            echo "<td class=\"text_align_right tr_nivel_2 font_size_10\"  > </td>";
                        echo "</tr>";

                        foreach ($empenho as $pagamento) {
                            echo "<tr>";
                                echo "<td class=\"text_align_right tr_nivel_1 font_size_10\"  > </td>";
                                echo "<td class=\"text_align_right tr_nivel_1 font_size_10\"  > </td>";
                                echo "<td class=\"text_align_right tr_nivel_1 font_size_10\"  > </td>";
                                echo "<td class=\"text_align_right tr_nivel_1 font_size_10\"  > </td>";
                                echo "<td class=\"text_align_center tr_nivel_1 font_size_10\" > ".$pagamento['cod_nota']."/".$pagamento['exercicio_nota']." </td>";
                                echo "<td class=\"text_align_center tr_nivel_1 font_size_10\" > ".$pagamento['dt_pagamento']."                              </td>";
                                echo "<td class=\"text_align_right tr_nivel_1 font_size_10\"  > ".number_format($pagamento['vl_nota'],2,',','.')."          </td>";
                                echo "<td class=\"text_align_right tr_nivel_1 font_size_10\"  > ".number_format($pagamento['vl_pagamento'],2,',','.')."     </td>";
                            echo "</tr>";
                        }

                        echo "<tr>";
                            echo "<td class=\"text_align_right tr_nivel_1 font_size_10\"                            > CREDOR                                        </td>";
                            echo "<td class=\"text_align_left tr_nivel_1 tabulacao_nivel_2 font_size_10\" colspan=7 > ".$arInfoEmpenho[$stCodEmpenho]['credor']."   </td>";
                        echo "</tr>";

                        echo "<tr>";
                            echo "<td class=\"text_align_right tr_nivel_1 font_size_10\"                            > DESCRIÇÃO                                      </td>";
                            echo "<td class=\"text_align_left tr_nivel_1 tabulacao_nivel_2 font_size_10\" colspan=7 > ".$arInfoEmpenho[$stCodEmpenho]['descricao']." </td>";
                        echo "</tr>";

                        echo "<tr>";
                            echo "<td class=\"border_bottom text_align_right tr_nivel_4 font_size_10\" colspan=3  > SUB-TOTAL EMPENHO                                                          </td>";
                            echo "<td class=\"border_bottom text_align_right tr_nivel_4 font_size_10\"            > ".number_format($arInfoEmpenho[$stCodEmpenho]['vl_empenho'],2,',','.')."   </td>";
                            echo "<td class=\"border_bottom text_align_right tr_nivel_4 font_size_10\"            >                                                                            </td>";
                            echo "<td class=\"border_bottom text_align_right tr_nivel_4 font_size_10\"            >                                                                            </td>";
                            echo "<td class=\"border_bottom text_align_right tr_nivel_4 font_size_10\"            > ".number_format($arInfoEmpenho[$stCodEmpenho]['vl_liquidado'],2,',','.')." </td>";
                            echo "<td class=\"border_bottom text_align_right tr_nivel_4 font_size_10\"            > ".number_format($arInfoEmpenho[$stCodEmpenho]['vl_pago'],2,',','.')."      </td>";
                        echo "</tr>";
                    }

                    foreach ($arTotalExercicio as $totalExercicio) {
                        echo "<tr>";
                            echo "<td class=\"text_align_right tr_nivel_4 font_size_10\" colspan=3  > SUB-TOTAL ".$totalExercicio['exercicio']."                   </td>";
                            echo "<td class=\"text_align_right tr_nivel_4 font_size_10\"            > ".number_format($totalExercicio['vl_empenho'],2,',','.')."   </td>";
                            echo "<td class=\"text_align_right tr_nivel_4 font_size_10\"            >                                                              </td>";
                            echo "<td class=\"text_align_right tr_nivel_4 font_size_10\"            >                                                              </td>";
                            echo "<td class=\"text_align_right tr_nivel_4 font_size_10\"            > ".number_format($totalExercicio['vl_liquidado'],2,',','.')." </td>";
                            echo "<td class=\"text_align_right tr_nivel_4 font_size_10\"            > ".number_format($totalExercicio['vl_pago'],2,',','.')."      </td>";
                        echo "</tr>";
                    }
                    
                    echo "<tr>";
                        echo "<td class=\"border_top text_align_right tr_nivel_4 font_size_10\" colspan=3  > TOTAL GERAL                                           </td>";
                        echo "<td class=\"border_top text_align_right tr_nivel_4 font_size_10\"            > ".number_format($arTotal['vl_empenho'],2,',','.')."   </td>";
                        echo "<td class=\"border_top text_align_right tr_nivel_4 font_size_10\"            >                                                       </td>";
                        echo "<td class=\"border_top text_align_right tr_nivel_4 font_size_10\"            >                                                       </td>";
                        echo "<td class=\"border_top text_align_right tr_nivel_4 font_size_10\"            > ".number_format($arTotal['vl_liquidado'],2,',','.')." </td>";
                        echo "<td class=\"border_top text_align_right tr_nivel_4 font_size_10\"            > ".number_format($arTotal['vl_pago'],2,',','.')."      </td>";
                    echo "</tr>";
                    

                echo "</tbody>";
            echo "</table>";
    ?>
</div>
<br/><br/>

<?php
    if(count($arAssinaturas) > 0){
        echo "<br/><br/>";
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
            if(count($filtro) > 0){
                foreach ($filtro as $chave => $value) {
                    echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"12%\" > ".$value['titulo']." </td>";
                        echo "<td class=\"text_align_left\"               > ".$value['valor']."  </td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>