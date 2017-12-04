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
* Página de Filtro de Exportação Principal
* Data de Criação: 11/04/2005

* @author Analista: Cassiano Vasconcellos Ferreira
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Mapeamento

$Revision: 59612 $
$Name$
$Author: gelson $
$Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

* Casos de uso: uc-02.08.08
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:12  hboaventura
Ticket#10234#

Revision 1.6  2006/07/05 20:46:20  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"          );

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoArquivosPrincipais";
//Filtro
$pgFilt = "FL".$stPrograma.".php";
//Lista
$pgList = "LS".$stPrograma.".php";
//Formulario
$pgForm = "FM".$stPrograma.".php";
//Processamento
$pgProc = "PR".$stPrograma.".php";
//Frame oculto
$pgOcul = "OC".$stPrograma.".php";
//Javascript
$pgJS   = "JS".$stPrograma.".js";
//include_once( $pgJS );

//Define a fun??o do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
Sessao::remove('link');

global $session;

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( "../processamento/PRExportador.php" );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Define o objeto de pagina exportação
$obHdnPaginaExportacao = new Hidden;
$obHdnPaginaExportacao->setName ("hdnPaginaExportacao");
$obHdnPaginaExportacao->setValue('../exportacao/'.$pgOcul);

//Define o objeto de dados da prefeitura
$arPrefeitura = Sessao::read('prefeitura');
$obHdnPrefeitura = new Hidden;
$obHdnPrefeitura->setName ("stCnpjSetor");
$obHdnPrefeitura->setValue($arPrefeitura["cnpj"].'-'.$arPrefeitura["prefeitura"]);

//Define o objeto de unidade gestora
$obTxtUnidadeGestora = new TextBox;
$obTxtUnidadeGestora->setRotulo          ( "Unidade Gestora" );
$obTxtUnidadeGestora->setName            ( "inOrgaoUnidade"  );
$obTxtUnidadeGestora->setId              ( "inOrgaoUnidade"  );
$obTxtUnidadeGestora->setSize            ( 4                 );
$obTxtUnidadeGestora->setMaxLength       ( 4                 );
$obTxtUnidadeGestora->setInteiro         ( true              );
$obTxtUnidadeGestora->setNull            ( false             );

$obCmbPeriodoExport = new Select();
$obCmbPeriodoExport->setRotulo          ( "Período"             )   ;
$obCmbPeriodoExport->setName            ( "stPeriodo"           )   ;
$obCmbPeriodoExport->setId              ( "stPeriodo"           )   ;
$obCmbPeriodoExport->setNull            ( false                 )   ;
$arPeriodo[0]['nome']     = "Janeiro";
$arPeriodo[0]['valor']    = "01/01/".Sessao::getExercicio()."-31/01/".Sessao::getExercicio()."";
$arPeriodo[1]['nome']     = "Fevereiro";
$arPeriodo[1]['valor']    = "01/02/".Sessao::getExercicio()."-28/02/".Sessao::getExercicio()."";
$arPeriodo[2]['nome']     = "Março";
$arPeriodo[2]['valor']    = "01/03/".Sessao::getExercicio()."-31/03/".Sessao::getExercicio()."";
$arPeriodo[3]['nome']     = "Abril";
$arPeriodo[3]['valor']    = "01/04/".Sessao::getExercicio()."-30/04/".Sessao::getExercicio()."";
$arPeriodo[4]['nome']     = "Maio";
$arPeriodo[4]['valor']    = "01/05/".Sessao::getExercicio()."-31/05/".Sessao::getExercicio()."";
$arPeriodo[5]['nome']     = "Junho";
$arPeriodo[5]['valor']    = "01/06/".Sessao::getExercicio()."-30/06/".Sessao::getExercicio()."";
$arPeriodo[6]['nome']     = "Julho";
$arPeriodo[6]['valor']    = "01/07/".Sessao::getExercicio()."-31/07/".Sessao::getExercicio()."";
$arPeriodo[7]['nome']     = "Agosto";
$arPeriodo[7]['valor']    = "01/08/".Sessao::getExercicio()."-31/08/".Sessao::getExercicio()."";
$arPeriodo[8]['nome']     = "Setembro";
$arPeriodo[8]['valor']    = "01/09/".Sessao::getExercicio()."-30/09/".Sessao::getExercicio()."";
$arPeriodo[9]['nome']     = "Outubro";
$arPeriodo[9]['valor']    = "01/10/".Sessao::getExercicio()."-31/10/".Sessao::getExercicio()."";
$arPeriodo[10]['nome']    = "Novembro";
$arPeriodo[10]['valor']   = "01/11/".Sessao::getExercicio()."-30/11/".Sessao::getExercicio()."";
$arPeriodo[11]['nome']    = "Dezembro";
$arPeriodo[11]['valor']   = "01/12/".Sessao::getExercicio()."-31/12/".Sessao::getExercicio()."";
for ($inContandorOpt=0;$inContandorOpt <= 11;$inContandorOpt++) {
        $obCmbPeriodoExport->addOption($arPeriodo[$inContandorOpt]['valor'],$arPeriodo[$inContandorOpt]['nome']);
    }

/* Recordset de entidades */
$obEntidade = new ROrcamentoEntidade;
$obEntidade->obRCGM->setNumCGM ( Sessao::read('numCgm') );
$rsEntidadesDisponiveis  = new RecordSet;
$rsEntidadesSelecionadas = new RecordSet;
$obEntidade->listarUsuariosEntidade($rsEntidadesDisponiveis , " ORDER BY cod_entidade" );

// Lista ENTIDADES para Selecionar
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName        ( 'arEntidadesSelecionadas'     );
$obCmbEntidades->setRotulo      ( "Entidade"                    );
$obCmbEntidades->setNull        ( false                         );
$obCmbEntidades->setTitle       ( 'Entidades Disponiveis'       );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidadesDisponiveis->getNumLinhas()==1) {
       $rsEntidadesSelecionadas = $rsEntidadesDisponiveis;
       $rsEntidadesDisponiveis = new RecordSet;
}

// Lista de ENTIDADES disponiveis
$obCmbEntidades->SetNomeLista1  ( 'arEntidadesDisponiveis'      );
$obCmbEntidades->setCampoId1    ( 'cod_entidade'                );
$obCmbEntidades->setCampoDesc1  ( '[cod_entidade] - [nom_cgm]'  );
$obCmbEntidades->SetRecord1     ( $rsEntidadesDisponiveis       );

// lista de ENTIDADES selecionadas
$obCmbEntidades->SetNomeLista2  ( 'arEntidadesSelecionadas'     );
$obCmbEntidades->setCampoId2    ( 'cod_entidade'                );
$obCmbEntidades->setCampoDesc2  ( '[cod_entidade] - [nom_cgm]'  );
$obCmbEntidades->SetRecord2     ( $rsEntidadesSelecionadas      );

// Define o objeto RADIO para selecionar o tipo de exportação
$obRdbTipoExportacao = new Radio();
$obRdbTipoExportacao->setRotulo ( "Tipo de Exportação"          );
$obRdbTipoExportacao->setName   ( "boTipoExportacao"            );
$obRdbTipoExportacao->setValue  ( 1                             );
$obRdbTipoExportacao->setLabel  ( "Arquivos Individuais"        );
$obRdbTipoExportacao->setChecked( ($boTipoExportacao == 1 OR !$boTipoExportacao));
$obRdbTipoExportacao->setNull   ( false                         );

$obRdbTipoExportacao2 = new Radio();
$obRdbTipoExportacao2->setRotulo( "Tipo de Exportação"          );
$obRdbTipoExportacao2->setName  ( "boTipoExportacao"            );
$obRdbTipoExportacao2->setValue ( 2                             );
$obRdbTipoExportacao2->setLabel ( "Arquivo Compactado"          );
$obRdbTipoExportacao2->setChecked(($boTipoExportacao == 2)      );
$obRdbTipoExportacao2->setNull  ( false                         );

//Define objeto SELECT para selecionar os arquivos
$obCmbArquivos = new SelectMultiplo();
$obCmbArquivos->setname         ( "inCodArquivoSelecionados"    );
$obCmbArquivos->setRotulo       ( "Arquivos"                    );
$obCmbArquivos->setNull         ( false                         );

//lista de arquivos
$arArquivos[0]['nome'] = "ALTORC.TXT";
$arArquivos[1]['nome'] = "CONTACONT.TXT";
$arArquivos[2]['nome'] = "DOTACAO.TXT";
$arArquivos[3]['nome'] = "EMPENHO.TXT";
$arArquivos[4]['nome'] = "ESPDESP.TXT";
$arArquivos[5]['nome'] = "ESPREC.TXT";
$arArquivos[6]['nome'] = "ESTOREMP.TXT";
$arArquivos[7]['nome'] = "FONTE.TXT";
$arArquivos[8]['nome'] = "LIQEMP.TXT";
$arArquivos[9]['nome'] = "MOVCONTA.TXT";
$arArquivos[10]['nome'] = "PAGEMP.TXT";
$arArquivos[11]['nome'] = "PREVREC.TXT";
$arArquivos[12]['nome'] = "RECLANC.TXT";
$arArquivos[13]['nome'] = "APREVREC.TXT";
$arArquivos[14]['nome'] = "PROJATV.TXT";
$arArquivos[15]['nome'] = "ORGAO.TXT";
$arArquivos[16]['nome'] = "UNIDORCA.TXT";
$arArquivos[17]['nome'] = "PROGRAMA.TXT";
$rsArquivosDisponiveis = new RecordSet();
$rsArquivosDisponiveis->preenche($arArquivos);
$rsArquivosSelecionados = new RecordSet();

//lista de arquivos dispon?veis
$obCmbArquivos->setNomeLista1   ( "arArquivosDisponiveis"   );
$obCmbArquivos->setCampoId1     ( "nome"                    );
$obCmbArquivos->setCampoDesc1   ( "nome"                    );
$obCmbArquivos->setRecord1      ( $rsArquivosDisponiveis    );

//lista de arquivos selecionados
$obCmbArquivos->setNomeLista2   ( "arArquivosSelecionados"  );
$obCmbArquivos->setCampoId2     ( "nome"                    );
$obCmbArquivos->setCampoDesc2   ( "nome"                    );
$obCmbArquivos->setRecord2      ( $rsArquivosSelecionados   );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                   );
$obFormulario->addHidden            ( $obHdnAcao                );
$obFormulario->addHidden            ( $obHdnCtrl                );
$obFormulario->addHidden            ( $obHdnPaginaExportacao    );
$obFormulario->addHidden            ( $obHdnPrefeitura          );
$obFormulario->addTitulo            ( "Dados para arquivos"     );
$obFormulario->addComponente        ( $obTxtUnidadeGestora      );
$obFormulario->addComponente        ( $obCmbPeriodoExport       );
$obFormulario->addComponente        ( $obCmbEntidades           );
$obFormulario->addComponenteComposto( $obRdbTipoExportacao,$obRdbTipoExportacao2  );
$obFormulario->addComponente        ( $obCmbArquivos            );
$obFormulario->setFormFocus         ( $obTxtUnidadeGestora->getId());
$obFormulario->Ok                   (                           );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
