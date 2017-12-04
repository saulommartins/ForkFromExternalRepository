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
    * Data de Criação   : 28/04/2005

    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Id: OCAnexo13.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"            );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioAnexo13.class.php" );

$obRRelatorio    = new RRelatorio;
$obRContabilidadeAnexo13      = new RContabilidadeRelatorioAnexo13;

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

//seta elementos do filtro
$stFiltro = "";
$arFiltro = Sessao::read('filtroRelatorio');
 foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
 $stEntidades .= $valor.",";
 }
 $stEntidades = trim(substr( $stEntidades, 0, strlen($stEntidades) - 1 ));

if ( $rsEntidades->getNumLinhas() == $inCount ) {
    $arFiltro['relatorio'] = "Consolidado";
} else {
    $arFiltro['relatorio'] = "";
}

$obRContabilidadeAnexo13->setExercicio               ( Sessao::getExercicio());
$obRContabilidadeAnexo13->setDataInicial             ( $arFiltro['stDataInicial'] );
$obRContabilidadeAnexo13->setDataFinal               ( $arFiltro['stDataFinal'] );
$obRContabilidadeAnexo13->setTipoRelatorio           ( $arFiltro['stTipoRelatorio'] );
$obRContabilidadeAnexo13->setCodDemonstracaoDespesa  ( $arFiltro['inCodDemonstracaoDespesa'] );
$obRContabilidadeAnexo13->setEntidades               ( $stEntidades);
$obRContabilidadeAnexo13->geraRecordSet              ( $rsAnexo13 );
Sessao::remove('rsAnexo13');
Sessao::write('rsAnexo13', $rsAnexo13);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioAnexo13.php" );

?>
