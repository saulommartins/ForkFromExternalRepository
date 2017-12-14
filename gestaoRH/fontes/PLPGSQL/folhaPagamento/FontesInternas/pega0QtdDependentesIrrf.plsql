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
* script de funcao PLSQL
* 
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br

* $Revision: 23095 $
* $Name$
* $Autor: Marcia $
* Date: 2006/05/31 10:50:00 $
*
* Caso de uso: uc-04.05.13
* Caso de uso: uc-04.05.48
*
* Objetivo: Recupera a quantidade de dependentes ativos para irrf
*/




CREATE OR REPLACE FUNCTION pega0QtdDependentesIrrf(integer,varchar) RETURNS integer as '

DECLARE
    inCodContrato             ALIAS FOR $1;
    stDataFinalCompetencia    ALIAS FOR $2;

    inCodServidor             INTEGER;
    inQtdIRRF                 INTEGER;
    stSql                     VARCHAR;
    reRegistro                RECORD;

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    --inCodContrato := recuperarBufferInteiro(''inCodContrato'');
    --stDataFinalCompetencia := recuperarBufferTexto(''stDataFinalCompetencia'');

    inCodServidor := pega0ServidorDoContrato(inCodContrato);

    stSql:= ''
    SELECT  COALESCE( COUNT(sd.cod_servidor),0 ) as qtd_irrf
      FROM pessoal''||stEntidade||''.servidor_dependente  as sd

     LEFT OUTER JOIN pessoal''||stEntidade||''.dependente as d
       ON d.cod_dependente = sd.cod_dependente

     LEFT OUTER JOIN public.sw_cgm_pessoa_fisica as pf
       ON d.numcgm = pf.numcgm

     LEFT OUTER JOIN folhapagamento''||stEntidade||''.vinculo_irrf as vi
       ON vi.cod_vinculo = d.cod_vinculo

     LEFT OUTER JOIN pessoal''||stEntidade||''.dependente_excluido as de
       ON sd.cod_dependente = de.cod_dependente
      AND sd.cod_servidor = de.cod_servidor

    WHERE sd.cod_servidor = ''||inCodServidor||''

      AND pf.dt_nascimento is not null

      AND d.cod_vinculo > 0

      AND ( vi.idade_limite = 0 
            or (idade( to_char(pf.dt_nascimento,''''yyyy-mm-dd'''' ), substr(''''''||stDataFinalCompetencia||'''''',1,10))) <= vi.idade_limite )

      AND de.cod_servidor is null
      '';

     EXECUTE stSql;

     FOR reRegistro IN EXECUTE stSql
     LOOP

        inQtdIRRF :=  reRegistro.qtd_irrf;

     END LOOP;

  RETURN inQtdIRRF;

END;
' LANGUAGE 'plpgsql';

