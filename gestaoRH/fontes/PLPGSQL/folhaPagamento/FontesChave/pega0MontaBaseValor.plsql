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
--
-- script de funcao PLSQL
--
-- URBEM Solugues de Gestco Pzblica Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23095 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2006/05/11 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: recebe string de eventos delimitados por ';' e retorna 
-- o somatorio ( conforme provento/desconto ) dos buffers dos eventos
--
--
--

CREATE OR REPLACE FUNCTION pega0MontaBaseValor(varchar) RETURNS numeric as $$

DECLARE
    stLista          ALIAS FOR $1;

    nuValorBase             NUMERIC := 0.00;
    inPosicaoDelimitador    INTEGER := 0;
    stEvento                VARCHAR := '';
    stListaEventos          VARCHAR := '';
    inTamanhoMascaraEvento  INTEGER := 1;
    inCodConfiguracao       INTEGER := 1;
    stEventoFormatado       VARCHAR := '';
    inCodEvento             INTEGER := 0;
    stNaturezaEvento        character(1) := 'P';
BEGIN

    stListaEventos := ltrim( stLista );

    inTamanhoMascaraEvento := pega0TamanhoMascaraEvento();

    inCodConfiguracao := recuperarBufferInteiro('inCodConfiguracao');
    
    WHILE char_length(stListaEventos ) > 0 LOOP
        inPosicaoDelimitador := position( ';' IN stListaEventos);

        IF inPosicaoDelimitador <= 0 THEN
            inPosicaoDelimitador := (char_length( stListaEventos )+1);
        END IF;

        stEvento := substr( stListaEventos,1,(inPosicaoDelimitador-1));

        stEventoFormatado = lpad( stEvento, inTamanhoMascaraEvento, '0');

        inCodEvento =  pega0CodigoEventoPeloNumero(stEventoFormatado);

        stNaturezaEvento = pega0NaturezaEvento( inCodEvento );    
        IF stNaturezaEvento = 'D' THEN 
            nuValorBase := nuValorBase - pegaValorCalculado( stEventoFormatado, inCodConfiguracao  );
        ELSE
            nuValorBase := nuValorBase + pegaValorCalculado( stEventoFormatado, inCodConfiguracao  );
        END IF;    

        stListaEventos := substr( stListaEventos, (inPosicaoDelimitador+1) ) ;

    END LOOP;

    RETURN nuValorBase;
END;
$$ LANGUAGE 'plpgsql';


