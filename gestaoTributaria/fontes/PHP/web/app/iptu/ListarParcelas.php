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
?>
<?php
    include_once '../../../../../../web/IniciaSessao.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';
    include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/negocio/RARRCarne.class.php';

/**
* Obter Espelho de IPTU
*/
// descobrir ultimo lancamento do imovel
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/negocio/RARRLancamento.class.php';
$obRARRLanc = new RARRLancamento ( new RARRCalculo );
$obRARRLanc->obRCIMImovel->setNumeroInscricao($_REQUEST['im']);
$obRARRLanc->roRARRCalculo->obRARRGrupo->setExercicio($_REQUEST["exercicio"]);
$obRARRLanc->buscaLancamentoAnterior($rsLancamento);
$inLancamento = $rsLancamento->getCampo('valor');

// busca cgm
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/mapeamento/FARRCgmLancamento.class.php';
$obCgmLanc = new FARRCgmLancamento;
$obCgmLanc->executaFuncao($rsCgm,$inLancamento);
$inCgm = $rsCgm->getCampo('valor');

$obRARRCarne = new RARRCarne;
$obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $inLancamento );
$obRARRCarne->inCodContribuinteInicial = $inCgm;
// Grupo IPTU estatico
$obRARRCarne->inCodGrupo = 101;
$obRARRCarne->stExercicio = $_REQUEST["exercicio"];
$obErro = $obRARRCarne->reemitirCarne( $rsEspelho );

while ( !$rsEspelho->eof() ) {
            $Ct               = $rsEspelho->getCampo( 'ano_aquisicao'       );
            $Ca               = $rsEspelho->getCampo( 'ca'                  );
            $Cep              = $rsEspelho->getCampo( 'cep'                 );
            $Exercicio        = $rsEspelho->getCampo( 'exercicio'           );
            $NomCgm           = $rsEspelho->getCampo( 'nom_cgm'             );
            $Rua              = $rsEspelho->getCampo( 'nom_logradouro'      );
            $Numero           = $rsEspelho->getCampo( 'numero'              );
            $Complemento      = $rsEspelho->getCampo( 'complemento'         );
            $Cidade           = $rsEspelho->getCampo( 'nom_municipio'       );
            $Uf               = $rsEspelho->getCampo( 'sigla_uf'            );
            $Inscricao        = $rsEspelho->getCampo( 'inscricao_municipal' );
            $CtmDci           = $rsEspelho->getCampo( 'ctm_dci'             );
            $CodLogradouro    = $rsEspelho->getCampo( 'cod_logradouro'      );
            $Distrito         = $rsEspelho->getCampo( 'distrito'            );
            $Processamento    = $rsEspelho->getCampo( 'data_processamento'  );
            $AreaTerreno      = $rsEspelho->getCampo( 'area_real'           );
            $AreaEdificada    = $rsEspelho->getCampo( 'area_edificada'      );
            $UtilizacaoImovel = $rsEspelho->getCampo( 'utilizacao'          );
            $Tributo          = $rsEspelho->getCampo( 'descricao'           );
            $ValorTributoReal = $rsEspelho->getCampo( 'valor_venal_total'   );
            $Observacao       = $rsEspelho->getCampo( 'observacao'          );
            $NomBairro        = $rsEspelho->getCampo( 'nom_bairro'          );
            $CodDivida        = $rsEspelho->getCampo( 'cod_grupo'           );

            if ( preg_match( '/LIMPEZA.*/i',$rsEspelho->getCampo( 'descricao_credito' ) ) ) {
                $TaxaLimpezaAnual =  $rsEspelho->getCampo( 'valor' );
            } else {
                $flImpostoAnualReal = $rsEspelho->getCampo( 'valor' );
                $ImpostoAnualReal =  $flImpostoAnualReal;
            }
    $rsEspelho->proximo();
}
$totalAnual = $ImpostoAnualReal+ $TaxaLimpezaAnual;
$ValorTributoReal = number_format($ValorTributoReal,2,',','.');
$ImpostoAnualReal = number_format($ImpostoAnualReal,2,',','.');
$TaxaLimpezaAnual = number_format($TaxaLimpezaAnual,2,',','.');
$totalAnual       = number_format($totalAnual      ,2,',','.');

/********************************************************************************************/
/**
* Recuperar Lista de Parcela
*/

$obRARRCarne = new RARRCarne;
$obRARRCarne->stTipo = "imobiliaria";
$obRARRCarne->setExercicio( $_REQUEST["exercicio"]);
//$obRARRCarne->setGrupo  ( 101 );

$obRARRCarne->setInscricaoImobiliariaInicial($_REQUEST['im']);

$obRARRCarne->listarReemitirCarne( $rsCarne );

$rsCarneEmitir = new RecordSet;
$stAux = "";
$cont = 0;
while ( !$rsCarne->eof() ) {
    if ( $stAux == $rsCarne->getCampo( 'cod_parcela' ) ) {
        $rsCarne->proximo();
    } else {
        $arElementos[$cont]['cod_calculo']         = $rsCarne->getCampo('cod_calculo');
        $arElementos[$cont]['cod_lancamento']      = $rsCarne->getCampo('cod_lancamento');
        $arElementos[$cont]['numeracao']           = $rsCarne->getCampo('numeracao');
        $arElementos[$cont]['situacao']            = $rsCarne->getCampo('situacao');
        $arElementos[$cont]['valor']               = $rsCarne->getCampo('valor');
        $arElementos[$cont]['vencimento']          = $rsCarne->getCampo('vencimento');
        $arElementos[$cont]['info_parcela']        = $rsCarne->getCampo('info_parcela');
        $arElementos[$cont]['cod_parcela']         = $rsCarne->getCampo('cod_parcela');
        $arElementos[$cont]['codigo_composto']     = $rsCarne->getCampo('codigo_composto');
        $arElementos[$cont]['cod_localizacao']     = $rsCarne->getCampo('cod_localizacao');
        $arElementos[$cont]['cod_atividade']       = $rsCarne->getCampo('cod_atividade');
        $arElementos[$cont]['cod_convenio']        = $rsCarne->getCampo('cod_convenio');
        $arElementos[$cont]['convenio_atual']      = $rsCarne->getCampo('convenio_atual');
        $arElementos[$cont]['carteira_atual']      = $rsCarne->getCampo('carteira_atual');
        $arElementos[$cont]['exercicio']           = $rsCarne->getCampo('exercicio');
        $arElementos[$cont]['cod_carteira']        = $rsCarne->getCampo('cod_carteira');
        $arElementos[$cont]['numcgm']              = $rsCarne->getCampo('numcgm');
        $arElementos[$cont]['nom_cgm']             = $rsCarne->getCampo('nom_cgm');
        $arElementos[$cont]['nr_parcela']          = $rsCarne->getCampo('nr_parcela');
        $stAux = $rsCarne->getCampo( 'cod_parcela' );
        $rsCarne->proximo();
        $cont++;
    }
    $rsCarneEmitir->preenche($arElementos);
}
$rsCarneEmitir->setPrimeiroElemento();

$arTemp     = $rsCarneEmitir->arElementos;
$arNormais  = array();

$cont = 0;
while ( !$rsCarneEmitir->eof() ) {
    if ( $rsCarneEmitir->getCampo("situacao") == "Vencida" ) {
        if ( $rsCarneEmitir->getCampo('info_parcela') != 'Única') {
            $arNormais[] = $arTemp[$cont];
        }
    } elseif ( $rsCarneEmitir->getCampo("situacao") == "A Vencer" ) {
        $arNormais[]  = $arTemp[$cont];
    }
    $cont++;
    $rsCarneEmitir->proximo();
}
// recordset de nao vencidas
$rsCarneEmitir->preenche($arNormais);
$rsCarneEmitir->setPrimeiroElemento();

$rsCarneEmitir->addFormatacao("valor","NUMERIC_BR");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Emissão de Carnê de IPTU</title>

    <link rel="stylesheet" href="../../../../../../web/css/tela.css" type="text/css" media="screen" />

    <script type="text/javascript" src="../../../../../../web/js/target.js"></script>
</head>
<body>
<div id="page">

<h1>IPTU<span>2a Via do Carnê</span></h1>
<div id="conteudo">
<!-- ESPELHO DO CALCULO-->
    <table class="espelho">
        <thead>
         <tr>
            <th><img src="LogoPetropolis.png" title="Logotipo Petrópolis" /></th>
            <th colspan="6">PREFEITURA MUNICIPAL DE PETRÓPOLIS <br /> SECRETARIA MUNICIPAL DA FAZENDA </th>
         </tr>
        </thead>

        <tbody>
            <tr>
                <td colspan="2" rowspan="3" width:="280">
                    <span class="legenda">CONTRIBUINTE</span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$NomCgm?></span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$Rua?> <?=$Numero?></span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$NomBairro?> &nbsp; <?=$Cep?></span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$Cidade?> <?=$Uf?></span> <br />
                </td>
                <td colspan="5">
                    <span class="legenda">CADASTRO SEQUENCIAL</span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$Inscricao?></span>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <span class="legenda">CTM/DCI</span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$CtmDci?></span> <br />
                </td>
            </tr>
            <tr>
                <td>
                    <span class="legenda">LOGRADOURO</span> <br />
                    <span class="campo"><?=$CodLogradouro?></span>
                </td>
                <td>
                    <span class="legenda">DIST</span> <br />
                    <span class="campo"><?=$Distrito?></span>
                </td>
                <td>
                    <span class="legenda">CT</span> <br />
                    <span class="campo"><?=$Ct?></span>
                </td>
                <td>
                    <span class="legenda">CA</span> <br />
                    <span class="campo"><?=$Ca?></span>
                </td>
                <td>
                    <span class="legenda">PROCESSAMENTO</span> <br />
                    <span class="campo"><?=$Processamento?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2" rowspan="2">
                    <span class="legenda">LOCALIZAÇÃO DO IMÓVEL</span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$Rua?> <?=$Numero?></span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$NomBairro?></span>
                </td>
                <td colspan="3">
                    <span class="legenda">AREA DO TERRENO</span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$AreaTerreno?></span>
                </td>
                <td colspan="2">
                    <span class="legenda">AREA EDIFICADA</span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$AreaEdificada?></span>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <span class="legenda">UTILIZAÇÃO</span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$UtilizacaoImovel?></span>
                </td>
                <td colspan="2">
                    <span class="legenda">TRIBUTO</span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$Tributo?></span>
                </td>
            </tr>

            <tr>
                <td>&nbsp;</td>
                <td>
                    <span class="legenda">EXERCÍCIO</span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$Exercicio?></span>
                </td>
                <td colspan="3" rowspan="1">
                    <span class="legenda">VALOR TRIBUTÁVEL </span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$ValorTributoReal?></span>
                </td>
                <td colspan="2">
                    <span class="legenda">IMPOSTO ANUAL</span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$ImpostoAnualReal?></span>
                </td>
            </tr>

            <tr>
                <td colspan="2" rowspan="2">
                     <span class="legenda">OBSERVAÇÕES</span> <br />
                     <span class="campo">&nbsp;&nbsp;&nbsp;<?=$Observacao?></span>
                </td>
                <td colspan="3">
                    <span class="legenda">TX. COLETA LIXO ANUAL</span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$TaxaLimpezaAnual?></span>
                </td>
                <td colspan="2">
                    <span class="legenda">TOTAL ANUAL</span> <br />
                    <span class="campo">&nbsp;&nbsp;&nbsp;<?=$totalAnual?></span>
                </td>
            </tr>
            <tr>
                <td colspan="3"><span class="legenda">REFERENCIA</span></td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
<!-- FIM ESPELHO -->

<!--  <h2>Lista de Parcelas Dísponiveis</h2> -->
    <table>
        <thead>
            <tr>
                <th colspan="6">Lista de Parcelas Disponiveis</th>
            </tr>
            <tr>
                <td>Parcela</td>
                <td>Contribuinte</td>
                <td>Vencimento</td>
                <td>Valor</td>
                <td>Situação</td>
                <td>Impressão</td>
            </tr>

        </thead>

        <tfoot>
            <tr>
                <th colspan="6"> Total de Parcelas: <?=count($rsCarneEmitir->arElementos);?></th>
            </tr>
        </tfoot>

        <tbody>
        <?php

        // listar parcelas
            while ( !$rsCarneEmitir->eof()) {
                echo "\t\t\t<tr>\n";
                echo "\t\t\t\t<td style='text-align:right;'>".$rsCarneEmitir->getCampo('info_parcela')."</td>\n";
                echo "\t\t\t\t<td>".$rsCarneEmitir->getCampo('nom_cgm')."</td>\n";
                echo "\t\t\t\t<td>".$rsCarneEmitir->getCampo('vencimento')."</td>\n";
                echo "\t\t\t\t<td>".$rsCarneEmitir->getCampo('valor')."</td>\n";
                echo "\t\t\t\t<td>".$rsCarneEmitir->getCampo('situacao')."</td>\n";
                $stLink  = "PRGeraCarne.php?";
                $stLink .= "cod_lancamento=".$rsCarneEmitir->getCampo ('cod_lancamento');
                $stLink .= "&cod_parcela=".$rsCarneEmitir->getCampo ('cod_parcela');
                $stLink .= "&cod_convenio=".$rsCarneEmitir->getCampo ('cod_convenio');
                $stLink .= "&cod_carteira=".$rsCarneEmitir->getCampo ('cod_carteira');
                $stLink .= "&exercicio=".$rsCarneEmitir->getCampo ('exercicio');
                $stLink .= "&convenio_atual=".$rsCarneEmitir->getCampo ('convenio_atual');
                $stLink .= "&carteira_atual=".$rsCarneEmitir->getCampo ('carteira_atual');
                $stLink .= "&numeracao=".$rsCarneEmitir->getCampo ('numeracao');
                $stLink .= "&vencimento=".$rsCarneEmitir->getCampo ('vencimento');
                $stLink .= "&valor=".$rsCarneEmitir->getCampo ('valor');
                $stLink .= "&info_parcela=".$rsCarneEmitir->getCampo ('info_parcela');
                $stLink .= "&numcgm=".$rsCarneEmitir->getCampo ('numcgm');
                echo "\t\t\t\t<td><a href=\"".$stLink."\" title=\"Imprimir 2a Via\" rel=\"externo\">Imprimir</a></td>\n";
                echo "\t\t\t<tr>\n";
                $rsCarneEmitir->proximo();
            }

        ?>
        </tbody>
    </table>

</div>

<div>
</body>
</html>
