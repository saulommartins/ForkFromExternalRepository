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
* Versao 2.05.0
*
* Fabio Bertoldi - 20151123
*
*/

----------------
-- Ticket #23413
----------------

CREATE TABLE familia(
    estrutural      VARCHAR(7),
    descricao       VARCHAR(100)
);

INSERT INTO familia(estrutural, descricao) VALUES ('002.000','equipamentos/materiais p/escritorio/escola/artes plasticas');
INSERT INTO familia(estrutural, descricao) VALUES ('003.000','servicos técnicos: projetos/auditorias/ consultorias/assessorias');
INSERT INTO familia(estrutural, descricao) VALUES ('007.000','serviços de engenharia/obras: resíduos sólidos');
INSERT INTO familia(estrutural, descricao) VALUES ('008.000','serviços de engenharia/obras: edificações');
INSERT INTO familia(estrutural, descricao) VALUES ('009.000','serviços de engenharia/obras: rodovias, ferrovias e aeroportos');
INSERT INTO familia(estrutural, descricao) VALUES ('010.000','serviços de engenharia/obras: obras-de-arte-especiais');
INSERT INTO familia(estrutural, descricao) VALUES ('011.000','serviços de engenharia/obras: urbanização');
INSERT INTO familia(estrutural, descricao) VALUES ('012.000','serviços de engenharia/obras: infraestrutura de energia');
INSERT INTO familia(estrutural, descricao) VALUES ('013.000','serviços de engenharia/obras: saneamento');
INSERT INTO familia(estrutural, descricao) VALUES ('014.000','serviços de engenharia/obras: obras portuárias, marítimas e fluviais');
INSERT INTO familia(estrutural, descricao) VALUES ('015.000','serviços de engenharia/obras: serviços especializados para construção');
INSERT INTO familia(estrutural, descricao) VALUES ('016.000','serviços de engenharia/obras: inst. elétricas, hidráulicas e outras inst. em construções');
INSERT INTO familia(estrutural, descricao) VALUES ('017.000','serviços de engenharia/obras: serviços técnicos de engenharia e arquitetura');
INSERT INTO familia(estrutural, descricao) VALUES ('029.000','serviços: credenciamento de serviços de educação');
INSERT INTO familia(estrutural, descricao) VALUES ('030.000','serviços: credenciamento de serviços de saúde');
INSERT INTO familia(estrutural, descricao) VALUES ('031.000','servicos: terceirizacao de mao-de-obra especializada');
INSERT INTO familia(estrutural, descricao) VALUES ('033.000','materiais p/escritório');
INSERT INTO familia(estrutural, descricao) VALUES ('034.000','materiais/ suprimentos p/informatica');
INSERT INTO familia(estrutural, descricao) VALUES ('035.000','equipamentos p/informatica');
INSERT INTO familia(estrutural, descricao) VALUES ('037.000','servicos: terceirizacao de mao-de-obra');
INSERT INTO familia(estrutural, descricao) VALUES ('042.000','servicos: transporte de cargas e passageiros');
INSERT INTO familia(estrutural, descricao) VALUES ('045.000','servicos: graficos/similares');
INSERT INTO familia(estrutural, descricao) VALUES ('047.000','servicos: som, imagem e programacao visual');
INSERT INTO familia(estrutural, descricao) VALUES ('052.000','servicos: manutencao de veiculos, equipamentos e aeronaves');
INSERT INTO familia(estrutural, descricao) VALUES ('057.000','servicos: manut/equip/escrit/eletrodomesticos/refrigeracao');
INSERT INTO familia(estrutural, descricao) VALUES ('059.000','servicos: serralheria/marcen./carpin./metalurgica/fundicao');
INSERT INTO familia(estrutural, descricao) VALUES ('062.000','servicos: locacao de veiculos, equipamentos e aeronaves');
INSERT INTO familia(estrutural, descricao) VALUES ('063.000','serviços: locacao de imoveis');
INSERT INTO familia(estrutural, descricao) VALUES ('064.000','aquisição de imoveis');
INSERT INTO familia(estrutural, descricao) VALUES ('070.000','maquinas p/autenticar/registrar/franquear e similares');
INSERT INTO familia(estrutural, descricao) VALUES ('072.000','servicos: vigilancia/seguranca/transporte de valores');
INSERT INTO familia(estrutural, descricao) VALUES ('077.000','servicos: alimentacao');
INSERT INTO familia(estrutural, descricao) VALUES ('082.000','servicos: hotelaria/agencias de viagem e turismo');
INSERT INTO familia(estrutural, descricao) VALUES ('097.000','servicos: bilheteria / estacionamento');
INSERT INTO familia(estrutural, descricao) VALUES ('105.000','livros/publicacoes/revistas');
INSERT INTO familia(estrutural, descricao) VALUES ('107.000','servicos: seguros');
INSERT INTO familia(estrutural, descricao) VALUES ('112.000','servicos: contratacao parceria/invest./arrend/merchandising');
INSERT INTO familia(estrutural, descricao) VALUES ('113.000','servicos: contratacao instituicao de ensino superior');
INSERT INTO familia(estrutural, descricao) VALUES ('117.000','servicos: informatica-software/hardware');
INSERT INTO familia(estrutural, descricao) VALUES ('120.000','papel/papelao/cartao/cartolina');
INSERT INTO familia(estrutural, descricao) VALUES ('122.000','servicos: fornecimento de vales/tickets');
INSERT INTO familia(estrutural, descricao) VALUES ('127.000','servicos: analises clinicas/laborat. e exames medicos/odont.');
INSERT INTO familia(estrutural, descricao) VALUES ('140.000','equipamentos/materiais p/recreacao/deficientes');
INSERT INTO familia(estrutural, descricao) VALUES ('150.000','instrumentos musicais/componentes/acessorios');
INSERT INTO familia(estrutural, descricao) VALUES ('160.000','equipamentos/materiais esportivos');
INSERT INTO familia(estrutural, descricao) VALUES ('185.000','embalagens em geral/cordas/barbantes/fitas (exceto p/med.)');
INSERT INTO familia(estrutural, descricao) VALUES ('205.000','bandeiras/flamulas/acessorios');
INSERT INTO familia(estrutural, descricao) VALUES ('215.000','servicos: insignias/brasoes/escudos/medalhas/trofeus/brindes');
INSERT INTO familia(estrutural, descricao) VALUES ('245.000','vestuarios/uniformes (exceto vestuario de seguranca)');
INSERT INTO familia(estrutural, descricao) VALUES ('250.000','calcados/bolsas/malas/mochila (exceto de seguranca)');
INSERT INTO familia(estrutural, descricao) VALUES ('255.000','materiais de armarinho/aviamentos');
INSERT INTO familia(estrutural, descricao) VALUES ('260.000','materiais p/cama/mesa/banho');
INSERT INTO familia(estrutural, descricao) VALUES ('270.000','equipamentos/materiais p/microfilmagem');
INSERT INTO familia(estrutural, descricao) VALUES ('285.000','eletrodomesticos');
INSERT INTO familia(estrutural, descricao) VALUES ('290.000','equipamentos/componentes/acessorios p/climatizacao');
INSERT INTO familia(estrutural, descricao) VALUES ('295.000','equipamentos/materiais/acessorios p/projecao/video/foto/som');
INSERT INTO familia(estrutural, descricao) VALUES ('320.000','moveis/estofados/componentes em geral');
INSERT INTO familia(estrutural, descricao) VALUES ('345.000','colchoes/colchonetes/travesseiros/almofadas/revestimentos');
INSERT INTO familia(estrutural, descricao) VALUES ('350.000','equipamentos/materiais/acessorios p/uso comercial/industrial');
INSERT INTO familia(estrutural, descricao) VALUES ('360.000','utensilios e materiais descartaveis p/copa/cozinha');
INSERT INTO familia(estrutural, descricao) VALUES ('380.000','equipamentos/materiais p/limpeza/higiene (uso geral)');
INSERT INTO familia(estrutural, descricao) VALUES ('390.000','equipamentos/acessorios p/acampamento');
INSERT INTO familia(estrutural, descricao) VALUES ('395.000','equipamentos/componentes/acessorios p/radiotelecomunicacao');
INSERT INTO familia(estrutural, descricao) VALUES ('397.000','equipamentos/componentes/acessorios p/radiodifusao');
INSERT INTO familia(estrutural, descricao) VALUES ('400.000','equipamentos/componentes/acessorios p/telefonia');
INSERT INTO familia(estrutural, descricao) VALUES ('405.000','equipamentos/componentes/acessorios p/medicao');
INSERT INTO familia(estrutural, descricao) VALUES ('410.000','equipamentos p/geracao/distribuicao de energia eletrica');
INSERT INTO familia(estrutural, descricao) VALUES ('420.000','componentes p/equipamentos eletricos/eletronicos');
INSERT INTO familia(estrutural, descricao) VALUES ('428.000','equipamentos p/controle de pessoal');
INSERT INTO familia(estrutural, descricao) VALUES ('435.000','equipamentos/componentes/acessorios p/solda (em geral)');
INSERT INTO familia(estrutural, descricao) VALUES ('440.000','feramentas manuais (uso geral)');
INSERT INTO familia(estrutural, descricao) VALUES ('445.000','equipamentos eletricos p/oficinas (uso geral)');
INSERT INTO familia(estrutural, descricao) VALUES ('450.000','ferragens/abrasivos');
INSERT INTO familia(estrutural, descricao) VALUES ('452.000','arames/telas');
INSERT INTO familia(estrutural, descricao) VALUES ('460.000','madeiras em geral');
INSERT INTO familia(estrutural, descricao) VALUES ('461.000','materia-prima plastica/sintetica/borracha/derivados');
INSERT INTO familia(estrutural, descricao) VALUES ('463.000','materia-prima p/metalurgia');
INSERT INTO familia(estrutural, descricao) VALUES ('465.000','equipamentos/materiais p/construcao civil');
INSERT INTO familia(estrutural, descricao) VALUES ('475.000','equipamentos/materiais p/instalacoes eletricas');
INSERT INTO familia(estrutural, descricao) VALUES ('480.000','equip./materiais p/instalacoes hidrosanitarias e gas natural');
INSERT INTO familia(estrutural, descricao) VALUES ('495.000','vidros planos/espelhos');
INSERT INTO familia(estrutural, descricao) VALUES ('505.000','materiais p/decoracao de interiores');
INSERT INTO familia(estrutural, descricao) VALUES ('510.000','obras de arte/objetos decorativos');
INSERT INTO familia(estrutural, descricao) VALUES ('515.000','equipamentos/materiais de seguranca e protecao');
INSERT INTO familia(estrutural, descricao) VALUES ('535.000','bombas/motobombas/compressores/componentes/acessorios');
INSERT INTO familia(estrutural, descricao) VALUES ('540.000','equipamentos/materiais p/irrigacao');
INSERT INTO familia(estrutural, descricao) VALUES ('548.000','equipamentos/materiais/suprimentos tratamento de agua/esgoto');
INSERT INTO familia(estrutural, descricao) VALUES ('550.000','equipamentos/pecas/aces. p/constr./conserv. rodovias/portos');
INSERT INTO familia(estrutural, descricao) VALUES ('555.000','equipamentos/pecas/acessorios p/mineracao/escavacao');
INSERT INTO familia(estrutural, descricao) VALUES ('565.000','equipamentos/acessorios p/transporte de mercadorias');
INSERT INTO familia(estrutural, descricao) VALUES ('580.000','equipamentos/pecas/acessorios p/ajardinamento');
INSERT INTO familia(estrutural, descricao) VALUES ('593.000','elevadores/pontes rolantes/guindastes/talhas');
INSERT INTO familia(estrutural, descricao) VALUES ('595.000','veiculos');
INSERT INTO familia(estrutural, descricao) VALUES ('600.000','equipamentos/pecas/materiais/acessorios p/conserv. veiculos');
INSERT INTO familia(estrutural, descricao) VALUES ('685.000','equipamentos/pecas/acessorios p/agricultura/pecuaria e pesca');
INSERT INTO familia(estrutural, descricao) VALUES ('736.000',' alimentacao humana especial/manipuladas/fracionada');
INSERT INTO familia(estrutural, descricao) VALUES ('745.000','pneus/camaras/protetores/materiais p/consertos');
INSERT INTO familia(estrutural, descricao) VALUES ('748.000','equipamentos/pecas/acessorios p/navegacao');
INSERT INTO familia(estrutural, descricao) VALUES ('750.000','materiais/acessorios/pecas fundidas');
INSERT INTO familia(estrutural, descricao) VALUES ('754.000','equipamentos p/lancamentos/pouso/manobras de aeronaves');
INSERT INTO familia(estrutural, descricao) VALUES ('757.000','combustiveis/lubrificantes/derivados de petroleo');
INSERT INTO familia(estrutural, descricao) VALUES ('758.000','botijoes/instalacoes industriais de gas glp');
INSERT INTO familia(estrutural, descricao) VALUES ('760.000','armamentos/explosivos/municoes');
INSERT INTO familia(estrutural, descricao) VALUES ('773.000','alimentacao humana - prod.origem animal in natura');
INSERT INTO familia(estrutural, descricao) VALUES ('775.000','alimentacao humana - prod.especial/manipulados/pre-elaborado');
INSERT INTO familia(estrutural, descricao) VALUES ('779.000','alimentacao humana-prod.origem animal embutidos');
INSERT INTO familia(estrutural, descricao) VALUES ('784.000','alimentacao humana - produtos de origem vegetal in natura');
INSERT INTO familia(estrutural, descricao) VALUES ('788.000','alimentacao humana - laticinios e correlatos');
INSERT INTO familia(estrutural, descricao) VALUES ('792.000','alimentacao humana - produtos nao pereciveis');
INSERT INTO familia(estrutural, descricao) VALUES ('796.000','alimentacao humana - produtos de panificacao');
INSERT INTO familia(estrutural, descricao) VALUES ('802.000','alimentacao humana: enteral/oral');
INSERT INTO familia(estrutural, descricao) VALUES ('803.000','alimentacao humana: produtos coloniais');
INSERT INTO familia(estrutural, descricao) VALUES ('805.000','equipamentos e gases uso hopitalar/laboratorial/industrial');
INSERT INTO familia(estrutural, descricao) VALUES ('820.000','equipamentos/materiais p/industria farmaceutica');
INSERT INTO familia(estrutural, descricao) VALUES ('830.000','equipamentos/materiais p/laboratorio');
INSERT INTO familia(estrutural, descricao) VALUES ('855.000','diagnostica');
INSERT INTO familia(estrutural, descricao) VALUES ('870.000','equipamentos/materiais medico-hospitalares/enfermagem');
INSERT INTO familia(estrutural, descricao) VALUES ('880.000','medicamentos de uso humano');
INSERT INTO familia(estrutural, descricao) VALUES ('882.000','medicamentos importados (uso humano)');
INSERT INTO familia(estrutural, descricao) VALUES ('884.000','medicamentos de uso humano - excepcionais');
INSERT INTO familia(estrutural, descricao) VALUES ('886.000','medicamentos de uso humano - especiais');
INSERT INTO familia(estrutural, descricao) VALUES ('888.000','medicamentos de uso humano - genericos');
INSERT INTO familia(estrutural, descricao) VALUES ('890.000','materiais p/higiene pessoal/profilaxia');
INSERT INTO familia(estrutural, descricao) VALUES ('905.000','servicos: orteses/proteses');
INSERT INTO familia(estrutural, descricao) VALUES ('910.000','equipamentos/materiais odontologicos');
INSERT INTO familia(estrutural, descricao) VALUES ('930.000','equipamentos/materiais/medicamentos veterinarios');
INSERT INTO familia(estrutural, descricao) VALUES ('950.000','animais');
INSERT INTO familia(estrutural, descricao) VALUES ('960.000','forragens e outros alimentos p/animais');
INSERT INTO familia(estrutural, descricao) VALUES ('965.000','adubos/corretivos do solo');
INSERT INTO familia(estrutural, descricao) VALUES ('970.000','defensivos agricolas/domesticos');
INSERT INTO familia(estrutural, descricao) VALUES ('980.000','sementes/mudas de plantas');
INSERT INTO familia(estrutural, descricao) VALUES ('990.000','produtos quimicos de limpeza/higiene');


CREATE TABLE subfamilia(
    estrutural      VARCHAR(7),
    descricao       VARCHAR(100)
);

INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.089','tinta para carimbos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.177','equipamentos p/escritorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.178','equipamentos p/reprografia/grafica/copiadoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.179','equipamentos didaticos/ensino/treinamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.221','filmes p/ plastificacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.500','materiais/suprimentos p/equipamentos de escritorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.504','materiais permanentes p/escritorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.508','materiais de consumo p/escritorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.514','materiais permanentes didaticos/escolares/desenho tecnico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.518','materiais de consumo didaticos/escolares/desenho tecnico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.522','materiais p/encadernacao/envelopamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.526','materiais p/arquivamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.528','materiais de consumo graficos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('002.632','pinceis profissional');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('003.001','auditoria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('003.002','assessoria em geral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('003.089','consultoria em geral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('003.133','desenho');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('003.180','estudo / analise de medicamentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('003.500','maqueteira');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('003.632','projetos em geral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('007.001','aterro sanitário ');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('007.002','coleta de resíduos sólidos urbanos (rsu)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('007.003','coleta e transporte de resíduos sólidos de serviços de saúde (rsss)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('007.004','coleta seletiva');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('007.005','disposição final de resíduos sólidos de serviços de saúde (rsss)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('007.006','disposição final de resíduos sólidos urbanos (rsu)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('007.007','estação de transbordo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('007.008','estação/central de tratamento de resíduos ');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('007.009','recuperação de área degradada');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('007.010','transporte de resíduos sólidos urbanos (rsu)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('007.011','triagem de resíduos sólidos urbanos (rsu)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('007.012','unidade de triagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('007.099','outro serviço de resíduos sólidos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.001','administrativo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.002','albergue/abrigo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.003','auditório/teatro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.004','creas/cras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.005','delegacia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.006','depósito/pavilhão');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.007','edifício-garagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.008','escola/creche');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.009','estação/terminal de passageiros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.010','ginásio de esportes/estádio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.011','habitação');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.012','hospital');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.013','laboratório');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.014','museu');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.015','posto de saúde/ubs');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.016','praça de pedágio/postos de pesagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.017','presídio/penitenciária');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.018','restaurante (popular)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('008.099','outra obra/serviço de edificações');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('009.001','ferrovias de superfície ou subterrâneas, inclusive para metropolitanos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('009.002','pista aeroportuária');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('009.003','rodovias e vias rurais pavimentadas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('009.004','sinalização horizontal  em rodovias e aeroportos ');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('009.005','sinalização vertical em rodovias e aeroportos ');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('009.006','vias rurais não pavimentadas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('009.099','outra obra de infraestrutura rodoviária/ferroviária/aeroportuária');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('010.001','passarela');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('010.002','ponte');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('010.003','túnel');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('010.004','viaduto / elevada');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('010.099','outra obra-de-arte-especiais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.001','ciclovia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.002','iluminação pública');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.003','infraestrutura urbana (loteamentos)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.004','paisagismo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.005','passeios públicos (calçadas)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.006','pavimentação asfáltica em vias urbanas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.007','pavimentação em bloco de concreto ');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.008','pavimentação em concreto de cimento portland em vias urbanas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.009','pavimentação em paralelepípedo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.010','pavimentação em pedra irregular');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.011','praças, parques e áreas de lazer');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.012','sinalização horizontal de vias urbanas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.013','sinalização semafórica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.014','sinalização vertical de vias urbanas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.015','vias urbanas não pavimentadas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('011.099','outra obra/serviço em via urbana');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('012.001','barragem e represa para geração de energia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('012.002','estação e subestação de energia elétrica ');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('012.003','gasoduto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('012.004','mineroduto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('012.005','oleoduto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('012.006','rede de distribuição de energia elétrica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('012.007','rede de transmissão de energia elétrica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('012.008','usina hidrelétrica, eólica, nuclear, termelétrica, etc.');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('012.099','outra obra/serviço de infraestrutura de energia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.001','barragem para captação de água');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.002','adutora');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.003','canal/galeria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.004','coleta e transporte de lodo/esgoto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.005','estação de bombeamento de água');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.006','estação de bombeamento de esgoto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.007','estação de captação de água');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.008','estação de tratamento de água ');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.009','estação de tratamento de esgoto ');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.010','fossa séptica/sumidouro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.011','perfuração/construção de poço de água');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.012','rede coletora');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.013','rede de distribuição');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.014','rede de drenagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.015','reservatório');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('013.099','outra obra/serviço de saneamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('014.001','aterro hidráulico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('014.002','barragem, represa e diques para navegação');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('014.003','dragagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('014.004','eclusas e canais de navegação');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('014.005','emissário submarino');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('014.006','enrocamentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('014.007','hidrovia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('014.008','instalação de cabos submarinos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('014.009','instalações portuárias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('014.010','portos e marinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('014.099','outra obra portuária, marítima ou fluvial');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.001','cercamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.002','contenção de taludes e encostas/muros de arrimo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.003','cortina atirantada');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.004','demolições e implosões');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.005','derrocamento (desmonte de rocha)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.006','fundações');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.007','montagem e desmontagem de andaimes e plataformas de trabalho');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.008','montagem e desmontagem de estruturas temporárias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.009','montagem e desmontagem de fôrmas para concreto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.010','serviços de impermeabilização');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.011','serviços de reforço e recuperação estrutural');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.012','serviços de terraplenagem ');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.013','sondagens e estudos geotécnicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('015.099','outro serviço especializado para construção');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('016.001','elevadores e escadas rolantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('016.002','instalações de alarmes, supervisão e automação predial');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('016.003','instalações de comunicação de dados e televisão a cabo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('016.004','instalações de controle de acesso e circuito fechado de televisão');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('016.005','instalações de eletricidade e iluminação (cabos e instalações elétricas)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('016.006','instalações de gás, fluidos e vapor');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('016.007','instalações de prevenção e combate à incêndio (ppci)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('016.008','instalações de refrigeração, climatização e aquecimento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('016.009','instalações de telefonia, de comunicações e sonorização ambiente');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('016.010','instalações de ventilação e exaustão');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('016.011','instalações hidráulicas e sanitárias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('016.012','sistema de proteção contra descargas atmosféricas (spda)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('016.099','outro serviço de instalações');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.001','assessorias ou consultorias técnicas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.002','auditorias de obras e serviços de engenharia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.003','cadastramento  imobiliário');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.004','elaboração de anteprojeto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.005','elaboração de orçamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.006','elaboração de projeto básico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.007','elaboração de projeto executivo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.008','ensaios tecnológicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.009','estudos de impacto ambiental-eia/relatório de impacto ambiental-rima');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.010','estudos de viabilidade técnica e econômica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.011','estudos técnicos/elaboração de planos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.012','fiscalização, supervisão ou gerenciamento de obras ou serviços');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.013','georreferenciamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.014','levantamentos aerofotogramétricos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.015','levantamentos topográficos, batimétricos e geodésicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.016','licenciamento ambiental');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.017','maquetes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.018','pareceres, perícias e avaliações');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('017.099','outros serviços técnicos de engenharia e arquitetura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('029.001','aquisição de vagas  ensino infantil');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('029.002','aquisição de vagas ensino fundamental');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('029.003','aquisição de vagas ensino médio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('029.099','outros credenciamentos de serviços de educação');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('030.001','serviços de clinicas médicas/odontológicas/hospitais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('030.002','serviços de exames laboratoriais/imagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('030.099','outros credenciamentos de serviços de saúde');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.733','tratamento e manutencao de agua de piscinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.734','limpeza e higienizacao de reservatorios de agua potavel');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.735','servicos de nutricao enteral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.736','servicos de veterinario/laboratorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.737','servicos tecnico de enfermagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.738','servicos de creche');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.739','servicos de desinsetizacao e desratizacao de predios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.740','servicos de medicina e seguranca do trabalho');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.741','servicos de condutor fluvial');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.742','servicos de restauracaoes em geral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.743','servicos de operador telecomunicacoes aeronauticas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.744','servicos de taquigrafia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.745','servicos de apoio maritimo/fluvial');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.746','servicos de manipulacao/acondic/transp. materiais perigosos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.747','servicos de monitoramento revistas/jornais/tv/radio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.748','servicos sist. operac. prisionais/penais/educ. correcionais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.749','servico de revestimento e impermeabilizacao de pisos/paredes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.750','servicos de profissional farmaceutico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.751','servicos de regente (maestro)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.752','servicos de esterilizacao de produtos hospitalares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.753','servico de revest./impermeab. do solo p/ aterro sanitario');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.754','servico de revestimento em prfv em diques/tanques');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.755','servicos de afinacoes de pianos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.756','servicos auxiliares p/ transporte aereo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.757','servicos tecnicos p/ habilitacao de condutores de veiculos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.758','servicos de assistencia juridica aos presos e familiares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.759','servico tecnico de oper/manut equipamentos de barragem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.760','arbitragem/ginastica laboral/recreacao/atividades esportivas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.770','medicina preventiva/assistencial/aconselhamento telefonico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('031.799','servicos de limpeza de esgotos sem remocao de residuos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('033.089','carimbos, almofadas e tintas p/ carimbos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('034.001','acessorios limpeza/ferramentas p/ computadores/impressoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('034.002','acessorios/ micros/impressoras/scanners/ copiadora');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('034.089','cartuchos/refis/toners/fitas p/impressoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('034.133','discos flexiveis/opticos/cds');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('034.177','etiquetas auto-adesivas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('034.221','fitas streamers/ lto/ minicassete p/ computadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('034.779','transparencias p/impressoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.090','centrais de trabalho multifuncional');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.095','lousa digital');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.133','drivers');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.134','disco rigido');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.177','estabilizadores/no-breaks/short-breaks/fontes alimentacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.181','eq. p/microcomputadores/impressoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.182','equipamentos para sistema de backup');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.315','hardware/software deficientes fisicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.353','impressoras/ copiadoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.456','licencas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.501','memorias de expansao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.502','monitores de video');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.503','mouse');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.504','microcomputadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.505','monitores de interface');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.544','notebooks');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.564','tablets');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.632','placas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.735','scaners');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.736','softwares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.737','servidores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.739','dispositivo para guarda de dados e arquivos (storage)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.779','terminais/quiosques');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.780','teclados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('035.867','sistemas videoconferencia/ sistema acesso a dados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.001','auxiliares de servicos gerais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.735','servicos de portaria/recepcionista');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.736','servicos de digitadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.737','servicos de telefonista/videofonista');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.738','servicos de ascensoristas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.739','servicos de motoristas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.740','servicos de continuos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.741','servicos de cozinheiros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.742','servicos de eletricistas e mecanicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.743','servico guincho/auto-socorro 24h');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.744','servicos de lavagem/lubrificacao/troca de oleo e filtros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.749','servicos de armazenagem/controle/recebimento/expedicao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.751','servicos de limpeza de logradouros e predios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.752','servicos de capina/rocado/ajardinamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.753','servicos de lavanderia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.754','servicos de tinturaria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.755','servicos recolhimento lixo (remocao/transporte/deposito)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.756','servicos de ligacao/interrupcao de ramais prediais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.757','servicos de escavacao/aterro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.758','servicos de leitura/corte e ligacao de hidrometro/cavaletes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.759','servicos de teleatendimento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.760','servicos de leitura/corte/ligacao medidores urbano/rural');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('037.761','servicos de operador de estacao aeronautica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('042.735','servicos de transporte de carga por via maritima');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('042.736','servicos de transporte de carga por via terrestre');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('042.737','servicos de transporte de carga por via fluvial e lacruste');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('042.738','servicos de transporte de carga por via aerea');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('042.739','servicos de transporte de carga por via ferroviaria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('042.740','servicos de transporte de passageiros por via maritima');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('042.741','servicos de transporte de passageiros por via terrestre');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('042.742','servicos de transporte de passag. por via fluvial e lacruste');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('042.743','servicos de transporte de passageiros por via aerea');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('042.744','servicos de transporte de passageiros por via ferroviaria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('042.745','servicos de transporte/colocacao/remocao placas educativas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('042.746','servicos de transporte/armazenagem/organizacao de documentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.089','capas/cartoes papelao/fichas/caderno pers./pastas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.090','confeccao de carimbos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.177','encadernacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.178','fitas/ etiquetas auto-adesivas/rotulos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.221','formularios continuos brancos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.222','formularios continuos zebrados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.223','fotolitagem/editoracao grafica/plotagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.354','impressos formularios continuos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.355','impressos formularios planos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.356','bobinas personalizadas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.360','impressos formularios padronizados - pe');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.400','envelopes/ bloco anotacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.632','plastificacao de documentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.691','reprografia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('045.735','serigrafia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('047.735','servicos de programacao visual e projecao de imagens');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('047.737','servicos fotograficos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('047.738','servicos de transm. de programas on line, via fm e/ou am');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('047.739','servicos de publicidade e propaganda');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('047.740','servicos de placas e luminosos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('047.741','servicos de microfilmagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('047.742','servicos inst./mont./manut. sistemas de sonorizacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('047.743','servicos de artefatos de acrilico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('047.744','servicos de copiagem de fitas vhs e dvd');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.735','servicos de manutencao de aeronaves');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.736','servicos de manutencao de veiculos leves e pesados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.737','servicos de manutencao de maquinas e implementos agricolas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.738','servicos de manutencao de maquinas e equip. rodoviarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.739','servicos de manutencao de maquinas e equip. aeroportuarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.740','servicos de manutencao de maquinas e equip. portuarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.741','servicos de manutencao de elevadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.742','servicos de manut/confeccao de sinalizacao nautica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.743','servicos de manutencao/afericao/calibracao de balancas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.744','servicos de manutencao equipamento geral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.745','servicos de manut. prev./corret./recarga de ext');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.746','servicos de manut/confec/inst.de isolamento acustico/termico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.747','servicos de manut./calib./certif. de equip. p/ laboratorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.748','servicos de manut. equip. p/ monit. eletronico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.749','servicos de manut. preventiva/corretiva gases medicinais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.750','servicos de manut./afericao/calib. equipamentos/instrumentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.752','manutencao/recapagem/recauchutagem de pneus');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('052.999','pecas e acessorios p manutencao de veiculos maquinas equipam');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('057.735','servicos de manutencao de centrais telefonica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('057.736','servicos manut./inst./mont. sist. de climatizacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('057.737','servicos de manut./inst./mont. equipamentos de escritorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('057.738','servicos manut./inst./mont. de eletrodomesticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('057.739','servicos de manutencao/instalacao de camaras frias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('057.740','servico de manutencao estacao trabalho/impressoras/scanner');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('059.735','servicos de montagem de estantes em eventos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('059.736','servicos de confeccao de reservatorios/tanques metalico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('059.737','servicos de fundicao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('059.738','servicos de confeccao de portoes/cortinas/portas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('059.739','servicos de confeccao acessorios/pecas em elastomero.');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('059.740','servico de confeccao de boias de sinalizacao nautica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('059.741','servicos de confeccao/conserto/instalacao de persianas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('059.742','servico de confeccao materiais p/ sistema climatizacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('059.743','servicos de confeccao de moveis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('059.744','servicos de confeccao de box em acrilico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('062.735','servicos de locacao de aeronaves');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('062.736','servicos de locacao de veiculos leves e pesados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('062.737','servicos de locacao de maquinas agricolas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('062.738','servicos de locacao de maquinas rodoviarias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('062.739','servicos de locacao de equipamentos portuarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('062.740','servicos de locacao de equipamentos aeroportuarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('062.741','servicos de locacao de equipamentos em geral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('062.742','servicos de locacao/mont./manut./inst. cabines sa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('062.743','servicos loc/mont/manut/inst. de palco/som/ilum.');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('062.744','servicos de locacao/mont/manut/inst. contrladores eletronico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('062.745','servicos de locacao de software/hardware');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('062.746','servicos de locacao p/ translado e transporte de cadaveres');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('063.001','locacao de imoveis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('064.001','aquisição de imoveis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('070.001','maquinas autenticadoras/cheque');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('070.221','maquinas plastificadoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('070.353','maquinas impressoras codificadoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('070.500','maquinas registradoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('070.501','maquinas contadoras de cedulas e documentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('070.502','maquinas franqueadoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('070.503','maquinas cortadoras (estampadoras)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('070.535','detector de notas/ documentos falsos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('070.632','maquinas perfuradoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('070.675','quiosques auto-atendimento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('070.779','maquinas terminais de auto atendimento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('072.735','servico vigil.fis. armada autor.p/dep.de pol.fed do min.just');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('072.736','servico vigilancia por monitoramento eletronico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('072.737','servicos transporte de valores/contagem numerario');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('077.735','servicos de refeitorio/lanches');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('082.735','servicos: hospedagens/passagens/translados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('082.736','srevicos: hotelaria p/ convencoes/congressos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('097.735','servicos: bilheteria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('097.736','servicos: estacionamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('097.737','servicos: estacionamento e/ou manobrista');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.001','atlas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.089','cartas cartograficas/georeferencias/vetoriais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.133','dicionarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.456','leis, codigos, estatutos e regulamentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.458','livros tecnicos juridicos, politicos e administrativos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.459','livros tecnicos portugues e literatura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.460','livros tecnicos saude e servico social');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.461','livros tecnicos desenho, arquitetura e urbanismo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.462','livros tecnicos didatica/ensino/testes/fl. teste');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.463','livros tecnicos informatica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.464','livros tecnicos historia e geografia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.465','livros tecnicos ciencias/matematica/fisica/quimica/biologia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.466','livros tecnicos agricultura e veterinaria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.467','livros tecnicos contabilidade');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.468','livros religiao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.469','livros tecnicos didaticas/ensino (cont. 462)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.470','livros tecnicos pericia / criminalistica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.471','livros tecnicos didaticos/ensino (cont.469)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.691','revistas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('105.870','videos / fitas vhs / dvds educativos didaticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('107.735','servicos de seguros de vida');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('107.736','servicos de seguros de ramos de elementares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('107.737','servicos de seguros de saude');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.632','servicos: permissao de servicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.700','servicos: administracao vale pedagio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.735','servicos: contratacao parceria/investidores/merchandising');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.736','servicos: contratacao arrendamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.737','servicos: concessao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.738','servicos: treinamentos/convencoes/eventos/cursos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.739','contratacao: servicos discagem direta gratuita/outros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.740','servicos: franquia/postais telematicos/recolhimento/postagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.741','servicos: admistracao/distribuicao/emissao bilhetes loterias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.742','servicos: operacao balancas moveis/fixas controle cargas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.743','servicos: contratacao de servico de busca/entrega');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.744','servicos: contratacao servicos tele-taxi');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.745','servicos: assinaturas e taxacoes de jornais e periodicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.746','servicos: contratacao de segmento espacial/satelite');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.747','servicos: contratacao de infraestrutura cursos/trein./evento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.748','servicos: contratacao de telefonia fixa/movel');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.749','servicos: conexao dedicada internet');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('112.991','servicos de assistencia a pessoas e veiculos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('113.730','servicos: treinamentos/convencoes/eventos/cursos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('117.735','servicos: inst/mont. sist. de infor soft/hardware');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('117.736','servicos: manut/rep. sist. de infor soft/hardware');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('117.737','servicos de automacao eletronica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('117.738','servicos acesso internet');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('117.739','servicos de digitalizacao de documentos/ impressao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('117.740','servicos de manutencao/instalacao de terminais eletronicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('117.741','servicos de instalacao e operacao de call center');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('117.742','servicos telemetria / telecomando / software de supervisao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('120.090','cartolinas/cartoes/papeloes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('120.632','papel almaco');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('120.633','papel de expediente p/escrita/impressao/reprografia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('120.634','papel capa fantasia e capa sem fantasia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('120.635','papel p/desenho tecnico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('120.636','papeis especiais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('120.637','papel jornal');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('120.639','papel p/ heliogravura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('120.640','papel westerprint e westerledger');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('120.645','papel p/telex/fac-simile');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('122.735','servicos: fornecimento de vale-alimentacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('122.736','servicos: fornecimento vale-combustivel');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('122.737','servicos:fornecimento cartoes p/ manutencao de v');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('127.735','servicos: clinica ginast.laboral/ergo/fisioterapia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('127.736','servicos: medicos/odontologicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('127.737','servicos: hemodialese');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('127.738','servicos: analises clinicas/laboratoriais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('127.739','servicos: planos assistencia saude');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('127.755','servicos: analise de aguas/ alimentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('140.045','brinquedos/jogos educativos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('140.046','brinquedos/jogos recreativos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('140.047','bicicletas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('140.048','brinquedos / carrinhos / bonecas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('140.050','artigos p/ festas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('140.177','equipamentos recreativos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('140.456','lupas eletronicas mouse');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('140.632','produtos p/ deficientes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('150.045','bandinhas ritmicas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('150.089','componentes/acessorios p/instrumentos musicais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('150.353','instrumentos de cordas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('150.354','instrumentos de percussao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('150.355','instrumentos de sopro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('150.360','instrumentos eletronicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('160.045','bolas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('160.177','equipamentos p/ atletismo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('160.178','equipamentos p/ esportes de quadra');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('160.179','equipamentos p/ ginastica olimpica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('160.180','equipamentos p/ musculacao e aerobica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('160.500','mesas ping-pong/fla-flu/tenis de mesa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('160.501','materiais esportivos em geral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('160.779','tatame');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('185.045','barbantes/cordas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('185.046','bombonas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('185.089','caixas/cestos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('185.090','chapas/bolas poliestireno');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('185.177','embalagens p/ substancias infecciosas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('185.178','embalagens fepps padrao lafergs');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('185.221','fitas/fitilhos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('185.265','garrafa/ garrafao para envase');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('185.267','tampa/ rolha - para garrafa/ garrafao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('185.632','papel e papelao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('185.691','filmes plasticos/polibolhas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('185.735','sacos/sacolas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('205.045','bandeiras brasileiras uso externo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('205.046','bandeiras estados/municipios uso externo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('205.047','bandeiras paises uso externo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('205.048','bandeiras especiais uso externo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('205.049','bandeiras - acessorios uso interno/externo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('205.050','bandeiras em geral uso interno');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('205.180','estandartes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.045','bottons');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.046','bolsas p/ eventos/cursos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.047','marca paginas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.089','carteiras funcionais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.090','crachas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.091','cartoes magneticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.092','canetas personalizadas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.177','escudos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.225','fitas personalizadas p/ crachas/cartoes ponto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.230','faca p/ churrasco prateada/gravada');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.231','conjunto bomba e cuia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.300','guarda sol/guarda chuva personalizados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.400','bones e camisetas personalizadas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.500','medalha/ trofeu');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.505','replica em metal');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.632','placas p/ identificacao de patrimonio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.635','placa de identificacao de veiculo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.779','trenas personalizadas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.800','artigos para copa cozinha personalizados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('215.805','balao personalizado');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.001','abrigos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.002','aventais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.003','aventais cirurgicos descartaveis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.045','bones/ toucas/ chapeus/ luvas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.089','calcas/calcoes/bermudas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.090','camisas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.091','camisetas/blusas/blusoes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.092','casacos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.093','cintos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.094','coletes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.095','campos/coberturas cirurgicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.103','confeccao de fantasia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.105','conjuntos diversos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.265','gravatas/lencos/lencos femininos/_mantas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.397','japonas/jaquetas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.398','jalecos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.500','macacoes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.501','meias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.502','mascaras/manguitos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.632','pro-pes/perneiras/perineais/triangulos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.691','roupas/acessorios p/camaras frigorificas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.692','roupas intimas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.693','roupas de banho');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.823','uniformes profissionais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.824','uniformes esportivos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('245.867','vestidos e saias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('250.045','botas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('250.046','bolsas/malas/mochilas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('250.089','calcados tipo tenis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('250.090','chinelos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('250.265','guarda-chuvas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('250.735','sapatos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('250.736','sandalias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('250.779','tamancos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('255.001','alfinetes/agulhas/botoes/porta-alfinetes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('255.221','fitas/fechos/elasticos/giz costura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('255.456','linhas/las/fios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('255.779','tecidos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('260.089','cobertores/colchas/acolchoados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('260.456','lencois/fronhas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('260.457','luvas/babeiros atoalhados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('260.779','toalhas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('270.001','acessorios/materiais p/microfilmagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('270.089','caixas p/microfilmes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('270.177','equipamentos p/microfilmagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('270.456','lampadas p/microfilmadoras/leitoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('270.500','microfilmes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('270.632','papel p/leitoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('270.779','toner');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('285.001','aparelhos p/ limpeza e higiene');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('285.002','aparelhos p/preparo de alimentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('285.003','aparelhos p/aquecimento e purificacao de agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('285.045','bebedouros e purificadores de agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('285.089','conjuntos compactos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('285.090','cafeteiras eletricas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('285.177','depuradores/exaustores domesticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('285.221','ferros eletricos/tabuas passar roupa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('285.222','fogoes e fornos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('285.500','maquinas de lavar roupa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('285.501','maquinas de secar roupa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('285.691','refrigeradores e congeladores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('290.001','aquecedores de ambiente');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('290.089','condicionadores de ar e sistemas de climatizacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('290.090','conjunto manifolds');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('290.177','equipamentos p/climatizacao/condicionadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('290.500','material p/aquecedor/condicionador/ventilador/desumificador');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('290.867','ventiladores/circuladores e desumidificadores de ar');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.002','aparelhos/equipamentos de som');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.089','cameras de video/equipamentos projecao/ binoculo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.095','cameras fotograficas/equipamentos fotograficos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.105','equipamento para edicao de audio/video');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.500','materiais p/projecao/video/som');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.510','materiais fotograficos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.589','materiais/ tecidos/ para palco e teatro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.595','equipamentos/mat/acessorios para projecao/video/foto/som');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.632','plataformas pantograficas/telescop./guarda corpo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.633','paineis eletronicos para atendimento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.735','sistema monitoramento eletronico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.779','televisores/acessorios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.867','videocassetes/acessorios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('295.868','video dvd/ home theater/ acessorios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.001','armarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.002','arquivos/ficharios/mapotecas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.045','beliches/camas/bercos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.047','balcoes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.089','cofres');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.090','cadeiras/bancos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.095','carrinhos bebe/cercados/andadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.177','estantes/suportes/racks/fruteiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.178','estacoes de trabalho');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.500','mesas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.508','mesa/cadeira (conjunto)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.510','moveis hospitalares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.511','moveis informatica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.512','moveis decoracao/jardim');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.513','moveis panificacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.515','moveis sob medida/ armarios, balcoes, mesas,...');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.632','poltronas/sofas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.635','pecas de reposicao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.676','quadros/murais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('320.999','moveis/estofados/componentes em geral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('345.089','colchoes/colchonetes/travesseiros/almofadas c/forro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('345.095','colchoes/colchonetes/travesseiros/espumas s/forro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('345.691','revestimentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.001','aparelhos para preparo de alimentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.002','armarios/estantes/mesas/estruturas metalic');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.045','bebedouros/purificadores de agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.089','cafeteiras eletricas/fogareiros eletricos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.090','caldeiroes p/ cozimento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.091','camaras frias / maquina de fabricar gelo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.092','caldeiras a vapor');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.133','digitos de borracha');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.171','pecas/materiais/acessorios uso comercial/industrial');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.177','eq. p/ exaustao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.178','eq. p/ padaria e confeitaria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.179','equipamentos p/cozinha industrial/comercial');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.180','equipamentos lavanderia industrial/limpeza');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.181','equipamentos p/barbearia e salao de beleza');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.182','equipamentos p/ industria de laticinios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.187','equipamentos/pecas/acessorios p/ industria de reciclagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.221','fogoes e fornos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.300','equipamentos para transporte de agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.500','maquinas de lavar louca');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.501','maquina costura/empacotadora automatica/seladora');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.502','materiais/acessorios para caldeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.521','maquina para triturar vidro/ plastico/ aluminio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.585','equipamentos controle de maquinas e processos industriais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.632','pallets');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.702','pecas e acessorios para reparo de maquinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.735','serra de fita p/ acougues');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.825','utensilios p/ cozinha industrial/comercial');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.867','valvulas p/ vapor');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.888','pecas e acessorios em aco pressurizado');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('350.999','pecas/mat./acessorios uso comercial/industrial');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.001','abridores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.045','bacias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.046','bandejas/ forros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.047','batedores/amassadores/rolos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.089','canecas e copos (exceto de plastico)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.090','cremeiras, tijelas e conchas terrinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.178','escorredores de massa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.179','espremedores de frutas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.180','esterilizador de utensilios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.221','filtros dagua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.222','funil, coador, lava-arroz e peneiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.265','garrafas termicas/ jarras e copos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.350','marmita termica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.505','materiais descartaveis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.632','paliteiros, saleiros, acucareiros e mantegueiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.633','panelas e formas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.634','porta utensilios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.635','potes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.636','purificadores de agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.691','raladores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.701','relogio de parede');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.705','supla/ lugar americano/ jogo americano');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.779','tabuas e tabuleiros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.780','talheres/tesouras p/ cozinha/ acendedor');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.781','tarros de leite e tachos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.782','travessas/pratos/conjuntos(exceto plast./descart.)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('360.926','xicaras / conjuntos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.045','baldes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.046','bomba manual p/inseticida');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.133','desentupidores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.177','esfregoes/esponjas de aco');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.178','espanadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.179','esponjas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.180','estopas/toalhas mecanicas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.181','escadas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.190','equipamentos p/limpeza');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.221','flanelas/panos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.456','lixeira metalica/plastica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.632','pas plasticas/metalicas e prendedores de roupa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.633','papel higienico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.634','papel toalha');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.635','cabide');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('380.867','vassouras/escovas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('390.045','barracas/ tendas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('390.089','camisas p/ lampioes e lanternas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('390.090','cantil');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('390.221','fogareiro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('390.265','gelo reutilizavel');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('390.456','lampiao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('390.457','lanternas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('390.460','lonas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('390.500','materiais/acessorios p/acampamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('390.510','mesas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('390.691','redes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('390.692','refrigeradores portateis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('390.735','sacos de dormir/colchoes inflaveis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.001','acessorios para radios transceptores e estacoes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.002','antenas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.003','antenas p/ estacoes fixas e estacoes moveis veiculares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.004','modulos receptores/transmissores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.045','baterias e carregadores de baterias p/ transceptores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.090','cristal oscilador');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.133','duplexadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.221','fonte de alimentacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.265','gps');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.266','radares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.505','monitores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.691','radios transceptores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.692','radios transmissores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('395.779','mastros e torres para antenas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('397.089','grafismo de audio/video');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('397.103','iluminacao cenica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('397.145','sistema de armazenamento de audio/video');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('397.172','sistema de captacao/comunicacao/edicao/de audio e video');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('397.181','sistema de codificacao/multiplexacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('397.198','sistema de exibicao de audio/video');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('397.215','materiais, equipamentos e acessorios para radiodifusao.');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('397.235','sistema irradiante');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('397.269','sistema monitoramento/medidas de sinais de audio/video');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('397.291','sistema de transmissao/recepcao via satelite');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('397.298','sistema de transmissao/recepcao via terrestre');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('400.001','acessorios/componentes/suprimentos p/telefonia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('400.003','acessorios/componentes/suprimentos p/centrais telefonicas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('400.089','centrais telefonicas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('400.133','detectores/bloqueadores de chamadas telefonicas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('400.177','estabilizadores de tensao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('400.221','fac-similes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('400.735','secretarias eletronicas/binas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('400.779','telefones');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.045','balancas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.089','caladores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.133','densimetros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.134','detectores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.177','estacoes meteorologicas/eq. meteorologicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.179','equipamentos p/topografia e cartografia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.181','equipamentos p/laboratorio quimico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.221','frequencimetros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.309','hidrometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.380','indicador de pesagem / celula de conversao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.500','multimetros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.501','medidores/calibradores/aferidores/controladores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.502','mesas p/ medicao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.505','materiais p/medicao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.588','osciloscopios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.590','odometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.779','termoigrometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('405.911','wattimetros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('410.089','capacitores potencia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('410.177','estabilizadores de tensao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('410.221','filtros redes energia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('410.265','grupos geradores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('410.779','transformadores de tensao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('410.781','turbinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.001','acessorios p/ condicionadores de ar');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.089','capacitores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.090','circuito integrado');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.091','conectores e redutores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.092','cabos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.093','componentes eletronicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.133','diodos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.221','filtros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.501','mantas dissipativa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.632','ponteira e resistencia p/ ferro de soldar');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.633','paineis solares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.650','potenciometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.660','pilhas/baterias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.691','resistores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.693','recarregadores/carregadores/transformadores de bateria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.779','transistores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.780','termostatos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('420.867','valvulas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('428.001','acionador comutador');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('428.089','catracas biometricas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('428.500','modulos detectores de veiculo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('428.501','cartao proximidade regravavel controle de acesso');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('428.555','pedestal organizador de fila (divisor de fluxo)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('428.632','porta-cartao ponto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('428.634','cartoes para relogio ponto digital');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('428.645','sistema automarizacao de portas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('428.691','relogio-ponto e registradores de frequencia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('428.692','fechaduras eletronicas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('428.735','software para registrador de frequencia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('428.736','sorteadores eletronicos microprocessados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('435.001','acessorios p/ solda');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('435.177','eletrodos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('435.178','equipamentos p/ solda');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('435.500','materiais p/ solda');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.001','alicates e torquesas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.002','arcos de puas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.045','bigornas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.046','bombas p/ graxa manual');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.089','chaves');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.090','cortadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.091','conjunto de ferramentas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.092','cavaletes/suportes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.133','desempenadeiras/colheres de pedreiro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.177','espatulas/escovas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.221','facas, facoes e canivetes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.222','ferramentas p/ apicultura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.223','ferramentas p/ perfuratriz');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.224','formao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.225','foices');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.226','fitas p/ medicao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.227','ferramentas diversas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.265','grampeadores p/ madeira, papelao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.266','grampos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.456','limas, grosas e travadeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.500','machados e machadinhas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.501','martelos e marretas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.502','macaricos de corte');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.503','macacos hidraulico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.504','morsas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.632','pas/cavadeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.633','pe de cabra');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.634','picaretas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.635','puncoes e saca-pinos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.636','pincas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.637','pistolas de pintura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.691','rebitadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.735','serras e serrotes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.736','soquetes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.779','talhadeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.780','tesoura p/ chapa de aco');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.781','tornos bancada');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('440.782','tarraxas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.089','carregadores de bateria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.090','cortadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.092','coladeiras de bordo p/ marcenaria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.177','equipamentos p/oficinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.222','furadeiras/perfuratrizes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.223','facetadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.224','fornos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.225','fresadoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.456','lixadeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.500','moto-esmerilhadeira');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.501','moto-politrizes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.502','marteletes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.503','motores eletricos trifasicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.504','maquinas confeccao telas de arame');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.632','plainas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.640','politrizes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.656','redutor de velocidade para motor');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.691','retificadeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.735','serras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.779','tornos/placas autocentrantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.780','tupias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('445.867','vibradores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.001','abracadeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.002','aneis retencao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.047','brocas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.048','buchas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.049','bancadas marcineiros / bancadas profissionais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.050','bicos encher pneu');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.051','bits');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.055','caixa para ferramentas/ maleta/ bolsa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.090','colas/adesivos/vedantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.133','discos de corte/debaste/serra/lamina');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.221','fechaduras/trincos/macanetas/dobradicas/molas/chaves');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.222','fitas gomadas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.266','gancho de inspecao p/ frigorifico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.458','lixas/fitas antiderrapantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.501','massas de vedacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.502','microesfera de vidro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.503','materiais/acessorios pintura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.504','materiais/acessorios p/marcenaria/ carpintaria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.505','massas e texturas em geral (exceto de vedacao)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.632','pedras de esmeril/afiar');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.633','pregos/parafusos/rebites/porcas/arruelas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.634','porta-cadeado/cadeados/correntes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.635','produtos p/ polimento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.691','rolos/trinchas/broxas/pinceis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.735','solventes/diluentes/removedores/retardadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('450.779','tintas/vernizes/seladores/primers');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('452.133','distanciadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('452.221','arame farpado/grampos/ concertina');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('452.222','arame galvanizado');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('452.223','arame p/ emplacamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('452.224','arame recozido');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('452.235','arame aco/ aco inox');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('452.779','tela');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.001','assoalhos/ lambri');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.089','caibro e caibrinho');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.090','compensado e aglomerado');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.091','caixao funebre');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.177','escoras e mouroes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.221','forrinho');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.265','guia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.456','laminas de madeira/formica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.457','lenha');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.500','mata junta');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.505','madeiras macicas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.632','pranchas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.691','ripa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.692','roda-pe');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.735','sarrafo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('460.779','tabuas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('461.001','acessorios/pecas de borracha/silicone');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('461.002','acessorios/pecas de plastico/teflon/tecnil/espuma');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('461.089','chapas de acrilico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('461.090','camara de butil p/ bolas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('461.501','materia-prima p/ confeccao de bolas esportiva');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('463.005','cobre redondo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('463.009','colarinho para cabo de aco');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.001','areia/argamassa/cimento/brita/rejunte/cordao solda');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.002','azulejos/ladrilhos/pisos/revestimentos/forros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.003','algeroz/calha');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.004','andaimes/arquibancadas moduladas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.005','acessorios p/ gesso acartonado');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.006','modulos construtivos moveis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.045','baldes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.046','betoneiras e misturadores mecanico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.089','carros de mao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.090','conjunto vibratorio completos e acessorios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.092','cal');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.093','concreto/mouroes/marcos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.177','esquadros/regua pedreiros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.178','equipamentos de teste');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.179','equipamentos p/ construcao civil');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.221','ferro/aco/aluminio/bronze/latao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.270','guaritas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.353','impermeabilizante e aditivo p/alvenaria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.354','materiais p/isolacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.500','metros e trenas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.501','mesas vibradoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.502','massas/fitas p/ gesso acartonado');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.544','niveis e prumos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.633','pedras/gessos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.634','perfis metalicos p/ fixacao de gesso acartonado');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.635','portas/marcos/guarnicoes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.779','tanques/pias/cubas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.780','telhas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.781','tijolos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('465.785','toldos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.001','armacoes secundarias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.089','chaves eletricas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.090','componentes p/ instalacoes eletricas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.091','cabo coaxial');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.133','disjuntores/reles');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.177','eletrodutos/conexoes/caixas de derivacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.178','eletrificadores p/ cerca eletrica rural');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.221','fios/cabos eletricos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.222','fitas isolantes/ emenda termocontratil/ contratil');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.223','fusiveis/bases');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.353','interruptores/tomadas/celulas fotoeletricas/acessorios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.354','isoladores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.456','lampadas/farois/refletores/sinaleiro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.457','luminarias/postes/calhas/suportes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.691','reatores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('475.705','vara de manobra telescopica para eletricista');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.001','aquecedores de agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.002','acessorios p/banheiros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.045','braco de chuveiro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.089','caixas de descarga e pecas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.090','caixas/ralos/grelhas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.091','chuveiros/duchas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.092','cola tubos de pvc');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.093','conexoes de pvc hidraulicas e sanitarias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.094','conexoes de ferro hidraulicas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.095','calhas e acessorios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.096','contentores flexiveis p/ agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.097','caixas de protecao para hidrometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.098','mangueira/ mangote');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.221','fitas/vedantes/aneis de borracha');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.222','fossa septica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.456','ligacoes flexiveis e bolsas p/ sanitarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.632','pastas lubrificantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.691','registros / plug para lavatorios e bide');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.692','reservatorios p/ agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.695','torre metalica p/ reservatorio de agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.700','redutores e moduladoers de agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.779','torneiras e reparos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.780','tubos pvc agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.781','tubos pvc esgoto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.782','tubos ferro/galvanizado agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.783','tubos de concreto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.784','tanques/pias/cubas/banheiras/vasos/tampos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('480.867','valvulas e reparos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('495.177','espelhos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('495.867','vidro padrao caff');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('495.868','vidro plano');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('495.900','molduras para quadros/diplomas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('505.089','cortinas/persianas/acessorios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('505.133','divisorias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('505.265','gabideiros/porta guarda-chuvas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('505.510','manequins expositor');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('505.570','materiais/ produtos para decoracao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('505.779','tapetes/capachos/forracoes/ isolamento acustico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('505.781','tela/fita antiderrapante p/tapete/capacho/forracao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('505.785','buques/ arranjos/ coroa de flores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('510.001','obras de arte');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('510.002','objetos decorativos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.001','algemas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.045','bastoes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.089','coletes salva-vida');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.090','coletes a prova de bala');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.091','cilindros p/ ar respiravel');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.092','cortinas de protecao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.094','cancelas eletronicas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.095','calcados de seguranca');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.133','detectores de metais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.177','eq. p/ prevencao de incendio/seguranca');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.178','equipamentos p/ investigacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.179','equipamentos anti-bombas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.180','equipamentos p/ identificacao e sinalizacao transito');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.181','equipamentos de raio-x');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.182','equipamentos de contra-espion./interc./audio/video');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.183','envelopes de seguranca');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.221','filmes de seguranca e controle solar');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.285','lanterna de servico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.456','lacres/selos seguranca');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.500','materiais de seguranca/protecao individual');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.501','materiais p/ prevencao de incendio/seguranca');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.505','materiais p/identificacao de veiculos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.507','materiais p/ papiloscopia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.510','materiais de seguranca/protecao coletiva');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.511','materia prima p/ confeccao de coletes balisticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.515','materiais p/ identificacao e sinalizacao transito');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.691','resgate e salvamento - equipamentos/manequim trein');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.750','sistemas de protecao ambientais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('515.868','vestuario de seguranca');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('535.045','bombas/motobombas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('535.089','compressores de ar');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('535.500','motores p/ compressores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('535.550','nebulizadores veicular p/ controle de mosquitos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('535.632','pecas/acessorios p/compressores de ar');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('535.633','pecas/acessorios p/bombas e motobombas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('535.645','pecas/acessorios p/ bomba de racalque');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('540.001','aspersores p/irrigacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('540.005','equipamentos/ pecas/ materiais para irrigacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('540.735','sistemas de irrigacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('548.177','equipamentos p/ tratamento de agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('548.178','equipamentos p/ tratamento de esgoto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('548.345','condensador de umidade atmosferica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('548.500','materiais/suprimentos p/ tratamento de agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('550.630','pecas e acessorios p/ balanca rodoviaria');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('550.632','pecas e acessorios p/dragas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('550.634','pecas e acessorios p/guindastes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('555.089','cabos de aco/correntes de aco/sapatilhas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('555.177','escavadeiras/motoniveladoras/pa carregadeiras/compactadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('555.221','ferramentas/materiais p/equipamentos de mineracao/escavacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('555.500','martelos rotopercussores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('555.501','materiais p/ perfuracao de pocos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('555.601','pecas/materiais e acessorios p manutencao de maquinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('555.632','perfuratrizes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('555.635','pecas p/perfuratrizes e martelos rotopercussores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('555.691','retroescavadeiras/carregadoras compactas/acessorios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('555.735','sondas p/pocos tubulares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('555.779','tubos/filtros/revestimentos geomecanicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('565.001','acessorios p/ carrinhos transporte');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('565.089','carrinhos p/ transporte');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('565.090','carregadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('565.177','empilhadeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('580.001','aspirador/soprador/residuos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('580.002','acessorios p/ rocadeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('580.005','placa identificacao de planta');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('580.065','banco de concreto/ madeira parajardim');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('580.221','ferramentas manuais p/ jardim');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('580.225','cabo para feramentas de ajardinamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('580.500','maquinas p/grama / podadores p cercas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('580.691','regadores/mangueiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('580.705','vaso para plantas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('580.779','telas plasticas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('580.999','equipamentos/pecas/acessorios p/ajardinamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('593.177','elevadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('593.250','esteira de movimentacao de bagagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('593.265','guindastes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('593.300','escada rolante');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('593.550','niveladoras de doca');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('593.605','pecas e acessorios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('593.779','talhas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('595.001','veiculos aeronaves');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('595.002','veiculos automoveis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('595.003','veiculos navais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('595.089','veiculos tipo pick-up');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('595.177','veiculos especiais (ambulancia/detentos/carro forte,etc)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('595.500','veiculos motocicletas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('595.691','veiculos reboques e semi-reboques');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('595.779','veiculos transporte coletivo/carga (onibus/caminhoes, etc.)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('595.823','veiculos utilitarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('595.824','veiculos utilitarios tipo jipe');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.037','alternadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.045','baterias/acessorios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.046','baus');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.089','cambios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.095','carrocerias/tanques');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.133','diferencias e semi-arvores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.140','direcoes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.177','embreagens');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.183','equipamentos acessorios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.221','ferramentas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.227','freios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.500','motores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.505','motores completos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.632','produtos e materiais p/limpeza e manutencao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.634','pecas/materiais/acessorios p/ aeronaves');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.645','kit gnv para conversao / acessorios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.691','radios/alto-falantes/tweeteres/amplificadores/modulos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.701','simulador veicular/ avaliador dirigibilidade');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.735','sistemas eletricos / fusiveis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.740','suspensao e rodas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.779','tapetes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('600.999','pecas/mat./acessorios de conserv.e manutencao veic');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.089','carretas agricola/elevador agricola');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.090','colhedeira/colhedora/colheitadeira');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.091','cultivadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.092','classificador cereais/descascador arroz');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.133','debulhador/secador de sementes/moinhos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.173','equipamentos para cunicultura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.174','equipamentos para suinocultura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.175','equipamentos para piscicultura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.176','equipamentos para avicultura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.177','enxadas rotativas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.178','estufas agricolas/viveiros aclimatador mudas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.179','equipamentos para apicultura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.180','equipamentos p/ vinificacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.181','esteiras transportadoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.183','equipamentos para bovinocultura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.265','guincho hidraulico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.500','motosserras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.501','maquinas p/ tratamento de sementes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.502','microssilos/silos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.632','pecas/acessorios equipamentos agricolas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.633','pulverizador');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.692','rocadeiras/raspadeiras/arados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.693','ordenhadeiras/resfriadores de leite');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.701','materiais/ pecas/ acessorios para implementos agricolas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.735','semeadeira/sulcadeira/adubadeira/arrancadeira/plantadeira');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.738','equipamento para producao racao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.779','tratores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.780','tanques coleta/transporte');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('685.795','tronco de contencao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('736.460','laticinios e correlatos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('736.640','produtos de origem animal in natura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('736.641','produtos de origem vegetal in natura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('736.642','produtos nao pereciveis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('736.643','produtos de panificacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('745.089','camara p/ pneus');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('745.090','calibrador pneus');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('745.500','materiais p/ conserto de pneus');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('745.502','protetor camera de ar');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('745.632','pneus');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('748.046','baterias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('748.047','boias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('748.089','cabos de aco/polipropileno/nylon');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('748.090','chapas de aco navegacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('748.133','defensas p/ cais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('748.456','lanternas e materiais p/ sinalizacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('748.500','motores de popa / motores diesel /motores gasolina');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('748.632','pecas/materiais para barcos/botes/lanchas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('750.001','tampao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('754.001','acessorios/componentes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('754.049','birutas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('754.456','lampadas p/aeroportos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('754.500','materiais/acessorios p/sinalizacao de aeroportos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('754.735','sistema de navegacao por satelite');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('754.736','sistemas balizamento/sinalizacao de aeroportos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('754.779','transformadores/estabilizadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('757.001','asfalto/aditivos asfalticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('757.002','alcool combustivel');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('757.003','aditivos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('757.089','carvao vegetal');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('757.265','gas liquefeito de petroleo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('757.266','gasolina');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('757.588','oleos e graxas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('757.632','piche');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('757.676','querosene');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('758.045','botijoes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('758.089','centrais de gases');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('758.353','instalacoes de gas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('760.001','armas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('760.005','acessorios e pecas p/armas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('760.089','cartuchos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('760.177','espoletas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('760.178','equipamento p/ recarga de cartuchos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('760.179','equipamentos anti-motim');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('760.180','estojos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('760.265','granadas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('760.500','material p/ limpeza de armas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('760.632','polvoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('760.635','projeteis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('773.089','carne bovina/produtos derivados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('773.090','carne avicola/produtos derivados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('773.091','carne ovina/produtos derivados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('773.092','carne suina/produtos derivados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('773.456','linguica/ fiambres');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('773.632','peixe/frutos do mar/produtos derivados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('773.633','produtos organicos e agroecologicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('773.635','pates');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('773.735','salsichas/ salsichoes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('773.779','tripa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('775.095','carnes/peixes/frutos do mar/produtos derivados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('775.225','fiambres/pates/linguicas/salsichas/salsichoes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('775.226','frutigranjeiros/hortigranjeiros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('775.460','leites/manteigas/queijos/produtos derivados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('775.632','produtos organicos agroecologicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('775.640','produtos nao pereciveis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('775.641','produtos de panificacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('779.456','linguica/fiambres');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('779.632','pates');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('779.735','salsichas/salsichoes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('779.779','tripa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('784.221','frutigranjeiros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('784.309','hortigranjeiros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('784.632','produtos organicos agroecologicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('788.133','derivados do leite');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('788.456','leite');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('788.500','manteiga');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('788.676','queijo e similares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('788.700','cremes vegetais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.001','acucares/complementos energeticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.002','alimentos enlatados/conservas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.003','agua mineral/refrigerantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.004','adocantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.005','arroz/feijao/lentilha/ervilha/canjica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.006','aveia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.089','cafe');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.090','cevada/cereais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.091','chas/ervas/essencias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.092','complementos e compostos alimentares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.093','condimentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.094','cesta basica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.133','doces em pasta/em calda/geleias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.221','farinhas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.222','fermentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.223','frutas secas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.265','graos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.500','massas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.501','mel');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.588','oleos/margarinas/maioneses/molhos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.632','produtos diversos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.633','produtos organicos agroecologicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.735','sal');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.737','sucos/chas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.739','substitutivos do leite natural');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('792.867','vinhos/vinagres');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('796.045','bolachas/biscoitos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('796.089','cucas/bolos/panetones');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('796.133','doces/salgados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('796.632','paes/sanduiches/pizzas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('802.089','complementos e compostos nutricionais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('802.133','dietas completas e modulares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('803.045','bolachas/biscoitos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('803.089','cucas/bolos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('803.133','doces/salgados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('803.225','fiambre/linguicas/salsichoes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('803.460','manteiga/queijos/produtos derivados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('803.632','geleias/conservas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('805.177','equipamentos p/gases de uso hospitalar/laboratorial/indust.');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('805.265','gases de uso hospitalar/ laboratorial/ industrial');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('805.353','instalacoes/centrais de gas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('820.177','equipamentos p/ industria farmaceutica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('820.502','materias-prima para fabricacao de medicamentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('820.503','materiais p/ acondicionamento e embalagem de medicamentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('820.633','pecas/acessorios para equipamentos da industria farmaceutica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('820.635','padroes primarios / substancias quimicas de referencia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.007','aparelhos e equipamentos para analise de solos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.008','aparelhos/equipamentos p/analise eletro-eletronica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.010','aparelhos/equipamentos p/lab. metal mecanico/metalurgia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.011','aparelhos/equipamentos p/lab. construcao civil');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.102','cromatografos/espectrofotometros/fotometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.103','congeladores/freezers/containers/maquinas gelo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.180','estufas/fornos/chapas eletricas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.183','equipamentos para laboratorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.441','kits/utensilios didaticos p/laboratorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.507','materiais p/laboratorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.508','microscopios/estereoscopios/lupas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.555','pecas/acessorios para equipamentos de laboratorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.636','materiais p/tratamento de agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.637','produtos quimicos p/laboratorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.638','produtos quimicos p/tratamento de agua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.750','softwares p/ equipamentos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('830.868','vidraria p/laboratorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('855.001','aditivos e acessorios para microbiologia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('855.133','diagnostica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('855.500','meios de cultura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.001','abaixador de lingua');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.002','afastadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.003','antropometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.004','anuscopio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.005','aparelhos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.006','aspiradores/compressores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.007','audiometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.008','autoclaves');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.009','ambus');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.010','aparelhos p/ medicina nuclear');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.045','balancas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.046','baldes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.047','banho-maria p/ mamadeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.048','bercos aquecidos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.049','biombos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.050','bisturis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.051','bracadeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.052','baracas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.053','bandejas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.054','bombas infusao/ bomba vacuo aspiradora');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.055','broncoscopios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.060','baterias');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.065','cadeira de rodas/ triciclo eletrico para deficientes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.089','cabines');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.090','cabos de instrumentais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.091','cadeiras oftalmologicas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.093','canetas p/ espirografos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.094','cardioversores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.095','ceratometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.096','colonoscopios (fribroscopio)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.097','colposcopios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.098','coluna oftalmologica');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.099','comadres');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.100','compasso dobra cutanea');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.101','conjunto baliu');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.102','conjunto instrumental p/ cesariana');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.103','cubas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.104','canulas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.105','curetas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.106','compressores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.133','desfribriladores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.134','detectores de batimentos cardiofetais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.135','diapasao de gaudencio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.136','descoladores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.177','eletrocardiografos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.178','eletrocauterios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.179','eletroencefalografos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.180','escalas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.181','escarradeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.182','esfingnomanometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.183','especulos/alcas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.184','espelhos p/ instrumentais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.185','espirometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.186','estetoscopios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.187','estiletes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.188','estribo de kirschner');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.189','estojos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.190','equipamentos medico-hospitalares e de enfermagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.191','equipamentos radiologicos (exceto odontologicos)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.192','equipamentos fisioterapico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.221','facas p/ necropsia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.222','focos/refletores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.223','forceps');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.224','fotoforos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.265','goteira de brown');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.266','guia ayoa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.267','ganchos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.268','goniometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.269','glicosimetros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.309','hamper');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.310','histerometro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.353','incubadoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.354','instrumentais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.355','incineradores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.441','kits parto/queimados/primeiro socorros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.456','laminas p/ bisturi');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.457','laminas p/ laringoscopio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.458','lampadas cirurgica de teto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.459','lamparinas p/desembacamento especular');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.460','lanternas p/ exame orofaringe');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.461','laringoscopios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.500','maletas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.501','mandris');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.503','manometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.504','martelos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.505','monitores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.506','microscopios cirurgicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.507','materiais medico-hospitalares e de enfermagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.508','materiais radiologicos (exceto odontologicos)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.509','materiais medico-hospitalares e de enfermagem (contin. 507)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.510','materiais fisioterapicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.544','nebulizadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.588','oftalmoscopios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.589','ordenha de leite materno');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.590','osteotomos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.591','otoscopios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.592','oxitenda');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.593','oximetros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.632','papagaios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.633','pincas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.634','porta-agulhas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.635','punchs');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.636','produtos oficinais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.637','pecas p/equipamentos medico-hospitalares e de enfermagem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.638','pneus/camaras/rodas p/equip medico-hospitalares e de enferma');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.691','raquimanometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.692','refratores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.693','regletes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.694','respiradores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.695','retinoscopios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.696','ruginas de farabeuf');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.700','seladoras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.702','dispensador eletronico hospitalar');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.735','seringas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.736','serras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.737','sindesmotomos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.738','sistemas de video');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.739','sonares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.740','suportes p/ soro/valvulas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.779','tambores de aco inoxidavel');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.781','tesouras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.782','treinadores de fala');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.783','tentacanulas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.784','tubos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.825','ultra-sonografos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.867','valvulas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.868','videolaparoscopios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('870.869','ventilometros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.001','anti-hemofilicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.002','anti-hipertensivos/diureticos/vasodilatadores coronarianos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.003','antipsoriase');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.004','anestesicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.005','ansioliticos/tranquilizantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.006','antagonistas da heparina');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.007','antagonistas dos narcoticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.008','antagonistas dos inseticidas organofosforados');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.009','anti-helminticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.010','anti-hemorragicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.011','anti-hemorroidarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.012','anti-septicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.013','antiacidos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.014','antiagregantes plaquetarios/antitrombocitos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.015','antialcoolicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.016','antialergicos/anti-histaminicos/antipruriginosos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.017','antianemicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.018','antiarritmicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.019','antiasmaticos/broncodilatadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.020','antibacterianos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.021','anticoagulantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.022','anticonvulsivantes/antiepilepticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.023','antidepressivos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.024','antidiarreicos/antidisentericos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.025','antiemeticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.026','antifiseticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.027','antigotosos/uricosuricos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.028','analgesicos/antitermicos/antiinflamatorios/antireumaticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.029','antagonistas dos receptores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.030','antimicoticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.031','antineuriticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.032','antiparkinsonianos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.033','antipsicoticos/neurolepticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.034','antitussigenos/expectorantes/mucoliticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.035','antiviroticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.036','antidiabeticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.037','antimalaricos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.038','anticoncepcionais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.039','antilipemicos/hipocolesterinemicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.040','antitireoideanos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.041','antiescleroticos/imunomodulador');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.042','antiprotozoarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.045','bloqueadores dos receptores h2 de histamina');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.046','bloqueadores neuromusculares (curarizantes)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.049','anticalculos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.089','cardiotonicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.090','cicatrizantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.091','complementos dieteticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.092','contrastes radiologicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.093','ceratoliticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.133','descongestionantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.134','diluentes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.176','dietas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.177','ectoparasiticidas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.178','edulcorantes (adocantes)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.179','expansores do plasma');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.180','estimuladores da hematopoese');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.181','eupepticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.182','estimulantes do sistema nervoso central');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.309','hipertensores arteriais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.310','hipnoticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.311','hidratantes pele');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.313','homeopaticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.315','hormonios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.353','imunoglobulinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.354','imunossupressores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.355','inibidores da lactacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.356','inibidores da secrecao de prolactina');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.357','inibidores da sintese de gonadotrofina');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.358','inibidores dos disturbios do metabolismo osseo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.359','inibidores da lipase gastrintestinal');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.456','laxativos/purgativos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.500','miorrelaxantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.544','narcoanalgesicos (opioides)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.588','ocitocicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.589','oficinais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.590','oncologicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.632','parassimpaticolicos/antiespasmodicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.633','parassimpaticomimeticos/anticolinesterasicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.634','produtos oftalmicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.635','produtos otologicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.676','quelantes/permutadores de ions');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.691','recalcificantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.692','reguladores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.693','reidratantes/repositores eletroliticos/solucoes/soros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.720','substituto');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.734','suplementos de magnesio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.735','simpaticomimeticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.736','surfactantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.760','tonicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.779','tricomonicidas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.867','vacinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.868','vasodilatadores cerebrais e perifericos/antivertiginosos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.869','vasodilatadores coronarianos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.870','anti-histam?nicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.871','antineopl?sicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.872','fator proteã?o solar (fps)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.873','anest?sicos locais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.874','implante sint?tico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.875','leites especiais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.876','suplementos alimentares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.877','dietas pedißtricas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.878','formula infantil');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.879','alimentaã?o enteral e oral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.880','cereais_,mucilagem e farinha l?ctea');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.881','vasodilatadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.883','medicamento de aã?o no m?sculo esquel?tico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.884','polivitaminicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.885','vitaminas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.886','estimulantes de apetite');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.887','nutriþòo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.888','mëdulo para dieta enteral ou oral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.889','dieta enteral sistema fechado');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.890','medicamento antitabagismo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.891','suplementos diet?tico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.900','aã?o no trato urin?rio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('880.901','insufici?ncia renal');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.002','anti-hipertensivos/diureticos/vasodilatadores coronarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.027','antibioticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.028','analgesicos/antitermicos/antiinflamatorios/antireumaticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.032','antiparkinsonianos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.035','antiviroticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.091','complementos dieteticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.178','enzimas para reposicao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.181','eupepticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.353','imunoglobulinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.501','medicamentos importados especiais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.590','oncologicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.632','parassimpaticolicos/antiespasmodicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.633','parassimpaticomimeticos/anticolinesterasicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('882.676','quelantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.001','antipsoriase');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.003','anticonvulsivante/antiepilepticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.004','antialergicos/anti-histaminicos/antipruriginosos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.005','analgesicos/antitermicos/antiinflamatorios/anti-reumaticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.007','antipsicoticos/neurolepticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.009','antitussigenos/expectorantes/mucoliticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.011','antiviroticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.013','antidiabeticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.014','antilipemicos/hipocolesterinemicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.015','antibacterianos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.016','antimalaricos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.017','antiparkinsonianos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.018','antitireoideanos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.019','antiasmaticos/broncodilatadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.021','antiacidos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.041','antiescleroticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.045','bloqueadores neuromusculares (curarizantes)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.136','dietas completas e modulares');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.175','medicamento do aparelho digestivo e metab¾lico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.176','enzimas digestivas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.177','estimuladores da hematopoese');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.179','eupepticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.200','estimulantes do sistema nervoso central');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.309','hormonios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.353','imunoglobulinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.355','inibidores da sintese de gonadotrofina');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.357','inibidores dos disturbios do metabolismo osseo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.359','inibidores da secrecao de prolactina');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.361','imunossupressores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.501','medicamentos importados excepcionais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.544','narcoanalgesicos (opioides)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.588','oncologicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.675','quelantes/permutadores de ions');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.871','vasodilatadores cerebrais e perifericos/antivertiginosos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('884.872','antimiast?nicos_e descurarizantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.001','anti-hipertensivos/diureticos/vasodilatadores coronarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.002','antigotosos/uricosuricos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.003','antiagregantes plaquetarios/antitrombocitos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.004','antibacterianos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.005','antianemicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.006','anticonvulsionantes/antiepilepticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.007','antiasmaticos/broncodilatadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.008','anti-helminticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.009','antiemeticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.010','anti-coagulantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.011','antimicoticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.012','antidepressivos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.013','antiparkinsonianos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.014','antilipemicos/hipocolesterinemicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.015','antipsicoticos/neurolepticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.016','antitireoideanos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.017','antiviroticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.018','analgesicos/antitermicos/antiinflamatorios/antireumaticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.019','antiescleroticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.020','antidiabeticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.021','antidiarreicos/antidisentericos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.022','ansioliticos/tranquilizantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.024','anti-hemorroidarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.025','anti-isquemico metabolico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.026','antiartrosico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.027','antifungico de amplo espectro');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.028','antialergicos / anti-histaminico / antipruriginosos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.029','antiarritmicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.030','antineoplasico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.031','antiosteoporotico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.035','antimalarico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.045','bloqueadores dos receptores h2 de histamina');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.091','complementos dieteticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.095','cardiotonicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.135','descongestionantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.177','eupepticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.178','estimulantes do sistema nervoso central');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.250','hormonios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.309','hipnoticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.310','hipocolesterolemiante');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.353','imunoglobulinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.355','imunossupressores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.357','inibidores da sintese de gonadotrofina');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.359','inibidores dos disturbios do metabolismo osseo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.456','laxativos/purgativos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.501','medicamentos importados especiais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.588','oncologicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.589','outros agentes antineoplasicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.632','parassimpaticolicos/antiespasmodicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.634','parassimpaticomimeticos/anticolinesterasicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.636','produtos oftalmicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.691','recalcificantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.692','reidratantes/repositores eletrolitos/solucoes/soros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.735','substitutos do leite natural');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.814','fërmula composta');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.815','inibidor v?rus sincial respiratërio(vsr)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.867','vasodilatadores cerebrais e perifericos/antivertiginosos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.868','dietoter?picos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.869','complemento alimentar');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.870','dietas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.871','fërmula infantil');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.872','suplemento alimentar');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('886.873','complemento nutricional');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.020','aparelho digestivo - adsorventes/antifiseticos intestinais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.021','aparelho digestivo - antiacidos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.022','aparelho digestivo - antiemeticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.023','aparelho digestivo - antiulcerosos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.024','aparelho respiratorio - antiasmaticos/broncodilatadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.025','aparelho respiratorio - antialergicos/anti-histam.');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.026','aparelho respirat. - antitussigenos/expectorantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.027','aparelho urinario - hiperplasia prostatica benigna');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.100','cardiovascular - antiangionosos/vasodilatadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.101','cardiovascular - antiarritmicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.102','cardiovascular - anti-hipertensivos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.103','cardiovascular - agente inotropico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.104','cardiovascular - diureticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.105','cardiovascular - vasoconstritores/hipertensores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.150','dermatologicos - antiacne');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.151','dermatologicos - atialopecia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.152','dor/inflamacao/febre - analgesicos e antitermicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.153','dor/inflamacao/febre - antiespasmodicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.154','dor/inflamacao/febre - antiinflamatorios/antireumaticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.155','dor/inflamacao/febre - relaxante muscular');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.322','hematologia/repositor eletrolitos - repositor de eletrolitos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.330','hematologia/repositor eletrolitos - antiagregantes palquet.');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.331','hematologia/repositor eletrolitos - antianemicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.360','infeccao/infestacao - antibioticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.361','infeccao/infestacao - amebicidas/giardicidas/tricomonicidas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.362','infeccao/infestacao - antifugicos/antimicoticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.363','infeccao/infestacao - anti-helminticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.364','infeccao/infestacao - antiretroviral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.365','infeccao/infestacao - antiviral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.370','imunossupressor - agente imunossupressor');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.371','imunossupressor - antineoplasicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.520','metabolismo - antidiabeticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.521','metabolismo - antigotoso');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.522','metabolismo - antilipemicos/redutores colesterol');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.523','metabolismo - glicocorticoides');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.580','oftalmicos - solucoes oftalmicas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.755','sistema nervoso central - anestesicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.756','sistema nervoso central - ansioliticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.757','sistema nervoso central - anticonvulsivantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.758','sistema nervoso central - antidepressivos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.759','sistema nervoso central - antiparkinsonianos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.760','sistema nervoso central - neurolepticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.761','sistema nervoso central - ativador metabolismo cerebral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('888.762','sistema nervoso central - hipnoticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('890.500','materiais p/higiene pessoal');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('890.501','materiais p/profilaxia (prevencao)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('890.502','aspirador nasal');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('890.505','creme hidratante / locao cremosa/ protetor solar');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('905.001','armacao de zilio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('905.045','bengalas/muletas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('905.046','cadeira de rodas e acessorios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('905.089','calcados ortopedicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('905.353','implantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('905.456','lentes p/ armacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('905.588','orteses/materias para fixacao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('905.632','proteses');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('905.735','stents');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('905.739','orteses/proteses/armacoes/ lentes oculares e contato');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.001','alavancas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.002','alicates');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.003','alveolotomo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.004','amalgamadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.005','aparelhos de raio-x');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.006','aparelhos p/ remocao de placas bacterianas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.008','armarios clinicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.009','aventais para protecao de raio-x');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.010','articuladores (oclusores)');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.045','biombos de chumbo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.046','broqueiros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.047','brunidores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.048','bancadas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.089','cabos p/ espelhos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.090','cadeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.091','calcadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.092','camaras escura');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.093','canetas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.094','compasso de willis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.095','compressores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.096','condensadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.097','conjuntos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.098','contra-angulos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.099','curetas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.100','cuspideiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.101','centrifugas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.102','cinzeis');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.133','desgastadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.134','dosadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.135','desinfetantes/esterelizantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.136','dispensadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.177','equipos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.178','escavadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.179','esculpidores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.180','espatulas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.181','escovas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.182','extratores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.183','estojos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.184','escovarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.185','especimetros');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.186','espelhos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.221','facas p/ gesso');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.222','forceps');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.223','fotopolimerizadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.224','fresas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.225','fornos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.226','filmes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.227','fixadores/reveladores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.228','frascos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.229','fios ortodonticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.265','gengivotomos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.266','grampos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.267','graxas/oleos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.268','gral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.269','godiva');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.353','instrumentais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.397','jatos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.445','kits miniplacas/microplacas cirurgicas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.456','lamparinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.457','limas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.500','mandris');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.501','micro-motores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.502','mochos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.503','moldeiras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.504','muflos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.505','macaricos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.506','motores de suspensao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.507','materiais p/ procedimentos e uso geral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.508','matrizes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.509','modelos didaticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.632','pecas de mao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.633','polidora para proteses');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.634','porta algodao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.635','porta-amalgamas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.636','porta-mandris');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.637','porta-matrizes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.638','porta-residuos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.639','prensas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.640','polimerizadores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.641','pedras afiacao/desgaste');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.643','equipamentos/ aparelhos para uso em geral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.691','refletores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.692','rotores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.693','resinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.694','reveladores placas bacterianas/caries');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.735','seringa de carpule');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.736','seringas triplice');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.737','sondas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.738','vibradores');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.779','tesouras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.780','turbinas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.781','tornos de polimento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('910.823','unidade auxiliar movel');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('930.002','armadilhas p/captura de animais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('930.003','armadilhas p/ captura de insetos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('930.004','agulhas/ seringas uso veterinario');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('930.090','caixas termicas para transporte de peixes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('930.091','correaria e arreamentos p/montaria e tracao');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('930.177','equipamentos veterinarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('930.500','medicamentos veterinarios/diagnosticos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('930.501','materiais veterinarios');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('930.691','redes/materiais p/bioterio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('930.740','semen');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('950.001','aves');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('950.177','equinos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('950.180','ovinos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('950.200','apicultura/enxame de abelha');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('950.250','peixes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('950.300','camundongos p/laboratorio');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('960.001','aveia');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('960.002','alfafa');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('960.003','azevem');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('960.089','carnes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('960.177','ervilhaca');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('960.221','farinha de ostra');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('960.691','racoes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('960.735','sal mineral');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('960.736','suplementos minerais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('965.001','adubos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('965.089','corretivos do solo');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('970.133','defensivos p/ uso domestico');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('970.134','defensivos agricolas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('980.045','bandejas/tubetes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('980.500','mudas frutiferas');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('980.501','mudas reflorestamento');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('980.502','mudas ornamentais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('980.735','sementes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('980.736','substrato');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.001','alvejantes/desinfetantes/detergentes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.002','amaciantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.003','aromatizantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.004','acidulantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.005','anti-septicos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.006','alcool para limpeza');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.089','ceras');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.133','desodorantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.177','esterilizantes');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.456','lustra moveis/polidor de metais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.632','produtos alcalificantes para limpeza');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.633','produtos p/ conservacao de instrumentais');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.735','saboes/sabonetes liquidos');
INSERT INTO subfamilia(estrutural, descricao) VALUES ('990.736','saponaceos');


CREATE OR REPLACE FUNCTION insere_catalogo() RETURNS VOID AS $$
DECLARE
    inCodCatalogo       INTEGER;
    inCodClassificacao  INTEGER := 1;

    stSQL               VARCHAR;
    reRecord            RECORD;
BEGIN
    PERFORM 1
       FROM pg_class
          , pg_attribute
          , pg_type
      WHERE pg_class.relname      = 'catalogo_classificacao'
        AND pg_attribute.attname  = 'importado'
        AND pg_attribute.attnum   > 0
        AND pg_attribute.attrelid = pg_class.oid
        AND pg_attribute.atttypid = pg_type.oid
          ;
    IF NOT FOUND THEN
        ALTER TABLE almoxarifado.catalogo_classificacao ADD COLUMN importado BOOLEAN DEFAULT FALSE NOT NULL;
    END IF;


    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2015'
        AND cod_modulo = 2
        AND parametro  = 'cod_uf'
        AND valor      = '23'
          ;
    IF FOUND THEN
        SELECT MAX(cod_catalogo) + 1
          INTO inCodCatalogo
          FROM almoxarifado.catalogo
             ;
    
        INSERT
          INTO administracao.configuracao
             ( exercicio
             , cod_modulo
             , parametro
             , valor
             )
        VALUES
             ( '2015'
             , 29
             , 'catalogo_tce'
             , inCodCatalogo
             );
    
        INSERT
          INTO almoxarifado.catalogo
             ( cod_catalogo
             , descricao
             , permite_manutencao
             )
        VALUES
             ( inCodCatalogo
             , 'Catálogo TCE-RS'
             , FALSE
             );
    
        INSERT
          INTO almoxarifado.catalogo_niveis
             ( nivel
             , cod_catalogo
             , mascara
             , descricao
             )
        VALUES
             ( 1
             , inCodCatalogo
             , '999'
             , 'Família'
             );
             
        INSERT
          INTO almoxarifado.catalogo_niveis
             ( nivel
             , cod_catalogo
             , mascara
             , descricao
             )
        VALUES
             ( 2
             , inCodCatalogo
             , '999'
             , 'Subfamília'
             );
             
        stSQL := '
                   SELECT * FROM familia ORDER BY estrutural;
                 ';
        FOR reRecord IN EXECUTE stSQL LOOP
            INSERT
              INTO almoxarifado.catalogo_classificacao
                 ( cod_classificacao   -- integer                | not null
                 , cod_catalogo        -- integer                | not null
                 , cod_estrutural      -- character varying(160) | not null
                 , descricao           -- character varying(400) | not null
                 , importado           -- boolean                | not null default false
                 )
            VALUES
                 ( inCodClassificacao
                 , inCodCatalogo
                 , reRecord.estrutural
                 , reRecord.descricao
                 , TRUE
                 );
            INSERT
              INTO almoxarifado.classificacao_nivel
                 ( cod_catalogo      -- integer | not null
                 , nivel             -- integer | not null
                 , cod_classificacao -- integer | not null
                 , cod_nivel         -- integer | not null
                 )
            VALUES
                 ( inCodCatalogo
                 , 1
                 , inCodClassificacao
                 , split_part(reRecord.estrutural, '.', 1)::INTEGER
                 );
            INSERT
              INTO almoxarifado.classificacao_nivel
                 ( cod_catalogo      -- integer | not null
                 , nivel             -- integer | not null
                 , cod_classificacao -- integer | not null
                 , cod_nivel         -- integer | not null
                 )
            VALUES
                 ( inCodCatalogo
                 , 2
                 , inCodClassificacao
                 , split_part(reRecord.estrutural, '.', 2)::INTEGER
                 );
            inCodClassificacao := inCodClassificacao + 1;
        END LOOP;
    
        stSQL := '
                   SELECT * FROM subfamilia ORDER BY estrutural;
                 ';
        FOR reRecord IN EXECUTE stSQL LOOP
            INSERT
              INTO almoxarifado.catalogo_classificacao
                 ( cod_classificacao   -- integer                | not null
                 , cod_catalogo        -- integer                | not null
                 , cod_estrutural      -- character varying(160) | not null
                 , descricao           -- character varying(400) | not null
                 , importado           -- boolean                | not null default false
                 )
            VALUES
                 ( inCodClassificacao
                 , inCodCatalogo
                 , reRecord.estrutural
                 , reRecord.descricao
                 , TRUE
                 );
            INSERT
              INTO almoxarifado.classificacao_nivel
                 ( cod_catalogo      -- integer | not null
                 , nivel             -- integer | not null
                 , cod_classificacao -- integer | not null
                 , cod_nivel         -- integer | not null
                 )
            VALUES
                 ( inCodCatalogo
                 , 1
                 , inCodClassificacao
                 , split_part(reRecord.estrutural, '.', 1)::INTEGER
                 );
            INSERT
              INTO almoxarifado.classificacao_nivel
                 ( cod_catalogo      -- integer | not null
                 , nivel             -- integer | not null
                 , cod_classificacao -- integer | not null
                 , cod_nivel         -- integer | not null
                 )
            VALUES
                 ( inCodCatalogo
                 , 2
                 , inCodClassificacao
                 , split_part(reRecord.estrutural, '.', 2)::INTEGER
                 );
            inCodClassificacao := inCodClassificacao + 1;
        END LOOP;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        insere_catalogo();
DROP FUNCTION insere_catalogo();

DROP TABLE familia;
DROP TABLE subfamilia;


----------------
-- Ticket #23670
----------------

UPDATE administracao.acao SET ordem =  7 WHERE cod_acao = 1640;
UPDATE administracao.acao SET ordem =  8 WHERE cod_acao = 1641;
UPDATE administracao.acao SET ordem =  9 WHERE cod_acao = 1642;
UPDATE administracao.acao SET ordem = 10 WHERE cod_acao = 1980;


----------------
-- Ticket #23723
----------------

ALTER TABLE licitacao.tipo_instrumento ALTER COLUMN codigo_tc DROP NOT NULL;

