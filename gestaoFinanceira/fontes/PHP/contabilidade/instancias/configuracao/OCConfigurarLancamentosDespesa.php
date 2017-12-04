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
  * Formulário oculto
  * Data de criação : 21/10/2011

    * @author Analista: Tonismar Bernardo
    * @author Programador: Davi Aroldi

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php" );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoCredito.class.php" );
include_once( CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

function montaLancamentoDespesa()
{
  $obTContabilidadeConfiguracaoLancamentoCredito = new TContabilidadeConfiguracaoLancamentoCredito;
  $stFiltro = " WHERE configuracao_lancamento_credito.cod_conta_despesa = ".$_REQUEST['cod_conta_despesa']."
                  AND configuracao_lancamento_credito.estorno = 'false'
                  AND configuracao_lancamento_credito.exercicio = '".Sessao::getExercicio()."' ";
  $obTContabilidadeConfiguracaoLancamentoCredito->recuperaContasConfiguracaoDespesa( $rsRecordSet, $stFiltro );

  $stJs = "";
  $stJs .= " jQuery('#codContaDespesaLista').val(".$_REQUEST['cod_conta_despesa']."); \n";

  if ($rsRecordSet->getNumLinhas() > 0) {
    while (!$rsRecordSet->eof()) {
      switch ($rsRecordSet->getCampo('tipo_despesa')) {
        case 'despesaPessoal':
        case "entidadeRPPS":
          $stJs .= " jQuery('#rdoLiquidacaoPessoal').attr('checked', true); \n";
          $stJs .= montaCombos($rsRecordSet->getCampo('cod_conta_despesa'), 'liquidacao', $rsRecordSet->getCampo('tipo_despesa'));
          $stJs .= " jQuery('#stLancamentoDebitoLiquidacao').val(".$rsRecordSet->getCampo('conta_debito')."); \n";
          $stJs .= " jQuery('#stLancamentoCreditoLiquidacao').val(".$rsRecordSet->getCampo('conta_credito')."); \n";
          break;
        case 'demaisDespesas':
          $stJs .= " jQuery('#rdoLiquidacaoDemais').attr('checked', true); \n";
          $stJs .= montaCombos($rsRecordSet->getCampo('cod_conta_despesa'), 'liquidacao', $rsRecordSet->getCampo('tipo_despesa'));
          $stJs .= " jQuery('#stLancamentoDebitoLiquidacao').val(".$rsRecordSet->getCampo('conta_debito')."); \n";
          $stJs .= " jQuery('#stLancamentoCreditoLiquidacao').val(".$rsRecordSet->getCampo('conta_credito')."); \n";
          break;
        case 'materialConsumo':
          $stJs .= " jQuery('#rdoLiquidacaoConsumo').attr('checked', true); \n";
          $stJs .= montaCombos($rsRecordSet->getCampo('cod_conta_despesa'), 'liquidacao', $rsRecordSet->getCampo('tipo_despesa'));
          $stJs .= " jQuery('#stLancamentoDebitoLiquidacao').val(".$rsRecordSet->getCampo('conta_debito')."); \n";
          $stJs .= " jQuery('#stLancamentoCreditoLiquidacao').val(".$rsRecordSet->getCampo('conta_credito')."); \n";
          break;
        case 'materialPermanente':
          $stJs .= " jQuery('#rdoLiquidacaoPermanente').attr('checked', true); \n";
          $stJs .= montaCombos($rsRecordSet->getCampo('cod_conta_despesa'), 'liquidacao', $rsRecordSet->getCampo('tipo_despesa'));
          $stJs .= " jQuery('#stLancamentoDebitoLiquidacao').val(".$rsRecordSet->getCampo('conta_debito')."); \n";
          $stJs .= " jQuery('#stLancamentoCreditoLiquidacao').val(".$rsRecordSet->getCampo('conta_credito')."); \n";
          break;
        case 'almoxarifado':
          $stJs .= " jQuery('#stLancamentoDebitoAlmoxarifado').val(".$rsRecordSet->getCampo('conta_debito')."); \n";
          $stJs .= " jQuery('#stLancamentoCreditoAlmoxarifado').val(".$rsRecordSet->getCampo('conta_credito')."); \n";
          break;
        default:
          # code...
          break;
      }

      $rsRecordSet->proximo();
    }
  } else {
    $stJs .= " jQuery('#rdoLiquidacaoConsumo').attr('checked', true); \n";
    $stJs .= montaCombos($_REQUEST['cod_conta_despesa'], 'liquidacao', 'materialConsumo');
    $stJs .= " jQuery('#stLancamentoDebitoAlmoxarifado').val(''); \n";
    $stJs .= " jQuery('#stLancamentoCreditoAlmoxarifado').val(''); \n";
  }

  return $stJs;
}

function montaCombos($inCodDespesa, $stAba = "", $stValorRadio = "")
{
  $obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;
  $stOrdem = " ORDER BY pc.cod_estrutural ";
  $stJs = "";

  switch ($stAba) {
    ###### ABA LIQUIDACAO
    case "liquidacao":

      $stComboDebito = "stLancamentoDebitoLiquidacao";
      $stComboCredito = "stLancamentoCreditoLiquidacao";
      $stFiltroCredito = '';
     
      //seleciona o filtro dos elementos da combo debito
      switch ($stValorRadio) {
        case "despesaPessoal":
          $stFiltroDebito = " AND pc.exercicio = '".Sessao::getExercicio()."'
                              AND pc.cod_estrutural like '3.%' ";

          $stFiltroCredito = " AND pc.exercicio = '".Sessao::getExercicio()."'
                               AND pc.cod_estrutural like '2.1.1%' ";
        break;
      
        case "entidadeRPPS":
          $stFiltroDebito = " AND pc.exercicio = '".Sessao::getExercicio()."'
                           AND ( pc.cod_estrutural like '3.1.1%' OR pc.cod_estrutural like '3.1.2%' OR pc.cod_estrutural like '3.1.3%' OR pc.cod_estrutural like '3.1.8%' OR pc.cod_estrutural like '3.1.9%' OR pc.cod_estrutural like '3.2.0%' OR pc.cod_estrutural like '3.2.1%' OR pc.cod_estrutural like '3.2.2%' OR pc.cod_estrutural like '3.2.4%' OR pc.cod_estrutural like '3.2.5%' OR pc.cod_estrutural like '3.2.9%') ";

          $stFiltroCredito = " AND pc.exercicio = '".Sessao::getExercicio()."'
                          AND pc.cod_estrutural like '2.1.1%' ";
          break;

        case 'materialConsumo':
          $stFiltroDebito = " AND pc.exercicio = '".Sessao::getExercicio()."'
                      AND pc.cod_estrutural like '1.1.5.6%' ";
          break;

        case 'materialPermanente':
          $stFiltroDebito = " AND pc.exercicio = '".Sessao::getExercicio()."'
                      AND pc.cod_estrutural like '1.2.3%' ";
          break;

        case 'demaisDespesas':
          $stFiltroDebito = " AND pc.exercicio = '".Sessao::getExercicio()."'
                      AND pc.cod_estrutural like '3.%' ";
          break;
      }
      if ($stFiltroCredito == '') {
          //seleciona o filtro dos elementos da combo credito
          $stFiltroCredito = " AND pc.exercicio = '".Sessao::getExercicio()."'
                          AND pc.cod_estrutural like '2.1.3.1.1.01%' ";
      }
      break;
  }
  
  $obTContabilidadePlanoConta->recuperaContaPlanoAnalitica($rsDebito, $stFiltroDebito, $stOrdem);
  $obTContabilidadePlanoConta->recuperaContaPlanoAnalitica($rsCredito, $stFiltroCredito, $stOrdem);

  $stJs .= "jQuery('#".$stComboDebito."').find('option').remove().end().append('<option value=\'\'>Selecione</option>'); \n";
  $stJs .= "jQuery('#".$stComboCredito."').find('option').remove().end().append('<option value=\'\'>Selecione</option>'); \n";

  $stAddOption = "";
  $stAddOption = ".append('<option value=\'\'>Selecione</option>')";

  $stJs .= "jQuery('#".$stComboCredito."').find('option').remove().end()".$stAddOption."; \n";

  while (!$rsDebito->eof()) {
    $stJs .= "jQuery('#".$stComboDebito."').append('<option value=\'".$rsDebito->getCampo('cod_conta')."\'>".$rsDebito->getCampo('cod_estrutural')." - ".$rsDebito->getCampo('nom_conta')."</option>'); \n";
    $rsDebito->proximo();
  }

  while (!$rsCredito->eof()) {
    $stJs .= "jQuery('#".$stComboCredito."').append('<option value=\'".$rsCredito->getCampo('cod_conta')."\'>".$rsCredito->getCampo('cod_estrutural')." - ".$rsCredito->getCampo('nom_conta')."</option>'); \n";
    $rsCredito->proximo();
  }

  return $stJs;
}

function montaTableDespesas()
{
    $obROrcamentoReceita = new ROrcamentoDespesa;
    $obROrcamentoReceita->obROrcamentoClassificacaoDespesa->setMascClassificacao($_REQUEST['mascara_classificacao']);
    $obROrcamentoReceita->listarDespesaConfiguracaoLancamentoDetalhadoNovo($rsLista);

    $obChkAcao = new Checkbox;
    $obChkAcao->setId('chkAcao_[cod_conta]');
    $obChkAcao->setName('chkAcao_[cod_conta]');
    $obChkAcao->setValue('[cod_conta]');
    $obChkAcao->obEvento->setOnClick('selecionaDespesa(this);');

    $obTable = new Table     ();
    $obTable->setRecordset    ($rsLista);
    $obTable->setSummary      ('Despesas');

    $obTable->Head->addCabecalho('Código Estrutural',10);
    $obTable->Head->addCabecalho('Descrição',40);
    $obTable->Head->addCabecalho('Ação',2);

    $obTable->Body->addCampo('[mascara_classificacao]','C');
    $obTable->Body->addCampo('[descricao]','E');
    $obTable->Body->addCampo($obChkAcao, 'C');

    $obTable->montaHTML(true);

    $obSpnListaDespesasDisponiveis = new Span;
    $obSpnListaDespesasDisponiveis->setStyle("height: 200px; overflow: scroll");
    $obSpnListaDespesasDisponiveis->setId("listaDespesasDisponiveis");
    $obSpnListaDespesasDisponiveis->setValue($obTable->getHtml());

    $obSpnListaDespesasDisponiveis->montaHTML();

    return $obSpnListaDespesasDisponiveis;
}

switch ($_REQUEST['stCtrl']) {
  case 'montaLancamentoDespesa':
    $stJs .= montaLancamentoDespesa();
    break;

  case 'carregaContasLancamentoLiquidacao':
    $stJs .= montaCombos($_REQUEST['cod_conta_despesa'], 'liquidacao', $_REQUEST['valor_radio']);
    break;

  case 'montaTableDespesas':
    echo montaTableDespesas()->getHTML();
    break;

  default:
    # code...
    break;
}

echo ($stJs);

?>
