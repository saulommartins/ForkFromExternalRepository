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
--    * Data de Criação: 00/00/0000
--
--
--    * @author Projetista: Vandré Miguel Ramos
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 26851 $
--    $Author: souzadl $
--    $Date: 2007-11-20 15:48:41 -0200 (Ter, 20 Nov 2007) $
--
--    * Casos de uso: uc-04.05.24
--*/

CREATE OR REPLACE FUNCTION mediaDecimoSomatorioPorDoze() RETURNS Numeric as $$
DECLARE
    inCodEvento                 INTEGER := 0;
    stSql                       VARCHAR := '';
    crCursor                    REFCURSOR;
    nuValor                     NUMERIC := 0;
    nuQuantidade                NUMERIC := 0;
    stFixado                    VARCHAR := 'V';
    nuRetorno                   NUMERIC := 0;
    stLido_de                   VARCHAR := 'evento_calculado';
    nuPercentualAdiantamento    NUMERIC := 0.00;
    inDivisao                   INTEGER;
    inMesPagamentoSaldo         INTEGER;
    stEntidade                  VARCHAR;
    stExercicio                 VARCHAR;
    stSituacaoDaFolha           VARCHAR;
    inCodPeriodoMovimentacao    INTEGER;
BEGIN
    inCodEvento              := recuperarBufferInteiro( 'inCodEvento' );  
    nuPercentualAdiantamento := recuperarBufferNumerico('nuPercentualAdiantamento'); 
    stEntidade               := recuperarBufferTexto('stEntidade');
    stExercicio              := recuperarBufferTexto('exercicio');
    inCodPeriodoMovimentacao := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    stSituacaoDaFolha        := pega0SituacaoDaFolhaSalario();
    
    stSql := '    SELECT valor::integer as mes_pagamento_saldo
                    FROM administracao.configuracao
                   WHERE parametro = ''mes_calculo_decimo'||stEntidade||'''
                     AND exercicio = (  SELECT to_char(dt_final,''yyyy'') 
                                          FROM folhapagamento'||stEntidade||'.periodo_movimentacao 
                                         WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                     )
                     AND cod_modulo = 27';
    inMesPagamentoSaldo := selectIntoInteger(stSql);

    IF inMesPagamentoSaldo = 11 THEN
        inDivisao := 11;
        -- VERIFICA SE NÃO É ADIANTAMENTO DE DÉCIMO TERCEIRO E SE A FOLHA SALÁRIO NÃO ESTÁ FECHADA    
        IF nuPercentualAdiantamento = 0 AND stSituacaoDaFolha = 'a' THEN
            inDivisao := 10;
        END IF;
    ELSE
        inDivisao := 12;
        -- VERIFICA SE NÃO É ADIANTAMENTO DE DÉCIMO TERCEIRO E SE A FOLHA SALÁRIO NÃO ESTÁ FECHADA    
        IF nuPercentualAdiantamento = 0 AND stSituacaoDaFolha = 'a' THEN
            inDivisao := 11;
        END IF;        
    END IF;
    
    stSql := '  SELECT  fixado
                     , ROUND( COALESCE(SUM(valor),0) / '||inDivisao||', 2) as valor
                     , ROUND( COALESCE(SUM(quantidade),0) / '||inDivisao||' , 2) as quantidade
                  FROM tmp_registro_evento_13
                 WHERE cod_evento = '||inCodEvento||'
                   AND (SUBSTR(lido_de,1,16) ILIKE  '''||stLido_de||''' OR SUBSTR(lido_de,1,29) ILIKE  ''evento_complementar_calculado'')
              GROUP BY fixado';

    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO stFixado, nuValor, nuQuantidade;
    CLOSE crCursor;

    IF stFixado = 'V' THEN
      nuRetorno := nuValor;
    ELSE
      nuRetorno := nuQuantidade;
    END IF;

    IF nuRetorno is null THEN
      nuRetorno = 0 ;
    END IF;

    --SE O PERCENTUAL  FOR MAIOR QUE ZERO IMPLICA EM DIZER QUE É ADIANTAMENTO E POSSUI PERCENTUAL
    IF nuPercentualAdiantamento > 0 AND pega0ProporcaoAdiantamentoDecimo() IS TRUE THEN
      nuRetorno := nuRetorno * (nuPercentualAdiantamento/100);
    END IF;

    RETURN nuRetorno; 
END;
$$LANGUAGE 'plpgsql';
