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
    * Data de Criação   : 30s/04/2007

    * @author Henrique Boaventura

    * @ignore

    *$Id: OCManterDividaFundada.php 61530 2015-01-30 13:52:09Z jean $

    * Casos de uso : uc-06.04.00
*/
/*
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(TTGO.'TTGOGrupoPlanoAnalitica.class.php');
include_once(TTGO.'TTGOTipoConta.class.php');
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php" );

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

    $obTable->Head->addCabecalho( 'Código Estrutural' , 20 );
    $obTable->Head->addCabecalho( 'Descrição' , 50 );
    $obTable->Head->addCabecalho( 'Lei de Autorização', 10 );
    $obTable->Head->addCabecalho( 'Data de Autorização', 10 );

    $obTable->Body->addCampo( 'cod_estrutural', 'E' );
    $obTable->Body->addCampo( 'nom_conta', 'E' );
    $obTable->Body->addCampo( 'lei_autorizacao', 'E' );
    $obTable->Body->addCampo( 'data_autorizacao', 'E' );

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
        if ( substr( $_REQUEST['inCodConta'],0,1 ) != 2 ) {
            $stJs.= "document.getElementById('inCodConta').value = '';";
            $stJs.= "alertaAviso('@Conta inválida!', 'form','erro','".Sessao::getId()."');";
            break;
        }
        include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php");
        $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica();
        $obTContabilidadePlanoAnalitica->setDado('exercicio',Sessao::getExercicio());
        $obTContabilidadePlanoAnalitica->setDado('cod_estrutural',$_REQUEST['inCodConta']);
        $stFiltro = " AND ( cod_estrutural LIKE '2.%' ) ";
        $obTContabilidadePlanoAnalitica->recuperaCodPlanoPorEstrutural( $rsConta, $stFiltro );
        if ( $rsConta->getNumLinhas() > 0 ) {
            $stJs = "document.getElementById('stConta').innerHTML = '".sistemaLegado::pegaDado("nom_conta","contabilidade.plano_conta"," where cod_estrutural = '".$_REQUEST['inCodConta']."' AND exercicio = '".Sessao::getExercicio()."' ")."'; ";
        } else {
            $stJs.= "alertaAviso('@Código estrutural inválido!', 'form','erro','".Sessao::getId()."');";
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inCodConta').value = '';";
        }
        break;
    case 'incluirConta' :
        if ( strlen( $_REQUEST['inCodConta'] ) <= 2 ) {
            $stMensagem = 'Conta inválida!';
        }

        if ( substr( $_REQUEST['inCodConta'],0,1 ) != 2 ) {
            $stMensagem = 'Conta inválida!';
        }

        if ($_REQUEST['stData'] == '') {
            $stMensagem = 'Data inválida!';
        }

        if ($_REQUEST['inNumeroLei'] == '') {
            $stMensagem = 'Lei de Autorização inválida!';
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

        if (!$stMensagem) {
            $inCount = count($arContas);

            $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica();
            $obTContabilidadePlanoAnalitica->setDado( 'exercicio', Sessao::getExercicio() );
            $obTContabilidadePlanoAnalitica->setDado( 'cod_estrutural', $_REQUEST['inCodConta'] );
            $obTContabilidadePlanoAnalitica->recuperaCodPlanoPorEstrutural( $rsConta );
            $arContas[$inCount]['id'] = $inCount;
            $arContas[$inCount]['cod_estrutural'] = $_REQUEST['inCodConta'];
            $arContas[$inCount]['cod_plano'] = $rsConta->getCampo('cod_plano');
            $arContas[$inCount]['nom_conta'] = sistemaLegado::pegaDado("nom_conta","contabilidade.plano_conta"," where cod_estrutural = '".$_REQUEST['inCodConta']."' AND exercicio = '".Sessao::getExercicio()."' ");
            $arContas[$inCount]['lei_autorizacao'] = $_REQUEST['inNumeroLei'];
            $arContas[$inCount]['data_autorizacao'] = $_REQUEST['stData'];

            Sessao::write('arContas', $arContas);
            $stJs.= montaLista( $arContas );

            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inCodConta').value = '';";
            $stJs.= "document.getElementById('inNumeroLei').value = '';";
            $stJs.= "document.getElementById('stData').value = '';";
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
        $stJs = montaLista( $arContas );
        break;
    case 'preencheLista' :
        $arContas = array();
        if ($_REQUEST['inTipoLancamento'] != '') {
            $obTTGOGrupoPlanoAnalitica = new TTGOGrupoPlanoAnalitica();
            $obTTGOGrupoPlanoAnalitica->setDado('exercicio',Sessao::getExercicio());
            $obTTGOGrupoPlanoAnalitica->setDado('cod_tipo_lancamento',2);
            $obTTGOGrupoPlanoAnalitica->setDado('cod_tipo',$_REQUEST['inTipoLancamento']);
            $obTTGOGrupoPlanoAnalitica->recuperaRelacionamento( $rsContas );

            while ( !$rsContas->eof() ) {
                $inCount = count($arContas);
                $arContas[$inCount]['id'] = $inCount;
                $arContas[$inCount]['cod_estrutural'] = $rsContas->getCampo('cod_estrutural');
                $arContas[$inCount]['cod_plano'] = $rsContas->getCampo('cod_plano');
                $arContas[$inCount]['nom_conta'] = $rsContas->getCampo('nom_conta');
                $arContas[$inCount]['lei_autorizacao'] = $rsContas->getCampo('nro_lei');
                $arContas[$inCount]['data_autorizacao'] = $rsContas->getCampo('data_lei');

                $rsContas->proximo();
            }
        }
        Sessao::write('arContas', $arContas);
        $stJs.= montaLista( $arContas );
        break;
}
echo $stJs;
*/