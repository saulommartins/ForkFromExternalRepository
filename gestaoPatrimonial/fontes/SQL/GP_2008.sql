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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id:$
*
* Versão 2.00.6
*/

----------------
-- Ticket #18365
----------------
/*Inclui para todas as prefeituras menos Manaquiri  */
CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2012'
        AND parametro  = 'cnpj'
        AND valor      = '04641551000195'
          ;
    IF NOT FOUND THEN
        INSERT INTO licitacao.tipo_contrato VALUES ( 1, 'CT'   , 'Termo de Contrato'                                );
        INSERT INTO licitacao.tipo_contrato VALUES ( 2, 'TACT' , 'Termo Aditivo ao Contrato'                        );
        INSERT INTO licitacao.tipo_contrato VALUES ( 3, 'TRRCT', 'Termo de Re-Ratificaçao de Contrato'              );
        INSERT INTO licitacao.tipo_contrato VALUES ( 4, 'TDCT' , 'Termo de Distrato de Contrato'                    );
        INSERT INTO licitacao.tipo_contrato VALUES ( 5, 'TRCT' , 'Termo de Rescisão de Contrato'                    );
        INSERT INTO licitacao.tipo_contrato VALUES ( 6, 'TCU'  , 'Termo de Concessão de Uso'                        );
        INSERT INTO licitacao.tipo_contrato VALUES ( 7, 'TACU' , 'Termo de Aditivo de Concessão de Uso'             );
        INSERT INTO licitacao.tipo_contrato VALUES ( 8, 'TPU'  , 'Termo de Permissão de Uso'                        );
        INSERT INTO licitacao.tipo_contrato VALUES ( 9, 'TAPU' , 'Termo Aditivo de Permissão de Uso'                );
        INSERT INTO licitacao.tipo_contrato VALUES (10, 'TAU'  , 'Termo de Autorização de Uso'                      );
        INSERT INTO licitacao.tipo_contrato VALUES (11, 'TAAU' , 'Termo Aditivo a Autorização de Uso'               );
        INSERT INTO licitacao.tipo_contrato VALUES (12, 'TC'   , 'Termo de Cessão'                                  );
        INSERT INTO licitacao.tipo_contrato VALUES (13, 'TAC'  , 'Termo Aditivo a Cessão'                           );
        INSERT INTO licitacao.tipo_contrato VALUES (14, 'TCO'  , 'Termo de Compromisso'                             );
        INSERT INTO licitacao.tipo_contrato VALUES (15, 'TACO' , 'Termo Aditivo ao Compromisso'                     );
        INSERT INTO licitacao.tipo_contrato VALUES (16, 'TDRU' , 'Termo de Direito Real de Uso'                     );
        INSERT INTO licitacao.tipo_contrato VALUES (17, 'TADU' , 'Termo Aditivo ao Direito Real de Uso'             );
        INSERT INTO licitacao.tipo_contrato VALUES (18, 'TD'   , 'Termo de Doação'                                  );
        INSERT INTO licitacao.tipo_contrato VALUES (19, 'CACT' , 'Carta Contrato'                                   );
        INSERT INTO licitacao.tipo_contrato VALUES (20, 'OS'   , 'Ordem de Serviços'                                );
        INSERT INTO licitacao.tipo_contrato VALUES (21, 'TAOS' , 'Termo Aditivo a Ordem de Serviços'                );
        INSERT INTO licitacao.tipo_contrato VALUES (22, 'TRTA' , 'Termo de Revogação do Termo de Autorização de Uso');
        INSERT INTO licitacao.tipo_contrato VALUES (23, 'TA'   , 'Termo de Adesão ao Contrato'                      );
        INSERT INTO licitacao.tipo_contrato VALUES (24, 'TOU'  , 'Termo de Outorga'                                 );
        INSERT INTO licitacao.tipo_contrato VALUES (25, 'TAOU' , 'Termo Aditivo de Outorga'                         );
        INSERT INTO licitacao.tipo_contrato VALUES (26, 'TEXO' , 'Termo de Ex-Ofício'                               );
        INSERT INTO licitacao.tipo_contrato VALUES (27, 'TACC' , 'Termo Adititvo a Carta Contrato'                  );
        INSERT INTO licitacao.tipo_contrato VALUES (28, 'TCT'  , 'Termo de Cooperação Técnica'                      );
        INSERT INTO licitacao.tipo_contrato VALUES (29, 'ATCT' , 'Termo Aditivo de Cooperação Técnica'              );
        INSERT INTO licitacao.tipo_contrato VALUES (30, 'TOS'  , 'Termo de Ordem de Serviços'                       );
        INSERT INTO licitacao.tipo_contrato VALUES (31, 'TRAA' , 'Termo de Recebimento de Auxílio Aluguel'          );
        INSERT INTO licitacao.tipo_contrato VALUES (32, 'TRCM' , 'Termo de Recebimento de Cheque Moradia'           );
        INSERT INTO licitacao.tipo_contrato VALUES (33, 'TRIN' , 'Termo de Recebimento de Indenização'              );
        INSERT INTO licitacao.tipo_contrato VALUES (34, 'TQC'  , 'Termo de Quitação de Contrato'                    );
        INSERT INTO licitacao.tipo_contrato VALUES (35, 'PI'   , 'Protocolo de Intenções'                           );
        INSERT INTO licitacao.tipo_contrato VALUES (36, 'TAPI' , 'Termo Aditivo de Protocolo de Intenções'          );
        INSERT INTO licitacao.tipo_contrato VALUES (37, 'TAD'  , 'Termo Aditivo de Doação'                          );
        INSERT INTO licitacao.tipo_contrato VALUES (38, 'ARC'  , 'Apostila de Retificação de Contrato'              );

    END IF;
END;
$$ LANGUAGE 'plpgsql';
SELECT manutencao();
DROP FUNCTION manutencao();
