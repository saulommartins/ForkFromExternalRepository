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
    * Página Oculta para Arrecadação Receita Extra Orçamentária
    * Data de Criação   : 14/09/2006
    *
    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Id: OCManterArrecadacaoReceitaExtra.php 60237 2014-10-08 11:41:58Z jean $

    * Casos de uso: uc-02.04.26

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_CONT_COMPONENTES."IPopUpContaAnalitica.class.php");
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php");
include_once(CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php");
include_once ( CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoTransferencia.class.php' );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php" );

function verificaCodBarras($inCodBarras)
{
    $inCodEntidade = ltrim(substr($inCodBarras, 18, 2),'0');
    $inCodRecibo   = ltrim(substr($inCodBarras,  9, 4),'0');

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
    $stJs = "//document.getElementById('frm').reset();\n
             document.getElementById('inCodEntidade').focus();\n
             document.getElementById('stNomEntidade').readOnly = false;\n
             document.getElementById('inCodEntidade').readOnly = false;\n
             document.getElementById('imgCredor').style.display = 'inline';\n
             document.getElementById('inCodCredor').readOnly = false;\n
             document.getElementById('inCodCredor').value = '';\n
             document.getElementById('inCodRecibo').value = '';\n
             document.getElementById('stNomCredor').innerHTML = '&nbsp;';\n
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
             document.getElementById('nuValor').readOnly = false;\n
             document.getElementById('nuValor').value = '';\n
             document.getElementById('inCodHistorico').value = '';\n
             document.getElementById('stNomHistorico').innerHTML = '&nbsp;';\n
             if (document.getElementById('inCodEntidade').value == '') {
                document.getElementById('spnBoletim').innerHTML = '';\n
                document.getElementById('spnContas').innerHTML = '';\n
             } else {
                montaParametrosGET('montaSpanContas' , 'inCodEntidade');\n
             }
    ";
    Sessao::remove('inCodPlanoDebito');
    Sessao::remove('stNomContaDebito');

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

           include_once (CAM_GF_EMP_MAPEAMENTO.'TEmpenhoOrdemPagamentoReciboExtra.class.php');
           $obTEmpenhoOrdemPagamentoReciboExtra = new TEmpenhoOrdemPagamentoReciboExtra();
           $obTEmpenhoOrdemPagamentoReciboExtra->setDado('exercicio',Sessao::getExercicio());
           $obTEmpenhoOrdemPagamentoReciboExtra->setDado('cod_entidade',$inCodEntidade);
           $obTEmpenhoOrdemPagamentoReciboExtra->setDado('cod_recibo_extra',$inCodRecibo);
           $obErro = $obTEmpenhoOrdemPagamentoReciboExtra->recuperaOrdemPagamentoReciboExtra( $rsRecordSetOrdemPagamento, $boTransacao );

           if (!$obErro->ocorreu()) {
                if ($rsRecordSetOrdemPagamento->getNumLinhas() == 1) {
                    $stJs .= "alertaAviso('@Código de recibo (".$inCodRecibo.") não pode ser arrecadado pois está vinculado a ordem de pagamento (".$rsRecordSetOrdemPagamento->getCampo('cod_ordem').").','form','erro','".Sessao::getId()."');\n";
                    $stJs .= limparCampos();
                } else {
                    $obCmbEntidades = Sessao::read('obIEntidade');
                    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaReciboExtra.class.php" );
                    $obTTesourariaReciboExtra = new TTesourariaReciboExtra();
                    $obTTesourariaReciboExtra->setDado("stExercicio", Sessao::getExercicio() );
                    $obTTesourariaReciboExtra->setDado("inCodRecibo", $inCodRecibo );

                    if ($tipoNumeracao == 'P') {
                        $obTTesourariaReciboExtra->setDado("inCodEntidade", $inCodEntidade );
                    }

                    $obErro = $obTTesourariaReciboExtra->recuperaReciboExtraArrecadacao( $rsRecordSet, $boTransacao );

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
                                    $stJs .= "  document.getElementById('stDestinacaoRecurso').focus();
                                                document.getElementById('stDestinacaoRecurso').value = '".$rsRecordSet->getCampo('masc_recurso')."';
                                                document.getElementById('inCodRecurso').value = '".$rsRecordSet->getCampo('cod_recurso')."';
                                                document.getElementById('stDestinacaoRecurso').readOnly = true;
                                                document.getElementById('inCodUso').disabled = true;
                                                document.getElementById('inCodDestinacao').disabled = true;
                                                document.getElementById('inCodEspecificacao').disabled = true;
                                                document.getElementById('inCodDetalhamento').disabled = true;
                                    ";
                                } else {
                                    $stJs .= "  d.getElementById('inCodRecurso').focus();\n
                                                d.getElementById('inCodRecurso').value = '".$rsRecordSet->getCampo("cod_recurso")."';\n
                                                if (d.getElementById('inCodRecurso').value) {
                                                    d.getElementById('inCodRecurso').readOnly = true;\n
                                                    d.getElementById('imgRecurso').style.display = 'none';\n
                                                } else {
                                                    d.getElementById('inCodRecurso').readOnly = false;\n
                                                    d.getElementById('imgRecurso').style.display = 'inline';\n
                                                } ";
                                }
                                $stJs .= "  d.getElementById('inCodPlanoDebito').focus();
                                            d.getElementById('inCodPlanoDebito').value = '".$rsRecordSet->getCampo("cod_plano_banco")."';\n
                                            if (d.getElementById('inCodPlanoDebito').value) {
                                                d.getElementById('inCodPlanoDebito').readOnly = true;\n
                                                d.getElementById('imgPlanoDebito').style.display = 'none';\n
                                            } else {
                                                d.getElementById('inCodPlanoDebito').readOnly = false;\n
                                                d.getElementById('imgPlanoDebito').style.display = 'inline';\n
                                            }

                                            d.getElementById('inCodPlanoCredito').focus();
                                            d.getElementById('inCodPlanoCredito').value = '".$rsRecordSet->getCampo("cod_plano_receita")."';\n
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
                            $stJs .= "alertaAviso('@Código de recibo inválido (".$inCodRecibo.") ou o recibo já foi arrecadado.','form','erro','".Sessao::getId()."');\n";
                            $stJs .= limparCampos();
                        }
                    }
                }
           }
        }

        return $stJs;
}
function montaBoletim($inCodEntidade, $inCodBoletim = '')
{
    if ($inCodEntidade) {
        require_once( CAM_GF_TES_COMPONENTES . 'ISelectBoletim.class.php' );
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
    // Define Objeto BuscaInner da conta de Receita
    $obBscContaCredito = new IPopUpContaAnalitica( $obCmbEntidades->obSelect );
    $obBscContaCredito->setRotulo                      ( "Conta de Receita" );
    $obBscContaCredito->setTitle                       ( "Informe a conta de receita extra-orçamentária." );
    $obBscContaCredito->setId                          ( "stNomContaCredito" );
    $obBscContaCredito->setNull                        ( false               );
    $obBscContaCredito->obCampoCod->setName            ( "inCodPlanoCredito" );
    $obBscContaCredito->obCampoCod->setId              ( "inCodPlanoCredito" );
    $obBscContaCredito->obImagem->setId                ( "imgPlanoCredito"   );
    $obBscContaCredito->setTipoBusca                   ( "tes_arrecadacao_extra_receita" );
    $obBscContaCredito->obCampoCod->obEvento->setOnBlur( " montaParametrosGET( 'verificaContas','inCodPlanoCredito,inCodPlanoDebito');");

    // Define Objeto BuscaInner da conta para caixa/banco
    $obBscContaDebito = new IPopUpContaAnalitica( $obCmbEntidades->obSelect );
    $obBscContaDebito->setRotulo                      ( "Conta Caixa/Bancos"    );
    $obBscContaDebito->setTitle                       ( "Informe a conta Caixa/Banco para recolhimento da receita extra." );
    $obBscContaDebito->setId                          ( "stNomContaDebito" );
    $obBscContaDebito->setNull                        ( false         );
    $obBscContaDebito->obCampoCod->setName            ( "inCodPlanoDebito" );
    $obBscContaDebito->obCampoCod->setId              ( "inCodPlanoDebito" );
    $obBscContaDebito->obImagem->setId                ( "imgPlanoDebito"   );
    $obBscContaDebito->setTipoBusca                   ( "tes_arrecadacao_extra_caixa_banco"    ); // Por utilizar as mesmas contas da arrecadação
    $obBscContaDebito->obCampoCod->obEvento->setOnBlur( " montaParametrosGET( 'verificaContas','inCodPlanoCredito,inCodPlanoDebito');");

    $obFormulario = new Formulario;
    $obFormulario->addComponente ( $obBscContaCredito      );
    $obFormulario->addComponente ( $obBscContaDebito       );
    $obFormulario->montaInnerHTML ();
    $stHTML = $obFormulario->getHTML ();

    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );
    $stHTML = str_replace( "\\\\'","\\'",$stHTML );

    $stJs  = "document.getElementById('spnContas').innerHTML = '".$stHTML."';";
    $stJs .= "if (document.getElementById('".$obCmbEntidades->obSelect->getName()."').value == '".Sessao::read('inCodEntidade')."') {";
    $stJs .= "  document.getElementById('inCodPlanoDebito').value = '".Sessao::read('inCodPlanoDebito')."'; ";
    $stJs .= "  document.getElementById('stNomContaDebito').innerHTML = '".Sessao::read('stNomContaDebito')."'; ";
    $stJs .= "  f.stNomContaDebito.value = '".Sessao::read('stNomContaDebito')."'; ";
    $stJs .= " } ";

    return $stJs;
}

function verificaContas($inCodPlanoCredito, $inCodPlanoDebito)
{
    if ( ($inCodPlanoDebito) && ( $inCodPlanoCredito == $inCodPlanoDebito) ) {
        $stJs .= "alertaAviso('A conta de caixa/banco não pode ser a mesma da receita! (".$inCodPlanoDebito.")', '', 'erro','".Sessao::getId()."' );\n";
        $_erro++;
    }
    
    if ($_erro) $stJs .= "d.getElementById('Ok').disabled = true;\n ";
    else        $stJs .= "if ( d.getElementById('inCodBoletim') ) {
                            if (d.getElementById('inCodBoletim').value != '') d.getElementById('Ok').disabled = false; \n
                          }";
                          
    $inCodUf = SistemaLegado::pegaConfiguracao('cod_uf');
    
    if ($inCodUf == 16 && $inCodPlanoCredito != '') {
        $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica;
        $obTContabilidadePlanoAnalitica->recuperaRelacionamento ($rsPlano, " WHERE pa.cod_plano = ".$inCodPlanoCredito." AND pc.cod_estrutural like '4.5.%'");
        
        if ($rsPlano->getNumLinhas() > 0) {
            
            $obTOrcamentoEntidade = new TOrcamentoEntidade;
            $obTOrcamentoEntidade->recuperaRelacionamento($rsEntidadesTransferidora, " AND E.cod_entidade <> ".Sessao::read('cod_entidade_form')." AND E.exercicio = '".Sessao::getExercicio()."'");
            
            $obCmbEntidadeTransferidora = new Select;
            $obCmbEntidadeTransferidora->setRotulo     ( "Entidade Transferidora" );
            $obCmbEntidadeTransferidora->setId         ( "inCodEntidadeTransferidora" );
            $obCmbEntidadeTransferidora->setName       ( "inCodEntidadeTransferidora" );
            $obCmbEntidadeTransferidora->setTitle      ( "Selecione o tipo de entidade transferidora" );
            $obCmbEntidadeTransferidora->setCampoID    ( "cod_entidade" );
            $obCmbEntidadeTransferidora->setCampoDesc  ( "nom_cgm" );
            $obCmbEntidadeTransferidora->addOption     ( "", "Selecione" );
            $obCmbEntidadeTransferidora->setNull       ( false );
            $obCmbEntidadeTransferidora->preencheCombo ( $rsEntidadesTransferidora );
            
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
            $obFormulario->addComponente ( $obCmbEntidadeTransferidora );
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
            $stJs  = "f.inCodBoletim.value = '" . $rsBoletimAberto->getCampo( 'cod_boletim' ). ":" . $rsBoletimAberto->getCampo( 'dt_boletim' ).":".$rsBoletimAberto->getCampo( 'exercicio' ).":".$rsBoletimAberto->getCampo('cod_entidade') . "';\r\n";
            $stJs .= "f.stDtBoletim.value = '" . $rsBoletimAberto->getCampo( 'dt_boletim' ) . "';\r\n";
            SistemaLegado::executaFrameOculto( "LiberaFrames(true,false);".$stJs );
        } else {
            $stJs  = "f.inCodBoletim.value = '';\r\n";
            $stJs .= "f.stDtBoletim.value = '';\r\n";
            SistemaLegado::executaFrameOculto( "LiberaFrames(true,false);".$stJs );
        }
        exit;
    break;
    case 'buscaBoletim':
        if ($_REQUEST['inCodEntidade']) {
            $stJs = montaBoletim( $_REQUEST['inCodEntidade'], $_REQUEST['inCodBoletim'] );

            include_once CAM_GF_TES_COMPONENTES . 'ISaldoCaixa.class.php';
            $ISaldoCaixa = new ISaldoCaixa();
            $ISaldoCaixa->inCodEntidade = $_REQUEST['inCodEntidade'];
            $stJs .= $ISaldoCaixa->montaSaldo();

        }
    break;
    case 'buscaDadosRecibo':
            $stJs  = buscaDadosRecibo($_GET['inCodEntidade'], $_GET['inCodRecibo']);
    break;
    case 'montaSpanContas':
            $obCmbEntidades = Sessao::read('obIEntidade');
            Sessao::write('cod_entidade_form',$request->get('inCodEntidade'));
            $stJs  = montaSpanContas($obCmbEntidades);
    break;
    case 'verificaContas':
            $stJs  = verificaContas($_GET['inCodPlanoCredito'],$_GET['inCodPlanoDebito']);
    break;
    case 'verificaCodBarras':
            $stJs = verificaCodBarras( $_REQUEST['inCodBarras'] );
    break;
}
if ($stJs) {
    echo $stJs;
}
?>
