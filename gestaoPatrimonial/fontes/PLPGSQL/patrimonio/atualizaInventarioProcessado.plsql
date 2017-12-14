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
* $Id:  $
*
*/

-- Função p/ atualizar coluna PROCESSADO em patrimonio.inventario
-- após todas as espécies de um inventário terem sido processadas

CREATE OR REPLACE FUNCTION patrimonio.atualiza_inventario_processado ( ) RETURNS TRIGGER AS $$
DECLARE

BEGIN
        PERFORM 1
           FROM patrimonio.inventario_especie
          WHERE exercicio     = NEW.exercicio
            AND id_inventario = NEW.id_inventario
            AND processado    = FALSE;

        IF NOT FOUND THEN

            UPDATE patrimonio.inventario
               SET processado    = TRUE
             WHERE exercicio     = NEW.exercicio
               AND id_inventario = NEW.id_inventario;

        END IF;

        RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';
