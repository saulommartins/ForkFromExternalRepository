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
  * Página de
  * Data de criação : 24/10/2005

  * @author Analista:
  * @author Programador: Fernando Zank Correa Evangelista

  $Id: FLFichaPatrimonial.php 66009 2016-07-07 13:32:43Z lisiane $

  Caso de uso: uc-03.01.11

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/mascarasLegado.lib.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

include_once CAM_GP_PAT_COMPONENTES.'IMontaClassificacao.class.php';
include_once CAM_GP_PAT_NEGOCIO.'RPatrimonioNatureza.class.php';
include_once CAM_GP_PAT_NEGOCIO.'RPatrimonioBem.class.php';

include_once CAM_GA_ADM_NEGOCIO.'ROrgao.class.php';
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";


$obOrgao = new ROrgao;
$obOrgao->listarTodos($rsOrgao,$stOrder,$boTransacao);
$inCount = 0;
while (!$rsOrgao->eof()) {
    $arOrgao[$inCount]['nom_orgao'] = $rsOrgao->getCampo('nom_orgao')." - ".$rsOrgao->getCampo('ano_exercicio');
    $arOrgao[$inCount]['cod_orgao'] = $rsOrgao->getCampo('cod_orgao');
    $arOrgao[$inCount]['ano_exercicio'] = $rsOrgao->getCampo('ano_exercicio');
    $inCount++;
    $rsOrgao->proximo();
}
$rsOrgao = new RecordSet;
$rsOrgao->preenche($arOrgao);

//Define o nome dos arquivos PHP
$stPrograma = "FichaPatrimonial";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgGera = "OCGera".$stPrograma.".php";

include_once($pgJS);

$naturezaPatrimonio = new RPatrimonioNatureza;
$naturezaPatrimonio->listar($natureza);
$natureza->ordena('nom_natureza',ASC,SORT_STRING);

$bemPatrimonio = new RPatrimonioBem;
$bemPatrimonio->listarMax($maxBem);

$codMax = $maxBem->getCampo("max");

$obForm = new Form;
$obForm->setAction($pgGera);
#$obForm->setTarget('oculto');

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GP_PAT_INSTANCIAS."relatorio/OCFichaPatrimonial.php" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue( " " );

//instancia o componente IMontaClassificacao
$obIMontaClassificacao = new IMontaClassificacao( $obForm );
$obIMontaClassificacao->setNull(true);

$obISelectEntidade = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidade->obTextBox->setNull(false);
$obISelectEntidade->setNull(false);

// Define Objeto TextBox para intervalo código bem inicial
$obTxtBemInicial = new TextBox;
$obTxtBemInicial->setRotulo  ( "Intervalo entre Códigos de Bens");
$obTxtBemInicial->setTitle   ( "Informe o intervalo de código do bem." );
$obTxtBemInicial->setName    ( "inCodBemInicial"                 );
$obTxtBemInicial->setValue   ( 1                                 );
$obTxtBemInicial->setNull    ( false                              );
$obTxtBemInicial->setInteiro ( true                              );

//define label do intervalo
$obLblIntervalo = new Label;
$obLblIntervalo->setValue ("até");

//instancia o componenete IMontaOrganograma
$obIMontaOrganograma = new IMontaOrganograma(true);
$obIMontaOrganograma->setCodOrgao($codOrgao);
$obIMontaOrganograma->setStyle('width:250px');

$obIMontaOrganogramaLocal = new IMontaOrganogramaLocal;
$obIMontaOrganogramaLocal->setValue($codLocal);

// Define Objeto TextBox para intervalo código bem final
$obTxtBemFinal = new TextBox;
$obTxtBemFinal->setRotulo ( "Intervalo entre Códigos de Bens"           );
$obTxtBemFinal->setTitle  ( "Informe o intervalo de código do bem." );
$obTxtBemFinal->setName   ( "inCodBemFinal"                      );
$obTxtBemFinal->setValue  ( "$codMax"                              );
$obTxtBemFinal->setNull   ( false                                  );
$obTxtBemFinal->setInteiro( true                                   );

$obIPopCGM = new IPopUpCGM($obForm);
$obIPopCGM->setRotulo("Fornecedor");
$obIPopCGM->setTitle ("Informe o fornecedor.");
$obIPopCGM->setTipo("juridica");
$obIPopCGM->setNull(true);

//Radios de Tipo de Relatório
$obRdbTipoResumido = new Radio;
$obRdbTipoResumido->setRotulo ( "Tipo de Relatório" );
$obRdbTipoResumido->setTitle  ( "Selecione o tipo de relatório." );
$obRdbTipoResumido->setName   ( "stTipoRelatorio" );
$obRdbTipoResumido->setChecked( true );
$obRdbTipoResumido->setValue  ( "resumido" );
$obRdbTipoResumido->setLabel  ( "Resumido" );

$obRdbTipoCompleto = new Radio;
$obRdbTipoCompleto->setName   ( "stTipoRelatorio" );
$obRdbTipoCompleto->setValue  ( "completo" );
$obRdbTipoCompleto->setLabel  ( "Completo" );

$obDtDataAquisicao = new Periodicidade();
$obDtDataAquisicao->setExercicio( Sessao::getExercicio() );
$obDtDataAquisicao->setRotulo ('Data de Aquisição');

$obPeriodicidadeIncorporacao = new Periodicidade();
$obPeriodicidadeIncorporacao->setIdComponente( 'Incorporacao' );
$obPeriodicidadeIncorporacao->setRotulo( 'Data de Incorporação');
$obPeriodicidadeIncorporacao->setTitle( 'Selecione o Período de Incorporação.' );
$obPeriodicidadeIncorporacao->setNull( true );
$obPeriodicidadeIncorporacao->setExercicio ( Sessao::getExercicio() );

//Filtro demonstrar depreciações
$obDepreciacoesSim = new Radio;
$obDepreciacoesSim->setRotulo ( "Demonstrar Depreciações" );
$obDepreciacoesSim->setTitle  ( "Selecione se é para demonstrar as depreciações." );
$obDepreciacoesSim->setName   ( "stDepreciacoes" );
$obDepreciacoesSim->setChecked( false );
$obDepreciacoesSim->setValue  ( "S" );
$obDepreciacoesSim->setLabel  ( "Sim" );

$obDepreciacoesNao = new Radio;
$obDepreciacoesNao->setName   ( "stDepreciacoes" );
$obDepreciacoesNao->setChecked( true );
$obDepreciacoesNao->setValue  ( "N" );
$obDepreciacoesNao->setLabel  ( "Não" );
//Radios de histórico
$obRdbComHistorico = new Radio;
$obRdbComHistorico->setRotulo ( "Imprimir Histórico" );
$obRdbComHistorico->setTitle  ( "Selecione a impressão histórico." );
$obRdbComHistorico->setName   ( "stHistorico" );
$obRdbComHistorico->setChecked( true );
$obRdbComHistorico->setValue  ( "não" );
$obRdbComHistorico->setLabel  ( "Não" );

$obRdbSemHistorico = new Radio;
$obRdbSemHistorico->setName   ( "stHistorico" );
$obRdbSemHistorico->setValue  ( "sim" );
$obRdbSemHistorico->setLabel  ( "Sim" );

$mascaraSetor = pegaConfiguracao("mascara_local");

$obTxtLocal = new TextBox;
$obTxtLocal->setName   ('codMasSetor');
$obTxtLocal->setRotulo ('Localização');
$obTxtLocal->setTitle  ('Informe a localização do bem.');
$obTxtLocal->setValue  ($codMasSetor);
$obTxtLocal->setSize   (strlen($mascaraSetor));
$obTxtLocal->setMaxLength (strlen($mascaraSetor));
$obTxtLocal->obEvento->setOnKeyUp ("mascaraDinamico('".$mascaraSetor."', this, event);");
$obTxtLocal->obEvento->setOnChange ("buscaValor('codMasSetor');");

//Radios de histórico
$obRdbSemQuebraPagina = new Radio;
$obRdbSemQuebraPagina->setRotulo ( "Quebrar Página por Código de Bem" );
$obRdbSemQuebraPagina->setTitle  ( "Selecione a quebra de página." );
$obRdbSemQuebraPagina->setName   ( "boQuebraPagina" );
$obRdbSemQuebraPagina->setChecked( true );
$obRdbSemQuebraPagina->setValue  ( 'false' );
$obRdbSemQuebraPagina->setLabel  ( "Não" );

$obRdbComQuebraPagina = new Radio;
$obRdbComQuebraPagina->setName   ( "boQuebraPagina" );
$obRdbComQuebraPagina->setValue  ( 'true' );
$obRdbComQuebraPagina->setLabel  ( "Sim" );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                          );
$obFormulario->setAjuda         ("UC-03.01.11");
$obFormulario->addHidden        ( $obHdnCaminho                    );
$obFormulario->addHidden        ( $obHdnCtrl                       );
$obFormulario->addTitulo        ( "Insira os Dados para Procura"   );
$obFormulario->addComponente    ( $obISelectEntidade  );
$obIMontaClassificacao->geraFormulario( $obFormulario );
$obFormulario->agrupaComponentes( array( $obTxtBemInicial ,$obLblIntervalo, $obTxtBemFinal));
$obFormulario->addComponente    ( $obIPopCGM );
$obFormulario->agrupaComponentes( array( $obRdbTipoResumido, $obRdbTipoCompleto));
$obFormulario->agrupaComponentes( array( $obRdbComHistorico, $obRdbSemHistorico));
$obFormulario->addComponente    ( $obDtDataAquisicao);
$obFormulario->addComponente    ( $obPeriodicidadeIncorporacao);
$obFormulario->agrupaComponentes( array( $obDepreciacoesSim, $obDepreciacoesNao));
$obFormulario->addTitulo        ( "Localização"   );
$obIMontaOrganograma->geraFormulario( $obFormulario );
$obIMontaOrganogramaLocal->geraFormulario( $obFormulario );

$obFormulario->agrupaComponentes( array( $obRdbSemQuebraPagina, $obRdbComQuebraPagina));

$obFormulario->OK();
$obFormulario->show();

?>
