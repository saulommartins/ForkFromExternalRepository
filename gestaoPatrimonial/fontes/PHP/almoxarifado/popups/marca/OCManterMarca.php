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
* Arquivo instância para popup de Marca
* Data de Criação: 07/03/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Diego Barbosa Victoria

$Revision: 25114 $
$Name$
$Author: hboaventura $
$Date: 2007-08-28 11:55:55 -0300 (Ter, 28 Ago 2007) $

* Casos de uso :uc-03.03.03
*/

/*
$Log$
Revision 1.7  2007/08/28 14:55:55  hboaventura
Bug#9985#

Revision 1.6  2006/07/10 19:40:21  rodrigo
Adicionado nos componentes de itens,marca e centro de custa a função ajax para manipulação dos dados.

Revision 1.5  2006/07/06 14:05:39  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:10:11  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoMarca.class.php");

$stCampoCod  = $_GET['stNomCampoCod'];
$stCampoDesc = $_GET['stIdCampoDesc'];
$inCodigo    = $_REQUEST[ 'inCodigo' ];

switch ($_GET['stCtrl']) {

    case 'buscaPopup':
    default:
        $stJs = isset($stJs) ? $stJs : null;
        if ($inCodigo != "") {
            $obRMarca = new RAlmoxarifadoMarca();
            $obRMarca->setCodigo( $inCodigo );
            $obRMarca->consultar();
            $stMarca = $obRMarca->getDescricao();
            $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = '".addslashes($stMarca)."';";
            $stJs .= "retornaValorBscInner( '".$stCampoCod."', '".$stCampoDesc."', '".$_GET['stNomForm']."', '".addslashes($stMarca)."');";
            if (!$stMarca) {
                $stJs .= "alertaAviso('@Código da Marca (". $inCodigo .") não encontrado.', 'form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
        }
        echo $stJs;
//      sistemaLegado::executaFrameOculto( $stJs );
    break;

}

?>
