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
DROP TYPE colunasCaged CASCADE;
CREATE TYPE colunasCaged AS (
    sequencia                       INTEGER,
    servidor_pis_pasep              VARCHAR,
    sexo                            INTEGER,
    dt_nascimento                   VARCHAR(8),
    cod_escolaridade                INTEGER,
    filler                          VARCHAR,
    salario                         INTEGER,
    horas_semanais                  INTEGER,
    dt_admissao                     VARCHAR(8),
    tipo_movimento                  INTEGER,    
    dia_rescisao                    VARCHAR(2),
    nom_cgm                         VARCHAR(200),
    numero                          VARCHAR,
    serie                           VARCHAR,
    cod_rais                        INTEGER,
    portador_deficiencia            INTEGER,
    cbo                             VARCHAR,
    aprendiz                        INTEGER,
    sigla_uf                        VARCHAR(2),                                                               
    num_deficiencia                 INTEGER
);

CREATE OR REPLACE FUNCTION caged(VARCHAR,INTEGER,VARCHAR,VARCHAR,VARCHAR,INTEGER,INTEGER) RETURNS SETOF colunasCaged AS $$
DECLARE    
    stEntidade          ALIAS FOR $1;
    inSequenciaParam    ALIAS FOR $2;
    stCompetencia       ALIAS FOR $3;    
    stTipoFiltro        ALIAS FOR $4;
    stCodigos           ALIAS FOR $5;
    inCodAtributo       ALIAS FOR $6;
    boArray             ALIAS FOR $7;
    stSql               VARCHAR;
    reRegistro          RECORD;
    nuRemuneracao       NUMERIC;
    nuRemuneracaoAnterior NUMERIC;
    inSequencia         INTEGER:=inSequenciaParam;
    inContador          INTEGER;
    rwCaged             colunasCaged%ROWTYPE;
BEGIN
    stSql := '
SELECT translate(sw_cgm_pessoa_fisica.servidor_pis_pasep::varchar,''.-'','''') as servidor_pis_pasep
     , CASE WHEN sw_cgm_pessoa_fisica.sexo = ''f'' THEN 2
       ELSE 1 END AS sexo
     , to_char(sw_cgm_pessoa_fisica.dt_nascimento,''ddmmyyyy'') as dt_nascimento
     , sw_cgm_pessoa_fisica.cod_escolaridade     
     , translate(contrato_servidor_salario.salario::varchar,''.'','''') as salario     
     , contrato_servidor_salario.horas_semanais::integer as horas_semanais     
     
     , contrato_servidor_nomeacao_posse.dt_admissao
     , (SELECT num_caged FROM pessoal'||stEntidade||'.caged WHERE cod_caged = tipo_admissao_caged.cod_caged) as num_caged_admissao
     , contrato_servidor_caso_causa.cod_caso_causa
     , contrato_servidor_caso_causa.num_caged as num_caged_desligamento    
     , contrato_servidor_caso_causa.dt_rescisao
     
     , upper(sw_cgm.nom_cgm) as nom_cgm
     , ctps.numero     
     , translate(ctps.serie,''-'','''') as serie     
     , (SELECT cod_rais FROM cse.raca WHERE cod_raca = servidor.cod_raca) as cod_rais     
     , servidor_cid.cod_cid          
     , (SELECT codigo FROM pessoal'||stEntidade||'.cbo WHERE cod_cbo = cbo_cargo.cod_cbo) as cbo
     , upper(ctps.sigla_uf) as sigla_uf
     , (SELECT num_deficiencia FROM pessoal'||stEntidade||'.tipo_deficiencia WHERE cod_tipo_deficiencia = servidor_cid.cod_tipo_deficiencia) as num_deficiencia
     , contrato_servidor.cod_contrato
     , contrato_servidor.cod_sub_divisao
  FROM pessoal'||stEntidade||'.servidor  
LEFT JOIN (SELECT servidor_cid.*
                , cid.cod_tipo_deficiencia
             FROM pessoal'||stEntidade||'.servidor_cid
                , (  SELECT cod_servidor
                          , max(timestamp) as timestamp
                       FROM pessoal'||stEntidade||'.servidor_cid
                   GROUP BY cod_servidor) as max_servidor_cid
                , pessoal'||stEntidade||'.cid
            WHERE servidor_cid.cod_servidor = max_servidor_cid.cod_servidor
              AND servidor_cid.timestamp = max_servidor_cid.timestamp
              AND servidor_cid.cod_cid = cid.cod_cid) as servidor_cid
       ON servidor.cod_servidor = servidor_cid.cod_servidor    
LEFT JOIN (SELECT servidor_ctps.*
                , ctps.numero
                , ctps.serie
                , (SELECT sigla_uf FROM sw_uf WHERE cod_uf = ctps.uf_expedicao) as sigla_uf
             FROM pessoal'||stEntidade||'.servidor_ctps  
                , pessoal'||stEntidade||'.ctps
                , (  SELECT cod_ctps
                          , max(dt_emissao) as dt_emissao
                       FROM pessoal'||stEntidade||'.ctps
                   GROUP BY cod_ctps) as max_ctps
            WHERE servidor_ctps.cod_ctps = ctps.cod_ctps
              AND ctps.cod_ctps = max_ctps.cod_ctps
              AND ctps.dt_emissao = max_ctps.dt_emissao) AS ctps
       ON servidor.cod_servidor = ctps.cod_servidor  
     , pessoal'||stEntidade||'.servidor_contrato_servidor 
     , pessoal'||stEntidade||'.contrato_servidor';
    IF stTipoFiltro = 'atributo_servidor' THEN
        stSql := stSql || ' LEFT JOIN (SELECT atributo_contrato_servidor_valor.*
                 FROM pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                    , (  SELECT cod_contrato
                              , max(timestamp) as timestamp
                           FROM pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                       GROUP BY cod_contrato) as max_atributo_contrato_servidor_valor
                WHERE atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato
                  AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp) as atributo_contrato_servidor_valor
           ON atributo_contrato_servidor_valor.cod_contrato = contrato_servidor.cod_contrato';
    END IF;                 
    stSql := stSql || '          
LEFT JOIN pessoal'||stEntidade||'.tipo_admissao_caged
       ON contrato_servidor.cod_tipo_admissao = tipo_admissao_caged.cod_tipo_admissao     
LEFT JOIN (SELECT cbo_cargo.*
             FROM pessoal'||stEntidade||'.cbo_cargo
                , (  SELECT cod_cargo
                          , max(timestamp) as timestamp
                       FROM pessoal'||stEntidade||'.cbo_cargo
                   GROUP BY cod_cargo) as max_cbo_cargo
            WHERE cbo_cargo.cod_cargo = max_cbo_cargo.cod_cargo
              AND cbo_cargo.timestamp = max_cbo_cargo.timestamp) AS cbo_cargo     
       ON contrato_servidor.cod_cargo = cbo_cargo.cod_cargo       
LEFT JOIN (SELECT contrato_servidor_local.*
             FROM pessoal'||stEntidade||'.contrato_servidor_local
                , (  SELECT cod_contrato
                          , max(timestamp) as timestamp
                       FROM pessoal'||stEntidade||'.contrato_servidor_local
                   GROUP BY cod_contrato) as max_contrato_servidor_local
            WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
              AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local     
       ON contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato     
LEFT JOIN (SELECT contrato_servidor_caso_causa.cod_contrato
                , contrato_servidor_caso_causa.cod_caso_causa
                , contrato_servidor_caso_causa.dt_rescisao
                , (SELECT num_caged FROM pessoal'||stEntidade||'.caged WHERE cod_caged = causa_rescisao_caged.cod_caged) as num_caged
             FROM pessoal'||stEntidade||'.contrato_servidor_caso_causa
                , pessoal'||stEntidade||'.caso_causa
                , pessoal'||stEntidade||'.causa_rescisao
        LEFT JOIN pessoal'||stEntidade||'.causa_rescisao_caged
               ON causa_rescisao.cod_causa_rescisao = causa_rescisao_caged.cod_causa_rescisao
            WHERE contrato_servidor_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa
              AND caso_causa.cod_causa_rescisao = causa_rescisao.cod_causa_rescisao) as contrato_servidor_caso_causa
       ON contrato_servidor.cod_contrato = contrato_servidor_caso_causa.cod_contrato     
     , pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
     , (  SELECT cod_contrato
               , max(timestamp) as timestamp
            FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
        GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse        
     , pessoal'||stEntidade||'.contrato_servidor_salario
     , (  SELECT cod_contrato
               , max(timestamp) as timestamp
            FROM pessoal'||stEntidade||'.contrato_servidor_salario
        GROUP BY cod_contrato) as max_contrato_servidor_salario                
     , pessoal'||stEntidade||'.contrato_servidor_orgao
     , (  SELECT cod_contrato
               , max(timestamp) as timestamp
            FROM pessoal'||stEntidade||'.contrato_servidor_orgao
        GROUP BY cod_contrato) as max_contrato_servidor_orgao                        
     , sw_cgm
     , sw_cgm_pessoa_fisica
 WHERE servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
   AND servidor.numcgm = sw_cgm.numcgm   
   AND sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
   AND servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
   AND servidor_contrato_servidor.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato
   AND contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
   AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp   
   AND servidor_contrato_servidor.cod_contrato = contrato_servidor_salario.cod_contrato
   AND contrato_servidor_salario.cod_contrato = max_contrato_servidor_salario.cod_contrato
   AND contrato_servidor_salario.timestamp = max_contrato_servidor_salario.timestamp      
   AND servidor_contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato
   AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
   AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp            
   AND contrato_servidor.cod_regime = 1
   AND (to_char(contrato_servidor_caso_causa.dt_rescisao,''yyyy-mm'') = '''||stCompetencia||'''
    OR to_char(contrato_servidor_nomeacao_posse.dt_admissao,''yyyy-mm'') = '''||stCompetencia||''')';
    IF stTipoFiltro = 'contrato_todos' OR stTipoFiltro = 'cgm_contrato_todos' THEN
        stSql := stSql || ' AND contrato_servidor.cod_contrato IN ('||stCodigos||')';
    END IF;
    IF stTipoFiltro = 'lotacao' THEN
        stSql := stSql || ' AND contrato_servidor_orgao.cod_orgao IN ('||stCodigos||')';
    END IF;    
    IF stTipoFiltro = 'local' THEN
        stSql := stSql || ' AND contrato_servidor_local.cod_local IN ('||stCodigos||')';
    END IF;        
    IF stTipoFiltro = 'atributo_servidor' THEN
        stSql := stSql || ' AND atributo_contrato_servidor_valor.cod_atributo = '||inCodAtributo;
        IF boArray = 1 THEN
            stSql := stSql || ' AND atributo_contrato_servidor_valor.valor IN ('||stCodigos||')';        
        ELSE
            stSql := stSql || ' AND atributo_contrato_servidor_valor.valor = '''||stCodigos||'''';        
        END IF;
    END IF;  
    stSql := stSql || ' ORDER by nom_cgm';       
  
    FOR reRegistro IN EXECUTE stSql LOOP

        stSql := 'SELECT translate(sum(evento_calculado.valor)::text,''.'','''') as valor
                    FROM folhapagamento'||stEntidade||'.evento_calculado
                       , folhapagamento'||stEntidade||'.registro_evento_periodo
                       , folhapagamento'||stEntidade||'.evento
                   WHERE evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                     AND evento_calculado.cod_evento = evento.cod_evento
                     AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                     AND registro_evento_periodo.cod_periodo_movimentacao = (SELECT cod_periodo_movimentacao
                                                                               FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                                                              WHERE to_char(dt_final,''yyyy-mm'') = '''||stCompetencia||''')
                     AND EXISTS (SELECT 1
                                   FROM ima'||stEntidade||'.caged_evento 
                                  WHERE caged_evento.cod_evento = evento_calculado.cod_evento)';    
                                  
        nuRemuneracao := selectIntoNumeric(stSql);                 
        IF to_char(reRegistro.dt_admissao,'yyyy-mm') = stCompetencia THEN            
            stSql := 'SELECT COUNT(1)
                        FROM ima'||stEntidade||'.caged_sub_divisao
                       WHERE cod_sub_divisao = '||reRegistro.cod_sub_divisao;
            inContador := selectIntoInteger(stSql);
            IF reRegistro.num_caged_admissao IS NOT NULL OR inContador >= 1 THEN
                rwCaged.sequencia               := inSequencia;
                rwCaged.servidor_pis_pasep      := reRegistro.servidor_pis_pasep;
                rwCaged.sexo                    := reRegistro.sexo;
                rwCaged.dt_nascimento           := reRegistro.dt_nascimento;
                rwCaged.cod_escolaridade        := reRegistro.cod_escolaridade;     
                rwCaged.salario                 := nuRemuneracao;     
                rwCaged.horas_semanais          := reRegistro.horas_semanais;         
                
                rwCaged.dt_admissao             := to_char(reRegistro.dt_admissao,'ddmmyyyy'); 
                rwCaged.tipo_movimento          := reRegistro.num_caged_admissao;  
                rwCaged.dia_rescisao            := '';
                
                rwCaged.nom_cgm                 := reRegistro.nom_cgm;
                rwCaged.numero                  := reRegistro.numero;     
                rwCaged.serie                   := reRegistro.serie;     
                rwCaged.cod_rais                := reRegistro.cod_rais;     
                IF reRegistro.cod_cid > 0 THEN
                    rwCaged.portador_deficiencia    := 1;
                ELSE
                    rwCaged.portador_deficiencia    := 2;
                END IF;
                rwCaged.cbo                     := reRegistro.cbo;
                rwCaged.aprendiz                := 2;
                rwCaged.sigla_uf                := reRegistro.sigla_uf;
                rwCaged.num_deficiencia         := reRegistro.num_deficiencia;
                inSequencia := inSequencia + 1;
                RETURN NEXT rwCaged;
            END IF;
        END IF;

        IF reRegistro.num_caged_desligamento IS NOT NULL THEN                              
            stSql := 'SELECT translate(sum(evento_calculado.valor)::text,''.'','''') as valor
                        FROM folhapagamento'||stEntidade||'.evento_calculado
                           , folhapagamento'||stEntidade||'.registro_evento_periodo
                           , folhapagamento'||stEntidade||'.evento
                       WHERE evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                         AND evento_calculado.cod_evento = evento.cod_evento
                         AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                         AND registro_evento_periodo.cod_periodo_movimentacao = (SELECT cod_periodo_movimentacao
                                                                                   FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                                                                  WHERE to_char(dt_final,''yyyy-mm'') = '''||to_char(to_date(stCompetencia,'yyyy-mm')::date-1,'yyyy-mm')||''')
                         AND EXISTS (SELECT 1
                                       FROM ima'||stEntidade||'.caged_evento 
                                      WHERE caged_evento.cod_evento = evento_calculado.cod_evento)';    

            nuRemuneracaoAnterior := selectIntoNumeric(stSql);
            
            IF nuRemuneracaoAnterior is NULL THEN
                stSql := 'SELECT translate(sum(evento_rescisao_calculado.valor)::text,''.'','''') as valor
                            FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado
                               , folhapagamento'||stEntidade||'.registro_evento_rescisao
                               , folhapagamento'||stEntidade||'.evento
                           WHERE evento_rescisao_calculado.cod_registro = registro_evento_rescisao.cod_registro
                             AND evento_rescisao_calculado.cod_evento = registro_evento_rescisao.cod_evento
                             AND evento_rescisao_calculado.desdobramento = registro_evento_rescisao.desdobramento
                             AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                             AND evento_rescisao_calculado.desdobramento = ''S''
                             AND evento_rescisao_calculado.cod_evento = evento.cod_evento
                             AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                             AND registro_evento_rescisao.cod_periodo_movimentacao = (SELECT cod_periodo_movimentacao
                                                                                        FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                                                                       WHERE to_char(dt_final,''yyyy-mm'') = '''||stCompetencia||''')
                             AND EXISTS (SELECT 1
                                           FROM ima'||stEntidade||'.caged_evento 
                                          WHERE caged_evento.cod_evento = evento_rescisao_calculado.cod_evento)';    

                nuRemuneracaoAnterior := selectIntoNumeric(stSql);            
            END IF;
            
            IF nuRemuneracaoAnterior is NULL THEN
                nuRemuneracaoAnterior := nuRemuneracao;  
            END IF;
            
      
            rwCaged.sequencia               := inSequencia;
            rwCaged.servidor_pis_pasep      := reRegistro.servidor_pis_pasep;
            rwCaged.sexo                    := reRegistro.sexo;
            rwCaged.dt_nascimento           := reRegistro.dt_nascimento;
            rwCaged.cod_escolaridade        := reRegistro.cod_escolaridade;     
            rwCaged.salario                 := nuRemuneracaoAnterior;     
            rwCaged.horas_semanais          := reRegistro.horas_semanais;         
            
            rwCaged.dt_admissao             := to_char(reRegistro.dt_admissao,'ddmmyyyy'); 
            rwCaged.tipo_movimento          := reRegistro.num_caged_desligamento;  
            rwCaged.dia_rescisao            := to_char(reRegistro.dt_rescisao,'dd');
            
            rwCaged.nom_cgm                 := reRegistro.nom_cgm;
            rwCaged.numero                  := reRegistro.numero;     
            rwCaged.serie                   := reRegistro.serie;     
            rwCaged.cod_rais                := reRegistro.cod_rais;     
            IF reRegistro.cod_cid > 0 THEN
                rwCaged.portador_deficiencia    := 1;
            ELSE
                rwCaged.portador_deficiencia    := 2;
            END IF;
            rwCaged.cbo                     := reRegistro.cbo;
            rwCaged.aprendiz                := 2;
            rwCaged.sigla_uf                := reRegistro.sigla_uf;
            rwCaged.num_deficiencia         := reRegistro.num_deficiencia;
            inSequencia := inSequencia + 1;
            RETURN NEXT rwCaged;        
        END IF; 
    END LOOP;
END
$$ LANGUAGE 'plpgsql';   

--  SELECT * FROM caged(''
--                                  ,3
--                                  ,'2008-01'
--                                  ,'contrato_todos'
--                                  ,'1344'
--                                  ,0
--                                  ,0); 

