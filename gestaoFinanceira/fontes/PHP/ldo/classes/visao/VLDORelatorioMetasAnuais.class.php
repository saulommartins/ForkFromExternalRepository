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
 * Classe Visao do 02.10.07 - Emitir Metas Anuais
 * Data de Criação: 16/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package gestaoFinanceira
 * @subpackage LDO
 * @uc 02.10.07 - Emitir Metas Anuais
 */

include_once CAM_GF_LDO_VISAO . 'VLDORelatorio.class.php';

class VLDORelatorioMetasAnuais extends VLDORelatorio implements IVLDORelatorio
{
    public static function recuperarInstancia()
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    public function emitir(array $arParametros)
    {
        if ($arParametros['stIncluirAssinaturas'] == 'sim') {

            $arSessao            = Sessao::read("assinaturas");
            $inCountDisponiveis  = count($arSessao["disponiveis"]);
            $inCountSelecionadas = count($arSessao["selecionadas"]);

            if ($inCountDisponiveis > 0) {
                if ($inCountSelecionadas <= 0) {
                    return SistemaLegado::exibeAviso('Selecione até três Assinaturas!', 'form', 'erro');
                } elseif ($inCountSelecionadas > 3) {
                    return SistemaLegado::exibeAviso('Selecione apenas três Assinaturas!', 'form', 'erro');
                }
            } else {
               return SistemaLegado::exibeAviso('Não há Assinaturas disponíveis!', 'form', 'erro');
            }
        }

        $pgProg = "PRRelatorioMetasAnuais.php?stAcao=gerar";
        $pgProg.= "&inAnoLDO=" . $arParametros['inAnoLDO'];
        $pgProg.= "&inNumCGM=" . $arParametros['inNumCGM'];
        $pgProg.= "&inCodNotaExplicativa=" . $arParametros['inCodNotaExplicativa'];
        $pgProg.= "&stIncluirAssinaturas=" . $arParametros['stIncluirAssinaturas'];
        $pgProg.= "&inCodEntidade=" . $arParametros['inCodEntidade'];

        sistemaLegado::alertaAviso($pgProg, '', "incluir", "aviso", Sessao::getId(), "../");
    }

    public function gerar(array $arParametros)
    {
        $obPreview = new PreviewBirt(2, 44, 1);
        $obPreview->setTitulo('Relatório do Birt');
        $obPreview->setVersaoBirt('2.5.0');
        $obPreview->addParametro("ano_ldo", $arParametros['inAnoLDO']);
        $obPreview->addParametro("bo_assinatura", $arParametros['stAssinatura']);
        $obPreview->addParametro("cod_nota_explicativa", $arParametros['inCodNotaExplicativa']);
        $obPreview->addParametro("cod_entidade", $arParametros['inCodEntidade']);

        ### Assinaturas ###
        $arAssinaturaSelecionada = array();

        if ($arParametros['stIncluirAssinaturas'] == "sim") {

            $arAssinatura = Sessao::read('assinaturas');

            if (is_array($arAssinatura) && isset($arAssinatura['selecionadas']) && count($arAssinatura['selecionadas']) > 0) {

                $arAssinaturaSelecionada = $arAssinatura['selecionadas'];
                for ($x = 0; $x < count($arAssinaturaSelecionada); $x++) {
                    $stParametroCgm = "assinatura_" . ($x + 1);
                    $stParametroCargo = "cargo_" . ($x + 1);
                    $obPreview->addParametro($stParametroCgm, $arAssinaturaSelecionada[$x]['stNomCGM']);
                    $obPreview->addParametro($stParametroCargo, $arAssinaturaSelecionada[$x]['stCargo']);
                }
            }
        }

        $arPreview = $obPreview->arParametros;

        if (!isset($arPreview['assinatura_1'])) {
            $obPreview->addParametro('assinatura_1', '');
            $obPreview->addParametro('cargo_1', '');
        }

        if (!isset($arPreview['assinatura_2'])) {
            $obPreview->addParametro('assinatura_2', '');
            $obPreview->addParametro('cargo_2', '');
        }

        if (!isset($arPreview['assinatura_3'])) {
            $obPreview->addParametro('assinatura_3', '');
            $obPreview->addParametro('cargo_3', '');
        }

        return $obPreview->preview();
    }
}
