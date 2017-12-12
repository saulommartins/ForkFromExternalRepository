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
* Página de Listagem de Classificação
* Data de Criação   : 27/05/2004

* @author Analista: ???
* @author Programador: Diego Barbosa Victoria

* @ignore

$Revision: 30860 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

Caso de uso: uc-04.04.08
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_PES_NEGOCIO."RPessoalClassificacaoAssentamento.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterClassificacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GRH_PES_INSTANCIAS."assentamento/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

if ($stAcao == "alterar") {
    $pgProx = $pgForm;
} else {
    $pgProx = $pgProc;
}

$obRPessoalClassificacaoAssentamento = new RPessoalClassificacaoAssentamento;

$stFiltro = $_POST['inFiltroCodTipo'] ? $_POST['inFiltroCodTipo'] : $_GET['inFiltroCodTipo'];
$stLink   = "";
$stLink .= "&stAcao=".$stAcao."&inFiltroCodTipo=".$stFiltro;

//MANTEM FILTRO E PAGINACAO
$arLink = Sessao::read('link');
if ($_GET["pg"] and  $_GET["pos"]) {
    $arLink["pg"]  = $_GET["pg"];
    $arLink["pos"] = $_GET["pos"];
}

$rsLista = new RecordSet;

if ($stFiltro != 'todos') {
    $obRPessoalClassificacaoAssentamento->setCodTipo( $stFiltro );
}
$obRPessoalClassificacaoAssentamento->listarClassificacao( $rsLista );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição da Classificação" );
$obLista->ultimoCabecalho->setWidth( 55 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "cod_classificacao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "descricao_tipo" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("&inCodClassificacao","cod_classificacao");
    $obLista->ultimaAcao->addCampo("stDescricao","descricao");
    $obLista->ultimaAcao->addCampo("stDescQuestao","descricao");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->addCampo("&inCodClassificacao","cod_classificacao");
    $obLista->ultimaAcao->addCampo("stDescricao","descricao");
    $obLista->ultimaAcao->addCampo("inCodTipo","cod_tipo");
    $obLista->ultimaAcao->addCampo("inCodTipoTxt","cod_tipo");
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

?>
