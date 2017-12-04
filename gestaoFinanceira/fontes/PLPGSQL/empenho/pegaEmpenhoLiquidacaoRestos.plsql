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
* $Revision: 30668 $
* $Name$
* $Author: cako $
* $Date: 2007-07-20 18:21:57 -0300 (Sex, 20 Jul 2007) $
*
* Casos de uso: uc-02.03.11
*/

/*
$Log$
Revision 1.2  2007/07/20 21:21:22  cako
Bug#9665#

Revision 1.1  2007/03/15 15:33:55  gris
-- Reescrita da função pegaempenholiquidacaorestos.

Revision 1.8  2006/08/28 15:03:43  cleisson
Bug #6762#

Revision 1.7  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos
*/

CREATE OR REPLACE FUNCTION pegaempenholiquidacaorestos( varExercicioEmpenho VARCHAR
                                                      , intCodNota          INTEGER
                                                      , intCodEntidade      INTEGER) RETURNS VARCHAR  AS $$
DECLARE
    varRetorno              VARCHAR;
BEGIN

   SELECT CASE WHEN configuracao.parametro = 'cod_entidade_camara' THEN 'Legislativo'
               WHEN configuracao.parametro = 'cod_entidade_rpps'   THEN 'RPPS'
               ELSE 'Executivo'
          END AS tipo_restos
     INTO varRetorno
     FROM orcamento.entidade
          LEFT JOIN administracao.configuracao ON configuracao.cod_modulo = 8
                                              AND configuracao.parametro  LIKE 'cod_entidade_%'
                                              AND configuracao.exercicio  = entidade.exercicio
                                              AND configuracao.valor      = entidade.cod_entidade
    WHERE entidade.exercicio    = varExercicioEmpenho
      AND entidade.cod_entidade = intCodEntidade;

    IF varRetorno IS NULL THEN
        varRetorno := '';
    END IF;

    RETURN varRetorno;
END;

$$language 'plpgsql';
