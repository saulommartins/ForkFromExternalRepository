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
* Página de Listagem Tipo de Assentamento
* Data de Criação   : 31/01/2004

* @author Analista: ???
* @author Programador: Lucas Leusin Oaigen

* @ignore

$Revision: 30860 $
$Name$
$Author: souzadl $
$Date: 2007-04-02 18:07:20 -0300 (Seg, 02 Abr 2007) $

Caso de uso: uc-04.04.08
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalVantagem.class.php"           );
include_once(CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php"         );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAssentamento";
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
} elseif ($stAcao == "baixar") {
    $pgProx = $pgBaix;
} else {
    $pgProx = $pgProc;
}

$obRPessoalVantagem      = new RPessoalVantagem;
$obRPessoalAssentamento  = new RPessoalAssentamento($obRPessoalVantagem);

$stLink .= '&inCodClassificacao='.$_REQUEST['inCodClassificacao'];
$stLink .= "&stAcao=".$stAcao;

//MANTEM FILTRO E PAGINACAO
$arLink = Sessao::read('link');
if ($_GET["pg"] and  $_GET["pos"]) {
    $arLink["pg"]  = $_GET["pg"];
    $arLink["pos"] = $_GET["pos"];
}

$rsLista = new RecordSet;
$obRPessoalAssentamento->obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento( $_REQUEST['inCodClassificacao'] );
$obRPessoalAssentamento->listarAssentamento( $rsLista );

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
$obLista->ultimoCabecalho->addConteudo("Sigla ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 55 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_assentamento" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "sigla" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("&inCodAssentamento","cod_assentamento");
    $obLista->ultimaAcao->addCampo("inCodClassificacao","cod_classificacao");
    $obLista->ultimaAcao->addCampo("stDescricao","descricao");
    $obLista->ultimaAcao->addCampo("stDescQuestao","descricao");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->addCampo("&inCodAssentamento","cod_assentamento");
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

?>
