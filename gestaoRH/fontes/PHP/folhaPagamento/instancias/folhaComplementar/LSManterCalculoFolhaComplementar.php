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
    * Lista de visualização de resultado de calculo de contrato
    * Data de Criação: 24/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-16 10:42:01 -0200 (Ter, 16 Out 2007) $

    * Casos de uso: uc-04.05.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php");
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php");

SistemaLegado::LiberaFrames();

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalculoFolhaComplementar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgProcImpressao = "PR".$stPrograma."Impressao.php";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$link = Sessao::read("link");
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write("link",$link);
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $slink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}

include_once($pgJS);
$jsOnload   = "executaFuncaoAjax('gerarSpanSucessoErro');";

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao" );
$obHdnAcao->setValue                            ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl" );
$obHdnCtrl->setValue                            ( $stStrl  );

$obHdnComplementar =  new Hidden;
$obHdnComplementar->setName                     ( "inCodComplementar" );
$obHdnComplementar->setValue                    ( Sessao::read("inCodComplementar") );

$obLblMesAno = new Label;
$obLblMesAno->setId                             ( "stMesAno" );
$obLblMesAno->setValue                          ( $stMesAno  );
$obLblMesAno->setRotulo                         ( "Mês/Ano"  );

$obLblPeriodo = new Label;
$obLblPeriodo->setId                            ( "stPeriodo" );
$obLblPeriodo->setValue                         ( $stPeriodo  );
$obLblPeriodo->setRotulo                        ( "Período de Movimentação" );

$obRdoCalculados = new Radio;
$obRdoCalculados->setName                       ( "stOpcao" );
$obRdoCalculados->setRotulo                     ( "Matrículas" );
$obRdoCalculados->setLabel                      ( "Calculados com sucesso" );
$obRdoCalculados->setValue                      ( "calculados" );
$obRdoCalculados->obEvento->setOnChange         ( "executaFuncaoAjax('gerarSpanSucessoCalculo',stOpcao);" );
$obRdoCalculados->setChecked                    ( $stOpcao == 'calculados' || !$stOpcao );

$obRdoErro = new Radio;
$obRdoErro->setName                             ( "stOpcao" );
$obRdoErro->setRotulo                           ( "Matrículas" );
$obRdoErro->setLabel                            ( "Erro no cálculo" );
$obRdoErro->setValue                            ( "erro" );
$obRdoErro->obEvento->setOnChange               ( "executaFuncaoAjax('gerarSpanErroCalculo',stOpcao);" );
$obRdoErro->setChecked                          ( $stOpcao == 'erro' );

$obLblContratos = new Label;
$obLblContratos->setName                         ( "inQuantContratos" );
$obLblContratos->setRotulo                       ( "Matrículas Calculadas" );
$obLblContratos->setValue                        (  Sessao::read("inContratosCalculados") );

$obLblContratosSucesso = new Label;
$obLblContratosSucesso->setId                    ( "inQuantContratosSucesso" );
$obLblContratosSucesso->setRotulo                ( "Matrículas Calculadas com Sucesso" );

$obLblContratosErro = new Label;
$obLblContratosErro->setId                       ( "inQuantContratosErro" );
$obLblContratosErro->setRotulo                   ( "Matrículas Calculadas com Erro" );

//Define objeto SPAN
$obSpnSpan1 = new Span;
$obSpnSpan1->setId                               ( "spnSpan1" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                               ( $pgProcImpressao );
$obForm->setTarget                               ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnComplementar );
$obFormulario->addTitulo            ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->agrupaComponentes    ( array($obRdoCalculados,$obRdoErro) );
$obFormulario->addComponente        ( $obLblContratos );
$obFormulario->addComponente        ( $obLblContratosSucesso );
$obFormulario->addComponente        ( $obLblContratosErro );
$obFormulario->addSpan              ( $obSpnSpan1 );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
