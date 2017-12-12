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
* $Revision: 27926 $
* $Name$
* $Author: rodrigosoares $
* $Date: 2008-02-08 16:03:10 -0200 (Sex, 08 Fev 2008) $
*
* Casos de uso: uc-01.05.01
*/
--
-- Função de inclusão de ultimo andamento.
--
CREATE OR REPLACE FUNCTION public.fn_atualiza_ultimo_andamento()
   RETURNS TRIGGER AS $$
DECLARE
   rUltimoAndamento     RECORD;
   rAndamento           RECORD;

   cAno_exercicio       CHAR(04);
   iCod_processo        INTEGER;
   iCod_Andamento       INTEGER;
   tTimestamp           TIMESTAMP;
   cAux                 VARCHAR;
BEGIN

   If TG_OP='INSERT' Or TG_OP='UPDATE' then
      --
      -- Define o código do processo a ser utilizado.
      --
      cAno_exercicio := new.ano_exercicio;
      iCod_processo  := new.cod_processo ;
      iCod_Andamento := new.cod_andamento;
      tTimestamp     := new.timestamp;

      --
      -- Verifica a existencia do ultimo andamento.
      --
      Select INTO rUltimoAndamento sw_ultimo_andamento.*
         From public.sw_ultimo_andamento
         Where sw_ultimo_andamento.ano_exercicio = cAno_exercicio
         And sw_ultimo_andamento.cod_processo  = iCod_processo;

      If Found Then

         If iCod_Andamento >= rUltimoAndamento.cod_andamento And
            To_Date(tTimestamp::varchar,'YYYY-MM-DD')    >= To_Date(rUltimoAndamento.timestamp::varchar,'YYYY-MM-DD')  Then
            Update sw_ultimo_andamento
               Set cod_andamento       = new.cod_andamento
                 , cod_orgao           = new.cod_orgao
                 , cod_usuario         = new.cod_usuario
                 , timestamp           = new.timestamp
             Where ano_exercicio = cAno_exercicio
               And cod_processo  = iCod_processo;
         Else
            cAux := '[' || LPad(btrim(To_char(iCod_processo,'9999999999')), 10,'0') || '/' || cAno_exercicio || '].';
            raise exception 'Tabela sw_ultimo_andamento inconsistente, contate suporte. Processo:%', cAux;
         End If;
      Else
         Insert Into public.sw_ultimo_andamento (  ano_exercicio
                                                ,  cod_processo
                                                ,  cod_andamento
                                                ,  cod_orgao
                                                ,  cod_usuario
                                                ,  timestamp           )
                                      Values    (  new.ano_exercicio
                                                ,  new.cod_processo
                                                ,  new.cod_andamento
                                                ,  new.cod_orgao
                                                ,  new.cod_usuario
                                                ,  new.timestamp
                                                );
      End If;
   Else
      raise exception 'Operação não permitida para a tabela sw_andamento %', TG_OP;
   End If;

   Return new;

END;
$$ LANGUAGE plpgsql;
