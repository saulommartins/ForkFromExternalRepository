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
* Página de lista da linha
* Data de Criação: 07/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30880 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_BEN_NEGOCIO."RBeneficioLinha.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterLinha";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GRH_BEN_INSTANCIAS."valeTransporte/";

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

$stLink = "&stAcao=".$stAcao;

$arSessaoLink = Sessao::read('link');
if ($_GET["pg"] and  $_GET["pos"]) {
    $arSessaoLink["pg"]  = $_GET["pg"];
    $arSessaoLink["pos"] = $_GET["pos"];
} elseif ( is_array($arSessaoLink) ) {
    $_GET       = $arSessaoLink;
    $_REQUEST   = $arSessaoLink;
    $arSessaoLink = "";
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arSessaoLink[$key] = $valor;
    }
}
Sessao::write('link', $arSessaoLink);

$obRBeneficioLinha  = new RBeneficioLinha;
$rsLista            = new RecordSet;
$obRBeneficioLinha->listarLinha( $rsLista,$stFiltro," ORDER BY descricao" );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Linhas Cadastradas");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição da Linha" );
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_linha" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodLinha" , "cod_linha" );
$obLista->ultimaAcao->addCampo( "&stDescricaoLinha" , "descricao" );
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"descricao");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

?>
