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
    * Arquivo de instância para manutenção de orgao
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    * $Id: LSManterOrgao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-01.05.02

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrgao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProx = $pgForm;

$stCaminho = CAM_GA_ORGAN_INSTANCIAS."orgao/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if (empty($stAcao))
    $stAcao = "alterar";

//Código para manter a paginação e filtro
$filtro = Sessao::read('filtro');
if (!$filtro['paginando']) {
    foreach ($_REQUEST as $stCampo => $stValor) {
        $filtro[$stCampo] = $stValor;
    }
    $filtro['pg']  = $_GET['pg'] ? $_GET['pg'] : 0;
    $filtro['pos'] = $_GET['pos']? $_GET['pos'] : 0;
    $filtro['paginando'] = true;
} else {
    $filtro['pg']  = $_GET['pg'];
    $filtro['pos'] = $_GET['pos'];
}

Sessao::write('filtro', $filtro);

$inCodOrganograma = (!empty($_REQUEST['inCodOrganograma']) ? $_REQUEST['inCodOrganograma'] : $filtro['inCodOrganograma']);

$stLink .= "&stAcao=".$stAcao;

if ($_REQUEST["pg"] and $_REQUEST["pos"]) {
    $stLink.= "&pg=".$_REQUEST["pg"]."&pos=".$_REQUEST["pos"];
}
//<--

$obRegra = new ROrganogramaOrgao;
$stFiltro = "";

$stLink .= '&inCodOrganograma='.$inCodOrganograma;

$rsLista = new RecordSet;
$obRegra->obROrganograma->setCodOrganograma( $inCodOrganograma );
$obRegra->setCodOrgao                   ( $_REQUEST['inCodigo'] );
$obRegra->setSigla                  	( $_REQUEST['stSigla'] );
$obRegra->setDescricao                  ( $_REQUEST['stDescricao'] );
$obRegra->listarAtivosCodigoComposto    ( $rsLista );

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Composto");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Sigla" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_orgao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "orgao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "&nbsp; [sigla_orgao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodOrgao","cod_orgao");
$obLista->ultimaAcao->addCampo("&stCodOrganograma","cod_organograma");

if ($stAcao == "excluir") {
   $obLista->ultimaAcao->addCampo("stDescQuestao" , "[orgao] - [descricao]");
   $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
} else {
   $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

?>
