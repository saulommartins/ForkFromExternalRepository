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
--    * Data de Criação: 18/04/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23095 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $
--
--    * Casos de uso: uc-04.05.09
--    * Casos de uso: uc-04.05.10
--*/

CREATE OR REPLACE FUNCTION pegaValorCalculadoFixo(VARCHAR,INTEGER) RETURNS NUMERIC AS $$
DECLARE
    stCodigoEventoFixoPar  ALIAS FOR $1;
    inCodConfiguracao      ALIAS FOR $2;
    stSql               VARCHAR := '';
    stFormula           VARCHAR := '';
    stCodigoEventoFixo  VARCHAR := '';
    stEntidade       VARCHAR; 
    crCursor            REFCURSOR;
    nuValorEvento       NUMERIC := 0.00;    
    nuValorFixo         NUMERIC;
    stRetorno           VARCHAR := '';
    inCountTabela       INTEGER := 0;
    inCodEventoFixo     INTEGER;
    inControle          INTEGER;
    boRetorno           BOOLEAN;
BEGIN
    stEntidade              := recuperarBufferTexto('stEntidade');
    stSql := 'SELECT cod_evento 
                                            FROM folhapagamento'||stEntidade||'.registro_evento_fixos 
                                           WHERE codigo = '||quote_literal(stCodigoEventoFixoPar)||'';    
    inCodEventoFixo := selectIntoInteger(stSql);  
    IF inCodEventoFixo IS NOT NULL THEN
                                
        inCodEventoFixo := criarBufferInteiroPilha('inCodEvento',inCodEventoFixo);

        stCodigoEventoFixo := criarBufferTextoPilha('stCodigoEvento',stCodigoEventoFixoPar);

        inControle := recuperarBufferInteiro('inControle');
        inControle := criarBufferInteiro('inControle',(inControle+1));

        stFormula := selectIntoVarchar('SELECT formula 
                                          FROM folhapagamento'||stEntidade||'.registro_evento_fixos 
                                         WHERE codigo = '||quote_literal(stCodigoEventoFixo)||'');

        IF countBufferNumerico(stCodigoEventoFixo||inCodConfiguracao||'valorfixo') = 0 THEN
            stRetorno := executaGCNumerico( stFormula );
        END IF;

        nuValorEvento := recuperarBufferNumerico(stCodigoEventoFixo||inCodConfiguracao||'valorfixo');
      --******************************************
      boRetorno := removerBufferInteiroPilha('inCodEvento');

      --*****************************************
      --Verifica pilha do codigo do evento
      boRetorno := removerBufferTextoPilha('stCodigoEvento');

      --****************************************
      --Verifica pilha do controle
      inControle := recuperarBufferInteiro('inControle');
        IF inControle > 1 THEN
            inControle := criarBufferInteiro('inControle',(inControle-1));
        END IF;
    END IF;
    IF nuValorEvento is not null THEN
        RETURN nuValorEvento;    
    ELSE
        return 0.00;
    END IF;
END;
$$ LANGUAGE 'plpgsql';
