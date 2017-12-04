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
* Versao 2.04.4
*
* Fabio Bertoldi - 20150909
*
*/

----------------
-- Ticket #23240
----------------

UPDATE administracao.funcionalidade SET ordem = ordem + 1 WHERE cod_modulo = 14 AND ordem > 5;

INSERT
  INTO administracao.funcionalidade
     ( cod_funcionalidade
     , cod_modulo
     , nom_funcionalidade
     , nom_diretorio
     , ordem
     , ativo
     )
     VALUES
     ( 495
     , 14
     , 'CNAE'
     , 'instancias/cnae/'
     , 6
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 3080
     , 495
     , 'FMDefinirGrauRisco.php'
     , 'definir'
     , 1
     , ''
     , 'Definir Grau de Risco'
     , TRUE
     );


CREATE TABLE migra_cnae(
    cod_estrutural  VARCHAR(12),
    nivel           INTEGER,
    descricao       varchar(200)
);

INSERT INTO migra_cnae VALUES ('A.00.00-0/00',1,'AGRICULTURA, PECUÁRIA, PRODUÇÃO FLORESTAL, PESCA E AQÜICULTURA');
INSERT INTO migra_cnae VALUES ('A.01.00-0/00',2,'AGRICULTURA, PECUÁRIA E SERVIÇOS RELACIONADOS');
INSERT INTO migra_cnae VALUES ('A.01.10-0/00',3,'Produção de lavouras temporárias');
INSERT INTO migra_cnae VALUES ('A.01.11-3/00',4,'Cultivo de cereais');
INSERT INTO migra_cnae VALUES ('A.01.11-3/01',5,'Cultivo de arroz');
INSERT INTO migra_cnae VALUES ('A.01.11-3/02',5,'Cultivo de milho');
INSERT INTO migra_cnae VALUES ('A.01.11-3/03',5,'Cultivo de trigo');
INSERT INTO migra_cnae VALUES ('A.01.11-3/99',5,'Cultivo de outros cereais não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('A.01.12-1/00',4,'Cultivo de algodão herbáceo e de outras fibras de lavoura temporária');
INSERT INTO migra_cnae VALUES ('A.01.12-1/01',5,'Cultivo de algodão herbáceo');
INSERT INTO migra_cnae VALUES ('A.01.12-1/02',5,'Cultivo de juta');
INSERT INTO migra_cnae VALUES ('A.01.12-1/99',5,'Cultivo de outras fibras de lavoura temporária não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('A.01.13-0/00',4,'Cultivo de cana-de-açúcar');
INSERT INTO migra_cnae VALUES ('A.01.13-0/00',5,'Cultivo de cana-de-açúcar');
INSERT INTO migra_cnae VALUES ('A.01.14-8/00',4,'Cultivo de fumo');
INSERT INTO migra_cnae VALUES ('A.01.14-8/00',5,'Cultivo de fumo');
INSERT INTO migra_cnae VALUES ('A.01.15-6/00',4,'Cultivo de soja');
INSERT INTO migra_cnae VALUES ('A.01.15-6/00',5,'Cultivo de soja');
INSERT INTO migra_cnae VALUES ('A.01.16-4/00',4,'Cultivo de oleaginosas de lavoura temporária, exceto soja');
INSERT INTO migra_cnae VALUES ('A.01.16-4/01',5,'Cultivo de amendoim');
INSERT INTO migra_cnae VALUES ('A.01.16-4/02',5,'Cultivo de girassol');
INSERT INTO migra_cnae VALUES ('A.01.16-4/03',5,'Cultivo de mamona');
INSERT INTO migra_cnae VALUES ('A.01.16-4/99',5,'Cultivo de outras oleaginosas de lavoura temporária não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('A.01.19-9/00',4,'Cultivo de plantas de lavoura temporária não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('A.01.19-9/01',5,'Cultivo de abacaxi');
INSERT INTO migra_cnae VALUES ('A.01.19-9/02',5,'Cultivo de alho');
INSERT INTO migra_cnae VALUES ('A.01.19-9/03',5,'Cultivo de batata-inglesa');
INSERT INTO migra_cnae VALUES ('A.01.19-9/04',5,'Cultivo de cebola');
INSERT INTO migra_cnae VALUES ('A.01.19-9/05',5,'Cultivo de feijão');
INSERT INTO migra_cnae VALUES ('A.01.19-9/06',5,'Cultivo de mandioca');
INSERT INTO migra_cnae VALUES ('A.01.19-9/07',5,'Cultivo de melão');
INSERT INTO migra_cnae VALUES ('A.01.19-9/08',5,'Cultivo de melancia');
INSERT INTO migra_cnae VALUES ('A.01.19-9/09',5,'Cultivo de tomate rasteiro');
INSERT INTO migra_cnae VALUES ('A.01.19-9/99',5,'Cultivo de outras plantas de lavoura temporária não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('A.01.20-0/00',3,'Horticultura e floricultura');
INSERT INTO migra_cnae VALUES ('A.01.21-1/00',4,'Horticultura');
INSERT INTO migra_cnae VALUES ('A.01.21-1/01',5,'Horticultura, exceto morango');
INSERT INTO migra_cnae VALUES ('A.01.21-1/02',5,'Cultivo de morango');
INSERT INTO migra_cnae VALUES ('A.01.22-9/00',4,'Cultivo de flores e plantas ornamentais');
INSERT INTO migra_cnae VALUES ('A.01.22-9/00',5,'Cultivo de flores e plantas ornamentais');
INSERT INTO migra_cnae VALUES ('A.01.30-0/00',3,'Produção de lavouras permanentes');
INSERT INTO migra_cnae VALUES ('A.01.31-8/00',4,'Cultivo de laranja');
INSERT INTO migra_cnae VALUES ('A.01.31-8/00',5,'Cultivo de laranja');
INSERT INTO migra_cnae VALUES ('A.01.32-6/00',4,'Cultivo de uva');
INSERT INTO migra_cnae VALUES ('A.01.32-6/00',5,'Cultivo de uva');
INSERT INTO migra_cnae VALUES ('A.01.33-4/00',4,'Cultivo de frutas de lavoura permanente, exceto laranja e uva');
INSERT INTO migra_cnae VALUES ('A.01.33-4/01',5,'Cultivo de açaí');
INSERT INTO migra_cnae VALUES ('A.01.33-4/02',5,'Cultivo de banana');
INSERT INTO migra_cnae VALUES ('A.01.33-4/03',5,'Cultivo de caju');
INSERT INTO migra_cnae VALUES ('A.01.33-4/04',5,'Cultivo de cítricos, exceto laranja');
INSERT INTO migra_cnae VALUES ('A.01.33-4/05',5,'Cultivo de coco-da-baía');
INSERT INTO migra_cnae VALUES ('A.01.33-4/06',5,'Cultivo de guaraná');
INSERT INTO migra_cnae VALUES ('A.01.33-4/07',5,'Cultivo de maçã');
INSERT INTO migra_cnae VALUES ('A.01.33-4/08',5,'Cultivo de mamão');
INSERT INTO migra_cnae VALUES ('A.01.33-4/09',5,'Cultivo de maracujá');
INSERT INTO migra_cnae VALUES ('A.01.33-4/10',5,'Cultivo de manga');
INSERT INTO migra_cnae VALUES ('A.01.33-4/11',5,'Cultivo de pêssego');
INSERT INTO migra_cnae VALUES ('A.01.33-4/99',5,'Cultivo de frutas de lavoura permanente não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('A.01.34-2/00',4,'Cultivo de café');
INSERT INTO migra_cnae VALUES ('A.01.34-2/00',5,'Cultivo de café');
INSERT INTO migra_cnae VALUES ('A.01.35-1/00',4,'Cultivo de cacau');
INSERT INTO migra_cnae VALUES ('A.01.35-1/00',5,'Cultivo de cacau');
INSERT INTO migra_cnae VALUES ('A.01.39-3/00',4,'Cultivo de plantas de lavoura permanente não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('A.01.39-3/01',5,'Cultivo de chá-da-índia');
INSERT INTO migra_cnae VALUES ('A.01.39-3/02',5,'Cultivo de erva-mate');
INSERT INTO migra_cnae VALUES ('A.01.39-3/03',5,'Cultivo de pimenta-do-reino');
INSERT INTO migra_cnae VALUES ('A.01.39-3/04',5,'Cultivo de plantas para condimento, exceto pimenta-do-reino');
INSERT INTO migra_cnae VALUES ('A.01.39-3/05',5,'Cultivo de dendê');
INSERT INTO migra_cnae VALUES ('A.01.39-3/06',5,'Cultivo de seringueira');
INSERT INTO migra_cnae VALUES ('A.01.39-3/99',5,'Cultivo de outras plantas de lavoura permanente não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('A.01.40-0/00',3,'Produção de sementes e mudas certificadas');
INSERT INTO migra_cnae VALUES ('A.01.41-5/00',4,'Produção de sementes certificadas');
INSERT INTO migra_cnae VALUES ('A.01.41-5/01',5,'Produção de sementes certificadas, exceto de forrageiras para pasto');
INSERT INTO migra_cnae VALUES ('A.01.41-5/02',5,'Produção de sementes certificadas de forrageiras para formação de pasto');
INSERT INTO migra_cnae VALUES ('A.01.42-3/00',4,'Produção de mudas e outras formas de propagação vegetal, certificadas');
INSERT INTO migra_cnae VALUES ('A.01.42-3/00',5,'Produção de mudas e outras formas de propagação vegetal, certificadas');
INSERT INTO migra_cnae VALUES ('A.01.50-0/00',3,'Pecuária');
INSERT INTO migra_cnae VALUES ('A.01.51-2/00',4,'Criação de bovinos');
INSERT INTO migra_cnae VALUES ('A.01.51-2/01',5,'Criação de bovinos para corte');
INSERT INTO migra_cnae VALUES ('A.01.51-2/02',5,'Criação de bovinos para leite');
INSERT INTO migra_cnae VALUES ('A.01.51-2/03',5,'Criação de bovinos, exceto para corte e leite');
INSERT INTO migra_cnae VALUES ('A.01.52-1/00',4,'Criação de outros animais de grande porte');
INSERT INTO migra_cnae VALUES ('A.01.52-1/01',5,'Criação de bufalinos');
INSERT INTO migra_cnae VALUES ('A.01.52-1/02',5,'Criação de eqüinos');
INSERT INTO migra_cnae VALUES ('A.01.52-1/03',5,'Criação de asininos e muares');
INSERT INTO migra_cnae VALUES ('A.01.53-9/00',4,'Criação de caprinos e ovinos');
INSERT INTO migra_cnae VALUES ('A.01.53-9/01',5,'Criação de caprinos');
INSERT INTO migra_cnae VALUES ('A.01.53-9/02',5,'Criação de ovinos, inclusive para produção de lã');
INSERT INTO migra_cnae VALUES ('A.01.54-7/00',4,'Criação de suínos');
INSERT INTO migra_cnae VALUES ('A.01.54-7/00',5,'Criação de suínos');
INSERT INTO migra_cnae VALUES ('A.01.55-5/00',4,'Criação de aves');
INSERT INTO migra_cnae VALUES ('A.01.55-5/01',5,'Criação de frangos para corte');
INSERT INTO migra_cnae VALUES ('A.01.55-5/02',5,'Produção de pintos de um dia');
INSERT INTO migra_cnae VALUES ('A.01.55-5/03',5,'Criação de outros galináceos, exceto para corte');
INSERT INTO migra_cnae VALUES ('A.01.55-5/04',5,'Criação de aves, exceto galináceos');
INSERT INTO migra_cnae VALUES ('A.01.55-5/05',5,'Produção de ovos');
INSERT INTO migra_cnae VALUES ('A.01.59-8/00',4,'Criação de animais não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('A.01.59-8/01',5,'Apicultura');
INSERT INTO migra_cnae VALUES ('A.01.59-8/02',5,'Criação de animais de estimação');
INSERT INTO migra_cnae VALUES ('A.01.59-8/03',5,'Criação de escargô');
INSERT INTO migra_cnae VALUES ('A.01.59-8/04',5,'Criação de bicho-da-seda');
INSERT INTO migra_cnae VALUES ('A.01.59-8/99',5,'Criação de outros animais não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('A.01.60-0/00',3,'Atividades de apoio à agricultura e à pecuária; atividades de pós-colheita');
INSERT INTO migra_cnae VALUES ('A.01.61-0/00',4,'Atividades de apoio à agricultura');
INSERT INTO migra_cnae VALUES ('A.01.61-0/01',5,'Serviço de pulverização e controle de pragas agrícolas');
INSERT INTO migra_cnae VALUES ('A.01.61-0/02',5,'Serviço de poda de árvores para lavouras');
INSERT INTO migra_cnae VALUES ('A.01.61-0/03',5,'Serviço de preparação de terreno, cultivo e colheita');
INSERT INTO migra_cnae VALUES ('A.01.61-0/99',5,'Atividades de apoio à agricultura não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('A.01.62-8/00',4,'Atividades de apoio à pecuária');
INSERT INTO migra_cnae VALUES ('A.01.62-8/01',5,'Serviço de inseminação artificial em animais');
INSERT INTO migra_cnae VALUES ('A.01.62-8/02',5,'Serviço de tosquiamento de ovinos');
INSERT INTO migra_cnae VALUES ('A.01.62-8/03',5,'Serviço de manejo de animais');
INSERT INTO migra_cnae VALUES ('A.01.62-8/99',5,'Atividades de apoio à pecuária não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('A.01.63-6/00',4,'Atividades de pós-colheita');
INSERT INTO migra_cnae VALUES ('A.01.63-6/00',5,'Atividades de pós-colheita');
INSERT INTO migra_cnae VALUES ('A.01.70-0/00',3,'Caça e serviços relacionados');
INSERT INTO migra_cnae VALUES ('A.01.70-9/00',4,'Caça e serviços relacionados');
INSERT INTO migra_cnae VALUES ('A.01.70-9/00',5,'Caça e serviços relacionados');
INSERT INTO migra_cnae VALUES ('A.02.00-0/00',2,'PRODUÇÃO FLORESTAL');
INSERT INTO migra_cnae VALUES ('A.02.10-0/00',3,'Produção florestal - florestas plantadas');
INSERT INTO migra_cnae VALUES ('A.02.10-1/00',4,'Produção florestal - florestas plantadas');
INSERT INTO migra_cnae VALUES ('A.02.10-1/01',5,'Cultivo de eucalipto');
INSERT INTO migra_cnae VALUES ('A.02.10-1/02',5,'Cultivo de acácia-negra');
INSERT INTO migra_cnae VALUES ('A.02.10-1/03',5,'Cultivo de pinus');
INSERT INTO migra_cnae VALUES ('A.02.10-1/04',5,'Cultivo de teca');
INSERT INTO migra_cnae VALUES ('A.02.10-1/05',5,'Cultivo de espécies madeireiras, exceto eucalipto, acácia-negra, pinus e teca');
INSERT INTO migra_cnae VALUES ('A.02.10-1/06',5,'Cultivo de mudas em viveiros florestais');
INSERT INTO migra_cnae VALUES ('A.02.10-1/07',5,'Extração de madeira em florestas plantadas');
INSERT INTO migra_cnae VALUES ('A.02.10-1/08',5,'Produção de carvão vegetal - florestas plantadas');
INSERT INTO migra_cnae VALUES ('A.02.10-1/09',5,'Produção de casca de acácia-negra - florestas plantadas');
INSERT INTO migra_cnae VALUES ('A.02.10-1/99',5,'Produção de produtos não-madeireiros não especificados anteriormente em florestas plantadas');
INSERT INTO migra_cnae VALUES ('A.02.20-0/00',3,'Produção florestal - florestas nativas');
INSERT INTO migra_cnae VALUES ('A.02.20-9/00',4,'Produção florestal - florestas nativas');
INSERT INTO migra_cnae VALUES ('A.02.20-9/01',5,'Extração de madeira em florestas nativas');
INSERT INTO migra_cnae VALUES ('A.02.20-9/02',5,'Produção de carvão vegetal - florestas nativas');
INSERT INTO migra_cnae VALUES ('A.02.20-9/03',5,'Coleta de castanha-do-pará em florestas nativas');
INSERT INTO migra_cnae VALUES ('A.02.20-9/04',5,'Coleta de látex em florestas nativas');
INSERT INTO migra_cnae VALUES ('A.02.20-9/05',5,'Coleta de palmito em florestas nativas');
INSERT INTO migra_cnae VALUES ('A.02.20-9/06',5,'Conservação de florestas nativas');
INSERT INTO migra_cnae VALUES ('A.02.20-9/99',5,'Coleta de produtos não-madeireiros não especificados anteriormente em florestas nativas');
INSERT INTO migra_cnae VALUES ('A.02.30-0/00',3,'Atividades de apoio à produção florestal');
INSERT INTO migra_cnae VALUES ('A.02.30-6/00',4,'Atividades de apoio à produção florestal');
INSERT INTO migra_cnae VALUES ('A.02.30-6/00',5,'Atividades de apoio à produção florestal');
INSERT INTO migra_cnae VALUES ('A.03.00-0/00',2,'PESCA E AQÜICULTURA');
INSERT INTO migra_cnae VALUES ('A.03.10-0/00',3,'Pesca');
INSERT INTO migra_cnae VALUES ('A.03.11-6/00',4,'Pesca em água salgada');
INSERT INTO migra_cnae VALUES ('A.03.11-6/01',5,'Pesca de peixes em água salgada');
INSERT INTO migra_cnae VALUES ('A.03.11-6/02',5,'Pesca de crustáceos e moluscos em água salgada');
INSERT INTO migra_cnae VALUES ('A.03.11-6/03',5,'Coleta de outros produtos marinhos');
INSERT INTO migra_cnae VALUES ('A.03.11-6/04',5,'Atividades de apoio à pesca em água salgada');
INSERT INTO migra_cnae VALUES ('A.03.12-4/00',4,'Pesca em água doce');
INSERT INTO migra_cnae VALUES ('A.03.12-4/01',5,'Pesca de peixes em água doce');
INSERT INTO migra_cnae VALUES ('A.03.12-4/02',5,'Pesca de crustáceos e moluscos em água doce');
INSERT INTO migra_cnae VALUES ('A.03.12-4/03',5,'Coleta de outros produtos aquáticos de água doce');
INSERT INTO migra_cnae VALUES ('A.03.12-4/04',5,'Atividades de apoio à pesca em água doce');
INSERT INTO migra_cnae VALUES ('A.03.20-0/00',3,'Aqüicultura');
INSERT INTO migra_cnae VALUES ('A.03.21-3/00',4,'Aqüicultura em água salgada e salobra');
INSERT INTO migra_cnae VALUES ('A.03.21-3/01',5,'Criação de peixes em água salgada e salobra');
INSERT INTO migra_cnae VALUES ('A.03.21-3/02',5,'Criação de camarões em água salgada e salobra');
INSERT INTO migra_cnae VALUES ('A.03.21-3/03',5,'Criação de ostras e mexilhões em água salgada e salobra');
INSERT INTO migra_cnae VALUES ('A.03.21-3/04',5,'Criação de peixes ornamentais em água salgada e salobra');
INSERT INTO migra_cnae VALUES ('A.03.21-3/05',5,'Atividades de apoio à aqüicultura em água salgada e salobra');
INSERT INTO migra_cnae VALUES ('A.03.21-3/99',5,'Cultivos e semicultivos da aqüicultura em água salgada e salobra não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('A.03.22-1/00',4,'Aqüicultura em água doce');
INSERT INTO migra_cnae VALUES ('A.03.22-1/01',5,'Criação de peixes em água doce');
INSERT INTO migra_cnae VALUES ('A.03.22-1/02',5,'Criação de camarões em água doce');
INSERT INTO migra_cnae VALUES ('A.03.22-1/03',5,'Criação de ostras e mexilhões em água doce');
INSERT INTO migra_cnae VALUES ('A.03.22-1/04',5,'Criação de peixes ornamentais em água doce');
INSERT INTO migra_cnae VALUES ('A.03.22-1/05',5,'Ranicultura');
INSERT INTO migra_cnae VALUES ('A.03.22-1/06',5,'Criação de jacaré');
INSERT INTO migra_cnae VALUES ('A.03.22-1/07',5,'Atividades de apoio à aqüicultura em água doce');
INSERT INTO migra_cnae VALUES ('A.03.22-1/99',5,'Cultivos e semicultivos da aqüicultura em água doce não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('B.00.00-0/00',1,'INDÚSTRIAS EXTRATIVAS');
INSERT INTO migra_cnae VALUES ('B.05.00-0/00',2,'EXTRAÇÃO DE CARVÃO MINERAL');
INSERT INTO migra_cnae VALUES ('B.05.00-0/00',3,'Extração de carvão mineral');
INSERT INTO migra_cnae VALUES ('B.05.00-3/00',4,'Extração de carvão mineral');
INSERT INTO migra_cnae VALUES ('B.05.00-3/01',5,'Extração de carvão mineral');
INSERT INTO migra_cnae VALUES ('B.05.00-3/02',5,'Beneficiamento de carvão mineral');
INSERT INTO migra_cnae VALUES ('B.06.00-0/00',2,'EXTRAÇÃO DE PETRÓLEO E GÁS NATURAL');
INSERT INTO migra_cnae VALUES ('B.06.00-0/00',3,'Extração de petróleo e gás natural');
INSERT INTO migra_cnae VALUES ('B.06.00-0/00',4,'Extração de petróleo e gás natural');
INSERT INTO migra_cnae VALUES ('B.06.00-0/01',5,'Extração de petróleo e gás natural');
INSERT INTO migra_cnae VALUES ('B.06.00-0/02',5,'Extração e beneficiamento de xisto');
INSERT INTO migra_cnae VALUES ('B.06.00-0/03',5,'Extração e beneficiamento de areias betuminosas');
INSERT INTO migra_cnae VALUES ('B.07.00-0/00',2,'EXTRAÇÃO DE MINERAIS METÁLICOS');
INSERT INTO migra_cnae VALUES ('B.07.10-0/00',3,'Extração de minério de ferro');
INSERT INTO migra_cnae VALUES ('B.07.10-3/00',4,'Extração de minério de ferro');
INSERT INTO migra_cnae VALUES ('B.07.10-3/01',5,'Extração de minério de ferro');
INSERT INTO migra_cnae VALUES ('B.07.10-3/02',5,'Pelotização, sinterização e outros beneficiamentos de minério de ferro');
INSERT INTO migra_cnae VALUES ('B.07.20-0/00',3,'Extração de minerais metálicos não-ferrosos');
INSERT INTO migra_cnae VALUES ('B.07.21-9/00',4,'Extração de minério de alumínio');
INSERT INTO migra_cnae VALUES ('B.07.21-9/01',5,'Extração de minério de alumínio');
INSERT INTO migra_cnae VALUES ('B.07.21-9/02',5,'Beneficiamento de minério de alumínio');
INSERT INTO migra_cnae VALUES ('B.07.22-7/00',4,'Extração de minério de estanho');
INSERT INTO migra_cnae VALUES ('B.07.22-7/01',5,'Extração de minério de estanho');
INSERT INTO migra_cnae VALUES ('B.07.22-7/02',5,'Beneficiamento de minério de estanho');
INSERT INTO migra_cnae VALUES ('B.07.23-5/00',4,'Extração de minério de manganês');
INSERT INTO migra_cnae VALUES ('B.07.23-5/01',5,'Extração de minério de manganês');
INSERT INTO migra_cnae VALUES ('B.07.23-5/02',5,'Beneficiamento de minério de manganês');
INSERT INTO migra_cnae VALUES ('B.07.24-3/00',4,'Extração de minério de metais preciosos');
INSERT INTO migra_cnae VALUES ('B.07.24-3/01',5,'Extração de minério de metais preciosos');
INSERT INTO migra_cnae VALUES ('B.07.24-3/02',5,'Beneficiamento de minério de metais preciosos');
INSERT INTO migra_cnae VALUES ('B.07.25-1/00',4,'Extração de minerais radioativos');
INSERT INTO migra_cnae VALUES ('B.07.25-1/00',5,'Extração de minerais radioativos');
INSERT INTO migra_cnae VALUES ('B.07.29-4/00',4,'Extração de minerais metálicos não-ferrosos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('B.07.29-4/01',5,'Extração de minérios de nióbio e titânio');
INSERT INTO migra_cnae VALUES ('B.07.29-4/02',5,'Extração de minério de tungstênio');
INSERT INTO migra_cnae VALUES ('B.07.29-4/03',5,'Extração de minério de níquel');
INSERT INTO migra_cnae VALUES ('B.07.29-4/04',5,'Extração de minérios de cobre, chumbo, zinco e outros minerais metálicos não-ferrosos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('B.07.29-4/05',5,'Beneficiamento de minérios de cobre, chumbo, zinco e outros minerais metálicos não-ferrosos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('B.08.00-0/00',2,'EXTRAÇÃO DE MINERAIS NÃO-METÁLICOS');
INSERT INTO migra_cnae VALUES ('B.08.10-0/00',3,'Extração de pedra, areia e argila');
INSERT INTO migra_cnae VALUES ('B.08.10-0/00',4,'Extração de pedra, areia e argila');
INSERT INTO migra_cnae VALUES ('B.08.10-0/01',5,'Extração de ardósia e beneficiamento associado');
INSERT INTO migra_cnae VALUES ('B.08.10-0/02',5,'Extração de granito e beneficiamento associado');
INSERT INTO migra_cnae VALUES ('B.08.10-0/03',5,'Extração de mármore e beneficiamento associado');
INSERT INTO migra_cnae VALUES ('B.08.10-0/04',5,'Extração de calcário e dolomita e beneficiamento associado');
INSERT INTO migra_cnae VALUES ('B.08.10-0/05',5,'Extração de gesso e caulim');
INSERT INTO migra_cnae VALUES ('B.08.10-0/06',5,'Extração de areia, cascalho ou pedregulho e beneficiamento associado');
INSERT INTO migra_cnae VALUES ('B.08.10-0/07',5,'Extração de argila e beneficiamento associado');
INSERT INTO migra_cnae VALUES ('B.08.10-0/08',5,'Extração de saibro e beneficiamento associado');
INSERT INTO migra_cnae VALUES ('B.08.10-0/09',5,'Extração de basalto e beneficiamento associado');
INSERT INTO migra_cnae VALUES ('B.08.10-0/10',5,'Beneficiamento de gesso e caulim associado à extração');
INSERT INTO migra_cnae VALUES ('B.08.10-0/99',5,'Extração e britamento de pedras e outros materiais para construção e beneficiamento associado');
INSERT INTO migra_cnae VALUES ('B.08.90-0/00',3,'Extração de outros minerais não-metálicos');
INSERT INTO migra_cnae VALUES ('B.08.91-6/00',4,'Extração de minerais para fabricação de adubos, fertilizantes e outros produtos químicos');
INSERT INTO migra_cnae VALUES ('B.08.91-6/00',5,'Extração de minerais para fabricação de adubos, fertilizantes e outros produtos químicos');
INSERT INTO migra_cnae VALUES ('B.08.92-4/00',4,'Extração e refino de sal marinho e sal-gema');
INSERT INTO migra_cnae VALUES ('B.08.92-4/01',5,'Extração de sal marinho');
INSERT INTO migra_cnae VALUES ('B.08.92-4/02',5,'Extração de sal-gema');
INSERT INTO migra_cnae VALUES ('B.08.92-4/03',5,'Refino e outros tratamentos do sal');
INSERT INTO migra_cnae VALUES ('B.08.93-2/00',4,'Extração de gemas (pedras preciosas e semipreciosas)');
INSERT INTO migra_cnae VALUES ('B.08.93-2/00',5,'Extração de gemas (pedras preciosas e semipreciosas)');
INSERT INTO migra_cnae VALUES ('B.08.99-1/00',4,'Extração de minerais não-metálicos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('B.08.99-1/01',5,'Extração de grafita');
INSERT INTO migra_cnae VALUES ('B.08.99-1/02',5,'Extração de quartzo');
INSERT INTO migra_cnae VALUES ('B.08.99-1/03',5,'Extração de amianto');
INSERT INTO migra_cnae VALUES ('B.08.99-1/99',5,'Extração de outros minerais não-metálicos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('B.09.00-0/00',2,'ATIVIDADES DE APOIO À EXTRAÇÃO DE MINERAIS');
INSERT INTO migra_cnae VALUES ('B.09.10-0/00',3,'Atividades de apoio à extração de petróleo e gás natural');
INSERT INTO migra_cnae VALUES ('B.09.10-6/00',4,'Atividades de apoio à extração de petróleo e gás natural');
INSERT INTO migra_cnae VALUES ('B.09.10-6/00',5,'Atividades de apoio à extração de petróleo e gás natural');
INSERT INTO migra_cnae VALUES ('B.09.90-0/00',3,'Atividades de apoio à extração de minerais, exceto petróleo e gás natural');
INSERT INTO migra_cnae VALUES ('B.09.90-4/00',4,'Atividades de apoio à extração de minerais, exceto petróleo e gás natural');
INSERT INTO migra_cnae VALUES ('B.09.90-4/01',5,'Atividades de apoio à extração de minério de ferro');
INSERT INTO migra_cnae VALUES ('B.09.90-4/02',5,'Atividades de apoio à extração de minerais metálicos não-ferrosos');
INSERT INTO migra_cnae VALUES ('B.09.90-4/03',5,'Atividades de apoio à extração de minerais não-metálicos');
INSERT INTO migra_cnae VALUES ('C.00.00-0/00',1,'INDÚSTRIAS DE TRANSFORMAÇÃO');
INSERT INTO migra_cnae VALUES ('C.10.00-0/00',2,'FABRICAÇÃO DE PRODUTOS ALIMENTÍCIOS');
INSERT INTO migra_cnae VALUES ('C.10.10-0/00',3,'Abate e fabricação de produtos de carne');
INSERT INTO migra_cnae VALUES ('C.10.11-2/00',4,'Abate de reses, exceto suínos');
INSERT INTO migra_cnae VALUES ('C.10.11-2/01',5,'Frigorífico - abate de bovinos');
INSERT INTO migra_cnae VALUES ('C.10.11-2/02',5,'Frigorífico - abate de eqüinos');
INSERT INTO migra_cnae VALUES ('C.10.11-2/03',5,'Frigorífico - abate de ovinos e caprinos');
INSERT INTO migra_cnae VALUES ('C.10.11-2/04',5,'Frigorífico - abate de bufalinos');
INSERT INTO migra_cnae VALUES ('C.10.11-2/05',5,'Matadouro - abate de reses sob contrato, exceto abate de suínos');
INSERT INTO migra_cnae VALUES ('C.10.12-1/00',4,'Abate de suínos, aves e outros pequenos animais');
INSERT INTO migra_cnae VALUES ('C.10.12-1/01',5,'Abate de aves');
INSERT INTO migra_cnae VALUES ('C.10.12-1/02',5,'Abate de pequenos animais');
INSERT INTO migra_cnae VALUES ('C.10.12-1/03',5,'Frigorífico - abate de suínos');
INSERT INTO migra_cnae VALUES ('C.10.12-1/04',5,'Matadouro - abate de suínos sob contrato');
INSERT INTO migra_cnae VALUES ('C.10.13-9/00',4,'Fabricação de produtos de carne');
INSERT INTO migra_cnae VALUES ('C.10.13-9/01',5,'Fabricação de produtos de carne');
INSERT INTO migra_cnae VALUES ('C.10.13-9/02',5,'Preparação de subprodutos do abate');
INSERT INTO migra_cnae VALUES ('C.10.20-0/00',3,'Preservação do pescado e fabricação de produtos do pescado');
INSERT INTO migra_cnae VALUES ('C.10.20-1/00',4,'Preservação do pescado e fabricação de produtos do pescado');
INSERT INTO migra_cnae VALUES ('C.10.20-1/01',5,'Preservação de peixes, crustáceos e moluscos');
INSERT INTO migra_cnae VALUES ('C.10.20-1/02',5,'Fabricação de conservas de peixes, crustáceos e moluscos');
INSERT INTO migra_cnae VALUES ('C.10.30-0/00',3,'Fabricação de conservas de frutas, legumes e outros vegetais');
INSERT INTO migra_cnae VALUES ('C.10.31-7/00',4,'Fabricação de conservas de frutas');
INSERT INTO migra_cnae VALUES ('C.10.31-7/00',5,'Fabricação de conservas de frutas');
INSERT INTO migra_cnae VALUES ('C.10.32-5/00',4,'Fabricação de conservas de legumes e outros vegetais');
INSERT INTO migra_cnae VALUES ('C.10.32-5/01',5,'Fabricação de conservas de palmito');
INSERT INTO migra_cnae VALUES ('C.10.32-5/99',5,'Fabricação de conservas de legumes e outros vegetais, exceto palmito');
INSERT INTO migra_cnae VALUES ('C.10.33-3/00',4,'Fabricação de sucos de frutas, hortaliças e legumes');
INSERT INTO migra_cnae VALUES ('C.10.33-3/01',5,'Fabricação de sucos concentrados de frutas, hortaliças e legumes');
INSERT INTO migra_cnae VALUES ('C.10.33-3/02',5,'Fabricação de sucos de frutas, hortaliças e legumes, exceto concentrados');
INSERT INTO migra_cnae VALUES ('C.10.40-0/00',3,'Fabricação de óleos e gorduras vegetais e animais');
INSERT INTO migra_cnae VALUES ('C.10.41-4/00',4,'Fabricação de óleos vegetais em bruto, exceto óleo de milho');
INSERT INTO migra_cnae VALUES ('C.10.41-4/00',5,'Fabricação de óleos vegetais em bruto, exceto óleo de milho');
INSERT INTO migra_cnae VALUES ('C.10.42-2/00',4,'Fabricação de óleos vegetais refinados, exceto óleo de milho');
INSERT INTO migra_cnae VALUES ('C.10.42-2/00',5,'Fabricação de óleos vegetais refinados, exceto óleo de milho');
INSERT INTO migra_cnae VALUES ('C.10.43-1/00',4,'Fabricação de margarina e outras gorduras vegetais e de óleos não-comestíveis de animais');
INSERT INTO migra_cnae VALUES ('C.10.43-1/00',5,'Fabricação de margarina e outras gorduras vegetais e de óleos não-comestíveis de animais');
INSERT INTO migra_cnae VALUES ('C.10.50-0/00',3,'Laticínios');
INSERT INTO migra_cnae VALUES ('C.10.51-1/00',4,'Preparação do leite');
INSERT INTO migra_cnae VALUES ('C.10.51-1/00',5,'Preparação do leite');
INSERT INTO migra_cnae VALUES ('C.10.52-0/00',4,'Fabricação de laticínios');
INSERT INTO migra_cnae VALUES ('C.10.52-0/00',5,'Fabricação de laticínios');
INSERT INTO migra_cnae VALUES ('C.10.53-8/00',4,'Fabricação de sorvetes e outros gelados comestíveis');
INSERT INTO migra_cnae VALUES ('C.10.53-8/00',5,'Fabricação de sorvetes e outros gelados comestíveis');
INSERT INTO migra_cnae VALUES ('C.10.60-0/00',3,'Moagem, fabricação de produtos amiláceos e de alimentos para animais');
INSERT INTO migra_cnae VALUES ('C.10.61-9/00',4,'Beneficiamento de arroz e fabricação de produtos do arroz');
INSERT INTO migra_cnae VALUES ('C.10.61-9/01',5,'Beneficiamento de arroz');
INSERT INTO migra_cnae VALUES ('C.10.61-9/02',5,'Fabricação de produtos do arroz');
INSERT INTO migra_cnae VALUES ('C.10.62-7/00',4,'Moagem de trigo e fabricação de derivados');
INSERT INTO migra_cnae VALUES ('C.10.62-7/00',5,'Moagem de trigo e fabricação de derivados');
INSERT INTO migra_cnae VALUES ('C.10.63-5/00',4,'Fabricação de farinha de mandioca e derivados');
INSERT INTO migra_cnae VALUES ('C.10.63-5/00',5,'Fabricação de farinha de mandioca e derivados');
INSERT INTO migra_cnae VALUES ('C.10.64-3/00',4,'Fabricação de farinha de milho e derivados, exceto óleos de milho');
INSERT INTO migra_cnae VALUES ('C.10.64-3/00',5,'Fabricação de farinha de milho e derivados, exceto óleos de milho');
INSERT INTO migra_cnae VALUES ('C.10.65-1/00',4,'Fabricação de amidos e féculas de vegetais e de óleos de milho');
INSERT INTO migra_cnae VALUES ('C.10.65-1/01',5,'Fabricação de amidos e féculas de vegetais');
INSERT INTO migra_cnae VALUES ('C.10.65-1/02',5,'Fabricação de óleo de milho em bruto');
INSERT INTO migra_cnae VALUES ('C.10.65-1/03',5,'Fabricação de óleo de milho refinado');
INSERT INTO migra_cnae VALUES ('C.10.66-0/00',4,'Fabricação de alimentos para animais');
INSERT INTO migra_cnae VALUES ('C.10.66-0/00',5,'Fabricação de alimentos para animais');
INSERT INTO migra_cnae VALUES ('C.10.69-4/00',4,'Moagem e fabricação de produtos de origem vegetal não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.10.69-4/00',5,'Moagem e fabricação de produtos de origem vegetal não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.10.70-0/00',3,'Fabricação e refino de açúcar');
INSERT INTO migra_cnae VALUES ('C.10.71-6/00',4,'Fabricação de açúcar em bruto');
INSERT INTO migra_cnae VALUES ('C.10.71-6/00',5,'Fabricação de açúcar em bruto');
INSERT INTO migra_cnae VALUES ('C.10.72-4/00',4,'Fabricação de açúcar refinado');
INSERT INTO migra_cnae VALUES ('C.10.72-4/01',5,'Fabricação de açúcar de cana refinado');
INSERT INTO migra_cnae VALUES ('C.10.72-4/02',5,'Fabricação de açúcar de cereais (dextrose) e de beterraba');
INSERT INTO migra_cnae VALUES ('C.10.80-0/00',3,'Torrefação e moagem de café');
INSERT INTO migra_cnae VALUES ('C.10.81-3/00',4,'Torrefação e moagem de café');
INSERT INTO migra_cnae VALUES ('C.10.81-3/01',5,'Beneficiamento de café');
INSERT INTO migra_cnae VALUES ('C.10.81-3/02',5,'Torrefação e moagem de café');
INSERT INTO migra_cnae VALUES ('C.10.82-1/00',4,'Fabricação de produtos à base de café');
INSERT INTO migra_cnae VALUES ('C.10.82-1/00',5,'Fabricação de produtos à base de café');
INSERT INTO migra_cnae VALUES ('C.10.90-0/00',3,'Fabricação de outros produtos alimentícios');
INSERT INTO migra_cnae VALUES ('C.10.91-1/00',4,'Fabricação de produtos de panificação');
INSERT INTO migra_cnae VALUES ('C.10.91-1/01',5,'Fabricação de produtos de panificação industrial');
INSERT INTO migra_cnae VALUES ('C.10.91-1/02',5,'Fabricação de produtos de padaria e confeitaria com predominância de produção própria');
INSERT INTO migra_cnae VALUES ('C.10.92-9/00',4,'Fabricação de biscoitos e bolachas');
INSERT INTO migra_cnae VALUES ('C.10.92-9/00',5,'Fabricação de biscoitos e bolachas');
INSERT INTO migra_cnae VALUES ('C.10.93-7/00',4,'Fabricação de produtos derivados do cacau, de chocolates e confeitos');
INSERT INTO migra_cnae VALUES ('C.10.93-7/01',5,'Fabricação de produtos derivados do cacau e de chocolates');
INSERT INTO migra_cnae VALUES ('C.10.93-7/02',5,'Fabricação de frutas cristalizadas, balas e semelhantes');
INSERT INTO migra_cnae VALUES ('C.10.94-5/00',4,'Fabricação de massas alimentícias');
INSERT INTO migra_cnae VALUES ('C.10.94-5/00',5,'Fabricação de massas alimentícias');
INSERT INTO migra_cnae VALUES ('C.10.95-3/00',4,'Fabricação de especiarias, molhos, temperos e condimentos');
INSERT INTO migra_cnae VALUES ('C.10.95-3/00',5,'Fabricação de especiarias, molhos, temperos e condimentos');
INSERT INTO migra_cnae VALUES ('C.10.96-1/00',4,'Fabricação de alimentos e pratos prontos');
INSERT INTO migra_cnae VALUES ('C.10.96-1/00',5,'Fabricação de alimentos e pratos prontos');
INSERT INTO migra_cnae VALUES ('C.10.99-6/00',4,'Fabricação de produtos alimentícios não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.10.99-6/01',5,'Fabricação de vinagres');
INSERT INTO migra_cnae VALUES ('C.10.99-6/02',5,'Fabricação de pós alimentícios');
INSERT INTO migra_cnae VALUES ('C.10.99-6/03',5,'Fabricação de fermentos e leveduras');
INSERT INTO migra_cnae VALUES ('C.10.99-6/04',5,'Fabricação de gelo comum');
INSERT INTO migra_cnae VALUES ('C.10.99-6/05',5,'Fabricação de produtos para infusão (chá, mate, etc.)');
INSERT INTO migra_cnae VALUES ('C.10.99-6/06',5,'Fabricação de adoçantes naturais e artificiais');
INSERT INTO migra_cnae VALUES ('C.10.99-6/07',5,'Fabricação de alimentos dietéticos e complementos alimentares');
INSERT INTO migra_cnae VALUES ('C.10.99-6/99',5,'Fabricação de outros produtos alimentícios não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.11.00-0/00',2,'FABRICAÇÃO DE BEBIDAS');
INSERT INTO migra_cnae VALUES ('C.11.10-0/00',3,'Fabricação de bebidas alcoólicas');
INSERT INTO migra_cnae VALUES ('C.11.11-9/00',4,'Fabricação de aguardentes e outras bebidas destiladas');
INSERT INTO migra_cnae VALUES ('C.11.11-9/01',5,'Fabricação de aguardente de cana-de-açúcar');
INSERT INTO migra_cnae VALUES ('C.11.11-9/02',5,'Fabricação de outras aguardentes e bebidas destiladas');
INSERT INTO migra_cnae VALUES ('C.11.12-7/00',4,'Fabricação de vinho');
INSERT INTO migra_cnae VALUES ('C.11.12-7/00',5,'Fabricação de vinho');
INSERT INTO migra_cnae VALUES ('C.11.13-5/00',4,'Fabricação de malte, cervejas e chopes');
INSERT INTO migra_cnae VALUES ('C.11.13-5/01',5,'Fabricação de malte, inclusive malte uísque');
INSERT INTO migra_cnae VALUES ('C.11.13-5/02',5,'Fabricação de cervejas e chopes');
INSERT INTO migra_cnae VALUES ('C.11.20-0/00',3,'Fabricação de bebidas não-alcoólicas');
INSERT INTO migra_cnae VALUES ('C.11.21-6/00',4,'Fabricação de águas envasadas');
INSERT INTO migra_cnae VALUES ('C.11.21-6/00',5,'Fabricação de águas envasadas');
INSERT INTO migra_cnae VALUES ('C.11.22-4/00',4,'Fabricação de refrigerantes e de outras bebidas não-alcoólicas');
INSERT INTO migra_cnae VALUES ('C.11.22-4/01',5,'Fabricação de refrigerantes');
INSERT INTO migra_cnae VALUES ('C.11.22-4/02',5,'Fabricação de chá mate e outros chás prontos para consumo');
INSERT INTO migra_cnae VALUES ('C.11.22-4/03',5,'Fabricação de refrescos, xaropes e pós para refrescos, exceto refrescos de frutas');
INSERT INTO migra_cnae VALUES ('C.11.22-4/04',5,'Fabricação de bebidas isotônicas');
INSERT INTO migra_cnae VALUES ('C.11.22-4/99',5,'Fabricação de outras bebidas não-alcoólicas não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('C.12.00-0/00',2,'FABRICAÇÃO DE PRODUTOS DO FUMO');
INSERT INTO migra_cnae VALUES ('C.12.10-0/00',3,'Processamento industrial do fumo');
INSERT INTO migra_cnae VALUES ('C.12.10-7/00',4,'Processamento industrial do fumo');
INSERT INTO migra_cnae VALUES ('C.12.10-7/00',5,'Processamento industrial do fumo');
INSERT INTO migra_cnae VALUES ('C.12.20-0/00',3,'Fabricação de produtos do fumo');
INSERT INTO migra_cnae VALUES ('C.12.20-4/00',4,'Fabricação de produtos do fumo');
INSERT INTO migra_cnae VALUES ('C.12.20-4/01',5,'Fabricação de cigarros');
INSERT INTO migra_cnae VALUES ('C.12.20-4/02',5,'Fabricação de cigarrilhas e charutos');
INSERT INTO migra_cnae VALUES ('C.12.20-4/03',5,'Fabricação de filtros para cigarros');
INSERT INTO migra_cnae VALUES ('C.12.20-4/99',5,'Fabricação de outros produtos do fumo, exceto cigarros, cigarrilhas e charutos');
INSERT INTO migra_cnae VALUES ('C.13.00-0/00',2,'FABRICAÇÃO DE PRODUTOS TÊXTEIS');
INSERT INTO migra_cnae VALUES ('C.13.10-0/00',3,'Preparação e fiação de fibras têxteis');
INSERT INTO migra_cnae VALUES ('C.13.11-1/00',4,'Preparação e fiação de fibras de algodão');
INSERT INTO migra_cnae VALUES ('C.13.11-1/00',5,'Preparação e fiação de fibras de algodão');
INSERT INTO migra_cnae VALUES ('C.13.12-0/00',4,'Preparação e fiação de fibras têxteis naturais, exceto algodão');
INSERT INTO migra_cnae VALUES ('C.13.12-0/00',5,'Preparação e fiação de fibras têxteis naturais, exceto algodão');
INSERT INTO migra_cnae VALUES ('C.13.13-8/00',4,'Fiação de fibras artificiais e sintéticas');
INSERT INTO migra_cnae VALUES ('C.13.13-8/00',5,'Fiação de fibras artificiais e sintéticas');
INSERT INTO migra_cnae VALUES ('C.13.14-6/00',4,'Fabricação de linhas para costurar e bordar');
INSERT INTO migra_cnae VALUES ('C.13.14-6/00',5,'Fabricação de linhas para costurar e bordar');
INSERT INTO migra_cnae VALUES ('C.13.20-0/00',3,'Tecelagem, exceto malha');
INSERT INTO migra_cnae VALUES ('C.13.21-9/00',4,'Tecelagem de fios de algodão');
INSERT INTO migra_cnae VALUES ('C.13.21-9/00',5,'Tecelagem de fios de algodão');
INSERT INTO migra_cnae VALUES ('C.13.22-7/00',4,'Tecelagem de fios de fibras têxteis naturais, exceto algodão');
INSERT INTO migra_cnae VALUES ('C.13.22-7/00',5,'Tecelagem de fios de fibras têxteis naturais, exceto algodão');
INSERT INTO migra_cnae VALUES ('C.13.23-5/00',4,'Tecelagem de fios de fibras artificiais e sintéticas');
INSERT INTO migra_cnae VALUES ('C.13.23-5/00',5,'Tecelagem de fios de fibras artificiais e sintéticas');
INSERT INTO migra_cnae VALUES ('C.13.30-0/00',3,'Fabricação de tecidos de malha');
INSERT INTO migra_cnae VALUES ('C.13.30-8/00',4,'Fabricação de tecidos de malha');
INSERT INTO migra_cnae VALUES ('C.13.30-8/00',5,'Fabricação de tecidos de malha');
INSERT INTO migra_cnae VALUES ('C.13.40-0/00',3,'Acabamentos em fios, tecidos e artefatos têxteis');
INSERT INTO migra_cnae VALUES ('C.13.40-5/00',4,'Acabamentos em fios, tecidos e artefatos têxteis');
INSERT INTO migra_cnae VALUES ('C.13.40-5/01',5,'Estamparia e texturização em fios, tecidos, artefatos têxteis e peças do vestuário');
INSERT INTO migra_cnae VALUES ('C.13.40-5/02',5,'Alvejamento, tingimento e torção em fios, tecidos, artefatos têxteis e peças do vestuário');
INSERT INTO migra_cnae VALUES ('C.13.40-5/99',5,'Outros serviços de acabamento em fios, tecidos, artefatos têxteis e peças do vestuário');
INSERT INTO migra_cnae VALUES ('C.13.50-0/00',3,'Fabricação de artefatos têxteis, exceto vestuário');
INSERT INTO migra_cnae VALUES ('C.13.51-1/00',4,'Fabricação de artefatos têxteis para uso doméstico');
INSERT INTO migra_cnae VALUES ('C.13.51-1/00',5,'Fabricação de artefatos têxteis para uso doméstico');
INSERT INTO migra_cnae VALUES ('C.13.52-9/00',4,'Fabricação de artefatos de tapeçaria');
INSERT INTO migra_cnae VALUES ('C.13.52-9/00',5,'Fabricação de artefatos de tapeçaria');
INSERT INTO migra_cnae VALUES ('C.13.53-7/00',4,'Fabricação de artefatos de cordoaria');
INSERT INTO migra_cnae VALUES ('C.13.53-7/00',5,'Fabricação de artefatos de cordoaria');
INSERT INTO migra_cnae VALUES ('C.13.54-5/00',4,'Fabricação de tecidos especiais, inclusive artefatos');
INSERT INTO migra_cnae VALUES ('C.13.54-5/00',5,'Fabricação de tecidos especiais, inclusive artefatos');
INSERT INTO migra_cnae VALUES ('C.13.59-6/00',4,'Fabricação de outros produtos têxteis não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.13.59-6/00',5,'Fabricação de outros produtos têxteis não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.14.00-0/00',2,'CONFECÇÃO DE ARTIGOS DO VESTUÁRIO E ACESSÓRIOS');
INSERT INTO migra_cnae VALUES ('C.14.10-0/00',3,'Confecção de artigos do vestuário e acessórios');
INSERT INTO migra_cnae VALUES ('C.14.11-8/00',4,'Confecção de roupas íntimas');
INSERT INTO migra_cnae VALUES ('C.14.11-8/01',5,'Confecção de roupas íntimas');
INSERT INTO migra_cnae VALUES ('C.14.11-8/02',5,'Facção de roupas íntimas');
INSERT INTO migra_cnae VALUES ('C.14.12-6/00',4,'Confecção de peças do vestuário, exceto roupas íntimas');
INSERT INTO migra_cnae VALUES ('C.14.12-6/01',5,'Confecção de peças do vestuário, exceto roupas íntimas e as confeccionadas sob medida');
INSERT INTO migra_cnae VALUES ('C.14.12-6/02',5,'Confecção, sob medida, de peças do vestuário, exceto roupas íntimas');
INSERT INTO migra_cnae VALUES ('C.14.12-6/03',5,'Facção de peças do vestuário, exceto roupas íntimas');
INSERT INTO migra_cnae VALUES ('C.14.13-4/00',4,'Confecção de roupas profissionais');
INSERT INTO migra_cnae VALUES ('C.14.13-4/01',5,'Confecção de roupas profissionais, exceto sob medida');
INSERT INTO migra_cnae VALUES ('C.14.13-4/02',5,'Confecção, sob medida, de roupas profissionais');
INSERT INTO migra_cnae VALUES ('C.14.13-4/03',5,'Facção de roupas profissionais');
INSERT INTO migra_cnae VALUES ('C.14.14-2/00',4,'Fabricação de acessórios do vestuário, exceto para segurança e proteção');
INSERT INTO migra_cnae VALUES ('C.14.14-2/00',5,'Fabricação de acessórios do vestuário, exceto para segurança e proteção');
INSERT INTO migra_cnae VALUES ('C.14.20-0/00',3,'Fabricação de artigos de malharia e tricotagem');
INSERT INTO migra_cnae VALUES ('C.14.21-5/00',4,'Fabricação de meias');
INSERT INTO migra_cnae VALUES ('C.14.21-5/00',5,'Fabricação de meias');
INSERT INTO migra_cnae VALUES ('C.14.22-3/00',4,'Fabricação de artigos do vestuário, produzidos em malharias e tricotagens, exceto meias');
INSERT INTO migra_cnae VALUES ('C.14.22-3/00',5,'Fabricação de artigos do vestuário, produzidos em malharias e tricotagens, exceto meias');
INSERT INTO migra_cnae VALUES ('C.15.00-0/00',2,'PREPARAÇÃO DE COUROS E FABRICAÇÃO DE ARTEFATOS DE COURO, ARTIGOS PARA VIAGEM E CALÇADOS');
INSERT INTO migra_cnae VALUES ('C.15.10-0/00',3,'Curtimento e outras preparações de couro');
INSERT INTO migra_cnae VALUES ('C.15.10-6/00',4,'Curtimento e outras preparações de couro');
INSERT INTO migra_cnae VALUES ('C.15.10-6/00',5,'Curtimento e outras preparações de couro');
INSERT INTO migra_cnae VALUES ('C.15.20-0/00',3,'Fabricação de artigos para viagem e de artefatos diversos de couro');
INSERT INTO migra_cnae VALUES ('C.15.21-1/00',4,'Fabricação de artigos para viagem, bolsas e semelhantes de qualquer material');
INSERT INTO migra_cnae VALUES ('C.15.21-1/00',5,'Fabricação de artigos para viagem, bolsas e semelhantes de qualquer material');
INSERT INTO migra_cnae VALUES ('C.15.29-7/00',4,'Fabricação de artefatos de couro não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.15.29-7/00',5,'Fabricação de artefatos de couro não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.15.30-0/00',3,'Fabricação de calçados');
INSERT INTO migra_cnae VALUES ('C.15.31-9/00',4,'Fabricação de calçados de couro');
INSERT INTO migra_cnae VALUES ('C.15.31-9/01',5,'Fabricação de calçados de couro');
INSERT INTO migra_cnae VALUES ('C.15.31-9/02',5,'Acabamento de calçados de couro sob contrato');
INSERT INTO migra_cnae VALUES ('C.15.32-7/00',4,'Fabricação de tênis de qualquer material');
INSERT INTO migra_cnae VALUES ('C.15.32-7/00',5,'Fabricação de tênis de qualquer material');
INSERT INTO migra_cnae VALUES ('C.15.33-5/00',4,'Fabricação de calçados de material sintético');
INSERT INTO migra_cnae VALUES ('C.15.33-5/00',5,'Fabricação de calçados de material sintético');
INSERT INTO migra_cnae VALUES ('C.15.39-4/00',4,'Fabricação de calçados de materiais não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.15.39-4/00',5,'Fabricação de calçados de materiais não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.15.40-0/00',3,'Fabricação de partes para calçados, de qualquer material');
INSERT INTO migra_cnae VALUES ('C.15.40-8/00',4,'Fabricação de partes para calçados, de qualquer material');
INSERT INTO migra_cnae VALUES ('C.15.40-8/00',5,'Fabricação de partes para calçados, de qualquer material');
INSERT INTO migra_cnae VALUES ('C.16.00-0/00',2,'FABRICAÇÃO DE PRODUTOS DE MADEIRA');
INSERT INTO migra_cnae VALUES ('C.16.10-0/00',3,'Desdobramento de madeira');
INSERT INTO migra_cnae VALUES ('C.16.10-2/00',4,'Desdobramento de madeira');
INSERT INTO migra_cnae VALUES ('C.16.10-2/01',5,'Serrarias com desdobramento de madeira');
INSERT INTO migra_cnae VALUES ('C.16.10-2/02',5,'Serrarias sem desdobramento de madeira');
INSERT INTO migra_cnae VALUES ('C.16.20-0/00',3,'Fabricação de produtos de madeira, cortiça e material trançado, exceto móveis');
INSERT INTO migra_cnae VALUES ('C.16.21-8/00',4,'Fabricação de madeira laminada e de chapas de madeira compensada, prensada e aglomerada');
INSERT INTO migra_cnae VALUES ('C.16.21-8/00',5,'Fabricação de madeira laminada e de chapas de madeira compensada, prensada e aglomerada');
INSERT INTO migra_cnae VALUES ('C.16.22-6/00',4,'Fabricação de estruturas de madeira e de artigos de carpintaria para construção');
INSERT INTO migra_cnae VALUES ('C.16.22-6/01',5,'Fabricação de casas de madeira pré-fabricadas');
INSERT INTO migra_cnae VALUES ('C.16.22-6/02',5,'Fabricação de esquadrias de madeira e de peças de madeira para instalações industriais e comerciais');
INSERT INTO migra_cnae VALUES ('C.16.22-6/99',5,'Fabricação de outros artigos de carpintaria para construção');
INSERT INTO migra_cnae VALUES ('C.16.23-4/00',4,'Fabricação de artefatos de tanoaria e de embalagens de madeira');
INSERT INTO migra_cnae VALUES ('C.16.23-4/00',5,'Fabricação de artefatos de tanoaria e de embalagens de madeira');
INSERT INTO migra_cnae VALUES ('C.16.29-3/00',4,'Fabricação de artefatos de madeira, palha, cortiça, vime e material trançado não especificados anteriormente, exceto móveis');
INSERT INTO migra_cnae VALUES ('C.16.29-3/01',5,'Fabricação de artefatos diversos de madeira, exceto móveis');
INSERT INTO migra_cnae VALUES ('C.16.29-3/02',5,'Fabricação de artefatos diversos de cortiça, bambu, palha, vime e outros materiais trançados, exceto móveis');
INSERT INTO migra_cnae VALUES ('C.17.00-0/00',2,'FABRICAÇÃO DE CELULOSE, PAPEL E PRODUTOS DE PAPEL');
INSERT INTO migra_cnae VALUES ('C.17.10-0/00',3,'Fabricação de celulose e outras pastas para a fabricação de papel');
INSERT INTO migra_cnae VALUES ('C.17.10-9/00',4,'Fabricação de celulose e outras pastas para a fabricação de papel');
INSERT INTO migra_cnae VALUES ('C.17.10-9/00',5,'Fabricação de celulose e outras pastas para a fabricação de papel');
INSERT INTO migra_cnae VALUES ('C.17.20-0/00',3,'Fabricação de papel, cartolina e papel-cartão');
INSERT INTO migra_cnae VALUES ('C.17.21-4/00',4,'Fabricação de papel');
INSERT INTO migra_cnae VALUES ('C.17.21-4/00',5,'Fabricação de papel');
INSERT INTO migra_cnae VALUES ('C.17.22-2/00',4,'Fabricação de cartolina e papel-cartão');
INSERT INTO migra_cnae VALUES ('C.17.22-2/00',5,'Fabricação de cartolina e papel-cartão');
INSERT INTO migra_cnae VALUES ('C.17.30-0/00',3,'Fabricação de embalagens de papel, cartolina, papel-cartão e papelão ondulado');
INSERT INTO migra_cnae VALUES ('C.17.31-1/00',4,'Fabricação de embalagens de papel');
INSERT INTO migra_cnae VALUES ('C.17.31-1/00',5,'Fabricação de embalagens de papel');
INSERT INTO migra_cnae VALUES ('C.17.32-0/00',4,'Fabricação de embalagens de cartolina e papel-cartão');
INSERT INTO migra_cnae VALUES ('C.17.32-0/00',5,'Fabricação de embalagens de cartolina e papel-cartão');
INSERT INTO migra_cnae VALUES ('C.17.33-8/00',4,'Fabricação de chapas e de embalagens de papelão ondulado');
INSERT INTO migra_cnae VALUES ('C.17.33-8/00',5,'Fabricação de chapas e de embalagens de papelão ondulado');
INSERT INTO migra_cnae VALUES ('C.17.40-0/00',3,'Fabricação de produtos diversos de papel, cartolina, papel-cartão e papelão ondulado');
INSERT INTO migra_cnae VALUES ('C.17.41-9/00',4,'Fabricação de produtos de papel, cartolina, papel-cartão e papelão ondulado para uso comercial e de escritório');
INSERT INTO migra_cnae VALUES ('C.17.41-9/01',5,'Fabricação de formulários contínuos');
INSERT INTO migra_cnae VALUES ('C.17.41-9/02',5,'Fabricação de produtos de papel, cartolina, papel-cartão e papelão ondulado para uso comercial e de escritório');
INSERT INTO migra_cnae VALUES ('C.17.42-7/00',4,'Fabricação de produtos de papel para usos doméstico e higiênico-sanitário');
INSERT INTO migra_cnae VALUES ('C.17.42-7/01',5,'Fabricação de fraldas descartáveis');
INSERT INTO migra_cnae VALUES ('C.17.42-7/02',5,'Fabricação de absorventes higiênicos');
INSERT INTO migra_cnae VALUES ('C.17.42-7/99',5,'Fabricação de produtos de papel para uso doméstico e higiênico-sanitário não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.17.49-4/00',4,'Fabricação de produtos de pastas celulósicas, papel, cartolina, papel-cartão e papelão ondulado não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.17.49-4/00',5,'Fabricação de produtos de pastas celulósicas, papel, cartolina, papel-cartão e papelão ondulado não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.18.00-0/00',2,'IMPRESSÃO E REPRODUÇÃO DE GRAVAÇÕES');
INSERT INTO migra_cnae VALUES ('C.18.10-0/00',3,'Atividade de impressão');
INSERT INTO migra_cnae VALUES ('C.18.11-3/00',4,'Impressão de jornais, livros, revistas e outras publicações periódicas');
INSERT INTO migra_cnae VALUES ('C.18.11-3/01',5,'Impressão de jornais');
INSERT INTO migra_cnae VALUES ('C.18.11-3/02',5,'Impressão de livros, revistas e outras publicações periódicas');
INSERT INTO migra_cnae VALUES ('C.18.12-1/00',4,'Impressão de material de segurança');
INSERT INTO migra_cnae VALUES ('C.18.12-1/00',5,'Impressão de material de segurança');
INSERT INTO migra_cnae VALUES ('C.18.13-0/00',4,'Impressão de materiais para outros usos');
INSERT INTO migra_cnae VALUES ('C.18.13-0/01',5,'Impressão de material para uso publicitário');
INSERT INTO migra_cnae VALUES ('C.18.13-0/99',5,'Impressão de material para outros usos');
INSERT INTO migra_cnae VALUES ('C.18.20-0/00',3,'Serviços de pré-impressão e acabamentos gráficos');
INSERT INTO migra_cnae VALUES ('C.18.21-1/00',4,'Serviços de pré-impressão');
INSERT INTO migra_cnae VALUES ('C.18.21-1/00',5,'Serviços de pré-impressão');
INSERT INTO migra_cnae VALUES ('C.18.22-9/00',4,'Serviços de acabamentos gráficos');
INSERT INTO migra_cnae VALUES ('C.18.22-9/01',5,'Serviços de encadernação e plastificação');
INSERT INTO migra_cnae VALUES ('C.18.22-9/99',5,'Serviços de acabamentos gráficos, exceto encadernação e plastificação');
INSERT INTO migra_cnae VALUES ('C.18.30-0/00',3,'Reprodução de materiais gravados em qualquer suporte');
INSERT INTO migra_cnae VALUES ('C.18.30-0/00',4,'Reprodução de materiais gravados em qualquer suporte');
INSERT INTO migra_cnae VALUES ('C.18.30-0/01',5,'Reprodução de som em qualquer suporte');
INSERT INTO migra_cnae VALUES ('C.18.30-0/02',5,'Reprodução de vídeo em qualquer suporte');
INSERT INTO migra_cnae VALUES ('C.18.30-0/03',5,'Reprodução de software em qualquer suporte');
INSERT INTO migra_cnae VALUES ('C.19.00-0/00',2,'FABRICAÇÃO DE COQUE, DE PRODUTOS DERIVADOS DO PETRÓLEO E DE BIOCOMBUSTÍVEIS');
INSERT INTO migra_cnae VALUES ('C.19.10-0/00',3,'Coquerias');
INSERT INTO migra_cnae VALUES ('C.19.10-1/00',4,'Coquerias');
INSERT INTO migra_cnae VALUES ('C.19.10-1/00',5,'Coquerias');
INSERT INTO migra_cnae VALUES ('C.19.20-0/00',3,'Fabricação de produtos derivados do petróleo');
INSERT INTO migra_cnae VALUES ('C.19.21-7/00',4,'Fabricação de produtos do refino de petróleo');
INSERT INTO migra_cnae VALUES ('C.19.21-7/00',5,'Fabricação de produtos do refino de petróleo');
INSERT INTO migra_cnae VALUES ('C.19.22-5/00',4,'Fabricação de produtos derivados do petróleo, exceto produtos do refino');
INSERT INTO migra_cnae VALUES ('C.19.22-5/01',5,'Formulação de combustíveis');
INSERT INTO migra_cnae VALUES ('C.19.22-5/02',5,'Rerrefino de óleos lubrificantes');
INSERT INTO migra_cnae VALUES ('C.19.22-5/99',5,'Fabricação de outros produtos derivados do petróleo, exceto produtos do refino');
INSERT INTO migra_cnae VALUES ('C.19.30-0/00',3,'Fabricação de biocombustíveis');
INSERT INTO migra_cnae VALUES ('C.19.31-4/00',4,'Fabricação de álcool');
INSERT INTO migra_cnae VALUES ('C.19.31-4/00',5,'Fabricação de álcool');
INSERT INTO migra_cnae VALUES ('C.19.32-2/00',4,'Fabricação de biocombustíveis, exceto álcool');
INSERT INTO migra_cnae VALUES ('C.19.32-2/00',5,'Fabricação de biocombustíveis, exceto álcool');
INSERT INTO migra_cnae VALUES ('C.20.00-0/00',2,'FABRICAÇÃO DE PRODUTOS QUÍMICOS');
INSERT INTO migra_cnae VALUES ('C.20.10-0/00',3,'Fabricação de produtos químicos inorgânicos');
INSERT INTO migra_cnae VALUES ('C.20.11-8/00',4,'Fabricação de cloro e álcalis');
INSERT INTO migra_cnae VALUES ('C.20.11-8/00',5,'Fabricação de cloro e álcalis');
INSERT INTO migra_cnae VALUES ('C.20.12-6/00',4,'Fabricação de intermediários para fertilizantes');
INSERT INTO migra_cnae VALUES ('C.20.12-6/00',5,'Fabricação de intermediários para fertilizantes');
INSERT INTO migra_cnae VALUES ('C.20.13-4/00',4,'Fabricação de adubos e fertilizantes');
INSERT INTO migra_cnae VALUES ('C.20.13-4/01',5,'Fabricação de adubos e fertilizantes organo-minerais');
INSERT INTO migra_cnae VALUES ('C.20.13-4/02',5,'Fabricação de adubos e fertilizantes, exceto organo-minerais');
INSERT INTO migra_cnae VALUES ('C.20.14-2/00',4,'Fabricação de gases industriais');
INSERT INTO migra_cnae VALUES ('C.20.14-2/00',5,'Fabricação de gases industriais');
INSERT INTO migra_cnae VALUES ('C.20.19-3/00',4,'Fabricação de produtos químicos inorgânicos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.20.19-3/01',5,'Elaboração de combustíveis nucleares');
INSERT INTO migra_cnae VALUES ('C.20.19-3/99',5,'Fabricação de outros produtos químicos inorgânicos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.20.20-0/00',3,'Fabricação de produtos químicos orgânicos');
INSERT INTO migra_cnae VALUES ('C.20.21-5/00',4,'Fabricação de produtos petroquímicos básicos');
INSERT INTO migra_cnae VALUES ('C.20.21-5/00',5,'Fabricação de produtos petroquímicos básicos');
INSERT INTO migra_cnae VALUES ('C.20.22-3/00',4,'Fabricação de intermediários para plastificantes, resinas e fibras');
INSERT INTO migra_cnae VALUES ('C.20.22-3/00',5,'Fabricação de intermediários para plastificantes, resinas e fibras');
INSERT INTO migra_cnae VALUES ('C.20.29-1/00',4,'Fabricação de produtos químicos orgânicos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.20.29-1/00',5,'Fabricação de produtos químicos orgânicos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.20.30-0/00',3,'Fabricação de resinas e elastômeros');
INSERT INTO migra_cnae VALUES ('C.20.31-2/00',4,'Fabricação de resinas termoplásticas');
INSERT INTO migra_cnae VALUES ('C.20.31-2/00',5,'Fabricação de resinas termoplásticas');
INSERT INTO migra_cnae VALUES ('C.20.32-1/00',4,'Fabricação de resinas termofixas');
INSERT INTO migra_cnae VALUES ('C.20.32-1/00',5,'Fabricação de resinas termofixas');
INSERT INTO migra_cnae VALUES ('C.20.33-9/00',4,'Fabricação de elastômeros');
INSERT INTO migra_cnae VALUES ('C.20.33-9/00',5,'Fabricação de elastômeros');
INSERT INTO migra_cnae VALUES ('C.20.40-0/00',3,'Fabricação de fibras artificiais e sintéticas');
INSERT INTO migra_cnae VALUES ('C.20.40-1/00',4,'Fabricação de fibras artificiais e sintéticas');
INSERT INTO migra_cnae VALUES ('C.20.40-1/00',5,'Fabricação de fibras artificiais e sintéticas');
INSERT INTO migra_cnae VALUES ('C.20.50-0/00',3,'Fabricação de defensivos agrícolas e desinfestantes domissanitários');
INSERT INTO migra_cnae VALUES ('C.20.51-7/00',4,'Fabricação de defensivos agrícolas');
INSERT INTO migra_cnae VALUES ('C.20.51-7/00',5,'Fabricação de defensivos agrícolas');
INSERT INTO migra_cnae VALUES ('C.20.52-5/00',4,'Fabricação de desinfestantes domissanitários');
INSERT INTO migra_cnae VALUES ('C.20.52-5/00',5,'Fabricação de desinfestantes domissanitários');
INSERT INTO migra_cnae VALUES ('C.20.60-0/00',3,'Fabricação de sabões, detergentes, produtos de limpeza, cosméticos, produtos de perfumaria e de higiene pessoal');
INSERT INTO migra_cnae VALUES ('C.20.61-4/00',4,'Fabricação de sabões e detergentes sintéticos');
INSERT INTO migra_cnae VALUES ('C.20.61-4/00',5,'Fabricação de sabões e detergentes sintéticos');
INSERT INTO migra_cnae VALUES ('C.20.62-2/00',4,'Fabricação de produtos de limpeza e polimento');
INSERT INTO migra_cnae VALUES ('C.20.62-2/00',5,'Fabricação de produtos de limpeza e polimento');
INSERT INTO migra_cnae VALUES ('C.20.63-1/00',4,'Fabricação de cosméticos, produtos de perfumaria e de higiene pessoal');
INSERT INTO migra_cnae VALUES ('C.20.63-1/00',5,'Fabricação de cosméticos, produtos de perfumaria e de higiene pessoal');
INSERT INTO migra_cnae VALUES ('C.20.70-0/00',3,'Fabricação de tintas, vernizes, esmaltes, lacas e produtos afins');
INSERT INTO migra_cnae VALUES ('C.20.71-1/00',4,'Fabricação de tintas, vernizes, esmaltes e lacas');
INSERT INTO migra_cnae VALUES ('C.20.71-1/00',5,'Fabricação de tintas, vernizes, esmaltes e lacas');
INSERT INTO migra_cnae VALUES ('C.20.72-0/00',4,'Fabricação de tintas de impressão');
INSERT INTO migra_cnae VALUES ('C.20.72-0/00',5,'Fabricação de tintas de impressão');
INSERT INTO migra_cnae VALUES ('C.20.73-8/00',4,'Fabricação de impermeabilizantes, solventes e produtos afins');
INSERT INTO migra_cnae VALUES ('C.20.73-8/00',5,'Fabricação de impermeabilizantes, solventes e produtos afins');
INSERT INTO migra_cnae VALUES ('C.20.90-0/00',3,'Fabricação de produtos e preparados químicos diversos');
INSERT INTO migra_cnae VALUES ('C.20.91-6/00',4,'Fabricação de adesivos e selantes');
INSERT INTO migra_cnae VALUES ('C.20.91-6/00',5,'Fabricação de adesivos e selantes');
INSERT INTO migra_cnae VALUES ('C.20.92-4/00',4,'Fabricação de explosivos');
INSERT INTO migra_cnae VALUES ('C.20.92-4/01',5,'Fabricação de pólvoras, explosivos e detonantes');
INSERT INTO migra_cnae VALUES ('C.20.92-4/02',5,'Fabricação de artigos pirotécnicos');
INSERT INTO migra_cnae VALUES ('C.20.92-4/03',5,'Fabricação de fósforos de segurança');
INSERT INTO migra_cnae VALUES ('C.20.93-2/00',4,'Fabricação de aditivos de uso industrial');
INSERT INTO migra_cnae VALUES ('C.20.93-2/00',5,'Fabricação de aditivos de uso industrial');
INSERT INTO migra_cnae VALUES ('C.20.94-1/00',4,'Fabricação de catalisadores');
INSERT INTO migra_cnae VALUES ('C.20.94-1/00',5,'Fabricação de catalisadores');
INSERT INTO migra_cnae VALUES ('C.20.99-1/00',4,'Fabricação de produtos químicos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.20.99-1/01',5,'Fabricação de chapas, filmes, papéis e outros materiais e produtos químicos para fotografia');
INSERT INTO migra_cnae VALUES ('C.20.99-1/99',5,'Fabricação de outros produtos químicos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.21.00-0/00',2,'FABRICAÇÃO DE PRODUTOS FARMOQUÍMICOS E FARMACÊUTICOS');
INSERT INTO migra_cnae VALUES ('C.21.10-0/00',3,'Fabricação de produtos farmoquímicos');
INSERT INTO migra_cnae VALUES ('C.21.10-6/00',4,'Fabricação de produtos farmoquímicos');
INSERT INTO migra_cnae VALUES ('C.21.10-6/00',5,'Fabricação de produtos farmoquímicos');
INSERT INTO migra_cnae VALUES ('C.21.20-0/00',3,'Fabricação de produtos farmacêuticos');
INSERT INTO migra_cnae VALUES ('C.21.21-1/00',4,'Fabricação de medicamentos para uso humano');
INSERT INTO migra_cnae VALUES ('C.21.21-1/01',5,'Fabricação de medicamentos alopáticos para uso humano');
INSERT INTO migra_cnae VALUES ('C.21.21-1/02',5,'Fabricação de medicamentos homeopáticos para uso humano');
INSERT INTO migra_cnae VALUES ('C.21.21-1/03',5,'Fabricação de medicamentos fitoterápicos para uso humano');
INSERT INTO migra_cnae VALUES ('C.21.22-0/00',4,'Fabricação de medicamentos para uso veterinário');
INSERT INTO migra_cnae VALUES ('C.21.22-0/00',5,'Fabricação de medicamentos para uso veterinário');
INSERT INTO migra_cnae VALUES ('C.21.23-8/00',4,'Fabricação de preparações farmacêuticas');
INSERT INTO migra_cnae VALUES ('C.21.23-8/00',5,'Fabricação de preparações farmacêuticas');
INSERT INTO migra_cnae VALUES ('C.22.00-0/00',2,'FABRICAÇÃO DE PRODUTOS DE BORRACHA E DE MATERIAL PLÁSTICO');
INSERT INTO migra_cnae VALUES ('C.22.10-0/00',3,'Fabricação de produtos de borracha');
INSERT INTO migra_cnae VALUES ('C.22.11-1/00',4,'Fabricação de pneumáticos e de câmaras-de-ar');
INSERT INTO migra_cnae VALUES ('C.22.11-1/00',5,'Fabricação de pneumáticos e de câmaras-de-ar');
INSERT INTO migra_cnae VALUES ('C.22.12-9/00',4,'Reforma de pneumáticos usados');
INSERT INTO migra_cnae VALUES ('C.22.12-9/00',5,'Reforma de pneumáticos usados');
INSERT INTO migra_cnae VALUES ('C.22.19-6/00',4,'Fabricação de artefatos de borracha não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.22.19-6/00',5,'Fabricação de artefatos de borracha não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.22.20-0/00',3,'Fabricação de produtos de material plástico');
INSERT INTO migra_cnae VALUES ('C.22.21-8/00',4,'Fabricação de laminados planos e tubulares de material plástico');
INSERT INTO migra_cnae VALUES ('C.22.21-8/00',5,'Fabricação de laminados planos e tubulares de material plástico');
INSERT INTO migra_cnae VALUES ('C.22.22-6/00',4,'Fabricação de embalagens de material plástico');
INSERT INTO migra_cnae VALUES ('C.22.22-6/00',5,'Fabricação de embalagens de material plástico');
INSERT INTO migra_cnae VALUES ('C.22.23-4/00',4,'Fabricação de tubos e acessórios de material plástico para uso na construção');
INSERT INTO migra_cnae VALUES ('C.22.23-4/00',5,'Fabricação de tubos e acessórios de material plástico para uso na construção');
INSERT INTO migra_cnae VALUES ('C.22.29-3/00',4,'Fabricação de artefatos de material plástico não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.22.29-3/01',5,'Fabricação de artefatos de material plástico para uso pessoal e doméstico');
INSERT INTO migra_cnae VALUES ('C.22.29-3/02',5,'Fabricação de artefatos de material plástico para usos industriais');
INSERT INTO migra_cnae VALUES ('C.22.29-3/03',5,'Fabricação de artefatos de material plástico para uso na construção, exceto tubos e acessórios');
INSERT INTO migra_cnae VALUES ('C.22.29-3/99',5,'Fabricação de artefatos de material plástico para outros usos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.23.00-0/00',2,'FABRICAÇÃO DE PRODUTOS DE MINERAIS NÃO-METÁLICOS');
INSERT INTO migra_cnae VALUES ('C.23.10-0/00',3,'Fabricação de vidro e de produtos do vidro');
INSERT INTO migra_cnae VALUES ('C.23.11-7/00',4,'Fabricação de vidro plano e de segurança');
INSERT INTO migra_cnae VALUES ('C.23.11-7/00',5,'Fabricação de vidro plano e de segurança');
INSERT INTO migra_cnae VALUES ('C.23.12-5/00',4,'Fabricação de embalagens de vidro');
INSERT INTO migra_cnae VALUES ('C.23.12-5/00',5,'Fabricação de embalagens de vidro');
INSERT INTO migra_cnae VALUES ('C.23.19-2/00',4,'Fabricação de artigos de vidro');
INSERT INTO migra_cnae VALUES ('C.23.19-2/00',5,'Fabricação de artigos de vidro');
INSERT INTO migra_cnae VALUES ('C.23.20-0/00',3,'Fabricação de cimento');
INSERT INTO migra_cnae VALUES ('C.23.20-6/00',4,'Fabricação de cimento');
INSERT INTO migra_cnae VALUES ('C.23.20-6/00',5,'Fabricação de cimento');
INSERT INTO migra_cnae VALUES ('C.23.30-0/00',3,'Fabricação de artefatos de concreto, cimento, fibrocimento, gesso e materiais semelhantes');
INSERT INTO migra_cnae VALUES ('C.23.30-3/00',4,'Fabricação de artefatos de concreto, cimento, fibrocimento, gesso e materiais semelhantes');
INSERT INTO migra_cnae VALUES ('C.23.30-3/01',5,'Fabricação de estruturas pré-moldadas de concreto armado, em série e sob encomenda');
INSERT INTO migra_cnae VALUES ('C.23.30-3/02',5,'Fabricação de artefatos de cimento para uso na construção');
INSERT INTO migra_cnae VALUES ('C.23.30-3/03',5,'Fabricação de artefatos de fibrocimento para uso na construção');
INSERT INTO migra_cnae VALUES ('C.23.30-3/04',5,'Fabricação de casas pré-moldadas de concreto');
INSERT INTO migra_cnae VALUES ('C.23.30-3/05',5,'Preparação de massa de concreto e argamassa para construção');
INSERT INTO migra_cnae VALUES ('C.23.30-3/99',5,'Fabricação de outros artefatos e produtos de concreto, cimento, fibrocimento, gesso e materiais semelhantes');
INSERT INTO migra_cnae VALUES ('C.23.40-0/00',3,'Fabricação de produtos cerâmicos');
INSERT INTO migra_cnae VALUES ('C.23.41-9/00',4,'Fabricação de produtos cerâmicos refratários');
INSERT INTO migra_cnae VALUES ('C.23.41-9/00',5,'Fabricação de produtos cerâmicos refratários');
INSERT INTO migra_cnae VALUES ('C.23.42-7/00',4,'Fabricação de produtos cerâmicos não-refratários para uso estrutural na construção');
INSERT INTO migra_cnae VALUES ('C.23.42-7/01',5,'Fabricação de azulejos e pisos');
INSERT INTO migra_cnae VALUES ('C.23.42-7/02',5,'Fabricação de artefatos de cerâmica e barro cozido para uso na construção, exceto azulejos e pisos');
INSERT INTO migra_cnae VALUES ('C.23.49-4/00',4,'Fabricação de produtos cerâmicos não-refratários não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.23.49-4/01',5,'Fabricação de material sanitário de cerâmica');
INSERT INTO migra_cnae VALUES ('C.23.49-4/99',5,'Fabricação de produtos cerâmicos não-refratários não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.23.90-0/00',3,'Aparelhamento de pedras e fabricação de outros produtos de minerais não-metálicos');
INSERT INTO migra_cnae VALUES ('C.23.91-5/00',4,'Aparelhamento e outros trabalhos em pedras');
INSERT INTO migra_cnae VALUES ('C.23.91-5/01',5,'Britamento de pedras, exceto associado à extração');
INSERT INTO migra_cnae VALUES ('C.23.91-5/02',5,'Aparelhamento de pedras para construção, exceto associado à extração');
INSERT INTO migra_cnae VALUES ('C.23.91-5/03',5,'Aparelhamento de placas e execução de trabalhos em mármore, granito, ardósia e outras pedras');
INSERT INTO migra_cnae VALUES ('C.23.92-3/00',4,'Fabricação de cal e gesso');
INSERT INTO migra_cnae VALUES ('C.23.92-3/00',5,'Fabricação de cal e gesso');
INSERT INTO migra_cnae VALUES ('C.23.99-1/00',4,'Fabricação de produtos de minerais não-metálicos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.23.99-1/01',5,'Decoração, lapidação, gravação, vitrificação e outros trabalhos em cerâmica, louça, vidro e cristal');
INSERT INTO migra_cnae VALUES ('C.23.99-1/02',5,'Fabricação de abrasivos');
INSERT INTO migra_cnae VALUES ('C.23.99-1/99',5,'Fabricação de outros produtos de minerais não-metálicos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.24.00-0/00',2,'METALURGIA');
INSERT INTO migra_cnae VALUES ('C.24.10-0/00',3,'Produção de ferro-gusa e de ferroligas');
INSERT INTO migra_cnae VALUES ('C.24.11-3/00',4,'Produção de ferro-gusa');
INSERT INTO migra_cnae VALUES ('C.24.11-3/00',5,'Produção de ferro-gusa');
INSERT INTO migra_cnae VALUES ('C.24.12-1/00',4,'Produção de ferroligas');
INSERT INTO migra_cnae VALUES ('C.24.12-1/00',5,'Produção de ferroligas');
INSERT INTO migra_cnae VALUES ('C.24.20-0/00',3,'Siderurgia');
INSERT INTO migra_cnae VALUES ('C.24.21-1/00',4,'Produção de semi-acabados de aço');
INSERT INTO migra_cnae VALUES ('C.24.21-1/00',5,'Produção de semi-acabados de aço');
INSERT INTO migra_cnae VALUES ('C.24.22-9/00',4,'Produção de laminados planos de aço');
INSERT INTO migra_cnae VALUES ('C.24.22-9/01',5,'Produção de laminados planos de aço ao carbono, revestidos ou não');
INSERT INTO migra_cnae VALUES ('C.24.22-9/02',5,'Produção de laminados planos de aços especiais');
INSERT INTO migra_cnae VALUES ('C.24.23-7/00',4,'Produção de laminados longos de aço');
INSERT INTO migra_cnae VALUES ('C.24.23-7/01',5,'Produção de tubos de aço sem costura');
INSERT INTO migra_cnae VALUES ('C.24.23-7/02',5,'Produção de laminados longos de aço, exceto tubos');
INSERT INTO migra_cnae VALUES ('C.24.24-5/00',4,'Produção de relaminados, trefilados e perfilados de aço');
INSERT INTO migra_cnae VALUES ('C.24.24-5/01',5,'Produção de arames de aço');
INSERT INTO migra_cnae VALUES ('C.24.24-5/02',5,'Produção de relaminados, trefilados e perfilados de aço, exceto arames');
INSERT INTO migra_cnae VALUES ('C.24.30-0/00',3,'Produção de tubos de aço, exceto tubos sem costura');
INSERT INTO migra_cnae VALUES ('C.24.31-8/00',4,'Produção de tubos de aço com costura');
INSERT INTO migra_cnae VALUES ('C.24.31-8/00',5,'Produção de tubos de aço com costura');
INSERT INTO migra_cnae VALUES ('C.24.39-3/00',4,'Produção de outros tubos de ferro e aço');
INSERT INTO migra_cnae VALUES ('C.24.39-3/00',5,'Produção de outros tubos de ferro e aço');
INSERT INTO migra_cnae VALUES ('C.24.40-0/00',3,'Metalurgia dos metais não-ferrosos');
INSERT INTO migra_cnae VALUES ('C.24.41-5/00',4,'Metalurgia do alumínio e suas ligas');
INSERT INTO migra_cnae VALUES ('C.24.41-5/01',5,'Produção de alumínio e suas ligas em formas primárias');
INSERT INTO migra_cnae VALUES ('C.24.41-5/02',5,'Produção de laminados de alumínio');
INSERT INTO migra_cnae VALUES ('C.24.42-3/00',4,'Metalurgia dos metais preciosos');
INSERT INTO migra_cnae VALUES ('C.24.42-3/00',5,'Metalurgia dos metais preciosos');
INSERT INTO migra_cnae VALUES ('C.24.43-1/00',4,'Metalurgia do cobre');
INSERT INTO migra_cnae VALUES ('C.24.43-1/00',5,'Metalurgia do cobre');
INSERT INTO migra_cnae VALUES ('C.24.49-1/00',4,'Metalurgia dos metais não-ferrosos e suas ligas não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.24.49-1/01',5,'Produção de zinco em formas primárias');
INSERT INTO migra_cnae VALUES ('C.24.49-1/02',5,'Produção de laminados de zinco');
INSERT INTO migra_cnae VALUES ('C.24.49-1/03',5,'Produção de ânodos para galvanoplastia');
INSERT INTO migra_cnae VALUES ('C.24.49-1/99',5,'Metalurgia de outros metais não-ferrosos e suas ligas não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.24.50-0/00',3,'Fundição');
INSERT INTO migra_cnae VALUES ('C.24.51-2/00',4,'Fundição de ferro e aço');
INSERT INTO migra_cnae VALUES ('C.24.51-2/00',5,'Fundição de ferro e aço');
INSERT INTO migra_cnae VALUES ('C.24.52-1/00',4,'Fundição de metais não-ferrosos e suas ligas');
INSERT INTO migra_cnae VALUES ('C.24.52-1/00',5,'Fundição de metais não-ferrosos e suas ligas');
INSERT INTO migra_cnae VALUES ('C.25.00-0/00',2,'FABRICAÇÃO DE PRODUTOS DE METAL, EXCETO MÁQUINAS E EQUIPAMENTOS');
INSERT INTO migra_cnae VALUES ('C.25.10-0/00',3,'Fabricação de estruturas metálicas e obras de caldeiraria pesada');
INSERT INTO migra_cnae VALUES ('C.25.11-0/00',4,'Fabricação de estruturas metálicas');
INSERT INTO migra_cnae VALUES ('C.25.11-0/00',5,'Fabricação de estruturas metálicas');
INSERT INTO migra_cnae VALUES ('C.25.12-8/00',4,'Fabricação de esquadrias de metal');
INSERT INTO migra_cnae VALUES ('C.25.12-8/00',5,'Fabricação de esquadrias de metal');
INSERT INTO migra_cnae VALUES ('C.25.13-6/00',4,'Fabricação de obras de caldeiraria pesada');
INSERT INTO migra_cnae VALUES ('C.25.13-6/00',5,'Fabricação de obras de caldeiraria pesada');
INSERT INTO migra_cnae VALUES ('C.25.20-0/00',3,'Fabricação de tanques, reservatórios metálicos e caldeiras');
INSERT INTO migra_cnae VALUES ('C.25.21-7/00',4,'Fabricação de tanques, reservatórios metálicos e caldeiras para aquecimento central');
INSERT INTO migra_cnae VALUES ('C.25.21-7/00',5,'Fabricação de tanques, reservatórios metálicos e caldeiras para aquecimento central');
INSERT INTO migra_cnae VALUES ('C.25.22-5/00',4,'Fabricação de caldeiras geradoras de vapor, exceto para aquecimento central e para veículos');
INSERT INTO migra_cnae VALUES ('C.25.22-5/00',5,'Fabricação de caldeiras geradoras de vapor, exceto para aquecimento central e para veículos');
INSERT INTO migra_cnae VALUES ('C.25.30-0/00',3,'Forjaria, estamparia, metalurgia do pó e serviços de tratamento de metais');
INSERT INTO migra_cnae VALUES ('C.25.31-4/00',4,'Produção de forjados de aço e de metais não-ferrosos e suas ligas');
INSERT INTO migra_cnae VALUES ('C.25.31-4/01',5,'Produção de forjados de aço');
INSERT INTO migra_cnae VALUES ('C.25.31-4/02',5,'Produção de forjados de metais não-ferrosos e suas ligas');
INSERT INTO migra_cnae VALUES ('C.25.32-2/00',4,'Produção de artefatos estampados de metal; metalurgia do pó');
INSERT INTO migra_cnae VALUES ('C.25.32-2/01',5,'Produção de artefatos estampados de metal');
INSERT INTO migra_cnae VALUES ('C.25.32-2/02',5,'Metalurgia do pó');
INSERT INTO migra_cnae VALUES ('C.25.39-0/00',4,'Serviços de usinagem, solda, tratamento e revestimento em metais');
INSERT INTO migra_cnae VALUES ('C.25.39-0/01',5,'Serviços de usinagem, tornearia e solda');
INSERT INTO migra_cnae VALUES ('C.25.39-0/02',5,'Serviços de tratamento e revestimento em metais');
INSERT INTO migra_cnae VALUES ('C.25.40-0/00',3,'Fabricação de artigos de cutelaria, de serralheria e ferramentas');
INSERT INTO migra_cnae VALUES ('C.25.41-1/00',4,'Fabricação de artigos de cutelaria');
INSERT INTO migra_cnae VALUES ('C.25.41-1/00',5,'Fabricação de artigos de cutelaria');
INSERT INTO migra_cnae VALUES ('C.25.42-0/00',4,'Fabricação de artigos de serralheria, exceto esquadrias');
INSERT INTO migra_cnae VALUES ('C.25.42-0/00',5,'Fabricação de artigos de serralheria, exceto esquadrias');
INSERT INTO migra_cnae VALUES ('C.25.43-8/00',4,'Fabricação de ferramentas');
INSERT INTO migra_cnae VALUES ('C.25.43-8/00',5,'Fabricação de ferramentas');
INSERT INTO migra_cnae VALUES ('C.25.50-0/00',3,'Fabricação de equipamento bélico pesado, armas e munições');
INSERT INTO migra_cnae VALUES ('C.25.50-1/00',4,'Fabricação de equipamento bélico pesado, armas de fogo e munições');
INSERT INTO migra_cnae VALUES ('C.25.50-1/01',5,'Fabricação de equipamento bélico pesado, exceto veículos militares de combate');
INSERT INTO migra_cnae VALUES ('C.25.50-1/02',5,'Fabricação de armas de fogo, outras armas e munições');
INSERT INTO migra_cnae VALUES ('C.25.90-0/00',3,'Fabricação de produtos de metal não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.25.91-8/00',4,'Fabricação de embalagens metálicas');
INSERT INTO migra_cnae VALUES ('C.25.91-8/00',5,'Fabricação de embalagens metálicas');
INSERT INTO migra_cnae VALUES ('C.25.92-6/00',4,'Fabricação de produtos de trefilados de metal');
INSERT INTO migra_cnae VALUES ('C.25.92-6/01',5,'Fabricação de produtos de trefilados de metal padronizados');
INSERT INTO migra_cnae VALUES ('C.25.92-6/02',5,'Fabricação de produtos de trefilados de metal, exceto padronizados');
INSERT INTO migra_cnae VALUES ('C.25.93-4/00',4,'Fabricação de artigos de metal para uso doméstico e pessoal');
INSERT INTO migra_cnae VALUES ('C.25.93-4/00',5,'Fabricação de artigos de metal para uso doméstico e pessoal');
INSERT INTO migra_cnae VALUES ('C.25.99-3/00',4,'Fabricação de produtos de metal não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.25.99-3/01',5,'Serviços de confecção de armações metálicas para a construção');
INSERT INTO migra_cnae VALUES ('C.25.99-3/02',5,'Serviço de corte e dobra de metais');
INSERT INTO migra_cnae VALUES ('C.25.99-3/99',5,'Fabricação de outros produtos de metal não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.26.00-0/00',2,'FABRICAÇÃO DE EQUIPAMENTOS DE INFORMÁTICA, PRODUTOS ELETRÔNICOS E ÓPTICOS');
INSERT INTO migra_cnae VALUES ('C.26.10-0/00',3,'Fabricação de componentes eletrônicos');
INSERT INTO migra_cnae VALUES ('C.26.10-8/00',4,'Fabricação de componentes eletrônicos');
INSERT INTO migra_cnae VALUES ('C.26.10-8/00',5,'Fabricação de componentes eletrônicos');
INSERT INTO migra_cnae VALUES ('C.26.20-0/00',3,'Fabricação de equipamentos de informática e periféricos');
INSERT INTO migra_cnae VALUES ('C.26.21-3/00',4,'Fabricação de equipamentos de informática');
INSERT INTO migra_cnae VALUES ('C.26.21-3/00',5,'Fabricação de equipamentos de informática');
INSERT INTO migra_cnae VALUES ('C.26.22-1/00',4,'Fabricação de periféricos para equipamentos de informática');
INSERT INTO migra_cnae VALUES ('C.26.22-1/00',5,'Fabricação de periféricos para equipamentos de informática');
INSERT INTO migra_cnae VALUES ('C.26.30-0/00',3,'Fabricação de equipamentos de comunicação');
INSERT INTO migra_cnae VALUES ('C.26.31-1/00',4,'Fabricação de equipamentos transmissores de comunicação');
INSERT INTO migra_cnae VALUES ('C.26.31-1/00',5,'Fabricação de equipamentos transmissores de comunicação, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.26.32-9/00',4,'Fabricação de aparelhos telefônicos e de outros equipamentos de comunicação');
INSERT INTO migra_cnae VALUES ('C.26.32-9/00',5,'Fabricação de aparelhos telefônicos e de outros equipamentos de comunicação, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.26.40-0/00',3,'Fabricação de aparelhos de recepção, reprodução, gravação e amplificação de áudio e vídeo');
INSERT INTO migra_cnae VALUES ('C.26.40-0/00',4,'Fabricação de aparelhos de recepção, reprodução, gravação e amplificação de áudio e vídeo');
INSERT INTO migra_cnae VALUES ('C.26.40-0/00',5,'Fabricação de aparelhos de recepção, reprodução, gravação e amplificação de áudio e vídeo');
INSERT INTO migra_cnae VALUES ('C.26.50-0/00',4,'Fabricação de aparelhos e instrumentos de medida, teste e controle; cronômetros e relógios');
INSERT INTO migra_cnae VALUES ('C.26.51-5/00',4,'Fabricação de aparelhos e equipamentos de medida, teste e controle');
INSERT INTO migra_cnae VALUES ('C.26.51-5/00',5,'Fabricação de aparelhos e equipamentos de medida, teste e controle');
INSERT INTO migra_cnae VALUES ('C.26.52-3/00',4,'Fabricação de cronômetros e relógios');
INSERT INTO migra_cnae VALUES ('C.26.52-3/00',5,'Fabricação de cronômetros e relógios');
INSERT INTO migra_cnae VALUES ('C.26.60-0/00',3,'Fabricação de aparelhos eletromédicos e eletroterapêuticos e equipamentos de irradiação');
INSERT INTO migra_cnae VALUES ('C.26.60-4/00',4,'Fabricação de aparelhos eletromédicos e eletroterapêuticos e equipamentos de irradiação');
INSERT INTO migra_cnae VALUES ('C.26.60-4/00',5,'Fabricação de aparelhos eletromédicos e eletroterapêuticos e equipamentos de irradiação');
INSERT INTO migra_cnae VALUES ('C.26.70-0/00',3,'Fabricação de equipamentos e instrumentos ópticos, fotográficos e cinematográficos');
INSERT INTO migra_cnae VALUES ('C.26.70-1/00',4,'Fabricação de equipamentos e instrumentos ópticos, fotográficos e cinematográficos');
INSERT INTO migra_cnae VALUES ('C.26.70-1/01',5,'Fabricação de equipamentos e instrumentos ópticos, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.26.70-1/02',5,'Fabricação de aparelhos fotográficos e cinematográficos, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.26.80-0/00',3,'Fabricação de mídias virgens, magnéticas e ópticas');
INSERT INTO migra_cnae VALUES ('C.26.80-9/00',4,'Fabricação de mídias virgens, magnéticas e ópticas');
INSERT INTO migra_cnae VALUES ('C.26.80-9/00',5,'Fabricação de mídias virgens, magnéticas e ópticas');
INSERT INTO migra_cnae VALUES ('C.27.00-0/00',2,'FABRICAÇÃO DE MÁQUINAS, APARELHOS E MATERIAIS ELÉTRICOS');
INSERT INTO migra_cnae VALUES ('C.27.10-0/00',3,'Fabricação de geradores, transformadores e motores elétricos');
INSERT INTO migra_cnae VALUES ('C.27.10-4/00',4,'Fabricação de geradores, transformadores e motores elétricos');
INSERT INTO migra_cnae VALUES ('C.27.10-4/01',5,'Fabricação de geradores de corrente contínua e alternada, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.27.10-4/02',5,'Fabricação de transformadores, indutores, conversores, sincronizadores e semelhantes, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.27.10-4/03',5,'Fabricação de motores elétricos, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.27.20-0/00',3,'Fabricação de pilhas, baterias e acumuladores elétricos');
INSERT INTO migra_cnae VALUES ('C.27.21-0/00',4,'Fabricação de pilhas, baterias e acumuladores elétricos, exceto para veículos automotores');
INSERT INTO migra_cnae VALUES ('C.27.21-0/00',5,'Fabricação de pilhas, baterias e acumuladores elétricos, exceto para veículos automotores');
INSERT INTO migra_cnae VALUES ('C.27.22-8/00',4,'Fabricação de baterias e acumuladores para veículos automotores');
INSERT INTO migra_cnae VALUES ('C.27.22-8/01',5,'Fabricação de baterias e acumuladores para veículos automotores');
INSERT INTO migra_cnae VALUES ('C.27.22-8/02',5,'Recondicionamento de baterias e acumuladores para veículos automotores');
INSERT INTO migra_cnae VALUES ('C.27.30-0/00',3,'Fabricação de equipamentos para distribuição e controle de energia elétrica');
INSERT INTO migra_cnae VALUES ('C.27.31-7/00',4,'Fabricação de aparelhos e equipamentos para distribuição e controle de energia elétrica');
INSERT INTO migra_cnae VALUES ('C.27.31-7/00',5,'Fabricação de aparelhos e equipamentos para distribuição e controle de energia elétrica');
INSERT INTO migra_cnae VALUES ('C.27.32-5/00',4,'Fabricação de material elétrico para instalações em circuito de consumo');
INSERT INTO migra_cnae VALUES ('C.27.32-5/00',5,'Fabricação de material elétrico para instalações em circuito de consumo');
INSERT INTO migra_cnae VALUES ('C.27.33-3/00',4,'Fabricação de fios, cabos e condutores elétricos isolados');
INSERT INTO migra_cnae VALUES ('C.27.33-3/00',5,'Fabricação de fios, cabos e condutores elétricos isolados');
INSERT INTO migra_cnae VALUES ('C.27.40-0/00',3,'Fabricação de lâmpadas e outros equipamentos de iluminação');
INSERT INTO migra_cnae VALUES ('C.27.40-6/00',4,'Fabricação de lâmpadas e outros equipamentos de iluminação');
INSERT INTO migra_cnae VALUES ('C.27.40-6/01',5,'Fabricação de lâmpadas');
INSERT INTO migra_cnae VALUES ('C.27.40-6/02',5,'Fabricação de luminárias e outros equipamentos de iluminação');
INSERT INTO migra_cnae VALUES ('C.27.50-0/00',3,'Fabricação de eletrodomésticos');
INSERT INTO migra_cnae VALUES ('C.27.51-1/00',4,'Fabricação de fogões, refrigeradores e máquinas de lavar e secar para uso doméstico');
INSERT INTO migra_cnae VALUES ('C.27.51-1/00',5,'Fabricação de fogões, refrigeradores e máquinas de lavar e secar para uso doméstico, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.27.59-7/00',4,'Fabricação de aparelhos eletrodomésticos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.27.59-7/01',5,'Fabricação de aparelhos elétricos de uso pessoal, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.27.59-7/99',5,'Fabricação de outros aparelhos eletrodomésticos não especificados anteriormente, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.27.90-0/00',3,'Fabricação de equipamentos e aparelhos elétricos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.27.90-2/00',4,'Fabricação de equipamentos e aparelhos elétricos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.27.90-2/01',5,'Fabricação de eletrodos, contatos e outros artigos de carvão e grafita para uso elétrico, eletroímãs e isoladores');
INSERT INTO migra_cnae VALUES ('C.27.90-2/02',5,'Fabricação de equipamentos para sinalização e alarme');
INSERT INTO migra_cnae VALUES ('C.27.90-2/99',5,'Fabricação de outros equipamentos e aparelhos elétricos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.28.00-0/00',2,'FABRICAÇÃO DE MÁQUINAS E EQUIPAMENTOS');
INSERT INTO migra_cnae VALUES ('C.28.10-0/00',3,'Fabricação de motores, bombas, compressores e equipamentos de transmissão');
INSERT INTO migra_cnae VALUES ('C.28.11-9/00',4,'Fabricação de motores e turbinas, exceto para aviões e veículos rodoviários');
INSERT INTO migra_cnae VALUES ('C.28.11-9/00',5,'Fabricação de motores e turbinas, peças e acessórios, exceto para aviões e veículos rodoviários');
INSERT INTO migra_cnae VALUES ('C.28.12-7/00',4,'Fabricação de equipamentos hidráulicos e pneumáticos, exceto válvulas');
INSERT INTO migra_cnae VALUES ('C.28.12-7/00',5,'Fabricação de equipamentos hidráulicos e pneumáticos, peças e acessórios, exceto válvulas');
INSERT INTO migra_cnae VALUES ('C.28.13-5/00',4,'Fabricação de válvulas, registros e dispositivos semelhantes');
INSERT INTO migra_cnae VALUES ('C.28.13-5/00',5,'Fabricação de válvulas, registros e dispositivos semelhantes, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.14-3/00',4,'Fabricação de compressores');
INSERT INTO migra_cnae VALUES ('C.28.14-3/01',5,'Fabricação de compressores para uso industrial, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.14-3/02',5,'Fabricação de compressores para uso não-industrial, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.15-1/00',4,'Fabricação de equipamentos de transmissão para fins industriais');
INSERT INTO migra_cnae VALUES ('C.28.15-1/01',5,'Fabricação de rolamentos para fins industriais');
INSERT INTO migra_cnae VALUES ('C.28.15-1/02',5,'Fabricação de equipamentos de transmissão para fins industriais, exceto rolamentos');
INSERT INTO migra_cnae VALUES ('C.28.20-0/00',3,'Fabricação de máquinas e equipamentos de uso geral');
INSERT INTO migra_cnae VALUES ('C.28.21-6/00',4,'Fabricação de aparelhos e equipamentos para instalações térmicas');
INSERT INTO migra_cnae VALUES ('C.28.21-6/01',5,'Fabricação de fornos industriais, aparelhos e equipamentos não-elétricos para instalações térmicas, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.21-6/02',5,'Fabricação de estufas e fornos elétricos para fins industriais, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.22-4/00',4,'Fabricação de máquinas, equipamentos e aparelhos para transporte e elevação de cargas e pessoas');
INSERT INTO migra_cnae VALUES ('C.28.22-4/01',5,'Fabricação de máquinas, equipamentos e aparelhos para transporte e elevação de pessoas, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.22-4/02',5,'Fabricação de máquinas, equipamentos e aparelhos para transporte e elevação de cargas, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.23-2/00',4,'Fabricação de máquinas e aparelhos de refrigeração e ventilação para uso industrial e comercial');
INSERT INTO migra_cnae VALUES ('C.28.23-2/00',5,'Fabricação de máquinas e aparelhos de refrigeração e ventilação para uso industrial e comercial, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.24-1/00',4,'Fabricação de aparelhos e equipamentos de ar condicionado');
INSERT INTO migra_cnae VALUES ('C.28.24-1/01',5,'Fabricação de aparelhos e equipamentos de ar condicionado para uso industrial');
INSERT INTO migra_cnae VALUES ('C.28.24-1/02',5,'Fabricação de aparelhos e equipamentos de ar condicionado para uso não-industrial');
INSERT INTO migra_cnae VALUES ('C.28.25-9/00',4,'Fabricação de máquinas e equipamentos para saneamento básico e ambiental');
INSERT INTO migra_cnae VALUES ('C.28.25-9/00',5,'Fabricação de máquinas e equipamentos para saneamento básico e ambiental, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.29-1/00',4,'Fabricação de máquinas e equipamentos de uso geral não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.28.29-1/01',5,'Fabricação de máquinas de escrever, calcular e outros equipamentos não-eletrônicos para escritório, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.29-1/99',5,'Fabricação de outras máquinas e equipamentos de uso geral não especificados anteriormente, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.30-0/00',3,'Fabricação de tratores e de máquinas e equipamentos para a agricultura e pecuária');
INSERT INTO migra_cnae VALUES ('C.28.31-3/00',4,'Fabricação de tratores agrícolas');
INSERT INTO migra_cnae VALUES ('C.28.31-3/00',5,'Fabricação de tratores agrícolas, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.32-1/00',4,'Fabricação de equipamentos para irrigação agrícola');
INSERT INTO migra_cnae VALUES ('C.28.32-1/00',5,'Fabricação de equipamentos para irrigação agrícola, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.33-0/00',4,'Fabricação de máquinas e equipamentos para a agricultura e pecuária, exceto para irrigação');
INSERT INTO migra_cnae VALUES ('C.28.33-0/00',5,'Fabricação de máquinas e equipamentos para a agricultura e pecuária, peças e acessórios, exceto para irrigação');
INSERT INTO migra_cnae VALUES ('C.28.40-0/00',3,'Fabricação de máquinas-ferramenta');
INSERT INTO migra_cnae VALUES ('C.28.40-2/00',4,'Fabricação de máquinas-ferramenta');
INSERT INTO migra_cnae VALUES ('C.28.40-2/00',5,'Fabricação de máquinas-ferramenta, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.50-0/00',3,'Fabricação de máquinas e equipamentos de uso na extração mineral e na construção');
INSERT INTO migra_cnae VALUES ('C.28.51-8/00',4,'Fabricação de máquinas e equipamentos para a prospecção e extração de petróleo');
INSERT INTO migra_cnae VALUES ('C.28.51-8/00',5,'Fabricação de máquinas e equipamentos para a prospecção e extração de petróleo, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.52-6/00',4,'Fabricação de outras máquinas e equipamentos para uso na extração mineral, exceto na extração de petróleo');
INSERT INTO migra_cnae VALUES ('C.28.52-6/00',5,'Fabricação de outras máquinas e equipamentos para uso na extração mineral, peças e acessórios, exceto na extração de petróleo');
INSERT INTO migra_cnae VALUES ('C.28.53-4/00',4,'Fabricação de tratores, exceto agrícolas');
INSERT INTO migra_cnae VALUES ('C.28.53-4/00',5,'Fabricação de tratores, peças e acessórios, exceto agrícolas');
INSERT INTO migra_cnae VALUES ('C.28.54-2/00',4,'Fabricação de máquinas e equipamentos para terraplenagem, pavimentação e construção, exceto tratores');
INSERT INTO migra_cnae VALUES ('C.28.54-2/00',5,'Fabricação de máquinas e equipamentos para terraplenagem, pavimentação e construção, peças e acessórios, exceto tratores');
INSERT INTO migra_cnae VALUES ('C.28.60-0/00',3,'Fabricação de máquinas e equipamentos de uso industrial específico');
INSERT INTO migra_cnae VALUES ('C.28.61-5/00',4,'Fabricação de máquinas para a indústria metalúrgica, exceto máquinas-ferramenta');
INSERT INTO migra_cnae VALUES ('C.28.61-5/00',5,'Fabricação de máquinas para a indústria metalúrgica, peças e acessórios, exceto máquinas-ferramenta');
INSERT INTO migra_cnae VALUES ('C.28.62-3/00',4,'Fabricação de máquinas e equipamentos para as indústrias de alimentos, bebidas e fumo');
INSERT INTO migra_cnae VALUES ('C.28.62-3/00',5,'Fabricação de máquinas e equipamentos para as indústrias de alimentos, bebidas e fumo, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.63-1/00',4,'Fabricação de máquinas e equipamentos para a indústria têxtil');
INSERT INTO migra_cnae VALUES ('C.28.63-1/00',5,'Fabricação de máquinas e equipamentos para a indústria têxtil, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.64-0/00',4,'Fabricação de máquinas e equipamentos para as indústrias do vestuário, do couro e de calçados');
INSERT INTO migra_cnae VALUES ('C.28.64-0/00',5,'Fabricação de máquinas e equipamentos para as indústrias do vestuário, do couro e de calçados, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.65-8/00',4,'Fabricação de máquinas e equipamentos para as indústrias de celulose, papel e papelão e artefatos');
INSERT INTO migra_cnae VALUES ('C.28.65-8/00',5,'Fabricação de máquinas e equipamentos para as indústrias de celulose, papel e papelão e artefatos, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.66-6/00',4,'Fabricação de máquinas e equipamentos para a indústria do plástico');
INSERT INTO migra_cnae VALUES ('C.28.66-6/00',5,'Fabricação de máquinas e equipamentos para a indústria do plástico, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.28.69-1/00',4,'Fabricação de máquinas e equipamentos para uso industrial específico não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.28.69-1/00',5,'Fabricação de máquinas e equipamentos para uso industrial específico não especificados anteriormente, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.29.00-0/00',2,'FABRICAÇÃO DE VEÍCULOS AUTOMOTORES, REBOQUES E CARROCERIAS');
INSERT INTO migra_cnae VALUES ('C.29.10-0/00',3,'Fabricação de automóveis, camionetas e utilitários');
INSERT INTO migra_cnae VALUES ('C.29.10-7/00',4,'Fabricação de automóveis, camionetas e utilitários');
INSERT INTO migra_cnae VALUES ('C.29.10-7/01',5,'Fabricação de automóveis, camionetas e utilitários');
INSERT INTO migra_cnae VALUES ('C.29.10-7/02',5,'Fabricação de chassis com motor para automóveis, camionetas e utilitários');
INSERT INTO migra_cnae VALUES ('C.29.10-7/03',5,'Fabricação de motores para automóveis, camionetas e utilitários');
INSERT INTO migra_cnae VALUES ('C.29.20-0/00',3,'Fabricação de caminhões e ônibus');
INSERT INTO migra_cnae VALUES ('C.29.20-4/00',4,'Fabricação de caminhões e ônibus');
INSERT INTO migra_cnae VALUES ('C.29.20-4/01',5,'Fabricação de caminhões e ônibus');
INSERT INTO migra_cnae VALUES ('C.29.20-4/02',5,'Fabricação de motores para caminhões e ônibus');
INSERT INTO migra_cnae VALUES ('C.29.30-0/00',3,'Fabricação de cabines, carrocerias e reboques para veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.30-1/00',4,'Fabricação de cabines, carrocerias e reboques para veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.30-1/01',5,'Fabricação de cabines, carrocerias e reboques para caminhões');
INSERT INTO migra_cnae VALUES ('C.29.30-1/02',5,'Fabricação de carrocerias para ônibus');
INSERT INTO migra_cnae VALUES ('C.29.30-1/03',5,'Fabricação de cabines, carrocerias e reboques para outros veículos automotores, exceto caminhões e ônibus');
INSERT INTO migra_cnae VALUES ('C.29.40-0/00',3,'Fabricação de peças e acessórios para veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.41-7/00',4,'Fabricação de peças e acessórios para o sistema motor de veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.41-7/00',5,'Fabricação de peças e acessórios para o sistema motor de veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.42-5/00',4,'Fabricação de peças e acessórios para os sistemas de marcha e transmissão de veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.42-5/00',5,'Fabricação de peças e acessórios para os sistemas de marcha e transmissão de veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.43-3/00',4,'Fabricação de peças e acessórios para o sistema de freios de veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.43-3/00',5,'Fabricação de peças e acessórios para o sistema de freios de veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.44-1/00',4,'Fabricação de peças e acessórios para o sistema de direção e suspensão de veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.44-1/00',5,'Fabricação de peças e acessórios para o sistema de direção e suspensão de veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.45-0/00',4,'Fabricação de material elétrico e eletrônico para veículos automotores, exceto baterias');
INSERT INTO migra_cnae VALUES ('C.29.45-0/00',5,'Fabricação de material elétrico e eletrônico para veículos automotores, exceto baterias');
INSERT INTO migra_cnae VALUES ('C.29.49-2/00',4,'Fabricação de peças e acessórios para veículos automotores não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.29.49-2/01',5,'Fabricação de bancos e estofados para veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.49-2/99',5,'Fabricação de outras peças e acessórios para veículos automotores não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('C.29.50-0/00',3,'Recondicionamento e recuperação de motores para veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.50-6/00',4,'Recondicionamento e recuperação de motores para veículos automotores');
INSERT INTO migra_cnae VALUES ('C.29.50-6/00',5,'Recondicionamento e recuperação de motores para veículos automotores');
INSERT INTO migra_cnae VALUES ('C.30.00-0/00',2,'FABRICAÇÃO DE OUTROS EQUIPAMENTOS DE TRANSPORTE, EXCETO VEÍCULOS AUTOMOTORES');
INSERT INTO migra_cnae VALUES ('C.30.10-0/00',3,'Construção de embarcações');
INSERT INTO migra_cnae VALUES ('C.30.11-3/00',4,'Construção de embarcações e estruturas flutuantes');
INSERT INTO migra_cnae VALUES ('C.30.11-3/01',5,'Construção de embarcações de grande porte');
INSERT INTO migra_cnae VALUES ('C.30.11-3/02',5,'Construção de embarcações para uso comercial e para usos especiais, exceto de grande porte');
INSERT INTO migra_cnae VALUES ('C.30.12-1/00',4,'Construção de embarcações para esporte e lazer');
INSERT INTO migra_cnae VALUES ('C.30.12-1/00',5,'Construção de embarcações para esporte e lazer');
INSERT INTO migra_cnae VALUES ('C.30.30-0/00',3,'Fabricação de veículos ferroviários');
INSERT INTO migra_cnae VALUES ('C.30.31-8/00',4,'Fabricação de locomotivas, vagões e outros materiais rodantes');
INSERT INTO migra_cnae VALUES ('C.30.31-8/00',5,'Fabricação de locomotivas, vagões e outros materiais rodantes');
INSERT INTO migra_cnae VALUES ('C.30.32-6/00',4,'Fabricação de peças e acessórios para veículos ferroviários');
INSERT INTO migra_cnae VALUES ('C.30.32-6/00',5,'Fabricação de peças e acessórios para veículos ferroviários');
INSERT INTO migra_cnae VALUES ('C.30.40-0/00',3,'Fabricação de aeronaves');
INSERT INTO migra_cnae VALUES ('C.30.41-5/00',4,'Fabricação de aeronaves');
INSERT INTO migra_cnae VALUES ('C.30.41-5/00',5,'Fabricação de aeronaves');
INSERT INTO migra_cnae VALUES ('C.30.42-3/00',4,'Fabricação de turbinas, motores e outros componentes e peças para aeronaves');
INSERT INTO migra_cnae VALUES ('C.30.42-3/00',5,'Fabricação de turbinas, motores e outros componentes e peças para aeronaves');
INSERT INTO migra_cnae VALUES ('C.30.50-0/00',3,'Fabricação de veículos militares de combate');
INSERT INTO migra_cnae VALUES ('C.30.50-4/00',4,'Fabricação de veículos militares de combate');
INSERT INTO migra_cnae VALUES ('C.30.50-4/00',5,'Fabricação de veículos militares de combate');
INSERT INTO migra_cnae VALUES ('C.30.90-0/00',3,'Fabricação de equipamentos de transporte não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.30.91-1/00',4,'Fabricação de motocicletas');
INSERT INTO migra_cnae VALUES ('C.30.91-1/01',5,'Fabricação de motocicletas');
INSERT INTO migra_cnae VALUES ('C.30.91-1/02',5,'Fabricação de peças e acessórios para motocicletas');
INSERT INTO migra_cnae VALUES ('C.30.92-0/00',4,'Fabricação de bicicletas e triciclos não-motorizados');
INSERT INTO migra_cnae VALUES ('C.30.92-0/00',5,'Fabricação de bicicletas e triciclos não-motorizados, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.30.99-7/00',4,'Fabricação de equipamentos de transporte não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.30.99-7/00',5,'Fabricação de equipamentos de transporte não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.31.00-0/00',2,'FABRICAÇÃO DE MÓVEIS');
INSERT INTO migra_cnae VALUES ('C.31.00-0/00',3,'Fabricação de móveis');
INSERT INTO migra_cnae VALUES ('C.31.01-2/00',4,'Fabricação de móveis com predominância de madeira');
INSERT INTO migra_cnae VALUES ('C.31.01-2/00',5,'Fabricação de móveis com predominância de madeira');
INSERT INTO migra_cnae VALUES ('C.31.02-1/00',4,'Fabricação de móveis com predominância de metal');
INSERT INTO migra_cnae VALUES ('C.31.02-1/00',5,'Fabricação de móveis com predominância de metal');
INSERT INTO migra_cnae VALUES ('C.31.03-9/00',4,'Fabricação de móveis de outros materiais, exceto madeira e metal');
INSERT INTO migra_cnae VALUES ('C.31.03-9/00',5,'Fabricação de móveis de outros materiais, exceto madeira e metal');
INSERT INTO migra_cnae VALUES ('C.31.04-7/00',4,'Fabricação de colchões');
INSERT INTO migra_cnae VALUES ('C.31.04-7/00',5,'Fabricação de colchões');
INSERT INTO migra_cnae VALUES ('C.32.00-0/00',2,'FABRICAÇÃO DE PRODUTOS DIVERSOS');
INSERT INTO migra_cnae VALUES ('C.32.10-0/00',3,'Fabricação de artigos de joalheria, bijuteria e semelhantes');
INSERT INTO migra_cnae VALUES ('C.32.11-6/00',4,'Lapidação de gemas e fabricação de artefatos de ourivesaria e joalheria');
INSERT INTO migra_cnae VALUES ('C.32.11-6/01',5,'Lapidação de gemas');
INSERT INTO migra_cnae VALUES ('C.32.11-6/02',5,'Fabricação de artefatos de joalheria e ourivesaria');
INSERT INTO migra_cnae VALUES ('C.32.11-6/03',5,'Cunhagem de moedas e medalhas');
INSERT INTO migra_cnae VALUES ('C.32.12-4/00',4,'Fabricação de bijuterias e artefatos semelhantes');
INSERT INTO migra_cnae VALUES ('C.32.12-4/00',5,'Fabricação de bijuterias e artefatos semelhantes');
INSERT INTO migra_cnae VALUES ('C.32.20-0/00',3,'Fabricação de instrumentos musicais');
INSERT INTO migra_cnae VALUES ('C.32.20-5/00',4,'Fabricação de instrumentos musicais');
INSERT INTO migra_cnae VALUES ('C.32.20-5/00',5,'Fabricação de instrumentos musicais, peças e acessórios');
INSERT INTO migra_cnae VALUES ('C.32.30-0/00',3,'Fabricação de artefatos para pesca e esporte');
INSERT INTO migra_cnae VALUES ('C.32.30-2/00',4,'Fabricação de artefatos para pesca e esporte');
INSERT INTO migra_cnae VALUES ('C.32.30-2/00',5,'Fabricação de artefatos para pesca e esporte');
INSERT INTO migra_cnae VALUES ('C.32.40-0/00',3,'Fabricação de brinquedos e jogos recreativos');
INSERT INTO migra_cnae VALUES ('C.32.40-0/00',4,'Fabricação de brinquedos e jogos recreativos');
INSERT INTO migra_cnae VALUES ('C.32.40-0/01',5,'Fabricação de jogos eletrônicos');
INSERT INTO migra_cnae VALUES ('C.32.40-0/02',5,'Fabricação de mesas de bilhar, de sinuca e acessórios não associada à locação');
INSERT INTO migra_cnae VALUES ('C.32.40-0/03',5,'Fabricação de mesas de bilhar, de sinuca e acessórios associada à locação');
INSERT INTO migra_cnae VALUES ('C.32.40-0/99',5,'Fabricação de outros brinquedos e jogos recreativos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.32.50-0/00',3,'Fabricação de instrumentos e materiais para uso médico e odontológico e de artigos ópticos');
INSERT INTO migra_cnae VALUES ('C.32.50-7/00',4,'Fabricação de instrumentos e materiais para uso médico e odontológico e de artigos ópticos');
INSERT INTO migra_cnae VALUES ('C.32.50-7/01',5,'Fabricação de instrumentos não-eletrônicos e utensílios para uso médico, cirúrgico, odontológico e de laboratório');
INSERT INTO migra_cnae VALUES ('C.32.50-7/02',5,'Fabricação de mobiliário para uso médico, cirúrgico, odontológico e de laboratório');
INSERT INTO migra_cnae VALUES ('C.32.50-7/03',5,'Fabricação de aparelhos e utensílios para correção de defeitos físicos e aparelhos ortopédicos em geral sob encomenda');
INSERT INTO migra_cnae VALUES ('C.32.50-7/04',5,'Fabricação de aparelhos e utensílios para correção de defeitos físicos e aparelhos ortopédicos em geral, exceto sob encomenda');
INSERT INTO migra_cnae VALUES ('C.32.50-7/05',5,'Fabricação de materiais para medicina e odontologia');
INSERT INTO migra_cnae VALUES ('C.32.50-7/06',5,'Serviços de prótese dentária');
INSERT INTO migra_cnae VALUES ('C.32.50-7/07',5,'Fabricação de artigos ópticos');
INSERT INTO migra_cnae VALUES ('C.32.50-7/09',5,'Serviço de laboratório óptico');
INSERT INTO migra_cnae VALUES ('C.32.90-0/00',3,'Fabricação de produtos diversos');
INSERT INTO migra_cnae VALUES ('C.32.91-4/00',4,'Fabricação de escovas, pincéis e vassouras');
INSERT INTO migra_cnae VALUES ('C.32.91-4/00',5,'Fabricação de escovas, pincéis e vassouras');
INSERT INTO migra_cnae VALUES ('C.32.92-2/00',4,'Fabricação de equipamentos e acessórios para segurança e proteção pessoal e profissional');
INSERT INTO migra_cnae VALUES ('C.32.92-2/01',5,'Fabricação de roupas de proteção e segurança e resistentes a fogo');
INSERT INTO migra_cnae VALUES ('C.32.92-2/02',5,'Fabricação de equipamentos e acessórios para segurança pessoal e profissional');
INSERT INTO migra_cnae VALUES ('C.32.99-0/00',4,'Fabricação de produtos diversos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.32.99-0/01',5,'Fabricação de guarda-chuvas e similares');
INSERT INTO migra_cnae VALUES ('C.32.99-0/02',5,'Fabricação de canetas, lápis e outros artigos para escritório');
INSERT INTO migra_cnae VALUES ('C.32.99-0/03',5,'Fabricação de letras, letreiros e placas de qualquer material, exceto luminosos');
INSERT INTO migra_cnae VALUES ('C.32.99-0/04',5,'Fabricação de painéis e letreiros luminosos');
INSERT INTO migra_cnae VALUES ('C.32.99-0/05',5,'Fabricação de aviamentos para costura');
INSERT INTO migra_cnae VALUES ('C.32.99-0/06',5,'Fabricação de velas, inclusive decorativas');
INSERT INTO migra_cnae VALUES ('C.32.99-0/99',5,'Fabricação de produtos diversos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.33.00-0/00',2,'MANUTENÇÃO, REPARAÇÃO E INSTALAÇÃO DE MÁQUINAS E EQUIPAMENTOS');
INSERT INTO migra_cnae VALUES ('C.33.10-0/00',3,'Manutenção e reparação de máquinas e equipamentos');
INSERT INTO migra_cnae VALUES ('C.33.11-2/00',4,'Manutenção e reparação de tanques, reservatórios metálicos e caldeiras, exceto para veículos');
INSERT INTO migra_cnae VALUES ('C.33.11-2/00',5,'Manutenção e reparação de tanques, reservatórios metálicos e caldeiras, exceto para veículos');
INSERT INTO migra_cnae VALUES ('C.33.12-1/00',4,'Manutenção e reparação de equipamentos eletrônicos e ópticos');
INSERT INTO migra_cnae VALUES ('C.33.12-1/02',5,'Manutenção e reparação de aparelhos e instrumentos de medida, teste e controle');
INSERT INTO migra_cnae VALUES ('C.33.12-1/03',5,'Manutenção e reparação de aparelhos eletromédicos e eletroterapêuticos e equipamentos de irradiação');
INSERT INTO migra_cnae VALUES ('C.33.12-1/04',5,'Manutenção e reparação de equipamentos e instrumentos ópticos');
INSERT INTO migra_cnae VALUES ('C.33.13-9/00',4,'Manutenção e reparação de máquinas e equipamentos elétricos');
INSERT INTO migra_cnae VALUES ('C.33.13-9/01',5,'Manutenção e reparação de geradores, transformadores e motores elétricos');
INSERT INTO migra_cnae VALUES ('C.33.13-9/02',5,'Manutenção e reparação de baterias e acumuladores elétricos, exceto para veículos');
INSERT INTO migra_cnae VALUES ('C.33.13-9/99',5,'Manutenção e reparação de máquinas, aparelhos e materiais elétricos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.33.14-7/00',4,'Manutenção e reparação de máquinas e equipamentos da indústria mecânica');
INSERT INTO migra_cnae VALUES ('C.33.14-7/01',5,'Manutenção e reparação de máquinas motrizes não-elétricas');
INSERT INTO migra_cnae VALUES ('C.33.14-7/02',5,'Manutenção e reparação de equipamentos hidráulicos e pneumáticos, exceto válvulas');
INSERT INTO migra_cnae VALUES ('C.33.14-7/03',5,'Manutenção e reparação de válvulas industriais');
INSERT INTO migra_cnae VALUES ('C.33.14-7/04',5,'Manutenção e reparação de compressores');
INSERT INTO migra_cnae VALUES ('C.33.14-7/05',5,'Manutenção e reparação de equipamentos de transmissão para fins industriais');
INSERT INTO migra_cnae VALUES ('C.33.14-7/06',5,'Manutenção e reparação de máquinas, aparelhos e equipamentos para instalações térmicas');
INSERT INTO migra_cnae VALUES ('C.33.14-7/07',5,'Manutenção e reparação de máquinas e aparelhos de refrigeração e ventilação para uso industrial e comercial');
INSERT INTO migra_cnae VALUES ('C.33.14-7/08',5,'Manutenção e reparação de máquinas, equipamentos e aparelhos para transporte e elevação de cargas');
INSERT INTO migra_cnae VALUES ('C.33.14-7/09',5,'Manutenção e reparação de máquinas de escrever, calcular e de outros equipamentos não-eletrônicos para escritório');
INSERT INTO migra_cnae VALUES ('C.33.14-7/10',5,'Manutenção e reparação de máquinas e equipamentos para uso geral não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.33.14-7/11',5,'Manutenção e reparação de máquinas e equipamentos para agricultura e pecuária');
INSERT INTO migra_cnae VALUES ('C.33.14-7/12',5,'Manutenção e reparação de tratores agrícolas');
INSERT INTO migra_cnae VALUES ('C.33.14-7/13',5,'Manutenção e reparação de máquinas-ferramenta');
INSERT INTO migra_cnae VALUES ('C.33.14-7/14',5,'Manutenção e reparação de máquinas e equipamentos para a prospecção e extração de petróleo');
INSERT INTO migra_cnae VALUES ('C.33.14-7/15',5,'Manutenção e reparação de máquinas e equipamentos para uso na extração mineral, exceto na extração de petróleo');
INSERT INTO migra_cnae VALUES ('C.33.14-7/16',5,'Manutenção e reparação de tratores, exceto agrícolas');
INSERT INTO migra_cnae VALUES ('C.33.14-7/17',5,'Manutenção e reparação de máquinas e equipamentos de terraplenagem, pavimentação e construção, exceto tratores');
INSERT INTO migra_cnae VALUES ('C.33.14-7/18',5,'Manutenção e reparação de máquinas para a indústria metalúrgica, exceto máquinas-ferramenta');
INSERT INTO migra_cnae VALUES ('C.33.14-7/19',5,'Manutenção e reparação de máquinas e equipamentos para as indústrias de alimentos, bebidas e fumo');
INSERT INTO migra_cnae VALUES ('C.33.14-7/20',5,'Manutenção e reparação de máquinas e equipamentos para a indústria têxtil, do vestuário, do couro e calçados');
INSERT INTO migra_cnae VALUES ('C.33.14-7/21',5,'Manutenção e reparação de máquinas e aparelhos para a indústria de celulose, papel e papelão e artefatos');
INSERT INTO migra_cnae VALUES ('C.33.14-7/22',5,'Manutenção e reparação de máquinas e aparelhos para a indústria do plástico');
INSERT INTO migra_cnae VALUES ('C.33.14-7/99',5,'Manutenção e reparação de outras máquinas e equipamentos para usos industriais não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.33.15-5/00',4,'Manutenção e reparação de veículos ferroviários');
INSERT INTO migra_cnae VALUES ('C.33.15-5/00',5,'Manutenção e reparação de veículos ferroviários');
INSERT INTO migra_cnae VALUES ('C.33.16-3/00',4,'Manutenção e reparação de aeronaves');
INSERT INTO migra_cnae VALUES ('C.33.16-3/01',5,'Manutenção e reparação de aeronaves, exceto a manutenção na pista');
INSERT INTO migra_cnae VALUES ('C.33.16-3/02',5,'Manutenção de aeronaves na pista');
INSERT INTO migra_cnae VALUES ('C.33.17-1/00',4,'Manutenção e reparação de embarcações');
INSERT INTO migra_cnae VALUES ('C.33.17-1/01',5,'Manutenção e reparação de embarcações e estruturas flutuantes');
INSERT INTO migra_cnae VALUES ('C.33.17-1/02',5,'Manutenção e reparação de embarcações para esporte e lazer');
INSERT INTO migra_cnae VALUES ('C.33.19-8/00',4,'Manutenção e reparação de equipamentos e produtos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.33.19-8/00',5,'Manutenção e reparação de equipamentos e produtos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.33.20-0/00',3,'Instalação de máquinas e equipamentos');
INSERT INTO migra_cnae VALUES ('C.33.21-0/00',4,'Instalação de máquinas e equipamentos industriais');
INSERT INTO migra_cnae VALUES ('C.33.21-0/00',5,'Instalação de máquinas e equipamentos industriais');
INSERT INTO migra_cnae VALUES ('C.33.29-5/00',4,'Instalação de equipamentos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('C.33.29-5/01',5,'Serviços de montagem de móveis de qualquer material');
INSERT INTO migra_cnae VALUES ('C.33.29-5/99',5,'Instalação de outros equipamentos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('D.00.00-0/00',1,'ELETRICIDADE E GÁS');
INSERT INTO migra_cnae VALUES ('D.35.00-0/00',2,'ELETRICIDADE, GÁS E OUTRAS UTILIDADES');
INSERT INTO migra_cnae VALUES ('D.35.10-0/00',3,'Geração, transmissão e distribuição de energia elétrica');
INSERT INTO migra_cnae VALUES ('D.35.11-5/00',4,'Geração de energia elétrica');
INSERT INTO migra_cnae VALUES ('D.35.11-5/01',5,'Geração de energia elétrica');
INSERT INTO migra_cnae VALUES ('D.35.11-5/02',5,'Atividades de coordenação e controle da operação da geração e transmissão de energia elétrica');
INSERT INTO migra_cnae VALUES ('D.35.12-3/00',4,'Transmissão de energia elétrica');
INSERT INTO migra_cnae VALUES ('D.35.12-3/00',5,'Transmissão de energia elétrica');
INSERT INTO migra_cnae VALUES ('D.35.13-1/00',4,'Comércio atacadista de energia elétrica');
INSERT INTO migra_cnae VALUES ('D.35.13-1/00',5,'Comércio atacadista de energia elétrica');
INSERT INTO migra_cnae VALUES ('D.35.14-0/00',4,'Distribuição de energia elétrica');
INSERT INTO migra_cnae VALUES ('D.35.14-0/00',5,'Distribuição de energia elétrica');
INSERT INTO migra_cnae VALUES ('D.35.20-0/00',3,'Produção e distribuição de combustíveis gasosos por redes urbanas');
INSERT INTO migra_cnae VALUES ('D.35.20-4/00',4,'Produção de gás; processamento de gás natural; distribuição de combustíveis gasosos por redes urbanas');
INSERT INTO migra_cnae VALUES ('D.35.20-4/01',5,'Produção de gás; processamento de gás natural');
INSERT INTO migra_cnae VALUES ('D.35.20-4/02',5,'Distribuição de combustíveis gasosos por redes urbanas');
INSERT INTO migra_cnae VALUES ('D.35.30-0/00',3,'Produção e distribuição de vapor, água quente e ar condicionado');
INSERT INTO migra_cnae VALUES ('D.35.30-1/00',4,'Produção e distribuição de vapor, água quente e ar condicionado');
INSERT INTO migra_cnae VALUES ('D.35.30-1/00',5,'Produção e distribuição de vapor, água quente e ar condicionado');
INSERT INTO migra_cnae VALUES ('E.00.00-0/00',1,'ÁGUA, ESGOTO, ATIVIDADES DE GESTÃO DE RESÍDUOS E DESCONTAMINAÇÃO');
INSERT INTO migra_cnae VALUES ('E.36.00-0/00',2,'CAPTAÇÃO, TRATAMENTO E DISTRIBUIÇÃO DE ÁGUA');
INSERT INTO migra_cnae VALUES ('E.36.00-0/00',3,'Captação, tratamento e distribuição de água');
INSERT INTO migra_cnae VALUES ('E.36.00-6/00',4,'Captação, tratamento e distribuição de água');
INSERT INTO migra_cnae VALUES ('E.36.00-6/01',5,'Captação, tratamento e distribuição de água');
INSERT INTO migra_cnae VALUES ('E.36.00-6/02',5,'Distribuição de água por caminhões');
INSERT INTO migra_cnae VALUES ('E.37.00-0/00',2,'ESGOTO E ATIVIDADES RELACIONADAS');
INSERT INTO migra_cnae VALUES ('E.37.00-0/00',3,'Esgoto e atividades relacionadas');
INSERT INTO migra_cnae VALUES ('E.37.01-1/00',4,'Gestão de redes de esgoto');
INSERT INTO migra_cnae VALUES ('E.37.01-1/00',5,'Gestão de redes de esgoto');
INSERT INTO migra_cnae VALUES ('E.37.02-9/00',4,'Atividades relacionadas a esgoto, exceto a gestão de redes');
INSERT INTO migra_cnae VALUES ('E.37.02-9/00',5,'Atividades relacionadas a esgoto, exceto a gestão de redes');
INSERT INTO migra_cnae VALUES ('E.38.00-0/00',2,'COLETA, TRATAMENTO E DISPOSIÇÃO DE RESÍDUOS; RECUPERAÇÃO DE MATERIAIS');
INSERT INTO migra_cnae VALUES ('E.38.10-0/00',3,'Coleta de resíduos');
INSERT INTO migra_cnae VALUES ('E.38.11-4/00',4,'Coleta de resíduos não-perigosos');
INSERT INTO migra_cnae VALUES ('E.38.11-4/00',5,'Coleta de resíduos não-perigosos');
INSERT INTO migra_cnae VALUES ('E.38.12-2/00',4,'Coleta de resíduos perigosos');
INSERT INTO migra_cnae VALUES ('E.38.12-2/00',5,'Coleta de resíduos perigosos');
INSERT INTO migra_cnae VALUES ('E.38.20-0/00',3,'Tratamento e disposição de resíduos');
INSERT INTO migra_cnae VALUES ('E.38.21-1/00',4,'Tratamento e disposição de resíduos não-perigosos');
INSERT INTO migra_cnae VALUES ('E.38.21-1/00',5,'Tratamento e disposição de resíduos não-perigosos');
INSERT INTO migra_cnae VALUES ('E.38.22-0/00',4,'Tratamento e disposição de resíduos perigosos');
INSERT INTO migra_cnae VALUES ('E.38.22-0/00',5,'Tratamento e disposição de resíduos perigosos');
INSERT INTO migra_cnae VALUES ('E.38.30-0/00',3,'Recuperação de materiais');
INSERT INTO migra_cnae VALUES ('E.38.31-9/00',4,'Recuperação de materiais metálicos');
INSERT INTO migra_cnae VALUES ('E.38.31-9/01',5,'Recuperação de sucatas de alumínio');
INSERT INTO migra_cnae VALUES ('E.38.31-9/99',5,'Recuperação de materiais metálicos, exceto alumínio');
INSERT INTO migra_cnae VALUES ('E.38.32-7/00',4,'Recuperação de materiais plásticos');
INSERT INTO migra_cnae VALUES ('E.38.32-7/00',5,'Recuperação de materiais plásticos');
INSERT INTO migra_cnae VALUES ('E.38.39-4/00',4,'Recuperação de materiais não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('E.38.39-4/01',5,'Usinas de compostagem');
INSERT INTO migra_cnae VALUES ('E.38.39-4/99',5,'Recuperação de materiais não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('E.39.00-0/00',2,'DESCONTAMINAÇÃO E OUTROS SERVIÇOS DE GESTÃO DE RESÍDUOS');
INSERT INTO migra_cnae VALUES ('E.39.00-0/00',3,'Descontaminação e outros serviços de gestão de resíduos');
INSERT INTO migra_cnae VALUES ('E.39.00-5/00',4,'Descontaminação e outros serviços de gestão de resíduos');
INSERT INTO migra_cnae VALUES ('E.39.00-5/00',5,'Descontaminação e outros serviços de gestão de resíduos');
INSERT INTO migra_cnae VALUES ('F.00.00-0/00',1,'CONSTRUÇÃO');
INSERT INTO migra_cnae VALUES ('F.41.00-0/00',2,'CONSTRUÇÃO DE EDIFÍCIOS');
INSERT INTO migra_cnae VALUES ('F.41.10-0/00',3,'Incorporação de empreendimentos imobiliários');
INSERT INTO migra_cnae VALUES ('F.41.10-7/00',4,'Incorporação de empreendimentos imobiliários');
INSERT INTO migra_cnae VALUES ('F.41.10-7/00',5,'Incorporação de empreendimentos imobiliários');
INSERT INTO migra_cnae VALUES ('F.41.20-0/00',3,'Construção de edifícios');
INSERT INTO migra_cnae VALUES ('F.41.20-4/00',4,'Construção de edifícios');
INSERT INTO migra_cnae VALUES ('F.41.20-4/00',5,'Construção de edifícios');
INSERT INTO migra_cnae VALUES ('F.42.00-0/00',2,'OBRAS DE INFRA-ESTRUTURA');
INSERT INTO migra_cnae VALUES ('F.42.10-0/00',3,'Construção de rodovias, ferrovias, obras urbanas e obras-de-arte especiais');
INSERT INTO migra_cnae VALUES ('F.42.11-1/00',4,'Construção de rodovias e ferrovias');
INSERT INTO migra_cnae VALUES ('F.42.11-1/01',5,'Construção de rodovias e ferrovias');
INSERT INTO migra_cnae VALUES ('F.42.11-1/02',5,'Pintura para sinalização em pistas rodoviárias e aeroportos');
INSERT INTO migra_cnae VALUES ('F.42.12-0/00',4,'Construção de obras-de-arte especiais');
INSERT INTO migra_cnae VALUES ('F.42.12-0/00',5,'Construção de obras-de-arte especiais');
INSERT INTO migra_cnae VALUES ('F.42.13-8/00',4,'Obras de urbanização - ruas, praças e calçadas');
INSERT INTO migra_cnae VALUES ('F.42.13-8/00',5,'Obras de urbanização - ruas, praças e calçadas');
INSERT INTO migra_cnae VALUES ('F.42.20-0/00',3,'Obras de infra-estrutura para energia elétrica, telecomunicações, água, esgoto e transporte por dutos');
INSERT INTO migra_cnae VALUES ('F.42.21-9/00',4,'Obras para geração e distribuição de energia elétrica e para telecomunicações');
INSERT INTO migra_cnae VALUES ('F.42.21-9/01',5,'Construção de barragens e represas para geração de energia elétrica');
INSERT INTO migra_cnae VALUES ('F.42.21-9/02',5,'Construção de estações e redes de distribuição de energia elétrica');
INSERT INTO migra_cnae VALUES ('F.42.21-9/03',5,'Manutenção de redes de distribuição de energia elétrica');
INSERT INTO migra_cnae VALUES ('F.42.21-9/04',5,'Construção de estações e redes de telecomunicações');
INSERT INTO migra_cnae VALUES ('F.42.21-9/05',5,'Manutenção de estações e redes de telecomunicações');
INSERT INTO migra_cnae VALUES ('F.42.22-7/00',4,'Construção de redes de abastecimento de água, coleta de esgoto e construções correlatas');
INSERT INTO migra_cnae VALUES ('F.42.22-7/01',5,'Construção de redes de abastecimento de água, coleta de esgoto e construções correlatas, exceto obras de irrigação');
INSERT INTO migra_cnae VALUES ('F.42.22-7/02',5,'Obras de irrigação');
INSERT INTO migra_cnae VALUES ('F.42.23-5/00',4,'Construção de redes de transportes por dutos, exceto para água e esgoto');
INSERT INTO migra_cnae VALUES ('F.42.23-5/00',5,'Construção de redes de transportes por dutos, exceto para água e esgoto');
INSERT INTO migra_cnae VALUES ('F.42.90-0/00',3,'Construção de outras obras de infra-estrutura');
INSERT INTO migra_cnae VALUES ('F.42.91-0/00',4,'Obras portuárias, marítimas e fluviais');
INSERT INTO migra_cnae VALUES ('F.42.91-0/00',5,'Obras portuárias, marítimas e fluviais');
INSERT INTO migra_cnae VALUES ('F.42.92-8/00',4,'Montagem de instalações industriais e de estruturas metálicas');
INSERT INTO migra_cnae VALUES ('F.42.92-8/01',5,'Montagem de estruturas metálicas');
INSERT INTO migra_cnae VALUES ('F.42.92-8/02',5,'Obras de montagem industrial');
INSERT INTO migra_cnae VALUES ('F.42.99-5/00',4,'Obras de engenharia civil não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('F.42.99-5/01',5,'Construção de instalações esportivas e recreativas');
INSERT INTO migra_cnae VALUES ('F.42.99-5/99',5,'Outras obras de engenharia civil não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('F.43.00-0/00',2,'SERVIÇOS ESPECIALIZADOS PARA CONSTRUÇÃO');
INSERT INTO migra_cnae VALUES ('F.43.10-0/00',3,'Demolição e preparação do terreno');
INSERT INTO migra_cnae VALUES ('F.43.11-8/00',4,'Demolição e preparação de canteiros de obras');
INSERT INTO migra_cnae VALUES ('F.43.11-8/01',5,'Demolição de edifícios e outras estruturas');
INSERT INTO migra_cnae VALUES ('F.43.11-8/02',5,'Preparação de canteiro e limpeza de terreno');
INSERT INTO migra_cnae VALUES ('F.43.12-6/00',4,'Perfurações e sondagens');
INSERT INTO migra_cnae VALUES ('F.43.12-6/00',5,'Perfurações e sondagens');
INSERT INTO migra_cnae VALUES ('F.43.13-4/00',4,'Obras de terraplenagem');
INSERT INTO migra_cnae VALUES ('F.43.13-4/00',5,'Obras de terraplenagem');
INSERT INTO migra_cnae VALUES ('F.43.19-3/00',4,'Serviços de preparação do terreno não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('F.43.19-3/00',5,'Serviços de preparação do terreno não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('F.43.20-0/00',3,'Instalações elétricas, hidráulicas e outras instalações em construções');
INSERT INTO migra_cnae VALUES ('F.43.21-5/00',4,'Instalações elétricas');
INSERT INTO migra_cnae VALUES ('F.43.21-5/00',5,'Instalação e manutenção elétrica');
INSERT INTO migra_cnae VALUES ('F.43.22-3/00',4,'Instalações hidráulicas, de sistemas de ventilação e refrigeração');
INSERT INTO migra_cnae VALUES ('F.43.22-3/01',5,'Instalações hidráulicas, sanitárias e de gás');
INSERT INTO migra_cnae VALUES ('F.43.22-3/02',5,'Instalação e manutenção de sistemas centrais de ar condicionado, de ventilação e refrigeração');
INSERT INTO migra_cnae VALUES ('F.43.22-3/03',5,'Instalações de sistema de prevenção contra incêndio');
INSERT INTO migra_cnae VALUES ('F.43.29-1/00',4,'Obras de instalações em construções não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('F.43.29-1/01',5,'Instalação de painéis publicitários');
INSERT INTO migra_cnae VALUES ('F.43.29-1/02',5,'Instalação de equipamentos para orientação à navegação marítima, fluvial e lacustre');
INSERT INTO migra_cnae VALUES ('F.43.29-1/03',5,'Instalação, manutenção e reparação de elevadores, escadas e esteiras rolantes');
INSERT INTO migra_cnae VALUES ('F.43.29-1/04',5,'Montagem e instalação de sistemas e equipamentos de iluminação e sinalização em vias públicas, portos e aeroportos');
INSERT INTO migra_cnae VALUES ('F.43.29-1/05',5,'Tratamentos térmicos, acústicos ou de vibração');
INSERT INTO migra_cnae VALUES ('F.43.29-1/99',5,'Outras obras de instalações em construções não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('F.43.30-0/00',3,'Obras de acabamento');
INSERT INTO migra_cnae VALUES ('F.43.30-4/00',4,'Obras de acabamento');
INSERT INTO migra_cnae VALUES ('F.43.30-4/01',5,'Impermeabilização em obras de engenharia civil');
INSERT INTO migra_cnae VALUES ('F.43.30-4/02',5,'Instalação de portas, janelas, tetos, divisórias e armários embutidos de qualquer material');
INSERT INTO migra_cnae VALUES ('F.43.30-4/03',5,'Obras de acabamento em gesso e estuque');
INSERT INTO migra_cnae VALUES ('F.43.30-4/04',5,'Serviços de pintura de edifícios em geral');
INSERT INTO migra_cnae VALUES ('F.43.30-4/05',5,'Aplicação de revestimentos e de resinas em interiores e exteriores');
INSERT INTO migra_cnae VALUES ('F.43.30-4/99',5,'Outras obras de acabamento da construção');
INSERT INTO migra_cnae VALUES ('F.43.90-0/00',3,'Outros serviços especializados para construção');
INSERT INTO migra_cnae VALUES ('F.43.91-6/00',4,'Obras de fundações');
INSERT INTO migra_cnae VALUES ('F.43.91-6/00',5,'Obras de fundações');
INSERT INTO migra_cnae VALUES ('F.43.99-1/00',4,'Serviços especializados para construção não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('F.43.99-1/01',5,'Administração de obras');
INSERT INTO migra_cnae VALUES ('F.43.99-1/02',5,'Montagem e desmontagem de andaimes e outras estruturas temporárias');
INSERT INTO migra_cnae VALUES ('F.43.99-1/03',5,'Obras de alvenaria');
INSERT INTO migra_cnae VALUES ('F.43.99-1/04',5,'Serviços de operação e fornecimento de equipamentos para transporte e elevação de cargas e pessoas para uso em obras');
INSERT INTO migra_cnae VALUES ('F.43.99-1/05',5,'Perfuração e construção de poços de água');
INSERT INTO migra_cnae VALUES ('F.43.99-1/99',5,'Serviços especializados para construção não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.00.00-0/00',1,'COMÉRCIO; REPARAÇÃO DE VEÍCULOS AUTOMOTORES E MOTOCICLETAS');
INSERT INTO migra_cnae VALUES ('G.45.00-0/00',2,'COMÉRCIO E REPARAÇÃO DE VEÍCULOS AUTOMOTORES E MOTOCICLETAS');
INSERT INTO migra_cnae VALUES ('G.45.10-0/00',3,'Comércio de veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.11-1/00',4,'Comércio a varejo e por atacado de veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.11-1/01',5,'Comércio a varejo de automóveis, camionetas e utilitários novos');
INSERT INTO migra_cnae VALUES ('G.45.11-1/02',5,'Comércio a varejo de automóveis, camionetas e utilitários usados');
INSERT INTO migra_cnae VALUES ('G.45.11-1/03',5,'Comércio por atacado de automóveis, camionetas e utilitários novos e usados');
INSERT INTO migra_cnae VALUES ('G.45.11-1/04',5,'Comércio por atacado de caminhões novos e usados');
INSERT INTO migra_cnae VALUES ('G.45.11-1/05',5,'Comércio por atacado de reboques e semi-reboques novos e usados');
INSERT INTO migra_cnae VALUES ('G.45.11-1/06',5,'Comércio por atacado de ônibus e microônibus novos e usados');
INSERT INTO migra_cnae VALUES ('G.45.12-9/00',4,'Representantes comerciais e agentes do comércio de veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.12-9/01',5,'Representantes comerciais e agentes do comércio de veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.12-9/02',5,'Comércio sob consignação de veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.20-0/00',3,'Manutenção e reparação de veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.20-0/00',4,'Manutenção e reparação de veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.20-0/01',5,'Serviços de manutenção e reparação mecânica de veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.20-0/02',5,'Serviços de lanternagem ou funilaria e pintura de veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.20-0/03',5,'Serviços de manutenção e reparação elétrica de veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.20-0/04',5,'Serviços de alinhamento e balanceamento de veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.20-0/05',5,'Serviços de lavagem, lubrificação e polimento de veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.20-0/06',5,'Serviços de borracharia para veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.20-0/07',5,'Serviços de instalação, manutenção e reparação de acessórios para veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.20-0/08',5,'Serviços de capotaria');
INSERT INTO migra_cnae VALUES ('G.45.30-0/00',3,'Comércio de peças e acessórios para veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.30-7/00',4,'Comércio de peças e acessórios para veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.30-7/01',5,'Comércio por atacado de peças e acessórios novos para veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.30-7/02',5,'Comércio por atacado de pneumáticos e câmaras-de-ar');
INSERT INTO migra_cnae VALUES ('G.45.30-7/03',5,'Comércio a varejo de peças e acessórios novos para veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.30-7/04',5,'Comércio a varejo de peças e acessórios usados para veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.30-7/05',5,'Comércio a varejo de pneumáticos e câmaras-de-ar');
INSERT INTO migra_cnae VALUES ('G.45.30-7/06',5,'Representantes comerciais e agentes do comércio de peças e acessórios novos e usados para veículos automotores');
INSERT INTO migra_cnae VALUES ('G.45.40-0/00',3,'Comércio, manutenção e reparação de motocicletas, peças e acessórios');
INSERT INTO migra_cnae VALUES ('G.45.41-2/00',4,'Comércio por atacado e a varejo de motocicletas, peças e acessórios');
INSERT INTO migra_cnae VALUES ('G.45.41-2/01',5,'Comércio por atacado de motocicletas e motonetas');
INSERT INTO migra_cnae VALUES ('G.45.41-2/02',5,'Comércio por atacado de peças e acessórios para motocicletas e motonetas');
INSERT INTO migra_cnae VALUES ('G.45.41-2/03',5,'Comércio a varejo de motocicletas e motonetas novas');
INSERT INTO migra_cnae VALUES ('G.45.41-2/04',5,'Comércio a varejo de motocicletas e motonetas usadas');
INSERT INTO migra_cnae VALUES ('G.45.41-2/05',5,'Comércio a varejo de peças e acessórios para motocicletas e motonetas');
INSERT INTO migra_cnae VALUES ('G.45.42-1/00',4,'Representantes comerciais e agentes do comércio de motocicletas, peças e acessórios');
INSERT INTO migra_cnae VALUES ('G.45.42-1/01',5,'Representantes comerciais e agentes do comércio de motocicletas e motonetas, peças e acessórios');
INSERT INTO migra_cnae VALUES ('G.45.42-1/02',5,'Comércio sob consignação de motocicletas e motonetas');
INSERT INTO migra_cnae VALUES ('G.45.43-9/00',4,'Manutenção e reparação de motocicletas');
INSERT INTO migra_cnae VALUES ('G.45.43-9/00',5,'Manutenção e reparação de motocicletas e motonetas');
INSERT INTO migra_cnae VALUES ('G.46.00-0/00',2,'COMÉRCIO POR ATACADO, EXCETO VEÍCULOS AUTOMOTORES E MOTOCICLETAS');
INSERT INTO migra_cnae VALUES ('G.46.10-0/00',3,'Representantes comerciais e agentes do comércio, exceto de veículos automotores e motocicletas');
INSERT INTO migra_cnae VALUES ('G.46.11-7/00',4,'Representantes comerciais e agentes do comércio de matérias-primas agrícolas e animais vivos');
INSERT INTO migra_cnae VALUES ('G.46.11-7/00',5,'Representantes comerciais e agentes do comércio de matérias-primas agrícolas e animais vivos');
INSERT INTO migra_cnae VALUES ('G.46.12-5/00',4,'Representantes comerciais e agentes do comércio de combustíveis, minerais, produtos siderúrgicos e químicos');
INSERT INTO migra_cnae VALUES ('G.46.12-5/00',5,'Representantes comerciais e agentes do comércio de combustíveis, minerais, produtos siderúrgicos e químicos');
INSERT INTO migra_cnae VALUES ('G.46.13-3/00',4,'Representantes comerciais e agentes do comércio de madeira, material de construção e ferragens');
INSERT INTO migra_cnae VALUES ('G.46.13-3/00',5,'Representantes comerciais e agentes do comércio de madeira, material de construção e ferragens');
INSERT INTO migra_cnae VALUES ('G.46.14-1/00',4,'Representantes comerciais e agentes do comércio de máquinas, equipamentos, embarcações e aeronaves');
INSERT INTO migra_cnae VALUES ('G.46.14-1/00',5,'Representantes comerciais e agentes do comércio de máquinas, equipamentos, embarcações e aeronaves');
INSERT INTO migra_cnae VALUES ('G.46.15-0/00',4,'Representantes comerciais e agentes do comércio de eletrodomésticos, móveis e artigos de uso doméstico');
INSERT INTO migra_cnae VALUES ('G.46.15-0/00',5,'Representantes comerciais e agentes do comércio de eletrodomésticos, móveis e artigos de uso doméstico');
INSERT INTO migra_cnae VALUES ('G.46.16-8/00',4,'Representantes comerciais e agentes do comércio de têxteis, vestuário, calçados e artigos de viagem');
INSERT INTO migra_cnae VALUES ('G.46.16-8/00',5,'Representantes comerciais e agentes do comércio de têxteis, vestuário, calçados e artigos de viagem');
INSERT INTO migra_cnae VALUES ('G.46.17-6/00',4,'Representantes comerciais e agentes do comércio de produtos alimentícios, bebidas e fumo');
INSERT INTO migra_cnae VALUES ('G.46.17-6/00',5,'Representantes comerciais e agentes do comércio de produtos alimentícios, bebidas e fumo');
INSERT INTO migra_cnae VALUES ('G.46.18-4/00',4,'Representantes comerciais e agentes do comércio especializado em produtos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.46.18-4/01',5,'Representantes comerciais e agentes do comércio de medicamentos, cosméticos e produtos de perfumaria');
INSERT INTO migra_cnae VALUES ('G.46.18-4/02',5,'Representantes comerciais e agentes do comércio de instrumentos e materiais odonto-médico-hospitalares');
INSERT INTO migra_cnae VALUES ('G.46.18-4/03',5,'Representantes comerciais e agentes do comércio de jornais, revistas e outras publicações');
INSERT INTO migra_cnae VALUES ('G.46.18-4/99',5,'Outros representantes comerciais e agentes do comércio especializado em produtos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.46.19-2/00',4,'Representantes comerciais e agentes do comércio de mercadorias em geral não especializado');
INSERT INTO migra_cnae VALUES ('G.46.19-2/00',5,'Representantes comerciais e agentes do comércio de mercadorias em geral não especializado');
INSERT INTO migra_cnae VALUES ('G.46.20-0/00',3,'Comércio atacadista de matérias-primas agrícolas e animais vivos');
INSERT INTO migra_cnae VALUES ('G.46.21-4/00',4,'Comércio atacadista de café em grão');
INSERT INTO migra_cnae VALUES ('G.46.21-4/00',5,'Comércio atacadista de café em grão');
INSERT INTO migra_cnae VALUES ('G.46.22-2/00',4,'Comércio atacadista de soja');
INSERT INTO migra_cnae VALUES ('G.46.22-2/00',5,'Comércio atacadista de soja');
INSERT INTO migra_cnae VALUES ('G.46.23-1/00',4,'Comércio atacadista de animais vivos, alimentos para animais e matérias-primas agrícolas, exceto café e soja');
INSERT INTO migra_cnae VALUES ('G.46.23-1/01',5,'Comércio atacadista de animais vivos');
INSERT INTO migra_cnae VALUES ('G.46.23-1/02',5,'Comércio atacadista de couros, lãs, peles e outros subprodutos não-comestíveis de origem animal');
INSERT INTO migra_cnae VALUES ('G.46.23-1/03',5,'Comércio atacadista de algodão');
INSERT INTO migra_cnae VALUES ('G.46.23-1/04',5,'Comércio atacadista de fumo em folha não beneficiado');
INSERT INTO migra_cnae VALUES ('G.46.23-1/05',5,'Comércio atacadista de cacau');
INSERT INTO migra_cnae VALUES ('G.46.23-1/06',5,'Comércio atacadista de sementes, flores, plantas e gramas');
INSERT INTO migra_cnae VALUES ('G.46.23-1/07',5,'Comércio atacadista de sisal');
INSERT INTO migra_cnae VALUES ('G.46.23-1/08',5,'Comércio atacadista de matérias-primas agrícolas com atividade de fracionamento e acondicionamento associada');
INSERT INTO migra_cnae VALUES ('G.46.23-1/09',5,'Comércio atacadista de alimentos para animais');
INSERT INTO migra_cnae VALUES ('G.46.23-1/99',5,'Comércio atacadista de matérias-primas agrícolas não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('G.46.30-0/00',3,'Comércio atacadista especializado em produtos alimentícios, bebidas e fumo');
INSERT INTO migra_cnae VALUES ('G.46.31-1/00',4,'Comércio atacadista de leite e laticínios');
INSERT INTO migra_cnae VALUES ('G.46.31-1/00',5,'Comércio atacadista de leite e laticínios');
INSERT INTO migra_cnae VALUES ('G.46.32-0/00',4,'Comércio atacadista de cereais e leguminosas beneficiados, farinhas, amidos e féculas');
INSERT INTO migra_cnae VALUES ('G.46.32-0/01',5,'Comércio atacadista de cereais e leguminosas beneficiados');
INSERT INTO migra_cnae VALUES ('G.46.32-0/02',5,'Comércio atacadista de farinhas, amidos e féculas');
INSERT INTO migra_cnae VALUES ('G.46.32-0/03',5,'Comércio atacadista de cereais e leguminosas beneficiados, farinhas, amidos e féculas, com atividade de fracionamento e acondicionamento associada');
INSERT INTO migra_cnae VALUES ('G.46.33-8/00',4,'Comércio atacadista de hortifrutigranjeiros');
INSERT INTO migra_cnae VALUES ('G.46.33-8/01',5,'Comércio atacadista de frutas, verduras, raízes, tubérculos, hortaliças e legumes frescos');
INSERT INTO migra_cnae VALUES ('G.46.33-8/02',5,'Comércio atacadista de aves vivas e ovos');
INSERT INTO migra_cnae VALUES ('G.46.33-8/03',5,'Comércio atacadista de coelhos e outros pequenos animais vivos para alimentação');
INSERT INTO migra_cnae VALUES ('G.46.34-6/00',4,'Comércio atacadista de carnes, produtos da carne e pescado');
INSERT INTO migra_cnae VALUES ('G.46.34-6/01',5,'Comércio atacadista de carnes bovinas e suínas e derivados');
INSERT INTO migra_cnae VALUES ('G.46.34-6/02',5,'Comércio atacadista de aves abatidas e derivados');
INSERT INTO migra_cnae VALUES ('G.46.34-6/03',5,'Comércio atacadista de pescados e frutos do mar');
INSERT INTO migra_cnae VALUES ('G.46.34-6/99',5,'Comércio atacadista de carnes e derivados de outros animais');
INSERT INTO migra_cnae VALUES ('G.46.35-4/00',4,'Comércio atacadista de bebidas');
INSERT INTO migra_cnae VALUES ('G.46.35-4/01',5,'Comércio atacadista de água mineral');
INSERT INTO migra_cnae VALUES ('G.46.35-4/02',5,'Comércio atacadista de cerveja, chope e refrigerante');
INSERT INTO migra_cnae VALUES ('G.46.35-4/03',5,'Comércio atacadista de bebidas com atividade de fracionamento e acondicionamento associada');
INSERT INTO migra_cnae VALUES ('G.46.35-4/99',5,'Comércio atacadista de bebidas não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('G.46.36-2/00',4,'Comércio atacadista de produtos do fumo');
INSERT INTO migra_cnae VALUES ('G.46.36-2/01',5,'Comércio atacadista de fumo beneficiado');
INSERT INTO migra_cnae VALUES ('G.46.36-2/02',5,'Comércio atacadista de cigarros, cigarrilhas e charutos');
INSERT INTO migra_cnae VALUES ('G.46.37-1/00',4,'Comércio atacadista especializado em produtos alimentícios não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.46.37-1/01',5,'Comércio atacadista de café torrado, moído e solúvel');
INSERT INTO migra_cnae VALUES ('G.46.37-1/02',5,'Comércio atacadista de açúcar');
INSERT INTO migra_cnae VALUES ('G.46.37-1/03',5,'Comércio atacadista de óleos e gorduras');
INSERT INTO migra_cnae VALUES ('G.46.37-1/04',5,'Comércio atacadista de pães, bolos, biscoitos e similares');
INSERT INTO migra_cnae VALUES ('G.46.37-1/05',5,'Comércio atacadista de massas alimentícias');
INSERT INTO migra_cnae VALUES ('G.46.37-1/06',5,'Comércio atacadista de sorvetes');
INSERT INTO migra_cnae VALUES ('G.46.37-1/07',5,'Comércio atacadista de chocolates, confeitos, balas, bombons e semelhantes');
INSERT INTO migra_cnae VALUES ('G.46.37-1/99',5,'Comércio atacadista especializado em outros produtos alimentícios não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.46.39-7/00',4,'Comércio atacadista de produtos alimentícios em geral');
INSERT INTO migra_cnae VALUES ('G.46.39-7/01',5,'Comércio atacadista de produtos alimentícios em geral');
INSERT INTO migra_cnae VALUES ('G.46.39-7/02',5,'Comércio atacadista de produtos alimentícios em geral, com atividade de fracionamento e acondicionamento associada');
INSERT INTO migra_cnae VALUES ('G.46.40-0/00',3,'Comércio atacadista de produtos de consumo não-alimentar');
INSERT INTO migra_cnae VALUES ('G.46.41-9/00',4,'Comércio atacadista de tecidos, artefatos de tecidos e de armarinho');
INSERT INTO migra_cnae VALUES ('G.46.41-9/01',5,'Comércio atacadista de tecidos');
INSERT INTO migra_cnae VALUES ('G.46.41-9/02',5,'Comércio atacadista de artigos de cama, mesa e banho');
INSERT INTO migra_cnae VALUES ('G.46.41-9/03',5,'Comércio atacadista de artigos de armarinho');
INSERT INTO migra_cnae VALUES ('G.46.42-7/00',4,'Comércio atacadista de artigos do vestuário e acessórios');
INSERT INTO migra_cnae VALUES ('G.46.42-7/01',5,'Comércio atacadista de artigos do vestuário e acessórios, exceto profissionais e de segurança');
INSERT INTO migra_cnae VALUES ('G.46.42-7/02',5,'Comércio atacadista de roupas e acessórios para uso profissional e de segurança do trabalho');
INSERT INTO migra_cnae VALUES ('G.46.43-5/00',4,'Comércio atacadista de calçados e artigos de viagem');
INSERT INTO migra_cnae VALUES ('G.46.43-5/01',5,'Comércio atacadista de calçados');
INSERT INTO migra_cnae VALUES ('G.46.43-5/02',5,'Comércio atacadista de bolsas, malas e artigos de viagem');
INSERT INTO migra_cnae VALUES ('G.46.44-3/00',4,'Comércio atacadista de produtos farmacêuticos para uso humano e veterinário');
INSERT INTO migra_cnae VALUES ('G.46.44-3/01',5,'Comércio atacadista de medicamentos e drogas de uso humano');
INSERT INTO migra_cnae VALUES ('G.46.44-3/02',5,'Comércio atacadista de medicamentos e drogas de uso veterinário');
INSERT INTO migra_cnae VALUES ('G.46.45-1/00',4,'Comércio atacadista de instrumentos e materiais para uso médico, cirúrgico, ortopédico e odontológico');
INSERT INTO migra_cnae VALUES ('G.46.45-1/01',5,'Comércio atacadista de instrumentos e materiais para uso médico, cirúrgico, hospitalar e de laboratórios');
INSERT INTO migra_cnae VALUES ('G.46.45-1/02',5,'Comércio atacadista de próteses e artigos de ortopedia');
INSERT INTO migra_cnae VALUES ('G.46.45-1/03',5,'Comércio atacadista de produtos odontológicos');
INSERT INTO migra_cnae VALUES ('G.46.46-0/00',4,'Comércio atacadista de cosméticos, produtos de perfumaria e de higiene pessoal');
INSERT INTO migra_cnae VALUES ('G.46.46-0/01',5,'Comércio atacadista de cosméticos e produtos de perfumaria');
INSERT INTO migra_cnae VALUES ('G.46.46-0/02',5,'Comércio atacadista de produtos de higiene pessoal');
INSERT INTO migra_cnae VALUES ('G.46.47-8/00',4,'Comércio atacadista de artigos de escritório e de papelaria; livros, jornais e outras publicações');
INSERT INTO migra_cnae VALUES ('G.46.47-8/01',5,'Comércio atacadista de artigos de escritório e de papelaria');
INSERT INTO migra_cnae VALUES ('G.46.47-8/02',5,'Comércio atacadista de livros, jornais e outras publicações');
INSERT INTO migra_cnae VALUES ('G.46.49-4/00',4,'Comércio atacadista de equipamentos e artigos de uso pessoal e doméstico não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.46.49-4/01',5,'Comércio atacadista de equipamentos elétricos de uso pessoal e doméstico');
INSERT INTO migra_cnae VALUES ('G.46.49-4/02',5,'Comércio atacadista de aparelhos eletrônicos de uso pessoal e doméstico');
INSERT INTO migra_cnae VALUES ('G.46.49-4/03',5,'Comércio atacadista de bicicletas, triciclos e outros veículos recreativos');
INSERT INTO migra_cnae VALUES ('G.46.49-4/04',5,'Comércio atacadista de móveis e artigos de colchoaria');
INSERT INTO migra_cnae VALUES ('G.46.49-4/05',5,'Comércio atacadista de artigos de tapeçaria; persianas e cortinas');
INSERT INTO migra_cnae VALUES ('G.46.49-4/06',5,'Comércio atacadista de lustres, luminárias e abajures');
INSERT INTO migra_cnae VALUES ('G.46.49-4/07',5,'Comércio atacadista de filmes, CDs, DVDs, fitas e discos');
INSERT INTO migra_cnae VALUES ('G.46.49-4/08',5,'Comércio atacadista de produtos de higiene, limpeza e conservação domiciliar');
INSERT INTO migra_cnae VALUES ('G.46.49-4/09',5,'Comércio atacadista de produtos de higiene, limpeza e conservação domiciliar, com atividade de fracionamento e acondicionamento associada');
INSERT INTO migra_cnae VALUES ('G.46.49-4/10',5,'Comércio atacadista de jóias, relógios e bijuterias, inclusive pedras preciosas e semipreciosas lapidadas');
INSERT INTO migra_cnae VALUES ('G.46.49-4/99',5,'Comércio atacadista de outros equipamentos e artigos de uso pessoal e doméstico não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.46.50-0/00',3,'Comércio atacadista de equipamentos e produtos de tecnologias de informação e comunicação');
INSERT INTO migra_cnae VALUES ('G.46.51-6/00',4,'Comércio atacadista de computadores, periféricos e suprimentos de informática');
INSERT INTO migra_cnae VALUES ('G.46.51-6/01',5,'Comércio atacadista de equipamentos de informática');
INSERT INTO migra_cnae VALUES ('G.46.51-6/02',5,'Comércio atacadista de suprimentos para informática');
INSERT INTO migra_cnae VALUES ('G.46.52-4/00',4,'Comércio atacadista de componentes eletrônicos e equipamentos de telefonia e comunicação');
INSERT INTO migra_cnae VALUES ('G.46.52-4/00',5,'Comércio atacadista de componentes eletrônicos e equipamentos de telefonia e comunicação');
INSERT INTO migra_cnae VALUES ('G.46.60-0/00',3,'Comércio atacadista de máquinas, aparelhos e equipamentos, exceto de tecnologias de informação e comunicação');
INSERT INTO migra_cnae VALUES ('G.46.61-3/00',4,'Comércio atacadista de máquinas, aparelhos e equipamentos para uso agropecuário; partes e peças');
INSERT INTO migra_cnae VALUES ('G.46.61-3/00',5,'Comércio atacadista de máquinas, aparelhos e equipamentos para uso agropecuário; partes e peças');
INSERT INTO migra_cnae VALUES ('G.46.62-1/00',4,'Comércio atacadista de máquinas, equipamentos para terraplenagem, mineração e construção; partes e peças');
INSERT INTO migra_cnae VALUES ('G.46.62-1/00',5,'Comércio atacadista de máquinas, equipamentos para terraplenagem, mineração e construção; partes e peças');
INSERT INTO migra_cnae VALUES ('G.46.63-0/00',4,'Comércio atacadista de máquinas e equipamentos para uso industrial; partes e peças');
INSERT INTO migra_cnae VALUES ('G.46.63-0/00',5,'Comércio atacadista de máquinas e equipamentos para uso industrial; partes e peças');
INSERT INTO migra_cnae VALUES ('G.46.64-8/00',4,'Comércio atacadista de máquinas, aparelhos e equipamentos para uso odonto-médico-hospitalar; partes e peças');
INSERT INTO migra_cnae VALUES ('G.46.64-8/00',5,'Comércio atacadista de máquinas, aparelhos e equipamentos para uso odonto-médico-hospitalar; partes e peças');
INSERT INTO migra_cnae VALUES ('G.46.65-6/00',4,'Comércio atacadista de máquinas e equipamentos para uso comercial; partes e peças');
INSERT INTO migra_cnae VALUES ('G.46.65-6/00',5,'Comércio atacadista de máquinas e equipamentos para uso comercial; partes e peças');
INSERT INTO migra_cnae VALUES ('G.46.69-9/00',4,'Comércio atacadista de máquinas, aparelhos e equipamentos não especificados anteriormente; partes e peças');
INSERT INTO migra_cnae VALUES ('G.46.69-9/01',5,'Comércio atacadista de bombas e compressores; partes e peças');
INSERT INTO migra_cnae VALUES ('G.46.69-9/99',5,'Comércio atacadista de outras máquinas e equipamentos não especificados anteriormente; partes e peças');
INSERT INTO migra_cnae VALUES ('G.46.70-0/00',3,'Comércio atacadista de madeira, ferragens, ferramentas, material elétrico e material de construção');
INSERT INTO migra_cnae VALUES ('G.46.71-1/00',4,'Comércio atacadista de madeira e produtos derivados');
INSERT INTO migra_cnae VALUES ('G.46.71-1/00',5,'Comércio atacadista de madeira e produtos derivados');
INSERT INTO migra_cnae VALUES ('G.46.72-9/00',4,'Comércio atacadista de ferragens e ferramentas');
INSERT INTO migra_cnae VALUES ('G.46.72-9/00',5,'Comércio atacadista de ferragens e ferramentas');
INSERT INTO migra_cnae VALUES ('G.46.73-7/00',4,'Comércio atacadista de material elétrico');
INSERT INTO migra_cnae VALUES ('G.46.73-7/00',5,'Comércio atacadista de material elétrico');
INSERT INTO migra_cnae VALUES ('G.46.74-5/00',4,'Comércio atacadista de cimento');
INSERT INTO migra_cnae VALUES ('G.46.74-5/00',5,'Comércio atacadista de cimento');
INSERT INTO migra_cnae VALUES ('G.46.79-6/00',4,'Comércio atacadista especializado de materiais de construção não especificados anteriormente e de materiais de construção em geral');
INSERT INTO migra_cnae VALUES ('G.46.79-6/01',5,'Comércio atacadista de tintas, vernizes e similares');
INSERT INTO migra_cnae VALUES ('G.46.79-6/02',5,'Comércio atacadista de mármores e granitos');
INSERT INTO migra_cnae VALUES ('G.46.79-6/03',5,'Comércio atacadista de vidros, espelhos e vitrais');
INSERT INTO migra_cnae VALUES ('G.46.79-6/04',5,'Comércio atacadista especializado de materiais de construção não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.46.79-6/99',5,'Comércio atacadista de materiais de construção em geral');
INSERT INTO migra_cnae VALUES ('G.46.80-0/00',3,'Comércio atacadista especializado em outros produtos');
INSERT INTO migra_cnae VALUES ('G.46.81-8/00',4,'Comércio atacadista de combustíveis sólidos, líquidos e gasosos, exceto gás natural e GLP');
INSERT INTO migra_cnae VALUES ('G.46.81-8/01',5,'Comércio atacadista de álcool carburante, biodiesel, gasolina e demais derivados de petróleo, exceto lubrificantes, não realizado por transportador retalhista (TRR)');
INSERT INTO migra_cnae VALUES ('G.46.81-8/02',5,'Comércio atacadista de combustíveis realizado por transportador retalhista (TRR)');
INSERT INTO migra_cnae VALUES ('G.46.81-8/03',5,'Comércio atacadista de combustíveis de origem vegetal, exceto álcool carburante');
INSERT INTO migra_cnae VALUES ('G.46.81-8/04',5,'Comércio atacadista de combustíveis de origem mineral em bruto');
INSERT INTO migra_cnae VALUES ('G.46.81-8/05',5,'Comércio atacadista de lubrificantes');
INSERT INTO migra_cnae VALUES ('G.46.82-6/00',4,'Comércio atacadista de gás liqüefeito de petróleo (GLP)');
INSERT INTO migra_cnae VALUES ('G.46.82-6/00',5,'Comércio atacadista de gás liqüefeito de petróleo (GLP)');
INSERT INTO migra_cnae VALUES ('G.46.83-4/00',4,'Comércio atacadista de defensivos agrícolas, adubos, fertilizantes e corretivos do solo');
INSERT INTO migra_cnae VALUES ('G.46.83-4/00',5,'Comércio atacadista de defensivos agrícolas, adubos, fertilizantes e corretivos do solo');
INSERT INTO migra_cnae VALUES ('G.46.84-2/00',4,'Comércio atacadista de produtos químicos e petroquímicos, exceto agroquímicos');
INSERT INTO migra_cnae VALUES ('G.46.84-2/01',5,'Comércio atacadista de resinas e elastômeros');
INSERT INTO migra_cnae VALUES ('G.46.84-2/02',5,'Comércio atacadista de solventes');
INSERT INTO migra_cnae VALUES ('G.46.84-2/99',5,'Comércio atacadista de outros produtos químicos e petroquímicos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.46.85-1/00',4,'Comércio atacadista de produtos siderúrgicos e metalúrgicos, exceto para construção');
INSERT INTO migra_cnae VALUES ('G.46.85-1/00',5,'Comércio atacadista de produtos siderúrgicos e metalúrgicos, exceto para construção');
INSERT INTO migra_cnae VALUES ('G.46.86-9/00',4,'Comércio atacadista de papel e papelão em bruto e de embalagens');
INSERT INTO migra_cnae VALUES ('G.46.86-9/01',5,'Comércio atacadista de papel e papelão em bruto');
INSERT INTO migra_cnae VALUES ('G.46.86-9/02',5,'Comércio atacadista de embalagens');
INSERT INTO migra_cnae VALUES ('G.46.87-7/00',4,'Comércio atacadista de resíduos e sucatas');
INSERT INTO migra_cnae VALUES ('G.46.87-7/01',5,'Comércio atacadista de resíduos de papel e papelão');
INSERT INTO migra_cnae VALUES ('G.46.87-7/02',5,'Comércio atacadista de resíduos e sucatas não-metálicos, exceto de papel e papelão');
INSERT INTO migra_cnae VALUES ('G.46.87-7/03',5,'Comércio atacadista de resíduos e sucatas metálicos');
INSERT INTO migra_cnae VALUES ('G.46.89-3/00',4,'Comércio atacadista especializado de outros produtos intermediários não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.46.89-3/01',5,'Comércio atacadista de produtos da extração mineral, exceto combustíveis');
INSERT INTO migra_cnae VALUES ('G.46.89-3/02',5,'Comércio atacadista de fios e fibras beneficiados');
INSERT INTO migra_cnae VALUES ('G.46.89-3/99',5,'Comércio atacadista especializado em outros produtos intermediários não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.46.90-0/00',3,'Comércio atacadista não-especializado');
INSERT INTO migra_cnae VALUES ('G.46.91-5/00',4,'Comércio atacadista de mercadorias em geral, com predominância de produtos alimentícios');
INSERT INTO migra_cnae VALUES ('G.46.91-5/00',5,'Comércio atacadista de mercadorias em geral, com predominância de produtos alimentícios');
INSERT INTO migra_cnae VALUES ('G.46.92-3/00',4,'Comércio atacadista de mercadorias em geral, com predominância de insumos agropecuários');
INSERT INTO migra_cnae VALUES ('G.46.92-3/00',5,'Comércio atacadista de mercadorias em geral, com predominância de insumos agropecuários');
INSERT INTO migra_cnae VALUES ('G.46.93-1/00',4,'Comércio atacadista de mercadorias em geral, sem predominância de alimentos ou de insumos agropecuários');
INSERT INTO migra_cnae VALUES ('G.46.93-1/00',5,'Comércio atacadista de mercadorias em geral, sem predominância de alimentos ou de insumos agropecuários');
INSERT INTO migra_cnae VALUES ('G.47.00-0/00',2,'COMÉRCIO VAREJISTA');
INSERT INTO migra_cnae VALUES ('G.47.10-0/00',3,'Comércio varejista não-especializado');
INSERT INTO migra_cnae VALUES ('G.47.11-3/00',4,'Comércio varejista de mercadorias em geral, com predominância de produtos alimentícios - hipermercados e supermercados');
INSERT INTO migra_cnae VALUES ('G.47.11-3/01',5,'Comércio varejista de mercadorias em geral, com predominância de produtos alimentícios - hipermercados');
INSERT INTO migra_cnae VALUES ('G.47.11-3/02',5,'Comércio varejista de mercadorias em geral, com predominância de produtos alimentícios - supermercados');
INSERT INTO migra_cnae VALUES ('G.47.12-1/00',4,'Comércio varejista de mercadorias em geral, com predominância de produtos alimentícios - minimercados, mercearias e armazéns');
INSERT INTO migra_cnae VALUES ('G.47.12-1/00',5,'Comércio varejista de mercadorias em geral, com predominância de produtos alimentícios - minimercados, mercearias e armazéns');
INSERT INTO migra_cnae VALUES ('G.47.13-0/00',4,'Comércio varejista de mercadorias em geral, sem predominância de produtos alimentícios');
INSERT INTO migra_cnae VALUES ('G.47.13-0/01',5,'Lojas de departamentos ou magazines');
INSERT INTO migra_cnae VALUES ('G.47.13-0/02',5,'Lojas de variedades, exceto lojas de departamentos ou magazines');
INSERT INTO migra_cnae VALUES ('G.47.13-0/03',5,'Lojas duty free de aeroportos internacionais');
INSERT INTO migra_cnae VALUES ('G.47.20-0/00',3,'Comércio varejista de produtos alimentícios, bebidas e fumo');
INSERT INTO migra_cnae VALUES ('G.47.21-1/00',4,'Comércio varejista de produtos de padaria, laticínio, doces, balas e semelhantes');
INSERT INTO migra_cnae VALUES ('G.47.21-1/02',5,'Padaria e confeitaria com predominância de revenda');
INSERT INTO migra_cnae VALUES ('G.47.21-1/03',5,'Comércio varejista de laticínios e frios');
INSERT INTO migra_cnae VALUES ('G.47.21-1/04',5,'Comércio varejista de doces, balas, bombons e semelhantes');
INSERT INTO migra_cnae VALUES ('G.47.22-9/00',4,'Comércio varejista de carnes e pescados - açougues e peixarias');
INSERT INTO migra_cnae VALUES ('G.47.22-9/01',5,'Comércio varejista de carnes - açougues');
INSERT INTO migra_cnae VALUES ('G.47.22-9/02',5,'Peixaria');
INSERT INTO migra_cnae VALUES ('G.47.23-7/00',4,'Comércio varejista de bebidas');
INSERT INTO migra_cnae VALUES ('G.47.23-7/00',5,'Comércio varejista de bebidas');
INSERT INTO migra_cnae VALUES ('G.47.24-5/00',4,'Comércio varejista de hortifrutigranjeiros');
INSERT INTO migra_cnae VALUES ('G.47.24-5/00',5,'Comércio varejista de hortifrutigranjeiros');
INSERT INTO migra_cnae VALUES ('G.47.29-6/00',4,'Comércio varejista de produtos alimentícios em geral ou especializado em produtos alimentícios não especificados anteriormente; produtos do fumo');
INSERT INTO migra_cnae VALUES ('G.47.29-6/01',5,'Tabacaria');
INSERT INTO migra_cnae VALUES ('G.47.29-6/02',5,'Comércio varejista de mercadorias em lojas de conveniência');
INSERT INTO migra_cnae VALUES ('G.47.29-6/99',5,'Comércio varejista de produtos alimentícios em geral ou especializado em produtos alimentícios não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.47.30-0/00',3,'Comércio varejista de combustíveis para veículos automotores');
INSERT INTO migra_cnae VALUES ('G.47.31-8/00',4,'Comércio varejista de combustíveis para veículos automotores');
INSERT INTO migra_cnae VALUES ('G.47.31-8/00',5,'Comércio varejista de combustíveis para veículos automotores');
INSERT INTO migra_cnae VALUES ('G.47.32-6/00',4,'Comércio varejista de lubrificantes');
INSERT INTO migra_cnae VALUES ('G.47.32-6/00',5,'Comércio varejista de lubrificantes');
INSERT INTO migra_cnae VALUES ('G.47.40-0/00',3,'Comércio varejista de material de construção');
INSERT INTO migra_cnae VALUES ('G.47.41-5/00',4,'Comércio varejista de tintas e materiais para pintura');
INSERT INTO migra_cnae VALUES ('G.47.41-5/00',5,'Comércio varejista de tintas e materiais para pintura');
INSERT INTO migra_cnae VALUES ('G.47.42-3/00',4,'Comércio varejista de material elétrico');
INSERT INTO migra_cnae VALUES ('G.47.42-3/00',5,'Comércio varejista de material elétrico');
INSERT INTO migra_cnae VALUES ('G.47.43-1/00',4,'Comércio varejista de vidros');
INSERT INTO migra_cnae VALUES ('G.47.43-1/00',5,'Comércio varejista de vidros');
INSERT INTO migra_cnae VALUES ('G.47.44-0/00',4,'Comércio varejista de ferragens, madeira e materiais de construção');
INSERT INTO migra_cnae VALUES ('G.47.44-0/01',5,'Comércio varejista de ferragens e ferramentas');
INSERT INTO migra_cnae VALUES ('G.47.44-0/02',5,'Comércio varejista de madeira e artefatos');
INSERT INTO migra_cnae VALUES ('G.47.44-0/03',5,'Comércio varejista de materiais hidráulicos');
INSERT INTO migra_cnae VALUES ('G.47.44-0/04',5,'Comércio varejista de cal, areia, pedra britada, tijolos e telhas');
INSERT INTO migra_cnae VALUES ('G.47.44-0/05',5,'Comércio varejista de materiais de construção não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.47.44-0/06',5,'Comércio varejista de pedras para revestimento');
INSERT INTO migra_cnae VALUES ('G.47.44-0/99',5,'Comércio varejista de materiais de construção em geral');
INSERT INTO migra_cnae VALUES ('G.47.50-0/00',3,'Comércio varejista de equipamentos de informática e comunicação; equipamentos e artigos de uso doméstico');
INSERT INTO migra_cnae VALUES ('G.47.51-2/00',4,'Comércio varejista especializado de equipamentos e suprimentos de informática');
INSERT INTO migra_cnae VALUES ('G.47.51-2/01',5,'Comércio varejista especializado de equipamentos e suprimentos de informática');
INSERT INTO migra_cnae VALUES ('G.47.51-2/02',5,'Recarga de cartuchos para equipamentos de informática');
INSERT INTO migra_cnae VALUES ('G.47.52-1/00',4,'Comércio varejista especializado de equipamentos de telefonia e comunicação');
INSERT INTO migra_cnae VALUES ('G.47.52-1/00',5,'Comércio varejista especializado de equipamentos de telefonia e comunicação');
INSERT INTO migra_cnae VALUES ('G.47.53-9/00',4,'Comércio varejista especializado de eletrodomésticos e equipamentos de áudio e vídeo');
INSERT INTO migra_cnae VALUES ('G.47.53-9/00',5,'Comércio varejista especializado de eletrodomésticos e equipamentos de áudio e vídeo');
INSERT INTO migra_cnae VALUES ('G.47.54-7/00',4,'Comércio varejista especializado de móveis, colchoaria e artigos de iluminação');
INSERT INTO migra_cnae VALUES ('G.47.54-7/01',5,'Comércio varejista de móveis');
INSERT INTO migra_cnae VALUES ('G.47.54-7/02',5,'Comércio varejista de artigos de colchoaria');
INSERT INTO migra_cnae VALUES ('G.47.54-7/03',5,'Comércio varejista de artigos de iluminação');
INSERT INTO migra_cnae VALUES ('G.47.55-5/00',4,'Comércio varejista especializado de tecidos e artigos de cama, mesa e banho');
INSERT INTO migra_cnae VALUES ('G.47.55-5/01',5,'Comércio varejista de tecidos');
INSERT INTO migra_cnae VALUES ('G.47.55-5/02',5,'Comercio varejista de artigos de armarinho');
INSERT INTO migra_cnae VALUES ('G.47.55-5/03',5,'Comercio varejista de artigos de cama, mesa e banho');
INSERT INTO migra_cnae VALUES ('G.47.56-3/00',4,'Comércio varejista especializado de instrumentos musicais e acessórios');
INSERT INTO migra_cnae VALUES ('G.47.56-3/00',5,'Comércio varejista especializado de instrumentos musicais e acessórios');
INSERT INTO migra_cnae VALUES ('G.47.57-1/00',4,'Comércio varejista especializado de peças e acessórios para aparelhos eletroeletrônicos para uso doméstico, exceto informática e comunicação');
INSERT INTO migra_cnae VALUES ('G.47.57-1/00',5,'Comércio varejista especializado de peças e acessórios para aparelhos eletroeletrônicos para uso doméstico, exceto informática e comunicação');
INSERT INTO migra_cnae VALUES ('G.47.59-8/00',4,'Comércio varejista de artigos de uso doméstico não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.47.59-8/01',5,'Comércio varejista de artigos de tapeçaria, cortinas e persianas');
INSERT INTO migra_cnae VALUES ('G.47.59-8/99',5,'Comércio varejista de outros artigos de uso pessoal e doméstico não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.47.60-0/00',3,'Comércio varejista de artigos culturais, recreativos e esportivos');
INSERT INTO migra_cnae VALUES ('G.47.61-0/00',4,'Comércio varejista de livros, jornais, revistas e papelaria');
INSERT INTO migra_cnae VALUES ('G.47.61-0/01',5,'Comércio varejista de livros');
INSERT INTO migra_cnae VALUES ('G.47.61-0/02',5,'Comércio varejista de jornais e revistas');
INSERT INTO migra_cnae VALUES ('G.47.61-0/03',5,'Comércio varejista de artigos de papelaria');
INSERT INTO migra_cnae VALUES ('G.47.62-8/00',4,'Comércio varejista de discos, CDs, DVDs e fitas');
INSERT INTO migra_cnae VALUES ('G.47.62-8/00',5,'Comércio varejista de discos, CDs, DVDs e fitas');
INSERT INTO migra_cnae VALUES ('G.47.63-6/00',4,'Comércio varejista de artigos recreativos e esportivos');
INSERT INTO migra_cnae VALUES ('G.47.63-6/01',5,'Comércio varejista de brinquedos e artigos recreativos');
INSERT INTO migra_cnae VALUES ('G.47.63-6/02',5,'Comércio varejista de artigos esportivos');
INSERT INTO migra_cnae VALUES ('G.47.63-6/03',5,'Comércio varejista de bicicletas e triciclos; peças e acessórios');
INSERT INTO migra_cnae VALUES ('G.47.63-6/04',5,'Comércio varejista de artigos de caça, pesca e camping');
INSERT INTO migra_cnae VALUES ('G.47.63-6/05',5,'Comércio varejista de embarcações e outros veículos recreativos; peças e acessórios');
INSERT INTO migra_cnae VALUES ('G.47.70-0/00',3,'Comércio varejista de produtos farmacêuticos, perfumaria e cosméticos e artigos médicos, ópticos e ortopédicos');
INSERT INTO migra_cnae VALUES ('G.47.71-7/00',4,'Comércio varejista de produtos farmacêuticos para uso humano e veterinário');
INSERT INTO migra_cnae VALUES ('G.47.71-7/01',5,'Comércio varejista de produtos farmacêuticos, sem manipulação de fórmulas');
INSERT INTO migra_cnae VALUES ('G.47.71-7/02',5,'Comércio varejista de produtos farmacêuticos, com manipulação de fórmulas');
INSERT INTO migra_cnae VALUES ('G.47.71-7/03',5,'Comércio varejista de produtos farmacêuticos homeopáticos');
INSERT INTO migra_cnae VALUES ('G.47.71-7/04',5,'Comércio varejista de medicamentos veterinários');
INSERT INTO migra_cnae VALUES ('G.47.72-5/00',4,'Comércio varejista de cosméticos, produtos de perfumaria e de higiene pessoal');
INSERT INTO migra_cnae VALUES ('G.47.72-5/00',5,'Comércio varejista de cosméticos, produtos de perfumaria e de higiene pessoal');
INSERT INTO migra_cnae VALUES ('G.47.73-3/00',4,'Comércio varejista de artigos médicos e ortopédicos');
INSERT INTO migra_cnae VALUES ('G.47.73-3/00',5,'Comércio varejista de artigos médicos e ortopédicos');
INSERT INTO migra_cnae VALUES ('G.47.74-1/00',4,'Comércio varejista de artigos de óptica');
INSERT INTO migra_cnae VALUES ('G.47.74-1/00',5,'Comércio varejista de artigos de óptica');
INSERT INTO migra_cnae VALUES ('G.47.80-0/00',3,'Comércio varejista de produtos novos não especificados anteriormente e de produtos usados');
INSERT INTO migra_cnae VALUES ('G.47.81-4/00',4,'Comércio varejista de artigos do vestuário e acessórios');
INSERT INTO migra_cnae VALUES ('G.47.81-4/00',5,'Comércio varejista de artigos do vestuário e acessórios');
INSERT INTO migra_cnae VALUES ('G.47.82-2/00',4,'Comércio varejista de calçados e artigos de viagem');
INSERT INTO migra_cnae VALUES ('G.47.82-2/01',5,'Comércio varejista de calçados');
INSERT INTO migra_cnae VALUES ('G.47.82-2/02',5,'Comércio varejista de artigos de viagem');
INSERT INTO migra_cnae VALUES ('G.47.83-1/00',4,'Comércio varejista de jóias e relógios');
INSERT INTO migra_cnae VALUES ('G.47.83-1/01',5,'Comércio varejista de artigos de joalheria');
INSERT INTO migra_cnae VALUES ('G.47.83-1/02',5,'Comércio varejista de artigos de relojoaria');
INSERT INTO migra_cnae VALUES ('G.47.84-9/00',4,'Comércio varejista de gás liqüefeito de petróleo (GLP)');
INSERT INTO migra_cnae VALUES ('G.47.84-9/00',5,'Comércio varejista de gás liqüefeito de petróleo (GLP)');
INSERT INTO migra_cnae VALUES ('G.47.85-7/00',4,'Comércio varejista de artigos usados');
INSERT INTO migra_cnae VALUES ('G.47.85-7/01',5,'Comércio varejista de antigüidades');
INSERT INTO migra_cnae VALUES ('G.47.85-7/99',5,'Comércio varejista de outros artigos usados');
INSERT INTO migra_cnae VALUES ('G.47.89-0/00',4,'Comércio varejista de outros produtos novos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.47.89-0/01',5,'Comércio varejista de suvenires, bijuterias e artesanatos');
INSERT INTO migra_cnae VALUES ('G.47.89-0/02',5,'Comércio varejista de plantas e flores naturais');
INSERT INTO migra_cnae VALUES ('G.47.89-0/03',5,'Comércio varejista de objetos de arte');
INSERT INTO migra_cnae VALUES ('G.47.89-0/04',5,'Comércio varejista de animais vivos e de artigos e alimentos para animais de estimação');
INSERT INTO migra_cnae VALUES ('G.47.89-0/05',5,'Comércio varejista de produtos saneantes domissanitários');
INSERT INTO migra_cnae VALUES ('G.47.89-0/06',5,'Comércio varejista de fogos de artifício e artigos pirotécnicos');
INSERT INTO migra_cnae VALUES ('G.47.89-0/07',5,'Comércio varejista de equipamentos para escritório');
INSERT INTO migra_cnae VALUES ('G.47.89-0/08',5,'Comércio varejista de artigos fotográficos e para filmagem');
INSERT INTO migra_cnae VALUES ('G.47.89-0/09',5,'Comércio varejista de armas e munições');
INSERT INTO migra_cnae VALUES ('G.47.89-0/99',5,'Comércio varejista de outros produtos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('G.47.90-0/00',4,'Comércio ambulante e outros tipos de comércio varejista');
INSERT INTO migra_cnae VALUES ('G.47.90-3/00',5,'Comércio ambulante e outros tipos de comércio varejista');
INSERT INTO migra_cnae VALUES ('H.00.00-0/00',1,'TRANSPORTE, ARMAZENAGEM E CORREIO');
INSERT INTO migra_cnae VALUES ('H.49.00-0/00',2,'TRANSPORTE TERRESTRE');
INSERT INTO migra_cnae VALUES ('H.49.10-0/00',3,'Transporte ferroviário e metroferroviário');
INSERT INTO migra_cnae VALUES ('H.49.11-6/00',4,'Transporte ferroviário de carga');
INSERT INTO migra_cnae VALUES ('H.49.11-6/00',5,'Transporte ferroviário de carga');
INSERT INTO migra_cnae VALUES ('H.49.12-4/00',4,'Transporte metroferroviário de passageiros');
INSERT INTO migra_cnae VALUES ('H.49.12-4/01',5,'Transporte ferroviário de passageiros intermunicipal e interestadual');
INSERT INTO migra_cnae VALUES ('H.49.12-4/02',5,'Transporte ferroviário de passageiros municipal e em região metropolitana');
INSERT INTO migra_cnae VALUES ('H.49.12-4/03',5,'Transporte metroviário');
INSERT INTO migra_cnae VALUES ('H.49.20-0/00',3,'Transporte rodoviário de passageiros');
INSERT INTO migra_cnae VALUES ('H.49.21-3/00',4,'Transporte rodoviário coletivo de passageiros, com itinerário fixo, municipal e em região metropolitana');
INSERT INTO migra_cnae VALUES ('H.49.21-3/01',5,'Transporte rodoviário coletivo de passageiros, com itinerário fixo, municipal');
INSERT INTO migra_cnae VALUES ('H.49.21-3/02',5,'Transporte rodoviário coletivo de passageiros, com itinerário fixo, intermunicipal em região metropolitana');
INSERT INTO migra_cnae VALUES ('H.49.22-1/00',4,'Transporte rodoviário coletivo de passageiros, com itinerário fixo, intermunicipal, interestadual e internacional');
INSERT INTO migra_cnae VALUES ('H.49.22-1/01',5,'Transporte rodoviário coletivo de passageiros, com itinerário fixo, intermunicipal, exceto em região metropolitana');
INSERT INTO migra_cnae VALUES ('H.49.22-1/02',5,'Transporte rodoviário coletivo de passageiros, com itinerário fixo, interestadual');
INSERT INTO migra_cnae VALUES ('H.49.22-1/03',5,'Transporte rodoviário coletivo de passageiros, com itinerário fixo, internacional');
INSERT INTO migra_cnae VALUES ('H.49.23-0/00',4,'Transporte rodoviário de táxi');
INSERT INTO migra_cnae VALUES ('H.49.23-0/01',5,'Serviço de táxi');
INSERT INTO migra_cnae VALUES ('H.49.23-0/02',5,'Serviço de transporte de passageiros - locação de automóveis com motorista');
INSERT INTO migra_cnae VALUES ('H.49.24-8/00',4,'Transporte escolar');
INSERT INTO migra_cnae VALUES ('H.49.24-8/00',5,'Transporte escolar');
INSERT INTO migra_cnae VALUES ('H.49.29-9/00',4,'Transporte rodoviário coletivo de passageiros, sob regime de fretamento, e outros transportes rodoviários não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('H.49.29-9/01',5,'Transporte rodoviário coletivo de passageiros, sob regime de fretamento, municipal');
INSERT INTO migra_cnae VALUES ('H.49.29-9/02',5,'Transporte rodoviário coletivo de passageiros, sob regime de fretamento, intermunicipal, interestadual e internacional');
INSERT INTO migra_cnae VALUES ('H.49.29-9/03',5,'Organização de excursões em veículos rodoviários próprios, municipal');
INSERT INTO migra_cnae VALUES ('H.49.29-9/04',5,'Organização de excursões em veículos rodoviários próprios, intermunicipal, interestadual e internacional');
INSERT INTO migra_cnae VALUES ('H.49.29-9/99',5,'Outros transportes rodoviários de passageiros não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('H.49.30-0/00',3,'Transporte rodoviário de carga');
INSERT INTO migra_cnae VALUES ('H.49.30-2/00',4,'Transporte rodoviário de carga');
INSERT INTO migra_cnae VALUES ('H.49.30-2/01',5,'Transporte rodoviário de carga, exceto produtos perigosos e mudanças, municipal');
INSERT INTO migra_cnae VALUES ('H.49.30-2/02',5,'Transporte rodoviário de carga, exceto produtos perigosos e mudanças, intermunicipal, interestadual e internacional');
INSERT INTO migra_cnae VALUES ('H.49.30-2/03',5,'Transporte rodoviário de produtos perigosos');
INSERT INTO migra_cnae VALUES ('H.49.30-2/04',5,'Transporte rodoviário de mudanças');
INSERT INTO migra_cnae VALUES ('H.49.40-0/00',3,'Transporte dutoviário');
INSERT INTO migra_cnae VALUES ('H.49.40-0/00',4,'Transporte dutoviário');
INSERT INTO migra_cnae VALUES ('H.49.40-0/00',5,'Transporte dutoviário');
INSERT INTO migra_cnae VALUES ('H.49.50-0/00',3,'Trens turísticos, teleféricos e similares');
INSERT INTO migra_cnae VALUES ('H.49.50-7/00',4,'Trens turísticos, teleféricos e similares');
INSERT INTO migra_cnae VALUES ('H.49.50-7/00',5,'Trens turísticos, teleféricos e similares');
INSERT INTO migra_cnae VALUES ('H.50.00-0/00',2,'TRANSPORTE AQUAVIÁRIO');
INSERT INTO migra_cnae VALUES ('H.50.10-0/00',3,'Transporte marítimo de cabotagem e longo curso');
INSERT INTO migra_cnae VALUES ('H.50.11-4/00',4,'Transporte marítimo de cabotagem');
INSERT INTO migra_cnae VALUES ('H.50.11-4/01',5,'Transporte marítimo de cabotagem - Carga');
INSERT INTO migra_cnae VALUES ('H.50.11-4/02',5,'Transporte marítimo de cabotagem - passageiros');
INSERT INTO migra_cnae VALUES ('H.50.12-2/00',4,'Transporte marítimo de longo curso');
INSERT INTO migra_cnae VALUES ('H.50.12-2/01',5,'Transporte marítimo de longo curso - Carga');
INSERT INTO migra_cnae VALUES ('H.50.12-2/02',5,'Transporte marítimo de longo curso - Passageiros');
INSERT INTO migra_cnae VALUES ('H.50.20-0/00',3,'Transporte por navegação interior');
INSERT INTO migra_cnae VALUES ('H.50.21-1/00',4,'Transporte por navegação interior de carga');
INSERT INTO migra_cnae VALUES ('H.50.21-1/01',5,'Transporte por navegação interior de carga, municipal, exceto travessia');
INSERT INTO migra_cnae VALUES ('H.50.21-1/02',5,'Transporte por navegação interior de carga, intermunicipal, interestadual e internacional, exceto travessia');
INSERT INTO migra_cnae VALUES ('H.50.22-0/00',4,'Transporte por navegação interior de passageiros em linhas regulares');
INSERT INTO migra_cnae VALUES ('H.50.22-0/01',5,'Transporte por navegação interior de passageiros em linhas regulares, municipal, exceto travessia');
INSERT INTO migra_cnae VALUES ('H.50.22-0/02',5,'Transporte por navegação interior de passageiros em linhas regulares, intermunicipal, interestadual e internacional, exceto travessia');
INSERT INTO migra_cnae VALUES ('H.50.30-0/00',3,'Navegação de apoio');
INSERT INTO migra_cnae VALUES ('H.50.30-1/00',4,'Navegação de apoio');
INSERT INTO migra_cnae VALUES ('H.50.30-1/01',5,'Navegação de apoio marítimo');
INSERT INTO migra_cnae VALUES ('H.50.30-1/02',5,'Navegação de apoio portuário');
INSERT INTO migra_cnae VALUES ('H.50.30-1/03',5,'Serviço de rebocadores e empurradores');
INSERT INTO migra_cnae VALUES ('H.50.90-0/00',3,'Outros transportes aquaviários');
INSERT INTO migra_cnae VALUES ('H.50.91-2/00',4,'Transporte por navegação de travessia');
INSERT INTO migra_cnae VALUES ('H.50.91-2/01',5,'Transporte por navegação de travessia, municipal');
INSERT INTO migra_cnae VALUES ('H.50.91-2/02',5,'Transporte por navegação de travessia intermunicipal, interestadual e internacional');
INSERT INTO migra_cnae VALUES ('H.50.99-8/00',4,'Transportes aquaviários não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('H.50.99-8/01',5,'Transporte aquaviário para passeios turísticos');
INSERT INTO migra_cnae VALUES ('H.50.99-8/99',5,'Outros transportes aquaviários não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('H.51.00-0/00',2,'TRANSPORTE AÉREO');
INSERT INTO migra_cnae VALUES ('H.51.10-0/00',3,'Transporte aéreo de passageiros');
INSERT INTO migra_cnae VALUES ('H.51.11-1/00',4,'Transporte aéreo de passageiros regular');
INSERT INTO migra_cnae VALUES ('H.51.11-1/00',5,'Transporte aéreo de passageiros regular');
INSERT INTO migra_cnae VALUES ('H.51.12-9/00',4,'Transporte aéreo de passageiros não-regular');
INSERT INTO migra_cnae VALUES ('H.51.12-9/01',5,'Serviço de táxi aéreo e locação de aeronaves com tripulação');
INSERT INTO migra_cnae VALUES ('H.51.12-9/99',5,'Outros serviços de transporte aéreo de passageiros não-regular');
INSERT INTO migra_cnae VALUES ('H.51.20-0/00',3,'Transporte aéreo de carga');
INSERT INTO migra_cnae VALUES ('H.51.20-0/00',4,'Transporte aéreo de carga');
INSERT INTO migra_cnae VALUES ('H.51.20-0/00',5,'Transporte aéreo de carga');
INSERT INTO migra_cnae VALUES ('H.51.30-0/00',3,'Transporte espacial');
INSERT INTO migra_cnae VALUES ('H.51.30-7/00',4,'Transporte espacial');
INSERT INTO migra_cnae VALUES ('H.51.30-7/00',5,'Transporte espacial');
INSERT INTO migra_cnae VALUES ('H.52.00-0/00',2,'ARMAZENAMENTO E ATIVIDADES AUXILIARES DOS TRANSPORTES');
INSERT INTO migra_cnae VALUES ('H.52.10-0/00',3,'Armazenamento, carga e descarga');
INSERT INTO migra_cnae VALUES ('H.52.11-7/00',4,'Armazenamento');
INSERT INTO migra_cnae VALUES ('H.52.11-7/01',5,'Armazéns gerais - emissão de warrant');
INSERT INTO migra_cnae VALUES ('H.52.11-7/02',5,'Guarda-móveis');
INSERT INTO migra_cnae VALUES ('H.52.11-7/99',5,'Depósitos de mercadorias para terceiros, exceto armazéns gerais e guarda-móveis');
INSERT INTO migra_cnae VALUES ('H.52.12-5/00',4,'Carga e descarga');
INSERT INTO migra_cnae VALUES ('H.52.12-5/00',5,'Carga e descarga');
INSERT INTO migra_cnae VALUES ('H.52.20-0/00',3,'Atividades auxiliares dos transportes terrestres');
INSERT INTO migra_cnae VALUES ('H.52.21-4/00',4,'Concessionárias de rodovias, pontes, túneis e serviços relacionados');
INSERT INTO migra_cnae VALUES ('H.52.21-4/00',5,'Concessionárias de rodovias, pontes, túneis e serviços relacionados');
INSERT INTO migra_cnae VALUES ('H.52.22-2/00',4,'Terminais rodoviários e ferroviários');
INSERT INTO migra_cnae VALUES ('H.52.22-2/00',5,'Terminais rodoviários e ferroviários');
INSERT INTO migra_cnae VALUES ('H.52.23-1/00',4,'Estacionamento de veículos');
INSERT INTO migra_cnae VALUES ('H.52.23-1/00',5,'Estacionamento de veículos');
INSERT INTO migra_cnae VALUES ('H.52.29-0/00',4,'Atividades auxiliares dos transportes terrestres não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('H.52.29-0/01',5,'Serviços de apoio ao transporte por táxi, inclusive centrais de chamada');
INSERT INTO migra_cnae VALUES ('H.52.29-0/02',5,'Serviços de reboque de veículos');
INSERT INTO migra_cnae VALUES ('H.52.29-0/99',5,'Outras atividades auxiliares dos transportes terrestres não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('H.52.30-0/00',3,'Atividades auxiliares dos transportes aquaviários');
INSERT INTO migra_cnae VALUES ('H.52.31-1/00',4,'Gestão de portos e terminais');
INSERT INTO migra_cnae VALUES ('H.52.31-1/01',5,'Administração da infra-estrutura portuária');
INSERT INTO migra_cnae VALUES ('H.52.31-1/02',5,'Atividades do Operador Portuário');
INSERT INTO migra_cnae VALUES ('H.52.31-1/03',5,'Gestão de terminais aquaviários ');
INSERT INTO migra_cnae VALUES ('H.52.32-0/00',4,'Atividades de agenciamento marítimo');
INSERT INTO migra_cnae VALUES ('H.52.32-0/00',5,'Atividades de agenciamento marítimo');
INSERT INTO migra_cnae VALUES ('H.52.39-7/00',4,'Atividades auxiliares dos transportes aquaviários não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('H.52.39-7/01',5,'Serviços de praticagem');
INSERT INTO migra_cnae VALUES ('H.52.39-7/99',5,'Atividades auxiliares dos transportes aquaviários não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('H.52.40-0/00',3,'Atividades auxiliares dos transportes aéreos');
INSERT INTO migra_cnae VALUES ('H.52.40-1/00',4,'Atividades auxiliares dos transportes aéreos');
INSERT INTO migra_cnae VALUES ('H.52.40-1/01',5,'Operação dos aeroportos e campos de aterrissagem');
INSERT INTO migra_cnae VALUES ('H.52.40-1/99',5,'Atividades auxiliares dos transportes aéreos, exceto operação dos aeroportos e campos de aterrissagem');
INSERT INTO migra_cnae VALUES ('H.52.50-0/00',3,'Atividades relacionadas à organização do transporte de carga');
INSERT INTO migra_cnae VALUES ('H.52.50-8/00',4,'Atividades relacionadas à organização do transporte de carga');
INSERT INTO migra_cnae VALUES ('H.52.50-8/01',5,'Comissaria de despachos');
INSERT INTO migra_cnae VALUES ('H.52.50-8/02',5,'Atividades de despachantes aduaneiros');
INSERT INTO migra_cnae VALUES ('H.52.50-8/03',5,'Agenciamento de cargas, exceto para o transporte marítimo');
INSERT INTO migra_cnae VALUES ('H.52.50-8/04',5,'Organização logística do transporte de carga');
INSERT INTO migra_cnae VALUES ('H.52.50-8/05',5,'Operador de transporte multimodal - OTM');
INSERT INTO migra_cnae VALUES ('H.53.00-0/00',2,'CORREIO E OUTRAS ATIVIDADES DE ENTREGA');
INSERT INTO migra_cnae VALUES ('H.53.10-0/00',3,'Atividades de Correio');
INSERT INTO migra_cnae VALUES ('H.53.10-5/00',4,'Atividades de Correio');
INSERT INTO migra_cnae VALUES ('H.53.10-5/01',5,'Atividades do Correio Nacional');
INSERT INTO migra_cnae VALUES ('H.53.10-5/02',5,'Atividades de franqueadas e permissionárias do Correio Nacional');
INSERT INTO migra_cnae VALUES ('H.53.20-0/00',3,'Atividades de malote e de entrega');
INSERT INTO migra_cnae VALUES ('H.53.20-2/00',4,'Atividades de malote e de entrega');
INSERT INTO migra_cnae VALUES ('H.53.20-2/01',5,'Serviços de malote não realizados pelo Correio Nacional');
INSERT INTO migra_cnae VALUES ('H.53.20-2/02',5,'Serviços de entrega rápida');
INSERT INTO migra_cnae VALUES ('I.00.00-0/00',1,'ALOJAMENTO E ALIMENTAÇÃO');
INSERT INTO migra_cnae VALUES ('I.55.00-0/00',2,'ALOJAMENTO');
INSERT INTO migra_cnae VALUES ('I.55.10-0/00',3,'Hotéis e similares');
INSERT INTO migra_cnae VALUES ('I.55.10-8/00',4,'Hotéis e similares');
INSERT INTO migra_cnae VALUES ('I.55.10-8/01',5,'Hotéis');
INSERT INTO migra_cnae VALUES ('I.55.10-8/02',5,'Apart-hotéis');
INSERT INTO migra_cnae VALUES ('I.55.10-8/03',5,'Motéis');
INSERT INTO migra_cnae VALUES ('I.55.90-0/00',3,'Outros tipos de alojamento não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('I.55.90-6/00',4,'Outros tipos de alojamento não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('I.55.90-6/01',5,'Albergues, exceto assistenciais');
INSERT INTO migra_cnae VALUES ('I.55.90-6/02',5,'Campings');
INSERT INTO migra_cnae VALUES ('I.55.90-6/03',5,'Pensões (alojamento)');
INSERT INTO migra_cnae VALUES ('I.55.90-6/99',5,'Outros alojamentos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('I.56.00-0/00',2,'ALIMENTAÇÃO');
INSERT INTO migra_cnae VALUES ('I.56.10-0/00',3,'Restaurantes e outros serviços de alimentação e bebidas');
INSERT INTO migra_cnae VALUES ('I.56.11-2/00',4,'Restaurantes e outros estabelecimentos de serviços de alimentação e bebidas');
INSERT INTO migra_cnae VALUES ('I.56.11-2/01',5,'Restaurantes e similares');
INSERT INTO migra_cnae VALUES ('I.56.11-2/02',5,'Bares e outros estabelecimentos especializados em servir bebidas');
INSERT INTO migra_cnae VALUES ('I.56.11-2/03',5,'Lanchonetes, casas de chá, de sucos e similares');
INSERT INTO migra_cnae VALUES ('I.56.12-1/00',4,'Serviços ambulantes de alimentação');
INSERT INTO migra_cnae VALUES ('I.56.12-1/00',5,'Serviços ambulantes de alimentação');
INSERT INTO migra_cnae VALUES ('I.56.20-0/00',3,'Serviços de catering, bufê e outros serviços de comida preparada');
INSERT INTO migra_cnae VALUES ('I.56.20-1/00',4,'Serviços de catering, bufê e outros serviços de comida preparada');
INSERT INTO migra_cnae VALUES ('I.56.20-1/01',5,'Fornecimento de alimentos preparados preponderantemente para empresas');
INSERT INTO migra_cnae VALUES ('I.56.20-1/02',5,'Serviços de alimentação para eventos e recepções - bufê');
INSERT INTO migra_cnae VALUES ('I.56.20-1/03',5,'Cantinas - serviços de alimentação privativos');
INSERT INTO migra_cnae VALUES ('I.56.20-1/04',5,'Fornecimento de alimentos preparados preponderantemente para consumo domiciliar');
INSERT INTO migra_cnae VALUES ('J.00.00-0/00',1,'INFORMAÇÃO E COMUNICAÇÃO');
INSERT INTO migra_cnae VALUES ('J.58.00-0/00',2,'EDIÇÃO E EDIÇÃO INTEGRADA À IMPRESSÃO');
INSERT INTO migra_cnae VALUES ('J.58.10-0/00',3,'Edição de livros, jornais, revistas e outras atividades de edição');
INSERT INTO migra_cnae VALUES ('J.58.11-5/00',4,'Edição de livros');
INSERT INTO migra_cnae VALUES ('J.58.11-5/00',5,'Edição de livros');
INSERT INTO migra_cnae VALUES ('J.58.12-3/00',4,'Edição de jornais');
INSERT INTO migra_cnae VALUES ('J.58.12-3/01',5,'Edição de jornais diários');
INSERT INTO migra_cnae VALUES ('J.58.12-3/02',5,'Edição de jornais não diários');
INSERT INTO migra_cnae VALUES ('J.58.13-1/00',4,'Edição de revistas');
INSERT INTO migra_cnae VALUES ('J.58.13-1/00',5,'Edição de revistas');
INSERT INTO migra_cnae VALUES ('J.58.19-1/00',4,'Edição de cadastros, listas e outros produtos gráficos');
INSERT INTO migra_cnae VALUES ('J.58.19-1/00',5,'Edição de cadastros, listas e outros produtos gráficos');
INSERT INTO migra_cnae VALUES ('J.58.20-0/00',3,'Edição integrada à impressão de livros, jornais, revistas e outras publicações');
INSERT INTO migra_cnae VALUES ('J.58.21-2/00',4,'Edição integrada à impressão de livros');
INSERT INTO migra_cnae VALUES ('J.58.21-2/00',5,'Edição integrada à impressão de livros');
INSERT INTO migra_cnae VALUES ('J.58.22-1/00',4,'Edição integrada à impressão de jornais');
INSERT INTO migra_cnae VALUES ('J.58.22-1/01',5,'Edição integrada à impressão de jornais diários');
INSERT INTO migra_cnae VALUES ('J.58.22-1/02',5,'Edição integrada à impressão de jornais não diários');
INSERT INTO migra_cnae VALUES ('J.58.23-9/00',4,'Edição integrada à impressão de revistas');
INSERT INTO migra_cnae VALUES ('J.58.23-9/00',5,'Edição integrada à impressão de revistas');
INSERT INTO migra_cnae VALUES ('J.58.29-8/00',4,'Edição integrada à impressão de cadastros, listas e outros produtos gráficos');
INSERT INTO migra_cnae VALUES ('J.58.29-8/00',5,'Edição integrada à impressão de cadastros, listas e outros produtos gráficos');
INSERT INTO migra_cnae VALUES ('J.59.00-0/00',2,'ATIVIDADES CINEMATOGRÁFICAS, PRODUÇÃO DE VÍDEOS E DE PROGRAMAS DE TELEVISÃO; GRAVAÇÃO DE SOM E EDIÇÃO DE MÚSICA');
INSERT INTO migra_cnae VALUES ('J.59.10-0/00',3,'Atividades cinematográficas, produção de vídeos e de programas de televisão');
INSERT INTO migra_cnae VALUES ('J.59.11-1/00',4,'Atividades de produção cinematográfica, de vídeos e de programas de televisão');
INSERT INTO migra_cnae VALUES ('J.59.11-1/01',5,'Estúdios cinematográficos');
INSERT INTO migra_cnae VALUES ('J.59.11-1/02',5,'Produção de filmes para publicidade');
INSERT INTO migra_cnae VALUES ('J.59.11-1/99',5,'Atividades de produção cinematográfica, de vídeos e de programas de televisão não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('J.59.12-0/00',4,'Atividades de pós-produção cinematográfica, de vídeos e de programas de televisão');
INSERT INTO migra_cnae VALUES ('J.59.12-0/01',5,'Serviços de dublagem');
INSERT INTO migra_cnae VALUES ('J.59.12-0/02',5,'Serviços de mixagem sonora em produção audiovisual');
INSERT INTO migra_cnae VALUES ('J.59.12-0/99',5,'Atividades de pós-produção cinematográfica, de vídeos e de programas de televisão não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('J.59.13-8/00',4,'Distribuição cinematográfica, de vídeo e de programas de televisão');
INSERT INTO migra_cnae VALUES ('J.59.13-8/00',5,'Distribuição cinematográfica, de vídeo e de programas de televisão');
INSERT INTO migra_cnae VALUES ('J.59.14-6/00',4,'Atividades de exibição cinematográfica');
INSERT INTO migra_cnae VALUES ('J.59.14-6/00',5,'Atividades de exibição cinematográfica');
INSERT INTO migra_cnae VALUES ('J.59.20-0/00',3,'Atividades de gravação de som e de edição de música');
INSERT INTO migra_cnae VALUES ('J.59.20-1/00',4,'Atividades de gravação de som e de edição de música');
INSERT INTO migra_cnae VALUES ('J.59.20-1/00',5,'Atividades de gravação de som e de edição de música');
INSERT INTO migra_cnae VALUES ('J.60.00-0/00',2,'ATIVIDADES DE RÁDIO E DE TELEVISÃO');
INSERT INTO migra_cnae VALUES ('J.60.10-0/00',3,'Atividades de rádio');
INSERT INTO migra_cnae VALUES ('J.60.10-1/00',4,'Atividades de rádio');
INSERT INTO migra_cnae VALUES ('J.60.10-1/00',5,'Atividades de rádio');
INSERT INTO migra_cnae VALUES ('J.60.20-0/00',3,'Atividades de televisão');
INSERT INTO migra_cnae VALUES ('J.60.21-7/00',4,'Atividades de televisão aberta');
INSERT INTO migra_cnae VALUES ('J.60.21-7/00',5,'Atividades de televisão aberta');
INSERT INTO migra_cnae VALUES ('J.60.22-5/00',4,'Programadoras e atividades relacionadas à televisão por assinatura');
INSERT INTO migra_cnae VALUES ('J.60.22-5/01',5,'Programadoras');
INSERT INTO migra_cnae VALUES ('J.60.22-5/02',5,'Atividades relacionadas à televisão por assinatura, exceto programadoras');
INSERT INTO migra_cnae VALUES ('J.61.00-0/00',2,'TELECOMUNICAÇÕES');
INSERT INTO migra_cnae VALUES ('J.61.10-0/00',3,'Telecomunicações por fio');
INSERT INTO migra_cnae VALUES ('J.61.10-8/00',4,'Telecomunicações por fio');
INSERT INTO migra_cnae VALUES ('J.61.10-8/01',5,'Serviços de telefonia fixa comutada - STFC');
INSERT INTO migra_cnae VALUES ('J.61.10-8/02',5,'Serviços de redes de transporte de telecomunicações - SRTT');
INSERT INTO migra_cnae VALUES ('J.61.10-8/03',5,'Serviços de comunicação multimídia - SCM');
INSERT INTO migra_cnae VALUES ('J.61.10-8/99',5,'Serviços de telecomunicações por fio não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('J.61.20-0/00',3,'Telecomunicações sem fio');
INSERT INTO migra_cnae VALUES ('J.61.20-5/00',4,'Telecomunicações sem fio');
INSERT INTO migra_cnae VALUES ('J.61.20-5/01',5,'Telefonia móvel celular');
INSERT INTO migra_cnae VALUES ('J.61.20-5/02',5,'Serviço móvel especializado - SME');
INSERT INTO migra_cnae VALUES ('J.61.20-5/99',5,'Serviços de telecomunicações sem fio não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('J.61.30-0/00',3,'Telecomunicações por satélite');
INSERT INTO migra_cnae VALUES ('J.61.30-2/00',4,'Telecomunicações por satélite');
INSERT INTO migra_cnae VALUES ('J.61.30-2/00',5,'Telecomunicações por satélite');
INSERT INTO migra_cnae VALUES ('J.61.40-0/00',3,'Operadoras de televisão por assinatura');
INSERT INTO migra_cnae VALUES ('J.61.41-8/00',4,'Operadoras de televisão por assinatura por cabo');
INSERT INTO migra_cnae VALUES ('J.61.41-8/00',5,'Operadoras de televisão por assinatura por cabo');
INSERT INTO migra_cnae VALUES ('J.61.42-6/00',4,'Operadoras de televisão por assinatura por microondas');
INSERT INTO migra_cnae VALUES ('J.61.42-6/00',5,'Operadoras de televisão por assinatura por microondas');
INSERT INTO migra_cnae VALUES ('J.61.43-4/00',4,'Operadoras de televisão por assinatura por satélite');
INSERT INTO migra_cnae VALUES ('J.61.43-4/00',5,'Operadoras de televisão por assinatura por satélite');
INSERT INTO migra_cnae VALUES ('J.61.90-0/00',3,'Outras atividades de telecomunicações');
INSERT INTO migra_cnae VALUES ('J.61.90-6/00',4,'Outras atividades de telecomunicações');
INSERT INTO migra_cnae VALUES ('J.61.90-6/01',5,'Provedores de acesso às redes de comunicações');
INSERT INTO migra_cnae VALUES ('J.61.90-6/02',5,'Provedores de voz sobre protocolo internet - VOIP');
INSERT INTO migra_cnae VALUES ('J.61.90-6/99',5,'Outras atividades de telecomunicações não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('J.62.00-0/00',2,'ATIVIDADES DOS SERVIÇOS DE TECNOLOGIA DA INFORMAÇÃO');
INSERT INTO migra_cnae VALUES ('J.62.00-0/00',3,'Atividades dos serviços de tecnologia da informação');
INSERT INTO migra_cnae VALUES ('J.62.01-5/00',4,'Desenvolvimento de programas de computador sob encomenda');
INSERT INTO migra_cnae VALUES ('J.62.01-5/01',5,'Desenvolvimento de programas de computador sob encomenda');
INSERT INTO migra_cnae VALUES ('J.62.01-5/02',5,'Web design');
INSERT INTO migra_cnae VALUES ('J.62.02-3/00',4,'Desenvolvimento e licenciamento de programas de computador customizáveis');
INSERT INTO migra_cnae VALUES ('J.62.02-3/00',5,'Desenvolvimento e licenciamento de programas de computador customizáveis');
INSERT INTO migra_cnae VALUES ('J.62.03-1/00',4,'Desenvolvimento e licenciamento de programas de computador não-customizáveis');
INSERT INTO migra_cnae VALUES ('J.62.03-1/00',5,'Desenvolvimento e licenciamento de programas de computador não-customizáveis');
INSERT INTO migra_cnae VALUES ('J.62.04-0/00',4,'Consultoria em tecnologia da informação');
INSERT INTO migra_cnae VALUES ('J.62.04-0/00',5,'Consultoria em tecnologia da informação');
INSERT INTO migra_cnae VALUES ('J.62.09-1/00',4,'Suporte técnico, manutenção e outros serviços em tecnologia da informação');
INSERT INTO migra_cnae VALUES ('J.62.09-1/00',5,'Suporte técnico, manutenção e outros serviços em tecnologia da informação');
INSERT INTO migra_cnae VALUES ('J.63.00-0/00',2,'ATIVIDADES DE PRESTAÇÃO DE SERVIÇOS DE INFORMAÇÃO');
INSERT INTO migra_cnae VALUES ('J.63.10-0/00',3,'Tratamento de dados, hospedagem na internet e outras atividades relacionadas');
INSERT INTO migra_cnae VALUES ('J.63.11-9/00',4,'Tratamento de dados, provedores de serviços de aplicação e serviços de hospedagem na internet');
INSERT INTO migra_cnae VALUES ('J.63.11-9/00',5,'Tratamento de dados, provedores de serviços de aplicação e serviços de hospedagem na internet');
INSERT INTO migra_cnae VALUES ('J.63.19-4/00',4,'Portais, provedores de conteúdo e outros serviços de informação na internet');
INSERT INTO migra_cnae VALUES ('J.63.19-4/00',5,'Portais, provedores de conteúdo e outros serviços de informação na internet');
INSERT INTO migra_cnae VALUES ('J.63.90-0/00',3,'Outras atividades de prestação de serviços de informação');
INSERT INTO migra_cnae VALUES ('J.63.91-7/00',4,'Agências de notícias');
INSERT INTO migra_cnae VALUES ('J.63.91-7/00',5,'Agências de notícias');
INSERT INTO migra_cnae VALUES ('J.63.99-2/00',4,'Outras atividades de prestação de serviços de informação não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('J.63.99-2/00',5,'Outras atividades de prestação de serviços de informação não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('K.00.00-0/00',1,'ATIVIDADES FINANCEIRAS, DE SEGUROS E SERVIÇOS RELACIONADOS');
INSERT INTO migra_cnae VALUES ('K.64.00-0/00',2,'ATIVIDADES DE SERVIÇOS FINANCEIROS');
INSERT INTO migra_cnae VALUES ('K.64.10-0/00',3,'Banco Central');
INSERT INTO migra_cnae VALUES ('K.64.10-7/00',4,'Banco Central');
INSERT INTO migra_cnae VALUES ('K.64.10-7/00',5,'Banco Central');
INSERT INTO migra_cnae VALUES ('K.64.20-0/00',3,'Intermediação monetária - depósitos à vista');
INSERT INTO migra_cnae VALUES ('K.64.21-2/00',4,'Bancos comerciais');
INSERT INTO migra_cnae VALUES ('K.64.21-2/00',5,'Bancos comerciais');
INSERT INTO migra_cnae VALUES ('K.64.22-1/00',4,'Bancos múltiplos, com carteira comercial');
INSERT INTO migra_cnae VALUES ('K.64.22-1/00',5,'Bancos múltiplos, com carteira comercial');
INSERT INTO migra_cnae VALUES ('K.64.23-9/00',4,'Caixas econômicas');
INSERT INTO migra_cnae VALUES ('K.64.23-9/00',5,'Caixas econômicas');
INSERT INTO migra_cnae VALUES ('K.64.24-7/00',4,'Crédito cooperativo');
INSERT INTO migra_cnae VALUES ('K.64.24-7/01',5,'Bancos cooperativos');
INSERT INTO migra_cnae VALUES ('K.64.24-7/02',5,'Cooperativas centrais de crédito');
INSERT INTO migra_cnae VALUES ('K.64.24-7/03',5,'Cooperativas de crédito mútuo');
INSERT INTO migra_cnae VALUES ('K.64.24-7/04',5,'Cooperativas de crédito rural');
INSERT INTO migra_cnae VALUES ('K.64.30-0/00',3,'Intermediação não-monetária - outros instrumentos de captação');
INSERT INTO migra_cnae VALUES ('K.64.31-0/00',4,'Bancos múltiplos, sem carteira comercial');
INSERT INTO migra_cnae VALUES ('K.64.31-0/00',5,'Bancos múltiplos, sem carteira comercial');
INSERT INTO migra_cnae VALUES ('K.64.32-8/00',4,'Bancos de investimento');
INSERT INTO migra_cnae VALUES ('K.64.32-8/00',5,'Bancos de investimento');
INSERT INTO migra_cnae VALUES ('K.64.33-6/00',4,'Bancos de desenvolvimento');
INSERT INTO migra_cnae VALUES ('K.64.33-6/00',5,'Bancos de desenvolvimento');
INSERT INTO migra_cnae VALUES ('K.64.34-4/00',4,'Agências de fomento');
INSERT INTO migra_cnae VALUES ('K.64.34-4/00',5,'Agências de fomento');
INSERT INTO migra_cnae VALUES ('K.64.35-2/00',4,'Crédito imobiliário');
INSERT INTO migra_cnae VALUES ('K.64.35-2/01',5,'Sociedades de crédito imobiliário');
INSERT INTO migra_cnae VALUES ('K.64.35-2/02',5,'Associações de poupança e empréstimo');
INSERT INTO migra_cnae VALUES ('K.64.35-2/03',5,'Companhias hipotecárias');
INSERT INTO migra_cnae VALUES ('K.64.36-1/00',4,'Sociedades de crédito, financiamento e investimento - financeiras');
INSERT INTO migra_cnae VALUES ('K.64.36-1/00',5,'Sociedades de crédito, financiamento e investimento - financeiras');
INSERT INTO migra_cnae VALUES ('K.64.37-9/00',4,'Sociedades de crédito ao microempreendedor');
INSERT INTO migra_cnae VALUES ('K.64.37-9/00',5,'Sociedades de crédito ao microempreendedor');
INSERT INTO migra_cnae VALUES ('K.64.38-7/00',4,'Bancos de câmbio e outras instituições de intermediação não-monetária');
INSERT INTO migra_cnae VALUES ('K.64.38-7/01',5,'Bancos de câmbio');
INSERT INTO migra_cnae VALUES ('K.64.38-7/99',5,'Outras instituições de intermediação não-monetária não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('K.64.40-0/00',3,'Arrendamento mercantil');
INSERT INTO migra_cnae VALUES ('K.64.40-9/00',4,'Arrendamento mercantil');
INSERT INTO migra_cnae VALUES ('K.64.40-9/00',5,'Arrendamento mercantil');
INSERT INTO migra_cnae VALUES ('K.64.50-0/00',3,'Sociedades de capitalização');
INSERT INTO migra_cnae VALUES ('K.64.50-6/00',4,'Sociedades de capitalização');
INSERT INTO migra_cnae VALUES ('K.64.50-6/00',5,'Sociedades de capitalização');
INSERT INTO migra_cnae VALUES ('K.64.60-0/00',3,'Atividades de sociedades de participação');
INSERT INTO migra_cnae VALUES ('K.64.61-1/00',4,'Holdings de instituições financeiras');
INSERT INTO migra_cnae VALUES ('K.64.61-1/00',5,'Holdings de instituições financeiras');
INSERT INTO migra_cnae VALUES ('K.64.62-0/00',4,'Holdings de instituições não-financeiras');
INSERT INTO migra_cnae VALUES ('K.64.62-0/00',5,'Holdings de instituições não-financeiras');
INSERT INTO migra_cnae VALUES ('K.64.63-8/00',4,'Outras sociedades de participação, exceto holdings');
INSERT INTO migra_cnae VALUES ('K.64.63-8/00',5,'Outras sociedades de participação, exceto holdings');
INSERT INTO migra_cnae VALUES ('K.64.70-0/00',3,'Fundos de investimento');
INSERT INTO migra_cnae VALUES ('K.64.70-1/00',4,'Fundos de investimento');
INSERT INTO migra_cnae VALUES ('K.64.70-1/01',5,'Fundos de investimento, exceto previdenciários e imobiliários');
INSERT INTO migra_cnae VALUES ('K.64.70-1/02',5,'Fundos de investimento previdenciários');
INSERT INTO migra_cnae VALUES ('K.64.70-1/03',5,'Fundos de investimento imobiliários');
INSERT INTO migra_cnae VALUES ('K.64.90-0/00',3,'Atividades de serviços financeiros não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('K.64.91-3/00',4,'Sociedades de fomento mercantil - factoring');
INSERT INTO migra_cnae VALUES ('K.64.91-3/00',5,'Sociedades de fomento mercantil - factoring');
INSERT INTO migra_cnae VALUES ('K.64.92-1/00',4,'Securitização de créditos');
INSERT INTO migra_cnae VALUES ('K.64.92-1/00',5,'Securitização de créditos');
INSERT INTO migra_cnae VALUES ('K.64.93-0/00',4,'Administração de consórcios para aquisição de bens e direitos');
INSERT INTO migra_cnae VALUES ('K.64.93-0/00',5,'Administração de consórcios para aquisição de bens e direitos');
INSERT INTO migra_cnae VALUES ('K.64.99-9/00',4,'Outras atividades de serviços financeiros não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('K.64.99-9/01',5,'Clubes de investimento');
INSERT INTO migra_cnae VALUES ('K.64.99-9/02',5,'Sociedades de investimento');
INSERT INTO migra_cnae VALUES ('K.64.99-9/03',5,'Fundo garantidor de crédito');
INSERT INTO migra_cnae VALUES ('K.64.99-9/04',5,'Caixas de financiamento de corporações');
INSERT INTO migra_cnae VALUES ('K.64.99-9/05',5,'Concessão de crédito pelas OSCIP');
INSERT INTO migra_cnae VALUES ('K.64.99-9/99',5,'Outras atividades de serviços financeiros não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('K.65.00-0/00',2,'SEGUROS, RESSEGUROS, PREVIDÊNCIA COMPLEMENTAR E PLANOS DE SAÚDE');
INSERT INTO migra_cnae VALUES ('K.65.10-0/00',3,'Seguros de vida e não-vida');
INSERT INTO migra_cnae VALUES ('K.65.11-1/00',4,'Seguros de vida');
INSERT INTO migra_cnae VALUES ('K.65.11-1/01',5,'Sociedade seguradora de seguros vida');
INSERT INTO migra_cnae VALUES ('K.65.11-1/02',5,'Planos de auxílio-funeral');
INSERT INTO migra_cnae VALUES ('K.65.12-0/00',4,'Seguros não-vida');
INSERT INTO migra_cnae VALUES ('K.65.12-0/00',5,'Sociedade seguradora de seguros não vida');
INSERT INTO migra_cnae VALUES ('K.65.20-0/00',3,'Seguros-saúde');
INSERT INTO migra_cnae VALUES ('K.65.20-1/00',4,'Seguros-saúde');
INSERT INTO migra_cnae VALUES ('K.65.20-1/00',5,'Sociedade seguradora de seguros saúde');
INSERT INTO migra_cnae VALUES ('K.65.30-0/00',3,'Resseguros');
INSERT INTO migra_cnae VALUES ('K.65.30-8/00',4,'Resseguros');
INSERT INTO migra_cnae VALUES ('K.65.30-8/00',5,'Resseguros');
INSERT INTO migra_cnae VALUES ('K.65.40-0/00',3,'Previdência complementar');
INSERT INTO migra_cnae VALUES ('K.65.41-3/00',4,'Previdência complementar fechada');
INSERT INTO migra_cnae VALUES ('K.65.41-3/00',5,'Previdência complementar fechada');
INSERT INTO migra_cnae VALUES ('K.65.42-1/00',4,'Previdência complementar aberta');
INSERT INTO migra_cnae VALUES ('K.65.42-1/00',5,'Previdência complementar aberta');
INSERT INTO migra_cnae VALUES ('K.65.50-0/00',3,'Planos de saúde');
INSERT INTO migra_cnae VALUES ('K.65.50-2/00',4,'Planos de saúde');
INSERT INTO migra_cnae VALUES ('K.65.50-2/00',5,'Planos de saúde');
INSERT INTO migra_cnae VALUES ('K.66.00-0/00',2,'ATIVIDADES AUXILIARES DOS SERVIÇOS FINANCEIROS, SEGUROS, PREVIDÊNCIA COMPLEMENTAR E PLANOS DE SAÚDE');
INSERT INTO migra_cnae VALUES ('K.66.10-0/00',3,'Atividades auxiliares dos serviços financeiros');
INSERT INTO migra_cnae VALUES ('K.66.11-8/00',4,'Administração de bolsas e mercados de balcão organizados');
INSERT INTO migra_cnae VALUES ('K.66.11-8/01',5,'Bolsa de valores');
INSERT INTO migra_cnae VALUES ('K.66.11-8/02',5,'Bolsa de mercadorias');
INSERT INTO migra_cnae VALUES ('K.66.11-8/03',5,'Bolsa de mercadorias e futuros');
INSERT INTO migra_cnae VALUES ('K.66.11-8/04',5,'Administração de mercados de balcão organizados');
INSERT INTO migra_cnae VALUES ('K.66.12-6/00',4,'Atividades de intermediários em transações de títulos, valores mobiliários e mercadorias');
INSERT INTO migra_cnae VALUES ('K.66.12-6/01',5,'Corretoras de títulos e valores mobiliários');
INSERT INTO migra_cnae VALUES ('K.66.12-6/02',5,'Distribuidoras de títulos e valores mobiliários');
INSERT INTO migra_cnae VALUES ('K.66.12-6/03',5,'Corretoras de câmbio');
INSERT INTO migra_cnae VALUES ('K.66.12-6/04',5,'Corretoras de contratos de mercadorias');
INSERT INTO migra_cnae VALUES ('K.66.12-6/05',5,'Agentes de investimentos em aplicações financeiras');
INSERT INTO migra_cnae VALUES ('K.66.13-4/00',4,'Administração de cartões de crédito');
INSERT INTO migra_cnae VALUES ('K.66.13-4/00',5,'Administração de cartões de crédito');
INSERT INTO migra_cnae VALUES ('K.66.19-3/00',4,'Atividades auxiliares dos serviços financeiros não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('K.66.19-3/01',5,'Serviços de liquidação e custódia');
INSERT INTO migra_cnae VALUES ('K.66.19-3/02',5,'Correspondentes de instituições financeiras');
INSERT INTO migra_cnae VALUES ('K.66.19-3/03',5,'Representações de bancos estrangeiros');
INSERT INTO migra_cnae VALUES ('K.66.19-3/04',5,'Caixas eletrônicos');
INSERT INTO migra_cnae VALUES ('K.66.19-3/05',5,'Operadoras de cartões de débito');
INSERT INTO migra_cnae VALUES ('K.66.19-3/99',5,'Outras atividades auxiliares dos serviços financeiros não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('K.66.20-0/00',3,'Atividades auxiliares dos seguros, da previdência complementar e dos planos de saúde');
INSERT INTO migra_cnae VALUES ('K.66.21-5/00',4,'Avaliação de riscos e perdas');
INSERT INTO migra_cnae VALUES ('K.66.21-5/01',5,'Peritos e avaliadores de seguros');
INSERT INTO migra_cnae VALUES ('K.66.21-5/02',5,'Auditoria e consultoria atuarial');
INSERT INTO migra_cnae VALUES ('K.66.22-3/00',4,'Corretores e agentes de seguros, de planos de previdência complementar e de saúde');
INSERT INTO migra_cnae VALUES ('K.66.22-3/00',5,'Corretores e agentes de seguros, de planos de previdência complementar e de saúde');
INSERT INTO migra_cnae VALUES ('K.66.29-1/00',4,'Atividades auxiliares dos seguros, da previdência complementar e dos planos de saúde não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('K.66.29-1/00',5,'Atividades auxiliares dos seguros, da previdência complementar e dos planos de saúde não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('K.66.30-0/00',3,'Atividades de administração de fundos por contrato ou comissão');
INSERT INTO migra_cnae VALUES ('K.66.30-4/00',4,'Atividades de administração de fundos por contrato ou comissão');
INSERT INTO migra_cnae VALUES ('K.66.30-4/00',5,'Atividades de administração de fundos por contrato ou comissão');
INSERT INTO migra_cnae VALUES ('L.00.00-0/00',1,'ATIVIDADES IMOBILIÁRIAS');
INSERT INTO migra_cnae VALUES ('L.68.00-0/00',2,'ATIVIDADES IMOBILIÁRIAS');
INSERT INTO migra_cnae VALUES ('L.68.10-0/00',3,'Atividades imobiliárias de imóveis próprios');
INSERT INTO migra_cnae VALUES ('L.68.10-2/00',4,'Atividades imobiliárias de imóveis próprios');
INSERT INTO migra_cnae VALUES ('L.68.10-2/01',5,'Compra e venda de imóveis próprios');
INSERT INTO migra_cnae VALUES ('L.68.10-2/02',5,'Aluguel de imóveis próprios');
INSERT INTO migra_cnae VALUES ('L.68.10-2/03',5,'Loteamento de imóveis próprios');
INSERT INTO migra_cnae VALUES ('L.68.20-0/00',3,'Atividades imobiliárias por contrato ou comissão');
INSERT INTO migra_cnae VALUES ('L.68.21-8/00',4,'Intermediação na compra, venda e aluguel de imóveis');
INSERT INTO migra_cnae VALUES ('L.68.21-8/01',5,'Corretagem na compra e venda e avaliação de imóveis');
INSERT INTO migra_cnae VALUES ('L.68.21-8/02',5,'Corretagem no aluguel de imóveis');
INSERT INTO migra_cnae VALUES ('L.68.22-6/00',4,'Gestão e administração da propriedade imobiliária');
INSERT INTO migra_cnae VALUES ('L.68.22-6/00',5,'Gestão e administração da propriedade imobiliária');
INSERT INTO migra_cnae VALUES ('M.00.00-0/00',1,'ATIVIDADES PROFISSIONAIS, CIENTÍFICAS E TÉCNICAS');
INSERT INTO migra_cnae VALUES ('M.69.00-0/00',2,'ATIVIDADES JURÍDICAS, DE CONTABILIDADE E DE AUDITORIA');
INSERT INTO migra_cnae VALUES ('M.69.10-0/00',3,'Atividades jurídicas');
INSERT INTO migra_cnae VALUES ('M.69.11-7/00',4,'Atividades jurídicas, exceto cartórios');
INSERT INTO migra_cnae VALUES ('M.69.11-7/01',5,'Serviços advocatícios');
INSERT INTO migra_cnae VALUES ('M.69.11-7/02',5,'Atividades auxiliares da justiça');
INSERT INTO migra_cnae VALUES ('M.69.11-7/03',5,'Agente de propriedade industrial');
INSERT INTO migra_cnae VALUES ('M.69.12-5/00',4,'Cartórios');
INSERT INTO migra_cnae VALUES ('M.69.12-5/00',5,'Cartórios');
INSERT INTO migra_cnae VALUES ('M.69.20-0/00',3,'Atividades de contabilidade, consultoria e auditoria contábil e tributária');
INSERT INTO migra_cnae VALUES ('M.69.20-6/00',4,'Atividades de contabilidade, consultoria e auditoria contábil e tributária');
INSERT INTO migra_cnae VALUES ('M.69.20-6/01',5,'Atividades de contabilidade');
INSERT INTO migra_cnae VALUES ('M.69.20-6/02',5,'Atividades de consultoria e auditoria contábil e tributária');
INSERT INTO migra_cnae VALUES ('M.70.00-0/00',2,'ATIVIDADES DE SEDES DE EMPRESAS E DE CONSULTORIA EM GESTÃO EMPRESARIAL');
INSERT INTO migra_cnae VALUES ('M.70.10-0/00',3,'Sedes de empresas e unidades administrativas locais');
INSERT INTO migra_cnae VALUES ('M.70.10-7/00',4,'Sedes de empresas e unidades administrativas locais');
INSERT INTO migra_cnae VALUES ('M.70.10-7/00',5,'Sedes de empresas e unidades administrativas locais');
INSERT INTO migra_cnae VALUES ('M.70.20-0/00',3,'Atividades de consultoria em gestão empresarial');
INSERT INTO migra_cnae VALUES ('M.70.20-4/00',4,'Atividades de consultoria em gestão empresarial');
INSERT INTO migra_cnae VALUES ('M.70.20-4/00',5,'Atividades de consultoria em gestão empresarial, exceto consultoria técnica específica');
INSERT INTO migra_cnae VALUES ('M.71.00-0/00',2,'SERVIÇOS DE ARQUITETURA E ENGENHARIA; TESTES E ANÁLISES TÉCNICAS');
INSERT INTO migra_cnae VALUES ('M.71.10-0/00',3,'Serviços de arquitetura e engenharia e atividades técnicas relacionadas');
INSERT INTO migra_cnae VALUES ('M.71.11-1/00',4,'Serviços de arquitetura');
INSERT INTO migra_cnae VALUES ('M.71.11-1/00',5,'Serviços de arquitetura');
INSERT INTO migra_cnae VALUES ('M.71.12-0/00',4,'Serviços de engenharia');
INSERT INTO migra_cnae VALUES ('M.71.12-0/00',5,'Serviços de engenharia');
INSERT INTO migra_cnae VALUES ('M.71.19-7/00',4,'Atividades técnicas relacionadas à arquitetura e engenharia');
INSERT INTO migra_cnae VALUES ('M.71.19-7/01',5,'Serviços de cartografia, topografia e geodésia');
INSERT INTO migra_cnae VALUES ('M.71.19-7/02',5,'Atividades de estudos geológicos');
INSERT INTO migra_cnae VALUES ('M.71.19-7/03',5,'Serviços de desenho técnico relacionados à arquitetura e engenharia');
INSERT INTO migra_cnae VALUES ('M.71.19-7/04',5,'Serviços de perícia técnica relacionados à segurança do trabalho');
INSERT INTO migra_cnae VALUES ('M.71.19-7/99',5,'Atividades técnicas relacionadas à engenharia e arquitetura não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('M.71.20-0/00',3,'Testes e análises técnicas');
INSERT INTO migra_cnae VALUES ('M.71.20-1/00',4,'Testes e análises técnicas');
INSERT INTO migra_cnae VALUES ('M.71.20-1/00',5,'Testes e análises técnicas');
INSERT INTO migra_cnae VALUES ('M.72.00-0/00',2,'PESQUISA E DESENVOLVIMENTO CIENTÍFICO');
INSERT INTO migra_cnae VALUES ('M.72.10-0/00',3,'Pesquisa e desenvolvimento experimental em ciências físicas e naturais');
INSERT INTO migra_cnae VALUES ('M.72.10-0/00',4,'Pesquisa e desenvolvimento experimental em ciências físicas e naturais');
INSERT INTO migra_cnae VALUES ('M.72.10-0/00',5,'Pesquisa e desenvolvimento experimental em ciências físicas e naturais');
INSERT INTO migra_cnae VALUES ('M.72.20-0/00',3,'Pesquisa e desenvolvimento experimental em ciências sociais e humanas');
INSERT INTO migra_cnae VALUES ('M.72.20-7/00',4,'Pesquisa e desenvolvimento experimental em ciências sociais e humanas');
INSERT INTO migra_cnae VALUES ('M.72.20-7/00',5,'Pesquisa e desenvolvimento experimental em ciências sociais e humanas');
INSERT INTO migra_cnae VALUES ('M.73.00-0/00',2,'PUBLICIDADE E PESQUISA DE MERCADO');
INSERT INTO migra_cnae VALUES ('M.73.10-0/00',3,'Publicidade');
INSERT INTO migra_cnae VALUES ('M.73.11-4/00',4,'Agências de publicidade');
INSERT INTO migra_cnae VALUES ('M.73.11-4/00',5,'Agências de publicidade');
INSERT INTO migra_cnae VALUES ('M.73.12-2/00',4,'Agenciamento de espaços para publicidade, exceto em veículos de comunicação');
INSERT INTO migra_cnae VALUES ('M.73.12-2/00',5,'Agenciamento de espaços para publicidade, exceto em veículos de comunicação');
INSERT INTO migra_cnae VALUES ('M.73.19-0/00',4,'Atividades de publicidade não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('M.73.19-0/01',5,'Criação de estandes para feiras e exposições');
INSERT INTO migra_cnae VALUES ('M.73.19-0/02',5,'Promoção de vendas');
INSERT INTO migra_cnae VALUES ('M.73.19-0/03',5,'Marketing direto');
INSERT INTO migra_cnae VALUES ('M.73.19-0/04',5,'Consultoria em publicidade');
INSERT INTO migra_cnae VALUES ('M.73.19-0/99',5,'Outras atividades de publicidade não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('M.73.20-0/00',3,'Pesquisas de mercado e de opinião pública');
INSERT INTO migra_cnae VALUES ('M.73.20-3/00',4,'Pesquisas de mercado e de opinião pública');
INSERT INTO migra_cnae VALUES ('M.73.20-3/00',5,'Pesquisas de mercado e de opinião pública');
INSERT INTO migra_cnae VALUES ('M.74.00-0/00',2,'OUTRAS ATIVIDADES PROFISSIONAIS, CIENTÍFICAS E TÉCNICAS');
INSERT INTO migra_cnae VALUES ('M.74.10-0/00',3,'Design e decoração de interiores');
INSERT INTO migra_cnae VALUES ('M.74.10-2/00',4,'Design e decoração de interiores');
INSERT INTO migra_cnae VALUES ('M.74.10-2/02',5,'Design de interiores');
INSERT INTO migra_cnae VALUES ('M.74.10-2/03',5,'Design de produto');
INSERT INTO migra_cnae VALUES ('M.74.10-2/99',5,'Atividades de design não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('M.74.20-0/00',3,'Atividades fotográficas e similares');
INSERT INTO migra_cnae VALUES ('M.74.20-0/00',4,'Atividades fotográficas e similares');
INSERT INTO migra_cnae VALUES ('M.74.20-0/01',5,'Atividades de produção de fotografias, exceto aérea e submarina');
INSERT INTO migra_cnae VALUES ('M.74.20-0/02',5,'Atividades de produção de fotografias aéreas e submarinas');
INSERT INTO migra_cnae VALUES ('M.74.20-0/03',5,'Laboratórios fotográficos');
INSERT INTO migra_cnae VALUES ('M.74.20-0/04',5,'Filmagem de festas e eventos');
INSERT INTO migra_cnae VALUES ('M.74.20-0/05',5,'Serviços de microfilmagem');
INSERT INTO migra_cnae VALUES ('M.74.90-0/00',3,'Atividades profissionais, científicas e técnicas não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('M.74.90-1/00',4,'Atividades profissionais, científicas e técnicas não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('M.74.90-1/01',5,'Serviços de tradução, interpretação e similares');
INSERT INTO migra_cnae VALUES ('M.74.90-1/02',5,'Escafandria e mergulho');
INSERT INTO migra_cnae VALUES ('M.74.90-1/03',5,'Serviços de agronomia e de consultoria às atividades agrícolas e pecuárias');
INSERT INTO migra_cnae VALUES ('M.74.90-1/04',5,'Atividades de intermediação e agenciamento de serviços e negócios em geral, exceto imobiliários');
INSERT INTO migra_cnae VALUES ('M.74.90-1/05',5,'Agenciamento de profissionais para atividades esportivas, culturais e artísticas');
INSERT INTO migra_cnae VALUES ('M.74.90-1/99',5,'Outras atividades profissionais, científicas e técnicas não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('M.75.00-0/00',2,'ATIVIDADES VETERINÁRIAS');
INSERT INTO migra_cnae VALUES ('M.75.00-0/00',3,'Atividades veterinárias');
INSERT INTO migra_cnae VALUES ('M.75.00-1/00',4,'Atividades veterinárias');
INSERT INTO migra_cnae VALUES ('M.75.00-1/00',5,'Atividades veterinárias');
INSERT INTO migra_cnae VALUES ('N.00.00-0/00',1,'ATIVIDADES ADMINISTRATIVAS E SERVIÇOS COMPLEMENTARES');
INSERT INTO migra_cnae VALUES ('N.77.00-0/00',2,'ALUGUÉIS NÃO-IMOBILIÁRIOS E GESTÃO DE ATIVOS INTANGÍVEIS NÃO-FINANCEIROS');
INSERT INTO migra_cnae VALUES ('N.77.10-0/00',3,'Locação de meios de transporte sem condutor');
INSERT INTO migra_cnae VALUES ('N.77.11-0/00',4,'Locação de automóveis sem condutor');
INSERT INTO migra_cnae VALUES ('N.77.11-0/00',5,'Locação de automóveis sem condutor');
INSERT INTO migra_cnae VALUES ('N.77.19-5/00',4,'Locação de meios de transporte, exceto automóveis, sem condutor');
INSERT INTO migra_cnae VALUES ('N.77.19-5/01',5,'Locação de embarcações sem tripulação, exceto para fins recreativos');
INSERT INTO migra_cnae VALUES ('N.77.19-5/02',5,'Locação de aeronaves sem tripulação');
INSERT INTO migra_cnae VALUES ('N.77.19-5/99',5,'Locação de outros meios de transporte não especificados anteriormente, sem condutor');
INSERT INTO migra_cnae VALUES ('N.77.20-0/00',3,'Aluguel de objetos pessoais e domésticos');
INSERT INTO migra_cnae VALUES ('N.77.21-7/00',4,'Aluguel de equipamentos recreativos e esportivos');
INSERT INTO migra_cnae VALUES ('N.77.21-7/00',5,'Aluguel de equipamentos recreativos e esportivos');
INSERT INTO migra_cnae VALUES ('N.77.22-5/00',4,'Aluguel de fitas de vídeo, DVDs e similares');
INSERT INTO migra_cnae VALUES ('N.77.22-5/00',5,'Aluguel de fitas de vídeo, DVDs e similares');
INSERT INTO migra_cnae VALUES ('N.77.23-3/00',4,'Aluguel de objetos do vestuário, jóias e acessórios');
INSERT INTO migra_cnae VALUES ('N.77.23-3/00',5,'Aluguel de objetos do vestuário, jóias e acessórios');
INSERT INTO migra_cnae VALUES ('N.77.29-2/00',4,'Aluguel de objetos pessoais e domésticos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('N.77.29-2/01',5,'Aluguel de aparelhos de jogos eletrônicos');
INSERT INTO migra_cnae VALUES ('N.77.29-2/02',5,'Aluguel de móveis, utensílios e aparelhos de uso doméstico e pessoal; instrumentos musicais');
INSERT INTO migra_cnae VALUES ('N.77.29-2/03',5,'Aluguel de material médico');
INSERT INTO migra_cnae VALUES ('N.77.29-2/99',5,'Aluguel de outros objetos pessoais e domésticos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('N.77.30-0/00',3,'Aluguel de máquinas e equipamentos sem operador');
INSERT INTO migra_cnae VALUES ('N.77.31-4/00',4,'Aluguel de máquinas e equipamentos agrícolas sem operador');
INSERT INTO migra_cnae VALUES ('N.77.31-4/00',5,'Aluguel de máquinas e equipamentos agrícolas sem operador');
INSERT INTO migra_cnae VALUES ('N.77.32-2/00',4,'Aluguel de máquinas e equipamentos para construção sem operador');
INSERT INTO migra_cnae VALUES ('N.77.32-2/01',5,'Aluguel de máquinas e equipamentos para construção sem operador, exceto andaimes');
INSERT INTO migra_cnae VALUES ('N.77.32-2/02',5,'Aluguel de andaimes');
INSERT INTO migra_cnae VALUES ('N.77.33-1/00',4,'Aluguel de máquinas e equipamentos para escritório');
INSERT INTO migra_cnae VALUES ('N.77.33-1/00',5,'Aluguel de máquinas e equipamentos para escritório');
INSERT INTO migra_cnae VALUES ('N.77.39-0/00',4,'Aluguel de máquinas e equipamentos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('N.77.39-0/01',5,'Aluguel de máquinas e equipamentos para extração de minérios e petróleo, sem operador');
INSERT INTO migra_cnae VALUES ('N.77.39-0/02',5,'Aluguel de equipamentos científicos, médicos e hospitalares, sem operador');
INSERT INTO migra_cnae VALUES ('N.77.39-0/03',5,'Aluguel de palcos, coberturas e outras estruturas de uso temporário, exceto andaimes');
INSERT INTO migra_cnae VALUES ('N.77.39-0/99',5,'Aluguel de outras máquinas e equipamentos comerciais e industriais não especificados anteriormente, sem operador');
INSERT INTO migra_cnae VALUES ('N.77.40-0/00',3,'Gestão de ativos intangíveis não-financeiros');
INSERT INTO migra_cnae VALUES ('N.77.40-3/00',4,'Gestão de ativos intangíveis não-financeiros');
INSERT INTO migra_cnae VALUES ('N.77.40-3/00',5,'Gestão de ativos intangíveis não-financeiros');
INSERT INTO migra_cnae VALUES ('N.78.00-0/00',2,'SELEÇÃO, AGENCIAMENTO E LOCAÇÃO DE MÃO-DE-OBRA');
INSERT INTO migra_cnae VALUES ('N.78.10-0/00',3,'Seleção e agenciamento de mão-de-obra');
INSERT INTO migra_cnae VALUES ('N.78.10-8/00',4,'Seleção e agenciamento de mão-de-obra');
INSERT INTO migra_cnae VALUES ('N.78.10-8/00',5,'Seleção e agenciamento de mão-de-obra');
INSERT INTO migra_cnae VALUES ('N.78.20-0/00',3,'Locação de mão-de-obra temporária');
INSERT INTO migra_cnae VALUES ('N.78.20-5/00',4,'Locação de mão-de-obra temporária');
INSERT INTO migra_cnae VALUES ('N.78.20-5/00',5,'Locação de mão-de-obra temporária');
INSERT INTO migra_cnae VALUES ('N.78.30-0/00',3,'Fornecimento e gestão de recursos humanos para terceiros');
INSERT INTO migra_cnae VALUES ('N.78.30-2/00',4,'Fornecimento e gestão de recursos humanos para terceiros');
INSERT INTO migra_cnae VALUES ('N.78.30-2/00',5,'Fornecimento e gestão de recursos humanos para terceiros');
INSERT INTO migra_cnae VALUES ('N.79.00-0/00',2,'AGÊNCIAS DE VIAGENS, OPERADORES TURÍSTICOS E SERVIÇOS DE RESERVAS');
INSERT INTO migra_cnae VALUES ('N.79.10-0/00',3,'Agências de viagens e operadores turísticos');
INSERT INTO migra_cnae VALUES ('N.79.11-2/00',4,'Agências de viagens');
INSERT INTO migra_cnae VALUES ('N.79.11-2/00',5,'Agências de viagens');
INSERT INTO migra_cnae VALUES ('N.79.12-1/00',4,'Operadores turísticos');
INSERT INTO migra_cnae VALUES ('N.79.12-1/00',5,'Operadores turísticos');
INSERT INTO migra_cnae VALUES ('N.79.90-0/00',3,'Serviços de reservas e outros serviços de turismo não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('N.79.90-2/00',4,'Serviços de reservas e outros serviços de turismo não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('N.79.90-2/00',5,'Serviços de reservas e outros serviços de turismo não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('N.80.00-0/00',2,'ATIVIDADES DE VIGILÂNCIA, SEGURANÇA E INVESTIGAÇÃO');
INSERT INTO migra_cnae VALUES ('N.80.10-0/00',3,'Atividades de vigilância, segurança privada e transporte de valores');
INSERT INTO migra_cnae VALUES ('N.80.11-1/00',4,'Atividades de vigilância e segurança privada');
INSERT INTO migra_cnae VALUES ('N.80.11-1/01',5,'Atividades de vigilância e segurança privada');
INSERT INTO migra_cnae VALUES ('N.80.11-1/02',5,'Serviços de adestramento de cães de guarda');
INSERT INTO migra_cnae VALUES ('N.80.12-9/00',4,'Atividades de transporte de valores');
INSERT INTO migra_cnae VALUES ('N.80.12-9/00',5,'Atividades de transporte de valores');
INSERT INTO migra_cnae VALUES ('N.80.20-0/00',3,'Atividades de monitoramento de sistemas de segurança');
INSERT INTO migra_cnae VALUES ('N.80.20-0/00',4,'Atividades de monitoramento de sistemas de segurança');
INSERT INTO migra_cnae VALUES ('N.80.20-0/01',5,'Atividades de monitoramento de sistemas de segurança eletrônico');
INSERT INTO migra_cnae VALUES ('N.80.20-0/02',5,'Outras atividades de serviços de segurança');
INSERT INTO migra_cnae VALUES ('N.80.30-0/00',3,'Atividades de investigação particular');
INSERT INTO migra_cnae VALUES ('N.80.30-7/00',4,'Atividades de investigação particular');
INSERT INTO migra_cnae VALUES ('N.80.30-7/00',5,'Atividades de investigação particular');
INSERT INTO migra_cnae VALUES ('N.81.00-0/00',2,'SERVIÇOS PARA EDIFÍCIOS E ATIVIDADES PAISAGÍSTICAS');
INSERT INTO migra_cnae VALUES ('N.81.10-0/00',3,'Serviços combinados para apoio a edifícios');
INSERT INTO migra_cnae VALUES ('N.81.11-7/00',4,'Serviços combinados para apoio a edifícios, exceto condomínios prediais');
INSERT INTO migra_cnae VALUES ('N.81.11-7/00',5,'Serviços combinados para apoio a edifícios, exceto condomínios prediais');
INSERT INTO migra_cnae VALUES ('N.81.12-5/00',4,'Condomínios prediais');
INSERT INTO migra_cnae VALUES ('N.81.12-5/00',5,'Condomínios prediais');
INSERT INTO migra_cnae VALUES ('N.81.20-0/00',3,'Atividades de limpeza');
INSERT INTO migra_cnae VALUES ('N.81.21-4/00',4,'Limpeza em prédios e em domicílios');
INSERT INTO migra_cnae VALUES ('N.81.21-4/00',5,'Limpeza em prédios e em domicílios');
INSERT INTO migra_cnae VALUES ('N.81.22-2/00',4,'Imunização e controle de pragas urbanas');
INSERT INTO migra_cnae VALUES ('N.81.22-2/00',5,'Imunização e controle de pragas urbanas');
INSERT INTO migra_cnae VALUES ('N.81.29-0/00',4,'Atividades de limpeza não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('N.81.29-0/00',5,'Atividades de limpeza não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('N.81.30-0/00',3,'Atividades paisagísticas');
INSERT INTO migra_cnae VALUES ('N.81.30-3/00',4,'Atividades paisagísticas');
INSERT INTO migra_cnae VALUES ('N.81.30-3/00',5,'Atividades paisagísticas');
INSERT INTO migra_cnae VALUES ('N.82.00-0/00',2,'SERVIÇOS DE ESCRITÓRIO, DE APOIO ADMINISTRATIVO E OUTROS SERVIÇOS PRESTADOS PRINCIPALMENTE ÀS EMPRESAS');
INSERT INTO migra_cnae VALUES ('N.82.10-0/00',3,'Serviços de escritório e apoio administrativo');
INSERT INTO migra_cnae VALUES ('N.82.11-3/00',4,'Serviços combinados de escritório e apoio administrativo');
INSERT INTO migra_cnae VALUES ('N.82.11-3/00',5,'Serviços combinados de escritório e apoio administrativo');
INSERT INTO migra_cnae VALUES ('N.82.19-9/00',4,'Fotocópias, preparação de documentos e outros serviços especializados de apoio administrativo');
INSERT INTO migra_cnae VALUES ('N.82.19-9/01',5,'Fotocópias');
INSERT INTO migra_cnae VALUES ('N.82.19-9/99',5,'Preparação de documentos e serviços especializados de apoio administrativo não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('N.82.20-0/00',3,'Atividades de teleatendimento');
INSERT INTO migra_cnae VALUES ('N.82.20-2/00',4,'Atividades de teleatendimento');
INSERT INTO migra_cnae VALUES ('N.82.20-2/00',5,'Atividades de teleatendimento');
INSERT INTO migra_cnae VALUES ('N.82.30-0/00',3,'Atividades de organização de eventos, exceto culturais e esportivos');
INSERT INTO migra_cnae VALUES ('N.82.30-0/00',4,'Atividades de organização de eventos, exceto culturais e esportivos');
INSERT INTO migra_cnae VALUES ('N.82.30-0/01',5,'Serviços de organização de feiras, congressos, exposições e festas');
INSERT INTO migra_cnae VALUES ('N.82.30-0/02',5,'Casas de festas e eventos');
INSERT INTO migra_cnae VALUES ('N.82.90-0/00',3,'Outras atividades de serviços prestados principalmente às empresas');
INSERT INTO migra_cnae VALUES ('N.82.91-1/00',4,'Atividades de cobrança e informações cadastrais');
INSERT INTO migra_cnae VALUES ('N.82.91-1/00',5,'Atividades de cobrança e informações cadastrais');
INSERT INTO migra_cnae VALUES ('N.82.92-0/00',4,'Envasamento e empacotamento sob contrato');
INSERT INTO migra_cnae VALUES ('N.82.92-0/00',5,'Envasamento e empacotamento sob contrato');
INSERT INTO migra_cnae VALUES ('N.82.99-7/00',4,'Atividades de serviços prestados principalmente às empresas não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('N.82.99-7/01',5,'Medição de consumo de energia elétrica, gás e água');
INSERT INTO migra_cnae VALUES ('N.82.99-7/02',5,'Emissão de vales-alimentação, vales-transporte e similares');
INSERT INTO migra_cnae VALUES ('N.82.99-7/03',5,'Serviços de gravação de carimbos, exceto confecção');
INSERT INTO migra_cnae VALUES ('N.82.99-7/04',5,'Leiloeiros independentes');
INSERT INTO migra_cnae VALUES ('N.82.99-7/05',5,'Serviços de levantamento de fundos sob contrato');
INSERT INTO migra_cnae VALUES ('N.82.99-7/06',5,'Casas lotéricas');
INSERT INTO migra_cnae VALUES ('N.82.99-7/07',5,'Salas de acesso à internet');
INSERT INTO migra_cnae VALUES ('N.82.99-7/99',5,'Outras atividades de serviços prestados principalmente às empresas não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('O.00.00-0/00',1,'ADMINISTRAÇÃO PÚBLICA, DEFESA E SEGURIDADE SOCIAL');
INSERT INTO migra_cnae VALUES ('O.84.00-0/00',2,'ADMINISTRAÇÃO PÚBLICA, DEFESA E SEGURIDADE SOCIAL');
INSERT INTO migra_cnae VALUES ('O.84.10-0/00',3,'Administração do estado e da política econômica e social');
INSERT INTO migra_cnae VALUES ('O.84.11-6/00',4,'Administração pública em geral');
INSERT INTO migra_cnae VALUES ('O.84.11-6/00',5,'Administração pública em geral');
INSERT INTO migra_cnae VALUES ('O.84.12-4/00',4,'Regulação das atividades de saúde, educação, serviços culturais e outros serviços sociais');
INSERT INTO migra_cnae VALUES ('O.84.12-4/00',5,'Regulação das atividades de saúde, educação, serviços culturais e outros serviços sociais');
INSERT INTO migra_cnae VALUES ('O.84.13-2/00',4,'Regulação das atividades econômicas');
INSERT INTO migra_cnae VALUES ('O.84.13-2/00',5,'Regulação das atividades econômicas');
INSERT INTO migra_cnae VALUES ('O.84.20-0/00',3,'Serviços coletivos prestados pela administração pública');
INSERT INTO migra_cnae VALUES ('O.84.21-3/00',4,'Relações exteriores');
INSERT INTO migra_cnae VALUES ('O.84.21-3/00',5,'Relações exteriores');
INSERT INTO migra_cnae VALUES ('O.84.22-1/00',4,'Defesa');
INSERT INTO migra_cnae VALUES ('O.84.22-1/00',5,'Defesa');
INSERT INTO migra_cnae VALUES ('O.84.23-0/00',4,'Justiça');
INSERT INTO migra_cnae VALUES ('O.84.23-0/00',5,'Justiça');
INSERT INTO migra_cnae VALUES ('O.84.24-8/00',4,'Segurança e ordem pública');
INSERT INTO migra_cnae VALUES ('O.84.24-8/00',5,'Segurança e ordem pública');
INSERT INTO migra_cnae VALUES ('O.84.25-6/00',4,'Defesa Civil');
INSERT INTO migra_cnae VALUES ('O.84.25-6/00',5,'Defesa Civil');
INSERT INTO migra_cnae VALUES ('O.84.30-0/00',3,'Seguridade social obrigatória');
INSERT INTO migra_cnae VALUES ('O.84.30-2/00',4,'Seguridade social obrigatória');
INSERT INTO migra_cnae VALUES ('O.84.30-2/00',5,'Seguridade social obrigatória');
INSERT INTO migra_cnae VALUES ('P.00.00-0/00',1,'EDUCAÇÃO');
INSERT INTO migra_cnae VALUES ('P.85.00-0/00',2,'EDUCAÇÃO');
INSERT INTO migra_cnae VALUES ('P.85.10-0/00',3,'Educação infantil e ensino fundamental');
INSERT INTO migra_cnae VALUES ('P.85.11-2/00',4,'Educação infantil - creche');
INSERT INTO migra_cnae VALUES ('P.85.11-2/00',5,'Educação infantil - creche');
INSERT INTO migra_cnae VALUES ('P.85.12-1/00',4,'Educação infantil - pré-escola');
INSERT INTO migra_cnae VALUES ('P.85.12-1/00',5,'Educação infantil - pré-escola');
INSERT INTO migra_cnae VALUES ('P.85.13-9/00',4,'Ensino fundamental');
INSERT INTO migra_cnae VALUES ('P.85.13-9/00',5,'Ensino fundamental');
INSERT INTO migra_cnae VALUES ('P.85.20-0/00',3,'Ensino médio');
INSERT INTO migra_cnae VALUES ('P.85.20-1/00',4,'Ensino médio');
INSERT INTO migra_cnae VALUES ('P.85.20-1/00',5,'Ensino médio');
INSERT INTO migra_cnae VALUES ('P.85.30-0/00',3,'Educação superior');
INSERT INTO migra_cnae VALUES ('P.85.31-7/00',4,'Educação superior - graduação');
INSERT INTO migra_cnae VALUES ('P.85.31-7/00',5,'Educação superior - graduação');
INSERT INTO migra_cnae VALUES ('P.85.32-5/00',4,'Educação superior - graduação e pós-graduação');
INSERT INTO migra_cnae VALUES ('P.85.32-5/00',5,'Educação superior - graduação e pós-graduação');
INSERT INTO migra_cnae VALUES ('P.85.33-3/00',4,'Educação superior - pós-graduação e extensão');
INSERT INTO migra_cnae VALUES ('P.85.33-3/00',5,'Educação superior - pós-graduação e extensão');
INSERT INTO migra_cnae VALUES ('P.85.40-0/00',3,'Educação profissional de nível técnico e tecnológico');
INSERT INTO migra_cnae VALUES ('P.85.41-4/00',4,'Educação profissional de nível técnico');
INSERT INTO migra_cnae VALUES ('P.85.41-4/00',5,'Educação profissional de nível técnico');
INSERT INTO migra_cnae VALUES ('P.85.42-2/00',4,'Educação profissional de nível tecnológico');
INSERT INTO migra_cnae VALUES ('P.85.42-2/00',5,'Educação profissional de nível tecnológico');
INSERT INTO migra_cnae VALUES ('P.85.50-0/00',3,'Atividades de apoio à educação');
INSERT INTO migra_cnae VALUES ('P.85.50-3/00',4,'Atividades de apoio à educação');
INSERT INTO migra_cnae VALUES ('P.85.50-3/01',5,'Administração de caixas escolares');
INSERT INTO migra_cnae VALUES ('P.85.50-3/02',5,'Atividades de apoio à educação, exceto caixas escolares');
INSERT INTO migra_cnae VALUES ('P.85.90-0/00',3,'Outras atividades de ensino');
INSERT INTO migra_cnae VALUES ('P.85.91-1/00',4,'Ensino de esportes');
INSERT INTO migra_cnae VALUES ('P.85.91-1/00',5,'Ensino de esportes');
INSERT INTO migra_cnae VALUES ('P.85.92-9/00',4,'Ensino de arte e cultura');
INSERT INTO migra_cnae VALUES ('P.85.92-9/01',5,'Ensino de dança');
INSERT INTO migra_cnae VALUES ('P.85.92-9/02',5,'Ensino de artes cênicas, exceto dança');
INSERT INTO migra_cnae VALUES ('P.85.92-9/03',5,'Ensino de música');
INSERT INTO migra_cnae VALUES ('P.85.92-9/99',5,'Ensino de arte e cultura não especificado anteriormente');
INSERT INTO migra_cnae VALUES ('P.85.93-7/00',4,'Ensino de idiomas');
INSERT INTO migra_cnae VALUES ('P.85.93-7/00',5,'Ensino de idiomas');
INSERT INTO migra_cnae VALUES ('P.85.99-6/00',4,'Atividades de ensino não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('P.85.99-6/01',5,'Formação de condutores');
INSERT INTO migra_cnae VALUES ('P.85.99-6/02',5,'Cursos de pilotagem');
INSERT INTO migra_cnae VALUES ('P.85.99-6/03',5,'Treinamento em informática');
INSERT INTO migra_cnae VALUES ('P.85.99-6/04',5,'Treinamento em desenvolvimento profissional e gerencial');
INSERT INTO migra_cnae VALUES ('P.85.99-6/05',5,'Cursos preparatórios para concursos');
INSERT INTO migra_cnae VALUES ('P.85.99-6/99',5,'Outras atividades de ensino não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('Q.00.00-0/00',1,'SAÚDE HUMANA E SERVIÇOS SOCIAIS');
INSERT INTO migra_cnae VALUES ('Q.86.00-0/00',2,'ATIVIDADES DE ATENÇÃO À SAÚDE HUMANA');
INSERT INTO migra_cnae VALUES ('Q.86.10-0/00',3,'Atividades de atendimento hospitalar');
INSERT INTO migra_cnae VALUES ('Q.86.10-1/00',4,'Atividades de atendimento hospitalar');
INSERT INTO migra_cnae VALUES ('Q.86.10-1/01',5,'Atividades de atendimento hospitalar, exceto pronto-socorro e unidades para atendimento a urgências');
INSERT INTO migra_cnae VALUES ('Q.86.10-1/02',5,'Atividades de atendimento em pronto-socorro e unidades hospitalares para atendimento a urgências');
INSERT INTO migra_cnae VALUES ('Q.86.20-0/00',3,'Serviços móveis de atendimento a urgências e de remoção de pacientes');
INSERT INTO migra_cnae VALUES ('Q.86.21-6/00',4,'Serviços móveis de atendimento a urgências');
INSERT INTO migra_cnae VALUES ('Q.86.21-6/01',5,'UTI móvel');
INSERT INTO migra_cnae VALUES ('Q.86.21-6/02',5,'Serviços móveis de atendimento a urgências, exceto por UTI móvel');
INSERT INTO migra_cnae VALUES ('Q.86.22-4/00',4,'Serviços de remoção de pacientes, exceto os serviços móveis de atendimento a urgências');
INSERT INTO migra_cnae VALUES ('Q.86.22-4/00',5,'Serviços de remoção de pacientes, exceto os serviços móveis de atendimento a urgências');
INSERT INTO migra_cnae VALUES ('Q.86.30-0/00',3,'Atividades de atenção ambulatorial executadas por médicos e odontólogos');
INSERT INTO migra_cnae VALUES ('Q.86.30-5/00',4,'Atividades de atenção ambulatorial executadas por médicos e odontólogos');
INSERT INTO migra_cnae VALUES ('Q.86.30-5/01',5,'Atividade médica ambulatorial com recursos para realização de procedimentos cirúrgicos');
INSERT INTO migra_cnae VALUES ('Q.86.30-5/02',5,'Atividade médica ambulatorial com recursos para realização de exames complementares');
INSERT INTO migra_cnae VALUES ('Q.86.30-5/03',5,'Atividade médica ambulatorial restrita a consultas');
INSERT INTO migra_cnae VALUES ('Q.86.30-5/04',5,'Atividade odontológica');
INSERT INTO migra_cnae VALUES ('Q.86.30-5/06',5,'Serviços de vacinação e imunização humana');
INSERT INTO migra_cnae VALUES ('Q.86.30-5/07',5,'Atividades de reprodução humana assistida');
INSERT INTO migra_cnae VALUES ('Q.86.30-5/99',5,'Atividades de atenção ambulatorial não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('Q.86.40-0/00',3,'Atividades de serviços de complementação diagnóstica e terapêutica');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/00',4,'Atividades de serviços de complementação diagnóstica e terapêutica');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/01',5,'Laboratórios de anatomia patológica e citológica');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/02',5,'Laboratórios clínicos');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/03',5,'Serviços de diálise e nefrologia');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/04',5,'Serviços de tomografia');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/05',5,'Serviços de diagnóstico por imagem com uso de radiação ionizante, exceto tomografia');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/06',5,'Serviços de ressonância magnética');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/07',5,'Serviços de diagnóstico por imagem sem uso de radiação ionizante, exceto ressonância magnética');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/08',5,'Serviços de diagnóstico por registro gráfico - ECG, EEG e outros exames análogos');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/09',5,'Serviços de diagnóstico por métodos ópticos - endoscopia e outros exames análogos');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/10',5,'Serviços de quimioterapia');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/11',5,'Serviços de radioterapia');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/12',5,'Serviços de hemoterapia');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/13',5,'Serviços de litotripsia');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/14',5,'Serviços de bancos de células e tecidos humanos');
INSERT INTO migra_cnae VALUES ('Q.86.40-2/99',5,'Atividades de serviços de complementação diagnóstica e terapêutica não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('Q.86.50-0/00',3,'Atividades de profissionais da área de saúde, exceto médicos e odontólogos');
INSERT INTO migra_cnae VALUES ('Q.86.50-0/00',4,'Atividades de profissionais da área de saúde, exceto médicos e odontólogos');
INSERT INTO migra_cnae VALUES ('Q.86.50-0/01',5,'Atividades de enfermagem');
INSERT INTO migra_cnae VALUES ('Q.86.50-0/02',5,'Atividades de profissionais da nutrição');
INSERT INTO migra_cnae VALUES ('Q.86.50-0/03',5,'Atividades de psicologia e psicanálise');
INSERT INTO migra_cnae VALUES ('Q.86.50-0/04',5,'Atividades de fisioterapia');
INSERT INTO migra_cnae VALUES ('Q.86.50-0/05',5,'Atividades de terapia ocupacional');
INSERT INTO migra_cnae VALUES ('Q.86.50-0/06',5,'Atividades de fonoaudiologia');
INSERT INTO migra_cnae VALUES ('Q.86.50-0/07',5,'Atividades de terapia de nutrição enteral e parenteral');
INSERT INTO migra_cnae VALUES ('Q.86.50-0/99',5,'Atividades de profissionais da área de saúde não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('Q.86.60-0/00',3,'Atividades de apoio à gestão de saúde');
INSERT INTO migra_cnae VALUES ('Q.86.60-7/00',4,'Atividades de apoio à gestão de saúde');
INSERT INTO migra_cnae VALUES ('Q.86.60-7/00',5,'Atividades de apoio à gestão de saúde');
INSERT INTO migra_cnae VALUES ('Q.86.90-0/00',3,'Atividades de atenção à saúde humana não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('Q.86.90-9/00',4,'Atividades de atenção à saúde humana não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('Q.86.90-9/01',5,'Atividades de práticas integrativas e complementares em saúde humana');
INSERT INTO migra_cnae VALUES ('Q.86.90-9/02',5,'Atividades de bancos de leite humano');
INSERT INTO migra_cnae VALUES ('Q.86.90-9/03',5,'Atividades de acupuntura');
INSERT INTO migra_cnae VALUES ('Q.86.90-9/04',5,'Atividades de podologia');
INSERT INTO migra_cnae VALUES ('Q.86.90-9/99',5,'Outras atividades de atenção à saúde humana não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('Q.87.00-0/00',2,'ATIVIDADES DE ATENÇÃO À SAÚDE HUMANA INTEGRADAS COM ASSISTÊNCIA SOCIAL, PRESTADAS EM RESIDÊNCIAS COLETIVAS E PARTICULARES');
INSERT INTO migra_cnae VALUES ('Q.87.10-0/00',3,'Atividades de assistência a idosos, deficientes físicos, imunodeprimidos e convalescentes, e de infra-estrutura e apoio a pacientes prestadas em residências coletivas e particulares');
INSERT INTO migra_cnae VALUES ('Q.87.11-5/00',4,'Atividades de assistência a idosos, deficientes físicos, imunodeprimidos e convalescentes prestadas em residências coletivas e particulares');
INSERT INTO migra_cnae VALUES ('Q.87.11-5/01',5,'Clínicas e residências geriátricas');
INSERT INTO migra_cnae VALUES ('Q.87.11-5/02',5,'Instituições de longa permanência para idosos');
INSERT INTO migra_cnae VALUES ('Q.87.11-5/03',5,'Atividades de assistência a deficientes físicos, imunodeprimidos e convalescentes');
INSERT INTO migra_cnae VALUES ('Q.87.11-5/04',5,'Centros de apoio a pacientes com câncer e com AIDS');
INSERT INTO migra_cnae VALUES ('Q.87.11-5/05',5,'Condomínios residenciais para idosos');
INSERT INTO migra_cnae VALUES ('Q.87.12-3/00',4,'Atividades de fornecimento de infra-estrutura de apoio e assistência a paciente no domicílio');
INSERT INTO migra_cnae VALUES ('Q.87.12-3/00',5,'Atividades de fornecimento de infra-estrutura de apoio e assistência a paciente no domicílio');
INSERT INTO migra_cnae VALUES ('Q.87.20-0/00',3,'Atividades de assistência psicossocial e à saúde a portadores de distúrbios psíquicos, deficiência mental e dependência química');
INSERT INTO migra_cnae VALUES ('Q.87.20-4/00',4,'Atividades de assistência psicossocial e à saúde a portadores de distúrbios psíquicos, deficiência mental e dependência química');
INSERT INTO migra_cnae VALUES ('Q.87.20-4/01',5,'Atividades de centros de assistência psicossocial');
INSERT INTO migra_cnae VALUES ('Q.87.20-4/99',5,'Atividades de assistência psicossocial e à saúde a portadores de distúrbios psíquicos, deficiência mental e dependência química não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('Q.87.30-0/00',3,'Atividades de assistência social prestadas em residências coletivas e particulares');
INSERT INTO migra_cnae VALUES ('Q.87.30-1/00',4,'Atividades de assistência social prestadas em residências coletivas e particulares');
INSERT INTO migra_cnae VALUES ('Q.87.30-1/01',5,'Orfanatos');
INSERT INTO migra_cnae VALUES ('Q.87.30-1/02',5,'Albergues assistenciais');
INSERT INTO migra_cnae VALUES ('Q.87.30-1/99',5,'Atividades de assistência social prestadas em residências coletivas e particulares não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('Q.88.00-0/00',2,'SERVIÇOS DE ASSISTÊNCIA SOCIAL SEM ALOJAMENTO');
INSERT INTO migra_cnae VALUES ('Q.88.00-0/00',3,'Serviços de assistência social sem alojamento');
INSERT INTO migra_cnae VALUES ('Q.88.00-6/00',4,'Serviços de assistência social sem alojamento');
INSERT INTO migra_cnae VALUES ('Q.88.00-6/00',5,'Serviços de assistência social sem alojamento');
INSERT INTO migra_cnae VALUES ('R.00.00-0/00',1,'ARTES, CULTURA, ESPORTE E RECREAÇÃO');
INSERT INTO migra_cnae VALUES ('R.90.00-0/00',2,'ATIVIDADES ARTÍSTICAS, CRIATIVAS E DE ESPETÁCULOS');
INSERT INTO migra_cnae VALUES ('R.90.00-0/00',3,'Atividades artísticas, criativas e de espetáculos');
INSERT INTO migra_cnae VALUES ('R.90.01-9/00',4,'Artes cênicas, espetáculos e atividades complementares');
INSERT INTO migra_cnae VALUES ('R.90.01-9/01',5,'Produção teatral');
INSERT INTO migra_cnae VALUES ('R.90.01-9/02',5,'Produção musical');
INSERT INTO migra_cnae VALUES ('R.90.01-9/03',5,'Produção de espetáculos de dança');
INSERT INTO migra_cnae VALUES ('R.90.01-9/04',5,'Produção de espetáculos circenses, de marionetes e similares');
INSERT INTO migra_cnae VALUES ('R.90.01-9/05',5,'Produção de espetáculos de rodeios, vaquejadas e similares');
INSERT INTO migra_cnae VALUES ('R.90.01-9/06',5,'Atividades de sonorização e de iluminação');
INSERT INTO migra_cnae VALUES ('R.90.01-9/99',5,'Artes cênicas, espetáculos e atividades complementares não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('R.90.02-7/00',4,'Criação artística');
INSERT INTO migra_cnae VALUES ('R.90.02-7/01',5,'Atividades de artistas plásticos, jornalistas independentes e escritores');
INSERT INTO migra_cnae VALUES ('R.90.02-7/02',5,'Restauração de obras de arte');
INSERT INTO migra_cnae VALUES ('R.90.03-5/00',4,'Gestão de espaços para artes cênicas, espetáculos e outras atividades artísticas');
INSERT INTO migra_cnae VALUES ('R.90.03-5/00',5,'Gestão de espaços para artes cênicas, espetáculos e outras atividades artísticas');
INSERT INTO migra_cnae VALUES ('R.91.00-0/00',2,'ATIVIDADES LIGADAS AO PATRIMÔNIO CULTURAL E AMBIENTAL');
INSERT INTO migra_cnae VALUES ('R.91.00-0/00',3,'Atividades ligadas ao patrimônio cultural e ambiental');
INSERT INTO migra_cnae VALUES ('R.91.01-5/00',4,'Atividades de bibliotecas e arquivos');
INSERT INTO migra_cnae VALUES ('R.91.01-5/00',5,'Atividades de bibliotecas e arquivos');
INSERT INTO migra_cnae VALUES ('R.91.02-3/00',4,'Atividades de museus e de exploração, restauração artística e conservação de lugares e prédios históricos e atrações similares');
INSERT INTO migra_cnae VALUES ('R.91.02-3/01',5,'Atividades de museus e de exploração de lugares e prédios históricos e atrações similares');
INSERT INTO migra_cnae VALUES ('R.91.02-3/02',5,'Restauração e conservação de lugares e prédios históricos');
INSERT INTO migra_cnae VALUES ('R.91.03-1/00',4,'Atividades de jardins botânicos, zoológicos, parques nacionais, reservas ecológicas e áreas de proteção ambiental');
INSERT INTO migra_cnae VALUES ('R.91.03-1/00',5,'Atividades de jardins botânicos, zoológicos, parques nacionais, reservas ecológicas e áreas de proteção ambiental');
INSERT INTO migra_cnae VALUES ('R.92.00-0/00',2,'ATIVIDADES DE EXPLORAÇÃO DE JOGOS DE AZAR E APOSTAS');
INSERT INTO migra_cnae VALUES ('R.92.00-0/00',3,'Atividades de exploração de jogos de azar e apostas');
INSERT INTO migra_cnae VALUES ('R.92.00-3/00',4,'Atividades de exploração de jogos de azar e apostas');
INSERT INTO migra_cnae VALUES ('R.92.00-3/01',5,'Casas de bingo');
INSERT INTO migra_cnae VALUES ('R.92.00-3/02',5,'Exploração de apostas em corridas de cavalos');
INSERT INTO migra_cnae VALUES ('R.92.00-3/99',5,'Exploração de jogos de azar e apostas não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('R.93.00-0/00',2,'ATIVIDADES ESPORTIVAS E DE RECREAÇÃO E LAZER');
INSERT INTO migra_cnae VALUES ('R.93.10-0/00',3,'Atividades esportivas');
INSERT INTO migra_cnae VALUES ('R.93.11-5/00',4,'Gestão de instalações de esportes');
INSERT INTO migra_cnae VALUES ('R.93.11-5/00',5,'Gestão de instalações de esportes');
INSERT INTO migra_cnae VALUES ('R.93.12-3/00',4,'Clubes sociais, esportivos e similares');
INSERT INTO migra_cnae VALUES ('R.93.12-3/00',5,'Clubes sociais, esportivos e similares');
INSERT INTO migra_cnae VALUES ('R.93.13-1/00',4,'Atividades de condicionamento físico');
INSERT INTO migra_cnae VALUES ('R.93.13-1/00',5,'Atividades de condicionamento físico');
INSERT INTO migra_cnae VALUES ('R.93.19-1/00',4,'Atividades esportivas não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('R.93.19-1/01',5,'Produção e promoção de eventos esportivos');
INSERT INTO migra_cnae VALUES ('R.93.19-1/99',5,'Outras atividades esportivas não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('R.93.20-0/00',3,'Atividades de recreação e lazer');
INSERT INTO migra_cnae VALUES ('R.93.21-2/00',4,'Parques de diversão e parques temáticos');
INSERT INTO migra_cnae VALUES ('R.93.21-2/00',5,'Parques de diversão e parques temáticos');
INSERT INTO migra_cnae VALUES ('R.93.29-8/00',4,'Atividades de recreação e lazer não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('R.93.29-8/01',5,'Discotecas, danceterias, salões de dança e similares');
INSERT INTO migra_cnae VALUES ('R.93.29-8/02',5,'Exploração de boliches');
INSERT INTO migra_cnae VALUES ('R.93.29-8/03',5,'Exploração de jogos de sinuca, bilhar e similares');
INSERT INTO migra_cnae VALUES ('R.93.29-8/04',5,'Exploração de jogos eletrônicos recreativos');
INSERT INTO migra_cnae VALUES ('R.93.29-8/99',5,'Outras atividades de recreação e lazer não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('S.00.00-0/00',1,'OUTRAS ATIVIDADES DE SERVIÇOS');
INSERT INTO migra_cnae VALUES ('S.94.00-0/00',2,'ATIVIDADES DE ORGANIZAÇÕES ASSOCIATIVAS');
INSERT INTO migra_cnae VALUES ('S.94.10-0/00',3,'Atividades de organizações associativas patronais, empresariais e profissionais');
INSERT INTO migra_cnae VALUES ('S.94.11-1/00',4,'Atividades de organizações associativas patronais e empresariais');
INSERT INTO migra_cnae VALUES ('S.94.11-1/00',5,'Atividades de organizações associativas patronais e empresariais');
INSERT INTO migra_cnae VALUES ('S.94.12-0/00',4,'Atividades de organizações associativas profissionais');
INSERT INTO migra_cnae VALUES ('S.94.12-0/01',5,'Atividades de fiscalização profissional');
INSERT INTO migra_cnae VALUES ('S.94.12-0/99',5,'Outras atividades associativas profissionais');
INSERT INTO migra_cnae VALUES ('S.94.20-0/00',3,'Atividades de organizações sindicais');
INSERT INTO migra_cnae VALUES ('S.94.20-1/00',4,'Atividades de organizações sindicais');
INSERT INTO migra_cnae VALUES ('S.94.20-1/00',5,'Atividades de organizações sindicais');
INSERT INTO migra_cnae VALUES ('S.94.30-0/00',3,'Atividades de associações de defesa de direitos sociais');
INSERT INTO migra_cnae VALUES ('S.94.30-8/00',4,'Atividades de associações de defesa de direitos sociais');
INSERT INTO migra_cnae VALUES ('S.94.30-8/00',5,'Atividades de associações de defesa de direitos sociais');
INSERT INTO migra_cnae VALUES ('S.94.90-0/00',3,'Atividades de organizações associativas não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('S.94.91-0/00',4,'Atividades de organizações religiosas');
INSERT INTO migra_cnae VALUES ('S.94.91-0/00',5,'Atividades de organizações religiosas ou filosóficas');
INSERT INTO migra_cnae VALUES ('S.94.92-8/00',4,'Atividades de organizações políticas');
INSERT INTO migra_cnae VALUES ('S.94.92-8/00',5,'Atividades de organizações políticas');
INSERT INTO migra_cnae VALUES ('S.94.93-6/00',4,'Atividades de organizações associativas ligadas à cultura e à arte');
INSERT INTO migra_cnae VALUES ('S.94.93-6/00',5,'Atividades de organizações associativas ligadas à cultura e à arte');
INSERT INTO migra_cnae VALUES ('S.94.99-5/00',4,'Atividades associativas não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('S.94.99-5/00',5,'Atividades associativas não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('S.95.00-0/00',2,'REPARAÇÃO E MANUTENÇÃO DE EQUIPAMENTOS DE INFORMÁTICA E COMUNICAÇÃO E DE OBJETOS PESSOAIS E DOMÉSTICOS');
INSERT INTO migra_cnae VALUES ('S.95.10-0/00',3,'Reparação e manutenção de equipamentos de informática e comunicação');
INSERT INTO migra_cnae VALUES ('S.95.11-8/00',4,'Reparação e manutenção de computadores e de equipamentos periféricos');
INSERT INTO migra_cnae VALUES ('S.95.11-8/00',5,'Reparação e manutenção de computadores e de equipamentos periféricos');
INSERT INTO migra_cnae VALUES ('S.95.12-6/00',4,'Reparação e manutenção de equipamentos de comunicação');
INSERT INTO migra_cnae VALUES ('S.95.12-6/00',5,'Reparação e manutenção de equipamentos de comunicação');
INSERT INTO migra_cnae VALUES ('S.95.20-0/00',3,'Reparação e manutenção de objetos e equipamentos pessoais e domésticos');
INSERT INTO migra_cnae VALUES ('S.95.21-5/00',4,'Reparação e manutenção de equipamentos eletroeletrônicos de uso pessoal e doméstico');
INSERT INTO migra_cnae VALUES ('S.95.21-5/00',5,'Reparação e manutenção de equipamentos eletroeletrônicos de uso pessoal e doméstico');
INSERT INTO migra_cnae VALUES ('S.95.29-1/00',4,'Reparação e manutenção de objetos e equipamentos pessoais e domésticos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('S.95.29-1/01',5,'Reparação de calçados, bolsas e artigos de viagem');
INSERT INTO migra_cnae VALUES ('S.95.29-1/02',5,'Chaveiros');
INSERT INTO migra_cnae VALUES ('S.95.29-1/03',5,'Reparação de relógios');
INSERT INTO migra_cnae VALUES ('S.95.29-1/04',5,'Reparação de bicicletas, triciclos e outros veículos não-motorizados');
INSERT INTO migra_cnae VALUES ('S.95.29-1/05',5,'Reparação de artigos do mobiliário');
INSERT INTO migra_cnae VALUES ('S.95.29-1/06',5,'Reparação de jóias');
INSERT INTO migra_cnae VALUES ('S.95.29-1/99',5,'Reparação e manutenção de outros objetos e equipamentos pessoais e domésticos não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('S.96.00-0/00',2,'OUTRAS ATIVIDADES DE SERVIÇOS PESSOAIS');
INSERT INTO migra_cnae VALUES ('S.96.00-0/00',3,'Outras atividades de serviços pessoais');
INSERT INTO migra_cnae VALUES ('S.96.01-7/00',4,'Lavanderias, tinturarias e toalheiros');
INSERT INTO migra_cnae VALUES ('S.96.01-7/01',5,'Lavanderias');
INSERT INTO migra_cnae VALUES ('S.96.01-7/02',5,'Tinturarias');
INSERT INTO migra_cnae VALUES ('S.96.01-7/03',5,'Toalheiros');
INSERT INTO migra_cnae VALUES ('S.96.02-5/00',4,'Cabeleireiros e outras atividades de tratamento de beleza');
INSERT INTO migra_cnae VALUES ('S.96.02-5/01',5,'Cabeleireiros, manicure e pedicure');
INSERT INTO migra_cnae VALUES ('S.96.02-5/02',5,'Atividades de Estética e outros serviços de cuidados com a beleza');
INSERT INTO migra_cnae VALUES ('S.96.03-3/00',4,'Atividades funerárias e serviços relacionados');
INSERT INTO migra_cnae VALUES ('S.96.03-3/01',5,'Gestão e manutenção de cemitérios');
INSERT INTO migra_cnae VALUES ('S.96.03-3/02',5,'Serviços de cremação');
INSERT INTO migra_cnae VALUES ('S.96.03-3/03',5,'Serviços de sepultamento');
INSERT INTO migra_cnae VALUES ('S.96.03-3/04',5,'Serviços de funerárias');
INSERT INTO migra_cnae VALUES ('S.96.03-3/05',5,'Serviços de somatoconservação');
INSERT INTO migra_cnae VALUES ('S.96.03-3/99',5,'Atividades funerárias e serviços relacionados não especificados anteriormente');
INSERT INTO migra_cnae VALUES ('S.96.09-2/00',4,'Atividades de serviços pessoais não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('S.96.09-2/02',5,'Agências matrimoniais');
INSERT INTO migra_cnae VALUES ('S.96.09-2/04',5,'Exploração de máquinas de serviços pessoais acionadas por moeda');
INSERT INTO migra_cnae VALUES ('S.96.09-2/05',5,'Atividades de sauna e banhos');
INSERT INTO migra_cnae VALUES ('S.96.09-2/06',5,'Serviços de tatuagem e colocação de piercing');
INSERT INTO migra_cnae VALUES ('S.96.09-2/07',5,'Alojamento de animais domésticos');
INSERT INTO migra_cnae VALUES ('S.96.09-2/08',5,'Higiene e embelezamento de animais domésticos');
INSERT INTO migra_cnae VALUES ('S.96.09-2/99',5,'Outras atividades de serviços pessoais não especificadas anteriormente');
INSERT INTO migra_cnae VALUES ('T.00.00-0/00',1,'SERVIÇOS DOMÉSTICOS');
INSERT INTO migra_cnae VALUES ('T.97.00-0/00',2,'SERVIÇOS DOMÉSTICOS');
INSERT INTO migra_cnae VALUES ('T.97.00-0/00',3,'Serviços domésticos');
INSERT INTO migra_cnae VALUES ('T.97.00-5/00',4,'Serviços domésticos');
INSERT INTO migra_cnae VALUES ('T.97.00-5/00',5,'Serviços domésticos');
INSERT INTO migra_cnae VALUES ('U.00.00-0/00',1,'ORGANISMOS INTERNACIONAIS E OUTRAS INSTITUIÇÕES EXTRATERRITORIAIS');
INSERT INTO migra_cnae VALUES ('U.99.00-0/00',2,'ORGANISMOS INTERNACIONAIS E OUTRAS INSTITUIÇÕES EXTRATERRITORIAIS');
INSERT INTO migra_cnae VALUES ('U.99.00-0/00',3,'Organismos internacionais e outras instituições extraterritoriais');
INSERT INTO migra_cnae VALUES ('U.99.00-8/00',4,'Organismos internacionais e outras instituições extraterritoriais');
INSERT INTO migra_cnae VALUES ('U.99.00-8/00',5,'Organismos internacionais e outras instituições extraterritoriais');


INSERT
  INTO economico.vigencia_cnae
     ( cod_vigencia
     , dt_inicio
     )
VALUES
     ( 2
     , '2013-09-24'
     );

INSERT
  INTO economico.nivel_cnae
     ( cod_nivel
     , cod_vigencia
     , nom_nivel
     , mascara
     )
SELECT cod_nivel
     , 2 AS cod_vigencia
     , nom_nivel
     , mascara
  FROM economico.nivel_cnae
 WHERE cod_vigencia = 1
     ;


CREATE OR REPLACE FUNCTION migra_cnae() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
    inCodCnae   INTEGER;
    stNivel1    VARCHAR;
    stNivel2    VARCHAR;
    stNivel3    VARCHAR;
    stNivel4    VARCHAR;
    stNivel5    VARCHAR;
BEGIN
    -- corrige VIGENCIA 1
    DELETE FROM economico.nivel_cnae_valor;
    stSQL := '
                 SELECT cod_cnae
                      , cod_estrutural
                   FROM economico.cnae_fiscal
               ORDER BY cod_cnae
                      ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP

        stNivel1 := substr(reRecord.cod_estrutural, 1,1);
        stNivel2 := substr(reRecord.cod_estrutural, 3,2);
        stNivel3 := substr(reRecord.cod_estrutural, 6,1);
        stNivel4 := substr(reRecord.cod_estrutural, 7,3);
        stNivel5 := substr(reRecord.cod_estrutural,11,2);

        INSERT
          INTO economico.nivel_cnae_valor
             ( cod_nivel
             , cod_vigencia
             , cod_cnae
             , valor
             )
        VALUES
             ( 1
             , 1
             , reRecord.cod_cnae
             , stNivel1
             );
        INSERT
          INTO economico.nivel_cnae_valor
             ( cod_nivel
             , cod_vigencia
             , cod_cnae
             , valor
             )
        VALUES
             ( 2
             , 1
             , reRecord.cod_cnae
             , stNivel2
             );
        INSERT
          INTO economico.nivel_cnae_valor
             ( cod_nivel
             , cod_vigencia
             , cod_cnae
             , valor
             )
        VALUES
             ( 3
             , 1
             , reRecord.cod_cnae
             , stNivel3
             );
        INSERT
          INTO economico.nivel_cnae_valor
             ( cod_nivel
             , cod_vigencia
             , cod_cnae
             , valor
             )
        VALUES
             ( 4
             , 1
             , reRecord.cod_cnae
             , stNivel4
             );
        INSERT
          INTO economico.nivel_cnae_valor
             ( cod_nivel
             , cod_vigencia
             , cod_cnae
             , valor
             )
        VALUES
             ( 5
             , 1
             , reRecord.cod_cnae
             , stNivel5
             );

    END LOOP;

    -- inclui VIGENCIA 2
    SELECT MAX(cod_cnae)
      INTO inCodCnae
      FROM economico.cnae_fiscal
         ;

    stSQL := '
                 SELECT *
                   FROM migra_cnae
               ORDER BY cod_estrutural
                      ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
        inCodCnae := inCodCnae + 1;

        INSERT
          INTO economico.cnae_fiscal
             ( cod_cnae
             , nom_atividade
             , cod_vigencia
             , cod_nivel
             , cod_estrutural
             )
        VALUES
             ( inCodCnae
             , reRecord.descricao
             , 2
             , reRecord.nivel
             , reRecord.cod_estrutural
             );

        stNivel1 := substr(reRecord.cod_estrutural, 1,1);
        stNivel2 := substr(reRecord.cod_estrutural, 3,2);
        stNivel3 := substr(reRecord.cod_estrutural, 6,1);
        stNivel4 := substr(reRecord.cod_estrutural, 7,3);
        stNivel5 := substr(reRecord.cod_estrutural,11,2);

        INSERT
          INTO economico.nivel_cnae_valor
             ( cod_nivel
             , cod_vigencia
             , cod_cnae
             , valor
             )
        VALUES
             ( 1
             , 2
             , inCodCnae
             , stNivel1
             );
        INSERT
          INTO economico.nivel_cnae_valor
             ( cod_nivel
             , cod_vigencia
             , cod_cnae
             , valor
             )
        VALUES
             ( 2
             , 2
             , inCodCnae
             , stNivel2
             );
        INSERT
          INTO economico.nivel_cnae_valor
             ( cod_nivel
             , cod_vigencia
             , cod_cnae
             , valor
             )
        VALUES
             ( 3
             , 2
             , inCodCnae
             , stNivel3
             );
        INSERT
          INTO economico.nivel_cnae_valor
             ( cod_nivel
             , cod_vigencia
             , cod_cnae
             , valor
             )
        VALUES
             ( 4
             , 2
             , inCodCnae
             , stNivel4
             );
        INSERT
          INTO economico.nivel_cnae_valor
             ( cod_nivel
             , cod_vigencia
             , cod_cnae
             , valor
             )
        VALUES
             ( 5
             , 2
             , inCodCnae
             , stNivel5
             );

    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT        migra_cnae();
DROP FUNCTION migra_cnae();
DROP TABLE    migra_cnae;


ALTER TABLE economico.cnae_fiscal ADD COLUMN risco CHAR(1) NOT NULL DEFAULT 'N';
ALTER TABLE economico.cnae_fiscal ADD CONSTRAINT ck_cnae_fiscal_1 CHECK (risco IN ('A', 'B', 'N'));


----------------
-- Ticket #23328
----------------

DROP FUNCTION arrecadacao.fn_consulta_endereco_mata_saojoao(INTEGER);


----------------
-- Ticket #22494
----------------

DROP FUNCTION arrecadacao.fn_carne_parcela( INTEGER );


----------------
-- Ticket #23364
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
SELECT 3093
     , 366
     , 'FLRelatorioPagadores.php'
     , 'emitir'
     , 14
     , ''
     , 'Relatório de Pagadores'
     , TRUE
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.acao
              WHERE cod_acao = 3093
           )
     ;

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 5
     , 33
     , 9
     , 'Relatório de Pagadores'
     , 'LHRelatorioPagadores.php'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 5
                AND cod_modulo    = 33
                AND cod_relatorio = 9
           )
     ;


----------------
-- Ticket #23408
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inCodModelo     INTEGER;
BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2015'
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;
    IF FOUND THEN
        PERFORM 1
           FROM arrecadacao.modelo_carne
          WHERE nom_arquivo = 'RCarneTFFMataSaoJoao2016.class.php'
              ;
        IF NOT FOUND THEN
            SELECT MAX(cod_modelo) + 1
              INTO inCodModelo
              FROM arrecadacao.modelo_carne
                 ;
            INSERT INTO arrecadacao.modelo_carne        VALUES (inCodModelo, 'Carne T.F.F. 2016', 'RCarneTFFMataSaoJoao2016.class.php', 14, FALSE);
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (inCodModelo, 963 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (inCodModelo, 964 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (inCodModelo, 978 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (inCodModelo, 979 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (inCodModelo, 1677);
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (inCodModelo, 1678);
        END IF;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #23410
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inCodModelo     INTEGER;
BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2015'
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;
    IF FOUND THEN
        PERFORM 1
           FROM arrecadacao.modelo_carne
          WHERE nom_arquivo = 'RCarneIPTUMataSaoJoao2016.class.php'
              ;
        IF NOT FOUND THEN
            SELECT MAX(cod_modelo) + 1
              INTO inCodModelo
              FROM arrecadacao.modelo_carne
                 ;
            INSERT INTO arrecadacao.modelo_carne        VALUES (inCodModelo, 'Carne I.P.T.U. 2016', 'RCarneIPTUMataSaoJoao2016.class.php', 12, FALSE);
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (inCodModelo, 963 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (inCodModelo, 964 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (inCodModelo, 978 );
            INSERT INTO arrecadacao.acao_modelo_carne   VALUES (inCodModelo, 979 );
        END IF;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #23442
----------------

   --
   -- Insere a função.
   --
   CREATE OR REPLACE function public.manutencao_funcao( intCodmodulo       INTEGER
                                                      , intCodBiblioteca   INTEGER
                                                      , varNomeFunc        VARCHAR
                                                      , intCodTiporetorno INTEGER)
   RETURNS integer as $$
   DECLARE
      intCodFuncao INTEGER := 0;
      varAux       VARCHAR;
   BEGIN

      SELECT cod_funcao
        INTO intCodFuncao
        FROM administracao.funcao
       WHERE cod_modulo                = intCodmodulo
         AND cod_biblioteca            = intCodBiblioteca
         AND Lower(Btrim(nom_funcao))  = Lower(Btrim(varNomeFunc))
      ;

      IF FOUND THEN
         DELETE FROM administracao.corpo_funcao_externa  WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.funcao_externa        WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.funcao_referencia     WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.parametro             WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.variavel              WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.funcao                WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
      END IF;

     -- Raise Notice ' Entrou 1 ';

     SELECT (max(cod_funcao)+1)
       INTO intCodFuncao
       FROM administracao.funcao
      WHERE cod_modulo       = intCodmodulo
        AND cod_biblioteca   = intCodBiblioteca
     ;

     --varAux := varNomeFunc || '  -   ' || To_Char( intCodFuncao, '999999') ;
     --RAise Notice '=> % ', varAux;

     IF intCodFuncao IS NULL OR intCodFuncao = 0 THEN
        intCodFuncao := 1;
     END IF;

     INSERT INTO administracao.funcao  ( cod_modulo
                                       , cod_biblioteca
                                       , cod_funcao
                                       , cod_tipo_retorno
                                       , nom_funcao)
                                VALUES ( intCodmodulo
                                       , intCodBiblioteca
                                       , intCodFuncao
                                       , intCodTiporetorno
                                       , varNomeFunc);

      RETURN intCodFuncao;

   END;
   $$ LANGUAGE 'plpgsql';

   --
   -- Inclusão de Váriaveis.
   --
   CREATE OR REPLACE function public.manutencao_variavel( intCodmodulo       INTEGER
                                                        , intCodBiblioteca   INTEGER
                                                        , intCodFuncao       INTEGER
                                                        , varNomVariavel     VARCHAR
                                                        , intTipoVariavel    INTEGER)
   RETURNS integer as $$
   DECLARE
      intCodVariavel INTEGER := 0;
   BEGIN

      If intCodFuncao != 0 THEN
         SELECT COALESCE((max(cod_variavel)+1),1)
           INTO intCodVariavel
           FROM administracao.variavel
          WHERE cod_modulo       = intCodmodulo
            AND cod_biblioteca   = intCodBiblioteca
            AND cod_funcao       = intCodFuncao
         ;

         INSERT INTO administracao.variavel ( cod_modulo
                                            , cod_biblioteca
                                            , cod_funcao
                                            , cod_variavel
                                            , nom_variavel
                                            , cod_tipo )
                                     VALUES ( intCodmodulo
                                            , intCodBiblioteca
                                            , intCodFuncao
                                            , intCodVariavel
                                            , varNomVariavel
                                            , intTipoVariavel
                                            );
      END IF;

      RETURN intCodVariavel;
   END;
   $$ LANGUAGE 'plpgsql';


   --
   -- Inclusão de parametro.
   --
   CREATE OR REPLACE function public.manutencao_parametro( intCodmodulo       INTEGER
                                                         , intCodBiblioteca   INTEGER
                                                         , intCodFuncao       INTEGER
                                                         , intCodVariavel     INTEGER)
   RETURNS VOID as $$
   DECLARE
      intOrdem INTEGER := 0;
   BEGIN
      If intCodFuncao != 0 THEN
         SELECT COALESCE((max(ordem)+1),1)
           INTO intOrdem
           FROM administracao.parametro
          WHERE cod_modulo       = intCodmodulo
            AND cod_biblioteca   = intCodBiblioteca
            AND cod_funcao       = intCodFuncao
         ;

         INSERT INTO administracao.parametro ( cod_modulo
                                             , cod_biblioteca
                                             , cod_funcao
                                             , cod_variavel
                                             , ordem)
                                      VALUES ( intCodmodulo
                                             , intCodBiblioteca
                                             , intCodFuncao
                                             , intCodVariavel
                                             , intOrdem );
      End If;

      RETURN;
   END;
   $$ LANGUAGE 'plpgsql';


   --
   -- Inclusão de parametro.
   --
   CREATE OR REPLACE function public.manutencao_funcao_externa( intCodmodulo       INTEGER
                                                              , intCodBiblioteca   INTEGER
                                                              , intCodFuncao       INTEGER )
   RETURNS VOID as $$
   DECLARE
      --intCodFuncao INTEGER;
   BEGIN

      -- RAise Notice ' =====> % ', intCodFuncao;

      If intCodFuncao != 0 THEN
         INSERT INTO administracao.funcao_externa ( cod_modulo
                                                  , cod_biblioteca
                                                  , cod_funcao
                                                  , comentario
                                                  )
                                           VALUES ( intCodmodulo
                                                  , intCodBiblioteca
                                                  , intCodFuncao
                                                  , ''
                                                  );
      END IF;
      RETURN;
   END;
   $$ LANGUAGE 'plpgsql';

   --
   -- Função principal.
   --
   CREATE OR REPLACE function public.manutencao() RETURNS VOID as $$
   DECLARE
      intCodFuncao   INTEGER;
      intCodVariavel INTEGER;
   BEGIN

      -- 1 | INTEIRO
      -- 2 | TEXTO
      -- 3 | BOOLEANO
      -- 4 | NUMERICO
      -- 5 | DATA

    --Inclusão de função interna arrecadacao/fn_acrescimo_indice.plsql

        PERFORM 1
           FROM administracao.configuracao
          WHERE cod_modulo = 2
            AND exercicio  = '2015'
            AND parametro  = 'cnpj'
            AND valor      = '94068418000184'
              ;
        IF FOUND THEN

          intCodFuncao   := public.manutencao_funcao   (  28, 2, 'fn_urm_mariana', 4);
                                                     --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )

          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtVencimento'  , 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel      );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtDataCalculo' , 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel      );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'nuValor'       , 4 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel      );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodAcrescimo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel      );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodTipo'     , 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel      );

          PERFORM           public.manutencao_funcao_externa( 28, 2, intCodFuncao );

            INSERT
              INTO monetario.formula_acrescimo
                 ( cod_acrescimo
                 , cod_tipo
                 , cod_modulo
                 , cod_biblioteca
                 , cod_funcao
                 )
            VALUES
                 ( 1
                 , 1
                 , 28
                 , 2
                 , intCodFuncao
                 );

        END IF;
      RETURN;
   END;
   $$ LANGUAGE 'plpgsql';

   --
   -- Execuçao  função.
   --
   Select public.manutencao();
   Drop Function public.manutencao();
   Drop Function public.manutencao_funcao(integer, integer, varchar, integer );
   Drop Function public.manutencao_variavel( integer, integer, integer, varchar, integer );
   Drop Function public.manutencao_parametro( integer, integer, integer, integer );
   Drop Function public.manutencao_funcao_externa( integer, integer, integer ) ;


