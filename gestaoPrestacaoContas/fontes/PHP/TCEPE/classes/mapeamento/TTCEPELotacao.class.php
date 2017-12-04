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
    * 
    * Data de Criação   : 29/10/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPELotacao.class.php 60565 2014-10-30 12:43:58Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPELotacao extends Persistente
{
    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPELotacao()
    {
        parent::Persistente();
    }

    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaLotacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaLotacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaLotacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaLotacao()
    {
        //A consulta está baseada no arquivo HistoricoFuncional, pois as Lotações deste arquivo são relativas ao uso no arquivo HistoricoFuncional.
        $stSql = "
               --SERVIDOR
                   SELECT servidor.lotacao
                        , servidor.cod_orgao
                        , orgao_descricao.descricao
                        , '".$this->getDado('stUnidadeGestora')."' AS unidade_gestora
                        
                     FROM (
                               SELECT sw_cgm_pessoa_fisica.cpf
                                    , contrato.registro AS matricula
                                    , contrato_servidor.cod_cargo
                                    , assentamento_gerado.periodo_inicial
                                    , assentamento_gerado.periodo_final
                                    , assentamento_assentamento.cod_motivo
                                    , assentamento_gerado.cod_assentamento
                                    , contrato.cod_contrato
                                    , assentamento_gerado_contrato_servidor.cod_assentamento_gerado
                                    , CASE WHEN to_char(contrato_servidor_nomeacao_posse.dt_admissao, 'mmyyyy') = '".$this->getDado('stMes')."' THEN
                                            CASE vinculo_regime_subdivisao.cod_tipo_vinculo
                                                WHEN 2  THEN 1
                                                WHEN 4  THEN 2
                                                WHEN 7  THEN 3
                                                WHEN 6  THEN 12
                                                WHEN 3  THEN 20
                                                WHEN 11 THEN 50
                                                WHEN 9  THEN
                                                    CASE WHEN contrato_servidor.cod_tipo_admissao IN (3,4) THEN 45
                                                    END
                                                ELSE
                                                    CASE contrato_servidor.cod_tipo_admissao
                                                        WHEN 5 THEN 4
                                                        WHEN 6 THEN 33
                                                        WHEN 7 THEN 19
                                                    END
                                                END
                                      WHEN to_char(aposentadoria.dt_concessao, 'mmyyyy') = '".$this->getDado('stMes')."' THEN 54
                                      WHEN to_char(ultimo_contrato_servidor_caso_causa.dt_rescisao, 'mmyyyy') = '".$this->getDado('stMes')."' THEN
                                            CASE vinculo_regime_subdivisao.cod_tipo_vinculo 
                                                WHEN 2  THEN 8
                                                WHEN 4  THEN 9
                                                WHEN 7  THEN 10
                                                WHEN 6  THEN
                                                    CASE WHEN causa_rescisao.num_causa = 12
                                                        THEN 27
                                                        ELSE 26
                                                    END
                                                WHEN 3  THEN 44
                                                WHEN 11 THEN 51
                                                WHEN 9  THEN
                                                    CASE WHEN contrato_servidor.cod_tipo_admissao IN (3,4) THEN 46
                                                    END
                                                ELSE
                                                    CASE WHEN causa_rescisao.num_causa IN (60,62,64) THEN 11
                                                    END
                                                END
                                      WHEN to_char(aposentadoria_encerramento.dt_encerramento, 'mmyyyy') = '".$this->getDado('stMes')."' THEN 53
                                      END AS tipo_ato
                                    , CASE WHEN to_char(contrato_servidor_nomeacao_posse.dt_admissao, 'mmyyyy') = '".$this->getDado('stMes')."' THEN
                                            CASE WHEN vinculo_regime_subdivisao.cod_tipo_vinculo IN (2,4,7,6,3,11) THEN
                                                CAST(to_char(contrato_servidor_nomeacao_posse.dt_admissao, 'ddmmyyyy') AS VARCHAR(8))
                                            WHEN contrato_servidor.cod_tipo_admissao IN (5,6,7) THEN
                                                CAST(to_char(contrato_servidor_nomeacao_posse.dt_admissao, 'ddmmyyyy') AS VARCHAR(8))
                                            WHEN contrato_servidor.cod_tipo_admissao IN (3,4) AND vinculo_regime_subdivisao.cod_tipo_vinculo = 9 THEN
                                                CAST(to_char(contrato_servidor_nomeacao_posse.dt_admissao, 'ddmmyyyy') AS VARCHAR(8))
                                            END
                                      WHEN to_char(aposentadoria.dt_concessao, 'mmyyyy') = '".$this->getDado('stMes')."' THEN
                                            CAST(to_char(aposentadoria.dt_concessao, 'ddmmyyyy') AS VARCHAR(8))
                                      WHEN to_char(ultimo_contrato_servidor_caso_causa.dt_rescisao, 'mmyyyy') = '".$this->getDado('stMes')."' THEN
                                            CASE WHEN vinculo_regime_subdivisao.cod_tipo_vinculo IN (2,4,7,6,3,11) THEN
                                                CAST(to_char(ultimo_contrato_servidor_caso_causa.dt_rescisao, 'ddmmyyyy') AS VARCHAR(8))
                                            WHEN causa_rescisao.num_causa IN (60,62,64) THEN
                                                CAST(to_char(ultimo_contrato_servidor_caso_causa.dt_rescisao, 'ddmmyyyy') AS VARCHAR(8))
                                            WHEN contrato_servidor.cod_tipo_admissao IN (3,4) AND vinculo_regime_subdivisao.cod_tipo_vinculo = 9 THEN
                                                CAST(to_char(ultimo_contrato_servidor_caso_causa.dt_rescisao, 'ddmmyyyy') AS VARCHAR(8))
                                            END
                                      WHEN to_char(aposentadoria_encerramento.dt_encerramento, 'mmyyyy') = '".$this->getDado('stMes')."' THEN
                                            CAST(to_char(aposentadoria_encerramento.dt_encerramento, 'ddmmyyyy') AS VARCHAR(8))
                                      END AS dt_movimentacao
                                    , CASE WHEN to_char(assentamento_gerado.periodo_inicial, 'mmyyyy') = '".$this->getDado('stMes')."' THEN
                                            CASE WHEN assentamento_assentamento.cod_motivo IN (5,6,7) THEN 21
                                            WHEN tcepe_configuracao_relaciona_historico.cod_tipo_movimentacao IS NOT NULL THEN
                                                tcepe_configuracao_relaciona_historico.cod_tipo_movimentacao
                                            END
                                      END AS tipo_ato_inicial
                                    , CASE WHEN to_char(assentamento_gerado.periodo_inicial, 'mmyyyy') = '".$this->getDado('stMes')."' THEN
                                            CASE WHEN assentamento_assentamento.cod_motivo IN (5,6,7) THEN
                                                CAST(to_char(assentamento_gerado.periodo_inicial, 'ddmmyyyy') AS VARCHAR(8))
                                            WHEN tcepe_configuracao_relaciona_historico.cod_tipo_movimentacao IS NOT NULL THEN
                                                CAST(to_char(assentamento_gerado.periodo_inicial, 'ddmmyyyy') AS VARCHAR(8))
                                            END
                                      END AS dt_movimentacao_inicial
                                    , CASE WHEN to_char(assentamento_gerado.periodo_final, 'mmyyyy') = '".$this->getDado('stMes')."' THEN
                                            CASE WHEN assentamento_assentamento.cod_motivo IN (5,6,7) THEN 25
                                            WHEN tcepe_configuracao_relaciona_historico.cod_tipo_movimentacao IN (40,41,42,43) THEN 23
                                            WHEN tcepe_configuracao_relaciona_historico.cod_tipo_movimentacao IN (16,17) THEN 24
                                            END
                                      END AS tipo_ato_final
                                    , CASE WHEN to_char(assentamento_gerado.periodo_final, 'mmyyyy') = '".$this->getDado('stMes')."' THEN
                                            CASE WHEN assentamento_assentamento.cod_motivo IN (5,6,7) THEN
                                                CAST(to_char(assentamento_gerado.periodo_final, 'ddmmyyyy') AS VARCHAR(8))
                                            WHEN tcepe_configuracao_relaciona_historico.cod_tipo_movimentacao IN (40,41,42,43,16,17) THEN
                                                CAST(to_char(assentamento_gerado.periodo_final, 'ddmmyyyy') AS VARCHAR(8))
                                            END
                                      END AS dt_movimentacao_final
                                    , CASE WHEN previdencia.cod_regime_previdencia = 1 THEN 1
                                      WHEN previdencia.cod_regime_previdencia = 2 THEN 2
                                      ELSE 0
                                      END AS regime_previdencia
                                    , vinculo_regime_subdivisao.cod_tipo_regime AS regime_trabalho
                                    , REPLACE(vw_orgao_nivel.orgao, '.', '') AS lotacao
                                    , vw_orgao_nivel.cod_orgao
                                    , CASE WHEN to_char(assentamento_gerado.periodo_final, 'mmyyyy') = '".$this->getDado('stMes')."' AND assentamento_assentamento.cod_motivo IN (5,6,7) THEN 8
                                      WHEN tcepe_configuracao_relaciona_historico.cod_tipo_movimentacao IN (16,17) THEN 9
                                      WHEN tcepe_configuracao_relaciona_historico.cod_tipo_movimentacao IN (13,35) THEN 5
                                      ELSE vinculo_regime_subdivisao.cod_tipo_vinculo
                                      END AS vinculo_publico
                                    , CASE WHEN causa_rescisao.num_causa IN (70,71,72,76,78) THEN 1
                                      WHEN causa_rescisao.num_causa IN (73,74,77) THEN 3
                                      WHEN causa_rescisao.num_causa IN (75,79) THEN 2
                                      WHEN contrato_servidor_situacao.cod_contrato IS NOT NULL THEN
                                            CASE WHEN aposentadoria.cod_enquadramento IN (1,2,3,4,5) THEN 1
                                            WHEN aposentadoria.cod_enquadramento IN (7) THEN 3
                                            END
                                      END AS tipo_inativacao
                                    , CASE WHEN causa_rescisao.cod_causa_rescisao IN (10,11,12) THEN
                                            CAST(to_char(ultimo_contrato_servidor_caso_causa.dt_rescisao, 'ddmmyyyy') AS VARCHAR(8))
                                      END AS dt_obito
                                    , ultimo_contrato_servidor_padrao.cod_padrao AS cod_classe
                                    , periodo_movimentacao.cod_periodo_movimentacao
                                    , assentamento_assentamento.descricao AS descricao_assentamento
                                    
                                 FROM sw_cgm

                           INNER JOIN sw_cgm_pessoa_fisica
                                   ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

                           INNER JOIN pessoal".$this->getDado('stEntidades').".servidor
                                   ON servidor.numcgm = sw_cgm.numcgm

                           INNER JOIN pessoal".$this->getDado('stEntidades').".servidor_contrato_servidor 
                                   ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                           INNER JOIN pessoal".$this->getDado('stEntidades').".contrato
                                   ON servidor_contrato_servidor.cod_contrato = contrato.cod_contrato

                           INNER JOIN pessoal".$this->getDado('stEntidades').".contrato_servidor
                                   ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato

                           INNER JOIN pessoal".$this->getDado('stEntidades').".contrato_servidor_nomeacao_posse
                                   ON contrato_servidor_nomeacao_posse.cod_contrato = contrato.cod_contrato

                           INNER JOIN folhapagamento".$this->getDado('stEntidades').".registro_evento_periodo
                                   ON registro_evento_periodo.cod_periodo_movimentacao = (
                                                                                            SELECT cod_periodo_movimentacao
                                                                                              FROM folhapagamento".$this->getDado('stEntidades').".periodo_movimentacao
                                                                                             WHERE TO_CHAR(periodo_movimentacao.dt_final, 'mmyyyy') = '".$this->getDado('stMes')."' 
                                                                                         )
                                  AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato

                            LEFT JOIN ultimo_contrato_servidor_previdencia( '".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao')." ) AS ultimo_contrato_servidor_previdencia
                                   ON ultimo_contrato_servidor_previdencia.cod_contrato = contrato.cod_contrato

                            LEFT JOIN ultimo_contrato_servidor_orgao( '".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao')." ) AS ultimo_contrato_servidor_orgao
                                   ON ultimo_contrato_servidor_orgao.cod_contrato = contrato.cod_contrato

                            LEFT JOIN ultimo_contrato_servidor_padrao( '".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao')." ) AS ultimo_contrato_servidor_padrao
                                   ON ultimo_contrato_servidor_padrao.cod_contrato = contrato.cod_contrato

                            LEFT JOIN folhapagamento".$this->getDado('stEntidades').".previdencia
                                   ON previdencia.cod_previdencia = ultimo_contrato_servidor_previdencia.cod_previdencia 

                            LEFT JOIN organograma.vw_orgao_nivel   
                                   ON vw_orgao_nivel.cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao    

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".contrato_servidor_situacao
                                   ON contrato_servidor_situacao.situacao='P'
                                  AND contrato_servidor_situacao.cod_contrato=contrato.cod_contrato
                                  AND contrato_servidor_situacao.cod_periodo_movimentacao<=registro_evento_periodo.cod_periodo_movimentacao

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".aposentadoria
                                   ON aposentadoria.cod_contrato=contrato.cod_contrato

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".aposentadoria_encerramento
                                   ON aposentadoria_encerramento.cod_contrato=aposentadoria.cod_contrato

                           INNER JOIN folhapagamento".$this->getDado('stEntidades').".periodo_movimentacao
                                   ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".vinculo_regime_subdivisao
                                   ON vinculo_regime_subdivisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".assentamento_gerado_contrato_servidor
                                   ON assentamento_gerado_contrato_servidor.cod_contrato=contrato.cod_contrato
                                  AND assentamento_gerado_contrato_servidor.cod_assentamento_gerado NOT IN (   SELECT assentamento_gerado_excluido.cod_assentamento_gerado
                                                                                                                 FROM pessoal".$this->getDado('stEntidades').".assentamento_gerado_excluido
                                                                                                           ) 

                            LEFT JOIN (
                                          SELECT cod_assentamento_gerado, periodo_inicial, null::date AS periodo_final, cod_assentamento
                                            FROM pessoal".$this->getDado('stEntidades').".assentamento_gerado AS AG_INICIAL
                                           WHERE to_char(AG_INICIAL.periodo_inicial, 'mmyyyy') = '".$this->getDado('stMes')."'
                                             AND AG_INICIAL.timestamp = ( SELECT assentamento_gerado.timestamp
                                                                            FROM pessoal".$this->getDado('stEntidades').".assentamento_gerado
                                                                           WHERE to_char(assentamento_gerado.periodo_inicial, 'mmyyyy') = '".$this->getDado('stMes')."'
                                                                             AND assentamento_gerado.cod_assentamento_gerado = AG_INICIAL.cod_assentamento_gerado
                                                                        ORDER BY assentamento_gerado.timestamp DESC LIMIT 1
                                                                        )
                                       UNION ALL
                                          SELECT cod_assentamento_gerado, null::date AS periodo_inicial, periodo_final, cod_assentamento
                                            FROM pessoal".$this->getDado('stEntidades').".assentamento_gerado AS AG_FINAL
                                           WHERE to_char(AG_FINAL.periodo_final, 'mmyyyy') = '".$this->getDado('stMes')."'
                                             AND AG_FINAL.periodo_final<>AG_FINAL.periodo_inicial
                                             AND AG_FINAL.timestamp = (   SELECT assentamento_gerado.timestamp
                                                                            FROM pessoal".$this->getDado('stEntidades').".assentamento_gerado
                                                                           WHERE to_char(assentamento_gerado.periodo_final, 'mmyyyy') = '".$this->getDado('stMes')."'
                                                                             AND assentamento_gerado.cod_assentamento_gerado=AG_FINAL.cod_assentamento_gerado
                                                                        ORDER BY assentamento_gerado.timestamp DESC LIMIT 1
                                                                      )
                                        ORDER BY cod_assentamento_gerado
                                      ) AS assentamento_gerado
                                   ON assentamento_gerado.cod_assentamento_gerado=assentamento_gerado_contrato_servidor.cod_assentamento_gerado

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".assentamento_assentamento
                                   ON assentamento_assentamento.cod_assentamento=assentamento_gerado.cod_assentamento

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".tcepe_configuracao_relaciona_historico
                                   ON tcepe_configuracao_relaciona_historico.cod_assentamento=assentamento_assentamento.cod_assentamento
                                  AND tcepe_configuracao_relaciona_historico.exercicio='".$this->getDado('stExercicio')."'
                                  
                            LEFT JOIN ultimo_contrato_servidor_caso_causa( '".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao')." ) AS ultimo_contrato_servidor_caso_causa 
                                   ON ultimo_contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato
                                  AND ( 
                                        ultimo_contrato_servidor_caso_causa.dt_rescisao = assentamento_gerado.periodo_inicial
                                        OR
                                        ultimo_contrato_servidor_caso_causa.dt_rescisao = assentamento_gerado.periodo_final
                                      )
                                      
                            LEFT JOIN pessoal".$this->getDado('stEntidades').".caso_causa  
                                   ON caso_causa.cod_caso_causa = ultimo_contrato_servidor_caso_causa.cod_caso_causa
                                   
                            LEFT JOIN pessoal".$this->getDado('stEntidades').".causa_rescisao
                                   ON causa_rescisao.cod_causa_rescisao = caso_causa.cod_causa_rescisao

                             GROUP BY sw_cgm_pessoa_fisica.cpf
                                    , contrato.registro
                                    , contrato_servidor_nomeacao_posse.dt_admissao
                                    , contrato_servidor.cod_cargo
                                    , previdencia.cod_regime_previdencia
                                    , vinculo_regime_subdivisao.cod_tipo_regime
                                    , contrato_servidor.cod_tipo_admissao
                                    , vw_orgao_nivel.orgao
                                    , contrato_servidor_situacao.cod_contrato
                                    , aposentadoria.cod_enquadramento
                                    , aposentadoria.dt_concessao
                                    , aposentadoria_encerramento.dt_encerramento
                                    , vinculo_regime_subdivisao.cod_tipo_vinculo
                                    , causa_rescisao.num_causa
                                    , causa_rescisao.cod_causa_rescisao
                                    , ultimo_contrato_servidor_caso_causa.dt_rescisao
                                    , ultimo_contrato_servidor_padrao.cod_padrao
                                    , periodo_movimentacao.cod_periodo_movimentacao
                                    , assentamento_gerado.periodo_inicial
                                    , assentamento_gerado.periodo_final
                                    , assentamento_gerado_contrato_servidor.cod_assentamento_gerado
                                    , contrato.cod_contrato
                                    , assentamento_assentamento.cod_motivo
                                    , tcepe_configuracao_relaciona_historico.cod_tipo_movimentacao
                                    , assentamento_assentamento.descricao
                                    , assentamento_gerado.cod_assentamento
                                    , vw_orgao_nivel.cod_orgao
                          ) AS servidor
                          
                     JOIN (
                               SELECT orgao_descricao.cod_orgao
                                    , orgao_descricao.descricao
                                    , orgao_descricao.timestamp
                                 FROM organograma.orgao_descricao
                                WHERE orgao_descricao.timestamp = ( SELECT OD.timestamp FROM organograma.orgao_descricao AS OD
                                                                     WHERE TO_DATE(to_char(OD.timestamp, 'mmyyyy'), 'mmyyyy') < (TO_DATE('".$this->getDado('stMes')."', 'mmyyyy')-1)
                                                                       AND OD.cod_orgao=orgao_descricao.cod_orgao
                                                                  ORDER BY OD.timestamp DESC
                                                                     LIMIT 1
                                                                  )
                             GROUP BY orgao_descricao.cod_orgao
                                    , orgao_descricao.descricao
                                    , orgao_descricao.timestamp
                          ) AS orgao_descricao
                       ON orgao_descricao.cod_orgao=servidor.cod_orgao

                    WHERE ( tipo_ato IS NULL AND to_char(periodo_inicial, 'mmyyyy') = '".$this->getDado('stMes')."' AND cod_motivo IS NOT NULL )
                       OR tipo_ato          IS NOT NULL
                       OR tipo_ato_inicial  IS NOT NULL
                       OR tipo_ato_final    IS NOT NULL
                        
                 GROUP BY servidor.lotacao
                        , servidor.cod_orgao
                        , orgao_descricao.descricao

                UNION ALL
            
            --PENSIONISTA
                   SELECT pensionista.lotacao
                        , pensionista.cod_orgao
                        , orgao_descricao.descricao
                        , '".$this->getDado('stUnidadeGestora')."' AS unidade_gestora

                     FROM (
                               SELECT sw_cgm_pessoa_fisica.cpf
                                    , contrato.registro AS matricula
                                    , contrato_servidor.cod_cargo
                                    , contrato.cod_contrato
                                    , CASE WHEN to_char(contrato_pensionista.dt_inicio_beneficio, 'mmyyyy') = '".$this->getDado('stMes')."' THEN 38
                                      WHEN to_char(contrato_pensionista.dt_encerramento, 'mmyyyy') = '".$this->getDado('stMes')."'          THEN 48
                                      WHEN to_char(assentamento_gerado.periodo_inicial, 'mmyyyy') = '".$this->getDado('stMes')."'           THEN 99
                                      END AS tipo_ato
                                    , CASE WHEN to_char(contrato_pensionista.dt_inicio_beneficio, 'mmyyyy') = '".$this->getDado('stMes')."' THEN
                                            CAST(to_char(contrato_pensionista.dt_inicio_beneficio, 'ddmmyyyy') AS VARCHAR(8))
                                      WHEN to_char(contrato_pensionista.dt_encerramento, 'mmyyyy') = '".$this->getDado('stMes')."'          THEN
                                            CAST(to_char(contrato_pensionista.dt_encerramento, 'ddmmyyyy') AS VARCHAR(8))
                                      WHEN to_char(assentamento_gerado.periodo_inicial, 'mmyyyy') = '".$this->getDado('stMes')."'           THEN 
                                            CAST(to_char(assentamento_gerado.periodo_inicial, 'ddmmyyyy') AS VARCHAR(8))
                                      END dt_movimentacao
                                    , CASE WHEN previdencia.cod_regime_previdencia = 1  THEN 1
                                      WHEN previdencia.cod_regime_previdencia = 2       THEN 2
                                      ELSE 0
                                      END AS regime_previdencia
                                    , vinculo_regime_subdivisao.cod_tipo_regime AS regime_trabalho
                                    , REPLACE(vw_orgao_nivel.orgao, '.', '') AS lotacao
                                    , vw_orgao_nivel.cod_orgao
                                    , vinculo_regime_subdivisao.cod_tipo_vinculo AS vinculo_publico
                                    , NULL::integer AS tipo_inativacao
                                    , null::varchar AS dt_obito
                                    , ultimo_contrato_servidor_padrao.cod_padrao AS cod_classe
                                    , periodo_movimentacao.cod_periodo_movimentacao
                                    , assentamento_assentamento.descricao AS descricao_assentamento

                                 FROM sw_cgm

                           INNER JOIN sw_cgm_pessoa_fisica
                                   ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

                           INNER JOIN pessoal".$this->getDado('stEntidades').".pensionista
                                   ON pensionista.numcgm = sw_cgm.numcgm

                           INNER JOIN pessoal".$this->getDado('stEntidades').".contrato_pensionista
                                   ON contrato_pensionista.cod_pensionista=pensionista.cod_pensionista
                                  AND contrato_pensionista.cod_contrato_cedente=pensionista.cod_contrato_cedente

                           INNER JOIN pessoal".$this->getDado('stEntidades').".contrato
                                   ON contrato.cod_contrato=contrato_pensionista.cod_contrato

                           INNER JOIN pessoal".$this->getDado('stEntidades').".contrato_servidor
                                   ON contrato_servidor.cod_contrato = pensionista.cod_contrato_cedente

                            LEFT JOIN (   SELECT contrato_pensionista_previdencia.*                                                             
                                            FROM pessoal".$this->getDado('stEntidades').".contrato_pensionista_previdencia                                                       
                                               , (  SELECT cod_contrato                                                                   
                                                         , max(timestamp) as timestamp                                                    
                                                      FROM pessoal".$this->getDado('stEntidades').".contrato_pensionista_previdencia                                             
                                                  GROUP BY cod_contrato
                                                 ) AS max_contrato_pensionista_previdencia                               
                                           WHERE contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato    
                                             AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp
                                      ) AS ultimo_contrato_pensionista_previdencia        
                                   ON ultimo_contrato_pensionista_previdencia.cod_contrato = contrato.cod_contrato
                                  AND ultimo_contrato_pensionista_previdencia.bo_excluido = false

                            LEFT JOIN ultimo_contrato_pensionista_orgao( '".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao')." ) AS ultimo_contrato_pensionista_orgao
                                   ON ultimo_contrato_pensionista_orgao.cod_contrato = contrato.cod_contrato

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".contrato_pensionista_orgao
                                   ON contrato_pensionista_orgao.cod_contrato = contrato.cod_contrato
                                  AND contrato_pensionista_orgao.timestamp = ( SELECT max(timestamp) as timestamp                                                    
                                                                                 FROM pessoal".$this->getDado('stEntidades').".contrato_pensionista_orgao
                                                                                WHERE contrato_pensionista_orgao.cod_contrato = contrato.cod_contrato                                           
                                                                             GROUP BY contrato_pensionista_orgao.cod_contrato
                                                                             )                                            

                            LEFT JOIN ultimo_contrato_servidor_padrao( '".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao')." ) AS ultimo_contrato_servidor_padrao
                                   ON ultimo_contrato_servidor_padrao.cod_contrato = contrato_servidor.cod_contrato

                            LEFT JOIN folhapagamento".$this->getDado('stEntidades').".previdencia
                                   ON previdencia.cod_previdencia = ultimo_contrato_pensionista_previdencia.cod_previdencia 

                            LEFT JOIN organograma.vw_orgao_nivel   
                                   ON vw_orgao_nivel.cod_orgao = contrato_pensionista_orgao.cod_orgao

                           INNER JOIN folhapagamento".$this->getDado('stEntidades').".periodo_movimentacao
                                   ON TO_CHAR(periodo_movimentacao.dt_final, 'mmyyyy') = '".$this->getDado('stMes')."' 

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".vinculo_regime_subdivisao
                                   ON vinculo_regime_subdivisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".assentamento_gerado_contrato_servidor
                                   ON assentamento_gerado_contrato_servidor.cod_contrato=contrato.cod_contrato
                                  AND assentamento_gerado_contrato_servidor.cod_assentamento_gerado NOT IN (   SELECT assentamento_gerado_excluido.cod_assentamento_gerado
                                                                                                                 FROM pessoal".$this->getDado('stEntidades').".assentamento_gerado_excluido
                                                                                                           ) 

                            LEFT JOIN (   SELECT cod_assentamento_gerado, periodo_inicial, null::date AS periodo_final, cod_assentamento
                                            FROM pessoal".$this->getDado('stEntidades').".assentamento_gerado AS AG_INICIAL
                                           WHERE to_char(AG_INICIAL.periodo_inicial, 'mmyyyy') = '".$this->getDado('stMes')."'
                                             AND AG_INICIAL.timestamp = ( SELECT assentamento_gerado.timestamp
                                                                            FROM pessoal".$this->getDado('stEntidades').".assentamento_gerado
                                                                           WHERE to_char(assentamento_gerado.periodo_inicial, 'mmyyyy') = '".$this->getDado('stMes')."'
                                                                             AND assentamento_gerado.cod_assentamento_gerado=AG_INICIAL.cod_assentamento_gerado
                                                                        ORDER BY assentamento_gerado.timestamp DESC
                                                                           LIMIT 1
                                                                        )
                                       UNION ALL
                                          SELECT cod_assentamento_gerado, null::date AS periodo_inicial, periodo_final, cod_assentamento
                                            FROM pessoal".$this->getDado('stEntidades').".assentamento_gerado AS AG_FINAL
                                           WHERE to_char(AG_FINAL.periodo_final, 'mmyyyy') = '".$this->getDado('stMes')."'
                                             AND AG_FINAL.periodo_final<>AG_FINAL.periodo_inicial
                                             AND AG_FINAL.timestamp = ( SELECT assentamento_gerado.timestamp
                                                                          FROM pessoal".$this->getDado('stEntidades').".assentamento_gerado
                                                                         WHERE to_char(assentamento_gerado.periodo_final, 'mmyyyy') = '".$this->getDado('stMes')."'
                                                                           AND assentamento_gerado.cod_assentamento_gerado=AG_FINAL.cod_assentamento_gerado
                                                                      ORDER BY assentamento_gerado.timestamp DESC
                                                                         LIMIT 1
                                                                      )
                                        ORDER BY cod_assentamento_gerado
                                      ) AS assentamento_gerado
                                   ON assentamento_gerado.cod_assentamento_gerado=assentamento_gerado_contrato_servidor.cod_assentamento_gerado
                        
                            LEFT JOIN pessoal".$this->getDado('stEntidades').".assentamento_assentamento
                                   ON assentamento_assentamento.cod_assentamento=assentamento_gerado.cod_assentamento

                             GROUP BY sw_cgm_pessoa_fisica.cpf
                                    , contrato.registro
                                    , contrato_servidor.cod_cargo
                                    , previdencia.cod_regime_previdencia
                                    , vinculo_regime_subdivisao.cod_tipo_regime
                                    , contrato_servidor.cod_tipo_admissao
                                    , vw_orgao_nivel.orgao
                                    , vw_orgao_nivel.cod_orgao
                                    , vinculo_regime_subdivisao.cod_tipo_vinculo
                                    , ultimo_contrato_servidor_padrao.cod_padrao
                                    , periodo_movimentacao.cod_periodo_movimentacao
                                    , contrato.cod_contrato
                                    , contrato_pensionista.dt_inicio_beneficio
                                    , contrato_pensionista.dt_encerramento
                                    , assentamento_gerado.periodo_inicial
                                    , assentamento_assentamento.descricao
                          ) AS pensionista

                    
                          
                     JOIN (
                               SELECT orgao_descricao.cod_orgao
                                    , orgao_descricao.descricao
                                    , orgao_descricao.timestamp
                                 FROM organograma.orgao_descricao
                                WHERE orgao_descricao.timestamp = ( SELECT OD.timestamp FROM organograma.orgao_descricao AS OD
                                                                     WHERE TO_DATE(to_char(OD.timestamp, 'mmyyyy'), 'mmyyyy') < (TO_DATE('".$this->getDado('stMes')."', 'mmyyyy')-1)
                                                                       AND OD.cod_orgao=orgao_descricao.cod_orgao
                                                                  ORDER BY OD.timestamp DESC
                                                                     LIMIT 1
                                                                  )
                             GROUP BY orgao_descricao.cod_orgao
                                    , orgao_descricao.descricao
                                    , orgao_descricao.timestamp
                          ) AS orgao_descricao
                       ON orgao_descricao.cod_orgao=pensionista.cod_orgao

                    WHERE tipo_ato IS NOT NULL
                        
                 GROUP BY pensionista.lotacao
                        , pensionista.cod_orgao
                        , orgao_descricao.descricao

                 ORDER BY lotacao        
                ";
        return $stSql;
    }

}
?>