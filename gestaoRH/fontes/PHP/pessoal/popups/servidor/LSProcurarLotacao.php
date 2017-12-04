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
* Arquivo de instância para procura de Lotação
* Data de Criação: 09/07/2007

* @author Analista: Dagiane
* @author Desenvolvedor: Alexandre Melo

Casos de uso: uc-04.04.07

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php" );

//DEFINE O NOME DOS ARQUIVOS PHP
$stPrograma = "ProcurarLotacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//CONTROLE DE TELA DO LOCAL SELECIONADO
$stFncJavaScript .= " function insereLotacao(num,nom,cod) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.Hdn".$_REQUEST["campoNum"].".value = cod; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNom"].".value = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.frm.".$_REQUEST["campoNum"].".focus(); \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$inCodOrganograma = $_REQUEST['inCodOrganograma'];
if ($inCodOrganograma == "") {
    $obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
    $obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);
    $inCodOrganograma = $rsOrganogramaVigente->getCampo('cod_organograma');
}

$stFiltro = " AND orgao_nivel.cod_organograma = ".$inCodOrganograma;

    $obTOrganogramaOrgao = new TOrganogramaOrgao;
    $obTOrganogramaOrgao->setDado('vigencia', date('Y-m-d'));

if ($_REQUEST['stDescricao']) {
    $stFiltro .= " AND recuperaDescricaoOrgao(orgao.cod_orgao,'".$obTOrganogramaOrgao->getDado('vigencia')."') ILIKE '".stripslashes($_REQUEST['stDescricao'])."%'";
    $stLink .= "&stDescricao=".$_REQUEST["stDescricao"];
}
if ($_REQUEST['inCodigo']) {
    $stFiltro .= " AND orgao_nivel.cod_estrutural = '".stripslashes($_REQUEST['inCodigo'])."'";
    $stLink .= "&inCodigo=".$_REQUEST["inCodigo"];
}

$stLink .= "&stAcao=".$stAcao."&campoNom=".$_REQUEST["campoNom"]."&campoNum=".$_REQUEST["campoNum"];

$obTOrganogramaOrgao->recuperaOrgaos( $rsListaLotacao, $stFiltro );

//INSTÂNCIA DO OBJETO LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsListaLotacao );
$obLista->setTitulo ("Lotações Cadastradas");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Estrutural");
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
// $obLista->ultimoDado->setCampo( "cod_estrutural" );
$obLista->ultimoDado->setCampo( "cod_estrutural" );
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
$obLista->ultimaAcao->setLink( "JavaScript:insereLotacao();" );
$obLista->ultimaAcao->addCampo("1","cod_estrutural"  );
$obLista->ultimaAcao->addCampo("2","descricao"  );
$obLista->ultimaAcao->addCampo("3","cod_orgao"  );
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
