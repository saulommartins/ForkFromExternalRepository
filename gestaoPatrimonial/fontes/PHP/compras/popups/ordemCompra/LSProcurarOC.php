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
* Arquivo instância para popup de CGM
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Thiago La Delfa  Cabelleira

$Revision: 18898 $
$Name$
$Author: fernando $
$Date: 2006-12-20 14:34:58 -0200 (Qua, 20 Dez 2006) $

Casos de uso: uc-03.04.29
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_COM_MAPEAMENTO."TComprasOrdemCompraNota.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarOC";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript .= " function insereOC(cod_oc,cod_ent,exercicio,empenho) {  \n";
$stFncJavaScript .= "   var sOC;                  \n";
$stFncJavaScript .= "   var sEnt;                 \n";
$stFncJavaScript .= "   var sEx;                  \n";
$stFncJavaScript .= "   sOC  = cod_oc;            \n";
$stFncJavaScript .= "   sEnt = cod_ent;           \n";
$stFncJavaScript .= "   sEx  = exercicio;         \n";
$stFncJavaScript .= "   sEmp  = empenho;         \n";
$stFncJavaScript .= "   window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sOC; \n";
$stFncJavaScript .= "   if ( window.opener.parent.frames['telaPrincipal'].document.getElementById('stExercicioOrdemCompra') ) { \n;";
$stFncJavaScript .= "       window.opener.parent.frames['telaPrincipal'].document.getElementById('stExercicioOrdemCompra').value = sEx;\n; ";
$stFncJavaScript .= "   } \n";
$stFncJavaScript .= "   if ( window.opener.parent.frames['telaPrincipal'].document.getElementById('inCodEntidadeOrdemCompra') ) { \n;";
$stFncJavaScript .= "       window.opener.parent.frames['telaPrincipal'].document.getElementById('inCodEntidadeOrdemCompra').value = sEnt;\n; ";
$stFncJavaScript .= "       window.opener.parent.frames['telaPrincipal'].document.getElementById('inCodEntidadeOrdemCompra').focus();\n";
$stFncJavaScript .= "   }                          \n";
$stFncJavaScript .= "   if ( window.opener.parent.frames['telaPrincipal'].document.getElementById('stEmpenhoOc') ) { \n;";
$stFncJavaScript .= "       window.opener.parent.frames['telaPrincipal'].document.getElementById('stEmpenhoOc').value = sEmp;\n; ";
$stFncJavaScript .= "   }                          \n";
$stFncJavaScript .= "   window.close();            \n";
$stFncJavaScript .= " }  \n";

$obOC = new TComprasOrdemCompraNota();

$obOC->setDado('exercicio',$_REQUEST['filtroExercicio']);
$obOC->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
$obOC->setDado('cod_ordem',$_REQUEST['inCodOrdem']);

if ($_REQUEST['stTipoBusca'] == 'notaFiscal') {
    $obOC->recuperaOrdemCompraNF( $rsLista );
} else {
    $obOC->recuperaOrdemCompra( $rsLista );
}

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ordem de Compra  ");
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidade" );
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Exercício" );
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Empenho  ");
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_ordem" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "exercicio" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "empenho" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereOC();" );
$obLista->ultimaAcao->addCampo("1","cod_ordem");
$obLista->ultimaAcao->addCampo("2","cod_entidade");
$obLista->ultimaAcao->addCampo("3","exercicio");
$obLista->ultimaAcao->addCampo("4","empenho");

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
