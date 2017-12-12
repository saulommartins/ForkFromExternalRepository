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
    * Página de Formulário PessoalPrevidencia
    * Data de Criação   : 03/05/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 31465 $
    $Name$
    $Author: andre $
    $Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

    * Casos de uso :uc-04.04.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_PES_NEGOCIO."RPessoalCausaRescisao.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalSefip.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCasoCausa.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCasoCausaSubDivisao.class.php");

$stPrograma = "ManterCausa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stLink = $pgFilt."?".Sessao::getId()."&inCodCausa=".$request->get('inCodCausa');
$stAcao = $request->get("stAcao");

Sessao::write('arCasosCausa', array());

$rsPeriodo                = new RecordSet;
$rsSubDivisaoSelecionados = new RecordSet;
$obRPessoalCausaRescisao  = new RPessoalCausaRescisao;
$obRPessoalCausaRescisao->addPessoalCasoCausa();
$obRPessoalCausaRescisao->roUltimoPessoalCasoCausa->addPessoalSubDivisao();
$obRPessoalCausaRescisao->roUltimoPessoalCasoCausa->roUltimoPessoalSubDivisao->listarSubDivisao($rsSubDivisaoDisponiveis);
$obRPessoalCausaRescisao->obRPessoalMovimentoSefipSaida->listarMovSefipSaidaSemRetorno($rsMovimentacao);
$obErro = $obRPessoalCausaRescisao->roUltimoPessoalCasoCausa->listarPeriodoRescisao($rsPeriodo);

if ($stAcao == "alterar") {
    $obTPessoalSefip = new TPessoalSefip();
    $stFiltro = " WHERE cod_sefip = ".$request->get('inCodSefipSaida');
    $obTPessoalSefip->recuperaTodos($rsSefip,$stFiltro);
    $inCodSefipSaida = $rsSefip->getCampo("num_sefip");
    $obTPessoalCasoCausa = new TPessoalCasoCausa();
    $stFiltro = " WHERE cod_causa_rescisao = ".$request->get('inCodigoCausa');
    $obTPessoalCasoCausa->recuperaTodos($rsCasoCausa,$stFiltro);
    $obTPessoalCasoCausaSubDivisao = new TPessoalCasoCausaSubDivisao();

    $arCasosCausa = array();
    while (!$rsCasoCausa->eof()) {
        $stFiltro = " WHERE cod_caso_causa = ".$rsCasoCausa->getCampo("cod_caso_causa");
        $obTPessoalCasoCausaSubDivisao->recuperaTodos($rsSubDivisao,$stFiltro);
        $arSubDivisao = array();
        while (!$rsSubDivisao->eof()) {
            $arSubDivisao[] = $rsSubDivisao->getCampo("cod_sub_divisao");
            $rsSubDivisao->proximo();
        }
        $arTemp['inId']                     = count($arCasosCausa);
        $arTemp['inCodCasoCausa']           = $rsCasoCausa->getCampo("cod_caso_causa");
        $arTemp['stDescricaoCaso']          = $rsCasoCausa->getCampo("descricao");
        $arTemp['inCodPeriodo']             = $rsCasoCausa->getCampo("cod_periodo");
        $arTemp['inCodRegimeSelecionados']  = $arSubDivisao;
        $arTemp['boPagaAvisoPrevio']        = $rsCasoCausa->getCampo("paga_aviso_previo");
        $arTemp['boFeriasVencidas']         = $rsCasoCausa->getCampo("paga_ferias_vencida");
        $arTemp['boFeriasProporcionais']    = $rsCasoCausa->getCampo("paga_ferias_proporcional");
        $arTemp['inCodSaqueFGTS']           = $rsCasoCausa->getCampo("cod_saque_fgts");
        $arTemp['flMultaFGTS']              = $rsCasoCausa->getCampo("multa_fgts");
        $arTemp['flContribuicao']           = $rsCasoCausa->getCampo("perc_cont_social");
        $arTemp['boFeriasFGTS']             = $rsCasoCausa->getCampo("inc_fgts_ferias");
        $arTemp['bo13FGTS']                 = $rsCasoCausa->getCampo("inc_fgts_13");
        $arTemp['boAvisoPrevioFGTS']        = $rsCasoCausa->getCampo("inc_fgts_aviso_previo");
        $arTemp['boFeriasIRRF']             = $rsCasoCausa->getCampo("inc_irrf_ferias");
        $arTemp['bo13IRRF']                 = $rsCasoCausa->getCampo("inc_irrf_13");
        $arTemp['boAvisoPrevioIRRF']        = $rsCasoCausa->getCampo("inc_irrf_aviso_previo");
        $arTemp['boFeriasPrevidencia']      = $rsCasoCausa->getCampo("inc_prev_ferias");
        $arTemp['bo13Previdencia']          = $rsCasoCausa->getCampo("inc_prev_13");
        $arTemp['boAvisoPrevioPrevidencia'] = $rsCasoCausa->getCampo("inc_prev_aviso_previo");
        $arTemp['boArtigo479']              = $rsCasoCausa->getCampo("inden_art_479");

        $arCasosCausa[] = $arTemp;

        $rsCasoCausa->proximo();
    }
    Sessao::write('arCasosCausa', $arCasosCausa);

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisaoCaged.class.php");
    $obTPessoalCausaRescisaoCaged = new TPessoalCausaRescisaoCaged();
    $stFiltro = " AND cod_causa_rescisao = ".$request->get('inCodigoCausa');
    $obTPessoalCausaRescisaoCaged->recuperaRelacionamento($rsCausaRescisaoCaged,$stFiltro);
    $inCodCaged = $rsCausaRescisaoCaged->getCampo("num_caged");

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisao.class.php");
    $obTPessoalCausaRescisao = new TPessoalCausaRescisao();
    $stFiltro = " AND cod_causa_rescisao = ".$request->get('inCodigoCausa');
    $obTPessoalCausaRescisao->recuperaRelacionamento($rsCausaRescisao,$stFiltro);
    $inCodCausaAfastamentoMTE = $rsCausaRescisao->getCampo("cod_causa_afastamento");
    
    SistemaLegado::executaFrameOculto("buscaValor('montaListaCasosCausa');");
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodCausa = new Hidden;
$obHdnCodCausa->setName( "inCodCausaRescisao" );
$obHdnCodCausa->setValue( $request->get("inCodigoCausa") );

//Define objeto TEXTBOX para armazenar  o código da causa
$obTxtCodCausa= new TextBox;
$obTxtCodCausa->setRotulo        ( "Código"     );
$obTxtCodCausa->setTitle         ( "Informe o código da causa." );
$obTxtCodCausa->setName          ( "inNumCausa" );
$obTxtCodCausa->setId            ( "inNumCausa" );
$obTxtCodCausa->setValue         ( $request->get("inNumCausa")  );
$obTxtCodCausa->setSize          ( 6            );
$obTxtCodCausa->setMaxLength     ( 3            );
$obTxtCodCausa->setNull          ( false        );
$obTxtCodCausa->setInteiro       ( true         );
if ($stAcao == 'alterar') {
    $obTxtCodCausa->setReadOnly  ( true         );
    $obTxtCodCausa->setStyle    ( "color:#333333");
}

//Define objeto TEXTBOX para armazenar a DESCRICAO do caso
$obTxtDescricaoCausa= new TextBox;
$obTxtDescricaoCausa->setRotulo        ( "Descrição da Causa" );
$obTxtDescricaoCausa->setTitle         ( "Informe a descrição da causa." );
$obTxtDescricaoCausa->setName          ( "stDescricaoCausa" );
$obTxtDescricaoCausa->setValue         ( $request->get("stDescricaoCausa")  );
$obTxtDescricaoCausa->setSize          ( 40 );
$obTxtDescricaoCausa->setMaxLength     ( 80 );
$obTxtDescricaoCausa->setNull          ( false );
$obTxtDescricaoCausa->setEspacosExtras ( false );
if ($stAcao == 'alterar') {
    $obTxtDescricaoCausa->setReadOnly  ( true         );
    $obTxtDescricaoCausa->setStyle    ( "color:#333333");
}

$obTxtCodMovimentacao= new TextBox;
$obTxtCodMovimentacao->setRotulo        ( "Código de Movimentação da Sefip" );
$obTxtCodMovimentacao->setTitle         ( "Selecione o código da movimentação." );
$obTxtCodMovimentacao->setName          ( "inCodTxtSefipSaida" );
$obTxtCodMovimentacao->setId            ( "inCodTxtSefipSaida" );
$obTxtCodMovimentacao->setValue         ( trim($inCodSefipSaida)  );
$obTxtCodMovimentacao->setSize          ( 6            );
$obTxtCodMovimentacao->setMaxLength     ( 3            );
$obTxtCodMovimentacao->setNull          ( false        );
$obTxtCodMovimentacao->setToUpperCase   ( true         );

$obCmbMovimentacao = new Select;
$obCmbMovimentacao->setRotulo           ( "Código de Movimentação da Sefip"  );
$obCmbMovimentacao->setName             ( "inCodSefipSaida"    );
$obCmbMovimentacao->setValue            ( trim($inCodSefipSaida)     );
$obCmbMovimentacao->setStyle            ( "width: 200px"   );
$obCmbMovimentacao->setCampoID          ( "num_sefip" );
$obCmbMovimentacao->setCampoDesc        ( "descricao"      );
$obCmbMovimentacao->addOption           ( "", "Selecione"  );
$obCmbMovimentacao->setNull             ( false            );
$obCmbMovimentacao->preencheCombo       ( $rsMovimentacao );

$rsCaged = new recordset;

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCaged.class.php");
$obTPessoalCaged = new TPessoalCaged();
$obTPessoalCaged->recuperaTodos($rsCaged," WHERE tipo = 'D'");

$obTxtCaged= new TextBox;
$obTxtCaged->setRotulo        ( "CAGED" );
$obTxtCaged->setTitle         ( "Selecione o código do CAGED." );
$obTxtCaged->setName          ( "inNumCagedTxt" );
$obTxtCaged->setId            ( "inNumCagedTxt" );
$obTxtCaged->setValue         ( trim($inCodCaged)  );
$obTxtCaged->setInteiro(true);
$obTxtCaged->setSize          ( 10            );

$obCmbCaged = new Select;
$obCmbCaged->setRotulo           ( "CAGED"  );
$obCmbCaged->setName             ( "inNumCaged"    );
$obCmbCaged->setValue            ( trim($inCodCaged)     );
$obCmbCaged->setStyle            ( "width: 200px"   );
$obCmbCaged->setCampoID          ( "num_caged" );
$obCmbCaged->setCampoDesc        ( "descricao"      );
$obCmbCaged->addOption           ( "", "Selecione"  );
$obCmbCaged->preencheCombo       ( $rsCaged );

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaAfastamentoMTE.class.php");
$rsCausaMTE = new RecordSet();
$TPessoalCausaAfastamentoMTE = new TPessoalCausaAfastamentoMTE();
$TPessoalCausaAfastamentoMTE->recuperaTodos($rsCausaMTE);

// Monta Causa MTE
$obTxtCausaMTE= new TextBox;
$obTxtCausaMTE->setRotulo        ( "Causa MTE" );
$obTxtCausaMTE->setTitle         ( "Selecione o código da Causa do Afastamento MTE." );
$obTxtCausaMTE->setName          ( "inNumCausaMTETxt" );
$obTxtCausaMTE->setId            ( "inNumCausaMTETxt" );
$obTxtCausaMTE->setValue         ( trim($inCodCausaAfastamentoMTE) );
$obTxtCausaMTE->setNull (false);
$obTxtCausaMTE->setSize          ( 5 );
$obTxtCausaMTE->setToUpperCase   ( true         );

$obCmbCausaMTE = new Select;
$obCmbCausaMTE->setRotulo           ( "Causa MTE"  );
$obCmbCausaMTE->setName             ( "inNumCausaMTE"    );
$obCmbCausaMTE->setValue            ( trim($inCodCausaAfastamentoMTE) );
$obCmbCausaMTE->setNull  (false);
//$obCmbCausaMTE->setStyle            ( "width: 200px"   );
$obCmbCausaMTE->setCampoID          ( "cod_causa_afastamento" );
$obCmbCausaMTE->setCampoDesc        ( "nom_causa_afastamento" );
$obCmbCausaMTE->addOption           ( "", "Selecione"  );
$obCmbCausaMTE->preencheCombo       ( $rsCausaMTE );

//Define objeto TEXTBOX para armazenar a DESCRICAO do caso
$obTxtDescricaoCaso= new TextBox;
$obTxtDescricaoCaso->setRotulo        ( "*Descrição do Caso" );
$obTxtDescricaoCaso->setTitle         ( "Informe a descrição do caso." );
$obTxtDescricaoCaso->setName          ( "stDescricaoCaso" );
$obTxtDescricaoCaso->setValue         ( $stDescricaoCaso  );
$obTxtDescricaoCaso->setSize          ( 40 );
$obTxtDescricaoCaso->setMaxLength     ( 80 );
$obTxtDescricaoCaso->setEspacosExtras ( false );

$obTxtCodPeriodo= new TextBox;
$obTxtCodPeriodo->setRotulo        ( "*Período para Rescisão" );
$obTxtCodPeriodo->setTitle         ( "Selecione um período para rescisão." );
$obTxtCodPeriodo->setName          ( "inCodTxtPeriodo" );
$obTxtCodPeriodo->setValue         ( $inCodTxtPeriodo  );
$obTxtCodPeriodo->setSize          ( 6            );
$obTxtCodPeriodo->setMaxLength     ( 3            );
$obTxtCodPeriodo->setInteiro       ( true         );

$obCmbPeriodo = new Select;
$obCmbPeriodo->setRotulo           ( "*Período para rescisão"  );
$obCmbPeriodo->setName             ( "inCodPeriodo"   );
$obCmbPeriodo->setValue            ( $inCodPeriodo    );
$obCmbPeriodo->setStyle            ( "width: 200px"   );
$obCmbPeriodo->setCampoID          ( "cod_periodo"    );
$obCmbPeriodo->setCampoDesc        ( "descricao"      );
$obCmbPeriodo->addOption           ( "", "Selecione"  );
$obCmbPeriodo->preencheCombo       ( $rsPeriodo       );

$obCmbRegimeSubDivisao = new SelectMultiplo();
$obCmbRegimeSubDivisao->setName   ('inCodRegime');
$obCmbRegimeSubDivisao->setRotulo ( "*Regime/Subdivisão" );
$obCmbRegimeSubDivisao->setNull   ( true );
$obCmbRegimeSubDivisao->setTitle  ( "Regime/Subdivisão." );

// lista de atributos disponiveis
$obCmbRegimeSubDivisao->SetNomeLista1 ('inCodRegimeDisponiveis');
$obCmbRegimeSubDivisao->setCampoId1   ('cod_sub_divisao');
$obCmbRegimeSubDivisao->setCampoDesc1 ('[nom_regime]/[nom_sub_divisao]');
$obCmbRegimeSubDivisao->setStyle1     ("width: 300px");
$obCmbRegimeSubDivisao->SetRecord1    ( $rsSubDivisaoDisponiveis );

// lista de atributos selecionados
$obCmbRegimeSubDivisao->SetNomeLista2 ('inCodRegimeSelecionados');
$obCmbRegimeSubDivisao->setCampoId2   ('cod_sub_divisao');
$obCmbRegimeSubDivisao->setCampoDesc2 ('[nom_regime]/[nom_sub_divisao]');
$obCmbRegimeSubDivisao->setStyle2     ("width: 300px");
$obCmbRegimeSubDivisao->SetRecord2    ( $rsSubDivisaoSelecionados );

$obChkPagarAvisoPrevio = new CheckBox;
$obChkPagarAvisoPrevio->setName    ( "boPagaAvisoPrevio" );
$obChkPagarAvisoPrevio->setRotulo  ( "Pagar Aviso Prévio" );
$obChkPagarAvisoPrevio->setTitle   ( "Indique se neste caso é pago aviso prévio." );
$obChkPagarAvisoPrevio->setValue   ( "t" );
$obChkPagarAvisoPrevio->setChecked ( $boPagaAvisoPrevio == 't' );

//############PAGAR FERIAS###################

$obChkPagarFerias = new CheckBox;
$obChkPagarFerias->setName    ( "boFeriasVencidas" );
$obChkPagarFerias->setRotulo  ( "Pagar Férias" );
$obChkPagarFerias->setTitle   ( "Indique se neste caso é pago férias." );
$obChkPagarFerias->setValue   ( "t" );
$obChkPagarFerias->setChecked ( $boFeriasVencidas == 't' );
$obChkPagarFerias->setLabel("Férias Vencidas");

$obChkFeriasProporcionais = new CheckBox;
$obChkFeriasProporcionais->setName    ( "boFeriasProporcionais" );
$obChkFeriasProporcionais->setTitle   ( "Indique se neste caso é pago férias." );
$obChkFeriasProporcionais->setValue   ( "t" );
$obChkFeriasProporcionais->setChecked ( $boFeriasProporcionais == 't' );
$obChkFeriasProporcionais->setLabel("Férias Proporcionais");

//############FIM PAGAR FERIAS###################

$obTxtCodSaqueFGTS = new TextBox;
$obTxtCodSaqueFGTS->setRotulo        ( "Código de saque do FGTS" );
$obTxtCodSaqueFGTS->setTitle         ( "Informe o código para saque do FGTS." );
$obTxtCodSaqueFGTS->setName          ( "inCodSaqueFGTS" );
$obTxtCodSaqueFGTS->setValue         ( $inCodSaqueFGTS  );
$obTxtCodSaqueFGTS->setSize          ( 6            );
$obTxtCodSaqueFGTS->setMaxLength     ( 6            );
$obTxtCodSaqueFGTS->setInteiro       ( true         );

$obTxtMultaFGTS = new Moeda;
$obTxtMultaFGTS->setRotulo     ( "Multa para FGTS(%)" );
$obTxtMultaFGTS->setName       ( "flMultaFGTS" );
$obTxtMultaFGTS->setValue      ( $flMultaFGTS  );
$obTxtMultaFGTS->setTitle      ( "Informe o percentual de multa para FGTS." );
$obTxtMultaFGTS->setNull       ( true );
$obTxtMultaFGTS->setMaxLength  ( 6     );
$obTxtMultaFGTS->obEvento->setOnChange("validaPercentual(this,'Multa para FGTS(%)');");

$obTxtContribuicao = new Moeda;
$obTxtContribuicao->setRotulo     ( "Contribuição Social Lei 110/2001(%)" );
$obTxtContribuicao->setName       ( "flContribuicao" );
$obTxtContribuicao->setValue      ( $flContribuicao  );
$obTxtContribuicao->setTitle      ( "Informe o percentual de contribuição." );
$obTxtContribuicao->setNull       ( true );
$obTxtContribuicao->setMaxLength  ( 6     );
$obTxtContribuicao->obEvento->setOnChange("validaPercentual(this,'Contribuição Social Lei 110/2001(%)');");

//############FGTS###################
$obChkFeriasFGTS = new CheckBox;
$obChkFeriasFGTS->setName    ( "boFeriasFGTS" );
$obChkFeriasFGTS->setRotulo  ( "Incidências para FGTS" );
$obChkFeriasFGTS->setTitle   ( "Indique as incidências para FGTS." );
$obChkFeriasFGTS->setValue   ( "t" );
$obChkFeriasFGTS->setChecked ( $boFeriasFGTS == 't' );
$obChkFeriasFGTS->setLabel("Férias");

$obChk13FGTS = new CheckBox;
$obChk13FGTS->setName    ( "bo13FGTS" );
$obChk13FGTS->setValue   ( "t" );
$obChk13FGTS->setChecked ( $bo13FGTS == 't' );
$obChk13FGTS->setLabel("13º");

$obChkAvisoPrevioFGTS = new CheckBox;
$obChkAvisoPrevioFGTS->setName    ( "boAvisoPrevioFGTS" );
$obChkAvisoPrevioFGTS->setValue   ( "t" );
$obChkAvisoPrevioFGTS->setChecked ( $boAvisoPrevioFGTS == 't' );
$obChkAvisoPrevioFGTS->setLabel("Aviso Prévio");

//############FIM FGTS###################

//############IRPF###################
$obChkFeriasIRRF = new CheckBox;
$obChkFeriasIRRF->setName    ( "boFeriasIRRF" );
$obChkFeriasIRRF->setRotulo  ( "Incidências para IRRF" );
$obChkFeriasIRRF->setTitle   ( "Indique as incidências para IRRF." );
$obChkFeriasIRRF->setValue   ( "t" );
$obChkFeriasIRRF->setChecked ( $boFeriasIRRF == 't' );
$obChkFeriasIRRF->setLabel("Férias");

$obChk13IRRF = new CheckBox;
$obChk13IRRF->setName    ( "bo13IRRF" );
$obChk13IRRF->setValue   ( "t" );
$obChk13IRRF->setChecked ( $bo13IRRF == 't' );
$obChk13IRRF->setLabel("13º");

$obChkAvisoPrevioIRRF = new CheckBox;
$obChkAvisoPrevioIRRF->setName    ( "boAvisoPrevioIRRF" );
$obChkAvisoPrevioIRRF->setValue   ( "t" );
$obChkAvisoPrevioIRRF->setChecked ( $boAvisoPrevioIRRF == 't' );
$obChkAvisoPrevioIRRF->setLabel("Aviso Prévio");

//############FIM IRPF###################

//############Previdencia###################

$obChkFeriasPrevidencia = new CheckBox;
$obChkFeriasPrevidencia->setName    ( "boFeriasPrevidencia" );
$obChkFeriasPrevidencia->setRotulo  ( "Incidências para Previdência" );
$obChkFeriasPrevidencia->setTitle   ( "Indique as incidências para previdência." );
$obChkFeriasPrevidencia->setValue   ( "t" );
$obChkFeriasPrevidencia->setChecked ( $boFeriasPrevidencia == 't' );
$obChkFeriasPrevidencia->setLabel("Férias");

$obChk13Previdencia = new CheckBox;
$obChk13Previdencia->setName    ( "bo13Previdencia" );
$obChk13Previdencia->setValue   ( "t" );
$obChk13Previdencia->setChecked ( $bo13Previdencia == 't' );
$obChk13Previdencia->setLabel("13º");

$obChkAvisoPrevioPrevidencia = new CheckBox;
$obChkAvisoPrevioPrevidencia->setName    ( "boAvisoPrevioPrevidencia" );
$obChkAvisoPrevioPrevidencia->setValue   ( "t" );
$obChkAvisoPrevioPrevidencia->setChecked ( $boAvisoPrevioPrevidencia == 't' );
$obChkAvisoPrevioPrevidencia->setLabel("Aviso Prévio");

//############FIM IRPF###################

$obChkArtigo479 = new CheckBox;
$obChkArtigo479->setName    ( "boArtigo479" );
$obChkArtigo479->setRotulo  ( "Indenização do Artigo 479 CLT ");
$obChkArtigo479->setTitle   ( "Indique a indenização no artigo 479." );
$obChkArtigo479->setValue   ( "t" );
$obChkArtigo479->setChecked ( $boArtigo479 == 't' );

$obBtnIncluir = new Button;
$obBtnIncluir->setName ( "btnIncluir" );
$obBtnIncluir->setValue( "Incluir" );
$obBtnIncluir->setTipo ( "button" );
$obBtnIncluir->obEvento->setOnClick ( "incluirCaso();" );

$obBtnAlterar = new Button;
$obBtnAlterar->setName ( "btnAlterar" );
$obBtnAlterar->setValue( "Alterar" );
$obBtnAlterar->setTipo ( "button" );
$obBtnAlterar->setDisabled(true);
$obBtnAlterar->obEvento->setOnClick ( "buscaValor('alterarCaso');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "button" );
$obBtnLimpar->obEvento->setOnClick ( "buscaValor('limparCaso');" );

$obSpnSubDivisao = new Span;
$obSpnSubDivisao->setId ( "spnSubDivisao" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

// Mensagem para quando não possui periodo de custo
if ($obErro->ocorreu()) {
    $stMensagem = $obErro->getDescricao();
}else{
    $stMensagem = "Deve ser criado o primeiro periodo de movimentação para acessar esta ação.";
}
$obLblMensagem = new Label;
$obLblMensagem->setRotulo( "Atenção"   );
$obLblMensagem->setValue ( $stMensagem );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );

if ($rsPeriodo->getNumLinhas() < 1) {
    $obFormulario->addComponente($obLblMensagem);
} else {
    $obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
    $obFormulario->addHidden            ( $obHdnAcao );
    $obFormulario->addHidden            ( $obHdnCtrl );
    $obFormulario->addHidden            ( $obHdnCodCausa);
    
    $obFormulario->addTitulo             ( "Dados da Causa"        );
    $obFormulario->addComponente         ( $obTxtCodCausa          );
    $obFormulario->addComponente         ( $obTxtDescricaoCausa    );
    $obFormulario->addComponenteComposto ( $obTxtCodMovimentacao,$obCmbMovimentacao );
    $obFormulario->addComponenteComposto($obTxtCaged,$obCmbCaged);
    $obFormulario->addComponenteComposto($obTxtCausaMTE,$obCmbCausaMTE);
    $obFormulario->addTitulo             ( "Casos de Causa de Rescisão"     );
    $obFormulario->addComponente         ( $obTxtDescricaoCaso    );
    $obFormulario->addComponenteComposto ( $obTxtCodPeriodo,$obCmbPeriodo );
    $obFormulario->addComponente         ( $obCmbRegimeSubDivisao  );
    $obFormulario->addComponente         ( $obChkPagarAvisoPrevio );
    $obFormulario->agrupaComponentes     ( array($obChkPagarFerias,$obChkFeriasProporcionais) );
    $obFormulario->addComponente         ( $obTxtCodSaqueFGTS);
    $obFormulario->addComponente         ( $obTxtMultaFGTS);
    $obFormulario->addComponente         ( $obTxtContribuicao);
    $obFormulario->agrupaComponentes     ( array($obChkFeriasFGTS,$obChk13FGTS,$obChkAvisoPrevioFGTS) );
    $obFormulario->agrupaComponentes     ( array($obChkFeriasIRRF,$obChk13IRRF,$obChkAvisoPrevioIRRF) );
    $obFormulario->agrupaComponentes     ( array($obChkFeriasPrevidencia,$obChk13Previdencia,$obChkAvisoPrevioPrevidencia) );
    $obFormulario->addComponente         ( $obChkArtigo479);
    $obFormulario->defineBarra           ( array ($obBtnIncluir,$obBtnAlterar,$obBtnLimpar),"","","" );
    $obFormulario->addSpan               ($obSpnSubDivisao);
    
    if ($_REQUEST['stAcao']=='incluir') {
        $obFormulario->OK                    ();
        $obFormulario->setFormFocus( $obTxtCodCausa->getId() );
    } else {
        $obFormulario->Cancelar($stLink);
        $obFormulario->setFormFocus( $obTxtCodMovimentacao->getId() );
    }
}

$obFormulario->show ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
