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
    * Arquivo de instância para Relatorio
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    $Id: OCRelatorioOrgao.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-01.05.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrganograma.class.php"    );
include_once( CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php"    );

$stCtrl = $_REQUEST['stCtrl'];

function MontaOrgao($stSelecionado = "")
{
    $obRegra             = new ROrganogramaOrganograma;
    $obROrganogramaOrgao = new ROrganogramaOrgao;
    $rsNorma = new RecordSet;

    $stCombo  = "inCodOrgao";
    $stFiltro = "inCodOrganograma";
    $stJs .= "limpaSelect(f.$stCombo,0); \n";
    $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
    $stJs .= "f.".$stCombo."Txt.value='$stSelecionado';\n";

    if ($_REQUEST[ $stFiltro ] != "") {
        $inCodOrganograma = $_REQUEST[ $stFiltro ];
        $obROrganogramaOrgao->obROrganograma->setCodOrganograma($inCodOrganograma);
        $obROrganogramaOrgao->listarAtivos( $rsCombo );
        $inCount = 0;
        while (!$rsCombo->eof()) {
            $inCount++;
            $inId   = $rsCombo->getCampo("cod_orgao");
            $stDesc = $rsCombo->getCampo("descricao");
            if( $stSelecionado == $inId )
                $stSelected = 'selected';
            else
                $stSelected = '';
            $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
            $rsCombo->proximo();
        }
    }

    return $stJs;
}

switch ($stCtrl) {

    case "MontaOrgao":
        $stJs = MontaOrgao( );
    break;
}
if($stJs)
    sistemaLegado::executaFrameOculto($stJs);

?>
