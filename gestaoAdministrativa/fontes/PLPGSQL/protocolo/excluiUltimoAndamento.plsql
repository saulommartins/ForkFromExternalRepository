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
* $Revision: 8821 $
* $Name$
* $Author: gris $
* $Date: 2006-04-24 16:09:00 -0300 (Seg, 24 Abr 2006) $
*
* Casos de uso: uc-01.05.01
*/
--
-- Triger que chama a função de exclusão do ultimo andamento.
--
CREATE OR REPLACE FUNCTION public.fn_exclui_ultimo_andamento()
   RETURNS TRIGGER AS $$
DECLARE
   rUltimoAndamento     RECORD;
   rAndamento           RECORD;

   cAno_exercicio       CHAR(04);
   iCod_processo        INTEGER;
   iCod_Andamento       INTEGER;
   cAux                 VARCHAR;

   rPenultimoAndamento  RECORD;
BEGIN
   --
   -- Define o código do processo a ser utilizado.
   --
   cAno_exercicio := old.ano_exercicio;
   iCod_processo  := old.cod_processo ;
   iCod_Andamento := old.cod_andamento;

   --
   -- Verifica a existencia do ultimo andamento.
   --
   Select INTO rUltimoAndamento sw_ultimo_andamento.cod_andamento
      From public.sw_ultimo_andamento
      Where sw_ultimo_andamento.ano_exercicio = cAno_exercicio
        And sw_ultimo_andamento.cod_processo  = iCod_processo;

   If Found Then
      If iCod_Andamento = rUltimoAndamento.cod_andamento Then
         If iCod_Andamento = 0 Then
            Select INTO rPenultimoAndamento *
                   From public.sw_andamento
                  Where sw_andamento.ano_exercicio = cAno_exercicio
                    And sw_andamento.cod_processo  = iCod_processo
                    And sw_andamento.cod_andamento = iCod_andamento;

            Update sw_ultimo_andamento
               Set cod_andamento       = rPenultimoAndamento.cod_andamento
                 , cod_orgao           = rPenultimoAndamento.cod_orgao
                 , cod_usuario         = rPenultimoAndamento.cod_usuario
                 , timestamp           = rPenultimoAndamento.timestamp
             Where ano_exercicio = cAno_exercicio
               And cod_processo  = iCod_processo;

         Else
            Select INTO rPenultimoAndamento *
                   From public.sw_andamento
                  Where sw_andamento.ano_exercicio = cAno_exercicio
                    And sw_andamento.cod_processo  = iCod_processo
                    And sw_andamento.cod_andamento = (iCod_andamento - 1);

            Update sw_ultimo_andamento
               Set cod_andamento       = rPenultimoAndamento.cod_andamento
                 , cod_orgao           = rPenultimoAndamento.cod_orgao
                 , cod_usuario         = rPenultimoAndamento.cod_usuario
                 , timestamp           = rPenultimoAndamento.timestamp
             Where ano_exercicio = cAno_exercicio
               And cod_processo  = iCod_processo;
         End If;
      Else
         -- Se o ultimo andamento for diferente do registrado na tabela sw_ultimo_andamento.
         cAux := '[' || LPad(btrim(To_char(iCod_processo,'9999999999')), 10,'0') || '].';
         If iCod_Andamento > rUltimoAndamento.cod_andamento Then
            raise exception 'Tabela sw_ultimo_andamento inconsistente, contate suporte. Processo:%', cAux;
         Else
            raise exception 'Exclusão não permitida. Processo:%', cAux;
         End If;
      End If;
   End If;

   Return old;

END;
$$ LANGUAGE plpgsql
;
