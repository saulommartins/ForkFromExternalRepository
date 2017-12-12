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
    * Página de processamento oculto para o cadastro de face de imóvel
    * Data de Criação   : 04/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: OCProcurarImovel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.6  2006/09/15 15:04:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"  );
include_once( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

if ($_REQUEST["stCtrl"]) {
    $obMontaLocalizacao = new MontaLocalizacao;
    $obMontaLocalizacao->setCadastroLocalizacao( false );
    $obMontaLocalizacao->setPopup( true );

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
            $obMontaLocalizacao->setCodigoVigencia    ( $request->get("inCodigoVigencia") );
            $obMontaLocalizacao->setCodigoNivel       ( $arChaveLocal[0] );
            $obMontaLocalizacao->setCodigoLocalizacao ( $arChaveLocal[1] );
            $obMontaLocalizacao->setValorReduzido     ( $arChaveLocal[3] );
            $obMontaLocalizacao->preencheProxCombo( $inPosicao , $request->get("inNumNiveis") );

        break;
        case "preencheCombos":
            $obMontaLocalizacao->setCodigoVigencia( $request->get("inCodigoVigencia")   );
            $obMontaLocalizacao->setCodigoNivel   ( $request->get("inCodigoNivel")      );
            $obMontaLocalizacao->setValorReduzido ( $request->get("stChaveLocalizacao") );
            $obMontaLocalizacao->preencheCombos();
        break;

        case 'BuscaLocalizacao':

            if (!$_REQUEST['stChaveLocalizacao']) {
                $stJs  = 'f.stChaveLocalizacao.value = "";';
                $stJs .= 'f.stNomeLocalizacao.value = "";';
                $stJs.= 'f.inCodigoLocalizacao.value = "";';
            } else {

                $obRCIMLocalizacao = new RCIMLocalizacao;
                $obRCIMLocalizacao->setValorComposto( $_REQUEST['stChaveLocalizacao'] );
                if ( $_REQUEST['stChaveLocalizacaoLoteamento'] )
                    $obRCIMLocalizacao->setValorComposto( $_REQUEST['stChaveLocalizacaoLoteamento'] );
                $obRCIMLocalizacao->listarNomLocalizacao( $rsLocalizacao );

                if ( $rsLocalizacao->getNumLinhas() > 0 ) {
                    $stDescricao = $rsLocalizacao->getCampo("nom_localizacao");
                    $stCodigo = $rsLocalizacao->getCampo("cod_localizacao");
                    $stJs = 'f.stNomeLocalizacao.value = "'. $stDescricao .'";';
                    $stJs.= 'f.inCodigoLocalizacao.value = "'. $stCodigo .'";';
                } else {
                    $stJs  = 'f.stChaveLocalizacao.value = "";';
                    $stJs .= 'f.stNomeLocalizacao.value = "";';
                    $stJs.= 'f.inCodigoLocalizacao.value = "";';
                    $stJs .= "alertaAviso('@Localização inválida. (".$_REQUEST["stChaveLocalizacao"].")', 'form','erro','".Sessao::getId()."');";
                }

            }
            SistemaLegado::executaIFrameOculto ( $stJs );

        break;
    }
}
