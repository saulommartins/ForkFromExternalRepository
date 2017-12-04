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
CREATE OR REPLACE FUNCTION tcepb.fn_retorna_assentamento( inCodContrato INTEGER, stPeriodoMovimentacao VARCHAR, stEntidade VARCHAR ) RETURNS INTEGER AS $$
DECLARE
    stSql               VARCHAR;
    inCodAssentamento   INTEGER;
BEGIN

    stSql := ' 
        select
             assentamento_motivo.cod_motivo
            ,max(assentamento_gerado.timestamp) as timestamp
        from
            pessoal'|| stEntidade ||'.contrato_servidor
        join
            pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
        on
            contrato_servidor'|| stEntidade ||'.cod_contrato = assentamento_gerado_contrato_servidor.cod_contrato
        join
            pessoal'|| stEntidade ||'.assentamento_gerado
        on
            assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
        join
            pessoal'|| stEntidade ||'.assentamento_assentamento
        on
            assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento
        join
            pessoal'|| stEntidade ||'.assentamento_motivo
        on
            assentamento_motivo.cod_motivo = assentamento_assentamento.cod_motivo
        where
                contrato_servidor.cod_contrato = '|| inCodContrato ||'
            and to_char(assentamento_gerado.periodo_inicial, ''mmyyyy'') = '|| quote_literal(stPeriodoMovimentacao) ||'
        group by
            assentamento_motivo.cod_motivo
        ';

    EXECUTE stSql INTO inCodAssentamento;            
    
    RETURN inCodAssentamento;

END;
$$ LANGUAGE plpgsql; 


CREATE OR REPLACE FUNCTION tcepb.fn_retorna_ato_pessoal( inCodTipoCargo              INTEGER,
                                                         inCodTipoAdimissao          INTEGER,
                                                         inCodContrato               INTEGER,
                                                         stPeriodo                   VARCHAR,
                                                         inCodPeriodoMovimentacao    INTEGER,
                                                         stEntidade                  VARCHAR,
                                                         inNumCgm                    INTEGER
                                                       ) RETURNS INTEGER AS $$
DECLARE
    rcDados            RECORD;
    inCodAtoPessoal    INTEGER;
    rcDadosAux         RECORD;
    stData             VARCHAR;
    stCampo            VARCHAR;
    stSql              VARCHAR;
    inNumCgmRegistrado INTEGER;
    
BEGIN
    -- Veririca se o servidor já foi cadastrado antes, caso seja, pode fazer todas as validações, caso contrário
    -- ele poderá apenas retornar o ato pessoal como 1, 2, 3, 5, 6, 8, 16
    SELECT numcgm
      INTO inNumCgmRegistrado
      FROM tcepb.servidores
     WHERE servidores.numcgm = inNumCgm
       AND to_number(substr(servidores.periodo, 3, 4)||substr(servidores.periodo, 1 ,2), '999999') < to_number(substr(stPeriodo, 3, 4)||substr(stPeriodo, 1 ,2), '999999');
    
    stSql := '
        SELECT causa_rescisao.num_causa AS codigo
             , to_char(contrato_servidor_caso_causa.dt_rescisao, ''ddmmyyyy'') AS data
          FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
    INNER JOIN pessoal'|| stEntidade ||'.caso_causa
            ON caso_causa.cod_caso_causa = contrato_servidor_caso_causa.cod_caso_causa
    INNER JOIN pessoal'|| stEntidade ||'.causa_rescisao
            ON causa_rescisao.cod_causa_rescisao = caso_causa.cod_causa_rescisao
         WHERE contrato_servidor_caso_causa.cod_contrato = '|| inCodContrato ||'
           AND to_char(contrato_servidor_caso_causa.dt_rescisao, ''mmyyyy'') = '|| quote_literal(stPeriodo) ||' ';
           
    IF (inNumCgmRegistrado IS NULL) THEN
        stSql := stSql ||' AND causa_rescisao.num_causa IN (10, 11, 12, 70, 79)';
    END IF;
    
    EXECUTE stSql INTO rcDados;

    -- reazlia a busca pelo num_causa, para saber se o tipo pode ser 5, 6, 7 ou 9
    SELECT cod_ato_pessoal
      INTO inCodAtoPessoal
      FROM tmp_ato_pessoal
     WHERE tipo   = 3
       AND codigo = rcDados.codigo;

    -- caso nao ache nenhum num_causa para o contrato, continua as verificações do tipo de ato_pessoal
    IF (inCodAtoPessoal IS NOT NULL) THEN
        INSERT INTO tmp_data_movimentacao VALUES (inCodContrato, rcDados.data);
    ELSE
        stSql := '
            SELECT assentamento_assentamento.cod_motivo AS codigo
                 , to_char(assentamento_gerado.periodo_inicial, ''ddmmyyyy'') AS data
              FROM pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
        INNER JOIN pessoal'|| stEntidade ||'.assentamento_gerado
                ON assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado
        INNER JOIN pessoal'|| stEntidade ||'.assentamento_assentamento
                ON assentamento_assentamento.cod_assentamento = assentamento_gerado.cod_assentamento

             WHERE ';

        IF ( stPeriodo = '012009' ) THEN 
            stSql := stSql  ||'
                ( to_date(to_char(assentamento_gerado.periodo_final, ''mmyyyy''), ''mmyyyy'')  <= to_date('|| quote_literal(stPeriodo) ||', ''mmyyyy'') OR
                  to_date(to_char(assentamento_gerado.periodo_inicial, ''mmyyyy''), ''mmyyyy'') = to_date('|| quote_literal(stPeriodo) ||', ''mmyyyy'') ) 
            ';
        ELSE
            stSql := stSql  ||'
                   assentamento_gerado.timestamp   <= ultimoTimestampPeriodoMovimentacao('|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||')::timestamp
               AND assentamento_gerado.timestamp   >= (SELECT MIN(periodo_movimentacao_situacao.timestamp)
                                                                              FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao_situacao
                                                                             WHERE periodo_movimentacao_situacao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                                           )
            ';

        END IF;

        stSql := stSql  ||'
                AND assentamento_gerado_contrato_servidor.cod_contrato = '|| inCodContrato ||'
               AND NOT EXISTS ( SELECT 1
                                  FROM pessoal'|| stEntidade ||'.assentamento_gerado_excluido
                                 WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                   AND assentamento_gerado_excluido.timestamp               = assentamento_gerado.timestamp
                               )
        ';

        IF ( stPeriodo != '012009' ) THEN 
            stSql := stSql  ||'
                ORDER BY assentamento_gerado.timestamp DESC
                LIMIT 1
            ';
        END IF;

        EXECUTE stSql INTO rcDados;

        -- Usado logo abaixo caso seja admissao ou um tipo de admissao determinado
        rcDadosAux := rcDados;
        
        -- Realiza a busca pelo motivo para verificar se ele se encontra entre os códigos de motivos por algum tipo de afastamento
        SELECT cod_ato_pessoal
          INTO inCodAtoPessoal
          FROM tmp_ato_pessoal
         WHERE tipo   = 4
           AND codigo = rcDados.codigo;

        IF (inCodAtoPessoal IS NOT NULL AND inNumCgmRegistrado IS NOT NULL) THEN
            INSERT INTO tmp_data_movimentacao VALUES (inCodContrato, rcDados.data);
        ELSE
            -- Realiza a busca se houve alguma alteração de lotação no contrato para o periodo e se esse dado que veio nao é o mesmo
            -- da admissao
            stSql := '
            SELECT ''18''  
              FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
             WHERE contrato_servidor_orgao.cod_contrato = '|| inCodContrato ||'
               AND contrato_servidor_orgao.timestamp   <= ultimoTimestampPeriodoMovimentacao('|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||')::timestamp
               AND contrato_servidor_orgao.timestamp   >= (SELECT MIN(periodo_movimentacao_situacao.timestamp)
                                                             FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao_situacao
                                                            WHERE periodo_movimentacao_situacao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                          )
               AND contrato_servidor_orgao.timestamp   <> (
                                                               SELECT assentamento_gerado.timestamp
                                                                 FROM pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
                                                           INNER JOIN pessoal'|| stEntidade ||'.assentamento_gerado
                                                                   ON assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado
                                                                WHERE assentamento_gerado_contrato_servidor.cod_contrato = '|| inCodContrato ||'
                                                                  AND NOT EXISTS ( SELECT 1
                                                                                   FROM pessoal'|| stEntidade ||'.assentamento_gerado_excluido
                                                                                  WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                                                                    AND assentamento_gerado_excluido.timestamp               = assentamento_gerado.timestamp
                                                                                )
                                                             ORDER BY assentamento_gerado.timestamp
                                                                LIMIT 1
                                                          )';
            EXECUTE stSql INTO inCodAtoPessoal;
            
            IF (inCodAtoPessoal IS NOT NULL AND inNumCgmRegistrado IS NOT NULL) THEN
                INSERT INTO tmp_data_movimentacao VALUES (inCodContrato, '01'|| stPeriodo);
            ELSE
                -- Verifica se o tipo de admissao do contrato
                -- se o tipo de admissao é 3,4,5 ou 7
                IF ((inCodTipoAdimissao > 2 AND inCodTipoAdimissao < 6) OR (inCodTipoAdimissao = 7) AND inNumCgmRegistrado IS NOT NULL) THEN    
                    SELECT cod_ato_pessoal
                      INTO inCodAtoPessoal
                      FROM tmp_ato_pessoal
                     WHERE codigo = inCodTipoAdimissao
                       AND tipo   = 2;

                ELSE
                    SELECT cod_ato_pessoal
                      INTO inCodAtoPessoal
                      FROM tmp_ato_pessoal
                     WHERE codigo = inCodTipoCargo
                       AND tipo   = 1;
                END IF;

                IF (rcDadosAux.data IS NULL) THEN
                    SELECT CASE WHEN valor = 'dtAdmissao' THEN 'dt_admissao'
                                WHEN valor = 'dtNomeacao' THEN 'dt_nomeacao'
                                WHEN valor = 'dtPosse'    THEN 'dt_posse'
                            END
                      INTO stCampo
                      FROM administracao.configuracao
                     WHERE parametro ilike 'dtContagemInicial'
                       AND exercicio = substr(stPeriodo, 3, 4);

                    stSql := '
                    SELECT to_char(contrato_servidor_nomeacao_posse.dt_admissao, ''ddmmyyyy'') AS dt_admissao
                         , to_char(contrato_servidor_nomeacao_posse.dt_nomeacao, ''ddmmyyyy'') AS dt_nomeacao
                         , to_char(contrato_servidor_nomeacao_posse.dt_posse, ''ddmmyyyy'') AS dt_posse
                      FROM pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
                     WHERE contrato_servidor_nomeacao_posse.cod_contrato = '|| inCodContrato;

                    IF ( stPeriodo != '012009' ) THEN 
                        stSql := stSql  ||'
                            AND contrato_servidor_nomeacao_posse.timestamp   <= ultimoTimestampPeriodoMovimentacao('|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||')::timestamp
                            AND contrato_servidor_nomeacao_posse.timestamp   >= (SELECT MIN(periodo_movimentacao_situacao.timestamp)
                                                                                   FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao_situacao
                                                                                  WHERE periodo_movimentacao_situacao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                                                )
                       ORDER BY contrato_servidor_nomeacao_posse.timestamp DESC
                        LIMIT 1
                        ';
                    END IF;

                    EXECUTE stSql INTO rcDadosAux;
                    
                    IF (stCampo = 'dtAdmissao') THEN
                        INSERT INTO tmp_data_movimentacao VALUES (inCodContrato, rcDadosAux.dt_admissao);
                    ELSIF (stCampo = 'dtNomeacao') THEN
                        INSERT INTO tmp_data_movimentacao VALUES (inCodContrato, rcDadosAux.dt_nomeacao);
                    ELSE
                        INSERT INTO tmp_data_movimentacao VALUES (inCodContrato, rcDadosAux.dt_posse);
                    END IF;
                    
                ELSE
                    INSERT INTO tmp_data_movimentacao VALUES (inCodContrato, rcDadosAux.data);
                END IF;
            END IF;
        END IF;
    END IF;
    
    -- se depois de todas as verificações, o código continuar vazio, retornará o código 99 (outros)
    IF (inCodAtoPessoal IS NULL) THEN
        inCodAtoPessoal = 99;
    END IF;

    RETURN inCodAtoPessoal;
END;
$$ LANGUAGE plpgsql;

--CREATE OR REPLACE FUNCTION tcepb.recupera_historico_funcional(stEntidade VARCHAR, stPeriodoMovimentacao VARCHAR) RETURNS VOID AS $$

CREATE OR REPLACE FUNCTION tcepb.recupera_historico_funcional(stEntidade VARCHAR, stPeriodoMovimentacao VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stSql           VARCHAR;
    stSql2          VARCHAR;
    stTmp           VARCHAR;
    reRegistro      RECORD;
    crCursor        REFCURSOR;
    inCodSituacao   INTEGER;
    masc_orgao      VARCHAR := '';
    masc_unidade    VARCHAR := '';
    inCodCargoTCE   INTEGER;
BEGIN

    CREATE TEMPORARY TABLE tmp_acumulacao (
        cod_servidor INTEGER NOT NULL,
        cod_contrato INTEGER NOT NULL,
        cod_situacao INTEGER NOT NULL
    );
    
    CREATE TEMPORARY TABLE tmp_ato_pessoal (
        codigo          INTEGER,
        tipo            INTEGER,
        cod_ato_pessoal INTEGER
    );
    
    CREATE TEMPORARY TABLE tmp_data_movimentacao (
        cod_contrato integer,
        data         varchar(8)
    );
    
    stSql = '      SELECT servidor.cod_servidor
                        , servidor_contrato_servidor.cod_contrato
                     FROM pessoal'|| stEntidade ||'.servidor_contrato_servidor
               INNER JOIN pessoal'|| stEntidade ||'.servidor
                       ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

               INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_periodo
                       ON registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato

               INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao

                    WHERE to_char(periodo_movimentacao.dt_final, ''mmyyyy'') = '|| quote_literal(stPeriodoMovimentacao) ||'

                 GROUP BY servidor.cod_servidor 
                        , servidor_contrato_servidor.cod_contrato

                 ORDER BY servidor.cod_servidor
                        , servidor_contrato_servidor.cod_contrato';
    
    FOR reRegistro IN EXECUTE stSql LOOP
        SELECT COALESCE(COUNT(cod_situacao), 0)
          INTO inCodSituacao
          FROM tmp_acumulacao
         WHERE cod_servidor = reRegistro.cod_servidor;

        INSERT INTO tmp_acumulacao VALUES (reRegistro.cod_servidor, reRegistro.cod_contrato, inCodSituacao);
    END LOOP;
    
    --codigo se refere a tabela interna do manual do sagres: tipo de cargo
    INSERT INTO tmp_ato_pessoal VALUES (1, 1, 1);
    INSERT INTO tmp_ato_pessoal VALUES (2, 1, 16);
    INSERT INTO tmp_ato_pessoal VALUES (3, 1, 2);
    INSERT INTO tmp_ato_pessoal VALUES (5, 1, 8);
    INSERT INTO tmp_ato_pessoal VALUES (6, 1, 3);
    
    -- codigo do campo cod_tipo_admissao, da tabela pessoal_.contrato_servidor
    INSERT INTO tmp_ato_pessoal VALUES (3, 2 ,12);
    INSERT INTO tmp_ato_pessoal VALUES (5, 2 ,4);
    INSERT INTO tmp_ato_pessoal VALUES (4, 2 ,13);
    INSERT INTO tmp_ato_pessoal VALUES (7, 2 ,15);
    
    -- campo num_causa da tabela pessoal_.causa_rescisao
    INSERT INTO tmp_ato_pessoal VALUES (10, 3, 6);
    INSERT INTO tmp_ato_pessoal VALUES (11, 3, 6);
    INSERT INTO tmp_ato_pessoal VALUES (12, 3, 9);
    INSERT INTO tmp_ato_pessoal VALUES (20, 3, 6);
    INSERT INTO tmp_ato_pessoal VALUES (21, 3, 9);
    INSERT INTO tmp_ato_pessoal VALUES (60, 3, 7);
    INSERT INTO tmp_ato_pessoal VALUES (62, 3, 7);
    INSERT INTO tmp_ato_pessoal VALUES (64, 3, 7);
    INSERT INTO tmp_ato_pessoal VALUES (70, 3, 5);
    INSERT INTO tmp_ato_pessoal VALUES (79, 3, 5);
    INSERT INTO tmp_ato_pessoal VALUES (80, 3, 9);
    
    -- codigo da do campo pessoal_.assentamento_assentamento.cod_motivo
    INSERT INTO tmp_ato_pessoal VALUES (3, 4, 10);
    INSERT INTO tmp_ato_pessoal VALUES (5, 4, 17);
    INSERT INTO tmp_ato_pessoal VALUES (6, 4, 17);
    INSERT INTO tmp_ato_pessoal VALUES (7, 4, 17);
    INSERT INTO tmp_ato_pessoal VALUES (9, 4, 10);
  
    -- buscando mascara de orgao e unidade     
    SELECT split_part(valor,'.',1) into masc_orgao from administracao.configuracao where parametro = 'masc_despesa' and exercicio = substr(stPeriodoMovimentacao, 3, 4);    
    SELECT split_part(valor,'.',2) into masc_unidade from administracao.configuracao where parametro = 'masc_despesa' and exercicio = substr(stPeriodoMovimentacao, 3, 4);


    stSql := '
    CREATE TEMPORARY TABLE tmp_historico_funcional AS (
            SELECT
                 sw_cgm_pessoa_fisica.cpf AS cpf_servidor
--                 ,case when tcepb.fn_retorna_ato_pessoal(  de_para_tipo_cargo.cod_tipo_cargo_tce
--                                               , contrato_servidor.cod_tipo_admissao
--                                               , contrato.cod_contrato
--                                               , to_char(periodo_movimentacao.dt_final, ''mmyyyy'')
--                                               , periodo_movimentacao.cod_periodo_movimentacao
--                                               , ''''
--                                               , sw_cgm_pessoa_fisica.numcgm
--                                              ) = 17 then
,                        case when tcepb.fn_retorna_assentamento(contrato.cod_contrato, to_char(periodo_movimentacao.dt_final, ''mmyyyy''), '''') = 5 then
                            8888
                        when tcepb.fn_retorna_assentamento(contrato.cod_contrato, to_char(periodo_movimentacao.dt_final, ''mmyyyy''), '''') = 7 then
                            9999

                        when tcepb.fn_retorna_assentamento(contrato.cod_contrato, to_char(periodo_movimentacao.dt_final, ''mmyyyy''), '''') = 8 then
                            0
--                        else 
--                            1111
--                        end
                  else                      
                        contrato_servidor.cod_cargo
                  end as cod_cargo
                , contrato.cod_contrato
                , contrato.registro AS matricula
                , (SELECT tmp_acumulacao.cod_situacao
                     FROM tmp_acumulacao
                    WHERE tmp_acumulacao.cod_contrato = contrato.cod_contrato
                      AND tmp_acumulacao.cod_servidor = servidor.cod_servidor
                   ) AS situacao_acumulacao
                , CAST(to_char(periodo_movimentacao.dt_final, ''mmyyyy'') AS VARCHAR(6)) AS ano_mes_referencia
                , pensionista.cpf AS cpf_pensionista
                , tcepb.fn_retorna_ato_pessoal(  de_para_tipo_cargo.cod_tipo_cargo_tce
                                               , contrato_servidor.cod_tipo_admissao
                                               , contrato.cod_contrato
                                               , to_char(periodo_movimentacao.dt_final, ''mmyyyy'')
                                               , periodo_movimentacao.cod_periodo_movimentacao
                                               , ''''
                                               , sw_cgm_pessoa_fisica.numcgm
                                              ) AS ato_movimentacao
                , recuperarSituacaoDoContrato(contrato.cod_contrato, periodo_movimentacao.cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') AS situacao_funcional
                , (
                    SELECT csp.cod_previdencia
                      FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia as csp
                     WHERE csp.cod_contrato = contrato.cod_contrato
                       AND csp.bo_excluido  = ''f''
                  ORDER BY csp.timestamp DESC
                     LIMIT 1
                  ) as tipo_regime_previdenciario
                --, contrato_servidor_previdencia.cod_previdencia AS tipo_regime_previdenciario
                , de_para_tipo_regime_trabalho.cod_tipo_regime_trabalho_tce AS tipo_regime_trabalho
                , CAST(sw_fn_mascara_dinamica('|| quote_literal(masc_orgao) ||', de_para_orgao_unidade.num_orgao::varchar)||sw_fn_mascara_dinamica('|| quote_literal(masc_unidade) ||', de_para_orgao_unidade.num_unidade::varchar) as varchar) AS lotacao_servidor_cargo

              FROM pessoal'|| stEntidade ||'.servidor
        
        INNER JOIN sw_cgm
                ON sw_cgm.numcgm = servidor.numcgm
        
        INNER JOIN sw_cgm_pessoa_fisica
                ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
        
        INNER JOIN pessoal'|| stEntidade ||'.servidor_contrato_servidor
                ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
        
        INNER JOIN pessoal'|| stEntidade ||'.contrato
                ON contrato.cod_contrato = servidor_contrato_servidor.cod_contrato
        
        INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor
                ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato
        
        INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor_orgao
                ON contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato
               AND contrato_servidor_orgao.timestamp = (SELECT MAX(timestamp)
                                                          FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao AS x
                                                         WHERE x.cod_contrato = contrato_servidor_orgao.cod_contrato
                                                       )
         LEFT JOIN pessoal'|| stEntidade ||'.de_para_orgao_unidade
                ON de_para_orgao_unidade.cod_orgao = contrato_servidor_orgao.cod_orgao
               AND de_para_orgao_unidade.exercicio = substr('|| quote_literal(stPeriodoMovimentacao) ||', 3, 4)
        
        INNER JOIN pessoal'|| stEntidade ||'.sub_divisao
                ON sub_divisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao
        
        INNER JOIN pessoal'|| stEntidade ||'.de_para_tipo_cargo
                ON de_para_tipo_cargo.cod_sub_divisao = sub_divisao.cod_sub_divisao
       
        INNER JOIN pessoal'|| stEntidade ||'.de_para_tipo_regime_trabalho
                ON de_para_tipo_regime_trabalho.cod_sub_divisao = sub_divisao.cod_sub_divisao
        
--        INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_periodo
--                ON registro_evento_periodo.cod_contrato = contrato.cod_contrato

        INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_periodo
                ON registro_evento_periodo.cod_periodo_movimentacao = (
                                                                        SELECT cod_periodo_movimentacao
                                                                          FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                                         WHERE TO_CHAR(periodo_movimentacao.dt_final, ''mmyyyy'') = '|| quote_literal(stPeriodoMovimentacao) ||'
                                                                      )
               AND registro_evento_periodo.cod_contrato = contrato.cod_contrato
        
        INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao
        
--        INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor_previdencia
--                ON contrato_servidor_previdencia.cod_contrato = contrato_servidor.cod_contrato
--               AND contrato_servidor_previdencia.timestamp = (
--                                                              SELECT MAX(timestamp)
--                                                                FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia AS x
--                                                               WHERE x.cod_contrato = contrato_servidor.cod_contrato
--                                                                 AND x.bo_excluido  = ''f''
--                                                            GROUP BY x.cod_contrato
--                                                             )

         LEFT JOIN (
                        SELECT sw_cgm_pessoa_fisica.cpf
                             , contrato_pensionista.cod_contrato
                          FROM pessoal'|| stEntidade ||'.contrato_pensionista
                    INNER JOIN pessoal'|| stEntidade ||'.pensionista
                            ON pensionista.cod_pensionista      = pensionista.cod_pensionista
                           AND pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
                    INNER JOIN sw_cgm_pessoa_fisica
                            ON pensionista.numcgm = sw_cgm_pessoa_fisica.numcgm
                   ) AS pensionista 
                ON pensionista.cod_contrato = contrato.cod_contrato
        
            WHERE EXISTS (
                    ( 
                        -- Verificando complementar
                          SELECT 1
                            FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                            JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento_complementar
                              ON ultimo_registro_evento_complementar.cod_registro     = registro_evento_complementar.cod_registro
                             AND ultimo_registro_evento_complementar.timestamp        = registro_evento_complementar.timestamp
                             AND ultimo_registro_evento_complementar.cod_evento       = registro_evento_complementar.cod_evento
                             AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao
                            JOIN folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                              ON evento_complementar_calculado.cod_registro       = ultimo_registro_evento_complementar.cod_registro
                             AND evento_complementar_calculado.timestamp_registro = ultimo_registro_evento_complementar.timestamp
                             AND evento_complementar_calculado.cod_evento         = ultimo_registro_evento_complementar.cod_evento
                             AND evento_complementar_calculado.cod_configuracao   = ultimo_registro_evento_complementar.cod_configuracao
                           WHERE registro_evento_complementar.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                             AND registro_evento_complementar.cod_contrato = contrato.cod_contrato
            
                           UNION
            
                        -- Verificando salario
                          SELECT 1
                            FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                            JOIN folhapagamento'|| stEntidade ||'.registro_evento
                              ON registro_evento.cod_registro = registro_evento_periodo.cod_registro
                            JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento
                              ON ultimo_registro_evento.cod_registro = registro_evento.cod_registro
                             AND ultimo_registro_evento.timestamp    = registro_evento.timestamp
                             AND ultimo_registro_evento.cod_evento   = registro_evento.cod_evento
                            JOIN folhapagamento'|| stEntidade ||'.evento_calculado
                              ON evento_calculado.cod_registro = ultimo_registro_evento.cod_registro
                             AND evento_calculado.timestamp_registro = ultimo_registro_evento.timestamp
                             AND evento_calculado.cod_evento         = ultimo_registro_evento.cod_evento
                           WHERE registro_evento_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                             AND registro_evento_periodo.cod_contrato = contrato.cod_contrato
            
                           UNION
            
                        -- Verificando ferias
                          SELECT 1
                            FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                            JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento_ferias
                              ON ultimo_registro_evento_ferias.cod_registro  = registro_evento_ferias.cod_registro
                             AND ultimo_registro_evento_ferias.timestamp     = registro_evento_ferias.timestamp
                             AND ultimo_registro_evento_ferias.cod_evento    = registro_evento_ferias.cod_evento
                             AND ultimo_registro_evento_ferias.desdobramento = registro_evento_ferias.desdobramento
                            JOIN folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                              ON evento_ferias_calculado.cod_registro       = ultimo_registro_evento_ferias.cod_registro
                             AND evento_ferias_calculado.timestamp_registro = ultimo_registro_evento_ferias.timestamp
                             AND evento_ferias_calculado.cod_evento         = ultimo_registro_evento_ferias.cod_evento
                             AND evento_ferias_calculado.desdobramento      = ultimo_registro_evento_ferias.desdobramento
                           WHERE registro_evento_ferias.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                             AND registro_evento_ferias.cod_contrato = contrato.cod_contrato

                           UNION
            
                         -- Verificando decimo
                          SELECT 1
                            FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                            JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento_decimo
                              ON ultimo_registro_evento_decimo.cod_registro  = registro_evento_decimo.cod_registro
                             AND ultimo_registro_evento_decimo.timestamp     = registro_evento_decimo.timestamp
                             AND ultimo_registro_evento_decimo.cod_evento    = registro_evento_decimo.cod_evento
                             AND ultimo_registro_evento_decimo.desdobramento = registro_evento_decimo.desdobramento
                            JOIN folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                              ON evento_decimo_calculado.cod_registro       = ultimo_registro_evento_decimo.cod_registro
                             AND evento_decimo_calculado.timestamp_registro = ultimo_registro_evento_decimo.timestamp
                             AND evento_decimo_calculado.cod_evento         = ultimo_registro_evento_decimo.cod_evento
                             AND evento_decimo_calculado.desdobramento      = ultimo_registro_evento_decimo.desdobramento
                           WHERE registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                             AND registro_evento_decimo.cod_contrato = contrato.cod_contrato

                           UNION
            
                         -- Verificando rescisao
                          SELECT 1
                            FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                            JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento_rescisao
                              ON ultimo_registro_evento_rescisao.cod_registro  = registro_evento_rescisao.cod_registro
                             AND ultimo_registro_evento_rescisao.timestamp     = registro_evento_rescisao.timestamp
                             AND ultimo_registro_evento_rescisao.cod_evento    = registro_evento_rescisao.cod_evento
                             AND ultimo_registro_evento_rescisao.desdobramento = registro_evento_rescisao.desdobramento
                            JOIN folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                              ON evento_rescisao_calculado.cod_registro       = ultimo_registro_evento_rescisao.cod_registro
                             AND evento_rescisao_calculado.timestamp_registro = ultimo_registro_evento_rescisao.timestamp
                             AND evento_rescisao_calculado.cod_evento         = ultimo_registro_evento_rescisao.cod_evento
                             AND evento_rescisao_calculado.desdobramento      = ultimo_registro_evento_rescisao.desdobramento
                           WHERE registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                             AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato
                    )
                    
                )
         
        GROUP BY sw_cgm_pessoa_fisica.cpf
               , contrato_servidor.cod_cargo
               , de_para_tipo_cargo.cod_tipo_cargo_tce
               , contrato.cod_contrato
               , contrato.registro
               , to_char(periodo_movimentacao.dt_final, ''mmyyyy'')
               , pensionista.cpf
               , contrato_servidor.cod_tipo_admissao 
               , periodo_movimentacao.cod_periodo_movimentacao
              -- , contrato_servidor_previdencia.cod_previdencia
               , de_para_tipo_regime_trabalho.cod_tipo_regime_trabalho_tce
               , de_para_orgao_unidade.num_orgao
               , de_para_orgao_unidade.num_unidade
               , servidor.cod_servidor
               , sw_cgm_pessoa_fisica.numcgm
        
        ORDER BY sw_cgm_pessoa_fisica.cpf
    );';
    
    EXECUTE stSql;

    stSql := ' SELECT tmp_historico_funcional.*
                    , tmp_data_movimentacao.data AS data_movimentacao
                    , CASE WHEN tmp_historico_funcional.situacao_funcional = ''A'' OR tmp_historico_funcional.situacao_funcional = ''R'' THEN 0
                           WHEN tmp_historico_funcional.situacao_funcional = ''P''                               THEN 1
                           WHEN tmp_historico_funcional.situacao_funcional = ''E''                               THEN 2
                      END AS cod_situacao_funcional
                 FROM tmp_historico_funcional
           INNER JOIN tmp_data_movimentacao
                   ON tmp_data_movimentacao.cod_contrato = tmp_historico_funcional.cod_contrato
            LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor_historico_funcional
                   ON contrato_servidor_historico_funcional.cod_contrato = tmp_historico_funcional.cod_contrato
                  AND tmp_historico_funcional.ato_movimentacao = contrato_servidor_historico_funcional.ato_movimentacao
                  AND tmp_historico_funcional.ano_mes_referencia = contrato_servidor_historico_funcional.periodo_movimentacao
    ';

    --cod_contrato,mes_ano,ato_movimentacao,data_movimentacao
    --dados (ato_movimentacao) que podem repetir (10,11,12,13,14,17,18,99)
    --dados (ato_movimentacao) que nao podem repetir (1,2,3,4,5,6,7,8,9,15,16)

    IF ( stPeriodoMovimentacao != '012009' ) THEN --as demais deve mostrar historico completo
        stSql := stSql ||'
                 WHERE ( SELECT tmp.data
                          FROM tmp_data_movimentacao AS tmp
                         WHERE tmp.cod_contrato = tmp_historico_funcional.cod_contrato
                       ) IS NOT NULL
                   AND tmp_historico_funcional.cod_cargo <> 0
                   AND CASE WHEN contrato_servidor_historico_funcional.cod_contrato IS NOT NULL THEN
                            TRUE --esta provavelmente gerando novamente o mesmo arquivo, deixa sair os dados!
                       ELSE
                            CASE WHEN --eh um dado que pode repetir e a data foi alterada!
                                ( SELECT DISTINCT tmp.cod_contrato
                                    FROM pessoal'|| stEntidade ||'.contrato_servidor_historico_funcional AS tmp
                                   WHERE tmp.cod_contrato = tmp_historico_funcional.cod_contrato
                                     AND tmp.ato_movimentacao in (10,11,12,13,14,17,18,99)
                                     AND to_char( tmp.data_apresentada, ''ddmmyyyy'') != tmp_data_movimentacao.data
                                     AND ( SELECT DISTINCT tmp2.cod_contrato
                                             FROM pessoal'|| stEntidade ||'.contrato_servidor_historico_funcional AS tmp2
                                            WHERE tmp2.cod_contrato = tmp.cod_contrato
                                              AND tmp2.ato_movimentacao in (10,11,12,13,14,17,18,99)
                                              AND to_char( tmp2.data_apresentada, ''ddmmyyyy'') = tmp_data_movimentacao.data
                                         ) IS NULL

                                ) IS NOT NULL THEN

                                 TRUE
                            ELSE
                                CASE WHEN --eh um dado que nao pode repetir!
                                    ( SELECT DISTINCT tmp.cod_contrato
                                    FROM pessoal'|| stEntidade ||'.contrato_servidor_historico_funcional AS tmp
                                   WHERE tmp.cod_contrato = tmp_historico_funcional.cod_contrato
                                     AND tmp.ato_movimentacao = tmp_historico_funcional.ato_movimentacao
                                    ) IS NOT NULL THEN
                                    FALSE
                                ELSE
                                    TRUE
                                END
                            END
                       END
                    -- adicionado para trazer apenas os historico do periodo em questao
                    AND substr(tmp_data_movimentacao.data, 3, 6) = '|| quote_literal(stPeriodoMovimentacao) ||'
        ';
    END IF;

    FOR reRegistro IN EXECUTE stSql LOOP
        stSql2 := '
             DELETE FROM pessoal'|| stEntidade ||'.contrato_servidor_historico_funcional 
                   WHERE cod_contrato = '|| reRegistro.cod_contrato ||'
                     AND periodo_movimentacao = '|| quote_literal(reRegistro.ano_mes_referencia) ||';
                  --   AND ato_movimentacao = '|| reRegistro.ato_movimentacao;

        EXECUTE stSql2;

        stTmp := reRegistro.data_movimentacao;
        stTmp := substring( stTmp from 5 for 4 ) ||'-'|| substring( stTmp from 3 for 2 ) ||'-'|| substring( stTmp from 1 for 2 );

        stSql2 := '
             INSERT INTO pessoal'|| stEntidade ||'.contrato_servidor_historico_funcional ( cod_contrato, periodo_movimentacao, ato_movimentacao, data_apresentada )
                  VALUES ( '|| reRegistro.cod_contrato ||', '|| quote_literal(reRegistro.ano_mes_referencia) ||', '|| reRegistro.ato_movimentacao ||', '|| quote_literal(stTmp) ||' );
        ';

        EXECUTE stSql2;
    
        RETURN NEXT reRegistro;
    END LOOP;

    --DROP TABLE tmp_ato_pessoal;
    --DROP TABLE tmp_acumulacao;
    --DROP TABLE tmp_data_movimentacao;
    --DROP TABLE tmp_historico_funcional;

END;
$$ LANGUAGE plpgsql;
