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
* Página de Formulario de Evento - Aba Salario
* Data de Criação   : 29/08/2005

* @author Analista: Leandro Oliveira
* @author Programador: Eduardo Antunez

* @ignore

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-07-13 12:21:51 -0300 (Sex, 13 Jul 2007) $

Caso de uso: uc-04.05.06
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$obBscRubricaDespesaSal = new BuscaInner;
$obBscRubricaDespesaSal->setRotulo                         ( "Rubrica de despesa"                                      );
$obBscRubricaDespesaSal->setTitle                          ( "Informe uma rubrica de despesa para o evento de salário" );
$obBscRubricaDespesaSal->setId                             ( "stRubricaDespesaSal"                                     );
$obBscRubricaDespesaSal->obCampoCod->setName               ( "stMascClassificacaoSal"                                  );
$obBscRubricaDespesaSal->obCampoCod->setSize               ( 18                                                        );
$obBscRubricaDespesaSal->obCampoCod->setMaxLength          ( 22                                                        );
$obBscRubricaDespesaSal->obCampoCod->setValue              ( $stRubricaDespesaSal                                      );
$obBscRubricaDespesaSal->obCampoCod->setAlign              ( "LEFT"                                                    );
$obBscRubricaDespesaSal->obCampoCod->setPreencheComZeros   ( "D" );
$obBscRubricaDespesaSal->obCampoCod->obEvento->setOnChange ( "buscaValor('preencheMascClassificacaoSal','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."');" );
$obBscRubricaDespesaSal->setFuncaoBusca                    ( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','stMascClassificacaoSal','stRubricaDespesaSal','&mascClassificacao=$stMascaraElementoDespesa','".Sessao::getId()."','800','550');" );

$obTxtDescricaoSal = new TextBox;
$obTxtDescricaoSal->setRotulo              ( "*Descrição"                                                                   );
$obTxtDescricaoSal->setTitle               ( "Informe a descrição auxiliar para a particularidade do evento de salário"    );
$obTxtDescricaoSal->setName                ( "stDescricaoSal"                                                              );
$obTxtDescricaoSal->setValue               ( $stDescricaoSal                                                               );
$obTxtDescricaoSal->setSize                ( 40                                                                            );
$obTxtDescricaoSal->setMaxLength           ( 80                                                                            );
$obTxtDescricaoSal->setCaracteresAceitos   ( "[0-9a-zA-Z áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ%--/*+%]"                              );
$obTxtDescricaoSal->setEspacosExtras       ( false                                                                         );

$obCmbCargoSal = new SelectMultiplo();
$obCmbCargoSal->setName                    ( 'inCodCargoSal'                                                            );
$obCmbCargoSal->setRotulo                  ( "*Cargos vinculados"                                                        );
$obCmbCargoSal->SetNomeLista1              ( 'inCodCargoDisponiveisSal'                                                 );
$obCmbCargoSal->setCampoId1                ( '[cod_cargo]'                                                              );
$obCmbCargoSal->setCampoDesc1              ( '[descr_cargo]/[descr_espec]'                                              );
$obCmbCargoSal->setStyle1                  ( "width: 300px"                                                             );
$obCmbCargoSal->SetRecord1                 ( $rsCargoDisponiveisSal                                                     );
$obCmbCargoSal->SetNomeLista2              ( 'inCodCargoSelecionadosSal'                                                );
$obCmbCargoSal->setCampoId2                ( '[cod_cargo]/[descr_cargo]/[cod_especialidade]/[descr_espec]'              );
$obCmbCargoSal->setCampoDesc2              ( '[descr_cargo]/[descr_espec]'                                              );
$obCmbCargoSal->setStyle2                  ( "width: 300px"                                                             );
$obCmbCargoSal->SetRecord2                 ( $rsCargoSelecionadosSal                                                    );

$obBscFuncaoSal = new BuscaInner;
$obBscFuncaoSal->setRotulo ( "*Função"                                                               );
$obBscFuncaoSal->setTitle  ( "Selecione uma função para esta particularidade de evento de salário"  );
$obBscFuncaoSal->setId     ( "stFuncaoSal"                                                          );
$obBscFuncaoSal->obCampoCod->setName   ( "inCodFuncaoSal" );
$obBscFuncaoSal->obCampoCod->setValue  ( $inCodFuncaoSal  );
$obBscFuncaoSal->obCampoCod->obEvento->setOnChange("buscaValor('buscaFuncaoSal');");
$obBscFuncaoSal->obCampoCod->obEvento->setOnBlur  ("buscaValor('buscaFuncaoSal');");
$obBscFuncaoSal->obCampoCod->setMascara("99.99.999");
$obBscFuncaoSal->setFuncaoBusca( "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php','frm','inCodFuncaoSal','stFuncaoSal','','".Sessao::getId()."','800','550');" );

$obBtnIncluirSal = new Button;
$obBtnIncluirSal->setName                  ( "btIncluirSal"                                                             );
$obBtnIncluirSal->setValue                 ( "Incluir"                                                                  );
$obBtnIncluirSal->obEvento->setOnClick     ( "selecionaSubDivisao('Sal',true);
                                              selecionaCargo('Sal',true);
                                              buscaValor('incluiCasoSal');
                                              selecionaSubDivisao('Sal',false);
                                              selecionaCargo('Sal',false);"                                             );

$obBtnAlterarSal = new Button;
$obBtnAlterarSal->setName                  ( "btAlterarSal"                                                             );
$obBtnAlterarSal->setValue                 ( "Alterar"                                                                  );
$obBtnAlterarSal->obEvento->setOnClick     ( "selecionaSubDivisao('Sal',true);
                                              selecionaCargo('Sal',true);
                                              buscaValor('alteraCasoSal');
                                              selecionaSubDivisao('Sal',false);
                                              selecionaCargo('Sal',false);"                                             );

$obBtnLimparSal = new Button;
$obBtnLimparSal->setName                   ( "btLimparSal"                                                              );
$obBtnLimparSal->setValue                  ( "Limpar"                                                                   );
$obBtnLimparSal->obEvento->setOnClick      ( "buscaValor('limpaCamposCasoSal')"                                         );

$obSpnListaSal = new Span;
$obSpnListaSal->setId ( "spnListaSal" );

$obHdnSal = new Hidden;
$obHdnSal->setName("hdnSal");

$obSpnEventoBaseSal = new Span;
$obSpnEventoBaseSal->setId ( "spnEventoBaseSal" );

$obSpnSpan1 = new Span;
$obSpnSpan1->setId ( "spnSpan1" );

?>
