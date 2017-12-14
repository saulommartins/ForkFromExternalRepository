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
    * Data de Criação   : 16/04/2007

    * @author Henrique Boaventura

    * @ignore

    * $Id: OCManterOrgao.php 63835 2015-10-22 13:53:31Z franver $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(TTGO."TTGOOrgao.class.php");
include_once(TTGO."TTGOOrgaoGestor.class.php");
include_once(TTGO."TTGOOrgaoControleInterno.class.php");
include_once(TTGO."TTGOOrgaoRepresentante.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrgao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

function montaLista($arGestor)
{
    $rsGestor = new RecordSet();
    $rsGestor->preenche( $arGestor );

    $obTable = new Table();
    $obTable->setRecordSet( $rsGestor );
    $obTable->setSummary('Gestores do Órgão');

    //$obTable->setConditional( true , "#efefef" );

    $obTable->Head->addCabecalho( 'Gestor' , 25);
    $obTable->Head->addCabecalho( 'Cargo' , 50 );
    $obTable->Head->addCabecalho( 'Data de Início' , 10 );
    $obTable->Head->addCabecalho( 'Data de Término' , 10 );

    $obTable->Body->addCampo( '[inCGMGestor] - [stNomCGMGestor]', 'E' );
    $obTable->Body->addCampo( 'stCargoGestor', 'E' );
    $obTable->Body->addCampo( 'dtInicio', 'E' );
    $obTable->Body->addCampo( 'dtTermino', 'E' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s)', array( 'id' ) );
    $obTable->Body->addAcao( 'alterar' ,  'montaAlteracaoLista(%s)' , array( 'id' ) );

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "document.getElementById('spnGestor').innerHTML = '".$stHTML."';";
    $stJs.= "limparGestor();";

    return $stJs;
}

function retornaData($stData)
{
    return implode('',array_reverse(explode('/',$stData)));
}

$arGestor = Sessao::read('arGestor');

switch ($stCtrl) {
    case 'incluiGestor' :
                if ($_REQUEST['inCGMGestor'] == '') {
            $stMensagem = 'Gestor inválido';
        } elseif ($_REQUEST['dtInicio'] == '') {
            $stMensagem = 'Data de início inválida';
        } elseif ($_REQUEST['dtTermino'] == '') {
            $stMensagem = 'Data de término inválida';
        } elseif ( retornaData($_REQUEST['dtInicio']) >= retornaData($_REQUEST['dtTermino']) ) {
            $stMensagem  = 'A data de término deve ser superior a data de início';
        }
        if ( is_array($arGestor) ) {
            foreach ($arGestor as $arGestor) {
                if ( !((retornaData($_REQUEST['dtInicio']) >= retornaData($arGestor['dtTermino'])) OR (retornaData($_REQUEST['dtTermino']) <= retornaData($arGestor['dtInicio']))) ) {
                    $stMensagem = 'Já existe um gestor cadastrado neste período';
                }
            }
        }

        $arGestor = Sessao::read('arGestor');

        $arElementos = array();
        if (!$stMensagem) {
            $arElementos['id']             = count($arGestor);
            $arElementos['inCGMGestor']    = $_REQUEST['inCGMGestor'];
            $arElementos['stNomCGMGestor'] = $_REQUEST['stNomCGMGestor'];
            $arElementos['stCargoGestor']  = $_REQUEST['stCargoGestor'];
            $arElementos['dtInicio']       = $_REQUEST['dtInicio'];
            $arElementos['dtTermino']      = $_REQUEST['dtTermino'];
            $arGestor[] = $arElementos;

            Sessao::write('arGestor', $arGestor);
            echo montaLista( $arGestor );
        } else {
            echo "alertaAviso('".$stMensagem."!','form','erro','".Sessao::getId()."');";
        }
        break;
    case 'excluirListaItens':
        $arTemp = array();
        $inCount = 0;
        $arGestor = Sessao::read('arGestor');
        foreach ($arGestor as $arValue) {
            if ($arValue['id'] != $_REQUEST['id']) {
                $arTemp[$inCount]['id']             = $arValue['id'];
                $arTemp[$inCount]['inCGMGestor']    = $arValue['inCGMGestor'];
                $arTemp[$inCount]['stNomCGMGestor'] = $arValue['stNomCGMGestor'];
                $arTemp[$inCount]['stCargoGestor']  = $arValue['stCargoGestor'];
                $arTemp[$inCount]['dtInicio']       = $arValue['dtInicio'];
                $arTemp[$inCount]['dtTermino']      = $arValue['dtTermino'];
                $inCount++;
            }
        }

        Sessao::write('arGestor',$arTemp);
        echo montaLista( $arTemp );
        break;

    case 'montaAlteracaoLista':

        $_REQUEST['stNomCGMGestor'] = "";

        $arGestor = $arGestor[$_REQUEST['id']];
        $stJs.= "document.getElementById('hdnId').value = '".$_REQUEST['id']."';";
        $stJs.= "document.getElementById('inCGMGestor').value = '".$arGestor['inCGMGestor']."';";
        $stJs.= "document.getElementById('stNomCGMGestor').innerHTML = '".$arGestor['stNomCGMGestor']."';";
        $stJs.= "document.getElementById('stNomCGMGestor').value = '".$arGestor['stNomCGMGestor']."';";
        $stJs.= "document.getElementById('stCargoGestor').value = '".$arGestor['stCargoGestor']."';";
        $stJs.= "document.getElementById('dtInicio').value = '".$arGestor['dtInicio']."';";
        $stJs.= "document.getElementById('dtTermino').value = '".$arGestor['dtTermino']."';";
        $stJs.= "document.getElementById('btIncluir').value = 'Alterar';";
        $stJs.= "document.getElementById('btIncluir').setAttribute('onClick','montaParametrosGET( \'alterarListaItens\', \'hdnId,inCGMGestor,stNomCGMGestor,stCargoGestor,dtInicio,dtTermino\' );');";
        break;
    case 'alterarListaItens':

        if ($_REQUEST['inCGMGestor'] == '') {
            $stMensagem = 'Gestor inválido';
        } elseif ($_REQUEST['dtInicio'] == '') {
            $stMensagem = 'Data de início inválida';
        } elseif ($_REQUEST['dtTermino'] == '') {
            $stMensagem = 'Data de término inválida';
        } elseif ( retornaData($_REQUEST['dtInicio']) >= retornaData($_REQUEST['dtTermino']) ) {
            $stMensagem  = 'A data de término deve ser superior a data de início';
        }
        if ( is_array($arGestor) ) {
            foreach ($arGestor as $arGestor) {
                if ( !((retornaData($_REQUEST['dtInicio']) >= retornaData($arGestor['dtTermino'])) OR (retornaData($_REQUEST['dtTermino']) <= retornaData($arGestor['dtInicio']))) AND ( $_REQUEST['hdnId'] != $arGestor['id']) ) {
                    $stMensagem = 'Já existe um gestor cadastrado neste período';
                }
            }
        }

        $arGestor = Sessao::read('arGestor');
        $inCount = 0;

        if (!$stMensagem) {
            foreach ($arGestor as $key => $value) {
                if ($_REQUEST['hdnId'] == $value['id']) {
                    $arGestor[$inCount]['id']             = $_REQUEST['hdnId'];
                    $arGestor[$inCount]['inCGMGestor']    = $_REQUEST['inCGMGestor'];
                    $arGestor[$inCount]['stNomCGMGestor'] = $_REQUEST['stNomCGMGestor'];
                    $arGestor[$inCount]['stCargoGestor']    = $_REQUEST['stCargoGestor'];
                    $arGestor[$inCount]['dtInicio']       = $_REQUEST['dtInicio'];
                    $arGestor[$inCount]['dtTermino']      = $_REQUEST['dtTermino'];
                }
                $inCount++;
            }
            Sessao::write('arGestor',$arGestor);
            echo 'limparGestor();';
            echo montaLista( $arGestor );
        } else {
            echo "alertaAviso('".$stMensagem."!','form','erro','".Sessao::getId()."');";
        }
        break;
    case 'preencheDados' :
        $obTTGOOrgao = new TTGOOrgao();
        $obTTGOOrgao->setDado( 'num_orgao', $_REQUEST['inOrgao'] );
        $obTTGOOrgao->setDado( 'exercicio', Sessao::getExercicio() );
        $obTTGOOrgao->recuperaPorChave( $rsOrgao );

        if ( $rsOrgao->getNumLinhas() > 0 ) {
            $stJs = "document.getElementById('inCGMOrgao').value = '".$rsOrgao->getCampo('numcgm_orgao')."'; ";
            $stJs.= "document.getElementById('stNomCGMOrgao').innerHTML = '".sistemaLegado::pegaDado('nom_cgm','sw_cgm',' where numcgm = '.$rsOrgao->getCampo('numcgm_orgao').' ')."'; ";
            $stJs.= "
                for (i=0;i<document.getElementById('inTipoOrgao').length;i++) {
                    if (document.getElementById('inTipoOrgao').options[i].value == '".$rsOrgao->getCampo('cod_tipo')."') {
                       document.getElementById('inTipoOrgao').selectedIndex = i;
                    }
                }";
            $stJs.= "
                for (i=0;i<document.getElementById('stSiglaUF').length;i++) {
                    if (document.getElementById('stSiglaUF').options[i].value == '".$rsOrgao->getCampo('uf_crc_contador')."') {
                       document.getElementById('stSiglaUF').selectedIndex = i;
                    }
                }";
            $stJs.= "document.getElementById('inCGMContador').value = '".$rsOrgao->getCampo('numcgm_contador')."'; ";
            $stJs.= "document.getElementById('stNomContador').innerHTML = '".sistemaLegado::pegaDado('nom_cgm','sw_cgm',' where numcgm = '.$rsOrgao->getCampo('numcgm_contador').' ')."'; ";
            $stJs.= "document.getElementById('stCRCContador').value = '".$rsOrgao->getCampo('crc_contador')."'; ";

            $obTTGOOrgaoControleInterno = new TTGOOrgaoControleInterno();
            $obTTGOOrgaoControleInterno->setDado( 'num_orgao', $rsOrgao->getCampo('num_orgao') );
            $obTTGOOrgaoControleInterno->setDado( 'exercicio', $rsOrgao->getCampo('exercicio') );
            $obTTGOOrgaoControleInterno->recuperaPorChave( $rsOrgaoControleInterno );
            if ( $rsOrgaoControleInterno->getNumLinhas() > 0 ) {
                $stJs.= "document.getElementById('inCGMReponsavelInterno').value = '".$rsOrgaoControleInterno->getCampo('numcgm')."'; ";
                $stJs.= "document.getElementById('stNomReponsavelInterno').innerHTML = '".sistemaLegado::pegaDado('nom_cgm','sw_cgm',' where numcgm = '.$rsOrgaoControleInterno->getCampo('numcgm').' ')."'; ";
            } else {
                $stJs.= "document.getElementById('inCGMReponsavelInterno').value = ''; ";
                $stJs.= "document.getElementById('stNomReponsavelInterno').innerHTML = ''; ";
            }

            $obTTGOOrgaoRepresentante = new TTGOOrgaoRepresentante();
            $obTTGOOrgaoRepresentante->setDado( 'num_orgao', $rsOrgao->getCampo('num_orgao') );
            $obTTGOOrgaoRepresentante->setDado( 'exercicio', $rsOrgao->getCampo('exercicio') );
            $obTTGOOrgaoRepresentante->recuperaPorChave( $rsOrgaoRepresentante );
            if ( $rsOrgaoRepresentante->getNumLinhas() > 0 ) {
                $stJs.= "document.getElementById('inCGMRepresentante').value = '".$rsOrgaoRepresentante->getCampo('numcgm')."'; ";
                $stJs.= "document.getElementById('stNomRepresentante').innerHTML = '".sistemaLegado::pegaDado('nom_cgm','sw_cgm',' where numcgm = '.$rsOrgaoRepresentante->getCampo('numcgm').' ')."'; ";
            } else {
                $stJs.= "document.getElementById('inCGMRepresentante').value = ''; ";
                $stJs.= "document.getElementById('stNomRepresentante').innerHTML = '&nbsp;'; ";
            }

            $obTTGOOrgaoGestor = new TTGOOrgaoGestor();
            $obTTGOOrgaoGestor->setDado( 'exercicio', $rsOrgao->getCampo('exercicio') );
            $obTTGOOrgaoGestor->setDado( 'num_orgao', $rsOrgao->getCampo('num_orgao') );
            $obTTGOOrgaoGestor->recuperaPorChave( $rsOrgaoGestor );
            $inCount = 0;
            while ( !$rsOrgaoGestor->eof() ) {
                $arGestor[$inCount]['id'] = $inCount;
                $arGestor[$inCount]['inCGMGestor'] = $rsOrgaoGestor->getCampo('numcgm');
                $arGestor[$inCount]['stNomCGMGestor'] = sistemaLegado::pegaDado( 'nom_cgm','sw_cgm',' where numcgm = '.$rsOrgaoGestor->getCampo('numcgm').' ');
                $arGestor[$inCount]['dtInicio'] = $rsOrgaoGestor->getCampo('dt_inicio');
                $arGestor[$inCount]['stCargoGestor'] = $rsOrgaoGestor->getCampo('cargo');
                $arGestor[$inCount]['dtTermino'] = $rsOrgaoGestor->getCampo('dt_fim');
                $inCount++;
                $rsOrgaoGestor->proximo();
            }
            Sessao::write('arGestor', $arGestor);
            echo montaLista( $arGestor );
        } else {
            $stJs = "document.getElementById('inCGMOrgao').value = ''; ";
            $stJs.= "document.getElementById('stNomCGMOrgao').innerHTML = ''; ";
            $stJs.= "document.getElementById('inTipoOrgao').selectedIndex = 0; ";
            $stJs.= "document.getElementById('stSiglaUF').selectedIndex = 0; ";
            $stJs.= "document.getElementById('inCGMContador').value = ''; ";
            $stJs.= "document.getElementById('stNomContador').innerHTML = ''; ";
            $stJs.= "document.getElementById('stCRCContador').value = ''; ";

            echo montaLista( array() );
        }
        echo $stJs;
        break;
}
echo $stJs;
