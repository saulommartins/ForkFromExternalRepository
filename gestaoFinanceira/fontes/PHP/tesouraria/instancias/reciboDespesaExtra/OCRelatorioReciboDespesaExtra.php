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
    * Página de geração do recordSet para o Relatório Metas de Execução da Despesa
    * Data de Criação   : 28/08/2006

    * @author Analista: Diego Vitoria
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Id: OCRelatorioReciboDespesaExtra.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoAnalitica.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$obRelatorio = new RRelatorio;

$arDados = Sessao::read('post');

///// Dados da Entidade
$arEntidade = array();
$obEntidade = new TOrcamentoEntidade;
$stFiltro   = ' and  E.exercicio = ' ."'". Sessao::read('exercicio') ."'". ' and E.cod_entidade = ' . $arDados['inCodEntidade'];
$obEntidade->recuperaRelacionamento ( $rsEntidade, $stFiltro );

$arEntidade[]['entidade'] = 'Entidade: ' . $arDados['inCodEntidade']. ' - '. $rsEntidade->getCampo('nom_cgm');
$arEntidade[]['entidade'] = '';

///// Data e Valor
$arDataValor = array();
$arDataValor[0]['valor']  = 'Valor do Recibo: R$'.$arDados['txtValor'] . ' ('. SistemaLegado::extenso(str_replace(',','.',str_replace('.','',$arDados['txtValor']))).' )';

//// Dados do credor
$arCredor = array();

///// essa ganbiarra é pra colocar um linha em branco antes e uma depois do credor no relatório
$arCredor[0]['credor'] = '';
if ($arDados['inCodCredor'] != '') {
    $arCredor[1]['credor'] = 'Credor: '.$arDados['inCodCredor'] .' - '. $arDados['stNomCredor'];
    
    //monta o novo campo de dados bancários do Credor do Recibo
    include_once(CAM_GP_COM_MAPEAMENTO."TComprasFornecedorConta.class.php");
    $obTContaBancaria = new TComprasFornecedorConta();
    $obTContaBancaria->setDado('cgm_fornecedor',$arDados["inCodCredor"]);
    $obTContaBancaria->recuperaListaFornecedorConta($rsContaBancaria);
    
    foreach($rsContaBancaria->getElementos() as $index => $value){
        if ($value['padrao'] == 't'){
            $arTemp = $value;
        }
    }
    
    //$arContaBancaria[0]['conta_bancaria'] = '';
    $arContaBancaria[0]['conta_bancaria'] = 'Banco: '.$arTemp['num_banco'].' - '.$arTemp['nom_banco'].'   Agência: '.$arTemp['num_agencia'].' - '.$arTemp['nom_agencia'].'   Conta: '.$arTemp['num_conta'].'';
    $arContaBancaria[1]['conta_bancaria'] = '';
    
} else {
    $arCredor[1]['credor'] = 'Credor: ';
    $arCredor[2]['credor'] = '';
    $arContaBancaria[0]['conta_bancaria'] = 'Banco:   Agência:   Conta: ';
    $arContaBancaria[1]['conta_bancaria'] = '';
}

///Conta de Despesa
$obTContPlanoAnalitica = new TContabilidadePlanoAnalitica;
$stFiltro= ' where pa.cod_plano = ' . $arDados['inCodContaDespesa']. ' and pa.exercicio = '. "'".Sessao::read('exercicio')."'";
$obTContPlanoAnalitica->recuperaRelacionamento( $rsConta, $stFiltro );

$arContas = array();
//// Linha de titulo
//$arContas[0]['cod_estrutural'] = '';

////Conta Caixa Banco
if ($arDados['inCodContaBanco']) {
    $stFiltro= ' where pa.cod_plano = ' . $arDados['inCodContaBanco']. ' and pa.exercicio = '. "'".Sessao::read('exercicio')."'";
    $obTContPlanoAnalitica->recuperaRelacionamento( $rsContaBanco, $stFiltro );
    $arContas[0]['nom_conta']      = '';
    $arContas[0]['cod_estrutural'] = 'Conta Caixa/Banco: '.$rsContaBanco->getCampo('cod_plano')." - ".$rsContaBanco->getCampo('nom_conta');
    $arContas[0]['cod_conta']      = '';
} else {
    $arContas[0]['nom_conta']      = '';
    $arContas[0]['cod_estrutural'] = 'Conta Caixa/Banco: ';
    $arContas[0]['cod_conta']      = '';
}

//// Linha de titulo
$arContas[1]['nom_conta'] = '';
$arContas[1]['cod_estrutural'] = '';
$arContas[1]['cod_conta']      = '';

$arContas[2]['nom_conta']      = '';
$arContas[2]['cod_estrutural'] = 'Conta de Despesa: '.$rsConta->getCampo('cod_plano')." - ".$rsConta->getCampo('nom_conta');
$arContas[2]['cod_conta']      = '';

////Historico
$arTexto = array();
$stHistorico = $arDados['txtHistorico'];
$stHistorico = str_replace( chr(10), "", $stHistorico );
$stHistorico = wordwrap ( $stHistorico, 113 , chr(13)) ;
$arTexto = explode ( chr(13), $stHistorico );

$arHistorico = array();
$inCont = 0;
foreach ($arTexto as $historico) {
    $arHistorico[$inCont]['historico'] = $historico;
    $arHistorico[$inCont]['titulo']    = '';
    $inCont++;
}
$arHistorico[0]['titulo']    = 'Histórico: ';

////Recurso
$arRecurso = array();
if ($arDados['stDestinacaoRecurso']) {
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEspecificacaoDestinacaoRecurso.class.php"        );
    $obTOrcamentoEspecificacaoDestinacaoRecurso = new TOrcamentoEspecificacaoDestinacaoRecurso;
    $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaTodos( $rsEspecDestinacao, " WHERE cod_especificacao = ".$arDados['inCodEspecificacao']." AND exercicio = '".$arDados['exercicio']."' " );
    $arDados['stDescricaoRecurso'] = $rsEspecDestinacao->getCampo('descricao');
    $arDados['inCodRecurso'] = substr($arDados['stDestinacaoRecurso'],0,6);
}
if ($arDados['inCodRecurso'] != '') {
    $arRecurso[0]['recurso'] = 'Recurso: '.$arDados['inCodRecurso'] .' - '. $arDados['stDescricaoRecurso'];
} else
    $arRecurso[0]['recurso'] = 'Recurso: ';

/// passando os o controle para o gerador do Relatório em PDF
$arRel = array();
$arRel['entidade']       = $arEntidade;
$arRel['dataValor']      = $arDataValor;
$arRel['credor']         = $arCredor;
$arRel['conta_bancaria'] = $arContaBancaria;
$arRel['contas']         = $arContas;
$arRel['historico']      = $arHistorico;
$arRel['recurso']        = $arRecurso;
$arRel['stNomeCredor']   = $arDados['stNomCredor'];
$arRel['numeroRecibo']   = $arDados['numeroRecibo'];
$arRel['codEntidade']    = $arDados['inCodEntidade'];
$arRel['exercicio']      = Sessao::getExercicio();

Sessao::write('filtroRelatorio', $arRel);

$obRelatorio->executaFrameOculto( 'OCGeraRelatorioReciboDespesaExtra.php' );

?>
