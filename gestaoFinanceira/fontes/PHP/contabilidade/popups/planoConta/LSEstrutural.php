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
    * Página de Listagem de Plano Conta
    * Data de Criação   : 12/05/2008

    * @author Analista: Tonismar RÃ©gis Bernardo
    * @author Desenvolvedor: Grasiele Torres

    * @ignore

    * $Id: LSEstrutural.php 30739 2008-07-03 18:12:09Z girardi $

    * Casos de uso: uc-02.02.02,uc-02.04.09,uc-02.04.28,uc-02.02.31,uc-02.03.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php");
//Define o nome dos arquivos PHP
$stPrograma = "PlanoConta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgCons = $pgFilt;

include_once( $pgJS );

//$stCaminho   = "../modulos/calendario/relatorio/";

$obRegra = new RContabilidadePlanoContaAnalitica;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'baixar'   : $pgProx = $pgBaix; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'prorrogar': $pgProx = $pgCons; break;
    case 'consultar': $pgProx = $pgCons; break;
    DEFAULT         : $pgProx = $pgForm;
}

//Monta sessao com os valores do filtro
if ( is_array(Sessao::read('linkPopUp')) ) {
    $_REQUEST = Sessao::read('linkPopUp');
} else {
    $arLinkPopUp = array();
    foreach ($_REQUEST as $key => $valor) {
        $arLinkPopUp[$key] = $valor;
    }
    Sessao::write('linkPopUp', $arLinkPopUp);
}

if ($_REQUEST['stCodEstrutural']) {
    $obRegra->setCodEstrutural( $_REQUEST['stCodEstrutural'] );
    $stLink .= '&stCodEstrutural='.$_REQUEST['stCodEstrutural'];
}
if ($_REQUEST['inCodPlano']) {
    $obRegra->setCodPlano( $_REQUEST['inCodPlano'] );
    $stLink .= '&inCodPlano='.$_REQUEST['inCodPlano'];
}
if ($_REQUEST['inCodPlanoInicial']) {
    $obRegra->setCodPlanoInicial( $_REQUEST['inCodPlanoInicial'] );
    $stLink .= '&inCodPlanoInicial='.$_REQUEST['inCodPlanoInicial'];
}
if ($_REQUEST['inCodPlanoFinal']) {
    $obRegra->setCodPlanoFinal( $_REQUEST['inCodPlanoFinal'] );
    $stLink .= '&inCodPlanoFinal='.$_REQUEST['inCodPlanoFinal'];
}


$stLink .= "&stAcao=".$stAcao;
$stLink .= "&nomForm=".$_REQUEST['nomForm'];
$stLink .= "&campoNum=".$_REQUEST['campoNum'];
$stLink .= "&campoNom=".$_REQUEST['campoNom'];
$stLink .= "&tipoBusca=".$_REQUEST['tipoBusca'];
$stLink .= "&inCodEntidade=".$_REQUEST['inCodEntidade'];

if (isset($_REQUEST['stExercicio'])) {
    $obRegra->setExercicio( $_REQUEST['stExercicio'] );    
}else{
    $obRegra->setExercicio( Sessao::getExercicio() );
}

if ($_REQUEST['stDescricao']) {
    $obRegra->setNomConta( $_REQUEST['stDescricao'] );
    $stLink .= '&stDescricao='.$_REQUEST['stDescricao'];
}

if ($_REQUEST['tipoBusca'] == "banco") {
    $obRegraBanco = new RContabilidadePlanoBanco;
    $obRegraBanco->setExercicio( Sessao::getExercicio() );
    $obRegraBanco->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
    $obRegraBanco->setCodEstrutural($_REQUEST['stCodEstrutural']);
    $obRegraBanco->obRMONBanco->setNomBanco($_REQUEST['stDescricao']);
    $obRegraBanco->listarContasBancos( $rsLista, "" );
} elseif ($_REQUEST['tipoBusca'] == "bordero_transf") {

    if ($_REQUEST['stTipoTransacao'] == "6") {

        $obRegraBanco = new RContabilidadePlanoBanco;
        $obRegraBanco->setExercicio( Sessao::getExercicio() );
        $obRegraBanco->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obRegraBanco->setCodEstrutural($_REQUEST['stCodEstrutural']);
        $obRegraBanco->obRMONBanco->setNomBanco($_REQUEST['stDescricao']);
        $obRegraBanco->listarContasBancos( $rsLista, "" );
    } elseif ($_REQUEST['stTipoTransacao'] == "7") {

        $obRegra->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
        $obRegra->setCodEstrutural( $_REQUEST['stCodEstrutural'] );
        $obRegra->setNomConta( $_REQUEST['stDescricao'] );
        $obRegra->listarPlanoContaTransferenciaEntidadeDiferente( $rsLista );
    } elseif ($_REQUEST['stTipoTransacao'] == "8") {

        $obRegra->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
        $obRegra->setCodEstrutural( $_REQUEST['stCodEstrutural'] );
        $obRegra->setNomConta( $_REQUEST['stDescricao'] );
        $obRegra->listarPlanoContaConsignacao( $rsLista );
    }

} elseif ($_REQUEST['tipoBusca'] == "tes_transf") {
    $obRegra->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $obRegra->listarPlanoContaTransferencia($rsLista, "" );

} elseif ($_REQUEST['tipoBusca'] == "tes_pag") {
    $obRegraBanco = new RContabilidadePlanoBanco;
    $obRegraBanco->setExercicio( Sessao::getExercicio() );
    $obRegraBanco->setCodEstrutural($_REQUEST['stCodEstrutural']);
    $obRegraBanco->setNomConta( $_REQUEST['stDescricao'] );
    $obRegraBanco->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $obRegraBanco->listarPlanoContaPagamento($rsLista, "" );

} elseif ($_REQUEST['tipoBusca'] == "tes_arrec") {
    $obRegra->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $obRegra->listarPlanoContaArrecadacao($rsLista, "" );
} elseif ($_REQUEST['tipoBusca'] == "conta_analitica" OR $_REQUEST['tipoBusca'] == 'conta_analitica_estrutural') {
    $obRegra->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $obRegra->setCodEstrutural( $_REQUEST['stCodEstrutural'] );
    $obRegra->setNomConta( $_REQUEST['stDescricao'] );
    if ($_REQUEST['tipoBusca'] == 'conta_analitica_estrutural') {
        $obRegra->setCodIniEstrutural( $_REQUEST['inCodIniEstrutural'] );
    }
    if ($_REQUEST['tipoBusca2']) {
        if ($_REQUEST['tipoBusca2'] == 'extmmaa') {
             $stFiltro2 .= " NOT EXISTS ( SELECT 	1
                                            FROM 	tcmgo.balancete_".strtolower($_REQUEST['tipoBusca2'])."
                                           WHERE    balancete_".strtolower($_REQUEST['tipoBusca2']).".cod_plano = pa.cod_plano
                                             AND    balancete_".strtolower($_REQUEST['tipoBusca2']).".exercicio = pa.exercicio
                                        ) AND ";
        } else {
            $stFiltro2 .=  " NOT EXISTS ( SELECT 	1
                                            FROM 	tcmgo.balanco_".strtolower($_REQUEST['tipoBusca2'])."
                                           WHERE    balanco_".strtolower($_REQUEST['tipoBusca2']).".cod_plano = pa.cod_plano
                                             AND    balanco_".strtolower($_REQUEST['tipoBusca2']).".exercicio = pa.exercicio
                                        ) AND ";
        }
    }
    $obRegra->listarContaAnaliticaFiltro( $rsLista, $stFiltro2 );
} else {
    if ($_REQUEST['tipoBusca'] == 'orcamentaria') {
        $obRegra->setCodEstrutural( '4' );
        if( $_REQUEST['tipoBusca2'] == 'receitas_primarias' )
            $obRegra->boFiltraReceitasPrimarias=true;
    } elseif ($_REQUEST['tipoBusca'] == 'extra') {
        $obRegra->setCodEstrutural( '1.1.2' );
    }

    $obRegra->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $obRegra->listarContaAnalitica( $rsLista );
}

if ($_REQUEST['tipoBusca'] == "contaSinteticaAtivoPermanente") {
    $obRegra->listarContaAnaliticaAtivoPermanente ($rsLista,"");
}

/* Inicio dos códigos melhorados
 * /\/\/\/\/\/\/\/\/\/\/\/\/\/\/
 */

if ($_REQUEST['tipoBusca']) {
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
    $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

    switch ($_REQUEST['tipoBusca']) {
        case 'tes_deposito':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $_REQUEST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_REQUEST['stCodEstrutural']."')||'%' AND ";
            if( $_REQUEST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_REQUEST['stDescricao']."%') AND ";
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
                $stFiltro .= "\n   (pc.cod_estrutural like '1.1.1.1.2.%' OR pc.cod_estrutural like '1.1.1.1.1.%' OR pc.cod_estrutural like '1.1.1.1.4.%' OR pc.cod_estrutural like '1.1.1.1.5.%') ) OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
            }

        break;

        case 'tes_retirada':
                $stFiltro  = "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $_REQUEST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_REQUEST['stCodEstrutural']."')||'%' AND ";
            if( $_REQUEST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_REQUEST['stDescricao']."%') AND ";
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
                $stFiltro .= "\n   (pc.cod_estrutural like '1.1.1.1.1.%' OR pc.cod_estrutural like '1.1.1.1.2.%' OR pc.cod_estrutural like '1.1.1.1.4.%' OR pc.cod_estrutural like '1.1.1.1.5.%') ) OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
            }
        break;

        case 'tes_aplicacao_entrada':
                $stFiltro  = "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $_REQUEST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_REQUEST['stCodEstrutural']."')||'%' AND ";
            if( $_REQUEST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_REQUEST['stDescricao']."%') AND ";
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
                $stFiltro .= "\n   (pc.cod_estrutural like '1.1.1.1.3.%' OR pc.cod_estrutural like '1.1.1.1.4.%' OR pc.cod_estrutural like '1.1.1.1.5.%') ) OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
            }
        break;

        case 'tes_aplicacao_contrapartida':
                $stFiltro  = "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $_REQUEST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_REQUEST['stCodEstrutural']."')||'%' AND ";
            if( $_REQUEST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_REQUEST['stDescricao']."%') AND ";
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
                $stFiltro .= "\n   (pc.cod_estrutural like '1.1.1.1.2.%' OR pc.cod_estrutural like '1.1.1.1.1.%' OR pc.cod_estrutural like '1.1.1.1.4.%' OR pc.cod_estrutural like '1.1.1.1.5.%' )) OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
            }
        break;

        case 'tes_resgate_entrada':
                $stFiltro  = "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $_REQUEST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_REQUEST['stCodEstrutural']."')||'%' AND ";
            if( $_REQUEST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_REQUEST['stDescricao']."%') AND ";
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
                $stFiltro .= "\n   (pc.cod_estrutural like '1.1.1.1.2.%' OR pc.cod_estrutural like '1.1.1.1.4.%' OR pc.cod_estrutural like '1.1.1.1.5.%' ) ) OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
            }
        break;

        case 'tes_resgate_contrapartida':
                $stFiltro  = "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $_REQUEST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_REQUEST['stCodEstrutural']."')||'%' AND ";
            if( $_REQUEST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_REQUEST['stDescricao']."%') AND ";
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
                $stFiltro .= "\n   ( pc.cod_estrutural like '1.1.1.1.3.%' OR pc.cod_estrutural like '1.1.1.1.4.%' OR pc.cod_estrutural like '1.1.1.1.5.%' ) ) OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
            }
        break;

        case 'tes_pagamento_extra_despesa':
                $stFiltro .= "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
                $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%'
                                OR pc.cod_estrutural like '2.1.1.%'
                                OR pc.cod_estrutural like '5%'
                                OR pc.cod_estrutural like '6%' ) AND ";

            if( $_POST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            if( $_POST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";
        break;

        case 'emp_conta_lancamento_adiantamentos':
                $stFiltro .= "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
                $stFiltro .= "\n pc.cod_estrutural like '1.9.9.1%' AND ";

            if( $_POST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            if( $_POST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";
        break;

        case 'tes_pagamento_extra_caixa_banco':
                $stFiltro  = "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $_POST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            if( $_POST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' ) OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
            }
        break;

        case 'tes_pagamento':
                $stFiltro  = "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $_POST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            if( $_POST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";

            $stFiltro .= "\n(( pb.cod_banco is not null AND ";
            $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' ) OR ";
            $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";

            /*  if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
            }*/
        break;

        case 'tes_arrecadacao_extra_receita':
                $stFiltro .= "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
                $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%' OR pc.cod_estrutural like '2.1.1.%' OR pc.cod_estrutural like '5%'      OR pc.cod_estrutural like '6%' ) AND ";

            if( $_POST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            if( $_POST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";
        break;

        case 'tes_arrecadacao_extra_caixa_banco':
                $stFiltro  = "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $_POST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            if( $_POST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' ) OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
            }
        break;

        case 'con_conta_lancamento_rp_credito':

                $inExercicio = Sessao::getExercicio();

               // $stFiltro  = "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . $inExercicio . "' AND ";

            if( $_POST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            if( $_POST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";
            $stFiltro .= "\n   (pc.cod_estrutural like '2.2.%'  OR pc.cod_estrutural like '2.1.2.%' )AND ";
        break;

        case 'con_conta_lancamento_rp_debito':

            $inExercicio = Sessao::getExercicio();

            //$stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . $inExercicio . "' AND ";

            if( $_POST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            if( $_POST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";

            $stFiltro .= "\n   pc.cod_estrutural like '2.1.2.%'  AND ";
        break;

        case 'tes_contrapartida_lancamento':

            $inExercicio = Sessao::getExercicio();

            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . $inExercicio . "' AND ";

            if( $_POST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            if( $_POST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";

            $stFiltro .= "\n   pc.cod_estrutural like '2.9.9.1.%'  AND ";
        break;

        case 'emp_retencao_op_extra':

            $inExercicio = Sessao::getExercicio();
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . $inExercicio . "' AND ";

            if( $_POST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            if( $_POST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";

            $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%' OR ";
            $stFiltro .= "\n   pc.cod_estrutural like '2.1.1.%' OR ";
            $stFiltro .= "\n   pc.cod_estrutural like '5.%' OR     ";
            $stFiltro .= "\n   pc.cod_estrutural like '6.%' ) AND ";

        break;

        case 'emp_conta_caixa':

                $stFiltro  = "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $_POST['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            if( $_POST['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.1.1.%' ) ) AND ";
            }

        break;

        case 'emp_conta_debito_incorp':

            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
            $stFiltro .= "\n ( pc.cod_estrutural LIKE '1.2.3.%' OR pc.cod_estrutural LIKE '1.4.%' ) AND ";

            if ($_POST['stCodEstrutural']) {
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            }
            if ($_POST['stDescricao']) {
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";
            }
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
            }
        break;

        case 'emp_conta_credito_incorp':

            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
            $stFiltro .= "\n (pc.cod_estrutural LIKE '6.1.3.0.%' OR pc.cod_estrutural LIKE '6.1.3.1.%' OR pc.cod_estrutural LIKE '6.1.3.2.%' ) AND ";

            if ($_POST['stCodEstrutural']) {
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            }
            if ($_POST['stDescricao']) {
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";
            }
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
            }
        break;

        case 'emp_conta_debito_amort':

            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
            $stFiltro .= "\n ( pc.cod_estrutural LIKE '2.1.%' OR pc.cod_estrutural LIKE '2.2.%' ) AND ";

            if ($_POST['stCodEstrutural']) {
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            }
            if ($_POST['stDescricao']) {
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";
            }
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
            }
        break;

        case 'emp_conta_credito_amort':

            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
            $stFiltro .= "\n pc.cod_estrutural LIKE '6.1.3.3.%' AND ";

            if ($_POST['stCodEstrutural']) {
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$_POST['stCodEstrutural']."')||'%' AND ";
            }
            if ($_POST['stDescricao']) {
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$_POST['stDescricao']."%') AND ";
            }
            if ($_REQUEST['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
            }
        break;

    }

    if ($stFiltro) {
        $stFiltro = " WHERE " . substr( $stFiltro, 0, strlen( $stFiltro ) -4 );
        $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
        $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $boTransacao );
    }

}
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Classificação");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Reduzido");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição ");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_estrutural" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_plano" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_conta" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insere();" );
$obLista->ultimaAcao->addCampo("1","cod_estrutural");
$obLista->ultimaAcao->addCampo("2","");
$obLista->ultimaAcao->addCampo("3","nom_conta");
$obLista->commitAcao();

$obLista->show();

?>
