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
* Página de Listagem dos Documentos
* Data de Criação   : 04/07/2007

* @author Analista: Gelson Gonsalves
* @author Desenvolvedor: Leandro André Zis

* @ignore

* $Id: LSManterDocumentosExigidos.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-03.05.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoDocumento.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterDocumentosExigidos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GP_LIC_INSTANCIAS."documentos/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

switch ($stAcao) {
    case 'excluir': $pgProx = $pgProc; break;
    case 'consultar': $pgProx = $pgForm; break;
}

$stLink = "&stAcao=".$stAcao;

$arFiltro = Sessao::read('filtro');
if ($_REQUEST['stNomeDocumento'] || $_REQUEST['stDataInicial']) {
    foreach ($_REQUEST as $key => $value) {
        $arFiltro[$key] = $value;
    }
} else {
    if ($arFiltro) {
        foreach ($arFiltro as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }
    Sessao::write('paginando', true);
}

Sessao::write('filtro', $arFiltro);

$obTLicitacaoDocumento = new TLicitacaoDocumento;
$rsLista = new RecordSet;

if ($_REQUEST['stNomeDocumento']) {
   $stFiltro .= " nom_documento = '". $_REQUEST['stNomeDocumento']."' and ";
}
if ($_REQUEST['stDataInicial']) {
   $stFiltro .= " timestamp::date between to_date('". $_REQUEST['stDataInicial']."','dd/mm/yyyy') and to_date('". $_REQUEST['stDataFinal']."', 'dd/mm/yyyy') and ";
}
$stFiltro = ($stFiltro)?' WHERE '.substr($stFiltro,0,strlen($stFiltro)-4):'';

$obTLicitacaoDocumento->recuperaDocumentos($rsLista, $stFiltro );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Documentos cadastrados");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome do Documento" );
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data de Cadastro" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_documento" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "timestamp" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodDocumento"              , "cod_documento" );
$obLista->ultimaAcao->addCampo( "&stNomDocumento"              , "nom_documento" );
$obLista->ultimaAcao->addCampo( "&stDescQuestao"  ,"nom_documento");
$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();

?>
