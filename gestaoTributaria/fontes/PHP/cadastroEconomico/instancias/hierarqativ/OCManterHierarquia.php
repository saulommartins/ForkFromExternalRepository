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
    * Página Oculta de Natureza de Transferência
    * Data de Criação   : 28/03/2005

    * @author Analista: Fabio
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: OCManterHierarquia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.06

*/

/*
$Log$
Revision 1.4  2006/09/15 14:32:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CEM_NEGOCIO . "RCEMNivelAtividade.class.php"         );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$rsRecordset               = new Recordset;
/************ Fim de Funções do Arquivo *************/

// Acoes por pagina
switch ($stCtrl) {
    case "UltimoNivel":
        $obRCIMNivel = new RCEMNivelAtividade;
        $obRCIMNivel->setCodigoVigencia($_REQUEST["inCodigoVigencia"]);
        $obRCIMNivel->recuperaUltimoNivel    ( $rsUltimoNivel );
        $stNivelSuperior = $rsUltimoNivel->getCampo( "nom_nivel" );
        if(empty($stNivelSuperior))
            $stNivelSuperior = "&nbsp;";
        $stJs .= "d.getElementById('stNivelSuperior').innerHTML = '".$stNivelSuperior."'";

    break;
    $obRCIMNivel->recuperaUltimoNivel    ( $rsUltimoNivel );
    $stNivelSuperior = $rsUltimoNivel->getCampo( "nom_nivel" );
}
if( $stJs )
    sistemaLegado::executaFrameOculto($stJs);
?>
