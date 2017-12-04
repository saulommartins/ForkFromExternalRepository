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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_juros_mariana.plsql 63702 2015-09-30 19:20:45Z evandro $
*
* Caso de uso: uc-05.03.00
* Calculo Valor de Juros para Mariana
*/ 
CREATE OR REPLACE FUNCTION fn_juros_mariana(dtVencimento date, dtDataCalculo date, nuValor numeric, inCodAcrescimo integer, inCodTipo integer) RETURNS numeric as $$
    DECLARE
        nuJuros         NUMERIC = 0.00;
        nuRetorno       NUMERIC = 0.00;
        inDiff          INTEGER;
        nuJuroCorrente  numeric = 0.0;
        nuJuroTotal     numeric = 0.0;
        inMesInicio     integer;
        inMesFim        integer;
        inAnoInicio     integer;
	inAnoFim 	integer;
	inMes 		integer;
	stFuncao 	varchar;
    nuCorrecao  NUMERIC;
	nuMulta 	NUMERIC = 0.00;
	stSQL 		varchar;
	reFormula 	RECORD;
	inDiffMes       integer;
	usefulDay 	date;

    inMesLoop    integer; 
    BEGIN
       -- Calculo de Juros simples                                                                            
        
        inDiff      := diff_datas_em_meses(dtVencimento,dtDataCalculo);	
        inMesInicio := date_part('month' , dtVencimento )::integer ;
        inMesFim    := date_part('month' , dtDataCalculo )::integer;
        inAnoInicio := date_part('year' , dtVencimento )::integer;	
	inAnoFim    := date_part('year' , dtDataCalculo )::integer;	

--calcula valor Multa
	   SELECT * INTO reFormula
	     FROM monetario.formula_acrescimo
	    WHERE cod_acrescimo = 3 and cod_tipo = 3
		ORDER BY timestamp desc
		LIMIT 1 ;

	   SELECT nom_funcao INTO stFuncao
             FROM administracao.funcao
   	    WHERE cod_modulo 	 = reFormula.cod_modulo
	      AND cod_biblioteca = reFormula.cod_biblioteca
	      AND cod_funcao 	 = reFormula.cod_funcao ;	

	  stSQL   := 'SELECT '||stFuncao||'('''||dtVencimento||'''::date,'''||dtDataCalculo||'''::date,'||nuValor||',3,3)';


	  EXECUTE stSQL INTO nuMulta;

	usefulDay = next_useful_day(dtVencimento);
        --usefulDay = next_useful_day(dtVencimento-1);

	IF dtDataCalculo <= usefulDay THEN
   --   nuRetorno := (nuValor + nuMulta) * (1.00 /100);
        nuRetorno := 0.00;
	ELSE
        IF ( inDiff > 0 ) THEN			   
--se for ultimo dia util do mes nao conta o mes
	inDiffMes = diff_datas_em_meses(dtVencimento, usefulDay);

	IF inDiffMes = 1 THEN
	   inMesInicio := inMesInicio + 1;
        END IF;


inMesLoop := inMesInicio;

	FOR iAno IN inAnoInicio..inAnoFim LOOP
        -- recupera valor do juro do mes corrente
		FOR iMes in inMesLoop..12 LOOP			
			EXIT WHEN( iAno = inAnoFim AND iMes = inMesFim );			   			

	       	        select valor 
        	          into nuJuroCorrente
        	          from monetario.valor_acrescimo 	
        	         where valor_acrescimo.cod_acrescimo = inCodAcrescimo
        	           and valor_acrescimo.cod_tipo      = inCodTipo
        	           and date_part('month' , valor_acrescimo.inicio_vigencia ) = iMes
        	           and date_part('year' , valor_acrescimo.inicio_vigencia )  = iAno
        	         order by valor_acrescimo.inicio_vigencia DESC LIMIT 1;

        	        -- soma juro do mes a soma total
			IF nuJuroCorrente IS NOT NULL THEN
	    		   nuJuroTotal := nuJuroTotal + nuJuroCorrente;
			END IF;
		END LOOP;
        inMesLoop := 1;
  	END LOOP;

        	    --adciona 1% pelo ultimo mes
        	    nuJuroTotal := nuJuroTotal + 1.00;

        nuCorrecao := fn_correcao_mariana(dtVencimento,dtDataCalculo,nuValor, 5 , 1);
        nuRetorno  := (nuValor + nuMulta + nuCorrecao ) * ( nuJuroTotal / 100 ); 
	ELSE 	
		nuRetorno := nuValor * (1.00 /100);
        END IF;
	END IF;

        RETURN nuRetorno::numeric(14,2);
    END;

$$language 'plpgsql';
           
