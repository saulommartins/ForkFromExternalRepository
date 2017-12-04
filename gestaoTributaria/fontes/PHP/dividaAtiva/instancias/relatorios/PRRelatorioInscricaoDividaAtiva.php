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


    * Processamento para Relatorio de Inscricao Divida Ativa
    * Data de Criação   : 12/09/2014    
    * @author Desenvolvedor: Evandro Melos
    * @package URBEM    

    * $Id: PRRelatorioInscricaoDividaAtiva.php 61417 2015-01-15 13:29:03Z evandro $
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_MPDF;

include_once CAM_GT_DAT_MAPEAMENTO."TRelatorioInscricaoDividaAtiva.class.php";
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$stPrograma      = "RelatorioInscricaoDividaAtiva";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

include_once $pgJs;

$boTransacao = new Transacao();

$obTRelatorioInscricaoDividaAtiva = new TRelatorioInscricaoDividaAtiva();

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "", "", $boTransacao);

foreach ($rsEntidade->getElementos() as $entidades) {
    $arCodEntidades[]  = $entidades['cod_entidade'];
}

sort($arCodEntidades);
$inCodEntidades = implode(",", $arCodEntidades);

$stFiltro = "\n WHERE 1=1  \n";

if (isset($_REQUEST["stDataInicial"])&&isset($_REQUEST["stDataFinal"])) {
    $stFiltro .= " AND divida_ativa.dt_inscricao BETWEEN TO_DATE('".$_REQUEST['stDataInicial']."','dd/mm/yyyy') AND TO_DATE('".$_REQUEST['stDataFinal']."','dd/mm/yyyy') \n";
}

if (isset($_REQUEST["stDataInscricao"])) {
    $stFiltro .= " AND divida_ativa.dt_inscricao = TO_DATE('".$_REQUEST["stDataInscricao"]."','dd/mm/yyyy') \n";
}

if (isset($_REQUEST["inNumeroParcelamento"])) {
    $stFiltro .= " AND divida_parcelamento IN (".$_REQUEST["inNumeroParcelamento"].") \n";
}

if (isset($_REQUEST["inNumInscricao"])) {
    $stFiltro .= " AND divida_ativa.cod_inscricao IN (".$_REQUEST["inNumInscricao"].") \n";
}

if (isset($_REQUEST['inNumModalidade'])) {
    $stFiltro .= " AND modalidade.cod_modalidade = ".$_REQUEST['inNumModalidade']." \n";
}

if ( $_REQUEST['inCodCredito'] ) {
    $arCredito = explode ( '.', $_REQUEST['inCodCredito'] );
    $stFiltro .= " AND parcela_origem.cod_credito  = ".$arCredito[0]." \n";
    $stFiltro .= " AND parcela_origem.cod_especie  = ".$arCredito[1]." \n";
    $stFiltro .= " AND parcela_origem.cod_especie  = ".$arCredito[2]." \n";
    $stFiltro .= " AND parcela_origem.cod_natureza = ".$arCredito[3]." \n";
}

if ( $_REQUEST['inCGM'] ) {
    //Validacao para nao chegar no limite da URL que passa todos os CGM
    if ($_REQUEST['inCGM'] == "muitos" ) {
        $inCgm = Sessao::read('inCGM');
        $stFiltro .= " AND divida_cgm.numcgm IN (".$inCgm.") \n";
    }else{
        $stFiltro .= " AND divida_cgm.numcgm IN (".$_REQUEST['inCGM'].") \n";        
    }
}

if ( $_REQUEST['inCodImovelInicial'] ) {
    $stFiltro .= " AND divida_imovel.inscricao_municipal >= ".$_REQUEST['inCodImovelInicial']." \n";
}

if ($_REQUEST['inCodImovelFinal']) {
    $stFiltro .= " AND divida_imovel.inscricao_municipal <= ".$_REQUEST['inCodImovelFinal']." \n";
}

if ( $_REQUEST['inNumInscricaoEconomicaInicial'] ) {
    $stFiltro .= " AND divida_empresa.inscricao_economica >= ".$_REQUEST['inNumInscricaoEconomicaInicial']." \n";
}

if ( $_REQUEST['inNumInscricaoEconomicaFinal'] ) {
    $stFiltro .= " AND divida_empresa.inscricao_economica <= ".$_REQUEST['inNumInscricaoEconomicaFinal']." \n";   
}

if($request->get('inCodGrupo')){
    $arTMP = explode( "/", $request->get('inCodGrupo') );
    $stFiltro .= " AND grupo_credito.cod_grupo     = ".$arTMP[0]."
                   AND grupo_credito.ano_exercicio = '".$arTMP[1]."' \n";
}

$stOrdem = "
  GROUP BY inscricao_origem  
         , divida_ativa.exercicio
         , imposto
         , livro
         , folha
         , ida

ORDER BY inscricao_origem, ida";

//Validacao para mostrar o campo de cgm na coluna do relatorio INSCRICAO ORIGEM se o usuario nao selecionar inscricao municipal ou economica
if ( $_REQUEST['inCodImovelInicial'] || $_REQUEST['inCodImovelFinal'] || $_REQUEST['inNumInscricaoEconomicaInicial'] || $_REQUEST['inNumInscricaoEconomicaFinal'] ) {
    $obTRelatorioInscricaoDividaAtiva->setDado( 'mostrar_cgm' ,false );
}else{
    $obTRelatorioInscricaoDividaAtiva->setDado( 'mostrar_cgm' ,true );
}

if($_REQUEST['stAcao'] == 'incluir'){
    $stFiltro .= " AND parcelamento.timestamp BETWEEN TO_TIMESTAMP('".$_REQUEST['stPrimeiroTimestamp']."','YYYY-MM-DD HH24:MI:SS')::TIMESTAMP WITHOUT TIME ZONE 
                                                 AND  TO_TIMESTAMP('".$_REQUEST['stUltimoTimestamp']."','YYYY-MM-DD HH24:MI:SS')::TIMESTAMP WITHOUT TIME ZONE";    
}

$obTRelatorioInscricaoDividaAtiva->recuperaRelatorioInscricaoDividaAtiva($rsInscricoes, $stFiltro, $stOrdem, $boTransacao );

$arDados['arDados'] = $rsInscricoes->getElementos();

SistemaLegado::BloqueiaFrames('true','false');
//Gerando o relatorio
set_time_limit(0);
ini_set('memory_limit', '2G');
$obMPDF = new FrameWorkMPDF(5,33,7);
$obMPDF->setCodEntidades ($inCodEntidades);
$obMPDF->setNomeRelatorio("Relatorio de Inscricoes da Divida Ativa");
$obMPDF->setFormatoFolha ("A4");
//Forcar o dowload do arquivo devido ao tamanho do relatorio
$obMPDF->setTipoSaida("F");

$obMPDF->setConteudo($arDados);

$obMPDF->gerarRelatorio();

//echo "<script>window.location.href = '".CAM_FW_TMP.$obMPDF->getDownloadNomeRelatorio()."';</script>";
echo "<script>
        window.open('".CAM_FW_TMP.$obMPDF->getDownloadNomeRelatorio()."','_parent');        
    </script>";

if($_REQUEST['stAcao'] == 'incluir'){
    //quando vier da ACAO de inscricao da divida ativa
    $stCaminho = CAM_GT_DAT_INSTANCIAS."inscricao/FLManterInscricao.php?".Sessao::getId()."&stAcao=incluir";
    SistemaLegado::alertaAviso( $stCaminho,"Inscricao de Dívida Ativa", "incluir","aviso", Sessao::getId(), "../");    
}else{
    //quando vier do RELATORIO da inscricao da divida ativa
    $stCaminho = CAM_GT_DAT_INSTANCIAS."relatorios/FLRelatorioInscricaoDividaAtiva.php?".Sessao::getId()."&stAcao=emitir";
    SistemaLegado::alertaAviso( $stCaminho,"Relatório de Dívida Ativa", "incluir","aviso", Sessao::getId(), "../");    
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
