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
 * Pagina Oculta de Solicitação de compra
 * Data de Criação: 11/09/2006

 * @author Analista     : Diego Victoria
 * @author Desenvolvedor: Rodrigo

 * Casos de uso: uc-03.04.01

 $Id: OCIMontaDotacaoDesdobramento.php 64420 2016-02-19 12:11:50Z arthur $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoPreEmpenho.class.php";

$stCtrl        = $request->get('stCtrl');
$inCodEntidade = $request->get('inCodEntidade');
$obREmpenhoAutorizacaoEmpenho = new REmpenhoPreEmpenho;
$obREmpenhoEmpenho            = new REmpenhoEmpenho;
$obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenho->setExercicio( Sessao::getExercicio() );

if ($request->get('HdnCodEntidade')) {
    $request->set('inCodEntidade',$request->get('HdnCodEntidade'));
}

switch ($request->get('stCtrl')) {
    case 'buscaDespesaDiverso':
        if ($request->get('inCodDespesa') != '') {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $request->get("inCodDespesa") );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodCentroCusto( $request->get("inCodCentroCusto") );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $request->get("inCodEntidade") );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );
            if ( $rsDespesa->getNumLinhas() > -1 ) {
                $js .= "jq_('#stNomDespesa').html('".$rsDespesa->getCampo('descricao')."');";

                $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho();
                $obTEmpenhoPreEmpenho->setDado( 'exercicio'  ,Sessao::getExercicio() );
                $obTEmpenhoPreEmpenho->setDado( 'cod_despesa',$request->get("inCodDespesa") );
                $obTEmpenhoPreEmpenho->setDado( "entidade"     , $request->get('HdnCodEntidade')   );
                $obTEmpenhoPreEmpenho->setDado( "dt_empenho"   , $request->get('HdnDtSolicitacao') );
                $obTEmpenhoPreEmpenho->setDado( "tipo_emissao" , "R" );
                $obTEmpenhoPreEmpenho->recuperaSaldoAnteriorDataEmpenho( $rsSaldoAnterior );

                $nuSaldoDotacao = $rsSaldoAnterior->getCampo('saldo_anterior');

                $js	.= "var saldoDotacao = '".number_format($nuSaldoDotacao,2,',','.')."';";
                $js .= "jq_('#nuSaldoDotacao').html(saldoDotacao);";
                $js .= "jq_('#nuHdnSaldoDotacao').val(".number_format($nuSaldoDotacao,2,'.','').");";

                $js .= montaComboDiverso($request);

            } else {
                $js .= "alertaAviso('@Dotação inválida. (".$request->get("inCodDespesa").")','form','erro','".Sessao::getId()."');";
                $js .= "jq_('#stNomDespesa').html('&nbsp;');";
                $js .= "jq_('#inCodDespesa').val('');";
                $js .= "jq_('#nuSaldoDotacao').html('&nbsp;');";
                $js .= "jq_('#stCodClassificacao').empty().append(new Option('Selecione',''));";
            }
        } else {
            $js .= "jq_('#stNomDespesa').html('&nbsp;');";
            $js .= "jq_('#inCodDespesa').val('');";
            $js .= "jq_('#nuSaldoDotacao').html('&nbsp;');";
            $js .= "jq_('#stCodClassificacao').empty().append(new Option('Selecione',''));";            
        }
        SistemaLegado::executaFrameOculto($js);
        break;
}

function montaComboDiverso(Request $request)
{
    global $obREmpenhoAutorizacaoEmpenho;
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $request->get('inCodDespesa') );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa( $rsConta );
    $stCodClassificacao = $rsConta->getCampo( "cod_estrutural" );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $stCodClassificacao );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( "" );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarCodEstruturalDespesa( $rsClassificacao );
    if ( $rsClassificacao->getNumLinhas() > -1 ) {
        $inContador = 1;
        $js .= "limpaSelect(f.stCodClassificacao,0); \n";
        if ($request->get('boMostraSintetico') == 'true') {
            $js .= "f.stCodClassificacao.options[0] = new Option('".$rsClassificacao->arElementos[0]['cod_estrutural'].' - '.$rsClassificacao->arElementos[0]['descricao']."','".$rsClassificacao->arElementos[0]['cod_conta']."', 'selected');\n";
        } else {
            $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
        }
        while ( !$rsClassificacao->eof() ) {
            $selected = "";
            $stMascaraReduzida = $rsClassificacao->getCampo("mascara_reduzida");
            if ($stMascaraReduzidaOld) {
                if ( $stMascaraReduzidaOld != substr($stMascaraReduzida,0,strlen($stMascaraReduzidaOld)) ) {
                    if ($inCodContaOld == $request->get("codClassificacao") or $inCodContaOld == $request->get("stCodClassificacao")) {
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    $arOptions[]['reduzido']                  = $stMascaraReduzidaOld;
                    $arOptions[count($arOptions)-1]['option'] = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$inCodContaOld."','".$selected."','".$selected."'";
                    $inContador++;
                }
            }
            $inCodContaOld        = $rsClassificacao->getCampo("cod_conta");
            $stCodEstruturalOld   = $rsClassificacao->getCampo("cod_estrutural");
            $stDescricaoOld       = $rsClassificacao->getCampo("descricao");
            $stMascaraReduzidaOld = $stMascaraReduzida;
            $stMascaraReduzida    = "";
            $rsClassificacao->proximo();
        }
        if ($stMascaraReduzidaOld) {
            if ($inCodContaOld == $request->get('codClassificacao')) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            $arOptions[]['reduzido'] = $stMascaraReduzidaOld;
            $arOptions[count($arOptions)-1]['option'] = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$inCodContaOld."','".$selected."','".$selected."'";

        }

        // Remove Contas Sintéticas
        if (is_array($arOptions)) {
            $count = 0;
            for ( $x=0 ; $x<count($arOptions) ; $x++ ) {
                for ( $y=0 ; $y<count($arOptions) ; $y++ ) {
                    $estruturalX = str_replace( '.', '', $arOptions[$x]['reduzido'] );
                    $estruturalY = str_replace( '.', '', $arOptions[$y]['reduzido'] );

                    if ((strpos($estruturalY,$estruturalX)!==false) && ($estruturalX !== $estruturalY) ) {
                        $count++;
                    }
                }
                if ($count>=1) {
                    unset($arOptions[$x]);
                }
                $count = 0;
            }
            $inContador = ( count($arOptions)>1 )? 1 : 0;
            asort( $arOptions );
            foreach ($arOptions as $option) {
                $js .= "if (f.inCodDespesa.value!='') {                                                            \n";
                $js .= "  f.stCodClassificacao.options[".$inContador++."] = new Option(". $option['option'] ."); \n";
                $js .= "}                                                                                        \n";
            }
        }

    } else {
        $js .= "limpaSelect(f.stCodClassificacao,0); \n";
        $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
    }
    $js .= "LiberaFrames(true,false);";

    return $js;
}

?>
