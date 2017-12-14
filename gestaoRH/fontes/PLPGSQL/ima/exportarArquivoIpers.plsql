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
    * PLpgsql Arquivo IPE/RS
    * Data de Criação: 01/07/2008
    
    
    * @author Analista: Dagiane Vieira  
    * @author Desenvolvedor: Rafael Garbin
    
    * @ignore
   
    $Id: $
   
    * Casos de uso: uc-04.08.28

*/

DROP TYPE colunasArquivoIpers CASCADE;

CREATE TYPE colunasArquivoIpers AS (
    registro                    INTEGER,
    nom_cgm                     VARCHAR,
    cpf                         VARCHAR,
    rg                          VARCHAR,
    sexo                        VARCHAR,    
    orgao                       VARCHAR,
    matricula_ipe               VARCHAR,
    dt_ingresso                 VARCHAR,
    dt_situacao                 DATE,
    dt_nascimento               DATE,
    cod_estado_civil            INTEGER,
    logradouro                  VARCHAR,
    cep                         VARCHAR,                                                                   
    situacao                    INTEGER,    
    valor                       VARCHAR                                                                   
);

CREATE OR REPLACE FUNCTION exportarArquivoIpers(INTEGER,VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR,INTEGER,INTEGER,VARCHAR,INTEGER, BOOLEAN) RETURNS SETOF colunasArquivoIpers AS $$
DECLARE
    inCodPeriodoMovimentacao    ALIAS FOR $1;
    stEntidade                  ALIAS FOR $2;
    stExercicio                 ALIAS FOR $3;
    stTipoFiltro                ALIAS FOR $4;
    stValoresFiltro             ALIAS FOR $5;
    stSituacaoCadastro          ALIAS FOR $6;
    inCodTipoEmissao            ALIAS FOR $7;
    inFolha                     ALIAS FOR $8;
    stDesdobramento             ALIAS FOR $9;
    inCodComplementar           ALIAS FOR $10;
    boAgruparFolhas             ALIAS FOR $11;

    stSql                       VARCHAR := '';
    stSqlAux                    VARCHAR := '';
    stFiltroServidor            VARCHAR := '';
    stFiltroPadraoServidor      VARCHAR := '';
    stFiltroPensionista         VARCHAR := '';
    stFiltroRescindidos         VARCHAR := '';
    stServidor                  VARCHAR := '';
    stPensionista               VARCHAR := '';
    stNomeTabela                VARCHAR := '';
    stSituacaoContratoServidor  VARCHAR := '';
    inSituacao                  INTEGER := NULL;
    dtSituacao                  DATE;
    nuValorEventoIPECalculado   NUMERIC(14,2);

    rwIPE                       colunasArquivoIpers%ROWTYPE;
    reRegistro                  RECORD;
    reConfiguracao              RECORD;    
    rePeriodoMovimentacao       RECORD;    
    crCursor                    REFCURSOR;   

BEGIN
    -- Buscando os dados da configuração do IPE
    stSql := ' SELECT configuracao_ipe.*
                    , configuracao_ipe_pensionista.cod_atributo_data as cod_atributo_data_pen
                    , configuracao_ipe_pensionista.cod_modulo_data   as cod_modulo_data_pen
                    , configuracao_ipe_pensionista.cod_cadastro_data as cod_cadastro_data_pen
                    , configuracao_ipe_pensionista.cod_atributo_mat  as cod_atributo_mat_pen
                    , configuracao_ipe_pensionista.cod_modulo_mat    as cod_modulo_mat_pen
                    , configuracao_ipe_pensionista.cod_cadastro_mat  as cod_cadastro_mat_pen         
                 FROM folhapagamento'|| stEntidade ||'.configuracao_ipe
            LEFT JOIN folhapagamento'|| stEntidade ||'.configuracao_ipe_pensionista
                   ON configuracao_ipe.cod_configuracao = configuracao_ipe_pensionista.cod_configuracao
                  AND configuracao_ipe.vigencia = configuracao_ipe_pensionista.vigencia
                    , ( SELECT max(cod_configuracao) as cod_configuracao
                             , vigencia
                         FROM folhapagamento'|| stEntidade ||'.configuracao_ipe
                      GROUP BY vigencia) as max_configuracao_ipe
                WHERE configuracao_ipe.cod_configuracao = max_configuracao_ipe.cod_configuracao
                  AND configuracao_ipe.vigencia = max_configuracao_ipe.vigencia
                  AND configuracao_ipe.vigencia <= ( SELECT dt_final
                                                       FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                      WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||') 
             ORDER BY configuracao_ipe.vigencia desc
                LIMIT 1';    

    -- Recuperando dados da configuração do IPE
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO reConfiguracao;
    CLOSE crCursor;

    /**** Monta Filtro de situação ****/
    IF trim(stSituacaoCadastro) = 'ativos' THEN
        stSituacaoContratoServidor := 'A';
    END IF;

    IF trim(stSituacaoCadastro) = 'aposentados' THEN
        stSituacaoContratoServidor := 'P';
    END IF;

    -- Monta Filtros dos rescindidos!
    IF trim(stSituacaoCadastro) = 'rescindidos' OR trim(stSituacaoCadastro) = 'todos' THEN
        stNomeTabela := 'temp_rescindidos';
        IF trim(stSituacaoCadastro) = 'rescindidos' THEN
            stNomeTabela := 'temp_table';
            stSituacaoContratoServidor := 'R';    
        END IF;

        stFiltroServidor := ' AND EXISTS ( SELECT 1
                                             FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                                            WHERE contrato_servidor_caso_causa.cod_contrato = '|| stNomeTabela ||'.cod_contrato
                                              AND dt_rescisao BETWEEN 
                                                   ( SELECT dt_inicial
                                                       FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                      WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                   )
                                                   AND 
                                                   ( SELECT dt_final
                                                       FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                      WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||')
                                          ) ';
        stFiltroRescindidos := stFiltroServidor;
        IF trim(stSituacaoCadastro) = 'todos' THEN
            stFiltroServidor := '';
        END IF;
    END IF;

    -- Monta Filtros do pensionista!
    IF trim(stSituacaoCadastro) = 'pensionistas' OR trim(stSituacaoCadastro) = 'todos' THEN
        stNomeTabela := 'temp_pensionistas';
        IF trim(stSituacaoCadastro) = 'pensionistas' THEN
            stNomeTabela := 'temp_table';
            stSituacaoContratoServidor := 'E';    
        END IF;

        stFiltroPensionista := ' AND EXISTS ( SELECT 1
                                                FROM pessoal'|| stEntidade ||'.contrato_pensionista
                                               WHERE contrato_pensionista.cod_contrato = '|| stNomeTabela ||'.cod_contrato
                                                 AND (contrato_pensionista.dt_encerramento is null OR to_char(dt_encerramento,'||quote_literal('yyyy-mm-dd')||')::date BETWEEN 
                                                       ( SELECT dt_inicial
                                                           FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                          WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                       )
                                                       AND 
                                                       ( SELECT dt_final
                                                           FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                          WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                       )
                                                     )
                                              )';    
    END IF;


    /* Inicia consulta principal */
    IF stSituacaoCadastro = 'ativos' OR
       stSituacaoCadastro = 'aposentados' OR
       stSituacaoCadastro = 'rescindidos' OR
       stSituacaoCadastro = 'todos' THEN    
        -- Monta consulta para os servidores
        stServidor := '
                  SELECT contrato.registro
                       , contrato.cod_contrato
                       , contrato.nom_cgm
                       , contrato.cpf
                       , contrato.rg
                       , contrato.dt_nascimento
                       , replace(atributo_matricula_ipe.valor, '' '', '''') as matricula_ipe
                       , to_date(atributo_data_ipe.valor,''dd/mm/yyyy'') as data_ipe
                       , recuperarSituacaoDoContrato(contrato.cod_contrato, '|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||') as situacao_contrato
                       , lpad(trim(sw_cgm.cep),8,''0'') as cep
                       , sw_cgm_pessoa_fisica.sexo
                       , (sw_cgm.logradouro || '' '' || sw_cgm.numero) as logradouro
                       , servidor.cod_estado_civil
                    FROM recuperarContratoServidor(''cgm''
                                                  , '|| quote_literal(stEntidade) ||'
                                                  , '|| inCodPeriodoMovimentacao ||'
                                                  , '|| quote_literal(stTipoFiltro) ||'
                                                  , '|| quote_literal(stValoresFiltro) ||'
                                                  , '|| quote_literal(stExercicio) ||') as contrato
              INNER JOIN ( SELECT atributo_contrato_servidor_valor.*                                                        
                             FROM pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor                        
                                , (SELECT cod_contrato  
                                        , cod_atributo
                                        , cod_cadastro
                                        , cod_modulo
                                        , max(timestamp) as timestamp                                                       
                                    FROM pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor                
                                GROUP BY cod_contrato, cod_atributo, cod_cadastro, cod_modulo) as max_atributo_contrato_servidor_valor                           
                          WHERE atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato
						    AND atributo_contrato_servidor_valor.cod_atributo = max_atributo_contrato_servidor_valor.cod_atributo
                            AND atributo_contrato_servidor_valor.cod_cadastro = max_atributo_contrato_servidor_valor.cod_cadastro
                            AND atributo_contrato_servidor_valor.cod_modulo = max_atributo_contrato_servidor_valor.cod_modulo
                            AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp
                            AND trim(atributo_contrato_servidor_valor.valor) != '''') as atributo_matricula_ipe
                     ON contrato.cod_contrato = atributo_matricula_ipe.cod_contrato                                       
                    AND atributo_matricula_ipe.cod_atributo = '|| reConfiguracao.cod_atributo_mat ||'
                    AND atributo_matricula_ipe.cod_cadastro = '|| reConfiguracao.cod_cadastro_mat ||'                      
                    AND atributo_matricula_ipe.cod_modulo   = '|| reConfiguracao.cod_modulo_mat ||'                        
             INNER JOIN ( SELECT atributo_contrato_servidor_valor.*                                                        
                            FROM pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor                        
                                , (SELECT cod_contrato 
								        , cod_atributo
                                        , cod_cadastro
                                        , cod_modulo			
                                        , max(timestamp) as timestamp                                                       
                                    FROM pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor                
                                GROUP BY cod_contrato, cod_atributo, cod_cadastro, cod_modulo) as max_atributo_contrato_servidor_valor                           
                          WHERE atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato
						    AND atributo_contrato_servidor_valor.cod_atributo = max_atributo_contrato_servidor_valor.cod_atributo
                            AND atributo_contrato_servidor_valor.cod_cadastro = max_atributo_contrato_servidor_valor.cod_cadastro
                            AND atributo_contrato_servidor_valor.cod_modulo = max_atributo_contrato_servidor_valor.cod_modulo
                            AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp
                            AND trim(atributo_contrato_servidor_valor.valor) != '''') as atributo_data_ipe
                     ON contrato.cod_contrato = atributo_data_ipe.cod_contrato                                            
                    AND atributo_data_ipe.cod_atributo = '|| reConfiguracao.cod_atributo_data ||'                          
                    AND atributo_data_ipe.cod_cadastro = '|| reConfiguracao.cod_cadastro_data ||'                          
                    AND atributo_data_ipe.cod_modulo   = '|| reConfiguracao.cod_modulo_data ||'
             INNER JOIN sw_cgm
                     ON contrato.numcgm = sw_cgm.numcgm
             INNER JOIN sw_cgm_pessoa_fisica
                     ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
             INNER JOIN pessoal'|| stEntidade ||'.servidor
                     ON servidor.cod_servidor = contrato.cod_servidor
            '; -- Fim da consulta do servidor
    END IF;      
    

    IF (trim(stSituacaoCadastro) = 'pensionistas' OR trim(stSituacaoCadastro) = 'todos') AND 
       reConfiguracao.cod_atributo_mat_pen IS NOT NULL THEN
        -- Monta consulta para os pensionistas
        stPensionista := '
                  SELECT contrato_pensionista.registro
                       , contrato_pensionista.cod_contrato
                       , contrato_pensionista.nom_cgm
                       , contrato_pensionista.cpf
                       , contrato_pensionista.rg
                       , contrato_pensionista.dt_nascimento
                       , replace(atributo_matricula_ipe.valor, '' '', '''') as matricula_ipe
                       , to_date(atributo_data_ipe.valor,''dd/mm/yyyy'') as data_ipe
                       , recuperarSituacaoDoContrato(contrato_pensionista.cod_contrato, '|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||') as situacao_contrato
                       , lpad(trim(sw_cgm.cep),8,''0'') as cep
                       , sw_cgm_pessoa_fisica.sexo
                       , (sw_cgm.logradouro || '' '' || sw_cgm.numero) as logradouro
                       , 0 as cod_estado_civil
                    FROM recuperarContratoPensionista(''cgm''
                                                , '|| quote_literal(stEntidade) ||'
                                                , '|| inCodPeriodoMovimentacao ||'
                                                , '|| quote_literal(stTipoFiltro) ||'
                                                , '|| quote_literal(stValoresFiltro) ||'
                                                , '|| quote_literal(stExercicio) ||') as contrato_pensionista
              INNER JOIN ( SELECT atributo_contrato_pensionista.*                                                        
                             FROM pessoal'|| stEntidade ||'.atributo_contrato_pensionista                        
                                , ( SELECT cod_contrato
                                         , cod_atributo
                                         , cod_cadastro
                                         , cod_modulo			
                                         , max(timestamp) as timestamp                                                       
                                      FROM pessoal'|| stEntidade ||'.atributo_contrato_pensionista                
                                  GROUP BY cod_contrato,cod_atributo, cod_cadastro, cod_modulo ) as max_atributo_contrato_pensionista                           
                            WHERE atributo_contrato_pensionista.cod_contrato = max_atributo_contrato_pensionista.cod_contrato
							  AND atributo_contrato_pensionista.cod_atributo = max_atributo_contrato_pensionista.cod_atributo
                              AND atributo_contrato_pensionista.cod_cadastro = max_atributo_contrato_pensionista.cod_cadastro
                              AND atributo_contrato_pensionista.cod_modulo = max_atributo_contrato_pensionista.cod_modulo
                              AND atributo_contrato_pensionista.timestamp = max_atributo_contrato_pensionista.timestamp
                              AND trim(atributo_contrato_pensionista.valor) != '''') as atributo_matricula_ipe
                      ON contrato_pensionista.cod_contrato = atributo_matricula_ipe.cod_contrato                                       
                     AND atributo_matricula_ipe.cod_atributo = '|| reConfiguracao.cod_atributo_mat_pen ||'
                     AND atributo_matricula_ipe.cod_cadastro = '|| reConfiguracao.cod_cadastro_mat_pen ||'
                     AND atributo_matricula_ipe.cod_modulo   = '|| reConfiguracao.cod_modulo_mat_pen ||'
              INNER JOIN ( SELECT atributo_contrato_pensionista.*
                             FROM pessoal'|| stEntidade ||'.atributo_contrato_pensionista
                                , ( SELECT cod_contrato
								         , cod_atributo
                                         , cod_cadastro
                                         , cod_modulo
                                         , max(timestamp) as timestamp
                                      FROM pessoal'|| stEntidade ||'.atributo_contrato_pensionista                
                                  GROUP BY cod_contrato, cod_atributo, cod_cadastro, cod_modulo) as max_atributo_contrato_pensionista                           
                            WHERE atributo_contrato_pensionista.cod_contrato = max_atributo_contrato_pensionista.cod_contrato
                              AND atributo_contrato_pensionista.cod_atributo = max_atributo_contrato_pensionista.cod_atributo
                              AND atributo_contrato_pensionista.cod_cadastro = max_atributo_contrato_pensionista.cod_cadastro
                              AND atributo_contrato_pensionista.cod_modulo = max_atributo_contrato_pensionista.cod_modulo
							  AND atributo_contrato_pensionista.timestamp = max_atributo_contrato_pensionista.timestamp
                              AND trim(atributo_contrato_pensionista.valor) != '''') as atributo_data_ipe
                      ON contrato_pensionista.cod_contrato = atributo_data_ipe.cod_contrato                                            
                     AND atributo_data_ipe.cod_atributo = '|| reConfiguracao.cod_atributo_data_pen ||'                          
                     AND atributo_data_ipe.cod_cadastro = '|| reConfiguracao.cod_cadastro_data_pen ||'                          
                     AND atributo_data_ipe.cod_modulo   = '|| reConfiguracao.cod_modulo_data_pen ||'
              INNER JOIN sw_cgm
                      ON contrato_pensionista.numcgm = sw_cgm.numcgm
              INNER JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
        ';

    END IF; -- Fim da consulta do pensionista

    -- Caso tipo de emissão FOR Inclusão ou Acerto de Inclusão
    -- Data de inicio do IPERS deve estar contida na competencia
    IF inCodTipoEmissao IN (3,4) THEN 
        -- Busca periodo inicial e final da competencia
        stSqlAux := ' SELECT *
                        FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;

        OPEN crCursor FOR EXECUTE stSqlAux;
            FETCH crCursor INTO rePeriodoMovimentacao;
        CLOSE crCursor;             
        
        stFiltroServidor := stFiltroServidor || ' AND data_ipe BETWEEN '|| quote_literal(rePeriodoMovimentacao.dt_inicial) ||' AND '|| quote_literal(rePeriodoMovimentacao.dt_final) ||' AND abs(matricula_ipe::numeric) = 0';
    ELSE
        stFiltroServidor := stFiltroServidor || ' AND abs(matricula_ipe::numeric) > 0';
    END IF;
    
    stFiltroPensionista := stFiltroPensionista || stFiltroServidor;
    stFiltroRescindidos := stFiltroRescindidos || stFiltroServidor;
    stFiltroPadraoServidor := stFiltroPadraoServidor || stFiltroServidor;
    
    IF trim(stSituacaoContratoServidor) != '' AND  trim(stSituacaoCadastro) != 'todos' THEN
        IF trim(stServidor) != '' THEN
            stSql := 'SELECT *
                        FROM ( '|| stServidor|| ') as temp_table
                       WHERE recuperarSituacaoDoContrato(cod_contrato, '|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||') = '|| quote_literal(stSituacaoContratoServidor) ||'
                        '|| stFiltroServidor;
        ELSE
            stSql := 'SELECT *
                        FROM ( '|| stPensionista|| ') as temp_table
                       WHERE recuperarSituacaoDoContrato(cod_contrato, '|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||') = '|| quote_literal(stSituacaoContratoServidor) ||'
                        '|| stFiltroPensionista;
        END IF;
    ELSE
        stSql := '
                  SELECT *
                    FROM ( '|| stServidor|| ') as temp_ativos
                   WHERE recuperarSituacaoDoContrato(temp_ativos.cod_contrato, '|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||') = ''A''      
                         '|| stFiltroPadraoServidor ||'
               UNION ALL 
                  SELECT *
                    FROM ( '|| stServidor|| ') as temp_aposentados
                   WHERE recuperarSituacaoDoContrato(temp_aposentados.cod_contrato, '|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||') = ''P''      
                         '|| stFiltroPadraoServidor ||'
               UNION ALL 
                  SELECT *
                    FROM ( '|| stServidor|| ') as temp_rescindidos
                   WHERE recuperarSituacaoDoContrato(temp_rescindidos.cod_contrato, '|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||') = ''R''
                        '|| stFiltroPadraoServidor ||'
                        '|| stFiltroRescindidos ||'   
                ';
                
        IF reConfiguracao.cod_atributo_mat_pen IS NOT NULL THEN
            stSql := stSql ||'
                   UNION ALL 
                      SELECT *
                        FROM ( '|| stPensionista|| ') as temp_pensionistas
                       WHERE recuperarSituacaoDoContrato(temp_pensionistas.cod_contrato, '|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||') = ''E''
                            '|| stFiltroPensionista ||'            
                    ';
        END IF;
    END IF;    
    /*** PERCORRENDO A CONSULTA PRINCIPAL ***/
    FOR reRegistro IN EXECUTE stSql LOOP
        dtSituacao := NULL;

        /* Busca situação do contrato */
        IF reRegistro.situacao_contrato = 'A' THEN
            inSituacao := 10;
        END IF;
    
        IF reRegistro.situacao_contrato = 'P' THEN
            inSituacao := 11;
        END IF;
    
        IF stSituacaoCadastro = 'E' THEN
            inSituacao := 39;
        END IF;

        IF reRegistro.situacao_contrato = 'R' THEN
            -- Busca a data do falecimento
            stSql := ' SELECT 31 as situacao
                            , dt_rescisao
                         FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                            , pessoal'|| stEntidade ||'.caso_causa
                            , pessoal'|| stEntidade ||'.causa_rescisao
                        WHERE cod_contrato = '|| reRegistro.cod_contrato ||'
                          AND contrato_servidor_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa
                          AND caso_causa.cod_causa_rescisao = causa_rescisao.cod_causa_rescisao
                          AND causa_rescisao.num_causa = 60';

                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO inSituacao,dtSituacao;
                CLOSE crCursor;   

            IF inSituacao IS NULL THEN 
                -- Busca a data da rescisão
                stSql := ' SELECT 30 as situacao
                                , dt_rescisao
                            FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                                , pessoal'|| stEntidade ||'.caso_causa
                                , pessoal'|| stEntidade ||'.causa_rescisao
                            WHERE cod_contrato = '|| reRegistro.cod_contrato ||'
                              AND contrato_servidor_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa
                              AND caso_causa.cod_causa_rescisao = causa_rescisao.cod_causa_rescisao
                              AND causa_rescisao.num_causa != 60';
    
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO inSituacao,dtSituacao;
                CLOSE crCursor;         

            END IF;              
        END IF;

        -- Caso não tenha sido falecido ou rescindido, retorna data do ipe
        IF dtSituacao IS NULL THEN 
            dtSituacao := reRegistro.data_ipe;
        END IF;

        /*** INICIO: Busca o valor de desconto do evento IPE do contrato ***/
        IF boAgruparFolhas IS TRUE THEN
            stSql := '
                      SELECT sum(tabela_teste.valor) as valor
                      FROM (
                            --COMPLEMENTAR
                            SELECT sum(valor) as valor
                              FROM recuperarEventosCalculados(0,'|| inCodPeriodoMovimentacao ||','|| reRegistro.cod_contrato ||',0,'|| quote_literal(stEntidade) ||', ''evento.cod_evento'')
                            WHERE cod_evento = '|| reConfiguracao.cod_evento_base ||'
                            --SALARIO
                            UNION ALL
                            SELECT sum(valor) as valor
                              FROM recuperarEventosCalculados(1,'|| inCodPeriodoMovimentacao ||','|| reRegistro.cod_contrato ||','|| inCodComplementar ||','|| quote_literal(stEntidade) ||', ''evento.cod_evento'')
                            WHERE cod_evento = '|| reConfiguracao.cod_evento_base ||'
                            --FERIAS
                            UNION ALL
                            SELECT sum(valor) as valor
                              FROM recuperarEventosCalculados(2,'|| inCodPeriodoMovimentacao ||','|| reRegistro.cod_contrato ||','|| inCodComplementar ||','|| quote_literal(stEntidade) ||', ''evento.cod_evento'')
                            WHERE cod_evento = '|| reConfiguracao.cod_evento_base ||'                      
                            --DECIMO
                            UNION ALL
                            SELECT sum(valor) as valor
                              FROM recuperarEventosCalculados(3,'|| inCodPeriodoMovimentacao ||','|| reRegistro.cod_contrato ||','|| inCodComplementar ||','|| quote_literal(stEntidade) ||', ''evento.cod_evento'')
                            WHERE cod_evento = '|| reConfiguracao.cod_evento_base ||'                                            
                            --DECIMO
                            UNION ALL
                            SELECT sum(valor) as valor
                              FROM recuperarEventosCalculados(4,'|| inCodPeriodoMovimentacao ||','|| reRegistro.cod_contrato ||','|| inCodComplementar ||','|| quote_literal(stEntidade) ||', ''evento.cod_evento'')
                            WHERE cod_evento = '|| reConfiguracao.cod_evento_base ||'
                          ) as tabela_teste';                            
                      
        ELSE
            stSql := 'SELECT sum(valor) as valor
                        FROM recuperarEventosCalculados('|| inFolha ||','|| inCodPeriodoMovimentacao ||','|| reRegistro.cod_contrato ||','|| inCodComplementar ||','|| quote_literal(stEntidade) ||', ''evento.cod_evento'')
                      WHERE cod_evento = '|| reConfiguracao.cod_evento_base;

            IF trim(stDesdobramento) != '' AND stDesdobramento IS NOT NULL THEN
                stSql := stSql || ' AND desdobramento = '|| quote_literal(stDesdobramento) ||' ';
            END IF;
        END IF;

        nuValorEventoIPECalculado := selectIntoNumeric(stSql);
                
        /*** FIM: Busca o valor de desconto do evento IPE do contrato ***/

        /*** Monta retorno dos dados ***/
        -- Caso o tipo de emissão FOR por inclusão ou acerto de inclusão 
        -- gerar no arquivo também, servidores sem que não tem cálculo de IPERS
        
        IF nuValorEventoIPECalculado > 0 OR 
            inCodTipoEmissao IN (3,4) THEN 

            rwIPE.orgao               := reConfiguracao.codigo_orgao;
            rwIPE.registro            := reRegistro.registro;                    
            rwIPE.nom_cgm             := reRegistro.nom_cgm;      
            rwIPE.cep                 := reRegistro.cep;               
            rwIPE.cpf                 := reRegistro.cpf;                         
            rwIPE.rg                  := reRegistro.rg;   
            rwIPE.dt_nascimento       := reRegistro.dt_nascimento;
            rwIPE.logradouro          := reRegistro.logradouro;       
            rwIPE.cod_estado_civil    := reRegistro.cod_estado_civil;                             
            rwIPE.sexo                := reRegistro.sexo;               
            rwIPE.matricula_ipe       := lpad(reRegistro.matricula_ipe,13,'0');            
            rwIPE.dt_ingresso         := recuperarDataInicioContagemTempoContrato(stEntidade,reRegistro.cod_contrato,stExercicio);
            rwIPE.situacao            := inSituacao;
            rwIPE.dt_situacao         := dtSituacao;
            rwIPE.valor               := nuValorEventoIPECalculado;

            RETURN NEXT rwIPE;                        
        END IF;
    END LOOP;
END;       
$$ LANGUAGE 'plpgsql';
