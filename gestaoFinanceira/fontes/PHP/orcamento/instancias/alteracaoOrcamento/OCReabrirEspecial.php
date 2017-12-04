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
    * Paginae Oculta de Reabertura de Cred Especial
    * Data de Criação   : 10/04/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30813 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.07
*/

/*
$Log$
Revision 1.3  2006/07/05 20:42:23  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ReabrirEspecial";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obROrcamentoSuplementacao = new ROrcamentoSuplementacao;
$obROrcamentoSuplementacao->setExercicio( Sessao::getExercicio() );

function montaListaDespesa($arRecordSet , $stSpanName , $boExecuta = true)
{
        foreach ($arRecordSet as $value) {
            $nuLblVlTotal += $value['valor'];
        }
        $nuLblVlTotal = number_format( $nuLblVlTotal, 2, '.', ',' );

        // Define Objeto Moeda para valor total dos itens
        $obTxtVlTotal = new Label;
        $obTxtVlTotal->setRotulo   ( 'Valor Total'  );
        $obTxtVlTotal->setName     ( 'nuLblVlTotal' );
        $obTxtVlTotal->setValue    ( $nuLblVlTotal  );

        $obFormulario = new Formulario;
        $obFormulario->addComponente( $obTxtVlTotal );
        $obFormulario->montaInnerHTML();

        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $rsLista->addFormatacao( "valor", "NUMERIC_BR" );
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Reduzido");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Despesa");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Descrição");
        $obLista->ultimoCabecalho->setWidth( 55 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_despesa" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "dotacao" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->ultimoDado->setCampo( "descricao" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "valor" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        if ($stSpanName == 'spnDespesaSuplementar') {
            $obLista->ultimaAcao->setLink( "JavaScript:excluirDespesa('excluirDespesaSuplementar');" );
        }
        $obLista->ultimaAcao->addCampo("1","cod_despesa");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHTML  = $obFormulario->getHTML();
        $stHTML .= $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        if ($boExecuta) {
            $js  = "d.getElementById('".$stSpanName."').innerHTML = '".$stHTML."';";
            SistemaLegado::executaFrameOculto($js);
        } else {
            return $stHTML;
        }

}

switch ($stCtrl) {

    case 'buscaDespesa':
    if ($_POST["inCodDespesaSuplementar"] != "" and $inCodEntidade != "") {
        $obROrcamentoSuplementacao->addDespesaReducao();
        $obROrcamentoSuplementacao->roUltimoDespesaReducao->setCodDespesa( $_POST["inCodDespesaSuplementar"] );
        $obROrcamentoSuplementacao->roUltimoDespesaReducao->obROrcamentoEntidade->setCodigoEntidade( $_POST["inCodEntidade"] );
        $obROrcamentoSuplementacao->roUltimoDespesaReducao->setExercicio( Sessao::getExercicio() );
        $obROrcamentoSuplementacao->roUltimoDespesaReducao->listarDespesa( $rsDespesa );

        $stNomDespesa = $rsDespesa->getCampo( "descricao" );
        if (!$stNomDespesa) {
            $js .= 'f.inCodDespesaSuplementar.value = "";';
            $js .= 'window.parent.frames["telaPrincipal"].document.frm.inCodDespesaSuplementar.focus();';
            $js .= 'd.getElementById("stNomDespesaSuplementar").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodDespesaSuplementar"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomDespesaSuplementar").innerHTML = "'.$stNomDespesa.'";';
        }
    } else $js .= 'd.getElementById("stNomDespesaSuplementar").innerHTML = "&nbsp;";';
    SistemaLegado::executaFrameOculto($js);
    break;

    case 'incluirDespesaSuplementar':
        $nuVlSomatoria=0;
        $nuVlTotal       = str_replace( '.' , '' , $_POST['nuVlTotal']       );
        $nuVlTotal       = str_replace( ',' ,'.' , $nuVlTotal                );
        $nuVlSuplementar = str_replace( '.' , '' , $_POST['nuVlSuplementar'] );
        $nuVlSuplementar = str_replace( ',' ,'.' , $nuVlSuplementar          );
        $nuVlSomatoria   = $nuVlSuplementar;
        $arDespesaSuplementar = Sessao::read('arDespesaSuplementar');
        $inCount = sizeof($arDespesaSuplementar);
        if ($inCount) {
            foreach ($arDespesaSuplementar as $value) {
                if ($value['cod_despesa'] != $_POST['inCodDespesaSuplementar']) {
                    $nuVlSomatoria = bcadd($nuVlSomatoria,$value['valor'],2);
                } else {
                    SistemaLegado::exibeAviso('Esta dotação já está presente na lista',"n_incluir","erro");
                    exit;
                }
            }
        }
        if ($nuVlSomatoria <= $nuVlTotal) {
            $obROrcamentoSuplementacao->addDespesaSuplementada();
            $obROrcamentoSuplementacao->roUltimoDespesaSuplementada->setCodDespesa($_POST['inCodDespesaSuplementar']);
            $obROrcamentoSuplementacao->roUltimoDespesaSuplementada->obROrcamentoEntidade->setCodigoEntidade( $_POST["inCodEntidade"] );
            $obROrcamentoSuplementacao->roUltimoDespesaSuplementada->setExercicio ( Sessao::getExercicio() );

            $obROrcamentoSuplementacao->roUltimoDespesaSuplementada->listarDespesa( $rsDespesa );

            $arDespesaSuplementar[$inCount]['cod_despesa'] = $_POST['inCodDespesaSuplementar'];
            $arDespesaSuplementar[$inCount]['dotacao']     = $rsDespesa->getCampo('dotacao');
            $arDespesaSuplementar[$inCount]['descricao']   = $rsDespesa->getCampo('descricao');
            $arDespesaSuplementar[$inCount]['valor']       = $nuVlSuplementar;

            Sessao::write('arDespesaSuplementar',$arDespesaSuplementar);
            $stHTML = montaListaDespesa( $arDespesaSuplementar , 'spnDespesaSuplementar');
        } else {
            SistemaLegado::exibeAviso('Valor a suplementar é superior ao permitido',"n_incluir","erro");
        }
    break;

    case 'excluirDespesaSuplementar':
        $arTEMP = array();
        $inCount = 0;
        $arDespesaSuplementar = Sessao::read('arDespesaSuplementar');
        foreach ($arDespesaSuplementar as $value) {
            if ( ($value['cod_despesa']) != $_GET['inCodDespesa'] ) {
                $arTEMP[$inCount]['cod_despesa'] = $value['cod_despesa'];
                $arTEMP[$inCount]['dotacao']     = $value['dotacao'];
                $arTEMP[$inCount]['descricao']   = $value['descricao'];
                $arTEMP[$inCount]['valor']       = $value['valor'];
                $inCount++;
            }
        }
        Sessao::write('arDespesaSuplementar',$arTEMP);
        montaListaDespesa( $arTEMP, 'spnDespesaSuplementar' );
    break;

    case 'limparDespesaSuplementar':
        Sessao::remove('arDespesaSuplementar');
        //sessao->transf3['arDespesaSuplementar'] = array();
    break;

    case 'buscaNorma':
        if ($_POST['inCodNorma']) {
            $obROrcamentoSuplementacao->obRNorma->setCodNorma( $_POST['inCodNorma'] );
            $obROrcamentoSuplementacao->obRNorma->setExercicio( Sessao::getExercicio() );
            $obErro = $obROrcamentoSuplementacao->obRNorma->consultar( $rsRecordSet );
            if ( !$obErro->ocorreu() ) {
                if ( $obROrcamentoSuplementacao->obRNorma->getNomeNorma() != NULL ) {
                    $stNorma  = $obROrcamentoSuplementacao->obRNorma->obRTipoNorma->getNomeTipoNorma().' '.$obROrcamentoSuplementacao->obRNorma->getNumNorma();
                    $stNorma .= '/'.$obROrcamentoSuplementacao->obRNorma->getExercicio().' - '.$obROrcamentoSuplementacao->obRNorma->getNomeNorma();
                     $js = 'd.getElementById("stNomTipoNorma").innerHTML = "'.$stNorma.'";';
                } else {
                    $js  = 'f.inCodNorma.value = "";';
                    $js .= 'window.parent.frames["telaPrincipal"].document.frm.inCodNorma.focus();';
                    $js .= 'd.getElementById("stNomTipoNorma").innerHTML = "&nbsp;";';
                    $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodNorma"].")','form','erro','".Sessao::getId()."');";
                }
            }
        } else {
            $js = 'd.getElementById("stNomTipoNorma").innerHTML = "&nbsp;";';
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

}
?>
