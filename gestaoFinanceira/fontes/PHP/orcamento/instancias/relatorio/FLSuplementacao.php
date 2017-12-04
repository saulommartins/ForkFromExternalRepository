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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 23/05/2005

    * @author Analista      : Dieine Silva
    * @author Desenvolvedor : Cleisson Barboza

    * @ignore

    $Revision: 30762 $
    $Name$
    $Author: melo $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.25
*/

/*
$Log$
Revision 1.8  2007/05/21 18:58:54  melo
Bug #9229#

Revision 1.7  2006/07/17 19:15:38  andre.almeida
Bug #6402#

Revision 1.6  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "Suplementacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
$arNomFiltro = Sessao::read('filtroNomRelatorio');
while ( !$rsEntidades->eof() ) {
    $arNomFiltro['entidade'][$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
    $rsEntidades->proximo();
}
$rsEntidades->setPrimeiroElemento();
$rsRecordset = new RecordSet;

$obROrcamentoSuplementacao = new ROrcamentoSuplementacao;
$obROrcamentoSuplementacao->setExercicio(Sessao::getExercicio());
$obROrcamentoSuplementacao->listarTipo( $rsTipoSuplementacao );
while ( !$rsTipoSuplementacao->eof() ) {
    $arNomFiltro['tipo'][$rsTipoSuplementacao->getCampo( 'cod_tipo' )] = $rsTipoSuplementacao->getCampo( 'nom_tipo' );
    $rsTipoSuplementacao->proximo();
}
$rsTipoSuplementacao->setPrimeiroElemento();

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_ORC_INSTANCIAS."relatorio/OCRelatorioSuplementacao.php" );

$obHdnCtrl   = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue( " " );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades para o filtro." );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidades );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

// Define Objeto BuscaInner para Norma
$obBscNorma = new BuscaInner;
$obBscNorma->setRotulo ( "Lei/Decreto"   );
$obBscNorma->setTitle  ( "Selecione uma lei ou decreto" );
$obBscNorma->setNulL   ( true                    );
$obBscNorma->setId     ( "stNomTipoNorma"         );
$obBscNorma->setValue  ( $stNomTipoNorma          );
$obBscNorma->obCampoCod->setName     ( "inCodNorma" );
$obBscNorma->obCampoCod->setId       ( "inCodNorma" );
$obBscNorma->obCampoCod->setSize     ( 10           );
$obBscNorma->obCampoCod->setMaxLength( 7            );
$obBscNorma->obCampoCod->setValue    ( $inCodNorma  );
$obBscNorma->obCampoCod->setAlign    ( "left"       );
$obBscNorma->setFuncaoBusca("abrePopUp('".CAM_GA_NORMAS_POPUPS."normas/FLNorma.php','frm','inCodNorma','stNomTipoNorma','','".Sessao::getId()."','800','550');");
$obBscNorma->setValoresBusca ( CAM_GA_NORMAS_POPUPS.'normas/OCManterNorma.php?'.Sessao::getId(), $obForm->getName() );

//Define Objetos para o Período
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio        ( Sessao::getExercicio()  );
$obPeriodo->setValidaExercicio  ( true                );

// Define Objeto BuscaInner para Despesa
$obBscDespesa = new BuscaInner;
$obBscDespesa->setRotulo ( "Dotação"   );
$obBscDespesa->setTitle  ( "Informe a dotação para o filtro." );
$obBscDespesa->setNulL   ( true                     );
$obBscDespesa->setId     ( "stNomDespesa"           );
$obBscDespesa->setValue  ( $stNomDespesa            );
$obBscDespesa->obCampoCod->setName ( "inCodDespesa" );
$obBscDespesa->obCampoCod->setSize ( 10 );
$obBscDespesa->obCampoCod->setMaxLength( 5 );
$obBscDespesa->obCampoCod->setValue ( $inCodDespesa );
$obBscDespesa->obCampoCod->setAlign ("left");
//$obBscDespesa->obCampoCod->obEvento->setOnBlur("buscaDado('buscaDespesa');");
$obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDespesa','stNomDespesa','','".Sessao::getId()."','800','550');");
$obBscDespesa->setValoresBusca ( CAM_GF_ORC_POPUPS.'despesa/OCDespesa.php?'.Sessao::getId(), $obForm->getName(),'');

// Define Objeto TextBox para Codigo do tipo de suplementação
$obTxtTipoSuplementacao = new TextBox;
$obTxtTipoSuplementacao->setRotulo  ( "Tipo de Suplementação"           );
$obTxtTipoSuplementacao->setTitle   ( "Informe o Tipo de Suplementação" );
$obTxtTipoSuplementacao->setName    ( "inCodTipoSuplementacao"          );
$obTxtTipoSuplementacao->setValue   ( $inCodTipoSuplementacao           );
$obTxtTipoSuplementacao->setNull    ( true                              );
$obTxtTipoSuplementacao->setInteiro ( true                              );

// Define Objeto Select para Tipo de Suplementação
$obCmbTipoSuplementacao = new Select;
$obCmbTipoSuplementacao->setRotulo    ( "Tipo de Suplementação"             );
$obCmbTipoSuplementacao->setName      ( "stTipoSuplementacao"               );
$obCmbTipoSuplementacao->setId        ( "stTipoSuplementacao"               );
$obCmbTipoSuplementacao->setTitle     ( "Informe o Tipo de Suplementação"   );
$obCmbTipoSuplementacao->setValue     ( $inCodTipoSuplementacao             );
$obCmbTipoSuplementacao->addOption    ( "", "Selecione"                     );
$obCmbTipoSuplementacao->setCampoId   ( "cod_tipo"                          );
$obCmbTipoSuplementacao->setCampoDesc ( "nom_tipo"                          );
$obCmbTipoSuplementacao->preencheCombo( $rsTipoSuplementacao                );
$obCmbTipoSuplementacao->setNull      ( true                                );

//Combo de Tipo de Relatório
$obCmbTipoRelatorio= new Select;
$obCmbTipoRelatorio->setRotulo  ( "Tipo de Relatório"                           );
$obCmbTipoRelatorio->setTitle   ( "Selecione o tipo de relatório para o filtro." );
$obCmbTipoRelatorio->setName    ( "stTipoRelatorio"                             );
$obCmbTipoRelatorio->setValue   ( 3                                             );
$obCmbTipoRelatorio->setStyle   ( "width: 200px"                                );
$obCmbTipoRelatorio->addOption  ( "entidade", "Entidade"                        );
$obCmbTipoRelatorio->addOption  ( "lei_decreto", "Lei/Decreto"                  );
$obCmbTipoRelatorio->addOption  ( "data", "Data"                                );
$obCmbTipoRelatorio->addOption  ( "dotacao", "Dotação"                          );
$obCmbTipoRelatorio->addOption  ( "anuladas", "Suplementações Anuladas"         );
$obCmbTipoRelatorio->addOption  ( "resumo", "Resumo das Suplementações"         );
$obCmbTipoRelatorio->setNull    ( false                                         );

//Combo de Situação
$obCmbSituacao= new Select;
$obCmbSituacao->setRotulo   ( "Situação"        );
$obCmbSituacao->setTitle    ( "Selecione a situação para o filtro."        );
$obCmbSituacao->setName     ( "stSituacao" );
$obCmbSituacao->setValue    ( 3                 );
$obCmbSituacao->setStyle    ( "width: 200px"    );
$obCmbSituacao->addOption   ( "", "Todas"      );
$obCmbSituacao->addOption   ( "Válida", "Válidas"    );
$obCmbSituacao->addOption   ( "Anulada", "Anuladas"   );
$obCmbSituacao->setNull     ( true             );

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->setAjuda         ( "UC-02.01.25"             );
$obFormulario->addForm          ( $obForm                   );
$obFormulario->addHidden        ( $obHdnCaminho             );
$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addTitulo        ( "Dados para Filtro"       );
$obFormulario->addComponente    ( $obCmbEntidades           );
$obFormulario->addComponente    ( $obPeriodo                );
$obFormulario->addComponente    ( $obBscNorma               );
$obFormulario->addComponente    ( $obBscDespesa             );
$obFormulario->addComponenteComposto( $obTxtTipoSuplementacao, $obCmbTipoSuplementacao );
$obFormulario->addComponente    ( $obCmbTipoRelatorio       );
$obFormulario->addComponente    ( $obCmbSituacao            );

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

?>
