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
/*
    * Lista de Contratos para Rescisão TCEMG
    * Data de Criação   : 05/05/2014
    
    * @author Analista      Silvia Martins Silva
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: LSRescindirContrato.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "RescindirContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$arLink = Sessao::read('link');

$stCaminho = CAM_GPC_TCEMG_INSTANCIAS."configuracao/";

$obForm = new Form;
$obForm->setAction ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$arFiltro = array();

if ($_REQUEST['inNumContrato']) {
    $arFiltro[] = " nro_contrato = " . $_REQUEST['inNumContrato'] ;
}
if ($_REQUEST['dtPublicacao']) {
    $arFiltro[] = " data_publicacao  = to_date('" .$_REQUEST['dtPublicacao'] ."','dd/mm/yyyy')";
}
if ($_REQUEST['dtInicial']) {
    $arFiltro[] = " data_inicio = to_date('". $_REQUEST['dtInicial'] ."','dd/mm/yyyy')";
}
if ($_REQUEST['dtFinal']) {
    $arFiltro[] = " data_final  = to_date('". $_REQUEST['dtFinal'] ."','dd/mm/yyyy')";
}
if ($_REQUEST['stObjContrato']) {
    $arFiltro[] = " objeto_contrato  ~* '". $_REQUEST['stObjContrato'] ."'";
}
if($stAcao == "excluir"){
    $aux = " (SELECT contrato_rescisao.cod_contrato FROM tcemg.contrato_rescisao
                WHERE contrato_rescisao.cod_contrato=contrato.cod_contrato
                AND contrato_rescisao.exercicio=contrato.exercicio
                AND contrato_rescisao.cod_entidade=contrato.cod_entidade";
    if($_REQUEST['dtRescisao']){
        $aux .= " AND contrato_rescisao.data_rescisao  = to_date('". $_REQUEST['dtRescisao'] ."','dd/mm/yyyy') ";
    }
    $aux .= ") IS NOT NULL ";
    
    $arFiltro[] = $aux;
}else{
    $arFiltro[] = " (SELECT contrato_rescisao.cod_contrato FROM tcemg.contrato_rescisao
                WHERE contrato_rescisao.cod_contrato=contrato.cod_contrato
                AND contrato_rescisao.exercicio=contrato.exercicio
                AND contrato_rescisao.cod_entidade=contrato.cod_entidade) IS NULL ";
}


if ( count( $arFiltro ) > 0 ) {
    $stFiltro = " where " .implode ( ' and ' , $arFiltro );
}

//Recupera Todos Contratos, com ou sem Filtro
include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContrato.class.php' );
$obTTCEMGContrato = new TTCEMGContrato;
$obTTCEMGContrato->recuperaContratoRescisao($rsRecordSet, $stFiltro, "cod_entidade, nro_contrato, exercicio");

$rsRecordSet->addFormatacao( 'vl_contrato', 'NUMERIC_BR');
$rsRecordSet->addFormatacao( 'valor_rescisao', 'NUMERIC_BR');

//*****************************************************//
// Define COMPONENTES DA LISTA
//*****************************************************//
$obLista = new Lista;
$obLista->setRecordSet( $rsRecordSet );
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Entidade');
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Contrato');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data Início');
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data Fim');
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Valor Contrato');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Objeto Contrato');
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

if($stAcao == "excluir"){
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo('Data Rescisão');
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo('Valor Rescisão');
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[nro_contrato]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "data_inicio" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "data_final" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "vl_contrato" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "objeto_contrato" );
$obLista->commitDado();

if($stAcao == "excluir"){
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "data_rescisao" );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "valor_rescisao" );
    $obLista->commitDado();
}

$obLista->addAcao();
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setAcao( "excluir" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?stAcao=$stAcao".Sessao::getId() );
} elseif ($stAcao == "rescindir") {
    $obLista->ultimaAcao->setAcao( "rescindir" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?stAcao=$stAcao".Sessao::getId() );
}

$obLista->ultimaAcao->addCampo("&inNumContrato"      , "nro_contrato" );
$obLista->ultimaAcao->addCampo("stExercicioContrato" , "exercicio"    );
$obLista->ultimaAcao->addCampo("inCodEntidade"       , "cod_entidade" );
$obLista->ultimaAcao->addCampo("inCodContrato"       , "cod_contrato" );
$obLista->commitAcao();
$obLista->show();
?>