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
* script de funcao PLSQL
* 
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br

* $Revision: 23095 $
* $Name$
* $Autor: MArcia $
* Date: 2006/04/20 10:50:00 $
*
* Caso de uso: uc-04.05.14
*
* Objetivo:retorna o valor do teto de desconto de previdencia da previdencia 
* oficial.
*/




CREATE OR REPLACE FUNCTION pega1TetoDescPrevidenciaOficial() RETURNS numeric as '

DECLARE
    inCodPrevidencia        INTEGER;
    stTimestampTabela       VARCHAR;

    stSql                   VARCHAR := '''';
    reRegistro              RECORD;
    dtTimestamp             DATE;
    nuTetoPrevidencia       NUMERIC := 0.00;
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    inCodPrevidencia := recuperarBufferInteiro( ''inCodPrevidenciaOficial'' );
    --    stTimestampTabela := pega1TimestampTabelaPrevidencia();
    stTimestampTabela := recuperarBufferTexto( ''stDataFinalCompetencia'' );

    dtTimestamp := to_date(substr( stTimestampTabela ,1,10),''yyyy-mm-dd'');

    stSql := '' SELECT 
                  COALESCE( valor_final,0) as valor_final 

              FROM folhapagamento''||stEntidade||''.previdencia_previdencia as pp

              JOIN folhapagamento''||stEntidade||''.faixa_desconto as fd
                ON fd.timestamp_previdencia = pp.timestamp
               AND fd.cod_previdencia       = pp.cod_previdencia

             WHERE pp.cod_previdencia = ''||inCodPrevidencia||''
               AND pp.vigencia  <= ''''''||dtTimesTamp||''''''

              ORDER BY fd.timestamp_previdencia desc, pp.vigencia desc, fd.valor_final desc
              LIMIT 1
            '' ;

    FOR reRegistro IN  EXECUTE stSql
    LOOP

       IF reRegistro.valor_final is null  THEN
          nuTetoPrevidencia := 0.00;
       ELSE
          nuTetoPrevidencia := reRegistro.valor_final;
       END IF;

    END LOOP;

    RETURN nuTetoPrevidencia;
END;
' LANGUAGE 'plpgsql';

