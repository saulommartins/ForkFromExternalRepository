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
* Página Oculta de Procura de Recurso
* Data de Criação: 26/10/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Fellipe Esteves dos Santos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDireto.class.php");
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php");

$boUtilizaDestinacao = $_GET['boUtilizaDestinacao'];
$stCampoCod 	= $_GET['stNomCampoCod'];
$stCampoDesc	= $_GET['stIdCampoDesc'];
$inCodRecurso   = $_GET['inCodRecurso'];

switch ($_REQUEST["stCtrl"]) {
    case "buscaRecurso":
        $rsRecurso = new RecordSet();

        if ($inCodRecurso) {
            if ($boUtilizaDestinacao == true) {
                $stFiltro = " WHERE recurso.exercicio = '".Sessao::read('exercicio')."' AND recurso_destinacao.cod_recurso = ".$inCodRecurso;
                $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao();
                $obTOrcamentoRecursoDestinacao->recuperaRelacionamento($rsRecurso, $stFiltro);

            }

            if ($boUtilizaDestinacao == false) {
                $obTOrcamentoRecursoDireto = new TOrcamentoRecursoDireto();
                $obTOrcamentoRecursoDireto->setDado('cod_recurso', $inCodRecurso);
                   $obTOrcamentoRecursoDireto->setDado('exercicio', Sessao::read('exercicio'));
                $obTOrcamentoRecursoDireto->recuperaPorChave($rsRecurso);
            }

            if ($rsRecurso->inNumLinhas > 0) {
                $stJs = "document.getElementById('".$stCampoDesc."').innerHTML = '".$rsRecurso->getCampo('nom_recurso')."';\n";

            } else {
                $stJs = "document.getElementById('".$stCampoCod."').value = '';";
                $stJs.= "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";

                $stJs.= "alertaAviso('@Recurso (". $inCodRecurso .") não encontrado.', 'form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs = "document.getElementById('".$stCampoCod."').value = '';";
            $stJs.= "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
        }
        echo $stJs;
    break;
}

?>
