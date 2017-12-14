<?php
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
?>
<?php
/**
*
* Data de Cria��o: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-04.00.00
*/

//DEFINICAO DE VALIDADE E VERSAO DA GESTAO
define( "VALIDADE_GRH",               "31/12/2017" );
define( "VERSAO_GRH",                 "2.05.4" );

define( "CAM_GRH",  "../../../../../../gestaoRH/fontes/" );

//DEFINICAO DOS CAMINHOS
define( "CAM_GRH_PESSOAL",                  CAM_GRH."PHP/pessoal/"              );
define( "CAM_GRH_CALENDARIO",               CAM_GRH."PHP/calendario/"             );
define( "CAM_GRH_CONCURSO",                 CAM_GRH."PHP/concurso/"             );
define( "CAM_GRH_BENEFICIOS",               CAM_GRH."PHP/beneficios/"           );
define( "CAM_GRH_FOLHA_PAGAMENTO",          CAM_GRH."PHP/folhaPagamento/"       );
define( "CAM_GRH_ESTAGIO",                  CAM_GRH."PHP/estagio/"       );
define( "CAM_GRH_IMA",                      CAM_GRH."PHP/IMA/"       );
define( "CAM_GRH_ENTIDADE",                 CAM_GRH."PHP/entidade/"       );
define( "CAM_GRH_DIARIAS",                  CAM_GRH."PHP/diarias/"       );
define( "CAM_GRH_PONTO",                    CAM_GRH."PHP/ponto/"         );

//PESSOAL
define( "CAM_GRH_PES_CLASSES",              CAM_GRH_PESSOAL."classes/"          );
define( "CAM_GRH_PES_MAPEAMENTO",           CAM_GRH_PES_CLASSES."mapeamento/"   );
define( "CAM_GRH_PES_NEGOCIO",              CAM_GRH_PES_CLASSES."negocio/"      );
define( "CAM_GRH_PES_COMPONENTES",          CAM_GRH_PES_CLASSES."componentes/"  );
define( "CAM_GRH_PES_INSTANCIAS",           CAM_GRH_PESSOAL."instancias/"       );
define( "CAM_GRH_PES_PROCESSAMENTO",        CAM_GRH_PES_INSTANCIAS."processamento/" );
define( "CAM_GRH_PES_POPUPS",               CAM_GRH_PESSOAL."popups/"           );
define( "CAM_GRH_PES_ANEXOS",               CAM_GRH_PESSOAL."anexos/"           );
define( "TPES",                             CAM_GRH_PES_MAPEAMENTO              );

//CALENDARIO
define( "CAM_GRH_CAL_CLASSES",              CAM_GRH_CALENDARIO."classes/"       );
define( "CAM_GRH_CAL_MAPEAMENTO",           CAM_GRH_CAL_CLASSES."mapeamento/"   );
define( "CAM_GRH_CAL_NEGOCIO",              CAM_GRH_CAL_CLASSES."negocio/"      );
define( "CAM_GRH_CAL_INSTANCIAS",           CAM_GRH_CALENDARIO."instancias/"    );
define( "CAM_GRH_CAL_POPUPS",               CAM_GRH_CALENDARIO."popups/"        );
define( "CAM_GRH_CAL_COMPONENTES",          CAM_GRH_CAL_CLASSES."componentes/"  );
define( "CAM_GRH_CAL_PROCESSAMENTO",        CAM_GRH_CAL_INSTANCIAS."processamento/" );
define( "TCAL",                             CAM_GRH_CAL_MAPEAMENTO              );

//CONCURSO
define( "CAM_GRH_CON_CLASSES",              CAM_GRH_CONCURSO."classes/"         );
define( "CAM_GRH_CON_MAPEAMENTO",           CAM_GRH_CON_CLASSES."mapeamento/"   );
define( "CAM_GRH_CON_NEGOCIO",              CAM_GRH_CON_CLASSES."negocio/"      );
define( "CAM_GRH_CON_INSTANCIAS",           CAM_GRH_CONCURSO."instancias/"      );
define( "CAM_GRH_CON_POPUPS",               CAM_GRH_CONCURSO."popups/"          );

//BENEFICIOS
define( "CAM_GRH_BEN_CLASSES",              CAM_GRH_BENEFICIOS."classes/"       );
define( "CAM_GRH_BEN_MAPEAMENTO",           CAM_GRH_BEN_CLASSES."mapeamento/"   );
define( "CAM_GRH_BEN_NEGOCIO",              CAM_GRH_BEN_CLASSES."negocio/"      );
define( "CAM_GRH_BEN_INSTANCIAS",           CAM_GRH_BENEFICIOS."instancias/"    );
define( "CAM_GRH_BEN_POPUPS",               CAM_GRH_BENEFICIOS."popups/"        );
define( "TBEN",                             CAM_GRH_BEN_MAPEAMENTO              );

//FOLHA_PAGAMENTO
define( "CAM_GRH_FOL_CLASSES",              CAM_GRH_FOLHA_PAGAMENTO."classes/"      );
define( "CAM_GRH_FOL_MAPEAMENTO",           CAM_GRH_FOL_CLASSES."mapeamento/"       );
define( "CAM_GRH_FOL_NEGOCIO",              CAM_GRH_FOL_CLASSES."negocio/"          );
define( "CAM_GRH_FOL_COMPONENTES",          CAM_GRH_FOL_CLASSES."componentes/"      );
define( "CAM_GRH_FOL_INSTANCIAS",           CAM_GRH_FOLHA_PAGAMENTO."instancias/"   );
define( "CAM_GRH_FOL_PROCESSAMENTO",        CAM_GRH_FOL_INSTANCIAS."processamento/" );
define( "CAM_GRH_FOL_POPUPS",               CAM_GRH_FOLHA_PAGAMENTO."popups/"       );
define( "CAM_GRH_FOL_AGT",                  CAM_GRH."AGT/folhaPagamento/"           );
define( "TFOL",                             CAM_GRH_FOL_MAPEAMENTO                  );

//ESTAGIO
define( "CAM_GRH_EST_CLASSES",              CAM_GRH_ESTAGIO."classes/"              );
define( "CAM_GRH_EST_MAPEAMENTO",           CAM_GRH_EST_CLASSES."mapeamento/"       );
define( "CAM_GRH_EST_NEGOCIO",              CAM_GRH_EST_CLASSES."negocio/"          );
define( "CAM_GRH_EST_COMPONENTES",          CAM_GRH_EST_CLASSES."componentes/"      );
define( "CAM_GRH_EST_INSTANCIAS",           CAM_GRH_ESTAGIO."instancias/"           );
define( "CAM_GRH_EST_PROCESSAMENTO",        CAM_GRH_EST_INSTANCIAS."processamento/" );
define( "CAM_GRH_EST_POPUPS",               CAM_GRH_ESTAGIO."popups/"               );
define( "TEST",                             CAM_GRH_EST_MAPEAMENTO                  );

//IMA
define( "CAM_GRH_IMA_CLASSES",              CAM_GRH_IMA."classes/"                  );
define( "CAM_GRH_IMA_MAPEAMENTO",           CAM_GRH_IMA_CLASSES."mapeamento/"       );
define( "CAM_GRH_IMA_NEGOCIO",              CAM_GRH_IMA_CLASSES."negocio/"          );
define( "CAM_GRH_IMA_COMPONENTES",          CAM_GRH_IMA_CLASSES."componentes/"      );
define( "CAM_GRH_IMA_INSTANCIAS",           CAM_GRH_IMA."instancias/"               );
define( "CAM_GRH_IMA_PROCESSAMENTO",        CAM_GRH_IMA_INSTANCIAS."processamento/" );
define( "CAM_GRH_IMA_POPUPS",               CAM_GRH_IMA."popups/"                   );
define( "TIMA",                             CAM_GRH_IMA_MAPEAMENTO                  );

//ENTIDADE
define( "CAM_GRH_ENT_CLASSES",              CAM_GRH_ENTIDADE."classes/"                  );
define( "CAM_GRH_ENT_MAPEAMENTO",           CAM_GRH_ENT_CLASSES."mapeamento/"       );
define( "CAM_GRH_ENT_NEGOCIO",              CAM_GRH_ENT_CLASSES."negocio/"          );
define( "CAM_GRH_ENT_COMPONENTES",          CAM_GRH_ENT_CLASSES."componentes/"      );
define( "CAM_GRH_ENT_INSTANCIAS",           CAM_GRH_ENTIDADE."instancias/"               );
define( "CAM_GRH_ENT_PROCESSAMENTO",        CAM_GRH_ENT_INSTANCIAS."processamento/" );
define( "CAM_GRH_ENT_POPUPS",               CAM_GRH_ENTIDADE."popups/"                   );
define( "TENT",                             CAM_GRH_ENT_MAPEAMENTO                  );

//DIARIAS
define( "CAM_GRH_DIA_CLASSES",              CAM_GRH_DIARIAS."classes/"              );
define( "CAM_GRH_DIA_MAPEAMENTO",           CAM_GRH_DIA_CLASSES."mapeamento/"       );
define( "CAM_GRH_DIA_NEGOCIO",              CAM_GRH_DIA_CLASSES."negocio/"          );
define( "CAM_GRH_DIA_COMPONENTES",          CAM_GRH_DIA_CLASSES."componentes/"      );
define( "CAM_GRH_DIA_INSTANCIAS",           CAM_GRH_DIARIAS."instancias/"           );
define( "CAM_GRH_DIA_PROCESSAMENTO",        CAM_GRH_DIA_INSTANCIAS."processamento/" );
define( "CAM_GRH_DIA_POPUPS",               CAM_GRH_DIARIAS."popups/"               );
define( "TDIA",                             CAM_GRH_DIA_MAPEAMENTO                  );

//RELOGIO PONTO
define( "CAM_GRH_PON_CLASSES",              CAM_GRH_PONTO."classes/"              );
define( "CAM_GRH_PON_MAPEAMENTO",           CAM_GRH_PON_CLASSES."mapeamento/"     );
define( "CAM_GRH_PON_NEGOCIO",              CAM_GRH_PON_CLASSES."negocio/"        );
define( "CAM_GRH_PON_COMPONENTES",          CAM_GRH_PON_CLASSES."componentes/"    );
define( "CAM_GRH_PON_INSTANCIAS",           CAM_GRH_PONTO."instancias/"           );
define( "CAM_GRH_PON_PROCESSAMENTO",        CAM_GRH_PON_INSTANCIAS."processamento/");
define( "CAM_GRH_PON_POPUPS",               CAM_GRH_PONTO."popups/"               );
define( "TPON",                             CAM_GRH_PON_MAPEAMENTO                );

?>
