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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 04/07/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30762 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.25
*/

/*
$Log$
Revision 1.4  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioSuplementacoes.class.php"  );

$obRRelatorio   = new RRelatorio;
$obRegra        = new ROrcamentoRelatorioSuplementacoes();

//seta elementos do filtro
$stFiltro = "";
$stEntidades = "";

$arFiltro = Sessao::read('filtroRelatorio');

//seta elementos do filtro para ENTIDADE
if ($arFiltro['inCodEntidade'] != "") {
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stEntidades .= $valor.",";
    }
    $stEntidades = substr( $stEntidades, 0, strlen($stEntidades) - 1 ) ;
    $obRegra->setCodEntidade($stEntidades);
}
$obRegra->setExercicio              ( Sessao::getExercicio() );
$obRegra->setCodDespesa     	    ( $arFiltro['inCodDespesa'] );
$obRegra->setCodNorma	     	    ( $arFiltro['inCodNorma'] );
$obRegra->setTipoSuplementacao	    ( $arFiltro['inCodTipoSuplementacao'] );
$obRegra->setTipoRelatorio	    ( $arFiltro['stTipoRelatorio'] );
if ($arFiltro['stTipoRelatorio']=='anuladas') {
    $obRegra->setSituacao		    ( 'Anulada' );
} else {
    $obRegra->setSituacao		    ( $arFiltro['stSituacao'] );
}
$obRegra->setDataInicial            ( $arFiltro['stDataInicial'] );
$obRegra->setDataFinal              ( $arFiltro['stDataFinal'] );

$obRegra->geraRecordSet( $arRecordSet, $arRecordSet1, $arRecordSet2, $arRecordSet3 );

Sessao::write('arRecordSet',$arRecordSet);
Sessao::write('arRecordSet1',$arRecordSet1);
Sessao::write('arRecordSet2',$arRecordSet2);
Sessao::write('arRecordSet3',$arRecordSet3);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioSuplementacao.php" );

?>
