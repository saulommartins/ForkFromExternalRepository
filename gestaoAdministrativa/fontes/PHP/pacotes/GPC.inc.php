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
* Data de Criação: 29/03/2006

* @author Desenvolvedor: Fernando Zank Correa Evangelista

* @author Documentor:
* @package framework
* @subpackage componentes

Casos de uso: uc-03.00.00
*/

//DEFINICAO DE VALIDADE E VERSAO DA GESTAO
define( "VALIDADE_GPC",               "31/12/2017" );
define( "VERSAO_GPC",                 "2.05.4" );

define( "CAM_GPC",  "../../../../../../gestaoPrestacaoContas/fontes/" );

//DEFINICAO DOS CAMINHOS
define( "CAM_GPC_TCERJ",              CAM_GPC."PHP/TCERJ/" );
define( "CAM_GPC_STN",                CAM_GPC."PHP/STN/"   );
define( "CAM_GPC_TPB",                CAM_GPC."PHP/TCEPB/" );
define( "CAM_GPC_TAM",                CAM_GPC."PHP/TCEAM/" );
define( "CAM_GPC_TGO",                CAM_GPC."PHP/TCMGO/" );
define( "CAM_GPC_TCERS",              CAM_GPC."PHP/TCERS/" );
define( "CAM_GPC_TCMBA",              CAM_GPC."PHP/TCMBA/" );
define( "CAM_GPC_TCERN",              CAM_GPC."PHP/TCERN/" );
define( "CAM_GPC_TCMPA",              CAM_GPC."PHP/TCMPA/" );
define( "CAM_GPC_TCEMG",              CAM_GPC."PHP/TCEMG/" );
define( "CAM_GPC_TCEMS",              CAM_GPC."PHP/TCEMS/" );
define( "CAM_GPC_TCEAM",              CAM_GPC."PHP/TCEAM/" );
define( "CAM_GPC_MANAD",              CAM_GPC."PHP/manad/" );
define( "CAM_GPC_TRANSPARENCIA",      CAM_GPC."PHP/transparencia/" );
define( "CAM_GPC_ITBI_IPTU",          CAM_GPC."PHP/itbi_iptu/" );
define( "CAM_GPC_SIOPE_SIOPS",        CAM_GPC."PHP/SIOPE_SIOPS/");
define( "CAM_GPC_TCEAL",              CAM_GPC."PHP/TCEAL/" );
define( "CAM_GPC_TCEPE",              CAM_GPC."PHP/TCEPE/" );
define( "CAM_GPC_TCETO",              CAM_GPC."PHP/TCETO/" );
define( "CAM_GPC_SICONFI",            CAM_GPC."PHP/SICONFI/" );


// RELATÓRIOS DE SIOPE / SIOPS
define( "CAM_GPC_SIOPE_SIOPS_CLASSES",          CAM_GPC_SIOPE_SIOPS."classes/"      );
define( "CAM_GPC_SIOPE_SIOPS_MAPEAMENTO",       CAM_GPC_SIOPE_SIOPS_CLASSES."mapeamento/"  );
define( "CAM_GPC_SIOPE_SIOPS_NEGOCIO",          CAM_GPC_SIOPE_SIOPS_CLASSES."negocio/"     );
define( "CAM_GPC_SIOPE_SIOPS_INSTANCIAS",       CAM_GPC_SIOPE_SIOPS."instancias/"   );
define( "CAM_GPC_SIOPE_SIOPS_POPUPS",           CAM_GPC_SIOPE_SIOPS."popups/"       );
define( "SIOPE_SIOPS",                          CAM_GPC_SIOPE_SIOPS_MAPEAMENTO               );

//TRIBUNAL DE CONTAS RJ
define( "CAM_GPC_TCERJ_CLASSES",          CAM_GPC_TCERJ."classes/"      );
define( "CAM_GPC_TCERJ_MAPEAMENTO",       CAM_GPC_TCERJ_CLASSES."mapeamento/"  );
define( "CAM_GPC_TCERJ_NEGOCIO",          CAM_GPC_TCERJ_CLASSES."negocio/"     );
define( "CAM_GPC_TCERJ_INSTANCIAS",       CAM_GPC_TCERJ."instancias/"   );
define( "CAM_GPC_TCERJ_POPUPS",           CAM_GPC_TCERJ."popups/"       );
define( "TCRJ",                           CAM_GPC_TCERJ_MAPEAMENTO               );

//TRIBUNAL DE CONTAS DA PARAÍBA
define( "CAM_GPC_TPB_CLASSES",          CAM_GPC_TPB."classes/"      );
define( "CAM_GPC_TPB_MAPEAMENTO",       CAM_GPC_TPB_CLASSES."mapeamento/"  );
define( "CAM_GPC_TPB_NEGOCIO",          CAM_GPC_TPB_CLASSES."negocio/"     );
define( "CAM_GPC_TPB_INSTANCIAS",       CAM_GPC_TPB."instancias/"   );
define( "CAM_GPC_TPB_POPUPS",           CAM_GPC_TPB."popups/"       );
define( "TTPB",                         CAM_GPC_TPB_MAPEAMENTO               );

//TRIBUNAL DE CONTAS DO AMAZONAS
define( "CAM_GPC_TCEAM_CLASSES",          CAM_GPC_TCEAM."classes/"      );
define( "CAM_GPC_TCEAM_MAPEAMENTO",       CAM_GPC_TCEAM_CLASSES."mapeamento/"  );
define( "CAM_GPC_TCEAM_NEGOCIO",          CAM_GPC_TCEAM_CLASSES."negocio/"     );
define( "CAM_GPC_TCEAM_INSTANCIAS",       CAM_GPC_TCEAM."instancias/"   );
define( "CAM_GPC_TCEAM_POPUPS",           CAM_GPC_TCEAM."popups/"       );
define( "TTCEAM",                         CAM_GPC_TCEAM_MAPEAMENTO               );

//TRIBUNAL DE CONTAS DO RIO GRANDE DO NORTE
define( "CAM_GPC_TCERN_CLASSES",          CAM_GPC_TCERN."classes/"      );
define( "CAM_GPC_TCERN_MAPEAMENTO",       CAM_GPC_TCERN_CLASSES."mapeamento/"  );
define( "CAM_GPC_TCERN_NEGOCIO",          CAM_GPC_TCERN_CLASSES."negocio/"     );
define( "CAM_GPC_TCERN_INSTANCIAS",       CAM_GPC_TCERN."instancias/"   );
define( "CAM_GPC_TCERN_POPUPS",           CAM_GPC_TCERN."popups/"       );
define( "TTRN",                           CAM_GPC_TCERN_MAPEAMENTO               );

//TRIBUNAL DE CONTAS DE GOIAS
define( "CAM_GPC_TGO_CLASSES",          CAM_GPC_TGO."classes/"             );
define( "CAM_GPC_TGO_MAPEAMENTO",       CAM_GPC_TGO_CLASSES."mapeamento/"  );
define( "CAM_GPC_TGO_NEGOCIO",          CAM_GPC_TGO_CLASSES."negocio/"     );
define( "CAM_GPC_TGO_CONTROLE",         CAM_GPC_TGO_CLASSES."controle/"    );
define( "CAM_GPC_TGO_COMPONENTES",      CAM_GPC_TGO_CLASSES."componentes/" );
define( "CAM_GPC_TGO_INSTANCIAS",       CAM_GPC_TGO."instancias/"          );
define( "CAM_GPC_TGO_POPUPS",           CAM_GPC_TGO."popups/"              );
define( "TTGO",                         CAM_GPC_TGO_MAPEAMENTO             );

//TRIBUNAL DE CONTAS DO RS
define( "CAM_GPC_TCERS_CLASSES",          CAM_GPC_TCERS."classes/"      );
define( "CAM_GPC_TCERS_MAPEAMENTO",       CAM_GPC_TCERS_CLASSES."mapeamento/"  );
define( "CAM_GPC_TCERS_NEGOCIO",          CAM_GPC_TCERS_CLASSES."negocio/"     );
define( "CAM_GPC_TCERS_INSTANCIAS",       CAM_GPC_TCERS."instancias/"   );
define( "CAM_GPC_TCERS_POPUPS",           CAM_GPC_TCERS."popups/"       );
define( "TTRS",                         CAM_GPC_TCERS_MAPEAMENTO               );

//TRIBUNAL DE CONTAS DO BA
define( "CAM_GPC_TCMBA_CLASSES",          CAM_GPC_TCMBA."classes/"      );
define( "CAM_GPC_TCMBA_MAPEAMENTO",       CAM_GPC_TCMBA_CLASSES."mapeamento/"  );
define( "CAM_GPC_TCMBA_NEGOCIO",          CAM_GPC_TCMBA_CLASSES."negocio/"     );
define( "CAM_GPC_TCMBA_INSTANCIAS",       CAM_GPC_TCMBA."instancias/"   );
define( "CAM_GPC_TCMBA_POPUPS",           CAM_GPC_TCMBA."popups/"       );
define( "TTBA",                         CAM_GPC_TCMBA_MAPEAMENTO               );

// STN ....
define( "CAM_GPC_STN_CLASSES"   ,       CAM_GPC_STN."classes/"           );
define( "CAM_GPC_STN_MAPEAMENTO",       CAM_GPC_STN_CLASSES."mapeamento/");
define( "CAM_GPC_STN_NEGOCIO"   ,       CAM_GPC_STN_CLASSES."negocio/"   );
define( "CAM_GPC_STN_CONTROLE"  ,       CAM_GPC_STN_CLASSES."controle/"  );
define( "CAM_GPC_STN_INSTANCIAS",       CAM_GPC_STN."instancias/"        );
define( "CAM_GPC_STN_POPUPS"    ,       CAM_GPC_STN."popups/"            );

//TRIBUNAL DE CONTAS DO PA
define( "CAM_GPC_TCMPA_CLASSES",        CAM_GPC_TCMPA."classes/"            );
define( "CAM_GPC_TCMPA_MAPEAMENTO",     CAM_GPC_TCMPA_CLASSES."mapeamento/" );
define( "CAM_GPC_TCMPA_NEGOCIO",        CAM_GPC_TCMPA_CLASSES."negocio/"    );
define( "CAM_GPC_TCMPA_INSTANCIAS",     CAM_GPC_TCMPA."instancias/"         );
define( "CAM_GPC_TCMPA_POPUPS",         CAM_GPC_TCMPA."popups/"             );
define( "TTPA",                         CAM_GPC_TCMPA_MAPEAMENTO            );

// TCEMG
define( "CAM_GPC_TCEMG_CLASSES"   ,       CAM_GPC_TCEMG."classes/"             );
define( "CAM_GPC_TCEMG_MAPEAMENTO",       CAM_GPC_TCEMG_CLASSES."mapeamento/"  );
define( "CAM_GPC_TCEMG_NEGOCIO"   ,       CAM_GPC_TCEMG_CLASSES."negocio/"     );
define( "CAM_GPC_TCEMG_CONTROLE"  ,       CAM_GPC_TCEMG_CLASSES."controle/"    );
define( "CAM_GPC_TCEMG_INSTANCIAS",       CAM_GPC_TCEMG."instancias/"          );
define( "CAM_GPC_TCEMG_POPUPS"    ,       CAM_GPC_TCEMG."popups/"              );
define( "CAM_GPC_TCEMG_RELATORIOS",       CAM_GPC_TCEMG_INSTANCIAS."relatorios/");

// TCEMS
define( "CAM_GPC_TCEMS_CLASSES"   ,       CAM_GPC_TCEMS."classes/"           );
define( "CAM_GPC_TCEMS_MAPEAMENTO",       CAM_GPC_TCEMS_CLASSES."mapeamento/");
define( "CAM_GPC_TCEMS_NEGOCIO"   ,       CAM_GPC_TCEMS_CLASSES."negocio/"   );
define( "CAM_GPC_TCEMS_CONTROLE"  ,       CAM_GPC_TCEMS_CLASSES."controle/"  );
define( "CAM_GPC_TCEMS_INSTANCIAS",       CAM_GPC_TCEMS."instancias/"        );
define( "CAM_GPC_TCEMS_POPUPS"    ,       CAM_GPC_TCEMS."popups/"            );

//DEFINICAO DOS CAMINHOS RELATORIOS
define( "CAM_GPC_AGT",                  CAM_GPC."AGT/"                    );
define( "CAM_GPC_AGT_TCERJ",            CAM_GPC_AGT."TCERJ/"              );
define( "CAM_GPC_AGT_STN",              CAM_GPC_AGT."STN/"                );

//MANAD
define( "CAM_GPC_MANAD_CLASSES"   , CAM_GPC_MANAD."classes/"            );
define( "CAM_GPC_MANAD_MAPEAMENTO", CAM_GPC_MANAD_CLASSES."mapeamento/" );
define( "CAM_GPC_MANAD_NEGOCIO"   , CAM_GPC_MANAD_CLASSES."negocio/"    );
define( "CAM_GPC_MANAD_INSTANCIAS", CAM_GPC_MANAD."instancias/"         );

// Transparencia
define( "CAM_GPC_TRANSPARENCIA_CLASSES"    , CAM_GPC_TRANSPARENCIA."classes/"           );
define( "CAM_GPC_TRANSPARENCIA_MAPEAMENTO" , CAM_GPC_TRANSPARENCIA_CLASSES."mapeamento/");
define( "CAM_GPC_TRANSPARENCIA_NEGOCIO"    , CAM_GPC_TRANSPARENCIA_CLASSES."negocio/"   );
define( "CAM_GPC_TRANSPARENCIA_CONTROLE"   , CAM_GPC_TRANSPARENCIA."controle/"  );
define( "CAM_GPC_TRANSPARENCIA_INSTANCIAS" , CAM_GPC_TRANSPARENCIA."instancias/"        );
define( "CAM_GPC_TRANSPARENCIA_ARQUIVOS"   , CAM_GPC_TRANSPARENCIA."instancias/exportacao/arquivos/" );

// Tributario
define( "CAM_GPC_ITBI_IPTU_CLASSES"   ,       CAM_GPC_ITBI_IPTU."classes/"           );
define( "CAM_GPC_ITBI_IPTU_MAPEAMENTO",       CAM_GPC_ITBI_IPTU_CLASSES."mapeamento/");
define( "CAM_GPC_ITBI_IPTU_NEGOCIO"   ,       CAM_GPC_ITBI_IPTU_CLASSES."negocio/"   );
define( "CAM_GPC_ITBI_IPTU_INSTANCIAS",       CAM_GPC_ITBI_IPTU."instancias/"        );

//TRIBUNAL DE CONTAS DE AL
define( "CAM_GPC_TCEAL_CLASSES"   ,       CAM_GPC_TCEAL."classes/"               );
define( "CAM_GPC_TCEAL_MAPEAMENTO",       CAM_GPC_TCEAL_CLASSES."mapeamento/"    );
define( "CAM_GPC_TCEAL_NEGOCIO"   ,       CAM_GPC_TCEAL_CLASSES."negocio/"       );
define( "CAM_GPC_TCEAL_CONTROLE"  ,       CAM_GPC_TCEAL_CLASSES."controle/"      );
define( "CAM_GPC_TCEAL_INSTANCIAS",       CAM_GPC_TCEAL."instancias/"            );
define( "CAM_GPC_TCEAL_POPUPS"    ,       CAM_GPC_TCEAL."popups/"                );
define( "CAM_GPC_TCEAL_RELATORIOS",       CAM_GPC_TCEAL_INSTANCIAS."relatorios/" );

//TRIBUNAL DE CONTAS DE PE
define( "CAM_GPC_TCEPE_CLASSES"   , CAM_GPC_TCEPE."classes/"           );
define( "CAM_GPC_TCEPE_MAPEAMENTO", CAM_GPC_TCEPE_CLASSES."mapeamento/");
define( "CAM_GPC_TCEPE_NEGOCIO"   , CAM_GPC_TCEPE_CLASSES."negocio/"   );
define( "CAM_GPC_TCEPE_CONTROLE"  , CAM_GPC_TCEPE_CLASSES."controle/"  );
define( "CAM_GPC_TCEPE_INSTANCIAS", CAM_GPC_TCEPE."instancias/"        );
define( "CAM_GPC_TCEPE_POPUPS"    , CAM_GPC_TCEPE."popups/"            );
define( "TTPE"                    , CAM_GPC_TCEPE_MAPEAMENTO           );

//TRIBUNAL DE CONTAS DE TO
define( "CAM_GPC_TCETO_CLASSES"   , CAM_GPC_TCETO."classes/"           );
define( "CAM_GPC_TCETO_MAPEAMENTO", CAM_GPC_TCETO_CLASSES."mapeamento/");
define( "CAM_GPC_TCETO_NEGOCIO"   , CAM_GPC_TCETO_CLASSES."negocio/"   );
define( "CAM_GPC_TCETO_CONTROLE"  , CAM_GPC_TCETO_CLASSES."controle/"  );
define( "CAM_GPC_TCETO_INSTANCIAS", CAM_GPC_TCETO."instancias/"        );
define( "CAM_GPC_TCETO_POPUPS"    , CAM_GPC_TCETO."popups/"            );
define( "TTTO"                    , CAM_GPC_TCETO_MAPEAMENTO           );

// RELATÓRIOS DE SIOPE / SIOPS
define( "CAM_GPC_SICONFI_CLASSES",          CAM_GPC_SICONFI."classes/"      );
define( "CAM_GPC_SICONFI_MAPEAMENTO",       CAM_GPC_SICONFI_CLASSES."mapeamento/"  );
define( "CAM_GPC_SICONFI_NEGOCIO",          CAM_GPC_SICONFI_CLASSES."negocio/"     );
define( "CAM_GPC_SICONFI_INSTANCIAS",       CAM_GPC_SICONFI."instancias/"   );
define( "CAM_GPC_SICONFI_POPUPS",           CAM_GPC_SICONFI."popups/"       );
define( "CAM_GPC_SICONFI_RELATORIOS",       CAM_GPC_SICONFI_INSTANCIAS."relatorios/");
define( "SICONFI",                          CAM_GPC_SICONFI_MAPEAMENTO               );
