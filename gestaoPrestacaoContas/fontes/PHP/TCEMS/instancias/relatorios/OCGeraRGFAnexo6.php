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
    * Página de Relatório RGF Anexo6
    * Data de Criação   : 15/02/2008

    * @author Analista: Valtair Lacerda
    * @author Desenvolvedor: Leopoldo Barreiro

    * @ignore

    $Revision: $
    $Name$
    $Author: $
    $Date: $

    * Casos de uso :
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , $_REQUEST['stExercicio'] );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, " and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$obErro = new Erro();

$preview = new PreviewBirt(6,57,3);
$preview->setTitulo('Demonstrativo dos Restos a Pagar');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel ( true );

$stDataInicial = "01/01/".$_REQUEST['stExercicio'];

if ( strtolower($_REQUEST['stTipoRelatorio']) == 'ultimoquadrimestre'  ) {
    switch ($_REQUEST['cmbQuadrimestre']) {
        case 3:
            $stDataFinal = '31/12';
            $stIntervalo = '3º Quadrimestre de ' . $_REQUEST['stExercicio'];
        break;
    }
    $nuPeriodo = $_REQUEST['cmbQuadrimestre'] ;
} elseif ( strtolower($_REQUEST['stTipoRelatorio']) == 'bimestre' ) {
    switch ($_REQUEST['cmbBimestre']) {
        case 1:
            $stDataFinal = '29/02';
            $stIntervalo = '1º Bimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 2:
            $stDataFinal = '30/04';
            $stIntervalo = '2º Bimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 3:
            $stDataFinal = '30/06';
            $stIntervalo = '3º Bimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 4:
            $stDataFinal = '31/08';
            $stIntervalo = '4º Bimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 5:
            $stDataFinal = '31/10';
            $stIntervalo = '5º Bimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 6:
            $stDataFinal = '31/12';
            $stIntervalo = '6º Bimestre de ' . $_REQUEST['stExercicio'];
        break;
    }
    $nuPeriodo = $_REQUEST['cmbBimestre'] ;
} elseif ( strtolower($_REQUEST['stTipoRelatorio']) == 'quadrimestre' ) {
    switch ($_REQUEST['cmbQuadrimestre']) {
        case 1:
            $stDataFinal = '30/04';
            $stIntervalo = '1º Quadrimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 2:
            $stDataFinal = '31/08';
            $stIntervalo = '2º Quadrimestre de ' . $_REQUEST['stExercicio'];
        break;
        case 3:
            $stDataFinal = '31/12';
            $stIntervalo = '3º Quadrimestre de ' . $_REQUEST['stExercicio'];
        break;
    }
    $nuPeriodo = $_REQUEST['cmbQuadrimestre'] ;
} elseif ( strtolower($_REQUEST['stTipoRelatorio']) == 'semestre' or  strtolower($_REQUEST['stTipoRelatorio']) == 'ultimosemestre' ) {
    switch ($_REQUEST['cmbSemestre']) {
        case 1:
            $stDataFinal = '30/06';
            $stIntervalo = '1º Semestre de ' . $_REQUEST['stExercicio'];
        break;
        case 2:
            $stDataFinal = '31/12';
            $stIntervalo = '2º Semestre de ' . $_REQUEST['stExercicio'];
        break;
    }
    $nuPeriodo = $_REQUEST['cmbSemestre'] ;
}

$stDataFinal = $stDataFinal . '/' . $_REQUEST['stExercicio'];

$preview->addParametro( 'entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
$preview->addParametro( 'data_inicio', $stDataInicial );
$preview->addParametro( 'data_fim', $stDataFinal );
$preview->addParametro( 'exercicio', $_REQUEST['stExercicio'] );
$preview->addParametro( 'intervalo', $stIntervalo );
$preview->addParametro( 'poder' , 'Legislativo' );

$rsEntidade->setPrimeiroElemento();
while (!$rsEntidade->eof()) {
    $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));
    $rsEntidade->proximo();
}
$preview->addAssinaturas(Sessao::read('assinaturas'));
if( !$obErro->ocorreu() )
    $preview->preview();
else
    SistemaLegado::alertaAviso("FLModelosRGF.php?'.Sessao::getId().&stAcao=$stAcao", $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
