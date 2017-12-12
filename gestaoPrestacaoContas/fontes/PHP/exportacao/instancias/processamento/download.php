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

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.2  2007/06/12 20:43:48  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.1  2007/01/25 20:29:30  diego
Novos arquivos de exportação.

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
