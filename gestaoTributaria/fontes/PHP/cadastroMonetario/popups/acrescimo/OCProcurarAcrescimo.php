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
  * Página de Processamento Oculto para popup de ACRESCIMO
  * Data de criação : 01/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: OCProcurarAcrescimo.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.05.11
*/

/*
$Log$
Revision 1.6  2006/09/18 08:47:04  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"  );
include_once( CAM_FW_URBEM."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCadastroLocalizacao( false );
$obMontaLocalizacao->setPopup( true );

$obErro = new Erro;

switch ($_REQUEST["stCtrl"]) {

    case "preencheProxCombo":
        $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaLocalizacao->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaLocalizacao->setCodigoLocalizacao ( $arChaveLocal[1] );
        $obMontaLocalizacao->setValorReduzido     ( $arChaveLocal[3] );
        $obMontaLocalizacao->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );

    break;
    case "preencheCombos":
        $obMontaLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaLocalizacao->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaLocalizacao->setValorReduzido ( $_REQUEST["stChaveLocalizacao"] );
        $obMontaLocalizacao->preencheCombos();
    break;
}
