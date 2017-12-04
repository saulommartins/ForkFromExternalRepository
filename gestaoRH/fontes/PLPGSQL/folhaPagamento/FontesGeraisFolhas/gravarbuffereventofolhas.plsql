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
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 25871 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-10-08 09:34:30 -0300 (Seg, 08 Out 2007) $
--
--    * Casos de uso: uc-04.05.09
--    * Casos de uso: uc-04.05.10
--*/

CREATE OR REPLACE FUNCTION gravarBufferEventoFolhas(NUMERIC,NUMERIC) RETURNS BOOLEAN AS $$
DECLARE
    nuValor             ALIAS FOR $1;
    nuQuantidade        ALIAS FOR $2;
    nuValorEvento       NUMERIC;
    nuQuantidadeEvento  NUMERIC;
    boRetorno           BOOLEAN:=TRUE;
    boEventoSistema     BOOLEAN;
    stPagar13Ferias     VARCHAR:='F';
    stCodigoEvento      VARCHAR:='';
    stTipoFolha         VARCHAR:='';
    stSql               VARCHAR:='';
    desdobramento       VARCHAR:='';
    stNatureza          VARCHAR:='';
    stEventoSistema     VARCHAR:='';
    inCodConfiguracao   INTEGER;
    inControle     INTEGER;
    crCursor            REFCURSOR;

    nuQuantidadeAux     NUMERIC;
    nuValorAux          NUMERIC;


BEGIN
    inControle := recuperarBufferInteiro('inControle');
    inCodConfiguracao  := recuperarBufferInteiro('inCodConfiguracao');

    nuQuantidadeAux := arredondar( nuQuantidade ,2 ); 
    nuValorAux := arredondar( nuValor ,2 ); 
    
    stCodigoEvento := recuperarBufferTextoPilha('stCodigoEvento');   
    IF inControle > 1 THEN
        nuQuantidadeEvento := criarBufferNumerico(stCodigoEvento||inCodConfiguracao||'QuantidadeFixo',nuQuantidadeAux);
        nuValorEvento      := criarBufferNumerico(stCodigoEvento||inCodConfiguracao||'ValorFixo', nuValorAux);        
    ELSE
        nuValorEvento      := criarBufferNumerico('nuValor', nuValorAux);
        nuQuantidadeEvento := criarBufferNumerico('nuQuantidade', nuQuantidadeAux );
        stTipoFolha        := recuperarBufferTexto('stTipoFolha');
        --Complementar
        IF stTipoFolha = 'C' THEN
            nuQuantidadeEvento := criarBufferNumerico(stCodigoEvento||inCodConfiguracao||'Quantidade',nuQuantidadeAux );
            nuValorEvento      := criarBufferNumerico(stCodigoEvento||inCodConfiguracao||'Valor', nuValorAux );
        END IF;
        --Salário  
        IF stTipoFolha = 'S' THEN
            nuQuantidadeEvento := criarBufferNumerico(stCodigoEvento||inCodConfiguracao||'Quantidade',nuQuantidadeAux );
            nuValorEvento      := criarBufferNumerico(stCodigoEvento||inCodConfiguracao||'Valor', nuValorAux );
        END IF;

        --Férias
        IF stTipoFolha = 'F' THEN
            stNatureza          := recuperarBufferTexto('stNatureza');
            stPagar13Ferias     := recuperarBufferTexto('stPagar13Ferias');
            stEventoSistema     := recuperarBufferTexto('stEventoSistema');

            IF (stNatureza = 'P' AND stEventoSistema ='nao' AND stPagar13Ferias ='t' ) THEN
               desdobramento      := recuperarBufferTexto('stDesdobramento');
               nuQuantidadeEvento := criarBufferNumerico(stCodigoEvento||desdobramento||inCodConfiguracao||'Quantidade',nuQuantidadeAux );
               nuValorEvento      := criarBufferNumerico(stCodigoEvento||desdobramento||inCodConfiguracao||'Valor', nuValorAux );
            ELSE
               desdobramento      := recuperarBufferTexto('stDesdobramento');
               nuQuantidadeEvento := criarBufferNumerico(stCodigoEvento||desdobramento||inCodConfiguracao||'Quantidade',nuQuantidadeAux );
               nuValorEvento      := criarBufferNumerico(stCodigoEvento||desdobramento||inCodConfiguracao||'Valor', nuValorAux );
            END IF;
        END IF;
        --Décimo
        IF stTipoFolha = 'D' THEN
            desdobramento:= recuperarBufferTexto('stDesdobramento');
            nuQuantidadeEvento := criarBufferNumerico(stCodigoEvento||desdobramento||inCodConfiguracao||'Quantidade',nuQuantidadeAux );
            nuValorEvento      := criarBufferNumerico(stCodigoEvento||desdobramento||inCodConfiguracao||'Valor', nuValorAux );
        END IF;
        --Rescisão
        IF stTipoFolha = 'R' THEN
            desdobramento:= recuperarBufferTexto('stDesdobramento');
            nuQuantidadeEvento := criarBufferNumerico(stCodigoEvento||desdobramento||inCodConfiguracao||'Quantidade',nuQuantidadeAux );
            nuValorEvento      := criarBufferNumerico(stCodigoEvento||desdobramento||inCodConfiguracao||'Valor', nuValorAux );
        END IF;
    END IF;
    RETURN boRetorno;
END;
$$ LANGUAGE 'plpgsql';
