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

CREATE OR REPLACE FUNCTION tcepe.recupera_evento_folha_pagamento (INTEGER,INTEGER,VARCHAR,VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    inCodPeriodoMovimentacao                ALIAS FOR $1;
    inCodComplementar                       ALIAS FOR $2;
    stNatureza                              ALIAS FOR $3;
    stEntidade                              ALIAS FOR $4;
    stSql                                   VARCHAR := '';
    reRegistro                              RECORD;
    
BEGIN
    stSql := '
        SELECT COALESCE(SUM(evento_calculado.valor),0.00) AS valor
             , evento_calculado.quantidade
             , evento.codigo
             , evento.cod_evento
             , evento.descricao
             , evento.natureza
             , CASE WHEN evento_calculado.desdobramento IS NULL THEN ''''
               ELSE evento_calculado.desdobramento END AS desdobramento
             , ''calculado'' AS tipo_evento
             , registro_evento_periodo.cod_contrato
          FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
             , folhapagamento'|| stEntidade ||'.evento_calculado
             , folhapagamento'|| stEntidade ||'.evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo
         WHERE registro_evento_periodo.cod_registro             = evento_calculado.cod_registro
           AND evento_calculado.cod_evento                      = evento.cod_evento
           AND evento.cod_evento                                = sequencia_calculo_evento.cod_evento
           AND sequencia_calculo_evento.cod_sequencia           = sequencia_calculo.cod_sequencia
           AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
           AND evento.natureza = ANY(STRING_TO_ARRAY('''|| stNatureza ||''', '',''))
         GROUP BY evento_calculado.quantidade,evento.codigo,evento.cod_evento,evento.descricao,evento.natureza,evento_calculado.desdobramento,tipo_evento,cod_contrato
         
         UNION
         
        SELECT COALESCE(SUM(evento_ferias_calculado.valor),0.00) AS valor
             , evento_ferias_calculado.quantidade
             , evento.codigo
             , evento.cod_evento
             , evento.descricao
             , evento.natureza
             , evento_ferias_calculado.desdobramento
             , ''ferias_calculado'' AS tipo_evento
             , registro_evento_ferias.cod_contrato
          FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
             , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
             , folhapagamento'|| stEntidade ||'.evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo
         WHERE registro_evento_ferias.cod_registro             = evento_ferias_calculado.cod_registro
           AND registro_evento_ferias.desdobramento            = evento_ferias_calculado.desdobramento
           AND registro_evento_ferias.timestamp                = evento_ferias_calculado.timestamp_registro
   	       AND registro_evento_ferias.cod_evento               = evento_ferias_calculado.cod_evento
           AND evento_ferias_calculado.cod_evento              = evento.cod_evento
           AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento
           AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia
           AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
           AND evento.natureza = ANY(STRING_TO_ARRAY('''|| stNatureza ||''', '',''))
         GROUP BY evento_ferias_calculado.quantidade,evento.codigo,evento.cod_evento,evento.descricao,evento.natureza,evento_ferias_calculado.desdobramento,tipo_evento,cod_contrato
         
         UNION
         
        SELECT COALESCE(SUM(evento_decimo_calculado.valor),0.00) AS valor
             , evento_decimo_calculado.quantidade
             , evento.codigo
             , evento.cod_evento
             , evento.descricao
             , evento.natureza
             , evento_decimo_calculado.desdobramento
             , ''decimo_calculado'' AS tipo_evento
             , registro_evento_decimo.cod_contrato
          FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
             , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
             , folhapagamento'|| stEntidade ||'.evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo
         WHERE registro_evento_decimo.cod_registro             = evento_decimo_calculado.cod_registro
           AND registro_evento_decimo.cod_evento               = evento_decimo_calculado.cod_evento
           AND registro_evento_decimo.desdobramento            = evento_decimo_calculado.desdobramento
           AND registro_evento_decimo.timestamp                = evento_decimo_calculado.timestamp_registro
           AND evento_decimo_calculado.cod_evento              = evento.cod_evento
           AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento
           AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia
           AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
           AND evento.natureza = ANY(STRING_TO_ARRAY('''|| stNatureza ||''', '',''))
         GROUP BY evento_decimo_calculado.quantidade,evento.codigo,evento.cod_evento,evento.descricao,evento.natureza,evento_decimo_calculado.desdobramento,tipo_evento,cod_contrato
         
         UNION
         
        SELECT COALESCE(SUM(evento_rescisao_calculado.valor),0.00) AS valor
             , evento_rescisao_calculado.quantidade
             , evento.codigo
             , evento.cod_evento
             , evento.descricao
             , evento.natureza
             , evento_rescisao_calculado.desdobramento
             , ''rescisao_calculado'' AS tipo_evento
             , registro_evento_rescisao.cod_contrato
          FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
             , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
             , folhapagamento'|| stEntidade ||'.evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo
         WHERE registro_evento_rescisao.cod_registro             = evento_rescisao_calculado.cod_registro
           AND registro_evento_rescisao.cod_evento               = evento_rescisao_calculado.cod_evento
           AND registro_evento_rescisao.desdobramento            = evento_rescisao_calculado.desdobramento
           AND registro_evento_rescisao.timestamp                = evento_rescisao_calculado.timestamp_registro
           AND evento_rescisao_calculado.cod_evento              = evento.cod_evento
           AND evento.cod_evento                                 = sequencia_calculo_evento.cod_evento
           AND sequencia_calculo_evento.cod_sequencia            = sequencia_calculo.cod_sequencia
           AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
           AND evento.natureza = ANY(STRING_TO_ARRAY('''|| stNatureza ||''', '',''))
         GROUP BY evento_rescisao_calculado.quantidade,evento.codigo,evento.cod_evento,evento.descricao,evento.natureza,evento_rescisao_calculado.desdobramento,tipo_evento,cod_contrato
         
         UNION
         
        SELECT COALESCE(SUM(evento_complementar_calculado.valor),0.00) AS valor
             , evento_complementar_calculado.quantidade
             , evento.codigo
             , evento.cod_evento
             , evento.descricao
             , evento.natureza
             , CASE WHEN evento_complementar_calculado.desdobramento IS NULL THEN ''''
               ELSE evento_complementar_calculado.desdobramento END AS desdobramento
             , ''complementar_calculado'' AS tipo_evento
             , registro_evento_complementar.cod_contrato
          FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
             , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
             , folhapagamento'|| stEntidade ||'.evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo
         WHERE registro_evento_complementar.cod_registro             = evento_complementar_calculado.cod_registro
           AND registro_evento_complementar.cod_evento               = evento_complementar_calculado.cod_evento
           AND registro_evento_complementar.cod_configuracao         = evento_complementar_calculado.cod_configuracao
           AND registro_evento_complementar.timestamp                = evento_complementar_calculado.timestamp_registro
           AND evento_complementar_calculado.cod_evento              = evento.cod_evento
           AND evento.cod_evento                                     = sequencia_calculo_evento.cod_evento
           AND sequencia_calculo_evento.cod_sequencia                = sequencia_calculo.cod_sequencia
           AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
           AND registro_evento_complementar.cod_complementar         = '|| inCodComplementar ||'
           AND evento.natureza = ANY(STRING_TO_ARRAY('''|| stNatureza ||''', '',''))
         GROUP BY evento_complementar_calculado.quantidade,evento.codigo,evento.cod_evento,evento.descricao,evento.natureza,evento_complementar_calculado.desdobramento,tipo_evento,cod_contrato
        ';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;
    
END;
$$LANGUAGE 'plpgsql';