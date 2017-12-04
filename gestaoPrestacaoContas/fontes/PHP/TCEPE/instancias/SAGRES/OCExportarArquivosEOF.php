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

/**
  * Página Oculta para o formulário FLExportarArquivosEOF.php
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: OCExportarArquivosEOF.php 60807 2014-11-17 16:20:40Z diogo.zarpelon $
  * $Date: 2014-11-17 14:20:40 -0200 (Mon, 17 Nov 2014) $
  * $Author: diogo.zarpelon $
  * $Rev: 60807 $
  *
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function montaListaArquivos($inCompetencia)
{
    $arArquivos = array();
    
    if ($inCompetencia == 1) {
        $arArquivos[] = array("nome" => "Orçamento", "arquivo" => "Orcamento" );
        $arArquivos[] = array("nome" => "Previsão Receita", "arquivo" => "PrevisaoReceita" );
        $arArquivos[] = array("nome" => "Restos Inscritos", "arquivo" => "RestosInscritos" );
        $arArquivos[] = array("nome" => "Saldo Inicial", "arquivo" => "SaldoInicial" );
    }

    if ($inCompetencia == 13) {
        $arArquivos[] = array("nome" => "Remuneração Agentes Eletivos", "arquivo" => "RemuneracaoAgentesEletivos" );
        $arArquivos[] = array("nome" => "Contribuição Previdenciária", "arquivo" => "ContribuicaoPrevidenciaria" );
        $arArquivos[] = array("nome" => "Dívida Fundada Operação Crédito", "arquivo" => "DividaFundadaOperacaoCredito" );
        $arArquivos[] = array("nome" => "Dívida Fundada Outra Operação Crédito", "arquivo" => "DividaFundadaOutraOperacaoCredito" );
    } else {
        $arArquivos[] = array("nome" => "Unidade Orçamentária", "arquivo" => "UnidadeOrcamentaria" );
        $arArquivos[] = array("nome" => "Programas", "arquivo" => "Programas" );
        $arArquivos[] = array("nome" => "Ação", "arquivo" => "Acao" );
        $arArquivos[] = array("nome" => "Dotação", "arquivo" => "Dotacao" );
        $arArquivos[] = array("nome" => "Elenco Contas", "arquivo" => "ElencoContas" );
        $arArquivos[] = array("nome" => "Cadastro Contas", "arquivo" => "CadastroContas" );
        $arArquivos[] = array("nome" => "Relacionamento Receita Orçamentária" , "arquivo" => "RelacionamentoReceitaOrcamentaria" );
        $arArquivos[] = array("nome" => "Relacionamento Receita Extra", "arquivo" => "RelacionamentoReceitaExtra" );
        $arArquivos[] = array("nome" => "Relacionamento Despesa Extra", "arquivo" => "RelacionamentoDespesaExtra" );
        $arArquivos[] = array("nome" => "Atualização Orçamentária", "arquivo" => "AtualizacaoOrcamentaria" );
        $arArquivos[] = array("nome" => "Norma Atualização", "arquivo" => "NormaAtualizacao" );
        $arArquivos[] = array("nome" => "Empenhos", "arquivo" => "Empenhos" );
        $arArquivos[] = array("nome" => "Empenho Estorno", "arquivo" => "EmpenhoEstorno" );
        $arArquivos[] = array("nome" => "Empenho Reforço", "arquivo" => "EmpenhoReforco" );
        $arArquivos[] = array("nome" => "Liquidação", "arquivo" => "Liquidacao" );
        $arArquivos[] = array("nome" => "Pagamentos", "arquivo" => "Pagamentos" );
        $arArquivos[] = array("nome" => "ItemPagamento", "arquivo" => "ItemPagamento" );
        $arArquivos[] = array("nome" => "Retenção", "arquivo" => "Retencao" );
        $arArquivos[] = array("nome" => "Receita Orçamentária", "arquivo" => "ReceitaOrcamentaria" );
        $arArquivos[] = array("nome" => "Receita Extra", "arquivo" => "ReceitaExtra" );
        $arArquivos[] = array("nome" => "Despesa Extra", "arquivo" => "DespesaExtra" );
        $arArquivos[] = array("nome" => "Estorno Restos", "arquivo" => "EstornoRestos" );
        $arArquivos[] = array("nome" => "Pagamentos Restos", "arquivo" => "PagamentosRestos" );
        $arArquivos[] = array("nome" => "Item Pagamentos Restos", "arquivo" => "ItemPagamentosRestos" );
        $arArquivos[] = array("nome" => "Retenção Restos", "arquivo" => "RetencaoRestos" );
        $arArquivos[] = array("nome" => "Conciliação Bancária", "arquivo" => "ConciliacaoBancaria" );
        $arArquivos[] = array("nome" => "Saldo Mensal", "arquivo" => "SaldoMensal" );
        $arArquivos[] = array("nome" => "Fornecedores", "arquivo" => "Fornecedores" );
        $arArquivos[] = array("nome" => "Pagamento Estorno", "arquivo" => "PagamentoEstorno" );
        $arArquivos[] = array("nome" => "Liquidação Estorno", "arquivo" => "LiquidacaoEstorno" );
        $arArquivos[] = array("nome" => "Pagamento Resto Estorno", "arquivo" => "PagamentoRestoEstorno" );
        $arArquivos[] = array("nome" => "Agente Político", "arquivo" => "AgentePolitico" );
        $arArquivos[] = array("nome" => "Ordenador", "arquivo" => "Ordenador" );
        $arArquivos[] = array("nome" => "Técnico Responsável", "arquivo" => "TecnicoResponsavel" );
        $arArquivos[] = array("nome" => "Transferência Recebida", "arquivo" => "TransferenciaRecebida" );
        $arArquivos[] = array("nome" => "Transferência Concedida", "arquivo" => "TransferenciaConcedida" );
        $arArquivos[] = array("nome" => "Relacionamento Fonte Recurso", "arquivo" => "RelacionamentoFonteRecurso" );
        $arArquivos[] = array("nome" => "Liquidação Restos", "arquivo" => "LiquidacaoRestos" );
        $arArquivos[] = array("nome" => "Liquidação Restos Estorno", "arquivo" => "LiquidacaoRestosEstorno" );
        $arArquivos[] = array("nome" => "Gestor", "arquivo" => "Gestor" );
    }

    $arArquivos[] = array("nome" => "Saldos Contas Contábeis", "arquivo" => "SaldosContasContabeis");
    $arArquivos[] = array("nome" => "Saldos Contas Correntes", "arquivo" => "SaldosContasCorrentes");

    return $arArquivos;
}

function montaPreencheArquivos($inCodCompetencia)
{
    $arArquivos = array();
    $rsArquivos = new RecordSet;
    $rsArquivos->preenche(montaListaArquivos($inCodCompetencia));
    $rsArquivos->ordena('nome', 'ASC', SORT_STRING);
    // Define SELECT multiplo para os arquivos
    $obCmbArquivos = new SelectMultiplo();
    $obCmbArquivos->setName( 'arArquivos' );
    $obCmbArquivos->setRotulo( 'Arquivos' );
    $obCmbArquivos->setTitle( '' );
    $obCmbArquivos->setNull( false );
    
    // lista as entidades disponiveis
    $obCmbArquivos->SetNomeLista1( 'arArquivoDisponivel' );
    $obCmbArquivos->setCampoId1( 'arquivo' );
    $obCmbArquivos->setCampoDesc1( 'nome' );
    $obCmbArquivos->SetRecord1( $rsArquivos );
    
    // lista as entidades selecionados
    $obCmbArquivos->SetNomeLista2( 'arArquivos' );
    $obCmbArquivos->setCampoId2( 'arquivo' );
    $obCmbArquivos->setCampoDesc2( 'nome' );
    $obCmbArquivos->SetRecord2( new RecordSet );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obCmbArquivos );
    $obFormulario->montaInnerHTML();
    
    $stHtml = $obFormulario->getHTML();
    $stJs .= "document.getElementById('obCmbArquivos').innerHTML = '".$stHtml."' ";
    
    return $stJs;
}

switch($_GET['stCtrl']){
    case "montaMultipleSelect":
        $inCodCompetencia = $_REQUEST["inCodCompetencia"];
        if( $inCodCompetencia != '' ){
            $stJs = montaPreencheArquivos($inCodCompetencia);
        }else{
            $stJs = "document.getElementById('obCmbArquivos').innerHTML = '' ";
        }
        break;
}

if ($stJs) {
   echo $stJs;
}

?>