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

    * Página de Relatório de Demonstrativo de Gastos com Pessoal
    * Data de Criação   : 09/07/2014
    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal
    * @ignore
    *   
    * $Id: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php" );

$preview = new PreviewBirt(6,55,1);
$preview->setTitulo('Relatorio do Quadro Demonstrativo dos Gastos com Pessoal');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel( true );

$stNomeArquivo = "Demonstrativo_gastos_pessoal_";

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (1,2,3)" );


$stNomeEntidade = '';

while (!$rsEntidade->eof()) {
    if ( strpos( strtolower($rsEntidade->getCampo('nom_cgm')),'prefeitura') > -1 ) {
        $stNomeEntidade = $rsEntidade->getCampo('nom_cgm');
        break;
    }
    $rsEntidade->proximo();
}
if ($stNomeEntidade == '') {
   $rsEntidade->setPrimeiroElemento();
   $stNomeEntidade = $rsEntidade->getCampo('nom_cgm');
}

if ( count($_REQUEST['inCodEntidade']) > 0 ) {
    $preview->addParametro( 'nom_entidade', $stNomeEntidade );
} else {
    $preview->addParametro( 'nom_entidade', '' );
}

if ( preg_match( "/prefeitura/i", $rsEntidade->getCampo( 'nom_cgm' ) ) || ( count($_REQUEST['inCodEntidade']) > 1 ) ) {
    $preview->addParametro( 'poder' , 'Executivo' );
} elseif ( preg_match( "/c[âa]mara/i", $rsEntidade->getCampo( 'nom_cgm' ) ) ) {
    $preview->addParametro( 'poder' , 'Legislativo' );
}
$stMes= explode("/",$_REQUEST["stDataInicial"]);

switch ($stMes[1]){
    case 01: $stMes = 'JANEIRO';
          break;
    case 02: $stMes = 'FEVEREIRO';
         break;
    case 03: $stMes = 'MARÇO';
          break;
    case 04: $stMes = 'ABRIL';
         break;  
    case 05: $stMes = 'MAIO';
          break;
    case 06: $stMes = 'JUNHO';
         break;    
    case 07: $stMes = 'JULHO';
          break;
    case 08: $stMes = 'AGOSTO';
         break;
    case 09: $stMes = 'SETEMBRO';
          break;
    case 10: $stMes = 'OUTUBRO';
         break;
    case 11: $stMes = 'NOVEMBRO';
         break;
    case 12: $stMes = 'DEZEMBRO';
         break;
}


if ($_REQUEST['inTipoPeriodo'] == 'Bimestre') {
    switch( $_REQUEST['cmbBimestre'] ):
    case 1:
        $preview->addParametro( 'data_ini', '01/01/'.Sessao::getExercicio() );
        if ( (Sessao::getExercicio() % 4) == 0 ) {
            $preview->addParametro( 'data_fim', '29/02/'.Sessao::getExercicio() );
        } else {
            $preview->addParametro( 'data_fim', '28/02/'.Sessao::getExercicio() );
        }
        $preview->addParametro( 'mes', 'JANEIRO E FEVEREIRO' );
        break;
    case 2:
        $preview->addParametro( 'data_ini', '01/03/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '30/04/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'MARÇO E ABRIL' );
        break;
    case 3:
        $preview->addParametro( 'data_ini', '01/05/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '30/06/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'MAIO E JUNHO' );
        break;
    case 4:
        $preview->addParametro( 'data_ini', '01/07/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '31/08/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'JULHO E AGOSTO' );
        break;
    case 5:
        $preview->addParametro( 'data_ini', '01/09/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '31/10/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'SETEMBRO E OUTUBRO' );
        break;
    case 6:
        $preview->addParametro( 'data_ini', '01/11/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '31/12/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'NOVEMBRO E DEZEMBRO' );
        break;
    endswitch;
    $preview->addParametro( 'periodo', $_REQUEST['cmbBimestre'] );
} elseif ($_REQUEST['inTipoPeriodo'] == 'Quadrimestre') {
    switch( $_REQUEST['cmbQuadrimestre']):
    case 1:
        $preview->addParametro( 'data_ini', '01/01/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '30/04/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'JANEIRO - ABRIL' );
        break;
    case 2:
        $preview->addParametro( 'data_ini', '01/05/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '31/08/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'MAIO - AGOSTO' );
        break;
    case 3:
        $preview->addParametro( 'data_ini', '01/09/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '31/12/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'SETEMBRO - DEZEMBRO' );
        break;
    endswitch;
    $preview->addParametro( 'periodo', $_REQUEST['cmbQuadrimestre'] );

} elseif ($_REQUEST['inTipoPeriodo'] == 'Semestre') {
    switch( $_REQUEST['cmbSemestre']):
    case 1:
        $preview->addParametro( 'data_ini', '01/01/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '30/06/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'JANEIRO - JUNHO' );
        break;
    case 2:
        $preview->addParametro( 'data_ini', '01/07/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '31/12/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'JULHO - DEZEMBRO' );
        break;
    endswitch;
    $preview->addParametro( 'periodo', $_REQUEST['cmbSemestre'] );
} elseif ($_REQUEST['inTipoPeriodo'] == 'Trimestre') {
    switch( $_REQUEST['cmbTrimestre']):
    case 1:
        $preview->addParametro( 'data_ini', '01/01/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '31/03/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'JANEIRO - MARÇO' );
        break;
    case 2:
        $preview->addParametro( 'data_ini', '01/04/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '30/06/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'ABRIL - JUNHO' );
        break;
    case 3:
        $preview->addParametro( 'data_ini', '01/07/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '30/09/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'JULHO - SETEMBRO' );
        break;
    case 4:
        $preview->addParametro( 'data_ini', '01/10/'.Sessao::getExercicio() );
        $preview->addParametro( 'data_fim', '31/12/'.Sessao::getExercicio() );
        $preview->addParametro( 'mes', 'OUTUBRO - DEZEMBRO' );
        break;
    endswitch;
    $preview->addParametro( 'periodo', $_REQUEST['cmbTrimestre'] );
}

$preview->addParametro('exercicio', Sessao::getExercicio());
$preview->addParametro('ano', Sessao::getExercicio());

$preview->addParametro('restos', ($_REQUEST['stRestos']=="true") ? "true" : "false"  );

$preview->addParametro( 'tipo_relatorio', (int)$_REQUEST  ["inTipoRelatorio"]);

$stFiltro = " WHERE sw_cgm.numcgm = ".Sessao::read('numCgm');

$obTAdministracaoUsuario = new TAdministracaoUsuario;
$obTAdministracaoUsuario->recuperaRelacionamento($rsUsuario, $stFiltro);

$preview->addParametro( 'unidade_responsavel', $rsUsuario->getCampo('orgao') );

$dtDataEmissao = date('d/m/Y');
$dtHoraEmissao = date('H:i');
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao );

$stNomeArquivo .= "_" . Sessao::getExercicio();

$preview->setNomeArquivo($stNomeArquivo);  
$preview->setNomeRelatorio($stNomeArquivo);
$arAssinaturas = Sessao::read('assinaturas');
$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
