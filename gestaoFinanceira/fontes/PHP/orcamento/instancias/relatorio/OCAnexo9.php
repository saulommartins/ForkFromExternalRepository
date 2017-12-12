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
    * Data de Criação   : 28/09/2004

    * @author Desenvolvedor: Gustavo Tourinho

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.16
*/

/*
$Log$
Revision 1.5  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo9.class.php"        );

$obRRelatorio              = new RRelatorio;
$obROrcamentoAnexo9        = new ROrcamentoRelatorioAnexo9;

$arFiltro = Sessao::read('filtroRelatorio');
    $stFiltro = "";
    if ($arFiltro['inCodEntidade'] != "") {
        $stFiltro = " AND cod_entidade IN  (";
        foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
            $stEntidades .= $valor.",";
        }
        if ($stEntidades != "") {
            $stEntidades = substr( $stEntidades, 0, strlen($stEntidades) - 1 );
        }
        $stFiltro .= $stEntidades . ")";
    }

    $obROrcamentoAnexo9->setFiltro( $stFiltro );
    $obROrcamentoAnexo9->setSituacao($arFiltro['stSituacao']);
    $obROrcamentoAnexo9->setTipoRelatorio( $arFiltro['stTipoRelatorio']);
    $obROrcamentoAnexo9->setDataInicial($arFiltro['stDataInicial']);
    $obROrcamentoAnexo9->setDataFinal( $arFiltro['stDataFinal']);
    $obROrcamentoAnexo9->setEntidades($stEntidades);
    $obROrcamentoAnexo9->setExercicio ( Sessao::getExercicio() );
    $obROrcamentoAnexo9->geraRecordSet( $rsAnexo9 );

    Sessao::write('arAnexo9',$rsAnexo9->getElementos());
    //sessao->transf5 = $rsAnexo9->getElementos();

    $obRRelatorio->executaFrameOculto( "OCGeraRelatorioAnexo9.php" );
?>
