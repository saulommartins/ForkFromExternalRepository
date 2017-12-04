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
    * Extensão da Classe de mapeamento
    * Data de Criação: 30/01/2007

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TTCMGOLNC.class.php 65190 2016-04-29 19:36:51Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMGOLNC extends Persistente{
    /**
    * Método Construtor
    * @access Private
    */
    public function __construct()
    {
    parent::Persistente();

    $this->setDado('exercicio', Sessao::getExercicio() );
    }
    
    public function recuperaLancamentoContabil(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;

       $stSql = $this->montaRecuperaLancamentoContabil();
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

       return $obErro;
    }
    
    public function montaRecuperaLancamentoContabil()
    {
        $stSql = "
            SELECT '10' AS tipo_registro
                 , (SELECT cod_tipo FROM tcmgo.orgao WHERE exercicio = '".$this->getDado('exercicio')."')::INTEGER AS tipo_unidade
                 , (LPAD(lo.cod_entidade::VARCHAR,2,'0')||LPAD(oa.num_letra::VARCHAR,2,'0')||LPAD(lo.cod_lote::VARCHAR, 9,'0')) AS num_controle
                 , TO_CHAR(lo.dt_lote,'mm') AS mes_referencia
                 , TO_CHAR(lo.dt_lote,'ddmmyyyy') AS data_registro
                 , CASE WHEN l.tipo = 'I'
                        THEN 1
                        WHEN l.tipo = 'M' AND l.cod_historico BETWEEN 800 and 899
                        THEN 3
                        ELSE 2
                    END AS tipo_lancamento
                 , TO_CHAR(lo.dt_lote,'ddmmyyyy') AS data_transacao
                 , hc.nom_historico AS historico
              FROM contabilidade.lancamento AS l
              JOIN contabilidade.lote AS lo 
                ON lo.cod_lote      = l.cod_lote
               AND lo.exercicio     = l.exercicio
               AND lo.tipo          = l.tipo
               AND lo.cod_entidade  = l.cod_entidade
              JOIN contabilidade.historico_contabil AS hc
                ON hc.cod_historico = l.cod_historico
               AND hc.exercicio     = l.exercicio
              JOIN tcmgo.ordem_alfabetica AS oa
                ON oa.letra = UPPER(l.tipo)
             WHERE l.exercicio = '".$this->getDado('exercicio')."'
               AND l.cod_entidade IN (".$this->getDado('entidade').")
               AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                  AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
            GROUP BY tipo_registro, tipo_unidade, lo.cod_lote, lo.dt_lote, l.tipo, l.cod_historico, hc.nom_historico, lo.exercicio, lo.cod_entidade, oa.num_letra
            ORDER BY num_controle, mes_referencia, data_transacao
        ";
        return $stSql;
    }
    
    public function recuperaDetalhamentoLancamentoContabil(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;

       $stSql = $this->montaRecuperaDetalhamentoLancamentoContabil();
       $this->setDebug( $stSql );     
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

       return $obErro;
    }
    
   
    
    public function montaRecuperaDetalhamentoLancamentoContabil()
    {
      
        $stSql = " -- CONTA CRÉDITO
                  SELECT tipo_registro
                       , tipo_unidade
                       , num_controle
                       , cod_conta
                       , atributo_conta
                       , natureza_lancamento
                       , REPLACE(REPLACE(SUM(valor)::VARCHAR, '.',','),'-','') AS valor
                       , tipo
                       , tipo_arquivo_sicom
                       , chave_arquivo
                   FROM (
                          -- REGISTRO A
                         SELECT DISTINCT '11' AS tipo_registro
                              , (SELECT cod_tipo FROM tcmgo.orgao WHERE exercicio =  '".$this->getDado('exercicio')."')::INTEGER AS tipo_unidade
                              , (LPAD(lo.cod_entidade::VARCHAR,2,'0')||LPAD(oa.num_letra::VARCHAR,2,'0')||LPAD(lo.cod_lote::VARCHAR, 9,'0')) AS num_controle
                              , REPLACE(pc.cod_estrutural, '.','') AS cod_conta
                              , CASE WHEN pc.indicador_superavit = 'financeiro'
                                     THEN 1
                                     WHEN pc.indicador_superavit = 'permanente'
                                     THEN 2
                                     ELSE 0
                                 END AS atributo_conta
                              , cc.tipo_valor AS natureza_lancamento
                              , CASE WHEN (vl.vl_lancamento < 0) THEN SUM(vl.vl_lancamento *-1)
                                     ELSE SUM (vl.vl_lancamento ) END AS valor
                              , l.tipo
                              , CASE WHEN l.tipo = 'A' THEN 
                                    CASE 
                                      WHEN lr.estorno = FALSE THEN 1 -- REC 
                                      ELSE 2 -- ARE 
                                    END                
                                END AS tipo_arquivo_sicom
                              , CASE WHEN l.tipo = 'I' OR l.tipo = 'M' THEN LPAD('0',150,'0')
                                     WHEN l.tipo = 'T' THEN
                                        CASE WHEN tt.cod_tipo = 3 OR tt.cod_tipo = 4 OR tt.cod_tipo = 5
                                            THEN 'Registro 11'
                                            ELSE 'Registro 10'
                                        END
                                     ELSE 'Registro 10'
                                END AS chave_arquivo
                 
                           FROM contabilidade.lancamento AS l
                 
                           JOIN contabilidade.lote AS lo
                             ON lo.cod_lote      = l.cod_lote
                            AND lo.exercicio     = l.exercicio
                            AND lo.tipo          = l.tipo
                            AND lo.cod_entidade  = l.cod_entidade 
                 
                           JOIN contabilidade.valor_lancamento AS vl
                             ON vl.cod_lote      = l.cod_lote
                            AND vl.tipo          = l.tipo
                            AND vl.sequencia     = l.sequencia
                            AND vl.exercicio     = l.exercicio
                            AND vl.cod_entidade  = l.cod_entidade
                 
                           JOIN tcmgo.ordem_alfabetica AS oa
                             ON oa.letra = UPPER(l.tipo)
                 
                           JOIN contabilidade.conta_credito AS cc
                             ON cc.exercicio    = vl.exercicio
                            AND cc.cod_lote     = vl.cod_lote
                            AND cc.tipo         = vl.tipo
                            AND cc.sequencia    = vl.sequencia
                            AND cc.tipo_valor   = vl.tipo_valor
                            AND cc.cod_entidade = vl.cod_entidade
                 
                           JOIN contabilidade.plano_analitica AS pa
                             ON cc.cod_plano = pa.cod_plano
                            AND cc.exercicio = pa.exercicio
                 
                           JOIN contabilidade.plano_conta AS pc
                             ON pc.exercicio = pa.exercicio
                            AND pc.cod_conta = pa.cod_conta
                 
                      LEFT JOIN tesouraria.transferencia AS tt
                             ON tt.cod_lote     = lo.cod_lote
                            AND tt.exercicio    = lo.exercicio
                            AND tt.tipo         = lo.tipo
                            AND tt.cod_entidade = lo.cod_entidade
                 
                    INNER JOIN contabilidade.lancamento_receita AS lr
                            ON lr.exercicio    = l.exercicio
                           AND lr.cod_entidade = l.cod_entidade
                           AND lr.tipo         = l.tipo
                           AND lr.cod_lote     = l.cod_lote
                           AND lr.sequencia    = l.sequencia
                 
                         WHERE l.exercicio = '".$this->getDado('exercicio')."'
                           AND l.cod_entidade IN (".$this->getDado('entidade').")
                           AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                           AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                                  
                      GROUP BY  tipo_registro
                             , tipo_unidade
                             , num_controle
                             , pc.cod_estrutural
                             , atributo_conta
                             , natureza_lancamento
                             , l.tipo
                             , tipo_arquivo_sicom
                             , chave_arquivo
                             , vl.vl_lancamento
                 UNION 
                 -- REGISTRO S, E, L e P
                        SELECT DISTINCT '11' AS tipo_registro
                             , (SELECT cod_tipo FROM tcmgo.orgao WHERE exercicio =  '".$this->getDado('exercicio')."')::INTEGER AS tipo_unidade
                             , (LPAD(lo.cod_entidade::VARCHAR,2,'0')||LPAD(oa.num_letra::VARCHAR,2,'0')||LPAD(lo.cod_lote::VARCHAR, 9,'0')) AS num_controle
                             , REPLACE(pc.cod_estrutural, '.','') AS cod_conta
                             , CASE WHEN pc.indicador_superavit = 'financeiro' THEN 1
                                    WHEN pc.indicador_superavit = 'permanente' THEN 2
                                    ELSE 0
                                END AS atributo_conta
                             , cc.tipo_valor AS natureza_lancamento
                             , CASE WHEN (vl.vl_lancamento < 0) THEN SUM(vl.vl_lancamento *-1)
                                    ELSE SUM (vl.vl_lancamento ) END AS valor
                             , l.tipo
                 
                             , CASE WHEN l.tipo = 'S' THEN 3 --AOC
                                    WHEN l.tipo = 'A' AND l.cod_historico = 907  THEN 1 -- REC 
			            WHEN l.tipo = 'A' AND l.cod_historico = 914  THEN 2 -- ARE 
                                    WHEN l.tipo = 'E' THEN 
                                        CASE WHEN le.estorno = false THEN 4 --EMP
                                        ELSE 5 --ANL
                                     END
                                    WHEN l.tipo = 'L' THEN 
                                        CASE WHEN l.cod_historico = 902 THEN 6 --LQD
                                             WHEN l.cod_historico = 905 THEN 7 --ALQ
                                         END
                                    WHEN l.tipo = 'P' THEN
                                         CASE WHEN cpe.cod_lote IS NULL THEN 10 --OPS
                                         ELSE 11 --AOP
                                     END
                                    WHEN l.tipo = 'T' THEN
                                        CASE WHEN tt.cod_tipo = 3 OR tt.cod_tipo = 4 OR tt.cod_tipo = 5 THEN 15 --TRB
                                        ELSE CASE WHEN tte.cod_lote_estorno IS NULL THEN 8 --EXT
                                                  ELSE 9 --AEX
                                              END 
                                        END     
                                    ELSE 0
                              END AS tipo_arquivo_sicom
                                   
                             , CASE WHEN l.tipo = 'I' OR l.tipo = 'M' THEN LPAD('0',150,'0')
                                    WHEN l.tipo = 'T' THEN
                                       CASE WHEN tt.cod_tipo = 3 OR tt.cod_tipo = 4 OR tt.cod_tipo = 5 THEN 'Registro 11'
                                            ELSE 'Registro 10'
                                        END
                                    ELSE 'Registro 10'
                               END AS chave_arquivo
                 
                          FROM contabilidade.lancamento AS l
                 
                          JOIN contabilidade.lote AS lo
                            ON lo.cod_lote      = l.cod_lote
                           AND lo.exercicio     = l.exercicio
                           AND lo.tipo          = l.tipo
                           AND lo.cod_entidade  = l.cod_entidade 
                 
                          JOIN contabilidade.valor_lancamento AS vl
                            ON vl.cod_lote      = l.cod_lote
                           AND vl.tipo          = l.tipo
                           AND vl.sequencia     = l.sequencia
                           AND vl.exercicio     = l.exercicio
                           AND vl.cod_entidade  = l.cod_entidade
                 
                          JOIN tcmgo.ordem_alfabetica AS oa
                            ON oa.letra = UPPER(l.tipo)
                 
                          JOIN contabilidade.conta_credito AS cc
                            ON cc.exercicio    = vl.exercicio
                           AND cc.cod_lote     = vl.cod_lote
                           AND cc.tipo         = vl.tipo
                           AND cc.sequencia    = vl.sequencia
                           AND cc.tipo_valor   = vl.tipo_valor
                           AND cc.cod_entidade = vl.cod_entidade
                 
                          JOIN contabilidade.plano_analitica AS pa
                            ON cc.cod_plano = pa.cod_plano
                           AND cc.exercicio = pa.exercicio
                 
                          JOIN contabilidade.plano_conta AS pc
                            ON pc.exercicio = pa.exercicio
                           AND pc.cod_conta = pa.cod_conta
                 
                     LEFT JOIN tesouraria.transferencia AS tt
                            ON tt.cod_lote     = lo.cod_lote
                           AND tt.exercicio    = lo.exercicio
                           AND tt.tipo         = lo.tipo
                           AND tt.cod_entidade = lo.cod_entidade
                 
                     LEFT JOIN contabilidade.lancamento_empenho AS le
                            ON le.cod_lote     = l.cod_lote
                           AND le.exercicio    = l.exercicio
                           AND le.tipo         = l.tipo
                           AND le.cod_entidade = l.cod_entidade
                 
                     LEFT JOIN contabilidade.pagamento AS cp
                            ON cp.exercicio    = le.exercicio
                           AND cp.cod_lote     = le.cod_lote
                           AND cp.tipo         = le.tipo
                           AND cp.sequencia    = le.sequencia
                           AND cp.cod_entidade = le.cod_entidade
                 
                     LEFT JOIN contabilidade.pagamento_estorno AS cpe
                            ON cpe.exercicio    = cp.exercicio   
                           AND cpe.cod_entidade = cp.cod_entidade    
                           AND cpe.sequencia    = cp.sequencia        
                           AND cpe.tipo         = cp.tipo   
                           AND cpe.cod_lote     = cp.cod_lote
                              
                     LEFT JOIN tesouraria.transferencia_estornada AS tte
                            ON tt.cod_lote     = tte.cod_lote
                           AND tt.exercicio    = tte.exercicio
                           AND tt.tipo         = tte.tipo
                           AND tt.cod_entidade = tte.cod_entidade
                        
                         WHERE l.exercicio = '".$this->getDado('exercicio')."'
                           AND l.cod_entidade IN (".$this->getDado('entidade').")
                           AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                           AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                                  
                      GROUP BY tipo_registro
                              , tipo_unidade
                              , num_controle
                              , pc.cod_estrutural
                              , atributo_conta
                              , natureza_lancamento
                              , l.tipo
                              , tipo_arquivo_sicom
                              , chave_arquivo
                              , vl.vl_lancamento
                 
                 UNION
                 
                     -- REGISTRO T
                         SELECT DISTINCT '11' AS tipo_registro
                              , (SELECT cod_tipo FROM tcmgo.orgao WHERE exercicio = '".$this->getDado('exercicio')."')::INTEGER AS tipo_unidade
                              , (LPAD(lo.cod_entidade::VARCHAR,2,'0')||LPAD(oa.num_letra::VARCHAR,2,'0')||LPAD(lo.cod_lote::VARCHAR, 9,'0')) AS num_controle
                              , REPLACE(pc.cod_estrutural, '.','') AS cod_conta
                              , CASE WHEN pc.indicador_superavit = 'financeiro' THEN 1
                                     WHEN pc.indicador_superavit = 'permanente' THEN 2
                                     ELSE 0
                                 END AS atributo_conta
                              , cc.tipo_valor AS natureza_lancamento
                              , CASE WHEN (vl.vl_lancamento < 0) THEN SUM(vl.vl_lancamento *-1)
                                     ELSE SUM (vl.vl_lancamento ) END AS valor
                              , l.tipo
                              , CASE WHEN l.tipo = 'T' THEN
                                     CASE WHEN tt.cod_tipo = 3 OR tt.cod_tipo = 4 OR tt.cod_tipo = 5 THEN 15 --TRB
                                          ELSE CASE WHEN tte.cod_lote_estorno IS NULL THEN 8 --EXT
                                                    ELSE 9 --AEX
                                                END
                                     END
                                    WHEN l.tipo = 'A' AND l.cod_historico = 907 THEN 1 --REC
                                    ELSE 0
                                 END AS tipo_arquivo_sicom
                              , CASE WHEN l.tipo = 'I' OR l.tipo = 'M' THEN LPAD('0',150,'0')
                                     WHEN l.tipo = 'T' THEN
                                     CASE WHEN tt.cod_tipo = 3 OR tt.cod_tipo = 4 OR tt.cod_tipo = 5 THEN 'Registro 11'
                                          ELSE 'Registro 10'
                                      END
                                     ELSE 'Registro 10'
                                 END AS chave_arquivo
                 
                          FROM contabilidade.lancamento AS l
                 
                          JOIN contabilidade.lote AS lo
                            ON lo.cod_lote      = l.cod_lote
                           AND lo.exercicio     = l.exercicio
                           AND lo.tipo          = l.tipo
                           AND lo.cod_entidade  = l.cod_entidade 
                 
                          JOIN contabilidade.valor_lancamento AS vl
                            ON vl.cod_lote      = l.cod_lote
                           AND vl.tipo          = l.tipo
                           AND vl.sequencia     = l.sequencia
                           AND vl.exercicio     = l.exercicio
                           AND vl.cod_entidade  = l.cod_entidade
                 
                          JOIN tcmgo.ordem_alfabetica AS oa
                            ON oa.letra = UPPER(l.tipo)
                 
                          JOIN contabilidade.conta_credito AS cc
                            ON cc.exercicio    = vl.exercicio
                           AND cc.cod_lote     = vl.cod_lote
                           AND cc.tipo         = vl.tipo
                           AND cc.sequencia    = vl.sequencia
                           AND cc.tipo_valor   = vl.tipo_valor
                           AND cc.cod_entidade = vl.cod_entidade
                 
                          JOIN contabilidade.plano_analitica AS pa
                            ON cc.cod_plano = pa.cod_plano
                           AND cc.exercicio = pa.exercicio
                 
                          JOIN contabilidade.plano_conta AS pc
                            ON pc.exercicio = pa.exercicio
                           AND pc.cod_conta = pa.cod_conta
                 
                    INNER JOIN tesouraria.transferencia AS tt
                            ON tt.cod_lote     = lo.cod_lote
                           AND tt.exercicio    = lo.exercicio
                           AND tt.tipo         = lo.tipo
                           AND tt.cod_entidade = lo.cod_entidade
                 
                     LEFT JOIN tesouraria.transferencia_estornada AS tte
                            ON tt.cod_lote     = tte.cod_lote
                           AND tt.exercicio    = tte.exercicio
                           AND tt.tipo         = tte.tipo
                           AND tt.cod_entidade = tte.cod_entidade
                 
                         WHERE l.exercicio = '".$this->getDado('exercicio')."'
                           AND l.cod_entidade IN (".$this->getDado('entidade').")
                           AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                           AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                                  
                      GROUP BY tipo_registro
                             , tipo_unidade
                             , num_controle
                             , pc.cod_estrutural
                             , atributo_conta
                             , natureza_lancamento
                             , l.tipo
                             , tipo_arquivo_sicom
                             , chave_arquivo 
                             , vl.vl_lancamento
                        ) as tabela_credito
                 GROUP BY tipo_registro
                        , tipo_unidade
                        , num_controle
                        , cod_conta
                        , atributo_conta
                        , natureza_lancamento
                        , tipo
                        , tipo_arquivo_sicom
                        , chave_arquivo

                   UNION 

   --------------------
   -- CONTA DÉBITO
   --------------------
                  -- REGISTRO A
                   SELECT DISTINCT '11' AS tipo_registro
                        , (SELECT cod_tipo FROM tcmgo.orgao WHERE exercicio = '".$this->getDado('exercicio')."')::INTEGER AS tipo_unidade
                        , (LPAD(lo.cod_entidade::VARCHAR,2,'0')||LPAD(oa.num_letra::VARCHAR,2,'0')||LPAD(lo.cod_lote::VARCHAR, 9,'0')) AS num_controle
                        , REPLACE(pc.cod_estrutural, '.','') AS cod_conta
                        , CASE WHEN pc.indicador_superavit = 'financeiro' THEN 1
                               WHEN pc.indicador_superavit = 'permanente' THEN 2
                               ELSE 0
                           END AS atributo_conta
                        , cd.tipo_valor AS natureza_lancamento
                        , REPLACE(REPLACE(SUM(vl.vl_lancamento)::VARCHAR, '.',','),'-','') AS valor
                        , l.tipo
                        , CASE WHEN l.tipo = 'A' THEN 
                              CASE 
                                WHEN lr.estorno = FALSE THEN 1 -- REC 
                                ELSE 2 -- ARE 
                              END                
                          END AS tipo_arquivo_sicom
                        , CASE WHEN l.tipo = 'I' OR l.tipo = 'M' THEN LPAD('0',150,'0')
                               WHEN l.tipo = 'T' THEN
                                 CASE WHEN tt.cod_tipo = 3 OR tt.cod_tipo = 4 OR tt.cod_tipo = 5 THEN 'Registro 11'
                                      ELSE 'Registro 10'
                                  END
                                 ELSE 'Registro 10'
                           END AS chave_arquivo

                    FROM contabilidade.lancamento AS l

                    JOIN contabilidade.lote AS lo
                      ON lo.cod_lote      = l.cod_lote
                     AND lo.exercicio     = l.exercicio
                     AND lo.tipo          = l.tipo
                     AND lo.cod_entidade  = l.cod_entidade 
                    
                    JOIN contabilidade.valor_lancamento AS vl
                      ON vl.cod_lote      = l.cod_lote
                     AND vl.tipo          = l.tipo
                     AND vl.sequencia     = l.sequencia
                     AND vl.exercicio     = l.exercicio
                     AND vl.cod_entidade  = l.cod_entidade
                    
                    JOIN tcmgo.ordem_alfabetica AS oa
                      ON oa.letra = UPPER(l.tipo)
                    
                    JOIN contabilidade.conta_debito AS cd
                      ON cd.exercicio    = vl.exercicio
                     AND cd.cod_lote     = vl.cod_lote
                     AND cd.tipo         = vl.tipo
                     AND cd.sequencia    = vl.sequencia
                     AND cd.tipo_valor   = vl.tipo_valor
                     AND cd.cod_entidade = vl.cod_entidade

                    JOIN contabilidade.plano_analitica AS pa
                      ON cd.cod_plano = pa.cod_plano
                     AND cd.exercicio = pa.exercicio
                   
                    JOIN contabilidade.plano_conta AS pc
                      ON pc.exercicio = pa.exercicio
                     AND pc.cod_conta = pa.cod_conta
                   
               LEFT JOIN tesouraria.transferencia AS tt
                      ON tt.cod_lote     = lo.cod_lote
                     AND tt.exercicio    = lo.exercicio
                     AND tt.tipo         = lo.tipo
                     AND tt.cod_entidade = lo.cod_entidade
                   
              INNER JOIN contabilidade.lancamento_receita AS lr
                      ON lr.exercicio    = l.exercicio
                     AND lr.cod_entidade = l.cod_entidade
                     AND lr.tipo         = l.tipo
                     AND lr.cod_lote     = l.cod_lote
                     AND lr.sequencia    = l.sequencia

                   WHERE l.exercicio = '".$this->getDado('exercicio')."'
                     AND l.cod_entidade IN (".$this->getDado('entidade').")
                     AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                     AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                 
                GROUP BY tipo_registro
                       , tipo_unidade
                       , num_controle
                       , pc.cod_estrutural
                       , atributo_conta
                       , natureza_lancamento
                       , l.tipo
                       , tipo_arquivo_sicom
                       , chave_arquivo

                UNION

               -- REGISTRO S, E, L e P
                  SELECT DISTINCT '11' AS tipo_registro
                       , (SELECT cod_tipo FROM tcmgo.orgao WHERE exercicio = '".$this->getDado('exercicio')."')::INTEGER AS tipo_unidade
                       , (LPAD(lo.cod_entidade::VARCHAR,2,'0')||LPAD(oa.num_letra::VARCHAR,2,'0')||LPAD(lo.cod_lote::VARCHAR, 9,'0')) AS num_controle
                       , REPLACE(pc.cod_estrutural, '.','') AS cod_conta
                       , CASE WHEN pc.indicador_superavit = 'financeiro' THEN 1
                              WHEN pc.indicador_superavit = 'permanente' THEN 2
                              ELSE 0
                          END AS atributo_conta
                       , cd.tipo_valor AS natureza_lancamento
                       , REPLACE(REPLACE(SUM(vl.vl_lancamento)::VARCHAR, '.',','),'-','') AS valor
                       , l.tipo
		       , CASE WHEN l.tipo = 'S' THEN 3 --AOC
                              WHEN l.tipo = 'A' AND l.cod_historico = 907  THEN 1 -- REC 
		       	      WHEN l.tipo = 'A' AND l.cod_historico = 914  THEN 2 -- ARE 
		       	      WHEN l.tipo = 'E' THEN 
		       		  CASE WHEN le.estorno = false THEN 4 --EMP
		       		       ELSE 5 --ANL
		       	           END
		       	      WHEN l.tipo = 'L' THEN 
		       		   CASE WHEN l.cod_historico = 902 THEN 6 --LQD
		       		        WHEN l.cod_historico = 905 THEN 7 --ALQ
		       	            END
		       	      WHEN l.tipo = 'P' THEN
		       		   CASE WHEN cpe.cod_lote IS NULL THEN 10 --OPS
		       		        ELSE 11 --AOP
		       	            END
                              WHEN l.tipo = 'T' THEN
                                   CASE WHEN tt.cod_tipo = 3 OR tt.cod_tipo = 4 OR tt.cod_tipo = 5 THEN 15 --TRB
                                        ELSE CASE WHEN tte.cod_lote_estorno IS NULL THEN 8 --EXT
                                                  ELSE 9 --AEX
                                              END 
                                    END     
		              ELSE 0
                          END AS tipo_arquivo_sicom
                        , CASE WHEN l.tipo = 'I' OR l.tipo = 'M' THEN LPAD('0',150,'0')
                               WHEN l.tipo = 'T' THEN
                               CASE WHEN tt.cod_tipo = 3 OR tt.cod_tipo = 4 OR tt.cod_tipo = 5 THEN 'Registro 11'
                                    ELSE 'Registro 10'
                                END
                               ELSE 'Registro 10'
                           END AS chave_arquivo

                    FROM contabilidade.lancamento AS l
                    
                    JOIN contabilidade.lote AS lo
                      ON lo.cod_lote      = l.cod_lote
                     AND lo.exercicio     = l.exercicio
                     AND lo.tipo          = l.tipo
                     AND lo.cod_entidade  = l.cod_entidade 
                    
                    JOIN contabilidade.valor_lancamento AS vl
                      ON vl.cod_lote      = l.cod_lote
                     AND vl.tipo          = l.tipo
                     AND vl.sequencia     = l.sequencia
                     AND vl.exercicio     = l.exercicio
                     AND vl.cod_entidade  = l.cod_entidade

                    JOIN tcmgo.ordem_alfabetica AS oa
                      ON oa.letra = UPPER(l.tipo)
                   
                    JOIN contabilidade.conta_debito AS cd
                      ON cd.exercicio    = vl.exercicio
                     AND cd.cod_lote     = vl.cod_lote
                     AND cd.tipo         = vl.tipo
                     AND cd.sequencia    = vl.sequencia
                     AND cd.tipo_valor   = vl.tipo_valor
                     AND cd.cod_entidade = vl.cod_entidade
                   
                    JOIN contabilidade.plano_analitica AS pa
                      ON cd.cod_plano = pa.cod_plano
                     AND cd.exercicio = pa.exercicio
                   
                    JOIN contabilidade.plano_conta AS pc
                      ON pc.exercicio = pa.exercicio
                     AND pc.cod_conta = pa.cod_conta
                   
               LEFT JOIN tesouraria.transferencia AS tt
                      ON tt.cod_lote     = lo.cod_lote
                     AND tt.exercicio    = lo.exercicio
                     AND tt.tipo         = lo.tipo
                     AND tt.cod_entidade = lo.cod_entidade

               LEFT JOIN contabilidade.lancamento_empenho AS le
                      ON le.cod_lote     = l.cod_lote
                     AND le.exercicio    = l.exercicio
                     AND le.tipo         = l.tipo
                     AND le.cod_entidade = l.cod_entidade
                    
               LEFT JOIN contabilidade.pagamento AS cp
                      ON cp.exercicio    = le.exercicio
                     AND cp.cod_lote     = le.cod_lote
                     AND cp.tipo         = le.tipo
                     AND cp.sequencia    = le.sequencia
                     AND cp.cod_entidade = le.cod_entidade
                    
               LEFT JOIN contabilidade.pagamento_estorno AS cpe
                      ON cpe.exercicio    = cp.exercicio   
                     AND cpe.cod_entidade = cp.cod_entidade    
                     AND cpe.sequencia    = cp.sequencia        
                     AND cpe.tipo         = cp.tipo   
                     AND cpe.cod_lote     = cp.cod_lote
                    
               LEFT JOIN tesouraria.transferencia_estornada AS tte
                      ON tt.cod_lote     = tte.cod_lote
                     AND tt.exercicio    = tte.exercicio
                     AND tt.tipo         = tte.tipo
                     AND tt.cod_entidade = tte.cod_entidade
             
                   WHERE l.exercicio = '".$this->getDado('exercicio')."'
                     AND l.cod_entidade IN (".$this->getDado('entidade').")
                     AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                     AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                 
                GROUP BY tipo_registro
                       , tipo_unidade
                       , num_controle
                       , pc.cod_estrutural
                       , atributo_conta
                       , natureza_lancamento
                       , l.tipo
                       , tipo_arquivo_sicom
                       , chave_arquivo

         UNION

-- REGISTRO T
                  SELECT DISTINCT '11' AS tipo_registro
                       , (SELECT cod_tipo FROM tcmgo.orgao WHERE exercicio = '".$this->getDado('exercicio')."')::INTEGER AS tipo_unidade
                       , (LPAD(lo.cod_entidade::VARCHAR,2,'0')||LPAD(oa.num_letra::VARCHAR,2,'0')||LPAD(lo.cod_lote::VARCHAR, 9,'0')) AS num_controle
                       , REPLACE(pc.cod_estrutural, '.','') AS cod_conta
                       , CASE WHEN pc.indicador_superavit = 'financeiro' THEN 1
                              WHEN pc.indicador_superavit = 'permanente' THEN 2
                              ELSE 0
                          END AS atributo_conta
                       , cd.tipo_valor AS natureza_lancamento
                       , REPLACE(REPLACE(SUM(vl.vl_lancamento)::VARCHAR, '.',','),'-','') AS valor
                       , l.tipo
	               , CASE WHEN l.tipo = 'T' THEN
                              CASE WHEN tt.cod_tipo = 3 OR tt.cod_tipo = 4 OR tt.cod_tipo = 5 THEN 15 --TRB
                                  ELSE CASE WHEN tte.cod_lote_estorno IS NULL THEN 8 --EXT
                                           ELSE 9 --AEX
                                        END
                               END
                              WHEN l.tipo = 'A' AND l.cod_historico = 907 THEN 1 --REC
		              ELSE 0
                         END AS tipo_arquivo_sicom
                        , CASE WHEN l.tipo = 'I' OR l.tipo = 'M' THEN LPAD('0',150,'0')
                               WHEN l.tipo = 'T' THEN
                                    CASE WHEN tt.cod_tipo = 3 OR tt.cod_tipo = 4 OR tt.cod_tipo = 5 THEN 'Registro 11'
                                         ELSE 'Registro 10'
                                     END
                               ELSE 'Registro 10'
                           END AS chave_arquivo

                     FROM contabilidade.lancamento AS l
                     
                     JOIN contabilidade.lote AS lo
                       ON lo.cod_lote      = l.cod_lote
                      AND lo.exercicio     = l.exercicio
                      AND lo.tipo          = l.tipo
                      AND lo.cod_entidade  = l.cod_entidade 
                     
                     JOIN contabilidade.valor_lancamento AS vl
                       ON vl.cod_lote      = l.cod_lote
                      AND vl.tipo          = l.tipo
                      AND vl.sequencia     = l.sequencia
                      AND vl.exercicio     = l.exercicio
                      AND vl.cod_entidade  = l.cod_entidade

                     JOIN tcmgo.ordem_alfabetica AS oa
                       ON oa.letra = UPPER(l.tipo)
                     
                     JOIN contabilidade.conta_debito AS cd
                       ON cd.exercicio    = vl.exercicio
                      AND cd.cod_lote     = vl.cod_lote
                      AND cd.tipo         = vl.tipo
                      AND cd.sequencia    = vl.sequencia
                      AND cd.tipo_valor   = vl.tipo_valor
                      AND cd.cod_entidade = vl.cod_entidade
                     
                     JOIN contabilidade.plano_analitica AS pa
                       ON cd.cod_plano = pa.cod_plano
                      AND cd.exercicio = pa.exercicio
                     
                     JOIN contabilidade.plano_conta AS pc
                       ON pc.exercicio = pa.exercicio
                      AND pc.cod_conta = pa.cod_conta
                     
               INNER JOIN tesouraria.transferencia AS tt
                       ON tt.cod_lote     = lo.cod_lote
                      AND tt.exercicio    = lo.exercicio
                      AND tt.tipo         = lo.tipo
                      AND tt.cod_entidade = lo.cod_entidade
                     
               LEFT JOIN tesouraria.transferencia_estornada AS tte
                       ON tt.cod_lote     = tte.cod_lote
                      AND tt.exercicio    = tte.exercicio
                      AND tt.tipo         = tte.tipo
                      AND tt.cod_entidade = tte.cod_entidade

                    WHERE l.exercicio = '".$this->getDado('exercicio')."'
                      AND l.cod_entidade IN (".$this->getDado('entidade').")
                      AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                      AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                 
                 GROUP BY tipo_registro
                        , tipo_unidade
                        , num_controle
                        , pc.cod_estrutural
                        , atributo_conta
                        , natureza_lancamento
                        , l.tipo
                        , tipo_arquivo_sicom
                        , chave_arquivo 

                 ORDER BY num_controle,natureza_lancamento ";
        return $stSql;
    }
}

?>
