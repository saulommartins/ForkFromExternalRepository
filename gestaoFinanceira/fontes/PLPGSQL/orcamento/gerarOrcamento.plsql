/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos (urbem@cnm.org.br)      *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo  sob *
    * os termos da Licença Pública Geral GNU conforme publicada pela  Free  Software *
    * Foundation; tanto a versão 2 da Licença, como (a seu critério) qualquer versão *
    * posterior.                                                                     *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral  do  GNU  junto  com *
    * este programa; se não, escreva para  a  Free  Software  Foundation,  Inc.,  no *
    * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.               *
    *                                                                                *
    **********************************************************************************
*/
/*
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: gerarOrcamento.plsql 66416 2016-08-25 19:46:03Z franver $
*
* Casos de uso: uc-02.01.31
*/

CREATE OR REPLACE FUNCTION orcamento.fn_gerar_orcamento(VARCHAR, VARCHAR[], VARCHAR) RETURNS BOOLEAN AS $$
DECLARE
    varExercicio                 ALIAS FOR $1;
    aParametros                  ALIAS FOR $2;
    stFiltro                     ALIAS FOR $3;

    varProximoExercicio          VARCHAR;
    varExercicioExiste           INTEGER := 0;

    bolRetorno                   BOOLEAN := true;
    
    varTabela                    VARCHAR;
    numAux                       NUMERIC(14,2);

    varSql                       VARCHAR;
    varSql2                      VARCHAR;
    varParametro                 VARCHAR;

    intQtdParametros             INTEGER;
    intLoop                      INTEGER;
    
    bolRecursoDestinacao         BOOLEAN := false;
    bolEntidadeReplicada         BOOLEAN := false;

    bolReceita                   BOOLEAN := false;
    bolZerarReceita              BOOLEAN := false;
    bolPrevisaoReceita           BOOLEAN := false;
    
    bolDespesa                   BOOLEAN := false;
    bolZerarDespesa              BOOLEAN := false;
    bolPrevisaoDespesa           BOOLEAN := false;
    
    stSql                        VARCHAR := '';
    stSqlAux                     VARCHAR := '';
    
    inCodDespesaNovo             INTEGER := 0;

    recRegistro                  RECORD;
    
    recRegistroAux               RECORD;
    
BEGIN

    varProximoExercicio := BTRIM(TO_CHAR(TO_NUMBER(varExercicio,'9999') + 1, '9999'));
    varExercicioExiste  := orcamento.fn_verifica_exercicio(varExercicio);

    IF (varExercicioExiste > 0) THEN
        bolRetorno := false;
    ELSE

        --
        -- TABELA ORCAMENTO.PREVISAO_ORCAMENTARIA
        --
        INSERT INTO orcamento.previsao_orcamentaria (exercicio) VALUES ( varProximoExercicio);
    
        --
        -- TABELA EMPENHO.HISTORICO
        --
        FOR recRegistro IN SELECT * FROM empenho.historico WHERE exercicio = varExercicio
        LOOP
            INSERT INTO empenho.historico (cod_historico, exercicio, nom_historico) VALUES (recRegistro.cod_historico, varProximoExercicio, recRegistro.nom_historico);
        END LOOP;
    
        --
        -- TABELA CONTABILIDADE.HISTORICO_CONTABIL
        --
        FOR recRegistro IN SELECT * FROM  contabilidade.historico_contabil WHERE exercicio =  varExercicio
        LOOP
            INSERT INTO contabilidade.historico_contabil (cod_historico, exercicio, nom_historico, complemento) VALUES ( recRegistro.cod_historico , varProximoExercicio, recRegistro.nom_historico, recRegistro.complemento);
        END LOOP;
    
        --
        -- TABELA CONTABILIDADE.TIPO_TRANSFERENCIA
        --
        FOR recRegistro IN SELECT * FROM contabilidade.tipo_transferencia WHERE exercicio = varExercicio
        LOOP
            INSERT INTO contabilidade.tipo_transferencia (cod_tipo, exercicio, nom_tipo, lancamento_contabil) VALUES ( recRegistro.cod_tipo ,varProximoExercicio, recRegistro.nom_tipo, recRegistro.lancamento_contabil);
        END LOOP;
    
        --
        -- TABELA ORCAMENTO.ENTIDADE
        --
        -- Verifica se já não foram replicadas as entidades na rotina Orçamento :: Configuração :: Replicar Entidade
        SELECT CASE WHEN entidade.exercicio IS NOT NULL
                    THEN true 
                    ELSE false 
                END AS bo_entidade
               INTO bolEntidadeReplicada 
          FROM orcamento.entidade 
         WHERE entidade.exercicio = varProximoExercicio 
      GROUP BY entidade.exercicio;
    
        IF (bolEntidadeReplicada IS NULL) THEN
            FOR recRegistro IN SELECT * FROM orcamento.entidade WHERE exercicio = varExercicio
            LOOP
                INSERT INTO orcamento.entidade (exercicio, cod_entidade, numcgm, cod_responsavel, cod_resp_tecnico, cod_profissao) VALUES ( varProximoExercicio, recRegistro.cod_entidade, recRegistro.numcgm, recRegistro.cod_responsavel, recRegistro.cod_resp_tecnico, recRegistro.cod_profissao );
            END LOOP;
    
            --
            -- TABELA ORCAMENTO.USUARIO_ENTIDADE
            --
            FOR recRegistro IN SELECT * FROM orcamento.usuario_entidade WHERE exercicio = varExercicio
            LOOP
                INSERT INTO orcamento.usuario_entidade (exercicio, numcgm, cod_entidade) VALUES ( varProximoExercicio, recRegistro.numcgm, recRegistro.cod_entidade );
            END LOOP;
        END IF;
    
        --
        -- TABELA ORCAMENTO.POSICAO_RECEITA
        --
        FOR recRegistro IN SELECT * FROM orcamento.posicao_receita WHERE exercicio = varExercicio
        LOOP
            INSERT INTO orcamento.posicao_receita (exercicio, cod_posicao, mascara, cod_tipo) VALUES ( varProximoExercicio, recRegistro.cod_posicao ,recRegistro.mascara, recRegistro.cod_tipo);
        END LOOP;
    
        --
        -- TABELA ORCAMENTO.CONTA_RECEITA
        --
        FOR recRegistro IN SELECT * FROM orcamento.conta_receita WHERE exercicio = varExercicio
        LOOP
            INSERT INTO orcamento.conta_receita (exercicio, cod_conta, cod_norma, descricao, cod_estrutural) VALUES ( varProximoExercicio, recRegistro.cod_conta, recRegistro.cod_norma ,recRegistro.descricao, recRegistro.cod_estrutural);
        END LOOP;
    
        --
        -- TABELA ORCAMENTO.CLASSIFICACAO_RECEITA
        --
        FOR recRegistro IN SELECT * FROM orcamento.classificacao_receita WHERE exercicio = varExercicio
        LOOP
            INSERT INTO orcamento.classificacao_receita (exercicio, cod_posicao, cod_conta, cod_classificacao, cod_tipo) VALUES ( varProximoExercicio, recRegistro.cod_posicao, recRegistro.cod_conta, recRegistro.cod_classificacao, recRegistro.cod_tipo );
        END LOOP;
    
        --
        -- TABELA ORCAMENTO.POSICAO_DESPESA
        --
        FOR recRegistro IN SELECT * FROM orcamento.posicao_despesa WHERE exercicio = varExercicio
        LOOP
            INSERT INTO orcamento.posicao_despesa (exercicio, cod_posicao, mascara) VALUES ( varProximoExercicio, recRegistro.cod_posicao ,recRegistro.mascara);
        END LOOP;
    
        --
        -- TABELA ORCAMENTO.CONTA_DESPESA
        --
        FOR recRegistro IN SELECT * FROM orcamento.conta_despesa WHERE exercicio = varExercicio
        LOOP
            INSERT INTO orcamento.conta_despesa (exercicio, cod_conta, descricao, cod_estrutural) VALUES ( varProximoExercicio, recRegistro.cod_conta ,recRegistro.descricao, recRegistro.cod_estrutural);
        END LOOP;
    
        --
        -- TABELA ORCAMENTO.CLASSIFICACAO_DESPESA
        --
        FOR recRegistro IN SELECT * FROM orcamento.classificacao_despesa WHERE exercicio = varExercicio
        LOOP
            INSERT INTO orcamento.classificacao_despesa (exercicio, cod_conta, cod_posicao, cod_classificacao) VALUES ( varProximoExercicio, recRegistro.cod_conta, recRegistro.cod_posicao, recRegistro.cod_classificacao );
        END LOOP;
          
        --
        -- TABELA ORCAMENTO.RECURSO
        --
        FOR recRegistro IN SELECT * FROM orcamento.recurso WHERE exercicio = varExercicio
        LOOP
            SELECT * INTO recRegistroAux FROM orcamento.recurso WHERE exercicio = varProximoExercicio AND cod_recurso = recRegistro.cod_recurso;
     
            IF (recRegistroAux.cod_recurso = NULL) THEN
                INSERT INTO orcamento.recurso (exercicio, cod_recurso, cod_fonte, nom_recurso) VALUES ( varProximoExercicio , recRegistro.cod_recurso , recRegistro.cod_fonte , recRegistro.nom_recurso);
            END IF;
        END LOOP;
            
        --
        -- TABELA ORCAMENTO.ORGAO
        --
        FOR recRegistro IN SELECT * FROM orcamento.orgao WHERE exercicio = varExercicio
        LOOP
            SELECT * INTO recRegistroAux FROM orcamento.orgao WHERE exercicio = varProximoExercicio AND num_orgao = recRegistro.num_orgao;
            IF (recRegistroAux.num_orgao = NULL) THEN
                INSERT INTO orcamento.orgao (exercicio, num_orgao , nom_orgao , usuario_responsavel) VALUES ( varProximoExercicio , recRegistro.num_orgao , recRegistro.nom_orgao , recRegistro.usuario_responsavel);
            END IF;
        END LOOP;
                  
        --
        -- TABELA ORCAMENTO.UNIDADE
        --
        FOR recRegistro IN SELECT * FROM orcamento.unidade WHERE exercicio = varExercicio
        LOOP
            SELECT * INTO recRegistroAux FROM orcamento.unidade WHERE exercicio = varProximoExercicio AND num_unidade = recRegistro.num_unidade AND num_orgao = recRegistro.num_orgao;
            IF (recRegistroAux.num_unidade = NULL AND recRegistroAux.num_orgao = NULL) THEN
                INSERT INTO orcamento.unidade (exercicio, num_unidade , num_orgao , nom_unidade, usuario_responsavel) VALUES ( varProximoExercicio , recRegistro.num_unidade , recRegistro.num_orgao , recRegistro.nom_unidade, recRegistro.usuario_responsavel);
            END IF;
        END LOOP;
          
        --
        -- TABELA ORCAMENTO.PAO
        --
        FOR recRegistro IN SELECT * FROM orcamento.pao WHERE exercicio = varExercicio
        LOOP
            SELECT * INTO recRegistroAux FROM orcamento.pao WHERE exercicio = varProximoExercicio AND num_pao = recRegistro.num_pao;        
            IF ( recRegistroAux.exercicio IS NULL AND recRegistroAux.num_pao IS NULL ) THEN
                INSERT INTO orcamento.pao (exercicio, num_pao , nom_pao , detalhamento) VALUES ( varProximoExercicio , recRegistro.num_pao , recRegistro.nom_pao , recRegistro.detalhamento);
            END IF;
        END LOOP;
    
        --
        -- TABELA ORCAMENTO.PAO_PPA_ACAO
        --
        FOR recRegistro IN SELECT * FROM orcamento.pao_ppa_acao WHERE exercicio = varExercicio
        LOOP
            SELECT * INTO recRegistroAux FROM orcamento.pao_ppa_acao WHERE exercicio = varProximoExercicio AND num_pao = recRegistro.num_pao AND cod_acao = recRegistro.cod_acao;
            IF ( recRegistroAux.exercicio IS NULL AND recRegistroAux.num_pao IS NULL AND recRegistroAux.cod_acao IS NULL ) THEN
                INSERT INTO orcamento.pao_ppa_acao (exercicio, num_pao , cod_acao) VALUES ( varProximoExercicio , recRegistro.num_pao , recRegistro.cod_acao );
            END IF;
        END LOOP;
    
        --
        -- TABELA EMPENHO.PERMISSAO_AUTORIZACAO
        --
        FOR recRegistro IN SELECT * FROM empenho.permissao_autorizacao WHERE exercicio = varExercicio
        LOOP
            INSERT INTO empenho.permissao_autorizacao (numcgm, num_unidade, num_orgao, exercicio) VALUES ( recRegistro.numcgm, recRegistro.num_unidade, recRegistro.num_orgao ,varProximoExercicio );
        END LOOP;
                                
        --
        -- TABELA ADMINISTRACAO.CONFIGURACAO
        --
        FOR recRegistro IN SELECT * FROM administracao.configuracao WHERE exercicio = varExercicio AND cod_modulo IN (8,9,10)
        LOOP
            SELECT * INTO recRegistroAux FROM administracao.configuracao WHERE exercicio = varProximoExercicio AND cod_modulo = recRegistro.cod_modulo AND parametro = recRegistro.parametro;
            IF ( recRegistroAux.exercicio IS NULL AND recRegistroAux.cod_modulo IS NULL AND recRegistroAux.parametro IS NULL ) THEN
                INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES (varProximoExercicio, recRegistro.cod_modulo, recRegistro.parametro, recRegistro.valor);
            END IF;
        END LOOP;
                
        /*----------------------------------------------*/
        /*   TABELAS REFERENTES AS OPÇÕES INFORMADAS    */
        /*----------------------------------------------*/
        intQtdParametros := Coalesce(array_upper(aParametros, 1 ),0);
    
        FOR intLoop IN 1..intQtdParametros LOOP
            IF aParametros[intLoop]  = 'receita'                THEN bolReceita         := true; END IF;
            IF (aParametros[intLoop] = 'zerar_receita')         THEN bolZerarReceita    := true; END IF;
            IF aParametros[intLoop]  = 'despesa'                THEN bolDespesa         := true; END IF;
            IF (aParametros[intLoop] = 'zerar_despesa')         THEN bolZerarDespesa    := true; END IF;
            IF aParametros[intLoop]  = 'metas_arrecadacao'      THEN bolPrevisaoReceita := true; END IF;
            IF aParametros[intLoop]  = 'metas_execucao_despesa' THEN bolPrevisaoDespesa := true; END IF;
        END LOOP;
    
        IF bolReceita = true THEN
            --
            -- TABELA ORCAMENTO.RECEITA
            --
            FOR recRegistro IN SELECT * FROM orcamento.receita WHERE exercicio = varExercicio
            LOOP
                numAux := CASE WHEN bolZerarReceita THEN 0 ELSE recRegistro.vl_original  END;
                INSERT INTO orcamento.receita (exercicio, cod_receita, cod_conta, cod_entidade, cod_recurso, dt_criacao, vl_original) VALUES ( varProximoExercicio, recRegistro.cod_receita, recRegistro.cod_conta, recRegistro.cod_entidade, recRegistro.cod_recurso ,current_date, numAux);
            END LOOP;
    
            --
            -- TABELA CONTABILIDADE.DESDOBRAMENTO_RECEITA
            --
            FOR recRegistro IN SELECT * FROM contabilidade.desdobramento_receita WHERE exercicio = varExercicio
            LOOP
                INSERT INTO contabilidade.desdobramento_receita (exercicio, cod_receita_principal, cod_receita_secundaria, percentual) VALUES ( varProximoExercicio, recRegistro.cod_receita_principal, recRegistro.cod_receita_secundaria, recRegistro.percentual);
            END LOOP;
        END IF;
    
        IF bolPrevisaoReceita = true THEN
            --
            --
            -- TABELA ORCAMENTO.PREVISAO_RECEITA
            --
            FOR recRegistro IN SELECT * FROM orcamento.previsao_receita WHERE exercicio = varExercicio
            LOOP
                INSERT INTO orcamento.previsao_receita (exercicio, cod_receita, periodo, vl_periodo) VALUES ( varProximoExercicio, recRegistro.cod_receita, recRegistro.periodo, recRegistro.vl_periodo);
            END LOOP;
           
            bolRetorno := true;
        END IF;
    
        --
        -- TABELA ORCAMENTO.DESPESA
        --
        IF bolDespesa = true THEN
            stSql := '
              SELECT DISTINCT despesa.cod_despesa
                   , despesa.cod_conta
                   , despesa.cod_entidade
                   , despesa.cod_recurso
                   , despesa.cod_programa
                   , despesa.cod_funcao
                   , despesa.cod_subfuncao
                   , despesa.num_pao
                   , despesa.num_orgao
                   , despesa.num_unidade
                   , despesa.vl_original
                   , acao.cod_acao
                FROM ppa.acao
          INNER JOIN ppa.acao_dados
                  ON acao.cod_acao                    = acao_dados.cod_acao
                 AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
          INNER JOIN ldo.acao_validada
                  ON acao_dados.cod_acao             = acao_validada.cod_acao
                 AND acao_dados.timestamp_acao_dados = acao_validada.timestamp_acao_dados
          INNER JOIN ppa.programa
                  ON acao.cod_programa = programa.cod_programa
          INNER JOIN ppa.programa_setorial
                  ON programa.cod_setorial = programa_setorial.cod_setorial
          INNER JOIN ppa.macro_objetivo
                  ON programa_setorial.cod_macro = macro_objetivo.cod_macro
          INNER JOIN ppa.ppa
                  ON macro_objetivo.cod_ppa = ppa.cod_ppa
          INNER JOIN orcamento.programa_ppa_programa
                  ON programa_ppa_programa.cod_programa_ppa = ppa.programa.cod_programa
          INNER JOIN orcamento.programa as OP
                  ON OP.exercicio    = programa_ppa_programa.exercicio
                 AND OP.cod_programa = programa_ppa_programa.cod_programa
          INNER JOIN orcamento.despesa_acao
                  ON despesa_acao.exercicio_despesa = '''||varExercicio||'''
                 AND despesa_acao.cod_acao          = acao_validada.cod_acao
          INNER JOIN orcamento.despesa
                  ON despesa.exercicio      = despesa_acao.exercicio_despesa
                 AND despesa.cod_despesa    = despesa_acao.cod_despesa
              '|| stFiltro ||'                    
                 AND despesa.exercicio = '|| quote_literal(varExercicio) ||'
            ORDER BY despesa.cod_despesa;
            ';
    
            FOR recRegistro IN EXECUTE stSql
            LOOP
                inCodDespesaNovo := inCodDespesaNovo + 1;
                numAux := CASE WHEN bolZerarDespesa THEN 0 ELSE recRegistro.vl_original  END;
                INSERT
                  INTO orcamento.despesa
                     ( exercicio
                     , cod_despesa
                     , cod_conta
                     , cod_entidade
                     , cod_recurso
                     , cod_programa
                     , num_pao
                     , num_orgao
                     , num_unidade
                     , cod_funcao
                     , cod_subfuncao
                     , dt_criacao
                     , vl_original )
                VALUES
                     ( varProximoExercicio
                     , inCodDespesaNovo
                     , recRegistro.cod_conta
                     , recRegistro.cod_entidade
                     , recRegistro.cod_recurso
                     , recRegistro.cod_programa 
                     , recRegistro.num_pao
                     , recRegistro.num_orgao
                     , recRegistro.num_unidade
                     , recRegistro.cod_funcao
                     , recRegistro.cod_subfuncao
                     , current_date
                     , numAux );
                
                --
                -- TABELA ORCAMENTO.DESPESA_ACAO
                --                                        
                INSERT
                  INTO orcamento.despesa_acao
                     ( exercicio_despesa
                     , cod_acao
                     , cod_despesa
                     )
                VALUES
                     ( varProximoExercicio
                     , recRegistro.cod_acao
                     , inCodDespesaNovo
                     );
    
                --
                -- TABELA ORCAMENTO.PREVISAO_DESPESA
                --
                IF bolPrevisaoDespesa = true THEN
                    stSqlAux := '
                      SELECT DISTINCT previsao_despesa.exercicio
                           , previsao_despesa.cod_despesa
                           , previsao_despesa.periodo
                           , previsao_despesa.vl_previsto
                        FROM ppa.acao
                  INNER JOIN ppa.acao_dados
                          ON acao.cod_acao                    = acao_dados.cod_acao
                         AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
                  INNER JOIN ldo.acao_validada
                          ON acao_dados.cod_acao             = acao_validada.cod_acao
                         AND acao_dados.timestamp_acao_dados = acao_validada.timestamp_acao_dados
                  INNER JOIN ppa.programa
                          ON acao.cod_programa = programa.cod_programa
                  INNER JOIN ppa.programa_setorial
                          ON programa.cod_setorial = programa_setorial.cod_setorial
                  INNER JOIN ppa.macro_objetivo
                          ON programa_setorial.cod_macro = macro_objetivo.cod_macro
                  INNER JOIN ppa.ppa
                          ON macro_objetivo.cod_ppa = ppa.cod_ppa
                  INNER JOIN orcamento.programa_ppa_programa
                          ON programa_ppa_programa.cod_programa_ppa = ppa.programa.cod_programa
                  INNER JOIN orcamento.programa as OP
                          ON OP.exercicio        = programa_ppa_programa.exercicio
                         AND OP.cod_programa     = programa_ppa_programa.cod_programa
                  INNER JOIN orcamento.despesa_acao
                          ON despesa_acao.exercicio_despesa  = '''||varExercicio||'''
                         AND despesa_acao.cod_acao           = acao_validada.cod_acao
                  INNER JOIN orcamento.despesa
                          ON despesa.exercicio      = despesa_acao.exercicio_despesa
                         AND despesa.cod_despesa    = despesa_acao.cod_despesa
                  INNER JOIN orcamento.previsao_despesa
                          ON previsao_despesa.exercicio    = despesa.exercicio
                         AND previsao_despesa.cod_despesa = '||recRegistro.cod_despesa||'
                      '|| stFiltro ||'
                         AND despesa.exercicio = '''||varExercicio||'''
                    ';
    
                    FOR recRegistroAux IN EXECUTE stSqlAux
                    LOOP
                        INSERT INTO orcamento.previsao_despesa (exercicio, cod_despesa, periodo, vl_previsto) VALUES ( varProximoExercicio, inCodDespesaNovo, recRegistroAux.periodo, recRegistroAux.vl_previsto);
                    END LOOP;
                    bolRetorno := true;   
                END IF;
            END LOOP;    
        END IF;
    END IF;
RETURN bolRetorno;

END;
$$ LANGUAGE 'plpgsql';

