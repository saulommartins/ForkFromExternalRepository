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
    * Página de Formulário para cadastro de servdor
    * Data de criação : 23/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Programador: Diego Lemos de Souza

    * @ignore

    $Revision: 30773 $
    $Name$
    $Author: tiago $
    $Date: 2007-07-17 12:38:24 -0300 (Ter, 17 Jul 2007) $

    * Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stNomeLote            = Sessao::read("stNomeLote");
$stCodigosLote         = Sessao::read("stCodigosLote");
$stTipoFiltroLote      = Sessao::read("stTipoFiltroLote");
$stDecricaoRegimeLote  = Sessao::read("stDecricaoRegimeLote");

switch ($stTipoFiltroLote) {
    case 'O':
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
        $dtVigencia = explode("/",$rsUltimaMovimentacao->getCampo("dt_inicial"));
        $dtVigencia = $dtVigencia[2]."-".$dtVigencia[1]."-".$dtVigencia[0];

        $stFiltro = " AND orgao.cod_orgao IN ".$stCodigosLote;
        include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php" );
        $obTOrganogramaOrgao = new TOrganogramaOrgao ;
        $obTOrganogramaOrgao->setDado("vigencia",$dtVigencia);
        $obTOrganogramaOrgao->recuperaOrgaos( $rsLista,$stFiltro," ORDER BY cod_estrutural" );
        break;
    case 'L':
        $stFiltro = " WHERE Local.cod_local IN ".$stCodigosLote;
        include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaLocal.class.php" );
        $obTOrganogramaLocal = new TOrganogramaLocal ;
        $obTOrganogramaLocal->recuperaTodos( $rsLista,$stFiltro," ORDER BY descricao" );
        break;
    case 'F':
        $stFiltro = " WHERE cod_cargo IN ".$stCodigosLote;
        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php" );
        $obTPessoalCargo = new TPessoalCargo ;
        $obTPessoalCargo->recuperaTodos( $rsLista,$stFiltro," ORDER BY descricao" );
        break;
    case 'C':
    case 'G':
        if ( trim(str_replace(')', '',str_replace('(', '',$stCodigosLote))) != "" ) {
            $stFiltro = " AND cod_contrato IN ".$stCodigosLote;
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
            $obTPessoalContrato = new TPessoalContrato();
            $obTPessoalContrato->recuperaCgmDoRegistro($rsLista,$stFiltro," nom_cgm");
        } else {
            $rsLista = new RecordSet();
        }
        break;
}

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$stTitulo = ' </div></td></tr><tr><td colspan="8" class="alt_dados">Filtros do Lote - '.$stNomeLote.$stDecricaoRegimeLote;
$obLista->setTitulo('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia().$stTitulo);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

switch ($stTipoFiltroLote) {
    case 'O':
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Código Estrutural" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Lotação" );
        $obLista->ultimoCabecalho->setWidth( 60 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[cod_estrutural]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[descricao]" );
        $obLista->commitDado();

        break;
    case 'L':

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Código" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Local" );
        $obLista->ultimoCabecalho->setWidth( 70 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[cod_local]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[descricao]" );
        $obLista->commitDado();
        break;
    case 'F':

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Código" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Função" );
        $obLista->ultimoCabecalho->setWidth( 70 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[cod_cargo]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[descricao]" );
        $obLista->commitDado();

        break;
    default:

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Matrícula" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "CGM - Nome" );
        $obLista->ultimoCabecalho->setWidth( 70 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[registro]" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
        $obLista->commitDado();
        break;
}

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
