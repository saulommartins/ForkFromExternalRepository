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
--    * Data de Criação: 24/08/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23159 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-11 14:49:27 -0300 (Seg, 11 Jun 2007) $
--
--    * Casos de uso: uc-04.04.14
--*/

CREATE OR REPLACE FUNCTION insertRegistroEventoAutomatico(INTEGER,INTEGER,INTEGER,NUMERIC,VARCHAR,VARCHAR) RETURNS BOOLEAN as $$

DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inCodEvento                 ALIAS FOR $3;
    nuQuantidadeValor           ALIAS FOR $4;
    stFixado                    ALIAS FOR $5;
    stProporcional              ALIAS FOR $6;
    inCodRegistro               INTEGER;
    nuValor                     NUMERIC := 0.00;
    nuQuantidade                NUMERIC := 0.00;
    stTimestamp                 TIMESTAMP;
    stSql                       VARCHAR := '';
    stEntidade                  VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    
    IF stFixado = 'V' THEN
        nuValor := nuQuantidadeValor;
    END IF;
    
    IF stFixado = 'Q' THEN
        nuQuantidade := nuQuantidadeValor;
    END IF;
    
    inCodRegistro := selectIntoInteger(' SELECT COALESCE(max(cod_registro)+1,1) as cod_registro
                                 FROM folhapagamento'||stEntidade||'.registro_evento_periodo ');
    
    stTimestamp = now();
    
    stSql := 'INSERT INTO folhapagamento'||stEntidade||'.registro_evento_periodo
                (cod_registro,cod_contrato,cod_periodo_movimentacao)
         VALUES ('||inCodRegistro||','||inCodContrato||','||inCodPeriodoMovimentacao||');';
    EXECUTE stSql;
    
    stSql := 'INSERT INTO folhapagamento'||stEntidade||'.registro_evento (cod_registro,timestamp,cod_evento,automatico,valor,quantidade,proporcional)
                    VALUES ('||inCodRegistro||',TO_TIMESTAMP('''||stTimestamp||''',''yyyy-mm-dd hh24:mi:ss.us''),'||inCodEvento||',true,'||nuValor||','||nuQuantidade||','''||stProporcional||''');';
    EXECUTE stSql;

    stSql := 'INSERT INTO folhapagamento'||stEntidade||'.ultimo_registro_evento (cod_registro,timestamp,cod_evento)
                    VALUES ('||inCodRegistro||',TO_TIMESTAMP('''||stTimestamp||''',''yyyy-mm-dd hh24:mi:ss.us''),'||inCodEvento||');
                    ';    
    EXECUTE stSql;
    
    RETURN true;
END;
$$
LANGUAGE 'plpgsql';