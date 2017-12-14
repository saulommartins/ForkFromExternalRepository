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
    * Página de geração do relatório de Balanço Patrimonial
    * Data de Criação   : 05/08/2013

    * @author Analista Tonismar Bernardo
    * @author Desenvolvedor Davi Aroldi

    * @ignore
    
    $Id: OCGeraBalancoPatrimonial.php 62473 2015-05-13 13:25:59Z michel $

    * Casos de uso: uc-02.03.18

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php");

$preview = new PreviewBirt(2,9,12);

$preview->setVersaoBirt( '4.4.0' );
$preview->setNomeRelatorio( 'balancoPatrimonial' );
$preview->setTitulo("Balanço Patrimonial");

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview->addParametro ( 'porcentagem', $_REQUEST['flPct'] );

$preview->addParametro ( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade'] ) );

if ( count($_REQUEST['inCodEntidade']) == 1 ) {
     $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    $inCodEntidadePrefeitura = SistemaLegado::pegaDado('valor','administracao.configuracao'," WHERE parametro = 'cod_entidade_prefeitura' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 8 ");
    $obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade = ".$inCodEntidadePrefeitura );
    while ( !$rsEntidade->eof() ) {
        $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
        $rsEntidade->proximo();
    }
}

list($dia,$mes,$ano) = explode('/', $_POST['stDataFinal'] );

switch ($mes) {
    case 1:
       $stPeriodo =  'Janeiro';
        break;
    case 2:
        $stPeriodo = 'Fevereiro';
        break;
    case 3:
        $stPeriodo = 'Março';
        break;
    case 4:
       $stPeriodo =  'Abril';
        break;
    case 5:
       $stPeriodo =  'Maio';
        break;
    case 6:
      $stPeriodo =   'Junho';
        break;
    case 7:
       $stPeriodo =  'Julho';
        break;
    case 8:
       $stPeriodo =  'Agosto';
        break;
    case 9:
      $stPeriodo =   'Setembro';
        break;
    case 10:
      $stPeriodo =   'Outubro';
        break;
    case 11:
       $stPeriodo =  'Novembro';
        break;
    case 12:
       $stPeriodo =  'Dezembro';
        break;
}
if ( ($_POST['stDataInicial'] == "01/01/".Sessao::getExercicio()) && ($_POST['stDataFinal'] == "31/12/".Sessao::getExercicio()) ) {
    $stPeriodo = "ANO - ".Sessao::getExercicio();
}

$preview->addParametro( "periodo"            , $stPeriodo );
$preview->addParametro( 'dt_inicial'         , $_POST['stDataInicial'] );
$preview->addParametro( 'dt_final'           , $_POST['stDataFinal'] );
$preview->addParametro( 'cod_entidades'      , implode(',', $_POST['inCodEntidade']) );
$preview->addParametro( 'data_emissao'       , date('d/m/Y') );
$preview->addParametro( 'data_inicial_nota'  , implode('-',array_reverse(explode('/', $_POST['stDataInicial']))));
$preview->addParametro( 'data_final_nota'    , implode('-',array_reverse(explode('/', $_POST['stDataFinal']))));

$preview->preview();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
