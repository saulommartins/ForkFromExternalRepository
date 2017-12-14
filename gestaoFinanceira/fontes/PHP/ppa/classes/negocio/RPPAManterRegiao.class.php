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
    * Classe de Negócio Manter Região
    * Data de Criação   : 22/09/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Marcio Medeiros

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso: uc-02.09.03
*/

require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAAcaoDados.class.php';

class RPPAManterRegiao
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
     * Verifica se já existe uma Região cadastrada com o mesmo nome.
     *
     * @param  string $stNomeRegiaoOriginal Nome da região como foi digitada
     * @param  string $stNomeRegiaoTeatada  Nome da região sem acentuação
     * @return int    Numero de registros encontrados
     * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
     */
    public function checarCadastroRegiao($stNomeOriginal, $stNomeTratado)
    {
        $stFiltro  = "WHERE UPPER(PR.nome) = UPPER('$stNomeOriginal')   \n";
        $stFiltro .= "   OR UPPER(PR.nome) = UPPER('$stNomeTratado')                                           \n";
        $obMapeamento = new TPPARegiao;
        $obMapeamento->recuperaRegiaoCadastrada($rsNomeRegiao, $stFiltro);

        return count($rsNomeRegiao->arElementos);
    }

    public function getListaRegioes($inCodRegiao = '', $stNomRegiao = '', $stDescricao = '')
    {
        $stMapeamento = "TPPARegiao";
        $stMetodo     = "recuperaTodos";
        $stCriterio   = "";
        $stOrdem      = "cod_regiao ASC";
        $stSeparador  = "";

        if (!empty($inCodRegiao)) {
            $stCriterio .= "cod_regiao = " . $inCodRegiao." and";
        }

        if (!empty($stNomRegiao)) {
            $stCriterio .= $stSeparador . " nome ilike '%" . addslashes( $stNomRegiao ) . "%' and ";
        }

        if (!empty($stDescricao)) {
            $stCriterio .= $stSeparador . " descricao ilike '%" . addslashes( $stDescricao ) . "%' and ";
        }
        if (!empty($stCriterio)) {
            $stCriterio = ' where '.substr( $stCriterio, 0, strlen( $stCriterio ) - 4 );
        }

        return $this->callMapeamento( $stMapeamento, $stMetodo, $stCriterio, $stOrdem );
    }

    public function incluir(Request $request)
    {
        $obMapeamento = new TPPARegiao;
        $obMapeamento->proximoCod($inCodRegiao, $boTransacao);
        $obMapeamento->setDado('cod_regiao', $inCodRegiao);
        $obMapeamento->setDado('nome', stripslashes($request->get('stNome')));
        $obMapeamento->setDado('descricao', stripslashes($request->get('stDescricao')));
        $obErro = $obMapeamento->inclusao($boTransacao);

        return $obErro;
    }

    public function alterar(Request $request)
    {
        $obMapeamento = new TPPARegiao;
        $obMapeamento->setDado('cod_regiao', $request->get('inCodRegiao'));
        $obMapeamento->setDado('nome', stripslashes($request->get('stNome')));
        $obMapeamento->setDado('descricao', stripslashes($request->get('stDescricao')));

        $obErro = $obMapeamento->alteracao();

        return $obErro;
    }

    public function excluir(Request $request)
    {
        # Recupera ppa.ppa_acao
        $stFiltro = ' where cod_regiao = ' . $request->get('inCodRegiao');
        $rsAcoes  = $this->callMapeamento('TPPAAcaoDados', 'recuperaTodos', $stFiltro);

        $arRetorno = array();

        if ($rsAcoes->inNumLinhas > 0) {
            $arRetorno['stMensagem'] = 'Região não pode ser excluída, pois a mesma está vinculada a ações ou histórico do ppa.';
            $arRetorno['stAcao']     = 'n_excluir';
            $arRetorno['boOcorreu']  = true;

            return $arRetorno;
        }

        $obMapeamento = new TPPARegiao;
        $obMapeamento->setDado('cod_regiao', $request->get('inCodRegiao'));
        $obErro = $obMapeamento->exclusao();

        $arRetorno['stMensagem'] = 'Região não pode ser excluída, pois a mesma está vinculada a ações ou histórico do ppa.';
        $arRetorno['stAcao']     = 'n_excluir';
        $arRetorno['boOcorreu']  = false;

        return $arRetorno;
    }

}
?>
