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
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-02.00.00
*/

//DEFINICAO DE VALIDADE E VERSAO DA GESTAO
define( "VALIDADE_GF",               "31/12/2017" );
define( "VERSAO_GF",                 "2.05.4" );

define( "CAM_GF",  "../../../../../../gestaoFinanceira/fontes/" );

//DEFINICAO DOS CAMINHOS

define( "CAM_GF_CONTABILIDADE",         CAM_GF."PHP/contabilidade/"         );
define( "CAM_GF_EMPENHO",               CAM_GF."PHP/empenho/"               );
define( "CAM_GF_ORCAMENTO",             CAM_GF."PHP/orcamento/"             );
define( "CAM_GF_TESOURARIA",            CAM_GF."PHP/tesouraria/"            );
define( "CAM_GF_EXPORTACAO",            CAM_GF."PHP/exportacao/"            );
define( "CAM_GF_LRF",                   CAM_GF."PHP/LRF/"                   );
define( "CAM_GF_PPA",                   CAM_GF."PHP/ppa/"                   );
define( "CAM_GF_LDO",                   CAM_GF."PHP/ldo/"                   );
define( "CAM_GF_INCLUDE",               CAM_GF."PHP/include/"               );

//CONTABILIDADE
define( "CAM_GF_CONT_CLASSES",          CAM_GF_CONTABILIDADE."classes/"     );
define( "CAM_GF_CONT_MAPEAMENTO",       CAM_GF_CONT_CLASSES."mapeamento/"   );
define( "CAM_GF_CONT_NEGOCIO",          CAM_GF_CONT_CLASSES."negocio/"      );
define( "CAM_GF_CONT_INSTANCIAS",       CAM_GF_CONTABILIDADE."instancias/"  );
define( "CAM_GF_CONT_POPUPS",           CAM_GF_CONTABILIDADE."popups/"      );
define( "CAM_GF_CONT_COMPONENTES",      CAM_GF_CONT_CLASSES."componentes/"   );
define( "CAM_GF_CONT_PROCESSAMENTO",    CAM_GF_CONT_INSTANCIAS."processamento/"   );
define( "TCON",                         CAM_GF_CONT_MAPEAMENTO              );

//EMPENHO
define( "CAM_GF_EMP_CLASSES",           CAM_GF_EMPENHO."classes/"           );
define( "CAM_GF_EMP_MAPEAMENTO",        CAM_GF_EMP_CLASSES."mapeamento/"    );
define( "CAM_GF_EMP_NEGOCIO",           CAM_GF_EMP_CLASSES."negocio/"       );
define( "CAM_GF_EMP_INSTANCIAS",        CAM_GF_EMPENHO."instancias/"        );
define( "CAM_GF_EMP_POPUPS",            CAM_GF_EMPENHO."popups/"            );
define( "CAM_GF_EMP_COMPONENTES",       CAM_GF_EMP_CLASSES."componentes/"   );
define( "CAM_GF_EMP_PROCESSAMENTO",     CAM_GF_EMP_INSTANCIAS."processamento/"   );

define( "TEMP",                         CAM_GF_EMP_MAPEAMENTO               );

//ORCAMENTO
define( "CAM_GF_ORC_CLASSES",           CAM_GF_ORCAMENTO."classes/"         );
define( "CAM_GF_ORC_MAPEAMENTO",        CAM_GF_ORC_CLASSES."mapeamento/"    );
define( "CAM_GF_ORC_NEGOCIO",           CAM_GF_ORC_CLASSES."negocio/"       );
define( "CAM_GF_ORC_INSTANCIAS",        CAM_GF_ORCAMENTO."instancias/"      );
define( "CAM_GF_ORC_POPUPS",            CAM_GF_ORCAMENTO."popups/"          );
define( "CAM_GF_ORC_COMPONENTES",       CAM_GF_ORC_CLASSES."componentes/"   );
define( "CAM_GF_ORC_PROCESSAMENTO",     CAM_GF_ORC_INSTANCIAS."processamento/"   );
define( "TORC",                         CAM_GF_ORC_MAPEAMENTO               );

//TESOURARIA
define( "CAM_GF_TES_CLASSES",           CAM_GF_TESOURARIA."classes/"        );
define( "CAM_GF_TES_MAPEAMENTO",        CAM_GF_TES_CLASSES."mapeamento/"    );
define( "CAM_GF_TES_NEGOCIO",           CAM_GF_TES_CLASSES."negocio/"       );
define( "CAM_GF_TES_INSTANCIAS",        CAM_GF_TESOURARIA."instancias/"     );
define( "CAM_GF_TES_POPUPS",            CAM_GF_TESOURARIA."popups/"         );
define( "CAM_GF_TES_COMPONENTES",       CAM_GF_TES_CLASSES."componentes/"   );
define( "CAM_GF_TES_PROCESSAMENTO",     CAM_GF_TES_INSTANCIAS."processamento/"   );
define( "CAM_GF_TES_CONTROLE",          CAM_GF_TES_CLASSES."controle/"      );

define( "TTES",                         CAM_GF_TES_MAPEAMENTO               );

//EXPORTACAO
define( "CAM_GF_EXP_CLASSES",           CAM_GF_EXPORTACAO."classes/"        );
define( "CAM_GF_EXP_MAPEAMENTO",        CAM_GF_EXP_CLASSES."mapeamento/"    );
define( "CAM_GF_EXP_NEGOCIO",           CAM_GF_EXP_CLASSES."negocio/"       );
define( "CAM_GF_EXP_INSTANCIAS",        CAM_GF_EXPORTACAO."instancias/"     );
define( "CAM_GF_EXP_POPUPS",            CAM_GF_EXPORTACAO."popups/"         );
define( "CAM_GF_EXP_COMPONENTES",       CAM_GF_EXP_CLASSES."componentes/"   );
define( "CAM_GF_EXP_PROCESSAMENTO",     CAM_GF_EXP_INSTANCIAS."processamento/"   );

define( "TEXP",                         CAM_GF_EXP_MAPEAMENTO               );

//LRF
define( "CAM_GF_LRF_CLASSES",           CAM_GF_LRF."classes/"               );
define( "CAM_GF_LRF_MAPEAMENTO",        CAM_GF_LRF_CLASSES."mapeamento/"    );
define( "CAM_GF_LRF_NEGOCIO",           CAM_GF_LRF_CLASSES."negocio/"       );
define( "CAM_GF_LRF_INSTANCIAS",        CAM_GF_LRF."instancias/"            );
define( "CAM_GF_LRF_POPUPS",            CAM_GF_LRF."popups/"                );
define( "CAM_GF_LRF_COMPONENTES",       CAM_GF_LRF_CLASSES."componentes/"   );
define( "CAM_GF_LRF_PROCESSAMENTO",     CAM_GF_LRF_INSTANCIAS."processamento/"   );

define( "TLRF",                         CAM_GF_LRF_MAPEAMENTO               );

//PPA
define( "CAM_GF_PPA_CLASSES",           CAM_GF_PPA."classes/"        );
define( "CAM_GF_PPA_MAPEAMENTO",        CAM_GF_PPA_CLASSES."mapeamento/"    );
define( "CAM_GF_PPA_NEGOCIO",           CAM_GF_PPA_CLASSES."negocio/"       );
define( "CAM_GF_PPA_INSTANCIAS",        CAM_GF_PPA."instancias/"     );
define( "CAM_GF_PPA_POPUPS",            CAM_GF_PPA."popups/"         );
define( "CAM_GF_PPA_COMPONENTES",       CAM_GF_PPA_CLASSES."componentes/"   );
define( "CAM_GF_PPA_PROCESSAMENTO",     CAM_GF_PPA_INSTANCIAS."processamentoa/");
define( "CAM_GF_PPA_VISAO",             CAM_GF_PPA_CLASSES."visao/"       );

define( "TPPA",                         CAM_GF_PPA_MAPEAMENTO               );

//LDO
define( "CAM_GF_LDO_CLASSES",           CAM_GF_LDO."classes/"        );
define( "CAM_GF_LDO_MAPEAMENTO",        CAM_GF_LDO_CLASSES."mapeamento/"    );
define( "CAM_GF_LDO_NEGOCIO",           CAM_GF_LDO_CLASSES."negocio/"       );
define( "CAM_GF_LDO_VISAO",             CAM_GF_LDO_CLASSES."visao/"       );
define( "CAM_GF_LDO_UTIL",              CAM_GF_LDO_CLASSES."util/"       );
define( "CAM_GF_LDO_INSTANCIAS",        CAM_GF_LDO."instancias/"     );
define( "CAM_GF_LDO_POPUPS",            CAM_GF_LDO."popups/"         );
define( "CAM_GF_LDO_COMPONENTES",       CAM_GF_LDO_CLASSES."componentes/"   );
define( "CAM_GF_LDO_PROCESSAMENTO",     CAM_GF_LDO_INSTANCIAS."processamento/"   );

define( "TLDO",                         CAM_GF_LDO_MAPEAMENTO               );

// Componentes
define( "CLA_IRECEITADEDUTORA",          CAM_GF_ORC_COMPONENTES."IReceitaDedutora.class.php"    );
define( "CLA_IAPPLETTERMINAL",          CAM_GF_TES_COMPONENTES."IAppletTerminal.class.php"      );
define( "CLA_IAPPLETAUTENTICACAO",      CAM_GF_TES_COMPONENTES."IAppletAutenticacao.class.php"  );

//DEFINICAO DOS CAMINHOS RELATORIOS
define( "CAM_GF_AGT",                  CAM_GF."AGT/"                      );

//CONTABILIDADE
define( "CAM_GF_AGT_CONT",              CAM_GF_AGT."contabilidade/"         );
?>
