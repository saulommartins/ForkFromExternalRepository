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
* Página de lista da Configuração de Salário Família
* Data de Criação: 28/04/2006

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 31475 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.05.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoSalarioFamilia.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoSalarioFamilia";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

$stCaminho = CAM_GRH_FOL_INSTANCIAS."salarioFamilia/";

//Define a função do arquivo, ex: excluir ou alterar
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}

$arFiltro = Sessao::read('filtroRelatorio');
if ($_REQUEST["dtFiltroVigencia"]) {
    $arFiltro["dtFiltroVigencia"] = $_REQUEST["dtFiltroVigencia"];
}
if ( isset($_REQUEST["stFiltroCodRegime"]) ) {
    $arFiltro["stFiltroCodRegime"] = $_REQUEST["stFiltroCodRegime"];
}
$dtFiltroVigencia  = $arFiltro["dtFiltroVigencia"];
$stFiltroCodRegime = $arFiltro["stFiltroCodRegime"];

$link = Sessao::read("link");
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write("link",$link);
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}

$obRFolhaPagamentoSalarioFamilia = new RFolhaPagamentoSalarioFamilia;
$obRFolhaPagamentoSalarioFamilia->setVigencia( $dtFiltroVigencia );
$obRFolhaPagamentoSalarioFamilia->obRFolhaPagamentoPrevidencia->setCodRegimePrevidencia( $stFiltroCodRegime );
$obRFolhaPagamentoSalarioFamilia->listarSalarioFamilia( $rsSalarioFamilia );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet          ( $rsSalarioFamilia );
$obLista->setTitulo             ("Configurações de Salário-Família Cadastrados");

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Regime Previdenciário" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Vigência" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "descricao_regime_previdencia" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "vigencia" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodRegimePrevidencia" , "cod_regime_previdencia" );
$obLista->ultimaAcao->addCampo( "&stTimestamp"            , "timestamp" );
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo( "stDescQuestao" , "descricao_regime_previdencia" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();

$obLista->show();

?>
