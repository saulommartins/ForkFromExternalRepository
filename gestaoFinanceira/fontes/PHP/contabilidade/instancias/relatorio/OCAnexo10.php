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
    * Data de Criação   : 06/10/2004

    * @author Desenvolvedor: Anderson Buzo
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    * $Id: OCAnexo10.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"            );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioAnexo10.class.php" );

$obRRelatorio    = new RRelatorio;
$obRAnexo10      = new RContabilidadeRelatorioAnexo10;

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

//seta elementos do filtro
$stFiltro = "";
$arFiltro = Sessao::read('filtroRelatorio');
if ($arFiltro['inCodEntidade'] != "") {
    $stFiltro = "'";
    $inCount = 0;
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stFiltro .= $valor.",";
        $inCount++;
    }
    if ($stFiltro != "") {
        $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 1 ) . "'";
    }
    $obRAnexo10->setFiltro( $stFiltro );
}
if ( $rsEntidades->getNumLinhas() == $inCount ) {
    $arFiltro['relatorio'] = "Consolidado";
} else {
    $arFiltro['relatorio'] = "";
}
$obRAnexo10->setExercicio (Sessao::getExercicio());
$obRAnexo10->setDataInicial( $arFiltro['stDataInicial'] );
$obRAnexo10->setDataFinal( $arFiltro['stDataFinal'] );
$obRAnexo10->geraRecordSet( $rsAnexo10 );
Sessao::remove('rsAnexo10');
Sessao::write('rsAnexo10', $rsAnexo10);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioAnexo10.php" );

?>
