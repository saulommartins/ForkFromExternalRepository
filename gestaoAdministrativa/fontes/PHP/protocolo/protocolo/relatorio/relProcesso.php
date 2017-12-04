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
    * Arquivo de instância para Relatorio.
    * Data de Criação: 25/03/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-01.06.99

    $Id: relProcesso.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );

$preview = new PreviewBirt(1,5,11);
$preview->setVersaoBirt( '2.5.0' );

$preview->addParametro ( 'pExercicioSessao' , Sessao::getExercicio() );

if ($_REQUEST['codProcesso'] != "") {
    $arCodProcessoExercicio = explode("/",$_REQUEST['codProcesso']);
    $codProcesso = $arCodProcessoExercicio[0];
    $anoExercicio = $arCodProcessoExercicio[1];

    $preview->addParametro( 'pCodProcesso', $codProcesso );
    $preview->addParametro( 'pAnoExercicio', $anoExercicio );

} else {
    $preview->addParametro( 'pCodProcesso', '' );
    $preview->addParametro( 'pAnoExercicio', '' );
}

if ($_REQUEST['codClassificacao'] != "xxx" && $_REQUEST['codClassificacao'] != "") {
    $preview->addParametro( 'pCodClassificacao', $_REQUEST['codClassificacao'] );
} else {
    $preview->addParametro( 'pCodClassificacao', '' );
}

if ($_REQUEST['codAssunto'] != "xxx" && $_REQUEST['codAssunto'] != "") {
    $preview->addParametro( 'pCodAssunto', $_REQUEST['codAssunto'] );
} else {
    $preview->addParametro( 'pCodAssunto', '' );
}

if ($_REQUEST['resumo'] != "") {
    $preview->addParametro( 'pResumo', $_REQUEST['resumo'] );
} else {
    $preview->addParametro( 'pResumo', '' );
}

if ($_REQUEST['codOrgao'] != "") {
    $preview->addParametro( 'pCodOrgao', $_REQUEST['codOrgao'] );
} else {
    $preview->addParametro( 'pCodOrgao', '' );
}

if ($_REQUEST['codSituacao']) {
    $codSituacao = explode(".",$_REQUEST['codSituacao']);
    if (is_array($codSituacao)) {
        $sqlCodSituacao = " AND ( ";
        $verS =  count($codSituacao);

        while (list($key, $val) = each($codSituacao)) {
            $sqlCodSituacao .= "sw_processo.cod_Situacao = '".$val."' OR ";
        }

        $sqlCodSituacao = substr( $sqlCodSituacao , 0 , (strlen($sqlCodSituacao) - 3));
        $sqlCodSituacao .= ")";
    }
    $preview->addParametro( 'pCodSituacao', $sqlCodSituacao);
} else {
    $preview->addParametro( 'pCodSituacao', '');
}

if (strlen($_REQUEST['numCgm']) > 0) {
    $preview->addParametro( 'pNumCgm', $_REQUEST['numCgm']);
} else {
    $preview->addParametro( 'pNumCgm', '');
}

if ( (strlen($_REQUEST['dataInicial'])>0) and (strlen($_REQUEST['dataFinal'])==0) ) {
    $dtInicial = dataToSql($_REQUEST['dataInicial']);
    $sqlPeriodo = " AND sw_processo.timestamp >= '".$dtInicial."' ";
    $preview->addParametro( 'pData', $sqlPeriodo);
} elseif ( (strlen($_REQUEST['dataInicial'])==0) and (strlen($_REQUEST['dataFinal'])>0) ) {
    $dtFinal = dataToSql($_REQUEST['dataFinal']);
    $sqlPeriodo .= " AND sw_processo.timestamp <= '".$dtFinal."' ";
    $preview->addParametro( 'pData', $sqlPeriodo);
} elseif ( (strlen($_REQUEST['dataInicial'])>0) and (strlen($_REQUEST['dataFinal'])>0) ) {
    $dtInicial = dataToSql($_REQUEST['dataInicial']);
    $dtFinal = dataToSql($_REQUEST['dataFinal'])." 23:59:59.999";
    $sqlPeriodo .= " AND sw_processo.timestamp Between '".$dtInicial."' AND '".$dtFinal."' ";
    $preview->addParametro( 'pData', $sqlPeriodo);
} else {
    $preview->addParametro( 'pData', '');
}

$arOrdem = Sessao::read('arOrdem');
$preview->addParametro ( 'pOrdem' , " ORDER BY ".$arOrdem[Sessao::read('ordem')]." ASC " );

$preview->preview();
