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

    * $Id: OCManterBlpaaaa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(TTGO.'TTGOBalancoBlpaaaa.class.php');
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );

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

    $obTable->Body->addCampo( 'cod_estrutural', 'E' );
    $obTable->Body->addCampo( 'nom_conta', 'E' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s,%s,%s)', array( 'id','tipo_lancamento','tipo_conta' ) );

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
    case 'buscaEstrutural' :
        if ( substr( $_REQUEST['inCodConta'],0,1 ) != $_REQUEST['inTipoLancamento'] ) {
            $stJs.= "document.getElementById('inCodConta').value = '';";
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "alertaAviso('@Conta inválida!', 'form','erro','".Sessao::getId()."');";
            break;
        }
        include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php");
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta();
        $obTContabilidadePlanoConta->setDado('exercicio',Sessao::getExercicio());
        $obTContabilidadePlanoConta->setDado('cod_estrutural',$_REQUEST['inCodConta']);
        $stFiltro = " AND NOT EXISTS ( SELECT 	1
                                                FROM 	tcmgo.balanco_blpaaaa
                                               WHERE    balanco_blpaaaa.cod_conta = plano_conta.cod_conta
                                                 AND    balanco_blpaaaa.exercicio = plano_conta.exercicio
                                            ) ";
        $obTContabilidadePlanoConta->recuperaContaSintetica( $rsConta, $stFiltro );
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

        if ( substr( $_REQUEST['inCodConta'],0,1 ) != $_REQUEST['inTipoLancamento'] ) {
            $stMensagem = 'Conta inválida!';
        }

        if ($_REQUEST['inTipoConta'] == '') {
            $stMensagem = 'Preencha o tipo da Conta!';
        }

        if ($_REQUEST['inTipoLancamento'] == '') {
            $stMensagem = 'Preencha o tipo do lançamento!';
        }

        if ( count( $arContas ) > 0 ) {
            foreach ($arContas as $arAux) {
                foreach ($arAux as $arContas) {
                    if ($arContas['cod_estrutural'] == $_REQUEST['inCodConta']) {
                        $stMensagem = 'Esta conta já consta na lista!';
                        break;
                    }
                }
            }
        }

        if (!$stMensagem) {
            $inCount = count($arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ]);
            $obTContabilidadePlanoConta = new TContabilidadePlanoConta();
            $obTContabilidadePlanoConta->setDado( 'exercicio', Sessao::getExercicio() );
            $obTContabilidadePlanoConta->setDado( 'cod_estrutural', $_REQUEST['inCodConta'] );
            $obTContabilidadePlanoConta->recuperaContaSintetica( $rsConta );
            $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ][$inCount]['id'] = $inCount;
            $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ][$inCount]['cod_estrutural'] = $_REQUEST['inCodConta'];
            $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ][$inCount]['cod_conta'] = $rsConta->getCampo('cod_conta');
            $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ][$inCount]['nom_conta'] = $rsConta->getCampo('nom_conta');
            $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ][$inCount]['tipo_lancamento'] = $_REQUEST['inTipoLancamento'];
            $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ][$inCount]['tipo_conta'] = $_REQUEST['inTipoConta'];

            if (count($arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta']]) > 0) {
                foreach ($arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta']] as $arAux) {
                    if ( $arAux['cod_conta'] != $rsConta->getCampo('cod_conta') ) {
                        $arTemp[] = $arAux;
                    }
                }
                $arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta']] = $arTemp;
                Sessao::write('arExcluidas',$arExcluidas);
            }

            Sessao::write('arContas', $arContas);
            $stJs.= montaLista( $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ] );
            $stJs.= "document.getElementById('stConta').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inCodConta').value = '';";
        } else {
            $stJs .= "alertaAviso('@".$stMensagem."!', 'form','erro','".Sessao::getId()."');";
        }
        break;
    case 'excluirListaItens' :
        $i=0;
        foreach ($arContas[ 'arContas_'.$_REQUEST['tipo_lancamento'].'_'.$_REQUEST['tipo_conta'] ] as $arAux) {
            if ($arAux['id'] != $_REQUEST['id']) {
                $arTemp[$i] = $arAux;
                $arTemp[$i]['id'] = $i;
                $i++;
            } else {
                $arExcluidas['arExcluidas_'.$_REQUEST['tipo_lancamento'].'_'.$_REQUEST['tipo_conta']][count($arExcluidas['arExcluidas_'.$_REQUEST['tipo_lancamento'].'_'.$_REQUEST['tipo_conta']])] = $arAux;
                Sessao::write('arExcluidas', $arExcluidas);
            }
        }
        $arContas[ 'arContas_'.$_REQUEST['tipo_lancamento'].'_'.$_REQUEST['tipo_conta'] ] = $arTemp;
        Sessao::write('arContas', $arContas);
        $stJs = montaLista( $arContas[ 'arContas_'.$_REQUEST['tipo_lancamento'].'_'.$_REQUEST['tipo_conta'] ] );
        break;
    case 'preencheLista' :
        $stJs.= "document.getElementById('spnContas').innerHTML = '';";
        if ($_REQUEST['inTipoLancamento'] != '' AND $_REQUEST['inTipoConta'] != '') {
            $obTTGOBalancoBlpaaaa = new TTGOBalancoBlpaaaa();
            $obTTGOBalancoBlpaaaa->setDado( 'exercicio', Sessao::getExercicio() );
            $obTTGOBalancoBlpaaaa->setDado( 'tipo_lancamento', $_REQUEST['inTipoLancamento'] );
            $obTTGOBalancoBlpaaaa->setDado( 'tipo_conta', $_REQUEST['inTipoConta'] );
            $obTTGOBalancoBlpaaaa->recuperaRelacionamento( $rsContas );
            while ( !$rsContas->eof() ) {
                $boExiste = false;
                if ( count($arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ]) > 0) {
                    foreach ($arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ] as $arAux) {
                        if ( $arAux['cod_conta'] == $rsContas->getCampo('cod_conta') ) {
                            $boExiste = true;

                        }
                    }
                }
                if ($arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta']]) {
                    foreach ($arExcluidas['arExcluidas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta']] as $arAux2) {
                        if ( $arAux2['cod_conta'] == $rsContas->getCampo('cod_conta') ) {
                            $boExiste = true;
                        }
                    }
                }
                if (!$boExiste) {
                    $inCount = count($arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ]);
                    $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ][$inCount]['id'] = $inCount;
                    $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ][$inCount]['cod_estrutural'] = $rsContas->getCampo('cod_estrutural');
                    $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ][$inCount]['cod_conta'] = $rsContas->getCampo('cod_conta');
                    $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ][$inCount]['nom_conta'] = $rsContas->getCampo('nom_conta');
                    $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ][$inCount]['tipo_lancamento'] = $_REQUEST['inTipoLancamento'];
                    $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ][$inCount]['tipo_conta'] = $_REQUEST['inTipoConta'];
                }
                $rsContas->proximo();
            }
        }
        Sessao::write('arContas', $arContas);
        $stJs.= montaLista( $arContas[ 'arContas_'.$_REQUEST['inTipoLancamento'].'_'.$_REQUEST['inTipoConta'] ] );
        break;
    case 'preencheTipoConta' :
        $stJs.= "limpaSelect(document.getElementById('inTipoConta'),0);";
        $stJs.= "document.getElementById('inTipoConta').options[0] = new Option('Selecione','','selected');";
        $stJs.= "document.getElementById('spnContas').innerHTML = '';";
        if ($_REQUEST['inTipoLancamento'] != '') {
            switch ($_REQUEST['inTipoLancamento']) {
                case 1 :
                    $stJs.= "document.getElementById('inTipoConta').options[1] = new Option('Disponível','1','');";
                    $stJs.= "document.getElementById('inTipoConta').options[2] = new Option('Realizável','2','');";
                    $stJs.= "document.getElementById('inTipoConta').options[3] = new Option('Bens Móveis','3','');";
                    $stJs.= "document.getElementById('inTipoConta').options[4] = new Option('Bens Imóveis','4','');";
                    $stJs.= "document.getElementById('inTipoConta').options[5] = new Option('Bens de Natureza Industrial','5','');";
                    $stJs.= "document.getElementById('inTipoConta').options[6] = new Option('Créditos (Dívida Ativa a Cobrar e Outros)','6','');";
                    $stJs.= "document.getElementById('inTipoConta').options[7] = new Option('Valores','7','');";
                    $stJs.= "document.getElementById('inTipoConta').options[8] = new Option('Diversos','8','');";
                    $stJs.= "document.getElementById('inTipoConta').options[9] = new Option('Ativo Compensado','9','');";
                    break;
                case 2:
                    $stJs.= "document.getElementById('inTipoConta').options[1] = new Option('Restos a Pagar','1','');";
                    $stJs.= "document.getElementById('inTipoConta').options[2] = new Option('Serviço da Dívida a Pagar','2','');";
                    $stJs.= "document.getElementById('inTipoConta').options[3] = new Option('Depósitos','3','');";
                    $stJs.= "document.getElementById('inTipoConta').options[4] = new Option('Débitos Tesouraria','4','');";
                    $stJs.= "document.getElementById('inTipoConta').options[5] = new Option('Diversos','5','');";
                    $stJs.= "document.getElementById('inTipoConta').options[6] = new Option('Dívida Fundada Interna','6','');";
                    $stJs.= "document.getElementById('inTipoConta').options[7] = new Option('Dívida Fundada Externa','7','');";
                    $stJs.= "document.getElementById('inTipoConta').options[8] = new Option('Diversos','8','');";
                    $stJs.= "document.getElementById('inTipoConta').options[9] = new Option('Passivo Compensado','9','');";

                    break;
            }
        }
        break;
}
echo $stJs;
