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
    * Página de Formulário Pessoal Regime
    * Data de Criação   : 22/04/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 30860 $
    $Name$
    $Author: andre $
    $Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

    Caso de uso: uc-04.04.05

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_PES_NEGOCIO."RPessoalRegime.class.php");
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );

$stPrograma = "ManterRegime";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once($pgJs);

$rsFaixas    = new RecordSet;
$obRegra     = new RPessoalRegime;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
Sessao::write('subDivisao', array());

if ($stAcao == 'alterar') {
   $inCodRegime = $_REQUEST['inCodRegime'];
    $stRegime = ( $inCodRegime == 1 ) ? "CLT" : "RJU";
   $obRegra->setCodRegime($_REQUEST['inCodRegime']);
   $obRegra->addPessoalSubDivisao();
   $obRegra->roUltimoPessoalSubDivisao->listarSubDivisao($rsSubDivisao,$stFiltro='',$boTransacao);
   $inCount = 0;
   $arSubDivisao = Sessao::read('subDivisao');
   while ( !$rsSubDivisao->eof() ) {
        $inCount = $inCount + 1;
        $arTMP['inId']             = $inCount;
        $arTMP['descricao']        = $rsSubDivisao->getCampo("nom_sub_divisao");
        $arTMP['inCodSubDivisao']  = $rsSubDivisao->getCampo("cod_sub_divisao");
        $arSubDivisao[] = $arTMP;
        $rsSubDivisao->proximo();
    }
    Sessao::write('subDivisao', $arSubDivisao);
    sistemaLegado::executaFrameOculto("buscaValor('preencheInner');");
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnstSubDivisao = new Hidden;
$obHdnstSubDivisao->setName( "stSubDivisao" );
$obHdnstSubDivisao->setValue( $stSubDivisao );

$obHdnCodRegime = new Hidden;
$obHdnCodRegime->setName( "inCodRegime" );
$obHdnCodRegime->setValue( $inCodRegime );

$obLblRegime = new label;
$obLblRegime->setRotulo( "Regime" );
$obLblRegime->setName( "stRegime" );
$obLblRegime->setValue( $stRegime );

$obHdnCodNorma = new Hidden;
$obHdnCodNorma->setName( "hdninCodFaixaDesconto" );
$obHdnCodNorma->setValue( $hdninCodFaixaDesconto );

$obRdoCLT = new Radio;
$obRdoCLT->setRotulo            ( "Regime" );
$obRdoCLT->setName              ( "inCodRegime" );
$obRdoCLT->setId                ( "inCodRegime" );
$obRdoCLT->setTitle             ( "Selecione o tipo de regime." );
$obRdoCLT->setLabel             ( "CLT" );
$obRdoCLT->setValue             ( 1 );
$obRdoCLT->setChecked           ( true );
$obRdoCLT->setNull              ( false );

$obRdoRJU = new Radio;
$obRdoRJU->setRotulo            ( "Regime" );
$obRdoRJU->setName              ( "inCodRegime" );
$obRdoRJU->setTitle             ( "Selecione o tipo de regime." );
$obRdoRJU->setLabel             ( "RJU" );
$obRdoRJU->setValue             ( 2 );
$obRdoRJU->setNull              ( false );

//Define objeto TEXTBOX para armazenar a DESCRICAO da sub divisão
$obTxtDescricaoSubDivisao= new TextBox;
$obTxtDescricaoSubDivisao->setRotulo              ( "*Descrição da Subdivisão");
$obTxtDescricaoSubDivisao->setTitle               ( "Informe a descrição da subdivisão." );
$obTxtDescricaoSubDivisao->setName                ( "stDescricaoSubDivisao" );
$obTxtDescricaoSubDivisao->setValue               ( $stDescricaoSubDivisao  );
$obTxtDescricaoSubDivisao->setSize                ( 40 );
$obTxtDescricaoSubDivisao->setMaxLength           ( 80 );
$obTxtDescricaoSubDivisao->setCaracteresAceitos ( '[0-9a-zA-Z áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ/-]' );
$obTxtDescricaoSubDivisao->setEspacosExtras       ( false );

$obBtnIncluir = new Button;
$obBtnIncluir->setName ( "btnIncluir" );
$obBtnIncluir->setValue( "Incluir" );
$obBtnIncluir->setTipo ( "button" );
$obBtnIncluir->obEvento->setOnClick ( "incluirSubDivisao();" );

$obBtnAlterar = new Button;
$obBtnAlterar->setName ( "btnAlterar" );
$obBtnAlterar->setValue( "Alterar" );
$obBtnAlterar->setTipo ( "button" );
$obBtnAlterar->obEvento->setOnClick ( "buscaValor('alterarSubDivisao');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "button" );
$obBtnLimpar->obEvento->setOnClick ( "limpaSubDivisao();" );

$obSpnSubDivisao = new Span;
$obSpnSubDivisao->setId ( "spnSubDivisao" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnstSubDivisao );
$obFormulario->addTitulo            ( "Dados do Regime"     );
if ($stAcao == "incluir") {
    $obFormulario->agrupaComponentes( array($obRdoCLT,$obRdoRJU)      );
} else {
    $obFormulario->addComponente    ( $obLblRegime );
    $obFormulario->addHidden        ( $obHdnCodRegime );
}
$obFormulario->addTitulo            ( "Subdivisão"     );
$obFormulario->addComponente        ( $obTxtDescricaoSubDivisao  );
$obFormulario->defineBarra          ( array ($obBtnIncluir,$obBtnAlterar,$obBtnLimpar),"","","" );
$obFormulario->addSpan              ( $obSpnSubDivisao );
if ( $stAcao == "incluir" )
    $obFormulario->OK               ();
else
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );

$obFormulario->setFormFocus($obRdoCLT->getId() );
$obFormulario->show             ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
