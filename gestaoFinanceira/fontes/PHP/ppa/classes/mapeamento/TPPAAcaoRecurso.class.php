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
 * Classe de mapeamento da tabela ppa.acao_recurso
 * Data de Criação: 03/10/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Mapeamento

 $Id: TPPAAcaoRecurso.class.php 38190 2009-02-16 18:56:27Z janio.magalhaes $

 * Casos de uso: uc-02.09.04
 */

class TPPAAcaoRecurso extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ppa.acao_recurso');

        $this->setCampoCod('cod_acao');
        $this->setComplementoChave('cod_programa');

        $this->addCampo('cod_acao', 'integer', true, '', true, false);
        $this->addCampo('timestamp_acao_dados', 'timestamp', true, '', true, true);
        $this->addCampo('cod_recurso', 'integer', true, '', true, true);
        $this->addCampo('exercicio_recurso', 'char', true, '4', false, true);
        $this->addCampo('ano', 'character', true, '', true, false);
        $this->addCampo('valor', 'numeric', true, '14,2', false, false);
    }

    public function recuperaDados(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaDados($stFiltro, $stOrdem);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaDados($stFiltro, $stOrdem)
    {
        $stSQL = '';

        if ($stFiltro) {
            $stFiltro = ' WHERE ' . $stFiltro;
        }

        if ($stOrdem) {
            $stOrdem = ' ORDER BY ' . $stOrdem;
        }

        $stSQL .= "SELECT  acao_recurso.cod_acao                                        \n";
        $stSQL .= "       ,acao_recurso.timestamp_acao_dados                                  \n";
        $stSQL .= "       ,acao_recurso.cod_recurso                                     \n";
        $stSQL .= "       ,acao_recurso.exercicio_recurso                               \n";
        $stSQL .= "       ,acao_recurso.ano                                             \n";
        $stSQL .= "       ,acao_recurso.valor                                           \n";
        $stSQL .= "       ,recurso.nom_recurso                                          \n";
        $stSQL .= "  FROM ppa.acao_recurso                                              \n";
        $stSQL .= "       INNER JOIN orcamento.recurso                                  \n";
        $stSQL .= "       ON acao_recurso.exercicio_recurso = recurso.exercicio AND     \n";
        $stSQL .= "          acao_recurso.cod_recurso = recurso.cod_recurso             \n";
        $stSQL .= $stFiltro . $stOrdem;

        return $stSQL;
    }

    public function recuperaRecursosAcao(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaRecursosAcao($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaRecursosAcao($stFiltro = '', $stOrdem = '')
    {
        $stSQL = '';

        if ($stFiltro) {
            $stFiltro = "\n            AND ".$stFiltro;
        }

        if ($stOrdem) {
            $stOrdem  = "\n       ORDER BY ".$stOrdem;
        }

    $stSQL .= "\n         SELECT ano1.cod_acao";
    $stSQL .= "\n              , ano1.timestamp_acao_dados";
    $stSQL .= "\n              , recurso.masc_recurso AS cod_recurso_mascarado";
    $stSQL .= "\n              , ano1.cod_recurso";
    $stSQL .= "\n              , recurso.nom_recurso";
    $stSQL .= "\n              , recurso.masc_recurso||' - '||recurso.nom_recurso AS nom_cod_recurso";
    $stSQL .= "\n              , ano1.exercicio_recurso";
    $stSQL .= "\n              , ano1.valor AS ano1";
    $stSQL .= "\n              , COALESCE(ano2.valor, 0.00) AS ano2";
    $stSQL .= "\n              , COALESCE(ano3.valor, 0.00) AS ano3";
    $stSQL .= "\n              , COALESCE(ano4.valor, 0.00) AS ano4";
        $stSQL .= "\n              , ano1.valor + COALESCE(ano2.valor, 0.00) + COALESCE(ano3.valor, 0.00) + COALESCE(ano4.valor, 0.00) as total";
    $stSQL .= "\n           FROM ppa.acao_recurso AS ano1";
        $stSQL .= "\n     INNER JOIN orcamento.recurso('".Sessao::getExercicio()."') AS recurso";
        $stSQL .= "\n             ON ano1.cod_recurso   = recurso.cod_recurso";
    $stSQL .= "\n      LEFT JOIN ppa.acao_recurso AS ano2";
    $stSQL .= "\n             ON ano2.ano = '2'";
    $stSQL .= "\n            AND ano1.cod_acao             = ano2.cod_acao";
    $stSQL .= "\n            AND ano1.timestamp_acao_dados = ano2.timestamp_acao_dados";
    $stSQL .= "\n            AND ano1.cod_recurso          = ano2.cod_recurso";
    $stSQL .= "\n      LEFT JOIN ppa.acao_recurso AS ano3";
    $stSQL .= "\n             ON ano3.ano = '3'";
    $stSQL .= "\n            AND ano1.cod_acao             = ano3.cod_acao";
    $stSQL .= "\n            AND ano1.timestamp_acao_dados = ano3.timestamp_acao_dados";
    $stSQL .= "\n            AND ano1.cod_recurso          = ano3.cod_recurso";
    $stSQL .= "\n      LEFT JOIN ppa.acao_recurso AS ano4";
    $stSQL .= "\n             ON ano4.ano = '4'";
    $stSQL .= "\n            AND ano1.cod_acao             = ano4.cod_acao";
    $stSQL .= "\n            AND ano1.timestamp_acao_dados = ano4.timestamp_acao_dados";
    $stSQL .= "\n            AND ano1.cod_recurso          = ano4.cod_recurso";
    $stSQL .= "\n          WHERE ano1.ano = '1'";
    $stSQL .= $stFiltro;
    $stSQL .= $stOrdem;

        return $stSQL;
    }

    public function recuperaDadosDespesa(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaDadosDespesa($stFiltro, $stOrdem);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaDadosDespesa($stFiltro, $stOrdem)
    {

        $stSQL  = "   SELECT recurso.masc_recurso AS cod_recurso                                 \n";
        $stSQL .= "        , recurso.nom_recurso                                                 \n";
        $stSQL .= "        , SUM(recursos.vl_estimado) AS vl_estimado                            \n";
        $stSQL .= "        , SUM(recursos.vl_despesa) AS vl_despesa                              \n";
        $stSQL .= "        , SUM(recursos.vl_estimado) - SUM(recursos.vl_despesa) AS vl_total    \n";
        $stSQL .= "     FROM ( SELECT acao_validada.cod_recurso                                  \n";
        $stSQL .= "                 , acao_validada.cod_acao                                     \n";
        $stSQL .= "                 , CAST(acao_validada.ano AS INTEGER) AS ano                  \n";
        $stSQL .= "                 , SUM(acao_validada.valor) AS vl_estimado                    \n";
        $stSQL .= "                 , 0.00 AS vl_despesa                                         \n";
        $stSQL .= "              FROM ldo.acao_validada                                          \n";
        $stSQL .= "             WHERE acao_validada.timestamp_acao_dados = ( SELECT MAX(timestamp_acao_dados)                                             \n";
        $stSQL .= "                                                            FROM ldo.acao_validada AS max_acao_validada                                \n";
        $stSQL .= "                                                           WHERE max_acao_validada.cod_acao          = acao_validada.cod_acao          \n";
        $stSQL .= "                                                             AND max_acao_validada.cod_recurso       = acao_validada.cod_recurso       \n";
        $stSQL .= "                                                             AND max_acao_validada.exercicio_recurso = acao_validada.exercicio_recurso \n";
        $stSQL .= "                                                             AND max_acao_validada.ano               = acao_validada.ano )             \n";
        $stSQL .= "          GROUP BY acao_validada.cod_recurso                                  \n";
        $stSQL .= "                 , acao_validada.cod_acao                                     \n";
        $stSQL .= "                 , acao_validada.ano                                          \n";
        $stSQL .= "                                                                              \n";
        $stSQL .= "         UNION ALL                                                            \n";
        $stSQL .= "                                                                              \n";
        $stSQL .= "            SELECT despesa.cod_recurso                                        \n";
        $stSQL .= "                 , despesa_acao.cod_acao                                      \n";
        $stSQL .= "                 , CAST(despesa.exercicio AS INTEGER) - CAST(ano_inicio AS INTEGER) + 1 AS ano \n";
        $stSQL .= "                 , 0.00 AS vl_estimado                                        \n";
        $stSQL .= "                 , SUM(despesa.vl_original) AS vl_despesa                     \n";
        $stSQL .= "              FROM orcamento.despesa                                          \n";
        $stSQL .= "              JOIN orcamento.despesa_acao                                     \n";
        $stSQL .= "                ON despesa_acao.cod_despesa       = despesa.cod_despesa       \n";
        $stSQL .= "               AND despesa_acao.exercicio_despesa = despesa.exercicio         \n";
        $stSQL .= "              JOIN ppa.acao                                                   \n";
        $stSQL .= "                ON acao.cod_acao = despesa_acao.cod_acao                      \n";
        $stSQL .= "              JOIN ppa.programa                                               \n";
        $stSQL .= "                ON programa.cod_programa = acao.cod_programa                  \n";
        $stSQL .= "              JOIN ppa.programa_setorial                                      \n";
        $stSQL .= "                ON programa_setorial.cod_setorial = programa.cod_setorial     \n";
        $stSQL .= "              JOIN ppa.macro_objetivo                                         \n";
        $stSQL .= "                ON macro_objetivo.cod_macro = programa_setorial.cod_macro     \n";
        $stSQL .= "              JOIN ppa.ppa                                                    \n";
        $stSQL .= "                ON ppa.cod_ppa = macro_objetivo.cod_ppa                       \n";
        $stSQL .= "             WHERE despesa.exercicio = '".Sessao::getExercicio()."'           \n";
        $stSQL .= "          GROUP BY despesa.cod_recurso                                        \n";
        $stSQL .= "                 , despesa_acao.cod_acao                                      \n";
        $stSQL .= "                 , despesa.exercicio                                          \n";
        $stSQL .= "                 , ppa.ano_inicio                                             \n";
        $stSQL .= "        ) AS recursos                                                         \n";
        $stSQL .= "     JOIN orcamento.recurso('".Sessao::getExercicio()."')                     \n";
        $stSQL .= "       ON recurso.cod_recurso = recursos.cod_recurso                          \n";
        $stSQL .= "    WHERE true                                                                \n";
        $stSQL .= $stFiltro."                                                                    \n";
        $stSQL .= " GROUP BY recursos.cod_recurso                                                \n";
        $stSQL .= "        , recurso.nom_recurso                                                 \n";
        $stSQL .= "        , recurso.masc_recurso                                                \n";
        $stSQL .= "        , recursos.cod_acao                                                   \n";

        $stSQL .= $stOrdem;

        return $stSQL;
    }

    public function recuperaRecursosAcaoMetasFisicas(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stOrdem = ( $stOrdem != "" ) ? " ORDER BY ".$stOrdem : " ORDER BY acao.num_acao, ano1.cod_recurso ";

        $stGroupBy = " GROUP BY ano1.cod_acao
                              , acao.num_acao
                              , ano1.timestamp_acao_dados
                              , recurso.masc_recurso
                              , ano1.cod_recurso
                              , recurso.nom_recurso
                              , ano1.exercicio_recurso
                              , ano2.exercicio_recurso
                              , ano3.exercicio_recurso
                              , ano4.exercicio_recurso
                              , ano1.valor
                              , ano2.valor
                              , ano3.valor
                              , ano4.valor
                              , qtd_ano1.quantidade
                              , qtd_ano2.quantidade
                              , qtd_ano3.quantidade
                              , qtd_ano4.quantidade
                              , ppa.cod_ppa
                              , realizada_1.valor
                              , realizada_2.valor
                              , realizada_3.valor
                              , realizada_4.valor
                              , realizada_1.justificativa
                              , realizada_2.justificativa
                              , realizada_3.justificativa
                              , realizada_4.justificativa \n";

        $stSql = $this->montaRecuperaDadosDespesaMetasFisicas().$stFiltro.$stGroupBy.$stOrdem;
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaDadosDespesaMetasFisicas()
    {
        $stSQL = "
                    SELECT ano1.cod_acao
                         , LPAD(acao.num_acao::TEXT, 4, '0') AS num_acao
                         , ano1.timestamp_acao_dados
                         , recurso.masc_recurso AS cod_recurso_mascarado
                         , ano1.cod_recurso
                         , recurso.nom_recurso
                         , recurso.masc_recurso||' - '||recurso.nom_recurso AS nom_cod_recurso
                         , ano1.exercicio_recurso AS ano1
                         , COALESCE(qtd_ano1.quantidade, 0.00) AS ano1_qtd
                         , COALESCE(ano1.valor, 0.00) AS ano1_valor
                         , COALESCE(realizada_1.valor, 0.00) AS ano1_realizada
                         , realizada_1.justificativa AS ano1_justificativa
                         , ano2.exercicio_recurso AS ano2
                         , COALESCE(qtd_ano2.quantidade, 0.00) AS ano2_qtd
                         , COALESCE(ano2.valor, 0.00) AS ano2_valor
                         , COALESCE(realizada_2.valor, 0.00) AS ano2_realizada
                         , realizada_2.justificativa AS ano2_justificativa
                         , ano3.exercicio_recurso AS ano3
                         , COALESCE(qtd_ano3.quantidade, 0.00) AS ano3_qtd
                         , COALESCE(ano3.valor, 0.00) AS ano3_valor
                         , COALESCE(realizada_3.valor, 0.00) AS ano3_realizada
                         , realizada_3.justificativa AS ano3_justificativa
                         , ano4.exercicio_recurso AS ano4
                         , COALESCE(qtd_ano4.quantidade, 0.00) AS ano4_qtd
                         , COALESCE(ano4.valor, 0.00) AS ano4_valor
                         , COALESCE(realizada_4.valor, 0.00) AS ano4_realizada
                         , realizada_4.justificativa AS ano4_justificativa
                         , COALESCE(qtd_ano1.quantidade, 0.00) + COALESCE(qtd_ano2.quantidade, 0.00) + COALESCE(qtd_ano3.quantidade, 0.00) + COALESCE(qtd_ano4.quantidade, 0.00) as total_qtd
                         , COALESCE(ano1.valor, 0.00) + COALESCE(ano2.valor, 0.00) + COALESCE(ano3.valor, 0.00) + COALESCE(ano4.valor, 0.00) as total_valor
                         , ppa.cod_ppa
                         , ppa.cod_ppa||' - '||ppa.ano_inicio||' a '||ppa.ano_final AS nom_ppa
                      FROM ppa.acao_recurso AS ano1
                INNER JOIN orcamento.recurso('".Sessao::getExercicio()."') AS recurso
                        ON ano1.cod_recurso   = recurso.cod_recurso
                 LEFT JOIN ppa.acao_recurso AS ano2
                        ON ano2.ano = '2'
                       AND ano1.cod_acao             = ano2.cod_acao
                       AND ano1.timestamp_acao_dados = ano2.timestamp_acao_dados
                       AND ano1.cod_recurso          = ano2.cod_recurso
                 LEFT JOIN ppa.acao_recurso AS ano3
                        ON ano3.ano = '3'
                       AND ano1.cod_acao             = ano3.cod_acao
                       AND ano1.timestamp_acao_dados = ano3.timestamp_acao_dados
                       AND ano1.cod_recurso          = ano3.cod_recurso
                 LEFT JOIN ppa.acao_recurso AS ano4
                        ON ano4.ano = '4'
                       AND ano1.cod_acao             = ano4.cod_acao
                       AND ano1.timestamp_acao_dados = ano4.timestamp_acao_dados
                       AND ano1.cod_recurso          = ano4.cod_recurso
                 LEFT JOIN ppa.acao_quantidade as qtd_ano1
                        ON qtd_ano1.ano                  = ano1.ano
                       AND qtd_ano1.cod_acao             = ano1.cod_acao
                       AND qtd_ano1.timestamp_acao_dados = ano1.timestamp_acao_dados
                       AND qtd_ano1.cod_recurso          = ano1.cod_recurso
                 LEFT JOIN ppa.acao_quantidade as qtd_ano2
                        ON qtd_ano2.ano                  = ano2.ano
                       AND qtd_ano2.cod_acao             = ano2.cod_acao
                       AND qtd_ano2.timestamp_acao_dados = ano2.timestamp_acao_dados
                       AND qtd_ano2.cod_recurso          = ano2.cod_recurso
                 LEFT JOIN ppa.acao_quantidade as qtd_ano3
                        ON qtd_ano3.ano                  = ano3.ano
                       AND qtd_ano3.cod_acao             = ano3.cod_acao
                       AND qtd_ano3.timestamp_acao_dados = ano3.timestamp_acao_dados
                       AND qtd_ano3.cod_recurso          = ano3.cod_recurso
                 LEFT JOIN ppa.acao_quantidade as qtd_ano4
                        ON qtd_ano4.ano                  = ano4.ano
                       AND qtd_ano4.cod_acao             = ano4.cod_acao
                       AND qtd_ano4.timestamp_acao_dados = ano4.timestamp_acao_dados
                       AND qtd_ano4.cod_recurso          = ano4.cod_recurso

                INNER JOIN ppa.acao
                        ON acao.cod_acao = ano1.cod_acao
                       AND acao.ultimo_timestamp_acao_dados = ano1.timestamp_acao_dados
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

                 LEFT JOIN ppa.acao_meta_fisica_realizada AS realizada_1
                        ON realizada_1.cod_acao             = ano1.cod_acao
                       AND realizada_1.timestamp_acao_dados = ano1.timestamp_acao_dados
                       AND realizada_1.cod_recurso          = ano1.cod_recurso
                       AND realizada_1.exercicio_recurso    = ano1.exercicio_recurso
                       AND realizada_1.ano                  = ano1.ano
                 LEFT JOIN ppa.acao_meta_fisica_realizada AS realizada_2
                        ON realizada_2.cod_acao             = ano2.cod_acao
                       AND realizada_2.timestamp_acao_dados = ano2.timestamp_acao_dados
                       AND realizada_2.cod_recurso          = ano2.cod_recurso
                       AND realizada_2.exercicio_recurso    = ano2.exercicio_recurso
                       AND realizada_2.ano                  = ano2.ano
                 LEFT JOIN ppa.acao_meta_fisica_realizada AS realizada_3
                        ON realizada_3.cod_acao             = ano3.cod_acao
                       AND realizada_3.timestamp_acao_dados = ano3.timestamp_acao_dados
                       AND realizada_3.cod_recurso          = ano3.cod_recurso
                       AND realizada_3.exercicio_recurso    = ano3.exercicio_recurso
                       AND realizada_3.ano                  = ano3.ano
                 LEFT JOIN ppa.acao_meta_fisica_realizada AS realizada_4
                        ON realizada_4.cod_acao             = ano4.cod_acao
                       AND realizada_4.timestamp_acao_dados = ano4.timestamp_acao_dados
                       AND realizada_4.cod_recurso          = ano4.cod_recurso
                       AND realizada_4.exercicio_recurso    = ano4.exercicio_recurso
                       AND realizada_4.ano                  = ano4.ano

                     WHERE ano1.ano = '1'
                       AND ".Sessao::getExercicio()." BETWEEN ppa.ano_inicio::INTEGER AND ppa.ano_final::INTEGER
                       AND ano1.timestamp_acao_dados = ( SELECT MAX(timestamp_acao_dados)
                                                           FROM ppa.acao_recurso AS AR
                                                          WHERE AR.cod_acao = ano1.cod_acao
                                                            AND AR.cod_recurso = ano1.cod_recurso
                                                            AND AR.exercicio_recurso = ano1.exercicio_recurso
                                                            AND AR.ano = ano1.ano )
                       ";

        return $stSQL;
    }

}

?>
