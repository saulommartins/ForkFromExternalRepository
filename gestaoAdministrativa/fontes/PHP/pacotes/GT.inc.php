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

Casos de uso: uc-05.00.00
*/

//DEFINICAO DE VALIDADE E VERSAO DA GESTAO
define( "VALIDADE_GT",               "31/12/2017" );
define( "VERSAO_GT",                 "2.05.4"   );

define( "CAM_GT",  "../../../../../../gestaoTributaria/fontes/" );

//DEFINICAO DOS CAMINHOS

define( "CAM_GT_ARRECADACAO",           CAM_GT."PHP/arrecadacao/"           );
define( "CAM_GT_IMOBILIARIO",           CAM_GT."PHP/cadastroImobiliario/"   );
define( "CAM_GT_ECONOMICO",             CAM_GT."PHP/cadastroEconomico/"     );
define( "CAM_GT_MONETARIO",             CAM_GT."PHP/cadastroMonetario/"     );
define( "CAM_GT_DIVIDA_ATIVA",          CAM_GT."PHP/dividaAtiva/"           );
define( "CAM_GT_FISCALIZACAO",          CAM_GT."PHP/fiscalizacao/"          );
define( "CAM_GT_OBRAS_PUBLICAS",        CAM_GT."PHP/obrasPublicas/"         );

//ARRECADACAO
define( "CAM_GT_ARR_CLASSES",           CAM_GT_ARRECADACAO."classes/"        );
define( "CAM_GT_ARR_ANEXOS",            CAM_GT_ARR_CLASSES."anexos/"         );
define( "CAM_GT_ARR_MODELOS",           CAM_GT_ARR_ANEXOS."modelos_usuario/" );
define( "CAM_GT_ARR_MAPEAMENTO",        CAM_GT_ARR_CLASSES."mapeamento/"     );
define( "CAM_GT_ARR_NEGOCIO",           CAM_GT_ARR_CLASSES."negocio/"        );
define( "CAM_GT_ARR_FUNCAO",            CAM_GT_ARR_CLASSES."funcao/"         );
define( "CAM_GT_ARR_INSTANCIAS",        CAM_GT_ARRECADACAO."instancias/"     );
define( "CAM_GT_ARR_POPUPS",            CAM_GT_ARRECADACAO."popups/"         );
define( "CAM_GT_ARR_COMPONENTES",       CAM_GT_ARR_CLASSES."componentes/"    );
define( "TARR",                         CAM_GT_ARR_MAPEAMENTO                );

//IMOBILIARIO
define( "CAM_GT_CIM_CLASSES",           CAM_GT_IMOBILIARIO."classes/"     );
define( "CAM_GT_CIM_ANEXOS",            CAM_GT_IMOBILIARIO."anexos/"      );
define( "CAM_GT_CIM_MAPEAMENTO",        CAM_GT_CIM_CLASSES."mapeamento/"  );
define( "CAM_GT_CIM_NEGOCIO",           CAM_GT_CIM_CLASSES."negocio/"     );
define( "CAM_GT_CIM_INSTANCIAS",        CAM_GT_IMOBILIARIO."instancias/"  );
define( "CAM_GT_CIM_POPUPS",            CAM_GT_IMOBILIARIO."popups/"      );
define( "CAM_GT_CIM_COMPONENTES",       CAM_GT_CIM_CLASSES."componentes/" );
define( "TCIM",                         CAM_GT_CIM_MAPEAMENTO             );

//ECONOMICO
define( "CAM_GT_CEM_CLASSES",           CAM_GT_ECONOMICO."classes/"       );
define( "CAM_GT_CEM_ANEXOS",            CAM_GT_ECONOMICO."anexos/"        );
define( "CAM_GT_CEM_MAPEAMENTO",        CAM_GT_CEM_CLASSES."mapeamento/"  );
define( "CAM_GT_CEM_NEGOCIO",           CAM_GT_CEM_CLASSES."negocio/"     );
define( "CAM_GT_CEM_INSTANCIAS",        CAM_GT_ECONOMICO."instancias/"    );
define( "CAM_GT_CEM_POPUPS",            CAM_GT_ECONOMICO."popups/"        );
define( "CAM_GT_CEM_COMPONENTES",       CAM_GT_CEM_CLASSES."componentes/" );
define( "TCEM",                         CAM_GT_CEM_MAPEAMENTO             );

//MONETARIO
define( "CAM_GT_MON_CLASSES",           CAM_GT_MONETARIO."classes/"       );
define( "CAM_GT_MON_MAPEAMENTO",        CAM_GT_MON_CLASSES."mapeamento/"  );
define( "CAM_GT_MON_NEGOCIO",           CAM_GT_MON_CLASSES."negocio/"     );
define( "CAM_GT_MON_INSTANCIAS",        CAM_GT_MONETARIO."instancias/"    );
define( "CAM_GT_MON_POPUPS",            CAM_GT_MONETARIO."popups/"        );
define( "CAM_GT_MON_COMPONENTES",       CAM_GT_MON_CLASSES."componentes/" );
define( "TMON",                         CAM_GT_MON_MAPEAMENTO             );

//DIVIDA ATIVA
define( "CAM_GT_DAT_CLASSES",           CAM_GT_DIVIDA_ATIVA."classes/"    );
define( "CAM_GT_DAT_ANEXOS",            CAM_GT_DAT_CLASSES."anexos/"      );
define( "CAM_GT_DAT_AGT",               CAM_GT_DAT_ANEXOS."agt/"          );
define( "CAM_GT_DAT_MODELOS",           CAM_GT_DAT_ANEXOS."modelos_usuario/" );
define( "CAM_GT_DAT_MAPEAMENTO",        CAM_GT_DAT_CLASSES."mapeamento/"  );
define( "CAM_GT_DAT_NEGOCIO",           CAM_GT_DAT_CLASSES."negocio/"     );
define( "CAM_GT_DAT_INSTANCIAS",        CAM_GT_DIVIDA_ATIVA."instancias/" );
define( "CAM_GT_DAT_POPUPS",            CAM_GT_DIVIDA_ATIVA."popups/"     );
define( "CAM_GT_DAT_COMPONENTES",       CAM_GT_DAT_CLASSES."componentes/" );
define( "CAM_GT_DAT_FUNCAO",            CAM_GT_DAT_CLASSES."funcao/"      );
define( "TDAT",                         CAM_GT_DAT_MAPEAMENTO             );

//OBRAS PUBLICAS
define( "CAM_GT_OBP_CLASSES",           CAM_GT_OBRAS_PUBLICAS."classes/"    );
define( "CAM_GT_OBP_MAPEAMENTO",        CAM_GT_OBP_CLASSES."mapeamento/"    );
define( "CAM_GT_OBP_NEGOCIO",           CAM_GT_OBP_CLASSES."negocio/"       );
define( "CAM_GT_OBP_INSTANCIAS",        CAM_GT_OBRAS_PUBLICAS."instancias/" );
define( "CAM_GT_OBP_POPUPS",            CAM_GT_OBRAS_PUBLICAS."popups/"     );
define( "CAM_GT_OBP_COMPONENTES",       CAM_GT_OBP_CLASSES."componentes/"   );
define( "TOBP",                         CAM_GT_OBP_MAPEAMENTO               );

//FISCALIZACAO
define( "CAM_GT_FIS_CLASSES",           CAM_GT_FISCALIZACAO."classes/"    );
define( "CAM_GT_FIS_MAPEAMENTO",        CAM_GT_FIS_CLASSES."mapeamento/"  );
define( "CAM_GT_FIS_NEGOCIO",           CAM_GT_FIS_CLASSES."negocio/"     );
define( "CAM_GT_FIS_INSTANCIAS",        CAM_GT_FISCALIZACAO."instancias/" );
define( "CAM_GT_FIS_POPUPS",            CAM_GT_FISCALIZACAO."popups/"     );
define( "CAM_GT_FIS_COMPONENTES",       CAM_GT_FIS_CLASSES."componentes/" );
define( "CAM_GT_FIS_VISAO",             CAM_GT_FIS_CLASSES."visao/"       );
define( "CAM_GT_FIS_ANEXOS",            CAM_GT_FIS_CLASSES."anexos/"      );
define( "CAM_GT_FIS_MODELOS",           CAM_GT_FIS_ANEXOS."modelos_usuario/" );

define( "TFIS",                         CAM_GT_FIS_MAPEAMENTO             );

// agata
define( "CAM_GT_AGT" , CAM_GT."AGT/"    );
define( "CAM_GT_AGT_ARR",       CAM_GT_AGT."arrecadacao/" );

?>
