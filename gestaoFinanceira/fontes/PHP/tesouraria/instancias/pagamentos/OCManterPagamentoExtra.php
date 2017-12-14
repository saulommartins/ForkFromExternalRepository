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
    * Página de Formulário para Pagamento de Despesas Extra Orçamentárias
    * Data de Criação   : 23/08/2006
    *
    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 32235 $
    $Name$
    $Author: cako $
    $Date: 2007-12-07 16:11:31 -0200 (Sex, 07 Dez 2007) $

    * Casos de uso: uc-02.04.27

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_CONT_COMPONENTES."IPopUpContaAnalitica.class.php");
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php");
include CAM_GF_TES_NEGOCIO  . 'RTesourariaCheque.class.php';
include CAM_GF_TES_CONTROLE . 'CTesourariaCheque.class.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php");
include_once ( CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoTransferencia.class.php' );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php" );

function verificaCodBarras($inCodBarras)
{
    $inCodEntidade = ltrim(substr($inCodBarras, 17, 2),'0');
    $inCodRecibo   = ltrim(substr($inCodBarras, 9, 4),'0');

    $stJs .= "d.getElementById('inCodRecibo').value = '".$inCodRecibo."'; \n";
    $stJs .= "d.getElementById('inCodEntidade').value = '".$inCodEntidade."';\n ";
    $stJs .= "d.getElementById('stNomEntidade').value = '".$inCodEntidade."';\n ";

    $obCmbEntidades = Sessao::read('obIEntidade');
    $stJs .= montaSpanContas($obCmbEntidades);
    $stJs .= buscaDadosRecibo( $inCodEntidade, $inCodRecibo );

    return $stJs;
}

function limparCampos()
{
    // Limpa os dados para que não apareça os dados da Conta Caixa/Banco quando trocar a entidade e voltar ela depois
    Sessao::remove('inCodPlanoCredito');
    Sessao::remove('inCodEntidade');
    Sessao::remove('stNomContaCredito');
    Sessao::remove('stNomContaCredito');

    $stJs = "//document.getElementById('frm').reset();\n
            document.getElementById('inCodEntidade').focus();\n
            document.getElementById('stNomEntidade').readOnly = false;\n
            document.getElementById('inCodEntidade').readOnly = false;\n
            document.getElementById('imgCredor').style.display = 'inline';\n
            document.getElementById('inCodCredor').readOnly = false;\n
            document.getElementById('inCodCredor').value = '';\n
            document.getElementById('inCodRecibo').value = '';\n
            document.getElementById('stNomCredor').innerHTML = '&nbsp;';\n
            
            if(document.getElementById('inCodRecurso') != null) {
                if (document.getElementById('imgRecurso')) {
                    document.getElementById('imgRecurso').style.display = 'inline';\n
                    document.getElementById('inCodRecurso').readOnly = false;\n
                } else {
                    document.getElementById('stDestinacaoRecurso').readOnly = false;
                    document.getElementById('inCodUso').disabled = false;
                    document.getElementById('inCodDestinacao').disabled = false;
                    document.getElementById('inCodEspecificacao').disabled = false;
                    document.getElementById('inCodDetalhamento').disabled = false;
                    document.getElementById('stDestinacaoRecurso').focus();
                    document.getElementById('stDestinacaoRecurso').value = '';
                    document.getElementById('inCodRecibo').focus();
                }
            }
            document.getElementById('nuValor').readOnly = false;\n
            document.getElementById('nuValor').value = '';\n
            document.getElementById('inCodHistorico').value = '';\n
            document.getElementById('stNomHistorico').innerHTML = '&nbsp';\n
            document.getElementById('stNomHistorico').value = '';\n
            if (document.getElementById('inCodEntidade').value == '') {
               document.getElementById('spnBoletim').innerHTML = '';\n
               document.getElementById('spnContas').innerHTML = '';\n
            } else {
               montaParametrosGET('montaSpanContas' , 'inCodEntidade');\n
            }
            jq('#spnCheques').html('');
    ";

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
           $obCmbEntidades = Sessao::read('obIEntidade');
           include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaReciboExtra.class.php" );
           $obTTesourariaReciboExtra = new TTesourariaReciboExtra();
           $obTTesourariaReciboExtra->setDado("stExercicio", Sessao::getExercicio() );
           $obTTesourariaReciboExtra->setDado("inCodRecibo", $inCodRecibo );
           if ($tipoNumeracao == 'P') {
               $obTTesourariaReciboExtra->setDado("inCodEntidade", $inCodEntidade );
           }
           $obErro = $obTTesourariaReciboExtra->recuperaReciboExtraPagamento( $rsRecordSet, $boTransacao );
           if (!$obErro->ocorreu()) {
               if ($rsRecordSet->getNumLinhas() == 1) {
                   if ($tipoNumeracao == 'G') {
                       $stJs .= "   d.getElementById('inCodEntidade').value = '".$rsRecordSet->getCampo("cod_entidade")."';\n";
                       $stJs .= "   d.getElementById('stNomEntidade').value = '".$rsRecordSet->getCampo("cod_entidade")."';\n";
                       $stJs .= montaSpanContas($obCmbEntidades);
                   }
                       $stJs .= "   d.getElementById('stDtRecibo').value = '".$rsRecordSet->getCampo("dt_recibo")."';\n
                                    d.getElementById('inCodEntidade').readOnly = true;\n
                                    d.getElementById('stNomEntidade').readOnly = true;\n

                                    d.getElementById('inCodCredor').focus();\n
                                    d.getElementById('inCodCredor').value = '".$rsRecordSet->getCampo("cod_credor")."';\n
                                    if (d.getElementById('inCodCredor').value) {
                                        d.getElementById('inCodCredor').readOnly = true;\n
                                        d.getElementById('imgCredor').style.display = 'none';\n
                                    } else {
                                        d.getElementById('inCodCredor').readOnly = false;\n
                                        d.getElementById('imgCredor').style.display = 'inline';\n
                                    } ";
                        if ($boDestinacao) {
                            $stJs .= "
                                    if(document.getElementById('inCodRecurso') != null) {
                                        document.getElementById('stDestinacaoRecurso').focus();
                                        document.getElementById('stDestinacaoRecurso').value = '".$rsRecordSet->getCampo('masc_recurso')."';
                                        document.getElementById('inCodRecurso').value = '".$rsRecordSet->getCampo('cod_recurso')."';
                                        document.getElementById('stDestinacaoRecurso').readOnly = true;
                                        document.getElementById('inCodUso').disabled = true;
                                        document.getElementById('inCodDestinacao').disabled = true;
                                        document.getElementById('inCodEspecificacao').disabled = true;
                                        document.getElementById('inCodDetalhamento').disabled = true;
                                    }
                            ";
                        } else {
                            $stJs .= "
                                    if(document.getElementById('inCodRecurso') != null) {
                                        d.getElementById('inCodRecurso').focus();\n
                                        d.getElementById('inCodRecurso').value = '".$rsRecordSet->getCampo("cod_recurso")."';\n
                                        if (d.getElementById('inCodRecurso').value) {
                                           d.getElementById('inCodRecurso').readOnly = true;\n
                                           d.getElementById('imgRecurso').style.display = 'none';\n
                                        } else {
                                           d.getElementById('inCodRecurso').readOnly = false;\n
                                           d.getElementById('imgRecurso').style.display = 'inline';\n
                                        }
                                    } ";
                        }

                        //Verifica se existem cheques vinculados a despesa extra
                        $obRTesourariaCheque = new RTesourariaCheque();
                        $obCTesourariaCheque = new CTesourariaCheque($obRTesourariaCheque);
                        //Parametros necessarios para buscar os cheques vinculados ao recibo extra
                        $arParam = array( 'inCodEntidade'      => $inCodEntidade
                                         ,'inCodReciboExtra'   => $inCodRecibo
                                         ,'stExercicio'        => Sessao::getExercicio()
                                        );
                        $obErro = $obRTesourariaCheque->listChequesReciboExtra($rsCheque,$arParam);
                        if ($rsCheque->getNumLinhas() > 0) {
                            while (!$rsCheque->eof()) {
                                $flValorCheque += $rsCheque->getCampo('valor');
                                $rsCheque->proximo();
                            }

                            if ((string) $flValorCheque != $rsRecordSet->getCampo("valor")) {
                                $stJs = "alertaAviso('@O valor total dos cheques (<em style=\"font-weight:bold;\">" . number_format($flValorCheque,2,',','.') . "</em>) está diferente do valor total do recibo (<em style=\"font-weight:bold;\">" . number_format($rsRecordSet->getCampo("valor"),2,',','.') . "</em>).','form','erro','".Sessao::getId()."');\n";
                                $stJs .= limparCampos();
                            } else {
                                $stHTML = $obCTesourariaCheque->buildListaChequeEmissao($rsCheque,'Lista de Cheques vinculados a Despesa Extra');
                                $stJs .= "jq('#spnCheques').html('" . $stHTML . "');";

                                //coloca os cheques na sessao
                                Sessao::write('arCheques',$rsCheque->arElementos);
                            }
                        } else {
                            $stJs .= "jq('#spnCheques').html('');";
                        }

                        $stJs .= "  d.getElementById('inCodPlanoDebito').focus();
                                    d.getElementById('inCodPlanoDebito').value = '".$rsRecordSet->getCampo("cod_plano_despesa")."';\n
                                    if (d.getElementById('inCodPlanoDebito').value) {
                                        d.getElementById('inCodPlanoDebito').readOnly = true;\n
                                        d.getElementById('imgPlanoDebito').style.display = 'none';\n
                                    } else {
                                        d.getElementById('inCodPlanoDebito').readOnly = false;\n
                                        d.getElementById('imgPlanoDebito').style.display = 'inline';\n
                                    }

                                    d.getElementById('inCodPlanoCredito').focus();
                        ";
                        $rsCheque->setPrimeiroElemento();
                        if ($rsCheque->getNumLinhas() > 0) {
                              $stJs .= " d.getElementById('inCodPlanoCredito').value = '".$rsCheque->getCampo('cod_plano')."';\n ";
                        } else {
                              $stJs .= " d.getElementById('inCodPlanoCredito').value = '".$rsRecordSet->getCampo("cod_plano_banco")."';\n ";
                        }
                        $stJs .= "
                                    if (d.getElementById('inCodPlanoCredito').value) {
                                        d.getElementById('inCodPlanoCredito').readOnly = true;\n
                                        d.getElementById('imgPlanoCredito').style.display = 'none';\n
                                    } else {
                                        d.getElementById('inCodPlanoCredito').readOnly = false;\n
                                        d.getElementById('imgPlanoCredito').style.display = 'inline';\n
                                    }

                                    d.getElementById('nuValor').value = '".number_format($rsRecordSet->getCampo("valor"),"2",",",".")."';\n
                                    d.getElementById('nuValor').readOnly = true;\n

                                    d.getElementById('inCodHistorico').value = '';\n
                                    d.getElementById('stNomHistorico').innerHTML = '&nbsp;'; \n

                                    d.getElementById('stTipoRecibo').value = '".$rsRecordSet->getCampo("tipo_recibo")."';\n

                                    d.getElementById('stObservacoes').focus();
                            ";
                            $stJs .= montaBoletim( $rsRecordSet->getCampo("cod_entidade") );
                } else {
                    $stJs .= "alertaAviso('@Código de recibo inválido (".$inCodRecibo.") ou o recibo já foi pago.','form','erro','".Sessao::getId()."');\n";
                    $stJs .= limparCampos();
                }
           }
        }

        return $stJs;
}
function montaBoletim($inCodEntidade, $inCodBoletim = '')
{
    if ($inCodEntidade) {
        require_once( CAM_GF_TES_COMPONENTES . 'ISelectBoletim.class.php' );
        include_once CAM_GF_TES_COMPONENTES . 'ISaldoCaixa.class.php';

        $obISelectBoletim = new ISelectBoletim;
        $obISelectBoletim->obBoletim->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade  );
        $obISelectBoletim->obBoletim->setExercicio( Sessao::getExercicio() );
        $obISelectBoletim->obEvento->setOnChange ( "buscaDado('alteraBoletim');");
        $obISelectBoletim->setNull ( false );

        $obFormulario = new Formulario;
        $obFormulario->addComponente ( $obISelectBoletim );
        $obFormulario->montaInnerHtml();
        $stHTML = $obFormulario->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\'","\\'",$stHTML );

        $stJs = "d.getElementById('spnBoletim').innerHTML = '".$stHTML."';\r\n";

        $ISaldoCaixa = new ISaldoCaixa();
        $ISaldoCaixa->inCodEntidade = $_REQUEST['inCodEntidade'];
        $stJs .= $ISaldoCaixa->montaSaldo();
      
        if ($inCodBoletim) {
            $stJs .= "if(d.getElementById('inCodBoletim')) \n
                        d.getElementById('inCodBoletim').value = '".$inCodBoletim."';\n
            ";
        }
    }
    return $stJs;
}

function montaSpanContas($obCmbEntidades)
{
    // Define Objeto BuscaInner da conta de despesa
    $obBscContaDebito = new IPopUpContaAnalitica( $obCmbEntidades->obSelect );
    $obBscContaDebito->setRotulo                      ( "Conta de Despesa" );
    $obBscContaDebito->setTitle                       ( "Informe a conta de despesa extra-orçamentária vinculada a este recibo." );
    $obBscContaDebito->setId                          ( "stNomContaDebito" );
    $obBscContaDebito->setNull                        ( false               );
    $obBscContaDebito->obCampoCod->setName            ( "inCodPlanoDebito" );
    $obBscContaDebito->obCampoCod->setId              ( "inCodPlanoDebito" );
    $obBscContaDebito->obImagem->setId                ( "imgPlanoDebito"   );
    $obBscContaDebito->setTipoBusca                   ( "tes_pagamento_extra_despesa" );
    $obBscContaDebito->obCampoCod->obEvento->setOnBlur( " montaParametrosGET( 'verificaContas','inCodPlanoCredito,inCodPlanoDebito,stDtBoletim');");

    // Define Objeto BuscaInner da conta para caixa/banco
    $obBscContaCredito = new IPopUpContaAnalitica( $obCmbEntidades->obSelect );
    $obBscContaCredito->setRotulo                      ( "Conta Caixa/Banco"    );
    $obBscContaCredito->setTitle                       ( "Informe a conta Caixa/Banco para pagamento da despesa extra." );
    $obBscContaCredito->setId                          ( "stNomContaCredito" );
    $obBscContaCredito->setNull                        ( false                );
    $obBscContaCredito->obCampoCod->setName            ( "inCodPlanoCredito" );
    $obBscContaCredito->obCampoCod->setId              ( "inCodPlanoCredito" );
    $obBscContaCredito->obImagem->setId                ( "imgPlanoCredito"   );
    if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao ) == 11 && SistemaLegado::pegaConfiguracao('cod_municipio', 2, Sessao::getExercicio(), $boTransacao ) == 79 && SistemaLegado::comparaDatas($stDataFinalAno, $stDataAtual, true))
        $obBscContaCredito->setTipoBusca               ( 'tes_pagamento_extra_caixa_banco_recurso_fixo'  );
    else
        $obBscContaCredito->setTipoBusca               ( 'tes_pagamento_extra_caixa_banco'  );
    $obBscContaCredito->obCampoCod->obEvento->setOnBlur( " montaParametrosGET( 'verificaContas','inCodPlanoCredito,inCodPlanoDebito,stDtBoletim');");
    
    
    $obFormulario = new Formulario;
    $obFormulario->addComponente ( $obBscContaDebito       );
    $obFormulario->addComponente ( $obBscContaCredito      );
    $obFormulario->montaInnerHTML ();
    $stHTML = $obFormulario->getHTML ();

    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );
    $stHTML = str_replace( "\\\\'","\\'",$stHTML );
    
    $stJs  = "document.getElementById('spnContas').innerHTML = '".$stHTML."';";
    $stJs .= "if (document.getElementById('".$obCmbEntidades->obSelect->getName()."').value == '".Sessao::read('inCodEntidade')."') {";
    $stJs .= "  document.getElementById('inCodPlanoCredito').value = '".Sessao::read('inCodPlanoCredito')."'; ";
    $stJs .= "  document.getElementById('stNomContaCredito').innerHTML = '".Sessao::read('stNomContaCredito')."'; ";
    $stJs .= "  f.stNomContaCredito.value = '".Sessao::read('stNomContaCredito')."'; ";
    $stJs .= " } ";
    
    return $stJs;
}

function verificaContas($inCodPlanoCredito, $inCodPlanoDebito, $stDtBoletim)
{

    if ( ($inCodPlanoCredito) && ($inCodPlanoCredito != $inCodPlanoDebito) ) {
        include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaTransferencia.class.php" );                
        $obTTesourariaTransferencia = new TTesourariaTransferencia();
        $obTTesourariaTransferencia->setDado("stExercicio" , Sessao::getExercicio() );
        $obTTesourariaTransferencia->setDado("inCodPlano"  , $inCodPlanoCredito);
        $obTTesourariaTransferencia->setDado("stDtBoletim" , $stDtBoletim);
        $obTTesourariaTransferencia->verificaSaldoContaAnalitica($nuVlSaldoContaAnalitica);
        
        $stJs .= "jQuery('#nuSaldoContaAnalitica').val('".$nuVlSaldoContaAnalitica."'); \n";
        $stJs .= "jQuery('#nuSaldoContaAnaliticaBR').val('".$nuVlSaldoContaAnalitica."'); \n";
        $stJs .= "jQuery('#stDtBoletim').val('".$stDtBoletim."');";
    }

    if ( ($inCodPlanoCredito) && ( $inCodPlanoCredito == $inCodPlanoDebito) ) {
        $stJs .= "alertaAviso('A conta de caixa/banco não pode ser a mesma de despesa! (".$inCodPlanoCredito.")', '', 'erro','".Sessao::getId()."' );\n";
        $_erro++;
    }
    
    if ($_erro) $stJs .= "d.getElementById('Ok').disabled = true;\n ";
    else        $stJs .= "if ( d.getElementById('inCodBoletim') ) {
                            if (d.getElementById('inCodBoletim').value != '') d.getElementById('Ok').disabled = false; \n
                          }";
                          
    $inCodUf = SistemaLegado::pegaConfiguracao('cod_uf');
    
    if ($inCodUf == 16 && $inCodPlanoDebito != '') {
        $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica;
        $obTContabilidadePlanoAnalitica->recuperaRelacionamento ($rsPlano, " WHERE pa.cod_plano = ".$inCodPlanoDebito." AND pc.cod_estrutural like '3.5.%'");
        
        if ($rsPlano->getNumLinhas() > 0) {
            
            $obTOrcamentoEntidade = new TOrcamentoEntidade;
            $obTOrcamentoEntidade->recuperaRelacionamento($rsEntidadesBeneficio, " AND E.cod_entidade <> ".Sessao::read('cod_entidade_form')." AND E.exercicio = '".Sessao::getExercicio()."'");
            
            $obCmbEntidadeBeneficio = new Select;
            $obCmbEntidadeBeneficio->setRotulo     ( "Entidade Beneficiada"   );
            $obCmbEntidadeBeneficio->setId         ( "inCodEntidadeBeneficio" );
            $obCmbEntidadeBeneficio->setName       ( "inCodEntidadeBeneficio" );
            $obCmbEntidadeBeneficio->setTitle      ( "Selecione o tipo de entidade a ser beneficiada" );
            $obCmbEntidadeBeneficio->setCampoID    ( "cod_entidade"  );
            $obCmbEntidadeBeneficio->setCampoDesc  ( "nom_cgm"       );
            $obCmbEntidadeBeneficio->addOption     ( "", "Selecione" );
            $obCmbEntidadeBeneficio->setNull       ( false           );
            $obCmbEntidadeBeneficio->preencheCombo ( $rsEntidadesBeneficio );
            
            $obTTCEPETipoTransferencia = new TTCEPETipoTransferencia;
            $obTTCEPETipoTransferencia->recuperaTodos($rsTipoTransferencia);
            
            $obCmbTipoTransferencia = new Select;
            $obCmbTipoTransferencia->setRotulo     ( "Tipo de Transferência" );
            $obCmbTipoTransferencia->setId         ( "inCodTransferencia" );
            $obCmbTipoTransferencia->setName       ( "inCodTransferencia" );
            $obCmbTipoTransferencia->setTitle      ( "Selecione o tipo de transferência" );
            $obCmbTipoTransferencia->setCampoID    ( "cod_tipo" );
            $obCmbTipoTransferencia->setCampoDesc  ( "descricao" );
            $obCmbTipoTransferencia->addOption     ( "", "Selecione" );
            $obCmbTipoTransferencia->setNull       ( false );
            $obCmbTipoTransferencia->preencheCombo ( $rsTipoTransferencia );
            
            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obCmbEntidadeBeneficio);
            $obFormulario->addComponente ( $obCmbTipoTransferencia);
            $obFormulario->montaInnerHtml();
            $stHTML = $obFormulario->getHTML();
        }
        
    }
    
    if ($stHTML != '') {
        $stJs .= " $('spnTipoTransferencia').innerHTML = '".$stHTML."';\r\n";
    } else {
        $stJs .= " $('spnTipoTransferencia').innerHTML = '';\r\n";
    }
    
    return $stJs;
}

function montaDescricaoTipoPagamento($inTIpoPagamento)
{
    if ($inTIpoPagamento == 1) {
        $obTxtDescricao = new TextBox;
        $obTxtDescricao->setName   ( "stDescricao" );
        $obTxtDescricao->setId     ( "stDescricao" );
        $obTxtDescricao->setValue  ( $stDescricao  );
        $obTxtDescricao->setRotulo ( "Número da Ordem Bancária"   );
        $obTxtDescricao->setTitle  ( "Informe o número da ordem bancária." );
        $obTxtDescricao->setNull   ( true            );
        $obTxtDescricao->setMaxLength( 10   );
        $obTxtDescricao->setSize     ( 10   );
    }

    if ($inTIpoPagamento == 2) {
        $obTxtDescricao = new TextBox;
        $obTxtDescricao->setName   ( "stDescricao" );
        $obTxtDescricao->setId     ( "stDescricao" );
        $obTxtDescricao->setValue  ( $stDescricao  );
        $obTxtDescricao->setRotulo ( "Cheque"   );
        $obTxtDescricao->setTitle  ( "Informe o cheque." );
        $obTxtDescricao->setNull   ( true            );
        $obTxtDescricao->setMaxLength( 10   );
        $obTxtDescricao->setSize     ( 10   );
    }
        $obFormulario = new Formulario;
        $obFormulario->addComponente ( $obTxtDescricao);
        $obFormulario->montaInnerHtml();
        $stHTML = $obFormulario->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\'","\\'",$stHTML );

        $stJs = "d.getElementById('spnTipoPagamento').innerHTML = '".$stHTML."';\r\n";

    return $stJs;
}

function montaDocumento($inCodTipoDocumento)
{
    if ($inCodTipoDocumento == 5 OR $inCodTipoDocumento == 4 ) {
        $stJs = "d.getElementById('spnNroDocumento').innerHTML = '';\r\n";
    }else{
        $obTxtNumeroDocumento = new TextBox;
        $obTxtNumeroDocumento->setName     ( "nroDoc"                         );
        $obTxtNumeroDocumento->setId       ( "nroDoc"                         );
        $obTxtNumeroDocumento->setRotulo   ( "Nr. Documento"                  );
        $obTxtNumeroDocumento->setTitle    ( "Informe o Número do Documento." );
        $obTxtNumeroDocumento->setDecimais ( 0                                );
        $obTxtNumeroDocumento->setinteiro  ( true                             );
        $obTxtNumeroDocumento->setSize     ( 15                               );
        $obTxtNumeroDocumento->setMaxLength( 15                               );
        
        $obFormulario = new Formulario;
        $obFormulario->addComponente ( $obTxtNumeroDocumento);
        $obFormulario->montaInnerHtml();
        $stHTML = $obFormulario->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\'","\\'",$stHTML );
    
        $stJs = "d.getElementById('spnNroDocumento').innerHTML = '".$stHTML."';\r\n";
    }
    return $stJs;
}


switch ($_REQUEST['stCtrl']) {
    
case 'limparCampos':
    $stJs  = limparCampos();
    break;
case 'alteraBoletim':
    $obRTesourariaBoletim = new RTesourariaBoletim();
    list( $inCodBoletim , $stDataBoletim ) = explode ( ':' , $_REQUEST['inCodBoletim'] );
    $obRTesourariaBoletim->setCodBoletim ( $inCodBoletim );
    $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
    $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $obErro = $obRTesourariaBoletim->listarBoletimAberto ( $rsBoletimAberto );

    if ( !$obErro->ocorreu() && $rsBoletimAberto->getNumLinhas() == 1 ) {
        $stJs .= "f.inCodBoletim.value = '" . $rsBoletimAberto->getCampo( 'cod_boletim' ) . "';\r\n";
        $stJs .= "jQuery('#stDtBoletim').val('" . $rsBoletimAberto->getCampo( 'dt_boletim' ) . "');\r\n"; 
        SistemaLegado::executaFrameOculto( "LiberaFrames(true,false);".$stJs );
    } else {
        $stJs .= "f.inCodBoletim.value = '';\r\n";
        $stJs .= "jQuery('#stDtBoletim').val('');\r\n";
        SistemaLegado::executaFrameOculto( "LiberaFrames(true,false);".$stJs );
    }
  
    break;
case 'buscaBoletim':
    if ($_REQUEST['inCodEntidade']) {
        $stJs = montaBoletim( $_REQUEST['inCodEntidade'], $_REQUEST['inCodBoletim'] );
    }
    break;
case 'buscaDadosRecibo':
    $stJs  = buscaDadosRecibo($_GET['inCodEntidade'], $_GET['inCodRecibo']);
    break;
case 'montaSpanContas':
    $obCmbEntidades = Sessao::read('obIEntidade');
    Sessao::write('cod_entidade_form', $request->get('inCodEntidade'));
    $stJs  = montaSpanContas($obCmbEntidades);
    break;
case 'verificaContas':
    $stJs  = verificaContas($_REQUEST['inCodPlanoCredito'],$_REQUEST['inCodPlanoDebito'], $_REQUEST['stDtBoletim']);
    break;
case 'verificaCodBarras':
    $stJs = verificaCodBarras( $_REQUEST['inCodBarras'] );
    break;
case 'montaDescricaoTipoPagamento':
    $stJs  = montaDescricaoTipoPagamento($_GET['cmbTipoPagamento']);
    break;
case 'montaDocumento':
    $stJs  = montaDocumento($_REQUEST['inCodDocTipo']);
    break;
}

if ($stJs) {
    echo $stJs;
}
?>
