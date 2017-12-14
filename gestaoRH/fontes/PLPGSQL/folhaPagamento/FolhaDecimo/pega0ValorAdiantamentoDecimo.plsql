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
--    * Pl para retorno do somatório dos eventos da natureza proventos do adiantamento de décimo
--    * Data de Criação: 08/11/2007
--
--
--    * @author Diego Lemos de Souza
--
--    * Casos de uso: uc-04.00.00
--
--    $Id: pega0ValorAdiantamentoDecimo.sql 31697 2008-08-04 19:33:31Z souzadl $
--*/
CREATE OR REPLACE FUNCTION pega0ValorAdiantamentoDecimo() RETURNS NUMERIC AS $$
DECLARE
    stSelect        VARCHAR:='';
    inCodContrato   INTEGER;
    inExercicio     INTEGER;
    inCodPeriodoMovimentacao INTEGER;
    stEntidade   VARCHAR:='';
    nuRetorno       NUMERIC;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    inCodContrato := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inExercicio   := to_char(pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao)::timestamp,'yyyy')::INTEGER;
stSelect := '    
SELECT SUM(valor) AS valor
FROM (
SELECT registro_evento_decimo.cod_registro, evento_decimo_calculado.valor
  FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo                                                         
     , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_decimo                                                  
     , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
     , folhapagamento'|| stEntidade ||'.evento
     , folhapagamento'|| stEntidade ||'.periodo_movimentacao
 WHERE registro_evento_decimo.cod_registro = ultimo_registro_evento_decimo.cod_registro              
   AND registro_evento_decimo.cod_evento = ultimo_registro_evento_decimo.cod_evento                  
   AND registro_evento_decimo.timestamp = ultimo_registro_evento_decimo.timestamp                    
   AND registro_evento_decimo.desdobramento = ultimo_registro_evento_decimo.desdobramento            
   AND registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro              
   AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento                  
   AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro                    
   AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento               
   AND registro_evento_decimo.cod_evento = evento.cod_evento
   AND registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
   AND to_char(periodo_movimentacao.dt_final,''yyyy'') =  '|| quote_literal(inExercicio) ||'
   AND registro_evento_decimo.cod_contrato = '|| quote_literal(inCodContrato) ||' 
   AND evento.natureza = ''P''
   AND evento_decimo_calculado.desdobramento = ''A''
UNION
SELECT registro_evento_periodo.cod_registro, evento_calculado.valor
  FROM folhapagamento'|| stEntidade ||'.registro_evento                                                      
     , folhapagamento'|| stEntidade ||'.ultimo_registro_evento                                                  
     , folhapagamento'|| stEntidade ||'.evento_calculado
     , folhapagamento'|| stEntidade ||'.evento
     , folhapagamento'|| stEntidade ||'.periodo_movimentacao
     , folhapagamento'|| stEntidade ||'.registro_evento_periodo
 WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro
   AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro              
   AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento                  
   AND registro_evento.timestamp = ultimo_registro_evento.timestamp                    
   AND registro_evento.cod_registro = evento_calculado.cod_registro              
   AND registro_evento.cod_evento = evento_calculado.cod_evento                  
   AND registro_evento.timestamp = evento_calculado.timestamp_registro                    
   AND registro_evento.cod_evento = evento.cod_evento
   AND registro_evento_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
   AND to_char(periodo_movimentacao.dt_final,''yyyy'') =  '|| quote_literal(inExercicio) ||'
   AND registro_evento_periodo.cod_contrato = '|| quote_literal(inCodContrato) ||' 
   AND evento.natureza = ''P''
   AND evento_calculado.desdobramento = ''I''
) AS DADOS';
   nuRetorno := selectIntoNumeric(stSelect);
   RETURN nuRetorno;
END;   
$$LANGUAGE 'plpgsql';

--SELECT pega0ValorAdiantamentoDecimo();
