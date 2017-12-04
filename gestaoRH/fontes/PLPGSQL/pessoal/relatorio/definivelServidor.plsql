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
/**
    * PL para relatório do devinível do servidor
    * Data de Criação: 20/03/2008


    * @author Rafael Garbin

    * Casos de uso: uc-04.04.48

    $Id: definivelServidor.sql 29220 2008-04-15 18:46:02Z souzadl $

*/
DROP TYPE colunasDefinivelServidor CASCADE;
CREATE TYPE colunasDefinivelServidor AS (
    cod_contrato                                INTEGER,
    nome                                        VARCHAR,
    endereco                                    VARCHAR,
    bairro                                      VARCHAR,
    cep                                         VARCHAR,
    municipio                                   VARCHAR,
    uf                                          VARCHAR,
    fone                                        VARCHAR,
    escolaridade                                VARCHAR,
    data_nascimento                             DATE,
    cpf                                         VARCHAR,
    rg                                          VARCHAR,
    processo                                    VARCHAR,
    data_inclusao_processo                      DATE,
    matricula                                   VARCHAR,
    grau_parentesco                             VARCHAR,
    ocupacao                                    VARCHAR,
    data_inicio_beneficio                       DATE,
    data_encerramento_beneficio                 DATE,
    motivo_encerramento                         VARCHAR,
    numero_beneficio                            VARCHAR,
    percentual_pagamento_pensao                 VARCHAR,
    tipo_dependencia                            VARCHAR,
    cid                                         VARCHAR,
    banco                                       VARCHAR,
    agencia                                     VARCHAR,
    conta                                       VARCHAR,
    lotacao                                     VARCHAR,
    previdencia                                 VARCHAR,
    matricula_gerador_beneficio                 VARCHAR,
    nome_gerador_beneficio                      VARCHAR,
    cgm_gerador_beneficio                       VARCHAR,
    pis_pasep                                   VARCHAR,
    titulo_de_eleitor                           VARCHAR,
    secao_do_titulo                             VARCHAR,
    zona_titulo                                 VARCHAR,
    ctps                                        VARCHAR,
    serie_ctps                                  VARCHAR,
    data_nomeacao                               DATE,
    data_posse                                  DATE,
    data_admissao                               DATE,
    data_rescisao                               DATE,
    causa_rescisao                              VARCHAR,
    regime_subdivisao_cargo                     VARCHAR,
    cargo_especialidade                         VARCHAR,
    regime_subdivisao_funcao                    VARCHAR,
    funcao                                      VARCHAR,
    categoria                                   VARCHAR,
    tipo_admissao                               VARCHAR,
    vinculo_empregaticio                        VARCHAR,
    classif_agentes_nocivos                     VARCHAR,
    horas_mensais                               VARCHAR,
    horas_semanais                              VARCHAR,
    padrao                                      VARCHAR,
    valor_padrao                                VARCHAR,
    salario                                     VARCHAR,
    forma_pagamento                             VARCHAR,
    local                                       VARCHAR,
    data_opcao_fgts                             DATE,
    agrupamento                                 VARCHAR,
    atributo1                                   VARCHAR,
    atributo2                                   VARCHAR,
    atributo3                                   VARCHAR,
    atributo4                                   VARCHAR,
    atributo5                                   VARCHAR,
    atributo6                                   VARCHAR,
    atributo7                                   VARCHAR,
    atributo8                                   VARCHAR,
    atributo9                                   VARCHAR,
    atributo10                                  VARCHAR,
    salario_bruto                               VARCHAR,
    salario_liquido                             VARCHAR,
    descontos_da_folha_salario                  VARCHAR,
    evento1_qte                                 NUMERIC,
    evento1_valor                               NUMERIC,
    evento2_qte                                 NUMERIC,
    evento2_valor                               NUMERIC,
    evento3_qte                                 NUMERIC,
    evento3_valor                               NUMERIC,
    evento4_qte                                 NUMERIC,
    evento4_valor                               NUMERIC 
);
CREATE OR REPLACE FUNCTION definivelServidor() RETURNS VARCHAR AS $$
DECLARE
    stTimeStamp   VARCHAR;
    stTimeStamp2  VARCHAR;
    stSql         VARCHAR;
BEGIN
    SELECT 'definivelServidor'|| replace(replace(replace(replace(current_timestamp::varchar,' '::varchar,''::varchar),':'::varchar,''::varchar),'-'::varchar,''::varchar),'.'::varchar,''::varchar)
      INTO stTimeStamp;
    stTimeStamp2 := stTimeStamp ||'_pk';
    stSql := 'CREATE TABLE '||  stTimeStamp || '(
                campo VARCHAR CONSTRAINT '|| stTimeStamp2 ||' PRIMARY KEY, 
                valor VARCHAR,
                parametrosPL BOOLEAN,
                tipo VARCHAR,
                ordem INTEGER
                )';
    EXECUTE stSql;
    RETURN stTimeStamp;
    
END;    
$$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION relatorioDefinivelServidor( VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE
    stNomeTabela ALIAS FOR $1;
    
    stConsultaTabelaFiltro VARCHAR := '';
    stConsultaRegistros VARCHAR := '';
    stFiltroRegistros VARCHAR := '';
    stFiltroPL VARCHAR := '';
    reRegistrosFiltro RECORD;
    reRegistros RECORD;
BEGIN

    stConsultaTabelaFiltro := '
            SELECT campo
                 , valor
                 , parametrosPL
                 , tipo
              FROM '|| stNomeTabela ||'
          ORDER BY ordem
    ';
   
    FOR reRegistrosFiltro IN EXECUTE stConsultaTabelaFiltro
    LOOP
        IF reRegistrosFiltro.parametrosPL = 'true' THEN
            IF reRegistrosFiltro.tipo = 'varchar' OR reRegistrosFiltro.tipo = 'boolean' THEN
                stFiltroPL := stFiltroPL || ', ' || quote_literal(reRegistrosFiltro.valor) ||' ' ;
            ELSE
                stFiltroPL := stFiltroPL || ',' ||reRegistrosFiltro.valor;
            END IF;
        ELSE
            stFiltroRegistros := stFiltroRegistros || ' AND ' || reRegistrosFiltro.valor || '';
        END IF;
    END LOOP;

    stConsultaRegistros := '
        SELECT *
          FROM definivelServidor('|| SUBSTR(stFiltroPL, 2) ||')
         WHERE 1 = 1
    '|| stFiltroRegistros;
    
    --EXECUTE 'DROP TABLE ' || stNomeTabela;
    
    FOR reRegistros IN EXECUTE stConsultaRegistros
    LOOP
        RETURN NEXT reRegistros;
    END LOOP;
    
END;
$$ language 'plpgsql';

CREATE OR REPLACE FUNCTION definivelServidor(VARCHAR,VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR,VARCHAR,INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR) RETURNS SETOF colunasDefinivelServidor AS $$
DECLARE
    stSituacao                 ALIAS FOR $1;
    stTipoFiltro               ALIAS FOR $2;
    stCodigos                  ALIAS FOR $3;
    inCodAtributo              ALIAS FOR $4;
    boArray                    ALIAS FOR $5;
    stEntidade                 ALIAS FOR $6;
    stCodAtributos             ALIAS FOR $7;
    inCodComplementar          ALIAS FOR $8;
    inCodPeriodoMovimentacao   ALIAS FOR $9;
    inCodConfiguracao          ALIAS FOR $10;
    stCodEventosQte            ALIAS FOR $11;
    stCodEventosValor          ALIAS FOR $12;
    stSQL                      VARCHAR;
    inTamanhoMascaraRegistro   VARCHAR;
    stExercicio                VARCHAR;
    stCodEvento                VARCHAR;
    arValoresAtributo          VARCHAR[];
    arCodAtributos             VARCHAR[];
    arCodEventosQte            VARCHAR[];
    arCodEventosValor          VARCHAR[];
    arCodEventosQtePosicao     INTEGER[];
    arCodEventosValorPosicao   INTEGER[];
    inIndex                    INTEGER;
    inCount                    INTEGER;
    inCodPosicaoEvento         INTEGER;
    nuEventoQte                NUMERIC[];
    nuEventoValor              NUMERIC[];
    nuDescontos                NUMERIC;
    nuProventos                NUMERIC;
    rwDefinivelServidor        colunasDefinivelServidor%ROWTYPE;
    reRegistro                 RECORD;
    dtInicioCompetencia        VARCHAR;
    dtFimCompetencia           VARCHAR;
BEGIN
    SELECT max(valor) as exercicio
      into stExercicio
      FROM administracao.configuracao 
     WHERE parametro = 'ano_exercicio';

    arCodEventosQte := string_to_array(stCodEventosQte, ',');
    arCodEventosValor := string_to_array(stCodEventosValor, ',');

    inCount := 1;
    FOR inIndex IN 1..4 LOOP
        IF (arCodEventosValor[inIndex] <> '0') THEN
            arCodEventosValorPosicao[inCount] := inIndex;
            inCount := inCount + 1;
        END IF;
    END LOOP;

    inCount := 1;
    FOR inIndex IN 1..4 LOOP
        IF (arCodEventosQte[inIndex] <> '0') THEN
            arCodEventosQtePosicao[inCount] := inIndex;
            inCount := inCount + 1;
        END IF;
    END LOOP;

    dtInicioCompetencia := selectIntoVarchar('SELECT dt_inicial
                                                FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                               WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'');

    dtFimCompetencia := selectIntoVarchar('SELECT dt_final
                                             FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                            WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'');


    stSQL := 'SELECT length(valor) as tamanho
                FROM administracao.configuracao 
               WHERE parametro = ''mascara_registro'' 
                 AND exercicio = (SELECT to_char(dt_final, ''yyyy'')
                                   FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                  WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||')';
    FOR reRegistro IN EXECUTE stSQL LOOP
        inTamanhoMascaraRegistro := reRegistro.tamanho;
    END LOOP;

    IF inTamanhoMascaraRegistro IS NULL THEN
        inTamanhoMascaraRegistro := 10;
    END IF;
    
    IF stSituacao = 'pensionistas' THEN       
        stSQL := 'SELECT contrato_pensionista.cod_contrato as cod_contrato     
                        , CAST(sw_cgm.nom_cgm AS VARCHAR) as nome
                        , CAST(sw_cgm.logradouro||'',''|| sw_cgm.numero||'' - ''|| sw_cgm.complemento AS VARCHAR) as endereco
                        , CAST(sw_cgm.bairro AS VARCHAR) AS bairro
                        , CAST(sw_cgm.cep AS VARCHAR) AS cep
                        , CAST((SELECT nom_municipio FROM sw_municipio WHERE sw_municipio.cod_municipio = sw_cgm.cod_municipio AND sw_municipio.cod_uf = sw_cgm.cod_uf) AS VARCHAR) as municipio   
                        , CAST((SELECT sigla_uf FROM sw_uf WHERE sw_uf.cod_uf = sw_cgm.cod_uf) AS VARCHAR) as uf   
                        , CAST(sw_cgm.fone_residencial AS VARCHAR) as fone   
                        , CAST((SELECT descricao FROM sw_escolaridade WHERE cod_escolaridade = sw_cgm_pessoa_fisica.cod_escolaridade) AS VARCHAR) as escolaridade   
                        , sw_cgm_pessoa_fisica.dt_nascimento as data_nascimento
                        , CAST(sw_cgm_pessoa_fisica.cpf AS VARCHAR) AS cpf
                        , CAST(sw_cgm_pessoa_fisica.rg AS VARCHAR) AS rg
                        , CAST(sw_processo.cod_processo AS VARCHAR) as processo 
                        , to_date(to_char( sw_processo.timestamp, ''dd/mm/yyyy'' ), ''dd/mm/yyyy'') as data_inclusao_processo 
                        , CAST((SELECT lpad(registro::varchar, '|| inTamanhoMascaraRegistro ||', ''0''::varchar) FROM pessoal'|| stEntidade ||'.contrato WHERE cod_contrato = contrato_pensionista.cod_contrato) AS VARCHAR) as matricula   
                        , CAST((SELECT cod_grau || '' - '' || nom_grau FROM cse.grau_parentesco WHERE pensionista.cod_grau = cod_grau) AS VARCHAR) as grau_parentesco   
                        , CAST((SELECT cod_profissao || '' - '' || nom_profissao FROM cse.profissao WHERE pensionista.cod_profissao = cod_profissao) AS VARCHAR) as ocupacao   
                        , contrato_pensionista.dt_inicio_beneficio as data_inicio_beneficio   
                        , contrato_pensionista.dt_encerramento as data_encerramento_beneficio   
                        , CAST(contrato_pensionista.motivo_encerramento AS VARCHAR) as motivo_encerramento   
                        , CAST(contrato_pensionista.num_beneficio AS VARCHAR) as numero_beneficio   
                        , CAST(contrato_pensionista.percentual_pagamento AS VARCHAR) as percentual_pagamento_pensao   
                        , CAST((SELECT cod_dependencia || '' - '' || descricao FROM pessoal'|| stEntidade ||'.tipo_dependencia WHERE contrato_pensionista.cod_dependencia = cod_dependencia) AS VARCHAR) as tipo_dependencia   
                        , CAST((SELECT sigla || '' - '' || descricao FROM pessoal'|| stEntidade ||'.cid WHERE cod_cid = pensionista_cid.cod_cid) AS VARCHAR) as cid
                        , CAST((SELECT num_banco FROM monetario.banco WHERE cod_banco = contrato_pensionista_conta_salario.cod_banco) ||''-''|| (SELECT nom_banco FROM monetario.banco WHERE cod_banco = contrato_pensionista_conta_salario.cod_banco) AS VARCHAR) as banco
                        , CAST((SELECT num_agencia ||'' - '' || nom_agencia FROM monetario.agencia WHERE cod_agencia = contrato_pensionista_conta_salario.cod_agencia AND cod_banco = contrato_pensionista_conta_salario.cod_banco) AS VARCHAR) as agencia
                        , CAST(contrato_pensionista_conta_salario.nr_conta AS VARCHAR) as conta
                        , CAST(trim(recuperaDescricaoOrgao(contrato_pensionista_orgao.cod_orgao,('|| quote_literal(stExercicio ||'-01-01') ||')::date)) AS VARCHAR) as lotacao
                        , CAST(previdencia.descricao AS VARCHAR) as previdencia
                        , gerador_beneficio.*
                        , CAST('''' AS VARCHAR) as pis_pasep
                        , CAST('''' AS VARCHAR) as titulo_de_eleitor
                        , CAST('''' AS VARCHAR) as secao_do_titulo
                        , CAST('''' AS VARCHAR) as zona_titulo   
                        , CAST('''' AS VARCHAR) as ctps
                        , CAST('''' AS VARCHAR) as serie_ctps
                        , null as data_nomeacao
                        , null as data_posse
                        , null as data_admissao
                        , null as data_rescisao
                        , CAST('''' AS VARCHAR) as causa_rescisao
                        , CAST('''' AS VARCHAR) as regime_subdivisao_cargo
                        , CAST('''' AS VARCHAR) as cargo_especialidade 
                        , CAST('''' AS VARCHAR) as regime_subdivisao_funcao
                        , CAST('''' AS VARCHAR) as funcao
                        , CAST('''' AS VARCHAR) as categoria
                        , CAST('''' AS VARCHAR) as tipo_admissao
                        , CAST('''' AS VARCHAR) as vinculo_empregaticio 
                        , CAST('''' AS VARCHAR) as classif_agentes_nocivos
                        , CAST('''' AS VARCHAR) as horas_mensais
                        , CAST('''' AS VARCHAR) as horas_semanais
                        , CAST('''' AS VARCHAR) as padrao
                        , CAST('''' AS VARCHAR) as valor_padrao
                        , CAST(0    AS VARCHAR) as salario 
                        , CAST('''' AS VARCHAR) as forma_pagamento
                        , CAST('''' AS VARCHAR) as local
                        , null as data_opcao_fgts
                        , provento_evento_calculado.bruto                                         AS salario_bruto
                        , desconto_evento_calculado.descontos                                     AS descontos_da_folha_salario
                        , (provento_evento_calculado.bruto - desconto_evento_calculado.descontos) AS salario_liquido

                     FROM pessoal'|| stEntidade ||'.pensionista    
                        , pessoal'|| stEntidade ||'.contrato_pensionista';    

        IF stTipoFiltro = 'atributo_pensionista_grupo' THEN
            stSQL := stSQL || ' INNER JOIN (SELECT ultimo_atributo_contrato_pensionista.* 
                                        , atributo_dinamico.cod_tipo
                                        , atributo_dinamico.nom_atributo
                                     FROM ultimo_atributo_contrato_pensionista('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_atributo_contrato_pensionista
                                     JOIN administracao.atributo_dinamico 
                                       ON ultimo_atributo_contrato_pensionista.cod_modulo   = atributo_dinamico.cod_modulo 
                                      AND ultimo_atributo_contrato_pensionista.cod_cadastro = atributo_dinamico.cod_cadastro 
                                      AND ultimo_atributo_contrato_pensionista.cod_atributo = atributo_dinamico.cod_atributo ) AS atributo_contrato_pensionista
                                  ON atributo_contrato_pensionista.cod_contrato = contrato_pensionista.cod_contrato
                                 AND atributo_contrato_pensionista.cod_atributo = '|| inCodAtributo;
            
            IF boArray = 'true' THEN
	            stSQL := stSQL || ' AND atributo_contrato_pensionista.valor IN ('|| stCodigos ||') ';            
	        ELSE
	            stSQL := stSQL || ' AND atributo_contrato_pensionista.valor = '|| quote_literal(stCodigos) ||' ';
	        END IF;
        END IF;    
        
        stSQL := stSQL || ' LEFT JOIN (SELECT contrato_pensionista_previdencia.cod_contrato 
                                            , previdencia_previdencia.descricao 
                                        FROM ultimo_contrato_pensionista_previdencia('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as contrato_pensionista_previdencia
                                        JOIN ultimo_previdencia_previdencia('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as previdencia_previdencia
                                          ON contrato_pensionista_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia 
                                       WHERE previdencia_previdencia.tipo_previdencia = ''o'') as previdencia                                            
                                ON contrato_pensionista.cod_contrato = previdencia.cod_contrato

                            LEFT JOIN (SELECT CAST(contrato.registro AS VARCHAR) as matricula_gerador_beneficio
                                            , sw_cgm.nom_cgm as nome_gerador_beneficio
                                            , CAST(sw_cgm.numcgm AS VARCHAR) as cgm_gerador_beneficio
                                            , servidor_contrato_servidor.cod_contrato as cod_contrato_cedente
                                       FROM pessoal'|| stEntidade ||'.contrato
                                            , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                            , pessoal'|| stEntidade ||'.servidor
                                            , sw_cgm
                                      WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato
                                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                        AND servidor.numcgm = sw_cgm.numcgm ) AS gerador_beneficio
                                ON gerador_beneficio.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente
        
                            LEFT JOIN ( SELECT registro_evento_periodo.cod_contrato
                                            , sum(registro_evento.valor) as valor
                                        FROM folhapagamento'|| stEntidade ||'.registro_evento
                                        JOIN folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                            ON registro_evento_periodo.cod_registro = registro_evento.cod_registro
                                        JOIN ( SELECT cod_contrato
                                                    , cod_periodo_movimentacao as cod_periodo_movimentacao
                                                 FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                                WHERE registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                            ) as ultimo_periodo_contrato
                                          ON ultimo_periodo_contrato.cod_contrato = registro_evento_periodo.cod_contrato
                                        AND ultimo_periodo_contrato.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao
                                        GROUP BY registro_evento_periodo.cod_contrato) AS valor_pensao
                                    ON valor_pensao.cod_contrato = contrato_pensionista.cod_contrato
                            LEFT JOIN pessoal'|| stEntidade ||'.contrato_pensionista_processo 
                                ON contrato_pensionista.cod_contrato = contrato_pensionista_processo.cod_contrato     
        
                            LEFT JOIN pessoal'|| stEntidade ||'.pensionista_cid  
                                ON pensionista_cid.cod_pensionista = contrato_pensionista.cod_pensionista     
                                AND pensionista_cid.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente     
        
                            LEFT JOIN (SELECT sw_processo.*
                                       FROM sw_processo 
                                            , ( SELECT cod_processo 
                                                     , max(timestamp) as timestamp
                                                  FROM sw_processo
                                              GROUP BY cod_processo) as max_processo_pensionista
                                                 WHERE sw_processo.cod_processo = max_processo_pensionista.cod_processo 
                                        AND sw_processo.timestamp = max_processo_pensionista.timestamp) as sw_processo 
                                    ON contrato_pensionista_processo.cod_processo = sw_processo.cod_processo 
        
                            LEFT JOIN ( SELECT ultimo_contrato_pensionista_orgao.*
                                          FROM ultimo_contrato_pensionista_orgao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||')
                                      ) as contrato_pensionista_orgao 
                                     ON contrato_pensionista.cod_contrato = contrato_pensionista_orgao.cod_contrato 
        
                            LEFT JOIN ( SELECT ultimo_contrato_pensionista_conta_salario.*  
                                          FROM ultimo_contrato_pensionista_conta_salario('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||')
                                      ) as contrato_pensionista_conta_salario 
                                     ON contrato_pensionista.cod_contrato = contrato_pensionista_conta_salario.cod_contrato

                            LEFT JOIN (   SELECT COALESCE(sum(evento_calculado.valor),0) as bruto
                                               , registro_evento_periodo.cod_periodo_movimentacao
                                               , registro_evento_periodo.cod_contrato
                                            FROM folhapagamento'|| stEntidade ||'.evento
                                               , folhapagamento'|| stEntidade ||'.evento_calculado
                                               , folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                           WHERE evento.cod_evento = evento_calculado.cod_evento
                                             AND evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                                             AND evento.natureza = ''P''
                                             AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                        GROUP BY 2,3
                                      ) AS provento_evento_calculado
                                   ON provento_evento_calculado.cod_contrato             = contrato_pensionista.cod_contrato
                                  AND provento_evento_calculado.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'

                            LEFT JOIN (   SELECT COALESCE(sum(evento_calculado.valor),0) as descontos
                                               , registro_evento_periodo.cod_periodo_movimentacao
                                               , registro_evento_periodo.cod_contrato
                                            FROM folhapagamento'|| stEntidade ||'.evento
                                               , folhapagamento'|| stEntidade ||'.evento_calculado
                                               , folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                           WHERE evento.cod_evento = evento_calculado.cod_evento
                                             AND evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                                             AND evento.natureza = ''D''
                                             AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                        GROUP BY 2,3
                                      ) AS desconto_evento_calculado
                                   ON desconto_evento_calculado.cod_contrato             = contrato_pensionista.cod_contrato
                                  AND desconto_evento_calculado.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'

                           , sw_cgm 
                           , sw_cgm_pessoa_fisica 

                    WHERE pensionista.numcgm = sw_cgm.numcgm 
                        AND pensionista.numcgm = sw_cgm_pessoa_fisica.numcgm 
                        AND pensionista.cod_pensionista = contrato_pensionista.cod_pensionista 
                        AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente';
    ELSE
    	stSQL := 'SELECT contrato_servidor.cod_contrato as cod_contrato
                       , CAST(servidor.nr_titulo_eleitor AS VARCHAR) as titulo_de_eleitor 
                       , CAST(servidor.secao_titulo AS VARCHAR) as secao_do_titulo
                       , CAST(servidor.zona_titulo AS VARCHAR) as zona_titulo 
                       , CAST(sw_cgm.nom_cgm AS VARCHAR) as nome 
                       , CAST(sw_cgm.logradouro||'',''|| sw_cgm.numero||'' - ''|| sw_cgm.complemento AS VARCHAR) as endereco
                       , CAST(sw_cgm.bairro AS VARCHAR) AS bairro
                       , CAST(sw_cgm.cep AS VARCHAR) AS cep
                       , CAST((SELECT nom_municipio FROM sw_municipio WHERE sw_municipio.cod_municipio = sw_cgm.cod_municipio AND sw_municipio.cod_uf = sw_cgm.cod_uf) AS VARCHAR) as municipio 
                       , CAST((SELECT sigla_uf FROM sw_uf WHERE sw_uf.cod_uf = sw_cgm.cod_uf) AS VARCHAR) as uf 
                       , CAST(sw_cgm.fone_residencial AS VARCHAR) as fone 
                       , CAST((SELECT descricao FROM sw_escolaridade WHERE cod_escolaridade = sw_cgm_pessoa_fisica.cod_escolaridade) AS VARCHAR) as escolaridade 
                       , sw_cgm_pessoa_fisica.dt_nascimento as data_nascimento
                       , CAST(sw_cgm_pessoa_fisica.cpf AS VARCHAR) AS cpf
                       , CAST(sw_cgm_pessoa_fisica.rg AS VARCHAR) AS rg
                       , CAST(sw_cgm_pessoa_fisica.servidor_pis_pasep AS VARCHAR) as PIS_PASEP 
                       , CAST((SELECT lpad(registro::varchar, '|| inTamanhoMascaraRegistro ||', ''0''::varchar) FROM pessoal'|| stEntidade ||'.contrato WHERE cod_contrato = servidor_contrato_servidor.cod_contrato) AS VARCHAR) as matricula 
                       , contrato_servidor_nomeacao_posse.dt_posse as data_posse 
                       , contrato_servidor_nomeacao_posse.dt_nomeacao as data_nomeacao
                       , contrato_servidor_nomeacao_posse.dt_admissao as data_admissao 
                       , contrato_servidor_caso_causa.dt_rescisao as data_rescisao 
                       , CAST((SELECT descricao FROM pessoal'|| stEntidade ||'.causa_rescisao WHERE cod_causa_rescisao = (SELECT cod_causa_rescisao FROM pessoal'|| stEntidade ||'.caso_causa WHERE cod_caso_causa = contrato_servidor_caso_causa.cod_caso_causa)) AS VARCHAR) as causa_rescisao 
                       , CAST((SELECT descricao FROM pessoal'|| stEntidade ||'.tipo_admissao WHERE cod_tipo_admissao = contrato_servidor.cod_tipo_admissao) AS VARCHAR) as tipo_admissao 
                       , CAST((SELECT descricao FROM pessoal'|| stEntidade ||'.vinculo_empregaticio WHERE cod_vinculo = contrato_servidor.cod_vinculo) AS VARCHAR) as vinculo_empregaticio 
                       , CAST((SELECT descricao FROM pessoal'|| stEntidade ||'.categoria WHERE cod_categoria = contrato_servidor.cod_categoria) AS VARCHAR) as categoria 
                       , CAST((SELECT descricao FROM pessoal'|| stEntidade ||'.ocorrencia WHERE cod_ocorrencia = contrato_servidor_ocorrencia.cod_ocorrencia) AS VARCHAR) as classif_agentes_nocivos 
                       , CAST((SELECT descricao FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = contrato_servidor.cod_regime)||''/''|| (SELECT descricao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = contrato_servidor.cod_sub_divisao) AS VARCHAR) as regime_subdivisao_cargo 
                       , CAST((SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = contrato_servidor.cod_cargo) AS VARCHAR) AS cargo 
                       , CAST( CASE WHEN contrato_servidor_especialidade_cargo.cod_especialidade IS NULL THEN (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = contrato_servidor.cod_cargo) 
                               ELSE (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = contrato_servidor.cod_cargo)||''/''|| (SELECT descricao FROM pessoal'|| stEntidade ||'.especialidade WHERE cod_especialidade = contrato_servidor_especialidade_cargo.cod_especialidade) END AS VARCHAR) AS cargo_especialidade 
                       , CAST((SELECT descricao FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = contrato_servidor_regime_funcao.cod_regime_funcao)||''/''|| (SELECT descricao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao) AS VARCHAR) as regime_subdivisao_funcao 
                       , CAST((SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = contrato_servidor_funcao.cod_cargo) AS VARCHAR) as funcao 
                       , CAST(to_real(contrato_servidor_salario.horas_mensais) AS VARCHAR) as horas_mensais 
                       , CAST(to_real(contrato_servidor_salario.horas_semanais) AS VARCHAR) as horas_semanais 
                       , CAST(to_real(contrato_servidor_salario.salario) AS VARCHAR) as salario
                       , CAST(contrato_servidor_padrao.descricao AS VARCHAR) as padrao
                       , CAST(to_real(contrato_servidor_padrao.valor) AS VARCHAR) as valor_padrao
                       , CAST((SELECT descricao FROM pessoal'|| stEntidade ||'.forma_pagamento WHERE cod_forma_pagamento = contrato_servidor_forma_pagamento.cod_forma_pagamento) AS VARCHAR) as forma_pagamento
                       , CASE WHEN contrato_servidor_forma_pagamento.cod_forma_pagamento = 3 /*CC*/ THEN
                                ((SELECT num_banco FROM monetario.banco WHERE cod_banco = contrato_servidor_conta_salario.cod_banco) ||''-''|| (SELECT nom_banco FROM monetario.banco WHERE cod_banco = contrato_servidor_conta_salario.cod_banco))::varchar
                              ELSE 
                                ''''::varchar
                          END as banco                                                                                                                                  
                       , CASE WHEN contrato_servidor_forma_pagamento.cod_forma_pagamento = 3 /*CC*/ THEN
                                ((SELECT num_agencia||'' - ''|| nom_agencia FROM monetario.agencia WHERE cod_agencia = contrato_servidor_conta_salario.cod_agencia AND cod_banco = contrato_servidor_conta_salario.cod_banco))::varchar
                              ELSE
                                ''''::varchar 
                          END as agencia
                       , CASE WHEN contrato_servidor_forma_pagamento.cod_forma_pagamento = 3 /*CC*/ THEN
                                contrato_servidor_conta_salario.nr_conta::varchar
                              ELSE
                                ''''::varchar 
                          END as conta
                       , CAST(trim(recuperaDescricaoOrgao(contrato_servidor_orgao.cod_orgao,('|| quote_literal(stExercicio ||'-01-01') ||')::date)) AS VARCHAR) as lotacao 
                       , CAST(trim((SELECT descricao FROM organograma.local WHERE cod_local = contrato_servidor_local.cod_local)) AS VARCHAR) as local
                       , CAST(contrato_servidor.dt_opcao_fgts AS DATE) as data_opcao_fgts 
                       , CAST(ctps.numero AS VARCHAR) as ctps 
                       , CAST(ctps.serie AS VARCHAR) as serie_ctps 
                       , CAST(previdencia.descricao AS VARCHAR) as previdencia 
                       , CAST('''' AS VARCHAR) as cid
                       , CAST('''' AS VARCHAR) as numero_beneficio
                       , CAST('''' AS VARCHAR) as matricula_gerador_beneficio
                       , CAST('''' AS VARCHAR) as ocupacao
                       , CAST('''' AS VARCHAR) as grau_parentesco
                       , CAST('''' AS VARCHAR) as tipo_dependencia
                       , CAST('''' AS VARCHAR) as percentual_pagamento_pensao 
                       , null as data_inicio_beneficio
                       , CAST('''' AS VARCHAR) as processo
                       , null as data_inclusao_processo
                       , null as data_encerramento_beneficio 
                       , CAST('''' AS VARCHAR) as motivo_encerramento     
                       , CAST('''' AS VARCHAR) as nome_gerador_beneficio
                       , CAST('''' AS VARCHAR) as cgm_gerador_beneficio
                       , provento_evento_calculado.bruto    as salario_bruto
                       , desconto_evento_calculado.descontos as descontos_da_folha_salario
                       , (provento_evento_calculado.bruto - desconto_evento_calculado.descontos) AS salario_liquido
                            
             FROM pessoal'|| stEntidade ||'.servidor 
             
                 LEFT JOIN (SELECT ctps.* 
                                  , servidor_ctps.cod_servidor 
                            FROM pessoal'|| stEntidade ||'.servidor_ctps 
                                , (SELECT cod_servidor 
                                        , max(cod_ctps) as cod_ctps 
                                    FROM pessoal'|| stEntidade ||'.servidor_ctps 
                                    GROUP BY cod_servidor) as max_servidor_ctps 
                                , pessoal'|| stEntidade ||'.ctps 
                            WHERE servidor_ctps.cod_servidor = max_servidor_ctps.cod_servidor 
                              AND servidor_ctps.cod_ctps = max_servidor_ctps.cod_ctps 
                              AND servidor_ctps.cod_ctps = ctps.cod_ctps) as ctps 
                       ON servidor.cod_servidor = ctps.cod_servidor 
                , sw_cgm 
                , sw_cgm_pessoa_fisica 
                , ultimo_servidor_pis_pasep('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as servidor_pis_pasep
                , pessoal'|| stEntidade ||'.servidor_contrato_servidor';
        
        IF stTipoFiltro = 'atributo_servidor_grupo' THEN     
            stSQL := stSQL || ' INNER JOIN (SELECT ultimo_atributo_contrato_servidor_valor.* 
                                                 , atributo_dinamico.cod_tipo 
                                                 , atributo_dinamico.nom_atributo 
                                              FROM ultimo_atributo_contrato_servidor_valor('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_atributo_contrato_servidor_valor
                                              JOIN administracao.atributo_dinamico 
                                                ON ultimo_atributo_contrato_servidor_valor.cod_modulo = atributo_dinamico.cod_modulo 
                                               AND ultimo_atributo_contrato_servidor_valor.cod_cadastro = atributo_dinamico.cod_cadastro 
                                               AND ultimo_atributo_contrato_servidor_valor.cod_atributo = atributo_dinamico.cod_atributo) as atributo_contrato_servidor_valor 
                                        ON atributo_contrato_servidor_valor.cod_contrato = servidor_contrato_servidor.cod_contrato 
                                       AND atributo_contrato_servidor_valor.cod_atributo = '|| inCodAtributo; 
            
            IF boArray = 'true' THEN
                 stSQL := stSQL || ' AND atributo_contrato_servidor_valor.valor IN ('|| stCodigos ||')';             
            ELSE
                 stSQL := stSQL || ' AND atributo_contrato_servidor_valor.valor = '|| quote_literal(stCodigos) ||' '; 
            END IF;
        END IF;
        
        stSQL := stSQL || ' , pessoal'|| stEntidade ||'.contrato_servidor
                  JOIN ultimo_contrato_servidor_forma_pagamento('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') AS contrato_servidor_forma_pagamento
                    ON contrato_servidor_forma_pagamento.cod_contrato = contrato_servidor.cod_contrato
                  JOIN ultimo_contrato_servidor_ocorrencia('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') AS contrato_servidor_ocorrencia
                    ON contrato_servidor_ocorrencia.cod_contrato = contrato_servidor.cod_contrato
                  JOIN ultimo_contrato_servidor_regime_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') AS contrato_servidor_regime_funcao
                    ON contrato_servidor_regime_funcao.cod_contrato = contrato_servidor.cod_contrato
                  JOIN ultimo_contrato_servidor_sub_divisao_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') AS contrato_servidor_sub_divisao_funcao
                    ON contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor.cod_contrato
                  JOIN ultimo_contrato_servidor_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') AS contrato_servidor_funcao
                    ON contrato_servidor_funcao.cod_contrato = contrato_servidor.cod_contrato
                  JOIN ultimo_contrato_servidor_nomeacao_posse('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') AS contrato_servidor_nomeacao_posse
                    ON contrato_servidor_nomeacao_posse.cod_contrato = contrato_servidor.cod_contrato
                  JOIN ultimo_contrato_servidor_salario('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') AS contrato_servidor_salario
                    ON contrato_servidor_salario.cod_contrato = contrato_servidor.cod_contrato
                  JOIN ultimo_contrato_servidor_padrao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') AS contrato_servidor_padrao
                    ON contrato_servidor_padrao.cod_contrato = contrato_servidor.cod_contrato
                  JOIN ultimo_contrato_servidor_orgao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') AS contrato_servidor_orgao
                    ON contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato
        
             LEFT JOIN ( SELECT contrato_servidor_previdencia.cod_contrato 
                              , previdencia_previdencia.descricao 
                           FROM ultimo_contrato_servidor_previdencia('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') AS contrato_servidor_previdencia
                           JOIN ultimo_previdencia_previdencia('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') AS previdencia_previdencia
                             ON contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia 
                          WHERE previdencia_previdencia.tipo_previdencia = ''o''
                            AND contrato_servidor_previdencia.bo_excluido is false
                     ) as previdencia 
                    ON contrato_servidor.cod_contrato = previdencia.cod_contrato
                        
             LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor_especialidade_cargo 
                    ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato 
                   
             LEFT JOIN ultimo_contrato_servidor_caso_causa('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') AS contrato_servidor_caso_causa
                    ON contrato_servidor.cod_contrato = contrato_servidor_caso_causa.cod_contrato 
               
             LEFT JOIN ( SELECT ultimo_contrato_servidor_local.* 
                           FROM ultimo_contrato_servidor_local('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') 
                     ) AS contrato_servidor_local 
                    ON contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato 
                        
             LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor_conta_salario                                  
                    ON contrato_servidor.cod_contrato = contrato_servidor_conta_salario.cod_contrato 

             LEFT JOIN (   SELECT COALESCE(sum(evento_calculado.valor),0) as bruto
                                , registro_evento_periodo.cod_periodo_movimentacao
                                , registro_evento_periodo.cod_contrato
                             FROM folhapagamento'|| stEntidade ||'.evento
                                , folhapagamento'|| stEntidade ||'.evento_calculado
                                , folhapagamento'|| stEntidade ||'.registro_evento_periodo
                            WHERE evento.cod_evento = evento_calculado.cod_evento
                              AND evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                              AND evento.natureza = ''P''
                              AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                         GROUP BY 2,3
                       ) AS provento_evento_calculado
                    ON provento_evento_calculado.cod_contrato             = contrato_servidor.cod_contrato
                   AND provento_evento_calculado.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'

             LEFT JOIN (   SELECT COALESCE(sum(evento_calculado.valor),0) as descontos
                                , registro_evento_periodo.cod_periodo_movimentacao
                                , registro_evento_periodo.cod_contrato
                             FROM folhapagamento'|| stEntidade ||'.evento
                                , folhapagamento'|| stEntidade ||'.evento_calculado
                                , folhapagamento'|| stEntidade ||'.registro_evento_periodo
                            WHERE evento.cod_evento = evento_calculado.cod_evento
                              AND evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                              AND evento.natureza = ''D''
                              AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                         GROUP BY 2,3
                       ) AS desconto_evento_calculado
                    ON desconto_evento_calculado.cod_contrato             = contrato_servidor.cod_contrato
                   AND desconto_evento_calculado.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'

                 WHERE servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
                   AND servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato  
                   AND servidor.numcgm = sw_cgm.numcgm                                                                                    
                   AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                      
                   AND servidor.cod_servidor = servidor_pis_pasep.cod_servidor ';
    END IF;
        
    IF (stSituacao = 'ativos') THEN
	    stSQL := stSQL || ' AND NOT EXISTS ( SELECT 1
	 		                                   FROM   pessoal'|| stEntidade ||'.contrato_servidor_caso_causa 
	 		                                  WHERE  contrato_servidor_caso_causa.cod_contrato = servidor_contrato_servidor.cod_contrato
                                         )
	 		                                
                            AND NOT EXISTS ( SELECT 1
                                               FROM pessoal'|| stEntidade ||'.aposentadoria
                                                  , ( SELECT cod_contrato
	                                                       , max(timestamp) as timestamp
	                                                    FROM pessoal'|| stEntidade ||'.aposentadoria
	                                                GROUP BY cod_contrato
                                                 ) as max_aposentadoria
	                                         WHERE aposentadoria.cod_contrato = max_aposentadoria.cod_contrato 
	                                           AND aposentadoria.timestamp = max_aposentadoria.timestamp 
	                                           AND aposentadoria.cod_contrato = servidor_contrato_servidor.cod_contrato
                                               AND NOT EXISTS (SELECT 1
                                                                 FROM pessoal'|| stEntidade ||'.aposentadoria_excluida
                                                                WHERE aposentadoria_excluida.cod_contrato = aposentadoria.cod_contrato
                                                                  AND aposentadoria_excluida.timestamp_aposentadoria = aposentadoria.timestamp
                                                            )
                                         )';
    END IF;

    IF (stSituacao = 'aposentados') THEN
	    stSQL := stSQL || ' AND EXISTS ( SELECT 1
                                           FROM pessoal'|| stEntidade ||'.aposentadoria
                                              , ( SELECT cod_contrato
                                                       , max(timestamp) as timestamp
                                                    FROM pessoal'|| stEntidade ||'.aposentadoria
                                                GROUP BY cod_contrato
                                              ) as max_aposentadoria
                                          WHERE aposentadoria.cod_contrato = max_aposentadoria.cod_contrato 
                                            AND aposentadoria.timestamp = max_aposentadoria.timestamp 
                                            AND aposentadoria.cod_contrato = servidor_contrato_servidor.cod_contrato
                                            AND NOT EXISTS ( SELECT 1
                                                               FROM pessoal'|| stEntidade ||'.aposentadoria_excluida
                                                              WHERE aposentadoria_excluida.cod_contrato = aposentadoria.cod_contrato
                                                                AND aposentadoria_excluida.timestamp_aposentadoria = aposentadoria.timestamp
                                                           )
                                     )';
    END IF;

    IF (stSituacao = 'rescindidos') THEN
	    stSQL := stSQL || ' AND EXISTS ( SELECT 1
	 		                               FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa 
	 		                              WHERE contrato_servidor_caso_causa.cod_contrato = servidor_contrato_servidor.cod_contrato)';
    END IF;

    IF (stTipoFiltro = 'lotacao_grupo') THEN
	    stSQL := stSQL || ' AND cod_orgao IN ('|| stCodigos ||')';
    END IF;

    IF (stTipoFiltro = 'local_grupo') THEN        
        stSQL := stSQL || ' AND cod_local IN ('|| stCodigos ||')';
    END IF;

    IF (stTipoFiltro = 'sub_divisao_funcao_grupo') THEN                
         stSQL := stSQL || ' AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao IN ('|| stCodigos ||')'; 
    END IF;

    IF (stTipoFiltro = 'cargo_grupo') THEN                        
         stSQL := stSQL || ' AND contrato_servidor.cod_cargo IN ('|| stCodigos ||')'; 
    END IF;

    IF (stTipoFiltro = 'funcao_grupo') THEN                                
	     stSQL := stSQL || ' AND contrato_servidor_funcao.cod_cargo IN ('|| stCodigos ||')'; 
    END IF;

    IF stSituacao = 'pensionistas' THEN
        IF (stTipoFiltro = 'contrato') THEN
            stSQL := stSQL || ' AND contrato_pensionista.cod_contrato IN ('|| stCodigos ||')';
        END IF;

        stSQL := stSQL || ' AND to_date(contrato_pensionista.dt_inicio_beneficio::varchar, ''yyyy-mm-dd''::varchar) <= to_date('|| quote_literal(dtFimCompetencia) ||', ''yyyy-mm-dd'') ';
        stSQL := stSQL || ' AND ( (to_date(contrato_pensionista.dt_encerramento::varchar,  ''yyyy-mm-dd''::varchar) <  to_date('|| quote_literal(dtFimCompetencia) ||', ''yyyy-mm-dd'') ) ';
        stSQL := stSQL || '    OR (contrato_pensionista.dt_encerramento IS NULL) )';
    ELSE 
        IF (stTipoFiltro = 'contrato_rescisao') or (stTipoFiltro = 'contrato') THEN
            stSQL := stSQL || ' AND servidor_contrato_servidor.cod_contrato IN ('|| stCodigos ||')';
        END IF;
    END IF;


    FOR reRegistro IN EXECUTE stSql LOOP
        -- SQL interno busca os atributos dinâmicos
        IF stCodAtributos != '' THEN
            arCodAtributos := string_to_array(stCodAtributos,',');
            inIndex := 1;
            WHILE arCodAtributos[inIndex] IS NOT NULL LOOP
                IF stSituacao = 'pensionistas' THEN     
                    stSQL := 'SELECT ( CASE WHEN atributo_dinamico.cod_tipo = 4 or atributo_dinamico.cod_tipo = 3 THEN 
                                         ( SELECT atributo_valor_padrao.valor_padrao
                                             FROM administracao.atributo_valor_padrao
                                            WHERE ultimo_atributo_contrato_pensionista.cod_modulo = atributo_valor_padrao.cod_modulo
                                              AND ultimo_atributo_contrato_pensionista.cod_cadastro = atributo_valor_padrao.cod_cadastro
                                              AND ultimo_atributo_contrato_pensionista.cod_atributo = atributo_valor_padrao.cod_atributo
                                              AND ultimo_atributo_contrato_pensionista.valor = atributo_valor_padrao.cod_valor)
                                       ELSE 
                                            ultimo_atributo_contrato_pensionista.valor 
                                       END ) AS valor
                                FROM ultimo_atributo_contrato_pensionista('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_atributo_contrato_pensionista
                                JOIN administracao.atributo_dinamico
                                  ON ultimo_atributo_contrato_pensionista.cod_atributo = atributo_dinamico.cod_atributo
                                 AND ultimo_atributo_contrato_pensionista.cod_cadastro = atributo_dinamico.cod_cadastro
                                 AND ultimo_atributo_contrato_pensionista.cod_modulo   = atributo_dinamico.cod_modulo
                               WHERE ultimo_atributo_contrato_pensionista.cod_contrato = '|| reRegistro.cod_contrato ||'
                                 AND ultimo_atributo_contrato_pensionista.cod_atributo = '|| arCodAtributos[inIndex] ||'
                                       ';

                   ELSE
                        stSQL := 'SELECT ( CASE WHEN atributo_dinamico.cod_tipo = 4 or atributo_dinamico.cod_tipo = 3 THEN 
                                            ( SELECT atributo_valor_padrao.valor_padrao
                                                FROM administracao.atributo_valor_padrao 
                                               WHERE ultimo_atributo_contrato_servidor_valor.cod_modulo = atributo_valor_padrao.cod_modulo
                                                 AND ultimo_atributo_contrato_servidor_valor.cod_cadastro = atributo_valor_padrao.cod_cadastro
                                                 AND ultimo_atributo_contrato_servidor_valor.cod_atributo = atributo_valor_padrao.cod_atributo
                                                 AND ultimo_atributo_contrato_servidor_valor.valor = atributo_valor_padrao.cod_valor::varchar)
                                           ELSE 
                                                ultimo_atributo_contrato_servidor_valor.valor 
                                           END ) AS valor
                                    FROM ultimo_atributo_contrato_servidor_valor('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_atributo_contrato_servidor_valor
                                    JOIN administracao.atributo_dinamico
                                      ON ultimo_atributo_contrato_servidor_valor.cod_atributo = atributo_dinamico.cod_atributo
                                     AND ultimo_atributo_contrato_servidor_valor.cod_cadastro = atributo_dinamico.cod_cadastro
                                     AND ultimo_atributo_contrato_servidor_valor.cod_modulo = atributo_dinamico.cod_modulo
                                   WHERE ultimo_atributo_contrato_servidor_valor.cod_atributo = '|| arCodAtributos[inIndex] ||'
                                     AND ultimo_atributo_contrato_servidor_valor.cod_contrato = '|| reRegistro.cod_contrato;
                   END IF;
            
                arValoresAtributo[inIndex] := selectIntoVarchar(stSQL);                
                inIndex := inIndex + 1;
            END LOOP;
        END IF;

        rwDefinivelServidor.cod_contrato                := reRegistro.cod_contrato;
        rwDefinivelServidor.nome                        := reRegistro.nome;
        rwDefinivelServidor.endereco                    := reRegistro.endereco;
        rwDefinivelServidor.bairro                      := reRegistro.bairro;
        rwDefinivelServidor.cep                         := reRegistro.cep;  
        rwDefinivelServidor.municipio                   := reRegistro.municipio;
        rwDefinivelServidor.uf                          := reRegistro.uf;
        rwDefinivelServidor.fone                        := reRegistro.fone;
        rwDefinivelServidor.escolaridade                := reRegistro.escolaridade;
        rwDefinivelServidor.data_nascimento             := reRegistro.data_nascimento;
        rwDefinivelServidor.cpf                         := reRegistro.cpf;
        rwDefinivelServidor.rg                          := reRegistro.rg;
        rwDefinivelServidor.processo                    := reRegistro.processo;
        rwDefinivelServidor.data_inclusao_processo      := reRegistro.data_inclusao_processo;
        rwDefinivelServidor.matricula                   := reRegistro.matricula;
        rwDefinivelServidor.grau_parentesco             := reRegistro.grau_parentesco;
        rwDefinivelServidor.ocupacao                    := reRegistro.ocupacao;
        rwDefinivelServidor.data_inicio_beneficio       := reRegistro.data_inicio_beneficio;
        rwDefinivelServidor.data_encerramento_beneficio := reRegistro.data_encerramento_beneficio;
        rwDefinivelServidor.motivo_encerramento         := reRegistro.motivo_encerramento;
        rwDefinivelServidor.numero_beneficio            := reRegistro.numero_beneficio;
        rwDefinivelServidor.percentual_pagamento_pensao := reRegistro.percentual_pagamento_pensao;
        rwDefinivelServidor.tipo_dependencia            := reRegistro.tipo_dependencia;
        rwDefinivelServidor.cid                         := reRegistro.cid;
        rwDefinivelServidor.banco                       := reRegistro.banco;
        rwDefinivelServidor.agencia                     := reRegistro.agencia;
        rwDefinivelServidor.conta                       := reRegistro.conta;
        rwDefinivelServidor.lotacao                     := reRegistro.lotacao;
        rwDefinivelServidor.previdencia                 := reRegistro.previdencia;
        rwDefinivelServidor.matricula_gerador_beneficio := reRegistro.matricula_gerador_beneficio;
        rwDefinivelServidor.nome_gerador_beneficio      := reRegistro.nome_gerador_beneficio;
        rwDefinivelServidor.cgm_gerador_beneficio       := reRegistro.cgm_gerador_beneficio;
        rwDefinivelServidor.pis_pasep                   := reRegistro.pis_pasep;
        rwDefinivelServidor.titulo_de_eleitor           := reRegistro.titulo_de_eleitor;
        rwDefinivelServidor.secao_do_titulo             := reRegistro.secao_do_titulo;
        rwDefinivelServidor.zona_titulo                 := reRegistro.zona_titulo;
        rwDefinivelServidor.ctps                        := reRegistro.ctps;
        rwDefinivelServidor.serie_ctps                  := reRegistro.serie_ctps;
        rwDefinivelServidor.data_nomeacao               := reRegistro.data_nomeacao;
        rwDefinivelServidor.data_posse                  := reRegistro.data_posse;
        rwDefinivelServidor.data_admissao               := reRegistro.data_admissao;
        rwDefinivelServidor.data_rescisao               := reRegistro.data_rescisao;
        rwDefinivelServidor.causa_rescisao              := reRegistro.causa_rescisao;
        rwDefinivelServidor.regime_subdivisao_cargo     := reRegistro.regime_subdivisao_cargo;
        rwDefinivelServidor.cargo_especialidade         := reRegistro.cargo_especialidade;
        rwDefinivelServidor.regime_subdivisao_funcao    := reRegistro.regime_subdivisao_funcao;
        rwDefinivelServidor.funcao                      := reRegistro.funcao;
        rwDefinivelServidor.categoria                   := reRegistro.categoria;
        rwDefinivelServidor.tipo_admissao               := reRegistro.tipo_admissao;
        rwDefinivelServidor.vinculo_empregaticio        := reRegistro.vinculo_empregaticio;
        rwDefinivelServidor.classif_agentes_nocivos     := reRegistro.classif_agentes_nocivos;
        rwDefinivelServidor.horas_mensais               := reRegistro.horas_mensais;
        rwDefinivelServidor.horas_semanais              := reRegistro.horas_semanais;
        rwDefinivelServidor.padrao                      := reRegistro.padrao;
        rwDefinivelServidor.salario                     := reRegistro.salario;
        rwDefinivelServidor.forma_pagamento             := reRegistro.forma_pagamento;
        rwDefinivelServidor.local                       := reRegistro.local;
        rwDefinivelServidor.data_opcao_fgts             := reRegistro.data_opcao_fgts;
        rwDefinivelServidor.valor_padrao                := reRegistro.valor_padrao;

        rwDefinivelServidor.atributo1                   := arValoresAtributo[1];
        rwDefinivelServidor.atributo2                   := arValoresAtributo[2];
        rwDefinivelServidor.atributo3                   := arValoresAtributo[3];
        rwDefinivelServidor.atributo4                   := arValoresAtributo[4];
        rwDefinivelServidor.atributo5                   := arValoresAtributo[5];
        rwDefinivelServidor.atributo6                   := arValoresAtributo[6];
        rwDefinivelServidor.atributo7                   := arValoresAtributo[7];
        rwDefinivelServidor.atributo8                   := arValoresAtributo[8];
        rwDefinivelServidor.atributo9                   := arValoresAtributo[9];
        rwDefinivelServidor.atributo10                  := arValoresAtributo[10];
        rwDefinivelServidor.salario_bruto               := reRegistro.salario_bruto;
        rwDefinivelServidor.descontos_da_folha_salario  := reRegistro.descontos_da_folha_salario;
        rwDefinivelServidor.salario_liquido             := reRegistro.salario_liquido;

        nuEventoValor[1] := 0;
        nuEventoValor[2] := 0;
        nuEventoValor[3] := 0;
        nuEventoValor[4] := 0;
        nuEventoQte[1] := 0;
        nuEventoQte[2] := 0;
        nuEventoQte[3] := 0;
        nuEventoQte[4] := 0;

        IF (arCodEventosQtePosicao IS NOT NULL) THEN
            FOR inIndex IN 1..array_upper(arCodEventosQtePosicao, 1) LOOP
                stCodEvento := arCodEventosQte[arCodEventosQtePosicao[inIndex]];
                inCodPosicaoEvento := arCodEventosQtePosicao[inIndex];
                nuEventoQte[inCodPosicaoEvento] := selectIntoNumeric('SELECT COALESCE(quantidade, 0.00) FROM recuperarEventosCalculados('|| inCodConfiguracao ||','|| inCodPeriodoMovimentacao ||','|| reRegistro.cod_contrato ||','|| inCodComplementar ||', '|| quote_literal(stEntidade) ||', ''evento.descricao'') as eventos_calculados_salario WHERE cod_evento = '|| quote_literal(stCodEvento) ||' ');
            END LOOP;
        END IF;

        IF (arCodEventosValorPosicao IS NOT NULL) THEN
            FOR inIndex IN 1..array_upper(arCodEventosValorPosicao, 1) LOOP
                stCodEvento := arCodEventosValor[arCodEventosValorPosicao[inIndex]];
                inCodPosicaoEvento := arCodEventosValorPosicao[inIndex];
                nuEventoValor[inCodPosicaoEvento] := selectIntoNumeric('SELECT COALESCE(valor, 0.00) FROM recuperarEventosCalculados('|| inCodConfiguracao ||','|| inCodPeriodoMovimentacao ||','|| reRegistro.cod_contrato ||','|| inCodComplementar ||', '|| quote_literal(stEntidade) ||', ''evento.descricao'') as eventos_calculados_salario WHERE cod_evento = '|| quote_literal(stCodEvento) ||' ');
            END LOOP;
        END IF;

        rwDefinivelServidor.evento1_qte   := COALESCE(nuEventoQte[1], 0.00); 
        rwDefinivelServidor.evento2_qte   := COALESCE(nuEventoQte[2], 0.00); 
        rwDefinivelServidor.evento3_qte   := COALESCE(nuEventoQte[3], 0.00); 
        rwDefinivelServidor.evento4_qte   := COALESCE(nuEventoQte[4], 0.00); 
        rwDefinivelServidor.evento1_valor := COALESCE(nuEventoValor[1], 0.00); 
        rwDefinivelServidor.evento2_valor := COALESCE(nuEventoValor[2], 0.00); 
        rwDefinivelServidor.evento3_valor := COALESCE(nuEventoValor[3], 0.00); 
        rwDefinivelServidor.evento4_valor := COALESCE(nuEventoValor[4], 0.00); 

        IF (stTipoFiltro = 'contrato' or stTipoFiltro = 'cgm_contrato') THEN
            rwDefinivelServidor.agrupamento := reRegistro.matricula;    
        END IF;

        IF (stTipoFiltro = 'lotacao_grupo') THEN
            rwDefinivelServidor.agrupamento := reRegistro.lotacao;    
        END IF;
    
        IF (stTipoFiltro = 'local_grupo') THEN        
            rwDefinivelServidor.agrupamento := reRegistro.local;
        END IF;
    
        IF (stTipoFiltro = 'sub_divisao_funcao_grupo') THEN                
            rwDefinivelServidor.agrupamento := reRegistro.regime_subdivisao_funcao;
        END IF;
    
        IF (stTipoFiltro = 'cargo_grupo') THEN                        
            rwDefinivelServidor.agrupamento := reRegistro.regime_subdivisao_cargo;
        END IF;
    
        IF (stTipoFiltro = 'funcao_grupo') THEN                                
            rwDefinivelServidor.agrupamento := reRegistro.funcao;
        END IF;

        RETURN NEXT rwDefinivelServidor;
    END LOOP;
        
END;

$$ LANGUAGE 'plpgsql';
