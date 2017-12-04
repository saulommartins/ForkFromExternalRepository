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
    * Página de Relatório RGF Anexo3
    * Data de Criação   : 25/10/2007

    * @author Tonismar Régis Bernardo

    * @ignore

    * Casos de uso : uc-06.01.22

    $Id: OCGeraRGFAnexo3.php 61605 2015-02-12 16:04:02Z diogo.zarpelon $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$obErro = new Erro();

if ($_REQUEST['stTipoRelatorio'] == 'Semestre') {
    $preview = new PreviewBirt(6,36,11);
}
else if ($_REQUEST['stTipoRelatorio'] == 'Mes') {
    $preview = new PreviewBirt(6,36,12);
}
else {
    $preview = new PreviewBirt(6,36,4);
}

$preview->setTitulo('Dem Garantias/Contragarantias de Valores');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel ( true );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview->addParametro( 'exercicio_anterior', (Sessao::getExercicio() - 1) );
$preview->addParametro( 'entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
$preview->addParametro( 'tipo_periodo', $_REQUEST['stTipoRelatorio'] );

if ( count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
} else {
    while ( !$rsEntidade->eof() ) {
        if ( preg_match( "/prefeitura.*/i", $rsEntidade->getCampo('nom_cgm')) ) {
            $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
            break;
        }
        $rsEntidade->proximo();
    }
}

if ( preg_match( "/PREFEITURA/i", $rsEntidade->getCampo( 'nom_cgm' ) ) || ( count($_REQUEST['inCodEntidade']) > 1 ) ) {
    $preview->addParametro( 'poder' , 'Executivo' );
} elseif ( preg_match( "/C[ÂA]MARA/i", $rsEntidade->getCampo( 'nom_cgm' ) ) ) {
    $preview->addParametro( 'poder' , 'Legislativo' );
}

$arStMes = array('01'=>'Janeiro', '02'=>'Fevereiro', '03'=>'Março', '04'=>'Abril', '05'=>'Maio', '06'=>'Junho', '07'=>'Julho', '08'=>'Agosto', '09'=>'Setembro', '10'=>'Outubro', '11'=>'Novembro', '12'=>'Dezembro');
$stDtInicial= "01/".str_pad($_REQUEST['cmbMensal'], 2, "0", STR_PAD_LEFT)."/".Sessao::getExercicio();
$stDtFinal  = SistemaLegado::retornaUltimoDiaMes(str_pad($_REQUEST['cmbMensal'], 2, "0", STR_PAD_LEFT), Sessao::getExercicio());

if ($_REQUEST['stTipoRelatorio'] == 'Semestre') {
    $preview->addParametro( 'periodo', $_REQUEST['cmbSemestre'] );
}else if ($_REQUEST['stTipoRelatorio'] == 'Mes') {
    $preview->addParametro( 'periodo'       , $_REQUEST['cmbMensal']            );
    $preview->addParametro( 'mes'           , $arStMes[$_REQUEST['cmbMensal']]  );
    $preview->addParametro( 'stDtInicial'   , $stDtInicial                      );
    $preview->addParametro( 'stDtFinal'     , $stDtFinal                        );
}else {
    $preview->addParametro( 'periodo', $_REQUEST['cmbQuadrimestre'] );
}

$preview->addParametro( 'perc_limit', 22 );

// verificando se foi selecionado Câmara e outra entidade junto
$rsEntidade->setPrimeiroElemento();
if ( count($_REQUEST['inCodEntidade']) != 1 ) {
    while ( !$rsEntidade->eof() ) {
        if ( preg_match( "/C[ÂA]MARA/i", $rsEntidade->getCampo( 'nom_cgm' ) ) ) {
            $obErro->setDescricao( "Entidade ".$rsEntidade->getCampo('nom_cgm')." deve ser selecionada sozinha.");
        }
        $rsEntidade->proximo();
    }
}
#############################Modificações do stn para o novo layout##############################
//adiciona unidade responsável ao relatório
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php";

$stFiltro = " WHERE sw_cgm.numcgm = ".Sessao::read('numCgm');
$obTAdministracaoUsuario = new TAdministracaoUsuario;

$obTAdministracaoUsuario->recuperaRelacionamento($rsUsuario, $stFiltro);
$preview->addParametro( 'unidade_responsavel', $rsUsuario->getCampo('orgao') );

//adicionada data de emissão no rodapé do relatório
$dtDataEmissao = date('d/m/Y');
$dtHoraEmissao = date('H:i');
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao );

if ($_REQUEST['stAcao'] == 'anexo3novo') {
    $preview->addParametro( 'relatorio_novo', 'sim' );
} else {
    $preview->addParametro( 'relatorio_novo', 'nao' );
}
#####################################################################################

$preview->addAssinaturas(Sessao::read('assinaturas'));
if ( !$obErro->ocorreu() ) {
    $preview->preview();
} else {
    SistemaLegado::alertaAviso("FLModelosRGF.php?'.Sessao::getId().&stAcao=$stAcao", $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
}
