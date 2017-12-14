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
    * Data de Criação: 09/09/2008

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Cercato

    $ID: $

    * Casos de uso: uc-05.04.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$pgProc = "PRConcederRemissao.php";
$pgForm = "FMConcederRemissao.php";
$pgOcul = "OCConcederRemissao.php";
$pgJs = "JSConcederRemissao.js";
$pgFormRelatorioExecucao = "FMConcederRemissaoRelatorio.php";

include_once( $pgJs );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue ( $stStrl );

$inInscricoesRemir = Sessao::read( "InscricoesRemir" );
$inTotalRemir = Sessao::read( "TotalRemir" );

$nuPorcentagem  = number_format( ($inInscricoesRemir * 100 / $inTotalRemir ), 2, ',', ' ');

$stHtml  = "<center>".$nuPorcentagem."% remido até o momento!<br>";
$stHtml .= "<img id=\"img_carregando\" src=\"".CAM_FW_IMAGENS."loading.gif\"></center>";
$stJs = "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';   \n";

//Define objeto SPAN
$obSpnSpan1 = new Span;
$obSpnSpan1->setId ( "spnSpan1" );
$obSpnSpan1->setValue ( $stHtml );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

//DEFINICAO DO FORM
$obForm = new Form;
if ($inInscricoesRemir < $inTotalRemir) {
    $obForm->setAction ( $pgProc );
    $obForm->setTarget ( "oculto" );
} else {
    //$obForm->setAction ( $pgFormRelatorioExecucao );
    //$obForm->setTarget ( "telaPrincipal" );

    echo "<script type=\"text/javascript\">\r\n";
    echo "    var sAux = window.open('".$pgFormRelatorioExecucao."?".Sessao::getId()."&stAcao=".$_REQUEST["stAcao"]."','','width=20,height=10,resizable=1,scrollbars=1,left=100,top=100');\r\n";
    echo "    eval(sAux)\r\n";
    echo "</script>\r\n";

    SistemaLegado::alertaAviso( $pgForm, "Remissão de Dívida Ativa", "incluir", "aviso", Sessao::getId(), "../" );
}

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

if ($inInscricoesRemir < $inTotalRemir) {
    $obFormulario->addTitulo ( "Processando" );
}

$obFormulario->addSpan ( $obSpnSpan1 );

$obFormulario->show();

if ($inInscricoesRemir < $inTotalRemir) {
    sistemaLegado::executaFrameOculto("buscaValor('atualizarRemissao');");
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
