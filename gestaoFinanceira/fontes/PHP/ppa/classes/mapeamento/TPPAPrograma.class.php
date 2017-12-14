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
 * Classe de mapeamento da tabela PPA.PROGRAMA
 * Data de Criação: 03/10/2008

 * @author Analista      : Bruno Ferreira
 * @author Desenvolvedor : Jânio Eduardo

 * @package URBEM
 * @subpackage Mapeamento

 * Casos de uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPPAPrograma extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */

    public function TPPAPrograma()
    {
        parent::Persistente();

        $this->setTabela('ppa.programa');

        $this->setCampoCod('cod_programa');
        $this->setComplementoChave('');
        $this->addCampo('cod_programa'                   , 'integer'  , true, '280', true , false);
        $this->addCampo('cod_setorial'                   , 'integer'  , true, '280', false, true);
        $this->addCampo('ultimo_timestamp_programa_dados', 'timestamp', true, ''   , false, false);
        $this->addCampo('ativo'                          , 'boolean'  , true, ''   , false, false);
        $this->addCampo('num_programa'                   , 'integer'  , true, ''   , false, false);
    }

    public function recuperaPrograma(&$rsRecordSet, $stFiltro='', $stOrdem='', $boTransacao='')
    {
        return $this->executaRecupera("montaRecuperaPrograma",$rsRecordSet,$stFiltro,$stOrdem,$boTransacao);
    }

    public function montaRecuperaPrograma($stCondicao = ' ')
    {
        $stSql = "
                      SELECT LPAD(programa.num_programa::VARCHAR, 4, '0000') AS num_programa
                           , LPAD(programa.cod_programa::VARCHAR, 4, '0000') AS cod_programa
                           , ppa.cod_ppa
                           , ppa.ano_inicio
                           , ppa.ano_final
                           , programa_setorial.cod_setorial
                           , programa_setorial.descricao AS nom_setorial
                           , macro_objetivo.cod_macro
                           , macro_objetivo.descricao AS nom_macro
                           , programa_dados.identificacao
                           , programa_dados.justificativa
                           , programa_dados.diagnostico
                           , programa_dados.objetivo
                           , programa_dados.diretriz
                           , programa_dados.publico_alvo
                           , programa_dados.cod_tipo_programa
                           , programa_dados.exercicio_unidade
                           , programa_dados.num_orgao
                           , programa_dados.num_unidade
                           , tipo_programa.descricao AS nom_tipo_programa
                           , programa_norma.cod_norma
                           , norma.nom_norma
                           , norma.dt_publicacao
                           , ppa.ano_inicio ||' a '|| ppa.ano_final AS periodo
                           , CASE programa_dados.continuo
                                WHEN true  THEN 'Contínuo'
                                WHEN false THEN 'Temporário'
                             END AS continuo
                           , TO_CHAR( programa_temporario_vigencia.dt_inicial , 'DD/MM/YYYY') AS  dt_inicial
                           , TO_CHAR( programa_temporario_vigencia.dt_final , 'DD/MM/YYYY') AS dt_final
                           , programa_setorial.cod_setorial
                           , macro_objetivo.cod_macro
                           , programa.ativo
                        FROM ppa.programa
                  INNER JOIN ppa.programa_dados
                          ON programa_dados.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados
                         AND programa_dados.cod_programa = programa.cod_programa
                  INNER JOIN ppa.programa_setorial
                          ON programa_setorial.cod_setorial = programa.cod_setorial
                  INNER JOIN ppa.macro_objetivo
                          ON macro_objetivo.cod_macro = programa_setorial.cod_macro
                  INNER JOIN ppa.ppa
                          ON ppa.cod_ppa = macro_objetivo.cod_ppa
                  INNER JOIN ppa.tipo_programa
                          ON tipo_programa.cod_tipo_programa = programa_dados.cod_tipo_programa
                   LEFT JOIN ppa.programa_temporario_vigencia
                          ON programa_temporario_vigencia.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados
                         AND programa_temporario_vigencia.cod_programa = programa.cod_programa
                   LEFT JOIN ppa.programa_norma
                          ON programa_norma.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados
                         AND programa_norma.cod_programa = programa.cod_programa
                   LEFT JOIN normas.norma
                          ON norma.cod_norma = programa_norma.cod_norma ";

        $stSql.= "  ".$stCondicao;

        return $stSql;
    }

    public function recuperaProgramaLista(&$rsRecordSet, $stFiltro='', $stOrdem='', $boTransacao='')
    {
        return $this->executaRecupera("montaRecuperaProgramaLista",$rsRecordSet,$stFiltro,$stOrdem,$boTransacao);
    }

    public function montaRecuperaProgramaLista($stCondicao = ' ')
    {
        $stSql = "
               SELECT DISTINCT num_programa
                    , cod_programa
                    , cod_ppa
                    , ano_inicio
                    , ano_final
                    , cod_setorial
                    , nom_setorial
                    , cod_macro
                    , nom_macro
                    , identificacao
                    , justificativa
                    , diagnostico
                    , objetivo
                    , diretriz
                    , publico_alvo
                    , cod_tipo_programa
                    , exercicio_unidade
                    , num_orgao
                    , num_unidade
                    , nom_tipo_programa
                    , cod_norma
                    , nom_norma
                    , dt_publicacao
                    , periodo
                    , continuo
                    , bo_continuo
                    , dt_inicial
                    , dt_final
                    , ativo
                    , cod_tipo_objetivo
                 FROM (
                    ( SELECT LPAD(programa.num_programa::VARCHAR, 4, '0000') AS num_programa
                           , programa.cod_programa
                           , ppa.cod_ppa
                           , ppa.ano_inicio
                           , ppa.ano_final
                           , programa_setorial.cod_setorial
                           , programa_setorial.descricao AS nom_setorial
                           , macro_objetivo.cod_macro
                           , macro_objetivo.descricao AS nom_macro
                           , programa_dados.identificacao
                           , programa_dados.justificativa
                           , programa_dados.diagnostico
                           , programa_dados.objetivo
                           , programa_dados.diretriz
                           , programa_dados.publico_alvo
                           , programa_dados.cod_tipo_programa
                           , programa_dados.exercicio_unidade
                           , programa_dados.num_orgao
                           , programa_dados.num_unidade
                           , tipo_programa.descricao AS nom_tipo_programa
                           , programa_norma.cod_norma
                           , norma.nom_norma
                           , norma.dt_publicacao
                           , ppa.ano_inicio ||' a '|| ppa.ano_final AS periodo
                           , CASE programa_dados.continuo
                                WHEN true  THEN 'Contínuo'
                                WHEN false THEN 'Temporário'
                             END AS continuo
                           , programa_dados.continuo AS bo_continuo
                           , TO_CHAR( programa_temporario_vigencia.dt_inicial , 'DD/MM/YYYY') AS  dt_inicial
                           , TO_CHAR( programa_temporario_vigencia.dt_final , 'DD/MM/YYYY') AS dt_final
                           , programa.ativo
                           , cod_tipo_objetivo AS cod_tipo_objetivo
                        FROM ppa.programa
                  INNER JOIN ppa.programa_dados
                          ON programa_dados.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados
                         AND programa_dados.cod_programa = programa.cod_programa
                  INNER JOIN ppa.programa_setorial
                          ON programa_setorial.cod_setorial = programa.cod_setorial
                  INNER JOIN ppa.macro_objetivo
                          ON macro_objetivo.cod_macro = programa_setorial.cod_macro
                  INNER JOIN ppa.ppa
                          ON ppa.cod_ppa = macro_objetivo.cod_ppa
                  INNER JOIN ppa.tipo_programa
                          ON tipo_programa.cod_tipo_programa = programa_dados.cod_tipo_programa
                   LEFT JOIN ppa.programa_temporario_vigencia
                          ON programa_temporario_vigencia.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados
                         AND programa_temporario_vigencia.cod_programa = programa.cod_programa
                   LEFT JOIN ppa.programa_norma
                          ON programa_norma.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados
                         AND programa_norma.cod_programa = programa.cod_programa
                   LEFT JOIN normas.norma
                          ON norma.cod_norma = programa_norma.cod_norma
                   LEFT JOIN (	SELECT cod_tipo_objetivo
				     , programa.cod_programa
				  FROM tcepb.programa_objetivo_milenio
				  JOIN orcamento.programa
				    ON programa_objetivo_milenio.cod_programa = programa.cod_programa
			           AND programa_objetivo_milenio.exercicio = programa.exercicio
			      ) AS objetivo_milenio
			   ON objetivo_milenio.cod_programa = ppa.programa.cod_programa )

                  UNION ALL

                    ( SELECT LPAD(programa.cod_programa::VARCHAR, 4, '0000') AS num_programa
                           , programa.cod_programa
                           , null AS cod_ppa
                           , null AS ano_inicio
                           , null AS ano_final
                           , null AS cod_setorial
                           , null AS nom_setorial
                           , null AS cod_macro
                           , null AS nom_macro
                           , programa.descricao AS identificacao
                           , null AS justificativa
                           , null AS diagnostico
                           , null AS objetivo
                           , null AS diretriz
                           , null AS publico_alvo
                           , null AS cod_tipo_programa
                           , null AS exercicio_unidade
                           , null AS num_orgao
                           , null AS num_unidade
                           , null AS nom_tipo_programa
                           , null AS cod_norma
                           , null AS nom_norma
                           , null AS dt_publicacao
                           , null AS periodo
                           , null AS continuo
                           , null AS bo_continuo
                           , null AS dt_inicial
                           , null AS dt_final
                           , null AS ativo
                           , programa_objetivo_milenio.cod_tipo_objetivo AS cod_tipo_objetivo
                        FROM orcamento.programa
                        JOIN ( SELECT MAX(exercicio) AS exercicio
                                    , cod_programa
                                 FROM orcamento.programa
                             GROUP BY programa.cod_programa
                             ) AS tabela_programa
                          ON tabela_programa.cod_programa = programa.cod_programa
                         AND tabela_programa.exercicio    = programa.exercicio
                   LEFT JOIN tcepb.programa_objetivo_milenio
                          ON programa_objetivo_milenio.cod_programa = programa.cod_programa
			 AND programa_objetivo_milenio.exercicio = programa.exercicio
                       WHERE NOT EXISTS ( SELECT 1
                                            FROM ppa.programa
                                           WHERE ppa.programa.cod_programa = orcamento.programa.cod_programa ) )
                 ) AS tabela ";

        $stSql.= "  ".$stCondicao;

        return $stSql;
    }

    public function recuperaProgramaListaExclusao(&$rsRecordSet, $stFiltro='', $stOrdem='', $boTransacao='')
    {
        return $this->executaRecupera("montaRecuperaProgramaListaExclusao",$rsRecordSet,$stFiltro,$stOrdem,$boTransacao);
    }

    public function montaRecuperaProgramaListaExclusao($stCondicao = ' ')
    {
        $stSql = "
               SELECT DISTINCT num_programa
                    , cod_programa
                    , cod_ppa
                    , ano_inicio
                    , ano_final
                    , cod_setorial
                    , nom_setorial
                    , cod_macro
                    , nom_macro
                    , identificacao
                    , justificativa
                    , diagnostico
                    , objetivo
                    , diretriz
                    , publico_alvo
                    , cod_tipo_programa
                    , exercicio_unidade
                    , num_orgao
                    , num_unidade
                    , nom_tipo_programa
                    , cod_norma
                    , nom_norma
                    , dt_publicacao
                    , periodo
                    , continuo
                    , bo_continuo
                    , dt_inicial
                    , dt_final
                    , ativo
                 FROM (
                    ( SELECT LPAD(programa.num_programa::VARCHAR, 4, '0000') AS num_programa
                           , programa.cod_programa
                           , ppa.cod_ppa
                           , ppa.ano_inicio
                           , ppa.ano_final
                           , programa_setorial.cod_setorial
                           , programa_setorial.descricao AS nom_setorial
                           , macro_objetivo.cod_macro
                           , macro_objetivo.descricao AS nom_macro
                           , programa_dados.identificacao
                           , programa_dados.justificativa
                           , programa_dados.diagnostico
                           , programa_dados.objetivo
                           , programa_dados.diretriz
                           , programa_dados.publico_alvo
                           , programa_dados.cod_tipo_programa
                           , programa_dados.exercicio_unidade
                           , programa_dados.num_orgao
                           , programa_dados.num_unidade
                           , tipo_programa.descricao AS nom_tipo_programa
                           , programa_norma.cod_norma
                           , norma.nom_norma
                           , norma.dt_publicacao
                           , ppa.ano_inicio ||' a '|| ppa.ano_final AS periodo
                           , CASE programa_dados.continuo
                                WHEN true  THEN 'Contínuo'
                                WHEN false THEN 'Temporário'
                             END AS continuo
                           , programa_dados.continuo AS bo_continuo
                           , TO_CHAR( programa_temporario_vigencia.dt_inicial , 'DD/MM/YYYY') AS  dt_inicial
                           , TO_CHAR( programa_temporario_vigencia.dt_final , 'DD/MM/YYYY') AS dt_final
                           , programa.ativo
                        FROM ppa.programa
                  INNER JOIN ppa.programa_dados
                          ON programa_dados.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados
                         AND programa_dados.cod_programa = programa.cod_programa
                  INNER JOIN ppa.programa_setorial
                          ON programa_setorial.cod_setorial = programa.cod_setorial
                  INNER JOIN ppa.macro_objetivo
                          ON macro_objetivo.cod_macro = programa_setorial.cod_macro
                  INNER JOIN ppa.ppa
                          ON ppa.cod_ppa = macro_objetivo.cod_ppa
                  INNER JOIN ppa.tipo_programa
                          ON tipo_programa.cod_tipo_programa = programa_dados.cod_tipo_programa
                   LEFT JOIN ppa.programa_temporario_vigencia
                          ON programa_temporario_vigencia.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados
                         AND programa_temporario_vigencia.cod_programa = programa.cod_programa
                   LEFT JOIN ppa.programa_norma
                          ON programa_norma.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados
                         AND programa_norma.cod_programa = programa.cod_programa
                   LEFT JOIN normas.norma
                          ON norma.cod_norma = programa_norma.cod_norma )
                 ) AS tabela ";

        $stSql.= "  ".$stCondicao;

        return $stSql;
    }

    public function recuperaOrgao(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaOrgao($stFiltro).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaOrgao($stCondicao = '')
    {
        $stSql = "    SELECT DISTINCT programa_dados.cod_programa                                   \n";
        $stSql.= "         , programa_dados.cod_ppa                                                 \n";
        $stSql.= "         , LPAD(programa_dados.num_programa, 4, '0000') as cod_programa           \n";
        $stSql.= "         , programa_dados.num_orgao                                               \n";
        $stSql.= "         , programa_dados.num_unidade                                             \n";
        $stSql.= "         , programa_dados.exercicio                                               \n";
        $stSql.= "         , orgao.nom_orgao                                                        \n";
        $stSql.= "         , unidade.nom_unidade                                                    \n";
        $stSql.= "         , programa_dados.cod_tipo_programa                                       \n";
        $stSql.= "         , tipo_programa.descricao as nom_tipo_programa                           \n";
        $stSql.= "      FROM ppa.programa                                                           \n";
        $stSql.= "INNER JOIN ppa.programa_dados                                                     \n";
        $stSql.= "           ON programa_dados.ultimo_timestamp_programa_dados = programa_dados.timestamp_programa_dados     \n";
        $stSql.= "INNER JOIN ppa.tipo_programa                                                      \n";
        $stSql.= "           ON programa_dados.cod_tipo_programa = tipo_programa.cod_tipo_programa  \n";
        $stSql.= " LEFT JOIN ppa.programa_temporario_vigencia                                       \n";
        $stSql.= "           ON programa_dados.ultimo_timestamp_programa_dados = programa_temporario_vigencia.timestamp_programa_dados    \n";
        $stSql.= "INNER JOIN orcamento.unidade                                                      \n";
        $stSql.= "           ON unidade.num_orgao   = programa_dados.num_orgao                      \n";
        $stSql.= "          AND unidade.num_unidade = programa_dados.num_orgao                      \n";
        $stSql.= "          AND unidade.exercicio   = programa_dados.exercicio                      \n";
        $stSql.= "INNER JOIN orcamento.orgao                                                        \n";
        $stSql.= "           ON orgao.num_orgao = unidade.num_orgao                                 \n";
        $stSql.= "          AND orgao.exercicio = unidade.exercicio                                 \n";
        $stSql.= $stCondicao;

        return $stSql;
    }

    public function recuperaIndicador(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaIndicador($stFiltro).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaIndicador($stCondicao = '')
    {
        $stSql = "    SELECT DISTINCT programa.num_programa                                         \n";
        $stSql.= "         , ppa.cod_ppa                                                            \n";
        $stSql.= "         , LPAD(programa.num_programa::VARCHAR, 4, '0000') as cod_programa                 \n";
        $stSql.= "         , programa_indicadores.cod_indicador                                     \n";
        $stSql.= "         , publico.fn_numeric_br(programa_indicadores.indice_recente) AS indice_recente \n";
        $stSql.= "         , programa_indicadores.descricao                                         \n";
        $stSql.= "         , publico.fn_numeric_br(programa_indicadores.indice_desejado) AS indice_desejado \n";
        $stSql.= "         , TO_CHAR(TO_DATE(programa_indicadores.dt_indice_recente::VARCHAR, 'yyyy-mm-dd'), 'dd/mm/yyyy') as dt_indice_recente \n";
        $stSql.= "         , programa_indicadores.cod_periodicidade                                 \n";
        $stSql.= "         , programa_indicadores.cod_unidade                                       \n";
        $stSql.= "         , programa_indicadores.cod_grandeza                                      \n";
        $stSql.= "         , programa_indicadores.fonte                                             \n";
        $stSql.= "         , programa_indicadores.forma_calculo                                     \n";
        $stSql.= "         , programa_indicadores.base_geografica                                   \n";
        $stSql.= "      FROM ppa.programa                                                           \n";
        $stSql.= "INNER JOIN ppa.programa_setorial                                                  \n";
        $stSql.= "        ON programa_setorial.cod_setorial = programa.cod_setorial                 \n";
        $stSql.= "INNER JOIN ppa.macro_objetivo                                                     \n";
        $stSql.= "        ON macro_objetivo.cod_macro = programa_setorial.cod_macro                 \n";
        $stSql.= "INNER JOIN ppa.ppa                                                                \n";
        $stSql.= "        ON ppa.cod_ppa = macro_objetivo.cod_ppa                                   \n";
        $stSql.= "INNER JOIN ppa.programa_dados                                                     \n";
        $stSql.= "        ON programa.cod_programa = programa_dados.cod_programa                    \n";
        $stSql.= "       AND programa.ultimo_timestamp_programa_dados = programa_dados.timestamp_programa_dados \n";
        $stSql.= "INNER JOIN ppa.programa_indicadores                                               \n";
        $stSql.= "        ON programa.cod_programa = programa_indicadores.cod_programa              \n";
        $stSql.= "       AND programa.ultimo_timestamp_programa_dados = programa_indicadores.timestamp_programa_dados \n";
        $stSql.= $stCondicao;

        return $stSql;
    }

    public function verificaCodProgramaOrcamento(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = " SELECT cod_programa FROM orcamento.programa ";
        $stSql .= $stFiltro;

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function setOperacao($valor)
    {
        $this->operacao = $valor;
    }

    /**
     * Método que trás a funcionalidade que o Programa faz parte na administracao
     * @return object
     */
    public function recuperaFuncionalidadePrograma(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaFuncionalidadePrograma($stFiltro).$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    /**
     * Método que monta o sql
     * @param $stCondicao
     * @return String
     */
    public function montaFuncionalidadePrograma($stCondicao = '')
    {
        $stSql = "    SELECT f.cod_funcionalidade                                               \n";
        $stSql.= "         , f.nom_funcionalidade                                               \n";
        $stSql.= "         , m.nom_modulo                                                       \n";
        $stSql.= "         , m.cod_modulo                                                       \n";
        $stSql.= "         , a.cod_acao                                                         \n";
        $stSql.= "         , a.nom_arquivo                                                      \n";
        $stSql.= "      FROM administracao.modulo as m                                          \n";
        $stSql.= "INNER JOIN administracao.funcionalidade as f                                  \n";
        $stSql.= "        ON m.cod_modulo = f.cod_modulo                                        \n";
        $stSql.= "INNER JOIN ( SELECT a.cod_funcionalidade                                      \n";
        $stSql.= "                  , a.cod_acao                                                \n";
        $stSql.= "                  , a.nom_arquivo                                             \n";
        $stSql.= "               FROM administracao.acao as a                                   \n";
        $stSql.= "         INNER JOIN administracao.permissao as p                              \n";
        $stSql.= "                 ON a.cod_acao = p.cod_acao                                   \n";
        $stSql.= "                AND p.numcgm = ".Sessao::read('numCgm')."                     \n";
        $stSql.= "                AND p.ano_exercicio = '".Sessao::getExercicio()."'            \n";
        $stSql.= "           GROUP BY a.cod_funcionalidade                                      \n";
        $stSql.= "                  , a.cod_acao                                                \n";
        $stSql.= "                  , a.nom_arquivo                                             \n";
        $stSql.= "           ) AS a                                                             \n";
        $stSql.= "        ON f.cod_funcionalidade = a.cod_funcionalidade                        \n";
        $stSql.= $stCondicao;

        return $stSql;
    }

    public function recuperaCodigosPrograma(&$rsRecordSet, $stFiltro='', $stOrdem='', $boTransacao='')
    {
        return $this->executaRecupera("montaRecuperaCodigosPrograma",$rsRecordSet,$stFiltro,$stOrdem,$boTransacao);
    }

    public function montaRecuperaCodigosPrograma()
    {
        $stSql = "
                SELECT DISTINCT cod_programa
                  FROM ( SELECT cod_programa
                           FROM ppa.programa
                      UNION ALL
                         SELECT cod_programa
                           FROM orcamento.programa
                       ) AS codigos
              ORDER BY cod_programa
        ";

        return $stSql;
    }
} // end of class
