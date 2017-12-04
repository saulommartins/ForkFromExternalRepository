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
 * Classe de negócio ManterProduto
 * Data de Criação: 22/09/2008
 *
 *
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 *
 * Caso de Uso: uc-02.09.11
 *
 * $Id: $
 *
 */

require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcaoDados.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAProduto.class.php';

class RPPAManterProduto
{

    /**
     * Invoca classe de mapeamento com chamada e critério em uma só etapa.
     * @param  string    $stMapeamento o nome da classe de mapeamento
     * @param  string    $stMetodo     o método invocado para a classe de mapeamento
     * @param  string    $stCriterio   o critério que delimita a busca
     * @return RecordSet
     */
    protected function callMapeamento($stMapeamento, $stMetodo, $stCriterio = "", $stOrdem = "")
    {
        $obMapeamento = new $stMapeamento();
        $obMapeamento->$stMetodo( $obRecordSet, $stCriterio, $stOrdem );

        return $obRecordSet;
    }

    /**
     * Verifica se já existe um Produto cadastrado com a mesma descricao.
     *
     * @param  string $stDescOriginal Descricao do produto como foi digitado
     * @param  string $stDescTratado  Descricao do produto sem acentuação
     * @return int    Numero de registros encontrados
     * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
     */
    public function checarCadastroProduto($stDescOriginal, $stDescTratado)
    {
        $stFiltro  = "WHERE UPPER(PP.descricao) = UPPER('$stDescOriginal')   \n";
        $stFiltro .= "   OR UPPER(PP.descricao) = UPPER('$stDescTratado')                                           \n";
        $obMapeamento = new TPPAProduto;
        $obMapeamento->recuperaProdutoCadastrado($rsDescProduto, $stFiltro);

        return count($rsDescProduto->arElementos);
    }

    public function getListaProdutos($inCodProduto = '', $stDescricao = '')
    {
        $stMapeamento = "TPPAProduto";
        $stMetodo     = "recuperaTodos";
        $stCriterio   = "";
        $stOrdem      = "cod_produto ASC";
        $stSeparador  = "";

        if (!empty($inCodProduto)) {
            $stCriterio .= "cod_produto = " . $inCodProduto." and";
        }

        if (!empty($stDescricao)) {
            $stCriterio .= $stSeparador . " descricao ilike '%" . addslashes( $stDescricao ) . "%' and ";
        }
        if (!empty($stCriterio)) {
            $stCriterio = ' where '.substr( $stCriterio, 0, strlen( $stCriterio ) - 4 );
        }

        return $this->callMapeamento( $stMapeamento, $stMetodo, $stCriterio, $stOrdem );
    }

    public function incluir($arParametros)
    {
        $obMapeamento = new TPPAProduto;
        $obMapeamento->proximoCod($arParametros['inCodProduto']);
        $obMapeamento->setDado('cod_produto', $arParametros['inCodProduto']);
        $obMapeamento->setDado('descricao', stripslashes($arParametros['stDescricao']));
        $obMapeamento->setDado('especificacao', stripslashes($arParametros['stEspecificacao']));
        $obErro = $obMapeamento->inclusao();

        return $obErro;
    }

    public function alterar($arParametros)
    {
        $obMapeamento = new TPPAProduto;
        $obMapeamento->setDado('cod_produto', $arParametros['inCodProduto']);
        $obMapeamento->setDado('descricao', stripslashes($arParametros['stDescricao']));
        $obMapeamento->setDado('especificacao', stripslashes($arParametros['stEspecificacao']));
        $obErro = $obMapeamento->alteracao();

        return $obErro;
    }

    public function excluir($arParametros)
    {
        # Recupera ppa.ppa_acao
        $stFiltro = ' where cod_produto = ' . $arParametros['inCodProduto'];
        $rsAcoes  = $this->callMapeamento('TPPAAcaoDados', 'recuperaTodos', $stFiltro);

        $arRetorno = array();

        if ($rsAcoes->inNumLinhas > 0) {
            $arRetorno['stMensagem'] = 'Produto não pode ser excluído, pois a mesmo está';
            $arRetorno['stMensagem'] .= ' vinculado a ações ou histórico do ppa.';
            $arRetorno['stAcao']     = 'n_excluir';
            $arRetorno['boOcorreu']  = true;

            return $arRetorno;
        }

        $obMapeamento = new TPPAProduto;
        $obMapeamento->setDado('cod_produto', $arParametros['inCodProduto']);
        $obErro = $obMapeamento->exclusao();

        $arRetorno['stMensagem'] = 'Produto não pode ser excluído, pois a mesmo está';
        $arRetorno['stMensagem'] .= ' vinculado a ações ou histórico do ppa.';
        $arRetorno['stAcao']     = 'n_excluir';
        $arRetorno['boOcorreu']  = false;

        return $arRetorno;
    }

}

?>
