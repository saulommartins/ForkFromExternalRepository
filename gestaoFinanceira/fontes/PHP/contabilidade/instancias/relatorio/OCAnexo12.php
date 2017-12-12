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
    * Data de Criação   : 27/04/2005

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: OCAnexo12.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioAnexo12.class.php" );

$obRRelatorio    = new RRelatorio;
$obROrcamentoAnexo12      = new RContabilidadeRelatorioAnexo12;

//seta elementos do filtro
$stFiltro = "";
$arFiltro = Sessao::read('filtroRelatorio');
if ($arFiltro['inCodEntidade'] != "") {
    $stEntidades = "";
    $inCount = 0;
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stEntidades .= $valor.",";
        $inCount++;
    }
    if ($stEntidades != "") {
        $stEntidades = substr( $stEntidades, 0, strlen($stEntidades) - 1 ) . "";
    }
    $obROrcamentoAnexo12->setEntidades( $stEntidades );
}

$obROrcamentoAnexo12->setExercicio  ( Sessao::getExercicio()                  );
$obROrcamentoAnexo12->setDataInicial( $arFiltro['stDataInicial']      );
$obROrcamentoAnexo12->setDataFinal  ( $arFiltro['stDataFinal']        );
$obROrcamentoAnexo12->setSituacao   ( $arFiltro['stTipoRelatorio']  );
$obROrcamentoAnexo12->geraRecordSet ( $rsAnexo12 );
Sessao::remove('rsAnexo12');
Sessao::write('rsAnexo12', $rsAnexo12);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioAnexo12.php" );

?>
