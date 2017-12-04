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
    * Página oculta pra pré-processar o relatorio
    * Data de Criação   : 25/09/2004

    * @author Desenvolvedor: Gustavo Passos Tourinho
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.13

*/

/*
$Log$
Revision 1.6  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo6.class.php" );

$obRRelatorio       = new RRelatorio;
$obROrcamentoAnexo6 = new ROrcamentoRelatorioAnexo6;

$arFiltro = Sessao::read('filtroRelatorio');

//seta elementos do filtro
$stFiltro = "";
$inCodEntidade = $arFiltro['inCodEntidade'];
$stDotacaoOrcamentaria = $arFiltro['stDotacaoOrcamentaria'];

if ($inCodEntidade != "") {
    $stFiltro = " AND cod_entidade IN  (";
    foreach ($inCodEntidade as $key => $valor) {
        $stEntidades .= $valor.",";
    }
    if ($stEntidades != "") {
        $stEntidades = substr( $stEntidades, 0, strlen($stEntidades) - 1 );
    }
    $stFiltro .= $stEntidades . ")";
}

//seta elementos do filtro para ORGAO E/OU UNIDADE
if ($stDotacaoOrcamentaria != "") {
    $arCodigos = explode (".", $stDotacaoOrcamentaria);
    $inCodOrgao = (int) $arCodigos[0];
    $inCodUnidade = (int) $arCodigos[1];

    if ($inCodOrgao) {
        $stFiltro .= " AND num_orgao = ".$inCodOrgao." ";
        $obROrcamentoAnexo6->setOrgao ( $inCodOrgao );
    }

    if ($inCodUnidade) {
        $stFiltro .= " AND num_unidade = ".$inCodUnidade." ";
        $obROrcamentoAnexo6->setUnidade ( $inCodUnidade );
   }
}

if ($stFiltro != "") {
    $obROrcamentoAnexo6->setFiltro( $stFiltro );
}

$obROrcamentoAnexo6->setSituacao( $arFiltro['stSituacao'] );
$obROrcamentoAnexo6->setTipoRelatorio( $arFiltro['stTipoRelatorio'] );
$obROrcamentoAnexo6->setDataInicial( $arFiltro['stDataInicial'] );
$obROrcamentoAnexo6->setDataFinal( $arFiltro['stDataFinal'] );
$obROrcamentoAnexo6->setExercicio ( Sessao::getExercicio() );
$obROrcamentoAnexo6->setEntidades($stEntidades);
$obROrcamentoAnexo6->geraRecordSet( $arAnexo6, $arCabecalho, $rsTotal, $arEntidade );

Sessao::write('arAnexo6',$arAnexo6);
Sessao::write('arCabecalho', $arCabecalho);
Sessao::write('rsTotal', $rsTotal);
Sessao::write('arEntidade',$arEntidade);
Sessao::write('stSituacao', Sessao::read('stSituacao') ) ;

//sessao->transf5 = array();
//sessao->transf5[0] = $arAnexo6;
//sessao->transf5[1] = $arCabecalho;
//sessao->transf5[2] = $rsTotal;
//sessao->transf5[3] = $arEntidade;
//sessao->transf5[4] = //sessao->filtro['stSituacao'];

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioAnexo6.php" );
?>
