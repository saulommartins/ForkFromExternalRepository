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
    * Página de Formulário para configuração
    * Data de Criação   : 31/01/2012

    * @author Carlos Adriano

    * @ignore

    * $Id: OCManterConfiguracaoDividaConsolidada.php 45121 2011-01-27 19:52:49Z silvia $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(TCGM."TCGM.class.php");
include_once(TTGO."TTCMGOTipoLancamento.class.php");
include_once(TTGO."TTCMGODividaConsolidada.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoDividaConsolidada";
$pgFilt = "FL".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

$arDivida = Sessao::read('arDivida');

switch ($stCtrl) {

    case 'buscaUnidade':
        buscaUnidade($_REQUEST['inOrgao']);
    break;

    case 'incluiDivida' :
        $arDivida   = Sessao::read('arDivida');
        $stMensagem = executaValidacao($_REQUEST);

        $arElementos = array();
        if ($stMensagem == "") {
            $obTTCMGOTipoLancamento = new TTCMGOTipoLancamento();
            $obTTCMGOTipoLancamento->setDado('cod_lancamento', $_REQUEST['inTipoLancamento']);
            $obTTCMGOTipoLancamento->recuperaPorChave($rsTipoLancamento);

            $obTCGM = new TCGM();
            $obTCGM->setDado('numcgm', $_REQUEST['inCGM']);
            $obTCGM->recuperaPorChave($rsCGM);

            $arElementos['id']                     = count($arDivida);
            $arElementos['inMes']                  = $_REQUEST['inMes'];
            $arElementos['inOrgao']                = $_REQUEST['inOrgao'];
            $arElementos['inUnidade']              = $_REQUEST['inUnidade'];
            $arElementos['inTipoLancamento']       = $_REQUEST['inTipoLancamento'];
            $arElementos['inTipoLancamentoRotulo'] = $rsTipoLancamento->getCampo('cod_lancamento').' - '.$rsTipoLancamento->getCampo('descricao');
            $arElementos['stLeiAutorizacao']       = $_REQUEST['stLeiAutorizacao'];
            $arElementos['dtLeiAutorizacao']       = $_REQUEST['dtLeiAutorizacao'];
            $arElementos['inCGM']                  = $_REQUEST['inCGM'];
            $arElementos['inCGMRotulo']            = $rsCGM->getCampo('numcgm').' - '.$rsCGM->getCampo('nom_cgm');
            $arElementos['vlSaldoAnterior']        = $_REQUEST['vlSaldoAnterior'];
            $arElementos['vlContratacao']          = $_REQUEST['vlContratacao'];
            $arElementos['vlAmortizacao']          = $_REQUEST['vlAmortizacao'];
            $arElementos['vlCancelamento']         = $_REQUEST['vlCancelamento'];
            $arElementos['vlEncampacao']           = $_REQUEST['vlEncampacao'];
            $arElementos['vlAtualizacao']          = $_REQUEST['vlAtualizacao'];
            $arElementos['vlSaldoAtual']           = $_REQUEST['vlSaldoAtual'];

            $arDivida[] = $arElementos;

            Sessao::write('arDivida', $arDivida);
            echo montaLista($arDivida);
        } else {
           echo "alertaAviso('".$stMensagem."!','form','erro','".Sessao::getId()."');";
        }

    break;

    case 'montaAlteracaoLista':
        $arDivida = $arDivida[$_REQUEST['id']];

        $obTCGM = new TCGM();
        $obTCGM->setDado('numcgm', $arDivida['inCGM']);
        $obTCGM->recuperaPorChave($rsCGM);

        $stJs = "document.getElementById('hdnId').value = '".$_REQUEST['id']."';";
        $stJs.= "document.getElementById('inMes').value = '".$arDivida['inMes']."';";
        $stJs.= "document.getElementById('inOrgao').value = '".$arDivida['inOrgao']."';";
        $stJs.= "document.getElementById('inTipoLancamento').value = '".$arDivida['inTipoLancamento']."';";
        $stJs.= "document.getElementById('stLeiAutorizacao').value = '".$arDivida['stLeiAutorizacao']."';";
        $stJs.= "document.getElementById('dtLeiAutorizacao').value = '".$arDivida['dtLeiAutorizacao']."';";
        $stJs.= "document.getElementById('inCGM').value = '".$arDivida['inCGM']."';";
        $stJs.= "document.getElementById('stCGM').innerHTML = '".$rsCGM->getCampo('nom_cgm')."';";
        $stJs.= "document.getElementById('vlSaldoAnterior').value = '".$arDivida['vlSaldoAnterior']."';";
        $stJs.= "document.getElementById('vlContratacao').value = '".$arDivida['vlContratacao']."';";
        $stJs.= "document.getElementById('vlAmortizacao').value = '".$arDivida['vlAmortizacao']."';";
        $stJs.= "document.getElementById('vlCancelamento').value = '".$arDivida['vlCancelamento']."';";
        $stJs.= "document.getElementById('vlEncampacao').value = '".$arDivida['vlEncampacao']."';";
        $stJs.= "document.getElementById('vlAtualizacao').value = '".$arDivida['vlAtualizacao']."';";
        $stJs.= "document.getElementById('vlSaldoAtual').value = '".$arDivida['vlSaldoAtual']."';";
        $stJs.= "document.getElementById('btIncluir').value = 'Alterar';";
        $stJs.= "document.getElementById('btIncluir').setAttribute('onClick','montaParametrosGET( \'alterarListaItens\', \'hdnId,inMes,inOrgao,inUnidade,inTipoLancamento,stLeiAutorizacao,dtLeiAutorizacao,inCGM,inTipoPessoa,stCpfCnpj,vlSaldoAnterior,vlContratacao,vlAmortizacao,vlCancelamento,vlEncampacao,vlAtualizacao,vlSaldoAtual\' );');";
        echo $stJs;

        buscaUnidade($arDivida['inOrgao'], false);
        $stJs.= "document.getElementById('inUnidade').value = '".$arDivida['inUnidade']."';";
        echo $stJs;

    break;

    case 'alterarListaItens':
        $stMensagem = executaValidacao($_REQUEST);
        $inCount    = 0;

        if ($stMensagem == "") {
            foreach ($arDivida as $key => $value) {
                if ($_REQUEST['hdnId'] == $value['id']) {
                    $obTTCMGOTipoLancamento = new TTCMGOTipoLancamento();
                    $obTTCMGOTipoLancamento->setDado('cod_lancamento', $_REQUEST['inTipoLancamento']);
                    $obTTCMGOTipoLancamento->recuperaPorChave($rsTipoLancamento);

                    $obTCGM = new TCGM();
                    $obTCGM->setDado('numcgm', $_REQUEST['inCGM']);
                    $obTCGM->recuperaPorChave($rsCGM);

                    $arDivida[$inCount]['id']                     = $_REQUEST['hdnId'];
                    $arDivida[$inCount]['inMes']                  = $_REQUEST['inMes'];
                    $arDivida[$inCount]['inOrgao']                = $_REQUEST['inOrgao'];
                    $arDivida[$inCount]['inUnidade']              = $_REQUEST['inUnidade'];
                    $arDivida[$inCount]['inTipoLancamento']       = $_REQUEST['inTipoLancamento'];
                    $arDivida[$inCount]['inTipoLancamentoRotulo'] = $rsTipoLancamento->getCampo('cod_lancamento').' - '.$rsTipoLancamento->getCampo('descricao');
                    $arDivida[$inCount]['stLeiAutorizacao']       = $_REQUEST['stLeiAutorizacao'];
                    $arDivida[$inCount]['dtLeiAutorizacao']       = $_REQUEST['dtLeiAutorizacao'];
                    $arDivida[$inCount]['inCGM']                  = $_REQUEST['inCGM'];
                    $arDivida[$inCount]['inCGMRotulo']            = $rsCGM->getCampo('numcgm').' - '.$rsCGM->getCampo('nom_cgm');
                    $arDivida[$inCount]['vlSaldoAnterior']        = $_REQUEST['vlSaldoAnterior'];
                    $arDivida[$inCount]['vlContratacao']          = $_REQUEST['vlContratacao'];
                    $arDivida[$inCount]['vlAmortizacao']          = $_REQUEST['vlAmortizacao'];
                    $arDivida[$inCount]['vlCancelamento']         = $_REQUEST['vlCancelamento'];
                    $arDivida[$inCount]['vlEncampacao']           = $_REQUEST['vlEncampacao'];
                    $arDivida[$inCount]['vlAtualizacao']          = $_REQUEST['vlAtualizacao'];
                    $arDivida[$inCount]['vlSaldoAtual']           = $_REQUEST['vlSaldoAtual'];
                }

                $inCount++;
            }

            Sessao::write('arDivida',$arDivida);

            echo 'limparDivida();';
            echo montaLista( $arDivida );

            $stJs = "document.getElementById('btIncluir').value = 'Incluir';";
            $stJs.= "document.getElementById('btIncluir').setAttribute('onClick','montaParametrosGET( \'incluiDivida\', \'inMes,inOrgao,inUnidade,inTipoLancamento,stLeiAutorizacao,dtLeiAutorizacao,inCGM,inTipoPessoa,stCpfCnpj,vlSaldoAnterior,vlContratacao,vlAmortizacao,vlCancelamento,vlEncampacao,vlAtualizacao,vlSaldoAtual\' );');";
            echo $stJs;
        } else {
            echo "alertaAviso('".$stMensagem."!','form','erro','".Sessao::getId()."');";
        }
    break;

    case 'excluirListaItens':
        $arTemp  = array();
        $inCount = 0;

        foreach ($arDivida as $arValue) {
            if ($arValue['id'] != $_REQUEST['id']) {
                $obTTCMGOTipoLancamento = new TTCMGOTipoLancamento();
                $obTTCMGOTipoLancamento->setDado('cod_lancamento', $arValue['inTipoLancamento']);
                $obTTCMGOTipoLancamento->recuperaPorChave($rsTipoLancamento);

                $obTCGM = new TCGM();
                $obTCGM->setDado('numcgm', $arValue['inCGM']);
                $obTCGM->recuperaPorChave($rsCGM);

                $arTemp[$inCount]['id']                     = $arValue['id'];
                $arTemp[$inCount]['inMes']                  = $arValue['inMes'];
                $arTemp[$inCount]['inOrgao']                = $arValue['inOrgao'];
                $arTemp[$inCount]['inUnidade']              = $arValue['inUnidade'];
                $arTemp[$inCount]['inTipoLancamento']       = $arValue['inTipoLancamento'];
                $arTemp[$inCount]['inTipoLancamentoRotulo'] = $rsTipoLancamento->getCampo('cod_lancamento').' - '.$rsTipoLancamento->getCampo('descricao');
                $arTemp[$inCount]['stLeiAutorizacao']       = $arValue['stLeiAutorizacao'];
                $arTemp[$inCount]['dtLeiAutorizacao']       = $arValue['dtLeiAutorizacao'];
                $arTemp[$inCount]['inCGM']                  = $arValue['inCGM'];
                $arTemp[$inCount]['inCGMRotulo']            = $rsCGM->getCampo('numcgm').' - '.$rsCGM->getCampo('nom_cgm');
                $arTemp[$inCount]['vlSaldoAnterior']        = $arValue['vlSaldoAnterior'];
                $arTemp[$inCount]['vlContratacao']          = $arValue['vlContratacao'];
                $arTemp[$inCount]['vlAmortizacao']          = $arValue['vlAmortizacao'];
                $arTemp[$inCount]['vlCancelamento']         = $arValue['vlCancelamento'];
                $arTemp[$inCount]['vlEncampacao']           = $arValue['vlEncampacao'];
                $arTemp[$inCount]['vlAtualizacao']          = $arValue['vlAtualizacao'];
                $arTemp[$inCount]['vlSaldoAtual']           = $arValue['vlSaldoAtual'];

                $inCount++;
            }
        }

        Sessao::write('arDivida', $arTemp);
        echo montaLista( $arTemp );
    break;

    case 'buscaDividas':
        $arTemp  = array();
        $inCount = 0;
        $rsDivida = new RecordSet;

        //Formata data
        $dtInicio = Sessao::getExercicio().'-'.$_REQUEST['inMes'].'-01';
        $dtFim    = SistemaLegado::dataToSql(SistemaLegado::retornaUltimoDiaMes($_REQUEST['inMes'], Sessao::getExercicio()));

        $obTTCMGODividaConsolidada = new TTCMGODividaConsolidada();
        $obTTCMGODividaConsolidada->setDado('dt_inicio' , $dtInicio);
        $obTTCMGODividaConsolidada->setDado('dt_fim'    , $dtFim);
        $obTTCMGODividaConsolidada->setDado('exercicio' , Sessao::getExercicio());
        $obTTCMGODividaConsolidada->recuperaDividaPorMes($rsDivida);

        foreach ($rsDivida->arElementos as $arValue) {
            $obTTGOTipoLancamento = new TTCMGOTipoLancamento();
            $obTTGOTipoLancamento->setDado('cod_lancamento', $arValue['tipo_lancamento']);
            $obTTGOTipoLancamento->recuperaPorChave($rsTipoLancamento);

            $obTCGM = new TCGM();
            $obTCGM->setDado('numcgm', $arValue['numcgm']);
            $obTCGM->recuperaPorChave($rsCGM);

            $arTemp[$inCount]['id']                     = $inCount;
            $arTemp[$inCount]['inMes']                  = $_REQUEST['inMes'];
            $arTemp[$inCount]['inOrgao']                = $arValue['num_orgao'];
            $arTemp[$inCount]['inUnidade']              = $arValue['num_unidade'];
            $arTemp[$inCount]['inTipoLancamento']       = $arValue['tipo_lancamento'];
            $arTemp[$inCount]['inTipoLancamentoRotulo'] = $rsTipoLancamento->getCampo('cod_lancamento').' - '.$rsTipoLancamento->getCampo('descricao');
            $arTemp[$inCount]['stLeiAutorizacao']       = $arValue['nro_lei_autorizacao'];
            $arTemp[$inCount]['dtLeiAutorizacao']       = SistemaLegado::dataToBr($arValue['dt_lei_autorizacao']);
            $arTemp[$inCount]['inCGM']                  = $arValue['numcgm'];
            $arTemp[$inCount]['inCGMRotulo']            = $rsCGM->getCampo('numcgm').' - '.$rsCGM->getCampo('nom_cgm');
            $arTemp[$inCount]['vlSaldoAnterior']        = number_format($arValue['vl_saldo_anterior'], '2', ',', '');
            $arTemp[$inCount]['vlContratacao']          = number_format($arValue['vl_contratacao'], '2', ',', '');
            $arTemp[$inCount]['vlAmortizacao']          = number_format($arValue['vl_amortizacao'], '2', ',', '');
            $arTemp[$inCount]['vlCancelamento']         = number_format($arValue['vl_cancelamento'], '2', ',', '');
            $arTemp[$inCount]['vlEncampacao']           = number_format($arValue['vl_encampacao'], '2', ',', '');
            $arTemp[$inCount]['vlAtualizacao']          = number_format($arValue['vl_atualizacao'], '2', ',', '');
            $arTemp[$inCount]['vlSaldoAtual']           = number_format($arValue['vl_saldo_atual'], '2', ',', '');

            $inCount++;
        }

        Sessao::write('arDivida', $arTemp);
        echo '<script>'.montaLista( $arTemp ).'</script>';
    break;
}

function montaLista($arDivida)
{
    $rsDivida = new RecordSet();
    $rsDivida->preenche( $arDivida  );

    $obTable = new Table();
    $obTable->setRecordSet( $rsDivida );
    $obTable->setSummary('Dívidas consolidadas');

   // $obTable->setConditional( true , "#efefef" );

    $obTable->Head->addCabecalho( 'Orgão' , 7 );
    $obTable->Head->addCabecalho( 'Unidade' , 7 );
    $obTable->Head->addCabecalho( 'Tipo Lançamento' , 20 );
    $obTable->Head->addCabecalho( 'n° Lei' , 7 );
    $obTable->Head->addCabecalho( 'Data da Lei' , 7 );
    $obTable->Head->addCabecalho( 'CGM Credor' , 30 );

    $obTable->Body->addCampo( 'inOrgao', 'C' );
    $obTable->Body->addCampo( 'inUnidade', 'C' );
    $obTable->Body->addCampo( 'inTipoLancamentoRotulo', 'E' );
    $obTable->Body->addCampo( 'stLeiAutorizacao', 'C' );
    $obTable->Body->addCampo( 'dtLeiAutorizacao', 'C' );
    $obTable->Body->addCampo( 'inCGMRotulo', 'E' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s)', array( 'id' ) );
    $obTable->Body->addAcao( 'alterar' ,  'montaAlteracaoLista(%s)' , array( 'id' ) );

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "window.parent.frames['telaPrincipal'].document.getElementById('spnDivida').innerHTML = '".$stHTML."';";
    $stJs.= "window.parent.frames['telaPrincipal'].limparDivida();";

    return $stJs;
}

function buscaUnidade($inOrgao, $tags=true)
{
    include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php" );

    $obForm = new Form;
    $obForm->setAction( $pgProc );
    $obForm->setTarget( "oculto" );

    $obTOrcamentoUnidade = new TOrcamentoUnidade();
    $obTOrcamentoUnidade->setDado('exercicio', Sessao::getExercicio());
    $obTOrcamentoUnidade->setDado('num_orgao', $inOrgao);
    $obTOrcamentoUnidade->recuperaPorOrgao( $rsUnidade );

    $obCmbUnidade = new Select();
    $obCmbUnidade->setRotulo( 'Unidade' );
    $obCmbUnidade->setTitle( 'Selecione a Unidade' );
    $obCmbUnidade->setName( 'inUnidade' );
    $obCmbUnidade->setId( 'inUnidade' );
    $obCmbUnidade->addOption( '', 'Selecione' );
    $obCmbUnidade->setCampoId( 'num_unidade' );
    $obCmbUnidade->setCampoDesc( 'nom_unidade' );
    $obCmbUnidade->setStyle('width: 520');
    $obCmbUnidade->preencheCombo( $rsUnidade );
    $obCmbUnidade->setNull( false );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obCmbUnidade );
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    if ($tags) {
        $js = "<script>window.parent.frames['telaPrincipal'].document.getElementById('spnUnidade').innerHTML = '".$stHtml."';</script>";
    } else {
        $js = "window.parent.frames['telaPrincipal'].document.getElementById('spnUnidade').innerHTML = '".$stHtml."';";
    }

    echo $js;
}

function executaValidacao($array)
{
    $stMensagem = "";

    if ($array['inMes'] == '') {
        $stMensagem = 'Perído inválido';

    } elseif ($array['inOrgao'] == '') {
        $stMensagem = 'Orgão inválido';

    } elseif ($array['inUnidade'] == '') {
        $stMensagem = 'Unidade inválida';

    } elseif ($array['inTipoLancamento'] == '') {
        $stMensagem = 'Tipo de lançamento inválido';

    } elseif ($array['stLeiAutorizacao'] == '') {
        $stMensagem = 'N° da lei inválido';

    } elseif ($array['stLeiAutorizacao'] == '') {
        $stMensagem = 'Data da lei inválido';
    }

    $arDivida   = Sessao::read('arDivida');
    foreach ($arDivida as $arValue) {
        if(($array['hdnId'] != $arValue['id'])&&
           ($array['inMes'] == $arValue['inMes'])&&
           ($array['inOrgao'] == $arValue['inOrgao'])&&
           ($array['inUnidade'] == $arValue['inUnidade'])&&
           ($array['inTipoLancamento'] == $arValue['inTipoLancamento'])&&
           ($array['stLeiAutorizacao'] == $arValue['stLeiAutorizacao'])&&
           ($array['dtLeiAutorizacao'] == $arValue['dtLeiAutorizacao'])){

            $stMensagem = "Já consta na lista um lançamento igual a este. Por favor, verifique os dados inseridos";
        }
    }

    return $stMensagem;
}
