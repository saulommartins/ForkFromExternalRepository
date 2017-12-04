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
    * Data de Criação   : 28/06/2005

    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30762 $
    $Name$
    $Autor: $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.09
*/

/*
$Log$
Revision 1.10  2007/05/21 18:59:10  melo
Bug #9229#

Revision 1.9  2006/07/19 18:24:43  leandro.zis
Bug #6385#

Revision 1.8  2006/07/14 20:09:49  leandro.zis
Bug #6385#

Revision 1.7  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo1.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "Anexo1";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRegra = new ROrcamentoRelatorioAnexo1;

$obRegra->obROrcamentoDespesa->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRegra->obROrcamentoDespesa->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRegra->obROrcamentoDespesa->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

$rsOrgao = $rsRecordset = new RecordSet;
$obRegra->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );
$arNomFiltro = Sessao::read('filtroNomRelatorio');
while ( !$rsOrgao->eof() ) {
    $arNomFiltro['orgao'][$rsOrgao->getCampo( 'num_orgao' )] = $rsOrgao->getCampo( 'nom_orgao' );
    $rsOrgao->proximo();
}
$rsOrgao->setPrimeiroElemento();

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName          ("stCaminho");
$obHdnCaminho->setValue         ( CAM_GF_ORC_INSTANCIAS."relatorio/OCAnexo1.php" );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName        ('inCodEntidade'            );
$obCmbEntidades->setRotulo      ( "Entidades"               );
$obCmbEntidades->setTitle       ( "Selecione as entidades." );
$obCmbEntidades->setNull        ( false                     );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1  ('inCodEntidadeDisponivel'  );
$obCmbEntidades->setCampoId1    ( 'cod_entidade'            );
$obCmbEntidades->setCampoDesc1  ( 'nom_cgm'                 );
$obCmbEntidades->SetRecord1     ( $rsEntidades              );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2  ('inCodEntidade'            );
$obCmbEntidades->setCampoId2    ('cod_entidade'             );
$obCmbEntidades->setCampoDesc2  ('nom_cgm'                  );
$obCmbEntidades->SetRecord2     ( $rsRecordset              );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValidaExercicio( true              );
$obPeriodicidade->setNull           ( false             );
$obPeriodicidade->setValue          ( 4                 );

// Define Objeto Select para Demonstrar Valores
$obCmbDemValores = new Select;
$obCmbDemValores->setRotulo     ( "Demonstrar Valores"      );
$obCmbDemValores->setTitle      ( "Selecione demonstrar valores."      );
$obCmbDemValores->setName       ( "inCodDemValores"         );
$obCmbDemValores->setId         ( "inCodDemValores"         );
$obCmbDemValores->setValue      ( $inCodDemValores          );
$obCmbDemValores->addOption     ( "1", "Orçamento"          );
$obCmbDemValores->addOption     ( "2", "Balanço"            );
$obCmbDemValores->setStyle      ( "width: 200px"            );
$obCmbDemValores->setNull       ( false                     );
$obCmbDemValores->obEvento->setOnChange( "if (document.frm.inCodDemValores.value=='1') {document.frm.inCodDemDespesa.value='';document.frm.inCodDemDespesa.disabled=true;} else {document.frm.inCodDemDespesa.disabled=false;}");

// Define Objeto Select para Demonstrar Despesa
$obCmbDemDespesa = new Select;
$obCmbDemDespesa->setRotulo     ( "Demonstrar Despesa"      );
$obCmbDemDespesa->setTitle      ( "Selecione demonstrar despesa." );
$obCmbDemDespesa->setName       ( "inCodDemDespesa"         );
$obCmbDemDespesa->setId         ( "inCodDemDespesa"         );
$obCmbDemDespesa->setValue      ( $inCodDemDespesa          );
$obCmbDemDespesa->addOption     ( "",  "Selecione"          );
$obCmbDemDespesa->addOption     ( "1", "Empenhada"          );
$obCmbDemDespesa->addOption     ( "2", "Liquidada"          );
$obCmbDemDespesa->addOption     ( "3", "Paga"               );
$obCmbDemDespesa->setStyle      ( "width: 200px"            );
$obCmbDemDespesa->setNull       ( true                      );
$obCmbDemDespesa->setDisabled   ( true                      );

$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo              ( "Órgão"                           );
$obTxtOrgao->setTitle               ( "Selecione o orgão orçamentário."  );
$obTxtOrgao->setName                ( "inNumOrgaoTxt"                   );
$obTxtOrgao->setValue               ( $inNumOrgaoTxt                    );
$obTxtOrgao->setSize                ( 10                                );
$obTxtOrgao->setMaxLength           ( 10                                );
$obTxtOrgao->setInteiro             ( true                              );
$obTxtOrgao->obEvento->setOnChange  ( "buscaDado('MontaUnidade');"      );

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo              ( "Órgão"                           );
$obCmbOrgao->setName                ( "inNumOrgao"                      );
$obCmbOrgao->setValue               ( $inNumOrgao                       );
$obCmbOrgao->setStyle               ( "width: 200px"                    );
$obCmbOrgao->setCampoID             ( "num_orgao"                       );
$obCmbOrgao->setCampoDesc           ( "nom_orgao"                       );
$obCmbOrgao->addOption              ( "", "Selecione"                   );
$obCmbOrgao->preencheCombo          ( $rsOrgao                          );
$obCmbOrgao->obEvento->setOnChange  ( "buscaDado('MontaUnidade');"      );

$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo            ( "Unidade"                         );
$obTxtUnidade->setTitle             ( "Selecione a unidade orçamentária.");
$obTxtUnidade->setName              ( "inNumUnidadeTxt"                 );
$obTxtUnidade->setValue             ( $inNumUnidadeTxt                  );
$obTxtUnidade->setSize              ( 10                                );
$obTxtUnidade->setMaxLength         ( 10                                );
$obTxtUnidade->setInteiro           ( true                              );

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo            ( "Unidade"                         );
$obCmbUnidade->setName              ( "inNumUnidade"                    );
$obCmbUnidade->setValue             ( $inNumUnidade                     );
$obCmbUnidade->setStyle             ( "width: 200px"                    );
$obCmbUnidade->setCampoID           ( "num_unidade"                     );
$obCmbUnidade->setCampoDesc         ( "descricao"                       );
$obCmbUnidade->addOption            ( "", "Selecione"                   );

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                           );
$obFormulario->setAjuda             ( "UC-02.01.09"                     );
$obFormulario->addHidden            ( $obHdnAcao                        );
$obFormulario->addHidden            ( $obHdnCtrl                        );
$obFormulario->addHidden            ( $obHdnCaminho                     );
$obFormulario->addTitulo            ( "Dados para Filtro"               );
$obFormulario->addComponente        ( $obCmbEntidades                   );
$obFormulario->addComponente        ( $obPeriodicidade                  );
$obFormulario->addComponente        ( $obCmbDemValores                  );
$obFormulario->addComponente        ( $obCmbDemDespesa                  );
$obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao          );
$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade      );

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

include_once ($pgJS);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
