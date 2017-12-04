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
 * Página de listagem de Manter Compensação da Renúncia de Receita
 * Data de Criação: 23/03/2009
 *
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.16 - Manter Compensação da Renúncia de Receita
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_LDO_VISAO.'VLDOManterRenunciaReceita.class.php';

//Define o nome dos arquivos PHP
$stModulo = 'ManterRenunciaReceita';
$pgForm   = 'FM' . $stModulo . '.php';
$pgProc   = 'PR' . $stModulo. '.php';
$pgExcl   = 'FMExcluirRenunciaReceita.php';
$pgCons   = 'FMConsultarRenunciaReceita.php';

$stAcao = trim(strtolower($_REQUEST['stAcao']));

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

$stCaminho  = CAM_GF_LDO_INSTANCIAS . 'configuracao/' . $pgAcao;
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

$rsRenunciaReceita = VLDOManterRenunciaReceita::recuperarInstancia()->recuperarRenunciaReceita($_REQUEST);

$obLista = new Lista;
$obLista->setMostraPaginacao( true );
$obLista->setTitulo( 'Lista de estimativas e compensação da renúncia de receita' );

$obLista->setRecordSet($rsRenunciaReceita);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth   ( 5 );
$obLista->commitCabecalho();
// Descrição
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( 'Código' );
$obLista->ultimoCabecalho->setWidth   ( 7 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( 'Tributo' );
$obLista->ultimoCabecalho->setWidth   ( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( 'Modalidade' );
$obLista->ultimoCabecalho->setWidth   ( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth   ( 7 );
$obLista->commitCabecalho();
// DADOS DA LISTA
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->ultimoDado->setCampo      ( 'cod_compensacao' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->ultimoDado->setCampo      ( 'tributo' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->ultimoDado->setCampo      ( 'modalidade' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );

$obLista->ultimaAcao->addCampo( '&inCodCompensacao', 'cod_compensacao' );
$obLista->ultimaAcao->addCampo( 'inAnoLDO', 'ano' );
$obLista->ultimaAcao->addCampo( 'inCodPPA', 'cod_ppa' );
$obLista->ultimaAcao->addCampo( 'stTributo', 'tributo' );
$obLista->ultimaAcao->addCampo( 'stModalidade', 'modalidade' );
$obLista->ultimaAcao->addCampo( 'stSetorProgramas', 'setores_programas' );
$obLista->ultimaAcao->addCampo( 'flValorAnoLDO', 'valor_ano_ldo' );
$obLista->ultimaAcao->addCampo( 'flValorAnoLDO1', 'valor_ano_ldo_1' );
$obLista->ultimaAcao->addCampo( 'flValorAnoLDO2', 'valor_ano_ldo_2' );
$obLista->ultimaAcao->addCampo( 'stCompensacao', 'compensacao' );
$obLista->ultimaAcao->addCampo( "&stDescQuestao","cod_compensacao" );

$obLista->ultimaAcao->setLink ( $stCaminho );
$obLista->commitAcao();
$obLista->show();

?>
