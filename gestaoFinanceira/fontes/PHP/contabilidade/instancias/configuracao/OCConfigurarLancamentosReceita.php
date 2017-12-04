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

  $Id: OCConfigurarLancamentosReceita.php 66481 2016-09-01 20:15:15Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoReceita.class.php";
include_once CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php";

function montaLancamentoReceita()
{
    $obTContabilidadeConfiguracaoLancamentoReceita = new TContabilidadeConfiguracaoLancamentoReceita;
    $stFiltro = " WHERE configuracao_lancamento_receita.cod_conta_receita = ".$_REQUEST['cod_conta_receita']."
                    AND configuracao_lancamento_receita.estorno = 'false'
                    AND configuracao_lancamento_receita.exercicio = '".Sessao::getExercicio()."' ";
    $obTContabilidadeConfiguracaoLancamentoReceita->recuperaContasConfiguracaoReceita( $rsRecordSet, $stFiltro );

    $stJs  = " jQuery('#codContaReceitaLista').val(".$_REQUEST['cod_conta_receita']."); \n";

    $stJs .= "jQuery('#arrecadacaoDireta').removeAttr('disabled');          \n";
    $stJs .= "jQuery('#operacoesCredito').removeAttr('disabled');           \n";
    $stJs .= "jQuery('#alienacaoBens').removeAttr('disabled');              \n";
    $stJs .= "jQuery('#dividaAtiva').removeAttr('disabled');                \n";
    $stJs .= "jQuery('#stLancamentoCreditoReceita').removeAttr('disabled'); \n";
    $stJs .= "jQuery('#inCodContaCredito').val('');                         \n";
    $stJs .= "jQuery('#boArrecadacao').val('FALSE');                        \n";

    if ($rsRecordSet->getNumLinhas() > 0) {
        while (!$rsRecordSet->eof()) {
            $stJs .= " jQuery('#".$rsRecordSet->getCampo('tipo_arrecadacao')."').attr('checked', true); \n";
            $stJs .= montaCombos($rsRecordSet->getCampo('cod_conta_receita'), $rsRecordSet->getCampo('tipo_arrecadacao'));
            $stJs .= " jQuery('#stLancamentoCreditoReceita').val('".$rsRecordSet->getCampo('cod_conta')."'); \n";

            if($rsRecordSet->getCampo('bo_arrecadacao')=='t'){
                $stJs .= " jQuery('#inCodContaCredito').val('".$rsRecordSet->getCampo('cod_conta')."'); \n";
                $stJs .= " jQuery('#boArrecadacao').val('TRUE');                                        \n";

                $stJs .= "jQuery('#arrecadacaoDireta').attr('disabled','disabled');            \n";
                $stJs .= "jQuery('#operacoesCredito').attr('disabled','disabled');             \n";
                $stJs .= "jQuery('#alienacaoBens').attr('disabled','disabled');                \n";
                $stJs .= "jQuery('#dividaAtiva').attr('disabled','disabled');                  \n";

                $stJs .= "jQuery('#stLancamentoCreditoReceita').attr('disabled','disabled');   \n";

                $stMensagem = "Receita (".$_REQUEST['descricao'].") com arrecadações efetuadas, configuração bloqueada!";
                $stJs .= " alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."'); \n";
            }

            $rsRecordSet->proximo();
        }
    } else {
        $stJs .= " jQuery('#arrecadacaoDireta').attr('checked', true); \n";
        $stJs .= montaCombos($_REQUEST['cod_conta_receita'], 'arrecadacaoDireta');
        $stJs .= " jQuery('#stLancamentoCreditoReceita').val(''); \n";
    }

    return $stJs;
}

function montaCombos($inCodDespesa, $stValorRadio = "")
{
    $obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;
    $stOrdem = " ORDER BY pc.cod_estrutural ";
    $stJs = "";

    switch ($stValorRadio) {
        case "arrecadacaoDireta":
            $stFiltro  = " AND pc.exercicio = '".Sessao::getExercicio()."' \n";
            $stFiltro .= " AND  ( pc.cod_estrutural like '4.%' ) ";
        break;

        case "operacoesCredito":
            $stFiltro  = " AND pc.exercicio = '".Sessao::getExercicio()."' \n";
            $stFiltro .= " AND pc.cod_estrutural like '2.1.2.%' ";
        break;

        case "alienacaoBens":
            $stFiltro  = " AND pc.exercicio = '".Sessao::getExercicio()."' \n";
            $stFiltro .= " AND pc.cod_estrutural like '1.2.3.%' ";
        break;

        case "dividaAtiva":
            $stFiltro  = " AND pc.exercicio = '".Sessao::getExercicio()."' \n";
            if(Sessao::getExercicio()>=2015)
                $stFiltro .= " AND ( pc.cod_estrutural like '1.1.2.3.%' OR pc.cod_estrutural like '1.1.2.4.%' OR pc.cod_estrutural like '1.1.2.5.1%' )";
            else
                $stFiltro .= " AND ( pc.cod_estrutural like '1.1.2.3.%' OR pc.cod_estrutural like '1.1.2.4.%')";
        break;
    }

    $obTContabilidadePlanoConta->recuperaContaPlanoAnalitica($rsCredito, $stFiltro, $stOrdem);

    $stJs .= "jQuery('#stLancamentoCreditoReceita').find('option').remove().end().append('<option value=\'\'>Selecione</option>'); \n";

    while (!$rsCredito->eof()) {
        $stJs .= "jQuery('#stLancamentoCreditoReceita').append('<option value=\'".$rsCredito->getCampo('cod_conta')."\'>".$rsCredito->getCampo('cod_estrutural')." - ".$rsCredito->getCampo('nom_conta')."</option>'); \n";
        $rsCredito->proximo();
    }

    return $stJs;
}

$stJs = '';
switch ($request->get('stCtrl')) {
    case 'montaLancamentoReceita':
        $stJs .= montaLancamentoReceita();
    break;

    case 'carregaContasLancamento':
        $stJs .= montaCombos($request->get('cod_conta_receita'), $request->get('valor_radio'));
    break;
}

echo ($stJs);
?>
