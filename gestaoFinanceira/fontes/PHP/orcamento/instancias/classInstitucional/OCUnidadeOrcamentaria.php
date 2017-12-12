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
    * Página Oculta de Unidade Orcamentaria
    * Data de Criação   : 13/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.02
*/

/*
$Log$
Revision 1.4  2006/07/05 20:42:39  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_NEGOCIO."RUnidade.class.php"      );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

// Acoes por pagina
switch ($stCtrl) {
    case "buscaOrgaoUnidade":
        $obRUnidade = new RUnidade;
        if ($_POST['inCodOrgao'] != "") {
            $arCodOrgao = explode( '-' , $_POST['inCodOrgao'] );
            $obRUnidade->obOrgao->setCodOrgao ( $arCodOrgao[1] );
            $obRUnidade->obOrgao->setExercicio( $arCodOrgao[2] );
            $obRUnidade->listar( $rsUnidade, "");
            if ( $rsUnidade->getNumLinhas() > -1 ) {
                $inContador = 1;
                $js .= "limpaSelect(f.inCodUnidade,0); \n";
                $js .= "f.inCodUnidade.options[0] = new Option('Selecione','', 'selected');\n";
                while ( !$rsUnidade->eof() ) {
                    $inCodUnidade   = $rsUnidade->getCampo("cod_unidade")."-".$rsUnidade->getCampo("exercicio");
                    $stNomUnidade   = $rsUnidade->getCampo("cod_unidade")."/".$rsUnidade->getCampo("exercicio")." - ";
                    $stNomUnidade  .= $rsUnidade->getCampo("nom_unidade");
                    $selected       = "";
                    if ($inCodUnidade == $_POST["inCodUnidade"]) {
                        $selected = "selected";
                    }
                    $js .= "f.inCodUnidade.options[$inContador] = new Option('".$stNomUnidade."','".$inCodUnidade."','".$selected."'); \n";
                    $inContador++;
                    $rsUnidade->proximo();
                }
            } else {
                $js .= "limpaSelect(f.inCodUnidade,0); \n";
                $js .= "f.inCodUnidade.options[0] = new Option('Selecione','', 'selected');\n";
            }
        } else {
            $js .= "limpaSelect(f.inCodUnidade,0); \n";
            $js .= "f.inCodUnidade.options[0] = new Option('Selecione','', 'selected');\n";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;
}
?>
