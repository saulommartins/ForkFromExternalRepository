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
    * Data de Criação   : 11/02/2005

    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.6  2006/07/05 20:46:14  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once(CLA_ARQUIVO                       );

$stLink         = $_GET['arq'];
$stLabel        = $_GET['label'];
$inPosBarra     = strrpos   ($stLink,'/')                   ;
$stNomeArquivo  = substr    ($stLink,$inPosBarra+1)         ;

//$inPosUnder     = strpos    ($stNomeArquivo,'_')            ;
//$stLabel        = substr    ($stNomeArquivo,$inPosUnder+1)  ;

$obArquivo = new Arquivo(CAM_FRAMEWORK.'tmp/'.$stNomeArquivo,$stLabel);
$obArquivo->Show();

?>
