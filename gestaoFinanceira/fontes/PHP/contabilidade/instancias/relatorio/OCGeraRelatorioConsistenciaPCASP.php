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
    * Página que gera o Relatório Consistência PCASP
    * Data de Criação   : 25/09/2013

    * @author Analista:  Sergio Luiz dos Santos
    * @author Desenvolvedor: Jean Silva

    * @ignore

    * $Id: FLConsistenciaPCASP.php 52880 2012-08-28 19:15:58Z tonismar $

    * Casos de uso: uc-02.02.22
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioBalanceteVerificacao.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php" );

//-----------------------------------------------------------------
// PARTE GERA RELATORIO
$obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
$obRContabilidadePlanoContaAnalitica->setExercicio ( Sessao::getExercicio() );
$obRContabilidadePlanoContaAnalitica->recuperaMascaraConta( $stMascara );

//seta elementos do filtro
$stFiltro = "";

$arFiltro = Sessao::read('filtroRelatorio');

//seta elementos do filtro para ENTIDADE
if ($_REQUEST['inCodEntidade'] != "") {
    $stFiltro .= "\n cod_entidade IN  (";
    foreach ($_REQUEST['inCodEntidade'] as $key => $valor) {
        $stFiltro .= $valor." , ";
    }
    $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 2 ) . ") AND ";
}

if ($stFiltro) $stFiltro = substr($stFiltro, 0, strlen($stFiltro)-4);

$stFiltro = trim($stFiltro);

$preview = new PreviewBirt(2,9,16);
$preview->setVersaoBirt( '2.5.0' );
$preview->setNomeRelatorio( 'consistenciaPCASP' );
$preview->setTitulo("Consistência PCASP");

$ano = substr($_REQUEST['stDataInicial'], 6, 4);
$exercicio = ($ano==Sessao::getExercicio()) ? Sessao::getExercicio() : $ano;
$preview->addParametro( 'dt_inicial', $_REQUEST['stDataInicial'] );
$preview->addParametro( 'dt_final', $_REQUEST['stDataFinal'] );
$preview->addParametro( 'filtro', $stFiltro );
$preview->addParametro( 'estilo', $_REQUEST['stEstiloConta'] );
$preview->addParametro( 'exercicio', $exercicio );
$preview->preview();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
