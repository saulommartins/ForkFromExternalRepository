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
* Arquivo de popup de busca de Acaos
* Data de Criação: 02/12/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Jânio Eduardo Vasconcellos de Magalhães
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_LDO_VISAO      . 'VLDOManterReceita.class.php';
include_once CAM_GF_PPA_NEGOCIO    . 'RPPAManterReceita.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceita.class.php';

$boExibePPA 	    = $_GET['boExibePPA'];
$boExibeValorReceita= $_GET['boExibeValorReceita'];
$boExibeRecurso 	= $_GET['boExibeRecurso'];
$stCampoCod 	    = $_GET['stNomCampoCod'];
$stCampoDesc	    = $_GET['stIdCampoDesc'];
$inNumEstruturalReceita       = $_REQUEST['inNumEstruturalReceita'];

switch ($_REQUEST["stCtrl"]) {
    case "listaReceita":
        $rsReceita = new RecordSet();
        $inAnoVigente = sessao::read('exercicio')+1;
        if ($inNumEstruturalReceita) {
            $stCriterio .= "where OCR.cod_estrutural = '".$inNumEstruturalReceita."'";
            $stCriterio .= " AND ppa.ano_inicio <=".$inAnoVigente." AND ppa.ano_final >= ".$inAnoVigente."\n";

           //busca de referencia da receita
            $obRPPAManterReceita = new RPPAManterReceita();
            $stCriterio .= " AND PR.ativo = 't'                                 \n";
            $stGroupBy  = " GROUP BY PR.cod_receita,                            \n";
            $stGroupBy .= "          PR.cod_ppa,                                \n";
            $stGroupBy .= "          PR.exercicio,                              \n";
            $stGroupBy .= "          PR.cod_conta,                              \n";
            $stGroupBy .= "          PR.cod_entidade,                           \n";
            $stGroupBy .= "          PR.valor_total,                            \n";
            $stGroupBy .= "          ppa.ano_inicio,                            \n";
            $stGroupBy .= "          ppa.ano_final,                             \n";
            $stGroupBy .= "          ppa.destinacao_recurso,                    \n";
            $stGroupBy .= "          OCR.descricao,                             \n";
            $stGroupBy .= "          PN.cod_norma,                              \n";
            $stGroupBy .= "          CGM.nom_cgm,                               \n";
            $stGroupBy .= "          OCR.cod_estrutural                         \n";
            $stCriterio .= $stGroupBy;
            $stOrdem     = ' ORDER BY PR.cod_conta';

            $rsReceita = $obRPPAManterReceita->pesquisar("TPPAReceita","recuperaListaReceitas",$stCriterio);

            if ($rsReceita->inNumLinhas > 0) {
                #Exite receitacadastra da com o código informado
                $stJs = "document.getElementById('".$stCampoDesc."').innerHTML = '".$rsReceita->getCampo('descricao')."';\n";
                $stJs.= "document.getElementById('inNumReceita').innerHTML = '".$rsReceita->getCampo('cod_receita')."';\n";

                if ($boExibeValorReceita) {
                    $stJs .= "document.getElementById('lbTotalReceita').innerHTML = retornaFormatoMonetario('".$rsReceita->getCampo('valor_total')."');\n";
                }
                if ($boExibeValorReceita) {
                    $param['inNumReceita'] = $rsReceita->getCampo('cod_receita');

                    $stJs .= $obVLDOManterReceita = VLDOManterReceita::recuperarInstancia()->exibirRecurso($param);
                    $stJs .= "document.frm.flValorReceita.value =".$rsReceita->getCampo('valor_total').";";
                }

            } else {
                $stJs = "document.getElementById('".$stCampoCod."').value = '';";
                $stJs.= "document.getElementById('inNumReceita').innerHTML = '';\n";
                $stJs.= "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
                $stJs.= "alertaAviso('@Esta conta analitica (". $inNumEstruturalReceita .") não encontrado.', 'form','erro','".Sessao::getId()."');";
                $stJs.= "document.getElementById('".$stCampoCod."').focus();";
                if ($boExibeRecurso) {
                    $stJs.= "document.getElementById('spnRecurso').innerHTML = '&nbsp;';";
                }
                if ($boExibeValorReceita) {
                    $stJs .= "document.getElementById('lbTotalReceita').innerHTML = 0000;\n";
                }
            }
        } else {
            $stJs = "document.getElementById('".$stCampoCod."').value = '';";
            $stJs.= "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inNumReceita').innerHTML = '';\n";

            if ($boExibeRecurso) {
                    $stJs .= "document.getElementById('spnRecurso').innerHTML = '&nbsp;';";
            }
            if ($boExibeValorReceita) {
                    $stJs .= "document.getElementById('flValorReceita').innerHTML = '0000';\n";
                    $stJs .= "document.getElementById('lbTotalReceita').innerHTML = 0000;\n";
            }

        }
        echo $stJs;
    break;
}
?>
