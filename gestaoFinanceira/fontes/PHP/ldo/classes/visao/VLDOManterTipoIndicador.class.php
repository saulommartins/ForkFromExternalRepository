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
 * Página de formulário de Cadastro de Tipo de Indicador
 * Data de Criação: 09/01/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Analista     : Tonismar Regis Bernardo     <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @package gestaoFinanceira
 * @subpackage ldo
 * @uc 02.10.02 - Manter
 */

require_once CAM_GF_LDO_VISAO.'VLDOPadrao.class.php';
require_once CAM_GF_LDO_NEGOCIO.'RLDOManterTipoIndicador.class.php';
require_once CAM_GF_LDO_UTIL.'LDOString.class.php';

class VLDOManterTipoIndicador extends VLDOPadrao implements IVLDOPadrao
{
    /**
     * Recupera a instância da classe
     * @return void
     */
    public static function recuperarInstancia($ob = NULL)
    {
        return parent::recuperarInstancia(__CLASS__);
    }

    /**
     * Inicia as Regras da Classe
     * @return void
     */
    public function inicializar()
    {
        parent::inicializarRegra(__CLASS__);
    }

    /**
     * Metodo que realiza o processo de inclusao dos dados na tabela ldo.tipo_indicador. Nele é feito a verificação de similaridade
     * dos dados do campo 'descricao' para que não seja incluido dados semelhantes ao já existentes na base.
     * Mostra-se uma popUp para o usuário caso encontre uma similaridade e o usuário deve informar se deseja ou não incluir o dado.
     *
     * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     *
     * @return javascript dados de javascript a partir dos método SistemaLegado::executaFrameOculto() onde é passado o codigo que insere
     *                    os dados ou a popUp de similaridade
     */
    public function verificaSimilaridade(array $arArgs)
    {
        // Deve ser usado o jq_ pois dentro do executaFrameOculto é assim que é executado o jQuery
        $stRetorno = "jq_('#stAcao').val('".$arArgs['stAcaoOriginal']."'); jq_('#Ok').trigger('click');";

        // Pega-se somente as 2 primeiras letras para que seja feita a filtragem dos dados para verificar a similaridade mais rápido
        $stDescricao = substr($arArgs['stDescricao'], 0, 2);

        try {
            // É feita essa filtragem para poder não demorar a verificação, assim busca-se somente os que tem descrição començando com as mesmas letras
            $rsTipoIndicador = $this->recuperarRegra()->retornaDadosTipoIndicador(array('stDescricaoInicio' => $stDescricao));
        } catch (RLDOExcecao $e) {
            SistemaLegado::exibeAviso($e->getMessage(), 'n_incluir', 'erro');
        }

        // Percorre o recordSet e bate cada descrição com o que foi preenchido na tela, esse método de similaridade realiza uma padronização nas
        // strings e depois verifica suas similaridades, caso a palavra seja 90% ou mais similar, o método retorna true
        // Ao retornar true, é montada uma pop up informando sobre a simlaridade, caso o usuário deseje inserir assim mesmo, é chamado o método de
        // inserir
        while (!$rsTipoIndicador->eof()) {
            if ($arArgs['inCodTipoIndicador'] != $rsTipoIndicador->getCampo('cod_tipo_indicador')) {
                if (LDOString::validateSimilarity($arArgs['stDescricao'], $rsTipoIndicador->getCampo('descricao'))) {
                    $stMensagem  = 'O Tipo de Indicador '.$arArgs['stDescricao'].' é similar com Tipo de Indicador já cadastrado no URBEM';
                    $stMensagem .= ' ('.$rsTipoIndicador->getCampo('cod_tipo_indicador').' - '.$rsTipoIndicador->getCampo('descricao').').';
                    $stMensagem .= ' Deseja continuar?';

                    // É substituido o jq_ pelo jq padrão para não causar problemas, pois essa confirmPopUp é chamada diretamente e não a partir dos
                    // dados do executaFrameOculto
                    $stRetorno   = 'confirmPopUp("Aviso", "'.$stMensagem.'", "'.str_replace('jq_', 'jq', $stRetorno).'");';
                    break;
                }
            }
            $rsTipoIndicador->proximo();
        }

        return SistemaLegado::executaFrameOculto($stRetorno);
    }

    /**
     * Metodo que inclui os dados da tela na tabela ldo.tipo_indicador, chamando o método da regra de negócio, onde realiza o processo de inclusão
     * diretamente com a base
     *
     * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     *
     * @return javascript dados de javascript a partir dos método da classe SistemaLegado
     */
    public function incluir(array $arArgs)
    {
        try {
            $this->recuperarRegra()->incluir($arArgs);

            return SistemaLegado::alertaAviso('FMManterTipoIndicador.php?stAcao=incluir', $arArgs['stDescricao'], 'incluir', 'aviso');
        } catch (RLDOExcecao $e) {
            return SistemaLegado::exibeAviso($e->getMessage(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo que altera os dados da tela na tabela ldo.tipo_indicador, chamando o método da regra de negócio, onde realiza o processo de inclusão
     * diretamente com a base
     *
     * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     *
     * @return javascript dados de javascript a partir dos método da classe SistemaLegado
     */
    public function alterar(array $arArgs)
    {
        try {
            $this->recuperarRegra()->alterar($arArgs);

            return SistemaLegado::alertaAviso('FLManterTipoIndicador.php?stAcao=alterar', $arArgs['stDescricao'], 'alterar', 'aviso');
        } catch (RLDOExcecao $e) {
            return SistemaLegado::exibeAviso($e->getMessage(), 'n_alterar', 'erro');
        }
    }

    /**
     * Metodo que exclui os dados da tela na tabela ldo.tipo_indicador, chamando o método da regra de negócio, onde realiza o processo de inclusão
     * diretamente com a base
     *
     * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
     *
     * @return javascript dados de javascript a partir dos método da classe SistemaLegado
     */
    public function excluir(array $arArgs)
    {
        try {
            $this->recuperarRegra()->excluir($arArgs);

            return SistemaLegado::alertaAviso('LSManterTipoIndicador.php?stAcao=excluir', $arArgs['inCodTipoIndicador'], 'excluir', 'aviso');
        } catch (RLDOExcecao $e) {
            return SistemaLegado::alertaAviso('LSManterTipoIndicador.php?stAcao=excluir', $e->getMessage(), 'n_excluir', 'erro');
        }
    }
}
