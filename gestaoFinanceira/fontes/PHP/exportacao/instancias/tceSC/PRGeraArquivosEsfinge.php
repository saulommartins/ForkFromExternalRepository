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
    * Página de Formulario de Manter Adjudicacao
    * Data de Criação: 23/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: andre.almeida $
    $Date: 2007-06-27 10:49:34 -0300 (Qua, 27 Jun 2007) $

    * Casos de uso: uc-03.05.20
*/

/*
    $Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_EXPORTADOR );
include_once 'PRGeraArquivosEsfingeLOA.php';
include_once 'PRGeraArquivosEsfingeExecucaoOrcamentaria.php';
include_once 'PRGeraArquivosEsfingeRegContabeis.php';
include_once 'PRGeraArquivosEsfingeContratos.php';
include_once 'PRGeraArquivosEsfingeConvenios.php';
include_once 'PRGeraArquivosEsfingeLicitacoes.php';
include_once 'PRGeraArquivosEsfingePessoal.php';
include_once 'PRGeraArquivosEsfingeConcursos.php';
include_once 'PRGeraArquivosEsfingePlanoCargos.php';
include_once 'PRGeraArquivosEsfingePessoal.php';

function bimestre($stExercicio, $inBemestre, &$stDataInicial, &$stDataFinal)
{
   $inNumDiasFevereiro = date('t', strtotime('02/01/'.$stExercicio));
   switch ($inBemestre) {
    case 1:
        $arDatas[0] = '01/01/'.$stExercicio;
        $arDatas[1] = $inNumDiasFevereiro.'/02/'.$stExercicio;
    break;
    case 2:
        $arDatas[0] = '01/03/'.$stExercicio;
        $arDatas[1] = '30/04/'.$stExercicio;
    break;
    case 3:
        $arDatas[0] = '01/05/'.$stExercicio;
        $arDatas[1] = '30/06/'.$stExercicio;
    break;
    case 4:
        $arDatas[0] = '01/07/'.$stExercicio;
        $arDatas[1] = '31/08/'.$stExercicio;
    break;
    case 5:
        $arDatas[0] = '01/09/'.$stExercicio;
        $arDatas[1] = '31/10/'.$stExercicio;
    break;
    case 6:
        $arDatas[0] = '01/11/'.$stExercicio;
        $arDatas[1] = '31/12/'.$stExercicio;
    break;
   }
   $stDataInicial = $arDatas[0];
   $stDataFinal = $arDatas[1];
}

$jsOnLoad = "LiberaFrames(true,false);";

$obExportador = new Exportador;

bimestre($sessao->filtro['stExercicio'], $sessao->filtro['bimestre'] , $stDataInicial, $stDataFinal);

if ( ($sessao->filtro['rdoGeraTodos'] == 'sim') || ($sessao->filtro['chkLOA'] == 'on') ) {
    geraArquivosLoa( $obExportador , $stDataInicial, $stDataFinal);
}
if ( ($sessao->filtro['rdoGeraTodos'] == 'sim') || ($sessao->filtro['chkExecOrcamentaria'] == 'on') ) {
    geraArquivosExecOrcamentaria( $obExportador, $stDataInicial, $stDataFinal );
}
if ( ($sessao->filtro['rdoGeraTodos'] == 'sim') || ($sessao->filtro['chkRegContabeis'] == 'on') ) {
    geraArquivosRegContabeis( $obExportador, $stDataInicial, $stDataFinal );
}
if ( ($sessao->filtro['rdoGeraTodos'] == 'sim') || ($sessao->filtro['chkContratos'] == 'on') ) {
    geraArquivosContratos( $obExportador, $stDataInicial, $stDataFinal );
}
if ( ($sessao->filtro['rdoGeraTodos'] == 'sim') || ($sessao->filtro['chkConvenios'] == 'on') ) {
    geraArquivosConvenios( $obExportador, $stDataInicial, $stDataFinal );
}
if ( ($sessao->filtro['rdoGeraTodos'] == 'sim') || ($sessao->filtro['chkLicitacao'] == 'on') ) {
    geraArquivosLicitacoes( $obExportador, $stDataInicial, $stDataFinal );
}
if ( ($sessao->filtro['rdoGeraTodos'] == 'sim') || ($sessao->filtro['chkConcursos'] == 'on') ) {
    geraArquivosConcursos( $obExportador, $stDataInicial, $stDataFinal );
}
if ( ($sessao->filtro['rdoGeraTodos'] == 'sim') || ($sessao->filtro['chkPlanoCargos'] == 'on') ) {
    geraArquivosPlanoCargos( $obExportador, $stDataInicial, $stDataFinal );
}
if ( ($sessao->filtro['rdoGeraTodos'] == 'sim') || ($sessao->filtro['chkPessoal'] == 'on') ) {
    geraArquivosPessoal( $obExportador, $stDataInicial, $stDataFinal );
}

if ( ($sessao->filtro['rdoGeraTodos'] == 'sim') || ($sessao->filtro['chkPessoal'] == 'on') ) {
    geraArquivosPessoal( $obExportador, $stDataInicial, $stDataFinal );
}

$obExportador->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
