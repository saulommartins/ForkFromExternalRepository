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
    * Data de Criação   : 30/04/2007

    * @author Henrique Boaventura

    * @ignore

    * $Id: OCManterTipoConta.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(TTGO.'TTGOGrupoPlanoConta.class.php');
include_once(TTGO.'TTGOTipoConta.class.php');

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoConta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

function montaLista($arConta)
{

    $rsConta = new RecordSet();
    if ( is_array($arConta) ) {
        $rsConta->preenche( $arConta );
    } else {
       $rsConta->preenche( array() );
    }

    $obTable = new Table();
    $obTable->setRecordSet( $rsConta );
    $obTable->setSummary('Lista de Contas');

    //$obTable->setConditional( true , "#efefef" );

    $obTable->Head->addCabecalho( 'Conta' , 10);
    $obTable->Head->addCabecalho( 'Código Estrutural' , 20 );
    $obTable->Head->addCabecalho( 'Descrição' , 50 );

    $obTable->Body->addCampo( 'cod_conta', 'E' );
    $obTable->Body->addCampo( 'cod_estrutural', 'E' );
    $obTable->Body->addCampo( 'nom_conta', 'E' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s)', array( 'id' ) );

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "document.getElementById('spnContas').innerHTML = '".$stHTML."';";

    return $stJs;
}

$arContas = Sessao::read('arContas');

switch ($stCtrl) {
    case 'buscaEstrutural' :
        if ( substr( $_REQUEST['inCodConta'],0,1 ) != $_REQUEST['inTipoLancamento'] ) {
            $stJs.= "document.getElementById('inCodConta').value = '';";
            $stJs.= "alertaAviso('@Conta inválida!', 'form','erro','".Sessao::getId()."');";
            break;
        }
        include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php");
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta();
        $obTContabilidadePlanoConta->setDado('exercicio',Sessao::getExercicio());
        $obTContabilidadePlanoConta->setDado('cod_estrutural',$_REQUEST['inCodConta']);
        $stFiltro = " AND ( cod_estrutural LIKE '1.%' OR cod_estrutural LIKE '2.%' ) ";
        $obTContabilidadePlanoConta->recuperaContaSintetica( $rsConta, $stFiltro );
        if ( $rsConta->getNumLinhas() > 0 ) {
            $stJs = "document.getElementById('stConta').innerHTML = '".$rsConta->getCampo('nom_conta')."'; ";
        } else {
            $stJs.= "alertaAviso('@Código estrutural inválido!', 'form','erro','".Sessao::getId()."');";
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inCodConta').value = '';";
        }
        break;
    case 'preencheTipoConta' :
        $stJs = "limpaSelect(document.getElementById('inTipoConta'),0);";
        $stJs.= "document.getElementById('inTipoConta').options[0] = new Option('Selecione','','selected');";
        $stJs.= "document.getElementById('inCodConta').value = '';";
        if ($_REQUEST['inTipoLancamento'] != '') {
            $obTTGOTipoConta = new TTGOTipoConta();
            $stFiltro = " WHERE cod_tipo_lancamento = ".$_REQUEST['inTipoLancamento']." ";
            $obTTGOTipoConta->recuperaTodos( $rsTipoConta, $stFiltro );
            $i = 0;
            while ( !$rsTipoConta->eof() ) {
                $stJs.= "document.getElementById('inTipoConta').options[".++$i."] = new Option('".$rsTipoConta->getCampo('descricao')."','".$rsTipoConta->getCampo('cod_tipo')."','".$selected."');";
                $rsTipoConta->proximo();
            }
            $stJs.= "document.getElementById('inCodConta').value = '".$_REQUEST['inTipoLancamento'].".';";
        }
        break;
    case 'incluirConta' :
        if ( strlen( $_REQUEST['inCodConta'] ) <= 2 ) {
            $stMensagem = 'Conta inválida!';
        }

        if ( substr( $_REQUEST['inCodConta'],0,1 ) != $_REQUEST['inTipoLancamento'] ) {
            $stMensagem = "Conta inválida!";
        }

        if ($_REQUEST['inTipoConta'] == '') {
            $stMensagem = 'Preencha o tipo da conta!';
        }

        if ($_REQUEST['inTipoLancamento'] == '') {
            $stMensagem = 'Preencha o tipo do lançamento!';
        }

        if ( count( $arContas ) > 0 ) {
            foreach ($arContas as $arAux) {
                if ($_REQUEST['inCodConta'] == $arAux['cod_estrutural']) {
                    $stMensagem = 'Esta conta já consta na lista!';
                    break;
                }
            }
        }

        if ( count( $arContas ) > 0 ) {
            foreach ($arContas as $arAux) {
                $arEstrutural = explode('.',$arAux['cod_estrutural']);
                $arNovoEstrutural = explode('.',$_REQUEST['inCodConta']);
                for ($i = 0; $i < count($arEstrutural); $i++ ) {
                    //echo $arEstrutural[$i].'-'.$arNovoEstrutural[$i].'<br/>';
                    if ( ($arEstrutural[$i] != $arNovoEstrutural[$i]) ) {
                        if ( intval($arNovoEstrutural[$i]) == 0 OR intval($arEstrutural[$i]) == 0 ) {
                            //Uma conta mãe ou uma conta filha da conta que se deseja cadastrar
                            //já foi incluída anteriormente
                            $stMensagem = 'Uma conta desta classificação já foi cadastrada';
                        }
                         break;
                    }
                }
            }
        }

        if (!$stMensagem) {
            $inCount = count($arContas);
            $arContas[$inCount]['id'] = $inCount;
            $arContas[$inCount]['cod_estrutural'] = $_REQUEST['inCodConta'];
            $arContas[$inCount]['cod_conta'] = sistemaLegado::pegaDado("cod_conta","contabilidade.plano_conta"," where cod_estrutural = '".$_REQUEST['inCodConta']."' AND exercicio = '".Sessao::getExercicio()."' ");
            $arContas[$inCount]['nom_conta'] = sistemaLegado::pegaDado("nom_conta","contabilidade.plano_conta"," where cod_estrutural = '".$_REQUEST['inCodConta']."' AND exercicio = '".Sessao::getExercicio()."' ");
            $stJs.= montaLista( $arContas );
            Sessao::write('arContas', $arContas);
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inCodConta').value = '';";
        } else {
            $stJs .= "alertaAviso('@".$stMensagem."!', 'form','erro','".Sessao::getId()."');";
        }
        break;
    case 'excluirListaItens' :
        $i=0;
        foreach ($arContas as $arAux) {
            if ($arAux['id'] != $_REQUEST['id']) {
                $arTemp[$i] = $arAux;
                $arTemp[$i]['id'] = $i;
                $i++;

            }
        }
        Sessao::write('arContas', $arTemp);
        $stJs = montaLista( $arTemp );
        break;
    case 'preencheLista' :
        $obTTGOGrupoPlanoConta = new TTGOGrupoPlanoConta();
        $obTTGOGrupoPlanoConta->setDado('exercicio',Sessao::getExercicio());
        $obTTGOGrupoPlanoConta->setDado('cod_tipo_lancamento',$_REQUEST['inTipoLancamento']);
        $obTTGOGrupoPlanoConta->setDado('cod_tipo',$_REQUEST['inTipoConta']);
        $obTTGOGrupoPlanoConta->recuperaGrupoPlanoConta( $rsContas );
        $arContas = array();

        while ( !$rsContas->eof() ) {
            $inCount = count($arContas);
            $arContas[$inCount]['id'] = $inCount;
            $arContas[$inCount]['cod_estrutural'] = sistemaLegado::pegaDado("cod_estrutural","contabilidade.plano_conta"," where cod_conta = '".$rsContas->getCampo('cod_conta')."' ");
            $arContas[$inCount]['cod_conta'] = $rsContas->getCampo('cod_conta');
            $arContas[$inCount]['nom_conta'] = sistemaLegado::pegaDado("nom_conta","contabilidade.plano_conta"," where cod_conta = '".$rsContas->getCampo('cod_conta')."' ");
            $rsContas->proximo();
        }
        $stJs.= montaLista( $arContas );
        Sessao::write('arContas', $arContas);
        break;
}
echo $stJs;
