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
  * Página de Consulta da Divida Ativa
  * Data de criação : 22/04/2009

  * @author Fernando Cercato

  * @package URBEM
  * @subpackage
  * @ignore

  $Id: $

  *Casos de uso:

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_FIS_MAPEAMENTO.'TFISProcessoFiscal.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarProcesso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$stAcao = $request->get('stAcao');

$obTFISProcessoFiscal = new TFISProcessoFiscal;
$obTFISProcessoFiscal->recuperaListaDocumentosParaConsultaProcessoFiscal( $rsListaDocs, $_REQUEST['inCodProcesso'] );
$obTFISProcessoFiscal->recuperaListaInfracoesParaConsultaProcessoFiscal( $rsListaInfracao, $_REQUEST['inCodProcesso'] );
if ( !$rsListaInfracao->Eof() ) {
    $arTmpDados = $rsListaInfracao->getElementos();
    $inCodInfracao = $arTmpDados[0]["cod_infracao"];
    $stPenalidade = "";
    for ( $inX=0; $inX<count($arTmpDados); $inX++ ) {
        if ($inCodInfracao == $arTmpDados[$inX]["cod_infracao"]) {
            if ( $stPenalidade )
                $stPenalidade .= "<br>";

            $stPenalidade .= $arTmpDados[$inX]["cod_penalidade"]." - ".$arTmpDados[$inX]["nom_penalidade"];
        } else {
            $arTmpDados[$inX]["penalidades"] = $stPenalidade;
            $stPenalidade .= $arTmpDados[$inX]["cod_penalidade"]." - ".$arTmpDados[$inX]["nom_penalidade"];
            $inCodInfracao = $arTmpDados[$inX]["cod_infracao"];
        }
    }

    $arTmpDados[count($arTmpDados)-1]["penalidades"] = $stPenalidade;
    $rsListaInfracao->preenche( $arTmpDados );
}

$obLblProc = new Label;
$obLblProc->setRotulo ( "Processo" );
$obLblProc->setName ( "stProcesso" );
$obLblProc->setValue ( $_REQUEST['inCodProcesso'] );
$obLblProc->setTitle ( "Processo." );

$obLblTipo = new Label;
$obLblTipo->setRotulo ( "Tipo" );
$obLblTipo->setName ( "stTipoProcesso" );
$obLblTipo->setValue ( $_REQUEST['inTipoFiscalizacao']." - ".$_REQUEST["stDescricao"] );
$obLblTipo->setTitle ( "Tipo de processo." );

$obLblSituacaoProc = new Label;
$obLblSituacaoProc->setRotulo ( "Situação" );
$obLblSituacaoProc->setName ( "stSituacaoProc" );
$obLblSituacaoProc->setValue ( $_REQUEST['stStatusProc'] );
$obLblSituacaoProc->setTitle ( "Situação." );

$obLblProcesso = new Label;
$obLblProcesso->setRotulo ( "Protocolo de Processo" );
$obLblProcesso->setName ( "stProcesso" );
$obLblProcesso->setValue ( $_REQUEST['inCodProcProt']." / ".$_REQUEST["inAnoExercicioProt"] );
$obLblProcesso->setTitle ( "Protocolo de processo." );

$obLblFiscal = new Label;
$obLblFiscal->setRotulo ( "Fiscal" );
$obLblFiscal->setName ( "stFiscal" );
$obLblFiscal->setValue ( $_REQUEST['inCodFiscal']." - ".$_REQUEST["stNomFiscal"] );
$obLblFiscal->setTitle ( "Fiscal." );

$obListaInf = new Lista;
$obListaInf->setMostraPaginacao( false );
$obListaInf->setTitulo( "Infrações" );
$obListaInf->setRecordSet( $rsListaInfracao );

$obListaInf->addCabecalho();
$obListaInf->ultimoCabecalho->addConteudo( "&nbsp;" );
$obListaInf->ultimoCabecalho->setWidth( 2 );
$obListaInf->commitCabecalho();

# Campo tipo de fiscalização
$obListaInf->addCabecalho();
$obListaInf->ultimoCabecalho->addConteudo( "Infração" );
$obListaInf->ultimoCabecalho->setWidth( 10 );
$obListaInf->commitCabecalho();

$obListaInf->addCabecalho();
$obListaInf->ultimoCabecalho->addConteudo( "Penalidade" );
$obListaInf->ultimoCabecalho->setWidth( 10 );
$obListaInf->commitCabecalho();

$obListaInf->addDado();
$obListaInf->ultimoDado->setAlinhamento( "CENTRO" );
$obListaInf->ultimoDado->setCampo( "[cod_infracao] - [nom_infracao]" );
$obListaInf->commitDado();

$obListaInf->addDado();
$obListaInf->ultimoDado->setAlinhamento( "CENTRO" );
$obListaInf->ultimoDado->setCampo( "penalidades" );
$obListaInf->commitDado();

$obListaInf->montaHTML();
$stHtml = $obListaInf->getHtml();

$obSpanInf = new Span;
$obSpanInf->setId      ( "spnInf" );
$obSpanInf->setValue   ( $stHtml );

$obListaDocs = new Lista;
$obListaDocs->setMostraPaginacao( false );
$obListaDocs->setTitulo( "Documentos" );
$obListaDocs->setRecordSet( $rsListaDocs );

# Campo numérico
$obListaDocs->addCabecalho();
$obListaDocs->ultimoCabecalho->addConteudo( "&nbsp;" );
$obListaDocs->ultimoCabecalho->setWidth( 2 );
$obListaDocs->commitCabecalho();

# Campo tipo de fiscalização
$obListaDocs->addCabecalho();
$obListaDocs->ultimoCabecalho->addConteudo( "Código" );
$obListaDocs->ultimoCabecalho->setWidth( 10 );
$obListaDocs->commitCabecalho();

$obListaDocs->addCabecalho();
$obListaDocs->ultimoCabecalho->addConteudo( "Nome" );
$obListaDocs->ultimoCabecalho->setWidth( 20 );
$obListaDocs->commitCabecalho();

$obListaDocs->addCabecalho();
$obListaDocs->ultimoCabecalho->addConteudo( "Situação" );
$obListaDocs->ultimoCabecalho->setWidth( 20 );
$obListaDocs->commitCabecalho();

$obListaDocs->addDado();
$obListaDocs->ultimoDado->setAlinhamento( "CENTRO" );
$obListaDocs->ultimoDado->setCampo( "cod_documento" );
$obListaDocs->commitDado();

$obListaDocs->addDado();
$obListaDocs->ultimoDado->setAlinhamento( "CENTRO" );
$obListaDocs->ultimoDado->setCampo( "nom_documento" );
$obListaDocs->commitDado();

$obListaDocs->addDado();
$obListaDocs->ultimoDado->setAlinhamento( "CENTRO" );
$obListaDocs->ultimoDado->setCampo( "situacao" );
$obListaDocs->commitDado();

$obListaDocs->montaHTML();
$stHtml = $obListaDocs->getHtml();

if ($_REQUEST["inTipoFiscalizacao"] == 1) {
    require_once( CAM_GT_FIS_NEGOCIO."RFISGerarPlanilhaLancamentos.class.php" );
    require_once( CAM_GT_FIS_VISAO."VFISGerarPlanilhaLancamentos.class.php" );

    //Instanciando a Classe de Controle e de Visao
    $obController = new RFISGerarPlanilhaLancamentos;
    $obVisao = new VFISGerarPlanilhaLancamentos( $obController );

    $where = " fpfc.cod_processo is null and ftf.cod_processo is null and fpl.cod_processo = ".$_REQUEST["inCodProcesso"];
    $rsProcessoFiscalLevantamentos = $obVisao->recuperaTodosCodProcessoLevantamentos($where);

    if ( !$rsProcessoFiscalLevantamentos->Eof() ) {
        //Filtros da pesquisa.
        $_REQUEST["inCodProcessoInscricao"] = $_REQUEST["inCodProcesso"]."-".$_REQUEST["inInscricao"];
        $obListaLancamento = $obVisao->montaPlanilhaLancamentos( $_REQUEST );

        $obSpanListaLancamentos = new Span;
        $obSpanListaLancamentos->setValue( $obListaLancamento );
    }else
        $obSpanListaLancamentos = new Span;
}

$obSpanDocs = new Span;
$obSpanDocs->setId      ( "spnDocs" );
$obSpanDocs->setValue   ( $stHtml );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$boBtnVoltar = new Voltar();
$boBtnVoltar->setName('btVoltar');
$boBtnVoltar->obEvento->setOnclick('Voltar()');

$obFormulario = new Formulario;
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addTitulo     ( "Processo Fiscal" );
$obFormulario->addComponente ( $obLblProc );
$obFormulario->addComponente ( $obLblTipo );
$obFormulario->addComponente ( $obLblSituacaoProc );
if ( $_REQUEST['inCodProcProt'] )
    $obFormulario->addComponente ( $obLblProcesso );

$obFormulario->addComponente ( $obLblFiscal );
$obFormulario->addSpan ( $obSpanDocs );
$obFormulario->addSpan ( $obSpanInf );
if ($_REQUEST["inTipoFiscalizacao"] == 1) {
    $obFormulario->addSpan( $obSpanListaLancamentos );
}

$obFormulario->defineBarra( array($boBtnVoltar) );
$obFormulario->show();
