<div>
    <table class='border'>
        <thead>
            <tr class='border font_weight_bold text_align_center'>
                <th colspan=3>Demonstração da Dívida Flutuante</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text_align_center" width="33%"><?php echo "Exercício: ".Sessao::getExercicio(); ?></td>
                <td class="text_align_center" width="33%"><?php echo "Município: ".$municipio; ?></td>
                <td class="text_align_right"  width="33%"><?php date_default_timezone_set("America/Sao_Paulo"); echo "Data: ". date('d/m/Y - G:i:s'); ?></td>
            </tr>
        </tbody>
    </table>
</div>
<br/>
<div id="restos_pagar_entidades">
    <table class='border'>
        <thead>
            <tr>
                <td class="border text_align_center" width="20%">Título</td>
                <td class="border text_align_center" width="08%">Identificação do Orgão</td>
                <td class="border text_align_center" width="14%">Saldo Anterior</td>
                <td class="border text_align_center" width="14%">Inscrição</td>
                <td class="border text_align_center" width="14%">Restabelicimento</td>
                <td class="border text_align_center" width="14%">Baixa</td>
                <td class="border text_align_center" width="14%">Cancelamento</td>
                <td class="border text_align_center" width="14%">Saldo Atual</td>
            </tr>
            <tr>
                <td class="text_align_left font_weight_bold" colspan=8>Restos a Pagar - Exercício Atual</td>
            </tr>
        </thead>
        <tbody>
            <?php
                //Exercício Atual
                foreach ($total_restos_entidade as $key => $exercicio) {
                    foreach ($exercicio as $stExercicio => $restos) {
                        $vlTotalInscricaoAtual  += $restos['inscricao_p'];
                        $vlTotalSaldoAtualAtual += $restos['saldo_atual_p'];

                        echo '<tr>';
                        echo '<td class="border text_align_left"  > Restos a Pagar '.$stExercicio.'                    </td>';
                        echo '<td class="border text_align_center"> '.$restos['entidade'].'                            </td>';
                        echo '<td class="border text_align_right"> 0,00                                                </td>';
                        echo '<td class="border text_align_right">'.number_format($restos['inscricao_p'],2,',','.').'  </td>';
                        echo '<td class="border text_align_right"> 0,00                                                </td>';
                        echo '<td class="border text_align_right"> 0,00                                                </td>';
                        echo '<td class="border text_align_right"> 0,00                                                </td>';
                        echo '<td class="border text_align_right">'.number_format($restos['saldo_atual_p'],2,',','.').'</td>';
                        echo '</tr>';
                    }
                }
            ?>
            <tr>
                <td class="border text_align_right font_weight_bold" colspan=2>Total                                                </td>
                <td class="border text_align_right font_weight_bold"> 0,00                                                          </td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalInscricaoAtual,2,',','.');  ?></td>
                <td class="border text_align_right font_weight_bold"> 0,00                                                          </td>
                <td class="border text_align_right font_weight_bold"> 0,00                                                          </td>
                <td class="border text_align_right font_weight_bold"> 0,00                                                          </td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalSaldoAtualAtual,2,',','.'); ?></td>
            </tr>
        </tbody>
    </table>
</div>
<br/>
<div  id="restos_pagar">
    <table class='border'>
        <thead>
            <tr>
                <td class="border text_align_center" width="20%">Título</td>
                <td class="border text_align_center" width="08%">Identificação do Orgão</td>
                <td class="border text_align_center" width="14%">Saldo Anterior</td>
                <td class="border text_align_center" width="14%">Inscrição</td>
                <td class="border text_align_center" width="14%">Restabelicimento</td>
                <td class="border text_align_center" width="14%">Baixa</td>
                <td class="border text_align_center" width="14%">Cancelamento</td>
                <td class="border text_align_center" width="14%">Saldo Atual</td>
            </tr>
        </thead>
        <tbody>
            <!-- RESTO PROCESSADOS -->
            <tr>
                <td class="text_align_left font_weight_bold" colspan=8>Restos a Pagar Processados - Exercícios Anteriores</td>
            </tr>
                <?php
                    //Processo para gerar os calculos
                    foreach ($restos_pagar->getElementos() as $key => $restos) {
                        $vlTotalSaldoAnteriorRP    += $restos['saldo_anterior_p'];
                        $vlTotalInscricaoRP        += $restos['inscricao_p'];
                        $vlTotalRestabelecimentoRP += $restos['restabelecimento_p'];
                        $vlTotalBaixaRP            += $restos['baixa_p'];
                        $vlTotalCancelamentoRP     += $restos['cancelamento_p'];
                        $vlTotalSaldoAtualRP       += $restos['saldo_atual_p'];

                        $restos['saldo_anterior_p'] = number_format($restos['saldo_anterior_p'],2,',','.');
                        $restos['inscricao_p'] = number_format($restos['inscricao_p'],2,',','.');
                        $restos['restabelecimento_p'] = number_format($restos['restabelecimento_p'],2,',','.');
                        $restos['baixa_p']          = number_format($restos['baixa_p'],2,',','.');
                        $restos['cancelamento_p']  = number_format($restos['cancelamento_p'],2,',','.');
                        $restos['saldo_atual_p']    = number_format($restos['saldo_atual_p'],2,',','.');

                        echo "<tr>";
                        echo "<td class=\"border text_align_left\"  > ". $restos['titulo']         ."</td>";
                        echo "<td class=\"border text_align_center\"> ". $restos['entidade']   ."</td>";
                        echo "<td class=\"border text_align_right\" > ". $restos['saldo_anterior_p'] ."</td>";
                        echo "<td class=\"border text_align_right\" > ".$restos['inscricao_p']."</td>";
                        echo "<td class=\"border text_align_right\" > ".$restos['restabelecimento_p']." </td>";
                        echo "<td class=\"border text_align_right\" > ". $restos['baixa_p']          ."</td>";
                        echo "<td class=\"border text_align_right\" > ". $restos['cancelamento_p']  ."</td>";
                        echo "<td class=\"border text_align_right\" > ". $restos['saldo_atual_p']    ."</td>";
                        echo "</tr>";
                    }
                ?>
            <tr>
                <td class="border text_align_right font_weight_bold" colspan=2 > Total </td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalSaldoAnteriorRP,2,',','.'); ?></td>
                <td class="border text_align_right font_weight_bold"> <?php echo number_format($vlTotalInscricaoRP,2,',','.'); ?> </td>
                <td class="border text_align_right font_weight_bold"> <?php echo number_format($vlTotalRestabelecimentoRP,2,',','.');         ?> </td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalBaixaRP,2,',','.');         ?></td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalCancelamentoRP,2,',','.');  ?></td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalSaldoAtualRP,2,',','.');    ?></td>
            </tr>

            <!-- RESTO NAO PROCESSADOS -->
            <tr>
                <td class="text_align_left font_weight_bold" colspan=8>Restos a Pagar Não Processados - Exercícios Anteriores</td>
            </tr>
                <?php
                    //Processo para gerar os calculos
                    foreach ($restos_pagar->getElementos() as $key => $restos) {
                        $vlTotalSaldoAnteriorRNP    += $restos['saldo_anterior_np'];
                        $vlTotalInscricaoRNP        += $restos['inscricao_np'];
                        $vlTotalRestabelecimentoRNP += $restos['restabelecimento_np'];
                        $vlTotalBaixaRNP            += $restos['baixa_np'];
                        $vlTotalCancelamentoRNP     += $restos['cancelamento_np'];
                        $vlTotalSaldoAtualRNP       += $restos['saldo_atual_np'];

                        $restos['saldo_anterior_np'] = number_format($restos['saldo_anterior_np'],2,',','.');
                        $restos['inscricao_np'] = number_format($restos['inscricao_np'],2,',','.');
                        $restos['restabelecimento_np'] = number_format($restos['restabelecimento_np'],2,',','.');
                        $restos['baixa_np']          = number_format($restos['baixa_np'],2,',','.');
                        $restos['cancelamento_np']  = number_format($restos['cancelamento_np'],2,',','.');
                        $restos['saldo_atual_np']    = number_format($restos['saldo_atual_np'],2,',','.');

                        echo "<tr>";
                        echo "<td class=\"border text_align_left\"  > ". $restos['titulo']         ."</td>";
                        echo "<td class=\"border text_align_center\"> ". $restos['entidade']   ."</td>";
                        echo "<td class=\"border text_align_right\" > ". $restos['saldo_anterior_np'] ."</td>";
                        echo "<td class=\"border text_align_right\" > ".$restos['inscricao_np']."</td>";
                        echo "<td class=\"border text_align_right\" > ".$restos['restabelecimento_np']." </td>";
                        echo "<td class=\"border text_align_right\" > ". $restos['baixa_np']          ."</td>";
                        echo "<td class=\"border text_align_right\" > ". $restos['cancelamento_np']  ."</td>";
                        echo "<td class=\"border text_align_right\" > ". $restos['saldo_atual_np']    ."</td>";
                        echo "</tr>";
                    }
                ?>
            <tr>
                <td class="border text_align_right font_weight_bold" colspan=2 > Total </td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalSaldoAnteriorRNP,2,',','.'); ?></td>
                <td class="border text_align_right font_weight_bold"> <?php echo number_format($vlTotalInscricaoRNP,2,',','.'); ?></td>
                <td class="border text_align_right font_weight_bold"> <?php echo number_format($vlTotalRestabelecimentoRNP,2,',','.'); ?> </td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalBaixaRNP,2,',','.');         ?></td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalCancelamentoRNP,2,',','.');  ?></td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalSaldoAtualRNP,2,',','.');    ?></td>
            </tr>

            <!-- SERVIÇOS DA DIVIDA A PAGAR -->
            <tr>
                <td class="text_align_left font_weight_bold" colspan=8>Serviços da Dívida a Pagar</td>
            </tr>
            <tr>
                <td class="border text_align_right font_weight_bold" colspan=2 > Total </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
            </tr>

            <!-- DEPOSITOS -->
            <tr>
                <td class="text_align_left font_weight_bold" colspan=8>Depósitos</td>
            </tr>
                <?php
                    //Processo para gerar os calculos
                    foreach ($depositos->getElementos() as $key => $deposito) {
                        $vlTotalSaldoAnteriorDepositos    += $deposito['vl_saldo_anterior'];
                        $vlTotalInscricaoDepositos        += $deposito['inscricao'];
                        $vlTotalRestabelecimentoDepositos += 0.00;
                        $vlTotalBaixaDepositos            += $deposito['baixa'];
                        $vlTotalCancelamentoDepositos     += 0.00;
                        $vlTotalSaldoAtualDepositos       += $deposito['vl_saldo_atual'];

                        $deposito['vl_saldo_anterior'] = number_format($deposito['vl_saldo_anterior'],2,',','.');
                        $deposito['inscricao']         = number_format($deposito['inscricao'],2,',','.');
                        $deposito['baixa']             = number_format($deposito['baixa'],2,',','.');
                        $deposito['vl_saldo_atual']    = number_format($deposito['vl_saldo_atual'],2,',','.');
                        
                        echo "<tr>";
                        echo "<td class=\"border text_align_left\"  >". $deposito['nom_conta']         ."</td>";
                        echo "<td class=\"border text_align_center\">". $deposito['nome_entidade']      ."</td>";
                        echo "<td class=\"border text_align_right\" >". $deposito['vl_saldo_anterior'] ."</td>";
                        echo "<td class=\"border text_align_right\" >". $deposito['inscricao']         ."</td>";
                        echo "<td class=\"border text_align_right\" > 0,00                               </td>";
                        echo "<td class=\"border text_align_right\" >". $deposito['baixa']            ."</td>";
                        echo "<td class=\"border text_align_right\" > 0,00                               </td>";
                        echo "<td class=\"border text_align_right\" >". $deposito['vl_saldo_atual']    ."</td>";
                        echo "</tr>";
                    }
                ?>
            <tr>
                <td class="border text_align_right font_weight_bold" colspan=2 > Total </td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalSaldoAnteriorDepositos,2,',','.'); ?></td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalInscricaoDepositos,2,',','.');     ?></td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalBaixaDepositos,2,',','.');         ?></td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalSaldoAtualDepositos,2,',','.');    ?></td>
            </tr>
            
            <!-- DEBITOS DA TESOURARIA -->
            <tr>
                <td class="text_align_left font_weight_bold" colspan=8>Débitos de Tesouraria</td>
            </tr>
            <tr>
                <td class="border text_align_right font_weight_bold" colspan=2 > Total </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
            </tr>

            <!-- Outras Operações -->
            <tr>
                <td class="text_align_left font_weight_bold" colspan=8>Outras Operações</td>
            </tr>
            <tr>
                <td class="border text_align_right font_weight_bold" colspan=2 > Total </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
                <td class="border text_align_right font_weight_bold"> 0,00 </td>
            </tr>

            <!-- Montante Final -->
            <tr>
                <td class="text_align_left font_weight_bold" colspan=8>Montante Final</td>
            </tr>
                <?php
                    $vlTotalSaldoAnteriorMontante    = $total_restos_entidade['saldo_anterior'] + $vlTotalSaldoAnteriorRP + $vlTotalSaldoAnteriorRNP + $vlTotalSaldoAnteriorDepositos;
                    $vlTotalInscricaoMontante        = $total_restos_entidade['inscricoes'] + $vlTotalInscricaoRP + $vlTotalInscricaoRNP + $vlTotalInscricaoDepositos + $vlTotalInscricaoAtual;
                    $vlTotalRestabelecimentoMontante = /*$total_restos_entidade['inscricoes'] + */ $vlTotalRestabelecimentoRP + $vlTotalRestabelecimentoRNP + $vlTotalRestabelecimentoDepositos;
                    $vlTotalBaixaMontante            = $total_restos_entidade['baixas'] + $vlTotalBaixaRP + $vlTotalBaixaRNP + $vlTotalBaixaDepositos;
                    $vlTotalCancelamentoMontante     = $total_restos_entidade['cancelamentos'] + $vlTotalCancelamentoRP + $vlTotalCancelamentoRNP + $vlTotalCancelamentoDepositos;
                    $vlTotalSaldoAtualMontante       = $total_restos_entidade['saldo_atual'] + $vlTotalSaldoAtualRP + $vlTotalSaldoAtualRNP + $vlTotalSaldoAtualDepositos +$vlTotalSaldoAtualAtual;
                ?>
            <tr>
                <td class="border text_align_right font_weight_bold" colspan=2 > Total </td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalSaldoAnteriorMontante,2,',','.');    ?></td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalInscricaoMontante,2,',','.');        ?></td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalRestabelecimentoMontante,2,',','.'); ?></td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalBaixaMontante,2,',','.');            ?></td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalCancelamentoMontante,2,',','.');     ?></td>
                <td class="border text_align_right font_weight_bold"><?php echo number_format($vlTotalSaldoAtualMontante,2,',','.');       ?></td>
            </tr>
        </tbody>
    </table>
</div>
<br/>
<pagebreak />
<div id="totais_orgao">
    <table>
        <!-- TOTAIS ORGAO -->
        <tbody>
            <tr>
                <td class="border text_align_left font_weight_bold" colspan=7>Totais por Órgão</td>
            </tr>
            <tr>
                <td class="border text_align_center" width="28%">Identificação do Órgão</td>
                <td class="border text_align_center" width="14%">Saldo Anterior</td>
                <td class="border text_align_center" width="14%">Inscrição</td>
                <td class="border text_align_center" width="14%">Restabelicimento</td>
                <td class="border text_align_center" width="14%">Baixa</td>
                <td class="border text_align_center" width="14%">Cancelamento</td>
                <td class="border text_align_center" width="14%">Saldo Atual</td>
            </tr>
            <?php
                    //Processo para gerar os calculos
                    foreach ($totais_orgao->getElementos() as $key => $totalOrgao) {

                        $totalOrgao['saldo_anterior']   = number_format($totalOrgao['saldo_anterior'],2,',','.');
                        $totalOrgao['inscricao']        = number_format($totalOrgao['inscricao'],2,',','.');
                        $totalOrgao['restabelecimento'] = number_format($totalOrgao['restabelecimento'],2,',','.');
                        $totalOrgao['baixa']            = number_format($totalOrgao['baixa'],2,',','.');
                        $totalOrgao['cancelamentos']    = number_format($totalOrgao['cancelamentos'],2,',','.');
                        $totalOrgao['saldo_atual']      = number_format($totalOrgao['saldo_atual'],2,',','.');
                        
                        echo "<tr>";
                        echo "<td class=\"border text_align_left\"  >". $totalOrgao['nom_entidade']     ."</td>";
                        echo "<td class=\"border text_align_right font_weight_bold\" >". $totalOrgao['saldo_anterior']   ."</td>";
                        echo "<td class=\"border text_align_right font_weight_bold\" >". $totalOrgao['inscricao']        ."</td>";
                        echo "<td class=\"border text_align_right font_weight_bold\" >". $totalOrgao['restabelecimento'] ."</td>";
                        echo "<td class=\"border text_align_right font_weight_bold\" >". $totalOrgao['baixa']            ."</td>";
                        echo "<td class=\"border text_align_right font_weight_bold\" >". $totalOrgao['cancelamentos']    ."</td>";
                        echo "<td class=\"border text_align_right font_weight_bold\" >". $totalOrgao['saldo_atual']      ."</td>";
                        echo "</tr>";
                    }
                ?>
        </tbody>
    </table>
</div>
<br/>
<div id="totais_contas_devedoras">
    <table>
        <!-- TOTAIS ORGAO CONTAS DEVEDORAS -->
        <tbody>
            <tr>
                <td class="border text_align_left font_weight_bold" colspan=7>Totais por Órgão (Contas devedoras compõem "Devedores Diversos")</td>
            </tr>
            <tr>
                <td class="border text_align_center" width="28%">Identificação do Órgão</td>
                <td class="border text_align_center" width="14%">Saldo Anterior</td>
                <td class="border text_align_center" width="14%">Inscrição</td>
                <td class="border text_align_center" width="14%">Restabelicimento</td>
                <td class="border text_align_center" width="14%">Baixa</td>
                <td class="border text_align_center" width="14%">Cancelamento</td>
                <td class="border text_align_center" width="14%">Saldo Atual</td>
            </tr>
            <!-- TOTAIS ORGAO CONTAS DEVEDORAS ESPERANDO DADOS QUE NAO EXISTES-->
            <?php
                    echo "<tr>";
                    echo "<td class=\"border text_align_left  \" >&nbsp;</td>";
                    echo "<td class=\"border text_align_right font_weight_bold\" >0,00</td>";
                    echo "<td class=\"border text_align_right font_weight_bold\" >0,00</td>";
                    echo "<td class=\"border text_align_right font_weight_bold\" >0,00</td>";
                    echo "<td class=\"border text_align_right font_weight_bold\" >0,00</td>";
                    echo "<td class=\"border text_align_right font_weight_bold\" >0,00</td>";
                    echo "<td class=\"border text_align_right font_weight_bold\" >0,00</td>";
                    echo "</tr>";
                ?>
        </tbody>
    </table>
</div>

