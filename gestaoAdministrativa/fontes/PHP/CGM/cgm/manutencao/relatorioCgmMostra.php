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
* Arquivo de instância para manutenção de CGM
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 28360 $
$Name$
$Author: diogo.zarpelon $
$Date: 2008-03-05 10:53:13 -0300 (Qua, 05 Mar 2008) $

* Casos de uso: uc-01.02.92, uc-01.02.93
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(1,4,1);
$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('relListagemCGM.rptdesign');

$preview->addParametro( 'sTipoFis', (!empty($_REQUEST["sTipoFis"]) ? $_REQUEST["sTipoFis"] : 0));
$preview->addParametro( 'sTipoJur', (!empty($_REQUEST["sTipoJur"]) ? $_REQUEST["sTipoJur"] : 0));
$preview->addParametro( 'sTipoInt', (!empty($_REQUEST["sTipoInt"]) ? $_REQUEST["sTipoInt"] : 0));

$preview->addParametro( 'sDataIni', $_REQUEST["sDataIni"] );
$preview->addParametro( 'sDataFim', $_REQUEST["sDataFim"] );

$preview->addParametro( 'sOrderBy', $_REQUEST["sOrderBy"] );

$preview->addParametro( 'endereco', $_REQUEST["endereco"] );

$preview->preview();

?>
