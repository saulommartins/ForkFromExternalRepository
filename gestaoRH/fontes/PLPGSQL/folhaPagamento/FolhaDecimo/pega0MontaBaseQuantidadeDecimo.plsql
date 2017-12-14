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
--    * Data de Criação: 09/09/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23097 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-05 17:53:30 -0300 (Ter, 05 Jun 2007) $
--
--    * Casos de uso: uc-04.05.11
--*/


CREATE OR REPLACE FUNCTION pega0MontaBaseQuantidadeDecimo(varchar) RETURNS numeric as $$

DECLARE
    stLista                 ALIAS FOR $1;
    nuQuantidadeBase        NUMERIC := 0.00;
    inPosicaoDelimitador    INTEGER := 0;
    stEvento                VARCHAR := '';
    stListaEventos          VARCHAR := '';
    inTamanhoMascaraEvento  INTEGER := 1;
    inCodConfiguracao       INTEGER := 1;
    stEventoFormatado       VARCHAR := '';
    inCodEvento             INTEGER := 0;
    stNaturezaEvento        character(1) := 'P';
    stCodigoEvento          VARCHAR := '';
    stDesdobramento         VARCHAR := '';
    stParteEvento           VARCHAR := '';
    nuValor                 NUMERIC := 0;
    nuQuantidade NUMERIC := 0;
    inControle INTEGER := 0;
BEGIN
    stListaEventos := ltrim( stLista );
    inTamanhoMascaraEvento := pega0TamanhoMascaraEvento();
    inCodConfiguracao := recuperarBufferInteiro('inCodConfiguracao');
    WHILE char_length(stListaEventos ) > 0
    LOOP
        inPosicaoDelimitador := position( ';' IN stListaEventos);
        IF inPosicaoDelimitador <= 0 THEN
            inPosicaoDelimitador := (char_length( stListaEventos )+1);
        END IF;
        stEvento := substr( stListaEventos,1,(inPosicaoDelimitador-1));
        stCodigoEvento  := '';
        stDesdobramento := '';
        WHILE char_length(stEvento ) > 0
        LOOP
            stParteEvento := substr(stEvento,1,1);
            IF is_number( stParteEvento ) THEN
                stCodigoEvento := stCodigoEvento || stParteEvento ; 
            ELSE
                stDesdobramento := UPPER(stParteEvento) ;
            END IF;
            stEvento := substr(stEvento,2,char_length(stEvento) );
        END LOOP;
        stEventoFormatado := lpad( stCodigoEvento, inTamanhoMascaraEvento, '0');
        inCodEvento :=  pega0CodigoEventoPeloNumero(stEventoFormatado);
        stNaturezaEvento := pega0NaturezaEvento( inCodEvento );    
        stDesdobramento := recuperarBufferTexto('stDesdobramento');
        IF stNaturezaEvento = 'D' THEN
            IF (stDesdobramento = '') THEN
                inControle := recuperarBufferInteiro('inControle');
                inControle := criarBufferInteiro('inControle',(inControle+1));
                nuQuantidadeBase := nuQuantidadeBase - pegaQuantidadeCalculado( stEventoFormatado, inCodConfiguracao  );
                inControle := criarBufferInteiro('inControle',(inControle-1));
            ELSE       
                nuQuantidadeBase := nuQuantidadeBase - pegaQuantidadeCalculadoDecimo( stEventoFormatado, stDesdobramento, inCodConfiguracao  );
            END IF;
        ELSE
            IF (stDesdobramento = '') THEN
                inControle := recuperarBufferInteiro('inControle');
                inControle := criarBufferInteiro('inControle',(inControle+1));
                nuQuantidadeBase := nuQuantidadeBase + pegaQuantidadeCalculado( stEventoFormatado, inCodConfiguracao  );
                inControle := criarBufferInteiro('inControle',(inControle-1));
            ELSE
                nuQuantidadeBase := nuQuantidadeBase + pegaQuantidadeCalculadoDecimo( stEventoFormatado, stDesdobramento, inCodConfiguracao  );
            END IF;
        END IF;    
        stListaEventos := substr( stListaEventos, (inPosicaoDelimitador+1) ) ;
    END LOOP;
    RETURN nuQuantidadeBase;
END;
$$ LANGUAGE 'plpgsql';


