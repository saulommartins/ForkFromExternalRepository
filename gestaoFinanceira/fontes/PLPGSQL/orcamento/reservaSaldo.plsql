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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* Casos de uso: uc-03.04.01
*               uc-02.01.08         
 
 $Id: reservaSaldo.plsql 59612 2014-09-02 12:00:51Z gelson $

*/

CREATE OR REPLACE FUNCTION orcamento.fn_reserva_saldo ( 

               pcod_reserva          integer
             , pexercicio            character(4)
             , pcod_despesa          integer
             , pdt_validade_inicial  date
             , pdt_validade_final    date
             , pvl_reserva           numeric(14,2)
             , ptipo                 character(1)
             , pmotivo               character varying(160)

 ) returns boolean as $$
DECLARE

    flSaldoDotacao numeric( 14,2 ) ;
    boRetorno boolean;

Begin

    boRetorno = false;

    lock table  orcamento.reserva_saldos in access exclusive mode;
    lock table  orcamento.reserva_saldos_anulada in access exclusive mode;

    select coalesce( fn_saldo_dotacao, 0 )  into flSaldoDotacao from empenho.fn_saldo_dotacao( pexercicio, pcod_despesa );
   
    if ( flSaldoDotacao >= pvl_reserva ) then 
     
            ----- incluindo a reserva
             insert into orcamento.reserva_saldos ( cod_reserva
                                                  , exercicio
                                                  , cod_despesa
                                                  , dt_validade_inicial
                                                  , dt_validade_final
                                                  , dt_inclusao
                                                  , vl_reserva
                                                  , tipo
                                                  , motivo )
                values (   pcod_reserva        
                         , pexercicio          
                         , pcod_despesa        
                         , pdt_validade_inicial
                         , pdt_validade_final  
                         , pdt_validade_inicial 
                         , pvl_reserva
                         , ptipo      
                         , pmotivo   );  



            boRetorno = true;
    end if; 
    

 RETURN boRetorno;

END;

$$ language 'plpgsql';







