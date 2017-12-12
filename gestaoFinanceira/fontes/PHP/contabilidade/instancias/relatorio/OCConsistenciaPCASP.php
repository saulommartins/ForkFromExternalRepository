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
    * Arquivo Oculo para Consistência PCASP
    * Data de Criação   : 25/09/2013

    * @author Analista:  Sergio Luiz dos Santos
    * @author Desenvolvedor: Jean Silva

    * @ignore

    * $Id: FLConsistenciaPCASP.php 52880 2012-08-28 19:15:58Z tonismar $

    * Casos de uso: uc-02.02.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioBalanceteVerificacao.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php" );

$obRRelatorio                        = new RRelatorio;
$obRegra                             = new RContabilidadeRelatorioBalanceteVerificacao;

$obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
$obRContabilidadePlanoContaAnalitica->setExercicio ( Sessao::getExercicio() );
$obRContabilidadePlanoContaAnalitica->recuperaMascaraConta( $stMascara );

//seta elementos do filtro
$stFiltro = "";

$arFiltro = Sessao::read('filtroRelatorio');
//seta elementos do filtro para ENTIDADE
if ($arFiltro['inCodEntidade'] != "") {
    $stFiltro .= "\n cod_entidade IN  (";
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stFiltro .= $valor." , ";
    }
    $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 2 ) . ") AND ";
} else {
    $stFiltro .= $arFiltro['stTodasEntidades'];
}

foreach ($arFiltro as $key => $valor) {
    if (substr($key, 0, 6) == "grupo_") {
        $stGrupos .= substr($key,6,99).", ";
        $boGrupo = true;
    }
    if (substr($key, 0, 8) == "sistema_") {
        $stSistemas .= substr($key,8,99).", ";
        $boSistema = true;
    }
}

if ($boGrupo)   $stFiltro .= "\n substr(cod_estrutural,1,1)::integer in (".substr($stGrupos,0,strlen($stGrupos)-2).") AND ";
if ($boSistema) $stFiltro .= "\n cod_sistema in (".substr($stSistemas,0,strlen($stSistemas)-2).") AND ";

if ($arFiltro['stCodEstruturalInicial'] or $arFiltro['stCodEstruturalFinal']) {
    if (!$arFiltro['stCodEstruturalInicial']) {
        $stCodEstruturalInicial = str_replace(9,'0',$stMascara);
    } else $stCodEstruturalInicial = $arFiltro['stCodEstruturalInicial'];

    if ($arFiltro['stCodEstruturalFinal']) {
        $arCodEstruturalFinal = explode( '.' ,$arFiltro['stCodEstruturalFinal'] );
        $inSize = sizeof($arCodEstruturalFinal);
        for ($inSize -1; $inSize >= 0 ; $inSize--) {
            if ($arCodEstruturalFinal[$inSize-1] == 0) {
                $arCodEstruturalFinal[$inSize-1] = str_pad(9,strlen($arCodEstruturalFinal[$inSize-1]),'9',STR_PAD_LEFT);
            } else {
                break;
            }
        }
        $stCodEstruturalFinal = implode('.',$arCodEstruturalFinal);
    } else {
        $stCodEstruturalFinal = $stMascara;
    }

    $stFiltro .= "\n cod_estrutural BETWEEN \'".$stCodEstruturalInicial."\' AND \'".$stCodEstruturalFinal."\' AND ";
}

if ($stFiltro) $stFiltro = substr($stFiltro, 0, strlen($stFiltro)-4);

$obRegra->setFiltro     ( $stFiltro );
$obRegra->setExercicio  ( Sessao::getExercicio() );
$obRegra->setDtInicial  ( $arFiltro['stDataInicial'] );
$obRegra->setDtFinal    ( $arFiltro['stDataFinal']   );
$obRegra->setEstilo     ( $arFiltro['stEstiloConta'] );

$obRegra->geraRecordSet( $rsRecordSet );
Sessao::write('rsRecordSet', $rsRecordSet);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioBalanceteVerificacao.php" );
?>
