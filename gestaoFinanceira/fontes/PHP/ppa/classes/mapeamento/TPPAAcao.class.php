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
 * Classe de mapeamento da tabela ppa.acao
 * Data de Criação: 23/09/2008
    
    
 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
    
 * @package URBEM
 * @subpackage Mapeamento
    
 $Id: TPPAAcao.class.php 39830 2009-04-20 20:16:26Z pedro.medeiros $

 * Casos de uso: uc-02.09.04
 */

include_once 'TPPAUtils.class.php';

class TPPAAcao extends TPPAUtils //Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela('ppa.acao');

        $this->setCampoCod('cod_acao');

        $this->addCampo('cod_acao'                   , 'sequence' , true, '', true, false);
        $this->addCampo('cod_programa'               , 'integer'  , true, '', false, true);
        $this->addCampo('ultimo_timestamp_acao_dados', 'timestamp', true, '', false, false);
        $this->addCampo('ativo'                      , 'boolean'  , true, '', false, false);
        $this->addCampo('num_acao'                   , 'integer'  , true, '', false, false);
    }
    /**
     * Retorna a soma dos valores da ação.
     */
    public function calculaTotalAcao(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        $stSQL = $this->montaCalculaTotalAcao($stFiltro, $stOrdem);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaCalculaTotalAcao($stCondicao = '', $stOrdem = '')
    {
        $stFiltro = " WHERE acao.ativo = 't' ";

        if ($stCondicao) {
            $stFiltro = $stFiltro . ' AND ' . $stCondicao;
        }

        if ($stOrdem) {
            $stOrdem = ' ORDER BY ' . $stOrdem;
        }

        $stSQL  = " SELECT SUM(acao_recurso.valor) AS valor                                         \n";
        $stSQL .= "   FROM ppa.acao                                                                 \n";
        $stSQL .= "        INNER JOIN ppa.acao_recurso                                              \n";
        $stSQL .= "        ON acao.cod_acao = acao_recurso.cod_acao AND                             \n";
        $stSQL .= "           acao.ultimo_timestamp_acao_dados = acao_recurso.timestamp_acao_dados  \n";
        $stSQL .= $stFiltro . $stOrdem;

        return $stSQL;
    }

    /**
     * Retorna a soma dos valores em todas as ações de um mesmo programa.
     */
    public function calculaTotalPrograma(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaCalculaTotalPrograma($stFiltro, $stOrdem);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaCalculaTotalPrograma($stCondicao = '', $stOrdem = '')
    {
        $stFiltro = " WHERE acao.ativo = 't' AND programa.ativo = 't'";

        if ($stCondicao) {
            $stFiltro = $stFiltro . ' AND ' . $stCondicao;
        }

        if ($stOrdem) {
            $stOrdem = ' ORDER BY ' . $stOrdem;
        }

        $stSQL  = " SELECT SUM(acao_recurso.valor) AS valor                                         \n";
        $stSQL .= "   FROM ppa.acao                                                                 \n";
        $stSQL .= "        INNER JOIN ppa.programa                                                  \n";
        $stSQL .= "        ON programa.cod_programa = acao.cod_programa                             \n";
        $stSQL .= "        INNER JOIN ppa.acao_recurso                                              \n";
        $stSQL .= "        ON acao.cod_acao = acao_recurso.cod_acao AND                             \n";
        $stSQL .= "           acao.ultimo_timestamp_acao_dados = acao_recurso.timestamp_acao_dados  \n";
        $stSQL .= $stFiltro . $stOrdem;

        return $stSQL;
    }

    /**
     * Retorna a soma dos valores em todas as ações de um mesmo PPA.
     */
    public function recuperaTotalPPA(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaTotalPPA($stFiltro, $stOrdem);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaTotalPPA($stCondicao = '', $stOrdem = '')
    {
        $stFiltro = " WHERE acao.ativo = 't' AND programa.ativo = 't'";

        if ($stCondicao) {
            $stFiltro = $stFiltro . ' AND ' . $stCondicao;
        }

        if ($stOrdem) {
            $stOrdem = ' ORDER BY ' . $stOrdem;
        }

        $stSQL  = "     SELECT ppa.valor_total_ppa AS valor                                  \n";
        $stSQL .= "       FROM ppa.acao                                                      \n";
        $stSQL .= " INNER JOIN ppa.programa                                                  \n";
        $stSQL .= "         ON programa.cod_programa = acao.cod_programa                     \n";
        $stSQL .= " INNER JOIN ppa.ppa                                                       \n";
        $stSQL .= "         ON programa.cod_ppa = ppa.cod_ppa                                \n";
        $stSQL .= $stFiltro . $stOrdem;
        $stSQL .= " GROUP BY ppa.valor_total_ppa                                             \n";

        return $stSQL;
    }

    public function recuperaProxCodAcao(&$rsAcao, $stCondicao = '', $stOrder = '', $boTransacao = '')
    {
        $obConexao = new Conexao();
        $rsAcao    = new RecordSet();

        $stFiltro = " WHERE acao.ativo = 't' ";

        if ($stCondicao) {
            $stFiltro = $stFiltro . $stCondicao;
        }

        $stSQL  = "     SELECT COALESCE(MAX(acao.cod_acao::INTEGER) + 1, 1) AS max                  \n";
        $stSQL .= "       FROM ppa.acao                                                             \n";
        $stSQL .= " INNER JOIN ppa.acao_dados                                                       \n"; 
        $stSQL .= "         ON acao_dados.cod_acao = acao.cod_acao                                  \n";
        $stSQL .= "        AND acao_dados.timestamp_acao_dados = acao.ultimo_timestamp_acao_dados   \n";
        $stSQL .= " INNER JOIN ppa.programa                                                         \n";
        $stSQL .= "         ON programa.cod_programa = acao.cod_programa                            \n";
        $stSQL .= $stFiltro . $stOrdem;

        return $obConexao->executaSQL($rsAcao, $stSQL, $boTransacao);
    }

    public function recuperaListaAcoes(&$rsAcoes, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaListaAcoes($stFiltro, $stOrdem);

        return $obConexao->executaSQL($rsAcoes, $stSQL, $boTransacao);
    }

    private function montaRecuperaListaAcoes($stCondicao = '', $stOrdem = '')
    {
        $stFiltro = " WHERE acao.ativo = 't' AND programa.ativo = 't'";

        if ($stCondicao) {
            $stFiltro = $stFiltro . ' AND ' . $stCondicao;
        }
        if ($stOrdem) {
            $stOrdem = ' ORDER BY ' . $stOrdem;
        }

        $stSQL  = "    SELECT acao.num_acao AS cod_acao                                             \n";
        $stSQL .= "         , programa.num_programa AS cod_programa                                 \n";
        $stSQL .= "         , SUM(acao_recurso.valor) AS valor                                      \n";
        $stSQL .= "         , acao.ultimo_timestamp_acao_dados                                      \n";
        $stSQL .= "         , ppa.cod_ppa                                                           \n";
        $stSQL .= "     FROM ppa.acao                                                               \n";
        $stSQL .= "INNER JOIN ppa.acao_recurso                                                      \n";
        $stSQL .= "        ON acao.cod_acao = acao_recurso.cod_acao                                 \n";
        $stSQL .= "       AND acao.ultimo_timestamp_acao_dados = acao_recurso.timestamp_acao_dados  \n";
        $stSQL .= "INNER JOIN ppa.programa                                                          \n";
        $stSQL .= "        ON acao.cod_programa = programa.cod_programa                             \n";
        $stSQL .= "INNER JOIN ppa.programa_setorial                                                 \n";
        $stSQL .= "        ON programa_setorial.cod_setorial = programa.cod_setorial                \n";
        $stSQL .= "INNER JOIN ppa.macro_objetivo                                                    \n";
        $stSQL .= "        ON macro_objetivo.cod_macro = programa_setorial.cod_macro                \n";
        $stSQL .= "INNER JOIN ppa.ppa                                                               \n";
        $stSQL .= "        ON ppa.cod_ppa = macro_objetivo.cod_ppa                                  \n";
        $stSQL .= $stFiltro . $stOrdem;
        $stSQL .= " GROUP BY acao.num_acao                                                          \n";
        $stSQL .= "        , programa.num_programa                                                  \n";
        $stSQL .= "        , acao.ultimo_timestamp_acao_dados                                       \n";
        $stSQL .= "        , ppa.cod_ppa                                                            \n";

        return $stSQL;
    }

    public function recuperaDados(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaDados($stFiltro, $stOrdem);
        $this->setDebug($stSQL);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaDados($stFiltro = '', $stOrdem = '')
    {
        $stSQL = '';

        if ($stFiltro) {
            $stFiltro = ' WHERE ' . $stFiltro;
        }

        if ($stOrdem) {
            $stOrdem = ' ORDER BY ' . $stOrdem;
        }

        $stSQL = "
        SELECT LPAD(acao.num_acao::VARCHAR,4,'0') AS num_acao
             , LPAD(acao.cod_acao::VARCHAR,4,'0') AS cod_acao
             , ppa.cod_ppa
             , acao.ultimo_timestamp_acao_dados
             , LPAD(programa.num_programa::VARCHAR,4,'0') AS num_programa
             , LPAD(programa.cod_programa::VARCHAR,4,'0') AS cod_programa
             , programa_dados.identificacao AS nom_programa
             , programa_dados.cod_tipo_programa
             , programa_setorial.cod_setorial
             , macro_objetivo.cod_macro
             , acao_dados.titulo
             , acao_dados.descricao
             , acao_dados.finalidade
             , acao_dados.detalhamento
             , acao_dados.cod_forma
             , acao_dados.cod_tipo
             , acao_dados.cod_natureza
             , regiao.cod_regiao
             , regiao.nome AS nom_regiao
             , produto.cod_produto
             , produto.descricao AS nom_produto
             , acao_norma.cod_norma
             , norma.nom_norma
             , acao_dados.cod_tipo_orcamento
             , funcao.cod_funcao
             , funcao.descricao AS nom_funcao
             , subfuncao.cod_subfuncao
             , subfuncao.descricao AS nom_subfuncao
             , acao_dados.cod_unidade_medida
             , acao_dados.cod_grandeza
             , acao_dados.valor_estimado
             , acao_dados.meta_estimada
             , LPAD(
                    acao_unidade_executora.num_orgao::VARCHAR,
                    length(
                            orcamento.fn_masc_orgao(
                                                    split_part(acao.ultimo_timestamp_acao_dados::VARCHAR, '-', 1)
                                                    )
                        ),'0'
                    ) AS num_orgao
             , LPAD(
                    acao_unidade_executora.num_unidade::VARCHAR,
                    length(
                            orcamento.fn_masc_unidade(
                                                    split_part(acao.ultimo_timestamp_acao_dados::VARCHAR, '-', 1)
                                                    )
                        ),'0'
                    ) AS num_unidade
             , tipo_acao.descricao AS nom_tipo_acao
             , SUM(acao_quantidade.valor) AS valor_acao
             , TO_CHAR(programa_temporario_vigencia.dt_inicial, 'dd/mm/yyyy') AS dt_inicial
             , TO_CHAR(programa_temporario_vigencia.dt_final, 'dd/mm/yyyy') AS dt_final
             , TO_CHAR(acao_periodo.data_inicio, 'dd/mm/yyyy') AS dt_inicial_acao
             , TO_CHAR(acao_periodo.data_termino, 'dd/mm/yyyy') AS dt_final_acao
             , programa_dados.continuo
          FROM ppa.acao
    INNER JOIN ppa.programa
            ON acao.cod_programa = programa.cod_programa
    INNER JOIN ppa.programa_dados
            ON programa.cod_programa = programa_dados.cod_programa
           AND programa.ultimo_timestamp_programa_dados = programa_dados.timestamp_programa_dados
    INNER JOIN ppa.programa_setorial
            ON programa.cod_setorial = programa_setorial.cod_setorial
    INNER JOIN ppa.macro_objetivo
            ON programa_setorial.cod_macro = macro_objetivo.cod_macro
    INNER JOIN ppa.ppa
            ON macro_objetivo.cod_ppa = ppa.cod_ppa
    INNER JOIN ppa.acao_dados
            ON acao.cod_acao                    = acao_dados.cod_acao
           AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
    INNER JOIN ppa.regiao
            ON acao_dados.cod_regiao = regiao.cod_regiao
     LEFT JOIN ppa.produto
            ON acao_dados.cod_produto = produto.cod_produto
    INNER JOIN ppa.tipo_acao
            ON tipo_acao.cod_tipo = acao_dados.cod_tipo
     LEFT JOIN ppa.acao_norma
            ON acao.cod_acao = acao_norma.cod_acao
           AND acao.ultimo_timestamp_acao_dados = acao_norma.timestamp_acao_dados
     LEFT JOIN normas.norma
            ON acao_norma.cod_norma = norma.cod_norma
     LEFT JOIN orcamento.funcao
            ON acao_dados.exercicio  = funcao.exercicio
           AND acao_dados.cod_funcao = funcao.cod_funcao
     LEFT JOIN orcamento.subfuncao
            ON acao_dados.exercicio     = subfuncao.exercicio
           AND acao_dados.cod_subfuncao = subfuncao.cod_subfuncao
    INNER JOIN ppa.acao_unidade_executora
            ON acao_dados.cod_acao             = acao_unidade_executora.cod_acao
           AND acao_dados.timestamp_acao_dados = acao_unidade_executora.timestamp_acao_dados
     LEFT JOIN ppa.acao_quantidade
            ON acao_quantidade.cod_acao             = acao_dados.cod_acao
           AND acao_quantidade.timestamp_acao_dados = acao_dados.timestamp_acao_dados
     LEFT JOIN ppa.acao_periodo
            ON acao_periodo.cod_acao = acao_dados.cod_acao
           AND acao_periodo.timestamp_acao_dados = acao_dados.timestamp_acao_dados
     LEFT JOIN ppa.programa_temporario_vigencia
            ON programa_temporario_vigencia.cod_programa             = programa_dados.cod_programa
           AND programa_temporario_vigencia.timestamp_programa_dados = programa_dados.timestamp_programa_dados
            ";

        $stSQL .= $stFiltro;

        $stSQL .= " 
      GROUP BY acao.num_acao
             , acao.cod_acao
             , ppa.cod_ppa
             , acao.ultimo_timestamp_acao_dados
             , programa.num_programa
             , programa.cod_programa
             , programa_dados.identificacao
             , programa_dados.cod_tipo_programa
             , programa_setorial.cod_setorial
             , macro_objetivo.cod_macro
             , acao_dados.titulo
             , acao_dados.descricao
             , acao_dados.finalidade
             , acao_dados.detalhamento
             , acao_dados.cod_forma
             , acao_dados.cod_tipo
             , acao_dados.cod_natureza
             , regiao.cod_regiao
             , regiao.nome
             , produto.cod_produto
             , produto.descricao 
             , acao_norma.cod_norma
             , norma.nom_norma
             , acao_dados.cod_tipo_orcamento
             , funcao.cod_funcao
             , funcao.descricao 
             , subfuncao.cod_subfuncao
             , subfuncao.descricao
             , acao_dados.cod_unidade_medida
             , acao_dados.cod_grandeza
             , acao_dados.valor_estimado
             , acao_dados.meta_estimada
             , acao_unidade_executora.num_orgao
             , acao_unidade_executora.num_unidade
             , programa_temporario_vigencia.dt_inicial
             , programa_temporario_vigencia.dt_final
             , programa_dados.continuo
             , acao_periodo.data_inicio
             , acao_periodo.data_termino
             , tipo_acao.descricao ";

        $stSQL .= $stOrdem;

        return $stSQL;
    }

    public function recuperaDadosDespesa(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaDadosDespesa($stFiltro, $stOrdem);
        $this->setDebug($stSQL);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaDadosDespesa($stFiltro = '', $stOrdem = '')
    {
        $stSQL = '';

        if ($stFiltro) {
            $stFiltro = ' WHERE ' . $stFiltro;
        }

        if ($stOrdem) {
            $stOrdem = ' ORDER BY ' . $stOrdem;
        }

        $stSQL = "
        SELECT LPAD(acao.num_acao::VARCHAR,4,'0') AS num_acao
             , LPAD(acao.cod_acao::VARCHAR,4,'0') AS cod_acao
             , ppa.cod_ppa
             , acao.ultimo_timestamp_acao_dados
             , LPAD(programa.cod_programa::VARCHAR,4,'0') AS cod_programa
             , LPAD(programa.num_programa::VARCHAR,4,'0') AS num_programa
             , programa_dados.identificacao AS nom_programa
             , programa_dados.cod_tipo_programa
             , programa_setorial.cod_setorial
             , macro_objetivo.cod_macro
             , acao_dados.titulo
             , acao_dados.descricao
             , acao_dados.finalidade
             , acao_dados.detalhamento
             , acao_dados.cod_forma
             , acao_dados.cod_tipo
             , acao_dados.cod_natureza
             , regiao.cod_regiao
             , regiao.descricao AS nom_regiao
             , produto.cod_produto
             , produto.descricao AS nom_produto
             , acao_norma.cod_norma
             , norma.nom_norma
             , acao_dados.cod_tipo_orcamento
             , funcao.cod_funcao
             , funcao.descricao AS nom_funcao
             , subfuncao.cod_subfuncao
             , subfuncao.descricao AS nom_subfuncao
             , acao_dados.cod_unidade_medida
             , acao_dados.cod_grandeza
             , acao_dados.valor_estimado
             , acao_dados.meta_estimada
/*             , LPAD(acao_unidade_executora.num_orgao,2,0) AS num_orgao
             , LPAD(acao_unidade_executora.num_unidade,2,0) AS num_unidade */
             , LPAD(
                    acao_unidade_executora.num_orgao::VARCHAR,
                    length(
                            orcamento.fn_masc_orgao(
                                                    split_part(acao.ultimo_timestamp_acao_dados::VARCHAR, '-', 1)
                                                    )
                        ),'0'
                    ) AS num_orgao
             , LPAD(
                    acao_unidade_executora.num_unidade::VARCHAR,
                    length(
                            orcamento.fn_masc_unidade(
                                                    split_part(acao.ultimo_timestamp_acao_dados::VARCHAR, '-', 1)
                                                    )
                        ),'0'
                    ) AS num_unidade
             , tipo_acao.descricao AS nom_tipo_acao
             , SUM(acao_recurso.valor) AS valor_acao
             , TO_CHAR(programa_temporario_vigencia.dt_inicial, 'dd/mm/yyyy') AS dt_inicial
             , TO_CHAR(programa_temporario_vigencia.dt_final, 'dd/mm/yyyy') AS dt_final
             , TO_CHAR(acao_periodo.data_inicio, 'dd/mm/yyyy') AS dt_inicial_acao
             , TO_CHAR(acao_periodo.data_termino, 'dd/mm/yyyy') AS dt_final_acao
             , programa_dados.continuo
             , pao_ppa_acao.num_pao
             , acao_quantidade.ano
          FROM ppa.acao
    INNER JOIN ppa.programa
            ON acao.cod_programa = programa.cod_programa
    INNER JOIN ppa.programa_dados
            ON programa.cod_programa = programa_dados.cod_programa
           AND programa.ultimo_timestamp_programa_dados = programa_dados.timestamp_programa_dados
    INNER JOIN ppa.programa_setorial
            ON programa.cod_setorial = programa_setorial.cod_setorial
    INNER JOIN ppa.macro_objetivo
            ON programa_setorial.cod_macro = macro_objetivo.cod_macro
    INNER JOIN ppa.ppa
            ON macro_objetivo.cod_ppa = ppa.cod_ppa
    INNER JOIN ppa.acao_dados
            ON acao.cod_acao                    = acao_dados.cod_acao
           AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
    INNER JOIN ppa.regiao
            ON acao_dados.cod_regiao = regiao.cod_regiao
     LEFT JOIN ppa.produto
            ON acao_dados.cod_produto = produto.cod_produto
    INNER JOIN ppa.tipo_acao
            ON tipo_acao.cod_tipo = acao_dados.cod_tipo
     LEFT JOIN ppa.acao_norma
            ON acao.cod_acao = acao_norma.cod_acao
           AND acao.ultimo_timestamp_acao_dados = acao_norma.timestamp_acao_dados
     LEFT JOIN normas.norma
            ON acao_norma.cod_norma = norma.cod_norma
     LEFT JOIN orcamento.funcao
            ON acao_dados.exercicio  = funcao.exercicio
           AND acao_dados.cod_funcao = funcao.cod_funcao
     LEFT JOIN orcamento.subfuncao
            ON acao_dados.exercicio     = subfuncao.exercicio
           AND acao_dados.cod_subfuncao = subfuncao.cod_subfuncao
    INNER JOIN ppa.acao_unidade_executora
            ON acao_dados.cod_acao             = acao_unidade_executora.cod_acao
           AND acao_dados.timestamp_acao_dados = acao_unidade_executora.timestamp_acao_dados
    INNER JOIN ppa.acao_recurso
            ON acao_recurso.cod_acao             = acao_dados.cod_acao
           AND acao_recurso.timestamp_acao_dados = acao_dados.timestamp_acao_dados
    INNER JOIN ppa.acao_quantidade
            ON acao_quantidade.cod_acao             = acao_recurso.cod_acao
           AND acao_quantidade.timestamp_acao_dados = acao_recurso.timestamp_acao_dados
           AND acao_quantidade.cod_recurso          = acao_recurso.cod_recurso
           AND acao_quantidade.exercicio_recurso    = acao_recurso.exercicio_recurso
           AND acao_quantidade.ano                  = acao_recurso.ano
     LEFT JOIN ppa.acao_periodo
            ON acao_periodo.cod_acao = acao_dados.cod_acao
           AND acao_periodo.timestamp_acao_dados = acao_dados.timestamp_acao_dados
     LEFT JOIN ppa.programa_temporario_vigencia
            ON programa_temporario_vigencia.cod_programa             = programa_dados.cod_programa
           AND programa_temporario_vigencia.timestamp_programa_dados = programa_dados.timestamp_programa_dados
     LEFT JOIN orcamento.pao_ppa_acao
            ON pao_ppa_acao.cod_acao  = acao.cod_acao
           AND pao_ppa_acao.exercicio = '".Sessao::getExercicio()."'

            ";

        $stSQL .= $stFiltro;

        $stSQL .= " GROUP BY acao.num_acao
                           , acao.cod_acao
                           , ppa.cod_ppa
                           , acao.ultimo_timestamp_acao_dados
                           , programa.num_programa
                           , programa.cod_programa
                           , programa_dados.identificacao
                           , programa_dados.cod_tipo_programa
                           , programa_setorial.cod_setorial
                           , macro_objetivo.cod_macro
                           , acao_dados.titulo
                           , acao_dados.descricao
                           , acao_dados.finalidade
                           , acao_dados.detalhamento
                           , acao_dados.cod_forma
                           , acao_dados.cod_tipo
                           , acao_dados.cod_natureza
                           , regiao.cod_regiao
                           , regiao.descricao
                           , produto.cod_produto
                           , produto.descricao 
                           , acao_norma.cod_norma
                           , norma.nom_norma
                           , acao_dados.cod_tipo_orcamento
                           , funcao.cod_funcao
                           , funcao.descricao 
                           , subfuncao.cod_subfuncao
                           , subfuncao.descricao
                           , acao_dados.cod_unidade_medida
                           , acao_dados.cod_grandeza
                           , acao_dados.valor_estimado
                           , acao_dados.meta_estimada
                           , acao_unidade_executora.num_orgao
                           , acao_unidade_executora.num_unidade
                           , programa_temporario_vigencia.dt_inicial
                           , programa_temporario_vigencia.dt_final
                           , programa_dados.continuo
                           , acao_periodo.data_inicio
                           , acao_periodo.data_termino
                           , tipo_acao.descricao
                           , pao_ppa_acao.num_pao
                           , acao_quantidade.ano ";

        $stSQL .= $stOrdem;

        return $stSQL;
    }

    public function recuperaQuantidades(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaQuantidades($stFiltro, $stOrdem);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaQuantidades($stFiltro = '', $stOrdem = '')
    {
        $stSQL = '';

        if ($stOrdem) {
            $stOrdem = ' ORDER BY ' . $stOrdem;
        }

        $stSQL .= " SELECT acao_quantidade.ano                                                                  \n";
        $stSQL .= "      , acao_quantidade.quantidade                                                           \n";
        $stSQL .= "   FROM ppa.acao                                                                             \n";
        $stSQL .= "        INNER JOIN ppa.acao_quantidade                                                       \n";
        $stSQL .= "        ON acao.cod_acao = acao_quantidade.cod_acao AND                                      \n";
        $stSQL .= "           acao.ultimo_timestamp_acao_dados = acao_quantidade.timestamp_acao_dados           \n";
        $stSQL .= $stFiltro . $stOrdem;

        return $stSQL;
    }

    public function recuperaListaAcoesProgramas(&$rsAcoes, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaListaAcoesProgramas($stFiltro, $stOrdem);

        $this->setDebug($stSQL);

        return $obConexao->executaSQL($rsAcoes, $stSQL, $boTransacao);
    }

    private function montaRecuperaListaAcoesProgramas($stCondicao = '', $stOrdem = '')
    {
        $stSQL = "
            SELECT *
              FROM (
                SELECT LPAD(acao.num_acao::VARCHAR,4,'0') AS num_acao
                     , LPAD(acao.cod_acao::VARCHAR,4,'0') AS cod_acao
                     , acao_dados.descricao
                     , acao_dados.titulo
                     , programa.num_programa
                     , programa_dados.identificacao
                     , programa_dados.objetivo
                     , programa_dados.diagnostico
                     , programa_dados.diretriz
                     , programa_dados.publico_alvo
                     , programa_dados.continuo
                     , to_real(SUM(acao_recurso.valor)) AS valor
                     , acao.ultimo_timestamp_acao_dados
                     , ppa.cod_ppa
                     , acao_dados.cod_funcao
                     , acao_dados.cod_subfuncao
                     , funcao.descricao AS desc_funcao
                     , subfuncao.descricao AS desc_subfuncao
                     , acao_dados.cod_tipo
                     , tipo_acao.descricao as desc_tipo
                     , '' AS exercicio
                  FROM ppa.acao
            INNER JOIN ppa.acao_dados
                    ON acao.cod_acao = acao_dados.cod_acao
                   AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
            INNER JOIN ppa.tipo_acao
                    ON acao_dados.cod_tipo = tipo_acao.cod_tipo
             LEFT JOIN orcamento.funcao
                    ON acao_dados.exercicio = funcao.exercicio
                   AND acao_dados.cod_funcao = funcao.cod_funcao
             LEFT JOIN orcamento.subfuncao
                    ON acao_dados.exercicio = subfuncao.exercicio
                   AND acao_dados.cod_subfuncao = subfuncao.cod_subfuncao
             LEFT JOIN ppa.acao_recurso
                    ON acao.cod_acao = acao_recurso.cod_acao
                   AND acao.ultimo_timestamp_acao_dados = acao_recurso.timestamp_acao_dados
            INNER JOIN ppa.programa
                    ON acao.cod_programa = programa.cod_programa
            INNER JOIN ppa.programa_dados
                    ON programa_dados.cod_programa = programa.cod_programa
                   AND programa_dados.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados
            INNER JOIN ppa.programa_setorial
                    ON programa.cod_setorial = programa_setorial.cod_setorial
            INNER JOIN ppa.macro_objetivo
                    ON macro_objetivo.cod_macro = programa_setorial.cod_macro
            INNER JOIN ppa.ppa
                    ON macro_objetivo.cod_ppa = ppa.cod_ppa
        ";
        $stSQL .= "GROUP BY acao.num_acao
                          , acao.cod_acao
                          , acao_dados.descricao
                          , acao_dados.titulo
                          , programa.num_programa
                          , programa_dados.identificacao
                          , programa_dados.objetivo
                          , programa_dados.diagnostico
                          , programa_dados.diretriz
                          , programa_dados.publico_alvo
                          , programa_dados.continuo
                          , acao.ultimo_timestamp_acao_dados
                          , ppa.cod_ppa
                          , acao_dados.cod_funcao
                          , acao_dados.cod_subfuncao
                          , funcao.descricao
                          , subfuncao.descricao
                          , acao_dados.cod_tipo
                          , tipo_acao.descricao";

        $stSQL .= "
                      UNION

                SELECT LPAD(pao.num_pao::VARCHAR,4,'0') AS num_acao
                     , LPAD(pao.num_pao::VARCHAR,4,'0') AS cod_acao
                     , pao.nom_pao AS descricao
                     , pao.nom_pao AS titulo
                     , null AS num_programa
                     , '' AS identificacao
                     , '' AS objetivo
                     , '' AS diagnostico
                     , '' AS diretriz
                     , '' AS publico_alvo
                     , null AS continuo
                     , TO_REAL(0) AS valor
                     , null AS ultimo_timestamp_acao_dados
                     , null AS cod_ppa
                     , null AS cod_funcao
                     , null AS cod_subfuncao
                     , '' AS desc_funcao
                     , '' AS desc_subfuncao
                     , (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) AS cod_tipo
                     , CASE WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) = 1 )
                            THEN 'Projeto'                                                                                                               WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) = 2 )
                            THEN 'Atividade'
                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) = 3 )
                            THEN 'Operações Especiais'
                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) = 4 )
                            THEN 'Não Orçamentária'
                       END AS desc_tipo
                     , pao.exercicio
                  FROM orcamento.pao
                 INNER JOIN ( SELECT num_pao
                                   , MAX(exercicio) AS exercicio
                                FROM orcamento.pao
                            GROUP BY num_pao
                            ) AS max_pao
                         ON max_pao.num_pao   = pao.num_pao
                        AND max_pao.exercicio = pao.exercicio
                      WHERE NOT EXISTS ( SELECT 1
                                           FROM orcamento.pao_ppa_acao
                                          WHERE pao.exercicio = pao_ppa_acao.exercicio
                                            AND pao.num_pao   = pao_ppa_acao.num_pao)
                    ) AS tabela ";

        return $stSQL . $stCondicao . ' ' . $stOrdem;
    }

    public function recuperaListaAcoesProgramasTCE(&$rsAcoes, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaListaAcoesProgramasTCE($stFiltro, $stOrdem);

        $this->setDebug($stSQL);

        return $obConexao->executaSQL($rsAcoes, $stSQL, $boTransacao);
    }

    private function montaRecuperaListaAcoesProgramasTCE($stCondicao = '', $stOrdem = '')
    {
        if ( $this->getDado('cod_uf') == 2 ) {
            $stTCE = "tceal";
        }elseif ( $this->getDado('cod_uf') == 27 ) {
            $stTCE = "tceto";
        }
        
        $stSQL = "
            SELECT num_acao
                 , tabela.cod_acao
                 , descricao
                 , titulo
                 , num_programa
                 , identificacao
                 , objetivo
                 , diagnostico
                 , diretriz
                 , publico_alvo
                 , continuo
                 , valor
                 , ultimo_timestamp_acao_dados
                 , cod_ppa
                 , cod_funcao
                 , cod_subfuncao
                 , desc_funcao
                 , desc_subfuncao
                 , cod_tipo
                 , desc_tipo
                 , exercicio
              FROM (
                SELECT LPAD(acao.num_acao::VARCHAR,4,'0') AS num_acao
                     , LPAD(acao.cod_acao::VARCHAR,4,'0') AS cod_acao
                     , acao_dados.descricao
                     , acao_dados.titulo
                     , programa.num_programa
                     , programa_dados.identificacao
                     , programa_dados.objetivo
                     , programa_dados.diagnostico
                     , programa_dados.diretriz
                     , programa_dados.publico_alvo
                     , programa_dados.continuo
                     , to_real(SUM(acao_recurso.valor)) AS valor
                     , acao.ultimo_timestamp_acao_dados
                     , ppa.cod_ppa
                     , acao_dados.cod_funcao
                     , acao_dados.cod_subfuncao
                     , funcao.descricao AS desc_funcao
                     , subfuncao.descricao AS desc_subfuncao
                     , acao_dados.cod_tipo
                     , tipo_acao.descricao as desc_tipo
                     , '' AS exercicio
                  FROM ppa.acao
            INNER JOIN ppa.acao_dados
                    ON acao.cod_acao = acao_dados.cod_acao
                   AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
            INNER JOIN ppa.tipo_acao
                    ON acao_dados.cod_tipo = tipo_acao.cod_tipo
             LEFT JOIN orcamento.funcao
                    ON acao_dados.exercicio = funcao.exercicio
                   AND acao_dados.cod_funcao = funcao.cod_funcao
             LEFT JOIN orcamento.subfuncao
                    ON acao_dados.exercicio = subfuncao.exercicio
                   AND acao_dados.cod_subfuncao = subfuncao.cod_subfuncao
             LEFT JOIN ppa.acao_recurso
                    ON acao.cod_acao = acao_recurso.cod_acao
                   AND acao.ultimo_timestamp_acao_dados = acao_recurso.timestamp_acao_dados
            INNER JOIN ppa.programa
                    ON acao.cod_programa = programa.cod_programa
            INNER JOIN ppa.programa_dados
                    ON programa_dados.cod_programa = programa.cod_programa
                   AND programa_dados.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados
            INNER JOIN ppa.programa_setorial
                    ON programa.cod_setorial = programa_setorial.cod_setorial
            INNER JOIN ppa.macro_objetivo
                    ON macro_objetivo.cod_macro = programa_setorial.cod_macro
            INNER JOIN ppa.ppa
                    ON macro_objetivo.cod_ppa = ppa.cod_ppa
        ";
        $stSQL .= "GROUP BY acao.num_acao
                          , acao.cod_acao
                          , acao_dados.descricao
                          , acao_dados.titulo
                          , programa.num_programa
                          , programa_dados.identificacao
                          , programa_dados.objetivo
                          , programa_dados.diagnostico
                          , programa_dados.diretriz
                          , programa_dados.publico_alvo
                          , programa_dados.continuo
                          , acao.ultimo_timestamp_acao_dados
                          , ppa.cod_ppa
                          , acao_dados.cod_funcao
                          , acao_dados.cod_subfuncao
                          , funcao.descricao
                          , subfuncao.descricao
                          , acao_dados.cod_tipo
                          , tipo_acao.descricao
                          ";

        $stSQL .= "
                      UNION

                SELECT LPAD(pao.num_pao::VARCHAR,4,'0') AS num_acao
                     , LPAD(pao.num_pao::VARCHAR,4,'0') AS cod_acao
                     , pao.nom_pao AS descricao
                     , pao.nom_pao AS titulo
                     , null AS num_programa
                     , '' AS identificacao
                     , '' AS objetivo
                     , '' AS diagnostico
                     , '' AS diretriz
                     , '' AS publico_alvo
                     , null AS continuo
                     , TO_REAL(0) AS valor
                     , null AS ultimo_timestamp_acao_dados
                     , null AS cod_ppa
                     , null AS cod_funcao
                     , null AS cod_subfuncao
                     , '' AS desc_funcao
                     , '' AS desc_subfuncao
                     , (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) AS cod_tipo
                     , CASE WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) = 1 )
                            THEN 'Projeto'                                                                                                               WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) = 2 )
                            THEN 'Atividade'
                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) = 3 )
                            THEN 'Operações Especiais'
                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) = 4 )
                            THEN 'Não Orçamentária'
                       END AS desc_tipo
                     , pao.exercicio
                  FROM orcamento.pao
                 INNER JOIN ( SELECT num_pao
                                   , MAX(exercicio) AS exercicio
                                FROM orcamento.pao
                            GROUP BY num_pao
                            ) AS max_pao
                         ON max_pao.num_pao   = pao.num_pao
                        AND max_pao.exercicio = pao.exercicio
                      WHERE NOT EXISTS ( SELECT 1
                                           FROM orcamento.pao_ppa_acao
                                          WHERE pao.exercicio = pao_ppa_acao.exercicio
                                            AND pao.num_pao   = pao_ppa_acao.num_pao)
                    ) AS tabela
                LEFT JOIN ".$stTCE.".acao_identificador_acao
                    ON acao_identificador_acao.cod_acao = tabela.cod_acao::integer ";

        return $stSQL . $stCondicao . ' ' . $stOrdem;
    }

    public function recuperaListaAcoesProgramasExclusao(&$rsAcoes, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaListaAcoesProgramasExclusao($stFiltro, $stOrdem);
        $this->setDebug($stSQL);

        return $obConexao->executaSQL($rsAcoes, $stSQL, $boTransacao);
    }

    private function montaRecuperaListaAcoesProgramasExclusao($stFiltro = '', $stOrdem = '')
    {
        $stSQL = "
            SELECT *
              FROM (
                SELECT acao.num_acao
                     , acao.cod_acao
                     , acao_dados.descricao
                     , programa.num_programa
                     , programa_dados.identificacao
                     , programa_dados.objetivo
                     , programa_dados.diagnostico
                     , programa_dados.diretriz
                     , programa_dados.publico_alvo
                     , programa_dados.continuo
                     , to_real(SUM(acao_recurso.valor)) AS valor
                     , acao.ultimo_timestamp_acao_dados
                     , ppa.cod_ppa
                     , acao_dados.cod_funcao
                     , acao_dados.cod_subfuncao
                     , funcao.descricao AS desc_funcao
                     , subfuncao.descricao AS desc_subfuncao
                     , acao_dados.cod_tipo
                     , tipo_acao.descricao as desc_tipo
                     , acao_dados.titulo
                  FROM ppa.acao
            
            INNER JOIN ppa.acao_recurso
                    ON acao.cod_acao                    = acao_recurso.cod_acao
                   AND acao.ultimo_timestamp_acao_dados = acao_recurso.timestamp_acao_dados
            
            INNER JOIN ppa.acao_dados
                    ON acao.cod_acao                    = acao_dados.cod_acao
                   AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
            
            INNER JOIN ppa.tipo_acao
                    ON acao_dados.cod_tipo = tipo_acao.cod_tipo
            
             LEFT JOIN orcamento.funcao
                    ON acao_dados.exercicio  = funcao.exercicio
                   AND acao_dados.cod_funcao = funcao.cod_funcao
            
             LEFT JOIN orcamento.subfuncao
                    ON acao_dados.exercicio     = subfuncao.exercicio
                   AND acao_dados.cod_subfuncao = subfuncao.cod_subfuncao
            
            INNER JOIN ppa.programa
                    ON acao.cod_programa = programa.cod_programa
            
            INNER JOIN ppa.programa_dados
                    ON programa.cod_programa                    = programa_dados.cod_programa
                   AND programa.ultimo_timestamp_programa_dados = programa_dados.timestamp_programa_dados
            
            INNER JOIN ppa.programa_setorial
                    ON programa.cod_setorial = programa_setorial.cod_setorial
            
            INNER JOIN ppa.macro_objetivo
                    ON macro_objetivo.cod_macro = programa_setorial.cod_macro
            
            INNER JOIN ppa.ppa
                    ON macro_objetivo.cod_ppa = ppa.cod_ppa
        ";
       
        $stSQL .= "GROUP BY acao.num_acao
                          , acao.cod_acao
                          , acao_dados.descricao
                          , programa.num_programa
                          , programa_dados.identificacao
                          , programa_dados.objetivo
                          , programa_dados.diagnostico
                          , programa_dados.diretriz
                          , programa_dados.publico_alvo
                          , programa_dados.continuo
                          , acao.ultimo_timestamp_acao_dados
                          , ppa.cod_ppa
                          , acao_dados.cod_funcao
                          , acao_dados.cod_subfuncao
                          , funcao.descricao
                          , subfuncao.descricao
                          , acao_dados.cod_tipo
                          , tipo_acao.descricao
                          , acao_dados.titulo
                ) As tabela";

         $stSQL .= $stFiltro . $stOrdem . "\n";

        return $stSQL;
    }

    function recuperaCodigosAcao(&$rsRecordSet, $stFiltro='', $stOrdem='', $boTransacao='') {
        return $this->executaRecupera("montaRecuperaCodigosAcao",$rsRecordSet,$stFiltro,$stOrdem,$boTransacao);
    }

    function montaRecuperaCodigosAcao()
    {
        $stSql = "    
                SELECT DISTINCT cod_acao
                  FROM ( SELECT cod_acao
                           FROM ppa.acao
                      UNION ALL 
                         SELECT num_pao AS cod_acao
                           FROM orcamento.pao
                       ) AS codigos
              ORDER BY cod_acao
        ";
        return $stSql;
    }

    public function recuperaDadosExportacaoDespesa(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaDadosExportacaoDespesa();
        $this->setDebug($stSQL);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaDadosExportacaoDespesa()
    {
        $stSql = "
            SELECT tipo_registro
                 , cod_despesa
                 , cod_orgao
                 , cod_unidade_sub
                 , cod_funcao
                 , cod_subfuncao
                 , cod_programa
                 , id_acao
                 , '' AS id_sub_acao
                 , natureza_despesa
                 , REPLACE(sum(vl_total_recurso)::VARCHAR, '.',',') AS vl_total_recurso
              FROM (
                   SELECT 10::INTEGER AS tipo_registro
                        , CASE WHEN SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6) = '339009' OR SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6) = '339005'
                          THEN
                              LPAD(acao.num_acao::VARCHAR, 4, '0')||'319005'
                          ELSE
                              CASE WHEN SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6) = '339013'
                              THEN
                                  LPAD(acao.num_acao::VARCHAR, 4, '0')||'319013'
                              ELSE
                                  LPAD(acao.num_acao::VARCHAR, 4, '0')||SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6)::VARCHAR
                              END
                          END AS cod_despesa
                        , configuracao_entidade.valor AS cod_orgao
                        , CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 1, 1) = '9'
                               THEN '09999'
                               ELSE LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0'),5,'0')::VARCHAR
                           END AS cod_unidade_sub
                        , CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 1, 1) = '9'
                               THEN '99'
                               ELSE LPAD(despesa.cod_funcao::VARCHAR, 2, '0')::VARCHAR
                           END AS cod_funcao
                        , CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 1, 1) = '9'
                               THEN '999'
                               ELSE LPAD(despesa.cod_subfuncao::VARCHAR, 3, '0')::VARCHAR
                           END AS cod_subfuncao
                        , CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 1, 1) = '9'
                               THEN '9999'
                               ELSE LPAD(programa.num_programa::VARCHAR, 4, '0')::VARCHAR
                           END AS cod_programa
                        , LPAD(acao.num_acao::VARCHAR, 4, '0')::VARCHAR AS id_acao
                        , CASE WHEN SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6) = '339009' OR SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6) = '339005'
                          THEN
                              '319005'
                          ELSE
                              CASE WHEN SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6) = '339013'
                              THEN
                                  '319013'
                              ELSE
                                  SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6)::VARCHAR
                              END
                          END AS natureza_despesa
                        , despesa.vl_original AS vl_total_recurso
                     FROM orcamento.despesa
                     JOIN orcamento.conta_despesa
                       ON conta_despesa.cod_conta = despesa.cod_conta
                      AND conta_despesa.exercicio = despesa.exercicio
                     JOIN administracao.configuracao_entidade
                       ON configuracao_entidade.cod_entidade = despesa.cod_entidade
                      AND configuracao_entidade.exercicio = despesa.exercicio
                     JOIN orcamento.programa_ppa_programa
                       ON programa_ppa_programa.cod_programa = despesa.cod_programa
                      AND programa_ppa_programa.exercicio   = despesa.exercicio
                     JOIN ppa.programa
                       ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                     JOIN orcamento.pao_ppa_acao
                       ON pao_ppa_acao.num_pao = despesa.num_pao
                      AND pao_ppa_acao.exercicio = despesa.exercicio
                     JOIN ppa.acao
                       ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                    WHERE despesa.exercicio = '".Sessao::getExercicio()."'
                      AND despesa.cod_entidade IN (".$this->getDado('entidades').")
                      AND configuracao_entidade.cod_modulo = 55
                      AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                      AND despesa.vl_original > 0.00
                    GROUP BY cod_despesa
                        , conta_despesa.cod_estrutural
                        , conta_despesa.descricao
                        , cod_orgao
                        , despesa.num_orgao
                        , despesa.num_unidade
                        , despesa.cod_funcao
                        , despesa.cod_subfuncao
                        , programa.num_programa
                        , despesa.vl_original
                        , acao.num_acao
                    ORDER BY tipo_registro, cod_despesa, cod_orgao, natureza_despesa
                    ) AS tabela
              GROUP BY tipo_registro
                  , cod_orgao
                  , cod_despesa
                  , cod_unidade_sub
                  , cod_funcao
                  , cod_subfuncao
                  , cod_programa
                  , id_acao
                  , natureza_despesa
              ORDER BY cod_despesa
            ";
        return $stSql;
    }

    public function recuperaDadosExportacaoDespesaFonteRecurso(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaDadosExportacaoDespesaFonteRecurso();
        $this->setDebug($stSQL);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaDadosExportacaoDespesaFonteRecurso()
    {
        $stSql = "
       SELECT 11::INTEGER AS tipo_registro
            , CASE WHEN SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6) = '339009' OR SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6) = '339005'
                   THEN
                       LPAD(acao.num_acao::VARCHAR, 4, '0')||'319005'
                   ELSE
                       CASE WHEN SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6) = '339013'
                       THEN
                           LPAD(acao.num_acao::VARCHAR, 4, '0')||'319013'
                       ELSE
                           LPAD(acao.num_acao::VARCHAR, 4, '0')||SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 1, 6)::VARCHAR
                       END
                   END AS cod_despesa
            , LPAD(despesa.cod_recurso::VARCHAR, 3, '0')::VARCHAR AS cod_recurso
            , REPLACE(despesa.vl_original::VARCHAR, '.',',') AS vl_total_recurso
         FROM orcamento.despesa
         JOIN orcamento.conta_despesa
           ON conta_despesa.cod_conta = despesa.cod_conta
          AND conta_despesa.exercicio = despesa.exercicio
          
         JOIN administracao.configuracao_entidade
           ON configuracao_entidade.cod_entidade = despesa.cod_entidade
          AND configuracao_entidade.exercicio = despesa.exercicio
          AND configuracao_entidade.cod_modulo = 55
          AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
          
         JOIN orcamento.programa_ppa_programa
           ON programa_ppa_programa.cod_programa = despesa.cod_programa
          AND programa_ppa_programa.exercicio   = despesa.exercicio
         JOIN ppa.programa
           ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
         JOIN orcamento.pao_ppa_acao
           ON pao_ppa_acao.num_pao = despesa.num_pao
          AND pao_ppa_acao.exercicio = despesa.exercicio
         JOIN ppa.acao
           ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
        WHERE despesa.exercicio = '".Sessao::getExercicio()."'
          AND despesa.cod_entidade IN (".$this->getDado('entidades').")
          AND despesa.vl_original > 0.00
        GROUP BY tipo_registro
            , cod_despesa
            , conta_despesa.cod_estrutural
            , conta_despesa.descricao
            , programa.num_programa
            , despesa.vl_original
            , acao.num_acao
            , despesa.cod_recurso
        ORDER BY tipo_registro, cod_despesa
             ";

        return $stSql;
    }
}

?>
