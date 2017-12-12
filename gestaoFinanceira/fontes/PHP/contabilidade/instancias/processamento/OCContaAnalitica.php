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
* Arquivo instância para popup de busca de contas analíticas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 32397 $
$Name:  $
$Author: lbbarreiro $
$Date: 2007-10-31 15:55:22 -0200 (Qua, 31 Out 2007) $

 $Id: OCContaAnalitica.php 64153 2015-12-09 19:16:02Z evandro $

Casos de uso: uc-02.02.02,uc-02.04.28,uc-02.02.31,uc-02.03.28
*/
header ("Content-Type: text/html; charset=utf-8");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCodEntidades = '';
$stFiltro = isset($stFiltro) ? $stFiltro : '';
$stOrder  = isset($stOrder)  ? $stOrder  : '';
$boTransacao = isset($boTransacao)  ? $boTransacao  : false;
if (is_array($request->get($request->get('stNomSelectMultiplo')))) {
    $stCodEntidades = implode(',',$_GET[$_GET['stNomSelectMultiplo']]);
} else
    $stCodEntidades = $_GET['inCodEntidade'];

switch ( $request->get('stCtrl') ) {
    case 'codigoReduzidoBanco':
    case 'somente_contas_analiticas':
    case 'conta_analitica':
        if ($_GET[$_GET['stNomCampoCod']]) {
           include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php");
           $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
           $obRContabilidadePlanoContaAnalitica->setCodPlano( $_GET[$_GET['stNomCampoCod']] );
           $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
           $obRContabilidadePlanoContaAnalitica->consultar();
           $stDescricao = $obRContabilidadePlanoContaAnalitica->getNomConta();
           if (!$stDescricao) {
               echo "alertaAviso('@Conta inválida (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
           }
        }
    break;

    case 'tes_deposito':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= " ( pb.cod_banco IS NOT NULL
                             AND pb.cod_entidade IN ( ".$request->get('inCodEntidade').")
                             AND ( pc.cod_estrutural LIKE '1.1.1.%'
                                OR pc.cod_estrutural LIKE '1.2.2.3%' )) AND ";
            } else {
                $stFiltro .= " (( pb.cod_banco IS NOT NULL
                              AND pb.cod_entidade IN (".$stCodEntidades.")
                              AND ( pc.cod_estrutural LIKE '1.1.1.1.2.%'
                                 OR pc.cod_estrutural LIKE '1.1.1.1.1.%'
                                 OR pc.cod_estrutural LIKE '1.1.1.1.4.%'
                                 OR pc.cod_estrutural LIKE '1.2.2.3%'
                              ))
                            OR pc.cod_estrutural LIKE '1.1.5.%' ) AND ";
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para depósitos (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }
       }
    break;

    case 'tes_retirada':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= " ( pb.cod_banco IS NOT NULL
                             AND pb.cod_entidade IN ( ".$request->get('inCodEntidade').")
                             AND ( pc.cod_estrutural LIKE '1.1.1.%'
                                OR pc.cod_estrutural LIKE '1.2.2.3%' )) AND ";
            } else {
                $stFiltro .= " (( pb.cod_banco IS NOT NULL
                              AND pb.cod_entidade IN (".$stCodEntidades.")
                              AND ( pc.cod_estrutural LIKE '1.1.1.1.1.%'
                                 OR pc.cod_estrutural LIKE '1.1.1.1.2.%'
                                 OR pc.cod_estrutural LIKE '1.1.1.1.4.%'
                                 OR pc.cod_estrutural LIKE '1.2.2.3%'
                               ))
                             OR pc.cod_estrutural LIKE '1.1.5.%' ) AND ";
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para retiradas (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }

        }
    break;

    case 'tes_aplicacao_entrada':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "( pb.cod_banco IS NOT NULL
                            AND pb.cod_entidade IN ( ".$request->get('inCodEntidade').")
                            AND ( pc.cod_estrutural LIKE '1.1.1.%'
                               OR pc.cod_estrutural LIKE '1.1.4.%'
                               OR pc.cod_estrutural LIKE '1.2.2.3%'
                             )) AND ";
            } else {
                $stFiltro .= "((   pb.cod_banco IS NOT NULL
                               AND pb.cod_entidade IN (".$stCodEntidades.")
                               AND ( pc.cod_estrutural LIKE '1.1.1.1.3.%'
                                  OR pc.cod_estrutural LIKE '1.1.1.1.4.%'
                                  OR pc.cod_estrutural LIKE '1.1.1.1.5.%'
                                  OR pc.cod_estrutural LIKE '1.2.2.3%'
                              ))
                             OR pc.cod_estrutural like '1.1.5.%' ) AND ";  
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para aplicações(".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }

        }
    break;

    case 'tes_aplicacao_contrapartida':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= " ( pb.cod_banco IS NOT NULL
                             AND pb.cod_entidade IN ( ".$request->get('inCodEntidade').")
                             AND ( pc.cod_estrutural LIKE '1.1.1.%'
                                OR pc.cod_estrutural LIKE '1.1.4.%'
                                OR pc.cod_estrutural LIKE '1.2.2.3%')) AND ";
            } else {
                $stFiltro .= " (( pb.cod_banco IS NOT NULL
                              AND pb.cod_entidade IN (".$stCodEntidades.")
                              AND ( pc.cod_estrutural LIKE '1.1.1.1.2.%'
                                 OR pc.cod_estrutural LIKE '1.1.1.1.1.%'
                                 OR pc.cod_estrutural LIKE '1.1.1.1.4.%'
                                 OR pc.cod_estrutural LIKE '1.1.1.1.5.%'
                              ))
                             OR pc.cod_estrutural like '1.1.5.%' ) AND ";
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para contrapartidas (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }

        }
    break;

    case 'tes_resgate_entrada':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "  ( pb.cod_banco IS NOT NULL
                              AND pb.cod_entidade IN ( ".$request->get('inCodEntidade').")
                              AND ( pc.cod_estrutural LIKE '1.1.1.%'
                                 OR pc.cod_estrutural LIKE '1.1.4.%'
                                 OR pc.cod_estrutural LIKE '1.2.2.3%' ))
                              AND ";
            } else {
                $stFiltro .= " (( pb.cod_banco IS NOT NULL
                              AND pb.cod_entidade IN (".$stCodEntidades.")
                              AND ( pc.cod_estrutural LIKE '1.1.1.1.2.%'
                                 OR pc.cod_estrutural LIKE '1.1.1.1.4.%'
                                 OR pc.cod_estrutural LIKE '1.1.1.1.5.%'
                                 OR pc.cod_estrutural LIKE '1.2.2.3%' 
                               ))
                              OR pc.cod_estrutural like '1.1.5.%' ) AND ";
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para resgates (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }

        }
    break;

    case 'tes_resgate_contrapartida':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= " ( pb.cod_banco IS NOT NULL
                             AND pb.cod_entidade IN ( ".$request->get('inCodEntidade').")
                             AND ( pc.cod_estrutural like '1.1.1.%'
                                OR pc.cod_estrutural like '1.1.4.%'
                                OR pc.cod_estrutural LIKE '1.2.2.3%' )) AND ";
            } else {
                $stFiltro .= " (( pb.cod_banco is not null
                              AND pb.cod_entidade in (".$stCodEntidades.")
                              AND ( pc.cod_estrutural like '1.1.1.1.3.%'
                                 OR pc.cod_estrutural like '1.1.1.1.4.%'
                                 OR pc.cod_estrutural like '1.1.1.1.5.%'
                                 OR pc.cod_estrutural LIKE '1.2.2.3%'
                               ))
                              OR pc.cod_estrutural like '1.1.5.%' ) AND ";
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para contrapartidas (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }

        }
    break;

    case 'tes_pagamento_extra_despesa':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
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
                                OR pc.cod_estrutural like '3.5.%' ) AND ";
            } else {
                $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%' OR pc.cod_estrutural like '2.1.1.%'  OR  pc.cod_estrutural like '5%' OR pc.cod_estrutural like '6%'  ) AND ";
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para despesa (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }
       }
    break;

    case 'emp_conta_lancamento_adiantamentos':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";

            if (Sessao::getExercicio() > '2012') {
                    $stFiltro .= "\n   pc.cod_estrutural like '7.1.1.1.%'  AND ";
            } else {
                    $stFiltro .= "\n pc.cod_estrutural like '1.9.9.1%' AND ";
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }
       }
    break;

    case 'tes_pagamento_extra_caixa_banco':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "\n( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
                $stFiltro .= "\n   ( pc.cod_estrutural like '1.1.1.%'  OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.4.%' )) AND ";
            } else {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in (".$stCodEntidades.") AND ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' ) OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para caixa/banco (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }

        }
    break;

    case 'tes_pagamento_extra_caixa_banco_recurso_fixo':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stSQLRelacionamentoRecurso = "
                LEFT JOIN contabilidade.plano_recurso
                       ON plano_recurso.exercicio = pa.exercicio
                      AND plano_recurso.cod_plano = pa.cod_plano
            ";
            
            $stFiltro .= "\n plano_recurso.cod_recurso = 100 AND ";
            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "\n( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$_REQUEST['inCodEntidade'].") AND ";
                $stFiltro .= "\n   ( pc.cod_estrutural like '1.1.1.%'  OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.4.%' )) AND ";
            } else {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in (".$stCodEntidades.") AND ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' ) OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stSQLRelacionamentoRecurso.$stFiltro, $stOrder, $boTransacao );
            
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else {
                    if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao ) == 11 && SistemaLegado::pegaConfiguracao('cod_municipio', 2, Sessao::getExercicio(), $boTransacao ) == 79 && SistemaLegado::comparaDatas($stDataFinalAno, $stDataAtual, true)){
                        $stJs .= "d.getElementById('inCodPlanoCredito').readOnly = false;\n";
                        $stJs .= "d.getElementById('imgPlanoCredito').style.display = 'inline';\n";
                    }
                    $stJs .= "alertaAviso('@Conta inválida para caixa/banco (".$_GET[$_GET['stNomCampoCod']]."). Conta Bancária deve ser do Recurso 100.','form','erro','".Sessao::getId()."'); \n";
                    echo $stJs;
                }
            }

        }
    break;

    case 'tes_arrecadacao_extra_receita':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
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
                $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%' OR pc.cod_estrutural like '2.1.1.%'
                                OR pc.cod_estrutural like '5%'      OR pc.cod_estrutural like '6%' ) AND ";
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para receita (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }
       }
    break;

    case 'gpc_parametros_rd_extra':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
            $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%' OR pc.cod_estrutural like '2.1.1.%' OR pc.cod_estrutural like '2.1.2.%'
                            OR pc.cod_estrutural like '5%'      OR pc.cod_estrutural like '6%' ) AND ";

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para receita (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }
       }
    break;

    case 'tes_arrecadacao_extra_caixa_banco':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "\n( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in ( ".$stCodEntidades.") AND ";
                $stFiltro .= "\n   ( pc.cod_estrutural like '1.1.1.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.4.%' )) AND ";
            } else {
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                $stFiltro .= "\n   pb.cod_entidade in (".$stCodEntidades.") AND ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' ) OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.5.%' ) AND ";
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para caixa/banco (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }

        }
    break;

    case 'con_conta_lancamento_rp_credito':

        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $inExercicio =  Sessao::getExercicio();

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '". $inExercicio ."' AND ";
            $stFiltro .= "\n (pc.cod_estrutural like '2.2%' OR pc.cod_estrutural like '2.1.2.%') ";

            $stFiltro = ($stFiltro) ? " WHERE " . $stFiltro : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }

        }
    break;

    case 'con_conta_lancamento_rp_debito':

        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $inExercicio =  Sessao::getExercicio();

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '". $inExercicio ."' AND ";
            $stFiltro .= "\n   pc.cod_estrutural like '2.1.2%'  ";

            $stFiltro = ($stFiltro) ? " WHERE " . $stFiltro : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }

        }
    break;

    case 'con_relac_conta_entidade':

        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica;

            $inExercicio =  Sessao::getExercicio();

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '". $inExercicio ."' AND ";
            $stFiltro .= "\n pc.cod_estrutural like '2.%' ";

            $stFiltro = ($stFiltro) ? " WHERE " . $stFiltro : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }

        }
    break;

    case 'tes_contrapartida_lancamento':

        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $inExercicio =  Sessao::getExercicio();

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '". $inExercicio ."' AND ";

            if (Sessao::getExercicio() > '2012') {
                $stFiltro .= "\n   pc.cod_estrutural like '8.1.1.1.%' AND ";
            } else {
                $stFiltro .= "\n   pc.cod_estrutural like '2.9.9.1.%' AND ";
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }

        }
    break;

    case 'emp_retencao_op_extra':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;

            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.1.3.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '1.2.1.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.1.1.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.1.2.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.1.8.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.1.9.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.2.1.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.2.2.%' ) AND ";
            } else {
                $stFiltro .= "\n ( pc.cod_estrutural like '1.1.2.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '2.1.1.%' OR ";
                $stFiltro .= "\n   pc.cod_estrutural like '5.%' OR     ";
                $stFiltro .= "\n   pc.cod_estrutural like '6.%' ) AND ";
            }

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para Retenção (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }
        }
    break;

    case 'emp_conta_caixa':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php"        );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;
            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '".Sessao::getExercicio()."' AND ";
            $stFiltro .= "\n(( pb.cod_banco is not null AND ";
            $stFiltro .= "\n   pb.cod_entidade in (".$stCodEntidades.") AND ";
            $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.1.1.%' ) ) AND";

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ($rsRecordSet->getCampo('cod_plano') <> "")
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }

        }
    break;

    case 'emp_conta_debito_incorp':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO . "TContabilidadePlanoAnalitica.class.php" );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;
            $inExercicio =  Sessao::getExercicio();
            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '". $inExercicio ."' AND ";
            $stFiltro .= "\n ( pc.cod_estrutural like '1.2.3.%' OR pc.cod_estrutural like '1.4.%' ) ";
            $stFiltro = ($stFiltro) ? " WHERE " . $stFiltro : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ( $rsRecordSet->getCampo('cod_plano') <> "" )
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para débito incorporação (" . $_GET[$_GET['stNomCampoCod']] . ")', 'form', 'erro', '" . Sessao::getId() . "');";
            }
        }
    break;

    case 'emp_conta_debito_amort':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO . "TContabilidadePlanoAnalitica.class.php" );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;
            $inExercicio =  Sessao::getExercicio();
            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '". $inExercicio ."' AND ";
            $stFiltro .= "\n ( pc.cod_estrutural like '2.1.%' OR pc.cod_estrutural like '2.2.%' )   ";
            $stFiltro = ($stFiltro) ? " WHERE " . $stFiltro : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ( $rsRecordSet->getCampo('cod_plano') <> "" )
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso(\"@Conta inválida para débito amortização (".$_GET[$_GET['stNomCampoCod']].")\",'form','erro','".Sessao::getId()."');";
            }
        }
    break;

    case 'emp_conta_credito_incorp':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO . "TContabilidadePlanoAnalitica.class.php" );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;
            $inExercicio =  Sessao::getExercicio();
            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '". $inExercicio ."' AND ";
            $stFiltro .= "\n (pc.cod_estrutural LIKE '6.1.3.0.%' OR pc.cod_estrutural LIKE '6.1.3.1.%' OR pc.cod_estrutural LIKE '6.1.3.2.%' )  ";
            $stFiltro = ($stFiltro) ? " WHERE " . $stFiltro : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ( $rsRecordSet->getCampo('cod_plano') <> "" )
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para crédito incorporação (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }
        }
    break;

    case 'emp_conta_credito_amort':
        if ($_GET[$_GET['stNomCampoCod']]) {
            include_once ( CAM_GF_CONT_MAPEAMENTO . "TContabilidadePlanoAnalitica.class.php" );
            $obTContabilidadePlanoAnalitica        = new TContabilidadePlanoAnalitica;
            $inExercicio =  Sessao::getExercicio();
            $stFiltro .= "\n pa.cod_plano is not null AND ";
            $stFiltro .= "\n pa.cod_plano = ".$_GET[$_GET['stNomCampoCod']]."  AND ";
            $stFiltro .= "\n pc.exercicio = '". $inExercicio ."' AND ";
            $stFiltro .= "\n pc.cod_estrutural LIKE '6.1.3.3.%' ";
            $stFiltro = ($stFiltro) ? " WHERE " . $stFiltro : "";
            $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';
            $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if (!$obErro->ocorreu()) {
                if ( $rsRecordSet->getCampo('cod_plano') <> "" )
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    echo "alertaAviso('@Conta inválida para crédito amortização (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }
        }
    break;
}
$js = " retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stDescricao."');";

echo $js;

?>
