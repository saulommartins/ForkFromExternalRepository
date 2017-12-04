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
    * Data de Criação   : 29/12/2004

    * @author Vandré Miguel Ramos
    * Casos de uso: uc-03.01.09, uc-03.01.19

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include_once( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/tabelas.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/mascarasLegado.lib.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/PDF/RRelatorio.class.php';
include_once '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/classes/negocio/RPatrimonioRelatorioCustomizavel.class.php';

$obRRelatorio             = new RRelatorio;
$obRPatrimonioRelatorioCustomizavel = new RPatrimonioRelatorioCustomizavel;

$stFiltro = "";
$obRPatrimonioRelatorioCustomizavel->geraRecordSet( $rsAnexo8, $rsTotal );

$sessao->transf5[0] = $rsAnexo8;

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioCustomizavel.php" );

?>
