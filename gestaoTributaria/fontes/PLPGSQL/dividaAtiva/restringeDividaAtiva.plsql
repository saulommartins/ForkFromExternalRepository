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
* $Id: restringeDividaAtiva.sql 29207 2008-04-15 14:51:15Z fabio $
*
* Caso de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.1  2006/09/18 17:11:23  gris
Criada trigger de restrição para delete e update para a tabela divida.divida_ativa.

Revision 1.2  2006/09/15 10:20:16  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

-- Restrição tabela divida.divida_ativa
   CREATE OR REPLACE FUNCTION divida.fn_restringe_divida_ativa()
      RETURNS TRIGGER AS $$
   DECLARE
   BEGIN

      If TG_OP!='INSERT' then
         raise exception 'Operação não permitida para tabela divida.divida_ativa.: %', TG_OP;
      End If;

      Return new;
   END;
   $$ LANGUAGE plpgsql;



