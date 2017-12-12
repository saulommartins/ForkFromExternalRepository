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
    * PL que verifica o exercício para replicar os dados do orçamento para o ppa
    * Data de Criação   : 18/08/2009


    * @author Analista      Tonismar Régis Bernardo 
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION ppa.fn_verifica_exercicio_ppa(VARCHAR) RETURNS INTEGER AS $$
DECLARE
    stExercicio                ALIAS FOR $1;

    stProximoExercicio         INTEGER;
    stExercicioExiste          INTEGER := 0;
BEGIN

    SELECT to_number(stExercicio, '9999') INTO stProximoExercicio;
    
    IF (stExercicioExiste < stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM administracao.configuracao
         WHERE cod_modulo IN (8,9,10)  
           AND exercicio  = CAST(stProximoExercicio AS VARCHAR)
        LIMIT 1;
    END IF;
    
    IF (stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;

    IF (stExercicioExiste < stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.funcao
         WHERE exercicio = CAST(stProximoExercicio AS VARCHAR)
         LIMIT 1;
    END IF;
    
    IF (stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;
    
    IF (stExercicioExiste < stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.subfuncao
         WHERE exercicio = CAST(stProximoExercicio AS VARCHAR)
         LIMIT 1;
    END IF;
    
    IF (stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;
    
    IF (stExercicioExiste < stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.recurso(CAST(stProximoExercicio AS VARCHAR))
         WHERE exercicio = CAST(stProximoExercicio AS VARCHAR)
         LIMIT 1;
    END IF;
    
    IF (stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;
    
    IF (stExercicioExiste < stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.orgao
         WHERE exercicio = CAST(stProximoExercicio AS VARCHAR)
         LIMIT 1;
    END IF;
    
    IF (stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;
    
    IF (stExercicioExiste < stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.unidade
         WHERE exercicio = CAST(stProximoExercicio AS VARCHAR)
        LIMIT 1;
    END IF;
    
    IF (stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;
    
    RETURN stExercicioExiste;

END;
$$ LANGUAGE 'plpgsql';

