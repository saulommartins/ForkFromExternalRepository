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
    * Página de
    * Data de criação : 24/10/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    Caso de uso: uc-03.02.11

    $Id: OCFichaPatrimonial.php 59612 2014-09-02 12:00:51Z gelson $

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once CAM_GP_PAT_NEGOCIO."RPatrimonioGrupo.class.php";
include_once CAM_GP_PAT_NEGOCIO."RPatrimonioNatureza.class.php";
include_once CAM_GP_PAT_NEGOCIO."RPatrimonioEspecie.class.php";
include_once CAM_GP_PAT_NEGOCIO."RPatrimonioBem.class.php";
include_once CAM_GP_PAT_NEGOCIO."RPatrimonioRelatorioFichaPatrimonial.class.php";

$obPatrimonioNatureza = new RPatrimonioNatureza;
$obPatrimonioGrupo    = new RPatrimonioGrupo;
$obPatrimonioEspecie  = new RPatrimonioEspecie;

switch ($_REQUEST['stCtrl']) {

    case "MontaGrupo":
        $stGrupo  = "inCodGrupo";
        $stJs .= "limpaSelect(f.$stGrupo,0); \n";
        $stJs .= "f.$stGrupo.options[0] = new Option('Selecione','', 'selected');\n";

        $stCombo  = "inCodEspecie";
        $stJs  .= "limpaSelect(f.$stCombo,0); \n";
        $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

        if ($_REQUEST["inCodNatureza"]) {
            $obPatrimonioGrupo->obRPatrimonioNatureza->setCodNatureza($_REQUEST["inCodNatureza"]);

            $obPatrimonioGrupo->listar( $rsGrupo, $stFiltro,"", $boTransacao );
            $rsGrupo->ordena('nom_grupo',ASC,SORT_STRING);
            $inCount = 0;

            while (!$rsGrupo->eof()) {
                $inCount++;
                $inId   = $rsGrupo->getCampo("cod_grupo");
                $stDesc = $rsGrupo->getCampo("nom_grupo");
                if( $stSelecionado == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                $stJs .= "f.$stGrupo.options[$inCount] = new Option('".addslashes($stDesc)."','".$inId."','".$stSelected."'); \n";
                $rsGrupo->proximo();
            }
        }

        $stJs .= $js;
        SistemaLegado::executaFrameOculto( $stJs );

    break;

    case "MontaEspecie":
        $stCombo  = "inCodEspecie";
        $stJs  = "limpaSelect(f.$stCombo,0); \n";
        $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

        if ($_REQUEST["inCodGrupo"]) {
            $obPatrimonioEspecie->obRPatrimonioGrupo->obRPatrimonioNatureza->setCodNatureza($_REQUEST["inCodNatureza"]);
            $obPatrimonioEspecie->obRPatrimonioGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
            $obPatrimonioEspecie->setCodEspecie($_REQUEST["inCodEspecie"]);
            $obPatrimonioEspecie->listar( $rsCombo, $stFiltro,"", $boTransacao );
            $rsCombo->ordena('nom_especie',ASC,SORT_STRING);
            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo("cod_especie");
                $stDesc = $rsCombo->getCampo("nom_especie");
                if( $stSelecionado == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                $stJs .= "f.$stCombo.options[$inCount] = new Option('".addslashes($stDesc)."','".$inId."','".$stSelected."'); \n";
                $rsCombo->proximo();
            }
        }

        $stJs .= $js;
        SistemaLegado::executaFrameOculto( $stJs );
    break;

    default:
    /*
        //seta as variaveis que serão utilizadas para gerar o relatório

        $arFiltro = Sessao::read('filtroRelatorio');

        $obRegra = new RPatrimonioRelatorioFichaPatrimonial;
        $obRegra->setCodBemInicial           ($arFiltro["inCodBemInicial"]);
        $obRegra->setTipoRelatorio           ($arFiltro["stTipoRelatorio"]);
        $obRegra->setCodBemFinal             ($arFiltro["inCodBemFinal"]);
        $obRegra->setCodFornecedor           ($arFiltro["inCGM"]);
        $obRegra->setCodNatureza             ($arFiltro["inCodNatureza"]);
        $obRegra->setCodGrupo                ($arFiltro["inCodGrupo"]);
        $obRegra->setCodEspecie              ($arFiltro["inCodEspecie"]);
        $obRegra->setCodOrgao                ($arFiltro["hdnUltimoOrgaoSelecionado"]);
        $obRegra->setCodLocal                ($arFiltro["inCodLocal"]);
        $obRegra->setHistorico               ($arFiltro["stHistorico"]);
        $obRegra->setDataInicial             ($arFiltro["stDataInicial"]);
        $obRegra->setDataFinal               ($arFiltro["stDataFinal"]);
        $obRegra->setDataInicialIncorporacao ($arFiltro["stDataInicialIncorporacao"]);
        $obRegra->setDataFinalIncorporacao   ($arFiltro["stDataFinalIncorporacao"]);
        $obRegra->setQuebraPagina            ($arFiltro["boQuebraPagina"]);
        $obRegra->GeraRecordSet($rsRecordSet);

        Sessao::write('recordset',$rsRecordSet);

        #$obRegra->obRRelatorio->executaFrameOculto(" OCGeraFichaPatrimonial.php");
    */
    break;
}

?>
