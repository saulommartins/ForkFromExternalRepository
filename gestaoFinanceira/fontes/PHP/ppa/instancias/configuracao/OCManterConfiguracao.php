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
    * Página de Oculto de Manter Configuração e-Sfinge
    * Data de Criação: 29/05/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Leandro Zis

    * @ignore

    * Casos de uso: uc-02.09.01
*/

/*
$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function preencheDadosNorma($inCodNorma)
{
    include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNormasNorma.class.php" );
    $obTNormasNorma = new TNormasNorma;
    $obTNormasNorma->setDado( 'cod_norma', $inCodNorma );
    $obTNormasNorma->recuperaPorChave( $rsNorma );
    $stDtNorma = $rsNorma->getCampo('dt_assinatura');
    $stDtPublicacao = $rsNorma->getCampo('dt_publicacao');

    if ($stDtNorma && $stDtPublicacao) {
        $stJs .= "document.getElementById('stDtNorma').value = '".$stDtNorma."';";
        $stJs .= "document.getElementById('stDtPublicacao').value = '".$stDtPublicacao."';";
    }

    return $stJs;
}

switch ($stCtrl) {
    case "preencheDadosNorma":
        $js  = preencheDadosNorma($_GET['inCodNorma']);
    break;
}

if ($js) {
    echo $js;
}
?>
