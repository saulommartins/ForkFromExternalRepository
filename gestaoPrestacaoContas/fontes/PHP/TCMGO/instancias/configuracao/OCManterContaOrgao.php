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

 * $Id: OCManterContaOrgao.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso : uc-06.04.00

 $Id: OCManterContaOrgao.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once TTGO.'TTGOOrgaoPlanoBanco.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterContaOrgao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $request->get('stCtrl');

function montaLista($arConta)
{
    $rsConta = new RecordSet();

    if (is_array($arConta)) {
        $rsConta->preenche( $arConta );
    } else {
       $rsConta->preenche( array() );
    }

    $obTable = new Table();
    $obTable->setRecordSet( $rsConta );
    $obTable->setSummary('Lista de Contas');

    $obTable->Head->addCabecalho( 'Banco' , 25);
    $obTable->Head->addCabecalho( 'Agência' , 20 );
    $obTable->Head->addCabecalho( 'Conta Corrente' , 20 );
    $obTable->Head->addCabecalho( 'Plano de Contas' , 10 );

    $obTable->Body->addCampo( '[num_banco] - [nom_banco]', 'E' );
    $obTable->Body->addCampo( '[num_agencia] - [nom_agencia]', 'E' );
    $obTable->Body->addCampo( 'conta_corrente', 'E' );
    $obTable->Body->addCampo( 'cod_plano', 'E' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s)', array( 'id' ) );

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

    case 'incluirConta' :
        $stMensagem = $stJs = "";
        if ($_REQUEST['inOrgao'] == '') {
            $stMensagem = "Preencha o órgão";
        } elseif ($_REQUEST['inCodBanco'] == '') {
            $stMensagem = "Preencha o banco";
        } elseif ($_REQUEST['inCodAgencia'] == '') {
            $stMensagem = "Preencha o agência";
        } elseif ($_REQUEST['inCodConta'] == '') {
            $stMensagem = "Preencha a conta";
        }

        if ( count($arContas) > 0 ) {
            foreach ($arContas as $arAux) {
                if ($arAux['cod_banco'] == $_REQUEST['inCodBanco'] AND $arAux['cod_agencia'] == $_REQUEST['inCodAgencia'] AND $arAux['cod_plano'] == $_REQUEST['inCodConta']) {
                    $stMensagem = "Esta conta já consta na lista";
                    break;
                }
            }
        }

        if (!$stMensagem) {
            $inCount = count($arContas);
            $arContas[$inCount]['id'] = $inCount;
            $arContas[$inCount]['num_orgao'] = $_REQUEST['inOrgao'];
            $arContas[$inCount]['cod_banco'] = $_REQUEST['inCodBanco'];
            $arContas[$inCount]['num_banco'] = sistemaLegado::pegaDado( 'num_banco','monetario.banco',' where cod_banco = '.$_REQUEST['inCodBanco'].' ');
            $arContas[$inCount]['nom_banco'] = sistemaLegado::pegaDado( 'nom_banco','monetario.banco',' where cod_banco = '.$_REQUEST['inCodBanco'].' ');
            $arContas[$inCount]['cod_agencia'] = $_REQUEST['inCodAgencia'];
            $arContas[$inCount]['num_agencia'] = sistemaLegado::pegaDado( 'num_agencia','monetario.agencia',' where cod_agencia = '.$_REQUEST['inCodAgencia'].' AND cod_banco = '.$_REQUEST['inCodBanco'].' ');
            $arContas[$inCount]['nom_agencia'] = sistemaLegado::pegaDado( 'nom_agencia','monetario.agencia',' where cod_agencia = '.$_REQUEST['inCodAgencia'].' AND cod_banco = '.$_REQUEST['inCodBanco'].' ');
            $arContas[$inCount]['conta_corrente'] = sistemaLegado::pegaDado( 'conta_corrente','contabilidade.plano_banco',' where cod_agencia = '.$_REQUEST['inCodAgencia'].' AND cod_banco = '.$_REQUEST['inCodBanco'].' AND cod_plano = '.$_REQUEST['inCodConta'].' ');
            $arContas[$inCount]['cod_plano'] = $_REQUEST['inCodConta'];

            $arTemp = array();

            if ( count($arExcluidas)>0 ) {
                foreach ($arExcluidas as $arAux) {
                    if ($arAux['id'] != $_REQUEST['inCodConta']) {
                        $arTemp[] = $arAux;
                    }
                }
            }
            Sessao::write('arExcluidas', $arTemp);
            Sessao::write('arContas', $arContas);

            $stJs .= montaLista( $arContas );
            $stJs .= "limpaCombos();";
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

            } else {
                $inCount = count($arExcluidas);
                $arExcluidas[$inCount]['id'] = $arAux['cod_plano'];
                Sessao::write('arExcluidas', $arExcluidas);
            }
        }
        Sessao::write('arContas', $arTemp);
        $stJs = montaLista( $arTemp );
        $stJs.= "limpaCombos();";
        break;

    case 'preencheLista' :
        $stJs = "";
        if ($_REQUEST['inOrgao']) {
            $obTTGOOrgaoPlanoBanco = new TTGOOrgaoPlanoBanco();
            $obTTGOOrgaoPlanoBanco->setDado('exercicio', Sessao::getExercicio());
            $obTTGOOrgaoPlanoBanco->setDado('num_orgao',$_REQUEST['inOrgao']);
            $obTTGOOrgaoPlanoBanco->recuperaOrgaoPlanoBanco( $rsContas );
            $arContas = array();
            $stJs.= "limpaCombos();";
            while ( !$rsContas->eof() ) {
                $inCount = count($arContas);
                $arContas[$inCount]['id'] = $inCount;
                $arContas[$inCount]['num_orgao'] = $rsContas->getCampo('num_orgao');
                $arContas[$inCount]['cod_banco'] = $rsContas->getCampo('cod_banco');
                $arContas[$inCount]['num_banco'] = $rsContas->getCampo('num_banco');
                $arContas[$inCount]['nom_banco'] = $rsContas->getCampo('nom_banco');
                $arContas[$inCount]['cod_agencia'] = $rsContas->getCampo('cod_agencia');
                $arContas[$inCount]['num_agencia'] = $rsContas->getCampo('num_agencia');
                $arContas[$inCount]['nom_agencia'] = $rsContas->getCampo('nom_agencia');
                $arContas[$inCount]['conta_corrente'] = $rsContas->getCampo('conta_corrente');
                $arContas[$inCount]['cod_plano'] = $rsContas->getCampo('cod_plano');
                $rsContas->proximo();
            }
            Sessao::write('arContas', $arContas);
            Sessao::write('inOrgao', $_REQUEST['inOrgao']);
            $stJs .= montaLista( $arContas );
        } else {
            $stJs .= "document.getElementById('spnContas').innerHTML = '';";
            $stJs .= "limpaCombos();";
        }
        break;

    case 'preencheAgencia' :
        $stJs = "limpaSelect(document.getElementById('inCodAgencia'),0);";
        $stJs.= "document.getElementById('inCodAgencia').options[0] = new Option('Selecione','','selected');";
        $stJs.= "limpaSelect(document.getElementById('inCodConta'),0);";
        $stJs.= "document.getElementById('inCodConta').options[0] = new Option('Selecione','','selected');";
        if ($_REQUEST['inCodBanco'] != '') {
            $obTTGOOrgaoPlanoBanco = new TTGOOrgaoPlanoBanco();
            $obTTGOOrgaoPlanoBanco->setDado('cod_banco',$_REQUEST['inCodBanco']);
            $obTTGOOrgaoPlanoBanco->recuperaAgencia( $rsAgencia );
            $i = 0;
            while ( !$rsAgencia->eof() ) {
                $stJs.= "document.getElementById('inCodAgencia').options[".++$i."] = new Option('".$rsAgencia->getCampo('num_agencia')." ".$rsAgencia->getCampo('nom_agencia')."','".$rsAgencia->getCampo('cod_agencia')."','');";
                $rsAgencia->proximo();
            }
        }
        break;

    case 'preencheConta' :
        $stJs = "document.getElementById('inCodConta').options[0] = new Option('Selecione','','selected');";
        if ($_REQUEST['inCodAgencia'] != '' AND $_REQUEST['inCodBanco']) {
            $obTTGOOrgaoPlanoBanco = new TTGOOrgaoPlanoBanco();
            $obTTGOOrgaoPlanoBanco->setDado('cod_banco',$_REQUEST['inCodBanco']);
            $obTTGOOrgaoPlanoBanco->setDado('cod_agencia',$_REQUEST['inCodAgencia']);
            $obTTGOOrgaoPlanoBanco->setDado('exercicio', Sessao::getExercicio() );
            if ( count( $arExcluidas ) > 0 ) {
                foreach ($arExcluidas as $arAux) {
                    $arExcluidas[] = $arAux['id'];
                }
            }
            if ( count($arExcluidas) > 0 ) {
                $obTTGOOrgaoPlanoBanco->setDado('conta_excluida', implode(',',$arExcluidas) );
            }
            $obTTGOOrgaoPlanoBanco->recuperaContaCorrente( $rsConta );
            $i = 0;
            while ( !$rsConta->eof() ) {
                $stJs.= "document.getElementById('inCodConta').options[".++$i."] = new Option('".$rsConta->getCampo('conta_corrente')." - ".$rsConta->getCampo('descricao_tipo_conta')." - Plano de Contas: ".$rsConta->getCampo('cod_plano')."','".$rsConta->getCampo('cod_plano')."','');";
                $rsConta->proximo();
            }
        }
        break;
}

echo $stJs;
