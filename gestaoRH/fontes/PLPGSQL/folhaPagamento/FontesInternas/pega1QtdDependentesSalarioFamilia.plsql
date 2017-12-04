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
* Date: 2006/05/22 10:50:00 $
*
* Caso de uso: uc-04.05.17
* Caso de uso: uc-04.05.48
*
* Objetivo: Recupera a quantidade de dependentes ativos para salario familia 
*/



CREATE OR REPLACE FUNCTION pega1QtdDependentesSalarioFamilia() RETURNS integer as '

DECLARE
    stDataFinalCompetencia    VARCHAR;
    dtDataFinalCompetencia    VARCHAR;
    inCodContrato             INTEGER;
    inCodServidor             INTEGER;
    stSql                     VARCHAR := '''';
    reRegistro                RECORD ;
    boDependente              VARCHAR := ''t'';
    inQtdDependentes          INTEGER ;

    inIdadeLimite              INTEGER := 0;

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


  inCodContrato := recuperarBufferInteiro(''inCodContrato'');
  stDataFinalCompetencia := recuperarBufferTexto(''stDataFinalCompetencia'');

  dtDataFinalCompetencia := substr(stDataFinalCompetencia,1,10) ; 

  inCodServidor := pega0ServidorDoContrato(inCodContrato);

  -- avalia o regime previdenciario (atraves do cadastro da previdencia)
  -- do contrato e retorna a idade do salario familia

  inIdadeLimite := pega1IdadeLimiteSalarioFamilia();

  IF inIdadeLimite > 0 THEN

    stSql:= ''
    SELECT COALESCE( COUNT(sd.cod_servidor),0 ) as qtd_dependente
      FROM pessoal''||stEntidade||''.servidor_dependente  as sd

      LEFT OUTER JOIN 
      ( SELECT 
             cod_dependente
           , dt_inicio_sal_familia
           , dependente_sal_familia
           , dependente_invalido
           , numcgm
      FROM pessoal''||stEntidade||''.dependente 
      WHERE dt_inicio_sal_familia <= ''''''||dtDataFinalCompetencia||''''''
        AND dependente_sal_familia = ''''''||boDependente||''''''
      ) as d
       ON d.cod_dependente = sd.cod_dependente

     LEFT OUTER JOIN pessoal''||stEntidade||''.dependente_excluido as de
       ON sd.cod_dependente = de.cod_dependente
      AND sd.cod_servidor = de.cod_servidor

     LEFT OUTER JOIN public.sw_cgm_pessoa_fisica as pf
       ON d.numcgm = pf.numcgm

     LEFT OUTER JOIN
      ( SELECT  cod_pensao
           , timestamp
           , cod_dependente
           , cod_servidor
           , tipo_pensao
           , dt_inclusao
           , dt_limite
           , percentual
      FROM pessoal''||stEntidade||''.pensao
      WHERE dt_inclusao <= ''''''||dtDataFinalCompetencia||''''''
        AND ( dt_limite is null
              or dt_limite >= ''''''||dtDataFinalCompetencia||''''''
            )
      ORDER BY timestamp desc
      ) as p
       ON p.cod_dependente = sd.cod_dependente
      AND p.cod_servidor = sd.cod_servidor

     LEFT OUTER JOIN  pessoal''||stEntidade||''.pensao_excluida as pe
       ON p.cod_pensao = pe.cod_pensao
      AND p.timestamp = pe.timestamp

    WHERE sd.cod_servidor = ''||inCodServidor||''
      AND de.cod_servidor is null
      AND pf.dt_nascimento is not null
      AND ( d.dependente_invalido = ''''''||boDependente||''''''
            or (idade( to_char(pf.dt_nascimento,''''yyyy-mm-dd'''' )
                       , substr(''''''||stDataFinalCompetencia||'''''',1,10))) < ''||inIdadeLimite||'' )
      AND ( p.cod_pensao is null
             or ( p.cod_pensao is not null AND pe.cod_pensao is not null )
          )

      '';

     EXECUTE stSql;

     FOR reRegistro IN EXECUTE stSql
     LOOP

        inQtdDependentes :=  reRegistro.qtd_dependente;

     END LOOP;

  END IF;
  RETURN inQtdDependentes;

END;
' LANGUAGE 'plpgsql';

