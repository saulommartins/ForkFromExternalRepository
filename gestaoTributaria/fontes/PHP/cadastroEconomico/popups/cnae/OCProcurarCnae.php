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
    * Página de Oculto de filtro de CNAE
    * Data de Criação   : 2i5/02/2005

    * @author Tonismar Régis Bernardo

    * $Id: OCProcurarCnae.php 63839 2015-10-22 18:08:07Z franver $

    * @ignore
*/

/*
$Log$
Revision 1.3  2006/09/15 13:46:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_REGRA."RCEMCnae.class.php"        );
include_once ( CAM_REGRA."RCEMNivelCnae.class.php"   );
include_once ( CAM_INTERFACE."MontaCnae.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCnae";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obMontaCnae = new MontaCnae;
$obErro      = new Erro;

//$obMontaCnae = new MontaCnae;

$obMontaCnae->setCodigoVigencia ( $_REQUEST["inCodigoVigencia"] );

switch ($_REQUEST["stCtrl"]) {
    case "preencheProxComboCnae":
        $stNomeComboCnae = "inCodCnae_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboCnae];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboCnae = "inCodCnae_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboCnae];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaCnae->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaCnae->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaCnae->setCodigoCnae        ( $arChaveLocal[1] );
        $obMontaCnae->setValorReduzido     ( $arChaveLocal[3] );
        $obMontaCnae->preencheProxCombo    ( $inPosicao , $_REQUEST["inNumNiveisCnae"] );
        echo "<h1>Done</h1>";
    break;
    case "preencheCombosCnae":
        echo "preencheCombosCnae";
        $obMontaCnae->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaCnae->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaCnae->setValorReduzido ( $_REQUEST["stChaveCnae"] );
        $obMontaCnae->preencheCombos();
    break;
}

?>
