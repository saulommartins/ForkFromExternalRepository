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
    * PÃ¡gina de Relatório RREO Anexo1
    * Data de Criação   : 10/04/2008

    * @author Henrique Boaventura

    * @ignore

    * Casos de uso :

    $Id: OCGeraRREOAnexo9.php 61605 2015-02-12 16:04:02Z diogo.zarpelon $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"                                    );

$preview = new PreviewBirt(6,36,26);
$preview->setTitulo('Demonstrativo dos Restos a Pagar por Poder e Órgão');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel ( true );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview->addParametro ( 'exercicio', Sessao::getExercicio() );
$preview->addParametro ( 'entidade', implode(',', $_REQUEST['inCodEntidade'] ) );

if ( count($_REQUEST['inCodEntidade']) == 1 ) {
     $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    $inCodEntidadePrefeitura = SistemaLegado::pegaDado('valor','administracao.configuracao'," WHERE parametro = 'cod_entidade_prefeitura' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 8 ");
    while ( !$rsEntidade->eof() ) {
      if ( $rsEntidade->getCampo('cod_entidade') == $inCodEntidadePrefeitura ) {
        $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
      }
      $rsEntidade->proximo();
    }
}

$preview->addParametro( 'tipo_periodo', $_REQUEST['stTipoRelatorio'] );

switch ($_REQUEST['stTipoRelatorio']) {
    case 'Bimestre'    :$preview->addParametro( 'periodo', $_REQUEST['cmbBimestre'    ] ); break;
    case 'Quadrimestre':$preview->addParametro( 'periodo', $_REQUEST['cmbQuadrimestre'] ); break;
    case 'Semestre'    :$preview->addParametro( 'periodo', $_REQUEST['cmbSemestre'    ] ); break;
}

$stDtFinal = '';

if ($request->get('cmbBimestre')) {
     $descricaoPeriodo = $request->get('cmbBimestre')."º Bimestre de ".Sessao::getExercicio();
     switch ($_REQUEST['cmbBimestre']) {
          case '1':
              if ( ( Sessao::getExercicio() % 4 ) == 0 ) {
                  $stDtFinal = '29/02/' . Sessao::getExercicio();
              } else {
                 $stDtFinal = '28/02/' . Sessao::getExercicio();
              }
          break;
          case '2':
              $stDtFinal = '30/04/' . Sessao::getExercicio();
          break;
          case '3':
              $stDtFinal = '30/06/' . Sessao::getExercicio();
          break;
          case '4':
              $stDtFinal = '31/08/' . Sessao::getExercicio();
          break;
          case '5':
              $stDtFinal = '31/10/' . Sessao::getExercicio();
          break;
          case '6':
              $stDtFinal = '31/12/' . Sessao::getExercicio();
          break;
     }
}else if ($request->get('cmbMes')) {
     $stDtFinal = SistemaLegado::retornaUltimoDiaMes($request->get('cmbMes'),Sessao::getExercicio());
     switch ($request->get('cmbMes')) {
          case '01':
              $descricaoPeriodo = "Janeiro de ".Sessao::getExercicio();
          break;
          case '02':
              $descricaoPeriodo = "Fevereiro de ".Sessao::getExercicio();
          break;
          case '03':
              $descricaoPeriodo = "Março de ".Sessao::getExercicio();
          break;
          case '04':
              $descricaoPeriodo = "Abril de ".Sessao::getExercicio();
          break;
          case '05':
              $descricaoPeriodo = "Maio de ".Sessao::getExercicio();
          break;
          case '06':
              $descricaoPeriodo = "Junho de ".Sessao::getExercicio();
          break;
          case '07':
              $descricaoPeriodo = "Julho de ".Sessao::getExercicio();
          break;
          case '08':
              $descricaoPeriodo = "Agosto de ".Sessao::getExercicio();
          break;
          case '09':
              $descricaoPeriodo = "Setembro de ".Sessao::getExercicio();
          break;
          case '10':
              $descricaoPeriodo = "Outubro de ".Sessao::getExercicio();
          break;
          case '11':
              $descricaoPeriodo = "Novembro de ".Sessao::getExercicio();
          break;
          case '12':
              $descricaoPeriodo = "Dezembro de ".Sessao::getExercicio();
          break;
     }
}

#############################Modificações do tce para o novo layout##############################
//adiciona unidade responsável ao relatório
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php"                                   );
$stFiltro = " WHERE sw_cgm.numcgm = ".Sessao::read('numCgm');
$obTAdministracaoUsuario = new TAdministracaoUsuario;
$obTAdministracaoUsuario->recuperaRelacionamento($rsUsuario, $stFiltro);

$preview->addParametro( 'unidade_responsavel', $rsUsuario->getCampo('orgao') );

//adicionada data de emissão no rodapé do relatório
$dtDataEmissao = date('d/m/Y');
$dtHoraEmissao = date('H:i');
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao );

if ($_REQUEST['stAcao'] == 'anexo7novo') {
    $preview->addParametro( 'relatorio_novo', 'sim' );
} else {
    $preview->addParametro( 'relatorio_novo', 'nao' );
}
#################################################################################################

$preview->addParametro( 'bimestre', $_REQUEST['cmbBimestre'] );
$preview->addParametro( 'dt_final', $stDtFinal );
$preview->addParametro( 'descricao', $descricaoPeriodo );
$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
