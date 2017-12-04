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

class TTCMGOAMP extends Persistente
{

    public function recuperaDadosTipo10(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();
        
        $stSQL = $this->montaRecuperaDadosTipo10($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        
        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaDadosTipo10($stFiltro = '', $stOrdem = '')
    {        
        $stSql  = " SELECT
                            10 AS tipo_registro 
                          , cod_orgao
                          , cod_unidade
                          , cod_programa
                          , nro_proj_ativ
                          , cod_funcao
                          , cod_subfuncao
                          , meta_fisica_1Ano
                          , meta_fisica_2Ano
                          , meta_fisica_3Ano
                          , meta_fisica_4Ano
                          , meta_financeira_1Ano
                          , meta_financeira_2Ano
                          , meta_financeira_3Ano
                          , meta_financeira_4Ano
                          , cod_natureza
                          , acao_detalhada
                          , unidade_medida
                          , acao_reduzida
                          , produto
                          , esfera_orcamentaria 


                    FROM ( SELECT
                            '".$this->getDado('exercicio')."' AS exercicio
                            , LPAD(programa_dados.num_orgao::varchar,2,'0') AS cod_orgao
                            , LPAD(programa_dados.num_unidade::varchar,4,'0') AS cod_unidade
                            , LPAD(programa.num_programa::varchar,4,'0') AS cod_programa
                            , LPAD(acao.num_acao::varchar,4,'0') AS nro_proj_ativ
                            , REPLACE(Ano1.quantidade::VARCHAR, '.', ',') AS meta_fisica_1Ano
                            , REPLACE(Ano2.quantidade::VARCHAR, '.', ',') AS meta_fisica_2Ano
                            , REPLACE(Ano3.quantidade::VARCHAR, '.', ',') AS meta_fisica_3Ano
                            , REPLACE(Ano4.quantidade::VARCHAR, '.', ',') AS meta_fisica_4Ano
                            , REPLACE(Ano1.valor::VARCHAR, '.', ',') AS meta_financeira_1Ano    
                            , REPLACE(Ano2.valor::VARCHAR, '.', ',') AS meta_financeira_2Ano
                            , REPLACE(Ano3.valor::VARCHAR, '.', ',') AS meta_financeira_3Ano
                            , REPLACE(Ano4.valor::VARCHAR, '.', ',') AS meta_financeira_4Ano
                            , acao_dados.cod_subfuncao
                            , acao_dados.cod_funcao
                            , CASE WHEN acao_dados.cod_tipo = 3 THEN 9
                                   ELSE acao_dados.cod_tipo
                               END AS cod_natureza
                            , acao_dados.detalhamento AS acao_detalhada
                            , acao_dados.cod_unidade_medida AS unidade_medida
                            , acao_dados.descricao AS acao_reduzida
                            , produto.descricao AS produto
                            , programa_dados.publico_alvo
                            , CASE WHEN acao_dados.cod_tipo_orcamento != 1 AND acao_dados.cod_tipo_orcamento != 2 THEN 0 ELSE acao_dados.cod_tipo_orcamento END AS esfera_orcamentaria 

                        FROM ppa.programa
               
                  INNER JOIN ppa.acao
                          ON programa.cod_programa = acao.cod_programa

                  INNER JOIN ppa.acao_quantidade
                          ON acao.cod_acao = acao_quantidade.cod_acao

                  INNER JOIN ppa.acao_dados
                          ON acao_dados.cod_acao = acao.cod_acao
                         AND acao_dados.timestamp_acao_dados = acao.ultimo_timestamp_acao_dados

                  INNER JOIN ppa.produto
                          ON produto.cod_produto = acao_dados.cod_produto

                  INNER JOIN (
                                SELECT *
                                  FROM ppa.acao_quantidade AS Ano1
                                 WHERE Ano1.exercicio_recurso = ((SELECT ano_inicio::INTEGER FROM ppa.ppa WHERE ".$this->getDado('exercicio')." BETWEEN ppa.ano_inicio::INTEGER AND ano_final::INTEGER)::VARCHAR)
                            ) AS Ano1
                          ON Ano1.cod_acao             = acao_quantidade.cod_acao
                         AND Ano1.timestamp_acao_dados = ( SELECT times.timestamp_acao_dados
                                                             FROM ppa.acao_quantidade as times
                                                            WHERE times.cod_acao          = Ano1.cod_acao
                                                              AND times.exercicio_recurso = Ano1.exercicio_recurso
                                                         ORDER BY timestamp_acao_dados DESC LIMIT 1
                                                        )

                  INNER JOIN (
                                SELECT *
                                  FROM ppa.acao_quantidade AS Ano2
                                 WHERE Ano2.exercicio_recurso = (((SELECT ano_inicio::INTEGER FROM ppa.ppa WHERE ".$this->getDado('exercicio')." BETWEEN ppa.ano_inicio::INTEGER AND ano_final::INTEGER)+1)::VARCHAR)
                            ) AS Ano2
                          ON Ano2.cod_acao             = acao_quantidade.cod_acao
                         AND Ano2.timestamp_acao_dados = ( SELECT times.timestamp_acao_dados
                                                             FROM ppa.acao_quantidade AS times
                                                            WHERE times.cod_acao          = Ano2.cod_acao
                                                              AND times.exercicio_recurso = Ano2.exercicio_recurso
                                                         ORDER BY timestamp_acao_dados DESC LIMIT 1
                                                        )

                  INNER JOIN (
                                SELECT *
                                  FROM ppa.acao_quantidade AS Ano3
                                 WHERE Ano3.exercicio_recurso = (((SELECT ano_inicio::INTEGER FROM ppa.ppa WHERE ".$this->getDado('exercicio')." BETWEEN ppa.ano_inicio::INTEGER AND ano_final::INTEGER)+2)::VARCHAR)
                            ) AS Ano3
                          ON Ano3.cod_acao             = acao_quantidade.cod_acao
                         AND Ano3.timestamp_acao_dados = ( SELECT times.timestamp_acao_dados
                                                             FROM ppa.acao_quantidade AS times
                                                            WHERE times.cod_acao          = Ano3.cod_acao
                                                              AND times.exercicio_recurso = Ano3.exercicio_recurso
                                                         ORDER BY timestamp_acao_dados DESC LIMIT 1
                                                        )

                  INNER JOIN (
                                SELECT *
                                  FROM ppa.acao_quantidade AS Ano4
                                 WHERE Ano4.exercicio_recurso = (((SELECT ano_inicio::INTEGER FROM ppa.ppa WHERE ".$this->getDado('exercicio')." BETWEEN ppa.ano_inicio::INTEGER AND ano_final::INTEGER)+3)::VARCHAR)
                            ) AS Ano4
                          ON Ano4.cod_acao             = acao_quantidade.cod_acao
                         AND Ano4.timestamp_acao_dados = ( SELECT times.timestamp_acao_dados
                                                             FROM ppa.acao_quantidade AS times
                                                            WHERE times.cod_acao          = Ano4.cod_acao
                                                              AND times.exercicio_recurso = Ano4.exercicio_recurso
                                                         ORDER BY timestamp_acao_dados DESC LIMIT 1
                                                        )

                  INNER JOIN ppa.programa_dados
                          ON programa_dados.cod_programa = programa.cod_programa

                  INNER JOIN ppa.programa_setorial
                          ON programa.cod_setorial = programa_setorial.cod_setorial

                  INNER JOIN ppa.macro_objetivo
                          ON programa_setorial.cod_macro = macro_objetivo.cod_macro

                  INNER JOIN ppa.ppa
                          ON macro_objetivo.cod_ppa = ppa.cod_ppa

                       WHERE acao_quantidade.exercicio_recurso    = '".$this->getDado('exercicio')."'
                         AND ppa.cod_ppa = ((SELECT cod_ppa::INTEGER FROM ppa.ppa WHERE ".$this->getDado('exercicio')." BETWEEN ppa.ano_inicio::INTEGER AND ano_final::INTEGER)::INTEGER)
                         --AND acao_quantidade.timestamp_acao_dados = Ano1.timestamp_acao_dados

                    GROUP BY   programa.cod_programa 
                             , nro_proj_ativ
                             , programa_dados.num_orgao
                             , programa_dados.num_unidade
                             , programa_dados.publico_alvo
                             , acao_dados.cod_tipo_orcamento
                             , acao_dados.detalhamento
                             , acao_dados.cod_unidade_medida
                             , acao_dados.cod_subfuncao
                             , acao_dados.cod_funcao
                             , acao_dados.cod_tipo
                             , produto.descricao
                             , Ano1.quantidade
                             , Ano2.quantidade
                             , Ano3.quantidade
                             , Ano4.quantidade
                             , Ano1.valor
                             , Ano2.valor
                             , Ano3.valor
                             , Ano4.valor
                             , acao_dados.descricao

                    ORDER BY  cod_programa
                            , nro_proj_ativ
                        ) AS TBL
                    ";
        return $stSql;
    }

}
?>