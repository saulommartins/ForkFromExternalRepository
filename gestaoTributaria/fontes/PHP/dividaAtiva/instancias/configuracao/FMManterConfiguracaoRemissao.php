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
    * Página de Formulário da Configuração de Remissão
    * Data de Criação   : 19/08/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: FMManterConfiguracaoRemissao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_NEGOCIO."RDATConfiguracao.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpModalidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgForm = "FM".$stPrograma."Remissao.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRDATConfiguracao = new RDATConfiguracao;
$obRDATConfiguracao->consultar();
$stLancamentosAtivos = $obRDATConfiguracao->getLancamentoAtivo();
$stInscricaoAutomatica = $obRDATConfiguracao->getInscricaoAutomatica();
$stValidacaoRemissao = $obRDATConfiguracao->getValidacao();
$stValorLimite = $obRDATConfiguracao->getLimites();
$inCodModalidade = $obRDATConfiguracao->getCodModalidade();

$stAcao = $request->get('stAcao');

//DEFINICAO DOS COMPONENTES
$obForm  = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );

//Lancamentos Ativos
$obRdbLancamentoVerificar = new Radio;
$obRdbLancamentoVerificar->setRotulo   ( "Lançamentos Ativos" );
$obRdbLancamentoVerificar->setTitle    ( "Informe se devem ser verificados lançamentos ativos, não inscritos em dívida ativa." );
$obRdbLancamentoVerificar->setName     ( "stLancamentoAtivo" );
$obRdbLancamentoVerificar->setLabel    ( "Verificar" );
$obRdbLancamentoVerificar->setValue    ( "verificar" );
$obRdbLancamentoVerificar->setChecked  ( $stLancamentosAtivos == "verificar" );
$obRdbLancamentoVerificar->setNull     ( false );

//Lancamentos Ativos
$obRdbLancamentoDesconsiderar = new Radio;
$obRdbLancamentoDesconsiderar->setRotulo   ( "Lançamentos Ativos" );
$obRdbLancamentoDesconsiderar->setTitle    ( "Informe se devem ser verificados lançamentos ativos, não inscritos em dívida ativa." );
$obRdbLancamentoDesconsiderar->setName     ( "stLancamentoAtivo" );
$obRdbLancamentoDesconsiderar->setLabel    ( "Desconsiderar" );
$obRdbLancamentoDesconsiderar->setValue    ( "desconsiderar" );
$obRdbLancamentoDesconsiderar->setChecked  ( $stLancamentosAtivos == "desconsiderar" );
$obRdbLancamentoDesconsiderar->setNull     ( false );

//Inscricao Automatica
$obRdbInscricaoAutomaticaDA = new Radio;
$obRdbInscricaoAutomaticaDA->setRotulo   ( "Inscrição Automática" );
$obRdbInscricaoAutomaticaDA->setTitle    ( "Informe se os lançamentos ativos deverão ser inscritos em dívida ativa, antes da remissão." );
$obRdbInscricaoAutomaticaDA->setName     ( "stInscricaoAutomatica" );
$obRdbInscricaoAutomaticaDA->setLabel    ( "Inscrever em DA" );
$obRdbInscricaoAutomaticaDA->setValue    ( "sim" );
$obRdbInscricaoAutomaticaDA->setChecked  ( $stInscricaoAutomatica == "sim" );
$obRdbInscricaoAutomaticaDA->setNull     ( false );

//Inscricao Automatica
$obRdbInscricaoAutomaticaNDA = new Radio;
$obRdbInscricaoAutomaticaNDA->setRotulo   ( "Inscrição Automática" );
$obRdbInscricaoAutomaticaNDA->setTitle    ( "Informe se os lançamentos ativos deverão ser inscritos em dívida ativa, antes da remissão." );
$obRdbInscricaoAutomaticaNDA->setName     ( "stInscricaoAutomatica" );
$obRdbInscricaoAutomaticaNDA->setLabel    ( "Não Inscrever em DA" );
$obRdbInscricaoAutomaticaNDA->setValue    ( "nao" );
$obRdbInscricaoAutomaticaNDA->setChecked  ( $stInscricaoAutomatica == "nao" );
$obRdbInscricaoAutomaticaNDA->setNull     ( false );

//Forma de Validacao para Remissao
$obRdbFormaValidacaoRemissaoTodos = new Radio;
$obRdbFormaValidacaoRemissaoTodos->setRotulo   ( "Forma de Validação para Remissão" );
$obRdbFormaValidacaoRemissaoTodos->setTitle    ( "Informe se deverão ser considerados apenas os lançamentos válidos para a validação do valor total." );
$obRdbFormaValidacaoRemissaoTodos->setName     ( "stValidacaoRemissao" );
$obRdbFormaValidacaoRemissaoTodos->setLabel    ( "Todos Lançamentos" );
$obRdbFormaValidacaoRemissaoTodos->setValue    ( "todos" );
$obRdbFormaValidacaoRemissaoTodos->setChecked  ( $stValidacaoRemissao == "todos" );
$obRdbFormaValidacaoRemissaoTodos->setNull     ( false );

//Forma de Validacao para Remissao
$obRdbFormaValidacaoRemissaoValidos = new Radio;
$obRdbFormaValidacaoRemissaoValidos->setRotulo   ( "Forma de Validação para Remissão" );
$obRdbFormaValidacaoRemissaoValidos->setTitle    ( "Informe se deverão ser considerados apenas os lançamentos válidos para a validação do valor total." );
$obRdbFormaValidacaoRemissaoValidos->setName     ( "stValidacaoRemissao" );
$obRdbFormaValidacaoRemissaoValidos->setLabel    ( "Lançamentos Válidos" );
$obRdbFormaValidacaoRemissaoValidos->setValue    ( "todos" );
$obRdbFormaValidacaoRemissaoValidos->setChecked  ( $stValidacaoRemissao == "validos" );
$obRdbFormaValidacaoRemissaoValidos->setNull     ( false );

$arLimites = array();
$arLimites[0]["cod_limite"] = 1;
$arLimites[0]["descricao_limite"] = "Limite por Crédito";
$arLimites[1]["cod_limite"] = 2;
$arLimites[1]["descricao_limite"] = "Limite por Exercício";
$arLimites[2]["cod_limite"] = 3;
$arLimites[2]["descricao_limite"] = "Limite Total";
$arLimites[3]["cod_limite"] = 4;
$arLimites[3]["descricao_limite"] = "Limite por Crédito e Limite Total";
$arLimites[4]["cod_limite"] = 5;
$arLimites[4]["descricao_limite"] = "Limite por Exercício e Limite Total";
$arLimites[5]["cod_limite"] = 6;
$arLimites[5]["descricao_limite"] = "Limite por Crédito, Exercício e Limite Total";
$arLimites[6]["cod_limite"] = 7;
$arLimites[6]["descricao_limite"] = "Limite por Crédito e Limite por Exercício";

$rsLimite = new RecordSet;
$rsLimite->preenche ( $arLimites );

$obCmbValoresLimite = new Select;
$obCmbValoresLimite->setRotulo               ( "Valores Limites" );
$obCmbValoresLimite->setTitle                ( "Selecione a forma de verificação dos valores para remissão." );
$obCmbValoresLimite->setName                 ( "cmbValoresLimites" );
$obCmbValoresLimite->addOption               ( "", "Selecione" );
$obCmbValoresLimite->setValue                ( $stValorLimite );
$obCmbValoresLimite->setCampoId              ( "cod_limite" );
$obCmbValoresLimite->setCampoDesc            ( "descricao_limite" );
$obCmbValoresLimite->preencheCombo           ( $rsLimite );
$obCmbValoresLimite->setNull                 ( false );
$obCmbValoresLimite->setStyle                ( "width: 220px" );

$obIPopUpModalidade = new IPopUpModalidade;
$obIPopUpModalidade->setTipoModalidade(1); //inscricao
$obIPopUpModalidade->obInnerModalidade->setTitle ( "Informe o código para a Modalidade" );
$obIPopUpModalidade->obInnerModalidade->setNull ( false );
$obIPopUpModalidade->inCodModalidade = $inCodModalidade;

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addTitulo ( "Dados para Configuração de Remissão" );
$obFormulario->addComponenteComposto ( $obRdbLancamentoVerificar, $obRdbLancamentoDesconsiderar );
$obFormulario->addComponenteComposto ( $obRdbInscricaoAutomaticaDA, $obRdbInscricaoAutomaticaNDA );
$obFormulario->addComponenteComposto ( $obRdbFormaValidacaoRemissaoTodos, $obRdbFormaValidacaoRemissaoValidos );
$obFormulario->addComponente ( $obCmbValoresLimite );
$obIPopUpModalidade->geraFormulario ( $obFormulario );

$obFormulario->Ok();
$obFormulario->show();

?>
