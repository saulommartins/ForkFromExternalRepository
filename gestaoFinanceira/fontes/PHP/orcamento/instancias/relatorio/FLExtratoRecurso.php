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
    * Data de Criação   : 04/07/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.29
*/

/*
$Log$
Revision 1.6  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioExtratoRecurso.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ExtratoRecurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once( $pgJS );

$obRegra                  = new ROrcamentoRelatorioExtratoRecurso;
$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->consultarConfiguracao();

$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

$rsOrgao = $rsRecordset = new RecordSet;

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
$obHdnCaminho->setValue         ( CAM_GF_ORC_INSTANCIAS."relatorio/OCExtratoRecurso.php" );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName        ('inCodEntidade'            );
$obCmbEntidades->setRotulo      ( "Entidades"               );
$obCmbEntidades->setTitle       ( ""                        );
$obCmbEntidades->setNull        ( false                     );

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

// Define Objeto Select para Demonstrar Valores
$obTxtExercicio = new Exercicio;
$obTxtExercicio->setRotulo     ( "Exercício"        );
$obTxtExercicio->setName       ( "stExercicio"      );
$obTxtExercicio->setId         ( "stExercicio"      );
$obTxtExercicio->setValue      ( Sessao::getExercicio() );
$obTxtExercicio->setNull       ( false              );

// Define Objeto TextBox para Data Final
$obTxtDtFinal = new Data();
$obTxtDtFinal->setRotulo( "Situação até" );
$obTxtDtFinal->setName  ( "stDataFinal"  );
$obTxtDtFinal->setId    ( "stDataFinal"  );
$obTxtDtFinal->setNull  ( false          );

// Define objeto BuscaInner para recurso
$obBscRecurso = new BuscaInner;
$obBscRecurso->setRotulo               ( "Recurso"            );
$obBscRecurso->setTitle                ( "Informe o recurso"  );
$obBscRecurso->setNulL                 ( true                 );
$obBscRecurso->setId                   ( "stDescricaoRecurso" );
$obBscRecurso->obCampoCod->setName     ( "inCodRecurso"       );
$obBscRecurso->obCampoCod->setSize     ( 10                   );
$obBscRecurso->obCampoCod->setMaxLength( strlen($obROrcamentoConfiguracao->getMascRecurso()) );
$obBscRecurso->obCampoCod->setPreencheComZeros( 'E' );
$obBscRecurso->obCampoCod->setValue    ( $inCodRecurso );
$obBscRecurso->obCampoCod->setAlign    ("left");
$obBscRecurso->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$obROrcamentoConfiguracao->getMascRecurso()."', this, event);");
$obBscRecurso->obCampoCod->obEvento->setOnChange("buscaValor('buscaRecurso','".$pgOcul."','".CAM_FW_POPUPS."relatorio/OCRelatorio.php','oculto','".Sessao::getId()."');");
$obBscRecurso->setFuncaoBusca          ( "abrePopUp('".CAM_GF_ORC_POPUPS."recurso/FLRecurso.php','frm','inCodRecurso','stDescricaoRecurso','','".Sessao::getId()."','800','550')" );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                           );
$obFormulario->addHidden            ( $obHdnAcao                        );
$obFormulario->addHidden            ( $obHdnCtrl                        );
$obFormulario->addHidden            ( $obHdnCaminho                     );
$obFormulario->addTitulo            ( "Dados para filtro"               );
$obFormulario->addComponente        ( $obCmbEntidades                   );
$obFormulario->addComponente        ( $obTxtExercicio                   );
$obFormulario->addComponente        ( $obTxtDtFinal                     );
$obFormulario->addComponente        ( $obBscRecurso                     );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
