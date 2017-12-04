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
* Date: 2005/1/14 10:50:00 $
*
* Caso de uso: uc-04.05.48
*
* Objetivo: recebe timestamp ( que tanto pode ser a de referencia do mes 
* - fim competencia - ou o retorno da funcao que traz o timestamp exato,
* ja tratado. Recebe , tambem, o valor da base liquida de ir , ou seja, 
* o valor da base de irrf ja diminuida das deducoes devidas ( dependentes, 
* previdencia, pensao alimenticia - quando FOR no segundo calculo , etc)
* O codigo da tabela de irrf assumida como default = 1

*/


CREATE OR REPLACE FUNCTION pega0DescontoIrrfNaData(varchar,numeric) RETURNS numeric as '

DECLARE
    stTimestamp              ALIAS FOR $1;
    nuValorBaseComDeducao    ALIAS FOR $2;

    inCodTabela              INTEGER := 1;
    dtTimestamp              DATE;
    nuValorDesconto          NUMERIC := 0.00;

    stSql                    VARCHAR := '''';
    reRegistro               RECORD;

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    dtTimestamp := to_date(substr( stTimestamp ,1,10),''yyyy-mm-dd'');

    stSql := '' SELECT 
                  COALESCE( aliquota,0) as aliquota 
                 , COALESCE( parcela_deduzir,0) as parcela_deduzir

              FROM folhapagamento''||stEntidade||''.tabela_irrf as ti

              LEFT OUTER JOIN folhapagamento''||stEntidade||''.faixa_desconto_irrf as fdi
                ON fdi.timestamp  = ti.timestamp
               AND fdi.cod_tabela = ti.cod_tabela
               AND ''||nuValorBaseComDeducao||'' between fdi.vl_inicial AND fdi.vl_final 

             WHERE ti.cod_tabela = ''||inCodTabela||''
               AND ti.vigencia  <= ''''''||dtTimesTamp||''''''

              ORDER BY ti.timestamp desc

              LIMIT 1
            '' ;


    FOR reRegistro IN  EXECUTE stSql
    LOOP

       IF reRegistro.aliquota is null  THEN
          nuValorDesconto := 0.00;
       ELSE  
          nuValorDesconto := round( nuValorBaseComDeducao * (reRegistro.aliquota / 100) - reRegistro.parcela_deduzir ,2);

          IF nuValorDesconto < 0 THEN
              nuValorDesconto := 0.00;
          END IF;
       END IF;

    END LOOP;

    RETURN nuValorDesconto;
END;
' LANGUAGE 'plpgsql';

