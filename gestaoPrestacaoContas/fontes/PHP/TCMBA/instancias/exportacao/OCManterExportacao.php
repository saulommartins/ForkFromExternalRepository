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
    * Página Oculta - Exportação Arquivos GF

    * Data de Criação   : 18/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Id: OCManterExportacao.php 64077 2015-11-27 19:50:48Z jean $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio().'/TTBAConfiguracao.class.php';
include_once(CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php" );
include_once CLA_EXPORTADOR;

$stAcao = $request->get('stAcao');

$arFiltro = Sessao::read('filtroRelatorio');

list( $inDia,$inMes,$inAno ) = explode( '/', $arFiltro['stDataFinal'] ); 
$stEntidades     = $arFiltro['inCodEntidade'];
$stTipoDocumento = "TCM_BA";

$obExportador    = new Exportador();

$obTMapeamento = new TTBAConfiguracao();
$obTMapeamento->setDado( 'exercicio', Sessao::getExercicio() );
$obTMapeamento->setDado( 'cod_entidade', $stEntidades );
$obTMapeamento->recuperaUnidadeGestoraEntidade( $rsEntidade );
$inCodUnidadeGestora = $rsEntidade->getCampo('cod_unidade_gestora');

$stDataInicial = $arFiltro['stDataInicial'];
$stDataFinal   = $arFiltro['stDataFinal'];

$stTipoPeriodicidade = $arFiltro['stTipoPeriodicidade'];

Sessao::write('cod_unidade_gestora',$rsEntidade->getCampo('cod_unidade_gestora'));
Sessao::write('nom_unidade'        ,$rsEntidade->getCampo('nom_entidade'));

if (SistemaLegado::pegaDado('parametro','administracao.configuracao', " WHERE valor = '".$stEntidades."' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 8") == 'cod_entidade_prefeitura') {
    $stEntidadeRH = '';
} else {
    $stEntidadeRH = "_".$stEntidades;
}

$arLogErros = array();

foreach ($arFiltro["arArquivosSelecionados"] as $stArquivo) {
    $obExportador->addArquivo($stArquivo);
    $obExportador->roUltimoArquivo->setTitulo(substr($stArquivo,0,strpos($stArquivo,'.txt')));
    $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);
   
    include (CAM_GPC_TCMBA_INSTANCIAS."layout_arquivos/".Sessao::getExercicio()."/".substr($stArquivo,0,strpos($stArquivo,'.txt')).".inc.php");
    $arRecordSet = null;
}

if ($arFiltro['stTipoExport'] == 'compactados') {
    $obExportador->setNomeArquivoZip('ExportacaoArquivosPrincipais.zip');
}

$stTemPeriodicidade = SistemaLegado::pegaConfiguracao("tcmba_tipo_periodicidade_patrimonio", 45, Sessao::getExercicio());

if ($stTemPeriodicidade != $arFiltro["stTipoPeriodicidade"]) {
    $obRConfiguracaoConfiguracao = new RConfiguracaoConfiguracao();
    $obRConfiguracaoConfiguracao->setCodModulo( 45 );
    $obRConfiguracaoConfiguracao->setParametro( 'tcmba_tipo_periodicidade_patrimonio' );
    $obRConfiguracaoConfiguracao->setValor    ( $arFiltro["stTipoPeriodicidade"] );
    $obRConfiguracaoConfiguracao->alterar($boTransacao);
}

if(count($arLogErros)>0 && $arFiltro['stInformativoErro']=='Sim'){
    $rsLogErros = new RecordSet();
    $rsLogErros->preenche($arLogErros);

    $obExportador->addArquivo('URBEM_Informativo_de_Erros.txt');
    $obExportador->roUltimoArquivo->setTitulo('Informativo de Erros para Usuário URBEM');
    $obExportador->roUltimoArquivo->addBloco($rsLogErros);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_arquivo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(35);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("erro");
}

$obExportador->show();
SistemaLegado::LiberaFrames();
ob_end_flush();

?>