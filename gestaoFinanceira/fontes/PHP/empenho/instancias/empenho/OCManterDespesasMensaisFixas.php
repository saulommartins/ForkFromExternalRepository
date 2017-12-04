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
    * Página de Oculto de Empenhamento de Despesas Mensais Fixas
    * Data de Criação : 08/09/2006

    * @author Analista:
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 31087 $
    $Name$
    $Autor: $
    $Date: 2007-06-12 18:52:51 -0300 (Ter, 12 Jun 2007) $

    * Casos de uso: uc-02.03.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma      = "ManterDespesasMensaisFixas";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

function montaListaItens($arRecordSet, $stAcao = '')
{
    $rsItens = new RecordSet;
    $rsItens->preenche( $arRecordSet );
    $obLista = new Lista;
    $obLista->setTitulo('');
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsItens );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Contrato");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Consumo");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "contrato" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "consumo" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirListaItens');" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('alterarListaItens');" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "d.getElementById('spnLista').innerHTML = '".$stHTML."';";

    return $stJs;
}

function montaLabelDiverso($flSaldoDotacao)
{
    global $obREmpenhoAutorizacaoEmpenho;
    $flSaldoDotacao = number_format( $flSaldoDotacao ,2,',','.');

    $obHdnSaldoDotacao = new Hidden;
    $obHdnSaldoDotacao->setName ( "flVlSaldoDotacao" );
    $obHdnSaldoDotacao->setValue( $flSaldoDotacao );

    $obLblSaldoDotacao = new Label;
    $obLblSaldoDotacao->setRotulo( "Saldo da Dotação" );
    $obLblSaldoDotacao->setValue ( $flSaldoDotacao );

    $obForm = new Formulario;
    $obForm->addHidden( $obHdnSaldoDotacao );
    $obForm->addComponente( $obLblSaldoDotacao );
    $obForm->montaInnerHTML();
    $stHtml = $obForm->getHTML();
    $stJs = "d.getElementById('spnSaldoDotacao').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaComboDiverso($inCodDespesa, $inCodEntidade)
{
    global $obREmpenhoAutorizacaoEmpenho;
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $inCodDespesa );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa( $rsConta );

    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoPreEmpenho.class.php");
    $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho;
    $obTEmpenhoPreEmpenho->setDado( "exercicio", Sessao::getExercicio() );
    $obTEmpenhoPreEmpenho->setDado( "cod_despesa", $inCodDespesa );
    $obErro = $obTEmpenhoPreEmpenho->recuperaSaldoAnterior( $rsRecordSet, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $nuSaldoDotacao = $rsRecordSet->getCampo( "saldo_anterior" );
    }
    $js .= montaLabelDiverso( $nuSaldoDotacao );

    $stCodClassificacao = $rsConta->getCampo( "cod_estrutural" );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $stCodClassificacao );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( "" );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarCodEstruturalDespesa( $rsClassificacao );
    $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento( $stFormaExecucao );

    if ( $rsClassificacao->getNumLinhas() > -1 ) {
        $inContador = 1;
        $js .= "limpaSelect(f.stDesdobramento,0); \n";
        $js .= "f.stDesdobramento.options[0] = new Option('Selecione','', 'selected');\n";
        while ( !$rsClassificacao->eof() ) {
            $stMascaraReduzida = $rsClassificacao->getCampo("mascara_reduzida");
            if ($stMascaraReduzidaOld) {

                if ( $stMascaraReduzidaOld != substr($stMascaraReduzida,0,strlen($stMascaraReduzidaOld)) ) {
                    $selected = "";
                    if ($stCodEstruturalOld == $_POST["stCodEstrutural"]) {
                        $selected = "selected";
                    }

                    $arOptions[]['reduzido']                  = $stMascaraReduzidaOld;
                    $arOptions[count($arOptions)-1]['option'] = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."','".$selected."'";

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
            if ($stCodEstruturalOld == $_POST['stCodEstrutural']) {
                $selected = "selected";
            }
            $arOptions[]['reduzido'] = $stMascaraReduzidaOld;
            $arOptions[count($arOptions)-1]['option'] = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."','".$selected."'";
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
            if($stFormaExecucao) $inContador = 1;
            else $inContador = 0;
            asort( $arOptions );
            foreach ($arOptions as $option) {
                $js .= "f.stDesdobramento.options[".$inContador++."] = new Option(". $option['option'] ."); \n";
            }
        }
    } else {
        $js .= "limpaSelect(f.stDesdobramento,0); \n";
        $js .= "f.stDesdobramento.options[0] = new Option('Selecione','', 'selected');\n";
    }

    return $js;
}

function limparDados()
{
    Sessao::remove('arItens');

//    $js .= "f.hdnContrato.value = '';\n";
    $js .= "f.stConsumo.value = '';\n";
    $js .= "f.stComplemento.value = '';\n";
    $js .= "f.flValor.value = '';\n";
    $js .= "f.dtDataDocumento.value = '';\n";

    return $js;
}

function limparTudo()
{
    $stJs  = "d.getElementById('stEntidade').innerHTML = '';\n";
    $stJs .= "f.inCodEntidade.value = '';\n";
    $stJs .= "d.getElementById('stCredor').innerHTML = '';\n";
    $stJs .= "f.inCodFornecedor.value = '';\n";
    $stJs .= "d.getElementById('stDotacao').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnSaldoDotacao').innerHTML = '';\n";
    $stJs .= "f.inCodDespesa.value = '';\n";
    $stJs .= "d.getElementById('stLocal').innerHTML = '';\n";
    $stJs .= "d.getElementById('stHistorico').innerHTML = '';\n";
    $stJs .= "d.getElementById('inContrato').innerHTML = '';\n";
    $stJs .= "f.hdnContrato.value = '';\n";
    $stJs .= "f.stNomLocal.value = '';\n";
    $stJs .= "f.stNomDespesa.value = '';\n";
    $stJs .= "f.inNumOrgao.value = '';\n";
    $stJs .= "f.inNumUnidade.value = '';\n";
    $stJs .= "f.inCodDespesaFixa.value = '';\n";
    $stJs .= "d.getElementById('stDtVencimento').innerHTML = '';\n";
    $stJs .= "f.dtVencimento.value = '';\n";
    $stJs .= "f.stDtEmpenho.value = '';\n";
    $stJs .= "f.dtUltimaDataEmpenho.value = '';\n";
    $stJs .= "limpaSelect(f.stDesdobramento,0); \n";
    $stJs .= "f.stDesdobramento.options[0] = new Option('Selecione','', 'selected');\n";

    return $stJs;
}

function recuperaDataVencimento($stDtEmpenho, $rsDespesasFixas = '')
{
    include_once( TEMP."TEmpenhoDespesasFixas.class.php");
    $obTEmpenhoDespesasFixas = new TEmpenhoDespesasFixas();
    $obTEmpenhoDespesasFixas->setDado('exercicio', Sessao::getExercicio() );
    $obTEmpenhoDespesasFixas->setDado('cod_tipo', $_REQUEST['inCodTipo']);
    $obTEmpenhoDespesasFixas->setDado('num_identificacao', $_REQUEST['stIdentificador']);
    if(!$rsDespesasFixas)
        $obTEmpenhoDespesasFixas->recuperaDespesasFixasIdentificacao( $rsDespesasFixas );
    $stDiaVencimento = str_pad(trim($rsDespesasFixas->getCampo('dia_vencimento')), 2, 00, STR_PAD_LEFT);
    if ($stDtEmpenho) {
        $arData = explode('/',$stDtEmpenho);

        $stMes = str_pad(trim($arData[1]),2,00, STR_PAD_LEFT);

        if ( ($arData[2].$arData[1].$arData[0] <= Sessao::getExercicio().$stMes.$stDiaVencimento) && $stMes != 12 ) {
            $stDtVencimento = $stDiaVencimento.'/'.$stMes.'/'.Sessao::getExercicio();
        } else {
            if ( ($arData[1] == 12) && ($arData[0] > $stDiaVencimento) ) {
               echo "alertaAviso('Este empenho deverá ser emitido no mês de Janeiro.','form','erro','".Sessao::getId()."','../');";
               echo "dtUltimoEmpenho = f.dtUltimaDataEmpenho.value.split('-');";
               echo "f.stDtEmpenho.value = dtUltimoEmpenho[2]+'/'+dtUltimoEmpenho[1]+'/'+dtUltimoEmpenho[0];";
               $stDtVencimento = '';
            } else {
               $stDtVencimento = $stDiaVencimento.'/'.str_pad(trim( ($arData[1] == 12 ? $arData[1] : ($arData[1]+1)) ),2,00,STR_PAD_LEFT).'/'.Sessao::getExercicio();
            }
        }
    }

    return $stDtVencimento;
}

switch ($_REQUEST['stCtrl']) {
    case "carregaIdentificador":
        if ($_REQUEST['inCodTipo']) {
            $stJs .= limparTudo();
            $stJs .= "f.stTipoDespesaFixa.value = '';\n";
            $stJs .= "limpaSelect(f.stIdentificador,0); f.stIdentificador.options[0] = new Option('Selecione','', 'selected');\n";
            include_once( TEMP."TEmpenhoDespesasFixas.class.php");
            $obTEmpenhoDespesasFixas = new TEmpenhoDespesasFixas();
            $obTEmpenhoDespesasFixas->setDado('cod_tipo', $_REQUEST['inCodTipo']);
            $obTEmpenhoDespesasFixas->setDado('exercicio', Sessao::getExercicio() );
            $obTEmpenhoDespesasFixas->recuperaIdentificador( $rsIdentificador );
            $inCount = 1;
            if ($rsIdentificador->eof()) {
                 $stJs .= "limpaSelect(f.stIdentificador,0); f.stIdentificador.options[0] = new Option('Selecione','', 'selected');\n";
                 $stJs .= limparTudo();
            }
            while ( !$rsIdentificador->eof() ) {
                $stJs .= "f.stIdentificador.options[$inCount] = new Option('".$rsIdentificador->getCampo('num_identificacao')."', '".$rsIdentificador->getCampo('num_identificacao')."','');";
                $rsIdentificador->proximo();
                $inCount++;
            }

            $obTEmpenhoDespesasFixas->recuperaComplementoTipo( $stDescricaoTipo );
            if ($stDescricaoTipo)
                $stJs .= "f.stTipoDespesaFixa.value = '".$stDescricaoTipo."';\n";
            else
                $stJs .= "f.stTipoDespesaFixa.value = '';\n";

        } else {
            $stJs .= "f.stTipoDespesaFixa.value = '';\n";
            $stJs .= "limpaSelect(f.stIdentificador,0); f.stIdentificador.options[0] = new Option('Selecione','', 'selected');\n";
            $stJs .= limparTudo();
        }

    break;
    case "carregaDespesasFixas":
        if (( $_REQUEST['inCodTipo'] ) && ( $_REQUEST['stIdentificador'] )) {
            include_once( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );
            $obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho();
            include_once( TEMP."TEmpenhoDespesasFixas.class.php");
            $obTEmpenhoDespesasFixas = new TEmpenhoDespesasFixas();
            $obTEmpenhoDespesasFixas->setDado('cod_tipo', $_REQUEST['inCodTipo']);
            $obTEmpenhoDespesasFixas->setDado('num_identificacao', $_REQUEST['stIdentificador']);
            $obTEmpenhoDespesasFixas->setDado('exercicio', Sessao::getExercicio());
            $obTEmpenhoDespesasFixas->recuperaDespesasFixasIdentificacao( $rsDespesasFixas );

            if ( $rsDespesasFixas->getNumLinhas() > 0 ) {
                $stJs .= "d.getElementById('stEntidade').innerHTML = '".$rsDespesasFixas->getCampo('cod_entidade')." - ".$rsDespesasFixas->getCampo('nom_entidade')."';\n";
                $stJs .= "f.inCodEntidade.value = ".$rsDespesasFixas->getCampo('cod_entidade').";\n";
                $stJs .= "d.getElementById('stCredor').innerHTML = '".$rsDespesasFixas->getCampo('numcgm_credor')." - ".$rsDespesasFixas->getCampo('nom_credor')."';\n";
                $stJs .= "f.inCodFornecedor.value = ".$rsDespesasFixas->getCampo('numcgm_credor').";\n";
                $stJs .= "d.getElementById('stDotacao').innerHTML = '".$rsDespesasFixas->getCampo('cod_despesa')." - ".$rsDespesasFixas->getCampo('descricao')."';\n";
                $stJs .= "f.inCodDespesa.value = ".$rsDespesasFixas->getCampo('cod_despesa').";\n";
                $stJs .= "d.getElementById('stLocal').innerHTML = '".$rsDespesasFixas->getCampo('cod_local')." - ".$rsDespesasFixas->getCampo('nom_local')."';\n";
                $stJs .= "d.getElementById('stHistorico').innerHTML = '".$rsDespesasFixas->getCampo('historico')."';\n";
                $stJs .= "d.getElementById('inContrato').innerHTML = '".$rsDespesasFixas->getCampo('num_contrato')."';\n";
                $stJs .= "f.hdnContrato.value = '".$rsDespesasFixas->getCampo('num_contrato')."';\n";
                $stJs .= "f.stNomLocal.value = '".$rsDespesasFixas->getCampo('nom_local')."';\n";
                $stJs .= "f.stNomDespesa.value = '".$rsDespesasFixas->getCampo('descricao')."';\n";
                $stJs .= "f.inNumOrgao.value = ".$rsDespesasFixas->getCampo('num_orgao').";\n";
                $stJs .= "f.inNumUnidade.value = ".$rsDespesasFixas->getCampo('num_unidade').";\n";
                $stJs .= "f.inCodDespesaFixa.value = ".$rsDespesasFixas->getCampo('cod_despesa_fixa').";\n";

                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $rsDespesasFixas->getCampo('cod_despesa') );
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $rsDespesasFixas->getCampo('cod_entidade') );
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesaDotacao );

                //$stNomDespesa = $rsDespesaDotacao->getCampo( "descricao" );

                $stJs .= montaComboDiverso( $rsDespesasFixas->getCampo('cod_despesa'), $rsDespesasFixas->getCampo('cod_entidade'));

                include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php" );
                include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoConfiguracao.class.php" );
                $obREmpenhoEmpenho = new REmpenhoEmpenho();
                $obREmpenhoEmpenho->setExercicio( Sessao::getExercicio() );

                $obREmpenhoConfiguracao = new REmpenhoConfiguracao();
                $obREmpenhoConfiguracao->consultar();
                if ($obREmpenhoConfiguracao->getNumeracao() == 'P') {
                    $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($rsDespesasFixas->getCampo('cod_entidade'));
                    $obErro = $obREmpenhoEmpenho->recuperaUltimoEmpenho($rsUltimoEmpenho);
                    $dtUltimaDataEmpenho = Sessao::getExercicio()."-01-01";
                    if (!$obErro->ocorreu() && $rsUltimoEmpenho->getNumLinhas() >= 1) {
                        if($rsUltimoEmpenho->getCampo("dt_empenho")!="")
                            $dtUltimaDataEmpenho = $rsUltimoEmpenho->getCampo("dt_empenho");
                    }
                    $js .= "f.dtUltimaDataEmpenho.value = '$dtUltimaDataEmpenho';";

                    if (!$obErro->ocorreu) {
                        $obErro = $obREmpenhoEmpenho->listarMaiorData( $rsMaiorData );
                        if (!$obErro->ocorreu()) {
                            $stDtEmpenho = $rsMaiorData->getCampo( "dataempenho" );
                            if ($stDtEmpenho) {
                                $stJs .= "f.stDtEmpenho.value = '".$stDtEmpenho."';\n";
                                $stJs .= "f.inCodDespesa.focus();\n";
                            } else {
                                $stJs .= "f.stDtEmpenho.value= '01/01/".Sessao::getExercicio()."';\n";
                            }
                        }
                    } else $stJs .= 'f.stDtEmpenho.value= "'.date("d/m/Y").'";\n';
                } else {
                    $obErro = $obREmpenhoEmpenho->recuperaUltimoEmpenho($rsUltimoEmpenho);
                    $dtUltimaDataEmpenho = Sessao::getExercicio()."-01-01";
                    if (!$obErro->ocorreu() && $rsUltimoEmpenho->getNumLinhas() >= 1) {
                        if($rsUltimoEmpenho->getCampo("dt_empenho")!="")
                            $dtUltimaDataEmpenho = $rsUltimoEmpenho->getCampo("dt_empenho");
                    }
                    $stJs .= "f.dtUltimaDataEmpenho.value = '$dtUltimaDataEmpenho';";

                    if (!$obErro->ocorreu) {
                        $obErro = $obREmpenhoEmpenho->listarMaiorData( $rsMaiorData );
                        if (!$obErro->ocorreu()) {
                            $stDtEmpenho = $rsMaiorData->getCampo( "dataempenho" );
                            if ($stDtEmpenho) {
                                $stJs .= "f.stDtEmpenho.value = '".$stDtEmpenho."';\n";
                            } else {
                                $stJs .= "f.stDtEmpenho.value= '01/01/".Sessao::getExercicio()."';\n";
                            }
                        }
                    }
                }

                $stDtVencimento = recuperaDataVencimento( $stDtEmpenho, $rsDespesasFixas );
                $stJs .= "d.getElementById('stDtVencimento').innerHTML = '".$stDtVencimento."';\n";
                $stJs .= "f.dtVencimento.value = '".$stDtVencimento."';\n";
            }
        } else {
            $stJs = limparTudo();
        }
    break;
    case "recuperaDataVencimento":
        $stDtVencimento = recuperaDataVencimento( $_REQUEST['stDtEmpenho'] );
        $stJs .= "d.getElementById('stDtVencimento').innerHTML = '".$stDtVencimento."';\n";
        $stJs .= "f.dtVencimento.value = '".$stDtVencimento."';\n";
    break;

    case "incluirListaItens":
        $inCount = sizeof(Sessao::read('arItens'));
        $inConsumo = number_format(str_replace(',','.',str_replace('.','',$_REQUEST['stConsumo'])),2,'.','');
        $inValor   = number_format(str_replace(',','.',str_replace('.','',$_REQUEST['flValor'  ])),2,'.','');
        $inValorTotal = $inConsumo * $inValor;
        if (!$_REQUEST['id']) {
            $arItens = array();
            $arItens[$inCount]['id'] = $inCount+1;
            $arItens[$inCount]['complemento'   ] = $_REQUEST['stComplemento'];
            $arItens[$inCount]['contrato'      ] = $_REQUEST['hdnContrato'];
            $arItens[$inCount]['consumo'       ] = $_REQUEST['stConsumo'];
            $arItens[$inCount]['valor'         ] = $_REQUEST['flValor'];
            //sessao->transf['arItens'][$inCount]['valor_total'   ] = $inValorTotal;
            $arItens[$inCount]['data_documento'] = $_REQUEST['dtDataDocumento'];
            Sessao::write('arItens', $arItens);
            $stJs .= montaListaItens( Sessao::read('arItens') );
            $stJs .= limparDados();
            $stJs .= "f.id.value = ''\n";
        } else {
            $arItens = array();
            $arItens[($_REQUEST['id']-1)]['complemento'   ] = $_REQUEST['stComplemento'];
            $arItens[($_REQUEST['id']-1)]['contrato'      ] = $_REQUEST['hdnContrato'];
            $arItens[($_REQUEST['id']-1)]['consumo'       ] = $_REQUEST['stConsumo'];
            $arItens[($_REQUEST['id']-1)]['valor'         ] = $_REQUEST['flValor'];
            //sessao->transf['arItens'][($_REQUEST['id']-1)]['valor_total'   ] = $inValorTotal;
            $arItens[($_REQUEST['id']-1)]['data_documento'] = $_REQUEST['dtDataDocumento'];
            Sessao::write('arItens', $arItens);
            $stJs .= montaListaItens( Sessao::read('arItens') );
            $stJs .= limparDados();
            $stJs .= "f.id.value = '';\n";
        }

        $arItens = array();
        $arItens = Sessao::read('arItens');
        foreach ($arItens as $key => $value) {
            $inTotal += number_format(str_replace(',','.',str_replace('.','',$value['valor'] )),2,'.','');
        }
        $stJs .= "f.flValorTotalItem.value = '".$inTotal."';\n";
        $stJs .= "d.getElementById('inTotal').innerHTML = '".number_format($inTotal,2 ,',','.')."';\n";
    break;
    case "excluirListaItens":
        $arTmp = array();
        $inCount = 0;
        $arItens = Sessao::read('arItens');
        foreach ($arItens as $key => $value) {
            if ($value['id'] == $_REQUEST['id']) {
                unset($arItens[$inCount]);
            }
            $inCount++;
        }
        $inCount = 0;
        foreach ($arItens as $key => $value) {
            $arTmp[$inCount]['id']       = $inCount+1;
            $arTmp[$inCount]['complemento'   ] = $value['complemento'];
            $arTmp[$inCount]['contrato'      ] = $value['contrato'];
            $arTmp[$inCount]['consumo'       ] = $value['consumo'];
            $arTmp[$inCount]['valor'         ] = $value['valor'];
//            $arTmp[$inCount]['valor_total'   ] = $value['valor_total'];
            $arTmp[$inCount]['data_documento'] = $value['data_documento'];
            $inTotal += $value['valor'];
            $inCount++;
        }
        Sessao::write('arItens', $arTmp);
        $stJs .= montaListaItens( Sessao::read('arItens') );
        $stJs .= "f.flValorTotalItem.value = '".$inTotal."';\n";
        $stJs .= "d.getElementById('inTotal').innerHTML = '".number_format($inTotal,2 ,',','.')."';\n";
    break;
    case "alterarListaItens":
        $arItens = array();
        $arItens = Sessao::read('arItens');
        foreach ($arItens as $key => $value) {
            if ($value['id'] == $_REQUEST['id']) {
                $stJs .= "f.stComplemento.value = '".$value['complemento']."'\n";
                $stJs .= "f.hdnContrato.value = '".$value['contrato']."'\n";
                $stJs .= "f.stConsumo.value = '".$value['consumo']."'\n";
                $stJs .= "f.flValor.value = ".$value['valor']."\n";
                $stJs .= "floatDecimal(f.flValor,2);\n";
                $stJs .= "f.dtDataDocumento.value = '".$value['data_documento']."'\n";
                $stJs .= "f.id.value = ".$value['id']."\n;";
                break;
            }
        }
    break;
    case "limpar":
        Sessao::remove('arItens');
        $stJs .= limparTudo();
    break;

}

echo $stJs;
