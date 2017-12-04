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
    * Página de Formulário para Estorno de Pagamento de Despesas Extra Orçamentárias
    * Data de Criação   : 05/09/2006
    *
    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 31732 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.27

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function verificaCodBarras($inCodBarras)
{
    $inCodEntidade = ltrim(substr($inCodBarras, 17, 2),'0');
    $inCodRecibo   = ltrim(substr($inCodBarras,  9, 4),'0');
    $stJs .= "d.getElementById('inCodRecibo').value = '".$inCodRecibo."'; \n";
    $stJs .= "d.getElementById('inCodEntidade').value = '".$inCodEntidade."';\n ";
    $stJs .= "d.getElementById('stNomEntidade').value = '".$inCodEntidade."';\n ";

    $stJs .= buscaDadosRecibo( $inCodEntidade, $inCodRecibo );

    return $stJs;
}

function atualizaStatus()
{
    include_once( CLA_IAPPLETTERMINAL );

    $obFormulario = new Formulario;
    $obFormulario->montaInnerHtml();

    $obIAppletTerminal = new IAppletTerminal( $obFormulario );
    $stText = " - Terminal Logado: ".Sessao::read('inCodTerminal');
    $stText .=" - Saldo da Conta Caixa: R$ ".$obIAppletTerminal->getSaldoCaixa();

    $stJs =  "parent.frames['telaStatus'].document.";
    $stJs .= "getElementById('stTerminalLogado').innerHTML = '$stText';\n";

    return $stJs;
}

function limparCampos()
{
    $stJs = "// frm.reset();\n
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
    ";

    return $stJs;

}
function verificaValorEstornado($inCodLote, $inCodEntidade, $stTipo, $nuValorPago)
{
        include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransferenciaEstornada.class.php" );
        $obTTesourariaTransferenciaEstornada = new TTesourariaTransferenciaEstornada();
        $obTTesourariaTransferenciaEstornada->setDado('exercicio', Sessao::getExercicio() );
        $obTTesourariaTransferenciaEstornada->setDado('cod_entidade', $inCodEntidade );
        $obTTesourariaTransferenciaEstornada->setDado('cod_lote', $inCodLote );
        $obTTesourariaTransferenciaEstornada->setDado('tipo', $stTipo );
        $obErro = $obTTesourariaTransferenciaEstornada->verificaValorEstornado( $nuValorEstornado, $boTransacao );
        if (!$obErro->ocorreu()) {
            $nuValorPago = str_replace(',','.', str_replace('.','',$nuValorPago));
            $nuValorMaxEstorno = bcsub($nuValorPago,$nuValorEstornado, 2);
            $stJs .= " d.getElementById('lblValorEstornado').innerHTML = '".number_format( $nuValorEstornado, 2, ',', '.' )."';\n
                       d.getElementById('nuValorEstornado').value = '".$nuValorEstornado."';\n
                       d.getElementById('nuValorMaxEstorno').value = '".$nuValorMaxEstorno."';\n
                       d.getElementById('nuValorPago').value = '".$nuValorPago."';\n
                       jq('#nuValorEstorno_label').html('".number_format($nuValorMaxEstorno,2,',','.')."');\n
                       d.getElementById('nuValorEstorno').value = '".number_format($nuValorMaxEstorno,2,',','.')."';\n
                       d.getElementById('stObservacoes').focus();
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

    $stJs .= "d.getElementById('inCodBoletim').value  = '".$inCodBoletimAberto."';\n";
    $stJs .= "d.getElementById('stDtBoletim').value  = '".$stDtBoletimAberto."';\n";
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
           $stFiltro = " AND (t.valor - COALESCE(te.valor,0)) > 0 ";
           $obErro = $obTTesourariaTransferencia->recuperaDadosReciboPagamentoExtra( $rsRecordSet, $stFiltro );
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
                                          d.getElementById('inCodCredor').value = '".$rsRecordSet->getCampo("cod_credor")."'; ";

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
                                          d.getElementById('inCodPlanoDebito').value = '".$rsRecordSet->getCampo("cod_plano_despesa")."';\n

                                          d.getElementById('inCodPlanoCredito').focus();\n
                                          d.getElementById('inCodPlanoCredito').value = '".$rsRecordSet->getCampo("cod_plano_banco")."';\n

                                          d.getElementById('Ok').focus();\n
                             ";
                              $stJs .= buscaBoletim( $rsRecordSet->getCampo("cod_boletim"), '', $rsRecordSet->getCampo("cod_entidade") );
                    } else {
                           $stJs .= "alertaAviso('@O pagamento para este recibo (".$inCodRecibo.") já foi totalmente estornado.','form','erro','".Sessao::getId()."');\n";
                           $stJs .= limparCampos();
                    }

               } else {
                    $stJs .= "alertaAviso('@Código de recibo inválido (".$inCodRecibo.") ou não está pago.','form','erro','".Sessao::getId()."');\n";
                    $stJs .= limparCampos();
               }
           }
        }

        return $stJs;
}

function verificaVinculoCheque($arParam)
{
    include CAM_GF_TES_CONTROLE . 'CTesourariaCheque.class.php';
    include CAM_GF_TES_NEGOCIO  . 'RTesourariaCheque.class.php';
    $obRTesourariaCheque = new RTesourariaCheque();
    $obCTesourariaCheque = new CTesourariaCheque($obRTesourariaCheque);

    if ($arParam['inCodRecibo']) {
        //Parametros necessarios para buscar os cheques vinculados ao recibo extra
        $arParam = array( 'inCodEntidade'      => $arParam['inCodEntidade']
                         ,'inCodReciboExtra'   => $arParam['inCodRecibo']
                         ,'stExercicio'        => Sessao::getExercicio()
                        );
        $obErro = $obCTesourariaCheque->obModel->listChequesReciboExtra($rsCheque,$arParam);
    }

    if ($rsCheque) {
        if ($rsCheque->getNumLinhas() > 0) {
            $stHTML = $obCTesourariaCheque->buildListaChequeEmissao($rsCheque,'Lista de Cheques vinculados a Despesa Extra');
            $stJs .= "setLabel('nuValorEstorno',false);";
            $stJs .= "jq('#spnCheques').html('" . $stHTML . "');";

            //coloca os cheques na sessao
            Sessao::write('arCheques',$rsCheque->arElementos);
        } else {
          $stJs .= "setLabel('nuValorEstorno',true);";
          $stJs .= "jq('#spnCheques').html('');";
        }
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
case 'limparCampos':
    $stJs  = limparCampos();
    break;

case 'buscaBoletim':
    $stJs  = buscaBoletim( $_GET['inCodBoletim'],
                           $_GET['stDtBoletim'],
                           $_GET['inCodEntidade'] );
    break;

case 'buscaDadosRecibo':
    $stJs .= buscaDadosRecibo( $_GET['inCodEntidade'],
                               $_GET['inCodRecibo'] );
    break;

case 'montaBoletim':
    $stJs .= montaBoletim( $_GET['inCodEntidade'] );
    break;

case 'verificaValorEstornado':
    $stJs .= verificaValorEstornado( $_GET['inCodLote'],
                                     $_GET['inCodEntidade'],
                                     $_GET['stTipo'],
                                     $_GET['nuValorPago'] );
    break;

case 'atualizaStatus':
    $stJs = atualizaStatus();
    break;

case 'verificaCodBarras':
    $stJs = verificaCodBarras( $_GET['inCodBarras'] );
    break;

case 'verificaVinculoCheque':
    $stJs = verificaVinculoCheque($_GET);

    break;

}
if ($stJs) {
    echo $stJs;
}
?>
