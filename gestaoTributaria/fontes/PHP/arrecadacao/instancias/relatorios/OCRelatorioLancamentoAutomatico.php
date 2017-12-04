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

    * Pagina executada no frame oculto para retornar valores para o principal
    * Data de Criação   : 21/01/2015
    * @author Analista: Luciana
    * @author Desenvolvedor: Carolina Schwaab Marçal
    * @package URBEM
    * @subpackage Regra

    * $Id: OCRelatorioLancamentoAutomatico.php 63884 2015-10-29 12:01:23Z evandro $

    * Casos de uso: 

*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once CLA_MPDF;
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php"            );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "Licencas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//relatorioDeLancamentosAutomatico.rptdesign
$preview = new PreviewBirt(5,25,7);
$preview->setTitulo('Relatório de Lançamentos Automáticos');
$preview->setVersaoBirt( '2.5.0' );
$preview->setFormato('pdf');
$stNomeArquivo = "RelatorioDeLancamentoAutomatico_";


$boTransacao = new Transacao();
list($inCodGrupoCreditoInicial, $stExercicioInicial) = explode('/', $request->get('inCodGrupoInicio'));
list($inCodGrupoCreditoFinal, $stExercicioFinal) = explode('/', $request->get('inCodGrupoTermino'));

$stFiltro = "";
if ( $request->get('inCodGrupoInicio') != "" && $request->get('inCodGrupoTermino') == "" ) {
    $stFiltro .= " lancamento.cod_grupo = ".$inCodGrupoCreditoInicial." AND  lancamento.ano_exercicio= '".$stExercicioInicial."' AND ";
} else if ( $request->get('inCodGrupoInicio') == "" && $request->get('inCodGrupoTermino') != "" ) {
    $stFiltro .= " lancamento.cod_grupo = ".$inCodGrupoCreditoFinal."  AND  lancamento.ano_exercicio= '".$stExercicioFInal."' AND  ";
} else if ( $request->get('inCodGrupoInicio') != "" && $request->get('inCodGrupoTermino') != "" ) {
    $stFiltro .= " lancamento.cod_grupo BETWEEN ".$inCodGrupoCreditoInicial." AND ".$inCodGrupoCreditoFinal." AND lancamento.ano_exercicio= '". $stExercicioInicial. "' AND " ;
}

if ( $request->get('inNumInscricaoImobiliariaInicial') != "" && $request->get('inNumInscricaoImobiliariaFinal') == "" ) {
    $stFiltro .= " lancamento.inscricao_municipal = ".$request->get('inNumInscricaoImobiliariaInicial')." AND ";
} else if ( $request->get('inNumInscricaoImobiliariaInicial') == "" && $request->get('inNumInscricaoImobiliariaFinal') != "" ) {
    $stFiltro .= " lancamento.inscricao_municipal = ".$request->get('inNumInscricaoImobiliariaFinal')." AND ";
} else if ( $request->get('inNumInscricaoImobiliariaInicial') != "" && $request->get('inNumInscricaoImobiliariaFinal') != "" ) {
    $stFiltro .= " lancamento.inscricao_municipal BETWEEN ".$request->get('inNumInscricaoImobiliariaInicial')." AND ".$request->get('inNumInscricaoImobiliariaFinal')." AND ";
}

if ( $request->get('inNumInscricaoEconomicaInicial') != "" && $request->get('inNumInscricaoEconomicaFinal') == "" ) {
    $stFiltro .= " lancamento.inscricao_economica = ".$request->get('inNumInscricaoEconomicaInicial')." AND ";
} else if ( $request->get('inNumInscricaoEconomicaInicial') == "" && $request->get('inNumInscricaoEconomicaFinal') != "" ) {
    $stFiltro .= " lancamento.inscricao_economica = ".$request->get('inNumInscricaoEconomicaFinal')." AND ";
} else if ( $request->get('inNumInscricaoEconomicaInicial') != "" && $request->get('inNumInscricaoEconomicaFinal') != "" ) {
    $stFiltro .= " lancamento.inscricao_economica BETWEEN ".$request->get('inNumInscricaoEconomicaInicial')." AND ".$request->get('inNumInscricaoEconomicaFinal')." AND ";
}
   
if ( $request->get('inCodContribuinteInicial') != "" && $request->get('inCodContribuinteFinal') == "" ) {
    $stFiltro .= " lancamento.numcgm = '".$request->get('inCodContribuinteInicial')."' AND ";
} else if ( $request->get('inCodContribuinteInicial') == "" && $request->get('inCodContribuinteFinal') != "" ) {
    $stFiltro .= " lancamento.numcgm = '".$request->get('inCodContribuinteFinal')."' AND ";
} else if ( $request->get('inCodContribuinteInicial') != "" && $request->get('inCodContribuinteFinal') != "" ) {
    $stFiltro .= " lancamento.numcgm BETWEEN '".$request->get('inCodContribuinteInicial')."' AND '".$request->get('inCodContribuinteFinal')."' AND ";
}
   
if ($stFiltro) {
    $stFiltro = " WHERE ". substr ( $stFiltro, 0, strlen ($stFiltro) - 4 ) ;
}
$stFiltro .=' ORDER BY cod_lancamento,  numeracao, ordenacao';
$preview->addParametro( 'filtro', $stFiltro);
$preview->addParametro( 'tipo_filtro', $tipo_filtro );

//adicionada data de emissão no rodapé do relatório
$dtDataEmissao = date('d/m/Y');
$dtHoraEmissao = date('H:i');

//necessário codificar os caracteres especias em ascii para o birt interpretar corretamente
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;
$preview->addParametro( 'data_emissao', $stDataEmissao );

$preview->preview();

?>
