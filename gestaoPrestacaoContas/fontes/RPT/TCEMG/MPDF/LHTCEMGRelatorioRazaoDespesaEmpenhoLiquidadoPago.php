<div class='text_align_center font_weight_bold'>
    <h4>Razão da Despesa - <?= $stTipoRelatorio ?></h4>
</div>

<!-- DESPESA -->

<?php foreach($arOrgaoUnidade as $orgaoUnidade){ ?>
    <h5>
        <?php 
            $arChaveOrgaoUnidade = explode(",", $orgaoUnidade );
            echo "Orgão: ".$arChaveOrgaoUnidade[0]." Unidade: ".$arChaveOrgaoUnidade[1];            
        ?>
    </h5>
    
    <table class='border'>
        <thead>
            <tr>
                <th class='text_align_center border' style="width:10mm;">Empenho</th>
                <th class='text_align_left border' style="width:15mm;">Data Emp.</th>
                <th class='text_align_left border' style="width:15mm;">Data Pag.</th>
                <th class='text_align_left border' style="width:35mm;">Credor</th>
                <th class='text_align_left border' style="width:40mm;">Banco / Ag. / Cc.</th>
                <th class='text_align_left border' style="width:10mm;">Recurso</th>
                <th class='text_align_left border' style="width:15mm;">Documento</th>
            <?php    
                switch ($stTipoRelatorio) {
                    case 'Empenhado':
                        echo "
                            <th class='text_align_right border' style=\"width:45mm;\">Valor Empenhado</th>
                            ";
                    break;
                    
                    case 'Liquidado':
                        echo "
                            <th class='text_align_right border' style=\"width:45mm;\">Valor Liquidado</th>
                            ";
                    break;

                    case 'Pago':
                        echo "
                            <th class='text_align_right border' style=\"width:45mm;\">Valor Pago</th>
                            ";
                    break;

                }
            ?>    
                <th class='text_align_left border' style="width:45mm;">Dotação</th>
                <th class='text_align_left border' style="width:15mm;">Recurso</th>
            </tr>
        </thead>
        <tbody>
            
        <?php foreach($registros as $registro){
            $stOrgaoUnidadeConsulta = $registro['num_orgao'].$registro['num_unidade'];
            $stOrgaoUnidadeChave = $arChaveOrgaoUnidade[0].$arChaveOrgaoUnidade[1];
        ?>
            <?php if( $stOrgaoUnidadeConsulta == $stOrgaoUnidadeChave ){ ?>
            <tr>
                <td class='text_align_center border'><?= $registro['empenho'] ?></td>
                <td class='text_align_left border'><?= $registro['dt_empenho'] ?></td>
                <td class='text_align_left border'><?= $registro['dt_pagamento'] ?></td>
                <td class='text_align_left border'><?= $registro['credor'] ?></td>
                <td class='text_align_left border'><?= $registro['banco'] ?></td>
                <td class='text_align_left border'><?= $registro['cod_recurso_banco'] ?></td>
                <td class='text_align_left border'><?= $registro['num_documento'] ?></td>          
            <?php    
                switch ($stTipoRelatorio) {
                    case 'Empenhado':
                        echo "
                            <td class='text_align_right border'>".number_format($registro['valor'], '2', ',', '.')."</td>
                            ";
                    break;
                    
                    case 'Liquidado':
                        echo "
                            <td class='text_align_right border'>".number_format($registro['valor_liquidado'], '2', ',', '.')."</td>
                            ";
                    break;

                    case 'Pago':
                        echo "
                            <td class='text_align_right border'>".number_format($registro['valor_pago'], '2', ',', '.')."</td>
                            ";
                    break;

                }
            ?>  
                <td class='text_align_left border'><?= $registro['dotacao'] ?></td>
                <td class='text_align_left border'><?= $registro['cod_recurso'] ?></td>
            </tr>
            <?php
                    switch ($stTipoRelatorio) {
                        case 'Empenhado':
                            if(($registroAnterior['empenho'] != $registro['empenho']) || (!isset($registroAnterior))){
                                $totalEmpenhado = $totalEmpenhado + $registro['valor'];
                            }
                        break;
                        
                        case 'Liquidado':
                            if(($registroAnterior['empenho'] != $registro['empenho']) || (!isset($registroAnterior))){                            
                                $totalLiquidado = $totalLiquidado + $registro['valor_liquidado'];
                            }
                        break;
    
                        case 'Pago':
                            $totalPago = $totalPago + $registro['valor_pago'];
                        break;

                    }
                    
                    $registroAnterior = $registro;
                    
                }//end if chave orgao
                
            }//end foreach
            ?>
        
        </tbody>
    </table>
    
    <p>
        <?php
            switch ($stTipoRelatorio) {
                case 'Empenhado':
                    echo "Total Empenhado: ".number_format($totalEmpenhado, '2', ',', '.')."<br />";
                break;
                
                case 'Liquidado':
                    echo "Total Liquidado: ".number_format($totalLiquidado, '2', ',', '.')."<br />";                            
                break;
            
                case 'Pago':                    
                    echo "Total Pago: ".number_format($totalPago, '2', ',', '.');
                break;

            }
        ?>
    </p>
    
    <?php
            switch ($stTipoRelatorio) {
                case 'Empenhado':
                    $totalGeralEmpenhado += $totalEmpenhado;
                    $totalEmpenhado = 0;
                break;
                
                case 'Liquidado':                    
                    $totalGeralLiquidado += $totalLiquidado;
                    $totalLiquidado = 0;
                break;
            
                case 'Pago':                    
                    $totalGeralPago      += $totalPago;
                    $totalPago      = 0;
                break;
            
            }
        }//end for foreach
    ?>
    
    <p>
        <h5>Total Geral</h5>
        <?php
            switch ($stTipoRelatorio) {
                case 'Empenhado':                    
                    echo "Empenhado: ".number_format($totalGeralEmpenhado, '2', ',', '.')."<br />";
                break;
                
                case 'Liquidado':                    
                    echo "Liquidado: ".number_format($totalGeralLiquidado, '2', ',', '.')."<br />";
                break;
            
                case 'Pago':                                        
                    echo "Pago: ".number_format($totalGeralPago, '2', ',', '.');
                break;

            }
        ?>
    </p>