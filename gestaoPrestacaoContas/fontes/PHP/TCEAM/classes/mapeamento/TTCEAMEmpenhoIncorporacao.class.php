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
/*
 * Classe de mapeamento da tabela tceam.empenho_incorporacao
 *
 * @package SW2
 * @subpackage Mapeamento
 * @version $Id$
 * @author eduardo.schitz@cnm.org.br
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEAMEmpenhoIncorporacao extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     * @author
     */
    public function TTCEAMEmpenhoIncorporacao()
    {
        parent::Persistente();
        $this->setTabela('tceam.empenho_incorporacao');

        $this->setCampoCod('cod_empenho,cod_entidade,exercicio');

        $this->AddCampo('cod_empenho_incorporacao'  , 'varchar', true  , '10'   , true, false);
        $this->AddCampo('cod_empenho'               , 'integer', true  , ''     , true, true);
        $this->AddCampo('cod_entidade'              , 'integer', true  , ''     , true, true);
        $this->AddCampo('exercicio'                 , 'varchar', true  , '4'    , true, true);
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaElementos(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaElementos();
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /**
        * Monta a cláusula SQL
        *
        * @access Public
        * @return String String contendo o SQL
    */
    public function montaRecuperaElementos()
    {
        $stSql = "  SELECT SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,1) AS categoria_economica
                         , SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),2,1) AS natureza
                         , SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),3,2) AS modalidade
                         , SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),5,2) AS elemento
                         , despesa.cod_entidade
                         , despesa.exercicio
                         , despesa.cod_recurso
                      FROM orcamento.conta_despesa
                INNER JOIN orcamento.despesa
                        ON conta_despesa.cod_conta = despesa.cod_conta
                       AND conta_despesa.exercicio = despesa.exercicio
                     WHERE despesa.exercicio = '".$this->getDado('exercicio')."'
                       AND despesa.cod_entidade IN (".$this->getDado('stCodEntidades').")
                  GROUP BY categoria_economica
                         , natureza
                         , modalidade
                         , elemento
                         , despesa.cod_entidade
                         , despesa.exercicio
                         , despesa.cod_recurso
                  ORDER BY despesa.cod_entidade
                         , categoria_economica
                         , natureza
                         , modalidade
                         , elemento";

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function incorporarEmpenhos($boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;

        $stSql = $this->montaIncorporar();
        $this->setDebug($stSql);

        $obErro = $obConexao->abreConexao();

        $inResult = pg_query($obConexao->getConnection() , $stSql);
        /* seta erro, retorna null caso nao exista*/
        $obErro->setDescricao(pg_last_error($obConexao->getConnection()));

        if (!$boTransacao) {
            $obConexao->fechaConexao();
        }

        return $obErro;
    }

    /**
        * Monta a cláusula SQL
        *
        * @access Public
        * @return String String contendo o SQL
    */

    public function montaIncorporar()
    {
        $stSql = "INSERT INTO tceam.empenho_incorporacao (  SELECT ( SELECT COALESCE(MAX(cod_empenho_incorporado), 0) + 1
                                                    FROM tceam.empenho_incorporacao
                                                 ) as cod_empenho_incorporacao
                                                 , empenho.cod_empenho
                                                 , empenho.cod_entidade
                                                 , empenho.exercicio
                                                 , 'INC.'||( SELECT COALESCE(MAX(cod_empenho_incorporado), 0) + 1
                                                               FROM tceam.empenho_incorporacao
                                                            )::varchar as descricao
                                              FROM empenho.empenho
                                        INNER JOIN empenho.pre_empenho
                                                ON empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                               AND empenho.exercicio       = pre_empenho.exercicio
                                        INNER JOIN empenho.pre_empenho_despesa
                                                ON pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                                               AND pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                                        INNER JOIN orcamento.despesa
                                                ON pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                                               AND pre_empenho_despesa.exercicio  = despesa.exercicio
                                        INNER JOIN orcamento.conta_despesa
                                                ON despesa.cod_conta = conta_despesa.cod_conta
                                               AND despesa.exercicio = conta_despesa.exercicio
                                             WHERE empenho.exercicio = '".$this->getDado('exercicio')."'
                                               AND despesa.cod_entidade = ".$this->getDado('cod_entidade')."
                                               AND SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,1) = '".$this->getDado('categoria_economica')."'
                                               AND SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),2,1) = '".$this->getDado('natureza')."'
                                               AND SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),3,2) = '".$this->getDado('modalidade')."'
                                               AND SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),5,2) = '".$this->getDado('elemento')."'
                                               AND despesa.cod_recurso = '".$this->getDado('cod_recurso')."'
                                          GROUP BY empenho.cod_empenho
                                                 , empenho.exercicio
                                                 , empenho.cod_entidade
                                                 , despesa.cod_recurso )";

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function excluirIncorporarEmpenhos($boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;

        $stSql = $this->montaExcluirIncorporar();

        $obErro = $obConexao->abreConexao();

        $inResult = pg_query($obConexao->getConnection() , $stSql);
        /* seta erro, retorna null caso nao exista*/
        $obErro->setDescricao(pg_last_error($obConexao->getConnection()));

        if (!$boTransacao) {
            $obConexao->fechaConexao();
        }

        return $obErro;
    }

    /**
        * Monta a cláusula SQL
        *
        * @access Public
        * @return String String contendo o SQL
    */

    public function montaExcluirIncorporar()
    {
        $stSql = " DELETE FROM tceam.empenho_incorporacao WHERE exercicio = '".$this->getDado('exercicio')."'";

        return $stSql;
    }
}
