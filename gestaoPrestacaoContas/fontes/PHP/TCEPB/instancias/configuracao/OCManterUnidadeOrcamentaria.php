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
    * Página Oculto para Vinculo de unidade orcamentaria da GF e organograma
    * Data de Criação: 20/07/2009

    * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoUnidade.class.php';
require_once CAM_GRH_PES_MAPEAMENTO.'TPessoalDeParaOrgaoUnidade.class.php';

$stPrograma = "ManterUnidadeOrcamentaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_POST['stCtrl'];

$stJs = '';
switch ($stCtrl) {
case "carregaUnidades":

    $stId = '#'.str_replace('Orgao', 'Unidade', $_POST['cbmId']);

    //limpa a combo de unidade orçamentária
    $stJs .= 'jq("'.$stId.'").removeOption(/./);';

    // Caso a combo de órgão não esteja vazia, realiza o processo de procura das unidades orçamentárias
    if ($_POST['inNumOrgao'] != '') {

        // Busca as unidades orçamentáiras do órgão selecionado
        $obTOrcamentoUnidade = new TOrcamentoUnidade;

        $stFiltro  = "\n WHERE unidade.exercicio = '".Sessao::getExercicio()."'";
        $stFiltro .= "\n   AND unidade.num_orgao = ".$_POST['inNumOrgao'];

        $stOrder   = "\n unidade.exercicio, unidade.num_orgao, unidade.num_unidade";
        $obTOrcamentoUnidade->recuperaRelacionamento($rsUnidadeOrcamentaria, $stFiltro, $stOrder);

        // monta um array contendo as informações, em um formato de objeto em javascript ('value':'text')
        $arOption = array();
        while (!$rsUnidadeOrcamentaria->eof()) {
            $arOptions[] = "'".$rsUnidadeOrcamentaria->getCampo('num_orgao')."_".$rsUnidadeOrcamentaria->getCampo('num_unidade')."':'".$rsUnidadeOrcamentaria->getCampo('nom_unidade')."'";
            $rsUnidadeOrcamentaria->proximo();
        }

        // Caso possua apenas uma unidade orçamentária para o órgão, já o deixa selecionado
        $stSelecionar = 'true';
        if (count($arOptions) > 1) {
            $stSelecionar = 'false';
        }

        // adiciona as opções na combo com o array de dados entre chaves para formar o objeto ({'value':'text'})
        $stJs .= 'jq("'.$stId.'").addOption({'.implode(',', $arOptions).'}, '.$stSelecionar.');';

        // Caso a chamada de carregar a unidade venha ao carregar a tela (se já possui dados salvos na tabela de de_para)
        // e possuir mais de uma unidade orçamentária, realiza a verificação na tabela para saber qual o valor que deve vir setado na combo
        if (isset($_POST['boCarregaInicio']) && $stSelecionar == 'false') {
            $obTPessoalDeParaOrgaoUnidade = new TPessoalDeParaOrgaoUnidade();
            $arCmbId = explode('_', $_POST['cbmId']);
            $stCondicao  = ' WHERE de_para_orgao_unidade.cod_orgao = '.$arCmbId[1];
            $stCondicao .= '   AND de_para_orgao_unidade.exercicio = '.Sessao::getExercicio();
            $obTPessoalDeParaOrgaoUnidade->recuperaTodos($rsOrgaoUnidade, $stCondicao);

            $stUnidade = $rsOrgaoUnidade->getCampo('num_orgao').'_'.$rsOrgaoUnidade->getCampo('num_unidade');

            // Seta o valor na combo
            $stJs .= 'jq("'.$stId.'").selectOptions("'.$stUnidade.'");';
        }
    }

    break;
}

echo $stJs;
