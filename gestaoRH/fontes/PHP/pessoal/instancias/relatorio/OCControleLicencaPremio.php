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
    * Página de Oculto de Controle de Licença Prêmio
    * Data de Criação : 22/10/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: OCControleLicencaPremio.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.04.18
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ControleLicencaPremio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function preencherAssentamento()
{
    $inCodClassificacao = ( $_GET['inCodClassificacao'] ) ? $_GET['inCodClassificacao'] : Sessao::read('inCodClassificacao');
    if (trim($inCodClassificacao) != "") {
        $stFiltro = " AND cod_classificacao = ".$inCodClassificacao;
        $stFiltro .= " AND cod_motivo = 9";
        $stOrder = " ORDER BY paa.descricao ";
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamento.class.php");
        $obTPessoalAssentamento = new TPessoalAssentamento();
        $obTPessoalAssentamento->recuperaAssentamentos( $rsAssentamentos, $stFiltro, $stOrder );

        $stJs .= "limpaSelect(f.inCodAssentamento,0); \n";
        $stJs .= "f.inCodAssentamento.options[0] = new Option('Selecione','', 'selected');\n";
        $stJs .= "f.inCodAssentamentoTxt.value='';\n";
        $i = 1;
        while (!$rsAssentamentos->eof()) {
            if ( $rsAssentamentos->getCampo('cod_assentamento') == Sessao::read('inCodAssentamento') ) {
                $stSelected = "selected";
                $stJs .= "f.inCodAssentamentoTxt.value='".$rsAssentamentos->getCampo('cod_assentamento')."';\n";
            } else {
                $stSelected = "";
            }
            $stJs .= "f.inCodAssentamento.options[".$i++."] = new Option('".$rsAssentamentos->getCampo("descricao")."','".$rsAssentamentos->getCampo("cod_assentamento")."', '$stSelected');\n";
            $rsAssentamentos->proximo();
        }
    }

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "preencherAssentamento":
        $stJs = preencherAssentamento();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
