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
    * Data de Criação   : 25/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    * $Id: OCHistoricoPadrao.php 60984 2014-11-27 12:35:45Z carlos.silva $

    * Casos de uso: uc-02.02.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"            );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioHistoricoPadrao.class.php"  );

$obRRelatorio       = new RRelatorio;
$obRHistoricoPadrao = new RRelatorioHistoricoPadrao;

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

$obRHistoricoPadrao->obRHistoricoPadrao->setExercicio( Sessao::getExercicio() );
$obRHistoricoPadrao->setCodHistoricoPadraoIni($arFiltroRelatorio['inCodHistoricoInicial']);
$obRHistoricoPadrao->setCodHistoricoPadraoFim($arFiltroRelatorio['inCodHistoricoFinal']);
$obRHistoricoPadrao->setComComplemento($arFiltroRelatorio['stComplemento']);
$obRHistoricoPadrao->setDescricao($arFiltroRelatorio['stDescricao']);
$obRHistoricoPadrao->setOrdenacao($arFiltroRelatorio['stOrdenacao']);

$obRHistoricoPadrao->geraRecordSet( $rsHistoricoPadrao );

Sessao::write('rsHistoricoPadrao', $rsHistoricoPadrao);
//sessao->transf5 = $rsHistoricoPadrao;
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioHistoricoPadrao.php" );
?>
