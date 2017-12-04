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
    * Página de Formulário para Filtro de Estorno de Arrecadação Extra Orçamentária
    * Data de Criação   : 14/09/2006
    *
    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Id: OCManterArrecadacaoReceitaExtraEstorno.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 30729 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.26

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function verificaCodBarras($inCodBarras)
{
    $inCodEntidade = ltrim(substr($inCodBarras, 16, 2),'0');
    $inCodRecibo   = ltrim(substr($inCodBarras,  8, 4),'0');

    $stJs .= "d.getElementById('inCodRecibo').value = '".$inCodRecibo."'; \n";
    $stJs .= "d.getElementById('inCodEntidade').value = '".$inCodEntidade."';\n ";
    $stJs .= "d.getElementById('stNomEntidade').value = '".$inCodEntidade."';\n ";

    $stJs .= buscaDadosRecibo( $inCodEntidade, $inCodRecibo );

    return $stJs;
}

function limparCampos()
{
    $stJs = "//document.getElementById('frm').reset();\n
             document.getElementById('inCodCredor').value = '';\n
             document.getElementById('inCodRecibo').value = '';\n
             document.getElementById('stNomCredor').innerHTML = '&nbsp;';\n
             if (document.getElementById('imgRecurso')) {
                document.getElementById('imgRecurso').style.display = 'inline';\n
             } else {
                document.getElementById('inCodUso').disabled = false;
                document.getElementById('inCodDestinacao').disabled = false;
                document.getElementById('inCodEspecificacao').disabled = false;
                document.getElementById('inCodDetalhamento').disabled = false;
                document.getElementById('stDestinacaoRecurso').focus();
                document.getElementById('stDestinacaoRecurso').value = '';
                document.getElementById('inCodRecibo').focus();
             }
             document.getElementById('inCodBoletim').value = '';
             document.getElementById('stDtBoletim').value = '';
             document.getElementById('inCodPlanoCredito').value = '';
             document.getElementById('stNomContaCredito').innerHTML = '&nbsp;';
             document.getElementById('inCodPlanoDebito').value = '';
             document.getElementById('stNomContaDebito').innerHTML = '&nbsp;';
             document.getElementById('inCodEntidade').focus();
    ";

    return $stJs;

}
function verificaValorEstornado($inCodLote, $inCodEntidade, $stTipo, $nuValorArrecadado)
{
        $boTransacao = "";
        include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransferenciaEstornada.class.php" );
        $obTTesourariaTransferenciaEstornada = new TTesourariaTransferenciaEstornada();
        $obTTesourariaTransferenciaEstornada->setDado('exercicio', Sessao::getExercicio() );
        $obTTesourariaTransferenciaEstornada->setDado('cod_entidade', $inCodEntidade );
        $obTTesourariaTransferenciaEstornada->setDado('cod_lote', $inCodLote );
        $obTTesourariaTransferenciaEstornada->setDado('tipo', $stTipo );
        $obErro = $obTTesourariaTransferenciaEstornada->verificaValorEstornado( $nuValorEstornado, $boTransacao );
        if (!$obErro->ocorreu()) {
            $nuValorArrecadado = str_replace(',','.', str_replace('.','',$nuValorArrecadado));
            $nuValorMaxEstorno = bcsub($nuValorArrecadado, $nuValorEstornado, 4);
            $stJs = " jq('#lblValorEstornado').html('".number_format( $nuValorEstornado, 2, ',', '.' )."');\n
                       jq('#nuValorEstornado').val('".$nuValorEstornado."');\n
                       jq('#nuValorMaxEstorno').val('".$nuValorMaxEstorno."');\n
                       jq('#nuValorArrecadado').val('".$nuValorArrecadado."');\n
                       jq('#nuValorEstorno').val('".number_format($nuValorMaxEstorno, 2, ',', '.')."');\n
                       jq('#stObservacoes').focus();
            ";
        }

        return $stJs;
}

function montaBoletim($inCodEntidade)
{
    include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
    $obRTesourariaBoletim = new RTesourariaBoletim;
    $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
    $obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.Sessao::getExercicio() ) );
    $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
    $obErro = $obRTesourariaBoletim->buscarCodigoBoletim( $inCodBoletimAberto, $stDtBoletimAberto );

    $stJs .= "d.getElementById('LblCodBoletimAberto').innerHTML = '".$inCodBoletimAberto."';\n";
    $stJs .= "d.getElementById('LblDtBoletimAberto').innerHTML  = '".$stDtBoletimAberto."';\n";

    if ($obErro->ocorreu()) {
        if (!$inCodBoletimAberto) {
               $stJs .= "f.Ok.disabled = true;";
        } else $stJs .= "f.Ok.disabled = false;";
         $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}

function buscaBoletim($inCodBoletim, $stDtBoletim, $inCodEntidade)
{
    if ($inCodEntidade) {
        include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaBoletim.class.php" );
        $obTTesourariaBoletim = new TTesourariaBoletim();
        if ($stDtBoletim) {
            $stFiltro .= " AND TB.exercicio = '".Sessao::getExercicio()."'";
            $stFiltro .= " AND TB.dt_boletim = TO_DATE( '".$stDtBoletim."', 'dd/mm/yyyy' )";
            $stFiltro .= " AND TB.cod_entidade = ".$inCodEntidade." ";
            $obErro = $obTTesourariaBoletim->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getNumLinhas() == 1) {
                    $stJs .= "d.getElementById('inCodBoletim').value = '".$rsRecordSet->getCampo('cod_boletim')."';\n";
                } else {
                    $stJs .= "alertaAviso('Não há boletim para a data informada.','form','erro','".Sessao::getId()."');\n";
                    $stJs .= "d.getElementById('stDtBoletim').value = '';\n";
                }
            }
        }
        if ($inCodBoletim) {
            $stFiltro  = " AND TB.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro .= " AND TB.cod_boletim = ".$inCodBoletim." ";
            $stFiltro .= " AND TB.cod_entidade = ".$inCodEntidade."  ";
            $obErro = $obTTesourariaBoletim->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getNumLinhas() == 1) {
                    $stJs .= "d.getElementById('stDtBoletim').value = '".$rsRecordSet->getCampo('data_boletim')."';\n";
                } else {
                    $stJs .= "alertaAviso('Não há boletim com o número informado.','form','erro','".Sessao::getId()."');\n";
                    $stJs .= "d.getElementById('inCodBoletim').value = '';\n";
                }
            }
        }
    } else {
        $stJs .= "alertaAviso('@Informe uma entidade','form','erro','".Sessao::getId()."');\n";
        $stJs .= "d.getElementById('stDtBoletim').value = '';\n";
        $stJs .= "d.getElementById('inCodBoletim').value = '';\n";
    }

    return $stJs;
}

function buscaDadosRecibo($inCodEntidade, $inCodRecibo)
{
        include_once ( CAM_GF_EMP_MAPEAMENTO.'TEmpenhoConfiguracao.class.php' );
        $obTEmpenhoConfiguracao = new TEmpenhoConfiguracao;
        $obTEmpenhoConfiguracao->setDado( 'parametro', 'numero_empenho' );
        $obTEmpenhoConfiguracao->consultar ();
        $tipoNumeracao = $obTEmpenhoConfiguracao->getDado( 'valor' );

        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php"        );
        $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
        $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
        $obTOrcamentoConfiguracao->consultar();
        if($obTOrcamentoConfiguracao->getDado("valor") == 'true') // Recurso com Destinação de Recurso || 2008 em diante
            $boDestinacao = true;

        if ($tipoNumeracao == 'P' && !$inCodEntidade) {
                $stJs .= "alertaAviso('@Você deve selecionar uma entidade.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "d.getElementById('inCodRecibo').value = '';\n";
        } else {
           include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransferencia.class.php" );
           $obTTesourariaTransferencia = new TTesourariaTransferencia();
           $obTTesourariaTransferencia->setDado("exercicio", Sessao::getExercicio() );
           $obTTesourariaTransferencia->setDado("cod_recibo", $inCodRecibo );
           if ($tipoNumeracao == 'P') {
               $obTTesourariaTransferencia->setDado("cod_entidade", $inCodEntidade );
           }
           $obErro = $obTTesourariaTransferencia->recuperaDadosReciboArrecadacaoExtra( $rsRecordSet, $boTransacao );
           if (!$obErro->ocorreu()) {
               if ($rsRecordSet->getNumLinhas() == 1) {
                    if ($rsRecordSet->getCampo("valor") > $rsRecordSet->getCampo("valor_estornado") ) {
                        if ($tipoNumeracao == 'G') {
                            $stJs .= "   d.getElementById('inCodEntidade').value = '".$rsRecordSet->getCampo("cod_entidade")."';\n";
                            $stJs .= "   d.getElementById('stNomEntidade').value = '".$rsRecordSet->getCampo("cod_entidade")."';\n";
                        }
                            $stJs .= "   d.getElementById('inCodBoletim').focus();\n
                                         d.getElementById('inCodBoletim').value = '".$rsRecordSet->getCampo("cod_boletim")."';\n

                                         d.getElementById('inCodCredor').focus();\n
                                         d.getElementById('inCodCredor').value = '".$rsRecordSet->getCampo("cod_credor")."';\n ";

                            if ($boDestinacao) {
                                $stJs .= "  document.getElementById('stDestinacaoRecurso').focus();
                                            document.getElementById('stDestinacaoRecurso').value = '".$rsRecordSet->getCampo('masc_recurso')."';
                                            document.getElementById('inCodRecurso').value = '".$rsRecordSet->getCampo('cod_recurso')."';
                                ";
                            } else {
                                $stJs .= "  d.getElementById('inCodRecurso').focus();\n
                                            d.getElementById('inCodRecurso').value = '".$rsRecordSet->getCampo("cod_recurso")."';\n
                                ";
                            }

                            $stJs .= "   d.getElementById('inCodPlanoDebito').focus();\n
                                         d.getElementById('inCodPlanoDebito').value = '".$rsRecordSet->getCampo("cod_plano_banco")."';\n

                                         d.getElementById('inCodPlanoCredito').focus();\n
                                         d.getElementById('inCodPlanoCredito').value = '".$rsRecordSet->getCampo("cod_plano_receita")."';\n

                                         d.getElementById('Ok').focus();\n
                            ";
                             $stJs .= buscaBoletim( $rsRecordSet->getCampo("cod_boletim"), '', $rsRecordSet->getCampo("cod_entidade") );
                    } else {
                        $stJs .= "alertaAviso('@A arrecadação para este recibo (".$inCodRecibo.") já foi totalmente estornada.','form','erro','".Sessao::getId()."');\n";
                        $stJs .= limparCampos();
                    }
               } else {
                    $stJs .= "alertaAviso('@Código de recibo inválido (".$inCodRecibo.") ou não está arrecadado.','form','erro','".Sessao::getId()."');\n";
                    $stJs .= limparCampos();
               }
           }
        }

        return $stJs;
}

function atualizaStatus()
{
    include_once( CLA_IAPPLETTERMINAL );
    $obIAppletTerminal = new IAppletTerminal( $obFormulario );
    $stStatus = " - Terminal Logado: ".Sessao::read('inCodTerminal');
    $stStatus .=" - Saldo da Conta Caixa: R$ ".$obIAppletTerminal->getSaldoCaixa();

    $stJs .= "parent.frames['telaStatus'].document.";
    $stJs .= "getElementById('stTerminalLogado').innerHTML = '$stStatus';\n";

    return $stJs;
}

switch ( $request->get('stCtrl') ) {
case 'atualizaStatus':
    $stJs  = atualizaStatus();
    break;

case 'limparCampos':
    $stJs  = limparCampos();
    break;

case 'buscaBoletim':
    $stJs  = buscaBoletim( $request->get('inCodBoletim' ),
                           $request->get('stDtBoletim'  ),
                           $request->get('inCodEntidade') );
    break;

case 'buscaDadosRecibo':
    $stJs = buscaDadosRecibo( $request->get('inCodEntidade'),
                              $request->get('inCodRecibo') );
    break;

case 'montaBoletim':
    $stJs = montaBoletim( $request->get('inCodEntidade') );
    break;

case 'verificaValorEstornado':
    $stJs = verificaValorEstornado( $request->get('inCodLote'),
                                    $request->get('inCodEntidade'),
                                    $request->get('stTipo'),
                                    $request->get('nuValorArrecadado') );
    break;

case 'verificaCodBarras':
    $stJs = verificaCodBarras( $request->get('inCodBarras') );
    break;

}
if ( isset($stJs) ) {
    echo $stJs;
}
?>
