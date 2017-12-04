<body>
<table style="font-size: 13px;">
    <thead>        
        <tr>
            <td class="font_weight_bold text_align_right"> CÓDIGO               </td>
            <td class="font_weight_bold text_align_left" > TIPO                 </td>
            <td class="font_weight_bold text_align_left" > NOME DO LOGRADOURO   </td>
            <td class="font_weight_bold text_align_left" > BAIRROS              </td>
            <td class="font_weight_bold text_align_left" > CEP                  </td>
            <td class="font_weight_bold text_align_left" > UF                   </td>
            <td class="font_weight_bold text_align_left" > MUNICÍPIO            </td>
            <td class="font_weight_bold text_align_left" > DATA INICIAL         </td>
            <td class="font_weight_bold text_align_left" > DATA FINAL           </td>
                        
        </tr>
    </thead>

    <tbody>
        
        <?php foreach($arDadosLogradouro as $logradouro) { 
            if ($logradouro["grupo"] == 3)
                $stCss .= "font-style: italic;";
            else
                $stCss .= "";
        ?>
        <tr>
            <td style="width: 12mm; <?= $stCss ?>" class="text_align_right tabulacao_nivel_<?= $logradouro["grupo"]?>"> <?= $logradouro["cod_logradouro"] ?></td>
            <td style="width: 14mm; <?= $stCss ?>" class="text_align_left tabulacao_nivel_<?= $logradouro["grupo"] ?>"> <?= $logradouro["nom_tipo"]       ?></td>
            <td style="width: 98mm; <?= $stCss ?>" class="text_align_left tabulacao_nivel_<?=$logradouro["grupo"]?>"><?= $logradouro["nom_logradouro"]        ?></td>
            <td rowspan="2" style="width: 30mm; font-size: 11px;" class="text_align_left "><?= $logradouro["nom_bairro"]    ?></td>
            <td rowspan="2" style="width: 18mm; <?= $stCss ?>"    class="text_align_left "><?= $logradouro["cep"]           ?></td>
            <td rowspan="2" style="width: 10mm; <?= $stCss ?>"    class="text_align_left "><?= $logradouro["sigla_uf"]      ?></td>
            <td rowspan="2" style="width: 40mm; <?= $stCss ?>"    class="text_align_left "><?= $logradouro["nom_municipio"] ?></td>
            <td rowspan="2" style="width: 20mm; <?= $stCss ?>"    class="text_align_left "><?= $logradouro["dt_inicio"]     ?></td>
            <td rowspan="2" style="width: 10mm; <?= $stCss ?>"    class="text_align_left "><?= $logradouro["dt_fim"]        ?></td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td >NORMA: <?php if($boNorma == 'true') echo $logradouro["descricao_norma_relatorio"];?></td>
        </tr>

        <?php } ?>
    </tbody>
    
    <tfoot>
        <tr>
            <td colspan="10"></td>
        </tr>
    </tfoot>
</table>
</body>
