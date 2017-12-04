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
    * Página de Filtro para Concessão de Diárias
    * Data de Criação: 05/08/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: FLConcederDiarias.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.09.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_DIA_MAPEAMENTO."TDiariasTipoDiaria.class.php"                                   );

//Define o nome dos arquivos PHP
$stPrograma    = "ConcederDiarias";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$jsOnload = "executaFuncaoAjax('atualizaSpanFiltro', '&rdoOpcao=2');";

Sessao::remove('link');

$stAcao = $request->get('stAcao');

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName('stCtrl');

$obHdnAcao = new Hidden;
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);

$obRdoRegistro = new Radio;
$obRdoRegistro->setName    ( "rdoOpcao"      );
$obRdoRegistro->setId      ( "rdoOpcao2"      );
$obRdoRegistro->setRotulo  ( "Opções"        );
$obRdoRegistro->setLabel   ( "Matrícula"     );
$obRdoRegistro->setValue   ( 2               );
$obRdoRegistro->setChecked ( true            );
$obRdoRegistro->obEvento->setOnChange( "montaParametrosGET('atualizaSpanFiltro', 'rdoOpcao');" );

$obRdoCGMRegistro = new Radio;
$obRdoCGMRegistro->setName    ( "rdoOpcao"      );
$obRdoCGMRegistro->setId      ( "rdoOpcao1"     );
$obRdoCGMRegistro->setRotulo  ( "Opções"        );
$obRdoCGMRegistro->setLabel   ( "CGM/Matrícula" );
$obRdoCGMRegistro->setValue   ( 1               );
$obRdoCGMRegistro->setChecked ( false           );
$obRdoCGMRegistro->obEvento->setOnChange( "montaParametrosGET('atualizaSpanFiltro', 'rdoOpcao');" );

$obSpnOpcao = new Span;
$obSpnOpcao->setID("spnOpcao");
/*
$obDataPagamentoInicial = new Data();
$obDataPagamentoInicial->setRotulo("Período de Pagamento");
$obDataPagamentoInicial->setName("dtPagamentoInicial");
$obDataPagamentoInicial->setId("dtPagamentoInicial");

$obDataPagamentoFinal = new Data();
$obDataPagamentoFinal->setName("dtPagamentoFinal");
$obDataPagamentoFinal->setId("dtPagamentoFinal");

$obLabelDataPagamento = new Label();
$obLabelDataPagamento->setValue("&nbsp;&nbsp;à&nbsp;&nbsp;");
*/

$obDataInicioViagem = new Data();
$obDataInicioViagem->setRotulo("Período da Viagem");
$obDataInicioViagem->setName("dtInicioViagem");
$obDataInicioViagem->setId("dtInicioViagem");

$obDataTerminoViagem = new Data();
$obDataTerminoViagem->setName("dtTerminoViagem");
$obDataTerminoViagem->setId("dtTerminoViagem");

$obLabelDataViagem = new Label();
$obLabelDataViagem->setValue("&nbsp;&nbsp;à&nbsp;&nbsp;");

$obTDiariasTipoDiaria = new TDiariasTipoDiaria();
$obTDiariasTipoDiaria->recuperaRelacionamento($rsTipoDiarias);

$obSelectTipoDiarias = new Select();
$obSelectTipoDiarias->setRotulo("Tipo de Diária");
$obSelectTipoDiarias->setName("inCodTipoDiaria");
$obSelectTipoDiarias->setId("inCodTipoDiaria");
$obSelectTipoDiarias->setStyle("width:300px;");
$obSelectTipoDiarias->addOption("", "Selecione");

while (!$rsTipoDiarias->eof()) {
    $obSelectTipoDiarias->addOption($rsTipoDiarias->getCampo("cod_tipo"), $rsTipoDiarias->getCampo("nom_tipo"));
    $rsTipoDiarias->proximo();
}

$obHdnEval = new HiddenEval;
$obHdnEval->setName ( "stEval" );
$obHdnEval->setValue( "" );

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "" );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                       );
$obFormulario->addTitulo			( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden            ( $obHdnCtrl                    );
$obFormulario->addHidden            ( $obHdnAcao                    );
$obFormulario->addHidden            ( $obHdnEval,true               );
$obFormulario->addTitulo            ( "Busca de Servidor"           );
$obFormulario->agrupaComponentes    ( array ( $obRdoRegistro, $obRdoCGMRegistro ) );
$obFormulario->addSpan              ( $obSpnOpcao                   );

if ($stAcao == "consultar") {
    $obFormulario->addTitulo            ( "Busca de Diárias"           );
    //$obFormulario->agrupaComponentes(  array($obDataPagamentoInicial, $obLabelDataPagamento, $obDataPagamentoFinal) );
    $obFormulario->agrupaComponentes(  array($obDataInicioViagem, $obLabelDataViagem, $obDataTerminoViagem) );
    $obFormulario->addComponente($obSelectTipoDiarias);
}

$obBtnClean = new Button;
$obBtnClean->setName                    ( "btnClean"              );
$obBtnClean->setValue                   ( "Limpar"                );
$obBtnClean->setTipo                    ( "button"                );
$obBtnClean->obEvento->setOnClick       ( "executaFuncaoAjax('atualizaSpanFiltro', '&rdoOpcao=2');" );
$obBtnClean->setDisabled                ( false                   );

$obBtnOK = new Ok;
$botoesForm  = array ( $obBtnOK , $obBtnClean );
$obFormulario->defineBarra($botoesForm);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
