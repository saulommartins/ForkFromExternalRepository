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
* $Id: atualizaUltimaModalidadeDivida.sql 29207 2008-04-15 14:51:15Z fabio $
*
* Caso de uso: uc-05.04.07
*/

/*
$Log$
Revision 1.2  2006/09/15 10:20:16  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

-- Atualiza a ultima modalidade de divida casdastrada na
-- inserindo o timestamp na tabela divida.modalidade.
   CREATE OR REPLACE FUNCTION divida.fn_atualiza_ultima_modalidade_divida()
      RETURNS TRIGGER AS $$
   DECLARE
      rModalidadeAtual     RECORD;
      iCod_modalidade      INTEGER;
      tNewTimestamp        TIMESTAMP;
      cAux                 VARCHAR;

   BEGIN

      If TG_OP='INSERT' then
         --
         -- Define a modalidade a ser inserida.
         --
         iCod_modalidade   := new.cod_modalidade;
         tNewTimestamp     := new.timestamp;

         --
         -- Verifica a existencia da ultima modalidade.
         --
         Select modalidade.*
           Into rModalidadeAtual
           From divida.modalidade
          Where modalidade.cod_modalidade = iCod_modalidade
         ;

         If Found Then

            tNewTimestamp := ('now'::text)::timestamp(3) with time zone ;
            If Coalesce(rModalidadeAtual.ultimo_timestamp, '1800-01-01') <= tNewTimestamp  Then
               Update divida.modalidade
                  Set ultimo_timestamp =  tNewTimestamp
                Where cod_modalidade   = iCod_modalidade
               ;
            Else
               cAux := To_char(iCod_modalidade,'9999');
               raise exception 'Tabela divida.modalidade inconsistente, contate suporte. Modalidade:%', cAux;
            End If;
         Else
            raise exception 'Falha de integridade referencial, tabela divida.modalidade.: %', TG_OP;
            raise exception 'Código modalidade: %', iCod_modalidade;
         End If;
      Else
         raise exception 'Operação não permitida para tabela divida.modalidade_vigencia.: %', TG_OP;
      End If;

      Return new;

   END;
   $$ LANGUAGE plpgsql;



