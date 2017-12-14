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
  * Data de Criação: 13/10/2008

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Fernando Cercato

  $Id:$

  * Casos de uso: uc-05.03.11
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$pgProc = "PREmitirCarne.php";
$pgOcul = "OCEmitirCarne.php";
$pgJs   = "JSEmitirCarne.js";
$pgFormRelatorioExecucao = "FMEmitirCarneGrafica.php";

include_once $pgJs;

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$nuPorcentagem = number_format((Sessao::read('listados') * 100 / Sessao::read('total_listar')), 2, ',', ' ');

$stHtml  = "<center>".$nuPorcentagem."% gerado até o momento!<br>";
$stHtml .= "<img id=\"img_carregando\" src=\"".CAM_FW_IMAGENS."loading.gif\"></center>";
$stJs = "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';   \n";

//Define objeto SPAN
$obSpnSpan1 = new Span;
$obSpnSpan1->setId    ( "spnSpan1" );
$obSpnSpan1->setValue ( $stHtml );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST["stAcao"] );

$nome_arquivo = Sessao::read( "NomeArquivoGrafica" );

$obHdnArquivo = new Hidden;
$obHdnArquivo->setName  ( "stNomeArquivo" );
$obHdnArquivo->setValue ( $nome_arquivo );

//DEFINICAO DO FORM
$obForm = new Form;

if (Sessao::read('listados') < Sessao::read('total_listar')) {
    $obForm->setAction ( $pgProc );
    $obForm->setTarget ( "oculto" );
} else {
    $obForm->setAction ( $pgFormRelatorioExecucao );
    $obForm->setTarget ( "telaPrincipal" );
}

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnArquivo );
$obFormulario->addTitulo ( "Processando" );

$obFormulario->addSpan   ( $obSpnSpan1 );

$obFormulario->show();

SistemaLegado::executaFrameOculto("buscaValor('atualizarCarneGrafica');");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
