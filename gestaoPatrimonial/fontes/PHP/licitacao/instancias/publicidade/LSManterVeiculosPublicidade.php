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
    * Página de Listagem da Objeto
    * Data de Criação   : 04/07/2007

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    $Id: LSManterVeiculosPublicidade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso :uc-03.05.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoVeiculosPublicidade.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterVeiculosPublicidade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GP_LIC_INSTANCIAS."publicidade/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

switch ($stAcao) {
    case 'excluir': $pgProx = $pgProc; break;
}

$stLink = "&stAcao=".$stAcao;

$arFiltro = Sessao::read('filtro');
if ($_REQUEST['inCodTipoVeiculosPublicidade'] || $_REQUEST['inCGM']) {
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

$obTLicitacaoVeiculosPublicidade = new TLicitacaoVeiculosPublicidade;
$rsLista = new RecordSet;

if ($_REQUEST['inCodTipoVeiculoPublicidade']) {
   $stFiltro .= " and tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = ". $_REQUEST['inCodTipoVeiculoPublicidade'];
}
if ($_REQUEST['inCGM']) {
   $stFiltro .= " and  sw_cgm.numcgm = ". $_REQUEST['inCGM'];
}

$obTLicitacaoVeiculosPublicidade->recuperaRelacionamento($rsLista, $stFiltro );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Veículos de Publicidade cadastrados");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de Veículo de Publicidade" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "CGM" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome do Veículo de Publicidade" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

if ($stAcao == 'excluir') {
   $obLista->addCabecalho();
   $obLista->ultimoCabecalho->addConteudo("&nbsp;");
   $obLista->ultimoCabecalho->setWidth( 2 );
   $obLista->commitCabecalho();
}

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "tipo_descricao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_cgm"   );
$obLista->commitDado();

if ($stAcao == 'excluir') {
   $obLista->addAcao();
   $obLista->ultimaAcao->setAcao ( $stAcao );
   $obLista->ultimaAcao->addCampo( "&inCGM"                       , "numcgm" );
   $obLista->ultimaAcao->addCampo( "&stNomCGM"                    , "nom_cgm" );
   $obLista->ultimaAcao->addCampo( "&inCodTipoVeiculoPublicidade" , "descricao" );
   $obLista->ultimaAcao->addCampo( "&stDescQuestao"  ,"[numcgm] - [nom_cgm]");
   $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
   $obLista->commitAcao();
}

$obLista->show();

?>
