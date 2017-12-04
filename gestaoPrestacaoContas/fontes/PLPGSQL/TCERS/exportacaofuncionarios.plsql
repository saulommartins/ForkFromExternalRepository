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
/*
    $Id: exportacaofuncionarios.plsql 66249 2016-08-01 12:55:19Z michel $
*/

CREATE OR REPLACE FUNCTION tcers.busca_regime(stEntidade varchar, contrato integer) RETURNS varchar as $$
DECLARE
  stDesc VARCHAR;
  stSql  VARCHAR;

BEGIN
	
  stSql := '  SELECT 
                      regime_previdencia.descricao 
                  FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
            INNER JOIN ( SELECT cod_contrato
                               , max(timestamp) as timestamp
                           FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                       GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                    ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                   AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
            INNER JOIN folhapagamento'|| stEntidade ||'.previdencia
                    ON contrato_servidor_previdencia.cod_previdencia = previdencia.cod_previdencia
            INNER JOIN folhapagamento'|| stEntidade ||'.regime_previdencia
                    ON previdencia.cod_regime_previdencia = regime_previdencia.cod_regime_previdencia
		WHERE contrato_servidor_previdencia.cod_contrato = '|| contrato ||'; ';

  stDesc := selectIntoVarChar(stSql);

RETURN stDesc;

end;
$$LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION tcers.recuperarCadastroFuncionarios ( stEntidade VARCHAR, dtInicial VARCHAR, dtFinal VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE
  stSql      VARCHAR;
  reRegistro RECORD;
BEGIN

stSql := '(
              ( SELECT to_char(max_timestamp_periodo_movimentacao.dt_inicial,''dd/mm/yyyy'') as dt_inicial
                     , sw_cgm.nom_cgm as nome
                     , to_char(dt_nascimento, ''dd/mm/yyyy'') as dt_nascimento
                     , sw_cgm_pessoa_fisica.cpf as cpf
                     , sw_cgm_pessoa_fisica.servidor_pis_pasep
                     , sw_cgm_pessoa_fisica.rg
                     , ( CASE WHEN sw_cgm_pessoa_fisica.sexo = ''m''
                              THEN 1
                              ELSE 2
                         END
                       ) as sexo 
                     , contrato.registro as cod_registro_funcionario
                     , to_char(contrato_servidor_nomeacao_posse.dt_admissao,''dd/mm/yyyy'') as dt_admissao
                     , CASE WHEN to_char(contrato_servidor_caso_causa.dt_rescisao,''dd/mm/yyyy'') is null
                            THEN ''00000000''
                            ELSE to_char(contrato_servidor_caso_causa.dt_rescisao,''dd/mm/yyyy'')
                       END as dt_rescisao
                     , recuperaDescricaoOrgao(orgao.cod_orgao, '|| quote_literal(dtFinal) ||') as Setor
                     , orgao.cod_orgao as cod_setor
                     , cargo.cod_cargo
                     , cargo.descricao as cargo
                     , (SELECT codigo FROM pessoal'|| stEntidade ||'.cbo WHERE cbo_cargo.cod_cbo = cbo.cod_cbo) as cbo
                     , (SELECT descricao FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = contrato_servidor.cod_regime)||''-''||(SELECT descricao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = contrato_servidor.cod_sub_divisao) as natureza_cargo
                     , (SELECT count(*) FROM pessoal'|| stEntidade ||'.dependente WHERE dependente.numcgm = sw_cgm.numcgm AND cod_vinculo != 0)::integer as qtd_dependentes_irrf
                     , ( CASE WHEN situacao_contrato.situacao = ''Ativo''
                              THEN ''01''
                              WHEN situacao_contrato.situacao = ''Aposentado''
                              THEN ''02''
                              WHEN situacao_contrato.situacao = ''Pensionista''
                              THEN ''03''
                              ELSE ''99''
                         END
                       )::varchar as situacao
                     , ( CASE WHEN (SELECT CASE WHEN folhapagamento'|| stEntidade ||'.previdencia.cod_regime_previdencia = 1
                                                THEN ''02''
                                                WHEN folhapagamento'|| stEntidade ||'.previdencia.cod_regime_previdencia = 2
                                                THEN ''01''
                                                ELSE ''02''
                                           END
                                      FROM folhapagamento'|| stEntidade ||'.previdencia
                                INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                        ON folhapagamento'|| stEntidade ||'.previdencia.cod_previdencia = pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_previdencia
                                       AND pessoal'|| stEntidade ||'.contrato_servidor_previdencia.bo_excluido = false
                                       AND timestamp = (SELECT max(max_timestamp.timestamp)
                                                          FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia AS max_timestamp
                                                         WHERE pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_contrato = max_timestamp.cod_contrato
                                                           AND max_timestamp.timestamp::VARCHAR <= max_timestamp_periodo_movimentacao.timestamp_fechamento_periodo)
                                       AND pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_contrato = pessoal'|| stEntidade ||'.contrato_servidor.cod_contrato
                                  GROUP BY pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_contrato,
                                           pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_previdencia,
                                           folhapagamento'|| stEntidade ||'.previdencia.cod_regime_previdencia
                                   ) IS NULL
                              THEN ''APOSENT.PENS. FUNDO PREVID.''
                              ELSE CASE WHEN situacao_contrato.situacao <> ''Ativo''
                                         AND situacao_contrato.situacao <> ''Aposentado''
                                         AND situacao_contrato.situacao <> ''Pensionista''
                                        THEN situacao_contrato.situacao
                                   END
                         END
                       )::varchar AS observacoes        
                     , (CASE WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_regime = 1
                             THEN ''C''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_regime = 2
                             THEN ''E''
                             ELSE ''O''
                        END
                       )::varchar as cod_regime
                     , (CASE WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 1 THEN ''E''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 2 THEN ''E''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 3 THEN ''T''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 4 THEN ''E''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 5 THEN ''T''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 6 THEN ''C''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 7 THEN ''E''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 8 THEN ''T''
                             ELSE ''O''
                        END
                       )::varchar as cod_sub_divisao
                     , CASE WHEN ( SELECT CASE WHEN folhapagamento'|| stEntidade ||'.previdencia.cod_regime_previdencia = 1 THEN ''02''
                                               WHEN folhapagamento'|| stEntidade ||'.previdencia.cod_regime_previdencia = 2 THEN ''01''
                                               ELSE ''02''
                                          END
                                     FROM folhapagamento'|| stEntidade ||'.previdencia
                               INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                       ON folhapagamento'|| stEntidade ||'.previdencia.cod_previdencia = pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_previdencia
                                      AND pessoal'|| stEntidade ||'.contrato_servidor_previdencia.bo_excluido = false
                                      AND timestamp = (SELECT max(max_timestamp.timestamp)
                                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia AS max_timestamp
                                                        WHERE pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_contrato = max_timestamp.cod_contrato
                                                          AND max_timestamp.timestamp::VARCHAR <= max_timestamp_periodo_movimentacao.timestamp_fechamento_periodo
                                                      )
                                      AND pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_contrato = pessoal'|| stEntidade ||'.contrato_servidor.cod_contrato
                                 GROUP BY pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_contrato, 
                                          pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_previdencia,
                                          folhapagamento'|| stEntidade ||'.previdencia.cod_regime_previdencia
                                 ) IS NULL
                            THEN ''99''
                            ELSE ( SELECT CASE WHEN folhapagamento'|| stEntidade ||'.previdencia.cod_regime_previdencia = 1 THEN ''02''
                                               WHEN folhapagamento'|| stEntidade ||'.previdencia.cod_regime_previdencia = 2 THEN ''01''
                                               ELSE ''02''
                                          END
                                     FROM folhapagamento'|| stEntidade ||'.previdencia
                               INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                       ON folhapagamento'|| stEntidade ||'.previdencia.cod_previdencia = pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_previdencia
                                      AND pessoal'|| stEntidade ||'.contrato_servidor_previdencia.bo_excluido = false
                                      AND timestamp = (SELECT max(max_timestamp.timestamp)
                                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia AS max_timestamp
                                                        WHERE pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_contrato = max_timestamp.cod_contrato
                                                          AND max_timestamp.timestamp::VARCHAR <= max_timestamp_periodo_movimentacao.timestamp_fechamento_periodo
                                                      )
                                      AND pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_contrato = pessoal'|| stEntidade ||'.contrato_servidor.cod_contrato
                                 GROUP BY pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_contrato,
                                          pessoal'|| stEntidade ||'.contrato_servidor_previdencia.cod_previdencia,
                                          folhapagamento'|| stEntidade ||'.previdencia.cod_regime_previdencia
                                 )
                       END::varchar AS cod_regime_previdencia
                     , pessoal'|| stEntidade ||'.contrato_servidor.cod_categoria as cod_categoria
                     , (sw_cgm.logradouro||'',''||sw_cgm.numero|| '' - '' ||sw_cgm.complemento)::varchar as endereco
                     , (SELECT UPPER(nom_municipio) as nom_municipio FROM sw_municipio WHERE cod_municipio = sw_cgm.cod_municipio and cod_uf = sw_cgm.cod_uf )::varchar as cidade
                     , (SELECT sigla_uf FROM sw_uf WHERE sw_uf.cod_uf = sw_cgm.cod_uf)::varchar as uf
                     , sw_cgm.cep
                     , contrato_servidor_salario.horas_mensais AS carga_horaria
                     , ''M''::CHAR as tipo_carga_horaria
                     , CASE WHEN adido_cedido.tipo_cedencia = ''a''
                            THEN ''A''::CHAR
                            WHEN adido_cedido.tipo_cedencia= ''c''
                            THEN ''C''::CHAR
                            ELSE ''X''::CHAR
                       END AS cedido_adido
                     , CASE WHEN adido_cedido.tipo_cedencia = ''a'' AND adido_cedido.indicativo_onus = ''c''
                            THEN ''S''::CHAR
                            WHEN adido_cedido.tipo_cedencia = ''c'' AND adido_cedido.indicativo_onus = ''e''
                            THEN ''N''::CHAR
                            ELSE ''X''::CHAR
                       END AS onus_origem
                     , ''X''::CHAR as ressarcimento
                     , CASE WHEN adido_cedido.cod_contrato IS NOT NULL
                            THEN adido_cedido.dt_inicial::varchar 
                            ELSE ''00/00/0000''::varchar 
                       END AS data_movimentacao
                     , CASE WHEN adido_cedido.cod_contrato IS NOT NULL
                            THEN (SELECT cnpj FROM sw_cgm_pessoa_juridica  where numcgm = adido_cedido.cgm_cedente_cessionario)
                            ELSE ''''::VARCHAR
                       END AS cnpj_orgao_origem_destino

                  FROM pessoal'|| stEntidade ||'.servidor

            INNER JOIN sw_cgm
                    ON servidor.numcgm = sw_cgm.numcgm

            INNER JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

            INNER JOIN pessoal'|| stEntidade ||'.servidor_contrato_servidor
                    ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

            INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor
                    ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

            INNER JOIN pessoal'|| stEntidade ||'.contrato
                    ON servidor_contrato_servidor.cod_contrato = contrato.cod_contrato

            INNER JOIN ( SELECT cod_periodo_movimentacao
                              , dt_inicial
                              , dt_final
                              , timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo
                           FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                          WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                       ) AS max_timestamp_periodo_movimentacao
                    ON max_timestamp_periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'

            INNER JOIN ( SELECT contrato.*
                              , recuperarSituacaoDoContratoLiteral(contrato.cod_contrato::integer,periodo_movimentacao.cod_periodo_movimentacao,  '|| quote_literal(stEntidade) ||')::varchar AS situacao
                              , periodo_movimentacao.cod_periodo_movimentacao
                           FROM pessoal'|| stEntidade ||'.contrato
                     INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                             ON periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                       ) AS situacao_contrato
                    ON situacao_contrato.cod_contrato = contrato.cod_contrato
                   AND situacao_contrato.cod_periodo_movimentacao = max_timestamp_periodo_movimentacao.cod_periodo_movimentacao

            INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
                    ON contrato.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato
        
            INNER JOIN ( SELECT cod_contrato, max(timestamp) as timestamp 
                           FROM pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
                     INNER JOIN ( SELECT dt_inicial,
                                         timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo
                                    FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                   WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                                ) AS max_timestamp_periodo_movimentacao
                             ON max_timestamp_periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                          WHERE contrato_servidor_nomeacao_posse.timestamp::VARCHAR <= max_timestamp_periodo_movimentacao.timestamp_fechamento_periodo
                       GROUP BY cod_contrato
                       ) as max_contrato_servidor_nomeacao_posse
                    ON contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                   AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp

            INNER JOIN pessoal'|| stEntidade ||'.cargo
                    ON contrato_servidor.cod_cargo = cargo.cod_cargo

            INNER JOIN pessoal'|| stEntidade ||'.cbo_cargo
                    ON cargo.cod_cargo = cbo_cargo.cod_cargo

            INNER JOIN ( SELECT cod_cargo, max(timestamp) as timestamp 
                           FROM pessoal'|| stEntidade ||'.cbo_cargo
                     INNER JOIN ( SELECT dt_inicial,
                                         timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo
                                    FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                   WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                                ) AS max_timestamp_periodo_movimentacao
                             ON max_timestamp_periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                          WHERE cbo_cargo.timestamp::VARCHAR <= max_timestamp_periodo_movimentacao.timestamp_fechamento_periodo 
                       GROUP BY cod_cargo
                       ) as max_cbo_cargo 
                    ON cbo_cargo.cod_cargo = max_cbo_cargo.cod_cargo
                   AND cbo_cargo.timestamp = max_cbo_cargo.timestamp

            INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor_orgao
                    ON contrato.cod_contrato = contrato_servidor_orgao.cod_contrato

            INNER JOIN ( SELECT cod_contrato, max(timestamp) as timestamp 
                           FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                     INNER JOIN ( SELECT dt_inicial,
                                         timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo
                                    FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                   WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                                ) AS max_timestamp_periodo_movimentacao
                             ON max_timestamp_periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                          WHERE contrato_servidor_orgao.timestamp::VARCHAR <= max_timestamp_periodo_movimentacao.timestamp_fechamento_periodo
                       GROUP BY cod_contrato
                       ) as max_contrato_servidor_orgao 
                    ON contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
                   AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp

            INNER JOIN organograma.orgao
                    ON contrato_servidor_orgao.cod_orgao = orgao.cod_orgao

             LEFT JOIN ( SELECT cod_contrato, cod_previdencia, max(timestamp) as timestamp 
                           FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                     INNER JOIN ( SELECT dt_inicial,
                                         timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo
                                    FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                   WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                                ) AS max_timestamp_periodo_movimentacao
                             ON max_timestamp_periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                          WHERE contrato_servidor_previdencia.timestamp::VARCHAR <= max_timestamp_periodo_movimentacao.timestamp_fechamento_periodo
                       GROUP BY cod_contrato, cod_previdencia
                       ) as contrato_servidor_previdencia 
                    ON contrato.cod_contrato = contrato_servidor_previdencia.cod_contrato	        

             LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                    ON servidor_contrato_servidor.cod_contrato = contrato_servidor_caso_causa.cod_contrato
               
            INNER JOIN ( SELECT contrato_servidor_salario.* 
                           FROM pessoal'|| stEntidade ||'.contrato_servidor_salario
                     INNER JOIN ( SELECT cod_contrato
                                       , MAX(timestamp)as timestamp
                                    FROM pessoal'|| stEntidade ||'.contrato_servidor_salario
                              INNER JOIN folhapagamento.periodo_movimentacao
                                      ON periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                                     AND contrato_servidor_salario.cod_periodo_movimentacao <= periodo_movimentacao.cod_periodo_movimentacao
                                GROUP BY cod_contrato
                                ) as max_contrato_servidor_salario
                             ON max_contrato_servidor_salario.cod_contrato = contrato_servidor_salario.cod_contrato
                            AND max_contrato_servidor_salario.timestamp   = contrato_servidor_salario.timestamp               
                       ) as contrato_servidor_salario
                    ON contrato_servidor_salario.cod_contrato = contrato_servidor.cod_contrato

             LEFT JOIN ( SELECT adido_cedido.* 
                           FROM pessoal'|| stEntidade ||'.adido_cedido
                     INNER JOIN ( SELECT cod_contrato
                                       , cod_norma
                                       , MAX(timestamp) as timestamp
                                   FROM pessoal'|| stEntidade ||'.adido_cedido
                               GROUP BY 1,2
                                ) as max_adido_cedido
                             ON max_adido_cedido.cod_contrato = adido_cedido.cod_contrato
                            AND max_adido_cedido.cod_norma = adido_cedido.cod_norma
                            AND max_adido_cedido.timestamp = adido_cedido.timestamp
                     INNER JOIN ( SELECT dt_inicial,
                                         timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo
                                    FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                   WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                                ) AS max_timestamp_periodo_movimentacao
                             ON max_timestamp_periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                            AND adido_cedido.timestamp::VARCHAR <= max_timestamp_periodo_movimentacao.timestamp_fechamento_periodo                        
                          WHERE adido_cedido.cod_contrato NOT IN (SELECT cod_contrato 
                                                                    FROM pessoal'|| stEntidade ||'.adido_cedido_excluido
                                                                   WHERE adido_cedido_excluido.cod_contrato = adido_cedido.cod_contrato
                                                                     AND adido_cedido_excluido.cod_norma  = adido_cedido.cod_norma
                                                                     AND adido_cedido_excluido.timestamp = adido_cedido.timestamp 
                                                                 )
                       ) as adido_cedido
                    ON adido_cedido.cod_contrato = contrato_servidor.cod_contrato
              )

               UNION

              ( SELECT to_char(max_timestamp_periodo_movimentacao.dt_inicial,''dd/mm/yyyy'') as dt_inicial
                     , nom_cgm as nome
                     , to_char(dt_nascimento, ''dd/mm/yyyy'') as dt_nascimento
                     , cpf
                     , servidor_pis_pasep 
                     , rg
                     , (CASE WHEN sw_cgm_pessoa_fisica.sexo = ''m''
                             THEN 1
                             ELSE 2
                         END
                       ) as sexo
                     , contrato.registro as cod_registro_funcionario
                     , to_char(dt_inicio_beneficio,''dd/mm/yyyy'') as dt_admissao
                     , (CASE WHEN to_char(dt_encerramento,''dd/mm/yyyy'') is null
                             THEN ''00000000''
                             ELSE to_char(dt_encerramento,''dd/mm/yyyy'')
                         END
                       ) as dt_rescisao
                     , recuperaDescricaoOrgao(orgao.cod_orgao, '|| quote_literal(dtFinal) ||') as Setor
                     , orgao.cod_orgao as cod_setor
                     , cargo.cod_cargo
                     , cargo.descricao as cargo
                     , (SELECT codigo FROM pessoal'|| stEntidade ||'.cbo WHERE cbo_cargo.cod_cbo = cbo.cod_cbo) as cbo
                     , (SELECT descricao 
                          FROM pessoal'|| stEntidade ||'.regime 
                         WHERE cod_regime = contrato_servidor.cod_regime
                       )||''-''||
                       (SELECT descricao
                          FROM pessoal'|| stEntidade ||'.sub_divisao
                         WHERE cod_sub_divisao = contrato_servidor.cod_sub_divisao
                       ) as natureza_cargo
                     , (SELECT count(servidor_dependente.cod_dependente)
                          FROM pessoal'|| stEntidade ||'.servidor_dependente, pessoal'|| stEntidade ||'.dependente
                         WHERE cod_servidor= servidor_contrato_servidor.cod_servidor
                           AND servidor_dependente.cod_dependente=dependente.cod_dependente 
                           AND dependente.cod_vinculo > 0
                           AND servidor_dependente.cod_dependente not in (SELECT cod_dependente FROM pessoal'|| stEntidade ||'.dependente_excluido)
                       )::integer as qtd_dependentes_irrf
                     , (CASE WHEN situacao_contrato.situacao = ''Ativo''
                             THEN ''01''
                             WHEN situacao_contrato.situacao = ''Aposentado''
                             THEN ''02''
                             WHEN situacao_contrato.situacao = ''Pensionista''
                             THEN ''03''
                             ELSE ''99''
                         END
                       )::varchar as situacao
                     , ''PENSIONISTA FUNDO PREVID.''::varchar  AS observacoes
                     , (CASE WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_regime = 1
                             THEN ''C''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_regime = 2
                             THEN ''E''
                             ELSE ''O''
                         END
                       )::varchar as cod_regime
                     , (CASE WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 1
                             THEN ''E''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 2
                             THEN ''E''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 3
                             THEN ''T''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 4
                             THEN ''E''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 5
                             THEN ''T''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 6
                             THEN ''C''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 7
                             THEN ''E''
                             WHEN pessoal'|| stEntidade ||'.contrato_servidor.cod_sub_divisao = 8
                             THEN ''T''
                             ELSE ''O''
                         END
                       )::varchar as cod_sub_divisao 
                     , COALESCE((SELECT (CASE WHEN previdencia.cod_regime_previdencia = 1 THEN ''02''
                                              WHEN previdencia.cod_regime_previdencia = 2 THEN ''01''
                                              ELSE ''99''
                                         END
                                        )
                                   FROM folhapagamento'|| stEntidade ||'.previdencia
                                  WHERE previdencia.cod_regime_previdencia = contrato_pensionista_previdencia.cod_previdencia
                                ),''99'')
                       as cod_regime_previdencia
                     , pessoal'|| stEntidade ||'.contrato_servidor.cod_categoria as cod_categoria
                     , (sw_cgm.logradouro||'',''||sw_cgm.numero|| '' - '' ||sw_cgm.complemento)::varchar as endereco
                     , (SELECT UPPER(nom_municipio) as nom_municipio FROM sw_municipio WHERE cod_municipio = sw_cgm.cod_municipio and cod_uf = sw_cgm.cod_uf )::varchar as cidade
                     , (SELECT sigla_uf FROM sw_uf WHERE sw_uf.cod_uf = sw_cgm.cod_uf)::varchar as uf
                     , sw_cgm.cep
                     , contrato_servidor_salario.horas_mensais AS carga_horaria
                     , ''M''::CHAR as tipo_carga_horaria
                     , CASE WHEN adido_cedido.tipo_cedencia = ''a''
                            THEN ''A''::CHAR
                            WHEN adido_cedido.tipo_cedencia= ''c''
                            THEN ''C''::CHAR
                            ELSE ''X''::CHAR
                       END AS cedido_adido
                     , CASE WHEN adido_cedido.tipo_cedencia = ''a'' AND adido_cedido.indicativo_onus = ''c''
                            THEN ''S''::CHAR
                            WHEN adido_cedido.tipo_cedencia = ''c'' AND adido_cedido.indicativo_onus = ''e''
                            THEN ''N''::CHAR
                            ELSE ''X''::CHAR
                       END AS onus_origem
                     , ''X''::CHAR as ressarcimento
                     , CASE WHEN adido_cedido.cod_contrato IS NOT NULL
                            THEN adido_cedido.dt_inicial::varchar 
                            ELSE ''00/00/0000''::varchar  
                       END AS data_movimentacao
                     , CASE WHEN adido_cedido.cod_contrato IS NOT NULL
                            THEN (SELECT cnpj FROM sw_cgm_pessoa_juridica  where numcgm = adido_cedido.cgm_cedente_cessionario)
                            ELSE ''''::VARCHAR
                       END AS cnpj_orgao_origem_destino

                  FROM sw_cgm

            INNER JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

            INNER JOIN pessoal'|| stEntidade ||'.pensionista
                    ON pensionista.numcgm = sw_cgm.numcgm

            INNER JOIN pessoal'|| stEntidade ||'.contrato_pensionista
                    ON contrato_pensionista.cod_pensionista=pensionista.cod_pensionista
                   AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente

            INNER JOIN pessoal'|| stEntidade ||'.contrato
                    ON contrato.cod_contrato = contrato_pensionista.cod_contrato

            INNER JOIN ( SELECT cod_periodo_movimentacao
                              , dt_inicial
                              , dt_final
                              , timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo
                           FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                          WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                       ) AS max_timestamp_periodo_movimentacao
                    ON max_timestamp_periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'

            INNER JOIN ( SELECT contrato.*
                              , recuperarSituacaoDoContratoLiteral(contrato.cod_contrato::integer,periodo_movimentacao.cod_periodo_movimentacao,  '|| quote_literal(stEntidade) ||')::varchar AS situacao
                              , periodo_movimentacao.cod_periodo_movimentacao
                           FROM pessoal'|| stEntidade ||'.contrato
                     INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                             ON periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                       ) AS situacao_contrato
                    ON situacao_contrato.cod_contrato = contrato.cod_contrato
                   AND situacao_contrato.cod_periodo_movimentacao = max_timestamp_periodo_movimentacao.cod_periodo_movimentacao

            INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor
                    ON contrato_servidor.cod_contrato = pensionista.cod_contrato_cedente

            INNER JOIN pessoal'|| stEntidade ||'.servidor_contrato_servidor
                    ON servidor_contrato_servidor.cod_contrato = pensionista.cod_contrato_cedente

            INNER JOIN pessoal'|| stEntidade ||'.cargo
                    ON contrato_servidor.cod_cargo = cargo.cod_cargo

             LEFT JOIN pessoal'|| stEntidade ||'.cbo_cargo
                    ON cargo.cod_cargo = cbo_cargo.cod_cargo

            INNER JOIN ( SELECT cod_cargo, max(timestamp) as timestamp 
                           FROM pessoal'|| stEntidade ||'.cbo_cargo
                     INNER JOIN ( SELECT dt_inicial,
                                         timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo
                                    FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                   WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                                ) AS max_timestamp_periodo_movimentacao
                             ON max_timestamp_periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                          WHERE cbo_cargo.timestamp::VARCHAR <= max_timestamp_periodo_movimentacao.timestamp_fechamento_periodo 
                       GROUP BY cod_cargo
                       ) as max_cbo_cargo 
                    ON cbo_cargo.cod_cargo = max_cbo_cargo.cod_cargo
                   AND cbo_cargo.timestamp = max_cbo_cargo.timestamp

            INNER JOIN pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                    ON contrato.cod_contrato = contrato_pensionista_orgao.cod_contrato

            INNER JOIN ( SELECT cod_contrato, max(timestamp) as timestamp 
                           FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                     INNER JOIN ( SELECT dt_inicial,
                                         timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo
                                    FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                   WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                                ) AS max_timestamp_periodo_movimentacao
                             ON max_timestamp_periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                          WHERE contrato_pensionista_orgao.timestamp::VARCHAR <= max_timestamp_periodo_movimentacao.timestamp_fechamento_periodo
                       GROUP BY cod_contrato
                       ) as max_contrato_pensionista_orgao 
                    ON contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato
                   AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp

            INNER JOIN organograma.orgao
                    ON contrato_pensionista_orgao.cod_orgao = orgao.cod_orgao

             LEFT JOIN ( SELECT cod_contrato, cod_previdencia, max(timestamp) as timestamp 
                           FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                     INNER JOIN ( SELECT dt_inicial,
                                         timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo
                                    FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                   WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                                ) AS max_timestamp_periodo_movimentacao
                             ON max_timestamp_periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                          WHERE contrato_pensionista_previdencia.timestamp::VARCHAR <= max_timestamp_periodo_movimentacao.timestamp_fechamento_periodo
                       GROUP BY cod_contrato, cod_previdencia
                       ) as contrato_pensionista_previdencia 
                    ON contrato.cod_contrato = contrato_pensionista_previdencia.cod_contrato

            INNER JOIN ( SELECT contrato_servidor_salario.* 
                           FROM pessoal'|| stEntidade ||'.contrato_servidor_salario
                     INNER JOIN (SELECT cod_contrato
                                      , MAX(timestamp)as timestamp
                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_salario
                             INNER JOIN folhapagamento.periodo_movimentacao
                                     ON periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                                    AND contrato_servidor_salario.cod_periodo_movimentacao <= periodo_movimentacao.cod_periodo_movimentacao
                               GROUP BY cod_contrato
                                ) as max_contrato_servidor_salario
                             ON max_contrato_servidor_salario.cod_contrato = contrato_servidor_salario.cod_contrato
                            AND max_contrato_servidor_salario.timestamp   = contrato_servidor_salario.timestamp
                       ) as contrato_servidor_salario
                    ON contrato_servidor_salario.cod_contrato = contrato_servidor.cod_contrato

             LEFT JOIN (
                         SELECT adido_cedido.* 
                           FROM pessoal'|| stEntidade ||'.adido_cedido
                     INNER JOIN ( SELECT cod_contrato
                                       , cod_norma
                                       , MAX(timestamp) as timestamp
                                    FROM pessoal'|| stEntidade ||'.adido_cedido
                                GROUP BY 1,2
                                ) as max_adido_cedido
                             ON max_adido_cedido.cod_contrato = adido_cedido.cod_contrato
                            AND max_adido_cedido.cod_norma = adido_cedido.cod_norma
                            AND max_adido_cedido.timestamp = adido_cedido.timestamp
                     INNER JOIN ( SELECT dt_inicial,
                                         timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo
                                    FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                   WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                                ) AS max_timestamp_periodo_movimentacao
                             ON max_timestamp_periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicial) ||' AND '|| quote_literal(dtFinal) ||'
                            AND adido_cedido.timestamp::VARCHAR <= max_timestamp_periodo_movimentacao.timestamp_fechamento_periodo
                          WHERE adido_cedido.cod_contrato NOT IN (SELECT cod_contrato 
                                                                    FROM pessoal'|| stEntidade ||'.adido_cedido_excluido
                                                                   WHERE adido_cedido_excluido.cod_contrato = adido_cedido.cod_contrato
                                                                     AND adido_cedido_excluido.cod_norma  = adido_cedido.cod_norma
                                                                    AND adido_cedido_excluido.timestamp = adido_cedido.timestamp 
                                                                 )
                       ) as adido_cedido
                    ON adido_cedido.cod_contrato = contrato_servidor.cod_contrato
              )
          )  ORDER BY dt_inicial, nome, cod_registro_funcionario ';

  FOR reRegistro IN EXECUTE stSql
     LOOP
       RETURN NEXT reRegistro;
     END LOOP;

  RETURN;
END;
$$LANGUAGE plpgsql;
