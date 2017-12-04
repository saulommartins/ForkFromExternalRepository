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
    * Classe de mapeamento da tabela contabilidade.plano_conta_geral
    * Data de Criação: 08/10/2012

    * @author Analista: Tonismar
    * @author Desenvolvedor: Eduardo

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TContabilidadePlanoContaEstrutura extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */

    public function TContabilidadePlanoContaEstrutura()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.plano_conta_estrutura');

        $this->setCampoCod('cod_plano');
        $this->setComplementoChave('cod_uf, codigo_estrutural');

        $this->AddCampo('cod_plano'          , 'integer', true , ''   , true , true);
        $this->AddCampo('codigo_estrutural'  , 'varchar', true , '25' , true , false);
        $this->AddCampo('cod_uf'             , 'integer', true , ''   , true , true);
        $this->AddCampo('titulo'             , 'varchar', true , '200', false, false);
        $this->AddCampo('funcao'             , 'text'   , false, ''   , false, false);
        $this->AddCampo('natureza_saldo'     , 'char'   , false, '1'  , false, false);
        $this->AddCampo('escrituracao'       , 'char'   , false, '1'  , false, false);
        $this->AddCampo('natureza_informacao', 'char'   , false, '1'  , false, false);
        $this->AddCampo('indicador_superavit', 'char'   , false, '1'  , false, false);
    }

        /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContasSemMovimentacao.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function listarContasParaIncluir(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stOrdem = " ORDER BY plano_conta_estrutura.codigo_estrutural ";
        $stSql = $this->montaListarContasParaIncluir().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListarContasParaIncluir()
    {
        $stSql  = "    SELECT plano_conta_estrutura.titulo                                                      \n";
        $stSql .= "         , plano_conta_estrutura.codigo_estrutural                                           \n";
        $stSql .= "         , plano_conta_estrutura.funcao                                                      \n";
        $stSql .= "         , plano_conta_estrutura.natureza_saldo                                              \n";
        $stSql .= "         , UPPER(plano_conta_estrutura.escrituracao) AS escrituracao                         \n";
        $stSql .= "         , plano_conta_estrutura.natureza_informacao                                         \n";
        $stSql .= "         , plano_conta_estrutura.indicador_superavit                                         \n";
        $stSql .= "         , plano_conta.cod_conta                                                             \n";
        $stSql .= "         , plano_analitica.cod_plano                                                         \n";
        $stSql .= "         , CASE WHEN plano_conta.cod_estrutural IS NULL THEN                                 \n";
        $stSql .= "                    'incluir'                                                                \n";
        $stSql .= "                ELSE                                                                         \n";
        $stSql .= "                    'alterar'                                                                \n";
        $stSql .= "           END AS acao                                                                       \n";
        $stSql .= "      FROM contabilidade.plano_conta_geral                                                   \n";
        $stSql .= "      JOIN contabilidade.plano_conta_estrutura                                               \n";
        $stSql .= "        ON plano_conta_estrutura.cod_uf = plano_conta_geral.cod_uf                           \n";
        $stSql .= "       AND plano_conta_estrutura.cod_plano = plano_conta_geral.cod_plano                     \n";
        $stSql .= " LEFT JOIN contabilidade.plano_conta                                                         \n";
        $stSql .= "        ON plano_conta.cod_estrutural = plano_conta_estrutura.codigo_estrutural              \n";
        $stSql .= "       AND plano_conta.exercicio = '".$this->getDado('exercicio')."'                         \n";
        $stSql .= " LEFT JOIN contabilidade.plano_analitica                                                     \n";
        $stSql .= "        ON plano_analitica.cod_conta = plano_conta.cod_conta                                 \n";
        $stSql .= "       AND plano_analitica.exercicio = plano_conta.exercicio                                 \n";
        $stSql .= "     WHERE plano_conta_geral.cod_plano = ".$this->getDado('cod_plano')."                     \n";
        $stSql .= "       AND plano_conta_geral.cod_uf = ".$this->getDado('cod_uf')."                           \n";
        $stSql .= "       AND plano_conta.cod_estrutural IS NULL                                                \n";

        return $stSql;
    }

        /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaListarContasDeletar
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function listarContasDeletar(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stOrdem = " ORDER BY plano_conta.cod_estrutural DESC ";
        $stSql = $this->montaListarContasDeletar().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListarContasDeletar()
    {
        $stSql  = "   SELECT plano_conta.exercicio                                                                              \n";
        $stSql .= "        , plano_conta.cod_conta                                                                              \n";
        $stSql .= "        , plano_analitica.cod_plano                                                                          \n";
        $stSql .= "     FROM contabilidade.plano_conta                                                                          \n";
        $stSql .= "LEFT JOIN contabilidade.plano_analitica                                                                      \n";
        $stSql .= "       ON plano_analitica.cod_conta = plano_conta.cod_conta                                                  \n";
        $stSql .= "      AND plano_analitica.exercicio = plano_conta.exercicio                                                  \n";
        $stSql .= "    WHERE plano_conta.exercicio = '".$this->getDado('exercicio')."'                                          \n";
        $stSql .= "      AND NOT EXISTS ( SELECT 1 FROM contabilidade.conta_debito                                              \n";
        $stSql .= "                               WHERE conta_debito.exercicio = plano_analitica.exercicio                      \n";
        $stSql .= "                                 AND conta_debito.cod_plano = plano_analitica.cod_plano )                    \n";
        $stSql .= "      AND NOT EXISTS ( SELECT 1 FROM contabilidade.conta_credito                                             \n";
        $stSql .= "                               WHERE conta_credito.exercicio = plano_analitica.exercicio                     \n";
        $stSql .= "                                 AND conta_credito.cod_plano = plano_analitica.cod_plano )                   \n";
        $stSql .= "      AND NOT EXISTS ( SELECT 1 FROM contabilidade.plano_banco                                               \n";
        $stSql .= "                               WHERE plano_banco.exercicio = plano_analitica.exercicio                       \n";
        $stSql .= "                                 AND plano_banco.cod_plano = plano_analitica.cod_plano )                     \n";
        $stSql .= "      AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_receita                           \n";
        $stSql .= "                               WHERE configuracao_lancamento_receita.exercicio = plano_conta.exercicio       \n";
        $stSql .= "                                 AND configuracao_lancamento_receita.cod_conta = plano_conta.cod_conta )     \n";
        $stSql .= "      AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_credito                           \n";
        $stSql .= "                               WHERE configuracao_lancamento_credito.exercicio = plano_conta.exercicio       \n";
        $stSql .= "                                 AND configuracao_lancamento_credito.cod_conta = plano_conta.cod_conta )     \n";
        $stSql .= "      AND NOT EXISTS ( SELECT 1 FROM contabilidade.configuracao_lancamento_debito                            \n";
        $stSql .= "                               WHERE configuracao_lancamento_debito.exercicio = plano_conta.exercicio        \n";
        $stSql .= "                                 AND configuracao_lancamento_debito.cod_conta = plano_conta.cod_conta )      \n";
        $stSql .= "      AND (plano_conta.cod_estrutural NOT LIKE '1.1.1%'                                                      \n";
        $stSql .= "        OR plano_conta.cod_estrutural NOT LIKE '7%'                                                          \n";
        $stSql .= "        OR plano_conta.cod_estrutural NOT LIKE '8%')                                                         \n";

        return $stSql;
    }
}
?>
