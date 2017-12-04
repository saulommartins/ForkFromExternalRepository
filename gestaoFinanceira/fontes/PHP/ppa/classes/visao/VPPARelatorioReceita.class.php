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
    * Classe de Visão de Relatório de Receita
    * Data de Criação: 12/11/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
require_once CAM_GF_PPA_NEGOCIO . 'RPPAManterReceita.class.php';

final class VPPARelatorioReceita
{
    private $obCtrl;

    /**
        * Método Construtor da Classe
        * @return void
    */
    public function __construct($obCtrl)
    {
        $this->obCtrl = $obCtrl;
    }

    /**
        * Método que encaminha para a Tela principal printar o Relatório de Receita
        * @param array $arParam
        * @return void
    */
    public function encaminhaRelatorioReceita($arParam)
    {
        if ($arParam['stIncluirAssinaturas'] == 'sim') {
            $arSessao = Sessao::read("assinaturas");

            $inCount = count($arSessao["selecionadas"]);

            if ($inCount <= 0) {
                return SistemaLegado::exibeAviso('Selecione até três Assinaturas!()', 'form', 'erro');
            } elseif ($inCount > 3) {
                return SistemaLegado::exibeAviso('Selecione apenas três Assinaturas!()', 'form', 'erro');
            }
        }

        $pgProg = "PRRelatorioReceita.php?stAcao=gerarRelatorioReceita";
        $pgProg.= "&inCodPPA=" . $arParam['inCodPPA'];
        $pgProg.= "&inCodContaIni=" . $arParam['inCodContaIni'];
        $pgProg.= "&inCodContaFim=" . $arParam['inCodContaFim'];
        $pgProg.= "&inCodRecurso=" . $arParam['inCodRecurso'];
        $pgProg.= "&inOrdem=" . $arParam['inOrdem'];
        $pgProg.= "&stIncluirAssinaturas=" . $arParam['stIncluirAssinaturas'];

        $return = sistemaLegado::alertaAviso($pgProg, '', "incluir", "aviso", Sessao::getId(), "../");

        return $return;
    }

    /**
        * Método que executa o Relatório na Tela Principal
        * @param array $arParam
        * @return void
    */
    public function gerarRelatorioReceita($arParam)
    {
    # FONTE NÃO UTILIZADO, REMOVER DO REPOSITÓRIO QUANDO POSSÍVEL.

    #    $obRPPAManterReceita = new RPPAManterReceita();
    #
    #    if ($arParam['inCodPPA'] == "") {
    #        $inCodPPA = 0;
    #    } else {
    #        $inCodPPA = $arParam['inCodPPA'];
    #    }
    #
    #    if (intval($arParam['inCodContaIni']) == 0 && intval($arParam['inCodContaFim']) == 0) {
    #        $stReceita = 'nao';
    #    } else {
    #        $stReceita = 'sim';
    #    }
    #
    #    if (intval($arParam['inCodContaIni']) == 0 && intval($arParam['inCodContaFim']) == 0) {
    #        $arParam['inCodContaIni'] = 0;
    #        $arParam['inCodContaFim'] = 0;
    #        $stConta = 'todos';
    #    } elseif (intval($arParam['inCodContaIni']) == 0 && intval($arParam['inCodContaFim']) != 0) {
    #        $arParam['inCodContaIni'] = 0;
    #        $stConta = 'menor';
    #    } elseif (intval($arParam['inCodContaIni']) != 0 && intval($arParam['inCodContaFim']) == 0) {
    #        $arParam['inCodContaFim'] = 0;
    #        $stConta = 'maior';
    #    } else {
    #        $stConta = 'compreendido';
    #    }
    #
    #    switch ($arParam['inOrdem']) {
    #        case 1:
    #            $stOrdem = 'cod_conta';
    #        break;
    #
    #        case 2:
    #            $stOrdem = 'descricao';
    #        break;
    #    }
    #
    #    if ($arParam['inCodRecurso'] == "") {
    #        $boRecurso = 'todos';
    #    } else {
    #        $boRecurso = 'unico';
    #    }
    #
    #    $boDestinacao = $this->obCtrl->pesquisaDestinacao($inCodPPA);
    #
    #    if ($boDestinacao) {
    #        $inDestinacao = '1';
    #    } else {
    #        $inDestinacao = '0';
    #    }
    #
    #    $obPreview = new PreviewBirt(2, 43, 3);
    #	$obPreview->setTitulo('Relatório do Birt');
    #	$obPreview->setVersaoBirt('2.2.1');
    #	$obPreview->addParametro("cod_ppa", $inCodPPA);
    #	$obPreview->addParametro("cod_receita_ini", $arParam['inCodContaIni']);
    #	$obPreview->addParametro("cod_receita_fim", $arParam['inCodContaFim']);
    #	$obPreview->addParametro("cod_recurso", $arParam['inCodRecurso']);
    #	$obPreview->addParametro("boDestinacao", $boDestinacao);
    #	$obPreview->addParametro("boReceita", $stReceita);
    #    $obPreview->addParametro("boRecurso", $boRecurso);
    #    $obPreview->addParametro("inDestinacao", $inDestinacao);
    #	$obPreview->addParametro("st_ordem", $stOrdem);
    #	$obPreview->addParametro("stConta", $stConta);
    #
    #    ### Assinaturas ###
    #    $arAssinaturaSelecionada = array();
    #
    #    if ($arParam['stIncluirAssinaturas'] == "sim") {
    #
    #        $arAssinatura = Sessao::read('assinaturas');
    #
    #        if (is_array($arAssinatura) && isset($arAssinatura['selecionadas']) && count($arAssinatura['selecionadas']) > 0) {
    #
    #            $arAssinaturaSelecionada = $arAssinatura['selecionadas'];
    #            for ($x = 0; $x < count($arAssinaturaSelecionada); $x++) {
    #                $stParametroCgm = "assinatura_" . ($x + 1);
    #                $stParametroCargo = "cargo_" . ($x + 1);
    #                $obPreview->addParametro($stParametroCgm, $arAssinaturaSelecionada[$x]['stNomCGM']);
    #                $obPreview->addParametro($stParametroCargo, $arAssinaturaSelecionada[$x]['stCargo']);
    #            }
    #        }
    #    }
    #
    #    $arParametros = $obPreview->arParametros;
    #
    #    if (!isset($arParametros['assinatura_1'])) {
    #        $obPreview->addParametro('assinatura_1', '');
    #        $obPreview->addParametro('cargo_1', '');
    #    }
    #    if (!isset($arParametros['assinatura_2'])) {
    #        $obPreview->addParametro('assinatura_2', '');
    #        $obPreview->addParametro('cargo_2', '');
    #    }
    #    if (!isset($arParametros['assinatura_3'])) {
    #        $obPreview->addParametro('assinatura_3', '');
    #        $obPreview->addParametro('cargo_3', '');
    #    }
    #
    #	return $obPreview->preview();
    }
}

?>
