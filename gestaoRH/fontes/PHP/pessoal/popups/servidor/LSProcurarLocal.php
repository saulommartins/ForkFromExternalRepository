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
* Arquivo de instância para manutenção de locais
* Data de Criação: 04/07/2007

* @author Analista: Dagiane
* @author Desenvolvedor: Alexandre Melo

Casos de uso: uc-01.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php"     );

//DEFINE O NOME DOS ARQUIVOS PHP
$stPrograma = "ProcurarLocal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//CONTROLE DE TELA DO LOCAL SELECIONADO
$stFncJavaScript .= " function insereLocal(num,nom) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNom"].".value = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".focus(); \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$obROrganogramaLocal = new ROrganogramaLocal;
$rsListaLocal = new RecordSet;

if ($_REQUEST['stDescricao']) {
    $obROrganogramaLocal->setDescricao(($_REQUEST['stDescricao']) ? $_REQUEST['stDescricao'] : $stDescricao);
    $stLink .= "&stDescricao=".$_REQUEST["stDescricao"];
}

if ($_REQUEST['inCodigo']) {
    $obROrganogramaLocal->setCodLocal(($_REQUEST['inCodigo']) ? $_REQUEST['inCodigo'] : $inCodLotacao);
    $stLink .= "&inCodigo=".$_REQUEST["inCodigo"];
}
$stLink .= "&stAcao=".$stAcao."&campoNom=".$_REQUEST["campoNom"]."&campoNum=".$_REQUEST["campoNum"];

$obROrganogramaLocal->listarUnicoLocal( $rsListaLocal );

//INSTÂNCIA DO OBJETO LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsListaLocal );
$obLista->setTitulo ("Locais cadastrados");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

//cod_local e descricao SAO CAMPOS RETORNADOS PELO SQL
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_local" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereLocal();" );
$obLista->ultimaAcao->addCampo("1","cod_local"  );
$obLista->ultimaAcao->addCampo("2","descricao"  );
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
