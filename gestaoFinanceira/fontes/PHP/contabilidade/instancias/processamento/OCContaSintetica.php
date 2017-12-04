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
    * Data de Criação: 04/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 30668 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-09-12 11:18:00 -0300 (Qua, 12 Set 2007) $

    Casos de uso: uc-02.02.02
*/

/*
$Log$
Revision 1.1  2007/09/12 14:18:00  hboaventura
criação do componente IPopUpContaSintetica

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php");

switch ($_GET['stCtrl']) {
    case 'conta_sintetica':
        if ($_GET[$_GET['stNomCampoCod']]) {
            $obTContabilidadePlanoConta = new TContabilidadePlanoConta();
            $obTContabilidadePlanoConta->setDado('exercicio', Sessao::getExercicio());
            $obTContabilidadePlanoConta->setDado('cod_estrutural',$_GET[$_GET['stNomCampoCod']]);
            $obTContabilidadePlanoConta->recuperaContaSintetica( $rsLista, $stFiltro, ' ORDER BY cod_estrutural ' );
            $stDescricao = $rsLista->getCampo('nom_conta');
            if (!$stDescricao) {
                echo "alertaAviso('@Conta inválida (".$_GET[$_GET['stNomCampoCod']].")','form','erro','".Sessao::getId()."');";
            }
        }
    break;
}

echo "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stDescricao."');";

?>
