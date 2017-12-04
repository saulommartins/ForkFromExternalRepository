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
* $Id: buscaCodigoAtividadeDaInscricaoEconomica.sql 29200 2008-04-15 13:48:27Z fabio $
*
* Casos de uso: uc-05.03.05
*
*/

/*
$Log$
Revision 1.1  2006/11/10 18:48:55  gris
   -- Inclusão da função interna buscaQtdTotalAtividadeDaInscricaoEconomica
   -- Inclusão da função interna buscaCodigoAtividadeDaInscricaoEconomica
   -- Inclusão da função interna buscaTipoDaInscricaoEconomica

*/

   CREATE OR REPLACE FUNCTION buscaCodigoAtividadeDaInscricaoEconomica( intInscricaoEconomica INTEGER
                                                                      , intOrdem              INTEGER)
   RETURNS INTEGER AS $$
   DECLARE
      intValor             INTEGER := 0 ;
      intUltimaOcorrencia  INTEGER;
   BEGIN

      -- Busca última ocorrencia válida.
      SELECT atividade_cadastro_economico.ocorrencia_atividade
        INTO intUltimaOcorrencia
        FROM economico.atividade_cadastro_economico
       WHERE atividade_cadastro_economico.inscricao_economica =  intInscricaoEconomica
       ORDER BY atividade_cadastro_economico.ocorrencia_atividade DESC
       LIMIT 1
      ;

      IF FOUND THEN
         SELECT atividade_cadastro_economico.cod_atividade
           INTO intValor
           FROM economico.atividade_cadastro_economico
          WHERE atividade_cadastro_economico.inscricao_economica  =  intInscricaoEconomica
            AND atividade_cadastro_economico.ocorrencia_atividade =  intUltimaOcorrencia
         ORDER BY atividade_cadastro_economico.principal desc, atividade_cadastro_economico.OID
         LIMIT 1 OFFSET  (intOrdem-1)
         ;
      END IF;

      RETURN intValor;
   END;
   $$ LANGUAGE 'plpgsql';
