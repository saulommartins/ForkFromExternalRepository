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
/**
    * Página de Frame Oculto para Consulta de Arrecadacao
    * Data de Criação   : 26/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: OCConsultaArrecadacao.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.01.18
*/

/*
$Log$
Revision 1.10  2006/09/15 11:04:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                      );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"                );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php"             );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php"       );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

switch ($_REQUEST["stCtrl"]) {
    case "procuraImovel":
        $obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote) );
        $stJs = "";
        $stNull = "&nbsp;";
        if ($_REQUEST["inInscricaoImobiliaria"]) {
            $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["inInscricaoImobiliaria"] );
            $obRCIMUnidadeAutonoma->roRCIMImovel->listarImoveisConsulta( $rsImoveis );
            if ( $rsImoveis->eof() ) {
                //nao encontrada
                $stJs .= 'f.inInscricaoImobiliaria.value = "";';
                $stJs .= 'f.inInscricaoImobiliaria.focus();';
                $stJs .= 'd.getElementById("stInscricaoInscricaoImobiliaria").innerHTML = "'.$stNull.'";';
                $stJs .= "alertaAviso('@Código de inscrição imobiliária inválido. (".$_REQUEST['inInscricaoImobiliaria'].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("stInscricaoInscricaoImobiliaria").innerHTML = "'.$rsImoveis->getCampo("endereco").'";';
            }
        } else {
            $stJs .= 'd.getElementById("stInscricaoInscricaoImobiliaria").innerHTML = "'.$stNull.'";';
        }

        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "buscaContribuinte":
        $stNull = "&nbsp;";
        $obRCGM = new RCGM;
        if ($_REQUEST[ 'inCodContribuinte' ] != "") {
            $obRCGM->setNumCGM( $_REQUEST['inCodContribuinte'] );
            $obRCGM->consultar( $rsCGM );
            if ( $rsCGM->getNumLinhas() <= 0) {
                $stJs .= 'f.inCodContribuinte.value = "";';
                $stJs .= 'f.inCodContribuinte.focus();';
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$stNull.'";';
                $stJs .= "alertaAviso('@CGM inválido. (".$_REQUEST['inCodContribuinte'].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
            }
        } else {
            $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$stNull.'";';
        }

        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaIE":
        if ($_REQUEST["inInscricaoEconomica"]) {
            include_once(CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php");
            $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
            $obRCEMInscricaoEconomica->setInscricaoEconomica($_REQUEST["inInscricaoEconomica"]);
            $obRCEMInscricaoEconomica->consultarNomeInscricaoEconomica($rsInscricao);
            if ( !$rsInscricao->eof()) {
                $js .= "f.inInscricaoEconomica.value = '".$_REQUEST["inInscricaoEconomica"]."';\n";
                $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '".$rsInscricao->getCampo("nom_cgm")."' ;\n";
            } else {
                $stMsg = "Inscrição Econômica ".$_REQUEST["inInscricaoEconomica"]."  não encontrada!";
                $js .= "f.inInscricaoEconomica.value = '".null."';\n";
                $js .= 'f.inInscricaoEconomica.focus();';
                $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;';\n";
                $js .= "alertaAviso('@".$stMsg."','form','erro','".Sessao::getId()."');";
            }
        } else {
            $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;';\n";
        }
        SistemaLegado::executaFrameOculto($js);
    break;
    case "detalheParcela":

if ($_GET['stDescricao']) {

    $arTMP          = explode ( '§', $_REQUEST['stDescricao'] );
    $inNumeracao    = $arTMP[0];
    $dtVencimentoPR = $arTMP[1];
    $dtPagamento    = $arTMP[2];
    $inOcorrencia   = $arTMP[3];

} else {

    $inNumeracao    = $_REQUEST['numeracao'];
    $dtVencimentoPR = $_REQUEST['vencimento'];
    $dtPagamento    = $_REQUEST['pagamento'];
    $inOcorrencia   = $_REQUEST['ocorrencia_pagamento'];

}

$inCodLancamento = $_REQUEST['cod_lancamento'];
$inCodParcela    = $_REQUEST['cod_parcela'];
$inExercicio     = $_REQUEST['inExercicio'];
$dtDataBaseBR    = $_REQUEST['database_br'];
$stInfoParcela   = $_REQUEST["info_parcela"];

$stIdCarregamento = Sessao::read('stIdCarregamento');
if (!$stIdCarregamento) {
    $stIdCarregamento = $_REQUEST['linha_table_tree']."_sub_cell_2";
    Sessao::write('stIdCarregamento', $stIdCarregamento);
}
//$dtPagamento = "2008-05-16";
if ($dtPagamento) {
    $arData = explode("-",$dtPagamento);
    $dtDataUS = $dtPagamento;
    $dtDataBase = $arData[2]."/".$arData[1]."/".$arData[0];

}

if ($dtDataBaseBR && !$dtPagamento) {
    $arData = explode("/",$dtDataBaseBR);
    $dtDataUS = $arData[2]."-".$arData[1]."-".$arData[0];
    $dtDataBase = $dtDataBaseBR;
}

include_once ( CAM_GT_ARR_MAPEAMENTO."Ffn_situacao_carne.class.php"               );
$obSituacao = new Ffn_situacao_carne;
$stParam =  "'$inNumeracao','f'";
$obSituacao->executaFuncao($rsTmp,$stParam);
$stSituacao = $rsTmp->getCampo('valor');

$obRARRCarne = new RARRCarne;
$obRARRCarne->setNumeracao                                      ( $inNumeracao      );
$obRARRCarne->setOcorrenciaPagamento                            ( $inOcorrencia     );
$obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento ( $inCodLancamento  );
$obRARRCarne->obRARRParcela->setCodParcela                      ( $inCodParcela     );
$obRARRCarne->setDataPagamento                                  ( $dtPagamento      );
$obRARRCarne->listarConsulta    ( $rsDetalheParcela, '', $dtDataUS, $dtVencimentoPR );

//$dtDataUS = $rsDetalheParcela->getCampo("parcela_vencimento_us"); //comentado 03_06

$obSpnQuebra = new Span;
$obSpnQuebra->setId ("spnQuebra");

$obTxtDataBase = new Data;
$obTxtDataBase->setName     ( "dtDataBase"  );
$obTxtDataBase->setId       ( "dtDataBase"  );
$obTxtDataBase->setValue    ( $dtDataBase   );
$obTxtDataBase->setRotulo   ( "Data Base"   );
$obTxtDataBase->setStyle    ( "vertical-align:top;");
$obTxtDataBase->setTitle    ( "Data Base para os valores informados, altere para atualização dos valores!");

$obButtonAtualizarData = new Img;
$obButtonAtualizarData->setId    ( "imgAtualizar" );
$obButtonAtualizarData->setTitle ( "Atualizar" );
$obButtonAtualizarData->setNull  ( true );
$obButtonAtualizarData->setCaminho (CAM_FW_TEMAS."/imagens/btnRefresh.png");

//$stIdCarregamento = $_REQUEST['stIdCarregamento'];

if ( $rsDetalheParcela->getCampo ('pagamento_data') ) {
    $obButtonAtualizarData->obEvento->setOnClick("visualizarDetalhesAtualizaReemitida( '$inCodLancamento', '$inNumeracao', '$inExercicio', '$inCodParcela','', '$dtDataBase', '$dtVencimentoPR', '$inOcorrencia', '$stIdCarregamento', '$stInfoParcela');");
    $obButtonAtualizarData->obEvento->setOnDblClick("visualizarDetalhesAtualizaReemitida( '$inCodLancamento', '$inNumeracao', '$inExercicio', '$inCodParcela','', '$dtDataBase', '$dtVencimentoPR', '$inOcorrencia', '$stIdCarregamento', '$stInfoParcela');");
} else {
    $obButtonAtualizarData->obEvento->setOnClick("visualizarDetalhesAtualizaReemitida( '$inCodLancamento', '$inNumeracao', '$inExercicio', '$inCodParcela','', document.getElementById('dtDataBase').value, '$dtVencimentoPR', '$inOcorrencia','$stIdCarregamento', '$stInfoParcela');");
    $obButtonAtualizarData->obEvento->setOnDblClick("visualizarDetalhesAtualizaReemitida('$inCodLancamento', '$inNumeracao', '$inExercicio', '$inCodParcela','', document.getElementById('dtDataBase').value, '$dtVencimentoPR', '$inOcorrencia','$stIdCarregamento', '$stInfoParcela');");

}

$obLblNumeracao = new Label;
$obLblNumeracao->setName        ( "stNumeracao" );
//$obLblNumeracao->setValue       ( "<span id='ult_num'>".$rsDetalheParcela->getCampo("numeracao")."</span>/".$rsDetalheParcela->getCampo("exercicio"));
$obLblNumeracao->setValue       ( $rsDetalheParcela->getCampo("numeracao")." / ".$rsDetalheParcela->getCampo("exercicio"));
$obLblNumeracao->setRotulo      ( "Ultima Numeração"   );

$obRARRParcela = new RARRParcela ( new RARRLancamento (new RARRCalculo) );
$obRARRParcela->setCodParcela ( $inCodParcela );
$obRARRParcela->listarReemissaoConsulta( $rsNumeracoes );

$obCmbNumeracao = new Select;
$obCmbNumeracao->setName         ( "cmbNumeracao"               );
$obCmbNumeracao->addOption       ( "", "Vencimentos"            );
$obCmbNumeracao->setTitle        ( "Selecione Numerações Anteriores");
$obCmbNumeracao->setCampoId      ( "[numeracao]§[vencimento]§[data_pagamento]§[ocorrencia_pagamento]" );
$obCmbNumeracao->setCampoDesc    ( "[numeracao]" );
$obCmbNumeracao->preencheCombo   ( $rsNumeracoes                );
$obCmbNumeracao->setNull         ( true                         );
$obCmbNumeracao->setStyle        ( "width: 220px"               );
$obCmbNumeracao->obEvento->setOnChange ("visualizarDetalhesAtualizaReemitidaCombo( 'parcela', '$inCodLancamento', this.value, '$inExercicio', '$inCodParcela','', document.getElementById('dtDataBase').value, '$stIdCarregamento', '$stInfoParcela' );");

$obLblNumeracaoMigrada = new Label;
$obLblNumeracaoMigrada->setName  ( "stNumeracaoMigrada" );
$obLblNumeracaoMigrada->setValue ( $rsDetalheParcela->getCampo("migracao_numeracao")."/".$rsDetalheParcela->getCampo("migracao_prefixo"));
$obLblNumeracaoMigrada->setRotulo( "Numeração Migrada"   );

$obLblParcela = new Label;
$obLblParcela->setName      ( "stParcela" );
$obLblParcela->setValue     ( $rsDetalheParcela->getCampo("info_parcela") );
$obLblParcela->setRotulo    ( "Parcela"   );

$obLblValor = new Label;
$obLblValor->setName        ( "stValor" );
$obLblValor->setValue       ( "R$ ". number_format( $rsDetalheParcela->getCampo("parcela_valor"), 2, ',', '.' ) );
$obLblValor->setRotulo      ( "Valor"   );

#if ( $rsDetalheParcela->getCampo("vencimento_original_br") )
#	$dtVencimento = $rsDetalheParcela->getCampo("vencimento_original_br");
#else

include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php"               );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php"             );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcelaReemissao.class.php"    );

$stFiltro = " WHERE cod_parcela = ".$rsDetalheParcela->getCampo("cod_parcela");
$stFiltroOrderBy = $stFiltro." ORDER BY timestamp";

$obTARRCarne = new TARRCarne;
$obTARRCarne->recuperaTodos($rsCarne,$stFiltroOrderBy);

$obTARRParcela = new TARRParcela;
$obTARRParcela->recuperaTodos($rsParcela,$stFiltro);

$obTARRParcelaReemissao = new TARRParcelaReemissao;
$obTARRParcelaReemissao->recuperaTodos($rsParcelaReemissao,$stFiltroOrderBy);

$dtVencimentoParcela = "";
while (!$rsCarne->eof()) {
    if ($rsDetalheParcela->getCampo("numeracao") == $rsCarne->getCampo("numeracao")) {
        //verifica se eh a ultima linha
       if ($rsCarne->getNumLinhas() == $rsCarne->getCorrente()) {
           $dtVencimentoParcela = $rsParcela->getCampo("vencimento");
       } else {
           $dtVencimentoParcela = $rsParcelaReemissao->getCampo("vencimento");
       }
    }
    $rsCarne->proximo();
}

$dtVencimento = $rsDetalheParcela->getCampo("parcela_vencimento_original");

$obLblVencimento = new Label;
$obLblVencimento->setName   ( "stVencimento" );
$obLblVencimento->setValue  ( $dtVencimentoParcela );
$obLblVencimento->setRotulo ( "Vencimento"   );

if ( $rsDetalheParcela->getCampo("numeracao_consolidacao") )
    $dtVencimento = $rsDetalheParcela->getCampo("vencimento");

$obLblVencimentoConsolidacao = new Label;
$obLblVencimentoConsolidacao->setName        ( "stVencimentoConsolidacao" );
$obLblVencimentoConsolidacao->setValue       ( $dtVencimento );
$obLblVencimentoConsolidacao->setRotulo      ( "Vencimento Consolidação"   );

if ( $rsDetalheParcela->getCampo("valor_consolidacao") )
    $valor_consolidacao = number_format ($rsDetalheParcela->getCampo('valor_consolidacao'), 2, ',', '.');

$obLblValorConsolidacao = new Label;
$obLblValorConsolidacao->setName        ( "stValorConsolidacao" 	);
$obLblValorConsolidacao->setValue       ( "R$ ".$valor_consolidacao );
$obLblValorConsolidacao->setRotulo      ( "Valor Consolidação"   	);

$obLblSituacao = new Label;
$obLblSituacao->setName     ( "stSituacao" );
$obLblSituacao->setValue    ( $rsDetalheParcela->getCampo("situacao") );
$obLblSituacao->setRotulo   ( "Situação"   );
// exclusivos para situacao = devolucao
$obLblDataDevolucao = new Label;
$obLblDataDevolucao->setName        ( "stDataDevolucao" );
$obLblDataDevolucao->setValue       ( $rsDetalheParcela->getCampo("devolucao_data") );
$obLblDataDevolucao->setRotulo      ( "Data de Devolução"   );

$obLblMotivo = new Label;
$obLblMotivo->setName        ( "stMotivo" );
$obLblMotivo->setValue       ( $rsDetalheParcela->getCampo("devolucao_descricao") );
$obLblMotivo->setRotulo      ( "Motivo"   );

$obLblDataPagamento = new Label;
$obLblDataPagamento->setName        ( "stDataPagamento" );
$obLblDataPagamento->setValue       ( $rsDetalheParcela->getCampo("pagamento_data") );
$obLblDataPagamento->setRotulo      ( "Data de Pagamento"   );

$obLblValorPagar = new Label;
$obLblValorPagar->setValue       ( "R$ ". number_format( $rsDetalheParcela->getCampo("parcela_valor"), 2, ',','.') );
$obLblValorPagar->setRotulo      ( "Valor da Parcela"   );

$stPercentual = '';
#if ( $rsDetalheParcela->getCampo("valor_desconto_percentual") > 0 ) {
if ( $rsDetalheParcela->getCampo("parcela_desconto_percentual") > 0 ) {
    #$stPercentual = " (".$rsDetalheParcela->getCampo("valor_desconto_percentual"). "%)";
    $stPercentual = " (".$rsDetalheParcela->getCampo("parcela_desconto_percentual"). "%)";

}

$obLblDescontosPagar = new Label;
$obLblDescontosPagar->setName  ( "stDescontos" );
#$obLblDescontosPagar->setValue  ( "R$ ".str_replace(".",",",$rsDetalheParcela->getCampo("valor_desconto_pagar")). $stPercentual );
$obLblDescontosPagar->setValue  ( "R$ ".str_replace(".",",",$rsDetalheParcela->getCampo("parcela_valor_desconto")). $stPercentual );
$obLblDescontosPagar->setRotulo ( "Descontos"   );

$obLblNumeracaoConsolidacao = new Label;
$obLblNumeracaoConsolidacao->setName ('stNumeracaoConsolidacao');
$obLblNumeracaoConsolidacao->setValue ( $rsDetalheParcela->getCampo ('numeracao_consolidacao') );
$obLblNumeracaoConsolidacao->setRotulo ('Numeração Consolidação');

// labels exclusivas de pagamento
$obLblLote = new Label;
$obLblLote->setName     ( "stLote" );
$obLblLote->setValue    ( $rsDetalheParcela->getCampo("pagamento_cod_lote") );
$obLblLote->setRotulo   ( "Lote" );

$obLblDtLote = new Label;
$obLblDtLote->setName     ( "stDtLote" );
#$obLblDtLote->setValue    ( $rsDetalheParcela->getCampo("data_lote") );
$obLblDtLote->setValue    ( $rsDetalheParcela->getCampo("pagamento_data_baixa"));
$obLblDtLote->setRotulo   ( "Data Processamento" );
$obLblDtLote->setTitle    ( "Data de Processamento do Lote" ) ;

$obLblProcesso = new Label;
$obLblProcesso->setName     ( "stProcesso" );
$obLblProcesso->setValue    ( $rsDetalheParcela->getCampo("processo") );
$obLblProcesso->setRotulo   ( "Processo" );

$obLblObservacao = new Label;
$obLblObservacao->setName     ( "stObservacao" );
$obLblObservacao->setValue    ( $rsDetalheParcela->getCampo("observacao") );
$obLblObservacao->setRotulo   ( "Observacao" );

$obLblBanco = new Label;
$obLblBanco->setName     ( "stBanco" );
#$obLblBanco->setValue    ( $rsDetalheParcela->getCampo("num_banco")." - ".$rsDetalheParcela->getCampo("nom_banco") );
$obLblBanco->setValue    ( $rsDetalheParcela->getCampo("pagamento_num_banco")." - ".$rsDetalheParcela->getCampo("pagamento_nom_banco") );

$obLblBanco->setRotulo   ( "Banco" );

$obLblAgencia = new Label;
$obLblAgencia->setName     ( "stAgencia" );
#$obLblAgencia->setValue    ( $rsDetalheParcela->getCampo("num_agencia")." - ".$rsDetalheParcela->getCampo("nom_agencia") );
$obLblAgencia->setValue    ( $rsDetalheParcela->getCampo("pagamento_num_agencia")." - ".$rsDetalheParcela->getCampo("pagamento_nom_agencia") );
$obLblAgencia->setRotulo   ( "Agência" );

$obLblUsuario = new Label;
$obLblUsuario->setName     ( "stUsuario" );
#$obLblUsuario->setValue    ( $rsDetalheParcela->getCampo("cgm_usuario")." - ".$rsDetalheParcela->getCampo("nom_usuario") );
$obLblUsuario->setValue    ( $rsDetalheParcela->getCampo("pagamento_numcgm")." - ".$rsDetalheParcela->getCampo("pagamento_nomcgm") );
$obLblUsuario->setRotulo   ( "Usuário" );
//guarda tipo baixa
$stTipoBaixa = $rsDetalheParcela->getCampo("cod_lote");

/** ********************* PAGAMENTOS DUPLICADOS *****************/

// antes de form , apresentar lista de pagamentos duplicados
$obRARRCarne->listarPagamentosConsulta( $rsPagDuplicados );
$rsPagDuplicados->addFormatacao("valor","NUMERIC_BR");

$inContPagamentos =  $rsPagDuplicados->getNumLinhas();
$arParcelasDuplicadas = array();
if ($inContPagamentos > 1) {
    //retira da lista de parcelas duplicadas, a parcela que está sendo exibida, indexada pelo "ocorrencia_pagamento"
    $arrParcelasDuplicadasTMP = $rsPagDuplicados->arElementos;

    $cont = $contParcelasOK = 0;
    while ($cont < $inContPagamentos) {
        if ( $arrParcelasDuplicadasTMP[$cont]['ocorrencia_pagamento'] != $rsDetalheParcela->getCampo('ocorrencia_pagamento') ) {
            $arParcelasDuplicadas[$contParcelasOK] = $arrParcelasDuplicadasTMP[$cont];
            $arParcelasDuplicadas[$contParcelasOK]['container'] = $stIdCarregamento;
            $contParcelasOK++;
        }
        $cont++;
    }

    $rsPagDuplicados = new RecordSet;
    $rsPagDuplicados->preenche ( $arParcelasDuplicadas );
} else {
    $rsPagDuplicados = new RecordSet;
}

if ( $rsPagDuplicados->getNumLinhas() > 0 ) {

    ########################### TABELA DOM
    $table = new Table();
    $table->setRecordset( $rsPagDuplicados );
    $table->setSummary('Pagamentos Duplicados');

    // lista zebrada
    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Numeração' , 20  );
    $table->Head->addCabecalho( 'Numeração Migrada' , 10  );
    $table->Head->addCabecalho( 'Ocorrência' , 10  );
    $table->Head->addCabecalho( 'Data Pagamento' , 10  );
    $table->Head->addCabecalho( 'Valor (R$)' , 10  );

    $table->Body->addCampo( '[numeracao]/[exercicio]' );
    $table->Body->addCampo( 'num_migrada'         , "D" );
    $table->Body->addCampo( 'ocorrencia_pagamento'      , "D" );
    $table->Body->addCampo( 'data_pagamento'   , "D" );
    $table->Body->addCampo( 'valor'   , "D" );

    #$table->Foot->addSoma ( 'valor_total', "D" );

    $table->Body->addAcao( 'consultar' ,  "visualizarDetalhesAtualizaReemitida ( %04d, %s, %04d, %04d, %s ,%s ,%s, %04d, %s, %s)" , array( 'cod_lancamento', 'numeracao', 'exercicio', 'cod_parcela', 'data_pagamento_us', "dtdatabase_br", 'vencimento', 'ocorrencia_pagamento', "container", 'info_parcela' ) );

    $table->montaHTML();

    echo $table->getHtml();
    #=======================================================================

}

//tDataUS = "2008-04-30";//Andre
//echo "data us = ".$dtDataUS."<br>";
//echo "venc = ".$dtVencimentoPR."<br>";
$obRARRCarne->listarDetalheCreditosConsulta( $rsListaDetalheCreditos, '', $dtDataUS , $dtVencimentoPR);
$arTMP = $rsListaDetalheCreditos->getElementos();
if ( count($arTMP) > 1 ) {
    $flValOrigem = 0.00;
    $flValCorrecao = 0.00;
    $flValJuros = 0.00;
    $flValMulta = 0.00;
    $flValDescontoPer = 0.00;
    $flValDescontoAplicar = 0.00;
    $flValDesconto = 0.00;
    for ( $inX=0; $inX<count($arTMP); $inX++ ) {
        $arTMP[$inX]["credito_descontos"] = 0.00;
        if ($arTMP[$inX]["usar_desconto"] == 't') {
            $flValDescontoAplicar += $arTMP[$inX]["valor_credito"];
        }
    }

    for ( $inX=0; $inX<count($arTMP)-1; $inX++ ) {
        $flValOrigem += $arTMP[$inX]["valor_credito"];
        $flValCorrecao += $arTMP[$inX]["credito_correcao_pagar"];
        $flValJuros += $arTMP[$inX]["credito_juros_pagar"];
        $flValMulta += $arTMP[$inX]["credito_multa_pagar"];
        if ($arTMP[$inX]["usar_desconto"] == 't') {
            if ( $flValDescontoAplicar )
                $flValDescontoPer = ($arTMP[$inX]["valor_credito"] * 100) / $flValDescontoAplicar;
            else
                $flValDescontoPer = 0.00;

            $arTMP[$inX]["credito_descontos"] = ( $flValDescontoPer * $rsDetalheParcela->getCampo("parcela_valor_desconto") ) / 100;
            $flValDesconto += $arTMP[$inX]["credito_descontos"];
        }

        $arTMP[$inX]["valor_total"] = ( $arTMP[$inX]["credito_multa_pagar"] + $arTMP[$inX]["credito_juros_pagar"] + $arTMP[$inX]["credito_correcao_pagar"] + $arTMP[$inX]["valor_credito"]) - $arTMP[$inX]["credito_descontos"];

        if ( $rsDetalheParcela->getCampo("pagamento_data") ) {
            if ($arTMP[$inX]["pagamento_diferenca"] > 0.00) {
                $arTMP[$inX]["valor_total"] = ( $arTMP[$inX]["credito_multa_pago"] + $arTMP[$inX]["credito_juros_pago"] + $arTMP[$inX]["credito_correcao_pago"] + $arTMP[$inX]["valor_credito"]) - $arTMP[$inX]["credito_descontos"];
                $arTMP[$inX]["diferenca"] = $arTMP[$inX]["pagamento_diferenca"];
            }else
                $arTMP[$inX]["diferenca"] = ( $arTMP[$inX]["pagamento_valor"] + $arTMP[$inX]["credito_correcao_pago"] + $arTMP[$inX]["credito_juros_pago"] + $arTMP[$inX]["credito_multa_pago"] ) - $arTMP[$inX]["valor_total"];
        }else
            $arTMP[$inX]["diferenca"] = 0.00;

        $arTMP[$inX]["valor_total"] += $arTMP[$inX]["diferenca"];
    }

    $inX = count($arTMP)-1;
    $arTMP[$inX]["valor_credito"] = $rsDetalheParcela->getCampo("parcela_valor") - $flValOrigem;
    $arTMP[$inX]["credito_correcao_pagar"] = $rsDetalheParcela->getCampo("parcela_correcao_pagar") - $flValCorrecao;
    $arTMP[$inX]["credito_juros_pagar"] = $rsDetalheParcela->getCampo("parcela_juros_pagar") - $flValJuros;
    $arTMP[$inX]["credito_multa_pagar"] = $rsDetalheParcela->getCampo("parcela_multa_pagar") - $flValMulta;
    $arTMP[$inX]["credito_descontos"] = $rsDetalheParcela->getCampo("parcela_valor_desconto") - $flValDesconto;
    $arTMP[$inX]["valor_total"] = ($arTMP[$inX]["credito_multa_pagar"] + $arTMP[$inX]["credito_juros_pagar"] + $arTMP[$inX]["credito_correcao_pagar"] + $arTMP[$inX]["valor_credito"]) - $arTMP[$inX]["credito_descontos"];

    if ( $rsDetalheParcela->getCampo("pagamento_data") ) {
        if ($arTMP[$inX]["pagamento_diferenca"] > 0.00) {
            $arTMP[$inX]["valor_total"] = ( $arTMP[$inX]["credito_multa_pago"] + $arTMP[$inX]["credito_juros_pago"] + $arTMP[$inX]["credito_correcao_pago"] + $arTMP[$inX]["valor_credito"]) - $arTMP[$inX]["credito_descontos"];
            $arTMP[$inX]["diferenca"] = $arTMP[$inX]["pagamento_diferenca"];
        }else
            $arTMP[$inX]["diferenca"] = ( $arTMP[$inX]["pagamento_valor"] + $arTMP[$inX]["credito_correcao_pago"] + $arTMP[$inX]["credito_juros_pago"] + $arTMP[$inX]["credito_multa_pago"] ) - $arTMP[$inX]["valor_total"];
    }else
        $arTMP[$inX]["diferenca"] = 0.00;

    $arTMP[$inX]["valor_total"] += $arTMP[$inX]["diferenca"];
    $rsListaDetalheCreditos->preenche( $arTMP );
    $rsListaDetalheCreditos->setPrimeiroElemento();

}

$rsDetalheParcela->addFormatacao ("parcela_juros_pagar","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_multa_pagar","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_juros","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_multa","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_valor","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_valor_desconto","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("valor_total","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_correcao_pago","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("parcela_correcao_pagar","NUMERIC_BR");
$rsDetalheParcela->addFormatacao ("pagamento_diferenca","NUMERIC_BR");

$obLblValorDiferenca = new Label;
$obLblValorDiferenca->setName        ( "stDiff" );
$obLblValorDiferenca->setValue       ( "R$ ". $rsDetalheParcela->getCampo('pagamento_diferenca') );
$obLblValorDiferenca->setRotulo      ( "Diferença de Pagamento"   );

$obLblJurosPagar = new Label;
$obLblJurosPagar->setValue       ("R$ ". $rsDetalheParcela->getCampo("parcela_juros_pagar") );
$obLblJurosPagar->setRotulo      ( "Juros");

$obLblMultaPagar = new Label;
$obLblMultaPagar->setValue       ( "R$ ". $rsDetalheParcela->getCampo("parcela_multa_pagar") );
$obLblMultaPagar->setRotulo      ( "Multa"   );

$obLblCorrecaoPagar = new Label;
$obLblCorrecaoPagar->setName        ( "stCorrecaoPagar" );
$obLblCorrecaoPagar->setValue   ("R$ ".$rsDetalheParcela->getCampo("parcela_correcao_pagar") );
$obLblCorrecaoPagar->setRotulo  ( "Correção");

$obLblTotalPago = new Label;
$obLblTotalPago->setName        ( "stValorTotalPago" );
$obLblTotalPago->setValue       ( "R$ ". $rsDetalheParcela->getCampo("valor_total") );
$obLblTotalPago->setRotulo      ( "Total Pago"   );

$obLblTotalPagar = new Label;
$obLblTotalPagar->setName        ( "stValorTotal" );
$obLblTotalPagar->setValue       ( "R$ ". $rsDetalheParcela->getCampo("valor_total") );
$obLblTotalPagar->setRotulo      ( "Total a Pagar"   );
/****************************************************************************/

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setName    ("frm_detalhes");
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addTitulo        ( "Detalhamento de Valores da parcela ".$_REQUEST['info_parcela'] );
$obFormulario->agrupaComponentes( array($obTxtDataBase,$obButtonAtualizarData) );
$obFormulario->addSpan          ( $obSpnQuebra              );
$obFormulario->agrupaComponentes (array( $obLblNumeracao,$obCmbNumeracao)           );

#if ( $rsDetalheParcela->getCampo("numeracao_migracao") ) {
if ( $rsDetalheParcela->getCampo("migracao_numeracao") ) {
    $obFormulario->addComponente( $obLblNumeracaoMigrada );
}

$obFormulario->addComponente    ( $obLblParcela             );

$obFormulario->addComponente    ( $obLblVencimento          );

#if ( $rsDetalheParcela->getCampo ('numeracao_consolidacao') ) {
if ( $rsDetalheParcela->getCampo ('consolidacao_numeracao') ) {
    $obFormulario->addComponente ( $obLblNumeracaoConsolidacao );
    $obFormulario->addComponente ( $obLblVencimentoConsolidacao );
    $obFormulario->addComponente ( $obLblValorConsolidacao );
}

$obFormulario->addComponente    ( $obLblSituacao            );
if ( $rsDetalheParcela->getCampo("situacao") == "Devolvido" ) {
    $obFormulario->addComponente    ( $obLblDataDevolucao   );
    $obFormulario->addComponente    ( $obLblMotivo          );
#} elseif ( $rsDetalheParcela->getCampo("data_pagamento") ) {
} elseif ( $rsDetalheParcela->getCampo("pagamento_data") ) {
    $obFormulario->addComponente    ( $obLblDataPagamento   );
    $obFormulario->addComponente    ( $obLblLote            );
    $obFormulario->addComponente    ( $obLblDtLote          );
    if ($stTipoBaixa != 'Baixa Manual') {
        $obFormulario->addComponente    ( $obLblBanco           );
        $obFormulario->addComponente    ( $obLblAgencia           );
    }
    if ( $rsDetalheParcela->getCampo("processo") ) {
        $obFormulario->addComponente    ( $obLblProcesso       );
    }
    if ( $rsDetalheParcela->getCampo("observacao") ) {
        $obFormulario->addComponente    ( $obLblObservacao    );
    }
    $obFormulario->addComponente    ( $obLblUsuario         );
    $obFormulario->addComponente    ( $obLblValor               );
    $obFormulario->addComponente    ( $obLblDescontosPagar      );
    $obFormulario->addComponente    ( $obLblJurosPagar       );
    $obFormulario->addComponente    ( $obLblMultaPagar       );
    $obFormulario->addComponente    ( $obLblCorrecaoPagar    );

    if ( $rsDetalheParcela->getCampo('tp_pagamento') == 't' ) {
        $obFormulario->addComponente    ( $obLblValorDiferenca );
        $obFormulario->addComponente    ( $obLblTotalPago        );
    } else {
        $obFormulario->addComponente    ( $obLblTotalPagar       );
    }
} else { // parcela em aberto
    $obFormulario->addComponente    ( $obLblValorPagar       );
    $obFormulario->addComponente    ( $obLblDescontosPagar   );
    $obFormulario->addComponente    ( $obLblJurosPagar       );
    $obFormulario->addComponente    ( $obLblMultaPagar       );
    $obFormulario->addComponente    ( $obLblCorrecaoPagar    );
    $obFormulario->addComponente    ( $obLblTotalPagar       );
}
$obFormulario->show();
//**********************************************************************************************
$rsListaDetalheCreditos->setPrimeiroElemento();

$rsListaDetalheCreditos->addFormatacao( "pagamento_valor"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "pagamento_diferenca"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "calculo_valor"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "credito_juros_pago"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "credito_multa_pago"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "valor_credito"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "credito_descontos"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "valor_credito_juros_pago"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "valor_credito_multa_pago"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "credito_juros_pagar"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "credito_multa_pagar"   , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "credito_correcao_pagar", "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "credito_correcao_pago" , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "diferenca"       , "NUMERIC_BR" );
$rsListaDetalheCreditos->addFormatacao( "valor_total"   , "NUMERIC_BR" );

########################### TABELA DOM

$table = new Table();
$table->setRecordset( $rsListaDetalheCreditos );
$table->setSummary('Detalhamento por Crédio');

// lista zebrada
//$table->setConditional( true , "#efefef" );

$table->Head->addCabecalho( 'Crédito' , 20  );
$table->Head->addCabecalho( 'Valor' , 10  );
$table->Head->addCabecalho( 'Descontos' , 10  );
$table->Head->addCabecalho( 'Juros' , 10  );
$table->Head->addCabecalho( 'Multa' , 10  );
$table->Head->addCabecalho( 'Correção' , 10  );
$table->Head->addCabecalho( 'Valor Diferença' , 10  );
$table->Head->addCabecalho( 'Valor Total (R$)' , 10  );

$table->Body->addCampo( '[credito_codigo_composto] - [credito_nome]' );

//if ( $rsDetalheParcela->getCampo("pagamento_data") ) {
//    $table->Body->addCampo( 'pagamento_valor'         , "D" );
//} else {
    $table->Body->addCampo( 'valor_credito'         , "D" );
//}

$table->Body->addCampo( 'credito_descontos'      , "D" );

if ( $rsDetalheParcela->getCampo("pagamento_data") ) {
    $table->Body->addCampo( 'credito_juros_pago'   , "D" );
    $table->Body->addCampo( 'credito_multa_pago'   , "D" );
    $table->Body->addCampo( 'credito_correcao_pago', "D" );
    //$table->Body->addCampo( 'pagamento_diferenca' , "D" );
} else {
    $table->Body->addCampo( 'credito_juros_pagar'   , "D" );
    $table->Body->addCampo( 'credito_multa_pagar'   , "D" );
    $table->Body->addCampo( 'credito_correcao_pagar', "D" );
// $table->Body->addCampo( 'diferenca' , "D" );
}

$table->Body->addCampo( 'diferenca' , "D" );
$table->Body->addCampo( 'valor_total'   , "D" );

$table->Foot->addSoma ( 'valor_total', "D" );

#$table->Body->addAcao( null ,  null , array( 'nome' ) );

$table->montaHTML();

echo $table->getHtml();
#########################################

//
    break;
}

?>
