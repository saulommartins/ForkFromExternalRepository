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
    * Classe de mapeamento da tabela PPA.receita
    * Data de Criação: 23/09/2008
    *
    *
    * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
    * @ignore
    *
    * $Id: $
    *
    * Casos de uso: uc-02.09.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPPAReceita extends Persistente
{

    /**
     * Método Construtor
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('ppa.ppa_receita');
        $this->setCampoCod('cod_receita');
        $this->setComplementoChave('cod_ppa, exercicio, cod_conta, cod_entidade');
        // campo, tipo, not_null, data_length, pk, fk
        $this->AddCampo('cod_receita',  'integer', true, '',     true,  false);
        $this->AddCampo('cod_ppa',      'integer', true, '',     true,  false);
        $this->AddCampo('exercicio',    'char',    true, '4',    true,  true);
        $this->AddCampo('cod_conta',    'integer', true, '',     true,  true);
        $this->AddCampo('cod_entidade', 'integer', true, '',     true,  true);
        $this->AddCampo('valor_total',  'numeric', true, '14,2', false, false);
        $this->AddCampo('ativo',        'boolean', true, '',     false, false);
    }

    /**
    * Recupera os dados da tabela ppa.ppa_receita
    *
    * @return RecordSet
    */
    public function recuperaReceitaPPA(&$rsRecordSet, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {
        return $this->executaRecupera("montaRecuperaReceitaPPA", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    /**
    * Monta SQL para recuperar todas as Receitas PPA
    *
    * @return string
    */
    protected function montaRecuperaReceitaPPA()
    {
        $stSql  = "SELECT * FROM ppa.ppa_receita     \n";

        return $stSql;
    }

    /**
    * Retorna os dados da Receita PPA com
    * os principais relacionamentos
    *
    * @param string $rsRecordSet
    * @param string $stFiltro
    * @param string $stOrder
    * @param string $boTransacao
    * @return RecordSet
    */
    public function recuperaListaReceitas(&$rsRecordSet, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {
        return $this->executaRecupera("montaRecuperaListaReceitas", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    /**
     * Busca os valores totais de Receita e Despesa para
     * verificar a diferença de valores entre receita e despesa de um PPA.
     *
     * @param  mixed     $rsRecordSet
     * @param  string    $stOrder
     * @param  bool      $boTransacao
     * @return RecordSet
     */
    public function recuperaValoresReceitaDespesa(&$rsRecordSet, $inCodPPA, $stOrder = '', $boTransacao = '')
    {
        $stFiltro  = "INNER JOIN (SELECT SUM(pr.valor_total) as total_receita  \n";
        $stFiltro .= "                  , pr.cod_receita                       \n";
        $stFiltro .= "                  , pr.cod_ppa                           \n";
        $stFiltro .= "                  , pr.exercicio                         \n";
        $stFiltro .= "                  , pr.cod_conta                         \n";
        $stFiltro .= "                  , pr.cod_entidade                      \n";
        $stFiltro .= "              FROM ppa.ppa_receita pr                    \n";
        $stFiltro .= "             WHERE pr.cod_ppa = $inCodPPA                \n";
        $stFiltro .= "          GROUP BY pr.cod_receita, pr.cod_conta, pr.cod_ppa, pr.exercicio, pr.cod_entidade) as total  \n";
        $stFiltro .= "        ON total.cod_ppa = ppa.cod_ppa                   \n";
        $stFiltro .= "     WHERE ppa.cod_ppa = $inCodPPA                       \n";

        return $this->executaRecupera("montaRecuperaValoresReceitaDespesa", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    /**
     * Monta sql para o método recuperaDiferencaReceitaDespesa
     *
     * @return string
     */
    protected function montaRecuperaValoresReceitaDespesa()
    {
        $stSql  = "    SELECT ppa.valor_total_ppa as total_despesa          \n";
        $stSql .= "         , total.total_receita                           \n";
        $stSql .= "         , ppa.cod_ppa                                   \n";
        $stSql .= "      FROM ppa.ppa as ppa                                \n";

        return $stSql;
    }

    /**
     * Monta Query para recuperar as Receitas do PPA
     *
     * @return string
     */
    protected function montaRecuperaListaReceitas()
    {
        $stSql  = " SELECT DISTINCT ON (PR.cod_conta)                               \n";
        $stSql .= "            PR.cod_receita,                                      \n";
        $stSql .= "            PR.cod_ppa,                                          \n";
        $stSql .= "            PR.exercicio,                                        \n";
        $stSql .= "            PR.cod_conta,                                        \n";
        $stSql .= "            OCR.cod_estrutural,                                   \n";
        $stSql .= "            PR.cod_entidade,                                     \n";
        $stSql .= "            PR.valor_total,                                      \n";
        $stSql .= "            ppa.ano_inicio ||' a '|| ppa.ano_final as periodo,   \n";
        $stSql .= "            ppa.destinacao_recurso,                              \n";
        $stSql .= "	 MAX (PRD.cod_receita_dados) AS cod_receita_dados,              \n";
        $stSql .= "            OCR.descricao,                                       \n";
        $stSql .= "            PN.cod_norma,                                        \n";
        $stSql .= "            CGM.nom_cgm as nom_entidade                          \n";
        $stSql .= "       FROM ppa.ppa_receita PR                                   \n";
        $stSql .= "      INNER JOIN ppa.ppa                                         \n";
        $stSql .= "         ON PR.cod_ppa = ppa.cod_ppa                             \n";
        $stSql .= " INNER JOIN ppa.ppa_receita_dados as PRD                         \n";
        $stSql .= "         ON (PRD.cod_receita = PR.cod_receita                    \n";
        $stSql .= "        AND PRD.cod_ppa = PR.cod_ppa                             \n";
        $stSql .= "        AND PRD.exercicio = PR.exercicio                         \n";
        $stSql .= "        AND PRD.cod_conta = PR.cod_conta                         \n";
        $stSql .= "        AND PRD.cod_entidade = PR.cod_entidade)                  \n";
        $stSql .= " INNER JOIN orcamento.conta_receita OCR                          \n";
        $stSql .= "         ON (OCR.cod_conta = PR.cod_conta                        \n";
        $stSql .= "        AND OCR.exercicio = PR.exercicio)                        \n";
        $stSql .= " INNER JOIN orcamento.entidade OE                                \n";
        $stSql .= "         ON (OE.cod_entidade = PR.cod_entidade                   \n";
        $stSql .= "        AND OE.exercicio = PR.exercicio)                         \n";
        $stSql .= "  LEFT JOIN ppa.ppa_norma as PN                                  \n";
        $stSql .= "  		ON PR.cod_ppa = PN.cod_ppa                              \n";
        $stSql .= " INNER JOIN sw_cgm CGM                                           \n";
        $stSql .= "         ON OE.numcgm = CGM.numcgm                               \n";

        return $stSql;
    }

    /**
    * Recupera o valor total de uma Receita
    *
    * @param mixed $rsTotalReceitas
    * @param string $stFiltro
    * @param string $stOrdem
    * @param bool $boTransacao
    *
    * @return RecordSet
    */
    public function recuperaValorTotalReceita(&$rsRecordSet, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {
        return $this->executaRecupera("montaRecuperaValorTotalReceita", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    /**
    * Monta string SQL para recuperar o valor total da receita
    *
    * @return string
    */
    protected function montaRecuperaValorTotalReceita()
    {
        $stSql  = "SELECT SUM(valor_total) as valor_total FROM ppa.ppa_receita     \n";

        return $stSql;
    }

    /**
    * Calcula todas as Receitas
    *
    * @param mixed $rsTotalReceitas
    * @param string $stFiltro
    * @param string $stOrdem
    * @param bool $boTransacao
    *
    * @return RecordSet
    */
    public function calculaTotalReceitas(&$rsTotalReceitas, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        $stSQL = $this->montaCalculaTotalReceitas($stFiltro, $stOrdem);

        return $obConexao->executaSQL($rsTotalReceitas, $stSQL, $boTransacao);
    }

    /**
    * Monta string SQL para recuperar o valor total de todas as Receitas
    *
    * @param string $stCondicao
    * @param string $stOrdem
    *
    * @return string
    */
    protected function montaCalculaTotalReceitas($stCondicao = '', $stOrdem = '')
    {
        $stFiltro = " WHERE ppa_receita.ativo = 't' ";

        if ($stCondicao) {
            $stFiltro = $stFiltro . ' AND ' . $stCondicao;
        }
        if ($stOrdem) {
            $stOrdem = ' ORDER BY ' . $stOrdem;
        }

        $stSQL  = " SELECT SUM(ppa_receita.valor_total) AS valor        \n";
        $stSQL .= "   FROM ppa.ppa_receita                              \n";
        $stSQL .= $stFiltro . $stOrdem;

        return $stSQL;
    }
}

?>
