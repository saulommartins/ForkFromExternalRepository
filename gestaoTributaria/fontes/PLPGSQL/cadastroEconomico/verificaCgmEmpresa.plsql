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
* Casos de uso: uc-05.02.10
*/

CREATE OR REPLACE FUNCTION economico.verifica_cgm_empresa( ) RETURNS TRIGGER AS $$
DECLARE

    inCGM       INTEGER;
    inEmpresa   INTEGER;

BEGIN

    IF    TG_RELNAME = 'cadastro_economico_autonomo' THEN
        PERFORM 1 FROM economico.cadastro_economico_autonomo        WHERE inscricao_economica = NEW.inscricao_economica;
    ELSIF TG_RELNAME = 'cadastro_economico_empresa_fato' THEN
        PERFORM 1 FROM economico.cadastro_economico_empresa_fato    WHERE inscricao_economica = NEW.inscricao_economica;
    ELSIF TG_RELNAME = 'cadastro_economico_empresa_direito' THEN
        PERFORM 1 FROM economico.cadastro_economico_empresa_direito WHERE inscricao_economica = NEW.inscricao_economica;
    END IF;

    IF FOUND THEN

        IF NEW.numcgm != OLD.numcgm THEN
    
               SELECT DISTINCT COALESCE( ef.numcgm, ed.numcgm, au.numcgm ) AS NUMCGM
                    , ce.inscricao_economica
                 INTO inCGM
                    , inEmpresa
                 FROM economico.cadastro_economico                         AS CE
            LEFT JOIN economico.cadastro_economico_empresa_fato            AS EF
                   ON ce.inscricao_economica = ef.inscricao_economica
            LEFT JOIN economico.cadastro_economico_autonomo                AS AU
                   ON ce.inscricao_economica = au.inscricao_economica
            LEFT JOIN economico.cadastro_economico_empresa_direito         AS ED
                   ON ce.inscricao_economica = ed.inscricao_economica
            LEFT JOIN economico.baixa_cadastro_economico
                   ON baixa_cadastro_economico.inscricao_economica = ce.inscricao_economica
                  AND baixa_cadastro_economico.dt_termino IS NULL
                WHERE COALESCE( ef.numcgm, ed.numcgm, au.numcgm ) = NEW.numcgm;
    
            IF FOUND THEN
                RAISE EXCEPTION 'CGM % pertencente a outra Inscrição Econômica. Contate suporte!',NEW.numcgm;
            END IF;
    
        END IF;

    ELSE

               SELECT DISTINCT COALESCE( ef.numcgm, ed.numcgm, au.numcgm ) AS NUMCGM
                    , ce.inscricao_economica
                 INTO inCGM
                    , inEmpresa
                 FROM economico.cadastro_economico                         AS CE
            LEFT JOIN economico.cadastro_economico_empresa_fato            AS EF
                   ON ce.inscricao_economica = ef.inscricao_economica
            LEFT JOIN economico.cadastro_economico_autonomo                AS AU
                   ON ce.inscricao_economica = au.inscricao_economica
            LEFT JOIN economico.cadastro_economico_empresa_direito         AS ED
                   ON ce.inscricao_economica = ed.inscricao_economica
            LEFT JOIN economico.baixa_cadastro_economico
                   ON baixa_cadastro_economico.inscricao_economica = ce.inscricao_economica
                  AND baixa_cadastro_economico.dt_termino IS NULL
                WHERE COALESCE( ef.numcgm, ed.numcgm, au.numcgm ) = NEW.numcgm;

            IF FOUND THEN
                RAISE EXCEPTION 'CGM % pertencente a outra Inscrição Econômica. Contate suporte!',NEW.numcgm;
            END IF;

    END IF;

    RETURN NEW;

END;
$$ LANGUAGE 'plpgsql';
