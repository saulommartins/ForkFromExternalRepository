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

Casos de uso: uc-03.00.00
*/

//DEFINICAO DE VALIDADE E VERSAO DA GESTAO
define( "VALIDADE_GP",               "31/12/2017" );
define( "VERSAO_GP",                 "2.05.4" );

define( "CAM_GP",  "../../../../../../gestaoPatrimonial/fontes/" );
define( "CAM_GP_JAVA",  "../../../../../../gestaoPatrimonial/fontes/java/" );

//DEFINICAO DOS CAMINHOS
define( "CAM_GP_PATRIMONIO",          CAM_GP."PHP/patrimonio/"           );
define( "CAM_GP_ALMOXARIFADO",        CAM_GP."PHP/almoxarifado/"         );
define( "CAM_GP_COMPRAS",             CAM_GP."PHP/compras/"              );
define( "CAM_GP_FROTA",               CAM_GP."PHP/frota/"                );
define( "CAM_GP_LICITACAO",           CAM_GP."PHP/licitacao/"            );

//PATRIMONIO
define( "CAM_GP_PAT_ANEXOS",           CAM_GP_PATRIMONIO."anexos/"             );
define( "CAM_GP_PAT_CLASSES",          CAM_GP_PATRIMONIO."classes/"      );
define( "CAM_GP_PAT_MAPEAMENTO",       CAM_GP_PAT_CLASSES."mapeamento/"  );
define( "CAM_GP_PAT_NEGOCIO",          CAM_GP_PAT_CLASSES."negocio/"     );
define( "CAM_GP_PAT_INSTANCIAS",       CAM_GP_PATRIMONIO."instancias/"   );
define( "CAM_GP_PAT_POPUPS",           CAM_GP_PATRIMONIO."popups/"       );
define( "CAM_GP_PAT_PROCESSAMENTO",    CAM_GP_PAT_INSTANCIAS."processamento/"   );
define( "CAM_GP_PAT_COMPONENTES",      CAM_GP_PAT_CLASSES."componentes/" );
define( "TPAT",                        CAM_GP_PAT_MAPEAMENTO             );

//ALMOXARIFADO
define( "CAM_GP_ALM_CLASSES",          CAM_GP_ALMOXARIFADO."classes/"    );
define( "CAM_GP_ALM_MAPEAMENTO",       CAM_GP_ALM_CLASSES."mapeamento/"  );
define( "CAM_GP_ALM_NEGOCIO",          CAM_GP_ALM_CLASSES."negocio/"     );
define( "CAM_GP_ALM_COMPONENTES",      CAM_GP_ALM_CLASSES."componentes/" );
define( "CAM_GP_ALM_INSTANCIAS",       CAM_GP_ALMOXARIFADO."instancias/" );
define( "CAM_GP_ALM_PROCESSAMENTO",    CAM_GP_ALM_INSTANCIAS."processamento/"   );
define( "CAM_GP_ALM_POPUPS",           CAM_GP_ALMOXARIFADO."popups/"     );
define( "TALM",                        CAM_GP_ALM_MAPEAMENTO             );

//COMPRAS
define( "CAM_GP_COM_CLASSES",          CAM_GP_COMPRAS."classes/"         );
define( "CAM_GP_COM_MAPEAMENTO",       CAM_GP_COM_CLASSES."mapeamento/"  );
define( "CAM_GP_COM_NEGOCIO",          CAM_GP_COM_CLASSES."negocio/"     );
define( "CAM_GP_COM_INSTANCIAS",       CAM_GP_COMPRAS."instancias/"      );
define( "CAM_GP_COM_PROCESSAMENTO",    CAM_GP_COM_INSTANCIAS."processamento/"   );
define( "CAM_GP_COM_POPUPS",           CAM_GP_COMPRAS."popups/"          );
define( "CAM_GP_COM_COMPONENTES",      CAM_GP_COM_CLASSES."componentes/" );
define( "TCOM",                        CAM_GP_COM_MAPEAMENTO             );

//FROTA
define( "CAM_GP_FRO_CLASSES",          CAM_GP_FROTA."classes/"          );
define( "CAM_GP_FRO_MAPEAMENTO",       CAM_GP_FRO_CLASSES."mapeamento/" );
define( "CAM_GP_FRO_NEGOCIO",          CAM_GP_FRO_CLASSES."negocio/"    );
define( "CAM_GP_FRO_COMPONENTES",      CAM_GP_FRO_CLASSES."componentes/" );
define( "CAM_GP_FRO_INSTANCIAS",       CAM_GP_FROTA."instancias/"       );
define( "CAM_GP_FRO_PROCESSAMENTO",    CAM_GP_FRO_INSTANCIAS."processamento/");
define( "CAM_GP_FRO_POPUPS",           CAM_GP_FROTA."popups/"           );
define( "TFRO",                        CAM_GP_FRO_MAPEAMENTO            );

//LICITACAO
define ( "CAM_GP_LIC_ANEXOS",          CAM_GP_LICITACAO."anexos/"             );
define ( "CAM_GP_LIC_CLASSES",         CAM_GP_LICITACAO."classes/"            );
define ( "CAM_GP_LIC_INSTANCIAS",      CAM_GP_LICITACAO."instancias/"         );
define ( "CAM_GP_LIC_PROCESSAMENTO",   CAM_GP_LIC_INSTANCIAS."processamento/" );
define ( "CAM_GP_LIC_PROCESSOLICITATORIO",   CAM_GP_LIC_INSTANCIAS."processoLicitatorio/" );
define ( "CAM_GP_LIC_MAPEAMENTO",      CAM_GP_LIC_CLASSES."mapeamento/"       );
define ( "CAM_GP_LIC_COMPONENTES",     CAM_GP_LIC_CLASSES."componentes/"      );
define ( "CAM_GP_LIC_POPUPS",          CAM_GP_LICITACAO."popups/"             );
define( "TLIC",                        CAM_GP_LIC_MAPEAMENTO                  );

//DEFINICAO DOS CAMINHOS RELATORIOS
define( "CAM_GP_AGT",                  CAM_GP."AGT/"                      );

//ALMOXARIFADO
define( "CAM_GP_AGT_ALM",              CAM_GP_AGT."almoxarifado/"         );
