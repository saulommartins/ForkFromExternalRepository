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
* 
* 
* 
* 
*
* Casos de uso: uc-03.04.01
*               uc-02.01.08
*/

/*
$Log$
Revision 1.3  2007/02/16 16:00:13  bruce
colocado UC e tag de log

Revision 1.2  2007/02/16 15:36:40  bruce
colocado UC e tag de log


*/


CREATE OR REPLACE FUNCTION orcamento.fn_altera_reserva_saldo ( pcod_reserva          integer
                                                             , pexercicio            character(4)
                                                             , pvl_reserva           numeric(14,2)
 ) returns boolean as $$
DECLARE

    flSaldoDotacao numeric( 14,2 ) ;
    flValorReserva numeric( 14,2 ) ;
    inCodDespesa  integer;
    boRetorno boolean;

Begin

    boRetorno = false;

    lock table  orcamento.reserva_saldos in access exclusive mode;
    lock table  orcamento.reserva_saldos_anulada in access exclusive mode;

    select vl_reserva, cod_despesa into flValorReserva,  inCodDespesa  from orcamento.reserva_saldos where cod_reserva = pcod_reserva and exercicio = pexercicio;
   
    select coalesce( fn_saldo_dotacao, 0 )  into flSaldoDotacao from empenho.fn_saldo_dotacao( pexercicio, inCodDespesa );

    if ( (flSaldoDotacao   + flValorReserva)  >= pvl_reserva ) then 
     
            ----- alterando a reserva
             update orcamento.reserva_saldos
                set vl_reserva  = pvl_reserva
              where cod_reserva = pcod_reserva
                and exercicio   = pexercicio ; 

            boRetorno = true;
    end if; 
    

 RETURN boRetorno;

END;

$$ language 'plpgsql';







