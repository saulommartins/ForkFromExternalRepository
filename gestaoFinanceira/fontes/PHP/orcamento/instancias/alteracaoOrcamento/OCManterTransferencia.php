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
    * Classe Oculta de Transferencias
    * Data de Criação   : 08/03/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: OCManterTransferencia.php 62400 2015-05-04 17:30:31Z michel $

    * Casos de uso: uc-02.01.07
*/

/*
$Log$
Revision 1.5  2006/07/05 20:42:23  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterSuplementacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

list($inCodNorma,$stDecreto)  = $_REQUEST['inCodNorma'];

$obRegra = new ROrcamentoSuplementacao;
$obRegra->setExercicio( Sessao::getExercicio() );

function montaLabelsNorma($inCodNorma)
{
    global $obRegra;

    $obRegra->obRNorma->setCodNorma( $inCodNorma );
    $obRegra->obRNorma->setExercicio( Sessao::getExercicio() );
    $obRegra->obRNorma->consultar( $rsNorma );

    if ( !$rsNorma->eof() ) {
        $js     = "d.getElementById('stLblNomeNorma').innerHTML = '". $obRegra->obRNorma->getNomeNorma()."';";
    } else {
        $js     = "d.getElementById('stLblNomeNorma').innerHTML = '&nbsp;';";
    }

    return $js;
}

function montaListaReducoes($arRecordSet , $nuVlTotalReducao = 0, $boExecuta = true)
{
        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $rsLista->addFormatacao( "vl_valor", "NUMERIC_BR" );
        if ( !$rsLista->eof() ) {

            $nuVlTotalReducao = number_format( $nuVlTotalReducao, 2, ',', '.');
            $obLblTotal = new Label;
            $obLblTotal->setRotulo        ( "Total da Reducao" );
            $obLblTotal->setName          ( "nuVlTotalReducao" );
            $obLblTotal->setId            ( "nuVlTotalReducao" );
            $obLblTotal->setValue         ( $nuVlTotalReducao  );

            $obFormulario = new Formulario;
            $obFormulario->addComponente        ( $obLblTotal   );
            $obFormulario->montaInnerHTML();

            $obLista = new Lista;
            $obLista->setTitulo( "Registros de reduções" );
            $obLista->setMostraPaginacao( false );
            $obLista->setRecordSet( $rsLista );
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Reduzido");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Despesa");
            $obLista->ultimoCabecalho->setWidth( 40 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Descrição");
            $obLista->ultimoCabecalho->setWidth( 40 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Valor");
            $obLista->ultimoCabecalho->setWidth( 15 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "cod_reduzido" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "dotacao" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "descricao" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "vl_valor" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "EXCLUIR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:excluirDotacaoRedutora('excluirReducao');" );
            $obLista->ultimaAcao->addCampo("1","cod_reduzido");
            $obLista->commitAcao();

            $obLista->montaHTML();

            $stHTML = $obFormulario->getHTML() . $obLista->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );

            $stLista   .= "d.getElementById('spnReducoes').innerHTML = '".$stHTML."'; ";
        } else {
            $stLista   .= "d.getElementById('spnReducoes').innerHTML = ''; ";
            Sessao::remove('arRedutoras');
        }

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto($stLista);
        } else {
            return $stLista;
        }

}

function montaListaSuplementada($arRecordSet, $nuVlTotalSuplementada = 0, $boExecuta = true)
{
        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $rsLista->addFormatacao( "vl_valor", "NUMERIC_BR" );
        if ( !$rsLista->eof() ) {

            $nuVlTotalSuplementada = number_format( $nuVlTotalSuplementada, 2, ',', '.');
            $obLblTotal = new Label;
            $obLblTotal->setRotulo        ( "Total da Suplementação" );
            $obLblTotal->setName          ( "nuVlTotalSuplementada" );
            $obLblTotal->setId            ( "nuVlTotalSuplementada" );
            $obLblTotal->setValue         ( $nuVlTotalSuplementada  );

            $obFormulario = new Formulario;
            $obFormulario->addComponente        ( $obLblTotal   );
            $obFormulario->montaInnerHTML();

            $obLista = new Lista;
            $obLista->setTitulo( "Registros de suplementações" );
            $obLista->setMostraPaginacao( false );
            $obLista->setRecordSet( $rsLista );
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Reduzido");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Despesa");
            $obLista->ultimoCabecalho->setWidth( 40 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Descrição");
            $obLista->ultimoCabecalho->setWidth( 40 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Valor");
            $obLista->ultimoCabecalho->setWidth( 15 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "cod_reduzido" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "dotacao" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "descricao" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "vl_valor" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "EXCLUIR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:excluirDotacaoSuplementada('excluirSuplementada');" );
            $obLista->ultimaAcao->addCampo("1","cod_reduzido");
            $obLista->commitAcao();

            $obLista->montaHTML();

            $stHTML = $obFormulario->getHTML() . $obLista->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );

            $stLista    = "d.getElementById('spnSuplementada').innerHTML = '".$stHTML."'; ";
        } else {
            $stLista    = "d.getElementById('spnSuplementada').innerHTML = ''; ";
            Sessao::remove('arSuplementada');
        }

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto($stLista);
        } else {
            return $stLista;
        }

}

switch ($stCtrl) {

    case 'norma':
        $js  = montaLabelsNorma( $inCodNorma );
        SistemaLegado::executaFrameOculto($js);
    break;
    case 'reducoes':
        $js  = montaListaReducoes( $arRecordReducoes );
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'incluirReducao':
        $nuVlTotal       = str_replace( '.' , '' , $_POST['nuVlTotal']           );
        $nuVlTotal       = str_replace( ',' ,'.' , $nuVlTotal                    );
        $nuVlRedutora    = str_replace( '.' , '' , $_POST['nuVlDotacaoRedutora'] );
        $nuVlRedutora    = str_replace( ',' ,'.' , $nuVlRedutora                 );
        $nuVlSomatoria   = $nuVlRedutora;

        $arRedutoras = Sessao::read('arRedutoras');
        $inCount = sizeof($arRedutoras);
        if ($inCount) {
            foreach ($arRedutoras as $value) {
                if ($value['cod_reduzido'] != $_POST['inCodDotacaoReducao']) {
                    $nuVlSomatoria += $value['vl_valor'];
                } else {
                    SistemaLegado::exibeAviso('Esta dotação já está presente na lista',"n_incluir","erro");
                    exit;
                }
            }
        }
//        if ($nuVlSomatoria <= $nuVlTotal) {
        if ( bcsub($nuVlSomatoria, $nuVlTotal, 4) <= 0 ) {
            $obRegra->addDespesaReducao();
            $obRegra->roUltimoDespesaReducao->setCodDespesa( $_REQUEST["inCodDotacaoReducao"] );
            $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
            $obRegra->roUltimoDespesaReducao->setExercicio( Sessao::getExercicio() );

            $obRegra->roUltimoDespesaReducao->consultarSaldoDotacao();
            if ( $nuVlRedutora <= $obRegra->roUltimoDespesaReducao->getSaldoDotacao() ) {
                $obRegra->roUltimoDespesaReducao->listarDespesaDotacao( $rsDespesa );

                $arRedutoras[$inCount]['num_redutora'] = $inCount+1;
                $arRedutoras[$inCount]['cod_reduzido'] = trim( $rsDespesa->getCampo('cod_despesa') );
                $arRedutoras[$inCount]['dotacao']      = trim( $rsDespesa->getCampo('dotacao')     );
                $arRedutoras[$inCount]['descricao']    = trim( $rsDespesa->getCampo('descricao')   );
                $arRedutoras[$inCount]['vl_valor']     = trim( $nuVlRedutora                       );

                Sessao::write('arRedutoras',$arRedutoras);
                $stHTML = montaListaReducoes( $arRedutoras, $nuVlSomatoria );
            } else {
                SistemaLegado::exibeAviso('Valor a reduzir é superior ao saldo da dotação',"n_incluir","erro");
            }
        } else {
            SistemaLegado::exibeAviso('Valor a reduzir é superior ao permitido',"n_incluir","erro");
        }
    break;

    case 'excluirReducao':
        $arTEMP    = array();
        $inCount   = 0;
        $nuVlTotal = 0;
        $arRedutoras = Sessao::read('arRedutoras');
        foreach ($arRedutoras as $value) {
            if ( ($value['cod_reduzido']) != $_GET['inCodDotacaoReducao'] ) {
                $arTEMP[$inCount]['cod_reduzido'] = $value['cod_reduzido'];
                $arTEMP[$inCount]['dotacao']      = $value['dotacao'];
                $arTEMP[$inCount]['descricao']    = $value['descricao'];
                $arTEMP[$inCount]['vl_valor']     = $value['vl_valor'];
                $nuVlTotal                       += $arTEMP[$inCount]['vl_valor'];
                $inCount++;
            }
        }
        Sessao::write('arRedutoras',$arTEMP);
        //sessao->transf3['arRedutoras'] = $arTEMP;
        montaListaReducoes( $arTEMP, $nuVlTotal );
    break;

    case 'incluirSuplementada':
        $nuVlTotal       = str_replace( '.' , '' , $_POST['nuVlTotal']           );
        $nuVlTotal       = str_replace( ',' ,'.' , $nuVlTotal                    );
        $nuVlSuplementar = str_replace( '.' , '' , $_POST['nuVlDotacaoSuplementada'] );
        $nuVlSuplementar = str_replace( ',' ,'.' , $nuVlSuplementar              );
        $nuVlSomatoria   = $nuVlSuplementar;

        $arSuplementada = Sessao::read('arSuplementada');
        $inCount = sizeof($arSuplementada);
        if ($inCount) {
            foreach ($arSuplementada as $value) {
                if ($value['cod_reduzido'] != $_POST['inCodDotacaoSuplementada']) {
                    $nuVlSomatoria += $value['vl_valor'];
                } else {
                    SistemaLegado::exibeAviso('Esta dotação já está presente na lista',"n_incluir","erro");
                    exit;
                }
            }
        }
        if ($nuVlSomatoria <= $nuVlTotal) {
            $obRegra->addDespesaSuplementada();
            $obRegra->roUltimoDespesaSuplementada->setCodDespesa( $_REQUEST["inCodDotacaoSuplementada"] );
            $obRegra->roUltimoDespesaSuplementada->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
            $obRegra->roUltimoDespesaSuplementada->setExercicio( Sessao::getExercicio() );

            $obRegra->roUltimoDespesaSuplementada->listarDespesaDotacao( $rsDespesa );

            $arSuplementada[$inCount]['num_redutora'] = $inCount+1;
            $arSuplementada[$inCount]['cod_reduzido'] = trim( $rsDespesa->getCampo('cod_despesa') );
            $arSuplementada[$inCount]['dotacao']      = trim( $rsDespesa->getCampo('dotacao')     );
            $arSuplementada[$inCount]['descricao']    = trim( $rsDespesa->getCampo('descricao')   );
            $arSuplementada[$inCount]['vl_valor']     = trim( $nuVlSuplementar                    );

            Sessao::write('arSuplementada',$arSuplementada);
            $stHTML = montaListaSuplementada( $arSuplementada, $nuVlSomatoria );
        } else {
            SistemaLegado::exibeAviso('Valor a suplementar é superior ao permitido',"n_incluir","erro");
        }
    break;
    case 'excluirSuplementada':
        $arTEMP    = array();
        $inCount   = 0;
        $nuVlTotal = 0;
        $arSuplementada = Sessao::read('arSuplementada');
        foreach ($arSuplementada as $value) {
            if ( ($value['cod_reduzido']) != $_GET['inCodDotacaoSuplementada'] ) {
                $arTEMP[$inCount]['cod_reduzido'] = $value['cod_reduzido'];
                $arTEMP[$inCount]['dotacao']      = $value['dotacao'];
                $arTEMP[$inCount]['descricao']    = $value['descricao'];
                $arTEMP[$inCount]['vl_valor']     = $value['vl_valor'];
                $nuVlTotal                       += $value['vl_valor'];
                $inCount++;
            }
        }
        Sessao::write('arSuplementada',$arTEMP);

        montaListaSuplementada( $arTEMP, $nuVlTotal );
    break;
    case 'buscaDespesaReducao':
        $boErro = false;
        $stMsg = '';
        
        if (($_REQUEST["inCodDotacaoReducao"] != "") && ($_REQUEST['inCodEntidade'] != "")) {
            $obRegra->addDespesaReducao();
            $obRegra->roUltimoDespesaReducao->setCodDespesa( $_REQUEST["inCodDotacaoReducao"] );
            $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
            $obRegra->roUltimoDespesaReducao->setExercicio( Sessao::getExercicio() );
            if( $_REQUEST['stAcao'] == 'Remaneja' )
                $obRegra->roUltimoDespesaReducao->obROrcamentoClassificacaoDespesa->setMascClassificacao('3.1');
            $obRegra->roUltimoDespesaReducao->listarDespesa( $rsDespesa );

            $stNomDespesa = $rsDespesa->getCampo( "descricao" );

            if (!$stNomDespesa) {
                $boErro = true;
                $stMsg .= "@Valor inválido. (".$_REQUEST["inCodDotacaoReducao"].")";
            } else {
                $js  = 'd.getElementById("stNomDotacaoRedutora").innerHTML = "'.$stNomDespesa.'";';
            }
        } else {
            $boErro = true;
            if($_REQUEST['inCodEntidade'] == "")
                $stMsg .= "@Selecione a Entidade para buscar a Dotação Orçamentária Redutora.";
        }
        
        if ($boErro) {
            $js  = 'f.inCodDotacaoReducao.value = "";';
            $js .= 'd.getElementById("stNomDotacaoRedutora").innerHTML = "&nbsp;";';
            if($stMsg!='')
                $js .= "alertaAviso('".$stMsg."','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'buscaDespesaSuplementada':
        $boErro = false;
        $stMsg = '';
        
        if (($_REQUEST["inCodDotacaoSuplementada"] != "") && ($_REQUEST['inCodEntidade'] != "")) {
            $obRegra->addDespesaSuplementada();
            $obRegra->roUltimoDespesaSuplementada->setCodDespesa( $_REQUEST["inCodDotacaoSuplementada"] );
            $obRegra->roUltimoDespesaSuplementada->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
            $obRegra->roUltimoDespesaSuplementada->setExercicio( Sessao::getExercicio() );
            if( $_REQUEST['stAcao'] == 'Remaneja' )
                $obRegra->roUltimoDespesaSuplementada->obROrcamentoClassificacaoDespesa->setMascClassificacao('3.1');
            $obRegra->roUltimoDespesaSuplementada->listarDespesa( $rsDespesa );

            $stNomDespesa = $rsDespesa->getCampo( "descricao" );

            if (!$stNomDespesa) {
                $boErro = true;
                $stMsg .= "@Valor inválido. (".$_REQUEST["inCodDotacaoSuplementada"].")";
            } else {
                $js  = 'd.getElementById("stNomDotacaoSuplementada").innerHTML = "'.$stNomDespesa.'";';
            }
        } else {
            $boErro = true;
            if($_REQUEST['inCodEntidade'] == "")
                $stMsg .= "@Selecione a Entidade para buscar a Dotação Orçamentária Suplementada.";
        }

        if ($boErro) {
            $js  = 'f.inCodDotacaoSuplementada.value = "";';
            $js .= 'd.getElementById("stNomDotacaoSuplementada").innerHTML = "&nbsp;";';
            if($stMsg!='')
                $js .= "alertaAviso('".$stMsg."','form','erro','".Sessao::getId()."');";
        }

        SistemaLegado::executaFrameOculto($js);
    break;
    case 'limparListas':
        Sessao::remove('arSuplementada');
        Sessao::remove('arRedutoras');
    break;

        case 'buscaNorma':
        if ($_POST['inCodNorma']) {
            $obRegra->obRNorma->setCodNorma( $_POST['inCodNorma'] );
            $obRegra->obRNorma->setExercicio( Sessao::getExercicio() );
            $obErro = $obRegra->obRNorma->consultar( $rsRecordSet );
            if ( !$obErro->ocorreu() ) {
                if ( $obRegra->obRNorma->getNomeNorma() != NULL ) {
                    $stNorma  = $obRegra->obRNorma->obRTipoNorma->getNomeTipoNorma().' '.$obRegra->obRNorma->getNumNorma();
                    $stNorma .= '/'.$obRegra->obRNorma->getExercicio().' - '.$obRegra->obRNorma->getNomeNorma();
                     $js = 'd.getElementById("stNomTipoNorma").innerHTML = "'.$stNorma.'";';
                } else {
                    $js  = 'f.inCodNorma.value = "";';
                    $js .= 'window.parent.frames["telaPrincipal"].document.frm.inCodNorma.focus();';
                    $js .= 'd.getElementById("stNomTipoNorma").innerHTML = "&nbsp;";';
                    $js .= ":alertaAviso('@Valor inválido. (".$_POST["inCodNorma"].")','form','erro','".Sessao::getId()."');";
                }
            }
        } else {
            $js = 'd.getElementById("stNomTipoNorma").innerHTML = "&nbsp;";';
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case "buscaHistorico":
    if ($_POST["inCodHistorico"] != "") {
        $obRegra->obRContabilidadeHistoricoPadrao->setCodHistorico( $_POST["inCodHistorico"] );
        $obRegra->obRContabilidadeHistoricoPadrao->setExercicio( Sessao::getExercicio() );
        $obRegra->obRContabilidadeHistoricoPadrao->consultar();
        $stNomHistorico = $obRegra->obRContabilidadeHistoricoPadrao->getNomHistorico();
        if (!$stNomHistorico) {
            $js .= 'f.inCodHistorico.value = "";';
            $js .= 'f.inCodHistorico.focus();';
            $js .= 'd.getElementById("stNomHistorico").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodHistorico"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomHistorico").innerHTML = "'.$stNomHistorico.'";';
        }
    } else $js .= 'd.getElementById("stNomHistorico").innerHTML = "&nbsp;";';
        SistemaLegado::executaFrameOculto($js);
    break;

}
?>
