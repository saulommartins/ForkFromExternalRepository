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
/* recuperarContratoServidor 
 * Data de Criação : 24/05/2016
 * Analista : Dagiane Vieira
 * Desenvolvedor : Evandro Melos
 */

CREATE OR REPLACE FUNCTION tcemg.arquivo_folhapagamento_12(VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR) RETURNS SETOF RECORD 
AS $$
DECLARE
    inCodPeriodoMovimentacao    ALIAS FOR $1;
    stEntidade                  ALIAS FOR $2;
    inCodEntidade               ALIAS FOR $3;
    inMes                       ALIAS FOR $4;
    stExercicio                 ALIAS FOR $5;
    stDataInicial               VARCHAR;
    stSql                       VARCHAR;
    reRegistro                  RECORD;
    
BEGIN
/*
    0 - Folha Complementar
    1 - Folha Salario
    2 - Folha Ferias
    3 - Folha Decimo
    4 - Folha Rescisao
*/

    stDataInicial := '01/'||inMes||'/'||stExercicio||'';

/*
    51 – Desconto do Abate Teto sobre Remuneração 
    (buscar valor do evento da configuração em Gestão Prestação de Contas :: TCE - MG :: Configuração :: Configurar Teto Remuneratório 
    sem desdobramento ou vazio, folha salario e complementar)
*/
    stSql := '  CREATE TEMPORARY TABLE tmp_teto_remuneracao AS 
                        SELECT 
                                     12 as tipo_registro                                   
                                    , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                               contrato.registro||''''||1
                                          WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                               contrato.registro||''''||2
                                          WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                               contrato.registro||''''||3
                                    END as cod_reduzido_pessoa
                                   , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                   , 51::integer as tipo_desconto
                                   , teto_remuneracao.cod_evento
                                   , SUM(teto_remuneracao.valor) as valor

                                FROM pessoal'||stEntidade||'.contrato

                          INNER JOIN folhapagamento'||stEntidade||'.contrato_servidor_periodo
                                  ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
                                 AND contrato_servidor_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'

                          INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                  ON contrato_servidor.cod_contrato = contrato.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                                  ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor
                                  ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                          INNER JOIN sw_cgm_pessoa_fisica
                                  ON sw_cgm_pessoa_fisica.numcgm = servidor.numcgm

                        LEFT JOIN ( 
                                    SELECT *
                                        , ''E'' as tipo_calculo
                                        FROM recuperarEventosCalculados(0,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''') 
                                        WHERE natureza = ''D''
                                    UNION
                                    SELECT *
                                        , ''M'' as tipo_calculo
                                        FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''') 
                                        WHERE natureza = ''D''
                                                                       
                                ) as teto_remuneracao
                                ON teto_remuneracao.cod_contrato = contrato.cod_contrato

                        INNER JOIN tcemg.teto_remuneratorio
                                ON teto_remuneratorio.cod_evento = teto_remuneracao.cod_evento
                                AND teto_remuneratorio.vigencia <= last_day(TO_DATE('''||stDataInicial||''',''dd/mm/yyyy''))
                                AND teto_remuneratorio.cod_entidade = '||inCodEntidade||'
                                AND teto_remuneratorio.vigencia = ( SELECT MAX(teto_remuneratorio.vigencia)
                                                                    FROM tcemg.teto_remuneratorio
                                                                    WHERE teto_remuneratorio.vigencia <= last_day(TO_DATE('''||stDataInicial||''',''dd/mm/yyyy''))
                                                                   )
                        
                        GROUP BY 1,2,3,4,5

            UNION

            --PENSIONISTA
                        SELECT 
                                     12 as tipo_registro
                                   , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                               contrato.registro||''''||1
                                          WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                               contrato.registro||''''||2
                                          WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                               contrato.registro||''''||3
                                    END as cod_reduzido_pessoa
                                   , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                   , 51::integer as tipo_desconto
                                   , teto_remuneracao.cod_evento
                                   , SUM(teto_remuneracao.valor) as valor

                            FROM pessoal'||stEntidade||'.contrato_pensionista
    
                            INNER JOIN pessoal'||stEntidade||'.pensionista
                                ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                                AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
    
                            INNER JOIN pessoal'||stEntidade||'.contrato
                                ON contrato.cod_contrato = contrato_pensionista.cod_contrato
    
                            INNER JOIN sw_cgm
                                ON sw_cgm.numcgm = pensionista.numcgm
    
                            INNER JOIN sw_cgm_pessoa_fisica
                                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                            
                            LEFT JOIN ( 
                                        SELECT *
                                            , ''E'' as tipo_calculo
                                            FROM recuperarEventosCalculados(0,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''') 
                                            WHERE natureza = ''D''
                                        UNION
                                        SELECT *
                                            , ''M'' as tipo_calculo
                                            FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''') 
                                            WHERE natureza = ''D''
                                ) as teto_remuneracao
                                ON teto_remuneracao.cod_contrato = contrato.cod_contrato

                            INNER JOIN tcemg.teto_remuneratorio
                                ON teto_remuneratorio.cod_evento = teto_remuneracao.cod_evento
                                AND teto_remuneratorio.vigencia <= last_day(TO_DATE('''||stDataInicial||''',''dd/mm/yyyy''))
                                AND teto_remuneratorio.cod_entidade = '||inCodEntidade||'
                                AND teto_remuneratorio.vigencia = ( SELECT MAX(teto_remuneratorio.vigencia)
                                                                    FROM tcemg.teto_remuneratorio
                                                                    WHERE teto_remuneratorio.vigencia <= last_day(TO_DATE('''||stDataInicial||''',''dd/mm/yyyy''))
                                                                   )
                            GROUP BY 1,2,3,4,5                               
                ';
    EXECUTE stSql;

/*
    52 – Desconto do Abate Teto sobre Férias 
    (buscar valor do evento da configuração em Gestão Prestação de Contas :: TCE - MG :: Configuração :: Configurar Teto Remuneratório 
    Nos desdobramentos:
    F - férias
    D - Adiant Férias
    A - Abono Ferias da folha férias e também da folha salário com estes desdobramentos)
*/
    stSql := '  CREATE TEMPORARY TABLE tmp_teto_ferias AS 
                        SELECT 
                                     12 as tipo_registro
                                   , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                               contrato.registro||''''||1
                                          WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                               contrato.registro||''''||2
                                          WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                               contrato.registro||''''||3
                                    END as cod_reduzido_pessoa
                                   , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                   , 52::integer as tipo_desconto
                                   , teto_remuneracao.cod_evento
                                   , SUM(teto_remuneracao.valor) as valor

                                FROM pessoal'||stEntidade||'.contrato

                          INNER JOIN folhapagamento'||stEntidade||'.contrato_servidor_periodo
                                  ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
                                 AND contrato_servidor_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'

                          INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                  ON contrato_servidor.cod_contrato = contrato.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                                  ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor
                                  ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                          INNER JOIN sw_cgm_pessoa_fisica
                                  ON sw_cgm_pessoa_fisica.numcgm = servidor.numcgm

                        LEFT JOIN ( 
                                    SELECT *
                                        , ''M'' as tipo_calculo
                                        FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''') 
                                        WHERE natureza = ''D''
                                        AND desdobramento IN (''F'',''D'',''A'')
                                    UNION
                                    SELECT *
                                        , ''M'' as tipo_calculo
                                        FROM recuperarEventosCalculados(2,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''') 
                                        WHERE natureza = ''D''
                                        AND desdobramento IN (''F'',''D'',''A'')
                                                                       
                                ) as teto_remuneracao
                                ON teto_remuneracao.cod_contrato = contrato.cod_contrato

                        INNER JOIN tcemg.teto_remuneratorio
                                ON teto_remuneratorio.cod_evento = teto_remuneracao.cod_evento
                                AND teto_remuneratorio.vigencia <= last_day(TO_DATE('''||stDataInicial||''',''dd/mm/yyyy''))
                                AND teto_remuneratorio.vigencia = ( SELECT MAX(teto_remuneratorio.vigencia)
                                                                    FROM tcemg.teto_remuneratorio
                                                                    WHERE teto_remuneratorio.vigencia <= last_day(TO_DATE('''||stDataInicial||''',''dd/mm/yyyy''))
                                                                   )
                                AND teto_remuneratorio.cod_entidade = '||inCodEntidade||'
                        GROUP BY 1,2,3,4,5
            
            UNION
            
            --PENSIONISTA
                        SELECT 
                                     12 as tipo_registro
                                   , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                               contrato.registro||''''||1
                                          WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                               contrato.registro||''''||2
                                          WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                               contrato.registro||''''||3
                                    END as cod_reduzido_pessoa
                                   , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                   , 51::integer as tipo_desconto
                                   , teto_remuneracao.cod_evento
                                   , SUM(teto_remuneracao.valor) as valor

                            FROM pessoal'||stEntidade||'.contrato_pensionista
    
                            INNER JOIN pessoal'||stEntidade||'.pensionista
                                ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                                AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
    
                            INNER JOIN pessoal'||stEntidade||'.contrato
                                ON contrato.cod_contrato = contrato_pensionista.cod_contrato
    
                            INNER JOIN sw_cgm
                                ON sw_cgm.numcgm = pensionista.numcgm
    
                            INNER JOIN sw_cgm_pessoa_fisica
                                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                            
                            LEFT JOIN ( 
                                        SELECT *
                                            , ''M'' as tipo_calculo
                                        FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''') 
                                        WHERE natureza = ''D''
                                        AND desdobramento IN (''F'',''D'',''A'')
                                    UNION
                                        SELECT *
                                            , ''M'' as tipo_calculo
                                        FROM recuperarEventosCalculados(2,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''') 
                                        WHERE natureza = ''D''
                                        AND desdobramento IN (''F'',''D'',''A'')
                                ) as teto_remuneracao
                                ON teto_remuneracao.cod_contrato = contrato.cod_contrato

                            INNER JOIN tcemg.teto_remuneratorio
                                ON teto_remuneratorio.cod_evento = teto_remuneracao.cod_evento
                                AND teto_remuneratorio.vigencia <= last_day(TO_DATE('''||stDataInicial||''',''dd/mm/yyyy''))
                                AND teto_remuneratorio.cod_entidade = '||inCodEntidade||'
                                AND teto_remuneratorio.vigencia = ( SELECT MAX(teto_remuneratorio.vigencia)
                                                                    FROM tcemg.teto_remuneratorio
                                                                    WHERE teto_remuneratorio.vigencia <= last_day(TO_DATE('''||stDataInicial||''',''dd/mm/yyyy''))
                                                                   )
                            GROUP BY 1,2,3,4,5                               
                    ';

    EXECUTE stSql;

/*
    53 – Desconto do Abate Teto sobre 13º Salário 
    (buscar valor do evento da configuração em Gestão Prestação de Contas :: TCE - MG :: Configuração :: Configurar Teto Remuneratório 
    Nos desdobramento: 
    D - décimo terceiro na folha de décimo)
*/
    stSql := '  CREATE TEMPORARY TABLE tmp_teto_decimo AS 
                        SELECT 
                                     12 as tipo_registro
                                   , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                               contrato.registro||''''||1
                                          WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                               contrato.registro||''''||2
                                          WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                               contrato.registro||''''||3
                                    END as cod_reduzido_pessoa
                                   , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                   , 53::integer as tipo_desconto
                                   , teto_remuneracao.cod_evento
                                   , SUM(teto_remuneracao.valor) as valor

                                FROM pessoal'||stEntidade||'.contrato

                          INNER JOIN folhapagamento'||stEntidade||'.contrato_servidor_periodo
                                  ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
                                 AND contrato_servidor_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'

                          INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                  ON contrato_servidor.cod_contrato = contrato.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                                  ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor
                                  ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                          INNER JOIN sw_cgm_pessoa_fisica
                                  ON sw_cgm_pessoa_fisica.numcgm = servidor.numcgm

                        LEFT JOIN ( 
                                    SELECT *
                                        , ''D''::VARCHAR as tipo_calculo
                                        FROM recuperarEventosCalculados(3,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''') 
                                        WHERE natureza = ''D''
                                        AND desdobramento IN (''D'')
                                ) as teto_remuneracao
                                ON teto_remuneracao.cod_contrato = contrato.cod_contrato

                        INNER JOIN tcemg.teto_remuneratorio
                                ON teto_remuneratorio.cod_evento = teto_remuneracao.cod_evento
                                AND teto_remuneratorio.vigencia <= last_day(TO_DATE('''||stDataInicial||''',''dd/mm/yyyy''))
                                AND teto_remuneratorio.vigencia = ( SELECT MAX(teto_remuneratorio.vigencia)
                                                                    FROM tcemg.teto_remuneratorio
                                                                    WHERE teto_remuneratorio.vigencia <= last_day(TO_DATE('''||stDataInicial||''',''dd/mm/yyyy''))
                                                                   )
                                AND teto_remuneratorio.cod_entidade = '||inCodEntidade||'
                        GROUP BY 1,2,3,4,5
            
            UNION
            
            --PENSIONISTA
                        SELECT 
                                     12 as tipo_registro
                                   , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                               contrato.registro||''''||1
                                          WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                               contrato.registro||''''||2
                                          WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                               contrato.registro||''''||3
                                    END as cod_reduzido_pessoa
                                   , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                   , 51::integer as tipo_desconto
                                   , teto_remuneracao.cod_evento
                                   , SUM(teto_remuneracao.valor) as valor

                            FROM pessoal'||stEntidade||'.contrato_pensionista
    
                            INNER JOIN pessoal'||stEntidade||'.pensionista
                                ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                                AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
    
                            INNER JOIN pessoal'||stEntidade||'.contrato
                                ON contrato.cod_contrato = contrato_pensionista.cod_contrato
    
                            INNER JOIN sw_cgm
                                ON sw_cgm.numcgm = pensionista.numcgm
    
                            INNER JOIN sw_cgm_pessoa_fisica
                                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                            
                            LEFT JOIN ( 
                                        SELECT *
                                            , ''D''::VARCHAR as tipo_calculo
                                        FROM recuperarEventosCalculados(3,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''') 
                                        WHERE natureza = ''D''
                                        AND desdobramento IN (''D'')
                                ) as teto_remuneracao
                                ON teto_remuneracao.cod_contrato = contrato.cod_contrato

                            INNER JOIN tcemg.teto_remuneratorio
                                ON teto_remuneratorio.cod_evento = teto_remuneracao.cod_evento
                                AND teto_remuneratorio.vigencia <= last_day(TO_DATE('''||stDataInicial||''',''dd/mm/yyyy''))
                                AND teto_remuneratorio.cod_entidade = '||inCodEntidade||'
                                AND teto_remuneratorio.vigencia = ( SELECT MAX(teto_remuneratorio.vigencia)
                                                                    FROM tcemg.teto_remuneratorio
                                                                    WHERE teto_remuneratorio.vigencia <= last_day(TO_DATE('''||stDataInicial||''',''dd/mm/yyyy''))
                                                                   )
                            GROUP BY 1,2,3,4,5
                     ';
    EXECUTE stSql;


/*
    54 – Desconto da Contribuição Previdenciária (buscar o valor de desconto da previdencia conforme informado no registro 10)
    55 – Desconto do Imposto de Renda Retido na Fonte (buscar o valor de desconto da irrf conforme informado no registro 10)
*/
    stSql:= ' CREATE TEMPORARY TABLE tmp_irrf_previdencia AS
                            SELECT 
                                     12 as tipo_registro
                                   , CASE WHEN irrf_previdencia.tipo_calculo = ''M'' THEN
                                               contrato.registro||''''||1
                                          WHEN irrf_previdencia.tipo_calculo = ''D'' THEN
                                               contrato.registro||''''||2
                                          WHEN irrf_previdencia.tipo_calculo = ''E'' THEN
                                               contrato.registro||''''||3
                                    END as cod_reduzido_pessoa
                                   , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                   , irrf_previdencia.tipo_desconto as tipo_desconto
                                   , irrf_previdencia.cod_evento
                                   , SUM(irrf_previdencia.valor) as valor

                                FROM pessoal'||stEntidade||'.contrato

                          INNER JOIN folhapagamento'||stEntidade||'.contrato_servidor_periodo
                                  ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
                                 AND contrato_servidor_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'

                          INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                  ON contrato_servidor.cod_contrato = contrato.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                                  ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor
                                  ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                          INNER JOIN sw_cgm_pessoa_fisica
                                  ON sw_cgm_pessoa_fisica.numcgm = servidor.numcgm

                        INNER JOIN (
                                        SELECT complementar.*
                                                , ''E'' as tipo_calculo
                                                ,irrf_previdencia_evento.tipo as tipo_desconto
                                         FROM recuperarEventosCalculados(0,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')  as complementar
                                   INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                           ON contrato_servidor.cod_contrato = complementar.cod_contrato
                                   INNER JOIN ( SELECT max.cod_evento
                                                     , 55 as tipo
                                                     , MAX(max.timestamp) as timestamp
                                                  FROM folhapagamento'||stEntidade||'.tabela_irrf_evento as max
                                                 WHERE cod_tipo IN (3,6)
                                              GROUP BY max.cod_evento
                                                     , max.cod_tabela                                                     
                                                 UNION
                                                SELECT previdencia_evento.cod_evento
                                                     , 54 as tipo
                                                     , MAX(previdencia_evento.timestamp)as timestamp
                                                  FROM folhapagamento'||stEntidade||'.previdencia_evento
                                            INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia 
                                                    ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                   AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp
                                                 WHERE cod_tipo = 1
                                                   AND tipo_previdencia = ''o''
                                              GROUP BY previdencia_evento.cod_evento
                                                     , previdencia_evento.cod_tipo
                                              ) as irrf_previdencia_evento
                                           ON irrf_previdencia_evento.cod_evento = complementar.cod_evento                                           
                                        UNION
                                       SELECT calculado.*
                                                , ''M'' as tipo_calculo           
                                                ,irrf_previdencia_evento.tipo     as tipo_desconto                            
                                         FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')  as calculado
                                   INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                           ON contrato_servidor.cod_contrato = calculado.cod_contrato
                                   INNER JOIN ( SELECT max.cod_evento
                                                     , 55 as tipo
                                                     , MAX(max.timestamp) as timestamp
                                                  FROM folhapagamento'||stEntidade||'.tabela_irrf_evento as max
                                                 WHERE cod_tipo IN (3,6)
                                              GROUP BY max.cod_evento
                                                     , max.cod_tabela
                                                 UNION
                                                SELECT previdencia_evento.cod_evento
                                                     , 54 as tipo
                                                     , MAX(previdencia_evento.timestamp)as timestamp
                                                  FROM folhapagamento'||stEntidade||'.previdencia_evento
                                            INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia 
                                                    ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                   AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp
                                                 WHERE cod_tipo = 1
                                                   AND tipo_previdencia = ''o''
                                              GROUP BY previdencia_evento.cod_evento
                                                     , previdencia_evento.cod_tipo
                                              ) as irrf_previdencia_evento
                                           ON irrf_previdencia_evento.cod_evento = calculado.cod_evento                                           
                                        UNION
                                       SELECT ferias.*
                                                ,''M'' as tipo_calculo
                                                ,irrf_previdencia_evento.tipo as tipo_desconto                                                                               
                                        FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')  as ferias

                                   INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                           ON contrato_servidor.cod_contrato = ferias.cod_contrato
                                   INNER JOIN ( SELECT max.cod_evento
                                                        , 55 as tipo
                                                     , MAX(max.timestamp) as timestamp
                                                  FROM folhapagamento'||stEntidade||'.tabela_irrf_evento as max
                                                 WHERE cod_tipo IN (3,6)
                                              GROUP BY max.cod_evento
                                                     , max.cod_tabela
                                                 UNION
                                                SELECT previdencia_evento.cod_evento
                                                , 54 as tipo
                                                     , MAX(previdencia_evento.timestamp)as timestamp
                                                  FROM folhapagamento'||stEntidade||'.previdencia_evento
                                            INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia 
                                                    ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                   AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp
                                                 WHERE cod_tipo = 1
                                                   AND tipo_previdencia = ''o''
                                              GROUP BY previdencia_evento.cod_evento
                                                     , previdencia_evento.cod_tipo
                                              ) as irrf_previdencia_evento
                                           ON irrf_previdencia_evento.cod_evento = ferias.cod_evento                                                                                      
                                        UNION
                                       SELECT decimo.*
                                              ,''D'' as tipo_calculo
                                              ,irrf_previdencia_evento.tipo as tipo_desconto
                                        FROM recuperarEventosCalculados(3,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')  as decimo
                                   INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                           ON contrato_servidor.cod_contrato = decimo.cod_contrato
                                   INNER JOIN ( SELECT max.cod_evento
                                                        , 55 as tipo
                                                     , MAX(max.timestamp) as timestamp
                                                  FROM folhapagamento'||stEntidade||'.tabela_irrf_evento as max
                                                 WHERE cod_tipo IN (3,6)
                                              GROUP BY max.cod_evento
                                                     , max.cod_tabela
                                                 UNION
                                                SELECT previdencia_evento.cod_evento
                                                        , 54 as tipo
                                                     , MAX(previdencia_evento.timestamp)as timestamp
                                                  FROM folhapagamento'||stEntidade||'.previdencia_evento
                                            INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia 
                                                    ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                   AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp
                                                 WHERE cod_tipo = 1
                                                   AND tipo_previdencia = ''o''
                                              GROUP BY previdencia_evento.cod_evento
                                                     , previdencia_evento.cod_tipo
                                              ) as irrf_previdencia_evento
                                           ON irrf_previdencia_evento.cod_evento = decimo.cod_evento                                           
                                        UNION
                                       SELECT rescisao.*
                                              ,''M'' as tipo_calculo 
                                              ,irrf_previdencia_evento.tipo as tipo_desconto                                                                                  
                                        FROM recuperarEventosCalculados(4,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')  as rescisao

                                   INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                           ON contrato_servidor.cod_contrato = rescisao.cod_contrato
                                   INNER JOIN ( SELECT max.cod_evento
                                                        , 55 as tipo
                                                     , MAX(max.timestamp) as timestamp
                                                  FROM folhapagamento'||stEntidade||'.tabela_irrf_evento as max
                                                 WHERE cod_tipo IN (3,6)
                                              GROUP BY max.cod_evento , max.cod_tabela
                                                 UNION
                                                SELECT previdencia_evento.cod_evento
                                                        , 54 as tipo
                                                     , MAX(previdencia_evento.timestamp)as timestamp
                                                  FROM folhapagamento'||stEntidade||'.previdencia_evento
                                            INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia 
                                                    ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                   AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp
                                                 WHERE cod_tipo = 1
                                                   AND tipo_previdencia = ''o''
                                              GROUP BY previdencia_evento.cod_evento
                                                     , previdencia_evento.cod_tipo
                                              ) as irrf_previdencia_evento
                                           ON irrf_previdencia_evento.cod_evento = rescisao.cod_evento
                                        WHERE rescisao.desdobramento != ''D''                                     
                                        UNION
                                       SELECT rescisao.*
                                              ,''D'' as tipo_calculo
                                              ,irrf_previdencia_evento.tipo  as tipo_desconto                                         
                                        FROM recuperarEventosCalculados(4,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')  as rescisao                                         
                                   INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                           ON contrato_servidor.cod_contrato = rescisao.cod_contrato
                                   INNER JOIN ( SELECT max.cod_evento
                                                        , 55 as tipo
                                                     , MAX(max.timestamp) as timestamp
                                                  FROM folhapagamento'||stEntidade||'.tabela_irrf_evento as max
                                                 WHERE cod_tipo IN (3,6)
                                              GROUP BY max.cod_evento , max.cod_tabela
                                                 UNION
                                                SELECT previdencia_evento.cod_evento
                                                        , 54 as tipo
                                                     , MAX(previdencia_evento.timestamp)as timestamp
                                                  FROM folhapagamento'||stEntidade||'.previdencia_evento
                                            INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia 
                                                    ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                   AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp
                                                 WHERE cod_tipo = 1
                                                   AND tipo_previdencia = ''o''
                                              GROUP BY previdencia_evento.cod_evento
                                                     , previdencia_evento.cod_tipo
                                              ) as irrf_previdencia_evento
                                           ON irrf_previdencia_evento.cod_evento = rescisao.cod_evento
                                        WHERE rescisao.desdobramento = ''D''
                                        
                            ) AS irrf_previdencia
                         ON irrf_previdencia.cod_contrato = contrato_servidor_periodo.cod_contrato                        

                    GROUP BY 1,2,3,4,5

            UNION
            -- PENSIONISTA
                    SELECT 
                          12 as tipo_registro
                        , CASE WHEN irrf_previdencia.tipo_calculo = ''M'' THEN
                                    contrato.registro||''''||1
                               WHEN irrf_previdencia.tipo_calculo = ''D'' THEN
                                    contrato.registro||''''||2
                               WHEN irrf_previdencia.tipo_calculo = ''E'' THEN
                                    contrato.registro||''''||3
                        END as cod_reduzido_pessoa
                        , sw_cgm_pessoa_fisica.cpf AS num_cpf
                        , irrf_previdencia.tipo_desconto as tipo_desconto
                        , irrf_previdencia.cod_evento                                   
                        , SUM(irrf_previdencia.valor) as valor

                        FROM pessoal'||stEntidade||'.contrato_pensionista
    
                        INNER JOIN pessoal'||stEntidade||'.pensionista
                            ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                            AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
    
                        INNER JOIN pessoal'||stEntidade||'.contrato
                            ON contrato.cod_contrato = contrato_pensionista.cod_contrato
    
                        INNER JOIN sw_cgm
                            ON sw_cgm.numcgm = pensionista.numcgm
    
                        INNER JOIN sw_cgm_pessoa_fisica
                            ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                        INNER JOIN (  SELECT complementar.*
                                            , ''E'' as tipo_calculo   
                                            ,irrf_previdencia_evento.tipo as tipo_desconto                                      
                                        FROM recuperarEventosCalculados(0,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')  as complementar
                                   INNER JOIN pessoal'||stEntidade||'.contrato_pensionista
                                           ON contrato_pensionista.cod_contrato = complementar.cod_contrato
                                   INNER JOIN ( SELECT max.cod_evento
                                                     , 55 as tipo
                                                     , MAX(max.timestamp) as timestamp
                                                  FROM folhapagamento'||stEntidade||'.tabela_irrf_evento as max
                                                 WHERE cod_tipo IN (3,6)
                                              GROUP BY max.cod_evento
                                                     , max.cod_tabela
                                                 UNION
                                                SELECT previdencia_evento.cod_evento
                                                        , 54 as tipo
                                                     , MAX(previdencia_evento.timestamp)as timestamp
                                                  FROM folhapagamento'||stEntidade||'.previdencia_evento
                                            INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia
                                                    ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                   AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp
                                                 WHERE cod_tipo = 1
                                                   AND tipo_previdencia = ''o''
                                              GROUP BY previdencia_evento.cod_evento
                                                     , previdencia_evento.cod_tipo
                                              ) as irrf_previdencia_evento
                                           ON irrf_previdencia_evento.cod_evento = complementar.cod_evento
                                        UNION
                                       SELECT calculado.*
                                            , ''M'' as tipo_calculo    
                                            ,irrf_previdencia_evento.tipo as tipo_desconto                                    
                                        FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')  as calculado
                                   INNER JOIN pessoal'||stEntidade||'.contrato_pensionista
                                           ON contrato_pensionista.cod_contrato = calculado.cod_contrato
                                   INNER JOIN ( SELECT max.cod_evento
                                                        , 55 as tipo
                                                     , MAX(max.timestamp) as timestamp
                                                  FROM folhapagamento'||stEntidade||'.tabela_irrf_evento as max
                                                 WHERE cod_tipo IN (3,6)
                                              GROUP BY max.cod_evento
                                                     , max.cod_tabela
                                                 UNION
                                                SELECT previdencia_evento.cod_evento
                                                        , 54 as tipo
                                                     , MAX(previdencia_evento.timestamp)as timestamp
                                                  FROM folhapagamento'||stEntidade||'.previdencia_evento
                                            INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia 
                                                    ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                   AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp
                                                 WHERE cod_tipo = 1
                                                   AND tipo_previdencia = ''o''
                                              GROUP BY previdencia_evento.cod_evento
                                                     , previdencia_evento.cod_tipo
                                              ) as irrf_previdencia_evento
                                           ON irrf_previdencia_evento.cod_evento = calculado.cod_evento
                                        UNION
                                       SELECT ferias.*
                                            , ''M'' as tipo_calculo 
                                            ,irrf_previdencia_evento.tipo as tipo_desconto                                       
                                        FROM recuperarEventosCalculados(2,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')  as ferias
                                   INNER JOIN pessoal'||stEntidade||'.contrato_pensionista
                                           ON contrato_pensionista.cod_contrato = ferias.cod_contrato
                                   INNER JOIN ( SELECT max.cod_evento
                                                        , 55 as tipo
                                                     , MAX(max.timestamp) as timestamp
                                                  FROM folhapagamento'||stEntidade||'.tabela_irrf_evento as max
                                                 WHERE cod_tipo IN (3,6)
                                              GROUP BY max.cod_evento
                                                     , max.cod_tabela
                                                 UNION
                                                SELECT previdencia_evento.cod_evento
                                                        , 54 as tipo
                                                     , MAX(previdencia_evento.timestamp)as timestamp
                                                  FROM folhapagamento'||stEntidade||'.previdencia_evento
                                            INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia
                                                    ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                   AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp
                                                 WHERE cod_tipo = 1
                                                   AND tipo_previdencia = ''o''
                                              GROUP BY previdencia_evento.cod_evento
                                                     , previdencia_evento.cod_tipo
                                              ) as irrf_previdencia_evento
                                           ON irrf_previdencia_evento.cod_evento = ferias.cod_evento
                                        UNION
                                       SELECT decimo.*
                                            , ''D'' as tipo_calculo  
                                            ,irrf_previdencia_evento.tipo as tipo_desconto                                      
                                        FROM recuperarEventosCalculados(3,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')  as decimo
                                   INNER JOIN pessoal'||stEntidade||'.contrato_pensionista
                                           ON contrato_pensionista.cod_contrato = decimo.cod_contrato
                                   INNER JOIN ( SELECT max.cod_evento
                                                        , 55 as tipo
                                                     , MAX(max.timestamp) as timestamp
                                                  FROM folhapagamento'||stEntidade||'.tabela_irrf_evento as max
                                                 WHERE cod_tipo IN (3,6)
                                              GROUP BY max.cod_evento
                                                     , max.cod_tabela
                                                 UNION
                                                SELECT previdencia_evento.cod_evento
                                                        , 54 as tipo
                                                     , MAX(previdencia_evento.timestamp)as timestamp
                                                  FROM folhapagamento'||stEntidade||'.previdencia_evento
                                            INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia
                                                    ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                   AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp
                                                 WHERE cod_tipo = 1
                                                   AND tipo_previdencia = ''o''
                                              GROUP BY previdencia_evento.cod_evento
                                                     , previdencia_evento.cod_tipo
                                              ) as irrf_previdencia_evento
                                           ON irrf_previdencia_evento.cod_evento = decimo.cod_evento
                                        UNION
                                       SELECT rescisao.*
                                            , ''M'' as tipo_calculo  
                                            ,irrf_previdencia_evento.tipo as tipo_desconto                                       
                                        FROM recuperarEventosCalculados(4,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')  as rescisao
                                   INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                           ON contrato_servidor.cod_contrato = rescisao.cod_contrato
                                   INNER JOIN ( SELECT max.cod_evento
                                                        , 55 as tipo
                                                     , MAX(max.timestamp) as timestamp
                                                  FROM folhapagamento'||stEntidade||'.tabela_irrf_evento as max
                                                 WHERE cod_tipo IN (3,6)
                                              GROUP BY max.cod_evento
                                                     , max.cod_tabela
                                                 UNION
                                                SELECT previdencia_evento.cod_evento
                                                        , 54 as tipo
                                                     , MAX(previdencia_evento.timestamp)as timestamp
                                                  FROM folhapagamento'||stEntidade||'.previdencia_evento
                                            INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia 
                                                    ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                   AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp
                                                 WHERE cod_tipo = 1
                                                   AND tipo_previdencia = ''o''
                                              GROUP BY previdencia_evento.cod_evento
                                                     , previdencia_evento.cod_tipo
                                              ) as irrf_previdencia_evento
                                           ON irrf_previdencia_evento.cod_evento = rescisao.cod_evento
                                        WHERE rescisao.desdobramento != ''D''
                                        UNION
                                       SELECT rescisao.*
                                            , ''D'' as tipo_calculo
                                            ,irrf_previdencia_evento.tipo as tipo_desconto
                                        FROM recuperarEventosCalculados(4,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')  as rescisao
                                   INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                           ON contrato_servidor.cod_contrato = rescisao.cod_contrato
                                   INNER JOIN ( SELECT max.cod_evento
                                                        , 55 as tipo
                                                     , MAX(max.timestamp) as timestamp
                                                  FROM folhapagamento'||stEntidade||'.tabela_irrf_evento as max
                                                 WHERE cod_tipo IN (3,6)
                                              GROUP BY max.cod_evento , max.cod_tabela
                                                 UNION
                                                SELECT previdencia_evento.cod_evento
                                                        , 54 as tipo
                                                     , MAX(previdencia_evento.timestamp)as timestamp
                                                  FROM folhapagamento'||stEntidade||'.previdencia_evento
                                            INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia
                                                    ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                   AND previdencia_previdencia.timestamp =  previdencia_evento.timestamp
                                                 WHERE cod_tipo = 1
                                                   AND tipo_previdencia = ''o''
                                              GROUP BY previdencia_evento.cod_evento
                                                     , previdencia_evento.cod_tipo
                                              ) as irrf_previdencia_evento
                                           ON irrf_previdencia_evento.cod_evento = rescisao.cod_evento
                                        WHERE rescisao.desdobramento = ''D''
                                     
                            ) AS irrf_previdencia
                         ON irrf_previdencia.cod_contrato = contrato_pensionista.cod_contrato
                    GROUP BY 1,2,3,4,5
                 ';

    EXECUTE stSql;

/*
    59 – Desconto da 1ª Parcela do 13° Salário 
    (buscar o valor do evento de Gestão Recursos Humanos :: Folha de Pagamento :: Configuração :: Configurar Cálculo de 13º Salário 
    no campo *Evento de Desconto Adiantamento de 13º Salário da folha salario e décimo)
*/

    stSql:= ' CREATE TEMPORARY TABLE tmp_parcela_decimo AS 
                        SELECT 
                                     12 as tipo_registro
                                   , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                               contrato.registro||''''||1
                                          WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                               contrato.registro||''''||2
                                          WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                               contrato.registro||''''||3
                                    END as cod_reduzido_pessoa
                                   , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                   , 59 as tipo_desconto
                                   , teto_remuneracao.cod_evento                                   
                                   , SUM(teto_remuneracao.valor) as valor

                                FROM pessoal'||stEntidade||'.contrato

                          INNER JOIN folhapagamento'||stEntidade||'.contrato_servidor_periodo
                                  ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
                                 AND contrato_servidor_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'

                          INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                  ON contrato_servidor.cod_contrato = contrato.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                                  ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor
                                  ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                          INNER JOIN sw_cgm_pessoa_fisica
                                  ON sw_cgm_pessoa_fisica.numcgm = servidor.numcgm

                        INNER JOIN ( 
                                   SELECT *
                                       , ''D'' as tipo_calculo
                                   FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                   WHERE natureza = ''D''
                                   UNION
                                   SELECT *
                                       , ''D'' as tipo_calculo                                                                          
                                   FROM recuperarEventosCalculados(3,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                   WHERE natureza = ''D''
                                   
                        ) as teto_remuneracao
                                ON teto_remuneracao.cod_contrato = contrato.cod_contrato

                        INNER JOIN ( SELECT decimo_evento.*
                                FROM folhapagamento'||stEntidade||'.decimo_evento
                                INNER JOIN( select cod_evento
                                                ,max( timestamp) as timestamp
                                                from folhapagamento'||stEntidade||'.decimo_evento
                                                group by 1
                                        ) as max_timestamp
                                        on max_timestamp.timestamp = decimo_evento.timestamp
                            ) as decimo_evento
                            ON decimo_evento.cod_evento = teto_remuneracao.cod_evento

                        GROUP BY 1,2,3,4,5

            UNION

            --PENSIONISTA
                        SELECT 
                                 12 as tipo_registro
                               , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                           contrato.registro||''''||1
                                      WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                           contrato.registro||''''||2
                                      WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                           contrato.registro||''''||3
                                END as cod_reduzido_pessoa
                               , sw_cgm_pessoa_fisica.cpf AS num_cpf
                               , 59 as tipo_desconto
                               , teto_remuneracao.cod_evento                                   
                               , SUM(teto_remuneracao.valor) as valor

                        FROM pessoal'||stEntidade||'.contrato_pensionista
    
                        INNER JOIN pessoal'||stEntidade||'.pensionista
                            ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                            AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
    
                        INNER JOIN pessoal'||stEntidade||'.contrato
                            ON contrato.cod_contrato = contrato_pensionista.cod_contrato
    
                        INNER JOIN sw_cgm
                            ON sw_cgm.numcgm = pensionista.numcgm
    
                        INNER JOIN sw_cgm_pessoa_fisica
                            ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                        INNER JOIN ( 
                                   SELECT *
                                       , ''D'' as tipo_calculo
                                   FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                   WHERE natureza = ''D''
                                   UNION
                                   SELECT *
                                       , ''D'' as tipo_calculo                                                                          
                                   FROM recuperarEventosCalculados(3,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                   WHERE natureza = ''D''
                            ) as teto_remuneracao
                                ON teto_remuneracao.cod_contrato = contrato.cod_contrato

                        INNER JOIN ( SELECT decimo_evento.*
                                FROM folhapagamento'||stEntidade||'.decimo_evento
                                INNER JOIN( select cod_evento
                                                ,max( timestamp) as timestamp
                                                from folhapagamento'||stEntidade||'.decimo_evento
                                                group by 1
                                        ) as max_timestamp
                                        on max_timestamp.timestamp = decimo_evento.timestamp
                            ) as decimo_evento
                            ON decimo_evento.cod_evento = teto_remuneracao.cod_evento

                        GROUP BY 1,2,3,4,5                          
            ';

    EXECUTE stSql;
/*
    63 – Desconto de Assistência Médica ou Odontológica 
    (buscar o valor do evento configurado em Gestão Recursos Humanos :: Folha de Pagamento :: Configuração :: Configurar Cálculo de Benefícios 
    campo Evento de Desconto de Plano de Saúde)
*/

    stSql := ' CREATE TEMPORARY TABLE tmp_assistencia_saude AS 
                        SELECT 
                                     12 as tipo_registro
                                   , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                               contrato.registro||''''||1
                                          WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                               contrato.registro||''''||2
                                          WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                               contrato.registro||''''||3
                                    END as cod_reduzido_pessoa
                                   , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                   , 63 as tipo_desconto
                                   , teto_remuneracao.cod_evento                                   
                                   , SUM(teto_remuneracao.valor) as valor

                                FROM pessoal'||stEntidade||'.contrato

                          INNER JOIN folhapagamento'||stEntidade||'.contrato_servidor_periodo
                                  ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
                                 AND contrato_servidor_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'

                          INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                  ON contrato_servidor.cod_contrato = contrato.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                                  ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor
                                  ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                          INNER JOIN sw_cgm_pessoa_fisica
                                  ON sw_cgm_pessoa_fisica.numcgm = servidor.numcgm

                        INNER JOIN ( 
                                   SELECT * 
                                            , ''E'' as tipo_calculo
                                         FROM recuperarEventosCalculados(0,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(2,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''D'' as tipo_calculo
                                         FROM recuperarEventosCalculados(3,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(4,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                          AND desdobramento != ''D''
                                        UNION
                                       SELECT * 
                                            , ''D'' as tipo_calculo
                                         FROM recuperarEventosCalculados(4,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                          AND desdobramento = ''D''
                                   
                        ) as teto_remuneracao
                                ON teto_remuneracao.cod_contrato = contrato.cod_contrato

                        INNER JOIN ( SELECT beneficio_evento.*
                                    FROM folhapagamento'||stEntidade||'.configuracao_beneficio
                                    INNER JOIN( SELECT cod_configuracao                                                   
                                                    , max(timestamp) as timestamp                                        
                                                FROM folhapagamento'||stEntidade||'.configuracao_beneficio                              
                                                GROUP BY cod_configuracao
                                            ) as max_timestamp
                                            on max_timestamp.timestamp = configuracao_beneficio.timestamp
                                    INNER JOIN folhapagamento'||stEntidade||'.beneficio_evento
                                            ON beneficio_evento.cod_configuracao    = configuracao_beneficio.cod_configuracao
                                            AND beneficio_evento.timestamp          = configuracao_beneficio.timestamp
                                            AND beneficio_evento.cod_tipo = 2
                        ) as beneficio_evento
                            ON teto_remuneracao.cod_evento = teto_remuneracao.cod_evento

                    GROUP BY 1,2,3,4,5
            UNION

            --PENSIONISTA

                        SELECT 
                                  12 as tipo_registro
                                , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                            contrato.registro||''''||1
                                       WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                            contrato.registro||''''||2
                                       WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                            contrato.registro||''''||3
                                 END as cod_reduzido_pessoa
                                , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                , 63 as tipo_desconto
                                , teto_remuneracao.cod_evento                                   
                                , SUM(teto_remuneracao.valor) as valor

                        FROM pessoal'||stEntidade||'.contrato_pensionista
    
                        INNER JOIN pessoal'||stEntidade||'.pensionista
                            ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                            AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
    
                        INNER JOIN pessoal'||stEntidade||'.contrato
                            ON contrato.cod_contrato = contrato_pensionista.cod_contrato
    
                        INNER JOIN sw_cgm
                            ON sw_cgm.numcgm = pensionista.numcgm
    
                        INNER JOIN sw_cgm_pessoa_fisica
                            ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                        INNER JOIN ( 
                                   SELECT * 
                                            , ''E'' as tipo_calculo
                                         FROM recuperarEventosCalculados(0,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(2,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''D'' as tipo_calculo
                                         FROM recuperarEventosCalculados(3,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(4,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                          AND desdobramento != ''D''
                                        UNION
                                       SELECT * 
                                            , ''D'' as tipo_calculo
                                         FROM recuperarEventosCalculados(4,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                          AND desdobramento = ''D''
                                   
                        ) as teto_remuneracao
                                ON teto_remuneracao.cod_contrato = contrato.cod_contrato

                        INNER JOIN ( SELECT beneficio_evento.*
                                    FROM folhapagamento'||stEntidade||'.configuracao_beneficio
                                    INNER JOIN( SELECT cod_configuracao                                                   
                                                    , max(timestamp) as timestamp                                        
                                                FROM folhapagamento'||stEntidade||'.configuracao_beneficio                              
                                                GROUP BY cod_configuracao
                                            ) as max_timestamp
                                            on max_timestamp.timestamp = configuracao_beneficio.timestamp
                                    INNER JOIN folhapagamento'||stEntidade||'.beneficio_evento
                                            ON beneficio_evento.cod_configuracao    = configuracao_beneficio.cod_configuracao
                                            AND beneficio_evento.timestamp          = configuracao_beneficio.timestamp
                                            AND beneficio_evento.cod_tipo = 2
                        ) as beneficio_evento
                            ON teto_remuneracao.cod_evento = teto_remuneracao.cod_evento

                    GROUP BY 1,2,3,4,5
    ';
    EXECUTE stSql;

/*
    64 – Desconto de Férias 
    (buscar o valor do evento configurado em 
    Gestão Recursos Humanos :: Folha de Pagamento :: Configuração :: Configurar Férias 
    campo *Evento de Desconto Férias Mês Anterior das folhas salario e/ou férias )
*/

    stSql := ' CREATE TEMPORARY TABLE tmp_configuracao_ferias AS 
                            SELECT 
                                     12 as tipo_registro
                                   , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                               contrato.registro||''''||1
                                          WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                               contrato.registro||''''||2
                                          WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                               contrato.registro||''''||3
                                    END as cod_reduzido_pessoa
                                   , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                   , 64 as tipo_desconto
                                   , teto_remuneracao.cod_evento                                   
                                   , SUM(teto_remuneracao.valor) as valor

                                FROM pessoal'||stEntidade||'.contrato

                          INNER JOIN folhapagamento'||stEntidade||'.contrato_servidor_periodo
                                  ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
                                 AND contrato_servidor_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'

                          INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                  ON contrato_servidor.cod_contrato = contrato.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                                  ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor
                                  ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                          INNER JOIN sw_cgm_pessoa_fisica
                                  ON sw_cgm_pessoa_fisica.numcgm = servidor.numcgm

                        INNER JOIN (                                    
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza =''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(2,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza =''D''
                        ) as teto_remuneracao
                            ON teto_remuneracao.cod_contrato = contrato.cod_contrato

                        INNER JOIN ( SELECT beneficio_evento.*
                                    FROM folhapagamento'||stEntidade||'.beneficio_evento
                                    INNER JOIN( SELECT cod_tipo                                                   
                                                      ,cod_evento
                                                      ,max(timestamp) as timestamp                                        
                                                FROM folhapagamento'||stEntidade||'.beneficio_evento
                                                GROUP BY 1,2
                                            ) as max_timestamp
                                            on max_timestamp.timestamp = beneficio_evento.timestamp
                                    WHERE beneficio_evento.cod_tipo = 1
                        ) as beneficio_evento
                            ON beneficio_evento.cod_evento = teto_remuneracao.cod_evento
                    GROUP BY 1,2,3,4,5
            
            UNION

            --PENSIONISTA
                        SELECT 
                                  12 as tipo_registro
                                , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                            contrato.registro||''''||1
                                       WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                            contrato.registro||''''||2
                                       WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                            contrato.registro||''''||3
                                 END as cod_reduzido_pessoa
                                , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                , 64 as tipo_desconto
                                , teto_remuneracao.cod_evento                                   
                                , SUM(teto_remuneracao.valor) as valor

                        FROM pessoal'||stEntidade||'.contrato_pensionista
    
                        INNER JOIN pessoal'||stEntidade||'.pensionista
                            ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                            AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
    
                        INNER JOIN pessoal'||stEntidade||'.contrato
                            ON contrato.cod_contrato = contrato_pensionista.cod_contrato
    
                        INNER JOIN sw_cgm
                            ON sw_cgm.numcgm = pensionista.numcgm
    
                        INNER JOIN sw_cgm_pessoa_fisica
                            ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                        INNER JOIN (                                    
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza =''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(2,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza =''D''
                        ) as teto_remuneracao
                            ON teto_remuneracao.cod_contrato = contrato.cod_contrato

                        INNER JOIN ( SELECT beneficio_evento.*
                                    FROM folhapagamento'||stEntidade||'.beneficio_evento
                                    INNER JOIN( SELECT cod_tipo                                                   
                                                      ,cod_evento
                                                      ,max(timestamp) as timestamp                                        
                                                FROM folhapagamento'||stEntidade||'.beneficio_evento
                                                GROUP BY 1,2
                                            ) as max_timestamp
                                            on max_timestamp.timestamp = beneficio_evento.timestamp
                                    WHERE beneficio_evento.cod_tipo = 1
                        ) as beneficio_evento
                            ON beneficio_evento.cod_evento = teto_remuneracao.cod_evento
                    GROUP BY 1,2,3,4,5

    ';

    EXECUTE stSql;

/*
    99 – Outros Descontos Totalizados 
    (preencher com este código para agrupar os demais eventos de natureza desconto que não se enquadrarem nos requisitos acima)
*/
    stSql := ' CREATE TEMPORARY TABLE tmp_outros AS     
                        SELECT 
                                     12 as tipo_registro
                                   , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                               contrato.registro||''''||1
                                          WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                               contrato.registro||''''||2
                                          WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                               contrato.registro||''''||3
                                    END as cod_reduzido_pessoa
                                   , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                   , 99 as tipo_desconto
                                   , teto_remuneracao.cod_evento                                   
                                   , SUM(teto_remuneracao.valor) as valor

                                FROM pessoal'||stEntidade||'.contrato

                          INNER JOIN folhapagamento'||stEntidade||'.contrato_servidor_periodo
                                  ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
                                 AND contrato_servidor_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'

                          INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                                  ON contrato_servidor.cod_contrato = contrato.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                                  ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

                          INNER JOIN pessoal'||stEntidade||'.servidor
                                  ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                          INNER JOIN sw_cgm_pessoa_fisica
                                  ON sw_cgm_pessoa_fisica.numcgm = servidor.numcgm

                          INNER JOIN ( 
                                   SELECT * 
                                            , ''E'' as tipo_calculo
                                         FROM recuperarEventosCalculados(0,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(2,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''D'' as tipo_calculo
                                         FROM recuperarEventosCalculados(3,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(4,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                          AND desdobramento != ''D''
                                        UNION
                                       SELECT * 
                                            , ''D'' as tipo_calculo
                                         FROM recuperarEventosCalculados(4,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                          AND desdobramento = ''D''
                                   
                            ) as teto_remuneracao
                                ON teto_remuneracao.cod_contrato = contrato.cod_contrato
                        GROUP BY 1,2,3,4,5

            UNION 
            --PENSIONISTA
                        SELECT 
                                  12 as tipo_registro
                                , CASE WHEN teto_remuneracao.tipo_calculo = ''M'' THEN
                                            contrato.registro||''''||1
                                       WHEN teto_remuneracao.tipo_calculo = ''D'' THEN
                                            contrato.registro||''''||2
                                       WHEN teto_remuneracao.tipo_calculo = ''E'' THEN
                                            contrato.registro||''''||3
                                 END as cod_reduzido_pessoa
                                , sw_cgm_pessoa_fisica.cpf AS num_cpf
                                , 99 as tipo_desconto                     
                                , teto_remuneracao.cod_evento
                                , SUM(teto_remuneracao.valor) as valor

                        FROM pessoal'||stEntidade||'.contrato_pensionista
    
                        INNER JOIN pessoal'||stEntidade||'.pensionista
                            ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                            AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
    
                        INNER JOIN pessoal'||stEntidade||'.contrato
                            ON contrato.cod_contrato = contrato_pensionista.cod_contrato
    
                        INNER JOIN sw_cgm
                            ON sw_cgm.numcgm = pensionista.numcgm
    
                        INNER JOIN sw_cgm_pessoa_fisica
                            ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                        INNER JOIN ( 
                                   SELECT * 
                                            , ''E'' as tipo_calculo
                                         FROM recuperarEventosCalculados(0,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(1,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(2,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''D'' as tipo_calculo
                                         FROM recuperarEventosCalculados(3,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                        UNION
                                       SELECT * 
                                            , ''M'' as tipo_calculo
                                         FROM recuperarEventosCalculados(4,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                          AND desdobramento != ''D''
                                        UNION
                                       SELECT * 
                                            , ''D'' as tipo_calculo
                                         FROM recuperarEventosCalculados(4,'''||inCodPeriodoMovimentacao||''',0,0,'''||stEntidade||''','''')
                                         WHERE natureza = ''D''
                                          AND desdobramento = ''D''
                                   
                            ) as teto_remuneracao
                                ON teto_remuneracao.cod_contrato = contrato.cod_contrato
                        GROUP BY 1,2,3,4,5

    ';

    EXECUTE stSql;


/*

    REALIZANDO O SOMATORIO DOS DADOS PARA O ARQUIVO

*/

    stSql := '  
                SELECT tipo_registro
                       ,cod_reduzido_pessoa
                       ,num_cpf
                       ,tipo_desconto
                       ,SUM(valor) as valor
                FROM tmp_teto_remuneracao
                GROUP BY 1,2,3,4
                UNION
                SELECT tipo_registro
                       ,cod_reduzido_pessoa
                       ,num_cpf
                       ,tipo_desconto
                       ,SUM(valor) as valor
                FROM tmp_teto_ferias
                GROUP BY 1,2,3,4
                UNION
                SELECT tipo_registro
                       ,cod_reduzido_pessoa
                       ,num_cpf
                       ,tipo_desconto
                       ,SUM(valor) as valor
                FROM tmp_teto_decimo
                GROUP BY 1,2,3,4
                UNION
                SELECT tipo_registro
                       ,cod_reduzido_pessoa
                       ,num_cpf
                       ,tipo_desconto
                       ,SUM(valor) as valor
                FROM tmp_irrf_previdencia
                GROUP BY 1,2,3,4
                UNION
                SELECT tipo_registro
                       ,cod_reduzido_pessoa
                       ,num_cpf
                       ,tipo_desconto
                       ,SUM(valor) as valor
                FROM tmp_parcela_decimo
                GROUP BY 1,2,3,4
                UNION
                SELECT tipo_registro
                       ,cod_reduzido_pessoa
                       ,num_cpf
                       ,tipo_desconto
                       ,SUM(valor) as valor
                FROM tmp_assistencia_saude
                GROUP BY 1,2,3,4
                UNION
                SELECT tipo_registro
                       ,cod_reduzido_pessoa
                       ,num_cpf
                       ,tipo_desconto
                       ,SUM(valor) as valor
                FROM tmp_configuracao_ferias
                GROUP BY 1,2,3,4
                UNION
                SELECT tipo_registro
                       ,cod_reduzido_pessoa
                       ,num_cpf
                       ,tipo_desconto
                       ,SUM(valor) as valor
                FROM tmp_outros
                WHERE tmp_outros.cod_evento NOT IN (SELECT cod_evento FROM tmp_teto_remuneracao
                                                    UNION
                                                    SELECT cod_evento FROM tmp_teto_ferias
                                                    UNION
                                                    SELECT cod_evento FROM tmp_teto_decimo
                                                    UNION
                                                    SELECT cod_evento FROM tmp_irrf_previdencia
                                                    UNION
                                                    SELECT cod_evento FROM tmp_parcela_decimo
                                                    UNION
                                                    SELECT cod_evento FROM tmp_assistencia_saude
                                                    UNION
                                                    SELECT cod_evento FROM tmp_configuracao_ferias
                                                    )
                GROUP BY 1,2,3,4
    ';

    FOR reRegistro IN EXECUTE stSql LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_teto_remuneracao;
    DROP TABLE tmp_teto_ferias;
    DROP TABLE tmp_teto_decimo;
    DROP TABLE tmp_irrf_previdencia;
    DROP TABLE tmp_parcela_decimo;
    DROP TABLE tmp_assistencia_saude;
    DROP TABLE tmp_configuracao_ferias;
    DROP TABLE tmp_outros;

END
$$ LANGUAGE plpgsql;