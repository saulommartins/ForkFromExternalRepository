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
    * Página que Processa a Emissão dos Documentos
    * Data de Criacao: 28/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Fellipe Esteves dos Santos

    * @package URBEM
    * @subpackage

    * @ignore

    *Casos de uso:

    $Id:$
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once ( CAM_GT_FIS_NEGOCIO."RFISEmitirDocumento.class.php");
require_once ( CAM_GT_FIS_VISAO."VFISEmitirDocumento.class.php");

$controller = new RFISEmitirDocumento;
$visao = new VFISEmitirDocumento($controller);

switch ($_REQUEST['stTipo']) {
    case "emitir":

        $resposta = $visao->emitir($_REQUEST);
    break;

    case "listar":

        $resposta = $visao->listar($_REQUEST);
    break;
}

echo $resposta;
?>
