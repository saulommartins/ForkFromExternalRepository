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
    * Página de Oculto do Entidade Intermediadora
    * Data de Criação: 03/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30843 $
    $Name$
    $Author: andre $
    $Date: 2007-06-04 11:54:54 -0300 (Seg, 04 Jun 2007) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEntidadeIntermediadora.class.php"                        );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEntidadeIntermediadora";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stCaminho = CAM_GRH_EST_INSTANCIAS."entidadesIntermediadoras/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}

$arSessaoLink = Sessao::read('link');
if ($_GET["pg"] and  $_GET["pos"]) {
    $arSessaoLink["pg"]  = $_GET["pg"];
    $arSessaoLink["pos"] = $_GET["pos"];
} elseif ( is_array($arSessaoLink) ) {
    $_GET = $arSessaoLink;
    $_REQUEST = $arSessaoLink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arSessaoLink[$key] = $valor;
    }
}
Sessao::write('link', $arSessaoLink);

$stLink  = "&stAcao=$stAcao";

$rsLista = new Recordset;
$obTEstagioEntidadeIntermediadora = new TEstagioEntidadeIntermediadora;
if ($_REQUEST['HdninCGM']) {
    $obTEstagioEntidadeIntermediadora->setDado("numcgm",$_REQUEST['HdninCGM']);
}
$obTEstagioEntidadeIntermediadora->recuperaEntidadesIntermediarias($rsLista);

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
//$obLista->setTitulo("Entidades Intermediadoras Cadastradas");
$stTitulo = ' </div></td></tr><tr><td colspan="5" class="alt_dados">Entidades Intermediadoras Cadastradas';
$obLista->setTitulo             ('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia().$stTitulo);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidades Intermediadoras" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->addCampo( "&inNumCGM"          , "numcgm" );
$obLista->ultimaAcao->addCampo( "&stNomCGM"          , "nom_cgm" );
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo( "&stDescQuestao"     , "nom_cgm" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
