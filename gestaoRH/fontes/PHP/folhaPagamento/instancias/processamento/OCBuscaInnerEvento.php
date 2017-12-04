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
/*
    * Titulo do arquivo (Ex.: "Formulario de configuração do IPERS")
    * Data de Criação   : 23/06/2008

    * @author Analista      Dagiane
    * @author Desenvolvedor Rafael Garbin

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: OCBuscaInnerEvento.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function preencheDescEvento()
{
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php" );

    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $inCodigoEvento     = $_REQUEST['inCodigoEvento'];
    $stIdDesc           = $_REQUEST['stIdDesc'];
    $stNameCodigo       = $_REQUEST['stNameCodigo'];
    $idNameCodigo       = $_REQUEST['stNameCodigo'];
    $boEventoSistema    = $_REQUEST['boEventoSistema'];
    $stNaturezasAceitas = formataNaturezaAceitas();

     $stTipo              = $_REQUEST['stTipo'];

    $stJs .= "$('".$stIdDesc."').innerHTML = '&nbsp;';\n";
    if ($inCodigoEvento) {
        $stFiltro = "\n WHERE codigo = '".$inCodigoEvento."'";
        if (trim($stNaturezasAceitas) != '') {
            $stFiltro .= "\n  AND natureza in (".$stNaturezasAceitas.")";
        }
        switch (trim($_REQUEST['stTipoEvento'])) {
            case "n_evento_sistema":
                    $stFiltro .= " AND evento_sistema = false";
                break;

            case "evento_sistema":
                    $stFiltro .= " AND evento_sistema = true";
                break;
        }
        if (trim($stTipo)!='') {
            $stFiltro .="\n AND tipo='".$stTipo."'";
        }
        $stOrdem = "descricao";
        $obTFolhaPagamentoEvento->recuperaTodos($rsEvento, $stFiltro, $stOrdem);

        if ( $rsEvento->getNumLinhas() > 0 ) {
            $stJs .= "$('".$stIdDesc."').innerHTML    = '".trim($rsEvento->getCampo('descricao'))."';\n";
            $stJs .= "jQuery('#".$stIdDesc."').attr('value','".trim($rsEvento->getCampo('descricao'))."');\n";
            $stJs .= "jQuery('#".$idNameCodigo."').attr('value','".$rsEvento->getCampo('codigo')."');\n";
        } else {
            $stJs .= "$('".$stIdDesc."').value = '';\n";
            $stJs .= "$('".$stIdDesc."').focus();\n";
            $stJs .= "jQuery('#".$stIdDesc."').attr('value','');\n";
            $stJs .= "jQuery('#".$idNameCodigo."').attr('value','');\n";
            $stJs .= "alertaAviso('Código de evento inválido. (".$inCodigoEvento.") ','form','erro','".Sessao::getId()."');\n";
        }
    }

    return $stJs;
}

function formataNaturezaAceitas()
{
    $stNaturezasAceitas = $_REQUEST['stNaturezasAceitas'];
    $arNaturezas = explode('-', $stNaturezasAceitas);
    $retorno = '';

    if (count($arNaturezas)>0) {
        foreach ($arNaturezas as $chave => $valor) {
            $retorno .= "'".$valor."',";
        }
        $retorno = substr($retorno, 0, -1);
    }

    return $retorno;
}

switch ($_GET['stCtrl']) {
    case "preencheDescEvento":
        $js = preencheDescEvento();
        break;
}

if ($js) {
    echo $js;
}
?>
