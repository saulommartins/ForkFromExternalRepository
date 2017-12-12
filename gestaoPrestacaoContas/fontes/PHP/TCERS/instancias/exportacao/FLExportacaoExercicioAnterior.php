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
    * Página Formulário - Exportação Arquivos Exercício Anterior
    * Data de Criação   : 04/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: FLExportacaoExercicioAnterior.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.08.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"              );
include_once (CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php"              );

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoExercicioAnterior";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
//destroi arrays de sessão que armazenam os dados do FILTRO
Sessao::remove('link');

$rsArqExport = $rsAtributos = new RecordSet;
$stAcao = $request->get('stAcao');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "hdnPaginaExportacao" );
$obHdnAcao->setValue( "../exportacao/".$pgOcul );

$obSlTipoPeriodo = new Select();
$obSlTipoPeriodo->setRotulo            ('Tipo Arquivo');
$obSlTipoPeriodo->setName              ('slTipoArquivo');
$obSlTipoPeriodo->setId                ('slTipoArquivo');
$obSlTipoPeriodo->addOption            ('','Selecione');
$obSlTipoPeriodo->addOption            ('2','Bimestre');
$obSlTipoPeriodo->addOption            ('3','Trimestre');
$obSlTipoPeriodo->addOption            ('4','Quadrimestre');
$obSlTipoPeriodo->setValue             ('');
$obSlTipoPeriodo->setNull              (false);
$obSlTipoPeriodo->obEvento->setOnChange('preenchePeriodo(this.value);');

$obTxtPeriodoExport = new Select();
$obTxtPeriodoExport->setRotulo              ("Periodo");
$obTxtPeriodoExport->setName                ("inPeriodo");
$obTxtPeriodoExport->setId                  ("inPeriodo");
$obTxtPeriodoExport->obEvento->setOnChange  ('rd_extra();');
$obTxtPeriodoExport->addOption              ('','Selecione');
$obTxtPeriodoExport->setNull                (false);

/* TextBox unidade de Orgao/Unidade
$obTxtOrgUnidade = new TextBox;
$obTxtOrgUnidade->setRotulo          ( "*Orgão / Unidade");
$obTxtOrgUnidade->setName            ( "inOrgaoUnidade"  );
$obTxtOrgUnidade->setId              ( "inOrgaoUnidade"  );
$obTxtOrgUnidade->setSize            ( 4                 );
$obTxtOrgUnidade->setMaxLength       ( 4                 );
$obTxtOrgUnidade->setMinLength       ( 4                 );
$obTxtOrgUnidade->setAlfaNumerico    ( true              );
*/
/* Recordset de entidades */

$obEntidade = new ROrcamentoEntidade;
$obEntidade->obRCGM->setNumCGM ( Sessao::read('numCgm') );
$rsEntidadesDisponiveis  = new RecordSet;
$rsEntidadesSelecionadas = new RecordSet;
$obEntidade->listarUsuariosEntidade($rsEntidadesDisponiveis , " ORDER BY cod_entidade" );
$obEntidade->listarUsuariosEntidadeCnpj($rsEntidadesDisponiveisCnpj , " ORDER BY cod_entidade" );

// select com setores do governo
$obTxtSetorGoverno = new Select();
$obTxtSetorGoverno->setRotulo       ( "Setor do Governo"            );
$obTxtSetorGoverno->setName         ( "stCnpjSetor"                 );
$obTxtSetorGoverno->setId           ( "stCnpjSetor"                 );
$obTxtSetorGoverno->setCampoID      ( "[cod_entidade]|[cnpj]|[nom_cgm]"            );
$obTxtSetorGoverno->setCampoDesc    ( "nom_cgm"                     );
$obTxtSetorGoverno->preencheCombo   ( $rsEntidadesDisponiveisCnpj   );

// Lista ENTIDADES para Selecionar
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName  ( 'arEntidadesSelecionadas' );
$obCmbEntidades->setRotulo( "Entidade" );
$obCmbEntidades->setNull  ( false );
$obCmbEntidades->setTitle ( 'Entidades Disponiveis' );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidadesDisponiveis->getNumLinhas()==1) {
       $rsEntidadesSelecionadas = $rsEntidadesDisponiveis;
       $rsEntidadesDisponiveis = new RecordSet;
}

// Lista de ENTIDADES disponiveis
$obCmbEntidades->SetNomeLista1( 'arEntidadesDisponiveis' );
$obCmbEntidades->setCampoId1  ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1( '[cod_entidade] - [nom_cgm]' );
$obCmbEntidades->SetRecord1   ( $rsEntidadesDisponiveis   );

// lista de ENTIDADES selecionadas
$obCmbEntidades->SetNomeLista2( 'arEntidadesSelecionadas' );
$obCmbEntidades->setCampoId2  ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc2( '[cod_entidade] - [nom_cgm]' );
$obCmbEntidades->SetRecord2   ( $rsEntidadesSelecionadas );

/* Radio para selecionar tipo de exportacao*/
/* Tipo Arquivo Individual */
$obRdbTipoExportArqIndividual = new Radio;
$obRdbTipoExportArqIndividual->setName   ( "stTipoExport"                        );
$obRdbTipoExportArqIndividual->setLabel  ( "Arquivos Individuais"                );
$obRdbTipoExportArqIndividual->setValue  ( "individuais"                         );
$obRdbTipoExportArqIndividual->setRotulo ( "*Tipo de Exportação"   );
$obRdbTipoExportArqIndividual->setTitle  ( "Tipo de Exportação"    );
$obRdbTipoExportArqIndividual->setChecked(true                                   );
/* Tipo Arquivo Compactado */
$obRdbTipoExportArqCompactado = new Radio;
$obRdbTipoExportArqCompactado->setName  ( "stTipoExport"    );
$obRdbTipoExportArqCompactado->setLabel ( "Compactados"     );
$obRdbTipoExportArqCompactado->setValue ( "compactados"     );

/* Lista Arquivos para Selecionar */
/* Elementos no array*/
// Array com o nomes
$arArquivos[0]['nome'] = "BVMOVANT.TXT";
$arArquivos[1]['nome'] = "BREC_ANT.TXT";
$arArquivos[2]['nome'] = "REC_ANT.TXT";
$arArquivos[3]['nome'] = "BRUB_ANT.TXT";
$arArquivos[4]['nome'] = "BVER_ANT.TXT";

$rsArquivosDisponiveis = new RecordSet();
$rsArquivosDisponiveis->preenche($arArquivos);
$rsArquivosSelecionados = new RecordSet();

$obCmbArquivos = new SelectMultiplo();
$obCmbArquivos->setName  ( 'arArquivosSelecionados' );
$obCmbArquivos->setRotulo( "Arquivos" );
$obCmbArquivos->setNull  ( false );
$obCmbArquivos->setTitle ( 'Arquivos Disponiveis' );

// lista de ARQUIVOS disponiveis
$obCmbArquivos->SetNomeLista1( 'arCodArqDisponiveis' );
$obCmbArquivos->setCampoId1  ( 'nome' );
$obCmbArquivos->setCampoDesc1( 'nome' );
$obCmbArquivos->SetRecord1   ( $rsArquivosDisponiveis   );

// lista de ARQUIVOS selecionados
$obCmbArquivos->SetNomeLista2( 'arArquivosSelecionados' );
$obCmbArquivos->setCampoId2  ( 'nome' );
$obCmbArquivos->setCampoDesc2( 'nome' );
$obCmbArquivos->SetRecord2   ( $rsArquivosSelecionados );

/* TextBox host */
$obTxtHost = new TextBox;
$obTxtHost->setRotulo          ( "Host"             );
$obTxtHost->setName            ( "stHost"           );
$obTxtHost->setId              ( "stHost"           );
$obTxtHost->setSize            ( 30                 );
$obTxtHost->setMaxLength       ( 30                 );
$obTxtHost->setAlfaNumerico    ( true               );
$obTxtHost->setNull            ( false              );

/* TextBox porta */
$obTxtPorta = new TextBox;
$obTxtPorta->setRotulo          ( "Porta"            );
$obTxtPorta->setName            ( "stPorta"          );
$obTxtPorta->setId              ( "stPorta"          );
$obTxtPorta->setSize            ( 30                 );
$obTxtPorta->setMaxLength       ( 30                 );
$obTxtPorta->setAlfaNumerico    ( true               );
$obTxtPorta->setNull            ( false              );

/* TextBox banco de dados */
$obTxtBanco = new TextBox;
$obTxtBanco->setRotulo          ( "Banco de Dados"   );
$obTxtBanco->setName            ( "stBanco"          );
$obTxtBanco->setId              ( "stBanco"          );
$obTxtBanco->setSize            ( 30                 );
$obTxtBanco->setMaxLength       ( 30                 );
$obTxtBanco->setAlfaNumerico    ( true               );
$obTxtBanco->setNull            ( false              );

/* TextBox usuário */
$obTxtUsuario = new TextBox;
$obTxtUsuario->setRotulo          ( "Usuario"          );
$obTxtUsuario->setName            ( "stUsuario"        );
$obTxtUsuario->setId              ( "stUsuario"        );
$obTxtUsuario->setSize            ( 30                 );
$obTxtUsuario->setMaxLength       ( 30                 );
$obTxtUsuario->setAlfaNumerico    ( true               );
$obTxtUsuario->setNull            ( false              );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction      ( "../processamento/PRExportador.php"   );
$obForm->setTarget      ( "telaPrincipal"                       ); //oculto - telaPrincipal

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ($obForm );
$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addTitulo            ("Dados para arquivos");
$obFormulario->addComponente        ($obSlTipoPeriodo);
$obFormulario->addComponente        ($obTxtPeriodoExport);
$obFormulario->addComponente        ($obTxtSetorGoverno);
$obFormulario->addComponente        ($obCmbEntidades);
// $obFormulario->addComponente        ( $obTxtOrgUnidade      );
$obFormulario->agrupaComponentes    (array($obRdbTipoExportArqIndividual,$obRdbTipoExportArqCompactado));
$obFormulario->addComponente        ($obCmbArquivos);
$obRConfiguracaoConfiguracao            =   new RConfiguracaoConfiguracao() ;
$obRConfiguracaoConfiguracao->setCodModulo  ( 2                  );
$obRConfiguracaoConfiguracao->setExercicio  ( Sessao::getExercicio() );
$obRConfiguracaoConfiguracao->setParametro  ( "samlink_host"     );
$obRConfiguracaoConfiguracao->consultar( "" );
if ( Sessao::getExercicio() < 2006 and $obRConfiguracaoConfiguracao->getValor() != ""  ) {
    $obTxtHost->setValue($obRConfiguracaoConfiguracao->getValor());
    $obFormulario->addComponente        ( $obTxtHost   );
    $obRConfiguracaoConfiguracao->setParametro  ( "samlink_port"     );
    $obRConfiguracaoConfiguracao->consultar( "" );
    $obTxtPorta->setValue($obRConfiguracaoConfiguracao->getValor());
    $obFormulario->addComponente        ( $obTxtPorta  );
    $obRConfiguracaoConfiguracao->setParametro  ( "samlink_dbname"     );
    $obRConfiguracaoConfiguracao->consultar( "" );
    $obTxtBanco->setValue($obRConfiguracaoConfiguracao->getValor());
    $obFormulario->addComponente        ( $obTxtBanco  );
    $obRConfiguracaoConfiguracao->setParametro  ( "samlink_user"     );
    $obRConfiguracaoConfiguracao->consultar( "" );
    $obTxtUsuario->setValue($obRConfiguracaoConfiguracao->getValor());
    $obFormulario->addComponente        ( $obTxtUsuario);
} else {
}
$obFormulario->setFormFocus         ($obTxtPeriodoExport->getId());

$obFormulario->OK                   ();
$obFormulario->show                 ();

//SistemaLegado::executaFramePrincipal("d.frm.stCnpjSetor.selected[0]=true");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
