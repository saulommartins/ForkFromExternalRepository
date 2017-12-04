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
 * @author Janilson Mendes Pereira da Silva <janilson.silva>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

class TLDOAcaoRecurso extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela('ldo.acao_recurso');

        $this->setCampoCod('cod_acao');
        $this->setComplementoChave('cod_acao_dados, cod_recurso, exercicio');

        $this->addCampo('cod_acao', 'integer', true, '', true, true);
        $this->addCampo('cod_acao_dados', 'integer', true, '', true, true);
        $this->addCampo('cod_recurso', 'integer', true, '', true, true);
        $this->addCampo('cod_conta', 'integer', true, '', false, true);
        $this->addCampo('exercicio', 'character', true, '4', true, true);
        $this->addCampo('valor', 'numeric', true, '14,2', false, false);
    }

    public function recuperaRecurso(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        $stSQL       = $this->montaRecuperaRecurso($stFiltro, $stOrdem);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaRecurso($stFiltro = '', $stOrdem = '')
    {
        if ($stFiltro != "") {
            $stWhere = " WHERE " . $stFiltro;
        }

        $stSql = "      SELECT                                                                              \n";
        $stSql.= "              acao.cod_acao                                                               \n";
        $stSql.= "            , acao_recurso.cod_acao                                                       \n";
        $stSql.= "            , acao_recurso.cod_acao_dados                                                 \n";
        $stSql.= "            , acao_recurso.cod_recurso                                                    \n";
        $stSql.= "            , acao_recurso.cod_conta                                                      \n";
        $stSql.= "            , acao_recurso.exercicio                                                      \n";
        $stSql.= "            , to_real(acao_recurso.valor) as valor                                        \n";
        $stSql.= "            , vw_classificacao_despesa.mascara_classificacao                              \n";
        $stSql.= "            , recurso.nom_recurso                                                         \n";
        $stSql.= "         FROM ldo.acao_recurso                                                            \n";
        $stSql.= "   INNER JOIN ldo.acao                                                                    \n";
        $stSql.= "           ON acao_recurso.cod_acao = acao.cod_acao                                       \n";
        $stSql.= "   INNER JOIN ldo.acao_dados                                                              \n";
        $stSql.= "           ON acao.cod_acao = acao_dados.cod_acao                                         \n";
        $stSql.= "          AND acao_recurso.cod_acao_dados = acao_dados.cod_acao_dados                     \n";
        $stSql.= "   INNER JOIN orcamento.recurso                                                           \n";
        $stSql.= "           ON acao_recurso.exercicio = recurso.exercicio                                  \n";
        $stSql.= "          AND acao_recurso.cod_recurso = recurso.cod_recurso                              \n";
        $stSql.= "   INNER JOIN orcamento.vw_classificacao_despesa                                          \n";
        $stSql.= "           ON acao_recurso.cod_conta = vw_classificacao_despesa.cod_conta                 \n";
        $stSql.= "          AND acao_recurso.exercicio = vw_classificacao_despesa.exercicio                 \n";

        if ($stOrdem != "") {
            $stOrdem = " ORDER BY " . $stOrdem;
        }

        return $stSql . $stWhere . $stOrdem;
    }

    public function recuperarRecursoOrcamento(&$rsRecordSet, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        $stSQL       = $this->montarRecuperaRecursoOrcamento($stFiltro, $stOrdem);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    public function montarRecuperaRecursoOrcamento($stFiltro, $stOrdem)
    {
        if ($stFiltro != "") {
            $stWhere = " WHERE " . $stFiltro;
        }

        $stSql  = " SELECT DISTINCT ON (cod_recurso)                          \n";
        $stSql .= "        recurso.exercicio                                  \n";
        $stSql .= ",       recurso.cod_recurso                                \n";
        $stSql .= ",       recurso.cod_fonte                                  \n";
        $stSql .= ",       recurso.nom_recurso                                \n";
        $stSql .= "       FROM orcamento.recurso                              \n";

        if ($stOrdem != "") {
            $stOrdem = " ORDER BY " . $stOrdem;
        }

        return $stSql . $stWhere . $stOrdem;
    }

}
