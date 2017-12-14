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
    * Página de Lista
    * Data de Criação: 19/11/2007

    * @author Desenvolvedor: Diego Lemos de Souza

    * $Id: LSNaturezaEstabelecimento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.08.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "NaturezaEstabelecimento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript .= " function insereNatureza(num,nom) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var   sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_POST["campoNom"]."').innerHTML = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_POST["campoNum"].".value = sNum; \n";
//$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.HdninCodCnae.value = cod;\n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_POST["campoNum"].".focus(); \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$stFiltro = "";
$stLink   = "";

$stLink .= "&stAcao=".$stAcao;
$rsLista = new RecordSet;

include_once( CAM_GRH_IMA_MAPEAMENTO."TIMANaturezaEstabelecimento.class.php" );
$obTIMANaturezaEstabelecimento = new TIMANaturezaEstabelecimento();
if ($_POST["inExercicio"] != "") {
    $stFiltro = " AND exercicio_vigencia <= ".$_POST["inExercicio"];
}
if ($_POST["stDescricao"] != "") {
    $stFiltro = " AND descricao ilike '%".trim($_POST["stDescricao"])."%'";
}
if ($stFiltro != "") {
    $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
}
$obTIMANaturezaEstabelecimento->recuperaTodos($rsLista,$stFiltro);

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_natureza" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereNatureza();" );
$obLista->ultimaAcao->addCampo("1","cod_natureza");
$obLista->ultimaAcao->addCampo("2","descricao");
$obLista->commitAcao();

$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
