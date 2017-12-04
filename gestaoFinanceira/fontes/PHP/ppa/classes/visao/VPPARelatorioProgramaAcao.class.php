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
 * Classe de Visão de Relatório Demonstrativo de Programas X Ações
 *
 * Data de Criação: 09/02/2009
 *
 *
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 *
 * @package URBEM
 * @subpackage
 *
 * Casos de uso: UC-02.09.14
 *
 * $Id $
 *
 */
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

class VPPARelatorioProgramaAcao
{

     /**
      * Método que encaminha para a Tela principal printar o Relatório
      * @param array $arParametros
      * @return void
      */
    public function encaminhaRelatorioProgramaAcao(array $arParametros)
    {

        if ($arParametros['stIncluirAssinaturas'] == 'sim') {
            $arSessao = Sessao::read("assinaturas");

            $inCount = count($arSessao["selecionadas"]);

            if ($inCount == 0) {
                return SistemaLegado::exibeAviso('Selecione pelo menos uma Assinatura!()', 'form', 'erro');
            } elseif ($inCount > 3) {
                return SistemaLegado::exibeAviso('Selecione no máximo três Assinaturas!()', 'form', 'erro');
            }
        }

        $pgProg  = "PRRelatorioProgramaAcao.php?stAcao=gerarRelatorioProgramaAcao";
        $pgProg .= "&inCodPPA=" . $arParametros['inCodPPA'];
        $pgProg .= "&inNumProgramaIni=" . $arParametros['inNumProgramaIni'];
        $pgProg .= "&inNumProgramaFim=" . $arParametros['inNumProgramaFim'];
        $pgProg .= "&stAssinatura=" . $arParametros['stAssinatura'];
        $pgProg .= "&stIncluirAssinaturas=" . $arParametros['stIncluirAssinaturas'];

        sistemaLegado::alertaAviso($pgProg, '', "incluir", "aviso", Sessao::getId(), "../");
    }

    /**
     * Método que executa o Relatório na Tela Principal
     * @param  array $arParametros
     * @return void
     */
    public function gerarRelatorioProgramaAcao(array $arParametros)
    {
        # FONTE NÃO UTILIZADO, REMOVER DO REPOSITÓRIO QUANDO POSSÍVEL.
        #$preview = new PreviewBirt(2, 43, 6);
        #$preview->setTitulo('Relatório do Birt');
        #$preview->setVersaoBirt('2.2.1');
        #$preview->addParametro("cod_ppa", $arParametros['inCodPPA']);
        #
        #if (empty($arParametros['inNumProgramaIni']) && empty($arParametros['inNumProgramaFim'])) {
        #    $boProgramaFiltro = 0;
        #} else {
        #    $boProgramaFiltro = 1;
        #}
        #
        #if (empty($arParametros['inNumProgramaIni'])) {
        #    $arParametros['inNumProgramaIni'] = 0;
        #}
        #if (empty($arParametros['inNumProgramaFim'])) {
        #    $arParametros['inNumProgramaFim'] = 9999;
        #}
        #
        #$preview->addParametro("num_programa_ini",  $arParametros['inNumProgramaIni']);
        #$preview->addParametro("num_programa_fim",  $arParametros['inNumProgramaFim']);
        #$preview->addParametro("bo_programa_filtro", $boProgramaFiltro);
        #
        #### Assinaturas ###
        #$arAssinaturaSelecionada = array();
        #
        #if ($arParametros['stIncluirAssinaturas'] == "sim") {
        #
        #    $arAssinatura = Sessao::read('assinaturas');
        #
        #    if (is_array($arAssinatura) && isset($arAssinatura['selecionadas']) && count($arAssinatura['selecionadas']) > 0) {
        #
        #        $arAssinaturaSelecionada = $arAssinatura['selecionadas'];
        #        for ($x = 0; $x < count($arAssinaturaSelecionada); $x++) {
        #            $stParametroCgm = "assinatura_" . ($x + 1);
        #            $stParametroCargo = "cargo_" . ($x + 1);
        #            $preview->addParametro($stParametroCgm, $arAssinaturaSelecionada[$x]['stNomCGM']);
        #            $preview->addParametro($stParametroCargo, $arAssinaturaSelecionada[$x]['stCargo']);
        #        }
        #    }
        #}
        #
        #$arParametros = $preview->arParametros;
        #
        #if (!isset($arParametros['assinatura_1'])) {
        #    $preview->addParametro('assinatura_1', '');
        #    $preview->addParametro('cargo_1', '');
        #}
        #if (!isset($arParametros['assinatura_2'])) {
        #    $preview->addParametro('assinatura_2', '');
        #    $preview->addParametro('cargo_2', '');
        #}
        #if (!isset($arParametros['assinatura_3'])) {
        #    $preview->addParametro('assinatura_3', '');
        #    $preview->addParametro('cargo_3', '');
        #}
        #
        #return $preview->preview();
    }

}
?>
