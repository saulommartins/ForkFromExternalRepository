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
 * Arquivo de instância para manutenção de tipo contrato
 * Data de Criação: 13/11/2015
 * @author Analista: Gelson Wolowski Gonçalves 
 * @author Desenvolvedor: Jean da Silva
 *
 * $Id: LSManterTipoContrato.php 64121 2015-12-04 12:53:09Z michel $
 * 
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TLIC."TLicitacaoTipoContrato.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GP_LIC_INSTANCIAS."contrato/";

$stAcao = $request->get('stAcao', 'excluir');

switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
}

$stLink = "&stAcao=".$stAcao;

$filtro = Sessao::read('filtro');

Sessao::write('filtro', $filtro);
Sessao::write('pos', $request->get('pos'));
Sessao::write('pg', $request->get('pg'));
Sessao::write('paginando', $request->get('paginando'));

$stFiltro = " ";

$obTLicitacaoTipoContrato = new TLicitacaoTipoContrato();
if ($request->get("inCodigo") != "") {
    $stFiltro .= "tipo_contrato.cod_tipo = ".$request->get("inCodigo")." AND \n";
}

if ($request->get("stSigla") != "") {
    $stFiltro .= "tipo_contrato.sigla = '".$request->get("stSigla")."' AND \n";
}

if ($request->get("stDescricao") != "") {
    $stFiltro .= "tipo_contrato.descricao = '".$request->get("stDescricao")."' AND \n";
}

if ($request->get("inCodigoTribunal") != "") {
    $stFiltro .= "tipo_contrato.tipo_tc = ".$request->get("inCodigoTribunal")." AND \n";
}

if ($stFiltro != " ") {
    $stFiltro = "\nWHERE ".substr($stFiltro, 1, -5);
}

$stOrdem = " ORDER BY cod_tipo";

$obTLicitacaoTipoContrato->recuperaTodos ($rsLista, $stFiltro, $stOrdem);

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Tipos de Contrato cadastrados");

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código do Tribunal" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Sigla" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descricao" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "cod_tipo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "tipo_tc" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "sigla" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addAcao();

$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodigo", "cod_tipo" );
$obLista->ultimaAcao->addCampo( "inCodigoTribunal", "tipo_tc" );
$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );

$obLista->commitAcao();

$obLista->show();
