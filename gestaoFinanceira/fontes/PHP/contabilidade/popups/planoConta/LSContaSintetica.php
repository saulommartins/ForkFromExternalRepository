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
    * Página de Listagem de Plano Conta
    * Data de Criação   : 15/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * $Id: LSContaSintetica.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.02,uc-02.04.09,uc-02.04.28 uc-02.02.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ContaSintetica";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgCons = $pgFilt;

include_once( $pgJS );

//$stCaminho   = "../modulos/calendario/relatorio/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'baixar'   : $pgProx = $pgBaix; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'prorrogar': $pgProx = $pgCons; break;
    case 'consultar': $pgProx = $pgCons; break;
    DEFAULT         : $pgProx = $pgForm;
}

//Monta sessao com os valores do filtro
if ( is_array(Sessao::read('linkPopUp')) ) {
    $_REQUEST = Sessao::read('linkPopUp');
} else {
    $arLinkPopUp = array();
    foreach ($_REQUEST as $key => $valor) {
        $arLinkPopUp[$key] = $valor;
    }
    Sessao::write('linkPopUp', $arLinkPopUp);
}

$obTContabilidadePlanoConta = new TContabilidadePlanoConta();
$obTContabilidadePlanoConta->setDado('exercicio', Sessao::getExercicio());
if ($_REQUEST['stCodEstrutural']) {
    $obTContabilidadePlanoConta->setDado('cod_estrutural',$_REQUEST['stCodEstrutural']);
    $stLink .= '&stCodEstrutural='.$_REQUEST['stCodEstrutural'];
}

$stLink .= "&stAcao=".$stAcao;
$stLink .= "&nomForm=".$_REQUEST['nomForm'];
$stLink .= "&campoNum=".$_REQUEST['campoNum'];
$stLink .= "&campoNom=".$_REQUEST['campoNom'];
$stLink .= "&tipoBusca=".$_REQUEST['tipoBusca'];

if ($_REQUEST['stDescricao']) {
    $obTContabilidadePlanoConta->setDado('descricao',$_REQUEST['stDescricao']);
    $stLink .= '&stDescricao='.$_REQUEST['stDescricao'];
}

if ($_REQUEST['tipoBusca'] == 'ativoPassivo' AND $_REQUEST['tipoLancamento']) {
    $stFiltro = " AND cod_estrutural LIKE '".$_REQUEST['tipoLancamento']."%' ";
}

switch ($_REQUEST['tipoBusca2']) {
    case 'Blpaaaa':
        $stFiltro .= " AND NOT EXISTS ( SELECT 	1
                                                FROM 	tcmgo.balanco_blpaaaa
                                               WHERE    balanco_blpaaaa.cod_conta = plano_conta.cod_conta
                                                 AND    balanco_blpaaaa.exercicio = plano_conta.exercicio
                                            ) ";
        break;

}

if ($_REQUEST['tipoBusca'] == 'conta_sintetica') {
    $stFiltro = " AND cod_estrutural LIKE '1.2.0.0.0.00.00.00.00.00%' ";
}

/*$stFiltro.= "
     AND   NOT EXISTS  (   SELECT  1
                             FROM  tcmgo.grupo_plano_conta
                            WHERE  grupo_plano_conta.exercicio = plano_conta.exercicio
                              AND  grupo_plano_conta.cod_conta = plano_conta.cod_conta
                       )
    ";*/
$obTContabilidadePlanoConta->recuperaContaSintetica( $rsLista, $stFiltro, ' ORDER BY cod_estrutural ' );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Classificação");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição ");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_estrutural" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_conta" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insere();" );
$obLista->ultimaAcao->addCampo("1","cod_estrutural");
$obLista->ultimaAcao->addCampo("2","nom_conta");
$obLista->commitAcao();

$obLista->show();

?>
