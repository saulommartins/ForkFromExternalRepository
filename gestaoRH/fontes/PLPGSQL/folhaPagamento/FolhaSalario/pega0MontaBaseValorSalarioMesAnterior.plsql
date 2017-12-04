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
/*
--
-- script de funcao PLSQL
--
-- URBEM Solugues de Gestco Pzblica Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 24425 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2006/07/19 10:50:00 $
--
-- Caso de uso: uc-04.05.09
--
-- Objetivo: recebe string de eventos delimitados por ';' e retorna 
-- o somatorio ( conforme provento/desconto ) dos buffers dos eventos.
-- No caso de ferias os eventos podem ou nao vir com a informacao do desdobramento,
-- Sendo as letras A = abono, F= gozo de ferias   e D = adiantamento de pagamento 
-- de gozo de ferias
-- 
*/


CREATE OR REPLACE FUNCTION pega0MontaBaseValorSalarioMesAnterior(varchar) RETURNS numeric as '

DECLARE    
    stLista                 ALIAS FOR $1;
    nuValorBase             NUMERIC := 0.00;
    inPosicaoDelimitador    INTEGER := 0;
    stEvento                VARCHAR := '''';
    stListaEventos          VARCHAR := '''';
    inTamanhoMascaraEvento  INTEGER := 1;
    inCodConfiguracao       INTEGER := 1;
    stEventoFormatado       VARCHAR := '''';
    inCodEvento             INTEGER := 0;
    stNaturezaEvento        character(1) := ''P'';
    stCodigoEvento          VARCHAR := '''';
    stDesdobramento         VARCHAR := '''';
    stParteEvento           VARCHAR := '''';
    stDataFinalCompetencia  VARCHAR:=''2006-07-31'';
    nuValor                 NUMERIC := 0;
    nuQuantidade            NUMERIC := 0;
    inControle              INTEGER := 0;
BEGIN
    stListaEventos := ltrim( stLista );
    inTamanhoMascaraEvento := pega0TamanhoMascaraEvento();
    inCodConfiguracao := recuperarBufferInteiro(''inCodConfiguracao'');
    WHILE char_length(stListaEventos ) > 0
    LOOP
        inPosicaoDelimitador := position( '';'' IN stListaEventos);
        IF inPosicaoDelimitador <= 0 THEN
           inPosicaoDelimitador := (char_length( stListaEventos )+1);
        END IF;
        stEvento := substr( stListaEventos,1,(inPosicaoDelimitador-1));
        stCodigoEvento  := '''';
        WHILE char_length(stEvento ) > 0
        LOOP
           stParteEvento := substr(stEvento,1,1);
           IF is_number( stParteEvento ) THEN
              stCodigoEvento := stCodigoEvento || stParteEvento ; 
           END IF;

           stEvento := substr(stEvento,2,char_length(stEvento) );
        END LOOP;
        stEventoFormatado = lpad( stCodigoEvento, inTamanhoMascaraEvento, ''0'');
        inCodEvento =  pega0CodigoEventoPeloNumero(stEventoFormatado);
        stNaturezaEvento = pega0NaturezaEvento( inCodEvento ); 

        IF stNaturezaEvento = ''D'' THEN
            nuValorBase := COALESCE(nuValorBase,0.00) - COALESCE(pegaValorCalculadoSalarioMesAnterior( inCodEvento ),0.00);
        ELSE
            nuValorBase := COALESCE(nuValorBase,0.00) + COALESCE(pegaValorCalculadoSalarioMesAnterior( inCodEvento ),0.00);
        END IF;    
        stListaEventos := substr( stListaEventos, (inPosicaoDelimitador+1) ) ;
    END LOOP;

 
    IF nuValorBase is NULL THEN
       nuValorBase := 0;
    END IF;

    RETURN nuValorBase;
END;
' LANGUAGE 'plpgsql';


