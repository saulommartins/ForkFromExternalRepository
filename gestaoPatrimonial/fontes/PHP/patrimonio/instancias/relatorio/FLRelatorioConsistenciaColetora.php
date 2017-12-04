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
    * Data de CriaÃ§Ã£o   : 13/08/2010

    * @description Tela filtro para escolher arquivo para relatÃ³rio

    * @author Desenvolvedor: Tonismar R. Bernardo

      $Id: FLRelatorioConsistenciaColetora.php 66466 2016-08-31 14:34:38Z michel $


    * @ignore
*/
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetora.class.php';
include_once CAM_GA_ORGAN_NEGOCIO.'ROrganogramaLocal.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeGeral.class.php';

$stAcao = $_POST['stAcao'] ? $_POST['stAcao'] : $_GET['stAcao'];

$form = new Form;
$form->setAction( CAM_GP_PAT_INSTANCIAS.'relatorio/LSRelatorioConsistenciaColetora.php' );
$form->setTarget( 'telaPrincipal');

$ctrl = new Hidden;
$ctrl->setName( 'stCtrl' );
$ctrl->setValue( '' );

$acao = new Hidden;
$acao->setName( 'stAcao' );
$acao->setValue( $stAcao );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setRotulo         ( "Periodicidade" );
$obPeriodicidade->setTitle          ( "Informe a Periodicidade de Importação dos arquivos que deseja pesquisar" );
$obPeriodicidade->setExibeDia		( false );
$obPeriodicidade->setNull 			( false );
$obPeriodicidade->setValue          ( 4 );

$obRadioRelatorioDivergencia = new Radio;
$obRadioRelatorioDivergencia->setName('tipoRelatorio');
$obRadioRelatorioDivergencia->setChecked(true);
$obRadioRelatorioDivergencia->setLabel('Com Divergência ');
$obRadioRelatorioDivergencia->setRotulo("Tipo de Relatório");
$obRadioRelatorioDivergencia->setValue('divergencia');

$obRadioRelatorioNaoLidos = new Radio;
$obRadioRelatorioNaoLidos->setName('tipoRelatorio');
$obRadioRelatorioNaoLidos->setChecked(false);
$obRadioRelatorioNaoLidos->setLabel('Não Lidos');
$obRadioRelatorioNaoLidos->setRotulo("Tipo de Relatório");
$obRadioRelatorioNaoLidos->setValue('naoLidos');

$obRadioRelatorioSemDivergencia = new Radio;
$obRadioRelatorioSemDivergencia->setName('tipoRelatorio');
$obRadioRelatorioSemDivergencia->setChecked(false);
$obRadioRelatorioSemDivergencia->setLabel('Sem Divergência');
$obRadioRelatorioSemDivergencia->setRotulo("Tipo de Relatório");
$obRadioRelatorioSemDivergencia->setValue('semDivergencia');

# Entidade Principal
$obITextBoxSelectEntidade = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidade->obTextBox->setId('inCodEntidade');
$obITextBoxSelectEntidade->obTextBox->setName('inCodEntidade');
$obITextBoxSelectEntidade->obSelect->setName('stNomEntidade');
$obITextBoxSelectEntidade->obSelect->setId('stNomEntidade');
$obITextBoxSelectEntidade->setObrigatorio(true);

$local = new ROrganogramaLocal();
$local->listarLocal($listaLocais);

$locaisSelecionados = new RecordSet;

/** Select para selecionar locais **/
$selectLocais = new SelectMultiplo();
$selectLocais->setName('locaisSelecionados');
$selectLocais->setRotulo('Locais');
$selectLocais->setNull(false);
$selectLocais->setTitle('Locais Disponíeis');

$selectLocais->SetNomeLista1('locaisDisponiveis');
$selectLocais->setCampoId1('cod_local');
$selectLocais->setCampoDesc1('descricao');
$selectLocais->setRecord1($listaLocais);

$selectLocais->SetNomeLista2('locaisSelecionados');
$selectLocais->setCampoId2('cod_local');
$selectLocais->setCampoDesc2('descricao');
$selectLocais->setRecord2($locaisSelecionados);

$formulario = new Formulario();
$formulario->addForm( $form );
$formulario->addHidden( $acao );
$formulario->addHidden( $ctrl );
$formulario->addTitulo( 'Dados para o filtro.' );
$formulario->addComponente( $obITextBoxSelectEntidade );
$formulario->addComponente( $obPeriodicidade);
$formulario->agrupaComponentes(array($obRadioRelatorioDivergencia,$obRadioRelatorioNaoLidos,$obRadioRelatorioSemDivergencia));
$formulario->addComponente( $selectLocais );
$formulario->Ok();
$formulario->show();
