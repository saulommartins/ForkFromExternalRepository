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
    * Data de Criação   : 15/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * $Id: LSPlanoConta.php 65901 2016-06-28 14:22:28Z michel $

    * Casos de uso: uc-02.02.02,uc-02.04.09,uc-02.04.28,uc-02.02.31,uc-02.03.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";

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

$obRegra = new RContabilidadePlanoContaAnalitica;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
$stLink = '';
$stFiltro = '';

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
    default         : $pgProx = $pgForm;
}

//Monta sessao com os valores do filtro
if ( is_array(Sessao::read('linkPopUp')) ) {
    $request = new Request( Sessao::read('linkPopUp') );
} else {
    $arLinkPopUp = array();
    foreach ($request->getAll() as $key => $valor) {
        $arLinkPopUp[$key] = $valor;
    }
    Sessao::write('linkPopUp',$arLinkPopUp);
}

$arRequest = $request->getAll();

if ($arRequest['stCodEstrutural']) {
    $obRegra->setCodEstrutural( $arRequest['stCodEstrutural'] );
    $stLink .= '&stCodEstrutural='.$arRequest['stCodEstrutural'];
}

$stLink .= "&stAcao=".$stAcao;
$stLink .= "&nomForm=".$arRequest['nomForm'];
$stLink .= "&campoNum=".$arRequest['campoNum'];
$stLink .= "&campoNom=".$arRequest['campoNom'];
$stLink .= "&tipoBusca=".$arRequest['tipoBusca'];
$stLink .= "&inCodEntidade=".$arRequest['inCodEntidade'];
$stLink .= "&tipoBusca2=".$arRequest['tipoBusca2'];

$obRegra->setExercicio( Sessao::getExercicio() );

if ($arRequest['stDescricao']) {
    $obRegra->setNomConta( $arRequest['stDescricao'] );
    $stLink .= '&stDescricao='.$arRequest['stDescricao'];
}

if ($arRequest['tipoBusca'] == "banco" || $arRequest['tipoBusca'] == "codigoReduzidoBanco") {

    $obRegraBanco = new RContabilidadePlanoBanco;
    $obRegraBanco->setExercicio( Sessao::getExercicio() );
    $obRegraBanco->obROrcamentoEntidade->setCodigoEntidade($arRequest['inCodEntidade']);
    $obRegraBanco->setCodEstrutural($arRequest['stCodEstrutural']);
    $obRegraBanco->obRMONBanco->setNomBanco($arRequest['stDescricao']);
    $obRegraBanco->listarContasBancos( $rsLista, "" );

} elseif ($arRequest['tipoBusca'] == "bordero_transf") {

    if ($arRequest['stTipoTransacao'] == "6") {
        $obRegraBanco = new RContabilidadePlanoBanco;
        $obRegraBanco->setExercicio( Sessao::getExercicio() );
        $obRegraBanco->obROrcamentoEntidade->setCodigoEntidade($arRequest['inCodEntidade']);
        $obRegraBanco->setCodEstrutural($arRequest['stCodEstrutural']);
        $obRegraBanco->obRMONBanco->setNomBanco($arRequest['stDescricao']);
        $obRegraBanco->listarContasBancos( $rsLista, "" );
    } elseif ($arRequest['stTipoTransacao'] == "7") {
        $obRegra->obROrcamentoEntidade->setCodigoEntidade( $arRequest['inCodEntidade'] );
        $obRegra->setCodEstrutural( $arRequest['stCodEstrutural'] );
        $obRegra->setNomConta( $arRequest['stDescricao'] );
        $obRegra->listarPlanoContaTransferenciaEntidadeDiferente( $rsLista );
    } elseif ($arRequest['stTipoTransacao'] == "8") {
        $obRegra->obROrcamentoEntidade->setCodigoEntidade( $arRequest['inCodEntidade'] );
        $obRegra->setCodEstrutural( $arRequest['stCodEstrutural'] );
        $obRegra->setNomConta( $arRequest['stDescricao'] );
        $obRegra->listarPlanoContaConsignacao( $rsLista );
    }

} elseif ($arRequest['tipoBusca'] == "tes_transf") {

    $obRegra->obROrcamentoEntidade->setCodigoEntidade( $arRequest['inCodEntidade'] );
    $obRegra->listarPlanoContaTransferencia($rsLista, "" );

} elseif ($arRequest['tipoBusca'] == "tes_pag") {

    $obRegraBanco = new RContabilidadePlanoBanco;
    $obRegraBanco->setExercicio( Sessao::getExercicio() );
    $obRegraBanco->setCodEstrutural($arRequest['stCodEstrutural']);
    $obRegraBanco->setNomConta( $arRequest['stDescricao'] );
    $obRegraBanco->obROrcamentoEntidade->setCodigoEntidade( $arRequest['inCodEntidade'] );
    $obRegraBanco->listarPlanoContaPagamento($rsLista, "" );

} elseif ($arRequest['tipoBusca'] == "tes_arrec") {

    $obRegra->obROrcamentoEntidade->setCodigoEntidade( $arRequest['inCodEntidade'] );
    $obRegra->listarPlanoContaArrecadacao($rsLista, "" );

} elseif ($arRequest['tipoBusca'] == "conta_analitica" OR $arRequest['tipoBusca'] == 'conta_analitica_estrutural') {

    $obRegra->obROrcamentoEntidade->setCodigoEntidade( $arRequest['inCodEntidade'] );
    $obRegra->setCodEstrutural( $arRequest['stCodEstrutural'] );
    $obRegra->setNomConta( $arRequest['stDescricao'] );
    if ($arRequest['tipoBusca'] == 'conta_analitica_estrutural') {
        $obRegra->setCodIniEstrutural( $arRequest['inCodIniEstrutural'] );
    }
    if ($arRequest['tipoBusca2']) {
        if ($arRequest['tipoBusca2'] == 'extmmaa') {
             $stFiltro2 .= " NOT EXISTS ( SELECT    1
                                            FROM    tcmgo.balancete_".strtolower($arRequest['tipoBusca2'])."
                                           WHERE    balancete_".strtolower($arRequest['tipoBusca2']).".cod_plano = pa.cod_plano
                                             AND    balancete_".strtolower($arRequest['tipoBusca2']).".exercicio = pa.exercicio
                                        ) AND ";
        } else {
            $stFiltro2 .=  " NOT EXISTS ( SELECT    1
                                            FROM    tcmgo.balanco_".strtolower($arRequest['tipoBusca2'])."
                                           WHERE    balanco_".strtolower($arRequest['tipoBusca2']).".cod_plano = pa.cod_plano
                                             AND    balanco_".strtolower($arRequest['tipoBusca2']).".exercicio = pa.exercicio
                                        ) AND ";
        }
    }
    if ( Sessao::getExercicio() > '2012' ) {
            $stFiltro2 .= ' ( pc.cod_estrutural like \'1.1.2.%\' OR
                              pc.cod_estrutural like \'1.1.3.%\' OR
                              pc.cod_estrutural like \'1.2.1.%\' OR
                              pc.cod_estrutural like \'2.1.1.%\' OR
                              pc.cod_estrutural like \'2.1.2.%\' OR
                              pc.cod_estrutural like \'2.1.9.%\' OR
                              pc.cod_estrutural like \'2.2.1.%\' OR
                              pc.cod_estrutural like \'2.2.2.%\' OR
                              pc.cod_estrutural like \'3.5.%\'   OR
                              pc.cod_estrutural like \'4.5.%\' ) AND ';
    }
    $obRegra->listarContaAnaliticaFiltro( $rsLista, $stFiltro2 );

} elseif ($arRequest['tipoBusca'] == "somente_contas_analiticas") {

    $obRegra->obROrcamentoEntidade->setCodigoEntidade( $arRequest['inCodEntidade'] );
    $obRegra->setCodEstrutural( $arRequest['stCodEstrutural'] );
    $obRegra->setNomConta( $arRequest['stDescricao'] );

    $obRegra->listarContaAnaliticaFiltro( $rsLista, $stFiltro2 );

} elseif ($arRequest['tipoBusca'] == 'estrutural') {

    if ( Sessao::getExercicio() > '2012' ) {
        $stFiltro2 .= ' pc.cod_estrutural like \'1.1.1.%\' AND ';
    }
    $obRegra->listarContaAnaliticaFiltro( $rsLista, $stFiltro2 );

} else {

    if ($arRequest['tipoBusca'] == 'orcamentaria') {
        $obRegra->setCodEstrutural( '4' );
        if( $arRequest['tipoBusca2'] == 'receitas_primarias' )
            $obRegra->boFiltraReceitasPrimarias=true;
    } elseif ($arRequest['tipoBusca'] == 'extra') {
        $obRegra->setCodEstrutural( '1.1.2' );
    }

    $obRegra->obROrcamentoEntidade->setCodigoEntidade( $arRequest['inCodEntidade'] );
    $obRegra->listarContaAnalitica( $rsLista );

}

/* Inicio dos códigos melhorados
 * /\/\/\/\/\/\/\/\/\/\/\/\/\/\/
 */
if ($arRequest['tipoBusca']) {
    include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php";
    $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica;
    $stSQLRelacionamentoRecurso = "";
    switch ($arRequest['tipoBusca']) {
        case 'tes_deposito':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            if ($arRequest['inCodEntidade']) {
                if ( Sessao::getExercicio() > '2012' ) {
                    $stFiltro .= "( pb.cod_banco is not null AND ";
                    $stFiltro .= "  pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "( pc.cod_estrutural like '1.1.1.%' OR pc.cod_estrutural like '1.2.2.3%' )) AND ";
                } else {
                    $stFiltro .= "( pb.cod_banco is not null AND ";
                    $stFiltro .= "  pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "( pc.cod_estrutural like '1.1.1.1.2.%'
                                 OR pc.cod_estrutural like '1.1.1.1.1.%'
                                 OR pc.cod_estrutural like '1.1.1.1.4.%'
                                 OR pc.cod_estrutural like '1.1.1.1.5.%'
                                 OR pc.cod_estrutural like '1.1.5.%'
                                 OR pc.cod_estrutural like '1.2.2.3%' )) AND ";
                }
            }
        break;

        case 'tes_retirada':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            if ($arRequest['inCodEntidade']) {
                if ( Sessao::getExercicio() > '2012' ) {
                    $stFiltro .= "( pb.cod_banco is not null AND ";
                    $stFiltro .= "  pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "( pc.cod_estrutural like '1.1.1.%'
                                 OR pc.cod_estrutural like '1.2.2.3%' )) AND ";
                } else {
                    $stFiltro .= "( pb.cod_banco is not null AND ";
                    $stFiltro .= "  pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "( pc.cod_estrutural like '1.1.1.1.1.%'
                                    OR pc.cod_estrutural like '1.1.1.1.2.%'
                                    OR pc.cod_estrutural like '1.1.1.1.4.%'
                                    OR pc.cod_estrutural like '1.1.1.1.5.%'
                                    OR pc.cod_estrutural like '1.1.5.%'
                                    OR pc.cod_estrutural like '1.2.2.3%' )) AND ";
                }
            }
        break;

        case 'tes_aplicacao_entrada':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            if ($arRequest['inCodEntidade']) {
                if ( Sessao::getExercicio() > '2012' ) {
                    $stFiltro .= "( pb.cod_banco is not null AND ";
                    $stFiltro .= "  pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "( pc.cod_estrutural like '1.1.1.%'
                                 OR pc.cod_estrutural like '1.1.4.%'
                                 OR pc.cod_estrutural like '1.2.2.3%' )) AND ";
                } else {
                    $stFiltro .= "( pb.cod_banco is not null AND ";
                    $stFiltro .= "  pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "( pc.cod_estrutural like '1.1.1.1.3.%'
                                 OR pc.cod_estrutural like '1.1.1.1.4.%'
                                 OR pc.cod_estrutural like '1.1.1.1.5.%'
                                 OR pc.cod_estrutural like '1.1.5.%'
                                 OR pc.cod_estrutural like '1.1.1.1.3.%'
                                 OR pc.cod_estrutural like '1.2.2.3%' )) AND ";
                }
            }
        break;

        case 'tes_aplicacao_contrapartida':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            if ($arRequest['inCodEntidade']) {
                if ( Sessao::getExercicio() > '2012' ) {
                    $stFiltro .= "( pb.cod_banco is not null AND ";
                    $stFiltro .= "  pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "( pc.cod_estrutural like '1.1.1.%'
                                 OR pc.cod_estrutural like '1.1.4.%'
                                 OR pc.cod_estrutural like '1.2.2.3%' )) AND ";
                } else {
                    $stFiltro .= "( pb.cod_banco is not null AND ";
                    $stFiltro .= "  pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "( pc.cod_estrutural like '1.1.1.1.2.%'
                                 OR pc.cod_estrutural like '1.1.1.1.1.%'
                                 OR pc.cod_estrutural like '1.1.1.1.4.%'
                                 OR pc.cod_estrutural like '1.1.1.1.5.%'
                                 OR pc.cod_estrutural like '1.1.5.%'
                                 OR pc.cod_estrutural like '1.2.2.3%')) AND ";
                }
            }
        break;

        case 'tes_resgate_entrada':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            if ($arRequest['inCodEntidade']) {
                if ( Sessao::getExercicio() > '2012' ) {
                    $stFiltro .= "( pb.cod_banco is not null AND ";
                    $stFiltro .= "  pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "( pc.cod_estrutural like '1.1.1.%'
                                OR  pc.cod_estrutural like '1.1.4.%' )) AND ";
                } else {
                    $stFiltro .= "( pb.cod_banco is not null AND ";
                    $stFiltro .= "  pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "( pc.cod_estrutural like '1.1.1.1.2.%'
                                 OR pc.cod_estrutural like '1.1.1.1.4.%'
                                 OR pc.cod_estrutural like '1.1.1.1.5.%'
                                 OR pc.cod_estrutural like '1.1.5.%'
                                 OR pc.cod_estrutural like '1.2.2.3%' )) AND ";
                }
            }
        break;

        case 'tes_resgate_contrapartida':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            if ($arRequest['inCodEntidade']) {
                if ( Sessao::getExercicio() > '2012' ) {
                    $stFiltro .= "\n( pb.cod_banco is not null AND ";
                    $stFiltro .= "\n  pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "\n( pc.cod_estrutural like '1.1.1.%'
                                   OR pc.cod_estrutural like '1.1.4.%' )) AND ";
                } else {
                    $stFiltro .= "\n( pb.cod_banco is not null AND ";
                    $stFiltro .= "\n  pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "\n( pc.cod_estrutural like '1.1.1.1.3.%'
                                   OR pc.cod_estrutural like '1.1.1.1.4.%'
                                   OR pc.cod_estrutural like '1.1.1.1.5.%'
                                   OR pc.cod_estrutural like '1.1.5.%'
                                   OR pc.cod_estrutural like '1.1.1.1.3.%'
                                   OR pc.cod_estrutural like '1.2.2.3%' )) AND ";
                }
            }
        break;

        case 'tes_pagamento_extra_despesa':
            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%'
                                OR pc.cod_estrutural like '1.1.3.%'
                                OR pc.cod_estrutural like '1.1.4.9%' 
                                OR pc.cod_estrutural like '1.2.1.%'
                                OR pc.cod_estrutural like '2.1.1.%'
                                OR pc.cod_estrutural like '2.1.2.%'
                                OR pc.cod_estrutural like '2.1.8.%'
                                OR pc.cod_estrutural like '2.1.9.%'
                                OR pc.cod_estrutural like '2.2.1.%'
                                OR pc.cod_estrutural like '2.2.2.%'
                                OR pc.cod_estrutural like '3.5.%' ) AND ";
            } else {
                $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%'
                                OR pc.cod_estrutural like '2.1.1.%'
                                OR pc.cod_estrutural like '5%'
                                OR pc.cod_estrutural like '6%' ) AND ";
            }

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
        break;

        case 'emp_conta_lancamento_adiantamentos':
            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if (Sessao::getExercicio() > '2012') {
                $stFiltro .= "\n   pc.cod_estrutural like '7.1.1.1.%'  AND ";
            } else {
                $stFiltro .= "\n pc.cod_estrutural like '1.9.9.1%' AND ";
            }

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
        break;

        case 'tes_pagamento_extra_caixa_banco':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            if ($arRequest['inCodEntidade']) {
                if ( Sessao::getExercicio() > '2012' ) {
                    $stFiltro .= "\n( pb.cod_banco is not null AND ";
                    $stFiltro .= "\n   pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "\n   ( pc.cod_estrutural like '1.1.1.%' OR ";
                    $stFiltro .= "\n   pc.cod_estrutural like '1.1.4.%' )) AND ";
                } else {
                    $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                    $stFiltro .= "\n   pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' ) OR ";
                    $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
                }
            }
        break;

        case 'tes_pagamento_extra_caixa_banco_recurso_fixo':
            $stSQLRelacionamentoRecurso = "
                LEFT JOIN contabilidade.plano_recurso
                       ON plano_recurso.exercicio = pa.exercicio
                      AND plano_recurso.cod_plano = pa.cod_plano
            ";
            $stFiltro .= "\n plano_recurso.cod_recurso = 100 AND ";
            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            if ($arRequest['inCodEntidade']) {
                if ( Sessao::getExercicio() > '2012' ) {
                    $stFiltro .= "\n( pb.cod_banco is not null AND ";
                    $stFiltro .= "\n   pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "\n   ( pc.cod_estrutural like '1.1.1.%' OR ";
                    $stFiltro .= "\n   pc.cod_estrutural like '1.1.4.%' )) AND ";
                } else {
                    $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                    $stFiltro .= "\n   pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' ) OR ";
                    $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
                }
            }
        break;

        case 'tes_pagamento':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "\n pb.cod_banco is not null AND ";
                $stFiltro .= "\n ( pc.cod_estrutural like '1.1.1.%' OR ";
                $stFiltro .= "\n pc.cod_estrutural like '1.1.4.%' ) AND ";
                if ($arRequest['inCodEntidade']) {
                    $stFiltro .= "\n pb.cod_entidade in (".$arRequest['inCodEntidade'].") AND ";
                }
            } else {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' ) OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
            }
        break;

        case 'tes_arrecadacao_extra_receita':
            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%'
                                OR pc.cod_estrutural like '1.1.3.%'
                                OR pc.cod_estrutural like '1.1.4.9.%'
                                OR pc.cod_estrutural like '1.2.1.%'
                                OR pc.cod_estrutural like '2.1.1.%'
                                OR pc.cod_estrutural like '2.1.2.%'
                                OR pc.cod_estrutural like '2.1.8.%'
                                OR pc.cod_estrutural like '2.1.9.%'
                                OR pc.cod_estrutural like '2.2.1.%'
                                OR pc.cod_estrutural like '2.2.2.%'
                                OR pc.cod_estrutural like '4.5.%' ) AND ";
            } else {
                $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%'
                                OR pc.cod_estrutural like '2.1.1.%'
                                OR pc.cod_estrutural like '5%'
                                OR pc.cod_estrutural like '6%' ) AND ";
            }

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
        break;

        case 'gpc_parametros_rd_extra':
            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
            $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%' OR pc.cod_estrutural like '2.1.1.%' OR pc.cod_estrutural like '5%' OR pc.cod_estrutural like '2.1.2.%'   OR pc.cod_estrutural like '6%' ) AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
        break;

        case 'tes_arrecadacao_extra_caixa_banco':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            if ($arRequest['inCodEntidade']) {
                if ( Sessao::getExercicio() > '2012' ) {
                    $stFiltro .= "\n( pb.cod_banco is not null AND ";
                    $stFiltro .= "\n   pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "\n   ( pc.cod_estrutural like '1.1.1.%' OR ";
                    $stFiltro .= "\n   pc.cod_estrutural like '1.1.4.%' )) AND ";
                } else {
                    $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                    $stFiltro .= "\n   pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                    $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' ) OR ";
                    $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
                }
            }
        break;

        case 'con_conta_lancamento_rp_credito':
            $inExercicio = Sessao::getExercicio();
            $stFiltro .= "\n pc.exercicio = '" . $inExercicio . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            $stFiltro .= "\n   (pc.cod_estrutural like '2.2.%'  OR pc.cod_estrutural like '2.1.2.%' )AND ";
        break;

        case 'con_conta_lancamento_rp_debito':
            $inExercicio = Sessao::getExercicio();

            $stFiltro .= "\n pc.exercicio = '" . $inExercicio . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";

            $stFiltro .= "\n   pc.cod_estrutural like '2.1.2.%'  AND ";
        break;

        case 'con_relac_conta_entidade':
            $inExercicio = Sessao::getExercicio();

            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . $inExercicio . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";

            $stFiltro .= "\n   (pc.cod_estrutural like '2.%' )AND ";
        break;

        case 'tes_contrapartida_lancamento':
            $inExercicio = Sessao::getExercicio();

            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . $inExercicio . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";

            if (Sessao::getExercicio() > '2012') {
                $stFiltro .= "\n   pc.cod_estrutural like '8.1.1.1.%'  AND ";
            } else {
                $stFiltro .= "\n   pc.cod_estrutural like '2.9.9.1.%'  AND ";
            }
        break;

        case 'emp_retencao_op_extra':
            $inExercicio = Sessao::getExercicio();
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . $inExercicio . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";

            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.3.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.2.1.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.1.1.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.1.2.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.1.9.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.1.8.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.2.1.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.2.2.%' ) AND ";
            } else {
                $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.1.1.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '5.%' OR     ";
                $stFiltro .= "\n   pc.cod_estrutural like '6.%' ) AND ";
            }
        break;

        case 'emp_conta_caixa':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            if ($arRequest['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.1.1.%' ) ) AND ";
            }
        break;

        case 'emp_conta_debito_incorp':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
            $stFiltro .= "\n ( pc.cod_estrutural LIKE '1.2.3.%' OR pc.cod_estrutural LIKE '1.4.%' ) AND ";

            if ($arRequest['stCodEstrutural']) {
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            }
            if ($arRequest['stDescricao']) {
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            }
            if ($arRequest['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
            }
        break;

        case 'emp_conta_credito_incorp':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
            $stFiltro .= "\n (pc.cod_estrutural LIKE '6.1.3.0.%' OR pc.cod_estrutural LIKE '6.1.3.1.%' OR pc.cod_estrutural LIKE '6.1.3.2.%' ) AND ";

            if ($arRequest['stCodEstrutural']) {
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            }
            if ($arRequest['stDescricao']) {
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            }
            if ($arRequest['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
            }
        break;

        case 'emp_conta_debito_amort':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
            $stFiltro .= "\n ( pc.cod_estrutural LIKE '2.1.%' OR pc.cod_estrutural LIKE '2.2.%' ) AND ";

            if ($arRequest['stCodEstrutural']) {
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            }
            if ($arRequest['stDescricao']) {
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            }
            if ($arRequest['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
            }
        break;

        case 'emp_conta_credito_amort':
            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
            $stFiltro .= "\n pc.cod_estrutural LIKE '6.1.3.3.%' AND ";

            if ($arRequest['stCodEstrutural']) {
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            }
            if ($arRequest['stDescricao']) {
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";
            }
            if ($arRequest['inCodEntidade']) {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
            }
        break;

        case 'banco':
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "\n pb.cod_banco is not null AND ";
                $stFiltro .= "\n pc.cod_estrutural LIKE '1.1.1.%' AND ";
                $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
                if ($arRequest['inCodEntidade']) {
                    $stFiltro .= "\n   pb.cod_entidade in ( ".$arRequest['inCodEntidade'].") AND ";
                }
            }
        break;

        case "contaSinteticaAtivoPermanente":
            if ( $arRequest['stCodEstrutural'] ) {
                $inCodEstrutural = $arRequest['stCodEstrutural'];
            }else{
                $inCodEstrutural = '1.2.3';
            }
            $obRegra->setCodEstrutural($inCodEstrutural);
            $obRegra->listarContaAnaliticaAtivoPermanente ($rsLista,"");
        break;

        case "contaContabilDepreciacaoAcumulada":
            if ( $arRequest['stCodEstrutural'] ) {
                $inCodEstrutural = $arRequest['stCodEstrutural'];
            }else{
                $inCodEstrutural = '1.2.3.8.1';
            }
            $obRegra->setCodEstrutural($inCodEstrutural);
            $obRegra->listarContaAnaliticaAtivoPermanente ($rsLista,"");
        break;

        case "contaContabilBaixaBem":
            if ( $arRequest['stCodEstrutural'] ) {
                $inCodEstrutural= $arRequest['stCodEstrutural'];
            }else{
                $inCodEstrutural = '3';
            }
            $obRegra->setCodEstrutural($inCodEstrutural);
            $obRegra->listarContaAnaliticaAtivoPermanente ($rsLista,"");
        break;

        case "contaContabilAlienacao":
            if ( $arRequest['stCodEstrutural'] ) {
                $inCodEstrutural = $arRequest['stCodEstrutural'];
            }else{
                if ( $arRequest['campoNum'] == "inCodContaVPAAlienacao" )
                    $inCodEstrutural = '4.6.2.2.1';
                else
                    $inCodEstrutural = '3.6.2.2.1';
            }

            $obRegra->setCodEstrutural($inCodEstrutural);
            $obRegra->listarContaAnaliticaAtivoPermanente ($rsLista,"");
        break;

        case 'sintetica':
            require_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoConta.class.php';

            $stCondicao = " AND plano_conta.escrituracao = '".$arRequest['tipoBusca']."' \n";
            $stOrdem    = " ORDER BY plano_conta.cod_estrutural ASC \n";

            $obTContabilidadePlanoConta = new TContabilidadePlanoConta();
            $obTContabilidadePlanoConta->setDado('exercicio'     , Sessao::getExercicio()      );
            $obTContabilidadePlanoConta->setDado('cod_estrutural', $arRequest['stCodEstrutural']);

            $obTContabilidadePlanoConta->recuperaContaSintetica($rsLista, $stCondicao, $stOrdem, $boTransacao);
        break;

        case 'plano_contas_PCASP':
            $inExercicio = Sessao::getExercicio();

            $stFiltro  = "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pc.exercicio = '" . $inExercicio . "' AND ";

            if( $arRequest['stCodEstrutural'] )
                $stFiltro .= "\n pc.cod_estrutural like publico.fn_mascarareduzida('".$arRequest['stCodEstrutural']."')||'%' AND ";
            if( $arRequest['stDescricao'] )
                $stFiltro .= "\n lower(pc.nom_conta) like lower('%".$arRequest['stDescricao']."%') AND ";

            if (Sessao::getExercicio() > '2012') {
                $stFiltro .= "\n   pc.cod_estrutural like '8.9.%'  AND ";
            } else {
                $stFiltro .= "\n   pc.cod_estrutural like '2.9.9.1.%'  AND ";
            }
        break;

        case "contaContabilReavaliacao":
            $inCodEstrutural = "";

            #$arRequest['tipoBusca2'] -> Natureza = 1 - Bens Móveis
            #$arRequest['tipoBusca2'] -> Natureza = 2 - Bens Imóveis
            if ( $arRequest['tipoBusca2'] == 1 )
                $inCodEstrutural = '4.6.1.1.1.01';
            else if ( $arRequest['tipoBusca2'] == 2 )
                $inCodEstrutural = '4.6.1.1.1.02';
            else
                $rsLista = new RecordSet();

            if( $inCodEstrutural != "" ){
                $obRegra->setCodEstrutural($inCodEstrutural);
                $obRegra->listarContaAnaliticaAtivoPermanente ($rsLista,"");
            }
        break;
    }

    if ($stFiltro) {
        $stFiltro = " WHERE " . substr( $stFiltro, 0, strlen( $stFiltro ) -4 );
        $stOrder = isset($stOrder) ?  $stOrder : 'cod_estrutural';
        $boTransacao = "";

        $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsLista, $stSQLRelacionamentoRecurso.$stFiltro, $stOrder, $boTransacao );
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

if ($arRequest['tipoBusca'] == 'estrutural') {
    $obLista->ultimaAcao->addCampo("1","cod_estrutural");
} else if ($arRequest['tipoBusca'] == 'sintetica') {
    $obLista->ultimaAcao->addCampo("1","cod_estrutural");
}else {
    $obLista->ultimaAcao->addCampo("1","cod_plano");
}
$obLista->ultimaAcao->addCampo("2","");
$obLista->ultimaAcao->addCampo("3","nom_conta");
if ($arRequest['tipoBusca'] == 'con_relac_conta_entidade') {
    $obLista->ultimaAcao->addCampo("4","cod_estrutural");
}
$obLista->commitAcao();

$obLista->show();

?>