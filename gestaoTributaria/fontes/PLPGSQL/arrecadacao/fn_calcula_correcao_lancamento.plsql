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
* $Id: fn_calcula_correcao_lancamento.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.3  2007/03/19 20:26:14  dibueno
Bug #8416#

Revision 1.2  2007/03/12 21:25:18  dibueno
*** empty log message ***

Revision 1.1  2007/02/07 12:52:28  dibueno
Melhorias da consulta da arrecadacao

Revision 1.4  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.calcula_correcao_lancamento( VARCHAR, DATE ) RETURNS VARCHAR AS '
DECLARE
    
    stNumeracao ALIAS FOR $1;
    dtDataBase   ALIAS for $2;
    
    valor float;
    
BEGIN

    SELECT 

        coalesce ( sum (tbl.valor_correcao_credito) , 0.00 )
    INTO
        valor
    FROM
        (
         select 
            (
                case when ( aplica_correcao (acar.numeracao, acar.exercicio::integer, apar.cod_parcela, dtDataBase ) ) > 0 then
            
                    ((                                                                   
                    alc.valor                                                        
                    * arrecadacao.calculaProporcaoParcela(apar.cod_parcela)
                    ) * ( 100 / apar.valor )                                        
                    )                                                                      
                    *  
                    aplica_correcao ( acar.numeracao, acar.exercicio::int, apar.cod_parcela, dtDataBase )
                    * arrecadacao.calculaProporcaoParcela(apar.cod_parcela)  
                    /100
                else
                    0.00
                end

            )::numeric(14,2) as valor_correcao_credito
         FROM
            arrecadacao.lancamento as al
            INNER JOIN arrecadacao.lancamento_calculo as alc ON alc.cod_lancamento = al.cod_lancamento
            INNER JOIN arrecadacao.parcela as apar ON apar.cod_lancamento = al.cod_lancamento
            INNER JOIN arrecadacao.carne as acar ON acar.cod_parcela = apar.cod_parcela
         WHERE acar.numeracao = stNumeracao
      ) as tbl;
      
    return valor;

END;
' LANGUAGE 'plpgsql';
