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
--/**
--    * Função PLSQL
--    * Data de Criação: 29/02/2008
--
--
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * Casos de uso: uc-04.07.04
--
--    $Id: reciboPagamentoEstagiario.sql 32984 2008-09-04 13:58:59Z domluc $
--*/
DROP TYPE colunasReciboPagamentoEstagiario CASCADE;
CREATE TYPE colunasReciboPagamentoEstagiario AS (
    numero_estagio              VARCHAR,
    nom_cgm                     VARCHAR(200),
    cpf                         VARCHAR,
    rg                          VARCHAR,
    num_banco                   VARCHAR,
    num_agencia                 VARCHAR,
    num_conta                   VARCHAR,
    local                       VARCHAR,
    lotacao                     VARCHAR,
    dt_inicio                   VARCHAR,
    dt_final                    VARCHAR,
    vl_bolsa                    VARCHAR,
    vl_bolsa_extenso            VARCHAR,
    faltas                      VARCHAR,
    vl_vale                     VARCHAR,
    vl_vale_extenso             VARCHAR,
    vl_desconto                 VARCHAR,
    vl_desconto_extenso         VARCHAR,
    mes_ano                     VARCHAR,
    entidade                    VARCHAR,
    cnpj                        VARCHAR,
    logotipo                    VARCHAR,
    qtd_vt                      VARCHAR,
    vl_vt                       VARCHAR,
    vl_vt_extenso               VARCHAR
);

CREATE OR REPLACE FUNCTION reciboPagamentoEstagiario(VARCHAR,VARCHAR,VARCHAR,INTEGER,VARCHAR,INTEGER,INTEGER,INTEGER,INTEGER, INTEGER) RETURNS SETOF colunasReciboPagamentoEstagiario AS $$
DECLARE
    stTipoFiltro                ALIAS FOR $1;
    stCodigos                   ALIAS FOR $2;
    stOrdem                     ALIAS FOR $3;
    boDuplicar                  ALIAS FOR $4;
    stCodEntidade               ALIAS FOR $5;
    inCodEntidade               ALIAS FOR $6;
    inExercicio                 ALIAS FOR $7;
    inCodAtributo               ALIAS FOR $8;
    boArray                     ALIAS FOR $9;
    inCodPeriodoMovimentacao    ALIAS FOR $10;
    stSql                       VARCHAR:='';
    reRegistro                  RECORD;
    reVR                        RECORD;
    reVT                        RECORD;
    stMesAno                    VARCHAR;
    stLogotipo                  VARCHAR;
    stEntidade                  VARCHAR;
    stCNPJ                      VARCHAR;
    nuNovoValor                 NUMERIC;
    crCursor                    REFCURSOR;
    rwReciboPagamentoEstagiario           colunasReciboPagamentoEstagiario%ROWTYPE;
BEGIN
    stSql := 'SELECT to_char(dt_final,''mm/yyyy'') FROM folhapagamento'|| stCodEntidade ||'.periodo_movimentacao where cod_periodo_movimentacao= '||inCodPeriodoMovimentacao||' ORDER BY cod_periodo_movimentacao desc LIMIT 1';
    stMesAno := selectIntoVarchar(stSql);
    stSql := 'SELECT (SELECT nom_cgm FROM sw_cgm WHERE numcgm = entidade.numcgm) as entidade
                   , (SELECT cnpj FROM sw_cgm_pessoa_juridica WHERE numcgm = entidade.numcgm) as cnpj
                FROM orcamento.entidade
               WHERE exercicio = '|| quote_literal(inExercicio) ||'
                 AND cod_entidade = '|| inCodEntidade;
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO stEntidade,stCNPJ;
    CLOSE crCursor;

    stSql := 'SELECT logotipo FROM orcamento.entidade_logotipo WHERE cod_entidade = '|| inCodEntidade ||' AND exercicio = '|| quote_literal(inExercicio) ||' ';
    stLogotipo := selectIntoVarchar(stSql);
    IF stLogotipo IS NULL OR stLogotipo = '' THEN
        stSql := 'SELECT ''../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/'' || valor AS logotipo
                    FROM administracao.configuracao
                   WHERE parametro = ''logotipo''
                     AND exercicio = '|| quote_literal(inExercicio) ||' ';
        stLogotipo := selectIntoVarchar(stSql);
    ELSE
        stLogotipo := '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/anexos/'||stLogotipo;
    END IF;

    stSql := '   SELECT estagiario_estagio.numero_estagio
                      , estagiario_estagio.cod_estagio
                      , estagiario_estagio.cod_curso
                      , estagiario_estagio.cgm_estagiario
                      , estagiario_estagio.cgm_instituicao_ensino
                      , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = estagiario_estagio.cgm_estagiario) as nom_cgm
                      , cpf
                      , rg
                      , (SELECT num_banco FROM monetario.banco WHERE cod_banco = estagiario_estagio_conta.cod_banco) as num_banco
                      , (SELECT num_agencia FROM monetario.agencia WHERE cod_banco = estagiario_estagio_conta.cod_banco AND cod_agencia = estagiario_estagio_conta.cod_agencia) as num_agencia
                      , estagiario_estagio_conta.num_conta
                      , (SELECT descricao FROM organograma.local WHERE cod_local = estagiario_estagio_local.cod_local) as local
                      , recuperaDescricaoOrgao(estagiario_estagio.cod_orgao,'|| quote_literal(inExercicio||'-01-01') ||') as lotacao
                      , to_char(estagiario_estagio.dt_inicio, ''dd/mm/yyyy'') as dt_inicio
                      , to_char(estagiario_estagio.dt_final, ''dd/mm/yyyy'') as dt_final
                      , estagiario_estagio_bolsa.vl_bolsa
                      , estagiario_estagio_bolsa.faltas
                   FROM estagio'|| stCodEntidade ||'.estagiario_estagio
              LEFT JOIN estagio'|| stCodEntidade ||'.entidade_intermediadora_estagio
                     ON entidade_intermediadora_estagio.cod_estagio = estagiario_estagio.cod_estagio
                    AND entidade_intermediadora_estagio.cgm_estagiario = estagiario_estagio.cgm_estagiario
                    AND entidade_intermediadora_estagio.cod_curso = estagiario_estagio.cod_curso
                    AND entidade_intermediadora_estagio.cgm_instituicao_ensino = estagiario_estagio.cgm_instituicao_ensino
              LEFT JOIN (SELECT estagiario_estagio_bolsa.*
                           FROM estagio'|| stCodEntidade ||'.estagiario_estagio_bolsa
                              , (SELECT cod_estagio
                                      , cgm_estagiario
                                      , cod_curso
                                      , cgm_instituicao_ensino
                                      , max(timestamp) as timestamp
                                   FROM estagio'|| stCodEntidade ||'.estagiario_estagio_bolsa
                               GROUP BY cod_estagio
                                      , cgm_estagiario
                                      , cod_curso
                                      , cgm_instituicao_ensino) as max_estagiario_estagio_bolsa
                          WHERE estagiario_estagio_bolsa.cod_estagio = max_estagiario_estagio_bolsa.cod_estagio
                            AND estagiario_estagio_bolsa.cgm_estagiario = max_estagiario_estagio_bolsa.cgm_estagiario
                            AND estagiario_estagio_bolsa.cod_curso = max_estagiario_estagio_bolsa.cod_curso
                            AND estagiario_estagio_bolsa.cgm_instituicao_ensino = max_estagiario_estagio_bolsa.cgm_instituicao_ensino
                            AND estagiario_estagio_bolsa.timestamp = max_estagiario_estagio_bolsa.timestamp
                            AND estagiario_estagio_bolsa.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                        ) as estagiario_estagio_bolsa
                     ON estagiario_estagio_bolsa.cod_estagio = estagiario_estagio.cod_estagio
                    AND estagiario_estagio_bolsa.cgm_estagiario = estagiario_estagio.cgm_estagiario
                    AND estagiario_estagio_bolsa.cod_curso = estagiario_estagio.cod_curso
                    AND estagiario_estagio_bolsa.cgm_instituicao_ensino = estagiario_estagio.cgm_instituicao_ensino
              LEFT JOIN estagio'|| stCodEntidade ||'.estagiario_estagio_local
                     ON estagiario_estagio_local.cod_estagio = estagiario_estagio.cod_estagio
                    AND estagiario_estagio_local.numcgm = estagiario_estagio.cgm_estagiario
                    AND estagiario_estagio_local.cod_curso = estagiario_estagio.cod_curso
                    AND estagiario_estagio_local.cgm_instituicao_ensino = estagiario_estagio.cgm_instituicao_ensino
              LEFT JOIN estagio'|| stCodEntidade ||'.estagiario_estagio_conta
                     ON estagiario_estagio_conta.cod_estagio = estagiario_estagio.cod_estagio
                    AND estagiario_estagio_conta.numcgm = estagiario_estagio.cgm_estagiario
                    AND estagiario_estagio_conta.cod_curso = estagiario_estagio.cod_curso
                    AND estagiario_estagio_conta.cgm_instituicao_ensino = estagiario_estagio.cgm_instituicao_ensino
                      , sw_cgm_pessoa_fisica
                  WHERE estagiario_estagio.cgm_estagiario = sw_cgm_pessoa_fisica.numcgm';
    IF stTipoFiltro = 'cgm_codigo_estagio' THEN
        stSql := stSql || ' AND numero_estagio::integer IN ('|| stCodigos ||')';
    END IF;
    IF stTipoFiltro = 'instituicao_ensino' THEN
        stSql := stSql || ' AND estagiario_estagio.cgm_instituicao_ensino = '|| stCodigos;
    END IF;
    IF stTipoFiltro = 'entidade_intermediadora' THEN
        stSql := stSql || ' AND entidade_intermediadora_estagio.cgm_entidade = '|| stCodigos;
    END IF;
    IF stTipoFiltro = 'atributo_estagiario' THEN
        stSql := stSql || ' AND EXISTS (SELECT 1
                                          FROM estagio'|| stCodEntidade ||'.atributo_estagiario_estagio
                                         WHERE atributo_estagiario_estagio.cod_estagio = estagiario_estagio.cod_estagio
                                           AND atributo_estagiario_estagio.numcgm = estagiario_estagio.cgm_estagiario
                                           AND atributo_estagiario_estagio.cod_curso = estagiario_estagio.cod_curso
                                           AND atributo_estagiario_estagio.cgm_instituicao_ensino = estagiario_estagio.cgm_instituicao_ensino
                                           AND atributo_estagiario_estagio.cod_atributo = '|| inCodAtributo;
        IF boArray = 1 THEN
            stSql := stSql || '                AND atributo_estagiario_estagio.valor IN ('|| stCodigos ||'))';
        ELSE
            stSql := stSql || '                AND atributo_estagiario_estagio.valor ilike '||  quote_literal('%'|| stCodigos ||'%') ||')';
        END IF;
    END IF;
    IF stTipoFiltro = 'lotacao_grupo' THEN
        stSql := stSql || ' AND estagiario_estagio.cod_orgao IN ('|| stCodigos ||')';
    END IF;
    IF stTipoFiltro = 'local_grupo' THEN
        stSql := stSql || ' AND estagiario_estagio_local.cod_local IN ('|| stCodigos ||')';
    END IF;
    stSql := stSql || ' ORDER BY '|| stOrdem;

    FOR reRegistro IN  EXECUTE stSql
    LOOP
        --Busca o valor do VR
        stSql := 'SELECT estagiario_vale_refeicao.quantidade
                       , estagiario_vale_refeicao.vl_vale
                       , estagiario_vale_refeicao.vl_desconto
                    FROM estagio'|| stCodEntidade ||'.estagiario_vale_refeicao
                       , (  SELECT cod_estagio
                                 , cod_curso
                                 , cgm_estagiario
                                 , cgm_instituicao_ensino
                                 , max(timestamp) as timestamp
                              FROM estagio'|| stCodEntidade ||'.estagiario_vale_refeicao
                          GROUP BY cod_estagio
                                 , cod_curso
                                 , cgm_estagiario
                                 , cgm_instituicao_ensino) AS max_estagiario_vale_refeicao
                   WHERE estagiario_vale_refeicao.cod_estagio            = max_estagiario_vale_refeicao.cod_estagio
                     AND estagiario_vale_refeicao.cod_curso              = max_estagiario_vale_refeicao.cod_curso
                     AND estagiario_vale_refeicao.cgm_estagiario         = max_estagiario_vale_refeicao.cgm_estagiario
                     AND estagiario_vale_refeicao.cgm_instituicao_ensino = max_estagiario_vale_refeicao.cgm_instituicao_ensino
                     AND estagiario_vale_refeicao.timestamp = max_estagiario_vale_refeicao.timestamp
                     AND estagiario_vale_refeicao.cod_estagio            = '|| reRegistro.cod_estagio            ||'
                     AND estagiario_vale_refeicao.cod_curso              = '|| reRegistro.cod_curso              ||'
                     AND estagiario_vale_refeicao.cgm_estagiario         = '|| reRegistro.cgm_estagiario         ||'
                     AND estagiario_vale_refeicao.cgm_instituicao_ensino = '|| reRegistro.cgm_instituicao_ensino ||'
                     ';
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reVR;
        CLOSE crCursor;

        --Busca o valor do VT
        stSql := 'SELECT CASE WHEN cod_tipo = 1 THEN diasUteisNoMes('|| quote_literal(stCodEntidade) ||', '|| quote_literal(substr(stMesAno, 1, 2)) ||', '|| quote_literal(inExercicio) ||', estagiario_vale_transporte.cod_calendar, estagiario_estagio.cod_grade) * estagiario_vale_transporte.quantidade
                              ELSE estagiario_vale_transporte.quantidade
                         END AS qtd_vt
                       , CASE WHEN cod_tipo = 1 THEN ((diasUteisNoMes('|| quote_literal(stCodEntidade) ||', '|| quote_literal(substr(stMesAno, 1, 2)) ||', '|| quote_literal(inExercicio) ||', estagiario_vale_transporte.cod_calendar, estagiario_estagio.cod_grade) * estagiario_vale_transporte.quantidade) * estagiario_vale_transporte.valor_unitario)
                              ELSE estagiario_vale_transporte.valor_unitario
                         END AS vl_vt
                    FROM estagio'|| stCodEntidade ||'.estagiario_vale_transporte
              INNER JOIN estagio'|| stCodEntidade ||'.estagiario_estagio
                      ON estagiario_vale_transporte.cod_estagio = estagiario_estagio.cod_estagio
                     AND estagiario_vale_transporte.cgm_estagiario = estagiario_estagio.cgm_estagiario
                     AND estagiario_vale_transporte.cod_curso = estagiario_estagio.cod_curso
                     AND estagiario_vale_transporte.cgm_instituicao_ensino = estagiario_estagio.cgm_instituicao_ensino
              INNER JOIN (  SELECT cod_estagio
                                 , cod_curso
                                 , cgm_estagiario
                                 , cgm_instituicao_ensino
                                 , max(timestamp) as timestamp
                              FROM estagio'|| stCodEntidade ||'.estagiario_vale_transporte
                          GROUP BY cod_estagio
                                 , cod_curso
                                 , cgm_estagiario
                                 , cgm_instituicao_ensino) AS max_estagiario_vale_transporte
                      ON estagiario_vale_transporte.cod_estagio            = max_estagiario_vale_transporte.cod_estagio
                     AND estagiario_vale_transporte.cod_curso              = max_estagiario_vale_transporte.cod_curso
                     AND estagiario_vale_transporte.cgm_estagiario         = max_estagiario_vale_transporte.cgm_estagiario
                     AND estagiario_vale_transporte.cgm_instituicao_ensino = max_estagiario_vale_transporte.cgm_instituicao_ensino
                     AND estagiario_vale_transporte.timestamp              = max_estagiario_vale_transporte.timestamp       
                   WHERE estagiario_vale_transporte.cod_estagio            = '|| reRegistro.cod_estagio            ||'
                     AND estagiario_vale_transporte.cod_curso              = '|| reRegistro.cod_curso              ||'
                     AND estagiario_vale_transporte.cgm_estagiario         = '|| reRegistro.cgm_estagiario         ||'
                     AND estagiario_vale_transporte.cgm_instituicao_ensino = '|| reRegistro.cgm_instituicao_ensino ||'
                     ';

        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reVT;
        CLOSE crCursor;

        IF reRegistro.faltas > 0 THEN
            nuNovoValor := reRegistro.vl_bolsa::numeric - (reRegistro.vl_bolsa::numeric/30)*reRegistro.faltas;
        ELSE
            nuNovoValor := reRegistro.vl_bolsa;
        END IF;

        rwReciboPagamentoEstagiario.numero_estagio      := reRegistro.numero_estagio;
        rwReciboPagamentoEstagiario.nom_cgm             := reRegistro.nom_cgm;
        rwReciboPagamentoEstagiario.cpf                 := reRegistro.cpf;
        rwReciboPagamentoEstagiario.rg                  := reRegistro.rg;
        rwReciboPagamentoEstagiario.num_banco           := reRegistro.num_banco;
        rwReciboPagamentoEstagiario.num_agencia         := reRegistro.num_agencia;
        rwReciboPagamentoEstagiario.num_conta           := reRegistro.num_conta;
        rwReciboPagamentoEstagiario.local               := reRegistro.local;
        rwReciboPagamentoEstagiario.lotacao             := reRegistro.lotacao;
        rwReciboPagamentoEstagiario.dt_inicio           := reRegistro.dt_inicio;
        rwReciboPagamentoEstagiario.dt_final            := reRegistro.dt_final;
        rwReciboPagamentoEstagiario.vl_bolsa            := to_real(nuNovoValor);
        rwReciboPagamentoEstagiario.faltas              := reRegistro.faltas;
        rwReciboPagamentoEstagiario.vl_vale             := to_real(reVR.vl_vale);
        rwReciboPagamentoEstagiario.vl_desconto         := to_real(reVR.vl_desconto);
        rwReciboPagamentoEstagiario.vl_vale_extenso     := lower(publico.fn_extenso(reVR.vl_vale));
        rwReciboPagamentoEstagiario.vl_bolsa_extenso    := lower(publico.fn_extenso(nuNovoValor));
        rwReciboPagamentoEstagiario.vl_desconto_extenso := lower(publico.fn_extenso(reVR.vl_desconto));
        rwReciboPagamentoEstagiario.mes_ano             := stMesAno;
        rwReciboPagamentoEstagiario.entidade            := stEntidade;
        rwReciboPagamentoEstagiario.cnpj                := stCNPJ;
        rwReciboPagamentoEstagiario.logotipo            := stLogotipo;
        rwReciboPagamentoEstagiario.qtd_vt              := reVT.qtd_vt;
        rwReciboPagamentoEstagiario.vl_vt               := to_real(reVT.vl_vt);
        rwReciboPagamentoEstagiario.vl_vt_extenso       := lower(publico.fn_extenso(reVT.vl_vt));

        --Fonte para a duplicação do recibo de pagamento
        IF boDuplicar = 1 THEN
            IF nuNovoValor > 0 THEN
                RETURN NEXT rwReciboPagamentoEstagiario;
            END IF;
        END IF;
        IF nuNovoValor > 0 THEN
            RETURN NEXT rwReciboPagamentoEstagiario;
        END IF;
    END LOOP;
    RETURN;
END;
$$LANGUAGE 'plpgsql';

--SELECT * FROM reciboPagamentoEstagiario('geral','',' nom_cgm',0,'',1,2009,0,0);
