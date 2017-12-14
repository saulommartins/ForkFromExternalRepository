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
    * Página Formulário - Parâmetros do Arquivo
    * Data de Criação   : 21/01/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPATipoRemuneracaoEvento.class.php";
require_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php";

SistemaLegado::BloqueiaFrames();

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoRemuneracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

require_once $pgJS;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$stFiltroPagamentoEvento = "\n Where natureza = 'P'";
$stOrdemPagamentoEvento  = "   descricao";

$obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
$obTFolhaPagamentoEvento->recuperaTodos( $rsFolhaPagamentoEvento,$stFiltroPagamentoEvento,$stOrdemPagamentoEvento );

$inCont = 0;
while (!$rsFolhaPagamentoEvento->eof()) {
    $arDescricoes[$rsFolhaPagamentoEvento->getCampo('cod_evento')] = $rsFolhaPagamentoEvento->getCampo('codigo').'-'.$rsFolhaPagamentoEvento->getCampo('descricao');
    $arCodEventoDisp[] = $rsFolhaPagamentoEvento->getCampo('cod_evento');
    $rsFolhaPagamentoEvento->proximo();
    $inCont++;
}

// busca os eventos selecionados com suas descrições
$obTTPATipoRemuneracaoEvento = new TTPATipoRemuneracaoEvento();
$stOrdemRemuneracaoEvento = " order by descricao";
$obTTPATipoRemuneracaoEvento->recuperaTipoRemuneracaoEventoDescricao( $rsTipoRemuneracaoEvento,'',$stOrdemRemuneracaoEvento );
$arOpcoesSelecionados[1] = array();
$arOpcoesSelecionados[2] = array();
$arOpcoesSelecionados[3] = array();
$arCodEventoSel[1] = array();
$arCodEventoSel[2] = array();
$arCodEventoSel[3] = array();

$inCont = 0;
$arOpcoesDisponiveis = array();
// faz a diferença entre os eventos disponíveis e os selecionados
foreach ($rsTipoRemuneracaoEvento->arElementos as $inChave => $arDados) {
    if (!empty($codAnterior) && $codAnterior != $arDados['codigo']) {
        $inCont = 0;
    }
    $arOpcoesSelecionados[$arDados['codigo']][$inCont]['cod_evento'] = $arDados['cod_evento'];
    $arOpcoesSelecionados[$arDados['codigo']][$inCont]['descricao'] = $arDados['codigodescricao'].'-'.$arDados['descricao'];
    $arCodEventoSel[$arDados['codigo']][] = $arDados['cod_evento'];
    $inCont++;
    $codAnterior = $arDados['codigo'];
}

/***********************************
* monta recordset remuneracao base
************************************/
$inCont = 0;
$arDiffRemuneracaoBase = array_diff($arCodEventoDisp, $arCodEventoSel[1]);
foreach ($arDiffRemuneracaoBase as $inChave => $inCodEvento) {
    $arOpcoesDisponiveis[$inCont]['cod_evento'] = $inCodEvento;
    $arOpcoesDisponiveis[$inCont]['descricao'] = $arDescricoes[$inCodEvento];
    $inCont++;
}

$rsDispRemuneracaoBase = new RecordSet;
$rsDispRemuneracaoBase->preenche($arOpcoesDisponiveis);
$rsSelRemuneracaoBase = new RecordSet;
$rsSelRemuneracaoBase->preenche($arOpcoesSelecionados[1]);
/* fim do monta recordset remuneracao base*/

/***********************************
* monta recordset gratificacao de funcao
************************************/
$inCont = 0;
// faz a diferença entre os eventos disponíveis e os selecionados
$arDiffGratificacaoFuncao = array_diff($arCodEventoDisp, $arCodEventoSel[2]);
$arOpcoesDisponiveis = array();
// monta o array no padrão para poder montar o recordset do select multiplo
foreach ($arDiffGratificacaoFuncao as $inChave => $inCodEvento) {
    $arOpcoesDisponiveis[$inCont]['cod_evento'] = $inCodEvento;
    $arOpcoesDisponiveis[$inCont]['descricao'] = $arDescricoes[$inCodEvento];
    $inCont++;
}

$rsDispGratificacaoFuncao = new RecordSet;
$rsDispGratificacaoFuncao->preenche( $arOpcoesDisponiveis );
$rsSelGratificacaoFuncao = new RecordSet;
$rsSelGratificacaoFuncao->preenche( $arOpcoesSelecionados[2] );
/* fim do monta recordset gratificacao de funcao */

/***********************************
* monta recordset outras remuneracoes
************************************/
// faz a diferença entre os selects multiplos para poder saber qual são as opções disponíveis
// precisa fazer a diferença entre os 3 pois o select multiplo de outras remunerações não pode ter as opções que já foram selecionadas
// pela remuneração base, gratificação da função e pelos selecionados de outras remunerações.
$arDiffOutrasRemuneracoes = array_diff($arCodEventoDisp, $arCodEventoSel[1]);
$arDiffOutrasRemuneracoes = array_diff($arDiffOutrasRemuneracoes, $arCodEventoSel[2]);
$arDiffOutrasRemuneracoes = array_diff($arDiffOutrasRemuneracoes, $arCodEventoSel[3]);

$arOpcoesDisponiveis = array();
$inCont = 0;
// monta o array no padrão para poder montar o recordset do select multiplo
foreach ($arDiffOutrasRemuneracoes as $inChave => $inCodEvento) {
    $arOpcoesDisponiveis[$inCont]['cod_evento'] = $inCodEvento;
    $arOpcoesDisponiveis[$inCont]['descricao'] = $arDescricoes[$inCodEvento];
    $inCont++;
}

$rsDispOutrasRemuneracoes = new RecordSet;
$rsDispOutrasRemuneracoes->preenche( $arOpcoesDisponiveis );
$rsSelOutrasRemuneracoes = new RecordSet;
$rsSelOutrasRemuneracoes->preenche( $arOpcoesSelecionados[3] );
/* fim do monta recordset outras remuneracoes */

/****************************************
 * Select Multiplo da Remuneração Base
****************************************/
$obCbmRemuneracaoBase = new SelectMultiplo();
$obCbmRemuneracaoBase->setName  ( 'arArquivosSelecionadosRemuneracaoBase' );
$obCbmRemuneracaoBase->setRotulo( "Eventos para Remuneração Base" );
$obCbmRemuneracaoBase->setNull  ( false );
$obCbmRemuneracaoBase->setTitle ( 'Arquivos Disponiveis' );
// lista de ARQUIVOS disponiveis
$obCbmRemuneracaoBase->SetNomeLista1( 'arCodArqDisponiveisRemuneracaoBase' );
$obCbmRemuneracaoBase->setCampoId1  ( 'cod_evento' );
$obCbmRemuneracaoBase->setCampoDesc1( 'descricao' );
$obCbmRemuneracaoBase->obSelect1->setStyle( 'width: 250px' );
$obCbmRemuneracaoBase->SetRecord1   ( $rsDispRemuneracaoBase );

// lista de ARQUIVOS selecionados
$obCbmRemuneracaoBase->SetNomeLista2( 'arArquivosSelecionadosRemuneracaoBase' );
$obCbmRemuneracaoBase->setCampoId2  ( 'cod_evento' );
$obCbmRemuneracaoBase->setCampoDesc2( 'descricao' );
$obCbmRemuneracaoBase->obSelect2->setStyle( 'width: 250px' );
$obCbmRemuneracaoBase->SetRecord2   ( $rsSelRemuneracaoBase );

$stURL = $pgOcul.'?'.Sessao::getId();
$obCbmRemuneracaoBase->obSelect1->obEvento->setOnDblClick("montaArrayOutrasRemuneracoes('remuneracao')");
$obCbmRemuneracaoBase->obSelect2->obEvento->setOnDblClick("montaArrayOutrasRemuneracoes('remuneracao')");
$obCbmRemuneracaoBase->obGerenciaSelects->obBotao1->obEvento->setOnClick("montaArrayOutrasRemuneracoes('remuneracao')");
$obCbmRemuneracaoBase->obGerenciaSelects->obBotao2->obEvento->setOnClick("montaArrayOutrasRemuneracoes('remuneracao')");
$obCbmRemuneracaoBase->obGerenciaSelects->obBotao3->obEvento->setOnClick("montaArrayOutrasRemuneracoes('remuneracao')");
$obCbmRemuneracaoBase->obGerenciaSelects->obBotao4->obEvento->setOnClick("montaArrayOutrasRemuneracoes('remuneracao')");

/****************************************
 * Select Multiplo da Gratificação de Função
****************************************/
$obCbmGratificacaoFuncao = new SelectMultiplo();
$obCbmGratificacaoFuncao->setName  ( 'arArquivosSelecionadosGratificacaoFuncao' );
$obCbmGratificacaoFuncao->setRotulo( "Eventos para Gratificação de Função" );
$obCbmGratificacaoFuncao->setNull  ( true );
$obCbmGratificacaoFuncao->setTitle ( 'Arquivos Disponiveis' );
// lista de ARQUIVOS disponiveis
$obCbmGratificacaoFuncao->SetNomeLista1      ( 'arCodArqDisponiveisGratificacaoFuncao' );
$obCbmGratificacaoFuncao->setCampoId1        ( 'cod_evento' );
$obCbmGratificacaoFuncao->setCampoDesc1      ( 'descricao' );
$obCbmGratificacaoFuncao->obSelect1->setStyle( 'width: 250px' );
$obCbmGratificacaoFuncao->SetRecord1         ( $rsDispGratificacaoFuncao );

// lista de ARQUIVOS selecionados
$obCbmGratificacaoFuncao->SetNomeLista2      ( 'arArquivosSelecionadosGratificacaoFuncao' );
$obCbmGratificacaoFuncao->setCampoId2        ( 'cod_evento' );
$obCbmGratificacaoFuncao->setCampoDesc2      ( 'descricao' );
$obCbmGratificacaoFuncao->obSelect2->setStyle( 'width: 250px' );
$obCbmGratificacaoFuncao->SetRecord2         ( $rsSelGratificacaoFuncao );

$obCbmGratificacaoFuncao->obSelect1->obEvento->setOnDblClick("montaArrayOutrasRemuneracoes('gratificacao')");
$obCbmGratificacaoFuncao->obSelect2->obEvento->setOnDblClick("montaArrayOutrasRemuneracoes('gratificacao')");
$obCbmGratificacaoFuncao->obGerenciaSelects->obBotao1->obEvento->setOnClick("montaArrayOutrasRemuneracoes('gratificacao')");
$obCbmGratificacaoFuncao->obGerenciaSelects->obBotao2->obEvento->setOnClick("montaArrayOutrasRemuneracoes('gratificacao')");
$obCbmGratificacaoFuncao->obGerenciaSelects->obBotao3->obEvento->setOnClick("montaArrayOutrasRemuneracoes('gratificacao')");
$obCbmGratificacaoFuncao->obGerenciaSelects->obBotao4->obEvento->setOnClick("montaArrayOutrasRemuneracoes('gratificacao')");

/****************************************
 * Select Multiplo de Outras Remunerações
****************************************/
$obCbmOutrasRemuneracoes = new SelectMultiplo();
$obCbmOutrasRemuneracoes->setName  ( 'arArquivosSelecionadosOutrasRemuneracoes' );
$obCbmOutrasRemuneracoes->setRotulo( "Eventos para Outras Remunerações" );
$obCbmOutrasRemuneracoes->setNull  ( true );
$obCbmOutrasRemuneracoes->setTitle ( 'Arquivos Disponiveis' );
// lista de ARQUIVOS disponiveis
$obCbmOutrasRemuneracoes->SetNomeLista1( 'arCodArqDisponiveisOutrasRemuneracoes' );
$obCbmOutrasRemuneracoes->setCampoId1  ( 'cod_evento' );
$obCbmOutrasRemuneracoes->setCampoDesc1( 'descricao' );
$obCbmOutrasRemuneracoes->obSelect1->setStyle( 'width: 250px' );
$obCbmOutrasRemuneracoes->SetRecord1   ( $rsDispOutrasRemuneracoes );

// lista de ARQUIVOS selecionados
$obCbmOutrasRemuneracoes->SetNomeLista2( 'arArquivosSelecionadosOutrasRemuneracoes' );
$obCbmOutrasRemuneracoes->setCampoId2  ( 'cod_evento' );
$obCbmOutrasRemuneracoes->setCampoDesc2( 'descricao' );
$obCbmOutrasRemuneracoes->obSelect2->setStyle( 'width: 250px' );
$obCbmOutrasRemuneracoes->SetRecord2   ( $rsSelOutrasRemuneracoes );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Remuneração Base" );
$obFormulario->addComponente( $obCbmRemuneracaoBase );
$obFormulario->addTitulo( "Gratificação de Função" );
$obFormulario->addComponente( $obCbmGratificacaoFuncao );
$obFormulario->addTitulo( "Outras Remunuerações" );
$obFormulario->addComponente( $obCbmOutrasRemuneracoes );
$obFormulario->defineBarra( array( new Ok(true) ) );
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
