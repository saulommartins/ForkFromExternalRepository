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
* $Revision: 27033 $
* $Name$
* $Author: cako $
* $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $
*
* Casos de uso: uc-02.01.31
*/

CREATE OR REPLACE FUNCTION orcamento.fn_verifica_exercicio(VARCHAR) RETURNS INTEGER AS $$
DECLARE
    stExercicio                ALIAS FOR $1;

    stProximoExercicio         INTEGER;
    stExercicioExiste          INTEGER := 0;
BEGIN
    SELECT to_number(stExercicio,'9999') + 1 INTO stProximoExercicio;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM empenho.historico
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM empenho.permissao_autorizacao
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM contabilidade.historico_contabil
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM contabilidade.posicao_plano
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM contabilidade.plano_conta
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM contabilidade.plano_analitica
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM contabilidade.desdobramento_receita
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM contabilidade.classificacao_contabil
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM contabilidade.plano_recurso
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM contabilidade.sistema_contabil
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM contabilidade.plano_banco
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM contabilidade.tipo_transferencia
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.posicao_receita
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.classificacao_receita
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.conta_receita
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.posicao_despesa
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.classificacao_despesa
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.conta_despesa
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.receita
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.previsao_receita
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.despesa
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.previsao_despesa
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste<stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.previsao_orcamentaria
         WHERE exercicio = stProximoExercicio::varchar
         LIMIT 1;
    END IF;

    IF(stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    RETURN stExercicioExiste;
END;
$$ LANGUAGE 'plpgsql';
