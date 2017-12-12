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
    * Data de Criação   : 12/08/2004

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Marcelo B Paulino

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.20
*/

/*
$Log$
Revision 1.5  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioRelacaoReceita.class.php"  );

$obRRelatorio      = new RRelatorio;
$obROrcamentoRelacaoReceita = new ROrcamentoRelatorioRelacaoReceita;

//seta elementos do filtro para ENTIDADE
$arFiltro = Sessao::read('filtroRelatorio');
$inCodEntidade = $arFiltro['inCodEntidade'];
if ($inCodEntidade != "") {
    $stFiltro .= " AND cod_entidade IN  (";
    foreach ($inCodEntidade as $key => $valor) {
        $stFiltro .= $valor.",";
    }
    $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 1 ) . ")";
} else {
    $stFiltro .= $arFiltro['stTodasEntidades'];
}
$obROrcamentoRelacaoReceita->setFiltro     ( $stFiltro );
$obROrcamentoRelacaoReceita->setExercicio  ( Sessao::getExercicio() );
$obROrcamentoRelacaoReceita->setTipoOrdenacao ( $arFiltro['stTipoOrdenacao'] );

$obROrcamentoRelacaoReceita->geraRecordSet( $rsRelacaoReceita );
Sessao::write('rsRelacaoReceita',$rsRelacaoReceita);
//sessao->transf5 = $rsRelacaoReceita;
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioRelacaoReceita.php" );
?>
