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
CREATE OR REPLACE FUNCTION realizacaoreceitafixa(character varying, numeric, character varying, integer, character varying, integer, numeric, numeric, integer) RETURNS INTEGER AS $$
 
DECLARE                                                                                                                                                             
    EXERCICIO ALIAS FOR $1;                                                                                                                                              
    VALOR ALIAS FOR $2;                                                                                                                                                  
    COMPLEMENTO ALIAS FOR $3;                                                                                                                                            
    CODLOTE ALIAS FOR $4;                                                                                                                                                
    TIPOLOTE ALIAS FOR $5;                                                                                                                                               
    CODENTIDADE ALIAS FOR $6;                                                                                                                                            
    VALOR1 ALIAS FOR $7;                                                                                                                                                 
    VALOR2 ALIAS FOR $8;                                                                                                                                                 
    CODHISTORICO ALIAS FOR $9;                                                                                                                                           
                                                                                                                                                                     
    CODHISTORICOINT INTEGER := 907;                                                                                                                                    
    SEQUENCIA INTEGER := 0;                                                                                                                                            
BEGIN                                                                                                                                                                
    IF  CODHISTORICO  IS NOT NULL THEN                                                                                                                                  
        CODHISTORICOINT := CODHISTORICO;                                                                                                                                 
    END IF;                                                                                                                                                              
    SEQUENCIA := FAZERLANCAMENTO(  '191140000000000' , '191110000000000' , CODHISTORICOINT , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );      
    IF VALOR1 > 0 THEN                                                                                                                                                   
        SEQUENCIA := FAZERLANCAMENTO(  '513120200010100' , '122110000010100' , CODHISTORICOINT , EXERCICIO , VALOR1 , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  ); 
    END IF;                                                                                                                                                              
    IF VALOR2 > 0 THEN                                                                                                                                                   
        SEQUENCIA := FAZERLANCAMENTO(  '513120200020200' , '122110000020200' , CODHISTORICOINT , EXERCICIO , VALOR2 , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  ); 
    END IF;                                                                                                                                                              
RETURN SEQUENCIA;                                                                                                                                                    
END;
$$ language 'plpgsql';                                                                                                                                                                 
