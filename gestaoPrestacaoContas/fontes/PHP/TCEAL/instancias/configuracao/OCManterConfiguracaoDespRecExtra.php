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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 08/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once( CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALConfiguracaoBalanceteDespRecExtra.class.php' );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoDespRecExtra";
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

    $obTable->Head->addCabecalho( 'Classificação' , 17 );
    $obTable->Head->addCabecalho( 'Código' , 8 );
    $obTable->Head->addCabecalho( 'Código Estrutural' , 15 );
    $obTable->Head->addCabecalho( 'Descrição' , 40 );

    $obTable->Body->addCampo( 'classificacao', 'E' );
    $obTable->Body->addCampo( 'cod_plano', 'E' );
    $obTable->Body->addCampo( 'cod_estrutural', 'E' );
    $obTable->Body->addCampo( 'nom_conta', 'E' );

    $obTable->Body->addAcao( 'alterar' ,  'montaAlteracaoItens(%s,%s,%s,%s)', array( 'id','classificacao','cod_plano','nom_conta' ));
    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s,%s,%s,%s)', array( 'id','classificacao','cod_plano','nom_conta' ));

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "document.getElementById('spnContas').innerHTML = '".$stHTML."';";

    return $stJs;
}

$arContas = Sessao::read('arContas');
$arClassificacao = Sessao::read('arClassificacao');

switch ($stCtrl) {
    case 'montaLista':
        $arContas = array();
        $rs = new RecordSet;
        $obTTCEALConfiguracaoBalanceteDespRecExtra = new TTCEALConfiguracaoBalanceteDespRecExtra();
        $obTTCEALConfiguracaoBalanceteDespRecExtra->recuperaTodos($rs);

        $inCount=0;
        foreach ($rs->arElementos as $elemento) {
            $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica();
            $stFiltro = "";
            $stFiltro.= " AND pa.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro.= " AND pa.cod_plano = ".$elemento['cod_plano']." ";
            $obTContabilidadePlanoAnalitica->recuperaContaAnalitica( $rsConta, $stFiltro );

            $arElemento                   = array();
            $arElemento['id']             = $inCount;
            $arElemento['cod_estrutural'] = $rsConta->getCampo('cod_estrutural');
            $arElemento['cod_plano']      = $rsConta->getCampo('cod_plano');
            $arElemento['nom_conta']      = $rsConta->getCampo('nom_conta');
            $arElemento['classificacao']  = $arClassificacao[$elemento['classificacao']];

            $arContas[] = $arElemento;
            $inCount++;
        }

        Sessao::write('arContas', $arContas);

        $stJs.= montaLista( $arContas );
    break;

    case 'limpaConta':
        $stJs.= "document.getElementById('inClassificacao').value = '';";
        $stJs.= "document.getElementById('inCodConta').value = '';";
        $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
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
        $stFiltro.= " AND (pc.cod_estrutural LIKE '1.%' OR pc.cod_estrutural LIKE '2.%' OR pc.cod_estrutural LIKE '5.%' OR pc.cod_estrutural LIKE '6.%' ) ";
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

        if ($_REQUEST['inClassificacao'] == '') {
            $stMensagem = 'Preencha a Classificação!';
        }

        if ( count( $arContas ) > 0 ) {
            foreach ($arContas as $arAux) {
                if ($arAux['cod_plano'] == $_REQUEST['inCodConta']) {

                    $stJs.= "document.getElementById('inCodConta').value = '';";
                    $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";

                    $stMensagem = 'Esta conta já consta na lista!';
                    break;
                }
            }
        }

        $arContas = array();
        $arContas = Sessao::read('arContas');

        if (!$stMensagem) {
            $inCount = count($arContas);

            $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica();
            $stFiltro.= " AND pa.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro.= " AND pa.cod_plano = ".$_REQUEST['inCodConta']." ";
            $obTContabilidadePlanoAnalitica->recuperaContaAnalitica( $rsConta, $stFiltro );

            $arElemento = array();
            $arElemento['id'] = $inCount;
            $arElemento['cod_estrutural'] = $rsConta->getCampo('cod_estrutural');
            $arElemento['cod_plano'] = $rsConta->getCampo('cod_plano');
            $arElemento['nom_conta'] = $rsConta->getCampo('nom_conta');
            $arElemento['classificacao'] = $arClassificacao[$_REQUEST['inClassificacao']];

            $arContas[] = $arElemento;

            Sessao::write('arContas', $arContas);

            $stJs.= montaLista( $arContas );
            $stJs.= "document.getElementById('inClassificacao').value = '';";
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inCodConta').value = '';";

        } else {
            $stJs .= "alertaAviso('@".$stMensagem."!', 'form','erro','".Sessao::getId()."');";
        }
    break;

    case 'montaAlteracaoItens':
        $stJs.= "document.getElementById('inClassificacao').value = '".substr($_REQUEST['classificacao'], 0, 2)."';";
        $stJs.= "document.getElementById('stConta').innerHTML = '".$_REQUEST['nom_conta']."';";
        $stJs.= "document.getElementById('inCodConta').value = '".$_REQUEST['cod_plano']."';";
        $stJs.= "document.getElementById('inId').value = '".$_REQUEST['id']."';";

        $stJs.= "document.getElementById('btnAlterar').style.display = 'inline';";
        $stJs.= "document.getElementById('btnIncluir').style.display = 'none';";
    break;

    case 'alterarConta' :
        if ($_REQUEST['inCodConta'] == '') {
            $stMensagem = 'Conta inválida!';
        }

        if ($_REQUEST['inClassificacao'] == '') {
            $stMensagem = 'Preencha a Classificação!';
        }

        if ( count( $arContas ) > 0 ) {
            foreach ($arContas as $arAux) {
                if (($arAux['cod_plano'] == $_REQUEST['inCodConta']) && ($arAux['id'] != $_REQUEST['inId'])) {

                    $stJs.= "document.getElementById('inCodConta').value = '';";
                    $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";

                    $stMensagem = 'Esta conta já consta na lista!';
                    break;
                }
            }
        }

        $arContas = array();
        $arContas = Sessao::read('arContas');
        $arClassificacao = Sessao::read('arClassificacao');

        if (!$stMensagem) {
            $inCount = count($arContas);

            $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica();
            $stFiltro.= " AND pa.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro.= " AND pa.cod_plano = ".$_REQUEST['inCodConta']." ";
            $obTContabilidadePlanoAnalitica->recuperaContaAnalitica( $rsConta, $stFiltro );

            for ($j=0; $j < count($arContas); $j++) {
                if ($arContas[$j]['id'] == $_REQUEST['inId']) {
                    $arContas[$j]['cod_estrutural'] = $rsConta->getCampo('cod_estrutural');
                    $arContas[$j]['cod_plano']      = $rsConta->getCampo('cod_plano');
                    $arContas[$j]['nom_conta']      = $rsConta->getCampo('nom_conta');
                    $arContas[$j]['classificacao']  = $arClassificacao[$_REQUEST['inClassificacao']];
                }
            }

            Sessao::write('arContas', $arContas);

            $stJs.= montaLista( $arContas );
            $stJs.= "document.getElementById('inClassificacao').value = '';";
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inCodConta').value = '';";
            $stJs.= "document.getElementById('btnIncluir').style.display = 'inline';";
            $stJs.= "document.getElementById('btnAlterar').style.display = 'none';";

        } else {
            $stJs .= "alertaAviso('@".$stMensagem."!', 'form','erro','".Sessao::getId()."');";
        }
    break;

    case 'excluirListaItens' :
        $i=0;
        $arExcluidas = Sessao::read('arExcluidas');
        $arTemp = array();

        foreach ($arContas as $arAux) {
            if ($arAux['id'] != $_REQUEST['id']) {
                $arTemp[$i] = $arAux;
                $arTemp[$i]['id'] = $i;
                $i++;
            } else {
                $arExcluidas[] = $arAux;
                Sessao::write('arExcluidas', $arExcluidas);
            }
        }

        $arContas = $arTemp;

        $stJs.= "document.getElementById('inClassificacao').value = '';";
        $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
        $stJs.= "document.getElementById('inCodConta').value = '';";
        $stJs.= "document.getElementById('btnIncluir').style.display = 'inline';";
        $stJs.= "document.getElementById('btnAlterar').style.display = 'none';";

        Sessao::write('arContas', $arContas);
        $stJs.= montaLista($arContas);
    break;

    case 'preencheLista' :
        $stJs.= "document.getElementById('spnContas').innerHTML = '';";
        if ($_REQUEST['inClassificacao'] != '' AND $_REQUEST['inCategoria'] != '') {
            if ( ($_REQUEST['inClassificacao'] == 1 OR $_REQUEST['inClassificacao'] == 4) AND ($_REQUEST['inSubTipo'] != '') ) {
                $obTTCEALConfiguracaoBalanceteDespRecExtra = new TTCEALConfiguracaoBalanceteDespRecExtra();
                $obTTCEALConfiguracaoBalanceteDespRecExtra->setDado( 'exercicio', Sessao::getExercicio() );
                $obTTCEALConfiguracaoBalanceteDespRecExtra->setDado( 'tipo_lancamento', $_REQUEST['inClassificacao'] );
                $obTTCEALConfiguracaoBalanceteDespRecExtra->setDado( 'categoria', $_REQUEST['inCategoria'] );
                $obTTCEALConfiguracaoBalanceteDespRecExtra->setDado( 'sub_tipo_lancamento', $_REQUEST['inSubTipo'] );
                $obTTCEALConfiguracaoBalanceteDespRecExtra->recuperaRelacionamento( $rsContas );

                while ( !$rsContas->eof() ) {
                    $boExiste = false;
                    if ( count($arContas[ 'arContas_'.'_'.$_REQUEST['inClassificacao'] ]) > 0) {
                        foreach ($arContas[ 'arContas_'.'_'.$_REQUEST['inClassificacao'] ] as $arAux) {
                            if ( $arAux['cod_plano'] == $rsContas->getCampo('cod_plano') ) {
                                $boExiste = true;

                            }
                        }
                    }

                    if ($arExcluidas['arExcluidas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inClassificacao'].'_'.$_REQUEST['inSubTipo'] ]) {
                        foreach ($arExcluidas['arExcluidas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inClassificacao'].'_'.$_REQUEST['inSubTipo']] as $arAux2) {
                            if ( $arAux2['cod_plano'] == $rsContas->getCampo('cod_plano') ) {
                                $boExiste = true;
                            }
                        }
                    }

                    if (!$boExiste) {
                        $inCount = count($arContas['arContas_'.'_'.$_REQUEST['inClassificacao']]);
                        $arContas['arContas_'.'_'.$_REQUEST['inClassificacao']][$inCount]['id'] = $inCount;
                        $arContas['arContas_'.'_'.$_REQUEST['inClassificacao']][$inCount]['cod_estrutural'] = $rsContas->getCampo('cod_estrutural');
                        $arContas['arContas_'.'_'.$_REQUEST['inClassificacao']][$inCount]['cod_plano'] = $rsContas->getCampo('cod_plano');
                        $arContas['arContas_'.'_'.$_REQUEST['inClassificacao']][$inCount]['nom_conta'] = $rsContas->getCampo('nom_conta');
                        $arContas['arContas_'.'_'.$_REQUEST['inClassificacao']][$inCount]['tipo_lancamento'] = $_REQUEST['inClassificacao'];
                    }
                    $rsContas->proximo();
                }
            }
        }
        Sessao::write('arContas', $arContas);
        $stJs.= montaLista( $arContas['arContas_'.'_'.$_REQUEST['inClassificacao']] );
        break;
}
echo $stJs;
?>
