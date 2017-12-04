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

    * $Id: OCManterPfdaaaa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(TTGO.'TTGOBalancoPfdaaaa.class.php');
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPfdaaaa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'] ?  $_REQUEST['stCtrl'] : $_REQUEST['stCtrl'];

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

    $obTable->Head->addCabecalho( 'Código' , 10 );
    $obTable->Head->addCabecalho( 'Código Estrutural' , 20 );
    $obTable->Head->addCabecalho( 'Descrição' , 50 );

    $obTable->Body->addCampo( 'cod_plano', 'E' );
    $obTable->Body->addCampo( 'cod_estrutural', 'E' );
    $obTable->Body->addCampo( 'nom_conta', 'E' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s,%s,%s)', array( 'id','tipo_lancamento','desdobramento_tipo' ) );

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "document.getElementById('spnContas').innerHTML = '".$stHTML."';";

    return $stJs;
}

$arContas = Sessao::read('arContas');
$arExcluidas = Sessao::read('arExcluidas');

switch ($stCtrl) {
    case 'preencheDesdobramento' :

        $stJs.= "document.getElementById('spnDesdobramento').innerHTML = '';";
        if ($_REQUEST['inTipoLancamento'] != '') {
            if ($_REQUEST['inTipoLancamento'] == 1) {
                $obCmbDesdobramento = new Select();
                $obCmbDesdobramento->setId( 'inCodDesdobramento' );
                $obCmbDesdobramento->setName( 'inCodDesdobramento' );
                $obCmbDesdobramento->setRotulo( 'Desdobramento' );
                $obCmbDesdobramento->setNull( false );
                $obCmbDesdobramento->addOption( '','Selecione' );
                $obCmbDesdobramento->addOption( '1','INSS' );
                $obCmbDesdobramento->addOption( '2','RPPS' );
                $obCmbDesdobramento->addOption( '3','IRRF' );
                $obCmbDesdobramento->addOption( '4','ISSQN' );
                $obCmbDesdobramento->addOption( '999','Outro' );
                $obCmbDesdobramento->obEvento->setOnChange("montaParametrosGET('preencheLista','inTipoLancamento,inCodDesdobramento','true');");
                $obFormulario = new Formulario();
                $obFormulario->addComponente( $obCmbDesdobramento );
                $obFormulario->montaInnerHTML();
                $stHTML = $obFormulario->getHTML();
                $stJs.= montaLista( array() );
                $stJs.= "document.getElementById('spnDesdobramento').innerHTML = '".$stHTML."';";
            } else {
                $obTTGOBalancoPfdaaaa = new TTGOBalancoPfdaaaa();
                $obTTGOBalancoPfdaaaa->setDado( 'exercicio', Sessao::getExercicio() );
                $obTTGOBalancoPfdaaaa->setDado( 'tipo_lancamento', $_REQUEST['inTipoLancamento'] );
                $_REQUEST['inCodDesdobramento'] = 0;
                $obTTGOBalancoPfdaaaa->setDado( 'desdobramento_tipo', $_REQUEST['inCodDesdobramento'] );
                $obTTGOBalancoPfdaaaa->recuperaRelacionamento( $rsContas );

                while ( !$rsContas->eof() ) {
                     $boExiste = false;
                    if ( count($arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento'] ]) > 0) {
                        foreach ($arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento'] ] as $arAux) {
                            if ( $arAux['cod_plano'] == $rsContas->getCampo('cod_plano') ) {
                                $boExiste = true;

                            }
                        }
                    }
                    if ($arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']]) {
                        foreach ($arExcluidas_['arExcluidas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']] as $arAux2) {
                            if ( $arAux2['cod_plano'] == $rsContas->getCampo('cod_plano') ) {
                                $boExiste = true;
                            }
                        }
                    }
                    if (!$boExiste) {
                        $inCount = count($arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']]);
                        $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['id'] = $inCount;
                        $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['cod_estrutural'] = $rsContas->getCampo('cod_estrutural');
                        $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['cod_plano'] = $rsContas->getCampo('cod_plano');
                        $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['nom_conta'] = $rsContas->getCampo('nom_conta');
                        $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['tipo_lancamento'] = $_REQUEST['inTipoLancamento'];
                        $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['desdobramento_tipo'] = $_REQUEST['inCodDesdobramento'];
                    }
                    $rsContas->proximo();
                }
                $stJs.= "document.getElementById('spnDesdobramento').innerHTML = '';";
            }
        }
        Sessao::write('arContas', $arContas);
        $stJs.= montaLista( $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']] );
        break;
    case 'buscaEstrutural' :
        if ($_REQUEST['inCodConta'] == '') {
            $stJs.= "document.getElementById('inCodConta').value = '';";
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "alertaAviso('@Conta inválida!', 'form','erro','".Sessao::getId()."');";
            break;
        }
        $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica();
        $stFiltro.= " AND pa.exercicio = '".Sessao::getExercicio()."' ";
        $stFiltro.= " AND pa.cod_plano = ".$_REQUEST['inCodConta']." ";
        $stFiltro.= " AND pc.cod_estrutural LIKE '2.%' ";
        $stFiltro.= " AND NOT EXISTS ( SELECT 	1
                                                FROM 	tcmgo.balanco_pfdaaaa
                                               WHERE    balanco_pfdaaaa.cod_plano = pa.cod_plano
                                                 AND    balanco_pfdaaaa.exercicio = pa.exercicio
                                            ) ";
        $obTContabilidadePlanoAnalitica->recuperaContaAnalitica( $rsConta, $stFiltro );
        if ( $rsConta->getNumLinhas() > 0 ) {
            $stJs = "document.getElementById('stConta').innerHTML = '".$rsConta->getCampo('nom_conta')."'; ";
        } else {
            $stJs.= "alertaAviso('@Código estrutural inválido!', 'form','erro','".Sessao::getId()."');";
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inCodConta').value = '';";
        }
        break;
    case 'incluirConta' :
        if ($_REQUEST['inCodConta'] == '') {
            $stMensagem = 'Conta inválida!';
        }

        if ($_REQUEST['inTipoLancamento'] == '') {
            $stMensagem = 'Preencha o tipo do lançamento!';
        }

        if ($_REQUEST['inTipoLancamento'] == 1 AND $_REQUEST['inCodDesdobramento'] == '') {
            $stMensagem = 'Preencha o tipo o desdobramento!';
        }
        $_REQUEST['inCodDesdobramento'] = ( $_REQUEST['inCodDesdobramento'] ) ? $_REQUEST['inCodDesdobramento'] : 0;
        if ( count( $arContas ) > 0 ) {
            foreach ($arContas as $arAux) {
                foreach ($arAux as $arContas) {
                    if ($arContas['cod_plano'] == $_REQUEST['inCodConta']) {
                        $stMensagem = 'Esta conta já consta na lista!';
                        break;
                    }
                }
            }
        }

        if (!$stMensagem) {
            //echo $_REQUEST['inCodDesdobramento'].'teste';
            $inCount = count($arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']]);
            //echo $inCount;
            $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica();
            $stFiltro.= " AND pa.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro.= " AND pa.cod_plano = ".$_REQUEST['inCodConta']." ";
            $obTContabilidadePlanoAnalitica->recuperaContaAnalitica( $rsConta, $stFiltro );
            $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['id'] = $inCount;
            $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['cod_estrutural'] = $rsConta->getCampo('cod_estrutural');
            $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['cod_plano'] = $rsConta->getCampo('cod_plano');
            $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['nom_conta'] = $rsConta->getCampo('nom_conta');
            $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['tipo_lancamento'] = $_REQUEST['inTipoLancamento'];
            $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['desdobramento_tipo'] = $_REQUEST['inCodDesdobramento'];
            Sessao::write('arContas', $arContas);
            $stJs.= montaLista( $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']] );
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inCodConta').value = '';";

            if (count($arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']]) > 0) {
                foreach ($arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']] as $arAux) {
                    if ( $arAux['cod_plano'] != $rsConta->getCampo('cod_plano') ) {
                        $arTemp[] = $arAux;
                    }
                }
                $arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']] = $arTemp;
                Sessao::write('arExcluidas', $arExcluidas);
            }
        } else {
            $stJs .= "alertaAviso('@".$stMensagem."!', 'form','erro','".Sessao::getId()."');";
        }
        break;
    case 'excluirListaItens' :
        $i=0;
        foreach ($arContas['arContas_'.$_REQUEST['tipo_lancamento'].'_'.$_REQUEST['desdobramento_tipo']] as $arAux) {
            if ($arAux['id'] != $_REQUEST['id']) {
                $arTemp[$i] = $arAux;
                $arTemp[$i]['id'] = $i;
                $i++;
            } else {
                $arExcluidas['arExcluidas_'.$_REQUEST['tipo_lancamento'].'_'.$_REQUEST['desdobramento_tipo']][count($arExcluidas['arExcluidas_'.$_REQUEST['tipo_lancamento'].'_'.$_REQUEST['desdobramento_tipo']])] = $arAux;
                Sessao::write('arExcluidas', $arExcluidas);
            }
        }
        $arContas['arContas_'.$_REQUEST['tipo_lancamento'].'_'.$_REQUEST['desdobramento_tipo']] = $arTemp;
        Sessao::write('arContas', $arContas);
        $stJs = montaLista( $arContas['arContas_'.$_REQUEST['tipo_lancamento'].'_'.$_REQUEST['desdobramento_tipo']] );
        break;
    case 'preencheLista' :

        if ($_REQUEST['inTipoLancamento'] != '') {
            if ( ($_REQUEST['inTipoLancamento'] == 1 AND $_REQUEST['inCodDesdobramento'] != '') OR ( $_REQUEST['inTipoLancamento'] > 1 ) ) {
                $obTTGOBalancoPfdaaaa = new TTGOBalancoPfdaaaa();
                $obTTGOBalancoPfdaaaa->setDado( 'exercicio', Sessao::getExercicio() );
                $obTTGOBalancoPfdaaaa->setDado( 'tipo_lancamento', $_REQUEST['inTipoLancamento'] );
                if ($_REQUEST['inCodDesdobramento'] == '') {
                    $_REQUEST['inCodDesdobramento'] = 0;
                }
                $obTTGOBalancoPfdaaaa->setDado( 'desdobramento_tipo', $_REQUEST['inCodDesdobramento'] );
                $obTTGOBalancoPfdaaaa->recuperaRelacionamento( $rsContas );

                while ( !$rsContas->eof() ) {
                     $boExiste = false;
                    if ( count($arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento'] ]) > 0) {
                        foreach ($arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento'] ] as $arAux) {
                            if ( $arAux['cod_plano'] == $rsContas->getCampo('cod_plano') ) {
                                $boExiste = true;

                            }
                        }
                    }
                    if ($arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']]) {
                        foreach ($arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']] as $arAux2) {
                            if ( $arAux2['cod_plano'] == $rsContas->getCampo('cod_plano') ) {
                                $boExiste = true;
                            }
                        }
                    }
                    if (!$boExiste) {
                        $inCount = count($arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']]);
                        $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['id'] = $inCount;
                        $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['cod_estrutural'] = $rsContas->getCampo('cod_estrutural');
                        $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['cod_plano'] = $rsContas->getCampo('cod_plano');
                        $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['nom_conta'] = $rsContas->getCampo('nom_conta');
                        $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['tipo_lancamento'] = $_REQUEST['inTipoLancamento'];
                        $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']][$inCount]['desdobramento_tipo'] = $_REQUEST['inCodDesdobramento'];
                    }
                    $rsContas->proximo();
                }
            }
        }
        Sessao::write('arContas', $arContas);
        $stJs.= montaLista( $arContas['arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inCodDesdobramento']] );
        break;
}
echo $stJs;
