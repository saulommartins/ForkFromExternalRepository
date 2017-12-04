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
  * Página de Lista de Cobranca
  * Data de criação : 03/01/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: LSManterCobranca.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.04.04
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpModalidade.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelamento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATParcelamentoCancelamento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcela.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATParcelamento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelaOrigem.class.php" );

include_once ( CAM_GT_DAT_MAPEAMENTO."FDATBuscaSaldoDivida.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCobranca";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgListParc = "LS".$stPrograma."Parcelas.php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "inscrever";
}

//Define arquivos PHP para cada acao
switch ($_REQUEST['stAcao']) {
    case 'alterar'   : $pgProx = $pgForm; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    DEFAULT          : $pgProx = $pgForm;
}

$link = Sessao::read('link');

if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link', $link);

//MONTAGEM DO FILTRO
$obFDATBuscaSaldoDivida = new FDATBuscaSaldoDivida;
$obFDATBuscaSaldoDivida->setDado('inNumCGM',$request->get('inCGM'));
$obFDATBuscaSaldoDivida->setDado('inInscMunIni',$request->get('inCodImovelInicial'));
$obFDATBuscaSaldoDivida->setDado('inInscMunFim',$request->get('inCodImovelFinal'));
$obFDATBuscaSaldoDivida->setDado('inInscEcoIni',$request->get('inNumInscricaoEconomicaInicial'));
$obFDATBuscaSaldoDivida->setDado('inInscEcoFim',$request->get('inNumInscricaoEconomicaFinal'));
if ( $request->get('inCodInscricao') ) {
    $arDadosInscricao = explode( "/",$request->get('inCodInscricao') );
    $obFDATBuscaSaldoDivida->setDado('inInscDivida',$arDadosInscricao[0]);
    $obFDATBuscaSaldoDivida->setDado('stExercicio', $arDadosInscricao[1]);
}
$obFDATBuscaSaldoDivida->setDado('boAgrupa','true');
$obFDATBuscaSaldoDivida->recuperaTodos($rsSaldo);

if ( $rsSaldo->Eof() ) {
    SistemaLegado::alertaAviso($pgFilt, "Nenhuma inscrição em divida ativa encontrada!", "n_incluir", "erro" );
    exit;
}

$arSaldo = array();
$stNomCGM = '';
$inNumCGM =  '';
$inNumCGMAnt =  '';
$stChave ='';
$stChaveAnt ='';
$stCGM = empty($stCGM) ? "" : $stCGM;
while (!$rsSaldo->eof()) {
    $stChave = $rsSaldo->getCampo('cod_inscricao').$rsSaldo->getCampo('exercicio');
    $inNumCGM =  $rsSaldo->getCampo('numcgm');
    if ($stChave <> $stChaveAnt) {
        $arCredito = explode('§',$rsSaldo->getCampo('credito_formatado'));
        $arDataVencimento = explode('-',$rsSaldo->getCampo('dt_vencimento_origem'));
        if ($stCGM == '') {
            $stNomCGM = $rsSaldo->getCampo('nom_cgm');
            $inNumCGM = $rsSaldo->getCampo('numcgm');

            $stProprietario = $inNumCGM .'-'.$stNomCGM;
        }
        $arTmp = array();
        $arTmp['numcgm'] = $rsSaldo->getCampo('numcgm');
        $arTmp['cod_inscricao'] = $rsSaldo->getCampo('cod_inscricao');
        $arTmp['exercicio'] = $rsSaldo->getCampo('exercicio');
        $arTmp['vlr_original_parcela'] = $rsSaldo->getCampo('valor');
        $arTmp['total_de_parcelas_divida'] = $rsSaldo->getCampo('total_parcelas_divida');
        $arTmp['inscricao'] = $rsSaldo->getCampo('inscricao');
        $arTmp['dt_vencimento_origem'] = $rsSaldo->getCampo('dt_vencimento_origem');
        $arTmp['valor_total_parcelas_divida'] = $rsSaldo->getCampo('valor_corrigido');
        $arTmp['inscricao_tipo'] = $rsSaldo->getCampo('inscricao_tipo');
        $arTmp['credito_formatado'] = $arCredito[0];
        if (isset($arCredito[5])) {
            $arTmp['credito_descricao'] = $arCredito[5];
        }
        $arTmp['dt_vencimento_origem_br'] = $arDataVencimento[2].'/'.$arDataVencimento[1].'/'.$arDataVencimento[0];
        $arTmp['stCreditos'] = $arCredito[0].';'.$rsSaldo->getCampo('valor_corrigido');
        if (isset($arCredito[5])) {
            $arTmp['origem'] = $arCredito[0].' - '.$arCredito[5];
        } else {
            $arTmp['origem'] = $arCredito[0];
        }
        $arTmp['grupo_original'] = $rsSaldo->getCampo('origem');
        $arTmp['total_de_parcelas_divida'] = $rsSaldo->getCampo('total_parcelas_divida');
        $arTmp['vlr_original_parcela_br'] = $rsSaldo->getCampo('valor_corrigido');
        $arSaldo[] = $arTmp;
    } elseif (($stChave == $stChaveAnt) && ($inNumCGM <> $inNumCGMAnt)) {
        $stProprietario .= ', '.$rsSaldo->getCampo('numcgm').'-'.$rsSaldo->getCampo('nom_cgm');
    }
    $stChaveAnt = $rsSaldo->getCampo('cod_inscricao').$rsSaldo->getCampo('exercicio');
    $inNumCGMAnt =  $rsSaldo->getCampo('numcgm');
    $rsSaldo->proximo();
}
$rsListaDivida = new RecordSet();
$rsListaDivida->preenche( $arSaldo );
$rsListaDivida->setPrimeiroElemento();
$rsListaDivida->addFormatacao ('vlr_original_parcela_br', 'NUMERIC_BR');
$rsListaDivida->ordena("exercicio");

$obLista = new Lista;
$obLista->setRecordSet( $rsListaDivida );
$obLista->setTitulo( "Registros de Parcelas" );
$obLista->setMostraPaginacao(false);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição/Ano" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Crédito/Grupo de Crédito" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Parcelas");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 17 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Vencimento");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento ("CENTRO");
$obLista->ultimoDado->setCampo( "[cod_inscricao]/[exercicio]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "grupo_original" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento ("CENTRO");
$obLista->ultimoDado->setCampo( "total_de_parcelas_divida" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento ("DIREITA");
$obLista->ultimoDado->setCampo( "vlr_original_parcela_br" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento ("CENTRO");
$obLista->ultimoDado->setCampo( "dt_vencimento_origem_br" );
$obLista->commitDado();
$obChkParcela = new Checkbox;
$obChkParcela->setName  ( "boSelecionada" );
$obChkParcela->setValue ( "[cod_inscricao]§[exercicio]§[arCreditos]§[num_parcela]§[vlr_original_parcela]§[dt_vencimento_parcela]§[total_de_parcelas_divida]§[inscricao]§[dt_vencimento_origem]§[valor_total_parcelas_divida]§[inscricao_tipo]§[credito_formatado]§[valor_por_credito]§[dt_vencimento_origem_br]§[stCreditos]§[origem]§[grupo_original]§[numcgm]" );

$obLista->addDadoComponente                    ( $obChkParcela );
$obLista->ultimoDado->setAlinhamento           ( 'CENTRO' );
$obLista->ultimoDado->setCampo                 ( "reemitir" );
$obLista->commitDadoComponente                 ();

$obChkTodosN = new Checkbox;
$obChkTodosN->setName                        ( "boTodos" );
$obChkTodosN->setId                          ( "boTodos" );
$obChkTodosN->setRotulo                      ( "Selecionar Todas" );
$obChkTodosN->obEvento->setOnChange          ( "selecionarTodos('n');" );
$obChkTodosN->montaHTML();

$obTabelaCheckboxN = new Tabela;
$obTabelaCheckboxN->addLinha();
$obTabelaCheckboxN->ultimaLinha->addCelula();
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setColSpan ( 2 );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Selecionar Todos".$obChkTodosN->getHTML()."&nbsp;</div>");
$obTabelaCheckboxN->ultimaLinha->commitCelula();
$obTabelaCheckboxN->commitLinha();
$obTabelaCheckboxN->montaHTML();

$obLista->montaHTML();

$stHtml = $obLista->getHTML();
$stHtml .= $obTabelaCheckboxN->getHTML();

$obSpanDados = new Span;
$obSpanDados->setId      ( "spnLista" );
$obSpanDados->setValue   ( $stHtml );

$obLblContribuinte = new Label;
$obLblContribuinte->setRotulo    ( "CGM" );
$obLblContribuinte->setName      ( "stContribuinte" );
$obLblContribuinte->setId        ( "stContribuinte" );
$obLblContribuinte->setValue     ( $stProprietario );
$obLblContribuinte->setTitle     ( "CGM" );

$obIPopUpModalidade = new IPopUpModalidade;
$obIPopUpModalidade->setTipoModalidade(4); //cobranca

$obDataVencimento = new Data;
$obDataVencimento->setName ( "inDataVencimento" );
$obDataVencimento->setTitle ( "Data de Vencimento" );
$obDataVencimento->setRotulo ( "Data de Vencimento" );
$obDataVencimento->setNull ( false );

$rsParcelas = new RecordSet;

$obCmbParcelas = new Select;
$obCmbParcelas->setRotulo               ( "Número de Parcelas" );
$obCmbParcelas->setTitle                ( "Número de Parcelas" );
$obCmbParcelas->setName                 ( "cmbParcelas" );
$obCmbParcelas->addOption               ( "", "Selecione" );
$obCmbParcelas->setCampoId              ( "inNumParcelas" );
$obCmbParcelas->setCampoDesc            ( "inNumParcelas" );
$obCmbParcelas->preencheCombo           ( $rsParcelas );
$obCmbParcelas->setNull                 ( false );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $request->get('stCtrl')  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $request->get('stAcao')  );

$obHdnNomCgm = new Hidden;
$obHdnNomCgm->setName  ( "stNomCGM" );
$obHdnNomCgm->setValue ( $stNomCGM );

$obHdnProprietario = new Hidden; //Pode conter 1 ou mais proprietários
$obHdnProprietario->setName  ( "stProprietario" );
$obHdnProprietario->setValue ( $stProprietario );

$obHdnCgm = new Hidden;
$obHdnCgm->setName  ( "inCGM" );
$obHdnCgm->setValue ( $inNumCGM );

$obBtnOK = new OK;
$obBtnOK->setName              ( "btnOk" );
$obBtnOK->obEvento->setOnClick ( "validarListar();"    );

$obBtnCancelar = new Cancelar;

$botoesSpanBotoes = array ( $obBtnOK, $obBtnCancelar );

$pgOcul = "'".$pgOcul."?".Sessao::getId()."&".$obIPopUpModalidade->obInnerModalidade->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$obIPopUpModalidade->obInnerModalidade->obCampoCod->getName()."&stIdCampoDesc=".$obIPopUpModalidade->obInnerModalidade->getId()."&tipoModalidade=".$obIPopUpModalidade->inCodTipoModalidade."'";
$obIPopUpModalidade->setFuncao("ajaxJavaScript(".$pgOcul.",'buscaModalidade' );");

Sessao::write('cobranca', true);

$obForm = new Form;
$obForm->setAction ( "LSManterCobrancaParcelas.php" );
//$obForm->settarget ( "oculto" );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName  ('campoNum');
$obHdnCampoNum->setValue ( $request->get('campoNum'));

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCgm );
$obFormulario->addHidden     ( $obHdnNomCgm );
$obFormulario->addHidden     ( $obHdnCampoNum );
$obFormulario->addHidden     ( $obHdnProprietario );
$obFormulario->addTitulo     ( "Dados do Contribuinte" );
$obFormulario->addComponente ( $obLblContribuinte );
$obFormulario->addSpan( $obSpanDados );
$obIPopUpModalidade->geraFormulario( $obFormulario );
$obFormulario->addComponente ( $obCmbParcelas );//$obTxtNumeroParcelas );
$obFormulario->addComponente ( $obDataVencimento );
$obFormulario->defineBarra   ( $botoesSpanBotoes, 'left', '' );

$obFormulario->show();
