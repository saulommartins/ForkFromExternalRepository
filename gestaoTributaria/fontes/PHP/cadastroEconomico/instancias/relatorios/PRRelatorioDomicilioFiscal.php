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


    * Filtro para Relatorio de Domicilio Fiscal
    * Data de Criação   : 09/09/2014    
    * @author Desenvolvedor: Evandro Melos
    * @package URBEM    

    * $Id: PRRelatorioDomicilioFiscal.php 59807 2014-09-12 12:31:14Z evandro $

*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GT_CEM_MAPEAMENTO."TRelatorioDomicilioFiscal.class.php";
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
include_once CLA_MPDF;

$stPrograma      = "RelatorioDomicilioFiscal";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$boTransacao = new Transacao();
$obTRelatorioDomicilioFiscal = new TRelatorioDomicilioFiscal();
$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "", "", $boTransacao);

foreach ($rsEntidade->getElementos() as $entidades) {
    $arCodEntidades[]  = $entidades['cod_entidade'];
}

sort($arCodEntidades);
$inCodEntidades = implode(",", $arCodEntidades);

$arCodAtividade_1 = explode("§", $request->get('inCodAtividade_1'));
$arCodAtividade_2 = explode("§", $request->get('inCodAtividade_2'));

$stFiltro = " WHERE 1=1 ";
$stOrdem  = "ORDER BY inscricao_economica";
$stInscricaoEconomica = $request->get('inInscricaoEconomica');
$inNumCGM             = $request->get('inNumCGM');
$inNumLogradouro      = $request->get('inNumLogradouro');
$stChaveAtividade     = $request->get('stChaveAtividade');
$inCodAtividade_1     = $arCodAtividade_1[1];
$inCodAtividade_2     = $arCodAtividade_2[1];

if ( $stInscricaoEconomica ) {
    $stFiltro .= " AND cadastro_economico.inscricao_economica = ".$stInscricaoEconomica." ";
}
if ( $inNumCGM ) {
    $stFiltro .= " AND sw_cgm_pessoa_juridica.numcgm = ".$inNumCGM." ";
}
if ( $inNumLogradouro ) {
    $stFiltro .= " AND sw_nome_logradouro.cod_logradouro = ".$inNumLogradouro." ";
}
if ( $inCodAtividade_2 ) {
    $stFiltro .= " AND atividade.cod_atividade = ".$inCodAtividade_2." ";   
}elseif ($inCodAtividade_1) {
    $stFiltro .= " AND atividade.cod_atividade = ".$inCodAtividade_1." ";   
}

$obTRelatorioDomicilioFiscal->recuperaRelatorioDomicilioFiscal($rsDadosRelatorio, $stFiltro, $stOrdem, $boTransacao );

$arDados['arDomicilioFiscal'] = $rsDadosRelatorio->getElementos();

Sessao::write('arDomicilioFiscal', $arDados );
Sessao::write('inCodEntidades'   , $inCodEntidades );

SistemaLegado::LiberaFrames(true,true);
$stCaminho = CAM_GT_CEM_INSTANCIAS."relatorios/OCGeraRelatorioDomicilioFiscal.php";

SistemaLegado::mudaFramePrincipal($stCaminho);

?>