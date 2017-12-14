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

    $Id: OCManterExtmmaa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(TTGO.'TTGOBalanceteExtmmaa.class.php');
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterExtmmaa";
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

    $obTable->Head->addCabecalho( 'Código' , 10 );
    $obTable->Head->addCabecalho( 'Código Estrutural' , 20 );
    $obTable->Head->addCabecalho( 'Descrição' , 50 );

    $obTable->Body->addCampo( 'cod_plano', 'E' );
    $obTable->Body->addCampo( 'cod_estrutural', 'E' );
    $obTable->Body->addCampo( 'nom_conta', 'E' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s,%s,%s,%s)', array( 'id','categoria','tipo_lancamento','sub_tipo_lancamento' ) );

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "document.getElementById('spnContas').innerHTML = '".$stHTML."';";

    return $stJs;
}

$arContas    = Sessao::read('arContas');
$arExcluidas = Sessao::read('arExcluidas');

switch ($stCtrl) {
    case 'limpaConta':
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

        if ($_REQUEST['inTipoLancamento'] == '') {
            $stMensagem = 'Preencha o tipo do lançamento!';
        }
        if ( ($_REQUEST['inTipoLancamento'] == 1 OR $_REQUEST['inTipoLancamento'] == 4) AND $_REQUEST['inSubTipo'] == '' ) {
            $stMensagem = 'Preencha o subtipo do lançamento!';
        }

        if ( count( $arContas ) > 0 ) {
            foreach ($arContas as $arAux) {
                foreach ($arAux as $arContas) {
                    if ($arContas['cod_plano'] == $_REQUEST['inCodConta']) {

                        $stJs.= "document.getElementById('inCodConta').value = '';";
                        $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";

                        $stMensagem = 'Esta conta já consta na lista!';
                        break;
                    }
                }
            }
        }

        $arContas = array();
        $arContas = Sessao::read('arContas');

        if (!$stMensagem) {
            $_REQUEST['inSubTipo'] = ( $_REQUEST['inSubTipo'] == '' ) ? 0 : $_REQUEST['inSubTipo'];
            $inCount = count($arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo']]);

            $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica();
            $stFiltro.= " AND pa.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro.= " AND pa.cod_plano = ".$_REQUEST['inCodConta']." ";
            $obTContabilidadePlanoAnalitica->recuperaContaAnalitica( $rsConta, $stFiltro );

            $arElementos = array();

            $arElementos['id'] = $inCount;
            $arElementos['cod_estrutural'] = $rsConta->getCampo('cod_estrutural');
            $arElementos['cod_plano'] = $rsConta->getCampo('cod_plano');
            $arElementos['nom_conta'] = $rsConta->getCampo('nom_conta');
            $arElementos['tipo_lancamento'] = $_REQUEST['inTipoLancamento'];
            $arElementos['categoria'] = $_REQUEST['inCategoria'];
            $arElementos['sub_tipo_lancamento'] = $_REQUEST['inSubTipo'];

            $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo']][] = $arElementos;

            Sessao::write('arContas', $arContas);

            $stJs.= montaLista( $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo']] );
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inCodConta').value = '';";
        } else {
            $stJs .= "alertaAviso('@".$stMensagem."!', 'form','erro','".Sessao::getId()."');";
        }
        break;
    case 'excluirListaItens' :
        $i=0;
        $arExcluidas = Sessao::read('arExcluidas');
        $arTemp = array();
        foreach ($arContas['arContas_'.$_REQUEST['categoria'].'_'.$_REQUEST['tipo_lancamento'].'_'.$_REQUEST['sub_tipo_lancamento']] as $arAux) {
            if ($arAux['id'] != $_REQUEST['id']) {
                $arTemp[$i] = $arAux;
                $arTemp[$i]['id'] = $i;
                $i++;
            } else {
                $arExcluidas[] = $arAux;
                Sessao::write('arExcluidas', $arExcluidas);
            }
        }
        $arContas['arContas_'.$_REQUEST['categoria'].'_'.$_REQUEST['tipo_lancamento'].'_'.$_REQUEST['sub_tipo_lancamento']] = $arTemp;

        Sessao::write('arContas', $arContas);
        $stJs = montaLista( $arContas['arContas_'.$_REQUEST['categoria'].'_'.$_REQUEST['tipo_lancamento'].'_'.$_REQUEST['sub_tipo_lancamento']] );
        break;
    case 'preencheLista' :
        $stJs.= "document.getElementById('spnContas').innerHTML = '';";
        if ($_REQUEST['inTipoLancamento'] != '' AND $_REQUEST['inCategoria'] != '') {
            if ( ($_REQUEST['inTipoLancamento'] == 1 OR $_REQUEST['inTipoLancamento'] == 4) AND ($_REQUEST['inSubTipo'] != '') ) {
                $obTTGOBalanceteExtmmaa = new TTGOBalanceteExtmmaa();
                $obTTGOBalanceteExtmmaa->setDado( 'exercicio', Sessao::getExercicio() );
                $obTTGOBalanceteExtmmaa->setDado( 'tipo_lancamento', $_REQUEST['inTipoLancamento'] );
                $obTTGOBalanceteExtmmaa->setDado( 'categoria', $_REQUEST['inCategoria'] );
                $obTTGOBalanceteExtmmaa->setDado( 'sub_tipo_lancamento', $_REQUEST['inSubTipo'] );
                $obTTGOBalanceteExtmmaa->recuperaRelacionamento( $rsContas );

                while ( !$rsContas->eof() ) {
                    $boExiste = false;
                    if ( count($arContas[ 'arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo'] ]) > 0) {
                        foreach ($arContas[ 'arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo'] ] as $arAux) {
                            if ( $arAux['cod_plano'] == $rsContas->getCampo('cod_plano') ) {
                                $boExiste = true;

                            }
                        }
                    }
                    if ($arExcluidas['arExcluidas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo'] ]) {
                        foreach ($arExcluidas['arExcluidas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo']] as $arAux2) {
                            if ( $arAux2['cod_plano'] == $rsContas->getCampo('cod_plano') ) {
                                $boExiste = true;
                            }
                        }
                    }
                    if (!$boExiste) {
                        $inCount = count($arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo']]);
                        $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo']][$inCount]['id'] = $inCount;
                        $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo']][$inCount]['cod_estrutural'] = $rsContas->getCampo('cod_estrutural');
                        $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo']][$inCount]['cod_plano'] = $rsContas->getCampo('cod_plano');
                        $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo']][$inCount]['nom_conta'] = $rsContas->getCampo('nom_conta');
                        $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo']][$inCount]['categoria'] = $_REQUEST['inCategoria'];
                        $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo']][$inCount]['tipo_lancamento'] = $_REQUEST['inTipoLancamento'];
                        $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo']][$inCount]['sub_tipo_lancamento'] = $_REQUEST['inSubTipo'];
                    }
                    $rsContas->proximo();
                }
            }
        }
        Sessao::write('arContas', $arContas);
        $stJs.= montaLista( $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inSubTipo']] );
        break;
    case 'preencheSubTipo' :
        $stJs.= "document.getElementById('spnSubTipo').innerHTML = '';";
        if ($_REQUEST['inTipoLancamento'] != '') {
            switch ($_REQUEST['inTipoLancamento']) {
                case 1:
                    $obCmbSubTipo = new Select();
                    $obCmbSubTipo->setId('inSubTipo');
                    $obCmbSubTipo->setName('inSubTipo');
                    $obCmbSubTipo->setRotulo( 'Subtipo' );
                    $obCmbSubTipo->addOption('','Selecione');
                    $obCmbSubTipo->addOption('1','INSS');
                    $obCmbSubTipo->addOption('2','RPPS');
                    $obCmbSubTipo->addOption('3','IRRF');
                    $obCmbSubTipo->addOption('4','ISSQN');
                    $obCmbSubTipo->addOption('999','Outro');
                    $obCmbSubTipo->setNull( false );
                    $obCmbSubTipo->obEvento->setOnChange("montaParametrosGET('preencheLista','inTipoLancamento,inCategoria,inSubTipo','true');");
                    $obFormulario = new Formulario();
                    $obFormulario->addComponente( $obCmbSubTipo );
                    $obFormulario->montaInnerHTML();
                    $stJs.= "document.getElementById('spnSubTipo').innerHTML = '".$obFormulario->getHTML()."';";
                    break;
                case 4:
                    $obCmbSubTipo = new Select();
                    $obCmbSubTipo->setId('inSubTipo');
                    $obCmbSubTipo->setName('inSubTipo');
                    $obCmbSubTipo->setRotulo( 'Subtipo' );
                    $obCmbSubTipo->addOption('','Selecione');
                    $obCmbSubTipo->addOption('1','Duodécimo Câmara Municipal');
                    $obCmbSubTipo->addOption('2','FUNDEF');
                    $obCmbSubTipo->addOption('3','F.M.S.');
                    $obCmbSubTipo->addOption('4','REPASSE FINANCEIRO AO IPAM');
                    $obCmbSubTipo->addOption('999','Outro');
                    $obCmbSubTipo->setNull( false );
                    $obCmbSubTipo->obEvento->setOnChange("montaParametrosGET('preencheLista','inTipoLancamento,inCategoria,inSubTipo','true');");
                    $obFormulario = new Formulario();
                    $obFormulario->addComponente( $obCmbSubTipo );
                    $obFormulario->montaInnerHTML();
                    $stJs.= "document.getElementById('spnSubTipo').innerHTML = '".$obFormulario->getHTML()."';";
                    break;
                default :
                $sesssao->transf3['arContas'] = array();
                    $obTTGOBalanceteExtmmaa = new TTGOBalanceteExtmmaa();
                    $obTTGOBalanceteExtmmaa->setDado( 'exercicio', Sessao::getExercicio() );
                    $obTTGOBalanceteExtmmaa->setDado( 'tipo_lancamento', $_REQUEST['inTipoLancamento'] );
                    $obTTGOBalanceteExtmmaa->setDado( 'categoria', $_REQUEST['inCategoria'] );
                    $obTTGOBalanceteExtmmaa->setDado( 'sub_tipo_lancamento', 0 );
                    $obTTGOBalanceteExtmmaa->recuperaRelacionamento( $rsContas );
                    while ( !$rsContas->eof() ) {
                        $boExiste = false;
                        if ( count($arContas[ 'arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_0' ]) > 0) {
                            foreach ($arContas[ 'arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_0'] as $arAux) {
                                if ( $arAux['cod_plano'] == $rsContas->getCampo('cod_plano') ) {
                                    $boExiste = true;

                                }
                            }
                        }
                        if ($arExcluidas['arExcluidas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_0']) {
                            foreach ($arExcluidas['arExcluidas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_0'] as $arAux2) {
                                if ( $arAux2['cod_plano'] == $rsContas->getCampo('cod_plano') ) {
                                    $boExiste = true;
                                }
                            }
                        }
                        if (!$boExiste) {
                            $inCount = count($arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_0']);
                            $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_0'][$inCount]['id'] = $inCount;
                            $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_0'][$inCount]['cod_estrutural'] = $rsContas->getCampo('cod_estrutural');
                            $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_0'][$inCount]['cod_plano'] = $rsContas->getCampo('cod_plano');
                            $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_0'][$inCount]['nom_conta'] = $rsContas->getCampo('nom_conta');
                            $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_0'][$inCount]['categoria'] = $_REQUEST['inCategoria'];
                            $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_0'][$inCount]['tipo_lancamento'] = $_REQUEST['inTipoLancamento'];
                            $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_0'][$inCount]['sub_tipo_lancamento'] = 0;
                        }
                        $rsContas->proximo();
                    }

                    break;
            }
        }
        Sessao::write('arContas', $arContas);
        $stJs.= montaLista( $arContas['arContas_'.$_REQUEST['inCategoria'].'_'.$_REQUEST['inTipoLancamento'].'_0'] );

        break;
}
echo $stJs;
