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
    * Paginae Oculta de Credito Especial/Suplementar por Superavit
    * Data de Criação   : 22/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30813 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.07
*/

/*
$Log$
Revision 1.6  2006/07/05 20:42:23  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterSuperavit";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obROrcamentoSuplementacao = new ROrcamentoSuplementacao;
$obROrcamentoSuplementacao->setExercicio( Sessao::getExercicio() );
$obROrcamentoDespesa = new RorcamentoDespesa;
$obROrcamentoDespesa->setExercicio( Sessao::getExercicio());

function montaListaDespesa($arRecordSet , $stSpanName , $boExecuta = true)
{
        foreach ($arRecordSet as $value) {
            $nuLblVlTotal += $value['valor'];
        }
        $nuLblVlTotal = number_format( $nuLblVlTotal, 2, ',', '.' );

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
    if ($_GET["inCodDespesaSuplementar"] != "" AND $_GET['inCodEntidade'] != "") {
        $obROrcamentoSuplementacao->addDespesaReducao();
        $obROrcamentoSuplementacao->roUltimoDespesaReducao->setCodDespesa( $_GET["inCodDespesaSuplementar"] );
        $obROrcamentoSuplementacao->roUltimoDespesaReducao->obROrcamentoEntidade->setCodigoEntidade( $_GET["inCodEntidade"] );
        $obROrcamentoSuplementacao->roUltimoDespesaReducao->setExercicio( Sessao::getExercicio() );
        $obROrcamentoSuplementacao->roUltimoDespesaReducao->listarDespesa( $rsDespesa );

        $stNomDespesa = $rsDespesa->getCampo( "descricao" );

        if (!$stNomDespesa) {
            $js .= 'jq("#inCodDespesaSuplementar").val("");';
            $js .= 'jq("#inCodDespesaSuplementar").focus();';
            $js .= 'jq("#stNomDespesaSuplementar").html("&nbsp;");';
            $js .= "alertaAviso('@Valor inválido. (".$_GET["inCodDespesaSuplementar"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'jq("#inCodDespesaSuplementar").val("'.$_GET['inCodDespesaSuplementar'].'");';
            $js .= 'jq("#stNomDespesaSuplementar").html("'.$stNomDespesa.'");';
        }
    } else {
        $js = 'd.getElementById("stNomDespesaSuplementar").innerHTML ="&nbsp;";';
    }

    echo $js;

    break;

    case 'buscaDespesaEspecial':
        if ($_REQUEST["inCodDespesaSuplementar"] != "" &&  $_REQUEST['inCodEntidade'] != "") {
            $obROrcamentoDespesa->setCodDespesa($_REQUEST["inCodDespesaSuplementar"] );
            $obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade($_REQUEST["inCodEntidade"] );
            $obROrcamentoDespesa->listarDespesaCredEspecial($rsEspDespesa);
            $stNomDespesa = $rsEspDespesa->getCampo("descricao");

            if ($rsEspDespesa->getCampo( "vl_original" )) {
                if ( $rsEspDespesa->getCampo( "vl_original" ) != 0.00 ) {
                    $js.= "alertaAviso('@Dotação não é um credito especial.','form','erro','".Sessao::getId()."');";
                    $js.= 'jq("#inCodDespesaSuplementar").val("");';
                    $js.= 'jq("#inCodDespesaSuplementar").focus();';
                    $js.= 'jq("#stNomDespesaSuplementar").html("&nbsp;");';
                } else {
                    $js .= 'jq("#stNomDespesaSuplementar").html("'.$stNomDespesa.'");';
                }
            } else {
                $js.= "alertaAviso('@Dotação não é um credito especial.','form','erro','".Sessao::getId()."');";
                $js.= 'jq("#inCodDespesaSuplementar").val("");';
                $js.= 'jq("#inCodDespesaSuplementar").focus();';
                $js.= 'jq("#stNomDespesaSuplementar").html("&nbsp;");';
            }
        } else $js .= 'jq("#stNomDespesaSuplementar").html("&nbsp;");';
        echo $js;
    break;

   case 'incluirDespesaSuplementar':
        $nuVlSomatoria=0;
        $nuVlTotal       = str_replace( '.' , '' , $_GET['nuVlTotal']       );
        $nuVlTotal       = str_replace( ',' ,'.' , $nuVlTotal                );
        $nuVlSuplementar = str_replace( '.' , '' , $_GET['nuVlSuplementar'] );
        $nuVlSuplementar = str_replace( ',' ,'.' , $nuVlSuplementar          );
            $nuVlSomatoria   = $nuVlSuplementar;
        $arDespesaSuplementar = Sessao::read('arDespesaSuplementar');
        $inCount = sizeof($arDespesaSuplementar);

        $obErro = new Erro();

        if ($_GET['inCodDespesaSuplementar'] == '') {
            $obErro->setDescricao('Campo Dotação Orçamentária inválido!');
        } elseif (($nuVlTotal == '') OR ($nuVlTotal == 0)) {
            $obErro->setDescricao('Campo Valor Total inválido!( o valor deve ser maior que 0 (zero) )');
        } elseif (($nuVlSuplementar == '') OR ($nuVlSuplementar == 0)) {
            $obErro->setDescricao('Campo Valor inválido!( o valor deve ser maior que 0 (zero) )');
        }

        if ( $inCount AND !$obErro->ocorreu() ) {
            foreach ($arDespesaSuplementar as $value) {
                if ($value['cod_despesa'] != $_GET['inCodDespesaSuplementar']) {
                    $nuVlSomatoria = bcadd($nuVlSomatoria,$value['valor'],2);
                } else {
                    $obErro->setDescricao('Esta dotação já está presente na lista');
                }
            }
            if ($nuVlSomatoria > $nuVlTotal) {
                $obErro->setDescricao('Valor a suplementar é superior ao permitido');
            }
        }

        if (!$obErro->ocorreu()) {
            $obROrcamentoSuplementacao->addDespesaSuplementada();
            $obROrcamentoSuplementacao->roUltimoDespesaSuplementada->setCodDespesa($_GET['inCodDespesaSuplementar']);
            $obROrcamentoSuplementacao->roUltimoDespesaSuplementada->obROrcamentoEntidade->setCodigoEntidade( $_GET["inCodEntidade"] );
            $obROrcamentoSuplementacao->roUltimoDespesaSuplementada->setExercicio ( Sessao::getExercicio() );

            $obROrcamentoSuplementacao->roUltimoDespesaSuplementada->listarDespesa( $rsDespesa );

            $arDespesaSuplementar[$inCount]['cod_despesa'] = $_GET['inCodDespesaSuplementar'];
            $arDespesaSuplementar[$inCount]['dotacao']     = $rsDespesa->getCampo('dotacao');
            $arDespesaSuplementar[$inCount]['descricao']   = $rsDespesa->getCampo('descricao');
            $arDespesaSuplementar[$inCount]['valor']       = $nuVlSuplementar;

            $stHTML = montaListaDespesa( $arDespesaSuplementar , 'spnDespesaSuplementar', false);
            $stJs  = 'jq("#spnDespesaSuplementar").html(\''.$stHTML.'\');';
            $stJs .= 'limparDespesaSuplementar();';

            Sessao::write('arDespesaSuplementar',$arDespesaSuplementar);

        } else {
            $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
        }
        echo $stJs;
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
