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
    * Página de Filtro para relatorico de Fluxo de Caixa
    * Data de Criação   : 19/07/2013
    * @author Analista: Valtair
    * @author Desenvolvedor: Evandro Melos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "DemostrativoFluxoCaixa";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgGera     = "OCGeraRelatorio".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

//Define Birt
//relatorioFluxoCaixa.rptdesign
$preview = new PreviewBirt(2,9,13);
$preview->setTitulo('Relatorio Demostrativo de Fluxo Caixa');
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);

$obTOrcamentoEntidade = new TOrcamentoEntidade;
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio()  );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

if (count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
    if (preg_match( "/prefeitura.*/i", $rsEntidade->getCampo('nom_cgm')) || $boConfirmaFundo > 0) {
        $preview->addParametro( 'poder' , 'Executivo' );
    } else {
        $preview->addParametro( 'poder' , 'Legislativo' );
    }
} else {
    while (!$rsEntidade->eof()) {
        if (preg_match( "/prefeitura.*/i", $rsEntidade->getCampo('nom_cgm')) ) {
            $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
            $preview->addParametro( 'poder' , 'Executivo' );
            break;
        }
        $rsEntidade->proximo();
    }
}

$stCodEntidades = implode(',', $_POST['inCodEntidade']);

//Data Inicial do exercicio anterior
list($dia,$mes,$ano) = explode('/', $_POST['stDataInicial'] );
$stDataInicialAnterior = $dia . "/" . $mes . "/". ($ano -1);
//Data Final do exercicio anterior
list($dia,$mes,$ano) = explode('/', $_POST['stDataFinal'] );
$stDataFinalAnterior = $dia . "/" . $mes . "/". ($ano -1);

$preview->addParametro("exercicio"								, Sessao::getExercicio() 					);
$preview->addParametro("cod_entidade"							, $stCodEntidades 							);
$preview->addParametro("dt_inicial"                             , $_POST['stDataInicial']					);
$preview->addParametro("dt_final"                               , $_POST['stDataFinal']						);
$preview->addParametro("exercicio_anterior"                     , (Sessao::getExercicio() - 1)				);
$preview->addParametro("dt_inicial_anterior"                    , $stDataInicialAnterior					);
$preview->addParametro("dt_final_anterior"                      , $stDataFinalAnterior						);
$preview->addParametro('data_inicial_nota',implode('-',array_reverse(explode('/', $_POST['stDataInicial']))));
$preview->addParametro('data_final_nota'  ,implode('-',array_reverse(explode('/', $_POST['stDataFinal']))));

$preview->addAssinaturas(Sessao::read('assinaturas'));

$preview->preview();

?>
