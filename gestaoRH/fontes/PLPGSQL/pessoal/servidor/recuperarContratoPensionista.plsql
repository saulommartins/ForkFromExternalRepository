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
/* Script de função PLPGSQL
 * URBEM Soluções de Gestão Pública Ltda
 * www.urbem.cnm.org.br
   $Id: recuperarContratoPensionista.plsql 65896 2016-06-24 20:14:24Z michel $
*/
CREATE OR REPLACE FUNCTION recuperarContratoPensionista(VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasContratoPensionista AS $$
DECLARE
    stConfiguracao                  ALIAS FOR $1;
    stEntidade                      ALIAS FOR $2;
    inCodPeriodoMovimentacao        ALIAS FOR $3;
    stTipoFiltro                    ALIAS FOR $4;
    stValoresFiltro                 ALIAS FOR $5;
    stExercicio                     ALIAS FOR $6;
    rwContratoPensionista           colunasContratoPensionista%ROWTYPE;
    stSql                           VARCHAR := '';
    stSqlWhere                      VARCHAR := '';
    stSqlAux                        VARCHAR;
    inCodTipoAtributo               INTEGER;
    stTimestampFechamentoPeriodo    VARCHAR;
    stCodigos                       VARCHAR;
    stContagemTempo                 VARCHAR;
    reContratoPensionista              RECORD;
    reRegistro                      RECORD;
    crCursor                        REFCURSOR;
    arConfiguracao                  VARCHAR[];
    arValoresFiltro                 VARCHAR[];
    inIndex                         INTEGER := 1;
    inCodOrganograma                INTEGER;
    inCodPeriodoMovimentacaoAux     INTEGER;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);
    arConfiguracao := string_to_array(stConfiguracao,',');

    stSql := 'SELECT valor
                FROM administracao.configuracao
               WHERE parametro = '|| quote_literal('dtContagemInicial'||stEntidade) ||' AND exercicio = '|| quote_literal(stExercicio)||' ';
    stContagemTempo := selectIntoVarchar(stSql);

    stSql := '    SELECT contrato_pensionista.*
                       , contrato.registro
                       , pensionista.*';

    IF stTipoFiltro = 'atributo_pensionista' OR stTipoFiltro = 'atributo_pensionista_grupo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');

        --Verifica o tipo do atributo
        --TODO - Pegar o cod_modulo e cod_cadastro vindos do parametro stValoresFiltro. atualmente so é passado o cod_atributo
        stSqlAux := 'SELECT cod_tipo FROM administracao.atributo_dinamico WHERE cod_modulo = 22 AND cod_cadastro = 7 AND cod_atributo = '||arValoresFiltro[2];
        inCodTipoAtributo := selectIntoInteger(stSqlAux);
        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql || ', atributo_valor_padrao.valor_padrao as valor_atributo';
        ELSE
            stSql := stSql || ', atributo_contrato_pensionista.valor as valor_atributo';
        END IF;
    ELSE
        stSql := stSql || ', ''''::varchar as valor_atributo';
    END IF;

    stSql := stSql || '
                    FROM pessoal'||stEntidade||'.contrato_pensionista
              INNER JOIN pessoal'||stEntidade||'.contrato
                      ON contrato.cod_contrato = contrato_pensionista.cod_contrato
              INNER JOIN pessoal'||stEntidade||'.pensionista
                      ON pensionista.cod_pensionista = contrato_pensionista.cod_pensionista
                     AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente
';

    IF stTipoFiltro = 'reg_sub_fun_esp' OR stTipoFiltro = 'reg_sub_fun_esp_grupo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');

        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                                    ON contrato_servidor_regime_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                   AND contrato_servidor_regime_funcao.cod_regime IN ('||arValoresFiltro[1]||')
                            INNER JOIN pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                    ON contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao IN ('||arValoresFiltro[2]||')
                            INNER JOIN pessoal'||stEntidade||'.contrato_servidor_funcao
                                    ON contrato_servidor_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                   AND contrato_servidor_funcao.cod_cargo IN ('||arValoresFiltro[3]||')';

        stSqlWhere := stSqlWhere || ' AND contrato_servidor_regime_funcao.timestamp = (  SELECT timestamp
                                                                             FROM pessoal'||stEntidade||'.contrato_servidor_regime_funcao as contrato_servidor_regime_funcao_interna
                                                                            WHERE contrato_servidor_regime_funcao_interna.cod_contrato = contrato_servidor_regime_funcao.cod_contrato
                                                                              AND contrato_servidor_regime_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                         ORDER BY timestamp desc
                                                                            LIMIT 1 )

                                      AND contrato_servidor_sub_divisao_funcao.timestamp = (  SELECT timestamp
                                                                                                FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao as contrato_servidor_sub_divisao_funcao_interna
                                                                                               WHERE contrato_servidor_sub_divisao_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                                 AND contrato_servidor_sub_divisao_funcao_interna.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                                                                            ORDER BY timestamp desc
                                                                                               LIMIT 1
                                                                                           )
                                      AND contrato_servidor_funcao.timestamp = (  SELECT timestamp
                                                                                    FROM pessoal'||stEntidade||'.contrato_servidor_funcao as contrato_servidor_funcao_interna
                                                                                   WHERE contrato_servidor_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                     AND contrato_servidor_funcao_interna.cod_contrato = contrato_servidor_funcao.cod_contrato
                                                                                ORDER BY timestamp desc
                                                                                   LIMIT 1 )';

        IF trim(arValoresFiltro[4]) != '' THEN
            stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
                                        ON contrato_servidor_especialidade_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                       AND contrato_servidor_especialidade_funcao.cod_especialidade IN ('||arValoresFiltro[4]||')';

            stSqlWhere := stSqlWhere || ' AND contrato_servidor_especialidade_funcao.timestamp = ( SELECT timestamp
                                                                                                     FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao as contrato_servidor_especialidade_funcao_interna
                                                                                                    WHERE contrato_servidor_especialidade_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                                      AND contrato_servidor_especialidade_funcao_interna.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato
                                                                                                 ORDER BY timestamp desc
                                                                                                    LIMIT 1
                                                                                                  )';

        END IF;
    END IF;

    IF stTipoFiltro = 'contrato'                   OR
       stTipoFiltro = 'contrato_pensionista'       OR
       stTipoFiltro = 'contrato_todos'             OR
       stTipoFiltro = 'cgm_contrato'               OR
       stTipoFiltro = 'cgm_contrato_pensionista'   OR
       stTipoFiltro = 'cgm_contrato_todos'      THEN
        stSql := stSql || ' WHERE contrato.cod_contrato IN ('||stValoresFiltro||')';
    END IF;
    IF stTipoFiltro = 'lotacao' OR stTipoFiltro = 'lotacao_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_pensionista_orgao
                                    ON contrato_pensionista_orgao.cod_contrato = contrato.cod_contrato
                                   AND contrato_pensionista_orgao.cod_orgao IN ('||stValoresFiltro||')';

        stSqlWhere := stSqlWhere || ' AND contrato_pensionista_orgao.timestamp = ( SELECT timestamp
                                                                                     FROM pessoal'||stEntidade||'.contrato_pensionista_orgao as contrato_pensionista_orgao_interna
                                                                                    WHERE contrato_pensionista_orgao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                      AND contrato_pensionista_orgao_interna.cod_contrato = contrato_pensionista_orgao.cod_contrato
                                                                                 ORDER BY timestamp desc
                                                                                    LIMIT 1
                                                                                  )';

    END IF;
    IF stTipoFiltro = 'local' OR stTipoFiltro = 'local_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_local
                                    ON contrato_servidor_local.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                   AND contrato_servidor_local.cod_local IN ('||stValoresFiltro||')';

        stSqlWhere := stSqlWhere || ' AND contrato_servidor_local.timestamp = (  SELECT timestamp
                                                                                   FROM pessoal'||stEntidade||'.contrato_servidor_local as contrato_servidor_local_interna
                                                                                  WHERE contrato_servidor_local_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                    AND contrato_servidor_local_interna.cod_contrato = contrato_servidor_local.cod_contrato
                                                                               ORDER BY timestamp desc
                                                                                  LIMIT 1
                                                                                  )';

    END IF;
--     IF stTipoFiltro = 'sub_divisao' OR stTipoFiltro = 'sub_divisao_grupo' THEN
--         stSql := stSql || ' WHERE contrato_servidor.cod_sub_divisao IN ('||stValoresFiltro||')';
--     END IF;
    IF stTipoFiltro = 'sub_divisao_funcao' OR stTipoFiltro = 'sub_divisao_funcao_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                    ON contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao IN ('||stValoresFiltro||')';

        stSqlWhere := stSqlWhere || ' AND contrato_servidor_sub_divisao_funcao.timestamp = ( SELECT timestamp
                                                                                               FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao as contrato_servidor_sub_divisao_funcao_interna
                                                                                              WHERE contrato_servidor_sub_divisao_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                                AND contrato_servidor_sub_divisao_funcao_interna.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                                                                           ORDER BY timestamp desc
                                                                                              LIMIT 1
                                                                                            )';

    END IF;

    IF stTipoFiltro = 'atributo_pensionista' OR stTipoFiltro = 'atributo_pensionista_grupo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.atributo_contrato_pensionista
                                    ON atributo_contrato_pensionista.cod_contrato = contrato.cod_contrato
                                   AND atributo_contrato_pensionista.cod_atributo = '||arValoresFiltro[2];
        
        IF arValoresFiltro[1] = '1' THEN
            stSql := stSql || '        AND atributo_contrato_pensionista.valor IN ('||arValoresFiltro[3]||')';
        ELSE
            stSql := stSql || '        AND atributo_contrato_pensionista.valor = '''||arValoresFiltro[3]||'''';
        END IF;

        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql || '
                            INNER JOIN administracao.atributo_valor_padrao
                                    ON atributo_valor_padrao.cod_modulo = atributo_contrato_pensionista.cod_modulo
                                   AND atributo_valor_padrao.cod_cadastro = atributo_contrato_pensionista.cod_cadastro
                                   AND atributo_valor_padrao.cod_atributo = atributo_contrato_pensionista.cod_atributo
                                   AND atributo_valor_padrao.cod_valor = atributo_contrato_pensionista.valor';
        END IF;

        stSqlWhere := stSqlWhere || ' AND atributo_contrato_pensionista.timestamp = (  SELECT timestamp
                                                                                         FROM pessoal'||stEntidade||'.atributo_contrato_pensionista as atributo_contrato_pensionista_interna
                                                                                        WHERE atributo_contrato_pensionista.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                          AND atributo_contrato_pensionista_interna.cod_contrato = atributo_contrato_pensionista.cod_contrato
                                                                                          AND atributo_contrato_pensionista_interna.cod_atributo = atributo_contrato_pensionista.cod_atributo
                                                                                     ORDER BY timestamp desc
                                                                                        LIMIT 1
                                                                                        )';
    END IF;

    IF stTipoFiltro = 'evento' THEN
        IF inCodPeriodoMovimentacao = 0 THEN
            inCodPeriodoMovimentacaoAux := selectIntoInteger('SELECT max(cod_periodo_movimentacao) FROM folhapagamento.periodo_movimentacao');
        ELSE
            inCodPeriodoMovimentacaoAux := inCodPeriodoMovimentacao;
        END IF;

        stSql := stSql || ' INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                                    ON registro_evento_periodo.cod_contrato = contrato.cod_contrato
                                   AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacaoAux||'
                            INNER JOIN folhapagamento'||stEntidade||'.registro_evento
                                    ON registro_evento.cod_registro = registro_evento_periodo.cod_registro
                                   AND registro_evento.cod_evento IN ('||stValoresFiltro||') ';
    END IF;

    stSql := stSql||regexp_replace(stSqlWhere, '^ AND', ' WHERE');
    stSqlWhere := '';

    FOR reContratoPensionista IN EXECUTE stSql LOOP
        rwContratoPensionista.cod_contrato           := reContratoPensionista.cod_contrato;
        rwContratoPensionista.registro               := reContratoPensionista.registro;
        rwContratoPensionista.cod_contrato_cedente   := reContratoPensionista.cod_contrato_cedente;
        rwContratoPensionista.cod_dependencia        := reContratoPensionista.cod_dependencia;
        rwContratoPensionista.cod_pensionista        := reContratoPensionista.cod_pensionista;
        rwContratoPensionista.num_beneficio          := reContratoPensionista.num_beneficio;
        rwContratoPensionista.percentual_pagamento   := reContratoPensionista.percentual_pagamento;
        rwContratoPensionista.dt_inicio_beneficio    := reContratoPensionista.dt_inicio_beneficio;
        rwContratoPensionista.dt_encerramento        := reContratoPensionista.dt_encerramento;
        rwContratoPensionista.motivo_encerramento    := reContratoPensionista.motivo_encerramento;
        rwContratoPensionista.cod_profissao          := reContratoPensionista.cod_profissao;
        rwContratoPensionista.numcgm                 := reContratoPensionista.numcgm;
        rwContratoPensionista.cod_grau               := reContratoPensionista.cod_grau;

        rwContratoPensionista.valor_atributo         := reContratoPensionista.valor_atributo;

        WHILE arConfiguracao[inIndex] IS NOT NULL LOOP
            --DADOS DA TABELA pessoal.contrato_pensionista_conta_salario
            IF arConfiguracao[inIndex] = 'cs' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_pensionista_conta_salario.cod_agencia as cod_agencia_salario
                                   , contrato_pensionista_conta_salario.cod_banco as cod_banco_salario
                                   , contrato_pensionista_conta_salario.nr_conta as nr_conta_salario
                                   , banco.num_banco as num_banco_salario
                                   , banco.nom_banco as nom_banco_salario
                                   , agencia.num_agencia as num_agencia_salario
                                   , agencia.nom_agencia as nom_agencia_salario
                                FROM pessoal'||stEntidade||'.contrato_pensionista_conta_salario
                          INNER JOIN monetario.agencia
                                  ON agencia.cod_banco = contrato_pensionista_conta_salario.cod_banco
                                 AND agencia.cod_agencia = contrato_pensionista_conta_salario.cod_agencia
                          INNER JOIN monetario.banco
                                  ON banco.cod_banco = contrato_pensionista_conta_salario.cod_banco
                               WHERE contrato_pensionista_conta_salario.cod_contrato = '||reContratoPensionista.cod_contrato||'
                                 AND contrato_pensionista_conta_salario.timestamp = ( SELECT timestamp
                                                                                        FROM pessoal'||stEntidade||'.contrato_pensionista_conta_salario as contrato_pensionista_conta_salario_interna
                                                                                       WHERE contrato_pensionista_conta_salario_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                         AND contrato_pensionista_conta_salario_interna.cod_contrato = contrato_pensionista_conta_salario.cod_contrato
                                                                                    ORDER BY timestamp DESC
                                                                                       LIMIT 1 )';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_agencia_salario     := reRegistro.cod_agencia_salario;
                rwContratoPensionista.cod_banco_salario       := reRegistro.cod_banco_salario;
                rwContratoPensionista.nr_conta_salario        := reRegistro.nr_conta_salario;
                rwContratoPensionista.num_banco_salario       := reRegistro.num_banco_salario;
                rwContratoPensionista.nom_banco_salario       := reRegistro.nom_banco_salario;
                rwContratoPensionista.num_agencia_salario     := reRegistro.num_agencia_salario;
                rwContratoPensionista.nom_agencia_salario     := reRegistro.nom_agencia_salario;
            END IF;

            --DADOS DA TABELA pessoal.contrato_pensionista_previdencia
            IF arConfiguracao[inIndex] = 'pr' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_pensionista_previdencia.cod_previdencia
                                FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia
                               WHERE contrato_pensionista_previdencia.timestamp = (  SELECT timestamp
                                                                                       FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia as contrato_pensionista_previdencia_interna
                                                                                      WHERE contrato_pensionista_previdencia_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                        AND contrato_pensionista_previdencia_interna.cod_contrato = contrato_pensionista_previdencia.cod_contrato
                                                                                   ORDER BY timestamp DESC
                                                                                      LIMIT 1 )
                                 AND contrato_pensionista_previdencia.cod_contrato = '||reContratoPensionista.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_previdencia  := reRegistro.cod_previdencia;
            END IF;

            --DADOS DA TABELA pessoal.contrato_pensionista_processo
            IF arConfiguracao[inIndex] = 'pro' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_pensionista_processo.cod_processo
                                FROM pessoal'||stEntidade||'.contrato_pensionista_processo
                               WHERE contrato_pensionista_processo.cod_contrato = '||reContratoPensionista.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_processo  := reRegistro.cod_processo;
            END IF;

            --DADOS DA TABELA pessoal.contrato_pensionista_orgao
            IF arConfiguracao[inIndex] = 'o' OR arConfiguracao[inIndex] = 'oo' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_pensionista_orgao.cod_orgao
                                   , recuperadescricaoorgao(contrato_pensionista_orgao.cod_orgao,'''||stTimestampFechamentoPeriodo||''') as descricao
                                FROM pessoal'||stEntidade||'.contrato_pensionista_orgao
                               WHERE contrato_pensionista_orgao.cod_contrato = '||reContratoPensionista.cod_contrato||'
                                 AND contrato_pensionista_orgao.timestamp = ( SELECT timestamp
                                                                                FROM pessoal'||stEntidade||'.contrato_pensionista_orgao as contrato_pensionista_orgao_interna
                                                                               WHERE contrato_pensionista_orgao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                 AND contrato_pensionista_orgao_interna.cod_contrato = contrato_pensionista_orgao.cod_contrato
                                                                            ORDER BY timestamp DESC
                                                                               LIMIT 1 )';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_orgao  := reRegistro.cod_orgao;
                rwContratoPensionista.desc_orgao := reRegistro.descricao;

                --DADOS DA TABELA organograma.fn_consulta_orgao
                IF arConfiguracao[inIndex] = 'oo' OR arConfiguracao[inIndex] = 'all' THEN
                    inCodOrganograma := selectIntoInteger('SELECT cod_organograma FROM organograma.orgao_nivel WHERE cod_orgao = '||reRegistro.cod_orgao);
                    stSql := 'SELECT organograma.fn_consulta_orgao('||inCodOrganograma||','||reRegistro.cod_orgao||')';
                    rwContratoPensionista.orgao := selectIntoVarchar(stSql);
                END IF;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_local
            IF arConfiguracao[inIndex] = 'l' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_local.cod_local
                                   , local.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_local
                          INNER JOIN organograma.local
                                  ON local.cod_local = contrato_servidor_local.cod_local
                               WHERE contrato_servidor_local.cod_contrato = '||reContratoPensionista.cod_contrato_cedente||'
                                 AND contrato_servidor_local.timestamp = ( SELECT timestamp
                                                                             FROM pessoal'||stEntidade||'.contrato_servidor_local as contrato_servidor_local_interna
                                                                            WHERE contrato_servidor_local_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                              AND contrato_servidor_local_interna.cod_contrato = contrato_servidor_local.cod_contrato
                                                                         ORDER BY timestamp DESC
                                                                            LIMIT 1 ) ';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_local  := reRegistro.cod_local;
                rwContratoPensionista.desc_local := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal.pensionista_cid
            IF arConfiguracao[inIndex] = 'cid' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT pensionista_cid.cod_cid
                                   , pensionista_cid.data_laudo
                                FROM pessoal'||stEntidade||'.pensionista_cid
                               WHERE pensionista_cid.cod_pensionista = '||reContratoPensionista.cod_pensionista;
                
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                
                rwContratoPensionista.cod_cid  := reRegistro.cod_cid;
                rwContratoPensionista.data_laudo  := reRegistro.data_laudo;
            END IF;

            --DADOS DA TABELA cgm
            IF arConfiguracao[inIndex] = 'cgm' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT sw_cgm.nom_cgm
                                   , sw_cgm_pessoa_fisica.servidor_pis_pasep
                                   , sw_cgm_pessoa_fisica.rg
                                   , cpf
                                   , sw_cgm_pessoa_fisica.dt_nascimento
                                FROM sw_cgm
                          INNER JOIN sw_cgm_pessoa_fisica
                                  ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                               WHERE sw_cgm.numcgm = '||reContratoPensionista.numcgm;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.nom_cgm              := reRegistro.nom_cgm;
                rwContratoPensionista.rg                   := reRegistro.rg;
                rwContratoPensionista.cpf                  := reRegistro.cpf;
                rwContratoPensionista.dt_nascimento        := reRegistro.dt_nascimento;
            END IF;

            --################################################################
            --Daqui pra baixo pega informações do servidor
            --através do cod_contrato_cedente

            --DADOS DA TABELA pessoal.contrato_servidor_regime_funcao
            IF arConfiguracao[inIndex] = 'rf' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_regime_funcao.cod_regime as cod_regime_funcao
                                   , regime.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                          INNER JOIN pessoal'||stEntidade||'.regime
                                  ON regime.cod_regime = contrato_servidor_regime_funcao.cod_regime
                               WHERE contrato_servidor_regime_funcao.cod_contrato = '||reContratoPensionista.cod_contrato_cedente||'
                                 AND contrato_servidor_regime_funcao.timestamp = ( SELECT timestamp
                                                                                   FROM pessoal'||stEntidade||'.contrato_servidor_regime_funcao as contrato_servidor_regime_funcao_interna
                                                                                  WHERE contrato_servidor_regime_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                    AND contrato_servidor_regime_funcao_interna.cod_contrato = contrato_servidor_regime_funcao.cod_contrato
                                                                               ORDER BY timestamp DESC
                                                                                  LIMIT 1 )';
                                                                                  
                raise notice '%', stSql;
                
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_regime_funcao  := reRegistro.cod_regime_funcao;
                rwContratoPensionista.desc_regime_funcao := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal.contrato_servidor_sub_divisao_funcao
            IF arConfiguracao[inIndex] = 'sf' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_sub_divisao_funcao.cod_sub_divisao as cod_sub_divisao_funcao
                                   , sub_divisao.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                          INNER JOIN pessoal'||stEntidade||'.sub_divisao
                                  ON sub_divisao.cod_sub_divisao = contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                               WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = '||reContratoPensionista.cod_contrato_cedente||'
                                 AND contrato_servidor_sub_divisao_funcao.timestamp = ( SELECT timestamp
                                                                                          FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao as contrato_servidor_sub_divisao_funcao_interna
                                                                                         WHERE contrato_servidor_sub_divisao_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                           AND contrato_servidor_sub_divisao_funcao_interna.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                                                                      ORDER BY timestamp DESC
                                                                                         LIMIT 1 ) ';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_sub_divisao_funcao  := reRegistro.cod_sub_divisao_funcao;
                rwContratoPensionista.desc_sub_divisao_funcao := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal.contrato_servidor_funcao
            IF arConfiguracao[inIndex] = 'f' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_funcao.cod_cargo as cod_funcao
                                   , cargo.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                          INNER JOIN pessoal'||stEntidade||'.cargo
                                  ON cargo.cod_cargo = contrato_servidor_funcao.cod_cargo
                               WHERE contrato_servidor_funcao.cod_contrato = '||reContratoPensionista.cod_contrato_cedente||'
                                 AND contrato_servidor_funcao.timestamp = ( SELECT timestamp
                                                                              FROM pessoal'||stEntidade||'.contrato_servidor_funcao as contrato_servidor_funcao_interna
                                                                             WHERE contrato_servidor_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                               AND contrato_servidor_funcao_interna.cod_contrato = contrato_servidor_funcao.cod_contrato
                                                                          ORDER BY timestamp DESC
                                                                             LIMIT 1 )';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_funcao  := reRegistro.cod_funcao;
                rwContratoPensionista.desc_funcao := reRegistro.descricao;


                stSql := ' SELECT * FROM
                         (   SELECT cargo.cod_cargo
                                 , cbo.codigo as cbo_codigo
                                 , cargo.descricao
                              FROM pessoal'||stEntidade||'.cargo
                                 , pessoal'||stEntidade||'.cbo_cargo
                                 , pessoal'||stEntidade||'.cbo
                             WHERE cargo.cod_cargo = cbo_cargo.cod_cargo
                               AND cbo_cargo.timestamp = ( SELECT timestamp
                                                             FROM pessoal'||stEntidade||'.cbo_cargo as cbo_cargo_interna
                                                            WHERE cbo_cargo_interna.cod_cargo = cbo_cargo.cod_cargo
                                                         ORDER BY timestamp desc
                                                            LIMIT 1 )
                               AND cbo_cargo.cod_cbo = cbo.cod_cbo
                            UNION
                            SELECT cargo.cod_cargo
                                 , cbo.codigo as cbo_codigo
                                 , cargo.descricao
                              FROM pessoal'||stEntidade||'.cargo
                                 , pessoal'||stEntidade||'.especialidade
                                 , pessoal'||stEntidade||'.cbo_especialidade
                                 , pessoal'||stEntidade||'.cbo
                             WHERE cargo.cod_cargo = especialidade.cod_cargo
                               AND especialidade.cod_especialidade = cbo_especialidade.cod_especialidade
                               AND cbo_especialidade.timestamp = (  SELECT timestamp
                                                                      FROM pessoal'||stEntidade||'.cbo_especialidade as cbo_especialidade_interna
                                                                     WHERE cbo_especialidade_interna.cod_especialidade = cbo_especialidade.cod_especialidade
                                                                  ORDER BY timestamp desc
                                                                      LIMIT 1 )
                               AND cbo_especialidade.cod_cbo = cbo.cod_cbo) as funcao
                    WHERE funcao.cod_cargo = '||reRegistro.cod_funcao;


                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.desc_cbo_funcao := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal.contrato_servidor_especialidade_funcao
            IF arConfiguracao[inIndex] = 'ef' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_especialidade_funcao.cod_especialidade as cod_especialidade_funcao
                                   , especialidade.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
                          INNER JOIN pessoal'||stEntidade||'.especialidade
                                  ON especialidade.cod_especialidade = contrato_servidor_especialidade_funcao.cod_especialidade
                               WHERE contrato_servidor_especialidade_funcao.cod_contrato = '||reContratoPensionista.cod_contrato_cedente||'
                                 AND contrato_servidor_especialidade_funcao.timestamp = ( SELECT timestamp
                                                                                          FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao as contrato_servidor_especialidade_funcao_interna
                                                                                         WHERE contrato_servidor_especialidade_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                           AND contrato_servidor_especialidade_funcao_interna.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato
                                                                                           ORDER BY timestamp desc
                                                                                           LIMIT 1
                                                                                       )';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_especialidade_funcao  := reRegistro.cod_especialidade_funcao;
                rwContratoPensionista.desc_especialidade_funcao := reRegistro.descricao;
            END IF;


            inIndex := inIndex + 1;
        END LOOP;
        inIndex := 1;

        RETURN NEXT rwContratoPensionista;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

