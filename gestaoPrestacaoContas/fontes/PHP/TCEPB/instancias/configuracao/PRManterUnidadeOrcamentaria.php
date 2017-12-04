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
    * Arquivo de processamento de unidade orcamentaria da GF e organograma
    * Data de Criação: 20/07/2009

    * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package URBEM
    * @subpackage

    $Id:$
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GRH_PES_MAPEAMENTO.'TPessoalDeParaOrgaoUnidade.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ManterUnidadeOrcamentaria';
$pgFilt    = 'FL'.$stPrograma.'.php';
$pgList    = 'LS'.$stPrograma.'.php';
$pgForm    = 'FM'.$stPrograma.'.php';
$pgProc    = 'PR'.$stPrograma.'.php';
$pgOcul    = 'OC'.$stPrograma.'.php';

$stAcao = $request->get('stAcao');

/**
 * Método responsável pela validação dos campos da configuração. É varrido os campos e verificado se as combos da Unidade Orçamentária estão
 * preenchidas, isso se as do Órgão também estiverem. Caso não estejam, é retornado um erro informando ao usuário para que ele preencha, informando
 * o campo e a linha onde se escontra o campo.
 *
 * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 *
 * @param  array        Array contedo os dados do post
 * @param  Erro Object  Reebe o objeto Erro para poder adicinar a mensagem de erro
 * @return boolean      Caso ache algum erro, retorna false
 */
function validarConfiguracao(&$arDados, &$obErro)
{
    $boReturn = true;
    $arTmp = array();
    foreach ($arDados as $stKey => $inValue) {
        if (strpos($stKey,'Unidade') !== false && $inValue == '') {
            if ($arDados[str_replace('Unidade', 'Orgao', $stKey)] != '') {
                $arKey = explode('_', $stKey);
                $obErro->setDescricao('Deve ser preenchido a Unidade Orçamentária da linha '.$arKey[2]);
                $boReturn = false;
                break;
            }
        } elseif (strpos($stKey,'Unidade') !== false) {
            $arTmp[$stKey] = $inValue;
        }
    }
    $arDados = $arTmp;

    return $boReturn;
}

Sessao::setTrataExcecao(true);

switch ($stAcao) {
case 'configurar' :
    $obErro = new Erro;
    $obTransacao = new Transacao;
    $obTransacao->abreTransacao($boFlag, $boTransacao);

    $arDados = $_POST;
    unset($arDados['stAcao']);

    // Valida os dados da configuração, caso haja algum problema, não é apagado os dados
    if (validarConfiguracao($arDados, $obErro)) {

        $rsOrgaoUnidade = new RecordSet;

        // É realizado o processo de apagar todos os dados da tabela para inserir novamente
        $obTPessoalDeParaOrgaoUnidade = new TPessoalDeParaOrgaoUnidade();
        $obTPessoalDeParaOrgaoUnidade->recuperaTodos($rsOrgaoUnidade, " WHERE de_para_orgao_unidade.exercicio = '".Sessao::getExercicio()."'");

        while (!$rsOrgaoUnidade->eof()) {
            $obTPessoalDeParaOrgaoUnidade->setDado('cod_orgao'  , $rsOrgaoUnidade->getCampo('cod_orgao'));
            $obTPessoalDeParaOrgaoUnidade->setDado('num_orgao'  , $rsOrgaoUnidade->getCampo('num_orgao'));
            $obTPessoalDeParaOrgaoUnidade->setDado('num_unidade', $rsOrgaoUnidade->getCampo('num_unidade'));
            $obTPessoalDeParaOrgaoUnidade->setDado('exercicio'  , $rsOrgaoUnidade->getCampo('exercicio'));
            $boErro = $obTPessoalDeParaOrgaoUnidade->exclusao($boTransacao);
            if ($obErro->ocorreu()) {
                break;
            }
            $rsOrgaoUnidade->proximo();
        }

        if (!$obErro->ocorreu()) {
            foreach ($arDados as $stKey => $stValue) {
                $arValue = explode('_', $stValue);
                $arKey = explode('_', $stKey);

                list($inNumOrgao, $inNumUnidade) = $arValue;
                $inCodOrgao = $arKey[1];

                $obTPessoalDeParaOrgaoUnidade->setDado('cod_orgao'  , $inCodOrgao);
                $obTPessoalDeParaOrgaoUnidade->setDado('num_orgao'  , $inNumOrgao);
                $obTPessoalDeParaOrgaoUnidade->setDado('num_unidade', $inNumUnidade);
                $obTPessoalDeParaOrgaoUnidade->setDado('exercicio'  , Sessao::getExercicio());
                $obErro = $obTPessoalDeParaOrgaoUnidade->inclusao($boTransacao);
                if ($obErro->ocorreu()) {
                    break;
                }
            }
        }
    }

    if (!$obErro->ocorreu()) {
        $obTransacao->commitAndClose();
        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");

    } else {
        $obTransacao->rollbackAndClose();
        sistemaLegado::exibeAviso($obErro->getDescricao() ,"n_incluir","erro");
    }
}

Sessao::encerraExcecao();
