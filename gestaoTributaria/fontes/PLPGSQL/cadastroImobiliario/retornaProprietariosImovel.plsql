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
*
* funcao imobiliario.retornaProprietariosImovel(INTEGER)
* retorno: STRING composta por numcgm + nome de todos os proprietarios
*
* Fabio Bertoldi - 20130328
*
*/

CREATE OR REPLACE FUNCTION imobiliario.retornaProprietariosImovel( inImovel     INTEGER
                                                                 ) RETURNS      VARCHAR AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;

    stNome      VARCHAR;
    stRetorno   VARCHAR = '';
BEGIN
    stSql := ' SELECT numcgm
                 FROM imobiliario.proprietario
                WHERE inscricao_municipal = '|| inImovel ||'
                    ;
             ';
    FOR reRecord IN EXECUTE stSql LOOP
        SELECT sw_cgm.nom_cgm
          INTO stNome
          FROM sw_cgm
         WHERE numcgm = reRecord.numcgm
             ;
        stRetorno := stRetorno || reRecord.numcgm || ' - ' || stNome || ', ';
    END LOOP;

    RETURN stRetorno;
END;
$$ LANGUAGE 'plpgsql';
