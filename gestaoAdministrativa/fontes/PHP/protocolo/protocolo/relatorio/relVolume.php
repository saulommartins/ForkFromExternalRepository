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
    * Arquivo de instância para Relatorio.
    * Data de Criação: 25/03/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-01.06.99

    $Id: relVolume.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );

$preview = new PreviewBirt(1,5,12);
$preview->setVersaoBirt( '2.5.0' );

$preview->addParametro ( 'pExercicioSessao' , Sessao::getExercicio() );

$dtInicial = dataToSql($_REQUEST['dataInicial']);
$preview->addParametro ( 'pDataInicial' , $_REQUEST['dataInicial'] );
$preview->addParametro ( 'pDtInicial' , $dtInicial );

$dtFinal = dataToSql($_REQUEST['dataFinal']);
$preview->addParametro ( 'pDataFinal' , $_REQUEST['dataFinal'] );
$preview->addParametro ( 'pDtFinal' , $dtFinal );

$arOrdem = Sessao::read('arOrdem');
$preview->addParametro ( 'pOrdem' , " ORDER BY cod_organograma, ".$arOrdem[Sessao::read('ordem')]." ASC " );

$preview->preview();
