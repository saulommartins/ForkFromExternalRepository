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
 * Classe Mapeameto do 02.10.03 - Manter Ação
 * Data de Criação: 05/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

class TLDOAcao extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ldo.acao');

        $this->setCampoCod('cod_acao');

        $this->addCampo('cod_acao', 'integer', true, '', true, false);
        $this->addCampo('cod_acao_ppa', 'integer', true, '', false, true);
        $this->addCampo('ano', 'character', true, '4', false, true);
        $this->addCampo('ativo', 'boolean', true, '', false, false);
    }

    public function recuperaAcaoDados(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        $stSQL       = $this->montaRecuperaAcaoDados($stFiltro);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaAcaoDados($stFiltro = "")
    {
        if ($stFiltro != "") {
            $stWhere = " WHERE " . $stFiltro . " AND acao.ativo = 't'";
        } else {
            $stWhere = " WHERE acao.ativo = 't'";
        }

        $stSql = "         SELECT DISTINCT ON (acao.cod_acao)                                                  \n";
        $stSql.= "                 acao.cod_acao                                                               \n";
        $stSql.= "               , acao.ano                                                                    \n";
        $stSql.= "               , acao.ativo                                                                  \n";
        $stSql.= "               , ppa_acao.descricao                                                          \n";
        $stSql.= "               , ppa_acao.cod_acao as cod_acao_ppa                                           \n";
        $stSql.= "               , ppa_programa.cod_ppa as cod_ppa                                             \n";
        $stSql.= "               , acao_dados.cod_acao_dados                                                   \n";
        $stSql.= "               , acao_dados.num_orgao                                                        \n";
        $stSql.= "               , acao_dados.num_unidade                                                      \n";
        $stSql.= "               , acao_dados.exercicio                                                        \n";
        $stSql.= "               , acao_dados.cod_entidade                                                     \n";
        $stSql.= "               , acao_dados.cod_norma                                                        \n";
        $stSql.= "               , lpad(acao_dados.num_orgao::varchar, 2, '0') || '.' ||                       \n";
        $stSql.= "                 lpad(acao_dados.num_unidade::varchar, 2, '0') as unidade_orcamentaria       \n";
        $stSql.= "               , to_real(COALESCE(SUM(acao_recurso.valor), 0.00)) AS valor                   \n";
        $stSql.= "            FROM ldo.acao                                                                    \n";
        $stSql.= "      INNER JOIN ( SELECT DISTINCT ON (acao_dados.cod_acao)                                  \n";
        $stSql.= "                          acao_dados.cod_acao                                                \n";
        $stSql.= "                        , MAX(acao_dados.cod_acao_dados) as cod_acao_dados                   \n";
        $stSql.= "                        , acao_dados.num_orgao                                               \n";
        $stSql.= "                        , acao_dados.num_unidade                                             \n";
        $stSql.= "                        , acao_dados.exercicio                                               \n";
        $stSql.= "                        , acao_dados.cod_entidade                                            \n";
        $stSql.= "                        , acao_dados.cod_norma                                               \n";
        $stSql.= "                     FROM ldo.acao                                                           \n";
        $stSql.= "               INNER JOIN ldo.acao_dados                                                     \n";
        $stSql.= "                       ON acao.cod_acao = acao_dados.cod_acao                                \n";
        $stSql.= "                 GROUP BY acao_dados.cod_acao                                                \n";
        $stSql.= "                        , acao_dados.cod_acao_dados                                          \n";
        $stSql.= "                        , acao_dados.num_orgao                                               \n";
        $stSql.= "                        , acao_dados.num_unidade                                             \n";
        $stSql.= "                        , acao_dados.exercicio                                               \n";
        $stSql.= "                        , acao_dados.cod_entidade                                            \n";
        $stSql.= "                        , acao_dados.cod_norma                                               \n";
        $stSql.= "                 ORDER BY acao_dados.cod_acao ASC                                            \n";
        $stSql.= "                        , acao_dados.cod_acao_dados DESC ) as acao_dados                     \n";
        $stSql.= "              ON acao.cod_acao = acao_dados.cod_acao                                         \n";
        $stSql.= "       LEFT JOIN ldo.acao_recurso                                                            \n";
        $stSql.= "              ON acao_dados.cod_acao = acao_recurso.cod_acao                                 \n";
        $stSql.= "             AND acao_dados.cod_acao_dados = acao_recurso.cod_acao_dados                     \n";
        $stSql.= "      INNER JOIN ppa.acao as ppa_acao                                                        \n";
        $stSql.= "              ON acao.cod_acao_ppa = ppa_acao.cod_acao                                       \n";
        $stSql.= "      INNER JOIN ppa.programa as ppa_programa                                                \n";
        $stSql.= "              ON ppa_acao.cod_programa = ppa_programa.cod_programa                           \n";
        $stSql.= "      INNER JOIN ppa.acao_dados as ppa_acao_dados                                            \n";
        $stSql.= "              ON ppa_acao.cod_acao = ppa_acao_dados.cod_acao                                 \n";
        $stSql.= "             AND ppa_acao.ultimo_timestamp_acao_dados = ppa_acao_dados.timestamp_acao_dados  \n";

        $stGroupBy = "    GROUP BY acao.cod_acao                                                               \n";
        $stGroupBy.= "           , acao.ano                                                                    \n";
        $stGroupBy.= "           , acao.ativo                                                                  \n";
        $stGroupBy.= "           , ppa_programa.cod_ppa                                                        \n";
        $stGroupBy.= "           , ppa_acao.descricao                                                          \n";
        $stGroupBy.= "           , ppa_acao.cod_acao                                                           \n";
        $stGroupBy.= "           , acao_dados.cod_acao_dados                                                   \n";
        $stGroupBy.= "           , acao_dados.num_orgao                                                        \n";
        $stGroupBy.= "           , acao_dados.num_unidade                                                      \n";
        $stGroupBy.= "           , acao_dados.exercicio                                                        \n";
        $stGroupBy.= "           , acao_dados.cod_entidade                                                     \n";
        $stGroupBy.= "           , acao_dados.cod_norma                                                        \n";

        $stOrderBy.= "    ORDER BY acao.cod_acao ASC                                                           \n";
        $stOrderBy.= "           , acao_dados.cod_acao_dados DESC                                              \n";

        return $stSql . $stWhere . $stGroupBy . $stOrderBy;
    }

    public function recuperaTotalAcao(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        $stSQL = $this->montaRecuperaTotalAcao($stFiltro, $stOrdem);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaTotalAcao($stFiltro = '', $stOrdem = '')
    {
        if ($stFiltro != "") {
            $stWhere = " WHERE " . $stFiltro . " AND acao.ativo = 't'";
        } else {
            $stWhere = " WHERE acao.ativo = 't'";
        }

        $stSql = "       SELECT COALESCE(SUM(acao_recurso.valor), 0.00) AS total                            \n";
        $stSql.= "         FROM ldo.acao                                                                    \n";
        $stSql.= "   INNER JOIN ( SELECT DISTINCT ON (acao_dados.cod_acao)                                  \n";
        $stSql.= "                       acao_dados.cod_acao                                                \n";
        $stSql.= "                     , MAX(acao_dados.cod_acao_dados) as cod_acao_dados                   \n";
        $stSql.= "                  FROM ldo.acao                                                           \n";
        $stSql.= "            INNER JOIN ldo.acao_dados                                                     \n";
        $stSql.= "                    ON acao.cod_acao = acao_dados.cod_acao                                \n";
        $stSql.= "              GROUP BY acao_dados.cod_acao                                                \n";
        $stSql.= "                     , acao_dados.cod_acao_dados                                          \n";
        $stSql.= "              ORDER BY acao_dados.cod_acao ASC                                            \n";
        $stSql.= "                     , acao_dados.cod_acao_dados DESC ) as acao_dados                     \n";
        $stSql.= "           ON acao.cod_acao = acao_dados.cod_acao                                         \n";
        $stSql.= "   INNER JOIN ldo.acao_recurso                                                            \n";
        $stSql.= "           ON acao_dados.cod_acao = acao_recurso.cod_acao                                 \n";
        $stSql.= "          AND acao_dados.cod_acao_dados = acao_recurso.cod_acao_dados                     \n";

        if ($stOrdem) {
            $stOrderBy = ' ORDER BY ' . $stOrdem;
        }

        return $stSql . $stWhere . $stOrderBy;
    }
}
