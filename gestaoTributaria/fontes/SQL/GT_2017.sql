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
* Script de DDL e DML
*
* Versao 2.01.7
*
* Fabio Bertoldi - 201320618
*
*/

----------------
-- Ticket #20368
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2013'
        AND parametro  = 'cod_uf'
        AND valor      = '23'
          ;
    IF FOUND THEN

        INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES ( 12, 5, 1000, 3, false, 'Iptu/Itbi - Espécie de Edificação Urbana', NULL, NULL, NULL, true, NULL, false);
        INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES ( 12, 5, 1001, 3, false, 'Iptu/Itbi - Espécie de Edificação Rural' , NULL, NULL, NULL, true, NULL, false);
        INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES ( 12, 5, 1002, 3, false, 'Iptu/Itbi - Padrão Construtivo'          , NULL, NULL, NULL, true, NULL, false);
        INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES ( 12, 5, 1003, 3, false, 'Iptu/Itbi - Tipo Material'               , NULL, NULL, NULL, true, NULL, false);
        INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES ( 12, 2, 1004, 3, false, 'Iptu/Itbi - Situação na Quadra'          , NULL, NULL, NULL, true, NULL, false);
        INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES ( 12, 3, 1005, 3, false, 'Iptu/Itbi - Situação Terras Rurais'      , NULL, NULL, NULL, true, NULL, false);
        INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES ( 12, 5, 1006, 3, false, 'Iptu/Itbi - Utilização Edificação'       , NULL, NULL, NULL, true, NULL, false);
        INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES ( 12, 3, 1007, 3, false, 'Iptu/Itbi - Utilização Terra'            , NULL, NULL, NULL, true, NULL, false);
        INSERT INTO administracao.atributo_dinamico (cod_modulo, cod_cadastro, cod_atributo, cod_tipo, nao_nulo, nom_atributo, valor_padrao, ajuda, mascara, ativo, interno, indexavel) VALUES ( 12, 3, 1008, 3, false, 'Iptu/Itbi - Topografia'                  , NULL, NULL, NULL, true, NULL, false);

        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1000,  1, true, 'Casa'                             );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1000,  2, true, 'Apartamento'                      );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1000,  3, true, 'Sala'                             );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1000,  4, true, 'Loja'                             );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1000,  5, true, 'Garagem'                          );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1000,  6, true, 'Box de estacionamento coberto'    );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1000,  7, true, 'Vaga em estacionamento descoberto');
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1000,  8, true, 'Galpão'                           );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1000,  9, true, 'Pavilhão Industrial'              );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1000, 16, true, 'Outro'                            );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1000, 18, true, 'Pavilhão'                         );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1000, 25, true, 'Telheiro'                         );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1001,  5, true, 'Garagem'                          );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1001,  8, true, 'Galpão'                           );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1001,  9, true, 'Pavilhão Industrial'              );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1001, 10, true, 'Silo graneleiro'                  );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1001, 11, true, 'Banheiro para gado'               );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1001, 12, true, 'Estufa de fumo'                   );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1001, 14, true, 'Aviário'                          );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1001, 15, true, 'Pocilga'                          );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1001, 16, true, 'Outro'                            );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1001, 17, true, 'Casa rural'                       );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1001, 18, true, 'Pavilhão'                         );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1001, 25, true, 'Telheiro'                         );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1002,  1, true, 'Alto'                             );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1002,  2, true, 'Normal'                           );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1002,  3, true, 'Baixo'                            );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1002,  4, true, 'Popular'                          );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1003,  2, true, 'Alvenaria'                        );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1003,  3, true, 'Madeira'                          );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1003,  4, true, 'Mista'                            );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 2, 1004,  1, true, 'Interno'                          );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 2, 1004,  2, true, 'Esquina'                          );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 2, 1004,  3, true, 'Encravado'                        );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 3, 1005,  1, true, 'Frente via pavimentada'           );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 3, 1005,  3, true, 'Frente via não pavimentada'       );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 3, 1005,  4, true, 'Encravada'                        );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 3, 1005,  5, true, 'Frente BR, RS - pavimentada'      );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1006,  1, true, 'Comércio/Serviço'                 );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1006,  2, true, 'Industrial'                       );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1006,  3, true, 'Religioso'                        );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 5, 1006,  4, true, 'Residencial'                      );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 3, 1007,  1, true, 'Agricultura'                      );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 3, 1007,  2, true, 'Agropecuária'                     );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 3, 1007,  3, true, 'Industrial'                       );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 3, 1007,  4, true, 'Pecuária'                         );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 3, 1007,  5, true, 'Outras Utilizações'               );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 3, 1008,  1, true, 'Aclive'                           );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 3, 1008,  2, true, 'Declive'                          );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 3, 1008,  3, true, 'Irregular'                        );
        INSERT INTO administracao.atributo_valor_padrao VALUES ( 12, 3, 1008,  4, true, 'Plano'                            );

    END IF;

END;
$$ LANGUAGE 'plpgsql';



ALTER TABLE imobiliario.matricula_imovel ADD   COLUMN zona varchar(10);
UPDATE      imobiliario.matricula_imovel SET          zona = '';
ALTER TABLE imobiliario.matricula_imovel ALTER COLUMN zona SET NOT NULL;

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #19866
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2013'
        AND parametro  = 'cnpj'
        AND valor      = '08148553000106'
          ;
    IF FOUND THEN
        INSERT
          INTO arrecadacao.modelo_carne
             ( cod_modelo
             , nom_modelo
             , nom_arquivo
             , cod_modulo
             , capa_primeira_folha
             )
        VALUES
             ( (SELECT MAX(cod_modelo)+1 FROM arrecadacao.modelo_carne)
             , 'Carnê IPTU 2013'
             , 'RCarneIPTUItau2013.class.php'
             , 12
             , TRUE
             );

        INSERT
          INTO arrecadacao.acao_modelo_carne
             ( cod_modelo
             , cod_acao
             )
        VALUES
             ( (SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne)
             , 963
             );
        INSERT
          INTO arrecadacao.acao_modelo_carne
             ( cod_modelo
             , cod_acao
             )
        VALUES
             ( (SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne)
             , 964
             );
        INSERT
          INTO arrecadacao.acao_modelo_carne
             ( cod_modelo
             , cod_acao
             )
        VALUES
             ( (SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne)
             , 978
             );
        INSERT
          INTO arrecadacao.acao_modelo_carne
             ( cod_modelo
             , cod_acao
             )
        VALUES
             ( (SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne)
             , 979
             );
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #20400
----------------
DROP   VIEW imobiliario.vw_matricula_imovel_atual;
CREATE VIEW imobiliario.vw_matricula_imovel_atual
         AS SELECT matricula_imovel.inscricao_municipal
                 , matricula_imovel.timestamp
                 , matricula_imovel.mat_registro_imovel
                 , matricula_imovel.zona
              FROM imobiliario.matricula_imovel
                 , (
                       SELECT matricula_imovel.inscricao_municipal
                            , max(matricula_imovel.timestamp) AS timestamp
                         FROM imobiliario.matricula_imovel
                     GROUP BY matricula_imovel.inscricao_municipal
                   ) AS max_matricula_imovel
             WHERE matricula_imovel.inscricao_municipal = max_matricula_imovel.inscricao_municipal
               AND matricula_imovel.timestamp           = max_matricula_imovel.timestamp
                 ;
GRANT ALL ON imobiliario.vw_matricula_imovel_atual TO siamweb;

