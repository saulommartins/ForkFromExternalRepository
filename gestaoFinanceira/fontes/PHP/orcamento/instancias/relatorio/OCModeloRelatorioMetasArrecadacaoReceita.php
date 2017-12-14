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
    * Data de Criação   : 29/08/2006

    * @author Rodrigo

    * @ignore

    $Revision: 30762 $
    $Name$
    $Author: eduardoschitz $
    $Date: 2008-03-04 09:28:44 -0300 (Ter, 04 Mar 2008) $

    * Casos de uso : uc-02.01.34
*/

/*
    $Log$
    Revision 1.4  2006/10/26 11:09:22  bruce
    Bug #7200#

    Revision 1.3  2006/10/24 12:52:06  bruce
    Bug #7200#

    Revision 1.2  2006/09/25 12:09:16  cleisson
    Bug #7032#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                       );

$obRRelatorio = new RRelatorio;

include_once ( CAM_GF_ORC_NEGOCIO."RRelatorioMetasArrecadacaoReceita.class.php"  );
$obRRelatorioMetasArrecadacaoReceita = new RRelatorioMetasArrecadacaoReceita();

$arFiltro = Sessao::read('filtroRelatorio');

$obRRelatorioMetasArrecadacaoReceita->setEntidade($arFiltro["inCodEntidade"]);
$obRRelatorioMetasArrecadacaoReceita->setExercicio($arFiltro["stExercicio"] );
$obRRelatorioMetasArrecadacaoReceita->setSimNao($arFiltro["SimNao"]);
$obRRelatorioMetasArrecadacaoReceita->setCodEstruturalInicial($arFiltro["stCodEstruturalInicial"]);
$obRRelatorioMetasArrecadacaoReceita->setCodEstruturalFinal($arFiltro["stCodEstruturalFinal"]);
$obRRelatorioMetasArrecadacaoReceita->setCodRecurso(        $arFiltro["inCodRecurso"]);
$obRRelatorioMetasArrecadacaoReceita->setCodReceitaDedutoraInicial($arFiltro['stCodReceitaDedutoraInicial']);
$obRRelatorioMetasArrecadacaoReceita->setCodReceitaDedutoraFinal($arFiltro['stCodReceitaDedutoraFinal']);
if($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao'])
    $obRRelatorioMetasArrecadacaoReceita->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );

$obRRelatorioMetasArrecadacaoReceita->setCodDetalhamento ( $arFiltro['inCodDetalhamento'] );

$obRRelatorioMetasArrecadacaoReceita->geraRecordSet( $rsDadosAnexo );

$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio( $arFiltro["stExercicio"] );
$obRConfiguracaoOrcamento->consultarConfiguracao();
if (Sessao::getExercicio() < '2014') {
    $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->getUnidadeMedidaMetas();
} else {
    $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->getUnidadeMedidaMetasReceita();
}

Sessao::write('rsDadosAnexo',$rsDadosAnexo);
Sessao::write('inUnidadesMedidasMetas',$inUnidadesMedidasMetas);
//sessao->transf5          = $rsDadosAnexo;
//sessao->transf5['periodos'] = $inUnidadesMedidasMetas;

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioMetasArrecadacaoReceita.php" );
?>
