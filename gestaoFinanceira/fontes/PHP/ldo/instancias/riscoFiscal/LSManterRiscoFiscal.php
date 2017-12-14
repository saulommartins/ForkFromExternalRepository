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
 * Página de listagem de Manter Riscos Fiscais
 * Data de Criação: 10/03/2009
 *
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.06 - Manter Riscos Fiscais
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_LDO_VISAO.'VLDOManterRiscoFiscal.class.php';

//Define o nome dos arquivos PHP
$stModulo = 'ManterRiscoFiscal';
$pgForm   = 'FM' . $stModulo . '.php';
$pgProc   = 'PR' . $stModulo. '.php';
$pgExcl   = 'FMExcluirRiscoFiscal.php';
$pgCons   = 'FMConsultarRiscoFiscal.php';

$stAcao = trim(strtolower($_REQUEST['stAcao']));

#echo '<pre>', print_r($_REQUEST), '</pre>';
#die;

// Definir link
if ($stAcao == 'excluir') {
    $pgAcao = $pgProc;
} elseif ($stAcao == 'alterar') {
    $pgAcao = $pgForm;
} else {
    $pgAcao = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
if ($_GET["pg"] && $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $sessao->link["pg"]  = $_GET["pg"];
    $sessao->link["pos"] = $_GET["pos"];
}

$stCaminho  = CAM_GF_LDO_INSTANCIAS . 'riscoFiscal/' . $pgAcao;
$stCaminho .= '?' . Sessao::getId() . '&stAcao=' . $stAcao . $stLink;

$link = Sessao::read( 'link' );
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if (is_array($link)) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write( 'link', $link );
}
$inAnoLDO = $_REQUEST['inAnoLDO'];
$rsRiscoFiscal = VLDOManterRiscoFiscal::recuperarInstancia()->recuperarRiscoFiscal($inAnoLDO);

$obLista = new Lista;
$obLista->setMostraPaginacao( true );
$obLista->setTitulo( 'Lista de Riscos Fiscais' );

$numElementos = count($rsRiscoFiscal->arElementos);
for ($i=0; $i < $numElementos; $i++) {
    $rsRiscoFiscal->arElementos[$i]['descricao'] = stripslashes($rsRiscoFiscal->arElementos[$i]['descricao']);
    if (strlen($rsRiscoFiscal->arElementos[$i]['descricao']) > 100) {
        $rsRiscoFiscal->arElementos[$i]['descricao'] = substr($rsRiscoFiscal->arElementos[$i]['descricao'], 0, 100) . '...';
    }
}

$obLista->setRecordSet($rsRiscoFiscal);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth   ( 5 );
$obLista->commitCabecalho();
// Descrição
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( 'Descrição' );
$obLista->ultimoCabecalho->setWidth   ( 70 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth   ( 7 );
$obLista->commitCabecalho();
// DADOS DA LISTA
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->ultimoDado->setCampo      ( 'descricao' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );

$obLista->ultimaAcao->addCampo( '&inCodRiscoFiscal', 'cod_risco_fiscal' );
$obLista->ultimaAcao->addCampo( 'inAnoLDO', 'ano' );
$obLista->ultimaAcao->addCampo( 'descricao', 'descricao' );
$obLista->ultimaAcao->addCampo( 'valor', 'valor' );
$obLista->ultimaAcao->addCampo( "stDescQuestao","descricao");

$obLista->ultimaAcao->setLink ( $stCaminho );
$obLista->commitAcao();
$obLista->show();

?>
