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
 * Arquivo oculto - Exportação arquivos Planejamento TCE/MG
 *
 * @category    Urbem
 * @package     TCE/MG
 * @author      Eduardo Schitz   <eduardo.schitz@cnm.org.br>
 * $Id: OCExportarFolhaPagamento.php 66544 2016-09-16 18:50:57Z evandro $
 */
set_time_limit(0);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_EXPORTADOR;
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConfigurarIDE.class.php';
include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ExportarFolhaPagamento" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

SistemaLegado::BloqueiaFrames();

$obExportador = new Exportador();
$obTEntidade = new TEntidade();

$arFiltro = Sessao::read('filtroRelatorio');
if( array_key_exists('stExercicioExportador',$arFiltro) ){
    $stExercicioFiltro = $arFiltro['stExercicioExportador'];
} else {
    $stExercicioFiltro = Sessao::getExercicio();
}

// Busca a entidade definida como prefeitura na configuração do orçamento
$stCampo   = "valor";
$stTabela  = "administracao.configuracao";
$stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
$stFiltro .= "   AND parametro = 'cod_entidade_prefeitura' ";

$inCodEntidadePrefeitura = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro,$boTransacao);
        
// Se não foi selecionada a entidade definida como prefeitura
// ao executar as consultas, automaticamente é adicionado o "_" + cod_entidade selecionada
$arSchemasRH = array();
$obTEntidade->recuperaSchemasRH($rsSchemasRH);
while (!$rsSchemasRH->eof()) {
    $arSchemasRH[] = $rsSchemasRH->getCampo("schema_nome");
    $rsSchemasRH->proximo();
}
Sessao::write('arSchemasRH', $arSchemasRH, true);

$stEntidades = $arFiltro['inCodEntidade'];
$inMes = $arFiltro['inMes'];

SistemaLegado::retornaInicialFinalMesesPeriodicidade($arDatasInicialFinal, '', $inMes, $stExercicioFiltro);

foreach($arFiltro['arArquivosSelecionados'] AS $stArquivo){
    $obExportador->addArquivo($stArquivo);
    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $arNomArquivo = explode('.',$stArquivo);    
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/folhaPagamento/".Sessao::getExercicio()."/".$arNomArquivo[0].".inc.php");
}

if ( $arFiltro['stTipoExport'] == 'compactados'){
    $rsRecordSet = new RecordSet();
    $obTTCEMGConfigurarIDE = new TTCEMGConfigurarIDE();
    $obTTCEMGConfigurarIDE->setDado('exercicio', $stExercicioFiltro);
    $obTTCEMGConfigurarIDE->setDado('entidades', implode(',',$stEntidades) );    
    $obErro = $obTTCEMGConfigurarIDE->recuperaDadosExportacaoFolha($rsRecordSet,"","",$boTransacao);    
    
    if ($rsRecordSet->getNumLinhas() > 0) {
        $inCodMunicipio = str_pad($rsRecordSet->getCampo('codmunicipio'), 5, '0', STR_PAD_LEFT);
        $inCodOrgao = str_pad($rsRecordSet->getCampo('codorgao'), 2, '0', STR_PAD_LEFT);
        $inMes = str_pad($inMes, 2, '0', STR_PAD_LEFT);
    } else {
        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao", "É necessário configurar a IDE para gerar um arquivo compactado.", "", "aviso", Sessao::getId(), "../");
        die;
    }
    $obExportador->setNomeArquivoZip('FLPG_'.$inCodMunicipio.'_'.$inCodOrgao.'_'.$inMes.'_'.$stExercicioFiltro.'.zip');
}
$obExportador->show();
SistemaLegado::LiberaFrames();

?>