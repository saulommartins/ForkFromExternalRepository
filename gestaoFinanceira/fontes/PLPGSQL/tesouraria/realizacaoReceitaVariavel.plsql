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
CREATE OR REPLACE FUNCTION realizacaoreceitavariavel(character varying, character varying, character varying, numeric, character varying, integer, character varying, integer, integer, integer) RETURNS INTEGER AS $$
DECLARE                                                                                                                                                  
    CONTARECEBIMENTO ALIAS FOR $1;                                                                                                                           
    CLASRECEITA ALIAS FOR $2;                                                                                                                                
    EXERCICIO ALIAS FOR $3;                                                                                                                                  
    VALOR ALIAS FOR $4;                                                                                                                                      
    COMPLEMENTO ALIAS FOR $5;                                                                                                                                
    CODLOTE ALIAS FOR $6;                                                                                                                                    
    TIPOLOTE ALIAS FOR $7;                                                                                                                                   
    CODENTIDADE ALIAS FOR $8;                                                                                                                                
    CODREDUZIDO ALIAS FOR $9;                                                                                                                                
    CODHISTORICO ALIAS FOR $10;                                                                                                                              
                                                                                                                                                         
    SEQUENCIA INTEGER;                                                                                                                                       
    CODHISTORICOINT INTEGER := 907;                                                                                                                          
BEGIN                                                                                                                                                    
    IF CODHISTORICO   IS NOT NULL THEN                                                                                                               
       CODHISTORICOINT := CODHISTORICO;                                                                                                                      
    END IF;                                                                                                                                                  
    SEQUENCIA := FAZERLANCAMENTO(  CONTARECEBIMENTO , CLASRECEITA , CODHISTORICOINT , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  ); 
RETURN SEQUENCIA;                                                                                                                                        
END;                                                                                                                                                     
$$ language 'plpgsql';
