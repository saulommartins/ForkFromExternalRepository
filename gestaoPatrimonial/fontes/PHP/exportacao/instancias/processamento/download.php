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
    * Download de Arquivo
    * Data de Criação   : 05/08/2010

    * @author Desenvolvedor: Tonismar R. Bernardo

    * $Id: download.php 66509 2016-09-08 18:53:27Z michel $

    * @ignore
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CLA_ARQUIVO;

$stLink         = $_GET['arq'];
$stLabel        = $_GET['label'];
$inPosBarra     = strrpos($stLink,'/');
$stNomeArquivo  = substr($stLink,$inPosBarra+1);

if(isset($_GET['boCompletaDir']) && $_GET['boCompletaDir'] == 'false')
    $stArquivo = $_GET['arq'];
else
    $stArquivo = CAM_FRAMEWORK.'tmp/'.$stNomeArquivo;

$obArquivo = new Arquivo($stArquivo,$stLabel);
$obArquivo->Show();

?>
