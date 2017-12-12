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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
if (is_object($sessao)) {
    if ($sessao->transf == "reload") {
        $sessao->transf = "0";
    } else {
        $sessao->transf = "0";
        $sessao->destroiSessao();
    }
    echo '
    <html>
        <head>
        <title>Encerrando...</title>
        <script type="text/JavaScript">
            function encerra()
            {
                //opener.parent.frames["telaTopo"].location.reload();
                if (opener) {
                    opener.parent.location.replace("index.php");
                }
                window.close();
            }
        </script>
        </head>
        <body onLoad="javascript:encerra();">
        <font face="helvetica, arial" size=+1><b>Encerrando...</b></font>
        </body>
    </html>';
} else {
    echo '
    <html>
        <head>
        <title>Encerrando...</title>
        <script type="text/JavaScript">
            function encerra()
            {
                window.close();
            }
        </script>
        </head>
        <body onLoad="javascript:encerra();">
        <font face="helvetica, arial" size=+1><b>Encerrando...</b></font>
        </body>
    </html>';
}
?>
