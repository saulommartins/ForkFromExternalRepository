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
* $Id: atributoElementoCadastroEconomivoValorQtdCaixasRegistradoras.sql 29200 2008-04-15 13:48:27Z fabio $
*
* Casos de uso: uc-05.03.05
*
*/

/*
$Log$
Revision 1.2  2006/11/10 18:48:32  gris
Ajuste no nome da variavel varInscricaoEconomica para intInscricaEconomica.

Revision 1.1  2006/11/08 15:47:18  gris
   -- Inclusão da função interna atributoElementoCadastroEconomicoValorQuantidadeApartamento
   -- Inclusão da função interna atributoElementoCadastroEconomicoValorISSAnual
   -- Inclusão da função interna atributoElementoCadastroEconomicoValorCREA
   -- Inclusão da função interna atributoElementoCadastroEconomicoValorPlacaVeiculo
   -- Inclusão da função interna atributoElementoCadastroEconomivoValorQtdCaixasRegistradoras
   -- Inclusão da função interna atributoElementoCadastroEconomivoValorClasse
   -- Inclusão da função interna atributoElementoCadastroEconomivoValorMetragem
   -- Inclusão da função interna atributoElementoCadastroEconomivoValorQtdVeiculos

*/

   CREATE OR REPLACE FUNCTION atributoElementoCadastroEconomivoValorQtdCaixasRegistradoras( intInscricaoEconomica INTEGER)
   RETURNS CHARACTER VARYING AS $$
   DECLARE
      varValor          VARCHAR := NULL;
      intCodModulo      INTEGER  := 14;
      intCodCadastro    INTEGER  := 5 ;
      intCodAtributo    INTEGER  := 9 ;
      booAtributoAtivo  BOOLEAN;
   BEGIN

      SELECT atributo_elemento.ativo
        INTO booAtributoAtivo
        FROM economico.atributo_elemento
       WHERE atributo_elemento.cod_modulo   = intCodModulo
         AND atributo_elemento.cod_cadastro = intCodCadastro
         AND atributo_elemento.cod_atributo = intCodAtributo
      ;

      IF FOUND AND booAtributoAtivo THEN
         SELECT atributo_elem_cad_economico_valor.valor
           INTO varValor
           FROM economico.atributo_elem_cad_economico_valor
         WHERE atributo_elem_cad_economico_valor.cod_modulo   = intCodModulo
           AND atributo_elem_cad_economico_valor.cod_cadastro = intCodCadastro
           AND atributo_elem_cad_economico_valor.cod_atributo = intCodAtributo
           AND atributo_elem_cad_economico_valor.inscricao_economica = intInscricaoEconomica
         ORDER BY atributo_elem_cad_economico_valor.timestamp desc
         LIMIT 1
          ;
      END IF;

      RETURN varValor;
   END;
   $$ LANGUAGE 'plpgsql';

