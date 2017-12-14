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
   $Id: recuperarContratoServidor.plsql 65896 2016-06-24 20:14:24Z michel $
*/
CREATE OR REPLACE FUNCTION recuperarContratoServidor(VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasContratoServidor AS $$
DECLARE
    stConfiguracao                  ALIAS FOR $1;
    stEntidade                      ALIAS FOR $2;
    inCodPeriodoMovimentacao        ALIAS FOR $3;
    stTipoFiltro                    ALIAS FOR $4;
    stValoresFiltro                 ALIAS FOR $5;
    stExercicio                     ALIAS FOR $6;
    rwContratoServidor              colunasContratoServidor%ROWTYPE;
    stSql                           VARCHAR;
    stSqlAux                        VARCHAR;
    inCodTipoAtributo               INTEGER;
    stTimestampFechamentoPeriodo    VARCHAR;
    stCodigos                       VARCHAR;
    stContagemTempo                 VARCHAR;
    reContratoServidor              RECORD;
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
               WHERE parametro = '|| quote_literal('dtContagemInicial'|| stEntidade) ||' AND exercicio = '|| quote_literal(stExercicio) ||' ';
    stContagemTempo := selectIntoVarchar(stSql);

    stSql := '    SELECT contrato_servidor.*
                       , contrato.registro
                       , servidor.*';
                       
    IF stTipoFiltro = 'atributo_servidor' OR stTipoFiltro = 'atributo_servidor_grupo' OR stTipoFiltro = 'atributo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');
        
        --Verifica o tipo do atributo 
        --TODO - Pegar o cod_modulo e cod_cadastro vindos do parametro stValoresFiltro. atualmente so é passado o cod_atributo
        stSqlAux := 'SELECT cod_tipo FROM administracao.atributo_dinamico WHERE cod_modulo = 22 AND cod_cadastro = 5 AND cod_atributo = '||arValoresFiltro[2];
        
        inCodTipoAtributo := selectIntoInteger(stSqlAux);
        
        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql || ', atributo_valor_padrao.valor_padrao as valor_atributo';
        ELSE
            stSql := stSql || ', atributo_contrato_servidor_valor.valor as valor_atributo';
        END IF;
    ELSE
        stSql := stSql || ', ''''::varchar as valor_atributo';
    END IF;          
    
    stSql := stSql || '
                    FROM pessoal'||stEntidade||'.contrato_servidor 
              INNER JOIN pessoal'||stEntidade||'.contrato
                      ON contrato.cod_contrato = contrato_servidor.cod_contrato
              INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                      ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
              INNER JOIN pessoal'||stEntidade||'.servidor
                      ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor';

    IF stTipoFiltro = 'reg_sub_fun_esp' OR stTipoFiltro = 'reg_sub_fun_esp_grupo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                                    ON contrato_servidor_regime_funcao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_regime_funcao.cod_regime IN ('||arValoresFiltro[1]||')
                            INNER JOIN (  SELECT contrato_servidor_regime_funcao.cod_contrato
                                               , max(contrato_servidor_regime_funcao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                                           WHERE contrato_servidor_regime_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_regime_funcao.cod_contrato) as max_contrato_servidor_regime_funcao
                                    ON max_contrato_servidor_regime_funcao.cod_contrato = contrato_servidor_regime_funcao.cod_contrato
                                   AND max_contrato_servidor_regime_funcao.timestamp = contrato_servidor_regime_funcao.timestamp
                            INNER JOIN pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                    ON contrato_servidor_sub_divisao_funcao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao IN ('||arValoresFiltro[2]||')
                            INNER JOIN (  SELECT contrato_servidor_sub_divisao_funcao.cod_contrato
                                               , max(contrato_servidor_sub_divisao_funcao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                           WHERE contrato_servidor_sub_divisao_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_sub_divisao_funcao.cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                    ON max_contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                   AND max_contrato_servidor_sub_divisao_funcao.timestamp = contrato_servidor_sub_divisao_funcao.timestamp
                            INNER JOIN pessoal'||stEntidade||'.contrato_servidor_funcao
                                    ON contrato_servidor_funcao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_funcao.cod_cargo IN ('||arValoresFiltro[3]||')
                            INNER JOIN (  SELECT contrato_servidor_funcao.cod_contrato
                                               , max(contrato_servidor_funcao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                                           WHERE contrato_servidor_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_funcao.cod_contrato) as max_contrato_servidor_funcao
                                    ON max_contrato_servidor_funcao.cod_contrato = contrato_servidor_funcao.cod_contrato
                                   AND max_contrato_servidor_funcao.timestamp = contrato_servidor_funcao.timestamp';
        IF trim(arValoresFiltro[4]) != '' THEN
            stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
                                        ON contrato_servidor_especialidade_funcao.cod_contrato = contrato.cod_contrato
                                       AND contrato_servidor_especialidade_funcao.cod_especialidade IN ('||arValoresFiltro[4]||')
                                INNER JOIN (  SELECT contrato_servidor_especialidade_funcao.cod_contrato
                                                   , max(contrato_servidor_especialidade_funcao.timestamp) as timestamp
                                                FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
                                               WHERE contrato_servidor_especialidade_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                            GROUP BY contrato_servidor_especialidade_funcao.cod_contrato) as max_contrato_servidor_especialidade_funcao
                                        ON max_contrato_servidor_especialidade_funcao.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato
                                       AND max_contrato_servidor_especialidade_funcao.timestamp = contrato_servidor_especialidade_funcao.timestamp';
        END IF;
    END IF;
    IF stTipoFiltro = 'reg_sub_car_esp' OR stTipoFiltro = 'reg_sub_car_esp_grupo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');
        IF trim(arValoresFiltro[4]) != '' THEN
            stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_especialidade_cargo
                                        ON contrato_servidor_especialidade_cargo.cod_contrato = contrato.cod_contrato
                                       AND contrato_servidor_especialidade_cargo.cod_especialidade IN ('||arValoresFiltro[4]||')';
        END IF;
        stSql := stSql || ' WHERE contrato_servidor.cod_regime IN ('||arValoresFiltro[1]||')
                              AND contrato_servidor.cod_sub_divisao IN ('||arValoresFiltro[2]||')
                              AND contrato_servidor.cod_cargo IN ('||arValoresFiltro[3]||')';
    END IF;
    
    IF stTipoFiltro = 'contrato'                OR
       stTipoFiltro = 'contrato_todos'          OR
       stTipoFiltro = 'contrato_rescisao'       OR
       stTipoFiltro = 'contrato_aposentado'     OR
       stTipoFiltro = 'cgm_contrato'            OR
       stTipoFiltro = 'cgm_contrato_aposentado' OR
       stTipoFiltro = 'cgm_contrato_rescisao'   OR
       stTipoFiltro = 'cgm_contrato_todos'      THEN
        stSql := stSql || ' WHERE contrato.cod_contrato IN ('||stValoresFiltro||')';
    END IF;
    
    IF stTipoFiltro = 'lotacao' OR stTipoFiltro = 'lotacao_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_orgao
                                    ON contrato_servidor_orgao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_orgao.cod_orgao IN ('||stValoresFiltro||')
                            INNER JOIN (  SELECT contrato_servidor_orgao.cod_contrato
                                               , max(contrato_servidor_orgao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_orgao
                                           WHERE contrato_servidor_orgao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_orgao.cod_contrato) as max_contrato_servidor_orgao
                                    ON max_contrato_servidor_orgao.cod_contrato = contrato_servidor_orgao.cod_contrato
                                   AND max_contrato_servidor_orgao.timestamp = contrato_servidor_orgao.timestamp';
    END IF;
    IF stTipoFiltro = 'local' OR stTipoFiltro = 'local_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_local
                                    ON contrato_servidor_local.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_local.cod_local IN ('||stValoresFiltro||')
                            INNER JOIN (  SELECT contrato_servidor_local.cod_contrato
                                               , max(contrato_servidor_local.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_local
                                           WHERE contrato_servidor_local.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_local.cod_contrato) as max_contrato_servidor_local
                                    ON max_contrato_servidor_local.cod_contrato = contrato_servidor_local.cod_contrato
                                   AND max_contrato_servidor_local.timestamp = contrato_servidor_local.timestamp';
    END IF;
    IF stTipoFiltro = 'sub_divisao' OR stTipoFiltro = 'sub_divisao_grupo' THEN
        stSql := stSql || ' WHERE contrato_servidor.cod_sub_divisao IN ('||stValoresFiltro||')';
    END IF;
    IF stTipoFiltro = 'sub_divisao_funcao' OR stTipoFiltro = 'sub_divisao_funcao_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                    ON contrato_servidor_sub_divisao_funcao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao IN ('||stValoresFiltro||')
                            INNER JOIN (  SELECT contrato_servidor_sub_divisao_funcao.cod_contrato
                                               , max(contrato_servidor_sub_divisao_funcao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                           WHERE contrato_servidor_sub_divisao_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_sub_divisao_funcao.cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                    ON max_contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                   AND max_contrato_servidor_sub_divisao_funcao.timestamp = contrato_servidor_sub_divisao_funcao.timestamp';
    END IF;
    
    IF stTipoFiltro = 'atributo_servidor' OR stTipoFiltro = 'atributo_servidor_grupo' OR stTipoFiltro = 'atributo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');
        
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                                    ON atributo_contrato_servidor_valor.cod_contrato = contrato.cod_contrato
                                   AND atributo_contrato_servidor_valor.cod_atributo = '''||arValoresFiltro[2]||'''';
                                   
        IF arValoresFiltro[1] = '1' THEN
            stSql := stSql || '        AND atributo_contrato_servidor_valor.valor IN ('||arValoresFiltro[3]||')';
        ELSE
            stSql := stSql || '        AND atributo_contrato_servidor_valor.valor = '''||arValoresFiltro[3]||'''';
        END IF;
        
        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql || '
                            INNER JOIN administracao.atributo_valor_padrao
                                    ON atributo_valor_padrao.cod_modulo = atributo_contrato_servidor_valor.cod_modulo
                                   AND atributo_valor_padrao.cod_cadastro = atributo_contrato_servidor_valor.cod_cadastro
                                   AND atributo_valor_padrao.cod_atributo = atributo_contrato_servidor_valor.cod_atributo
                                   AND atributo_valor_padrao.cod_valor::INTEGER = atributo_contrato_servidor_valor.valor::INTEGER ';
        END IF;
        
        stSql := stSql || '
                            INNER JOIN (  SELECT atributo_contrato_servidor_valor.cod_contrato
                                               , atributo_contrato_servidor_valor.cod_atributo
                                               , max(atributo_contrato_servidor_valor.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                                           WHERE atributo_contrato_servidor_valor.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY atributo_contrato_servidor_valor.cod_contrato
                                               , atributo_contrato_servidor_valor.cod_atributo) as max_atributo_contrato_servidor_valor
                                    ON max_atributo_contrato_servidor_valor.cod_contrato = atributo_contrato_servidor_valor.cod_contrato
                                   AND max_atributo_contrato_servidor_valor.cod_atributo = atributo_contrato_servidor_valor.cod_atributo
                                   AND max_atributo_contrato_servidor_valor.timestamp = atributo_contrato_servidor_valor.timestamp';
    END IF;
    
    IF stTipoFiltro = 'padrao' OR stTipoFiltro = 'padrao_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_padrao
                                    ON contrato_servidor_padrao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_padrao.cod_padrao IN ('||stValoresFiltro||')
                            INNER JOIN (  SELECT contrato_servidor_padrao.cod_contrato
                                               , max(contrato_servidor_padrao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_padrao
                                           WHERE contrato_servidor_padrao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_padrao.cod_contrato) as max_contrato_servidor_padrao
                                    ON max_contrato_servidor_padrao.cod_contrato = contrato_servidor_padrao.cod_contrato
                                   AND max_contrato_servidor_padrao.timestamp = contrato_servidor_padrao.timestamp';
    END IF;
    IF stTipoFiltro = 'cargo' OR stTipoFiltro = 'cargo_grupo' THEN
        stSql := stSql || ' WHERE contrato_servidor.cod_cargo IN ('||stValoresFiltro||')';
    END IF;
    IF stTipoFiltro = 'funcao' OR stTipoFiltro = 'funcao_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_funcao
                                    ON contrato_servidor_funcao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_funcao.cod_cargo IN ('||stValoresFiltro||')
                            INNER JOIN (  SELECT contrato_servidor_funcao.cod_contrato
                                               , max(contrato_servidor_funcao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                                           WHERE contrato_servidor_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_funcao.cod_contrato) as max_contrato_servidor_funcao
                                    ON max_contrato_servidor_funcao.cod_contrato = contrato_servidor_funcao.cod_contrato
                                   AND max_contrato_servidor_funcao.timestamp = contrato_servidor_funcao.timestamp';
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

    FOR reContratoServidor IN EXECUTE stSql LOOP
        --DADOS DA TABELA pessoal'||stEntidade||'.contrato
        rwContratoServidor.registro            := reContratoServidor.registro;       

        --DADOS DA TABELA pessoal'||stEntidade||'.servidor
        rwContratoServidor.cod_servidor        := reContratoServidor.cod_servidor;     
        rwContratoServidor.cod_uf              := reContratoServidor.cod_uf;           
        rwContratoServidor.cod_municipio       := reContratoServidor.cod_municipio;    
        rwContratoServidor.numcgm              := reContratoServidor.numcgm;           
        rwContratoServidor.nome_pai            := reContratoServidor.nome_pai;         
        rwContratoServidor.nome_mae            := reContratoServidor.nome_mae;         
        rwContratoServidor.zona_titulo         := reContratoServidor.zona_titulo;      
        rwContratoServidor.secao_titulo        := reContratoServidor.secao_titulo;     
        rwContratoServidor.caminho_foto        := reContratoServidor.caminho_foto;     
        rwContratoServidor.nr_titulo_eleitor   := reContratoServidor.nr_titulo_eleitor;
        rwContratoServidor.cod_estado_civil    := reContratoServidor.cod_estado_civil; 
        rwContratoServidor.cod_raca            := reContratoServidor.cod_raca;         


        --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor
        rwContratoServidor.cod_contrato        := reContratoServidor.cod_contrato;       
        rwContratoServidor.cod_norma           := reContratoServidor.cod_norma;          
        rwContratoServidor.cod_tipo_pagamento  := reContratoServidor.cod_tipo_pagamento; 
        rwContratoServidor.cod_tipo_salario    := reContratoServidor.cod_tipo_salario;   
        rwContratoServidor.cod_tipo_admissao   := reContratoServidor.cod_tipo_admissao;  
        rwContratoServidor.cod_categoria       := reContratoServidor.cod_categoria;      
        rwContratoServidor.cod_vinculo         := reContratoServidor.cod_vinculo;        
        rwContratoServidor.cod_cargo           := reContratoServidor.cod_cargo;          
        rwContratoServidor.cod_regime          := reContratoServidor.cod_regime;         
        rwContratoServidor.cod_sub_divisao     := reContratoServidor.cod_sub_divisao;    
        rwContratoServidor.nr_cartao_ponto     := reContratoServidor.nr_cartao_ponto;    
        rwContratoServidor.ativo               := reContratoServidor.ativo;              
        rwContratoServidor.dt_opcao_fgts       := reContratoServidor.dt_opcao_fgts;      
        rwContratoServidor.adiantamento        := reContratoServidor.adiantamento;       
        rwContratoServidor.cod_grade           := reContratoServidor.cod_grade;  
        rwContratoServidor.valor_atributo  := reContratoServidor.valor_atributo;               

        WHILE arConfiguracao[inIndex] IS NOT NULL LOOP
            --DADOS DA TABELA pessoal.contrato_servidor_nomeacao_posse
            IF arConfiguracao[inIndex] = 'anp' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '  SELECT contrato_servidor_nomeacao_posse.dt_nomeacao
                                 , contrato_servidor_nomeacao_posse.dt_posse
                                 , contrato_servidor_nomeacao_posse.dt_admissao
                              FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                        INNER JOIN (  SELECT contrato_servidor_nomeacao_posse.cod_contrato
                                           , max(contrato_servidor_nomeacao_posse.timestamp) as timestamp
                                        FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                       WHERE timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                    GROUP BY contrato_servidor_nomeacao_posse.cod_contrato) as max_contrato_servidor_nomeacao_posse
                                ON max_contrato_servidor_nomeacao_posse.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato
                               AND max_contrato_servidor_nomeacao_posse.timestamp = contrato_servidor_nomeacao_posse.timestamp
                             WHERE contrato_servidor_nomeacao_posse.cod_contrato = '||reContratoServidor.cod_contrato;

                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.dt_nomeacao := reRegistro.dt_nomeacao;
                rwContratoServidor.dt_posse    := reRegistro.dt_posse;
                rwContratoServidor.dt_admissao := reRegistro.dt_admissao;               
            END IF;

            --DADOS DA TABELA pessoal.cargo
            IF arConfiguracao[inIndex] = 'ca' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT cargo.descricao as desc_cargo
                                FROM pessoal'||stEntidade||'.cargo
                               WHERE cargo.cod_cargo = '||reContratoServidor.cod_cargo;
                rwContratoServidor.desc_cargo   := selectIntoVarchar(stSql);
            END IF;

            --DADOS DA TABELA pessoal.regime
            IF arConfiguracao[inIndex] = 'car' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT regime.descricao as desc_regime
                                FROM pessoal'||stEntidade||'.regime
                               WHERE regime.cod_regime = '||reContratoServidor.cod_regime;
                rwContratoServidor.desc_regime   := selectIntoVarchar(stSql);
            END IF;

            --DADOS DA TABELA pessoal.sub_divisao
            IF arConfiguracao[inIndex] = 'cas' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT sub_divisao.descricao as desc_sub_divisao
                                FROM pessoal'||stEntidade||'.sub_divisao
                               WHERE sub_divisao.cod_sub_divisao = '||reContratoServidor.cod_sub_divisao;
                rwContratoServidor.desc_sub_divisao   := selectIntoVarchar(stSql);
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_cedencia
            IF arConfiguracao[inIndex] = 'c' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_cedencia.cod_tipo
                                   , tipo_cedencia.descricao as desc_tipo_cedencia
                                FROM pessoal'||stEntidade||'.contrato_servidor_cedencia
                          INNER JOIN pessoal'||stEntidade||'.tipo_cedencia
                                  ON tipo_cedencia.cod_tipo = contrato_servidor_cedencia.cod_tipo
                               WHERE contrato_servidor_cedencia.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_tipo             := reRegistro.cod_tipo;
                rwContratoServidor.desc_tipo_cedencia   := reRegistro.desc_tipo_cedencia;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_conselho
            IF arConfiguracao[inIndex] = 'co' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_conselho.cod_conselho
                                   , conselho.sigla as sigla_conselho
                                   , conselho.descricao as desc_conselho
                                FROM pessoal'||stEntidade||'.contrato_servidor_conselho
                          INNER JOIN pessoal'||stEntidade||'.conselho
                                  ON conselho.cod_conselho = contrato_servidor_conselho.cod_conselho
                               WHERE contrato_servidor_conselho.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_conselho     := reRegistro.cod_conselho;
                rwContratoServidor.sigla_conselho   := reRegistro.sigla_conselho;
                rwContratoServidor.desc_conselho    := reRegistro.desc_conselho;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_conta_fgts
            IF arConfiguracao[inIndex] = 'cf' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_conta_fgts.cod_agencia as cod_agencia_fgts
                                   , contrato_servidor_conta_fgts.cod_banco as cod_banco_fgts
                                   , contrato_servidor_conta_fgts.nr_conta as nr_conta_fgts
                                FROM pessoal'||stEntidade||'.contrato_servidor_conta_fgts
                               WHERE contrato_servidor_conta_fgts.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_agencia_fgts     := reRegistro.cod_agencia_fgts;
                rwContratoServidor.cod_banco_fgts       := reRegistro.cod_banco_fgts;
                rwContratoServidor.nr_conta_fgts        := reRegistro.nr_conta_fgts;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_conta_salario
            IF arConfiguracao[inIndex] = 'cs' OR arConfiguracao[inIndex] = 'all' THEN
                -- Verifica no histórico se foi pago em crédito em banco ou foi pago em outra forma de pagamento
                -- Caso tenha sido pago em outra forma, retornar vazio os dados da conta salário
                stSql := '    SELECT contrato_servidor_forma_pagamento.*
                                FROM pessoal'||stEntidade||'.contrato_servidor_forma_pagamento
                          INNER JOIN (  SELECT contrato_servidor_forma_pagamento.cod_contrato
                                             , max(timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_forma_pagamento
                                         WHERE timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_forma_pagamento.cod_contrato
                                     ) as max_contrato_servidor_forma_pagamento
                                  ON contrato_servidor_forma_pagamento.cod_contrato = max_contrato_servidor_forma_pagamento.cod_contrato
                              AND contrato_servidor_forma_pagamento.timestamp = max_contrato_servidor_forma_pagamento.timestamp
                            WHERE contrato_servidor_forma_pagamento.cod_contrato = '||reContratoServidor.cod_contrato;

                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_forma_pagamento := reRegistro.cod_forma_pagamento;

                IF rwContratoServidor.cod_forma_pagamento = 3 THEN --Crédito em conta
                    stSql := '    SELECT contrato_servidor_conta_salario_historico.cod_agencia as cod_agencia_salario
                                    , contrato_servidor_conta_salario_historico.cod_banco as cod_banco_salario
                                    , contrato_servidor_conta_salario_historico.nr_conta as nr_conta_salario
                                    , banco.num_banco as num_banco_salario
                                    , banco.nom_banco as nom_banco_salario
                                    , agencia.num_agencia as num_agencia_salario
                                    , agencia.nom_agencia as nom_agencia_salario
                                 FROM pessoal'||stEntidade||'.contrato_servidor_conta_salario_historico
                           INNER JOIN (  SELECT contrato_servidor_conta_salario_historico.cod_contrato
                                              , max(contrato_servidor_conta_salario_historico.timestamp) as timestamp
                                           FROM pessoal'||stEntidade||'.contrato_servidor_conta_salario_historico
                                          WHERE contrato_servidor_conta_salario_historico.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                       GROUP BY contrato_servidor_conta_salario_historico.cod_contrato) as max_contrato_servidor_conta_salario_historico
                                    ON max_contrato_servidor_conta_salario_historico.cod_contrato = contrato_servidor_conta_salario_historico.cod_contrato
                                   AND max_contrato_servidor_conta_salario_historico.timestamp = contrato_servidor_conta_salario_historico.timestamp
                            INNER JOIN monetario.agencia
                                    ON agencia.cod_banco = contrato_servidor_conta_salario_historico.cod_banco
                                   AND agencia.cod_agencia = contrato_servidor_conta_salario_historico.cod_agencia
                            INNER JOIN monetario.banco
                                    ON banco.cod_banco = contrato_servidor_conta_salario_historico.cod_banco
                                 WHERE contrato_servidor_conta_salario_historico.cod_contrato = '||reContratoServidor.cod_contrato;

                    OPEN crCursor FOR EXECUTE stSql;
                        FETCH crCursor INTO reRegistro;
                    CLOSE crCursor;
                    rwContratoServidor.cod_agencia_salario     := reRegistro.cod_agencia_salario;
                    rwContratoServidor.cod_banco_salario       := reRegistro.cod_banco_salario;
                    rwContratoServidor.nr_conta_salario        := reRegistro.nr_conta_salario;
                    rwContratoServidor.num_banco_salario       := reRegistro.num_banco_salario;
                    rwContratoServidor.nom_banco_salario       := reRegistro.nom_banco_salario;
                    rwContratoServidor.num_agencia_salario     := reRegistro.num_agencia_salario;
                    rwContratoServidor.nom_agencia_salario     := reRegistro.nom_agencia_salario;
                ELSE
                    rwContratoServidor.cod_agencia_salario     := NULL;
                    rwContratoServidor.cod_banco_salario       := NULL;
                    rwContratoServidor.nr_conta_salario        := NULL;
                    rwContratoServidor.num_banco_salario       := NULL;
                    rwContratoServidor.nom_banco_salario       := NULL;
                    rwContratoServidor.num_agencia_salario     := NULL;
                    rwContratoServidor.nom_agencia_salario     := NULL;
                END IF;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_exame_medico
            IF arConfiguracao[inIndex] = 'em' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_exame_medico.dt_validade_exame
                                FROM pessoal'||stEntidade||'.contrato_servidor_exame_medico
                          INNER JOIN (  SELECT contrato_servidor_exame_medico.cod_contrato
                                             , max(contrato_servidor_exame_medico.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_exame_medico
                                         WHERE contrato_servidor_exame_medico.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_exame_medico.cod_contrato) as max_contrato_servidor_exame_medico
                                  ON max_contrato_servidor_exame_medico.cod_contrato = contrato_servidor_exame_medico.cod_contrato
                                 AND max_contrato_servidor_exame_medico.timestamp = contrato_servidor_exame_medico.timestamp
                               WHERE contrato_servidor_exame_medico.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.dt_validade_exame  := reRegistro.dt_validade_exame;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_inicio_progressao
            IF arConfiguracao[inIndex] = 'ip' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_inicio_progressao.dt_inicio_progressao
                                FROM pessoal'||stEntidade||'.contrato_servidor_inicio_progressao
                          INNER JOIN (  SELECT contrato_servidor_inicio_progressao.cod_contrato
                                             , max(contrato_servidor_inicio_progressao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_inicio_progressao
                                         WHERE contrato_servidor_inicio_progressao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_inicio_progressao.cod_contrato) as max_contrato_servidor_inicio_progressao
                                  ON max_contrato_servidor_inicio_progressao.cod_contrato = contrato_servidor_inicio_progressao.cod_contrato
                                 AND max_contrato_servidor_inicio_progressao.timestamp = contrato_servidor_inicio_progressao.timestamp
                               WHERE contrato_servidor_inicio_progressao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.dt_inicio_progressao  := reRegistro.dt_inicio_progressao;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_nivel_padrao
            IF arConfiguracao[inIndex] = 'np' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_nivel_padrao.cod_nivel_padrao
                                FROM pessoal'||stEntidade||'.contrato_servidor_nivel_padrao
                          INNER JOIN (  SELECT contrato_servidor_nivel_padrao.cod_contrato
                                             , max(contrato_servidor_nivel_padrao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_nivel_padrao
                                         WHERE contrato_servidor_nivel_padrao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_nivel_padrao.cod_contrato) as max_contrato_servidor_nivel_padrao
                                  ON max_contrato_servidor_nivel_padrao.cod_contrato = contrato_servidor_nivel_padrao.cod_contrato
                                 AND max_contrato_servidor_nivel_padrao.timestamp = contrato_servidor_nivel_padrao.timestamp
                               WHERE contrato_servidor_nivel_padrao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_nivel_padrao  := reRegistro.cod_nivel_padrao;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_ocorrencia
            IF arConfiguracao[inIndex] = 'oc' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_ocorrencia.cod_ocorrencia
                                FROM pessoal'||stEntidade||'.contrato_servidor_ocorrencia
                          INNER JOIN (  SELECT contrato_servidor_ocorrencia.cod_contrato
                                             , max(contrato_servidor_ocorrencia.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_ocorrencia
                                         WHERE contrato_servidor_ocorrencia.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_ocorrencia.cod_contrato) as max_contrato_servidor_ocorrencia
                                  ON max_contrato_servidor_ocorrencia.cod_contrato = contrato_servidor_ocorrencia.cod_contrato
                                 AND max_contrato_servidor_ocorrencia.timestamp = contrato_servidor_ocorrencia.timestamp
                               WHERE contrato_servidor_ocorrencia.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_ocorrencia  := reRegistro.cod_ocorrencia;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_padrao
            IF arConfiguracao[inIndex] = 'p' OR arConfiguracao[inIndex] = 'pp' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_padrao.cod_padrao
                                FROM pessoal'||stEntidade||'.contrato_servidor_padrao
                          INNER JOIN (  SELECT contrato_servidor_padrao.cod_contrato
                                             , max(contrato_servidor_padrao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_padrao
                                         WHERE contrato_servidor_padrao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_padrao.cod_contrato) as max_contrato_servidor_padrao
                                  ON max_contrato_servidor_padrao.cod_contrato = contrato_servidor_padrao.cod_contrato
                                 AND max_contrato_servidor_padrao.timestamp = contrato_servidor_padrao.timestamp
                               WHERE contrato_servidor_padrao.cod_contrato = '||reContratoServidor.cod_contrato;
                
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_padrao  := reRegistro.cod_padrao;
                
                IF arConfiguracao[inIndex] = 'pp' THEN
                    IF rwContratoServidor.cod_padrao IS NOT NULL THEN 
                        stSql := '    SELECT padrao_padrao.valor
                                        , padrao.descricao
                                        FROM folhapagamento'||stEntidade||'.padrao_padrao
                                INNER JOIN (  SELECT padrao_padrao.cod_padrao
                                                    , max(padrao_padrao.timestamp) as timestamp
                                                FROM folhapagamento'||stEntidade||'.padrao_padrao
                                                WHERE padrao_padrao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                            GROUP BY padrao_padrao.cod_padrao) as max_padrao_padrao
                                        ON max_padrao_padrao.cod_padrao = padrao_padrao.cod_padrao
                                        AND max_padrao_padrao.timestamp = padrao_padrao.timestamp
                                INNER JOIN folhapagamento'||stEntidade||'.padrao
                                        ON padrao.cod_padrao = padrao_padrao.cod_padrao
                                    WHERE padrao_padrao.cod_padrao = '||rwContratoServidor.cod_padrao;
                        OPEN crCursor FOR EXECUTE stSql;
                            FETCH crCursor INTO reRegistro;
                        CLOSE crCursor;
                        rwContratoServidor.desc_padrao   := reRegistro.descricao;
                        rwContratoServidor.valor_padrao  := reRegistro.valor;
                    END IF;
                END IF;
            END IF;

            --DADOS DA TABELA pessoal.contrato_servidor_previdencia
            IF arConfiguracao[inIndex] = 'pr' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_previdencia.cod_previdencia
                                FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                          INNER JOIN (  SELECT contrato_servidor_previdencia.cod_contrato
                                             , max(contrato_servidor_previdencia.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                         WHERE contrato_servidor_previdencia.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_previdencia.cod_contrato) as max_contrato_servidor_previdencia
                                  ON max_contrato_servidor_previdencia.cod_contrato = contrato_servidor_previdencia.cod_contrato
                                 AND max_contrato_servidor_previdencia.timestamp = contrato_servidor_previdencia.timestamp
                          INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia
                                  ON previdencia_previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
                                 AND previdencia_previdencia.tipo_previdencia = ''o''
                          INNER JOIN (  SELECT previdencia_previdencia.cod_previdencia
                                             , max(previdencia_previdencia.timestamp) as timestamp
                                          FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                                      GROUP BY previdencia_previdencia.cod_previdencia) as max_previdencia_previdencia
                                  ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                 AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp
                               WHERE contrato_servidor_previdencia.bo_excluido = false
                                 AND contrato_servidor_previdencia.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_previdencia  := reRegistro.cod_previdencia;
            END IF;

            --DADOS DA TABELA pessoal.contrato_servidor_salario
            IF arConfiguracao[inIndex] = 's' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_salario.salario
                                   , contrato_servidor_salario.horas_mensais
                                   , contrato_servidor_salario.horas_semanais
                                   , contrato_servidor_salario.vigencia
                                FROM pessoal'||stEntidade||'.contrato_servidor_salario
                          INNER JOIN (  SELECT contrato_servidor_salario.cod_contrato
                                             , max(contrato_servidor_salario.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_salario
                                         WHERE contrato_servidor_salario.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_salario.cod_contrato) as max_contrato_servidor_salario
                                  ON max_contrato_servidor_salario.cod_contrato = contrato_servidor_salario.cod_contrato
                                 AND max_contrato_servidor_salario.timestamp = contrato_servidor_salario.timestamp
                               WHERE contrato_servidor_salario.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.salario          := reRegistro.salario;       
                rwContratoServidor.horas_mensais    := reRegistro.horas_mensais; 
                rwContratoServidor.horas_semanais   := reRegistro.horas_semanais;
                rwContratoServidor.vigencia         := reRegistro.vigencia;      
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_sindicato
            IF arConfiguracao[inIndex] = 'si' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_sindicato.numcgm_sindicato
                                FROM pessoal'||stEntidade||'.contrato_servidor_sindicato
                               WHERE contrato_servidor_sindicato.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.numcgm_sindicato          := reRegistro.numcgm_sindicato;       
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_orgao
            IF arConfiguracao[inIndex] = 'o' OR arConfiguracao[inIndex] = 'oo' OR arConfiguracao[inIndex] = 'all' THEN        
                stSql := '    SELECT contrato_servidor_orgao.cod_orgao
                                   , recuperadescricaoorgao(contrato_servidor_orgao.cod_orgao,'''||stTimestampFechamentoPeriodo||''') as descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_orgao
                          INNER JOIN (  SELECT contrato_servidor_orgao.cod_contrato
                                             , max(contrato_servidor_orgao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_orgao
                                         WHERE contrato_servidor_orgao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_orgao.cod_contrato) as max_contrato_servidor_orgao
                                  ON max_contrato_servidor_orgao.cod_contrato = contrato_servidor_orgao.cod_contrato
                                 AND max_contrato_servidor_orgao.timestamp = contrato_servidor_orgao.timestamp
                               WHERE contrato_servidor_orgao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_orgao  := reRegistro.cod_orgao;
                rwContratoServidor.desc_orgao := reRegistro.descricao;

                --DADOS DA TABELA organograma.fn_consulta_orgao
                IF arConfiguracao[inIndex] = 'oo' OR arConfiguracao[inIndex] = 'all' THEN        
                    inCodOrganograma := selectIntoInteger('SELECT cod_organograma FROM organograma.orgao_nivel WHERE cod_orgao = '||reRegistro.cod_orgao);
                    stSql := 'SELECT organograma.fn_consulta_orgao('||inCodOrganograma||','||reRegistro.cod_orgao||')';
                    rwContratoServidor.orgao := selectIntoVarchar(stSql); 
                END IF;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_local
            IF arConfiguracao[inIndex] = 'l' OR arConfiguracao[inIndex] = 'all' THEN        
                stSql := '    SELECT contrato_servidor_local.cod_local
                                   , local.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_local
                          INNER JOIN (  SELECT contrato_servidor_local.cod_contrato
                                             , max(contrato_servidor_local.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_local
                                         WHERE contrato_servidor_local.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_local.cod_contrato) as max_contrato_servidor_local
                                  ON max_contrato_servidor_local.cod_contrato = contrato_servidor_local.cod_contrato
                                 AND max_contrato_servidor_local.timestamp = contrato_servidor_local.timestamp
                          INNER JOIN organograma.local
                                  ON local.cod_local = contrato_servidor_local.cod_local
                               WHERE contrato_servidor_local.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_local  := reRegistro.cod_local;
                rwContratoServidor.desc_local := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_regime_funcao
            IF arConfiguracao[inIndex] = 'rf' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT contrato_servidor_regime_funcao.cod_regime as cod_regime_funcao
                                   , regime.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                          INNER JOIN (  SELECT contrato_servidor_regime_funcao.cod_contrato
                                             , max(contrato_servidor_regime_funcao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                                         WHERE contrato_servidor_regime_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_regime_funcao.cod_contrato) as max_contrato_servidor_regime_funcao
                                  ON max_contrato_servidor_regime_funcao.cod_contrato = contrato_servidor_regime_funcao.cod_contrato
                                 AND max_contrato_servidor_regime_funcao.timestamp = contrato_servidor_regime_funcao.timestamp
                          INNER JOIN pessoal'||stEntidade||'.regime
                                  ON regime.cod_regime = contrato_servidor_regime_funcao.cod_regime
                               WHERE contrato_servidor_regime_funcao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_regime_funcao  := reRegistro.cod_regime_funcao;
                rwContratoServidor.desc_regime_funcao := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
            IF arConfiguracao[inIndex] = 'sf' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT contrato_servidor_sub_divisao_funcao.cod_sub_divisao as cod_sub_divisao_funcao
                                   , sub_divisao.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                          INNER JOIN (  SELECT contrato_servidor_sub_divisao_funcao.cod_contrato
                                             , max(contrato_servidor_sub_divisao_funcao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                         WHERE contrato_servidor_sub_divisao_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_sub_divisao_funcao.cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                  ON max_contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                 AND max_contrato_servidor_sub_divisao_funcao.timestamp = contrato_servidor_sub_divisao_funcao.timestamp
                          INNER JOIN pessoal'||stEntidade||'.sub_divisao
                                  ON sub_divisao.cod_sub_divisao = contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                               WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_sub_divisao_funcao  := reRegistro.cod_sub_divisao_funcao;
                rwContratoServidor.desc_sub_divisao_funcao := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_funcao
            IF arConfiguracao[inIndex] = 'f' OR arConfiguracao[inIndex] = 'all' THEN        
                stSql := '    SELECT contrato_servidor_funcao.cod_cargo as cod_funcao
                                   , cargo.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                          INNER JOIN (  SELECT contrato_servidor_funcao.cod_contrato
                                             , max(contrato_servidor_funcao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                                         WHERE contrato_servidor_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_funcao.cod_contrato) as max_contrato_servidor_funcao
                                  ON max_contrato_servidor_funcao.cod_contrato = contrato_servidor_funcao.cod_contrato
                                 AND max_contrato_servidor_funcao.timestamp = contrato_servidor_funcao.timestamp
                          INNER JOIN pessoal'||stEntidade||'.cargo
                                  ON cargo.cod_cargo = contrato_servidor_funcao.cod_cargo
                               WHERE contrato_servidor_funcao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_funcao  := reRegistro.cod_funcao;
                rwContratoServidor.desc_funcao := reRegistro.descricao;

                IF rwContratoServidor.cod_funcao IS NOT NULL THEN 
                    stSql := ' SELECT * 
                                 FROM ( SELECT cargo.cod_cargo
                                             , cbo.codigo as cbo_codigo
                                             , cbo.cod_cbo as cod_cbo
                                             , cargo.descricao
                                          FROM pessoal'||stEntidade||'.cargo
                                             , pessoal'||stEntidade||'.cbo_cargo
                                             , (  SELECT cod_cargo
                                                       , max(timestamp) as timestamp
                                                    FROM pessoal'||stEntidade||'.cbo_cargo
                                                GROUP BY cod_cargo) as max_cbo_cargo
                                             , pessoal'||stEntidade||'.cbo
                                         WHERE cargo.cod_cargo = cbo_cargo.cod_cargo
                                           AND cbo_cargo.cod_cargo = max_cbo_cargo.cod_cargo 
                                           AND cbo_cargo.timestamp = max_cbo_cargo.timestamp
                                           AND cbo_cargo.cod_cbo = cbo.cod_cbo
                                         UNION
                                        SELECT cargo.cod_cargo
                                             , cbo.codigo as cbo_codigo
                                             , cbo.cod_cbo as cod_cbo
                                             , cargo.descricao
                                          FROM pessoal'||stEntidade||'.cargo
                                             , pessoal'||stEntidade||'.especialidade
                                             , pessoal'||stEntidade||'.cbo_especialidade
                                             , (  SELECT cod_especialidade
                                             , max(timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.cbo_especialidade
                                      GROUP BY cod_especialidade) as max_cod_especialidade
                                             , pessoal'||stEntidade||'.cbo
                                         WHERE cargo.cod_cargo = especialidade.cod_cargo
                                           AND especialidade.cod_especialidade = cbo_especialidade.cod_especialidade
                                           AND cbo_especialidade.cod_especialidade = max_cod_especialidade.cod_especialidade 
                                           AND cbo_especialidade.timestamp = max_cod_especialidade.timestamp
                                           AND cbo_especialidade.cod_cbo = cbo.cod_cbo) as funcao
                                         WHERE funcao.cod_cargo = '||reRegistro.cod_funcao;
    
                    OPEN crCursor FOR EXECUTE stSql;
                        FETCH crCursor INTO reRegistro;
                    CLOSE crCursor;
                    rwContratoServidor.cod_cbo_funcao               := reRegistro.cod_cbo;
                    rwContratoServidor.desc_cbo_funcao              := reRegistro.descricao;
                END IF;

            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
            IF arConfiguracao[inIndex] = 'ef' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT contrato_servidor_especialidade_funcao.cod_especialidade as cod_especialidade_funcao
                                   , especialidade.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
                          INNER JOIN (  SELECT contrato_servidor_especialidade_funcao.cod_contrato
                                             , max(contrato_servidor_especialidade_funcao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
                                         WHERE contrato_servidor_especialidade_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_especialidade_funcao.cod_contrato) as max_contrato_servidor_especialidade_funcao
                                  ON max_contrato_servidor_especialidade_funcao.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato
                                 AND max_contrato_servidor_especialidade_funcao.timestamp = contrato_servidor_especialidade_funcao.timestamp
                          INNER JOIN pessoal'||stEntidade||'.especialidade
                                  ON especialidade.cod_especialidade = contrato_servidor_especialidade_funcao.cod_especialidade
                               WHERE contrato_servidor_especialidade_funcao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_especialidade_funcao  := reRegistro.cod_especialidade_funcao;
                rwContratoServidor.desc_especialidade_funcao := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_especialidade_cargo
            IF arConfiguracao[inIndex] = 'ec' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT contrato_servidor_especialidade_cargo.cod_especialidade as cod_especialidade_cargo
                                   , especialidade.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_cargo
                          INNER JOIN pessoal'||stEntidade||'.especialidade
                                  ON especialidade.cod_especialidade = contrato_servidor_especialidade_cargo.cod_especialidade
                               WHERE contrato_servidor_especialidade_cargo.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_especialidade_cargo  := reRegistro.cod_especialidade_cargo;
                rwContratoServidor.desc_especialidade_cargo := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.servidor_cid
            IF arConfiguracao[inIndex] = 'cid' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT  servidor_cid.cod_cid
                                    , servidor_cid.data_laudo
                                FROM pessoal'||stEntidade||'.servidor_cid
                          INNER JOIN (  SELECT servidor_cid.cod_servidor
                                             , max(servidor_cid.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.servidor_cid
                                         WHERE servidor_cid.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY servidor_cid.cod_servidor) as max_servidor_cid
                                  ON max_servidor_cid.cod_servidor = servidor_cid.cod_servidor
                                 AND max_servidor_cid.timestamp = servidor_cid.timestamp
                               WHERE servidor_cid.cod_servidor = '||reContratoServidor.cod_servidor;

                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_cid  := reRegistro.cod_cid;
                rwContratoServidor.data_laudo  := reRegistro.data_laudo;
                
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.servidor_conjuge
            IF arConfiguracao[inIndex] = 'con' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT servidor_conjuge.numcgm as numcgm_conjuge
                                FROM pessoal'||stEntidade||'.servidor_conjuge
                          INNER JOIN (  SELECT servidor_conjuge.cod_servidor
                                             , max(servidor_conjuge.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.servidor_conjuge
                                         WHERE servidor_conjuge.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY servidor_conjuge.cod_servidor) as max_servidor_conjuge
                                  ON max_servidor_conjuge.cod_servidor = servidor_conjuge.cod_servidor
                                 AND max_servidor_conjuge.timestamp = servidor_conjuge.timestamp
                               WHERE servidor_conjuge.bo_excluido = false
                                 AND servidor_conjuge.cod_servidor = '||reContratoServidor.cod_servidor;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.numcgm_conjuge  := reRegistro.numcgm_conjuge;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.servidor_reservista
            IF arConfiguracao[inIndex] = 'res' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT servidor_reservista.nr_carteira_res
                                   , servidor_reservista.cat_reservista
                                   , servidor_reservista.origem_reservista
                                FROM pessoal'||stEntidade||'.servidor_reservista
                               WHERE servidor_reservista.cod_servidor = '||reContratoServidor.cod_servidor;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.nr_carteira_res      := reRegistro.nr_carteira_res;
                rwContratoServidor.cat_reservista       := reRegistro.cat_reservista;
                rwContratoServidor.origem_reservista    := reRegistro.origem_reservista;
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
                               WHERE sw_cgm.numcgm = '||reContratoServidor.numcgm;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.nom_cgm              := reRegistro.nom_cgm;            
                rwContratoServidor.servidor_pis_pasep   := reRegistro.servidor_pis_pasep; 
                rwContratoServidor.rg                   := reRegistro.rg;                 
                rwContratoServidor.cpf                  := reRegistro.cpf;                
                rwContratoServidor.dt_nascimento        := reRegistro.dt_nascimento;      
            END IF;
                
            inIndex := inIndex + 1;
        END LOOP;
        inIndex := 1;

        RETURN NEXT rwContratoServidor;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';
