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

    * Data de Criação: 15/05/2008

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Cercato

    $ID: $

    * Casos de uso: uc-05.03.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$link = Sessao::read( "link" );
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"] = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write( "link", $link );

//Define o nome dos arquivos PHP
$pgProc = "PREfetuarLancamentos.php";
$pgOcul = "OCManterCalculo.php";
$pgJs = "JSManterCalculo.js";
$pgFormRelatorioExecucaoLancamento = "FMRelatorioExecucaoLancamento.php";

include_once( $pgJs );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue ( $stStrl );

if(Sessao::read( "lancados_total") != 0 ){
    $nuPorcentagem  = number_format( (Sessao::read( "lancados" ) * 100 / Sessao::read( "lancados_total" ) ), 2, ',', ' ');
} else {
     $nuPorcentagem  = number_format( 0, 2, ',', ' ');;
}

$stHtml  = "<center>".$nuPorcentagem."% processada até o momento!<br>";
$stHtml .= "<img id=\"img_carregando\" src=\"".CAM_FW_IMAGENS."loading.gif\"></center>";
$stJs = "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';   \n";

//Define objeto SPAN
$obSpnSpan1 = new Span;
$obSpnSpan1->setId ( "spnSpan1" );
$obSpnSpan1->setValue ( $stHtml );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnTipoCalculo =  new Hidden;
$obHdnTipoCalculo->setName ( "stTipoCalculo" );
$obHdnTipoCalculo->setValue ( "geral" );

$obHdnCodGrupo =  new Hidden;
$obHdnCodGrupo->setName ( "inCodGrupo" );
$obHdnCodGrupo->setValue ( $_REQUEST["inCodGrupo"] );

//DEFINICAO DO FORM
$obForm = new Form;
if ( Sessao::read( "lancados" ) < Sessao::read( "lancados_total" ) ) {
    $obForm->setAction ( $pgProc );
    $obForm->setTarget ( "oculto" );
} else {
    $obForm->setAction ( $pgFormRelatorioExecucaoLancamento );
    $obForm->setTarget ( "telaPrincipal" );

    Sessao::write( "lancamentos_cods", Sessao::read( "lancados_resultado" ) );
}

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnTipoCalculo );
$obFormulario->addHidden ( $obHdnCodGrupo );
if ( Sessao::read( "lancados" ) <= Sessao::read( "lancados_total" ) ) {
    $obFormulario->addTitulo ( "Processando" );
}

$obFormulario->addSpan ( $obSpnSpan1 );

$obFormulario->show();

sistemaLegado::executaFrameOculto("buscaValor('atualizarCalculo');"); //mesmo comando do calculo

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
