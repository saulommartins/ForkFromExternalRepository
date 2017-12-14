<?php  
foreach ($arDados as $dados) {
?>
<table width="100%" class="font_size_9">
  <tbody>
    <tr>
      <td width="50%" style="border-bottom: 1px dotted black; border-right:1px dotted black;">
            <table >
                <tbody>
                    <tr>
                        <td rowspan="3" width="10%">
                            <!-- <img height="30" width="50" src=<?=$dados["cabecalho"]["imagem_brasao"]?> />  -->
                        </td>
                        <td width="60%">
                            PREFEITURA MUNICIPAL DE PRESIDENTE FIGUEIREDO
                        </td>
                        <td rowspan="3" class="text_align_right vertical_align_bottom" width="40%"> 
                            Usuário: <?php echo $dados["cabecalho"]["usuario"];?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            SECRETARIA MUNICIPAL DE FINANÇAS
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b> DOCUMENTO DE ARRECADAÇÃO MUNICIPAL - DAM </b>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table >
                <tbody>
                <tr class="border_top border_left border_right">
                  <td width="60" class="border_right">
                     VENCIMENTO
                   </td>
                   <td width="60" class="border_right">
                     EMISSÃO
                   </td>
                   <td colspan=3 class="border_right" width="30">
                     MATRÍCULA DO IMÓVEL
                   </td>
                   <td width="80" class="border_right">
                        RECEITA
                   </td>
                   <td width="100" class="text_align_center">
                        VALOR
                   </td>
                </tr>
                <tr class="border_bottom border_left border_right">
                  <td class="border_right">
                    <b> <?=$dados["dados"]["vencimento"]?> </b>
                  </td>
                  <td class="text_align_center border_right">
                    <?=$dados["dados"]["data_emissao"]?>
                  </td>
                  <td colspan=3 class="text_align_center border_right">
                    <?=$dados["dados"]["inscricao"]?>
                  </td>
                  <td rowspan=5 class="text_align_left vertical_align_top tabulacao_nivel_1 border_right">
                    <?php
                        foreach ($dados["valores"]["credito"] as $credito => $valor) {
                            echo $credito." <br/>";
                        }
                    ?>                  
                  </td>
                  <td rowspan=5 class="text_align_left vertical_align_top tabulacao_nivel_1">
                  <?php
                        foreach ($dados["valores"]["credito"] as $credito => $valor) {
                            echo "(R$) &emsp; &emsp; &emsp;".number_format($valor,2,',','.')." <br/>";
                        }
                    ?>                                   
                  </td>
                 </tr>
                <tr>
                  <td class="border_left border_right">
                    TRIBUTO/RENDA
                  </td>
                  <td class="border_left border_right">
                    EXERC.
                  </td>
                  <td class="border_left border_right">
                    PARCELA
                  </td>
                  <td class="border_left border_right">
                    SP
                  </td>
                  <td class="border_left border_right">
                    PROCESSAMENTO
                  </td>
                </tr>
               <tr>
                 <td class="text_align_center border_left border_right border_bottom">
                   <?=$dados["dados"]["tributo"]?>
                 </td>
                 <td class="text_align_center border_left border_right border_bottom">
                   <?=$dados["dados"]["exercicio"]?>
                 </td>
                 <td class="text_align_center border_left border_right border_bottom">
                   <?=$dados["dados"]["num_parcela"]?>
                 </td>
                 <td class="text_align_center border_left border_right border_bottom">
                   <?=$dados["dados"]["sp"]?>
                 </td>
                 <td class="text_align_right border_left border_right border_bottom">
                   <?=$dados["dados"]["processamento"]?>
                 </td>
               </tr>
               <tr> 
                 <td colspan=2 class="border_left border_right">
                   INSCRIÇÃO CADASTRAL
                 </td>
                 <td colspan=2 class="border_left border_right">
                   BASE DE CÁLCULO
                 </td>
                 <td class="border_left border_right text_align_center">
                   ALÍQUOTA
                 </td>
                      
               </tr>
               <tr> 
                 <td colspan=2 class="tabulacao_nivel_1 border_left border_right border_bottom">
                   <?=$dados["dados"]["inscricao_cadastral"]?>
                 </td>
                 <td colspan=2 class="text_align_center border_left border_right border_bottom">
                   R$ <?=$dados["dados"]["base_calculo"]?>
                 </td>
                 <td class="text_align_center border_left border_right border_bottom">
                   <?=$dados["dados"]["aliquota"]?>
                 </td>
               </tr>
               <tr>
                 <td colspan=5 class="border_left border_right">
                   NOME CONTRIBUINTE
                 </td>
                 <td rowspan=2 class="vertical_align_top border_left border_bottom">
                    SUB-TOTAL
                 </td>
                 <td rowspan=2 class="vertical_align_top tabulacao_nivel_1 border_right border_bottom">
                    <?php
                        foreach ($dados["valores"]["credito"] as $credito => $valor) {
                            $total = $total+$valor;
                        }
                        echo "(R$) &emsp; &emsp; &emsp;".number_format($total,2,',','.');
                    ?>
                 </td>
               </tr>
               <tr>
                <td colspan=5 class="tabulacao_nivel_1 border_left border_right border_bottom">
                  <?=$dados["dados"]["contribuinte"]?>
                </td>
               </tr>
               <tr>
                <td colspan=5 class="border_left border_right">
                  ENDEREÇO DO IMÓVEL
                </td>
                <td rowspan=2 class="vertical_align_top border_left border_bottom">
                    JUROS      
                </td>
                <td rowspan=2 class="tabulacao_nivel_1 border_right border_bottom" >
                    <?php 
                        if($dados["valores"]["juros"] != '0,00'){
                            echo"(R$) &emsp; &emsp; &emsp;".$dados["valores"]["juros"];
                        }
                    ?>
                </td>
               </tr>   
               <tr>
                <td colspan=5 class="tabulacao_nivel_1 border_left border_right border_bottom">
                  <?=$dados["dados"]["endereco"]?>
                </td>                                
               </tr> 
               <tr>
                <td colspan=5 class="border_left border_right">
                  ESPECIFICAÇÃO DA RECEITA
                </td>
                <td rowspan=2 class="vertical_align_top border_left border_bottom">
                    MULTA
                </td>
                <td rowspan=2 class="tabulacao_nivel_1 border_right border_bottom">
                    <?php 
                        if($dados["valores"]["multa"] != '0,00'){
                            echo"(R$) &emsp; &emsp; &emsp;".$dados["valores"]["multa"];
                        }
                    ?>
                </td>
               </tr>
               <tr>
                <td colspan=5 class="tabulacao_nivel_1 border_left border_right border_bottom">
                  <?=$dados["dados"]["especificacao_receita"]?>
                </td>                
               </tr> 
               <tr>
                <td colspan=5 class="border_left border_right">
                  <b>INFORMAÇÕES COMPLEMENTARES </b>
                </td>
                <td rowspan=2 class="font_weight_bold vertical_align_top font_size_8 border_left border_bottom">
                    TOTAL EM REAL
                </td>
                <td rowspan=2 class="font_weight_bold text_align_right font_size_9 border_right border_bottom">
                    <?php 
                        if($dados["valores"]["total"] != '0,00'){
                            echo $dados["valores"]["total"];
                        }
                    ?>
                </td>
               </tr>  
               <tr>
                <td colspan=5 class="tabulacao_nivel_1 border_left border_right border_bottom">
                  <?=$dados["dados"]["informacoes"]?>
                </td>                
               </tr>
               <tr>
                    <td class="font_size_6 text_align_center" colspan=6 >
                        AUTENTICAÇÃO MECÂNICA NO VERSO
                    </td>
                    <td class="font_size_6 font_weight_bold text_align_right">
                        VIA CONTRINUINTE
                    </td>
                </tr>
              </tbody>
            </table>

      </td>
      <td width="50%" class="tabulacao_nivel_1" style="border-bottom: 1px dotted black; border-left:1px dotted black;">
        <table>
            <tbody>
                <tr>
                    <td >
                     <div> <barcode code=<?=$dados['dados']['codigo_barra']?> type="I25" size="0.9" height="1.5" /></div>                     
                    </td>
                </tr>
                <tr>
                    <td class="text_align_center">
                        <div> <b> <?=$dados['dados']['linha_digitavel']?> </b> </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <br/>
        <table >
                <tbody>
                    <tr>
                        <td rowspan="3" width="10%">
                            <!-- <img height="30" width="50" src=<?=$dados["cabecalho"]["imagem_brasao"]?> />  -->
                        </td>
                        <td colspan=4>
                            PREFEITURA MUNICIPAL DE PRESIDENTE FIGUEIREDO
                        </td>                        
                        <td width="80" class="border_right border_left border_top">
                            RECEITA
                        </td>
                        <td class="text_align_center border_top border_right">
                            VALOR
                        </td>
                    </tr>
                    <tr>
                        <td colspan=4>
                            SECRETARIA MUNICIPAL DE FINANÇAS 
                        </td>                        
                        <td rowspan=4 class="text_align_left vertical_align_top tabulacao_nivel_1 border_right border_bottom border_left">
                            <?php
                                foreach ($dados["valores"]["credito"] as $credito => $valor) {
                                    echo $credito." <br/>";
                                }
                            ?>                  
                        </td>
                        <td rowspan=4 class="text_align_left vertical_align_top tabulacao_nivel_1 border_right border_bottom">
                            <?php
                                foreach ($dados["valores"]["credito"] as $credito => $valor) {
                                    echo "(R$) &emsp;".number_format($valor,2,',','.')." <br/>";
                                }
                            ?>                                   
                        </td>
                        
                    </tr>
                    <tr>
                        <td colspan=4>
                            <b>DOCUMENTO DE ARRECADAÇÃO MUNICIPAL - DAM</b>
                        </td>                        
                    </tr>
                    <tr>
                        <td class="border_right border_top border_left">
                            VENCIMENTO                        
                        </td>
                        <td class="border_right border_top">
                            EMISSÃO
                        </td>
                        <td colspan=3 class="border_right border_top">
                            INSCRIÇÃO MUNÍCIPAL
                        </td>
                    </tr>
                    <tr class="border_bottom border_left border_right">
                        <td class="border_right">
                          <b> <?=$dados["dados"]["vencimento"]?> </b>
                        </td>
                        <td class="text_align_center border_right">
                          <?=$dados["dados"]["data_emissao"]?>
                        </td>
                        <td colspan=3 class="text_align_center border_right">
                          <?=$dados["dados"]["inscricao"]?>
                        </td>                        
                    </tr>
                    <tr>
                        <td class="border_left border_right">
                            TRIBUTO/RENDA
                        </td>
                        <td class="border_right">
                            EXERC.
                        </td>
                        <td class="border_right">
                            PARCELA
                        </td>
                        <td class="border_right">
                            SP
                        </td>
                        <td class="border_right">
                            PROCESSAMENTO
                        </td>
                        <td class="tabulacao_nivel_1">
                            SUB-TOTAL
                        </td>
                        <td rowspan=2 class="vertical_align_top tabulacao_nivel_1 border_right border_bottom">
                            <?php
                                foreach ($dados["valores"]["credito"] as $credito => $valor) {
                                    $total = $total+$valor;
                                }
                                echo "(R$) &emsp;".number_format($total,2,',','.');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text_align_center border_left border_right border_bottom">
                          <?=$dados["dados"]["tributo"]?>
                        </td>
                        <td class="text_align_center border_left border_right border_bottom">
                          <?=$dados["dados"]["exercicio"]?>
                        </td>
                        <td class="text_align_center border_left border_right border_bottom">
                          <?=$dados["dados"]["num_parcela"]?>
                        </td>
                        <td class="text_align_center border_left border_right border_bottom">
                          <?=$dados["dados"]["sp"]?>
                        </td>
                        <td class="text_align_right border_left border_right border_bottom">
                          <?=$dados["dados"]["processamento"]?>
                        </td>
                        <td class="border_bottom">
                        </td>
                        <td >
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2 class="border_left border_right"> 
                            INSCRIÇÃO CADASTRAL                       
                        </td>
                        <td colspan=2 class="border_right">
                            BASE DE CÁLCULO
                        </td>
                        <td class="border_right">
                            ALÍQUOTA
                        </td>                        
                        <td class="border_bottom tabulacao_nivel_1">
                            JUROS
                        </td>
                        <td class="tabulacao_nivel_1 border_right border_bottom" >
                            <?php 
                                if($dados["valores"]["juros"] != '0,00'){
                                    echo"(R$) &emsp;".$dados["valores"]["juros"];
                                }
                            ?>
                        </td>
                    </tr>
                    <tr> 
                        <td colspan=2 class="tabulacao_nivel_1 border_left border_right border_bottom">
                          <?=$dados["dados"]["inscricao_cadastral"]?>
                        </td>
                        <td colspan=2 class="text_align_center border_left border_right border_bottom">
                          R$ <?=$dados["dados"]["base_calculo"]?>
                        </td>
                        <td class="text_align_center border_left border_right border_bottom">
                          <?=$dados["dados"]["aliquota"]?>
                        </td>
                        <td class="border_bottom tabulacao_nivel_1">
                            MULTA
                        </td>
                        <td class="tabulacao_nivel_1 border_right border_bottom">
                            <?php 
                                if($dados["valores"]["multa"] != '0,00'){
                                    echo"(R$) &emsp;".$dados["valores"]["multa"];
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=5 class="border_left border_right">
                            INFORMAÇÕES COMPLEMENTARES                        
                        </td>                        
                        <td class="font_weight_bold vertical_align_top font_size_8 tabulacao_nivel_1"> 
                            TOTAL EM REAL
                        </td>
                        <td class="border_right">
                        </td>
                    </tr>
                    <tr>
                        <td colspan=5 class="tabulacao_nivel_1 border_left border_right border_bottom">
                            <?=$dados["dados"]["informacoes"]?>
                        </td>                
                        <td class="border_bottom">                            
                        </td>
                        <td class="border_right border_bottom font_weight_bold text_align_right font_size_9">
                            <?php 
                                if($dados["valores"]["total"] != '0,00'){
                                    echo $dados["valores"]["total"];
                                }
                            ?>
                        </td>
                    </tr>
                    <tr >                        
                        <td class="font_size_6 text_align_center" colspan=6 >
                            AUTENTICAÇÃO MECÂNICA NO VERSO
                        </td>
                        <td class="font_size_6 font_weight_bold text_align_right">
                            VIA BANCO
                        </td>                        
                    </tr>
                </tbody>
            </table>
      </td>
    </tr>
  </tbody>
</table>
<?php
}
?>