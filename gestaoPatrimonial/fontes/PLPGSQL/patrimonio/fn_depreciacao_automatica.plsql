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
CREATE TYPE depreciacao_automatica AS (
      cod_bem                           INTEGER
    , descricao                         VARCHAR
    , dt_incorporacao                   DATE
    , dt_aquisicao                      DATE
    , competencia_incorporacao          TEXT
    , vl_bem                            NUMERIC
    , quota_depreciacao_anual           NUMERIC
    , quota_depreciacao_anual_acelerada NUMERIC
    , depreciacao_acelerada             BOOLEAN
    , cod_plano                         INTEGER
    , cod_reavaliacao                   INTEGER
    , dt_reavaliacao                    DATE
    , vida_util                         INTEGER
    , motivo                            VARCHAR
    , exercicio_aquisicao               VARCHAR
    , mes_aquisicao                     INTEGER
);
*/

CREATE OR REPLACE FUNCTION patrimonio.fn_depreciacao_automatica(stExercicio   VARCHAR,
                                                                stCompetencia VARCHAR,
                                                                inCodNatureza INTEGER,
                                                                inCodGrupo    INTEGER,
                                                                inCodEspecie  INTEGER,
                                                                stMotivo      VARCHAR) RETURNS SETOF depreciacao_automatica
AS $$ DECLARE
    
    stQuery                   VARCHAR;
    stQueryDepreciacao        VARCHAR;
    stQueryDelete             VARCHAR;
    stQueryInsert             VARCHAR;
    arCompetencia             VARCHAR[];
    stSubstituirDepreciacao   VARCHAR;
    vlBem                     NUMERIC(14,2);
    vlQuota                   NUMERIC(14,2);
    vlQuotaPrimeira           NUMERIC(14,2);
    vlQuotaUsada              NUMERIC(14,2);
    vlPrimeiraDepreciacao     NUMERIC(14,2);
    vlDepreciacao             NUMERIC(14,2);
    vlDepreciacaoTmp          NUMERIC(14,2);
    vlDepreciacaoAnual        NUMERIC(14,2);
    vlValorMinimoDepreciacao  NUMERIC(14,2);
    inCompetencia             INTEGER;
    inCodDepreciacao          INTEGER;
    inCompetenciaDepreciacao  INTEGER;
    inCodBemDepreciacao       INTEGER;
    inUltimoDiaMes            INTEGER;
    inCount                   INTEGER;
    inCodContaAnalitica       INTEGER;
    timestampDepreciacao      TIMESTAMP;
    boPlanoAtivoExercicio     BOOLEAN;
        
    rcBens                    RECORD;
    rcDepreciacao             RECORD;
    rcDepreciacaoAcumulada    RECORD;
    rcConfiguracao            RECORD;
    
BEGIN

-- flag que verifica se existe algum bem depreciavel com cod_plano, no bem ou no grupo, configurado para o exercicio.
boPlanoAtivoExercicio := false;

stQuery := 'SELECT parametro
                 , valor
              FROM administracao.configuracao
             WHERE parametro IN (''valor_minimo_depreciacao'',''competencia_depreciacao'',''substituir_depreciacao'')
               AND exercicio  = '|| quote_literal(stExercicio) ||'
               AND cod_modulo = 6';
               
EXECUTE stQuery INTO rcConfiguracao;

FOR rcConfiguracao IN EXECUTE stQuery LOOP
    IF rcConfiguracao.parametro = 'valor_minimo_depreciacao' THEN
        vlValorMinimoDepreciacao := rcConfiguracao.valor::NUMERIC;
    ELSEIF rcConfiguracao.parametro = 'competencia_depreciacao' THEN
        inCompetenciaDepreciacao := rcConfiguracao.valor::INTEGER;
    ELSEIF rcConfiguracao.parametro = 'substituir_depreciacao' THEN
        stSubstituirDepreciacao := rcConfiguracao.valor;
    END IF;
END LOOP;

arCompetencia := STRING_TO_ARRAY(stCompetencia,',');

-- RECUPERA OS BENS QUE POSSUEM PLANO RELACIONADO
stQuery := '   SELECT  bem.cod_bem
                    ,  bem.descricao
                    ,  COALESCE(reavaliacao.dt_reavaliacao,bem.dt_incorporacao,bem.dt_aquisicao) AS dt_incorporacao
                    ,  bem.dt_aquisicao
                    ,  TO_CHAR(COALESCE(reavaliacao.dt_reavaliacao,bem.dt_incorporacao,bem.dt_aquisicao),''YYYYMM'' ) AS competencia_incorporacao
                    ,  COALESCE(reavaliacao.vl_reavaliacao, bem.vl_bem ) AS vl_bem
                    ,  CASE WHEN bem_plano_depreciacao.cod_plano IS NOT NULL
                            THEN bem.quota_depreciacao_anual
                            ELSE grupo.depreciacao
                       END AS quota_depreciacao_anual
                    ,  bem.quota_depreciacao_anual_acelerada
                    ,  bem.depreciacao_acelerada
                    ,  CASE WHEN bem_plano_depreciacao.cod_plano IS NOT NULL
                            THEN bem_plano_depreciacao.cod_plano
                            ELSE grupo_plano_depreciacao.cod_plano
                       END AS cod_plano
                    ,  reavaliacao.cod_reavaliacao
                    ,  reavaliacao.dt_reavaliacao
                    ,  reavaliacao.vida_util
                    ,  reavaliacao.motivo
                    ,  to_char(bem.dt_aquisicao, ''YYYY'')::VARCHAR AS exercicio_aquisicao
                    ,  to_char(bem.dt_aquisicao, ''MM'')::INTEGER AS mes_aquisicao
                    
                 FROM patrimonio.bem

           INNER JOIN  patrimonio.especie
                   ON  especie.cod_natureza = bem.cod_natureza
                  AND  especie.cod_grupo    = bem.cod_grupo
                  AND  especie.cod_especie  = bem.cod_especie
                  -- Não pode depreciar bens adquiridos após a competencia da depreciação
                  AND to_char(bem.dt_aquisicao, ''YYYYMM'')::INTEGER <= '|| stExercicio || stCompetencia ||'

           INNER JOIN  patrimonio.grupo
                   ON  grupo.cod_natureza = especie.cod_natureza
                  AND  grupo.cod_grupo    = especie.cod_grupo

           INNER JOIN  patrimonio.natureza
                   ON  natureza.cod_natureza = grupo.cod_natureza

            LEFT JOIN  patrimonio.grupo_plano_depreciacao
                   ON  grupo_plano_depreciacao.cod_grupo    = grupo.cod_grupo
                  AND  grupo_plano_depreciacao.cod_natureza = grupo.cod_natureza
                  AND  grupo_plano_depreciacao.exercicio = '|| quote_literal(stExercicio) ||'
                  
            LEFT JOIN  (  SELECT cod_bem
                               , MAX(timestamp) AS timestamp
                               , exercicio
                            FROM patrimonio.bem_plano_depreciacao
                           WHERE bem_plano_depreciacao.exercicio   = '|| quote_literal(stExercicio) ||'
                        GROUP BY cod_bem, exercicio
                       ) AS ultimo_plano_bem_depreciacao
                   ON  bem.cod_bem = ultimo_plano_bem_depreciacao.cod_bem

            LEFT JOIN  patrimonio.bem_plano_depreciacao
                   ON  ultimo_plano_bem_depreciacao.cod_bem   = bem_plano_depreciacao.cod_bem
                  AND  ultimo_plano_bem_depreciacao.timestamp = bem_plano_depreciacao.timestamp
                  AND  ultimo_plano_bem_depreciacao.exercicio = bem_plano_depreciacao.exercicio
                  AND  bem_plano_depreciacao.exercicio   = '|| quote_literal(stExercicio) ||'

            LEFT JOIN  (   SELECT MAX(cod_reavaliacao) AS cod_reavaliacao
                                , cod_bem
                             FROM patrimonio.reavaliacao
                         GROUP BY cod_bem
                       ) AS ultima_reavaliacao
                   ON  bem.cod_bem = ultima_reavaliacao.cod_bem

            LEFT JOIN  patrimonio.reavaliacao
                   ON  ultima_reavaliacao.cod_reavaliacao = reavaliacao.cod_reavaliacao
                  AND  ultima_reavaliacao.cod_bem         = reavaliacao.cod_bem

                WHERE  bem.depreciavel = true
                  AND  NOT EXISTS ( SELECT 1
                                      FROM patrimonio.bem_baixado
                                     WHERE bem_baixado.cod_bem = bem.cod_bem ) ';
                                     
    IF inCodNatureza IS NOT NULL THEN
        stQuery := stQuery||' AND bem.cod_natureza = '||inCodNatureza;
    END IF;

    IF inCodGrupo IS NOT NULL THEN
        stQuery := stQuery||' AND bem.cod_grupo = '||inCodGrupo;
    END IF;
    
    IF inCodEspecie IS NOT NULL THEN
        stQuery := stQuery||' AND bem.cod_especie = '||inCodEspecie;
    END IF;
    
    IF vlValorMinimoDepreciacao IS NOT NULL THEN
        stQuery := stQuery||' AND bem.vl_bem >= '||vlValorMinimoDepreciacao;
    END IF;
        
FOR rcBens IN EXECUTE stQuery LOOP
    
    boPlanoAtivoExercicio := true;
       
    -- Caso o bem esteja marcado como depreciavel, mas não possua um cod_plano configurado alerta o usuario que deve configurar uma especifica para o bem ou para o grupo.
    IF (rcBens.cod_plano IS NULL) THEN
        RAISE EXCEPTION 'Necessário configurar uma Conta Contábil de Depreciação Acumulada para o grupo ou no próprio bem: % - %', rcBens.cod_bem, rcBens.descricao;
    END IF;
    
    -- Caso não tenha sido configurada um valor de quota de depreciação, alerta ao usuário para configurar
    IF (rcBens.quota_depreciacao_anual = 0.00) THEN
        RAISE EXCEPTION 'Necessário configurar valor de quota depreciação para o grupo ou no próprio bem: % - %', rcBens.cod_bem, rcBens.descricao;
    END IF;
    
    -- Verifica se o cod_plano do bem, está cadastrado na tabela conta_analitica. Pois após o lançamento de depreciação não é possível alterar a conta contábil.
    -- Caso alterar o bem para nenhuma conta contábil, desmarcar a opção de depreciavel.
    SELECT INTO
           inCodContaAnalitica
           cod_conta
      FROM contabilidade.plano_analitica
     WHERE cod_plano = rcBens.cod_plano
       AND exercicio = stExercicio;
       
    IF inCodContaAnalitica IS NULL THEN
       RAISE EXCEPTION 'Conta Contábil de Depreciação Acumulada % do bem % não é analítica ou não está cadastrada no plano de contas.',rcBens.cod_plano, rcBens.cod_bem;
    END IF;

    -- Calcula o valor da quota, para a primeira inserção e dos proximos meses
    -- Caso o bem tenha depreciação acelerada, soma junto ao calculo de depreciação.
    IF (rcBens.depreciacao_acelerada IS TRUE) THEN
        vlQuota := TRUNC(((rcBens.quota_depreciacao_anual+rcBens.quota_depreciacao_anual_acelerada)/12),2);
        vlQuotaPrimeira := vlQuota + ((rcBens.quota_depreciacao_anual+rcBens.quota_depreciacao_anual_acelerada) - (vlQuota * 12));
    ELSE
        vlQuota := TRUNC((rcBens.quota_depreciacao_anual/12),2);
        vlQuotaPrimeira := vlQuota + (rcBens.quota_depreciacao_anual - (vlQuota * 12));
    END IF;

    stQueryDepreciacao :=  'SELECT    vl_bem
                                    , vl_atualizado
                                    , vl_acumulado
                                    , min_competencia
                                    , max_competencia
                                    FROM patrimonio.fn_depreciacao_acumulada('||rcBens.cod_bem||')
                                    as retorno (
                                                cod_bem             INTEGER
                                                ,vl_acumulado       NUMERIC
                                                ,vl_atualizado      NUMERIC
                                                ,vl_bem             NUMERIC
                                                ,min_competencia    VARCHAR
                                                ,max_competencia    VARCHAR
                                               )';

    EXECUTE stQueryDepreciacao INTO rcDepreciacaoAcumulada;
    
    -- Caso o bem possua alguma valor de depreciação acumulada, pega o valor atualizado, e calcula o valor da depreciação do bem para a competencia
    IF rcDepreciacaoAcumulada.vl_acumulado > '0.00' THEN
        vlPrimeiraDepreciacao := TRUNC(((rcDepreciacaoAcumulada.vl_atualizado * vlQuotaPrimeira) / 100),2);
        vlDepreciacaoTmp      := TRUNC(((rcDepreciacaoAcumulada.vl_atualizado * vlQuota) / 100),2);
    ELSE  
    --Calula o valor da primeira depreciação do bem
        vlPrimeiraDepreciacao := TRUNC(((rcDepreciacaoAcumulada.vl_bem * vlQuotaPrimeira) / 100),2);
        vlDepreciacaoTmp      := TRUNC(((rcDepreciacaoAcumulada.vl_bem * vlQuota) / 100),2);
    END IF;

    FOR inCount IN 1..ARRAY_UPPER(arCompetencia,1) LOOP
       
       -- SÓ DEPRECIA A PARTIR DA DATA DE INCORPORACAO OU DA DATA DE REAVALIACAO
       -- Caso o bem possua reavaliação, somente será depreciado a partir da data de reavaliação.
        IF rcBens.competencia_incorporacao::INT > (stExercicio||arCompetencia[inCount])::INT THEN
           CONTINUE;
        END IF;
                
        -- Pega o ultimo cod_depreciacao do bem, caso exista na patrimonio depreciacao e soma mais um
        SELECT COALESCE(MAX(cod_depreciacao),0) + 1
          INTO inCodDepreciacao
          FROM patrimonio.depreciacao
         WHERE cod_bem   = rcBens.cod_bem;
         
        inCompetencia := arCompetencia[inCount];

        -- Valida se é a primeira depreciação para aplicar
        IF inCount = 1 AND (rcDepreciacaoAcumulada.vl_acumulado = '0.00' OR rcDepreciacaoAcumulada.min_competencia IS NULL) THEN
            vlDepreciacao := vlPrimeiraDepreciacao;
            vlQuotaUsada  := vlQuotaPrimeira;
        ELSEIF rcDepreciacaoAcumulada.vl_acumulado > '0.00' AND rcDepreciacaoAcumulada.min_competencia = stExercicio||arCompetencia[inCount] THEN
            vlDepreciacao := vlPrimeiraDepreciacao;
            vlQuotaUsada  := vlQuotaPrimeira;
        ELSE
            vlDepreciacao := vlDepreciacaoTmp;
            vlQuotaUsada  := vlQuota;
        END IF;
               
        -- Se valor da depreciação for maior que o valor do bem, os dois valores são igualados
        IF vlBem < vlDepreciacao THEN
           vlDepreciacao := vlBem;
        END IF;

        -- Verifica se a configuração determina que as depreciações calculadas na competencia informada não sejam substituidos
        SELECT * INTO rcDepreciacao
          FROM patrimonio.depreciacao
         WHERE cod_bem     = rcBens.cod_bem
           AND competencia = stExercicio||LPAD(inCompetencia::VARCHAR,2,'0');
        
        vlBem := vlBem - vlDepreciacao;
        timestampDepreciacao := ('now'::text)::timestamp(3);
        
        INSERT INTO patrimonio.depreciacao
                (cod_depreciacao, cod_bem, timestamp, vl_depreciado, dt_depreciacao, competencia, motivo, acelerada, quota_utilizada) 
            VALUES
                (inCodDepreciacao, rcBens.cod_bem, timestampDepreciacao, vlDepreciacao, NOW(), stExercicio||LPAD(inCompetencia::VARCHAR,2,'0'), stMotivo, rcBens.depreciacao_acelerada, vlQuotaUsada);
        
        IF rcBens.cod_reavaliacao IS NOT NULL THEN
            INSERT INTO patrimonio.depreciacao_reavaliacao
                        (cod_depreciacao, cod_reavaliacao, cod_bem, timestamp)
                 VALUES
                        (inCodDepreciacao, rcBens.cod_reavaliacao, rcBens.cod_bem, timestampDepreciacao);
        END IF;

        EXIT WHEN vlBem = 0;
        
    END LOOP;
    
    RETURN next rcBens;
    
END LOOP;

IF boPlanoAtivoExercicio = false THEN
    RAISE EXCEPTION 'Necessário configurar uma Conta Contábil de Depreciação Acumulada para o grupo ou nos bens depreciavéis do exercicío - %', stExercicio;
END IF;

RETURN;
END;
$$ LANGUAGE 'plpgsql';