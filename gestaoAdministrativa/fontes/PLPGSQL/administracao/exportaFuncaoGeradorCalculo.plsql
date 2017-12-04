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
* $Revision: 26768 $
* $Name$
* $Author: gris $
* $Date: 2007-11-13 17:27:22 -0200 (Ter, 13 Nov 2007) $
*
* Casos de uso: uc-00.00.00
*/
CREATE OR REPLACE FUNCTION public.exporta_funcao(intCodfuncao     INTEGER
                                                ,intCodmodulo     INTEGER
                                                ,intCodbiblioteca INTEGER
                                                ,varNomenovo      VARCHAR
                                                ,bolExportaBanco  BOOLEAN)
RETURNS TEXT AS $$
DECLARE
   recFuncao         Record;
   recCorpofn        Record;
   recFnreferencia   Record;
   recVariavel       Record;
   recParamentro     Record;

   varAux            VARCHAR;
   varNomevelho      VARCHAR;
   varComando        VARCHAR;
   varNomecorpopl    VARCHAR;
   varReturn         VARCHAR;

   varPegaProxCod    VARCHAR;
   varPegaAtuaCod    VARCHAR;
   varCodmodulo      VARCHAR := BTrim(To_Char(intCodmodulo    ,'99999'));
   varCodbiblioteca  VARCHAR := BTrim(To_Char(intCodbiblioteca,'99999'));

   txtcomentario     TEXT;
   txtcorpopl        TEXT;
   txtcorpoln        TEXT;
BEGIN
   varAux          := '';
   varNomevelho    := '';
   varNomecorpopl  := '';
   txtcomentario   := '';
   txtcorpopl      := '';
   txtcorpoln      := '';
   varComando      := '';
   varReturn       := '';

   varPegaProxCod  :=                  '(SELECT (Max(cod_funcao)+1)'                         ;
   varPegaProxCod  := varPegaProxCod ||  ' FROM administracao.funcao'                        ;
   varPegaProxCod  := varPegaProxCod || ' WHERE cod_modulo     = ' || varCodmodulo           ;
   varPegaProxCod  := varPegaProxCod || '   AND cod_biblioteca = ' || varCodbiblioteca || ')';

   varPegaAtuaCod  :=                  '(SELECT Max(cod_funcao)'                             ;
   varPegaAtuaCod  := varPegaAtuaCod ||  ' FROM administracao.funcao'                        ;
   varPegaAtuaCod  := varPegaAtuaCod || ' WHERE cod_modulo     = ' || varCodmodulo           ;
   varPegaAtuaCod  := varPegaAtuaCod || '   AND cod_biblioteca = ' || varCodbiblioteca || ')';

   SELECT INTO recFuncao
               funcao.cod_funcao
             , funcao.cod_modulo
             , funcao.cod_biblioteca
             , BTrim(To_Char(funcao.cod_tipo_retorno, '99999')) AS cod_tipo_retorno
             , funcao.nom_funcao
          FROM administracao.funcao LEFT JOIN administracao.funcao_externa ON
                                              (administracao.funcao.cod_funcao     = administracao.funcao_externa.cod_funcao AND
                                               administracao.funcao.cod_modulo     = administracao.funcao_externa.cod_modulo AND
                                               administracao.funcao.cod_biblioteca = administracao.funcao_externa.cod_biblioteca)
         WHERE administracao.funcao.cod_funcao     = intCodfuncao
           AND administracao.funcao.cod_modulo     = intCodmodulo
           AND administracao.funcao.cod_biblioteca = intCodbiblioteca
           AND administracao.funcao_externa.cod_funcao IS NOT NULL;


   IF NOT FOUND THEN
      varAux := ' Cod Função: ' || intCodfuncao  || ' Cod Modulo: ' || intCodmodulo || ' Cod Biblioteca: ' || intCodbiblioteca;
   ELSE

      varComando := ' INSERT INTO administracao.funcao ( cod_modulo
                                                       , cod_biblioteca
                                                       , cod_funcao
                                                       , cod_tipo_retorno
                                                       , nom_funcao)
                                                VALUES ('|| varCodmodulo                                                 ||'
                                                       ,'|| varCodbiblioteca                                             ||'
                                                       ,'|| varPegaProxCod                                               ||'
                                                       ,'|| recFuncao.cod_tipo_retorno                                   ||'
                                                       ,'|| quote_literal(COALESCE( varNomenovo, recFuncao.nom_funcao )) ||'
                                                       );
                    ';

      IF bolExportaBanco THEN
         EXECUTE varComando;
      ELSE
         varReturn := varReturn || varComando || '\n' ;
      END IF;

      -- Dados da tabela administracao.funcao_externa correspondentes a funcao
      SELECT comentario
           , corpo_pl
           , corpo_ln
        INTO txtcomentario
           , txtcorpopl
           , txtcorpoln
        FROM administracao.funcao_externa
       WHERE administracao.funcao_externa.cod_funcao     = intCodfuncao
         AND administracao.funcao_externa.cod_modulo     = recFuncao.cod_modulo
         AND administracao.funcao_externa.cod_biblioteca = recFuncao.cod_biblioteca
           ;


      txtcorpopl := REPLACE(txtcorpopl,'''','''''''');
      txtcorpopl := REPLACE(txtcorpopl,'\\','\\\\');


      -- Altera o nome da função no campo corpo_pl
      IF varNomenovo IS NOT NULL THEN
         varNomevelho := SUBSTR(txtcorpopl, 1, (POSITION('(' IN  txtcorpopl)-1));
         varNomecorpopl:= 'FUNCTION ' || REPLACE (txtcorpopl, varNomevelho, varNomenovo);
      ELSE
         varNomecorpopl := txtcorpopl;
      END IF;

      -- Insere dados da função clone na tabela administracao.funcao_externa
      varComando := 'INSERT INTO administracao.funcao_externa( cod_modulo
                                                             , cod_biblioteca
                                                             , cod_funcao
                                                             , comentario
                                                             , corpo_pl
                                                             , corpo_ln
                                                             )
                                                      VALUES ('|| varCodmodulo                    ||'
                                                             ,'|| varCodbiblioteca                ||'
                                                             ,'|| varPegaAtuaCod                  ||'
                                                             ,'|| quote_literal( txtcomentario  ) ||'
                                                             ,'|| quote_literal( varNomecorpopl ) ||'
                                                             ,'|| quote_literal( txtcorpoln     ) ||'
                                                             );
                    ';

      IF bolExportaBanco THEN
         EXECUTE varComando;
      ELSE
         varReturn := varReturn || varComando || '\n' ;
      END IF;


      -- Insere dados na tabela corpo_funcao_externa
      FOR recCorpofn IN SELECT cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                           , cod_linha
                           , nivel
                           , linha
                        FROM administracao.corpo_funcao_externa
                        WHERE cod_funcao     = intcodfuncao
                          AND cod_modulo     = intCodmodulo
                          AND cod_biblioteca = intCodbiblioteca
      LOOP

         varComando:= 'INSERT INTO administracao.corpo_funcao_externa ( cod_modulo
                                                                      , cod_biblioteca
                                                                      , cod_funcao
                                                                      , cod_linha
                                                                      , nivel
                                                                      , linha)
                                                               VALUES ('|| varCodmodulo                                  ||'
                                                                      ,'|| varCodbiblioteca                              ||'
                                                                      ,'|| varPegaAtuaCod                                ||'
                                                                      ,'|| BTrim(To_Char(recCorpofn.cod_linha, '99999')) ||'
                                                                      ,'|| quote_literal( recCorpofn.nivel )             ||'
                                                                      ,'|| quote_literal( recCorpofn.linha )             ||'
                                                                      );
                      ';

         IF bolExportaBanco THEN
            EXECUTE varComando;
         ELSE
            varReturn := varReturn || varComando || '\n' ;
         END IF;

      END LOOP;

      -- Insere dados na tabela funcao_referencia
      FOR recFnreferencia IN SELECT cod_modulo_externa
                                 , cod_biblioteca_externa
                                 , cod_funcao_externa
                                 , cod_modulo
                                 , cod_biblioteca
                                 , cod_funcao
                              FROM administracao.funcao_referencia
                              WHERE administracao.funcao_referencia.cod_funcao_externa     = intcodfuncao
                                AND administracao.funcao_referencia.cod_modulo_externa     = intCodmodulo
                                AND administracao.funcao_referencia.cod_biblioteca_externa = intCodbiblioteca
      LOOP
         varComando := 'INSERT INTO administracao.funcao_referencia ( cod_modulo_externa
                                                                    , cod_biblioteca_externa
                                                                    , cod_funcao_externa
                                                                    , cod_funcao
                                                                    , cod_biblioteca
                                                                    , cod_modulo
                                                                    )
                                                             VALUES ('|| BTrim(To_Char(recFnreferencia.cod_modulo_externa, '99999'))     || '
                                                                    ,'|| BTrim(To_Char(recFnreferencia.cod_biblioteca_externa, '99999')) || '
                                                                    ,'|| varPegaAtuaCod                                                  || '
                                                                    ,'|| BTrim(To_Char(recFnreferencia.cod_funcao, '99999'))             || '
                                                                    ,'|| BTrim(To_Char(recFnreferencia.cod_biblioteca, '99999'))         || '
                                                                    ,'|| BTrim(To_Char(recFnreferencia.cod_modulo, '99999'))             || '
                                                                    );
                       ';




         IF bolExportaBanco THEN
            EXECUTE varComando;
         ELSE
            varReturn := varReturn || varComando || '\n' ;
         END IF;

      END LOOP;

      -- Insere dados na tabela variavel
      FOR recVariavel IN SELECT cod_modulo
                           , cod_biblioteca
                           , cod_funcao
                           , cod_variavel
                           , cod_tipo
                           , nom_variavel
                           , valor_inicial
                        FROM administracao.variavel
                        WHERE cod_funcao     = intcodfuncao
                           AND cod_modulo     = intCodmodulo
                           AND cod_biblioteca = intCodbiblioteca
      LOOP
         varComando := 'INSERT INTO administracao.variavel ( cod_modulo
                                                           , cod_biblioteca
                                                           , cod_funcao
                                                           , cod_variavel
                                                           , cod_tipo
                                                           , nom_variavel
                                                           , valor_inicial
                                                           )
                                                    VALUES ('|| BTrim(To_Char(recVariavel.cod_modulo, '99999'))     || '
                                                           ,'|| BTrim(To_Char(recVariavel.cod_biblioteca, '99999')) || '
                                                           ,'|| varPegaAtuaCod                                      || '
                                                           ,'|| BTrim(To_Char(recVariavel.cod_variavel, '99999'))   || '
                                                           ,'|| BTrim(To_Char(recVariavel.cod_tipo, '99999'))       || '
                                                           ,'|| quote_literal( recVariavel.nom_variavel  )          || '
                                                           ,'|| quote_literal( recVariavel.valor_inicial )          || '
                                                           );
                       ';


         IF bolExportaBanco THEN
            EXECUTE varComando;
         ELSE
            varReturn := varReturn || varComando || '\n' ;
         END IF;

      END LOOP;

      -- Insere dados na tabela paramentro
      FOR recParamentro IN SELECT cod_modulo
                              , cod_biblioteca
                              , cod_funcao
                              , cod_variavel
                              , ordem
                           FROM administracao.parametro
                           WHERE cod_funcao     = intcodfuncao
                              AND cod_modulo     = intCodmodulo
                              AND cod_biblioteca = intCodbiblioteca
      LOOP
         varComando := 'INSERT INTO administracao.parametro ( cod_modulo
                                                            , cod_biblioteca
                                                            , cod_funcao
                                                            , cod_variavel
                                                            , ordem)
                                                     VALUES ('|| BTrim(To_Char(recParamentro.cod_modulo, '99999'))     || '
                                                            ,'|| BTrim(To_Char(recParamentro.cod_biblioteca, '99999')) || '
                                                            ,'|| varPegaAtuaCod                                        || '
                                                            ,'|| BTrim(To_Char(recParamentro.cod_variavel, '99999'))   || '
                                                            ,'|| BTrim(To_Char(recParamentro.ordem, '99999'))          || '
                                                            );
                       ';

         IF bolExportaBanco THEN
            EXECUTE varComando;
         ELSE
            varReturn := varReturn || varComando || '\n' ;
         END IF;

      END LOOP;

   END IF;

   RETURN varReturn;

END;

$$ LANGUAGE plpgsql;

--SELECT public.exporta_funcao(25,12,1,'CalculaIPTU2008Alagoinhas'  ,false);
--SELECT public.exporta_funcao(2,33,1, NULL ,false);
--DROP FUNCTION public.exporta_funcao(INTEGER, INTEGER, INTEGER, VARCHAR, BOOLEAN);

